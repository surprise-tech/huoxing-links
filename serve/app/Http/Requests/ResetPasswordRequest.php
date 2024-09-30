<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Cache;

class ResetPasswordRequest extends FormRequest
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
        return [
            'username' => 'required|regex:/^1[3-9]\d{9}$/|exists:users,username',
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
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => '请输入手机号！',
            'username.regex' => '手机号格式错误！',
            'password.required' => '请输入密码！',
            'password.min' => '密码不少于6位！',
            'password.confirmed' => '密码不一致！',
            'captcha.required' => '请输入短信验证码！',
        ];
    }
}
