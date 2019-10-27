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

namespace Gram\Project\Lib\Cookie;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface Psr7CookieInterface
 * @package Gram\Project\Lib\Cookie
 *
 * Simple Cookie Implementation für Psr 7
 */
interface Psr7CookieInterface
{
	/**
	 * Gibt ein Value zu einem Namen zurück
	 *
	 * @param ServerRequestInterface $request
	 * @param $name
	 * @return mixed|false
	 */
	public function get(ServerRequestInterface $request, $name);

	/**
	 * Erstellt einen Cookie mit dem Namen $name und dem Value $value
	 *
	 * Fügt diesen in den Response als header ein
	 *
	 * @param ResponseInterface $response
	 * @param $name
	 * @param $value
	 * @param bool $expire
	 * @return ResponseInterface
	 */
	public function set(ResponseInterface $response, $name, $value, $expire=false):ResponseInterface;

	/**
	 * Erstellt nur den Header ohne diesen in einen Response ein zusetzen
	 *
	 * Gibt ein Array: ['Set-Cookie',$cookie] zurück
	 *
	 * Dieses Array kann mit @see setRawCookie in einen Response gesetzt werden
	 *
	 * @param $name
	 * @param $value
	 * @param bool $expire
	 * @return array
	 */
	public function setRaw($name, $value, $expire=false):array;

	/**
	 * Überschreibt einen Cookie, sodass seine Zeit abgelaufen ist
	 *
	 * @param ResponseInterface $response
	 * @param $name
	 * @return ResponseInterface
	 */
	public function delete(ResponseInterface $response, $name):ResponseInterface;

	/**
	 * @see setRaw
	 * @see delete
	 *
	 * @param $name
	 * @return array
	 */
	public function deleteRaw($name):array;

	/**
	 * Setzt einen Cookie der mit dem Raw Methods erstellt wurde in einen Response ein
	 *
	 * @param ResponseInterface $response
	 * @param array $rawCookies
	 * @return ResponseInterface
	 */
	public function setRawCookie(ResponseInterface $response,array $rawCookies):ResponseInterface;
}