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

namespace Gram\Project\Lib\DB;

use PDO;

class StdDB implements DBInterface
{
	/** @var PDO  */
	private $pdo;
	/** @var \PDOStatement */
	private $stmt;

	private $exe;

	private static $_instance = null;

	/**
	 * StdDB constructor.
	 *
	 * Init die PDO Instance
	 *
	 * Es kann angegeben werden ob Errors gezeigt werden sollen
	 *
	 * @param $host
	 * @param $dbName
	 * @param $charSet
	 * @param $dbUser
	 * @param $dbPw
	 * @param bool $error
	 */
	public function __construct($host,$dbName,$charSet,$dbUser,$dbPw,bool $error=true)
	{
		$this->pdo = new PDO(
			'mysql:host='.$host.';dbname='.$dbName.';charset='.$charSet.'',
			''.$dbUser.'',
			''.$dbPw.''
		);

		if($error){
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
	}

	/**
	 * @inheritdoc
	 */
	/**
	 * @param $sql
	 * @return bool|mixed|\PDOStatement
	 */
	public function prepare($sql):\PDOStatement
	{
		return $this->stmt = $this->pdo->prepare($sql);
	}

	/**
	 * @inheritdoc
	 */
	public function execute(array $args=[])
	{
		return $this->exe = $this->stmt->execute($args);
	}

	/**
	 * @inheritdoc
	 */
	public function query($sql, array $args = [])
	{
		$this->stmt = $this->pdo->prepare($sql);

		$this->exe = $this->stmt->execute($args);

		return $this->exe;
	}

	/**
	 * @inheritdoc
	 */
	public function getLastId()
	{
		return $this->pdo->lastInsertId();
	}

	/**
	 * @inheritdoc
	 */
	public function fetch($fetchStyle=null)
	{
		if(!$this->checkStmt()){
			return false;
		}

		return $this->stmt->fetch($fetchStyle);
	}

	/**
	 * @inheritdoc
	 */
	public function fetchAll($fetchStyle=null)
	{
		if(!$this->checkStmt()){
			return false;
		}

		return $this->stmt->fetchAll($fetchStyle);
	}

	/**
	 * @inheritdoc
	 */
	public function qNf($sql, array $args = [], $fetch = 0, $fetchStyle = null)
	{
		$query = $this->query($sql,$args);

		if($query===false || $fetch===0){
			return $query;
		}

		if($fetch===1){
			return $this->fetch($fetchStyle);
		}

		if ($fetch===2){
			return $this->fetchAll($fetchStyle);
		}

		return false;
	}

	/**
	 * Prüft ob ein Stmt und ein execute erfolgreich waren
	 *
	 * @return bool
	 */
	private function checkStmt()
	{
		return isset($this->stmt) && isset($this->exe) && $this->stmt!==false && $this->exe!==false;
	}

	/**
	 * @inheritdoc
	 */
	public function count($table = "", $count = "*", $where = "", array $args = [])
	{
		$sql="SELECT count($count) FROM $table WHERE $where";

		if($this->query($sql,$args)===false){
			return false;
		}

		return $this->stmt->fetchColumn();
	}

	/**
	 * @inheritdoc
	 */
	public function exist($table = "", $where = "", array $args = [])
	{
		$sql="SELECT * FROM $table WHERE $where LIMIT 1";

		if($this->query($sql)===false){
			return false;
		}

		return ($this->stmt->rowCount()>0);
	}

	/**
	 * @inheritdoc
	 * @return PDO
	 */
	public function getDB()
	{
		return $this->pdo;
	}

	//Diese Funktionen dürfen nicht aufgerufen werden von anderen Klassen
	private function __clone(){}
	private function __wakeup(){}

	/**
	 * Gibt PDO Objekt zurück
	 * Prüft ob bereits eine offene Datenbankverbindung besteht und gibt diese zurück
	 * Wenn nicht wird eine neue erstellt
	 * Datenbankquery wird so aufgerufen: StdDB::db()->q();
	 */
	public static function db() {
		if(!isset(self::$_instance)) {
			self::$_instance = new self(
				getenv("DBHOST"),
				getenv("DBNAME"),
				getenv("DBCHARSET"),
				getenv("DBUSER"),
				getenv("DBUSERPW")
			);
		}
		return self::$_instance;
	}
}