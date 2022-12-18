<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Lib\App;
use App\Lib\Router;
use App\Lib\Request;
use App\Lib\Response;

use App\Controller\Contacts;

Router::get('/contacts', function () {
    $res = new Response();
    $contact = new Contacts();

    $res->toJSON($contact->allAction());
});

Router::get('/contacts/([0-9]*)', function ($request) {
    $req = new Request($request->params);
    $res = new Response();
    $contact = new Contacts();

    $res->toJSON($contact->findAction($req->params[0]));
});

Router::post('/contacts', function ($request) {
    $req = new Request($request->params);
    $res = new Response();
    $contact = new Contacts();

    $response = $contact->newAction($req->getJSON());

    $res->status(201)->toJSON($response);
});

Router::put('/contacts/([0-9]*)', function ($request) {
    $req = new Request($request->params);
    $res = new Response();
    $contact = new Contacts();

    $response = $contact->updateAction($req->getJSON(), $req->params[0]);

    $res->status(200)->toJSON($response);
});

Router::delete('/contacts/([0-9]*)', function ($request) {
    $req = new Request($request->params);
    $res = new Response();
    $contact = new Contacts();

    $response = $contact->deleteAction($req->getJSON(), $req->params[0]);

    $res->status(200)->toJSON($response);
});

Router::get('/(.*)', function () {
    $res = new Response();

    $res->status(404)->toJSON([
        "status" => false,
        "message" => "Resource not found"
    ]);
});

App::run();
