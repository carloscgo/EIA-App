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

Router::get('/contacts', function (Request $req, Response $res) {
    $res->toJSON((new Contacts())->allAction());
});

Router::get('/contacts/([0-9]*)', function (Request $req, Response $res) {
    $res->toJSON((new Contacts())->findAction($req->params[0]));
});

App::run();
