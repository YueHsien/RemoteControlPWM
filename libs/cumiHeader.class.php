<?php
/*
 ******** 套件說明 ********
 cumi - Header 套件
 last updated: 2016/11/10
 
 ******** 使用方法 ********
 //自動:utf8, text/html, minify,
 new cumiHeader;
 //手動
 new cumiHeader(array(
 	"charset"	=> "UTF-8",
 	"ctype"		=> "json",
 	"minify"	=> false,
 	"cache"		=> false,
 	"debug"		=> true
 ));
 */


class cumiHeader {
	protected $charset	= "UTF-8";
	protected $ctype	= "text/html";
	protected $minify	= true;
	protected $nocache	= true;
	protected $debug	= false;
	
	function __construct($default = null) {
		self::clear();
		if(!empty($default)){
			foreach($default as $key => $value){
				if($key == 'charset'){
					self::set_charset($value);
				}
				if($key == 'ctype'){
					self::set_ctype($value);
				}
				if($key == 'minify'){
					$this->minify = $value;
				}
				if($key == 'nocache'){
					$this->nocache = $value;
				}
				if($key == 'debug'){
					$this->debug = $value;
				}
			}
			self::init();
		}else{
			self::init();
		}
	}
	
	function init() {
		@header("Content-Type: {$this->ctype}; charset={$this->charset};");
		if($this->minify === true) {
			@ob_start(function($b) {
				return cumiConvert::html_minify($b);
			});
		}
		
		if($this->nocache === true){
			@header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
			@header("Pragma: no-cache");
		}
		
		if($this->debug === true){
			@ini_set('display_errors', 1);
			@ini_set('display_startup_errors', 1);
			@error_reporting(E_ALL);
		}
	}
	
	static public function clear(){
		@header_remove("Content-Type");
		@header_remove("Cache-Control");
		@header_remove("Pragma");
		return true;
	}
	
	static public function dump(){
		return headers_list();
	}
	
	function set_charset($default = "UTF-8") {
		$this->charset = $default;
	}
	
	function set_ctype($default = "html") {
		if($default === 'html') {
			$this->ctype = 'text/html';
		} else if($default === 'xml') {
			$this->ctype = "text/xml";
		} else if($default === 'csv') {
			$this->ctype = "text/csv";
		} else if($default === 'txt') {
			$this->ctype = "text/plain";
		} else if($default === 'json') {
			$this->ctype = "application/json";
		} else if($default === 'download') {
			$this->ctype = "application/force-download";
		} else {
			$this->ctype = $default;
		}
	}
}