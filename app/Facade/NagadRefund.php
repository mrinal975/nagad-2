<?php

namespace App\Facade;

use Illuminate\Support\Facades\Facade;

class NagadRefund extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'refundPayment';
    }
}
