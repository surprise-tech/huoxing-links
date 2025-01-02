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
            'is_give_vip' => (bool) SystemConfig::get('is_give_vip'),
            'give_vip_id' => SystemConfig::get('give_vip_id'),
            'give_vip_days' => SystemConfig::get('give_vip_days'),
            'recommend_commission_1' => SystemConfig::get('recommend_commission_1'),
            'recommend_commission_2' => SystemConfig::get('recommend_commission_2'),
            'member_pay_commission_1' => SystemConfig::get('member_pay_commission_1'),
            'member_pay_commission_2' => SystemConfig::get('member_pay_commission_2'),
            'send_code_mode' => SystemConfig::get('send_code_mode'),
        ];
    }
}
