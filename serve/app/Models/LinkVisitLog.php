<?php

namespace App\Models;

use App\Traits\BelongToUser;
use Illuminate\Database\Eloquent\Model;

class LinkVisitLog extends Model
{
    use BelongToUser;

    protected $guarded = [];

    protected $casts = [
        'cache' => 'json',
    ];
}
