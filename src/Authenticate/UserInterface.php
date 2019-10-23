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


interface UserInterface
{
	/**
	 * Lade user Informationen und prüfe ob es den User gibt
	 * Anhand des Usernamen
	 * @param string $name
	 * Username um den User zu laden
	 * @return bool
	 * Gebe true zurück wenn es den User gibt
	 * sonst false
	 */
	public function selectUserByName($name):bool;

	/**
	 * Gebe den Usernamen zurück
	 * @return mixed
	 */
	public function getUserName();

	/**
	 * Gebe Userid zurück
	 * @return mixed
	 */
	public function getUserId();

	public function userExist($username):bool;

	public function setUser($username,$pw,$infos=[]):bool;

	public function updateUser($nameold,$name,$infos=[]):bool;

	/**
	 * Gebe Passwort des Users (gehasht) zurück
	 * @param $user
	 * @return mixed
	 */
	public function getPw($user);

	public function setPw($pwhash): bool;

	public function sessionIdExist($sessionid):bool;

	public function tokenExist($token):bool;

	public function setSession($oldsessionid,$sessionid):bool;

	public function setToken($oldtoken,$token):bool;

	public function deleteSession($userid,$sessionid):bool;

	public function insertCookie($sessionid,$token):bool;

	public function selectUserByCookie($sessionid,$token):bool;
}