<?php

namespace DKulyk\LiqPay\Facades;

use Illuminate\Support\Facades\Facade;

class LiqPay extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'liqpay';
    }
}