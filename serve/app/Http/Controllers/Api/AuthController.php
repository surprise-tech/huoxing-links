<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserType;
use App\Enums\VipStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Resources\UserInfoResource;
use App\Jobs\PayVip;
use App\Models\Link;
use App\Models\User;
use App\Models\VipLogs;
use App\Models\VipPackage;
use App\PayChannels\WeChatPayNative;
use App\Services\SystemConfig;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ugly\Base\Models\Payment;
use Ugly\Base\Traits\ApiResource;

class AuthController extends Controller
{
    use ApiResource;

    // 登录
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::query()->where('username', $request->input('username'))->first();

        if (empty($user) || ! password_verify($request->input('password'), $user->password)) {
            return $this->failed('账号密码不正确！');
        }
        if (empty($user->status)) {
            return $this->failed('账号被禁用');
        }

        return $this->success([
            'token' => $user->createToken('api')->plainTextToken,
        ]);
    }

    // 注册
    public function register(RegisterRequest $request): JsonResponse
    {
        $referral_code = $request->input('referral_code');
        $data = [
            'username' => $request->input('username'),
            'password' => bcrypt($request->input('password')),
            'type' => UserType::MEMBER,
            'status' => true,
        ];
        if ($referral_code) {
            $agent = User::query()->where('referral_code', $referral_code)->first();
            $data['parent_id'] = $agent->id;
        }
        // 会员注册
        $user = User::query()->create($data);

        if (SystemConfig::get('is_give_vip')) {
            // 赠送体验会员
            $vip_days = (int) SystemConfig::get('give_vip_days');
            $vip_id = (int) SystemConfig::get('give_vip_id');
            if ($vip_days && $vip_id) {
                $log = VipLogs::query()->create([
                    'user_id' => $user->id,
                    'vip_id' => $vip_id,
                    'status' => VipStatus::ACTIVE,
                    'start_at' => now(),
                    'end_at' => now()->addDays($vip_days),
                ]);
                $user->vip_id = $vip_id;
                $user->start_at = $log->start_at;
                $user->end_at = $log->end_at;
                $user->save();
            }
        }

        return $this->success();
    }

    // 重置密码
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        User::query()->where('username', $request->input('username'))
            ->update(['password' => bcrypt($request->input('password'))]);

        return $this->success();
    }

    // 修改密码
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = $request->user('api');
        $user->password = bcrypt($request->input('password'));
        $user->save();

        return $this->success();
    }

    // 用户信息
    public function userInfo(Request $request): JsonResponse
    {
        $user = $request->user('api');
        if ($user->type === UserType::MEMBER) {
            $user->load('vipPackage:id,name,config');
            $user->link_amount = $user->link_total = $user->link_created = 0;
            $config = $user->vipPackage['config'] ?? null;
            if ($config) {
                $link_count = Link::query()->where('user_id', $user->id)->count();
                $user->link_total = (int) $config['count_limit'];
                $user->link_created = $link_count;
                $user->link_amount = (int) bcsub($config['count_limit'], $link_count);
            }
        }
        $user->login_time = $user->currentAccessToken()->created_at;

        return $this->success(UserInfoResource::make($user));
    }

    // 购买会员
    public function buyVip(Request $request): JsonResponse
    {
        $user = $request->user('api');
        $vip = VipPackage::query()->findOrFail($request->input('vip_id'));

        if (SystemConfig::get('wechat_pay_app_id') == null ||
            SystemConfig::get('wechat_pay_mch_id') == null ||
            SystemConfig::get('wechat_pay_secret_key') == null ||
            SystemConfig::get('wechat_pay_secret_key') == null ||
            SystemConfig::get('wechat_pay_certificate') == null) {
            return $this->failed('清设置支付信息');
        }
        try {
            $res = Payment::pay(
                channel: WeChatPayNative::class,
                amount: $vip->price,
                job: PayVip::class,
                attach: [
                    'vip_id' => $vip->id,
                    // 生效类型：1-立即生效 2-延期生效
                    'type' => $request->integer('type', 1),
                ],
                payer: $user
            )->send(['description' => '购买会员']);

            return $this->success($res);
        } catch (\Exception $e) {
            info($e->getMessage());

            return $this->failed('支付失败!');
        }
    }
}
