<?php
/*
 ******** 套件說明 ********
 cumi - Convert 套件
 last updated: 2016/11/27
 
 ******** 使用說明 ********
 //DES CRYPT
 $str = 'hello world';
 $key = '123abc456def';
 $enc = cumiConvert::des_encrypt($str, $key);
 $dec = cumiConvert::des_decrypt($dec, $key);
 */


class cumiConvert{
	/**************** base64_encode **************/
	//json to base64_encode
	static function json2enc($json) {
		@$array = json_decode($json, true);
		if(!$array) {
			return false;
		} else {
			return @self::arr2enc($array);
		}
	}
	
	//base64_encode to json
	static function enc2json($enc) {
		@$array = self::enc2arr($enc);
		if(!$array) {
			return false;
		} else {
			return @json_encode($array);
		}
	}
	
	//array to base64_encode
	static function arr2enc($arr) {
		if(!empty($arr) && is_array($arr)) {
			return @base64_encode(serialize($arr));
		} else {
			return false;
		}
	}
	
	//base64_encode to array
	static function enc2arr($enc) {
		@$return = unserialize(base64_decode($enc));
		if(is_array($return) && sizeof($return) > 0) {
			return $return;
		} else {
			return false;
		}
	}
	
	/**************** DES CRYPT ******************/
	//encrypt
	static function des_encrypt($str, $key) {
		srand((double)microtime() * 1000000);
		$encrypt_key = md5(rand(0, 32000));
		$ctr = 0;
		$tmp = '';
		for($i=0; $i<strlen($str); $i++){
			$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
			$tmp .= $encrypt_key[$ctr].($str[$i] ^ $encrypt_key[$ctr++]);
		}
		return base64_encode(self::des_key($tmp, $key));
	}
	
	//decrypt
	static function des_decrypt($str, $key) {
		$str = self::des_key(base64_decode($str), $key);
		$tmp = '';
		for($i=0; $i<strlen($str); $i++){
			$md5 = $str[$i];
			$tmp .= $str[++$i] ^ $md5;
		}
		return $tmp;
	}
	
	//key tool
	function des_key($str, $encrypt_key) {
		$encrypt_key = md5($encrypt_key);
		$ctr = 0;
		$tmp = '';
		for($i=0; $i<strlen($str); $i++){
			$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
			$tmp .= $str[$i] ^ $encrypt_key[$ctr++];
		}
		return $tmp;
	}
	
	
	/**************** REPLACEMENT ****************/
	static function html_minify($buffer) {
		preg_match_all(
			'#\<textarea.*\>.*\<\/textarea\>#Uis',
			$buffer,
			$foundTxt
		);
		preg_match_all(
			'#\<pre.*\>.*\<\/pre\>#Uis',
			$buffer,
			$foundPre
		);
		$buffer = str_replace(
			$foundTxt[0],
			array_map(
				function($el) {
					return '<textarea>'.$el.'</textarea>';
				},
				array_keys($foundTxt[0])
			),
			$buffer
		);
		$buffer = str_replace(
			$foundPre[0],
			array_map(
				function($el) {
					return '<pre>'.$el.'</pre>';
				},
				array_keys($foundPre[0])
			),
			$buffer
		);
		$search = array(
			'/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\'|\")\/\/.*))/',
			'/<!--(.*)-->/Uis',
			'/(\t)|(\r)|(\n)/',
			'/\>[^\S ]+/s',
			'/[^\S ]+\</s',
			'/(\s)+/s'
		);
		$replace = array(
			'',
			'',
			'',
			'>',
			'<',
			'\\1'
		);
		$buffer = preg_replace($search, $replace, $buffer);
		$buffer = str_replace(
			array_map(function($el) {
				return '<textarea>'.$el.'</textarea>';
			},
			array_keys($foundTxt[0])),
			$foundTxt[0],
			$buffer
		);
		$buffer = str_replace(
			array_map(function($el) {
				return '<pre>'.$el.'</pre>';
			},
			array_keys($foundPre[0])),
			$foundPre[0],
			$buffer
		);
		return $buffer;
	}
}