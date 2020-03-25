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

namespace Gram\Project\Lib;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Input
 * @package Gram\Project\Lib
 *
 * Erfasst, filtert und prüft Inputs mit get, post oder stream (z. B. mit delete oder put)
 *
 * Filtert nach xss
 */
class Input
{
	/** @var array */
	private $json_input;

	/** @var array */
	private $get;

	/** @var array */
	private $post;

	/** @var bool */
	private $strict;

	/** @var bool */
	private $check;

	/** @var ServerRequestInterface */
	private $request;
	
	/**
	 * Input constructor.
	 *
	 * @param ServerRequestInterface $request
	 * Informationen über den Request
	 *
	 * @param callable|null $bodyInput
	 * Wenn der body des Requests anders behandelt werden soll
	 * Standard: json_decode
	 */
	public function __construct(ServerRequestInterface $request, callable $bodyInput = null)
	{
		$this->get = $request->getQueryParams();
		$this->post = $request->getParsedBody();

		$stream = $request->getBody()->__toString();

		if(!isset($bodyInput)) {
			$this->json_input = ($stream==='')?[]:json_decode($stream,true);
		} else {
			$this->json_input = $bodyInput($stream);
		}
		
		$this->request = $request;
	}

	/**
	 * Gibt den Request wieder zurück
	 * 
	 * @return ServerRequestInterface
	 */
	public function getRequest():ServerRequestInterface
	{
		return $this->request;
	}

	/**
	 * Gibt zu einem geg. Namen das Input value oder '' zurück
	 *
	 * Name kann auch ein assoc Array sein, dann wird der Value an die Stelle des Namens
	 * im Array gespeichert
	 *
	 * z. B.:
	 * ['input1'=>"name"] = ['input1'=>"value"]
	 *
	 * @param $name
	 * @param bool $clean
	 * @return array|mixed|string
	 */
	public function get($name, $clean=true)
	{
		$value=[];

		if(is_array($name)){
			foreach ($name as $key=>$item) {
				//speichere den Input an der gleichen Stelle wie der name des Inputs
				$value[$key] = $this->get_value($item);
			}
		}else{
			$value=$this->get_value($name);
		}

		if($clean){
			$value=$this->clean($value);
		}

		return $value;
	}

	/**
	 * Filtert einen Input mit @see htmlspecialchars
	 *
	 * @param $value
	 * @return array|string
	 */
	public function clean($value)
	{
		if(is_array($value)){
			array_walk_recursive($value,[$this, 'cleanInputArray']);
			return $value;
		}

		return $this->cleanInputSingle($value);
	}

	/**
	 * Prüft einen Wert oder ein Array ob es nicht '' (leer) ist
	 *
	 * @param $value
	 * @param bool $strict
	 * @return bool
	 */
	public function check($value,$strict=true)
	{
		$this->strict = $strict;

		if(is_array($value)){
			array_walk_recursive($value,[$this, 'checkArray']);
			return $this->check;
		}

		return $this->checkSingle($value);
	}

	/**
	 * Benutzt @see get()
	 * und @see check()
	 *
	 * in Kombination
	 *
	 * @param $name
	 * @param bool $strict
	 * @param bool $clean
	 * @return array|bool|mixed|string
	 */
	public function gNc($name, $strict=true, $clean = true)
	{
		$value = $this->get($name,$clean);

		$check = $this->check($value,$strict);

		$this->check = null;

		if($check!=null && $check==true){
			return $value;
		}

		return false;
	}

	/**
	 * Holt einen Wert aus:
	 * - Post
	 * - Get
	 * - Inputstream (json)
	 *
	 * @param $name
	 * @return mixed|string
	 */
	private function get_value($name){
		if(isset($this->post[$name])){
			return $this->post[$name];
		}elseif(isset($this->get[$name])){
			return $this->get[$name];
		}elseif(isset($this->json_input[$name])){
			return $this->json_input[$name];
		}else{
			return "";
		}
	}

	/**
	 * Die Function die beim Durchlauf des Arrays aufgerufen wird
	 * für jedes Element.
	 *
	 * Speichert den gefilterten Wert anstelle des Values ab
	 *
	 * @param $a
	 */
	private function cleanInputArray(&$a){
		$a = $this->cleanInputSingle($a);
	}

	/**
	 * Filtert einen Wert
	 *
	 * @param $a
	 * @return string
	 */
	private function cleanInputSingle($a){
		return htmlspecialchars($a,ENT_QUOTES);
	}

	/**
	 * Prüft ein Array ob Werte gesetzt sind
	 *
	 * @param $a
	 */
	private function checkArray(&$a){
		if($this->strict){
			//wenn alle werte gesetzt sein müssen
			if(!isset($this->check) || $this->check==true){
				$this->check=$this->checkSingle($a);
			}else{
				return;
			}
		}else{
			//wenn nur ein wert gesetzt sein muss
			if(!isset($this->check) || $this->check==false){
				//wenn noch kein wert positiv geprüft wurde
				$this->check=$this->checkSingle($a);
			}else{
				return;
			}
		}
	}

	/**
	 * Prüft einen einzelnen Wert ob dieser gesetzt ist
	 *
	 * @param $a
	 * @return bool
	 */
	private function checkSingle($a){
		if(isset($a) && $a!="" && $a!=null){
			return true;
		}

		return false;
	}
}