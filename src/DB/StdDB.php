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
	public function query($sql, array $args = [])
	{
		try{
			$this->stmt = $this->pdo->prepare($sql);

			$this->exe = $this->stmt->execute($args);

			return $this->exe;
		}catch (\PDOException $e){
			echoExep($e);
			return false;
		}
	}

	public function getLastId()
	{
		return $this->pdo->lastInsertId();
	}

	public function fetch($fetchStyle=null)
	{
		if(!$this->checkStmt()){
			return false;
		}

		try{
			return $this->stmt->fetch($fetchStyle);
		}catch (\PDOException $e){
			echoExep($e);
			return false;
		}
	}

	public function fetchAll($fetchStyle=null)
	{
		if(!$this->checkStmt()){
			return false;
		}

		try{
			return $this->stmt->fetchAll($fetchStyle);
		}catch (\PDOException $e){
			echoExep($e);
			return false;
		}
	}

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

	private function checkStmt()
	{
		return isset($this->stmt) && isset($this->exe) && $this->stmt!==false && $this->exe!==false;
	}

	public function count($table = "", $count = "*", $where = "", array $args = [])
	{
		$sql="SELECT count($count) FROM $table WHERE $where";

		if($this->query($sql)===false){
			return false;
		}

		return $this->stmt->fetchColumn();
	}

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
}