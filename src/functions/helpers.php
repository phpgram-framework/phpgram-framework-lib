<?php
/**
 * phpgram project
 *
 * This File is part of the phpgram Mvc Framework Lib
 *
 * Web: https://gitlab.com/grammm/php-gram/phpgram-framework-lib/tree/master
 *
 * @license https://gitlab.com/grammm/php-gram/phpgram-framework-lib/blob/master/LICENSE
 *
 * @author Jörn Heinemann <joernheinemann@gmx.de>
 */

if(!function_exists('url')){

	/**
	 * Function gibt die Aktuelle Url zurück
	 *
	 * @param string $path
	 * @param bool $full
	 * @return string
	 */
	function url(string $path,$full = true){
		$url = "";

		if($full===true){
			$url.=getenv('ROOT_URL');
		}

		$url.=getenv('ROOT_URL_PATH')."/";

		return $url.$path;
	}
}

if(!function_exists('url_r')){

	function url_r(string $path,$full = true){
		return url("resources/$path",$full);
	}
}

if(!function_exists('debug_console')){
	/**
	 * Einfache Debugausgabe in die js Console
	 *
	 * @param $data
	 */
	function debug_console($data) {
		if (is_array($data))
			$output = "<script>console.log('Debugausgabe: ".implode(',', $data). "');</script>";
		else
			$output = "<script>console.log('Debugausgabe: ".$data."');</script>";

		echo $output;
	}
}

if(!function_exists('loadJSON')) {
	/**
	 * Funktion läd einen Json String und wandelt diesen in ein Array um
	 * @param $path
	 * @return mixed
	 */
	function loadJSON($path){
		$file = file_get_contents($path);

		return json_decode($file, true);
	}
}