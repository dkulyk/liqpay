<?php

namespace DKulyk\LiqPay\Model;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = 'liqpay_log';

    protected $fillable
        = [
            'transaction',
            'status',
            'data',
        ];
    protected $casts = ['data' => 'array'];
}