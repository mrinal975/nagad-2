<?php

namespace App\Facade;

use Illuminate\Support\Facades\Facade;

class Key extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'generate';
    }
}