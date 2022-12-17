<?php

namespace App\Model;

use App\Lib\Connection;

class Contact extends Connection
{
    private static $table = "contacts";
    private static $successMessage = "Contact list";
    private static $data = [];

    public static function all()
    {
        $status = false;
        $message = self::$messages->CONNECTION->noRecords;
        $data = [];

        try {
            $sql = "SELECT id, firstname, lastname, email, phone, created_at, updated_at
				FROM %s
				WHERE deleted_at IS NULL";

            $rs = self::query(sprintf($sql, self::$table));

            if ($row = self::fetchArray($rs)) {
                $status = true;
                $message = self::$successMessage;
                $data = $row;
            }
        } catch (\Exception $e) {
            $message = self::$messages->CONNECTION->error . '\n' . $e->getMessage();
        }

        return json_encode([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ]);
    }

    public static function add($post)
    {
        $post->id = count(self::$data) + 1;

        self::$data[] = $post;

        return $post;
    }

    public static function findById(int $id)
    {
        foreach (self::$data as $post) {
            if ($post->id === $id) {
                return $post;
            }
        }

        return [];
    }
}
