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
 * Class Element
 * @package Gram\Project\Lib\Form\Element
 *
 * Ein normales Form Element
 */
class Element implements FormElementInterface
{
	/** @var string */
	private $name;

	/** @var bool */
	private $check;

	/** @var bool */
	private $strict;

	/** @var bool */
	private $clean;

	/** @var callable|null */
	private $conditions;

	/**
	 * Element constructor.
	 *
	 * @param string $name
	 * @param bool $check
	 * @param bool $strict
	 * @param bool $clean
	 * @param callable|null $conditions
	 */
	public function __construct(
		string $name,
		bool $check = true,
		bool $strict = true,
		bool $clean = true,
		callable $conditions = null
	) {
		$this->name = $name;
		$this->check = $check;
		$this->strict = $strict;
		$this->clean = $clean;
		$this->conditions = $conditions;
	}

	/**
	 * @inheritdoc
	 *
	 * Prüfe mithilfe der Inputclass den Input
	 */
	public function evaluateElement(Input $input)
	{
		if(!$this->check) {
			$input = $input->get($this->name,$this->clean);
		} else {
			$input = $input->gNc($this->name,$this->strict,$this->clean);
		}

		if(isset($this->conditions)) {
			$conditions = $this->conditions;

			if($conditions($input) === false) {
				return false;
			}
		}

		return $input;
	}

	/**
	 * @inheritdoc
	 */
	public function getName(): string
	{
		return $this->name;
	}
}