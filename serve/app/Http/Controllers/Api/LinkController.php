<?php

namespace App\Http\Controllers\Api;

use App\Enums\LinkType;
use App\Enums\SwitchType;
use App\Enums\UserType;
use App\Enums\UVLimitType;
use App\Models\Domain;
use App\Models\Link;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Ugly\Base\Http\Controllers\FormController;
use Ugly\Base\Services\FormService;

class LinkController extends FormController
{
    public function index(Request $request): JsonResponse
    {
        $username = $request->input('username', null);
        $query = Link::search([
            'type' => '=',
            'title' => 'like',
        ], ['user:id,username'])->withCount(['visitLogs as visit_uv_count' => function ($query) {
            $query->select(DB::raw('COUNT(DISTINCT device_uid)'));
        }])
            ->when(
                auth('api')->user()->type !== UserType::Admin,
                fn ($query) => $query->where('user_id', auth('api')->user()->id),
            )
            ->when($username, fn ($query) => $query->whereHas('user', fn ($query) => $query->where('username', 'like', "%{$username}%")))
            ->orderByDesc('id');

        return $this->paginate($query);
    }

    protected function form(): FormService
    {
        $rule = [
            'title' => 'required|string|max:80',
            'type' => ['required', new Enum(LinkType::class)],
            'icon' => 'required',
            'description' => 'nullable|string|max:500',
            'remark' => 'nullable|string',
            'expired_at' => 'string',
        ];
        if (request('type') == LinkType::LANDING_MINI->value) {
            unset($rule['title'], $rule['description']);
        }

        $form = FormService::make(Link::class);
        $form->validate(fn (FormService $form) => array_merge($rule, $this->getConfigRules()), [
            'config.wx.qr.*.name.required' => '请填写二维码名称',
            'config.wx.qr.*.sort.required' => '请填写二维码排序',
            'config.wx.qr.*.path.required' => '请上传二维码',
            'config.url.starts_with' => '请正确上传草料图片类生成的二维码图片',
        ]);

        $user = auth('api')->user();
        $form->policy(function (FormService $form) use ($user) {
            if ($user->type === UserType::Admin) {
                return true;
            }
            if ($form->isCreate()) {
                $config = data_get($user->getPackageConfig(), 'config');

                // 类型限制
                $typeName = LinkType::from((int) request()->input('type'))->name;
                if (! data_get($config, 'allow_type.'.$typeName)) {
                    return '当前套餐不支持该类型链接！';
                }

                // 会员，数量限制
                if (
                    $user->type === UserType::MEMBER &&
                    data_get($config, 'count_limit') <= Link::query()->where('user_id', $user->id)->count()
                ) {
                    return '拥有的链接数量已达上限！';
                }

                // 是否允许自定义落地页
                $inputConfig = request()->input('config');
                if (
                    ! data_get($config, 'cur_index') &&
                    (
                        data_get($inputConfig, 'wx.avatar') ||
                        data_get($inputConfig, 'wx.title') ||
                        data_get($inputConfig, 'wx.sub_title')
                    )
                ) {
                    return '当前套餐不允许自定义落地页！';
                }
            }

            return $form->isCreate() || $form->getModel()->user_id === $user->id;
        });

        $form->saving(function (FormService $form) use ($user) {
            if ($form->isCreate()) {
                $form->safeFormData['status'] = 1;
                $form->safeFormData['user_id'] = $user->id;

                if ($form->safeFormData['type'] == LinkType::LANDING_MINI->value) {
                    foreach ($form->safeFormData['config']['wx']['qr'] as &$it) {
                        $it['visit_uv'] = 0;
                    }
                    $form->safeFormData['title'] = $form->safeFormData['config']['wx']['title'] ?? '';
                    $form->safeFormData['description'] = $form->safeFormData['config']['wx']['sub_title'] ?? '';
                }
                $form->safeFormData['expired_at'] = now()->addMonth();
            }
        });

        return $form;
    }

    // 详情.
    public function show($id): JsonResponse
    {
        $link = Link::query()
            ->where(function ($query) {
                if (auth('api')->user()->type !== UserType::Admin) {
                    $query->where('user_id', auth('api')->user()->id);
                }
            })
            ->findOrFail($id)
            ->toArray();
        $domain = Domain::query()
            ->find(data_get($link, 'config.domain_id'));
        $link['share_link'] = $domain ? rtrim($domain->url, '/').'?code='.data_get($link, 'code') : '';

        return $this->success($link);
    }

    // 生成config字段的验证规则.
    private function getConfigRules(): array
    {
        $user = auth('api')->user();
        $iptType = request()->integer('type');
        $is_wx = $iptType === LinkType::LANDING_MINI->value;

        $res = [
            'config.domain_id' => [
                'required',
                Rule::exists('domains', 'id')->where('enable', true),
            ],
            'config.url' => 'required_unless:type,'.LinkType::LANDING_MINI->value,
        ];

        if ($iptType && in_array($iptType, [LinkType::MINI_PROGRAM->value, LinkType::LANDING_MINI->value])) {
            $res['config.min_id'] = 'required';
            $res['config.domain_id'] = '';
            $res['config.url'] = '';
        }

        if ($iptType === LinkType::CLI_QR->value) {
            $res['config.url'] = 'required|url|starts_with:https://qr61.cn/';
        }

        if ($iptType === LinkType::QR_QQ->value) {
            $res['config.url'] = 'required|url|starts_with:https://ym.link/';
        }

        return $is_wx ? array_merge($res, [
            'config.wx' => [
                'required_if:type,'.LinkType::LANDING_MINI->value,
                'array',
            ],
            'config.wx.avatar' => 'nullable|string',
            'config.wx.title' => 'nullable|string',
            'config.wx.sub_title' => 'nullable|string',
            'config.wx.qr' => 'required|array',
            'config.wx.qr.*.sort' => 'required|integer|min:0|max:200',
            'config.wx.qr.*.name' => 'required|string',
            'config.wx.qr.*.path' => 'required|string',
            'config.wx.qr.*.uv_limit_num' => 'nullable',
            'config.wx.qr.*.visit_uv' => 'nullable',
            'config.wx.qr.*.expired_at' => 'nullable|date_format:Y-m-d',
            'config.wx.switch_type' => ['required', new Enum(SwitchType::class)],
            'config.wx.uv_limit_type' => ['required', new Enum(UVLimitType::class)],
        ]) : $res;
    }

    // 获取下拉数据源
    public function link_list(): JsonResponse
    {
        $list = Link::query()
            ->where('user_id', auth('api')->user()->id)
            ->orderByDesc('type')
            ->orderByDesc('id')
            ->get(['id', 'icon', 'title', 'type']);

        return $this->success($list);
    }
}
