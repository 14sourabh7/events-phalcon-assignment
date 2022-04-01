<?php



namespace App\Console;

use Phalcon\Cli\Task;
use Firebase\JWT\JWT;
use Settings;
use Products;
use Orders;

class StoreTask extends Task
{
    public function mainAction()
    {
        echo 'This is the default task and the default action' . PHP_EOL;
    }


    /**
     * setsettingsAction($price, $stock)
     * 
     *action to set settings

     * @param [type] $price
     * @param [type] $stock
     * @return void
     */
    public function setsettingsAction($price, $stock)
    {
        $setting = Settings::findFirst("admin_id = '1'");
        $setting->price  = $price;
        $setting->stock = $stock;
        $result = $setting->save();
        echo $result . PHP_EOL;
    }

    /**
     * getstockAction()
     * action to get stocks < 10
     *
     * @return void
     */
    public function getstockAction()
    {
        $products = Products::find(['conditions' => "stock < '10'"]);
        foreach ($products as $product) {
            echo $product->product_id . " " . $product->name . " " . $product->price . " " . $product->stock . "\n";
        }
        echo PHP_EOL;
    }

    /**
     * gettodayorderAction()
     * action to get todays order
     *
     * @return void
     */
    public function gettodayorderAction()
    {
        $date = getDate();
        $currentDate =  $date['year'] . "-" . $date['mon'] . "-" . $date['mday'];
        $orders = Orders::find(['conditions' => "date = '$currentDate'"]);
        foreach ($orders as $order) {
            echo $order->order_id . " " . $order->name . " " . $order->quantity . " " . $order->date . "\n";
        }
        echo PHP_EOL;
    }
}
