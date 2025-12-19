<?php

namespace Tests\Feature;

use App\Jobs\SendEmailJobs;
use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_send_mail()
    {
        SendEmailJobs::dispatch('123321@qq.com', '123321');
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
    }

    private function get_admin_token()
    {
        $admin = User::query()->where('username', 'admin')->first();

        return $admin->createToken('api')->plainTextToken;
    }
}
