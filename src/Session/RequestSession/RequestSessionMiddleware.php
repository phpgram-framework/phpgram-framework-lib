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

namespace Gram\Project\Lib\Session\RequestSession;

use Gram\Project\Lib\Cookie\Psr7CookieInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * Class RequestSessionMiddleware
 * @package Gram\Project\Lib\Session\RequestSession
 *
 * Middleware die die Request Session aktiviert
 */
class RequestSessionMiddleware implements MiddlewareInterface
{
	private $cookie_name;

	/** @var CacheInterface */
	private $cache;

	/** @var Psr7CookieInterface */
	private $cookie;

	public function __construct(
		$cookie_name,
		CacheInterface $cache,
		Psr7CookieInterface $cookie
	){
		$this->cookie_name = $cookie_name;
		$this->cache = $cache;
		$this->cookie = $cookie;
	}

	/**
	 * @inheritdoc
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 *
	 * Stellt die Request Session zur Verfügung
	 *
	 * Nimmt diese nach dem Request entgegen, cacht den Inhalt und
	 * speichert die Id als Cookie ab
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$session = $this->getSessionFromRequest($request);

		$request = $request->withAttribute(RequestSession::class,$session);

		$response = $handler->handle($request);

		$this->saveCache($session);

		return $this->setCookie($session,$response);
	}

	/**
	 * Hole die Session Id aus dem Cookie
	 *
	 * Sollte es keinen geben wird eine neue Session erstellt
	 *
	 * @param ServerRequestInterface $request
	 * @return RequestSession
	 */
	private function getSessionFromRequest(ServerRequestInterface $request)
	{
		$id = $this->cookie->get($request,$this->cookie_name);

		if($id === false){
			return new RequestSession('',[]);
		}

		try{
			$data = $this->cache->get($id,[]);
		}catch (\Psr\SimpleCache\InvalidArgumentException $e){
			return new RequestSession($id,[]);
		}

		return new RequestSession($id,$data);
	}

	/**
	 * Speichere den Inhalt der Session im Cache
	 *
	 * @param RequestSession $session
	 * @return bool
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	private function saveCache(RequestSession $session)
	{
		$id = $session->getId();

		if($id === '') {
			return false;
		}

		if ($session->isDeleted() === true) {
			return $this->cache->delete($id);
		}

		if($session->getStatus() === \PHP_SESSION_ACTIVE){
			return $this->cache->set($id,$session->getContent());
		}

		return true;
	}

	/**
	 * Setze den Session Cookie
	 *
	 * @param RequestSession $session
	 * @param ResponseInterface $response
	 * @return ResponseInterface
	 */
	private function setCookie(RequestSession $session, ResponseInterface $response)
	{
		if ($session->getStatus() === \PHP_SESSION_NONE) {
			return $this->cookie->delete($response,$this->cookie_name);
		}

		$id = $session->getId();

		return $this->cookie->set($response,$this->cookie_name,$id,false,0);
	}
}