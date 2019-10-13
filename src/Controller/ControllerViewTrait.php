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

use Gram\Project\Lib\View\Strategy\ViewStrategyInterface;

/**
 * Trait ControllerViewTrait
 * @package Gram\Project\Lib\Controller
 *
 * View Erweiterung
 */
trait ControllerViewTrait
{
	/** @var ViewStrategyInterface */
	protected $view;

	abstract protected function initView();

	/**
	 * @param $tpl
	 * @param array $vars
	 * @return ViewStrategyInterface
	 */
	protected function view($tpl,array $vars=[])
	{
		$this->initView();

		return $this->view->view($tpl,$vars);
	}
}