<?php
// Chequea que ningun cliente llame directamente a este archivo
if (stristr($_SERVER['PHP_SELF'], 'funciones.php')) {
	header('Location: ../index.php');
	die();
}

function fecha($le)
{
	if ($le == "1") {
		setlocale(LC_TIME, "english");
		$long_date = ucwords(strftime("%A, %B %d, %Y"));
	}
	if ($le == "2") {
		setlocale(LC_TIME, "es_VE.UTF-8");
		$long_date = str_replace("De", "de", ucwords(strftime("%A, %d de %B de %Y")));
	}
	if ($le == "3") {
		setlocale(LC_TIME, "es_VE.UTF-8");
		$long_date = ucwords(strftime("%A %d, %B %Y"));
	}

	return utf8_encode_seguro($long_date);
}

function meses($mes)
{
	$meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

	return $meses[$mes - 1];
}

// paginado
$url = explode('_', isset($_GET["url"]) ? $_GET["url"] : ''); //paginado
$url_mod = $url[0]; //paginado
$data_per_pag = 25; //paginado 25
$rango_pag = 5; //paginado
$res = (isset($_GET['res']) ? $_GET['res'] : 0); //paginado
$show = ($res != 0 ? $res * $data_per_pag : 0); //paginado

function paginar($res, $show, $data_per_pag, $rango_pag, $total_data, $this_script, $ajax = false)
{
	global $url_mod;

	$nro_pags = ceil($total_data / $data_per_pag);
	$actual = $res;

	$anterior  = $actual - 1;
	$posterior = $actual + 1;

	$ak = $actual; //+1;
	$texto = "<p class=\"items-shown\"><span>$ak</span> de <span>$nro_pags</span> p&aacute;ginas</p>
			<div class=\"pagination-links\">
				<ul>";

	if ($actual > 1) {
		$texto .= "<li>
					<a href=\"$this_script?url=$url_mod&current=$anterior\" class=\"prev\">
						<span><i class=\"fa fa-angle-left\"></i></span> anterior
					</a>
				   </li>";
	} else {
		$texto .= "<li class=\"disabled\">
					<a class=\"prev\">
						<span><i class=\"fa fa-angle-left\"></i></span> anterior
					</a>
				   </li>";
	}

	$r1 = ($actual < $rango_pag ? 1 : $actual - ($rango_pag - 2));
	$r1 = ($r1 < 1 ? 1 : $r1);

	$r2 = (($actual + 1) == $nro_pags ? $nro_pags : $actual + $rango_pag);
	$r2 = ($r2 > $nro_pags ? $nro_pags : $r2);

	for ($i = $r1; $i <= $r2; $i++) {
		$ik = $i; //-1;

		if ($i == $ak) {
			$texto .= "<li class=\"disabled active\">
						<a>$ak</a>
					   </li>";
		} else {
			$texto .= "<li>
						<a href=\"$this_script?url=$url_mod&current=$ik\">
							$i
						</a>
					   </li>";
		}
	}

	if ($actual < $nro_pags) {
		$texto .= "<li>
					<a href=\"$this_script?url=$url_mod&current=$posterior\" class=\"next\">
						siguiente <span><i class=\"fa fa-angle-right\"></i></span>
					</a>
				   </li>";
	} else {
		$texto .= "<li class=\"disabled\">
					<a class=\"next\">
						siguiente <span><i class=\"fa fa-angle-right\"></i></span>
					</a>
				   </li>";
	}

	$texto .= "		</ul>
				</div>";

	return "<div class=\"pagination-area fix\">
				$texto
			</div>";
}

// Consulta 1 valor en especifico
function consulta_valor($tabla, $value, $arg, $conn)
{
	$sql = "SELECT $value AS valor FROM $tabla WHERE $arg";
	$rs  = $conn->DB_Consulta($sql);
	if ($row = $conn->DB_fetch_array($rs)) {
		$result = $row["valor"];
	} else {
		$result = "";
	}

	return $result;
}

// Funcion que cambia el formato de una fecha DD-MM-AAA, deacuerdo a lo requerido. Si recibe "es" devuelve: DD-MM-AAAA, de lo contrario devuelve el formato: AAAA-MM-DD
function traduce_fecha($fecha)
{
	if (strrpos($fecha, '-') !== false) {
		$str = explode("-", $fecha);
	}

	if (strrpos($fecha, '/') !== false) {
		$str = explode("/", $fecha);
	}

	return $str[2] . "-" . $str[1] . "-" . $str[0];
}

//Funcion que dado una fecha en formato ingles la convierte a espaol
function entosp($fecha_texto, $for = 0)
{
	$num = strtotime($fecha_texto);
	switch ($for) {
		case 0:
			$e = date('d/m/Y', $num);
			break;
		case 1:
			$e = date('d-m-Y h:i:s a', $num);
			break;
		case 2:
			$e = date('d', $num); // solo dia
			break;
		case 3:
			$e = date('m', $num); // solo mes
			break;
		case 4:
			$e = date('Y', $num); // solo a�
			break;
		case 5:
			$e = date('z', $num); // Numero de dia del a� (desde 1 hasta 365)
			break;
		case 6:
			$e = date('d F Y', $num); //formato en ingles
			break;
		case 7:
			$e = date('Y-m-d', $num);
			break;
		case 8:
			$e = date('m/d/Y', $num);
			break;
	}

	return $e;
}

// Transforma hora 24 unix a hora 12
function miltociv($hor_crea)
{
	$hor_crea = strtok(".");
	$hor = explode(":", $hor_crea);
	$hora = $hor[0] / 1;
	if ($hora <= 12)
		$hor_crea = $hora . ":" . $hor[1] . ":" . $hor[2] . " am";
	if ($hora > 12) {
		$hora = $hora - 12;
		$hor_crea = $hora . ":" . $hor[1] . ":" . $hor[2] . " pm";
	}
	if ($hora == 0)
		$hor_crea = "12" . ":" . $hor[1] . ":" . $hor[2] . " am";

	return $hor_crea;
}

function recortar_str($texto, $lng)
{
	return (strlen($texto) > $lng ? substr($texto, 0, $lng) . '...' : $texto);
}

function mostrar_fecha($fecha)
{
	$num = strtotime($fecha);
	$mes = date("m", $num);

	switch ($mes) {
		case 1:
		case '01':
			$mes = "Enero";
			break;
		case 2:
		case '02':
			$mes = "Febrero";
			break;
		case 3:
		case '03':
			$mes = "Marzo";
			break;
		case 4:
		case '04':
			$mes = "Abril";
			break;
		case 5:
		case '05':
			$mes = "Mayo";
			break;
		case 6:
		case '06':
			$mes = "Junio";
			break;
		case 7:
		case '07':
			$mes = "Julio";
			break;
		case 8:
		case '08':
			$mes = "Agosto";
			break;
		case 9:
		case '09':
			$mes = "Septiembre";
			break;
		case 10:
			$mes = "Octubre";
			break;
		case 11:
			$mes = "Noviembre";
			break;
		case 12:
			$mes = "Diciembre";
			break;
	}

	return date("d", $num) . ' de ' . $mes . ' de ' . date("Y", $num);
}

/*--------------------------------------------
* funcion para codicar de manera correcta los caracteres
*/
function codificacion($texto)
{
	$c = 0;
	$ascii = true;
	for ($i = 0; $i < strlen($texto); $i++) {
		$byte = ord($texto[$i]);
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

function utf8_encode_seguro($texto)
{
	return (codificacion($texto) == 'ISO_8859_1') ? utf8_encode($texto) : $texto;
}

function identidicar_ip_real()
{
	if ($_SERVER) {
		if ($_SERVER["HTTP_X_FORWARDED_FOR"]) {
			$realip = $_SERVER["HTTP_X_FORWARDED_FOR"] . "1";
		} elseif ($_SERVER["HTTP_CLIENT_IP"]) {
			$realip = $_SERVER["HTTP_CLIENT_IP"] . "2";
		} else {
			$realip = $_SERVER["REMOTE_ADDR"] . "3";
		}
	} else {
		if (getenv("HTTP_X_FORWARDED_FOR")) {
			$realip = getenv("HTTP_X_FORWARDED_FOR") . "4";
		} elseif (getenv("HTTP_CLIENT_IP")) {
			$realip = getenv("HTTP_CLIENT_IP") . "5";
		} else {
			$realip = getenv("REMOTE_ADDR") . "6";
		}
	}

	return $realip;
}

function strtonum($fecha_texto)
{
	$cadena = explode("-", $fecha_texto);
	$fecha_en = $cadena[2] . "/" . $cadena[1] . "/" . $cadena[0] . " " . $cadena[3];
	$num = strtotime($fecha_en);
	return $num;
}

function comprobar_email($email)
{
	$mail_correcto = 0;

	// compruebo unas cosas primeras
	if ((strlen($email) >= 6) && (substr_count($email, "@") == 1) && (substr($email, 0, 1) != "@") && (substr($email, strlen($email) - 1, 1) != "@")) {
		if ((!strstr($email, "'")) && (!strstr($email, "\"")) && (!strstr($email, "\\")) && (!strstr($email, "\$")) && (!strstr($email, " "))) {
			//miro si tiene caracter.
			if (substr_count($email, ".") >= 1) {
				//obtengo la terminacion del dominio
				$term_dom = substr(strrchr($email, '.'), 1);
				//compruebo que la terminación del dominio sea correcta
				if (strlen($term_dom) > 1 && strlen($term_dom) < 5 && (!strstr($term_dom, "@"))) {
					//compruebo que lo de antes del dominio sea correcto
					$antes_dom = substr($email, 0, strlen($email) - strlen($term_dom) - 1);
					$caracter_ult = substr($antes_dom, strlen($antes_dom) - 1, 1);
					if ($caracter_ult != "@" && $caracter_ult != ".") {
						$mail_correcto = 1;
					}
				}
			}
		}
	}

	if ($mail_correcto) {
		if (@preg_match("/^[a-zA-Z0-9_.-]{2,}@[a-zA-Z0-9_-]{2,}\.[a-zA-Z]{2,4}(\.[a-zA-Z]{2,4})?$/i", $email)) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}


function slug($text)
{
	$utf8 = array(
		'/[áàâãªäÁÀÂÃÄ]/u'  => 'a',
		'/[ÍÌÎÏíìîï]/u'     => 'i',
		'/[éèêëÉÈÊË]/u'     => 'e',
		'/[óòôõºöÓÒÔÕÖ]/u'  => 'o',
		'/[úùûüÚÙÛÜ]/u'     => 'u',
		'/[çÇ]/u'          	=> 'c',
		'/[ñÑ]/u'          	=> 'n',
		'/[|.,;:\/<>{}]/u' 	=> '-',
		'/[*+()$%&#@!¡?¿]/u' => '-',
		'/[’‘‹›‚]/u'    	=> '-',
		'/[“”«»„]/u'    	=> '-',
		'/ /'           	=> '-',
		'/-{2,}/i'			=> '-'
	);

	$vSlug = preg_replace(array_keys($utf8), array_values($utf8), $text);

	return strtolower($vSlug);
}

function titleHeader($title, $dir_ima = ".")
{
	print "
		<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"header_list\">
			<tr height=\"28px\">
				<td background=\"$dir_ima/images/header_left.gif\" width=\"8px\"></td>
				<td background=\"$dir_ima/images/header_back.gif\" align=\"center\"><b>$title</b></td>
				<td background=\"$dir_ima/images/header_right.gif\" width=\"8px\"></td>
			</tr>
		</table>";
}
