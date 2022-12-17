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

        if (!class_exists($className, false)) :
            include_once APP_PATH . '/controllers/' . $className . '/index.php';
        endif;

        $class = new $className();

        if (count($keys)) :
            $function = array_shift($keys);

            if (count($keys) && !@preg_match('/?/i', $args)) :
                $values = array();
                $values = array_slice($args, 1, count($keys));

                for ($i = 0; $i < count($keys); $i = ($i + 2)) {
                    $values[$keys[$i]] = $keys[$i + 1];
                }

                $class->$function($values);
            else :
                $class->$function();
            endif;
        else :
            $class->index();
        endif;

        $class->__destruct();
    }

    public static function process($uri, $mapping)
    {
        foreach ($mapping as $from => $to) :
            if (preg_match($from, $uri, $args)) :
                return self::execute($to, $args);
            endif;
        endforeach;

        return self::execute('errors/e404', null);
    }

    public function __destruct()
    {
        //codigo que implementa el destructor
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

    Route::process(
        $url,
        array(

            '#^(.*)/ajax/getSelect#i'            => 'general/getSelect',
            '#^(.*)/ajax/send/contact#i'        => 'contact/sendMail',
            '#^(.*)/ajax/newsletter/suscribe#i'    => 'newsletter/suscribe',

            '#^(.*)/ajax/getArtists/search#Di' => 'artists/getListTypeahead/search/' . return_id($url, 'search/'),
            '#^(.*)/ajax/getGallery/search#Di' => 'gallery/getListTypeahead/search/' . return_id($url, 'search/'),
            '#^(.*)/ajax/getBlog/search#Di'    => 'blog/getListTypeahead/search/' . return_id($url, 'search/'),

            /* BEGIN Store */

            '#^(.*)/ajax/store/showCart#i'   => 'store/showToCart',
            '#^(.*)/ajax/store/delProduct#i' => 'store/delToCart',
            '#^(.*)/ajax/store/addProduct#i' => 'store/addToCart',
            '#^(.*)/ajax/store/modProduct#i' => 'store/modToCart',
            '#^(.*)/ajax/store/validateDiscountCode#i' => 'store/validateDiscountCode',

            '#^(.*)/store/buy/product/#Di'     => 'store/product/type/product/slug/' . return_id($url, 'product/'),
            '#^(.*)/store/buy/album/#Di'     => 'store/product/type/album/slug/' . return_id($url, 'album/'),
            '#^(.*)/store/buy/song/#Di'     => 'store/product/type/song/slug/' . return_id($url, 'song/'),
            '#^(.*)/store/cart#i'              => 'store/cart',
            '#^(.*)/payment/verify#i'          => 'store/payment_verify',
            '#^(.*)/payment/notify#i'          => 'store/payment_notify',
            '#^(.*)/ajax/music/downloadFile/#Di'     => 'store/downloadFile/type/song/file/' . return_id($url, 'downloadFile/'),
            '#^(.*)/ajax/album/downloadFile/#Di'     => 'store/downloadFile/type/album/file/' . return_id($url, 'downloadFile/'),
            '#^(.*)/ajax/preorder/downloadFile/#Di' => 'store/downloadFile/type/preorder/file/' . return_id($url, 'downloadFile/'),

            /* END Store */

            '#^(.*)/preorder#Di'         => 'landing',

            '#^(.*)/contact#Di'            => 'contact',
            '#^(.*)/blog/post/#Di'        => 'blog/index/slug/' . return_id($url, 'post/'),
            '#^(.*)/blog#Di'            => 'blog',
            '#^(.*)/store#Di'            => 'store',
            '#^(.*)/gallery-images#Di'    => 'gallery/images',
            '#^(.*)/gallery-videos#Di'    => 'gallery/videos',
            '#^(.*)/artist/info/#Di'    => 'artists/index/slug/' . return_id($url, 'info/'),
            '#^(.*)/artists#Di'            => 'artists',
            '#^(.*)/studio/info/#Di'    => 'studio/index/slug/' . return_id($url, 'info/'),
            '#^(.*)/studio#Di'            => 'studio',
            '#^(.*)/home#D'                => 'home',
            '#^(.*)/$#'                    => 'home',

            '#^(.*)/404#Di'     =>     'errors/e404',

        )
    );
}

route();
