<?php

namespace App\Models;

use App\Traits\BelongToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ugly\Base\Traits\SearchModel;

class Material extends Model
{
    use BelongToUser, SearchModel;

    protected $guarded = [];

    public function cate(): BelongsTo
    {
        return $this->belongsTo(MaterialCategory::class, 'cate_id', 'id');
    }
}
