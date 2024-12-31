<?php

namespace App\PayChannels;

use App\Services\SystemConfig;
use EasyWeChat\Pay\Application;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Ugly\Base\Exceptions\ApiCustomError;
use Ugly\Base\Models\Payment;
use Ugly\Base\Traits\ApiResource;

class WeChatPayNative
{
    use ApiResource;

    private Application $payApp;

    public function __construct()
    {
        $config = [
            'appid' => SystemConfig::get('wechat_pay_app_id'),
            'mch_id' => SystemConfig::get('wechat_pay_mch_id'),
            'secret_key' => SystemConfig::get('wechat_pay_secret_key'),
            // 商户证书
            'private_key' => SystemConfig::get('wechat_pay_secret_key'),
            'certificate' => SystemConfig::get('wechat_pay_certificate'),
            /*'platform_certs' => [
                storage_path('/certs/wechatpay.pem'),
            ],*/
            'http' => [
                'throw' => false,
            ],
        ];
        $this->payApp = new Application($config);
    }

    // 支付
    public function pay($payment, array $data = []): array
    {
        $payInfo = $this->payApp->getClient()->postJson('v3/pay/transactions/native', [
            'appid' => config('services.wechat_pay.appid'),
            'mchid' => config('services.wechat_pay.mch_id'),
            'description' => data_get($data, 'description', ''),
            'out_trade_no' => $payment->no,
            'notify_url' => config('app.url').'/api/wechat/payment_notify',
            'time_expire' => $payment->expired_at->toRfc3339String(),
            'amount' => [
                'total' => (int) bcmul($payment->amount, 100, 0),
                'currency' => 'CNY',
            ],
        ])->toArray();

        throw_if(
            ! isset($payInfo['code_url']),
            new ApiCustomError(data_get($payInfo, 'message', '微信下单失败！'), Response::HTTP_INTERNAL_SERVER_ERROR, Response::HTTP_INTERNAL_SERVER_ERROR)
        );

        return [
            'no' => $payment->no,
            'amount' => $payment->amount,
            'desc' => $data['description'] ?? '',
            'expired_at' => $payment->expired_at->toDateTimeString(),
            'code_url' => $payInfo['code_url'],
        ];
    }

    // 成功后通知.
    public function notify()
    {
        $server = $this->payApp->getServer();

        // 支付成功
        $server->handlePaid(function ($message) {
            Payment::success($message['out_trade_no'], [
                'notification_no' => $message['transaction_id'],
                'notification_data' => $message,
                'success_at' => Carbon::parse($message['success_time']),
            ]);
        });

        // 退款成功
        $server->handleRefunded(function ($message) {
            if ($message['refund_status'] === 'SUCCESS') {
                Payment::success($message['out_trade_no'], [
                    'notification_no' => $message['refund_id'],
                    'notification_data' => $message,
                    'success_at' => Carbon::parse($message['success_time']),
                ]);
            }
        });

        return $server->serve();
    }
}
