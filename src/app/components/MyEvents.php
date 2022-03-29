<?php

namespace App\Components;

use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;

class Myevents
{
    public $product;
    public $setting;
    public $order;
    public function productSave($product, $setting)
    {
        $this->product = $product;
        $this->setting = $setting;
        $eventsManager = new EventsManager();

        $eventsManager->attach('product:afterSave', function () {
            $logger = new \App\Components\MyLogger();
            $setting = $this->setting;
            if ($this->product->price == 0 || $this->product->stock == 0) {
                if ($this->product->stock == 0) {
                    $this->product->stock = $setting->stock;
                }
                if ($this->product->price == 0) {
                    $this->product->price = $setting->price;
                }
                if ($setting->title == 'with') {

                    $name = $this->product->name . " " . $this->product->tags;

                    $this->product->name = $name;
                }
                $this->product->update();
                $logger->log("product updated");
            }
        });
        return $eventsManager;
    }

    public function orderSave($order, $setting)
    {
        $this->order = $order;
        $this->setting = $setting;
        $logger = new \App\Components\MyLogger();
        $eventsManager = new EventsManager();

        $eventsManager->attach(
            'order:afterSave',
            function () {
                $logger = new \App\Components\MyLogger();
                $setting = $this->setting;
                if ($this->order->zip == 0) {
                    $this->order->zip = $setting->zipcode;
                }
                $this->order->update();
                $logger->log("order updated");
            }
        );
        return $eventsManager;
    }
}
