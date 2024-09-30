<?php

namespace App\Enums;

enum UVLimitType: int
{
    // 访问上限类型1-累计，2-每日
    case ACCUMULATE = 1;
    case DAILY = 2;
}
