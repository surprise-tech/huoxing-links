<?php

namespace App\Http\Controllers\Api;

use App\Forms\BaseConfig;
use App\Forms\PaymentSet;
use App\Forms\VerifyCode;
use App\Forms\WebSite;
use Ugly\Base\Http\Controllers\SimpleFormController;

class ConfigController extends SimpleFormController
{
    protected array $form = [
        'base' => BaseConfig::class,
        'web_site' => WebSite::class,
        'payment' => PaymentSet::class,
        'verify_code' => VerifyCode::class,
    ];
}
