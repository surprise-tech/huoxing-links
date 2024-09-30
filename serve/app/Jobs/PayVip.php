<?php

namespace App\Jobs;

use App\Enums\CommissionType;
use App\Enums\VipStatus;
use App\Enums\WithdrawStatus;
use App\Models\VipLogs;
use App\Services\DistributorsService;
use App\Services\SystemConfig;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class PayVip implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected $payment)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $attach = data_get($this->payment, 'attach', []);
        $user = $this->payment->payer;
        $type = data_get($attach, 'type', 1); // 1-立即生效 2-延期生效
        if ($type === 1) {
            // 1-立即生效
            $start_at = now();
            $status = VipStatus::ACTIVE;
        } else {
            // 2-延期生效
            $info = VipLogs::query()
                ->where('user_id', $user->id)
                ->where('status', [VipStatus::PENDING, VipStatus::ACTIVE])
                ->orderByDesc('end_at')
                ->first();
            $start_at = $info ? Carbon::parse($info->end_at) : now();
            $status = VipStatus::PENDING;
        }

        // 创建记录
        VipLogs::query()->create([
            'user_id' => $user->id,
            'payment_id' => $this->payment->id,
            'vip_id' => $attach['vip_id'],
            'status' => $status,
            'start_at' => $start_at,
            'end_at' => $start_at->addMonth(),
        ]);
        if ($status === VipStatus::ACTIVE) {
            $user->vip_id = $attach['vip_id'];
            $user->start_at = now();
            $user->end_at = now()->addMonth();
            $user->save();
        }
        // 一级分销
        $this->distributors(
            (int) SystemConfig::get('member_pay_commission_1'),
            $user,
        );
        // 二级分销
        $this->distributors(
            (int) SystemConfig::get('member_pay_commission_2'),
            $user->parent,
            '二级分销邀请人消费提成',
        );
    }

    private function distributors($rate, $user, $title = null): void
    {
        if (! empty($rate)) {
            $commission = bcdiv(bcmul($this->payment->amount, $rate, 4), 100, 4);

            DistributorsService::commission(
                $commission,
                $user,
                [
                    'title' => $title ?? '邀请人消费提成',
                    'type' => CommissionType::Commission,
                    'is_withdraw' => false,
                    'status' => WithdrawStatus::NONE,
                ]
            );
        }
    }
}
