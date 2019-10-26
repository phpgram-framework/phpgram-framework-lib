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

use Gram\Project\Lib\CookieH;
use Gram\Project\Lib\SessionH;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LoginCookie extends Login
{
	protected $request, $response, $session, $token;

	public function __construct(UserInterface $user, ServerRequestInterface $request, ResponseInterface $response=null)
	{
		parent::__construct($user);

		$this->request = $request;
		$this->response = $response;
	}

	public function loginCookie($username, $password, $cookie = false)
	{
		if(parent::login($username, $password)===false){
			return false;
		}

		if($cookie===false){
			return true;
		}

		return $this->generateCookie(true);
	}

	public function isLoggedIn()
	{
		if(parent::isLoggedIn()){
			return 1;
		}

		//auf cookie prüfen
		$seesionid = CookieH::get('sessionid');
		$token = CookieH::get('token');

		if(!$token || !$seesionid){
			return 0;
		}

		//hole user infos und prüfe gleichzeitig ob es den user mit dem gerät und dem token gibt
		if(!$this->user->selectUserByCookie($seesionid,$token)){
			return 0;
		}

		//erstellt nach jedem Cookie login ein neues Token und session id

		//Speichere neuen Cookie
		if(!$this->generateCookie()){
			return 0;
		}

		//setze Werte in die Session
		$this->setSessionValues();

		return 2;
	}

	public function logout()
	{
		$userid= SessionH::get('user','userid');

		//Werte aus DB löschen
		$this->user->deleteSession($userid,CookieH::get('sessionid'));

		//Cookie löschen
		CookieH::delete('sessionid');
		CookieH::delete('token');

		return parent::logout();
	}

	protected function generateCookie($new=false){
		$this->userSessionId();
		$this->userToken();

		//wenn user zum ersten mal einen Cookie setzt

		if($new){
			//DB Eintrag
			if(!$this->user->insertCookie($this->session,$this->token)){
				return false;
			}
		}else{
			$oldsid=CookieH::get('sessionid');
			$oldtoken = CookieH::get('token');

			//DB Eintrag
			if(!$this->user->setSession($oldsid,$this->session) || !$this->user->setToken($oldtoken,$this->token)){
				return false;
			}
		}

		return (CookieH::set('token',$this->token)
			&& CookieH::set('sessionid',$this->session));
	}

	protected function userSessionId(){
		//erstelle sessionid
		do{
			$this->session=AuthToken::generateToken(false);
		}while($this->user->sessionIdExist($this->session));	//wiederhole solange bis ein neues Token gefunden wurde

		return true;
	}

	protected function userToken(){
		//erstelle token
		do{
			$this->token=AuthToken::generateToken(false);
		}while($this->user->tokenExist($this->token));	//wiederhole solange bis ein neues Token gefunden wurde

		return true;
	}
}