<?php
/*****
cumi - php Verify module
version: 1.1
last updated: 2016/09/22
*****/
class cumiVerify {
	var $_return = true;
	/*******擴充*******/
	function _is_provider($_input) {
		if(empty($_input) || !preg_match('/^(gps|network|null){1}$/', $_input)) {
			$this->_return = false;
		}
	}
	//驗證(陣列)
	function _is_array($_input) {
		if(empty($_input) || !is_array($_input)) {
			$this->_return = false;
		}
	}
	//驗證(布林)
	function _is_bool($_input) {
		if(empty($_input) || !is_bool($_input)) {
			$this->_return = false;
		}
	}
	//驗證(座標)
	function _is_loc($_input) {
		if(empty($_input) || !is_numeric($_input)) {
			$this->_return = false;
		}
	}
	//驗證(浮點)
	function _is_double($_input) {
		if(empty($_input) || !is_numeric($_input)) {
			$this->_return = false;
		}
	}
	//驗證[信箱]
	function _is_mail($_input) {
		if(empty($_input) || !filter_var($_input, FILTER_VALIDATE_EMAIL)) {
			$this->_return = false;
		}
	}
	//驗證[整數]
	function _is_int($_input) {
		if(empty($_input) || !preg_match("/^[0-9]+$/", $_input)) {
			$this->_return = false;
		}
	}
	//驗證[整數]
	function _is_ints($_input) {
		if(empty($_input) || !preg_match("/^[0-9,]+$/", $_input)) {
			$this->_return = false;
		}
	}
	//驗證[浮點]
	function _is_float($_input) {
		if(empty($_input) || !preg_match('/^(\-?\d+(\.\d+)?)$/', $_input)) {
			$this->_return = false;
		}
	}
	//驗證[系統秒]
	function _is_timestamp($_input) {
		if(empty($_input) || !preg_match("/^[0-9]{10}$/", $_input)) {
			$this->_return = false;
		}
	}
	//驗證[mac address]
	function _is_macaddr($_input) {
		if(empty($_input) || !preg_match("/^([a-fA-F0-9]{2}[:|\-]?){6}$/", $_input)) {
			$this->_return = false;
		}
	}
	//驗證[版本號碼]
	function _is_vercode($_input) {
		if(empty($_input) || !preg_match("/^[0-9]{1,3}$/", $_input)) {
			$this->_return = false;
		}
	}
	
	/*******基本*******/
	//初始化
	function __construct() {
		$this->_return = true;
    }
	//清除狀態
	function clear() {
		$this->_return = true;
	}
	//讀取狀態
	function result() {
		return $this->_return;
	}
}