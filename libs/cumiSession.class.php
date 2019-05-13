<?php
/*
 ******** 套件說明 ********
 cumi - session 套件
 last updated: 2017/07/24
 */


class cumiSession{
	//初始化
	function __construct($expire = 604800){
		if(!isset($_SESSION)){
			if(!empty($_COOKIE["PHPSESSID"])){
				@session_id($_COOKIE["PHPSESSID"]);
			}
			@session_start();
			@ini_set('session.gc_maxlifetime', $expire);
			@self::settimeout($expire);
		}
	}

	//timeout
	function settimeout($expire){
		@setcookie('PHPSESSID', session_id(), time() + $expire, '/', $_SERVER['SERVER_NAME']);
		@session_set_cookie_params($expire);
	}

	//讀取
	function get($key){
		global $_AU;
		if(!empty($_SESSION[$_AU["header"].$key])){
			return $_SESSION[$_AU["header"].$key];
		}else{
			return false;
		}
	}

	//取得session_id
	function get_sid(){
		return session_id();
	}

	//讀取全部
	static function dump(){
		return $_SESSION;
	}

	//設定
	function set($key, $value){
		global $_AU;
		@$_SESSION[$_AU["header"].$key] = $value;
	}

	//刪除
	function del($key){
		global $_AU;
		@$_SESSION["{$_AU["header"]}{$key}"] = null;
		return true;
	}

	//全部清除
	function destory(){
		@session_unset();
		@session_destroy();
		return true;
	}
}