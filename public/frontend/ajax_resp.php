<?php
// Chequea que ningun cliente llame directamente a este archivo
if (stristr($_SERVER['PHP_SELF'], 'funciones.php')) {
	header('Location: ../index.php');
	die();
}

include_once '../include/session_header_public.php';
include_once '../include/config.php';
include_once '../include/funciones.php';

function loadController($className)
{
	include_once $_SERVER["DOCUMENT_ROOT"] . '/controllers/' . $className . '/index.php';

	// Verificar si la sentencia include declaró la clase
	if (!class_exists($class, false)) :
		trigger_error('No es posible cargar la clase: ' . $className, E_USER_WARNING);
	endif;
}

if (version_compare(PHP_VERSION, '5.1.2', '>=')) {
	//SPL autoloading was introduced in PHP 5.1.2
	if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
		spl_autoload_register(function ($className) {
			loadController($className);
		}, true, true);
	} else {
		spl_autoload_register(function ($className) {
			loadController($className);
		});
	}
} else {
	/**
	 * Fall back to traditional autoload for old PHP versions
	 * @param string $className The name of the class to load
	 */
	function __autoload($className)
	{
		loadController($className);
	}
}


class AjaxResp
{
	static $dB = null;
	public static $CONFIG;

	public function __construct()
	{
		self::$dB = new DB_Class();
		self::$CONFIG = json_decode(json_encode(unserialize(CONFIG), JSON_FORCE_OBJECT));

		self::index();
	}

	private static function getContacts()
	{
		$status = false;
		$message = '';
		$row	= array();

		$sql = 'SELECT id, firstname, lastname, email, phone, created_at, updated_at
				FROM contacts
				WHERE deleted_at IS NULL';

		$rs = self::$dB->DB_Consulta($sql);

		if (!$row = self::$dB->DB_fetch_array($rs)) {
			$message = self::$CONFIG->msj_no_con;
			$row = array();
		}

		echo json_encode(array(
			'status' => $status,
			'message' => $message,
			'data'	 => $row
		));
	}

	public static function index()
	{

		switch ($_POST['acc']):
			case 1:
				$accion    = 'N';
				$max_cert  = 4;
				$cant_cert = 0;
				$list_cert = array();

				if (is_numeric($_POST['id'])) :
					$sql = 'SELECT * 
							FROM view_pagos_pendientes 
							WHERE id_cliente = ' . $_POST['id'] . ' 
								AND pendiente = 0';
					$rs  = self::$dB->DB_Consulta($sql);
					if ($row = self::$dB->DB_fetch_array($rs)) :
						$sql = 'SELECT num_cert, status
								FROM certificados 
								WHERE id_sponsor = ' . $_POST['id'];
						$rs  = self::$dB->DB_Consulta($sql);
						while ($row = self::$dB->DB_fetch_array($rs)) {
							$cant_cert++;
							$list_cert[] = array(
								'num_cert' => $row['num_cert'],
								'status' => $row['status']
							);
						}

						if ($cant_cert < $max_cert) :
							$accion = 'S';
						else :
							$datos = general::closed_cycles($_POST['id']);
							if ($datos['referidos'] == $datos['ciclos']) :
								$max_cert = $datos['ciclos'];
							endif;
						endif;
					endif;
				endif;

				echo json_encode(array(
					'accion' 	=> $accion,
					'cant_cert' => $cant_cert,
					'max_cert'  => $max_cert,
					'list_cert' => $list_cert
				));
				break;

			case 3:
				$result = 'N';

				if (is_numeric($_POST['id_vaucher'])) :
					$rs_mod = self::$dB->DB_Modificar('vauchers', 'entregado = (CASE WHEN entregado = \'1\' THEN \'0\' ELSE \'1\' END)', "id_vaucher = '" . $_POST['id_vaucher'] . "'");
					if ($rs_mod) :
						$result = "S";
					endif;
				endif;

				echo json_encode(array(
					'accion' => $result
				));
				break;

			case 4:
				$result = 'N';
				$mensaje = '';

				if ($_POST['comment'] != '' && is_numeric($_POST['id'])) :
					$rs_ins = self::$dB->DB_Insertar('vauchers_comentarios', 'id_vaucher, observacion', $_POST['id'] . ", '" . utf8_encode_seguro(trim($_POST['comment'])) . "'");
					if ($rs_ins) :
						$result = 'S';
						$mensaje = 'El Comentario se agregó correctamente.';
					endif;
				endif;

				echo json_encode(array(
					'accion'  => $result,
					'mensaje' => $mensaje
				));
				break;

			case 5:
				$result = 'N';
				$mensaje = 'El Correo Electrónico Ingresado es Invalido!';

				if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) :
					if (in_array(strtolower(explode('.', explode('@', $_POST['email'])[1])[0]), array('gmail', 'outlook', 'hotmail', 'yahoo'), true)) :
						$result = 'S';
					else :
						$mensaje = 'El Correo Electrónico debe ser Gmail, Outlook, Hotmail o Yahoo!';
					endif;
				endif;

				echo json_encode(array(
					'accion'  => $result,
					'mensaje' => $mensaje
				));
				break;

			case 6:
				$result = 'N';
				$mensaje = 'La Cédula de Identidad Ingresada se encuentra Registrada!';

				if (filter_var($_POST['cedula'], FILTER_VALIDATE_INT)) :
					$sql = "SELECT id_cliente
							FROM view_clientes
							WHERE cedula = '" . $_POST['cedula'] . "'";
					$rs = self::$dB->DB_Consulta($sql);

					if (self::$dB->DB_num_rows($rs) == 0) :
						$result = 'S';
					endif;
				endif;

				echo json_encode(array(
					'accion'  => $result,
					'mensaje' => $mensaje
				));
				break;

			case 7:
				$result = 'N';
				$mensaje = 'El Código del Certificado Ingresado no corresponde a la Cédula de Identidad del Patrocinador!';
				$field  = 'ci_sponsor';

				if (is_numeric($_POST['cedula']) && $_POST['num_cert'] != '') :
					$sql = "SELECT c.status
							FROM certificados AS c, view_clientes AS v
							WHERE c.id_sponsor = v.id_cliente
								AND cedula = '" . $_POST['cedula'] . "'
								AND num_cert = '" . strtoupper($_POST['num_cert']) . "'";
					$rs = self::$dB->DB_Consulta($sql);

					if ($row = self::$dB->DB_fetch_array($rs)) :
						if ($row['status'] == 'E') :
							$result = 'S';
						else :
							$mensaje = 'El Código del Certificado Ingresado se encuentra utilizado!';
							$field  = 'num_cert';
						endif;
					endif;
				endif;

				echo json_encode(array(
					'accion'  => $result,
					'field'   => $field,
					'mensaje' => $mensaje
				));
				break;
		endswitch;
	}
}

$ajax_resp = new AjaxResp();

unset($ajax_resp);

$ajax_resp = null;
