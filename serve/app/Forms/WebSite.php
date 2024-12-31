<?php

namespace App\Forms;

use App\Services\SystemConfig;
use Illuminate\Http\Request;
use Ugly\Base\Contracts\SimpleForm;

class WebSite implements SimpleForm
{
    public function policy(Request $request): array
    {
        return $request->validate([
            'web_site_customer_service' => 'nullable',
            'web_site_title' => 'nullable',
            'web_site_logo' => 'nullable',
            'web_site_bottom_logo' => 'nullable',
        ]);
    }

    // 保存
    public function handle(array $input): void
    {
        SystemConfig::set($input);
    }

    public function default(): array
    {
        return [
            'web_site_customer_service' => SystemConfig::get('web_site_customer_service'),
            'web_site_title' => SystemConfig::get('web_site_title'),
            'web_site_logo' => SystemConfig::get('web_site_logo'),
            'web_site_bottom_logo' => SystemConfig::get('web_site_bottom_logo'),
        ];
    }
}
