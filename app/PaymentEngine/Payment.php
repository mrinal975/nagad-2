<?php

namespace App\PaymentEngine;

use Illuminate\Support\Facades\Http;
use App\PaymentEngine\BaseApi;
use Carbon\Carbon;
use Exception;

class Payment extends BaseApi
{

    private function initPayment($invoice, $account = null)
    {
        $baseUrl = $this->baseUrl . "check-out/initialize/" . config("nagad.merchant_id$account") . "/{$invoice}" . "?purpose=DIRECT_DEBIT_TOKEN_GEN";
        $sensitiveData = $this->getSensitiveData($invoice, $account);
        $body = [
            "accountNumber" => config("nagad.merchant_number$account"),
            "dateTime" => Carbon::now()->timezone(config("nagad.timezone"))->format('YmdHis'),
            "sensitiveData" => $this->encryptWithPublicKey(json_encode($sensitiveData), $account),
            'signature' => $this->signatureGenerate(json_encode($sensitiveData), $account),
        ];
        $response = Http::withHeaders($this->headers())->post($baseUrl, $body);
        $response = json_decode($response->body());
        if (isset($response->reason)) {
            throw new Exception($response->message);
        }

        return $response;
    }


    public function create($amount, $invoice, $call_back, $account = 1)
    {
        if ($account == 1)
            $account = null;
        else
            $account = "_$account";
        $initialize = $this->initPayment($invoice, $account);
        if ($initialize->sensitiveData && $initialize->signature) {
            $decryptSensitiveData = json_decode($this->decryptDataPrivateKey($initialize->sensitiveData, $account));
            $url = $this->baseUrl . "/check-out/complete/" . $decryptSensitiveData->paymentReferenceId;
            $sensitiveOrderData = [
                'merchantId' => config("nagad.merchant_id$account"),
                'orderId' => $invoice,
                'currencyCode' => '050',
                'amount' => $amount,
                "customerId" => "customer_001",
                'challenge' => $decryptSensitiveData->challenge
            ];

            $response = Http::withHeaders($this->headers())
                ->post($url, [
                    'sensitiveData' => $this->encryptWithPublicKey(json_encode($sensitiveOrderData), $account),
                    'signature' => $this->signatureGenerate(json_encode($sensitiveOrderData), $account),
                    'merchantCallbackURL' => $call_back,
                ]);
            $response = json_decode($response->body());
            if (isset($response->reason)) {
                throw new Exception($response->message);
            }

            return $response;
        }
    }

    public function executePayment($amount, $invoice, $account = 1)
    {
        if ($account == 1)
            $account = null;
        else
            $account = "_$account";
        $response = $this->create($amount, $invoice, $account);
        if ($response->status == "Success") {
            return redirect($response->callBackUrl);
        }
    }

    /**
     * Verify Payment
     *
     * @param string $paymentRefId
     *
     * @return mixed
     */
    public function verify(string $paymentRefId)
    {
        $url = $this->baseUrl . "verify/payment/{$paymentRefId}";
        $response = Http::withHeaders($this->headers())->get($url);
        return json_decode($response->body());
    }

}
