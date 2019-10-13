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

namespace Gram\Project\Lib\Controller;

use Gram\Middleware\Classes\ClassInterface;
use Gram\Middleware\Classes\ClassTrait;
use Gram\Project\Lib\Input;
use Gram\Project\Lib\View\Strategy\View;

/**
 * Class BaseController
 * @package Gram\Project\Lib\Controller
 *
 * Controller um Basiselemente von phpgram project zu nutzen
 *
 * Nutzt Traits damit nicht zwingend geerbt werden muss
 */
abstract class BaseController implements ClassInterface
{
	use ClassTrait, ControllerInputTrait, ControllerViewTrait;

	protected function initInput()
	{
		if($this->input === null){
			$input = $this->request->getAttribute('InputClass',null);

			if($input===null){
				$this->input = new Input($this->request);
			}
		}
	}

	protected function initView()
	{
		if($this->view=== null){
			$this->view = new View(TEMPLATES);
		}
	}
}