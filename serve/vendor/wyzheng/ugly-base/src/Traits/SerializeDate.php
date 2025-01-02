<?php

namespace Ugly\Base\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait SerializeDate
{
    /**
     * 序列化日期格式.
     */
    protected function serializeDate($date): mixed
    {
        return $date->format($this->dateFormat ?? 'Y-m-d H:i:s');
    }
}
