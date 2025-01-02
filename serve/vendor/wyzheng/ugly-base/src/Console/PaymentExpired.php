<?php

namespace Ugly\Base\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Ugly\Base\Enums\PaymentStatus;
use Ugly\Base\Models\Payment;

class PaymentExpired extends Command
{
    protected $signature = 'ugly:payment-expired';

    protected $description = '处理支付超时的订单';

    public function handle(): void
    {
        DB::transaction(function () {
            Payment::query()
                ->lockForUpdate()
                ->where('status', PaymentStatus::Processing)
                ->where('expired_at', '<', now())
                ->chunkById(100, function ($payments) {
                    foreach ($payments as $payment) {
                        $payment->fail('TIMEOUT', '付款超时');
                    }
                });
        });
    }
}
