<?php

namespace App\Models;

use App\Enums\CommissionType;
use App\Enums\WithdrawStatus;
use App\Traits\BelongToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ugly\Base\Traits\SearchModel;
use Ugly\Base\Traits\SerializeDate;

class CommissionLog extends Model
{
    use BelongToUser,SearchModel,SerializeDate;

    protected $guarded = [];

    protected $casts = [
        'is_withdraw' => 'boolean',
        'status' => WithdrawStatus::class,
        'type' => CommissionType::class,
        'payee' => 'json',
    ];

    public function children_user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
