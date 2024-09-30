<?php

namespace App\Enums;

enum VipStatus: int
{
    // 会员状态： 1未生效 2已生效 3已过期
    case PENDING = 1;
    case ACTIVE = 2;
    case EXPIRED = 3;
}
