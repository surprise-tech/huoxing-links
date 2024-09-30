<?php

namespace App\Console\Commands;

use App\Enums\VipStatus;
use App\Models\User;
use App\Models\VipLogs;
use Illuminate\Console\Command;

class VipExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:vip-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '处理vip套餐的过期和生效';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 已生效的，要过期掉, 未生效的，要生效
        VipLogs::query()
            ->where(function ($query) {
                $query->where('status', VipStatus::ACTIVE)
                    ->where('end_at', '<=', now());
            })
            ->orWhere(function ($query) {
                $query->where('status', VipStatus::PENDING)
                    ->where('start_at', '<=', now());
            })
            ->chunkById(30, function ($logs) {
                foreach ($logs as $log) {
                    if ($log->status === VipStatus::ACTIVE) {
                        // 生效中的要过期
                        $log->status = VipStatus::EXPIRED;
                        $log->save();
                        User::query()->where('user_id', $log->user_id)->update([
                            'vip_id' => null,
                            'start_at' => null,
                            'end_at' => null,
                        ]);
                    } else {
                        // 未生效的，要生效
                        $log->status = VipStatus::ACTIVE;
                        $log->save();
                        User::query()->where('user_id', $log->user_id)->update([
                            'vip_id' => $log->vip_id,
                            'start_at' => $log->start_at,
                            'end_at' => $log->ent_at,
                        ]);
                    }
                }
            });

    }
}
