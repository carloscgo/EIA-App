<?php

namespace App\Lib;

use App\Lib\TraitMySQL;

class Connection
{
    use TraitMySQL;

    private static $db = null;
    private static $server = null;
    private static $user = null;
    private static $password = null;
    private static $encoding = null;

    public static $messages = [];

    public function __construct($type = 'MYSQL')
    {
        self::$messages = json_decode(json_encode(Config::get('MESSAGES')));

        if ($type === 'MYSQL') {
            $config = Config::get('CONNECTION')[$type];

            self::$server = $config['host'];
            self::$encoding = $config['charset'];
            self::$user = $config['username'];
            self::$password = $config['password'];
            self::$db = $config['dbname'];

            $this->connectionMySQL();
        }
    }
}
