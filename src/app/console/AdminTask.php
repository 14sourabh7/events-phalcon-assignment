<?php



namespace App\Console;

use Phalcon\Cli\Task;
use Firebase\JWT\JWT;


class AdminTask extends Task
{
    public function mainAction()
    {
        echo 'This is the default task and the default action' . PHP_EOL;
    }


    /**
     * buildadminAction
     * 
     * action to create admin jwt token
     *
     * @return void
     */
    public function buildadminAction()
    {
        $key =  'RwII94n0W/wnXyq5fU3SD6FUFz8IcyYUXjUqpUoCqXg=';

        $payload = array(
            "iss" => "localhost:8080",
            "aud" => "localhost:8080",
            "iat" => 1356999524,
            "nbf" => 1357000000,

            'role' => 'admin'
        );

        $jwt = JWT::encode($payload, $key, 'HS256');
        echo $jwt . PHP_EOL;
    }
}
