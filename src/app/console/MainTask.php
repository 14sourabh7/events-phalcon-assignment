<?php

declare(strict_types=1);

namespace App\Console;

use Phalcon\Cli\Task;

class MainTask extends Task
{
    public function mainAction()
    {
        echo 'This is the default task and the default action' . PHP_EOL;
    }
    public function addAction(int $first, int $second)
    {
        echo $first + $second . PHP_EOL;
    }
    public function printAction()
    {
        echo 'I will get printed too!' . PHP_EOL;
    }
}
