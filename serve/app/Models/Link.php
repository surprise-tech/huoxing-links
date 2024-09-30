<?php

namespace App\Models;

use App\Enums\LinkType;
use App\Traits\BelongToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Ugly\Base\Casts\Amount;
use Ugly\Base\Traits\SearchModel;
use Ugly\Base\Traits\SerializeDate;

class Link extends Model
{
    use BelongToUser,SearchModel,SerializeDate;

    protected $guarded = [];

    protected static function booted(): void
    {
        self::creating(function (Link $link) {
            $link->code = Str::random(8);
        });
    }

    protected $casts = [
        'config' => 'json',
        'type' => LinkType::class,
        'price' => Amount::class.':4',
        'cache' => 'json',
    ];

    // 访问记录.
    public function visitLogs(): HasMany
    {
        return $this->hasMany(LinkVisitLog::class, 'link_id');
    }
}
