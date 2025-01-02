<?php

namespace Ugly\Base\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * 金额转换器. 单位分转换为元.
 */
class Amount implements CastsAttributes
{
    public function __construct(protected ?int $scale = 2)
    {
    }

    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): float
    {
        return (float) bcdiv($value, 100, $this->scale);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): int
    {
        return (int) bcmul($value, 100, 0);
    }
}
