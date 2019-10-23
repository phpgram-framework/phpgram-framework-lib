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


class UserController extends AuthenticateAbstract
{
	public function register($name,$password,$infos=[],$check=false)
	{
		//wenn es den user schon gibt
		if(!$check){
			if($this->user->userExist($name)){
				return false;
			}
		}

		//userdaten setzen
		return $this->user->setUser($name,$this->pwHash($password),$infos);
	}

	public function updateUser($nameold,$name="",$infos=[])
	{
		if(!$this->user->selectUserByName($nameold)){
			return false;
		}

		//wenn username geändert werden soll es diesen aber schon gibt
		if($name!="" && $this->user->userExist($name)){
			return false;
		}

		return $this->user->updateUser($nameold,$name,$infos);
	}

	public function changePW($name,$pwold,$pwnew)
	{
		if(!$this->user->userExist($name) || !$this->pwValid($name,$pwold)){
			return false;
		}

		return $this->user->setPw($this->pwHash($pwnew));
	}

	public function resetPW($name,$pwnew)
	{
		if(!$this->user->userExist($name)){
			return false;
		}

		return $this->user->setPw($this->pwHash($pwnew));
	}
}