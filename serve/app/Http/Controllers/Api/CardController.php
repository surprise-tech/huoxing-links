<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Card;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Ugly\Base\Traits\ApiResource;

class CardController extends Controller
{
    use ApiResource;

    // 列表
    public function index(): JsonResponse
    {
        $query = Card::search([
            'used' => '=',
            'vip_id' => '=',
            'status' => '=',
            'exchange_time' => 'between',
            'user.username' => 'like',
        ], ['vipPackage:id,name', 'user:id,username'])->orderByDesc('id');

        return $this->paginate($query);
    }

    // 新增
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'vip_id' => 'required|exists:vip_packages,id',
            'number' => 'required|integer|min:1|max:100',
            'month' => 'required|integer|min:1|max:360',
            'expiry_time' => 'required',
        ]);
        $number = $request->integer('number');
        $tplData = [
            'vip_id' => $request->integer('vip_id'),
            'month' => $request->integer('month'),
            'expiry_time' => $request->input('expiry_time'),
            'used' => false,
            'status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $inserts = [];
        for ($i = 0; $i < $number; $i++) {
            $inserts[] = array_merge($tplData, [
                'secret' => 'SP'.Str::ulid()->toBase32(),
            ]);
        }
        Card::query()->insert($inserts);

        return $this->success();
    }

    public function update(Request $request, $id): JsonResponse
    {
        $card = Card::query()->where('status', true)->findOrFail($id);
        $card->update(['status' => false]);

        return $this->success();
    }

    // 消费
    public function consume(Request $request): JsonResponse
    {
        DB::transaction(function () use ($request) {
            $secret = $request->input('secret');
            $card = Card::query()->lockForUpdate()
                ->where('secret', $secret)
                ->where('used', false)
                ->where('status', true)
                ->first();
            throw_if(empty($card), ValidationException::withMessages(['secret' => '卡密不存在或已使用！']));

            throw_if((! empty($card->expiry_time) && $card->expiry_time < now()), ValidationException::withMessages(['secret' => '卡密已过期！']));

            $user = $request->user('api');

            $card->exchange_time = now();
            $card->used = true;
            $card->user_id = $user->id;
            $card->save();

            $user->vip_id = $card->vip_id;
            $user->end_at = ($user->end_at && $user->end_at > now()) ? Carbon::parse($user->end_at)->addMonths($card->month) : now()->addMonths($card->month);
            $user->save();
        });

        return $this->success();
    }
}
