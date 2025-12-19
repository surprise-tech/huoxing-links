<?php

namespace App\Forms;

use App\Services\SystemConfig;
use Illuminate\Http\Request;
use Ugly\Base\Contracts\SimpleForm;

class BaseConfig implements SimpleForm
{
    public function policy(Request $request): array
    {
        return $request->validate([
            'recommend_commission' => 'nullable|integer', // 推荐佣金

            'web_site_customer_service' => 'nullable',
            'web_site_title' => 'nullable',
            'web_site_logo' => 'nullable',
            'web_site_bottom_logo' => 'nullable',

            'verify_code_is_open' => 'nullable',
            'send_code_mode' => 'nullable|integer|in:1,2', // 1短信2邮箱

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
        array_filter($input);

        SystemConfig::set($input);
    }

    public function default(): array
    {
        return [
            'web_site_customer_service' => SystemConfig::get('web_site_customer_service'),
            'web_site_title' => SystemConfig::get('web_site_title'),
            'web_site_logo' => SystemConfig::get('web_site_logo'),
            'web_site_bottom_logo' => SystemConfig::get('web_site_bottom_logo'),

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
