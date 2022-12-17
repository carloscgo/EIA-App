<?php

namespace App\Controller;

use App\Model\Contact as Model;

class Contacts
{
    private $model;

    public function __construct()
    {
        $this->model = new Model();
    }

    public function indexAction()
    {
        return $this->model::all();
    }
}
