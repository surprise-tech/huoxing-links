<?php

namespace Ugly\Base\Tests;

use Illuminate\Config\Repository;
use Orchestra\Testbench\TestCase as Orchestra;
use Ugly\Base\UglyBaseServiceProvider;

class TestCase extends Orchestra
{
    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->artisan('migrate');
    }

    protected function defineEnvironment($app)
    {
        tap($app['config'], function (Repository $config) {
            $config->set('ugly.config', ['enable' => true]);
            $config->set('ugly.payment', ['enable' => true]);
        });
    }

    protected function getPackageProviders($app): array
    {
        return [
            UglyBaseServiceProvider::class,
        ];
    }
}
