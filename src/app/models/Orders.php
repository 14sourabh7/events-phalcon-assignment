<?php

use Phalcon\Mvc\Model;

class Orders extends Model
{
    public $order_id;
    public $name;
    public $address;
    public $zip;
    public $product;
    public $quantity;
    public $date;

    public function getOrders()
    {
        return Orders::find();
    }
}
