<?php

use Phalcon\Mvc\Model;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;

class Products extends Model
{
    public $product_id;
    public $name;
    public $description;
    public $tags;
    public $price;
    public $stock;
}
