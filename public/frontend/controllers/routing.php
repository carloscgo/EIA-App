<?php

/**
 * Una clase de enrutamiento para el uso con los controladores que no utiliza la herencia.
 *
 * @autor Carlos G. Camacho O.
 * @license MIT License
 * @version 1.0
 * @Actualizacion 14/05/2015
 */

class Route
{

    private static function execute($to, $args)
    {
        $keys = explode('/', $to);
        $className = array_shift($keys);
        echo 'execute ' . $keys;
        echo 'execute ' . $className;

        if (!class_exists($className, false)) {
            include_once './controllers/' . $className . '/index.php';
        }

        $class = new $className();

        if (count($keys)) {
            $function = array_shift($keys);

            if (count($keys) && !@preg_match('/?/i', $args)) {
                $values = array();
                $values = array_slice($args, 1, count($keys));

                for ($i = 0; $i < count($keys); $i = ($i + 2)) {
                    $values[$keys[$i]] = $keys[$i + 1];
                }

                $class->$function($values);
            } else {
                $class->$function();
            }
        } else {
            $class->index();
        }

        $class->__destruct();
    }

    public static function process($uri, $mapping)
    {
        foreach ($mapping as $from => $to) {
            if (preg_match($from, $uri, $args)) {
                return self::execute($to, $args);
            }
        }

        return self::execute('errors/e404', null);
    }

    public function __destruct()
    {
        // codigo que implementa el destructor
    }
}

function return_id($url, $hack)
{
    $id = explode($hack, $url);

    return $id[1];
}

function route()
{
    $url = urldecode($_SERVER['REQUEST_URI']);
    echo $url;
    Route::process(
        $url,
        array(

            '#^(.*)/ajax/getContacts#i' => 'contacts/getContacts',

            '#^(.*)/404#Di' => 'errors/e404',

        )
    );
}

route();
