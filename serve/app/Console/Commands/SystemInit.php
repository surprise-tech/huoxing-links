<?php

namespace App\Console\Commands;

use App\Enums\UserType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SystemInit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:system-init';

    /**
     * The console command desc.
     *
     * @var string
     */
    protected $desc = '系统初始数据';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (DB::table('sys_configs')->count() > 0) {
            echo '重复操作，已存在数据！！';

            return Command::FAILURE;
        }
        // 初始配置
        DB::table('sys_configs')->insert($this->config_data());
        DB::table('users')->insert([
            'username' => 'admin',
            'password' => bcrypt('admin123'),
            'type' => UserType::Admin,
            'referral_code' => '100000',
            'status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        // 套餐
        DB::unprepared(file_get_contents(database_path('packages.sql')));

        return Command::SUCCESS;
    }

    private function config_data(): array
    {
        return [
            [
                'slug' => 'ali_sms_key',
                'value' => '',
                'desc' => '阿里短信key',
            ],
            [
                'slug' => 'ali_sms_secret',
                'value' => '',
                'desc' => '阿里短信secret',
            ],
            [
                'slug' => 'ali_sms_sign_name',
                'value' => '',
                'desc' => '阿里短信签名',
            ],
            [
                'slug' => 'mail_from_address',
                'value' => '',
                'desc' => '发信地址',
            ],
            [
                'slug' => 'mail_from_name',
                'value' => '',
                'desc' => '发信名称',
            ],
            [
                'slug' => 'mail_host',
                'value' => '',
                'desc' => '服务器地址',
            ],
            [
                'slug' => 'mail_password',
                'value' => '',
                'desc' => '发信密码',
            ],
            [
                'slug' => 'mail_port',
                'value' => '',
                'desc' => '端口',
            ],
            [
                'slug' => 'mail_username',
                'value' => '',
                'desc' => '发信账号',
            ],
            [
                'slug' => 'send_code_mode',
                'value' => '2',
                'desc' => '发送验证码类型',
            ],
            [
                'slug' => 'verify_code_is_open',
                'value' => '0',
                'desc' => '是否开启验证码',
            ],
            [
                'slug' => 'web_site_bottom_logo',
                'value' => '/image/toplogo.png',
                'desc' => '网站logo深色',
            ],
            [
                'slug' => 'web_site_customer_service',
                'value' => '/image/web-qr.png',
                'desc' => '客服二维码',
            ],
            [
                'slug' => 'web_site_logo',
                'value' => '/image/toplogo.png',
                'desc' => '网站logo浅色',
            ],
            [
                'slug' => 'web_site_title',
                'value' => '蚂蚁快链-智慧引流工具',
                'desc' => '网站名称',
            ],
        ];
    }
}
