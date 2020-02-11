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
 * Interface LanguageInterface
 * @package Gram\Project\Lib\View
 *
 * Ein Interface um Texte in unterschiedlichen Sprachen an zuzeigen
 *
 * Kann mit dem @see \Gram\Project\Lib\View\ViewInterface verbunden werden
 */
interface LanguageInterface
{
	/**
	 * Gibt für ein geg Var Namen den String in der
	 * aktuellen Sprache zurück
	 *
	 * @param $side
	 * @param $var
	 * @return string
	 */
	public function get($side,$var): string;

	/**
	 * Ändert eine aktuelle Sprache
	 *
	 * @param int $lang
	 * @return void
	 */
	public function changeLang(int $lang);

	/**
	 * Gibt alle erfügbaren Sprachen aus
	 *
	 * @return mixed
	 */
	public function getAvailableLanguages();

	/**
	 * Gibt die aktuelle Sprache zurück
	 *
	 * @return int
	 */
	public function getCurrentLanguage();
}