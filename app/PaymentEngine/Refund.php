<?php

namespace App\PaymentEngine;

use Illuminate\Support\Facades\Http;
use App\PaymentEngine\BaseApi;
use Exception;


class Refund extends BaseApi
{
    /**
     * Payment refund
     *
     * @param $paymentRefId
     * @param float $refundAmount
     * @param string $referenceNo
     * @param string $message
     *
     * @return mixed
     */
    public function refund($paymentRefId, $refundAmount, $referenceNo = "", $message = "Requested for refund", $account = 1)
    {
        if ($account == 1)
            $account = null;
        else
            $account = "_$account";
        $paymentDetails = (new Payment())->verify($paymentRefId);
        if (isset($paymentDetails->reason)) {
            throw new Exception($paymentDetails->message);
        }

        if (empty($referenceNo)) {
            $referenceNo = $this->getRandomString(10);
        }

        $sensitiveOrderData = [
            'merchantId' => config("nagad.merchant_id$account"),
            "originalRequestDate" => date("Ymd"),
            'originalAmount' => $paymentDetails->amount,
            'cancelAmount' => $refundAmount,
            'referenceNo' => $referenceNo,
            'referenceMessage' => $message,
        ];

        $response = Http::withHeaders($this->headers())
            ->post($this->baseUrl . "purchase/cancel?paymentRefId={$paymentDetails->paymentRefId}&orderId={$paymentDetails->orderId}", [
                "sensitiveDataCancelRequest" => $this->encryptWithPublicKey(json_encode($sensitiveOrderData), $account),
                "signature" => $this->signatureGenerate(json_encode($sensitiveOrderData), $account)
            ]);

        $responseData = json_decode($response->body());
        if (isset($responseData->reason)) {
            throw new Exception($responseData->message);
        }

        return json_decode($this->decryptDataPrivateKey($responseData->sensitiveData, $account));
    }
}