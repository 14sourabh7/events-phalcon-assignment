<?php

use Phalcon\Mvc\Controller;


class SettingsController extends Controller
{
    public function indexAction()
    {
        $escaper = new \App\Components\MyEscaper();
        $setting = Settings::findFirst('admin_id=1');
        $this->view->price = $setting->price;
        $this->view->stock = $setting->stock;
        $this->view->zip = $setting->zipcode;
        $this->view->title = $setting->title;
        $this->view->errorMessage = "";
        $check = $this->request->isPost();
        if ($check) {
            $inputs = $this->request->getPost();

            if ($inputs['title'] && $inputs['price'] && $inputs['stock'] && $inputs['zip']) {
                if (is_numeric($inputs['price']) && is_numeric($inputs['stock']) && is_numeric($inputs['zip'])) {
                    $settingArr = [
                        'title' => $escaper->sanitize($inputs['title']),
                        'price' => $escaper->sanitize($inputs['price']),
                        'stock' => $escaper->sanitize($inputs['stock']),
                        'zipcode' => $escaper->sanitize($inputs['zip'])
                    ];

                    $setting->assign(
                        $settingArr,
                        [
                            'title', 'price', 'stock', 'zipcode'
                        ]
                    );
                    $success = $setting->update();
                    if ($success) {
                        $this->response->redirect('/');
                    }
                } else {
                    $this->view->errorMessage = '*price, stock and zip must be numeric';
                }
            } else {
                $this->view->errorMessage = '*please fill all fields';
            }
        }
    }
}
