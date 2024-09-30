<?php

namespace App\Http\Controllers\Api;

use App\Enums\CommissionType;
use App\Enums\WithdrawStatus;
use App\Http\Controllers\Controller;
use App\Models\CommissionLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Ugly\Base\Traits\ApiResource;

class CommissionController extends Controller
{
    use ApiResource;

    // 佣金记录
    public function logs(Request $request): JsonResponse
    {
        return $this->paginate(CommissionLog::search([
            'status' => '=',
            'type' => 'in',
        ], ['children_user:id,username'])
            ->where('user_id', $request->user('api')->id)
            ->orderByDesc('id'));
    }

    public function agent_logs(Request $request, $id)
    {
        return $this->paginate(CommissionLog::search([
            'status' => '=',
            'type' => 'in',
        ], ['children_user:id,username'])
            ->where('user_id', $id)
            ->orderByDesc('id'));
    }

    // 申请提现
    public function applyWithdraw(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payee' => 'required|array',
            'payee.name' => 'required|string',
            'payee.account' => 'required|string',
        ]);
        Cache::lock('app_withdraw_lock', 5)
            ->block(3, function () use ($request) {
                $amount = (float) $request->input('amount');
                $payee = (array) $request->input('payee');
                $user = $request->user('api');

                throw_if(
                    $user->commission < $amount,
                    ValidationException::withMessages(['amount' => '提现金额不能大于可提现金额！'])
                );

                DB::transaction(function () use ($user, $amount, $payee) {
                    $user->commission -= $amount;
                    $user->save();
                    $user->commissionLogs()->create([
                        'children_user_id' => $user->id,
                        'amount' => -$amount,
                        'is_withdraw' => true,
                        'title' => '申请提现',
                        'type' => CommissionType::Withdraw,
                        'status' => WithdrawStatus::PENDING,
                        'payee' => $payee,
                    ]);
                });
            });

        return $this->success();
    }
}
