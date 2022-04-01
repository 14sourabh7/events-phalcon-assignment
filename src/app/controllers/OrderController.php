<?php

use Phalcon\Mvc\Controller;


class OrderController extends Controller
{
    public function indexAction()
    {
        $eventManager = $this->di->get('EventsManager');
        $this->view->locale = $this->locale;
        // $eventManager->fire('application:beforeHandleRequest', $this);
        $order = new Orders();
        $this->view->orders = $order->getOrders();
    }

    public function addAction()
    {
        $date = getDate();
        $currentDate =  $date['year'] . "-" . $date['mon'] . "-" . $date['mday'];
        $eventManager = $this->di->get('EventsManager');
        $this->view->locale = $this->locale;
        // $eventManager->fire('application:beforeHandleRequest', $this);
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
                                'date' => $currentDate
                            ];

                            $order = new Orders();
                            $order->assign(
                                $orderArr,
                                [
                                    'name', 'address', 'zip', 'product', 'quantity', 'date'
                                ]
                            );
                            $success = $order->save();

                            if ($success) {

                                $this->response->redirect("/order?bearer=" . $_GET['bearer'] . "&locale=" . $_GET['locale']);
                            }
                        } else {
                            $this->view->errorMessage = $this->locale->_('er1');
                        }
                    } else {
                        $orderArr = [
                            'name' => $escaper->sanitize($inputs['name']),
                            'address' => $escaper->sanitize($inputs['address']),
                            'zip' => 0,
                            'product' => $escaper->sanitize($inputs['product']),
                            'quantity' => $escaper->sanitize($inputs['quantity']),
                            'date' => $currentDate
                        ];

                        $order = new Orders();
                        $order->assign(
                            $orderArr,
                            [
                                'name', 'address', 'zip', 'product', 'quantity', 'date'
                            ]
                        );
                        $success = $order->save();
                        if ($success) {
                            $eventManager = $this->di->get('EventsManager');
                            $eventManager->fire('order:orderSave', $this);
                            $this->response->redirect("/order?bearer=" . $_GET['bearer'] . "&locale=" . $_GET['locale']);
                        }
                    }
                } else {
                    $this->view->errorMessage =
                        $this->locale->_('er2');
                }
            } else {
                $this->view->errorMessage
                    = $this->locale->_('er3');
            }
        }
    }
}
