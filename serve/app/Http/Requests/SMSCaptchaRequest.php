<?php

namespace App\Http\Requests;

use App\Enums\CodeMode;
use App\Services\SystemConfig;
use Illuminate\Foundation\Http\FormRequest;

class SMSCaptchaRequest extends FormRequest
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
            'tel' => [
                'required',
                $rule,
            ],
            'captcha' => 'required|captcha_api:'.request()->input('key').',math',
            'key' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'tel.required' => '请输入手机号！',
            'tel.regex' => '手机号格式错误！',
            'tel.email' => '不是有效的邮箱！',
            'captcha.captcha_api' => '图片验证码错误！',
            'captcha.required' => '请输入验证码！',
        ];
    }
}
