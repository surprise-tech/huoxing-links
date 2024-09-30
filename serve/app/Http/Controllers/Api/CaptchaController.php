<?php

namespace App\Http\Controllers\Api;

use App\Enums\CodeMode;
use App\Http\Controllers\Controller;
use App\Http\Requests\SMSCaptchaRequest;
use App\Jobs\SendEmailJobs;
use App\Services\AliDySms;
use App\Services\SystemConfig;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Ugly\Base\Traits\ApiResource;

class CaptchaController extends Controller
{
    use ApiResource;

    // 图片验证码
    public function image(): JsonResponse
    {
        return $this->success(app('captcha')->create('math', true));
    }

    // 短信验证码
    public function sms(SMSCaptchaRequest $request): JsonResponse
    {
        $tel = $request->input('tel');
        $captcha = Cache::get('sms_captcha_'.$tel);
        if ($captcha) {
            return $this->failed('请勿重复发送！');
        }
        $captcha = rand(1000, 9999);
        $code_mode = SystemConfig::get('send_code_mode');

        if ($code_mode == CodeMode::SMS->value) {
            // 短信验证码
            (new AliDySms)->captcha($captcha, $tel);
        } elseif ($code_mode == CodeMode::Email->value) {
            // 邮箱验证码
            SendEmailJobs::dispatch($tel, $captcha);
        }

        Cache::put('sms_captcha_'.$tel, $captcha, now()->addMinute());

        return $this->success([]);
    }
}
