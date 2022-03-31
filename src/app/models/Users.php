<?php

use Phalcon\Mvc\Model;

class Users extends Model
{
    public $user_id;
    public $name;
    public $email;
    public $password;
    public $role;

    public function checkUser($email, $password)
    {
        $user =  Users::findFirst(['conditions' => "email = '$email' AND password = '$password'"]);
        return $user;
    }
    public function checkMail($email)
    {
        $user =  Users::findFirst(['conditions' => "email = '$email'"]);
        return $user;
    }
}
