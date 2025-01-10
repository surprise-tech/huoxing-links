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
            'is_give_vip' => 'nullable', // 开启赠送
            'give_vip_days' => 'nullable|integer|min:0|max:365', //赠送天数
            'give_vip_id' => 'nullable|integer', //赠送套餐
            'recommend_commission' => 'nullable|integer', // 推荐佣金
            'recommend_commission_1' => 'nullable|integer',
            'recommend_commission_2' => 'nullable|integer',
            'member_pay_commission_1' => 'nullable|integer', // 一级邀请人消费提成 1%
            'member_pay_commission_2' => 'nullable|integer', // 二级邀请人消费提成 1%

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

            'wechat_pay_app_id' => 'nullable',
            'wechat_pay_mch_id' => 'nullable',
            'wechat_pay_secret_key' => 'nullable',
            'wechat_pay_private_cert' => 'nullable',
            'wechat_pay_certificate' => 'nullable',
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
            'is_give_vip' => (bool) SystemConfig::get('is_give_vip'),
            'give_vip_id' => SystemConfig::get('give_vip_id'),
            'give_vip_days' => SystemConfig::get('give_vip_days'),
            'recommend_commission_1' => SystemConfig::get('recommend_commission_1'),
            'recommend_commission_2' => SystemConfig::get('recommend_commission_2'),
            'member_pay_commission_1' => SystemConfig::get('member_pay_commission_1'),
            'member_pay_commission_2' => SystemConfig::get('member_pay_commission_2'),

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

            'wechat_pay_app_id' => SystemConfig::get('wechat_pay_app_id'),
            'wechat_pay_mch_id' => SystemConfig::get('wechat_pay_mch_id'),
            'wechat_pay_secret_key' => SystemConfig::get('wechat_pay_secret_key'),
            'wechat_pay_private_cert' => SystemConfig::get('wechat_pay_secret_key'), // 私钥证书
            'wechat_pay_certificate' => SystemConfig::get('wechat_pay_certificate'), // 公钥证书

        ];
    }
}
