<?php

use Phalcon\Mvc\Controller;


class OrderController extends Controller
{
    public function indexAction()
    {
        $this->view->orders = Orders::find();
    }
    public function addorderAction()
    {
        $this->view->products = Products::find();

        $escaper = new \App\Components\MyEscaper();
        $checkPost = $this->request->isPost();
        $this->view->errorMessage = "";
        if ($checkPost) {

            $inputs = $this->request->getPost();

            if ($inputs['name'] && $inputs['address'] && $inputs['quantity'] && $inputs['product']) {
                if (is_numeric($inputs['quantity'])) {
                    if ($inputs['zip']) {
                        if (is_numeric($inputs['zip'])) {
                            $zip = $escaper->sanitize($inputs['zip']);
                            $orderArr = [
                                'name' => $escaper->sanitize($inputs['name']),
                                'address' => $escaper->sanitize($inputs['address']),
                                'zip' => $zip,
                                'product' => $escaper->sanitize($inputs['product']),
                                'quantity' => $escaper->sanitize($inputs['quantity']),
                            ];

                            $order = new Orders();
                            $order->assign(
                                $orderArr,
                                [
                                    'name', 'address', 'zip', 'product', 'quantity'
                                ]
                            );
                            $success = $order->save();
                            if ($success) {
                                $this->response->redirect('/order');
                            }
                        } else {
                            $this->view->errorMessage = '*zipcode must be numeric';
                        }
                    } else {
                        $orderArr = [
                            'name' => $escaper->sanitize($inputs['name']),
                            'address' => $escaper->sanitize($inputs['address']),
                            'zip' => 0,
                            'product' => $escaper->sanitize($inputs['product']),
                            'quantity' => $escaper->sanitize($inputs['quantity']),
                        ];

                        $order = new Orders();
                        $order->assign(
                            $orderArr,
                            [
                                'name', 'address', 'zip', 'product', 'quantity'
                            ]
                        );
                        $success = $order->save();
                        if ($success) {
                            $setting = Settings::findFirst('admin_id=1');
                            $event = new \App\Components\MyEvents();
                            $event->orderSave($order, $setting)->fire('order:afterSave', $this);
                            $this->response->redirect('/order');
                        }
                    }
                } else {
                    $this->view->errorMessage = '*quantity must be numeric';
                }
            } else {
                $this->view->errorMessage = '*only zip can be left blank';
            }
        }
    }
}
