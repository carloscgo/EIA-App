<?php

function validateEmail($email)
{
    $emailChecked = false;

    // compruebo unas cosas primeras
    if ((strlen($email) >= 6) && (substr_count($email, "@") == 1) && (substr($email, 0, 1) != "@") && (substr($email, strlen($email) - 1, 1) != "@")) {
        if ((!strstr($email, "'")) && (!strstr($email, "\"")) && (!strstr($email, "\\")) && (!strstr($email, "\$")) && (!strstr($email, " "))) {
            //miro si tiene caracter.
            if (substr_count($email, ".") >= 1) {
                //obtengo la terminacion del dominio
                $domain = substr(strrchr($email, '.'), 1);
                //compruebo que la terminación del dominio sea correcta
                if (strlen($domain) > 1 && strlen($domain) < 5 && (!strstr($domain, "@"))) {
                    //compruebo que lo de antes del dominio sea correcto
                    $userMail = substr($email, 0, strlen($email) - strlen($domain) - 1);
                    $charDot = substr($userMail, strlen($userMail) - 1, 1);
                    if ($charDot != "@" && $charDot != ".") {
                        $emailChecked = true;
                    }
                }
            }
        }
    }

    if ($emailChecked) {
        return @preg_match("/^[a-zA-Z0-9_.-]{2,}@[a-zA-Z0-9_-]{2,}\.[a-zA-Z]{2,4}(\.[a-zA-Z]{2,4})?$/i", $email);
    } else {
        return false;
    }
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
