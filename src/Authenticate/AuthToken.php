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

namespace Gram\Project\Lib\Authenticate;

use Gram\Project\Lib\SessionH;

class AuthToken
{
	public static function generateToken($session=true)
	{
		try {
			$token = bin2hex(random_bytes(10));
		} catch (\Exception $e) {
			echo $e;
			return false;
		}

		//setze Token in die Session um es beim form absenden zu überprüfen
		if($session){
			SessionH::set('token',$token);
		}

		return $token;		//gebe Token zurück um es ins form ein zubinden
	}

	public static function validToken($token){
		return (SessionH::get('token') == $token);
	}
}