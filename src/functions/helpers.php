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

	function url(string $path,$full = true){
		$url = "";

		if($full===true){
			$url.=getenv('ROOT_URL');
		}

		$url.=getenv('ROOT_URL_PATH');

		return $url.$path;
	}
}