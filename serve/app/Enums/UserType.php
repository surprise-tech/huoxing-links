<?php

namespace App\Enums;

enum UserType: int
{
    // 1-会员；2-代理商; 3-管理员
    case MEMBER = 1;
    case AGENT = 2;
    case Admin = 3;
}
