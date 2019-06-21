<?php
namespace controller\app;

require_once 'service/app/accountService.php';

use core\coreController;
use lib\redis;
use service\app\accountService;

class account extends coreController
{
    public function signin()
    {
        $this->param('phone');
        (new accountService())->signin($this->phone);

        redis::init() -> set( "test" , "Hello World");
        echo redis::init() -> get('test');
    }
}
