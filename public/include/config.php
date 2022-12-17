<?php
//Archivo de Configuracion
if (stristr($_SERVER['PHP_SELF'], 'config.php')) {
	header('Location: /');
	die();
}

require_once "environment.php";

$messages = array(
	'msj_si_insert' => 'Record successfully inserted',
	'msj_no_insert' => 'Failed to insert',

	'msj_si_edit' => 'Record successfully modified',
	'msj_no_edit' => 'Failed to Modify',

	'msj_si_del' => 'Record successfully deleted',
	'msj_no_del' => 'Failed to delete',

	'msj_no_con' => 'You do not have permission to consult'
);

define('DIR', '');

define('CONFIG', serialize($messages));
define('NAME_WEB', 'EIA - Test');
define('AUTHOR', serialize(array('site' => 'http://www.linkedin.com/in/carlos-camacho-29755043', 'name' => 'Carlos Camacho')));
