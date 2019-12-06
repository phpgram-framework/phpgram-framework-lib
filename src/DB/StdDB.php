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
	private $pdo;

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
	public function prepare($sql):\PDOStatement
	{
		return $this->pdo->prepare($sql);
	}

	/**
	 * @inheritdoc
	 */
	public function query($sql, array $args = [])
	{
		$stmt = $this->pdo->prepare($sql);

		return $stmt->execute($args);
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
	public function qNf($sql, array $args = [], $fetch = 0, $fetchStyle = null)
	{
		$stmt = $this->pdo->prepare($sql);

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

		$stmt = $this->pdo->prepare($sql);

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

		$stmt = $this->pdo->prepare($sql);

		if($stmt->execute($args)===false){
			return false;
		}

		return ($stmt->rowCount()>0);
	}

	/**
	 * @return PDO
	 */
	public function getDB():\PDO
	{
		return $this->pdo;
	}

	//Diese Funktionen dürfen nicht aufgerufen werden von anderen Klassen
	private function __clone(){}
}