<?php

namespace App\Enums;

enum CommissionType: int
{
    // 申请提现
    case Withdraw = 1;

    // 邀请的代理消费提成
    case Commission = 2;

    // 推荐返佣
    case Rebates = 3;
}
