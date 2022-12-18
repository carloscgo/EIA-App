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
    private static $error = null;

    public static $typeConnection = null;
    public static $messages = [];

    public function __construct($type = 'MYSQL')
    {
        self::$messages = json_decode(json_encode(Config::get('MESSAGES')));

        self::$typeConnection = $type;

        $config = Config::get('CONNECTION')[$type];

        if ($this->isMySQL()) {
            self::$server = $config['host'];
            self::$encoding = $config['charset'];
            self::$user = $config['username'];
            self::$password = $config['password'];
            self::$db = $config['dbname'];

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

    public static function isMySQL()
    {
        return self::$typeConnection === 'MYSQL';
    }

    public static function isMongo()
    {
        return self::$typeConnection === 'MONGO';
    }
}
