<?php

namespace Tests\Feature;

use App\Enums\CommissionType;
use App\Jobs\SendEmailJobs;
use App\Models\User;
use App\Models\VipPackage;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_send_mail()
    {
        SendEmailJobs::dispatch('724323954@qq.com', '123321');
    }

    public function test_get_token()
    {
        dd($this->get_admin_token());
    }

    // 注册
    public function test_register()
    {
        $agent = User::query()->where('id', 2)->first();
        $referral_code = $agent->referral_code;
        $phone = fake('zh_CN')->phoneNumber();

        $response = $this
            ->postJson('/api/register', [
                'username' => $phone,
                'password' => '123456',
                'password_confirmation' => '123456',
                'captcha' => '6666',
                'referral_code' => $referral_code ?? null,
            ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'username' => $phone,
        ]);
        if (! empty($referral_code)) {
            $child = User::query()->where('parent_id', $agent->id)->orderByDesc('id')->first();
            $this->assertDatabaseHas('commission_logs', [
                'user_id' => $agent->id,
                'type' => CommissionType::Rebates->value,
                'children_user_id' => $child->id,
            ]);
        }
    }

    private function get_admin_token()
    {
        $admin = User::query()->where('username', 'admin')->first();

        return $admin->createToken('api')->plainTextToken;
    }

    public function test_user_package_fix(): void
    {
        $token = $this->get_admin_token();

        $user = User::query()
            ->inRandomOrder()
            ->where('id', 2)
            ->first();
        $id = $user->id;
        $u_vip_id = $user->vip_id;
        $vip = VipPackage::query()
            ->when(! empty($u_vip_id), function ($query) use ($u_vip_id) {
                $query->whereNot('id', $u_vip_id);
            })
            ->inRandomOrder()
            ->first();

        $response = $this
            ->withHeaders([
                'Authorization' => "Bearer {$token}",
            ])
            ->putJson("/api/users/{$id}", [
                'vip_id' => $vip->id,
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'vip_id' => $vip->id,
            // 'end_at' => '', // TODO
        ]);
    }
}
