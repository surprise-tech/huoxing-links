<?php

namespace Ugly\Base\Enums;

enum FormCallback: string
{
    case Validate = 'validate'; // 表单验证
    case Policy = 'policy'; // 模型策略
    case HandleInput = 'handle-input'; // 处理输入
    case Saving = 'saving'; // 保存前
    case Saved = 'saved'; // 保存后
    case Deleting = 'deleting'; // 删除前
    case Deleted = 'deleted'; // 删除后
}
