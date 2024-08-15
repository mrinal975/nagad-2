<?php

namespace App\Http\Helpers;

use Carbon\Carbon;

trait NagadHelpers
{
    /**
     * @return string|null
     */
    public function getIp()
    {
        return request()->ip();
    }

    /**
     * @param int $length
     *
     * @return string
     */
    public function getRandomString($length = 45)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * @param string $invoice
     *
     * @return array
     */
    public function getSensitiveData(string $invoice, $account = null)
    {
        return [
            'merchantId' => config("nagad.merchant_id$account"),
            'datetime' => Carbon::now(config("nagad.timezone"))->format("YmdHis"),
            'orderId' => $invoice,
            'challenge' => $this->getRandomString()
        ];
    }

    /**
     * @param string $data
     *
     * @return string
     * @throws InvalidPublicKey
     */
    public function encryptWithPublicKey(string $data, $account = null)
    {
        $publicKey = "-----BEGIN PUBLIC KEY-----\n" . config("nagad.public_key$account") . "\n-----END PUBLIC KEY-----";
        $keyResource = openssl_get_publickey($publicKey);
        $status = openssl_public_encrypt($data, $cryptoText, $keyResource);
        if ($status) {
            return base64_encode($cryptoText);
        } else {
            throw new ('Invalid Public key');
        }
    }

    /**
     * @param string $data
     *
     * @return mixed
     */
    public static function decryptDataPrivateKey(string $data, $account = null)
    {
        $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" . config("nagad.private_key$account") . "\n-----END RSA PRIVATE KEY-----";
        openssl_private_decrypt(base64_decode($data), $plain_text, $private_key);
        return $plain_text;
    }

    /**
     * @param string $data
     *
     * @return string
     * @throws InvalidPrivateKey
     */
    public function signatureGenerate(string $data, $account = null)
    {
        $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" . config("nagad.private_key$account") . "\n-----END RSA PRIVATE KEY-----";
        $status = openssl_sign($data, $signature, $private_key, OPENSSL_ALGO_SHA256);
        if ($status) {
            return base64_encode($signature);
        } else {
            throw new ('Invalid private key');
        }

    }

    protected function headers()
    {
        return [
            "Content-Type" => "application/json",
            "X-KM-IP-V4" => $this->getIp(),
            "X-KM-Api-Version" => "v-4.0.1",
            "X-KM-Client-Type" => "PC_WEB"
        ];
    }
}
