<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ugly\Base\Traits\SearchModel;
use Ugly\Base\Traits\SerializeDate;

class Domain extends Model
{
    use SearchModel, SerializeDate;

    protected $guarded = [];

    protected $casts = [
        'enable' => 'boolean',
    ];
}
