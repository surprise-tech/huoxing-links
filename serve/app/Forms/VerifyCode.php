<?php

namespace App\Forms;

use App\Services\SystemConfig;
use Illuminate\Http\Request;
use Ugly\Base\Contracts\SimpleForm;

class VerifyCode implements SimpleForm
{
    public function policy(Request $request): array
    {
        return $request->validate([
            'verify_code_is_open' => 'nullable|boolen',
            'send_code_mode' => 'nullable',
            'ali_sms_key' => 'nullable',
            'ali_sms_secret' => 'nullable',
            'ali_sms_sign_name' => 'nullable',

            'mail_from_name' => 'nullable',
            'mail_host' => 'nullable',
            'mail_port' => 'nullable',
            'mail_from_address' => 'nullable',
            'mail_username' => 'nullable',
            'mail_password' => 'nullable',
        ]);
    }

    // 保存
    public function handle(array $input): void
    {
        SystemConfig::set($input);
    }

    public function default(): array
    {
        return [
            'verify_code_is_open' => SystemConfig::get('verify_code_is_open'), // 开启
            'send_code_mode' => SystemConfig::get('send_code_mode'), //

            'ali_sms_key' => SystemConfig::get('ali_sms_key'),
            'ali_sms_secret' => SystemConfig::get('ali_sms_secret'),
            'ali_sms_sign_name' => SystemConfig::get('ali_sms_sign_name'),

            'mail_from_name' => SystemConfig::get('mail_from_name'),
            'mail_host' => SystemConfig::get('mail_host'),
            'mail_port' => SystemConfig::get('mail_port'),
            'mail_from_address' => SystemConfig::get('mail_from_address'),
            'mail_username' => SystemConfig::get('mail_username'),
            'mail_password' => SystemConfig::get('mail_password'),
        ];
    }
}
