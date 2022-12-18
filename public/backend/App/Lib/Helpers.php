<?php

use App\Lib\Config;

session_id('frontend');
session_start();

function validateEmail($email)
{
    return @preg_match("/^[a-zA-Z0-9_.\-\+]{2,}@[a-zA-Z0-9_-]{2,}\.[a-zA-Z]{2,4}(\.[a-zA-Z]{2,4})?$/i", $email);
}

function validatePhone($phone)
{
    return @preg_match("/^[0-9-]{2,}?$/i", $phone);
}

function getErrors($errors)
{
    return [
        'status' => false,
        'message' => implode('. ', $errors),
        'data' => (object) []
    ];
}

function setTypeConnection($type)
{
    $_SESSION['TYPE_CONNECTION'] = $type;
}

function getTypeConnection()
{
    return $_SESSION['TYPE_CONNECTION'] ? $_SESSION['TYPE_CONNECTION'] : Config::get('CONNECTION')['DEFAULT'];
}
