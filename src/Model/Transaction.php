<?php

namespace DKulyk\LiqPay\Model;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'liqpay_transactions';
    protected $fillable = ['order', 'data'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function log()
    {
        return $this->hasMany('LiqPay\Model\Log', 'transaction');
    }

    public function callback($json)
    {
        $this->log()->create(
            [
                'status' => $json['status'],
                'data'   => $json,
            ]
        );
    }
}