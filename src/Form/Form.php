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

use Gram\Project\Lib\Exceptions\Form\FormElementNotFoundException;
use Gram\Project\Lib\Form\Element\FormElementInterface;
use Gram\Project\Lib\Input;

/**
 * Class Form
 * @package Gram\Project\Lib\Form
 */
class Form implements FormInterface
{
	/** @var FormElementInterface[] */
	private $formElements = [];

	/**
	 * @inheritdoc
	 */
	public function addFormElement(FormElementInterface $formElement): void
	{
		$this->formElements[] = $formElement;
	}

	/**
	 * @inheritdoc
	 * @throws FormElementNotFoundException
	 */
	public function addFormElements(array $formElements): void
	{
		foreach ($formElements as $formElement) {
			if(! $formElement instanceof FormElementInterface) {
				throw new FormElementNotFoundException("Element is not from type FormElement!");
			}

			$this->addFormElement($formElement);
		}
	}

	/**
	 * @inheritdoc
	 */
	public function evaluateForm(Input $input): array
	{
		$status = true;

		$falseInput = [];

		$rightInput = [];

		foreach ($this->formElements as $formElement) {
			$result = $formElement->evaluateElement($input);

			if($result === false) {
				//wenn Input nicht gesetzt wurde
				$status = false;
				$falseInput[] = $formElement->getName();
			} else {
				//Wenn es den Input gibt
				$rightInput[$formElement->getName()] = $result;
			}
		}

		return [$status,$rightInput,$falseInput];
	}
}