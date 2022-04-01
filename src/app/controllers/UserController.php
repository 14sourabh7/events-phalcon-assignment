<?php

use Phalcon\Mvc\Controller;



use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class UserController extends Controller
{
    public function indexAction()
    {
        $escaper = new \App\Components\MyEscaper();
        $this->view->locale = $this->locale;
        $this->view->message = '';
        $locale = $this->request->get()['locale'];
        $check = $this->request->isPost();
        if ($check) {
            if ($this->request->getPost()['email'] && $this->request->getPost()['password']) {
                $email =
                    $escaper->sanitize($this->request->getPost()['email']);
                $password =
                    $escaper->sanitize($this->request->getPost()['password']);
                $user = new Users();
                $data = $user->checkUser($email, $password);
                if ($data) {


                    $key =  'RwII94n0W/wnXyq5fU3SD6FUFz8IcyYUXjUqpUoCqXg=';

                    $payload = array(
                        "iss" => "localhost:8080",
                        "aud" => "localhost:8080",
                        "iat" => 1356999524,
                        "nbf" => 1357000000,
                        'name' => $data->name,
                        'role' => $data->role
                    );

                    $jwt = JWT::encode($payload, $key, 'HS256');
                    if ($locale) {
                        $locale = $locale;
                    } else {
                        $locale = 'en';
                    }

                    $this->response->redirect("/product?bearer=$jwt&locale=$locale");
                } else {
                    $this->view->message = 'authentication failed';
                }
            } else {
                $this->view->message = $this->locale->_('er6');
            }
        }
    }
    public function signupAction()
    {
        $escaper = new \App\Components\MyEscaper();
        $roles = Roles::find();
        $this->view->locale = $this->locale;
        $this->view->roles = $roles;
        $this->view->tokenCheck = 0;
        $this->view->msg = "";
        $check = $this->request->isPost();
        if ($check) {
            $inputs = $this->request->getPost();

            $user = new Users();

            $user->name = $escaper->sanitize($inputs['name']);
            $user->email
                = $escaper->sanitize($inputs['email']);
            $user->password =
                $escaper->sanitize($inputs['password']);
            $user->role =
                $escaper->sanitize($inputs['roles']);
            $result = $user->save();

            if ($result) {


                $key =  'RwII94n0W/wnXyq5fU3SD6FUFz8IcyYUXjUqpUoCqXg=';

                $payload = array(
                    "iss" => "localhost:8080",
                    "aud" => "localhost:8080",
                    "iat" => 1356999524,
                    "nbf" => 1357000000,
                    'name' => $inputs['name'],
                    'role' => $inputs['roles']
                );

                $jwt = JWT::encode($payload, $key, 'HS256');

                $this->view->tokenCheck = 1;
                $this->view->token = $jwt;
            }
        }
    }
}
