<?php

namespace App\Http\Requests;

use App\Enums\CodeMode;
use App\Services\SystemConfig;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
                'exclude_if:username,admin',
                $rule,
            ],
            'password' => 'required|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => '请输入用户名',
            'username.regex' => '手机号格式错误！',
            'username.email' => '用户名不是有效的邮箱！',
            'password.required' => '请输入密码',
            'password.min' => '密码不少于6位',
        ];
    }
}
