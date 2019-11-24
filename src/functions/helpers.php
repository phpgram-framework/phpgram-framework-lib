<?php
/**
 * phpgram project
 *
 * This File is part of the phpgram Framework Lib
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

	/**
	 * Url Function die auf resources verweist
	 *
	 * @param string $path
	 * @param bool $full
	 * @return string
	 */
	function url_r(string $path,$full = true){
		return url("resources/$path",$full);
	}
}

if(!function_exists('url_s')){

	/**
	 * Url Function die auf storage verweist
	 *
	 * @param string $path
	 * @param bool $full
	 * @return string
	 */
	function url_s(string $path,$full = true){
		return url("storage/$path",$full);
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