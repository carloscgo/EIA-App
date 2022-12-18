<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Lib\App;
use App\Lib\Router;
use App\Lib\Request;
use App\Lib\Response;

use App\Controller\Contacts;
use App\Controller\Connection;

Router::get('/', function () {
  require_once __DIR__ . '/App/Controller/Home.php';
});

Router::get('/contact/new', function () {
  $title = 'Add Contact';
  $action = 'new';

  require_once __DIR__ . '/App/Controller/Form.php';
});

Router::get('/contact/edit/([0-9]*)', function ($request) {
  $title = 'Update Contact';
  $action = 'edit';
  $id = $request->params[0];

  require_once __DIR__ . '/App/Controller/Form.php';
});

Router::get('/api/contacts', function () {
  $res = new Response();

  $contact = new Contacts();

  $res->status(200)->toJSON($contact->allAction());
});

Router::get('/api/contacts/([0-9]*)', function ($request) {
  $res = new Response();

  $contact = new Contacts();

  $res->status(200)->toJSON($contact->findAction($request->params[0]));
});

Router::post('/api/contacts', function ($request) {
  $req = new Request($request->params);
  $res = new Response();

  $contact = new Contacts();

  $res->status(201)->toJSON($contact->newAction($req->getJSON()));
});

Router::put('/api/contacts/([0-9]*)', function ($request) {
  $req = new Request($request->params);
  $res = new Response();

  $contact = new Contacts();

  $res->status(200)->toJSON($contact->updateAction($req->getJSON(), $req->params[0]));
});

Router::delete('/api/contacts/([0-9]*)', function ($request) {
  $req = new Request($request->params);
  $res = new Response();

  $contact = new Contacts();

  $res->status(200)->toJSON($contact->deleteAction($req->getJSON(), $req->params[0]));
});


Router::get('/api/connection', function () {
  $res = new Response();

  $connection = new Connection();

  $res->status(200)->toJSON($connection->getAction());
});

Router::put('/api/connection', function () {
  $req = new Request();
  $res = new Response();

  $connection = new Connection();

  $res->status(200)->toJSON($connection->setAction($req->getJSON()->type));
});

Router::get('/api/(.*)', function () {
  $res = new Response();

  $res->status(404)->toJSON([
    "status" => false,
    "message" => "Resource not found"
  ]);
});

App::run();
