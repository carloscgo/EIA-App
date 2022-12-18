<?php

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
