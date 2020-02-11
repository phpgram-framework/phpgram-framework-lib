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


interface StdViewInterface extends ViewInterface
{
	/**
	 * Startet den Outbutbuffer mit @see ob_start()
	 *
	 * Kann genutzt werden um Content Sections ein zufangen
	 *
	 * @return void
	 */
	public function start();

	/**
	 * Beendet den in @see start() gestarteten Ob
	 *
	 * @param $var
	 * besagt wie die Section (Inhalt des ob) heißen soll
	 *
	 * @return void
	 */
	public function end($var);

	/**
	 * Kann ein Template erweitern
	 *
	 * Das erweiterte Template hat dann Zugriff auf alle neuen und
	 * bereits vorhanden Variablen
	 *
	 * Neue Variablen können mit
	 * @see assign()
	 * und
	 * @see assignArray()
	 * hinzugefügt werden
	 *
	 * @param $tpl
	 * Das Template welches geladen werden soll
	 *
	 * @return void
	 */
	public function extend($tpl);

	/**
	 * Kann eine Varaible zuweisen
	 * diese wird dann für die extend Templates verfügbar sein
	 *
	 * @param $var
	 * Der Variablen Name
	 *
	 * @param $value
	 * Der Wert der Variable
	 *
	 * @return void
	 */
	public function assign($var,$value);

	/**
	 * Kann ein associative Array in Variablen umwandeln
	 *
	 * Array muss foldendes Format haben:
	 * ['var_name'=>value]
	 *
	 * @param $vars
	 * @return void
	 */
	public function assignArray($vars);

	/**
	 * Fügt eine neue Datei hinzu relativ zum tpl Pfad
	 *
	 * @param $file
	 * @return void
	 */
	public function include_file($file);

	/**
	 * Gibt eine vorher definierte Variable aus oder einen leeren String
	 * 
	 * @param $var
	 * @return mixed
	 */
	public function e($var);

	/**
	 * Kann einen String in verschiedenen Sprachen zurück geben
	 *
	 * Sollte Language gesetzt werden
	 *
	 * @param $side
	 * @param $var
	 * @return string
	 */
	public function lang($side,$var);
}