<?php

namespace Ugly\Base;

use Illuminate\Support\ServiceProvider;
use Ugly\Base\Console\InitCommand;
use Ugly\Base\Console\PaymentExpired;
use Ugly\Base\Support\Config;

class UglyBaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // 注册系统配置服务.
        $this->app->singleton('ugly.config', Config::class);

        if (config('ugly.payment.enable')) {
            $this->commands([PaymentExpired::class]);
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                InitCommand::class,
            ]);
        }
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../config' => config_path()], 'ugly-base-config');
        }
    }
}
