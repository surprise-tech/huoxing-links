<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ugly\Base\Casts\Amount;
use Ugly\Base\Traits\SerializeDate;

class VipPackage extends Model
{
    use SerializeDate;

    protected $guarded = [];

    protected $casts = [
        'price' => Amount::class,
        'config' => 'json',
    ];
}
