<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Model
 */
trait BelongToUser
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
