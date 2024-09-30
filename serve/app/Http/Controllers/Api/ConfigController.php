<?php

namespace App\Http\Controllers\Api;

use App\Forms\BaseConfig;
use Ugly\Base\Http\Controllers\SimpleFormController;

class ConfigController extends SimpleFormController
{
    protected array $form = [
        'base' => BaseConfig::class,
    ];
}
