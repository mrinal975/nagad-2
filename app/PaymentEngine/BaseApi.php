<?php

namespace App\PaymentEngine;

use App\Http\Helpers\NagadHelpers;

class BaseApi
{
    use NagadHelpers;

    /**
     * @var string $baseUrl
     */
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl();
    }

    /**
     * Nagad Base Url
     * if sandbox is true it will be sandbox url otherwise it is host url
     */
    private function baseUrl()
    {
        $this->baseUrl = 'http://sandbox.mynagad.com:10080/remote-payment-gateway-1.0/api/dfs/';
        if (env('APP_ENV') == 'production') {
            $this->baseUrl = 'https://api.mynagad.com/api/dfs/';
        }
    }

    /**
     * Nagad Request Headers
     *
     * @return array
     */
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
