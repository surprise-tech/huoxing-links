<?php

namespace Ugly\Base\Console;

use Illuminate\Console\Command;
use Ugly\Base\Models\SysConfig;

class InitCommand extends Command
{
    protected $signature = 'ugly:init';

    protected $description = '初始化ugly配置';

    public function handle(): void
    {
        if (config('ugly.config.enable')) {
            $this->initConfig();
        }
        if (config('ugly.payment.enable')) {
            $this->initPayment();
        }

        // 执行迁移
        $this->call('migrate');

        // 执行填充
        if (config('ugly.config.enable')) {
            $default = config('ugly.config.default');
            if (is_array($default) && count($default)) {
                SysConfig::query()->insert(config('ugly.config.default'));
                $this->info('已写入默认配置项');
            }
        }
    }

    /**
     * 初始化系统配置功能.
     */
    private function initConfig(): void
    {
        $this->generateMigration('sys_configs_table');
    }

    /**
     * 初始化支付功能.
     */
    private function initPayment(): void
    {
        $this->generateMigration('payments_table');
    }

    /**
     * 生成迁移文件.
     */
    private function generateMigration(string $name): void
    {
        $exists = glob(database_path('migrations/*_create_'.$name.'.php'));
        if (count($exists)) {
            $this->info('跳过已存在的迁移文件：'.$exists[0]);

            return;
        }
        $path = database_path('migrations/'.date('Y_m_d_His').'_create_'.$name.'.php');
        file_put_contents($path, file_get_contents(__DIR__.'/../../database/migrations/'.$name.'.php'));
        $this->info('生成迁移文件：'.$path);
    }
}
