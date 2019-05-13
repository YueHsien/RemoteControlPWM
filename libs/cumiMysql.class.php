<?php
/*
 ******** 套件說明 ********
 cumi - MySQL 套件
 last updated: 2017/01/21
 */


class cumiMysql {
	var $_mysqli = 0;
	var $_queryResource = 0;
	
	protected $_host;
	protected $_username;
	protected $_password;
	protected $_dbname;
	protected $_timezone;
	
	function __construct() {
		global $_DB;
		if(empty($_DB)){
			exit("401 Unable to connect to database.");
		}
		$this->_host		= $_DB["host"];
		$this->_username	= $_DB["username"];
		$this->_password	= $_DB["password"];
		$this->_dbname		= $_DB["dbname"];
		
		self::connect(); 
		self::set_encode();
		self::set_timezone();
	}
	
	function __destruct() {
		self::disconnect();
	}
	
	//結束連線
	function disconnect(){
		@$this->_mysqli->close();
	}
	
	private function connect() {
		@$this->_mysqli = new mysqli($this->_host, $this->_username, $this->_password, $this->_dbname);
		if (@$this->_mysqli->connect_error) {
			exit("402 Unable to connect to database.");
		}
		return true;
	}
	
	private function set_timezone($timezone = 'GMT+8') {
		@$this->_mysqli->query("SET timezone = '".$timezone."'");
	}
	
	private function set_encode($encode = 'UTF8') {  
		@$this->_mysqli->query("SET NAMES '".$encode."'");  
	}
	
	//clear sql query
	function clear(){
		@$this->_queryResource = 0;
	}
	
	function query($sql) {
		if(!$queryResource = @$this->_mysqli->query($sql)){
			return false;
		}
		$this->_queryResource = $queryResource;
		return $queryResource;
	}
	
	//fetch key => value
	function fetch_arr() {
		if($this->_queryResource === 0 || self::size() == 0){
			return null;
		}
		$return = $this->_queryResource->fetch_assoc();
		return $return;
	}
	
	//fetch idx => value
	function fetch_row() {
		if($this->_queryResource === 0 || self::size() == 0){
			return null;
		}
		$return = $this->_queryResource->fetch_row();
		return $return;
	}
	
	//smart get all
	function get_all($smart_mode = true) {
		if($this->_queryResource === 0 || self::size() == 0){
			return null;
		}
		$_m_return = array();
		while($_m_row = self::fetch_arr()){
			$_m_return[] = $_m_row;
		}
		//smart mode auto return row or value
		if($smart_mode == true){
			//one row
			if(sizeof($_m_return) == 1){
				//one value
				if(sizeof($_m_return[0]) == 1){
					foreach($_m_return[0] as &$value){
						return $value;
					}
				}
				return $_m_return[0];
			}
		}
		return $_m_return;
	}
	
	//query size
	function size(){
		if($this->_queryResource === 0){
			return 0;
		}
		return $this->_queryResource->num_rows;
	}
	
	//insert id
	function iid(){
		if($this->_queryResource === 0){
			return 0;
		}
		return $this->_mysqli->insert_id;
	}
}
?>