<?php

if (! function_exists('arr2tree')) {
    /**
     * 数组/集合 转换成树级结构.
     */
    function arr2tree(array $list, Closure $transform = null, string $id = 'id', string $pid = 'pid', string $children = 'children'): array
    {
        [$map, $tree] = [[], []];
        foreach ($list as $item) {
            $map[data_get($item, $id)] = $transform ? call_user_func($transform, $item) : $item;
        }

        foreach ($list as $item) {
            if (isset($item[$pid]) && isset($map[$item[$pid]])) {
                $map[$item[$pid]][$children][] = &$map[$item[$id]];
            } else {
                $tree[] = &$map[$item[$id]];
            }
        }
        unset($map);

        return $tree;
    }
}

if (! function_exists('sys_config')) {
    /**
     * 系统配置辅助函数.
     */
    function sys_config(array|string $key = null, mixed $default = null)
    {
        /**
         * @var \Ugly\Base\Support\Config $config
         */
        $config = app('ugly.config');
        if (is_null($key)) {
            return $config;
        }
        if (is_array($key)) {
            // 存储.
            return $config->set($key);
        } else {
            // 读取.
            return $config->get($key, $default);
        }
    }
}
