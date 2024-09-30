<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // 微信支付
    'wechat_pay' => [
        'appid' => env('WECHAT_PAY_APP_ID'),
        'mch_id' => env('WECHAT_PAY_MCH_ID'),
        'secret_key' => env('WECHAT_PAY_SECRET_KEY'),
        // 商户证书
        'private_key' => storage_path('/certs/apiclient_key.pem'),
        'certificate' => storage_path('/certs/apiclient_cert.pem'),
        'platform_certs' => [
            storage_path('/certs/wechatpay.pem'),
        ],
        'http' => [
            'throw' => false,
        ],
    ],
    // 阿里短信配置
    'ali_sms' => [
        'key' => env('ALI_SMS_KEY'),
        'secret' => env('ALI_SMS_SECRET'),
        'sign_name' => env('ALI_SMS_SIGN_NAME'),
        'test_code' => env('ALI_SMS_TEST_CODE'),
    ],
];
