<?php
// event handle class
namespace App\Handler;

use Products;
use Orders;
use Settings;
use Permissions;
use Phalcon\Acl\Adapter\Memory;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

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
        $locale = new \App\Components\Locale();
        $local = $locale->getTranslator();
        $aclFile = '../app/security/acl.cache';
        $aclContent = file_get_contents($aclFile);
        $application = new \Phalcon\Mvc\Application();

        if (!file_exists($aclFile) || (strlen($aclContent) == 0)) {
            $aclContent = new Memory();
            $aclContent->addrole('admin');
            $aclContent->allow('*', '*', '*');
            file_put_contents($aclFile, serialize($aclContent));
            $permission = new Permissions();
            $permission->role = 'admin';
            $permission->controller = '*';
            $permission->action = '*';

            $result = $permission->save();
        }
        if (true === is_file($aclFile)) {
            $acl = unserialize(file_get_contents($aclFile));

            $role = $application->request->get('bearer');
            if ($role) {
                try {

                    $key =  'RwII94n0W/wnXyq5fU3SD6FUFz8IcyYUXjUqpUoCqXg=';
                    $decoded = JWT::decode($role, new Key($key, 'HS256'));
                    $decodeArr = (array)$decoded;
                    $role = $decodeArr['role'];

                    $controller
                        = $application->router->getControllerName() ? $application->router->getControllerName() : 'product';
                    $action
                        = $application->router->getActionName() ? $application->router->getActionName() : 'index';


                    if (!$role || true !== $acl->isAllowed($role, $controller, $action)) {

                        die($local->_('authorised'));
                    }
                } catch (\Exception $e) {
                    echo $e->getMessage();
                    echo $local->_('access');
                    die;
                }
            } else {
                //providing access to login / signup without jwt
                $controller
                    = $application->router->getControllerName();
                if ($controller == 'user') {
                    $role = 'guest';
                    $controller = 'user';
                    $action
                        = $application->router->getActionName() ? $application->router->getActionName() : 'index';
                    if (!$role || true !== $acl->isAllowed($role, $controller, $action)) {
                        die($local->_('authorised'));
                    }
                } else {
                    die($local->_('authorised'));
                }
            }
        } else {
            die($local->_('filenot'));
        }
    }
}
