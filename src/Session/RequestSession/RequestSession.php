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

use Gram\Project\Lib\Session\SessionInterface;

/**
 * Class RequestSession
 * @package Gram\Project\Lib\Session\RequestSession
 *
 * Session Speicher
 *
 * Speichert die Daten für die Dauer des Requests
 */
class RequestSession implements SessionInterface
{
	/** @var int */
	protected $active = \PHP_SESSION_NONE;

	protected $delete = false;

	protected $session_id, $old_session_ids = [];

	protected $content;

	public function __construct(
		$session_id,
		array $content
	){
		$this->session_id = $session_id;
		$this->content = $content;

		//besteht bereits eine active session
		if ($this->session_id !== '') {
			$this->active = \PHP_SESSION_ACTIVE;
		}
	}

	public function get($key, $key2 = false)
	{
		$this->start();

		//2 Dim Array
		if($key2!==false && isset($this->content[$key][$key2]))
			return $this->content[$key][$key2];
		elseif ($key2!==false){
			return false;
		}

		return (isset($this->content[$key]))?$this->content[$key]:false;
	}

	public function set($key, $value)
	{
		$this->start();

		//wenn das array bereits daten hat nur daten austauschen die neu sind
		if(isset($this->content[$key]) && is_array($value)){
			foreach ($value as $key1=>$item){
				if(self::get($key,$key1)!=$value[$key1]){
					$this->content[$key][$key1]=$item;
				}
			}
		}else
			$this->content[$key]=$value;
	}

	public function keyExist($key, $key2 = false): bool
	{
		$this->start();

		return $key2?(isset($this->content[$key][$key2])):(isset($this->content[$key]));
	}

	public function start()
	{
		if ($this->active === \PHP_SESSION_ACTIVE) {
			return;
		}

		$this->active = \PHP_SESSION_ACTIVE;

		if ($this->session_id === '') {
			$this->session_id = $this->generate();
		}
	}

	public function destroy()
	{
		if ($this->active === \PHP_SESSION_NONE) {
			return;
		}

		$this->old_session_ids[] = $this->session_id;
		$this->session_id = '';
		$this->active = \PHP_SESSION_NONE;
		$this->content = [];
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->session_id;
	}

	/**
	 * @return array
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * @return int
	 */
	public function getStatus()
	{
		return $this->active;
	}

	public function getOldIds()
	{
		return $this->old_session_ids;
	}

	protected function generate()
	{
		try {
			return \bin2hex(\random_bytes(32));
		} catch (\Exception $e) {
			return '';
		}
	}
}