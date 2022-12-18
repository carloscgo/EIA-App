<?php

namespace App\Controller;

use GuzzleHttp\Client;
use App\Lib\Config;

class Main
{
    private $client;

    public function __construct()
    {
        $hostAPI = Config::get('CONNECTION')['API']['host'];

        $this->client = new Client([
            'base_uri' => $hostAPI,
            'timeout'  => 2.0,
        ]);
    }

    public function response($request)
    {
        $res = $this->client->sendAsync($request)->wait();

        return $res->getBody()->getContents();
    }
}
