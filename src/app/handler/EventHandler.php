<?php
// event handle class
namespace App\Handler;

use Products;
use Orders;
use Settings;
use Phalcon\Acl\Adapter\Memory;
use Phalcon\Acl\Role;
use Phalcon\Acl\Component;

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
        $logger = new \App\Components\MyLogger();

        $setting = Settings::findFirst('admin_id=1');
        $product = Products::findFirst(['order' => 'product_id DESC']);

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
        $logger = new \App\Components\MyLogger();
        $setting = Settings::findFirst('admin_id=1');
        $order = Orders::findFirst(['order' => 'order_id DESC']);

        if ($order->zip == 0) {
            $order->zip = $setting->zipcode;
        }

        $order->update();
        $logger->log("order updated");
    }


    public function beforeHandleRequest()
    {
        $aclFile = '../app/security/acl.cache';
        $application = new \Phalcon\Mvc\Application();
        if (true === is_file($aclFile)) {
            $acl = unserialize(file_get_contents($aclFile));

            $role = $application->request->get('role');
            $controller
                = $application->router->getControllerName();
            $action
                = $application->router->getActionName() ? $application->router->getActionName() : 'index';


            if (!$role || true !== $acl->isAllowed($role, $controller, $action)) {
                die('You are not authorised');
            }
        } else {
            die('file not found');
        }
    }
}
