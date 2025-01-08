<?php

namespace App\Enums;

enum MiniProgramType: int
{
    case User = 1; // 用户小程序
    case Platform = 2; // 平台小程序
}
