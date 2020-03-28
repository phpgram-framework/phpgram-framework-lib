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

namespace Gram\Project\Lib\Form\Element;

use Gram\Project\Lib\Input;

/**
 * Interface FormElementInterface
 * @package Gram\Project\Lib\Form\Element
 *
 * Ein Element eines Forms
 *
 * Kann beliebig sein
 */
interface FormElementInterface
{
	/**
	 * Gibt den Namen des Elements zurück
	 *
	 * @return string
	 */
	public function getName():string;

	/**
	 * Überprüfe das Element mithilfe der Input Class
	 *
	 * @param Input $input
	 * @return mixed
	 */
	public function evaluateElement(Input $input);

	/**
	 * Gibt an ob die Evaluation erfolgreich war
	 *
	 * @return bool
	 */
	public function getStatus():bool;
}