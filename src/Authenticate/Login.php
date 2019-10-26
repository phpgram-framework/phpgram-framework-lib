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

class Login extends AuthenticateAbstract
{
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

	public function logout()
	{
		SessionH::destroy();

		return (!SessionH::existK('user','username'));
	}

	public function isLoggedIn()
	{
		return SessionH::existK('user','username') && SessionH::existK('user','userid');
	}

	protected function setSessionValues()
	{
		SessionH::set('user',[
			'username'=>$this->user->getUserName(),
			'userid'=>$this->user->getUserId()
		]);
	}

}