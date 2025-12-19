<?php

namespace App\Models;

use App\Enums\UserType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Ugly\Base\Casts\Amount;
use Ugly\Base\Traits\SearchModel;
use Ugly\Base\Traits\SerializeDate;

class User extends Authenticatable
{
    use HasApiTokens, SearchModel, SerializeDate;

    protected $guarded = [];

    protected $hidden = ['password'];

    protected $casts = [
        'type' => UserType::class,
        'credit' => Amount::class.':4',
        'accumulate_credit' => Amount::class.':4',
        'commission' => Amount::class,
        'accumulate_commission' => Amount::class,
    ];

    protected static function booted(): void
    {
        self::creating(function (User $user) {
            $user->referral_code = Str::random(8);
        });
    }

    // 推荐人
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    // 下级
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
