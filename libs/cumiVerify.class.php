<?php
/*****
cumi - php Verify module
version: 1.1
last updated: 2016/09/22
*****/
class cumiVerify {
	var $_return = true;
	/*******�X�R*******/
	function _is_provider($_input) {
		if(empty($_input) || !preg_match('/^(gps|network|null){1}$/', $_input)) {
			$this->_return = false;
		}
	}
	//����(�}�C)
	function _is_array($_input) {
		if(empty($_input) || !is_array($_input)) {
			$this->_return = false;
		}
	}
	//����(���L)
	function _is_bool($_input) {
		if(empty($_input) || !is_bool($_input)) {
			$this->_return = false;
		}
	}
	//����(�y��)
	function _is_loc($_input) {
		if(empty($_input) || !is_numeric($_input)) {
			$this->_return = false;
		}
	}
	//����(�B�I)
	function _is_double($_input) {
		if(empty($_input) || !is_numeric($_input)) {
			$this->_return = false;
		}
	}
	//����[�H�c]
	function _is_mail($_input) {
		if(empty($_input) || !filter_var($_input, FILTER_VALIDATE_EMAIL)) {
			$this->_return = false;
		}
	}
	//����[���]
	function _is_int($_input) {
		if(empty($_input) || !preg_match("/^[0-9]+$/", $_input)) {
			$this->_return = false;
		}
	}
	//����[���]
	function _is_ints($_input) {
		if(empty($_input) || !preg_match("/^[0-9,]+$/", $_input)) {
			$this->_return = false;
		}
	}
	//����[�B�I]
	function _is_float($_input) {
		if(empty($_input) || !preg_match('/^(\-?\d+(\.\d+)?)$/', $_input)) {
			$this->_return = false;
		}
	}
	//����[�t�ά�]
	function _is_timestamp($_input) {
		if(empty($_input) || !preg_match("/^[0-9]{10}$/", $_input)) {
			$this->_return = false;
		}
	}
	//����[mac address]
	function _is_macaddr($_input) {
		if(empty($_input) || !preg_match("/^([a-fA-F0-9]{2}[:|\-]?){6}$/", $_input)) {
			$this->_return = false;
		}
	}
	//����[�������X]
	function _is_vercode($_input) {
		if(empty($_input) || !preg_match("/^[0-9]{1,3}$/", $_input)) {
			$this->_return = false;
		}
	}
	
	/*******��*******/
	//��l��
	function __construct() {
		$this->_return = true;
    }
	//�M�����A
	function clear() {
		$this->_return = true;
	}
	//Ū�����A
	function result() {
		return $this->_return;
	}
}