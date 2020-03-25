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

namespace Gram\Project\Lib\Form;

use Gram\Project\Lib\Form\Element\FormElementInterface;
use Gram\Project\Lib\Input;

/**
 * Interface FormInterface
 * @package Gram\Project\Lib\Form
 *
 * Ein Form durchläuft seine Elemente
 * und gibt deren Inhalte zurück
 */
interface FormInterface
{
	/**
	 * Füge ein Form Element hinzu
	 *
	 * @param FormElementInterface $formElement
	 */
	public function addFormElement(FormElementInterface $formElement):void;

	/**
	 * Füge mehrere Elemente als Array hinzu
	 *
	 * @param FormElementInterface[] $formElements
	 */
	public function addFormElements(array $formElements):void;

	/**
	 * Wertet das Form aus
	 *
	 * @param Input $input
	 *
	 * @return array($status,$rightInput,$falseInput)
	 * Gebe den Status der Evaluation,
	 * die Inputs die richtig eingegeben wurden
	 * und die die falsch eingegeben wurden zurück
	 */
	public function evaluateForm(Input $input):array;

}