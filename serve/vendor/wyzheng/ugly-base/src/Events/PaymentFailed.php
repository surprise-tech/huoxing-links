<?php

namespace Ugly\Base\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentFailed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public $payment, public string $eventType)
    {
        //
    }
}
