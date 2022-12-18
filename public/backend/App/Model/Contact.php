<?php

namespace App\Model;

use App\Lib\Connection;

class Contact extends Connection
{
    private static $table = "contacts";

    public static function all()
    {
        $status = false;
        $message = self::$messages->CONNECTION->noRecords;
        $data = [];

        try {
            if (self::isMySQL()) {
                $sql = "SELECT id, firstname, lastname, email, phone, created_at, updated_at
                    FROM `%s`
                    WHERE deleted_at IS NULL";

                $rs = self::query(sprintf($sql, self::$table));

                while ($row = self::fetchArray($rs)) {
                    $data[] = $row;
                }
            }

            if (count($data)) {
                $status = true;
                $message = 'Contact list';
            }
        } catch (\Exception $e) {
            $message = self::$messages->CONNECTION->error . '\n' . $e->getMessage();
        }

        return [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
    }

    public static function findById($id)
    {
        $status = false;
        $message = self::$messages->CONNECTION->noRecords;
        $data = (object) [];

        try {
            if (self::isMySQL()) {
                $sql = "SELECT id, firstname, lastname, email, phone, created_at, updated_at
				FROM `%s`
				WHERE deleted_at IS NULL
                    AND id = %d";

                $rs = self::query(sprintf($sql, self::$table, $id));

                if ($row = self::fetchSingle($rs)) {
                    $status = true;
                    $message = 'A single contact';
                    $data = $row;
                }
            }
        } catch (\Exception $e) {
            $message = self::$messages->CONNECTION->error . '\n' . $e->getMessage();
        }

        return [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
    }

    // TODO Mongo
    public static function findByField($fields, $values, $casts)
    {
        $status = false;
        $message = self::$messages->CONNECTION->noRecords;
        $data = (object) [];

        try {
            if (self::isMySQL()) {
                $sql = "SELECT id, firstname, lastname, email, phone, created_at, updated_at
				FROM `%s`
				WHERE deleted_at IS NULL";

                $sql = sprintf($sql, self::$table);

                foreach ($fields as $key => $field) {
                    $sql .= sprintf(" AND $casts[$key]", $fields[$key], $values[$key]);
                }

                $rs = self::query($sql);

                if ($row = self::fetchSingle($rs)) {
                    $status = true;
                    $message = 'A single contact';
                    $data = $row;
                }
            }
        } catch (\Exception $e) {
            $message = self::$messages->CONNECTION->error . '\n' . $e->getMessage();
        }

        return [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
    }

    public static function add($contact)
    {
        $status = false;
        $message = self::$messages->CONNECTION->noRecords;
        $data = (object) [];

        try {
            if (self::isMySQL()) {
                $into  = "firstname, lastname, email, phone";
                $values = "'%s', '%s', '%s', '%s'";

                $rs = self::insert(
                    self::$table,
                    $into,
                    sprintf($values, $contact->firstname, $contact->lastname, $contact->email, $contact->phone)
                );

                if ($rs) {
                    $status = true;
                    $message = self::$messages->INSERT->success;
                    $data = self::findById(self::insertID())['data'];
                } else {
                    $message = self::$messages->INSERT->error;
                }
            }
        } catch (\Exception $e) {
            $message = self::$messages->CONNECTION->error . '\n' . $e->getMessage();
        }

        return [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
    }

    public static function change($contact, $id)
    {
        $status = false;
        $message = self::$messages->CONNECTION->noRecords;
        $data = (object) [];

        try {
            if (self::isMySQL()) {
                $set = "firstname = '%s',
                lastname = '%s',
                email = '%s',
                phone = '%s'";

                $rs = self::update(
                    self::$table,
                    sprintf($set, $contact->firstname, $contact->lastname, $contact->email, $contact->phone),
                    sprintf("id = %d", $id)
                );

                if ($rs) {
                    $status = true;
                    $message = self::$messages->UPDATE->success;
                    $data = self::findById($id)['data'];
                } else {
                    $message = self::$messages->UPDATE->error;
                }
            }
        } catch (\Exception $e) {
            $message = self::$messages->CONNECTION->error . '\n' . $e->getMessage();
        }

        return [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
    }

    public static function deleteById($params, $id)
    {
        $status = false;
        $message = self::$messages->CONNECTION->noRecords;
        $data = (object) [];

        try {
            if (self::isMySQL()) {
                if (@$params->permanent) {
                    $rs = self::delete(self::$table, sprintf("id = %d", $id));
                } else {
                    $rs = self::update(
                        self::$table,
                        "deleted_at = now()",
                        sprintf("id = %d", $id)
                    );
                }

                if ($rs) {
                    $status = true;
                    $message = self::$messages->DELETE->success;
                } else {
                    $message = self::$messages->DELETE->error;
                }
            }
        } catch (\Exception $e) {
            $message = self::$messages->CONNECTION->error . '\n' . $e->getMessage();
        }

        return [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
    }
}
