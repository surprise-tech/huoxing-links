<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ugly\Base\Traits\SerializeDate;

class MaterialCategory extends Model
{
    use HasFactory;
    use SerializeDate;

    protected $guarded = [];
}
