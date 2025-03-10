<?php

namespace Vrajroham\LaravelBitpay;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Route;


/**
 * LaravelBitpay Facade.
 */
class LaravelBitpay extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return LaravelBitpayClient::class;
    }
}
