<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Lib\App;
use App\Lib\Router;
use App\Lib\Request;
use App\Lib\Response;

use App\Controller\Contacts;

Router::get('/contacts', function () {
    $res = new Response();

    $contact = new Contacts(getTypeConnection());

    $res->toJSON($contact->allAction());
});

Router::get('/contacts/([0-9]*)', function ($request) {
    $req = new Request($request->params);
    $res = new Response();
    $contact = new Contacts(getTypeConnection());

    $res->toJSON($contact->findAction($req->params[0]));
});

Router::post('/contacts', function ($request) {
    $req = new Request($request->params);
    $res = new Response();
    $contact = new Contacts(getTypeConnection());

    $response = $contact->newAction($req->getJSON());

    $res->status(201)->toJSON($response);
});

Router::put('/contacts/([0-9]*)', function ($request) {
    $req = new Request($request->params);
    $res = new Response();
    $contact = new Contacts(getTypeConnection());

    $response = $contact->updateAction($req->getJSON(), $req->params[0]);

    $res->status(200)->toJSON($response);
});

Router::delete('/contacts/([0-9]*)', function ($request) {
    $req = new Request($request->params);
    $res = new Response();
    $contact = new Contacts(getTypeConnection());

    $response = $contact->deleteAction($req->getJSON(), $req->params[0]);

    $res->status(200)->toJSON($response);
});

Router::put('/connection', function ($request) {
    $req = new Request($request->params);
    $res = new Response();

    $type = $req->getJSON()->type;

    setTypeConnection($type);

    $res->status(200)->toJSON([
        "status" => true,
        "message" => "Successfully changed connection type",
        "data" => $type
    ]);
});

Router::get('/connection', function () {
    $res = new Response();

    $res->status(200)->toJSON([
        "status" => true,
        "message" => "Connection type",
        "data" => getTypeConnection()
    ]);
});

Router::get('/(.*)', function () {
    $res = new Response();

    $res->status(404)->toJSON([
        "status" => false,
        "message" => "Resource not found"
    ]);
});

App::run();
