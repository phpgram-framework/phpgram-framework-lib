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

namespace Gram\Project\Lib\View;

interface ViewInterface
{
	/**
	 * Speichert die Template Param
	 *
	 * @param $template
	 * @param array $variables
	 * @return ViewInterface
	 */
	public function view($template, array $variables = []);

	/**
	 * Rendert das Tempalte mit den Parametern
	 *
	 * @return string
	 */
	public function render():string;
}