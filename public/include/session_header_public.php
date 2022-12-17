<?php
include_once '_class.session_secure.php';

SessionManager::sessionStart('_eia-front#' . $_SERVER['HTTP_HOST'] . '_public_');

// Chequea que ningun cliente llame directamente a este archivo
if (stristr($_SERVER['PHP_SELF'], 'session_header_public.php')) {
	header('Location: /');
	die();
}
