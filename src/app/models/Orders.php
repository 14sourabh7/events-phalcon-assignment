<?php

use Phalcon\Mvc\Model;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\Stream;

class Orders extends Model
{
    public $order_id;
    public $name;
    public $address;
    public $zip;
    public $product;
    public $quantity;
}
