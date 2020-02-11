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

/**
 * Class Language
 * @package Gram\Project\Lib\View
 *
 * Language Implementation with JSON
 */
class Language implements LanguageInterface
{
	/** @var int|null */
	private $actualLang;

	/**
	 * Mit welcher Sprache gestartet werden soll
	 *
	 * @var int
	 */
	private $stdLang;

	/** @var string */
	private $langFilesPath;

	/**
	 * Static für async Requests
	 *
	 * @var array
	 */
	private static $file = [];

	/**
	 * Language constructor.
	 *
	 * @param $langFilesPath
	 * @param int $startLang
	 * Mit welcher Sprache gestartet werden soll
	 * (kann z. B. aus dem Cookie ausgelesen werden)
	 */
	public function __construct($langFilesPath, int $startLang = 0)
	{
		$this->langFilesPath = $langFilesPath;
		$this->stdLang = $startLang;
	}

	/**
	 * Gibt für die aktuelle Sprache die File zurück
	 *
	 * Sollte auf die File bereits zugegriffen sein
	 * wird diese nicht nochmal geladen
	 *
	 * @param $lang
	 * @param $side
	 * @return array
	 */
	private function getFileToLang($lang,$side)
	{
		if(!isset(self::$file[$lang][$side])) {
			$file = $this->langFilesPath.$lang.DIRECTORY_SEPARATOR.$side;

			self::$file[$lang][$side] = $this->loadFile($file);
		}

		return self::$file[$lang][$side];
	}

	/**
	 * Läd eine Json Sprachfile
	 *
	 * @param $path
	 * @return mixed
	 */
	private function loadFile($path)
	{
		$file = \file_get_contents($path.".json");

		return \json_decode($file, true);
	}

	/**
	 * @inheritdoc
	 */
	public function getCurrentLanguage()
	{
		if(!isset($this->actualLang)) {
			$this->actualLang = $this->stdLang;
		}

		return $this->actualLang;
	}

	/**
	 * @inheritdoc
	 */
	public function changeLang(int $lang)
	{
		$this->actualLang = $lang;
	}

	/**
	 * @inheritdoc
	 */
	public function get($side,$var): string
	{
		$lang = $this->getCurrentLanguage();

		$langFile = $this->getFileToLang($lang,$side);

		return $langFile[$var] ?? '';
	}

	/**
	 * @inheritdoc
	 */
	public function getAvailableLanguages()
	{
		return $this->loadFile($this->langFilesPath."config");
	}
}