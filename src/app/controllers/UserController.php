<?php

use Phalcon\Mvc\Controller;

use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;


class UserController extends Controller
{
    public function indexAction()
    {
        $escaper = new \App\Components\MyEscaper();
        $this->view->message = '';
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

                    $signer  = new Hmac();
                    $builder = new Builder($signer);

                    $now        = new DateTimeImmutable();
                    $issued     = $now->getTimestamp();
                    $notBefore  = $now->modify('-1 minute')->getTimestamp();
                    $expires    = $now->modify('+1 day')->getTimestamp();
                    $passphrase = 'QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2';


                    // Setup
                    $builder
                        ->setAudience('localhost:8080')  // aud
                        ->setContentType('application/json')        // cty - header
                        ->setExpirationTime($expires)               // exp 
                        ->setId('abcd123456789')                    // JTI id 
                        ->setIssuedAt($issued)                      // iat 
                        ->setIssuer('https://phalcon.io')           // iss 
                        ->setNotBefore($notBefore)                  // nbf
                        ->setSubject($data->role)   // sub
                        ->setPassphrase($passphrase)                // password 
                    ;

                    // Phalcon\Security\JWT\Token\Token object
                    $tokenObject = $builder->getToken();
                    // The token
                    $userToken =  $tokenObject->getToken();

                    $this->response->redirect("/product?bearer=$userToken");
                } else {
                    $this->view->message = 'authentication failed';
                }
            } else {
                $this->view->message = 'please fill all fields';
            }
        }
    }
    public function signupAction()
    {
        $escaper = new \App\Components\MyEscaper();
        $roles = Roles::find();
        $this->view->roles = $roles;
        $this->view->tokenCheck = 0;
        $this->view->msg = "";
        $check = $this->request->isPost();
        if ($check) {
            $inputs = $this->request->getPost();

            $user = new Users();
            $user->checkUser($inputs['email'], $inputs['password']);
            if ($checkUser) {
                $this->view->msg = 'user exists you can login now or can use the token send to you to access';
            } else {
                $user->name = $escaper->sanitize($inputs['name']);
                $user->email
                    = $escaper->sanitize($inputs['email']);
                $user->password =
                    $escaper->sanitize($inputs['password']);
                $user->role =
                    $escaper->sanitize($inputs['roles']);
                $result = $user->save();

                if ($result) {
                    $signer  = new Hmac();
                    $builder = new Builder($signer);

                    $now        = new DateTimeImmutable();
                    $issued     = $now->getTimestamp();
                    $notBefore  = $now->modify('-1 minute')->getTimestamp();
                    $expires    = $now->modify('+1 day')->getTimestamp();
                    $passphrase = 'QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2';


                    // Setup
                    $builder
                        ->setAudience('localhost:8080')  // aud
                        ->setContentType('application/json')        // cty - header
                        ->setExpirationTime($expires)               // exp 
                        ->setId('abcd123456789')                    // JTI id 
                        ->setIssuedAt($issued)                      // iat 
                        ->setIssuer('https://phalcon.io')           // iss 
                        ->setNotBefore($notBefore)                  // nbf
                        ->setSubject($inputs['roles'])   // sub
                        ->setPassphrase($passphrase)                // password 
                    ;

                    // Phalcon\Security\JWT\Token\Token object
                    $tokenObject = $builder->getToken();
                    // The token
                    $userToken =  $tokenObject->getToken();
                    $this->view->tokenCheck = 1;
                    $this->view->token = $userToken;
                }
            }
        }
    }
}
