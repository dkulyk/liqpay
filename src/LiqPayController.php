<?php

namespace DKulyk\LiqPay;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LiqPayController extends Controller
{
    public function callback(Application $app, Request $request)
    {
        /**
         * @var LiqPay $liqPay
         */
        $liqPay = $app->make('liqpay');

        return $liqPay->callback($request->input('data'), $request->input('signature'));
    }
}