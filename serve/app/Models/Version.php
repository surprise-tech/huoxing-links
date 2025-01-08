<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Ugly\Base\Traits\SearchModel;
use Ugly\Base\Traits\SerializeDate;

class Version extends Model
{
    use HasApiTokens, SearchModel, SerializeDate;
    use HasFactory;

    protected $guarded = [];
}
