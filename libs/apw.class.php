<?php

class apw{
	
	function __construct() {
		
	}
	
	/********* 裝置 *********/
	function encrypt_uuid($uuid) {
		@$skey = "UYFJ4tcx$qDf^8*s#y4v";
		@$dbi = new cumiMysql();
		@$dbi->query("SELECT UPPER(SHA1(CONCAT('{$uuid}', '{$skey}', STR_TO_DATE(NOW(), '%Y-%m-%d')))) as `skey`");
		if($dbi->size() > 0) {
			return $dbi->get_all(true);
		} else {
			return false;
		}
	}
	
	function decrypt_uid($skey) {
		@$skey = "UYFJ4tcx$qDf^8*s#y4v";
		@$dbi = new cumiMysql();
		@$dbi->query("SELECT `uuid` FROM `devices` WHERE UPPER(SHA1(CONCAT(`uuid`, '{$skey}', STR_TO_DATE(NOW(), '%Y-%m-%d')))) LIKE '{$skey}'");
		if($dbi->size() > 0) {
			return $dbi->get_all(true);
		} else {
			return false;
		}
	}
	
	function get_devices($uid) {
		@$skey = "UYFJ4tcx$qDf^8*s#y4v";
		@$dbi = new cumiMysql();
		@$dbi->query("
		SELECT
			`id`, `uuid`, `name`, UPPER(SHA1(CONCAT(`uuid`, '{$skey}', STR_TO_DATE(NOW(), '%Y-%m-%d')))) as `skey`
		FROM
			`devices`
		WHERE
			`enabled` = '1' AND `uid` = '{$uid}'
		ORDER BY
			`uuid` ASC
		");
		if($dbi->size() > 0) {
			return $dbi->get_all(false);
		} else {
			return false;
		}
	}
	
	function get_device($id) {
		@$dbi = new cumiMysql();
		@$dbi->query("
		SELECT
			`id`, `uuid`, `name`, `enabled`, UPPER(SHA1(CONCAT(`uuid`, '{$skey}', STR_TO_DATE(NOW(), '%Y-%m-%d')))) as `skey`
		FROM
			`devices`
		WHERE
			`enabled` = '1' AND `id` = '{$id}'
		LIMIT 1
		");
		if($dbi->size() > 0) {
			return $dbi->get_all(true);
		} else {
			return false;
		}
	}
	
	function add_device($uid, $uuid, $name) {
		@$dbi = new cumiMysql();
		$flag = @$dbi->query("INSERT INTO `devices`(`uid`,`uuid`,`name`) VALUE ('{$uid}', '{$uuid}', '{$name}')");
		if($flag) {
			return true;
		} else {
			return false;
		}
	}
	
	function modify_device($id, $name) {
		@$dbi = new cumiMysql();
		$flag = @$dbi->query("UPDATE `devices` SET `name` = '{$name}' WHERE `id` = '{$id}'");
		if($flag) {
			return true;
		} else {
			return false;
		}
	}
	
	function del_device($id) {
		@$dbi = new cumiMysql();
		$flag = @$dbi->query("
		DELETE FROM `devices` WHERE `devices`.`id` = '{$id}'");
		if($flag) {
			return true;
		} else {
			return false;
		}
	}
}