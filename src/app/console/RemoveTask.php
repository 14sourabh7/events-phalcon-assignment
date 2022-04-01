<?php

namespace App\Console;

use Phalcon\Cli\Task;
use Permissions;

class RemoveTask extends Task
{
    public function mainAction()
    {
        echo 'This is the default task and the default action' . PHP_EOL;
    }

    /**
     * removelogAction()
     * action to clear logs
     *
     * @return void
     */
    public function removelogAction()
    {
        file_put_contents('../app/logs/main.log', "");
    }

    /**
     * removeaclAction()
     * action to clear acl
     *
     * @return void
     */
    public function removeaclAction()
    {
        file_put_contents('../app/security/acl.cache', "");
        $permissions = Permissions::find();
        $result = $permissions->delete();
        echo $result . PHP_EOL;
    }
}
