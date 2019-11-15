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

namespace Gram\Project\Lib\Session;


class StdPhpSession implements SessionInterface
{

	public function get($key, $key2 = false)
	{
		$this->start();

		//2 Dim Array
		if($key2!==false && isset($_SESSION[$key][$key2]))
			return $_SESSION[$key][$key2];
		elseif ($key2!==false){
			return false;
		}

		return (isset($_SESSION[$key]))?$_SESSION[$key]:false;
	}

	public function set($key, $value)
	{
		$this->start();

		//wenn das array bereits daten hat nur daten austauschen die neu sind
		if(isset($_SESSION[$key]) && is_array($value)){
			foreach ($value as $key1=>$item){
				if(self::get($key,$key1)!=$value[$key1]){
					$_SESSION[$key][$key1]=$item;
				}
			}
		}else
			$_SESSION[$key]=$value;
	}

	public function start()
	{
		if(session_status() == PHP_SESSION_NONE){
			ini_set('session.cookie_httponly',true);
			session_start();
		}
	}

	public function destroy()
	{
		$this->start();

		session_unset();
		session_destroy();
	}

	public function keyExist($key, $key2 = false): bool
	{
		$this->start();

		return $key2?(isset($_SESSION[$key][$key2])):(isset($_SESSION[$key]));
	}
}