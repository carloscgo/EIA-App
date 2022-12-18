<?php

namespace App\Controller;

use GuzzleHttp\Psr7\Request;
use App\Controller\Main;

class Connection extends Main
{
    public function getAction()
    {
        $request = new Request('GET', '/connection', [], '');

        return $this->response($request);
    }

    public function setAction($type)
    {
        $request = new Request('PUT', "/connection", [
            'Content-Type' => 'application/json'
        ], json_encode((array) [
            "type" => $type
        ]));

        return $this->response($request);
    }
}
