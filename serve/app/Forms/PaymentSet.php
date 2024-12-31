<?php

namespace App\Forms;

use App\Services\SystemConfig;
use Illuminate\Http\Request;
use Ugly\Base\Contracts\SimpleForm;

class PaymentSet implements SimpleForm
{
    public function policy(Request $request): array
    {
        return $request->validate([
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
        SystemConfig::set($input);
    }

    public function default(): array
    {
        return [
            'wechat_pay_app_id' => SystemConfig::get('wechat_pay_app_id'),
            'wechat_pay_mch_id' => SystemConfig::get('wechat_pay_mch_id'),
            'wechat_pay_secret_key' => SystemConfig::get('wechat_pay_secret_key'),
            'wechat_pay_private_cert' => SystemConfig::get('wechat_pay_secret_key'), // 私钥证书
            'wechat_pay_certificate' => SystemConfig::get('wechat_pay_certificate'), // 公钥证书
        ];
    }
}
