<?php

use Phalcon\Mvc\Controller;
use Phalcon\Acl\Adapter\Memory;
use Phalcon\Acl\Role;
use Phalcon\Acl\Component;

class AccessController extends Controller
{
    public function buildAclAction()
    {
        $aclFile = APP_PATH . '/security/acl.cache';
        $acl = new Memory();
        if (true !== is_file($aclFile)) {
            $acl->addRole('admin');
            $acl->addRole('manager');
            $acl->addRole('guest');

            $acl->addComponent('settings', ['index']);
            $acl->addComponent('product', ['index', 'add']);
            $acl->addComponent('order', ['index', 'addorder']);

            $acl->allow('admin', '*', '*');
            $acl->allow('manager', 'product', '*');
            $acl->allow('manager', 'order', '*');
            $acl->allow('guest', 'product', 'index');

            file_put_contents($aclFile, serialize($acl));
        } else {
            $acl = unserialize(file_get_contents($aclFile));;
        }
    }
}
