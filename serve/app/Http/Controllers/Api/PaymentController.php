<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Models\VipPackage;
use Ugly\Base\Models\Payment;
use Ugly\Base\Traits\ApiResource;

class PaymentController extends Controller
{
    use ApiResource;

    public function index()
    {
        $query = Payment::search([
            'status' => '=',
            'payer.username' => 'like',
            'created_at' => 'between',
            'success_at' => 'between',
        ], ['payer:id,username,type'])->orderByDesc('id');

        return $this->paginate($query, PaymentResource::class, [
            'vip' => VipPackage::query()->pluck('name', 'id'),
        ]);
    }
}
