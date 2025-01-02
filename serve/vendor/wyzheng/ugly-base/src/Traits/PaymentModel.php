<?php

namespace Ugly\Base\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Ugly\Base\Enums\PaymentStatus;
use Ugly\Base\Enums\PaymentType;
use Ugly\Base\Events\PaymentFailed;

/**
 *  统一支付.
 *
 * @mixin Model
 */
trait PaymentModel
{
    /**
     * 生成唯一号.
     */
    protected static function generateNo(): string
    {
        [$m, $s] = explode(' ', microtime());

        return date('ymdHis', $s).substr($m, 2).rand(10, 99);
    }

    /**
     * 获取支付通道类.
     *
     * @param  string  $channel 支付通道
     *
     * @throws \Exception
     */
    private static function getChannelClass(string $channel): string
    {
        if (class_exists($channel)) {
            return $channel;
        } else {
            throw new \Exception('支付通道不存在:'.$channel);
        }
    }

    /**
     * 默认创建.
     *
     * @param  Model|Builder|null  $payer 支付者.
     * @param  Model|Builder|null  $merchant 商户.
     */
    private static function defaultCreate(array $data, Model|Builder $payer = null,
        Model|Builder $merchant = null): Model|Builder
    {
        return self::query()->create(array_merge([
            'no' => self::generateNo(),
            'status' => PaymentStatus::Processing,
            'merchant_id' => $merchant?->getKey(),
            'merchant_type' => $merchant?->getMorphClass(),
            'payer_id' => $payer?->getKey(),
            'payer_type' => $payer?->getMorphClass(),
        ], $data));
    }

    /**
     * 创建付款.
     *
     * @param  string  $channel 支付通道.
     * @param  float  $amount 支付金额/元.
     * @param  string  $job 成功后需要执行的任务.
     * @param  Carbon|string|null  $expired_at 过期时间.
     * @param  string|null  $order_no 内部订单号.
     * @param  array  $attach 附加信息.
     * @param  Model|Builder|null  $payer 支付者.
     * @param  Model|Builder|null  $merchant 商户.
     */
    public static function pay(string $channel, float $amount, string $job, string $order_no = null,
        array $attach = [], Carbon|string $expired_at = null,
        Model|Builder $payer = null, Model|Builder $merchant = null): Model|Builder
    {
        $expired_at = $expired_at ?: now()->addMinutes((int) config('ugly.payment.expire'));
        $data = compact('channel', 'amount', 'job', 'expired_at', 'order_no', 'attach');
        $data['type'] = PaymentType::Pay;

        return self::defaultCreate($data, $payer, $merchant);
    }

    /**
     * 支付单退款.
     *
     * @param  float  $amount 退款金额/元.
     * @param  string  $job 退款后需要执行的任务.
     * @param  array  $attach 附加信息.
     * @param  Model|Builder|null  $payer 商户.
     * @param  Model|Builder|null  $merchant 商户.
     *
     * @throws \Exception
     */
    public function refund(float $amount, string $job = '', array $attach = [],
        Model|Builder $payer = null, Model|Builder $merchant = null): Model|Builder
    {
        if (
            $this->getAttribute('status') !== PaymentStatus::Success ||
            $this->getAttribute('type') !== PaymentType::Pay
        ) {
            throw new \Exception('支付单状态不正确！');
        }

        $data = compact('amount', 'job', 'attach');
        $data['type'] = PaymentType::Refund;
        $data['channel'] = $this->getAttribute('channel');
        $data['payment_id'] = $this->getKey();
        $data['order_no'] = $this->getAttribute('order_no');

        return self::defaultCreate($data, $payer, $merchant);
    }

    /**
     * 创建转账单.
     *
     * @param  string  $channel 支付通道.
     * @param  float  $amount 金额/元.
     * @param  string|null  $job 成功后需要执行的任务.
     * @param  array  $attach 附加信息.
     * @param  Model|Builder|null  $payer 收款人.
     * @param  Model|Builder|null  $merchant 商户.
     */
    public static function transfer(string $channel, float $amount, string $job = null, array $attach = [],
        Model|Builder $payer = null, Model|Builder $merchant = null): Model|Builder
    {
        $data = compact('channel', 'amount', 'job', 'attach');
        $data['type'] = PaymentType::Transfer;

        return self::defaultCreate($data, $payer, $merchant);
    }

    /**
     * 发送请求，获取第三方接口响应数据.
     *
     * @throws \Exception
     */
    public function send(array $data = []): mixed
    {
        $channel = self::getChannelClass($this->getAttribute('channel'));
        $method = strtolower($this->getAttribute('type')->name);

        return App::call([new $channel, $method], ['payment' => $this, 'data' => $data]);
    }

    /**
     * 支付成功.
     *
     * @param  string  $no 支付单号.
     * @param  array  $data 其他入库数据.
     */
    public static function success(string $no, array $data = []): void
    {
        DB::transaction(function () use ($no, $data) {
            $payment = self::query()
                ->lockForUpdate()
                ->where('no', $no)
                ->where('status', PaymentStatus::Processing)
                ->first();
            if ($payment) {
                $payment->fill(array_merge([
                    'success_at' => now(),
                    'status' => PaymentStatus::Success,
                ], $data))->save();

                // 成功后需要执行的任务.
                $job = $payment->job;
                if ($job && class_exists($job) && method_exists($job, 'dispatchSync')) {
                    App::call([$job, 'dispatchSync'], ['payment' => $payment]);
                }
            }
        });
    }

    /**
     * 支付失败.
     *
     * @param  string|null  $remark 失败原因.
     * @param  Carbon|string|null  $time 支付失败时间.
     */
    public function fail(string $eventType, string $remark = null, Carbon|string $time = null): void
    {
        $this->setAttribute('fail_at', $time ?: now());
        $this->setAttribute('status', PaymentStatus::Fail);
        $this->setAttribute('remark', $remark);
        $this->save();

        // 触发失败事件.
        PaymentFailed::dispatch($this, $eventType);
    }
}
