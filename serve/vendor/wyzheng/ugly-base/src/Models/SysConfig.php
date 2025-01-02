<?php

namespace Ugly\Base\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class SysConfig extends Model
{
    protected $table = 'sys_configs';

    public $incrementing = false;

    protected $fillable = ['slug', 'value', 'desc'];

    protected $primaryKey = 'slug';

    /**
     * 修改器.
     */
    public function value(): Attribute
    {
        return new Attribute(
            get: function ($value) {
                if (json_validate($value)) {
                    return json_decode($value, true);
                }

                return $value;
            },
            set: function ($value) {
                if (is_null($value) || is_string($value) || is_numeric($value)) {
                    return $value;
                } elseif (is_array($value)) {
                    return json_encode($value);
                }

                return null;
            }
        );
    }
}
