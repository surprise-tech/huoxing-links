<?php

namespace App\Http\Requests;

use App\Enums\CodeMode;
use App\Services\SystemConfig;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Cache;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $code_mode = SystemConfig::get('send_code_mode');
        $rule = $code_mode == CodeMode::Email->value ? 'email' : 'regex:/^1[3-9]\d{9}$/';

        return [
            'username' => [
                'required',
                $rule,
                'unique:users,username',
            ],
            'password' => 'required|min:6|confirmed',
            'captcha' => ['required', function (string $attribute, mixed $value, \Closure $fail) {
                $test_code = config('services.ali_sms.test_code');
                if (empty($test_code) || $value != $test_code) {
                    $tel = $this->input('username');
                    $captcha = Cache::get('sms_captcha_'.$tel);
                    if ((int) $captcha !== (int) $value) {
                        $fail('验证码错误！');
                    }
                }
            }],
            'referral_code' => '', // 推荐码
        ];
    }

    public function messages(): array
    {
        $txt = CodeMode::Email->value ? '邮箱' : '手机号';

        return [
            'username.required' => "请输入{$txt}！",
            'username.regex' => '手机号格式错误！',
            'username.email' => '不是有效的邮箱！',
            'username.unique' => "{$txt}已存在！",
            'password.required' => '请输入密码！',
            'password.confirmed' => '密码不一致！',
            'password.min' => '密码不少于6位！',
            'captcha.required' => '请输入短信验证码！',
            'agent_id.required' => '请选择代理套餐！',
            'referral_code.required' => '请输入推荐码！',
        ];
    }
}
