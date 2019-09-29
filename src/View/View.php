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

use Exception;

class View implements ViewInterface
{
	private $tplPath, $tpl;

	public function __construct($tplPath)
	{
		$this->tplPath = $tplPath;
	}

	/**
	 * @inheritdoc
	 *
	 * Holt sich das Tpl
	 *
	 * Extract die Variables aus dem Array
	 */
	public function view($template, array $variables = []):string
	{
		$this->render($template);

		extract($variables);

		//startet den Output Buffer: bis zu clean() wird alles gesammelt und nicht ausgegeben
		ob_start();
		require $this->tpl;
		$renderedView = ob_get_clean(); //übergibt alles was im Outputbuffer gesammelt wurde und beendet ihn.

		return $renderedView;
	}

	/**
	 * Holt das Tpl
	 *
	 * @param $tpl
	 */
	private function render($tpl)
	{
		try {
			$file = $this->tplPath . strtolower($tpl) . '.php';

			if (file_exists($file)) {
				$this->tpl = $file;
			} else {
				throw new Exception('Template ' . $file . ' not found!');
			}
		}
		catch (Exception $e) {
			echoExep($e);
		}
	}
}