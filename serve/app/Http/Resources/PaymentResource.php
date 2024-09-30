<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'no' => $this->no,
            'amount' => $this->amount,
            'type' => $this->type,
            'status' => $this->status,
            'attach' => $this->attach,
            'success_at' => $this->success_at,
            'expired_at' => $this->expired_at,
            'job' => $this->job,
            'remark' => $this->remark,
            'created_at' => (string) $this->created_at,
            'payer' => $this->payer,
        ];
    }
}
