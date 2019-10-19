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

namespace Gram\Project\Lib\View;

use Gram\Project\Lib\Exceptions\TemplateNotFoundException;

class View implements ViewInterface
{
	protected $template, $path, $args;

	public function __construct($tplPath)
	{
		$this->path = $tplPath;
	}

	/**
	 * @inheritdoc
	 */
	public function view($template, array $variables = [])
	{
		$this->template = $template;
		$this->args = $variables;

		return $this;
	}

	/**
	 * @inheritdoc
	 * @throws TemplateNotFoundException
	 */
	public function render(): string
	{
		$file = $this->path . strtolower($this->template) . '.php';

		if (!file_exists($file)) {
			throw new TemplateNotFoundException('Template ' . $file . ' not found!');
		}

		extract($this->args);

		ob_start();
		require $file;
		$renderedView = ob_get_clean();

		ob_end_flush();

		return $renderedView;
	}
}