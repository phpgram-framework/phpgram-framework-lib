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

namespace Gram\Project\Lib\DB;

/**
 * Interface DBInterface
 * @package Gram\Project\Lib\DB
 *
 * Ein Datenbank Interface, dass Datenbanken Klassen implementieren können
 * um Datenbanken unkompleziert zu ändern
 */
interface DBInterface
{
	/**
	 * Bereitet ein Statement vor
	 *
	 * Kann dann von execute mit Parameter ausgeführt werden
	 *
	 * @param $sql
	 * @return \PDOStatement
	 */
	public function prepare($sql):\PDOStatement;

	/**
	 * Führt ein Sql query aus
	 *
	 * Gibt true zurück wenn Query erfolgreich
	 * sonst false
	 *
	 * @param string $sql
	 * @param array $args
	 * @return bool
	 */
	public function query($sql, array $args=[]);

	/**
	 * Gibt die Id des letzten Datenbankeintrags zurück
	 *
	 * @return mixed
	 */
	public function getLastId();

	/**
	 * Query and fetch
	 *
	 * Führt ein Query durch und fetch gleichzeitig die Datensätze
	 *
	 * @param $sql
	 * @param array $args
	 * @param int $fetch
	 * = 0 -> nicht fetchen nur true oder false
	 * = 1 -> fetch
	 * = 2 -> fetchAll
	 * @param null $fetchStyle
	 * @return mixed
	 */
	public function qNf($sql, array $args=[],$fetch=0,$fetchStyle=null);

	/**
	 * Zählt alle gefunden Datensätze in der Tabelle die mit dem
	 * $where gefunden wurden
	 *
	 * @param string $table
	 * @param string $count
	 * @param string $where
	 * @param array $args
	 * @return mixed
	 */
	public function count($table="",$count="*",$where="",array $args=[]);

	/**
	 * Prüft ob ein Datensatz in einer Tabelle existert
	 *
	 * @param string $table
	 * @param string $where
	 * @param array $args
	 * @return mixed
	 */
	public function exist($table="",$where="",array $args=[]);

	/**
	 * @return \PDO
	 */
	public function getDB():\PDO;
}