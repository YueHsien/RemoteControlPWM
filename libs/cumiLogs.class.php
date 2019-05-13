<?php
/*
 ******** 套件說明 ********
 cumi - Logs 套件
 last updated: 2016/11/17

 ******** 使用方法 ********
 //宣告物件
 $mlog = new cumiLogs;
 
 //紀錄
 $mlog->put(array(
	"POST" => $_POST,
	"GET" => $_GET,
	"SESSION" => $_SESSION
 ));
 
 //讀取
 $log = $mlog->get(1);
 print_r($log);
 */


class cumiLogs{
	
	var $db_table = "users_logs";
	
	function __construct() {
		//auto install
		@self::install();
	}
	
	function install(){
		@$sql_check_table = "show tables like '{$this->db_table}'";
		@$dbi_check_table = new cumiMysql();
		@$dbi_check_table->query($sql_check_table);
		if($dbi_check_table->size() == 0){
			@$sql_install_part1 = "
			CREATE TABLE `{$this->db_table}` (
				`id` int(11) NOT NULL,
				`act` char(25) NOT NULL,
				`ip` char(25) NOT NULL,
				`data` blob NULL,
				`post` blob NULL,
				`get` blob NULL,
				`puts` blob NULL,
				`session` blob NULL,
				`created_at` int(11) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";
			@$sql_install_part2 = "ALTER TABLE `{$this->db_table}` ADD PRIMARY KEY (`id`), ADD KEY `ip` (`ip`)";
			@$sql_install_part3 = "ALTER TABLE `{$this->db_table}` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1";
			
			@$dbi_install = new cumiMysql();
			@$dbi_install->query($sql_install_part1);
			@$dbi_install->query($sql_install_part2);
			@$dbi_install->query($sql_install_part3);
			return true;
		} else {
			return false;
		}
	}
	
	function put($input = null) {
		//values
		@$uip = self::get_user_ip();
		$act = '';
		$data = '';
		$post = '';
		$get = '';
		$puts = '';
		$session = '';

		foreach($input as $key => $value) {
			if(strtoupper($key) == 'POST') {
				@$post = !empty($value) ? cumiConvert::arr2enc($value) : '';
			} else if(strtoupper($key) == 'GET') {
				@$get = !empty($value) ? cumiConvert::arr2enc($value) : '';
			} else if(strtoupper($key) == 'PUTS') {
				@$puts = !empty($value) ? cumiConvert::json2enc($value) : '';
			} else if(strtoupper($key) == 'SESSION') {
				@$session = !empty($value) ? cumiConvert::arr2enc($value) : '';
			} else if(strtoupper($key) == 'ACT') {
				@$act = !empty($value) ? $value : '';
			} else if(strtoupper($key) == 'DATA') {
				@$data = !empty($value) ? cumiConvert::arr2enc($value) : '';
			}
		}

		//database
		@$dbi = new cumiMysql;
		@$res = $dbi->query("
		INSERT INTO `{$this->db_table}` (
			`ip`,
			`act`,
			`data`,
			`post`,
			`get`,
			`puts`,
			`session`,
			`created_at`
		) VALUE (
			'{$uip}',
			'{$act}',
			'{$data}',
			'{$post}',
			'{$get}',
			'{$puts}',
			'{$session}',
			UNIX_TIMESTAMP()
		)
		");
		if(!@$res) {
			return false;
		} else {
			return @$dbi->iid();
		}
	}
	
	function get($lid = 0){
		if($lid == 0) {
			return false;
		}
		
		@$dbi = new cumiMysql;
		@$dbi->query("SELECT * FROM `{$this->db_table}` WHERE `id` = {$lid} LIMIT 1");
		
		if(@$dbi->size() == 0){
			return false;
		}
		
		@$log = $dbi->get_all();
		@$return = array();
		@$return["ip"] = $log["ip"];
		@$return["created_at"] = date("Y-m-d H:i:s", $log["created_at"]);
		if(@$log["post"] != null){
			@$return["POST"] = cumiConvert::enc2arr($log["post"]);
		}
		if(@$log["get"] != null){
			@$return["GET"] = cumiConvert::enc2arr($log["get"]);
		}
		if(@$log["puts"] != null){
			@$return["PUTS"] = cumiConvert::enc2arr($log["puts"]);
		}
		if(@$log["session"] != null){
			@$return["SESSION"] = cumiConvert::enc2arr($log["session"]);
		}
		return @$return;
	}
	
	function get_user_ip(){
		if(!empty($_SERVER["HTTP_CLIENT_IP"])){
			@$cip = $_SERVER["HTTP_CLIENT_IP"];
		}elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
			@$cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}elseif(!empty($_SERVER["REMOTE_ADDR"])){
			@$cip = $_SERVER["REMOTE_ADDR"];
		}else{
			@$cip = "noip";
		}
		return @$cip;
	}
}