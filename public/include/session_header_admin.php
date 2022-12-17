<?php
include_once '_class.session_secure.php';

SessionManager::sessionStart('_eia-back#' . $_SERVER['HTTP_HOST'] . '_root_');

// Chequea que ningun cliente llame directamente a este archivo
if (stristr($_SERVER['PHP_SELF'], 'session_header_admin.php')) {
	header('Location: /');
	die();
}
