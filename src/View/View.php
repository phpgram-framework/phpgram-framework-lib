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

use Gram\Project\Lib\Exceptions\TemplateNotFoundException;

/**
 * Class View
 * @package Gram\Project\Lib\View
 *
 * Die Standard View Klasse
 */
class View implements StdViewInterface
{
	protected $template=null, $path, $args=[], $extendtpl=null;

	/** @var LanguageInterface|null */
	protected $language;

	public function __construct($tplPath, ?LanguageInterface $language = null)
	{
		$this->path = $tplPath;
		$this->language = $language;
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
	 *
	 * Führt sich ggf. selber wieder aus wenn das Template von einem anderen erbt
	 * Ruft dann das neue Template mit den neu gesammelten Variablen auf
	 *
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

		if($this->extendtpl!==null){
			ob_end_clean();	//leere den Buffer
			$this->template = $this->extendtpl;
			$this->extendtpl = null;
			return $this->render();
		}

		$renderedView = ob_get_clean();

		return $renderedView;
	}

	/**
	 * @inheritdoc
	 */
	public function start()
	{
		ob_start();
	}

	/**
	 * @inheritdoc
	 */
	public function end($var)
	{
		$this->args[$var]= ob_get_clean();
	}

	/**
	 * @inheritdoc
	 */
	public function extend($tpl)
	{
		$this->extendtpl = $tpl;
	}

	/**
	 * @inheritdoc
	 */
	public function assign($var,$value)
	{
		$this->args[$var]=$value;
	}

	/**
	 * @inheritdoc
	 */
	public function assignArray($vars)
	{
		if(!is_array($vars)){
			return;
		}

		foreach ($vars as $var=>$value) {
			$this->assign($var,$value);
		}
	}

	/**
	 * @inheritdoc
	 * @throws TemplateNotFoundException
	 */
	public function include_file($file)
	{
		$file = $this->path.strtolower($file) . '.php';

		if (!file_exists($file)) {
			throw new TemplateNotFoundException('Template ' . $file . ' not found!');
		}

		include $file;
	}

	/**
	 * @inheritdoc
	 */
	public function e($var)
	{
		return $this->args[$var] ?? '';
	}

	/**
	 * @inheritdoc
	 */
	public function lang($side,$var)
	{
		if(!isset($this->language)) {
			return '';
		}

		return $this->language->get($side,$var);
	}
}