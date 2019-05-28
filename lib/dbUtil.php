<?php
/**
 * 账号相关model
 */
class accountModel{
	/**
	 * 检查用户是否存在
	 */
	public static function ifUserExist($usr){
		$res = db::init() -> query("SELECT * FROM user WHERE usr='{$usr}' AND valid=1",true);
		if($res){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * 用户直接存入数据库
	 */
	public static function addUser($usr,$pwd){
		$today = date('Y-m-d H:i:s',time());
		$pwd = md5(md5($pwd));
		$res = db::init() -> query("INSERT INTO user(usr,pwd,valid,create_at,update_at) VALUES('{$usr}','{$pwd}',1,'{$today}','{$today}');");
		return $res;
	}

	/**
	 * 检查用户密码 
	 */
	public static function usrConfirm($usr,$pwd,$returnUserInfo = false){
		$pwd = md5(md5($pwd));
		$res = db::init() -> query("SELECT * FROM user WHERE usr='{$usr}' AND pwd='{$pwd}' AND valid=1;",true);
		if($res){
			if($returnUserInfo){
				return $res[0];
			}else{
				return true;
			}
		}else{
			return false;
		}
	}
}

/**
 * token相关
 */
class tokenModel{
	/**
	 * 获取token信息
	 */
	public static function getTokenInfo($token){
		$today = date('Y-m-d H:i:s',time());
		$fields = 'user.id as userid,user.usr,user.nickname,user.icon';
		$res = db::init() -> query("SELECT {$fields} FROM token LEFT JOIN user ON token.userid=user.id WHERE token.token='{$token}' AND token.die_at>'{$today}' AND user.valid=1;",true);
		if($res){
			return $res;
		}else{
			return false;
		}
	}

	/**
	 * 存入token
	 */
	public static function saveToken($token,$userid){
		$today = date('Y-m-d H:i:s',time());
		$sevenDaysAfter = date('Y-m-d H:i:s',time() + 604800);
		$res = db::init() -> query("INSERT INTO token(userid,token,create_at,die_at) VALUES({$userid},'{$token}','{$today}','{$sevenDaysAfter}');");
		if($res){
			return true;
		}else{
			return false;
		}
	}
}