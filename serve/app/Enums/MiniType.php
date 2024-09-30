<?php

namespace App\Enums;

enum MiniType: int
{
    // 小程序类型：1-落地小程序；2-自有小程序
    case LANDING = 1;
    case OWN = 2;
}
