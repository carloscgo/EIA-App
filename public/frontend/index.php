<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Lib\App;
use App\Lib\Router;
use App\Lib\Request;
use App\Lib\Response;

use App\Controller\Contacts;

Router::get('/', function () {
    echo 'Hello World';
});

Router::get('/post/([0-9]*)', function (Request $req, Response $res) {
    echo 'post';
    $res->toJSON([
        'post' =>  ['id' => $req->params[0]],
        'status' => 'ok'
    ]);
});

Router::get('/contacts', function (Response $res) {
    $res->toJSON((new Contacts())->allAction());
});

Router::get('/contacts/([0-9]*)', function (Request $req, Response $res) {
    $res->toJSON((new Contacts())->findAction($req->params[0]));
});

Router::post('/contacts', function (Request $req, Response $res) {
    $response = (new Contacts())->newAction($req->getJSON());

    $res->status(201)->toJSON($response);
});

Router::put('/contacts/([0-9]*)', function (Request $req, Response $res) {
    $response = (new Contacts())->updateAction($req->getJSON(), $req->params[0]);

    $res->status(200)->toJSON($response);
});

Router::delete('/contacts/([0-9]*)', function (Request $req, Response $res) {
    $response = (new Contacts())->deleteAction($req->getJSON(), $req->params[0]);

    $res->status(200)->toJSON($response);
});

App::run();
