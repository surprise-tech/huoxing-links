<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Resources\UserInfoResource;
use App\Models\Link;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        User::query()->create($data);

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
            $user->link_amount = $user->link_total = $user->link_created = 0;

            $link_count = Link::query()->where('user_id', $user->id)->count();
            $link_expire = Link::query()->where('user_id', $user->id)->where('expired_at', '<', now())->count();
            $user->link_created = $link_count;
            $user->link_expire = $link_expire;
        }
        $user->login_time = $user->currentAccessToken()->created_at;

        return $this->success(UserInfoResource::make($user));
    }
}
