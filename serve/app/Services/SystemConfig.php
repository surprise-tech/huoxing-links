<?php

namespace App\Services;

use App\Models\SysConfig;
use Illuminate\Support\Facades\Cache;

class SystemConfig
{
    /**
     * 获取系统配置.
     */
    public static function get(?string $key = null, mixed $default = null): mixed
    {
        $config = Cache::get('_db_system_config_', function () {
            $db_config = [];
            $list = SysConfig::query()->get();
            foreach ($list as $item) {
                $db_config[data_get($item, 'slug')] = data_get($item, 'value');
            }
            Cache::put('_db_system_config_', $db_config);

            return $db_config;
        });

        if ($key === null) {
            return $config;
        }

        return data_get($config, $key, $default);
    }

    /**
     * 设置系统配置.
     */
    public static function set(string|array $key, mixed $value = null): void
    {
        $data = is_array($key) ? $key : [$key => $value];
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $v = json_encode($v);
            }
            SysConfig::query()
                ->updateOrCreate([
                    'slug' => $k,
                ], [
                    'slug' => $k,
                    'value' => (string) $v,
                ]);
        }

        // 清除缓存
        Cache::forget('_db_system_config_');
    }
}
