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

namespace Gram\Project\Lib\View;

interface ViewInterface
{
	/**
	 * Zeige ein übergebenes Tpl mit übergebenen Variables an
	 *
	 * Stelle dazu die Vars dem Tpl zur Verfügung
	 *
	 * @param $template
	 * @param array $variables
	 * @return string
	 */
	public function view($template,array $variables = []):string;
}