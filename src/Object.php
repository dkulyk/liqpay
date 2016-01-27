<?php

namespace DKulyk\LiqPay;

class Object implements \ArrayAccess
{
    protected $fields = [];
    protected $data = [];

    public function __construct(array $values = [])
    {
        $this->data = $this->fields();
        array_walk(
            $values,
            function ($value, $field) {
                $this[$field] = $value;
            }
        );
    }

    protected function fields()
    {
        return [];
    }

    public function toArray()
    {
        return $this->data;
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->data);
    }

    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->data[$offset];
        }
        throw new \Exception('Filed '.$offset.' not found in class '.get_class($this));
    }

    public function offsetSet($offset, $value)
    {
        if ($this->offsetExists($offset)) {
            $this->data[$offset] = $value;
        } else {
            throw new \Exception('Filed '.$offset.' not found in class '.get_class($this));
        }
    }

    public function offsetUnset($offset)
    {
        throw new \Exception('Not implemented');
    }

    public function __get($offset)
    {
        return $this->offsetGet($offset);
    }

    public function __set($offset, $value)
    {
        $this->offsetSet($offset, $value);
    }
}