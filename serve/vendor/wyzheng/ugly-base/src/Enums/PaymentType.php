<?php

namespace Ugly\Base\Enums;

enum PaymentType: int
{
    // 支付类型: 1付款 2退款 3转账
    case Pay = 1;

    case Refund = 2;

    case Transfer = 3;
}
