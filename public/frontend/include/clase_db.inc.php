<?php
if (stristr($_SERVER['PHP_SELF'], 'clase_db.inc.php')) { // evita que el script sea llamado directamente por el cliente
	header('location: /'); // enviandolo directamente al index
	die(); // Finaliza el script
}

class DB_Class
{

	// definir variables para la Conexionion
	private static $db 		= null;
	private static $servidor = null;
	private static $usuario = null;
	private static $clave 	= null;
	private static $encoding = null;
	private static $Error 	= null;

	// identificador de Conexionion y de consultas
	private static $Connection = null;
	private static $Query_ID = null;

	// Metodo Constructor: Cada vez que creemos una variable
	// de esta clase, se debe ejecutar esta funcion
	public function __construct()
	{
		self::DB_Init();
	}

	public static function DB_Init()
	{
		include_once 'funciones.php';
		include_once 'environment.php';

		$db_config = $dataBase[ENV];

		self::$servidor = $db_config['host'];
		self::$encoding	= $db_config['charset'];
		self::$usuario 	= $db_config['username'];
		self::$clave 	= $db_config['password'];
		self::$db 	    = $db_config['dbname'];

		self::DB_Conectar();
	}

	public static function get_Error()
	{
		return self::$Error;
	}

	public static function get_DB()
	{
		return self::$db;
	}

	// Connection a la base de datos
	public static function DB_Conectar()
	{
		self::$Connection = new mysqli(self::$servidor, self::$usuario, self::$clave, self::$db);

		if (self::$Connection->connect_error) {
			header('location:/');
		} else {
			self::$Connection->query("SET NAMES " . self::$encoding);
		}
	}

	public static function DB_Desconectar()
	{
		@self::$Connection->close();
	}

	// fecha actual para insertar
	public static function date_now()
	{
		return date('Y-m-d');
	}

	public static function DB_Freeres()
	{
		self::$Connection = self::$Connection->free_result(self::$Query_ID);

		return self::$Connection;
	}

	public static function DB_num_rows($rsConn = null)
	{
		try {
			if ($rsConn) {
				return $rsConn->num_rows;
			} else {
				return 0;
			}
		} catch (Exception $e) {
			self::$Error = $e->getMessage();
			return 0;
		}
	}

	public static function DB_fetch_array($rsConn = null)
	{
		try {
			if ($rsConn) {
				return $rsConn->fetch_assoc();
			} else {
				return 0;
			}
		} catch (Exception $e) {
			self::$Error = $e->getMessage();
			return 0;
		}
	}

	public static function DB_affected_rows()
	{
		try {
			return self::$Connection->affected_rows;
		} catch (Exception $e) {
			self::$Error = $e->getMessage();
			return 0;
		}
	}

	public static function DB_begin()
	{
		self::$Connection->autocommit(FALSE);
	}

	public static function DB_commit()
	{
		self::$Connection->commit();
	}

	public static function DB_rollback()
	{
		self::$Connection->rollback();
	}

	// Ejecutar una query
	public static function DB_Consulta($sql = "")
	{
		if ($sql == "") {
			throw new Exception('No ha especificado una consulta SQL', 201);
			return 0;
		}

		try {
			$sql = self::DB_utf8_encode_seguro($sql);

			self::$Query_ID = self::$Connection->query($sql);

			if (self::$Query_ID) {
				return self::$Query_ID;
			} else {
				return 0;
			}
		} catch (Exception $e) {
			self::$Error = $e->getMessage() . ': ' . $e->getCode();
			return 0;
		}
	}

	//Funciones especiales
	public static function DB_Insertar($tabla, $into, $values)
	{
		if ($tabla == "") {
			throw new Exception('No ha especificado la tabla', 301);
			return 0;
		}
		if ($into == "") {
			throw new Exception('No ha especificado los campos', 302);
			return 0;
		}
		if ($values == "") {
			throw new Exception('No ha especificado los valores', 303);
			return 0;
		}

		try {
			if (self::DB_field_exist('created_at', $tabla)) {
				$into   .= ", created_at";
				$values .= ", now()";
			}

			$query = self::DB_utf8_encode_seguro("INSERT INTO `$tabla` ($into) VALUES (" . trim($values) . ")");

			//ejecutamos la consulta
			self::$Query_ID = @self::$Connection->query($query);

			if (self::$Query_ID) {
				return self::$Query_ID;
			} else {
				return 0;
			}
		} catch (Exception $e) {
			self::$Error = $e->getMessage() . ': ' . $e->getCode();
			return 0;
		}
	}

	public static function DB_Modificar($tabla, $set, $where)
	{
		if ($tabla == "") {
			throw new Exception('No ha especificado la tabla', 401);
			return 0;
		}
		if ($set == "") {
			throw new Exception('No ha especificado las modificaciones', 402);
			return 0;
		}
		if ($where == "") {
			throw new Exception('No ha especificado el registro a modificar', 403);
			return 0;
		}

		try {
			$query = self::DB_utf8_encode_seguro("UPDATE `$tabla` SET " . trim($set) . " WHERE $where");

			//ejecutamos la consulta
			self::$Query_ID = self::$Connection->query($query);

			if (self::$Query_ID) {
				return self::$Query_ID;
			} else {
				return 0;
			}
		} catch (Exception $e) {
			self::$Error = $e->getMessage() . ': ' . $e->getCode();
			return 0;
		}
	}

	public static function DB_Eliminar($tabla, $where)
	{
		if ($tabla == "") {
			throw new Exception('No ha especificado la tabla', 501);
			return 0;
		}
		if ($where == "") {
			throw new Exception('No ha especificado el registro a eliminar', 502);
			return 0;
		}

		try {
			$query = "DELETE FROM `$tabla` WHERE $where";

			//ejecutamos la consulta
			self::$Query_ID = self::$Connection->query($query);

			if (self::$Query_ID) {
				return self::$Query_ID;
			} else {
				return 0;
			}
		} catch (Exception $e) {
			self::$Error = $e->getMessage() . ': ' . $e->getCode();
			return 0;
		}
	}

	public static function DB_Insert_ID()
	{
		try {
			return self::$Connection->insert_id;
		} catch (Exception $e) {
			self::$Error = 'No se ha logrado capturar el ID del ultimo registro';
			return 0;
		}
	}

	private static function DB_field_exist($field, $tabla)
	{
		if ($tabla == "") {
			throw new Exception('No ha especificado la tabla', 601);
			return 0;
		}
		if ($field == "") {
			throw new Exception('No ha especificado el campo de consulta', 602);
			return 0;
		}

		try {
			$rs = self::DB_Consulta("SHOW FIELDS FROM `$tabla` WHERE FIELD = '$field'");

			return (self::DB_num_rows($rs) > 0 ? 1 : 0);
		} catch (Exception $e) {
			self::$Error = $e->getMessage() . ': ' . $e->getCode();
			return 0;
		}
	}

	public static function DB_show_data($tabla, $where)
	{
		if ($tabla == "") {
			throw new Exception('No ha especificado la tabla', 701);
			return 0;
		}
		if ($where == "") {
			throw new Exception('No ha especificado la condicion de la consulta', 702);
			return 0;
		}

		try {
			$sql = self::DB_utf8_encode_seguro("SELECT * FROM `$tabla` WHERE $where");
			self::$Query_ID = self::$Connection->query($sql);

			if (self::$Query_ID) {
				$data = '<br>';

				$fetch = array();

				while ($fetch = self::$Query_ID->fetch_assoc()) {
					$data .= "(TABLA AFECTADA `$tabla`, CONDICION $where) DATA: " . print_r($fetch, true) . "<br>";
				}

				return $data;
			} else {
				return 0;
			}
		} catch (Exception $e) {
			self::$Error = $e->getMessage() . ': ' . $e->getCode();
			return 0;
		}
	}

	private static function DB_utf8_encode_seguro($texto)
	{
		return utf8_encode_seguro($texto);
	}

	public static function DBArrayToPHPArray($pgArray)
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
