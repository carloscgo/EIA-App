<?php

echo $_SERVER['HTTP_HOST'];

define('DIR', '');
define('APP_DIR', DIR . '/');
define('APP_PATH', $_SERVER['DOCUMENT_ROOT'] . APP_DIR);

require_once APP_PATH . '/vendor/autoload.php';
