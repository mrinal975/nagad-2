<?php

namespace App\Services;

use App\Facade\NagadPayment;
use App\Facade\NagadRefund;
use App\Services\Interfaces\NagadPaymentServiceInterface;

class NagadPaymentService implements NagadPaymentServiceInterface
{

    public function payment($amount, $trxId, $callback)
    {
        $response = NagadPayment::create($amount, $trxId, $callback);
        return $response;
    }

    public function refundPayment($paymentRefId, $refundAmount)
    {
        $verify = NagadRefund::refund($paymentRefId, $refundAmount);
    }
}
