<?php

declare(strict_types=1);

namespace App\Console;

use Phalcon\Cli\Task;

class UserTask extends Task
{
    public function mainAction()
    {
        echo 'ths is default';
        $this->console->handle([
            'task' => 'main',
            'action' => 'print'
        ]);
    }

    public function regenerateAction(int $count = 0)
    {
        echo 'This is the retenerate action'  . PHP_EOL;
    }
}
