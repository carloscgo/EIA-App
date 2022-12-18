<?php

namespace App\Controller;

use GuzzleHttp\Psr7\Request;

class Contacts extends Main
{
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
