<?php

return [
    // 系统设置
    'config' => [
        'enable' => false,
        'cache_driver' => 'file', // null 表示不缓存
        'cache_key' => 'ugly-sys-config',
        'cache_ttl' => 0, // 缓存有效期单位秒 0表示永久缓存
        'default' => [
            // 这里设置系统初始化时的配置项
            // ['slug' => '', 'value' => '', 'desc' => '']
        ],
    ],
    // 支付设置
    'payment' => [
        'enable' => true,
        'expire' => 30, // 默认支付过期时间单位分钟
    ],
];
