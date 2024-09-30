<?php

namespace App\Enums;

enum LinkType: int
{
    // 链接类型 1-跳转小程序 2-跳转金山文档 3-跳转草料二维码 4-跳转企业微信 5-跳转微信二维码
    case MINI_PROGRAM = 1;
    case KING_DOC = 2;
    case CLI_QR = 3;
    case WORK_WECHAT = 4;
    case LANDING_MINI = 5;

    // 获取所有的类型
    public static function getAllType(): array
    {
        $types = [];
        foreach (self::cases() as $case) {
            $types[] = $case->name;
        }

        return $types;
    }
}
