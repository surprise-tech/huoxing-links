<?php

namespace App\Http\Resources;

use App\Enums\UserType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'username' => $this->username,
            'type' => $this->type,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            $this->mergeWhen($this->type === UserType::MEMBER, [
                'vip_package' => $this->vipPackage,
                'link_amount' => $this->link_amount,
                'yuanma_amount' => $this->yuanma_amount,
                'dy_card_amount' => $this->dy_card_amount,
                'referral_code' => $this->referral_code,
                'commission' => $this->commission, // 未提现佣金
                'accumulate_commission' => $this->accumulate_commission, // 累计佣金
                'parent' => $this->parent,
            ]),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'login_time' => $this->login_time?->format('Y-m-d H:i:s'),
        ];
    }
}
