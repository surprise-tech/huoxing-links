<?php

namespace Ugly\Base\Enums;

enum PaymentStatus: int
{
    // 状态: 1处理中 2成功 3失败
    case Processing = 1;

    case Success = 2;

    case Fail = 3;
}
