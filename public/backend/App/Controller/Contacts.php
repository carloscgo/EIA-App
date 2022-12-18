<?php

namespace App\Controller;

use App\Model\Contact as Model;

class Contacts extends Model
{
    private $existsEmailMessage = 'El email ya existe para otro contacto';

    private function validate($fields)
    {
        $error = [];

        if (empty($fields->firstname) || strlen($fields->firstname) > 45) {
            $error[] = 'El campo `firstname` es invalido';
        }

        if (empty($fields->lastname) || strlen($fields->lastname) > 45) {
            $error[] = 'El campo `lastname` es invalido';
        }

        if (!validateEmail($fields->email)) {
            $error[] = 'El `email` es invalido';
        }

        if (empty($fields->phone) || strlen($fields->phone) > 15 || !validatePhone($fields->phone)) {
            $error[] = 'El campo `phone` es invalido';
        }

        return $error;
    }

    public function allAction()
    {
        return self::all();
    }

    public function findAction($id)
    {
        return self::findById($id);
    }

    public function newAction($contact)
    {
        $exists = (array) self::findByField(
            ['email'],
            [$contact->email],
            ["`%s` = '%s'"]
        )['data'];

        if (!empty($exists)) {
            return getErrors([
                $this->existsEmailMessage
            ]);
        }

        $errors = $this->validate($contact);

        if (!empty($errors)) {
            return getErrors($errors);
        }

        return self::add($contact);
    }

    public function updateAction($contact, $id)
    {
        $exists = (array) self::findByField(
            ['email', 'id'],
            [$contact->email, $id],
            ["`%s` = '%s'", "`%s` != %d"]
        )['data'];

        if (!empty($exists)) {
            return getErrors([
                $this->existsEmailMessage
            ]);
        }

        $errors = $this->validate($contact);

        if (!empty($errors)) {
            return getErrors($errors);
        }

        return self::change($contact, $id);
    }

    public function deleteAction($params, $id)
    {
        return self::deleteById($params, $id);
    }
}
