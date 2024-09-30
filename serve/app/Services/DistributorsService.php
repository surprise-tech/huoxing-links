<?php

namespace App\Services;

class DistributorsService
{
    public static function commission($commission, $user, $data): void
    {
        if ($commission > 0) {
            $user->with('parent');
            if ($user->parent) {
                $user->parent->commission = bcadd($user->parent->commission, $commission);
                $user->parent->accumulate_commission = bcadd($user->parent->accumulate_commission, $commission);
                $user->parent->save();
                $user->parent->commissionLogs()->create(array_merge([
                    'amount' => $commission,
                    'children_user_id' => $user->id,
                ], $data));
            }
        }
    }
}
