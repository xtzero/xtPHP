<?php
use lib\controller;
class c1 extends controller{
    function f1(){
        // $res = Db('xttable') -> query('sql query');
        $res = [
            '测试接口',
            '貌似可以写简单的接口了'
        ];
        ajax(1,'成功',$res);
    }

    function f2(){

    }
}