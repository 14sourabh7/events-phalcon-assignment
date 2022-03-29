<?php
// event handle class
namespace App\Handler;

use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;
use Products;
use Orders;
use Settings;

class EventHandler
{

    /**
     * productSave()
     *
     * this event function triggers once the product is saved
     * @param [type] $product
     * @param [type] $setting
     * @return void
     */
    public function productSave()
    {

        // $this->setting = $setting;

        $eventsManager = new EventsManager();

        $eventsManager->attach('product:afterSave', function () {

            $logger = new \App\Components\MyLogger();

            $setting = Settings::findFirst('admin_id=1');
            $product = Products::findFirst(['order' => 'product_id DESC']);

            if ($product->price == 0 || $product->stock == 0) {

                if ($product->stock == 0) {
                    $product->stock = $setting->stock;
                }

                if ($product->price == 0) {
                    $product->price = $setting->price;
                }

                if ($setting->title == 'with') {
                    $name = $product->name . " " . $product->tags;
                    $product->name = $name;
                }

                $product->update();
                $logger->log("product updated");
            }
        });
        return $eventsManager;
    }

    /**
     * orderSave($order, $setting)
     * 
     * this event function triggers when the order is saved
     *
     * @param [type] $order
     * @param [type] $setting
     * @return void
     */
    public function orderSave()
    {
        $eventsManager = new EventsManager();

        $eventsManager->attach(
            'order:afterSave',
            function () {
                $logger = new \App\Components\MyLogger();
                $setting = Settings::findFirst('admin_id=1');
                $order = Orders::findFirst(['order' => 'order_id DESC']);

                if ($order->zip == 0) {
                    $order->zip = $setting->zipcode;
                }

                $order->update();
                $logger->log("order updated");
            }
        );
        return $eventsManager;
    }
}
