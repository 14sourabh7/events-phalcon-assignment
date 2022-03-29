<?php

use Phalcon\Mvc\Controller;

class ProductController extends Controller
{
    public function indexAction()
    {

        $this->view->products = Products::find();
    }


    public function addAction()
    {
        $escaper = new \App\Components\MyEscaper();

        $checkPost = $this->request->isPost();
        $this->view->errorMessage = "";

        if ($checkPost) {

            $inputs = $this->request->getPost();

            if ($inputs['name'] && $inputs['description'] && $inputs['tags']) {

                if ($inputs['price'] || $inputs['stock']) {
                    $checkP = 1;
                    $checkS = 1;
                    $price = 0;
                    $stock = 0;

                    if ($inputs['price']) {

                        if (is_numeric($inputs['price'])) {
                            $checkP = 1;
                            $price = $escaper->sanitize($inputs['price']);
                        } else {
                            $checkP = 0;
                            $this->view->errorMessage = '*price and stock must be numeric';
                        }
                    }

                    if ($inputs['stock']) {

                        if (is_numeric($inputs['stock'])) {
                            $checkS = 1;
                            $stock = $escaper->sanitize($inputs['stock']);
                        } else {
                            $checkS = 0;
                            $this->view->errorMessage = '*price and stock must be numeric';
                        }
                    }

                    if ($checkP && $checkS) {
                        $productArr = [
                            'name' => $escaper->sanitize($inputs['name']),
                            'description' => $escaper->sanitize($inputs['description']),
                            'tags' => $escaper->sanitize($inputs['tags']),
                            'price' => $price,
                            'stock' => $stock
                        ];

                        $product = new Products();
                        $product->assign(
                            $productArr,
                            [
                                'name', 'description', 'tags', 'price', 'stock'
                            ]
                        );
                        $success = $product->save();

                        if ($success) {
                            $this->eventHandler->productSave()->fire('product:afterSave', $this, ['product' => $product, 'setting' => $setting]);
                            $this->response->redirect('/product');
                        }
                    }
                } else {
                    $productArr = [
                        'name' => $escaper->sanitize($inputs['name']),
                        'description' => $escaper->sanitize($inputs['description']),
                        'tags' => $escaper->sanitize($inputs['tags']),
                        'price' => 0,
                        'stock' => 0
                    ];

                    $product = new Products();

                    $product->assign(
                        $productArr,
                        [
                            'name', 'description', 'tags', 'price', 'stock'
                        ]
                    );

                    $success = $product->save();
                    if ($success) {
                        $this->eventHandler->productSave($product, $setting)->fire('product:afterSave', $this);
                        $this->response->redirect('/product');
                    }
                }
            } else {
                $this->view->errorMessage = '*only price and stock can be left blank';
            }
        }
    }
}
