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


abstract class AuthenticateAbstract
{
	const PW_ALOG=PASSWORD_DEFAULT;
	const PW_COST = ['cost'=>12];

	/** @var UserInterface */
	protected $user;

	public function __construct(UserInterface $user)
	{
		$this->user = $user;
	}

	public function getUser()
	{
		return $this->user;
	}

	protected function pwHash($pw)
	{
		return password_hash($pw,self::PW_ALOG,self::PW_COST);
	}

	protected function pwValid($user,$pw)
	{
		return password_verify($pw,$this->user->getPw($user));
	}
}