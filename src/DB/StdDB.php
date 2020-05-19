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

use PDO;

class StdDB implements DBInterface
{
	/** @var PDO  */
	protected $pdo;

	/** @var string Driver der DB z. B. mysql */
	private $driver;

	/** @var string Host der DB */
	private $host;

	/** @var string|int Port der DB*/
	private $port;

	/** @var string Welche DB */
	private $dbName;

	/** @var string User */
	private $dbUser;

	/** @var string Password des Users */
	private $dbPw;

	/** @var bool */
	private $error;

	/** @var bool */
	private $reconnect;

	/**
	 * StdDB constructor.
	 *
	 * Init die PDO Instance
	 *
	 * Es kann angegeben werden ob Errors gezeigt werden sollen
	 *
	 * @param $driver
	 * @param $host
	 * @param $port
	 * @param $dbName
	 * @param $dbUser
	 * @param $dbPw
	 * @param bool $error
	 * @param bool $reconnect
	 */
	public function __construct($driver, $host, $port, $dbName, $dbUser, $dbPw, bool $error=true, bool $reconnect= false)
	{
		$this->driver = $driver;
		$this->host = $host;
		$this->port = $port;
		$this->dbName = $dbName;
		$this->dbUser = $dbUser;
		$this->dbPw = $dbPw;
		$this->error = $error;
		$this->reconnect = $reconnect;

		$this->init();
	}

	/**
	 * @inheritdoc
	 */
	public function prepare($sql):\PDOStatement
	{
		return $this->pdo()->prepare($sql);
	}

	/**
	 * @inheritdoc
	 */
	public function query($sql, array $args = [])
	{
		$stmt = $this->pdo()->prepare($sql);

		return $stmt->execute($args);
	}

	/**
	 * @inheritdoc
	 */
	public function getLastId()
	{
		return $this->pdo()->lastInsertId();
	}

	/**
	 * @inheritdoc
	 */
	public function qNf($sql, array $args = [], $fetch = 0, $fetchStyle = null)
	{
		$stmt = $this->pdo()->prepare($sql);

		$query = $stmt->execute($args);

		if($query===false || $fetch===0){
			return $query;
		}

		if($fetch===1){
			return $stmt->fetch($fetchStyle);
		}

		if ($fetch===2){
			return $stmt->fetchAll($fetchStyle);
		}

		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function count($table = "", $count = "*", $where = "", array $args = [])
	{
		$sql="SELECT count($count) FROM $table WHERE $where";

		$stmt = $this->pdo()->prepare($sql);

		if($stmt->execute($args)===false){
			return false;
		}

		return $stmt->fetchColumn();
	}

	/**
	 * @inheritdoc
	 */
	public function exist($table = "", $where = "", array $args = [])
	{
		$sql="SELECT * FROM $table WHERE $where LIMIT 1";

		$stmt = $this->pdo()->prepare($sql);

		if($stmt->execute($args)===false){
			return false;
		}

		return ($stmt->rowCount()>0);
	}

	private function pdo(): PDO
	{
		if($this->reconnect) {
			//Prüfe die Verbindung, wenn nicht mehr verfügbar -> erstelle neue
			try{
				$this->pdo->query("SELECT 1");
			} catch (\PDOException $e) {
				$this->init();
			}
		}

		return $this->pdo;
	}

	private function init()
	{
		if($this->driver == "mysql") {
			//Bei Mysql ein Charset angeben
			$charset = "charset=utf8;";
		} else {
			$charset = "";
		}

		$this->pdo = new PDO("{$this->driver}:" . sprintf(
				"host=%s;port=%s;dbname=%s;$charset",
				$this->host,
				$this->port,
				$this->dbName
			),$this->dbUser,$this->dbPw);

		if($this->error){
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
	}

	/**
	 * @return PDO
	 */
	public function getDB():\PDO
	{
		return $this->pdo();
	}

	//Diese Funktionen dürfen nicht aufgerufen werden von anderen Klassen
	private function __clone(){}
}