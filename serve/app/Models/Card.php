<?php

namespace App\Models;

use App\Traits\BelongToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ugly\Base\Traits\SearchModel;
use Ugly\Base\Traits\SerializeDate;

class Card extends Model
{
    use BelongToUser, SearchModel, SerializeDate;

    protected $guarded = [];

    protected $casts = [
        'used' => 'boolean',
    ];

    public function vipPackage(): BelongsTo
    {
        return $this->belongsTo(VipPackage::class, 'vip_id');
    }
}
