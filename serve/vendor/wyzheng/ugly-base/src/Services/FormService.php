<?php

namespace Ugly\Base\Services;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Validation\ValidationException;
use Ugly\Base\Enums\FormCallback;
use Ugly\Base\Enums\FormScene;
use Ugly\Base\Exceptions\ApiCustomError;

class FormService
{
    /**
     * 当前场景.
     */
    private FormScene $scene;

    /**
     * 表单钩子.
     */
    private array $formCallback = [];

    /**
     * 表单验证规则.
     */
    private array $validateRules = [];

    /**
     * 表单验证消息.
     */
    private array $validateMessages = [];

    /**
     * 表单验证属性.
     */
    private array $validateAttribute = [];

    /**
     * 需要忽略的字段.
     */
    private array $ignoreFields = [];

    /**
     * 需要处理的模型.
     */
    private mixed $model;

    /**
     * 操作model的主键值.
     */
    private int|string $key;

    /**
     * 允许行内编辑的字段.
     */
    private array $allowInlineEditFields = [];

    /**
     * 存储通过表单验证和handleInput处理后的数据.
     */
    public array $safeFormData = [];

    /**
     * 构造函数.
     */
    private function __construct($model)
    {
        $this->model = is_string($model) ? app($model) : $model;
    }

    /**
     * 创建实例.
     */
    public static function make($model): static
    {
        return new static($model);
    }

    /**
     * 获取模型.
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * 设置主键（编辑/删除）.
     *
     * @return $this
     */
    public function setKey(int|string $key): static
    {
        $this->key = $key;

        return $this;
    }

    /**
     * 获取模型ID.
     */
    public function getKey(): string|int
    {
        return $this->key ?? $this->model->getKey();
    }

    /**
     * 设置form场景.
     */
    public function setScene(FormScene $scene): static
    {
        $this->scene = $scene;

        return $this;
    }

    /**
     * 是否是创建.
     */
    public function isCreate(): bool
    {
        return $this->scene === FormScene::Create;
    }

    /**
     * 是否是编辑.
     */
    public function isEdit(): bool
    {
        return $this->scene === FormScene::Edit;
    }

    /**
     * 是否是行内编辑.
     */
    public function isInlineEdit(): bool
    {
        // 约定PATCH请求为行内编辑的请求
        return request()->isMethod('PATCH');
    }

    /**
     * 是否是删除.
     */
    public function isDelete(): bool
    {
        return $this->scene === FormScene::Delete;
    }

    /**
     * 设置模型策略.
     */
    public function policy(\Closure $closure): static
    {
        $this->formCallback[FormCallback::Policy->value] = $closure;

        return $this;
    }

    /**
     * 设置验证规则.
     */
    public function validate(\Closure|array $closure, array $messages = [], array $attributes = []): static
    {
        $this->formCallback[FormCallback::Validate->value] = is_array($closure) ? fn () => $closure : $closure;
        $this->validateMessages = array_merge($this->validateMessages, $messages);
        $this->validateAttribute = array_merge($this->validateAttribute, $attributes);

        return $this;
    }

    /**
     * 设置处理输入的回调.
     */
    public function handleInput(\Closure $closure): static
    {
        $this->formCallback[FormCallback::HandleInput->value] = $closure;

        return $this;
    }

    /**
     * 表单唯一验证.
     */
    public function unique(string $table = null, $column = 'NULL'): Unique
    {
        $rule = Rule::unique($table ?: get_class($this->getModel()), $column);
        if ($this->isEdit()) {
            $rule->ignore($this->getKey());
        }

        return $rule;
    }

    /**
     * 设置允许行内编辑的字段.
     */
    public function inlineEdit(array $fields): static
    {
        foreach ($fields as $key => $val) {
            if (is_numeric($key)) {
                $this->allowInlineEditFields[$val] = [];
            } elseif (is_array($val)) {
                $this->allowInlineEditFields[$key] = $val;
            }
        }

        return $this;
    }

    /**
     * 保存.
     */
    public function save()
    {
        // 请求实例
        $request = request();

        // 编辑模式下先获取需要编辑的资源.
        if ($this->isEdit()) {
            $this->model = $this->model->findOrFail($this->key);
        }

        // 获取表单验证规则配置.
        if ($validateFn = $this->checkFormCallback(FormCallback::Validate)) {
            $this->validateRules = call_user_func($validateFn, $this);
        }

        // 行内编辑的情况下重新设置表单验证规则
        if ($this->isInlineEdit()) {
            $this->validateRules = $this->generateLineEditValidateRules($request);
        }

        // 执行表单验证.
        if (! empty($this->validateRules)) {
            $this->safeFormData = $request->validate(
                $this->decodeIgnoreFields($this->validateRules),
                $this->validateMessages,
                $this->validateAttribute
            );
        }

        // 执行策略检查
        if ($policyFn = $this->checkFormCallback(FormCallback::Policy)) {
            $result = call_user_func($policyFn, $this);
            if ($result === false || is_string($result)) {
                throw new ApiCustomError($result ?: '非法操作！');
            }
        }

        // 处理输入的值
        if ($handleInputFn = $this->checkFormCallback(FormCallback::HandleInput)) {
            $this->safeFormData = $this->decodeIgnoreFields(call_user_func($handleInputFn, $this));
        }

        // 保存前钩子.
        if ($savingFn = $this->checkFormCallback(FormCallback::Saving)) {
            call_user_func($savingFn, $this);
        }

        // 填充数据.
        $allowData = $this->delIgnoreFields($this->safeFormData);
        if (! empty($allowData)) {
            if ($this->isEdit()) {
                foreach ($allowData as $key => $val) {
                    $this->model->$key = $val;
                }
                $this->model->save();
            }
            if ($this->isCreate()) {
                $this->model = $this->model->create($allowData);
            }
        }

        // 保存后钩子.
        if ($savedFn = $this->checkFormCallback(FormCallback::Saved)) {
            call_user_func($savedFn, $this);
        }

        return $this->model;
    }

    /**
     * 保存前回调函数.
     */
    public function saving(\Closure $callback = null): static
    {
        $this->formCallback[FormCallback::Saving->value] = $callback;

        return $this;
    }

    /**
     * 保存后回调函数.
     */
    public function saved(\Closure $callback = null): static
    {
        $this->formCallback[FormCallback::Saved->value] = $callback;

        return $this;
    }

    /**
     * 删除前回调.
     */
    public function deleting(\Closure $callback = null): static
    {
        $this->formCallback[FormCallback::Deleting->value] = $callback;

        return $this;
    }

    /**
     * 删除后回调.
     */
    public function deleted(\Closure $callback = null): static
    {
        $this->formCallback[FormCallback::Deleted->value] = $callback;

        return $this;
    }

    /**
     * 删除.
     */
    public function delete()
    {
        $this->model = $this->model->findOrFail($this->key);
        // 执行策略检查
        if ($policyFn = $this->checkFormCallback(FormCallback::Policy)) {
            $result = call_user_func($policyFn, $this);
            if ($result === false || is_string($result)) {
                throw new ApiCustomError($result ?: '非法操作！');
            }
        }
        // 删除前
        if ($deletingFn = $this->checkFormCallback(FormCallback::Deleting)) {
            call_user_func($deletingFn, $this);
        }
        // 删除
        $res = $this->model->delete();

        // 删除后
        if ($deletedFn = $this->checkFormCallback(FormCallback::Deleted)) {
            call_user_func($deletedFn, $this->key);
        }

        return $res;
    }

    /**
     * 检查表单回调函数是否可以调用，如果可以调用就返回可调用函数.
     */
    private function checkFormCallback(FormCallback $key): bool|\Closure
    {
        if (isset($this->formCallback[$key->value]) && $this->formCallback[$key->value] instanceof \Closure) {
            $enabled = true;
            if ($this->isInlineEdit() && ! in_array($key, [FormCallback::Validate, FormCallback::Policy])) {
                $enabled = in_array($key, data_get($this->allowInlineEditFields, request('field'), []));
            }

            return $enabled ? $this->formCallback[$key->value] : false;
        }

        return false;
    }

    /**
     * 解析需要忽略的字段.
     */
    private function decodeIgnoreFields(array $fields): array
    {
        $results = [];
        foreach ($fields as $key => $val) {
            if (str_starts_with($key, '!')) {
                $realKey = substr($key, 1);
                $results[$realKey] = $val;
                if (! str_contains($key, '.')) {
                    $this->ignoreFields[] = $realKey;
                }
            } else {
                $results[$key] = $val;
            }
        }

        return $results;
    }

    /**
     * 删除需要忽略的字段.
     */
    private function delIgnoreFields(array $formData): array
    {
        if (empty($this->ignoreFields)) {
            return $formData;
        }

        return array_diff_key($formData, array_flip($this->ignoreFields));
    }

    /**
     * 生成行内编辑表单验证规则.
     */
    private function generateLineEditValidateRules(Request $request): array
    {
        $validMessage = [];
        if (! $request->has('field')) {
            $validMessage['field'] = 'field is required';
        }
        if (! $request->has('value')) {
            $validMessage['value'] = 'value is required';
        }
        if (! empty($validMessage)) {
            throw ValidationException::withMessages($validMessage);
        }

        $field = $request->input('field');
        $value = $request->input('value');

        // 检查是否允许行内编辑
        if (! in_array($field, array_keys($this->allowInlineEditFields))) {
            ValidationException::withMessages([$field => '不允许修改！']);
        }

        // 合并到请求中
        $request->merge([$field => $value]);

        // 返回字段对应的验证规则
        return array_filter($this->validateRules, function ($key) use ($field) {
            return str_starts_with($key, $field) || str_starts_with($key, "!$field");
        }, ARRAY_FILTER_USE_KEY);
    }
}
