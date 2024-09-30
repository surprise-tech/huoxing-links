<?php

namespace App\Models;

use App\Enums\MiniType;
use App\Traits\BelongToUser;
use Illuminate\Database\Eloquent\Model;
use Ugly\Base\Traits\SearchModel;
use Ugly\Base\Traits\SerializeDate;

class MiniProgram extends Model
{
    use BelongToUser, SearchModel, SerializeDate;

    protected $guarded = [];

    protected $casts = [
        'type' => MiniType::class,
    ];
}
