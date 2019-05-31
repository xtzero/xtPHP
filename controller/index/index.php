<?php
namespace controller\index;

use core\coreController;

class index extends coreController {
    public function index() {
        ajax(200, '接口访问成功了！', [
            'ret' => 0,
            'nowListening' => '如果有一个世界温柔得不像话'
        ]);
    }
}