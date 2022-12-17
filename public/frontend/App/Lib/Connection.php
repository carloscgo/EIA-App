<?php

namespace App\Lib;

class Connection
{

	private static $db = null;
	private static $server = null;
	private static $user = null;
	private static $password = null;
	private static $encoding = null;
	private static $error = null;

	public static $connection = null;
	private static $queryID = null;
	public static $messages = [];

	public function __construct($type = 'MYSQL')
	{
		$config = Config::get('CONNECTION')[$type];

		self::$server = $config['host'];
		self::$encoding	= $config['charset'];
		self::$user = $config['username'];
		self::$password = $config['password'];
		self::$db = $config['dbname'];

		self::$messages = json_decode(json_encode(Config::get('MESSAGES')));

		if ($type === 'MYSQL') {
			$this->connectionMySQL();
		}
	}

	public static function getError()
	{
		return self::$error;
	}

	public static function getDB()
	{
		return self::$db;
	}

	public static function connectionMySQL()
	{
		self::$connection = new \mysqli(self::$server, self::$user, self::$password, self::$db);

		if (self::$connection->connect_error) {
			header('location:/');
		} else {
			self::$connection->query("SET NAMES " . self::$encoding);
		}
	}

	public static function disconnect()
	{
		@self::$connection->close();
	}

	public static function dateNow()
	{
		return date('Y-m-d');
	}

	public static function releaseResults()
	{
		self::$connection = self::$connection->free_result(self::$queryID);

		return self::$connection;
	}

	public static function getNumRows($rsConn = null)
	{
		try {
			if ($rsConn) {
				return $rsConn->num_rows;
			} else {
				return 0;
			}
		} catch (\Exception $e) {
			self::$error = $e->getMessage();

			return 0;
		}
	}

	public static function fetchArray($rsConn = null)
	{
		try {
			if ($rsConn) {
				return $rsConn->fetch_assoc();
			} else {
				return 0;
			}
		} catch (\Exception $e) {
			self::$error = $e->getMessage();

			return 0;
		}
	}

	public static function affectedRows()
	{
		try {
			return self::$connection->affected_rows;
		} catch (\Exception $e) {
			self::$error = $e->getMessage();

			return 0;
		}
	}

	public static function beginTransaction()
	{
		self::$connection->autocommit(FALSE);
	}

	public static function commitTransaction()
	{
		self::$connection->commit();
	}

	public static function rollbackTransaction()
	{
		self::$connection->rollback();
	}

	public static function query($sql = "")
	{
		if ($sql == "") {
			throw new \Exception('No ha especificado una consulta SQL', 201);

			return 0;
		}

		try {
			$sql = self::utf8EncodeSecure($sql);

			self::$queryID = self::$connection->query($sql);

			if (self::$queryID) {
				return self::$queryID;
			} else {
				return 0;
			}
		} catch (\Exception $e) {
			self::$error = $e->getMessage() . ': ' . $e->getCode();

			return 0;
		}
	}

	public static function insert($tabla, $into, $values)
	{
		if ($tabla == "") {
			throw new \Exception('No ha especificado la tabla', 301);

			return 0;
		}
		if ($into == "") {
			throw new \Exception('No ha especificado los campos', 302);

			return 0;
		}
		if ($values == "") {
			throw new \Exception('No ha especificado los valores', 303);

			return 0;
		}

		try {
			if (self::checkFieldExists('created_at', $tabla)) {
				$into   .= ", created_at";
				$values .= ", now()";
			}

			$query = self::utf8EncodeSecure("INSERT INTO `$tabla` ($into) VALUES (" . trim($values) . ")");

			self::$queryID = @self::$connection->query($query);

			if (self::$queryID) {
				return self::$queryID;
			} else {
				return 0;
			}
		} catch (\Exception $e) {
			self::$error = $e->getMessage() . ': ' . $e->getCode();

			return 0;
		}
	}

	public static function update($tabla, $set, $where)
	{
		if ($tabla == "") {
			throw new \Exception('No ha especificado la tabla', 401);

			return 0;
		}
		if ($set == "") {
			throw new \Exception('No ha especificado las modificaciones', 402);

			return 0;
		}
		if ($where == "") {
			throw new \Exception('No ha especificado el registro a modificar', 403);

			return 0;
		}

		try {
			$query = self::utf8EncodeSecure("UPDATE `$tabla` SET " . trim($set) . " WHERE $where");

			self::$queryID = self::$connection->query($query);

			if (self::$queryID) {
				return self::$queryID;
			} else {
				return 0;
			}
		} catch (\Exception $e) {
			self::$error = $e->getMessage() . ': ' . $e->getCode();

			return 0;
		}
	}

	public static function delete($tabla, $where)
	{
		if ($tabla == "") {
			throw new \Exception('No ha especificado la tabla', 501);

			return 0;
		}
		if ($where == "") {
			throw new \Exception('No ha especificado el registro a eliminar', 502);

			return 0;
		}

		try {
			$query = "DELETE FROM `$tabla` WHERE $where";

			self::$queryID = self::$connection->query($query);

			if (self::$queryID) {
				return self::$queryID;
			} else {
				return 0;
			}
		} catch (\Exception $e) {
			self::$error = $e->getMessage() . ': ' . $e->getCode();

			return 0;
		}
	}

	public static function insertID()
	{
		try {
			return self::$connection->insert_id;
		} catch (\Exception $e) {
			self::$error = 'No se ha logrado capturar el ID del ultimo registro';

			return 0;
		}
	}

	private static function checkFieldExists($field, $tabla)
	{
		if ($tabla == "") {
			throw new \Exception('No ha especificado la tabla', 601);

			return 0;
		}
		if ($field == "") {
			throw new \Exception('No ha especificado el campo de consulta', 602);

			return 0;
		}

		try {
			$rs = self::query("SHOW FIELDS FROM `$tabla` WHERE FIELD = '$field'");

			return (self::getNumRows($rs) > 0 ? 1 : 0);
		} catch (\Exception $e) {
			self::$error = $e->getMessage() . ': ' . $e->getCode();

			return 0;
		}
	}

	public static function showData($tabla, $where)
	{
		if ($tabla == "") {
			throw new \Exception('No ha especificado la tabla', 701);

			return 0;
		}
		if ($where == "") {
			throw new \Exception('No ha especificado la condicion de la consulta', 702);

			return 0;
		}

		try {
			$sql = self::utf8EncodeSecure("SELECT * FROM `$tabla` WHERE $where");
			self::$queryID = self::$connection->query($sql);

			if (self::$queryID) {
				$data = '<br>';

				$fetch = array();

				while ($fetch = self::$queryID->fetch_assoc()) {
					$data .= "(TABLA AFECTADA `$tabla`, CONDICION $where) DATA: " . print_r($fetch, true) . "<br>";
				}

				return $data;
			} else {
				return 0;
			}
		} catch (\Exception $e) {
			self::$error = $e->getMessage() . ': ' . $e->getCode();

			return 0;
		}
	}

	private static function encode($text)
	{
		$c = 0;
		$ascii = true;

		for ($i = 0; $i < strlen($text); $i++) {
			$byte = ord($text[$i]);
			if ($c > 0) {
				if (($byte >> 6) != 0x2) {
					return 'ISO_8859_1';
				} else {
					$c--;
				}
			} elseif ($byte & 0x80) {
				$ascii = false;
				if (($byte >> 5) == 0x6) {
					$c = 1;
				} elseif (($byte >> 4) == 0xE) {
					$c = 2;
				} elseif (($byte >> 3) == 0x14) {
					$c = 3;
				} else {
					return 'ISO_8859_1';
				}
			}
		}

		return ($ascii) ? 'ASCII' : 'UTF_8';
	}

	private static function utf8EncodeSecure($text)
	{
		return (self::encode($text) == 'ISO_8859_1') ? utf8_encode($text) : $text;
	}

	public static function arrayToPHPArray($pgArray)
	{
		$ret = array();
		$pgArray    = substr($pgArray, 1, -1);
		$pgElements = explode(",", $pgArray);

		foreach ($pgElements as $elem) {
			if (substr($elem, -1) == "}") {
				$elem   = substr($elem, 0, -1);
				$newSub = array();

				while (substr($elem, 0, 1) != "{") {
					$newSub[] = $elem;
					$elem = array_pop($ret);
				}

				$newSub[] = substr($elem, 1);
				$ret[] = array_reverse($newSub);
			} else {
				$ret[] = $elem;
			}
		}

		return $ret;
	}
}
