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


/**
 * Class Login
 * @package Gram\Project\Lib\Authenticate
 *
 * methods um Login Prozess durch zuführen, aus zulogen
 * oder zu prüfen ob eingelogt
 */
class Login extends AuthenticateAbstract
{
	/**
	 * Führt den Login Prozess durch
	 *
	 * Prüft ob es den user gibt
	 *
	 * Setzt Weidererkennungs Merkmale in die Session
	 *
	 * @param $username
	 * @param $password
	 * @return bool
	 */
	public function login($username,$password)
	{
		//Prüfe ob User existiert und hole gleichzeitig die unterinfos
		if(!$this->user->selectUserByName($username) || !$this->pwValid($username,$password)){
			return false;
		}

		//Session
		$this->setSessionValues();

		return true;
	}

	/**
	 * Zerstört die Session des Nutzers
	 *
	 * @return bool
	 */
	public function logout()
	{
		$this->session->destroy();

		return (!$this->session->keyExist('user','username'));
	}

	/**
	 * Püft ob username und userid in der Session sind
	 *
	 * @return bool
	 */
	public function isLoggedIn()
	{
		return $this->session->keyExist('user','username') && $this->session->keyExist('user','userid');
	}

	/**
	 * Setzt username und userid in die Session
	 */
	protected function setSessionValues()
	{
		$this->session->set('user',[
			'username'=>$this->user->getUserName(),
			'userid'=>$this->user->getUserId()
		]);
	}

}