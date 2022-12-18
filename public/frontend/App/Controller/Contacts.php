<?php

namespace App\Controller;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use App\Lib\Config;


class Contacts
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

    private function response($request)
    {
        $res = $this->client->sendAsync($request)->wait();

        return $res->getBody()->getContents();
    }

    public function allAction()
    {
        $request = new Request('GET', '/contacts', [], '');

        return $this->response($request);
    }

    public function findAction(int $id)
    {
        $request = new Request('GET', "/contacts/$id", [], '');

        return $this->response($request);
    }

    public function newAction($contact)
    {
        $request = new Request('POST', "/contacts", [
            'Content-Type' => 'application/json'
        ], json_encode((array) $contact));

        return $this->response($request);
    }

    public function updateAction($contact, $id)
    {
        $request = new Request('PUT', "/contacts/$id", [
            'Content-Type' => 'application/json'
        ], json_encode((array) $contact));

        return $this->response($request);
    }

    public function deleteAction($params, $id)
    {
        $request = new Request('DELETE', "/contacts/$id", [
            'Content-Type' => 'application/json'
        ], json_encode((array) $params));

        return $this->response($request);
    }
}
