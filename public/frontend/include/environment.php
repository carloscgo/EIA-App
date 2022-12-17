<?php
defined('ENV') || define('ENV', 'dev');

$dataBase = [
	'dev' => [
		'adapter'     => 'Mysql',
		'host'        => '172.10.0.14',
		'username'    => 'user',
		'password'    => 'secret',
		'dbname'      => 'db',
		'charset'     => 'utf8',
	]
];
