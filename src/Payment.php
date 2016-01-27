<?php

namespace DKulyk\LiqPay;

class Payment extends Object
{
    protected $liqpay;
    protected $model;
    protected $fields
        = [
            'amount'      => 0,
            'currency'    => null,
            'description' => null,
            'order_id'    => null,
            'type'        => null,
            'result_url'  => null,
            'pay_way'     => null,
            'language'    => null,
            'sandbox'     => null,
        ];

    protected $order;

    public function __construct(LiqPay $liqpay, $order, array $values = [])
    {
        parent::__construct($values);

        $this->liqpay = $liqpay;
        $this->order = $order;
    }

    protected function fields()
    {
        return [
            'amount'      => 0,
            'currency'    => null,
            'description' => null,
            'order_id'    => null,
            'type'        => null,
            'result_url'  => null,
            'pay_way'     => null,
            'language'    => null,
            'sandbox'     => null,
        ];
    }

    public function prepare()
    {
        $this->model = Model\Transaction::create(
            [
                'order' => $this->order,
                'data'  => json_encode($this->toArray(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            ]
        );
        $this['order_id'] = $this->model->getKey();
    }

    public function form()
    {
        $this->prepare();

        return $this->liqpay->form($this->toArray());
    }

}