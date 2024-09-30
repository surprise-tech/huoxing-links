<?php

namespace App\Enums;

enum WithdrawStatus: int
{
    // 提现状态 0-无需处理；1-未处理；2-已打款；3-已拒绝；4-打款失败
    case NONE = 0;
    case PENDING = 1;
    case PAID = 2;
    case REJECTED = 3;
    case FAILED = 4;
}
