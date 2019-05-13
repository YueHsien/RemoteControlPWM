<?php
//PHP Configure
{
	@date_default_timezone_set("Asia/Taipei");   				//時區
	
	//debug
	@ini_set('display_errors',1);
	@ini_set('display_startup_errors',1);
	@error_reporting(-1);
	$_AU["header"] = "SA75R@##45RER87GWT%TG5Gsadfa8h32256365D5AS3";
}
//Database Configure
{
	//google cloud sql
	$_DB["host"] = "localhost";							//主機
	$_DB["username"] = "hc";							//帳號
	$_DB["password"] = "123456789";						//密碼
	$_DB["dbname"] = "apw";								//資料庫
	//$_DB["timezone"] = "GMT+0";						//時區
}
?>