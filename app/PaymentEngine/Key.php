<?php

namespace App\PaymentEngine;

class Key
{
    public function generate()
    {
        $privateKey = openssl_pkey_new(
            array(
                "private_key_bits" => 2048,
                "private_key_type" => OPENSSL_KEYTYPE_RSA,
            )
        );

        // Extract the private key from $privateKey to $privateKeyStr
        openssl_pkey_export($privateKey, $privateKeyStr);

        // Generate a new public key
        $publicKey = openssl_pkey_get_details($privateKey)["key"];
        $key = [
            'public_key' => $publicKey,
            'private_key' => $privateKeyStr
        ];
        return $key;
    }
}