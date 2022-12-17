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

    public function allAction()
    {
        return $this->model::all();
    }

    public function findAction(int $id)
    {
        return $this->model::findById($id);
    }
}
