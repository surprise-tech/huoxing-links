<?php

namespace Ugly\Base\Tests;

use Illuminate\Http\Request;
use Ugly\Base\Enums\PaymentStatus;
use Ugly\Base\Models\Payment;

class PaymentTestChannel
{
    public function pay($payment, array $data = [])
    {
        return 'pay';
    }

    public function refund($payment, array $data = [])
    {
        return 'refund';
    }

    public function transfer($payment, array $data = [])
    {
        return 'transfer';
    }

    public function notify(Request $request)
    {
        return [];
    }
}

class PaymentTest extends TestCase
{
    private $payment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->payment = Payment::pay(PaymentTestChannel::class, 12.5, 'test', now()->addMinutes(30));
    }

    public function test_create_pay()
    {
        $res = $this->payment->send();
        $this->assertTrue($res === 'pay');
    }

    public function test_create_refund()
    {
        $this->payment->status = PaymentStatus::Success;
        $this->payment->save();
        $res = $this->payment->refund(12.5)->send();
        $this->assertTrue($res === 'refund');
    }

    public function test_create_transfer()
    {
        $res = Payment::transfer(PaymentTestChannel::class, 12.5)->send();
        $this->assertTrue($res === 'transfer');
    }
}
