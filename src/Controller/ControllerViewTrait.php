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

use Gram\Project\Lib\View\ViewInterface;

/**
 * Trait ControllerViewTrait
 * @package Gram\Project\Lib\Controller
 *
 * View Erweiterung
 */
trait ControllerViewTrait
{
	/** @var ViewInterface */
	protected $view;

	protected function view($tpl,array $vars=[])
	{
		return $this->view->view($tpl,$vars);
	}
}