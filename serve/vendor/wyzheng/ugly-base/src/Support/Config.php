<?php

namespace Ugly\Base\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Fluent;
use Ugly\Base\Models\SysConfig;

class Config extends Fluent
{
    /**
     * 获取数据库中的原始数据.
     */
    private function getRawData(): array
    {
        return SysConfig::query()->get(['value', 'slug'])->pluck('value', 'slug')->toArray();
    }

    /**
     * 从缓存中获取数据.
     */
    private function getCacheData(): array
    {
        $driver = config('ugly.config.cache_driver');
        if (config('ugly.config.cache_driver')) {
            $cache_ttl = config('ugly.config.cache_ttl');
            $cache_key = config('ugly.config.cache_key');
            if ($cache_ttl) { // 缓存
                return Cache::driver($driver)->remember($cache_key, $cache_ttl, fn () => $this->getRawData());
            } else { // 0 永久缓存
                return Cache::driver($driver)->rememberForever($cache_key, fn () => $this->getRawData());
            }
        }

        return $this->getRawData();
    }

    /**
     * 清除缓存.
     */
    private function clearCache(): void
    {
        $driver = config('ugly.config.cache_driver');
        if ($driver) {
            Cache::driver($driver)->forget(config('ugly.config.cache_key'));
        }
    }

    public function get($key, $default = null)
    {
        if (empty($this->attributes)) {
            $this->attributes = $this->getCacheData();
        }

        return parent::get($key, $default);
    }

    public function set($key, $value = null): static
    {
        $data = is_array($key) ? $key : [$key => $value];
        foreach ($data as $slug => $val) {
            SysConfig::query()->updateOrCreate(['slug' => $slug], ['value' => $val]);
            parent::offsetSet($slug, $val);
        }

        // 清除缓存
        $this->clearCache();

        return $this;
    }
}
