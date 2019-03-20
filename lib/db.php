<?php
class db{
    private $db_host;                                           //数据库域名
    private $db_usr     =           'root';                     //数据库用户名
    private $db_pwd     =           '';                         //数据库密码
    private $db;                                                //当前数据库链接
    private static $obj =           null;                       //属性值为对象,默认为null

    private function  __construct(){
        $this->db_host = '';
        $db = mysqli_connect($this->db_host,$this->db_usr,$this->db_pwd);
        if($db){
            mysqli_select_db($db,'');
            mysqli_query($db,'set names \'utf8\'');
            $this->db = $db;

            return $this;
        }else{
            error('db connect error:'.mysqli_error($db));
        }
    }

    public static function init(){
        if(self::$obj === null){
            self::$obj = new self();
        }

        return self::$obj;
    }

    public function query($sql,$dbResultToArray = false){
        if($this->db){
            $res = mysqli_query($this->db,$sql);
            if($dbResultToArray){
                if($res){
                    $res = fetchDbResult($res);
                }else{
                    $res = [];
                }
            }

            return $res;
        }else{
            error('db instance not found!');
            die();
        }
    }
}

function fetchDbResult($dbResult){
    $data = [];
    while($row = mysqli_fetch_assoc($dbResult)){
        $data[] = $row;
    }

    return $data;
}

function __destruct(){
    if($this->db){
        mysqli_close($this->db);
    }
}
