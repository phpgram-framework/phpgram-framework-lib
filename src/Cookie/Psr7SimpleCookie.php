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
 * Class Psr7SimpleCookie
 * @package Gram\Project\Lib\Cookie
 *
 * Eine einfache Cookie implementation mit Psr 7 Request und Response
 */
class Psr7SimpleCookie implements Psr7CookieInterface
{
	/**
	 * @inheritdoc
	 */
	public function get(ServerRequestInterface $request, $name)
	{
		$cookies = $request->getCookieParams();
		return isset($cookies[$name])?$cookies[$name]:false;
	}

	/**
	 * @inheritdoc
	 * @throws \Exception
	 */
	public function set(ResponseInterface $response, $name, $value, $expire=false): ResponseInterface
	{
		return $this->setRawCookie($response,$this->setRaw($name,$value,$expire));
	}

	/**
	 * @inheritdoc
	 * @throws \Exception
	 */
	public function setRaw($name, $value, $expire=false): array
	{
		if($expire===false){
			$expire='30';
		}

		$expiry = new \DateTimeImmutable('now + '.$expire.'days');

		$cookie = urlencode($name).'='.
			urlencode($value).'; expires='.$expiry->format(\DateTime::COOKIE).'; Max-Age=' .
			$expire * 60 * 60 * 24 . '; httponly';

		return ['Set-Cookie',$cookie];
	}

	/**
	 * @inheritdoc
	 */
	public function delete(ResponseInterface $response, $name): ResponseInterface
	{
		return $this->setRawCookie($response,$this->deleteRaw($name));
	}

	/**
	 * @inheritdoc
	 */
	public function deleteRaw($name): array
	{
		$cookie = urlencode($name).'='.
			urlencode('deleted').'; expires=Thu, 01-Jan-1970 00:00:01 GMT; Max-Age=0; httponly';

		return ['Set-Cookie',$cookie];
	}

	/**
	 * @inheritdoc
	 */
	public function setRawCookie(ResponseInterface $response,array $rawCookies):ResponseInterface
	{
		[$name,$value]=$rawCookies;

		return $response->withAddedHeader($name,$value);
	}
}