<?php

namespace App\Http\Controllers\Api;

use App\Enums\WithdrawStatus;
use App\Http\Controllers\Controller;
use App\Models\CommissionLog;
use Illuminate\Http\JsonResponse;
use Ugly\Base\Traits\ApiResource;

class WithdrawController extends Controller
{
    use ApiResource;

    // 申请记录
    public function index(): JsonResponse
    {
        $query = CommissionLog::search(['status' => '='], ['user:id,username'])
            ->where('is_withdraw', true)
            ->orderByDesc('id');

        return $this->paginate($query);
    }

    // 拒绝
    public function reject($id): JsonResponse
    {
        $log = CommissionLog::query()
            ->where('is_withdraw', true)
            ->where('status', WithdrawStatus::PENDING)
            ->findOrFail($id);
        $log->status = WithdrawStatus::REJECTED;
        $log->save();
        $log->user->commission = bcadd($log->user->commission, abs($log->amount), 4);
        $log->user->save();

        return $this->success();
    }

    // 确认打款
    public function confirm($id): JsonResponse
    {
        $log = CommissionLog::query()
            ->where('is_withdraw', true)
            ->where('status', WithdrawStatus::PENDING)
            ->findOrFail($id);
        $log->status = WithdrawStatus::PAID;
        $log->save();

        return $this->success();
    }
}
