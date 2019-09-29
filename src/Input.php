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
 * @author Jörn Heinemann <j.heinemann1@web.de>
 */

namespace Gram\Project\Lib;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Input
 * @package Gram\Project\Lib
 * Core class um Inputs von Post oder Get zu verwalten
 * bietet auch Funktionen an um Inputs von xss Möglichkeiten zu säubern und auf Vollständigkeit zu prüfen
 */
class Input
{
	private $strict, $value=[], $name, $check, $clean;

	/** @var ServerRequestInterface */
	private $request;
	private $get, $post;

	public function __construct(ServerRequestInterface $request)
	{
		$this->request = $request;
		$this->get = $request->getQueryParams();
		$this->post = $request->getParsedBody();
	}

	public function get($name, $clean=true)
	{
		$this->name = $name;
		$this->clean = $clean;
		$this->value = [];	//setze Value immer wieder zurück

		$this->getInput();

		return $this->value;
	}

	public function check($value,$strict=true)
	{
		$this->value=$value;
		$this->strict=$strict;

		$this->checkInput();

		if($this->check==null){
			return false;
		}
		return $this->check;
	}

	public function clean($value)
	{
		$this->value=$value;

		$this->cleanInput();

		return $this->value;
	}

	public function gNc($name, $strict=true, $clean = true)
	{
		$this->name = $name;
		$this->strict = $strict;
		$this->clean = $clean;
		$this->value = []; //setze Value immer wieder zurück

		$this->getInput();
		$this->checkInput();

		if($this->check!=null && $this->check==true){
			return $this->value;
		}

		return false;
	}

	//Input get

	/**
	 * holt sich einen Inputwert (get oder post) und filtert alle xss Möglichkeiten raus
	 * funktioniert auch bei Arrays
	 * funktioniert auch mit meheren Inputindizes auf einmal
	 */
	private function getInput(){
		//wenn mehere Inputs in einem Array zusammengefasst werden sollen
		if(is_array($this->name)){
			foreach ($this->name as $key=>$item) {
				//speichere den Input an der gleichen Stelle wie der name des Inputs
				$this->value[$key] = $this->post_get($item);
			}
		}else{
			$this->value=$this->post_get($this->name);
		}

		if($this->clean){
			$this->cleanInput();
		}
	}

	/**
	 * Gibt einen Wert zurück der mit get oder post übergeben wurde
	 * @param $name
	 * @return string
	 */
	private function post_get($name){
		if(isset($this->post[$name])){
			return $this->post[$name];
		}elseif(isset($this->get[$name])){
			return $this->get[$name];
		}else{
			return "";
		}
	}

	//Input säubern

	/**
	 * Filtert alle Xss Möglichkeiten raus
	 * funktioniert auch über Arrays
	 */
	private function cleanInput(){
		if(is_array($this->value)){
			array_walk_recursive($this->value,array($this, 'cleanInputArray'));
			return;
		}

		$this->value = $this->cleanInputSingle($this->value);
	}

	/**
	 * Bekommt Wert von einem Array übergeben und wendet die Filterfunktion an
	 * @param $a
	 */
	private function cleanInputArray(&$a){
		$a = $this->cleanInputSingle($a);
	}

	/**
	 * Wendet die Filterfunktion auf einen Wert an
	 * @param $a
	 * @return string
	 */
	private function cleanInputSingle($a){
		return htmlspecialchars($a,ENT_QUOTES);
	}

	//Input auf vollständigkeit prüfen

	/**
	 * Prüft ob Inputwerte vollständig ausgefüllt sind
	 */
	private function checkInput(){
		if(is_array($this->value)){
			array_walk_recursive($this->value,array($this, 'checkArray'));
			return;
		}

		$this->check = $this->checkSingle($this->value);
	}

	/**
	 * Wendet die Prüffunktion für jeden Wert des Arrays an
	 * Wenn strict = false: gebe true zurück sobald ein Wert gesetzt wurde
	 * sonst prüfe jeden Wert ob dieser gesetzt ist
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
	 * Überprüft einen einzelnen Wert ob dieser gesetzt wurde
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