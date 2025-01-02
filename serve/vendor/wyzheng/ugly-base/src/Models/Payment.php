<?php

namespace Ugly\Base\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Ugly\Base\Casts\Amount;
use Ugly\Base\Enums\PaymentStatus;
use Ugly\Base\Enums\PaymentType;
use Ugly\Base\Traits\PaymentModel;
use Ugly\Base\Traits\SearchModel;
use Ugly\Base\Traits\SerializeDate;

class Payment extends Model
{
    use SerializeDate, SearchModel, PaymentModel;

    protected $table = 'payments';

    protected $guarded = [];

    protected $casts = [
        'amount' => Amount::class,
        'status' => PaymentStatus::class,
        'type' => PaymentType::class,
        'notification_data' => 'json',
        'attach' => 'json',
    ];

    /**
     * 对应的商户.
     */
    public function merchant(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * 对应的支付者.
     */
    public function payer(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * 关联的退款单.
     */
    public function refunds(): HasMany
    {
        return $this->hasMany(Payment::class, 'payment_id');
    }
}
