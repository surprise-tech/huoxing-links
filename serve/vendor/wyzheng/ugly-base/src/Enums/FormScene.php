<?php

namespace Ugly\Base\Enums;

enum FormScene: int
{
    case Create = 1; // 创建
    case Edit = 2; // 编辑
    case Delete = 3; // 删除
}
