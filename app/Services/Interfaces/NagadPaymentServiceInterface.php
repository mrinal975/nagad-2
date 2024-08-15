<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

interface NagadPaymentServiceInterface
{

    public function payment(float $amount, string $trxId, string $callback);


    public function refundPayment(string $paymentRefId, string $refundAmount);
}