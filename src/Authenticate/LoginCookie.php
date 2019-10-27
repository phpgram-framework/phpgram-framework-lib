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

use Gram\Project\Lib\Cookie\Psr7CookieInterface;
use Gram\Project\Lib\SessionH;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class LoginCookie
 * @package Gram\Project\Lib\Authenticate
 *
 * Führt den Login Prozess durch mit Cookie
 */
class LoginCookie extends Login
{
	protected $request, $response, $cookie, $session, $token;

	public function __construct(
		UserInterface $user,
		ServerRequestInterface $request,
		ResponseInterface $response=null,
		Psr7CookieInterface $cookie
	){
		parent::__construct($user);

		$this->request = $request;
		$this->response = $response;
		$this->cookie = $cookie;
	}

	/**
	 * @inheritdoc
	 *
	 * gibt den Login Status und den Response (@see generateCookie) zurück
	 *
	 * @param $username
	 * @param $password
	 * @param bool $cookie
	 * @return array({status},{response})
	 */
	public function loginCookie($username, $password, $cookie = false)
	{
		if(parent::login($username, $password)===false){
			return [false,null];
		}

		if($cookie===false){
			return [true,null];
		}

		return [$this->generateCookie(true),$this->response];
	}

	/**
	 * @inheritdoc
	 *
	 * Püft, wenn nicht schon durch Session eingelogt (parent), ob es einen Cookie gibt
	 *
	 * Wenn ja, versuche User durch Cookie zu autehtifizieren
	 *
	 * Setzt neuen Cookie und gibt Response mit neuem Cookie zurück
	 *
	 * @return array({status},{response})
	 *
	 * $status:
	 * 0 => nicht eingelogt
	 * 1 => durch Session eingelogt
	 * 2 => mit Cookie eingelogt
	 */
	public function isLoggedIn()
	{
		if(parent::isLoggedIn()){
			return [1,null];
		}

		//auf cookie prüfen
		$seesionid = $this->cookie->get($this->request,'sessionid');
		$token = $this->cookie->get($this->request,'token');

		if(!$token || !$seesionid){
			return [0,null];
		}

		//hole user infos und prüfe gleichzeitig ob es den user mit dem gerät und dem token gibt
		if(!$this->user->selectUserByCookie($seesionid,$token)){
			return [0,null];
		}

		//erstellt nach jedem Cookie login ein neues Token und session id

		//Speichere neuen Cookie
		if(!$this->generateCookie()){
			return [0,null];
		}

		//setze Werte in die Session
		$this->setSessionValues();

		return [2,$this->response];
	}

	/**
	 * @inheritdoc
	 *
	 * löscht zusätzlich die Cookies
	 *
	 * gibt den status und response mit den gelöschten cookies zurück
	 *
	 * @return array({status}, {response})
	 */
	public function logout()
	{
		$userid= SessionH::get('user','userid');

		//Werte aus DB löschen
		$this->user->deleteSession($userid,$this->cookie->get($this->request,'sessionid'));

		//Cookie löschen
		$this->response = $this->cookie->delete($this->response,'sessionid');
		$this->response = $this->cookie->delete($this->response,'token');

		return [parent::logout(),$this->response];
	}

	/**
	 * Erstellt die auth Cookies mithilfe von Psr7 Cookie
	 *
	 * Fügt auch die Tokens der Db hinzu
	 *
	 * Setzt einen neuen Response wenn ein Response übergeben wurde
	 *
	 * Sonst gebe den header ohne Response zurück
	 * Dieser muss dann manuel in den Response gesetzt werden
	 *
	 * @param bool $new
	 * @return bool
	 */
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
			$oldsid = $this->cookie->get($this->request,'sessionid');
			$oldtoken = $this->cookie->get($this->request,'token');

			//DB Eintrag
			if(!$this->user->setSession($oldsid,$this->session) || !$this->user->setToken($oldtoken,$this->token)){
				return false;
			}
		}

		//wenn noch kein Response erzeugt wurde
		//muss nachträglich in Response eingesetzt werden mit withAddedHeader()
		if($this->response===null){
			$this->response = [
				$this->cookie->setRaw('token',$this->token),
				$this->cookie->setRaw('sessionid',$this->session)
			];
		}else{
			$this->response = $this->cookie->set($this->response,'token',$this->token);
			$this->response = $this->cookie->set($this->response,'sessionid',$this->session);
		}

		return true;
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