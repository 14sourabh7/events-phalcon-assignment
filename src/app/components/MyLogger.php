<?php
// helper class for logger
namespace App\Components;

use Phalcon\Logger;
use Phalcon\Logger\Adapter\Stream;

class MyLogger
{
    public function log($message)
    {
        $adapter = new Stream('../app/logs/main.log');
        $logger  = new Logger(
            'messages',
            [
                'main' => $adapter,
            ]
        );

        $logger->info($message);
    }
}
