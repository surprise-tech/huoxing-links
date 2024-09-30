<?php

namespace App\Enums;

enum SwitchType: int
{
    // 切换方式1-按优先级顺序，2-随机
    case SEQUENCE = 1;
    case RANDOM = 2;
}
