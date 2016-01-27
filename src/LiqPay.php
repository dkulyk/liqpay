<?php

namespace DKulyk\LiqPay;

use Illuminate\Support\Arr;
use InvalidArgumentException;

class LiqPay
{
    const VERSION = 3;
    private $_public_key;
    private $_private_key;
    private $_api_url = 'https://www.liqpay.com/api/';
    private $_checkout_url = 'https://www.liqpay.com/api/checkout';
    protected $_supportedCurrencies = ['EUR', 'UAH', 'USD', 'RUB', 'RUR'];
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
        $this->_public_key = $this->config('public_key');
        $this->_private_key = $this->config('private_key');
        if (empty($this->_public_key)) {
            throw new InvalidArgumentException('public_key is empty');
        }

        if (empty($this->_private_key)) {
            throw new InvalidArgumentException('private_key is empty');
        }
    }

    public function config($param, $default = null)
    {
        return Arr::get($this->config, $param, $default);
    }

    public function create($order, array $values = [])
    {
        return new Payment($this, $order, $values);
    }

    public function prepare(array $params)
    {
        $params['version'] = self::VERSION;
        $params['public_key'] = $this->_public_key;
        $params['server_url'] = route('liqpay.callback');
        foreach (['currency', 'type', 'result_url', 'pay_way', 'language', 'sandbox'] as $field) {
            if (empty($params[$field])) {
                $params[$field] = $this->config($field);
            }
        }

        return $params;
    }

    public function buildData($params)
    {
        $params = $this->prepare($params);
        $data = base64_encode(json_encode($params));
        $signature = $this->signStr($data);

        return [
            'raw'       => $params,
            'action'    => $this->_checkout_url,
            'data'      => $data,
            'signature' => $signature,
        ];
    }

    /**
     * cnb_form
     *
     * @param array $params
     *
     * @return string
     */
    public function form($params)
    {
        $data = $this->buildData($params);

        return sprintf(
            '
            <form method="POST" action="%s" accept-charset="utf-8">
                %s
                %s
                <input type="image" src="//static.liqpay.com/buttons/p1%s.radius.png" name="btn_text" />
            </form>
            ',
            $data['action'],
            sprintf('<input type="hidden" name="%s" value="%s" />', 'data', $data['data']),
            sprintf('<input type="hidden" name="%s" value="%s" />', 'signature', $data['signature']),
            $params['language']
        );
    }

    /**
     * Call API
     *
     * @param string $path
     * @param array  $params
     *
     * @return string
     */
    public function api($path, $params = [])
    {
        $url = $this->_api_url.$path;
        $postFields = http_build_query($this->buildData($params));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $server_output = curl_exec($ch);
        curl_close($ch);

        return json_decode($server_output);
    }

    protected function signStr($str)
    {
        return base64_encode(sha1($this->_private_key.$str.$this->_private_key, 1));
    }

    public function callback($data, $signature)
    {
        $json = json_decode(base64_decode($data), true);
        if ($this->signStr($data) !== $signature) {
            \Event::fire('liqpay.error', []);

            return ['error' => 'wrong signature'];
        }
        /**
         * @var Model\Transaction $order
         */
        $order = empty($json['order_id']) ? null : $order = Model\Transaction::query()->find($json['order_id']);
        if ($order) {
            $order->callback($json);
        } else {
            Model\Log::create(
                [
                    'status' => $json['status'],
                    'data'   => $json,
                ]
            );
        }
        \Event::fire('liqpay.callback', [$order, $json]);
        \Event::fire('liqpay.'.$json['status'], [$order, $json]);

        return [];
    }
}