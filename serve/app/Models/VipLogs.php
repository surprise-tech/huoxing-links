<?php

namespace App\Models;

use App\Enums\VipStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ugly\Base\Models\Payment;

class VipLogs extends Model
{
    protected $guarded = [];

    protected $casts = [
        'status' => VipStatus::class,
    ];

    // 对应的payment
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    // 对应的会员套餐
    public function vipPackage(): BelongsTo
    {
        return $this->belongsTo(VipPackage::class, 'vip_id');
    }
}
