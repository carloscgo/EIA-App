<?php

class Contacts extends ContactsController
{
    public static $data = array();

    public function __construct()
    {
        parent::__construct();

        self::setData($_POST + $_GET);
    }

    public static function setData($datos)
    {
        self::$data = $datos;
        parent::$data = $datos;
    }
}
