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

namespace Gram\Project\Lib\Authenticate;

use Gram\Project\Lib\SessionH;

class AuthToken
{
	private static $gen_tocken = null;

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

	public static function csrf()
	{
		if(self::$gen_tocken === null){
			self::$gen_tocken = self::generateToken();
		}

		return '<input type="hidden" name="csrf_token" value="'.self::$gen_tocken.'">';
	}
}