<?php

namespace App\Lib;

use MongoDB\Client;

trait TraitMongo
{
    private static $db = null;
    private static $server = null;
    private static $error = null;
    private static $connection = null;
    private static $queryID = null;
    private static $insert_id = null;

    public static function connectionMongo()
    {
        $client = new Client('mongodb://' . self::$server . ':27117');

        self::$connection = $client->selectDatabase(self::$db);
    }

    public static function mongoArray($document)
    {
        try {
            $collection = self::$connection->{$document};

            if (!$collection) {
                $collection = self::$connection->createCollection($document);
            }

            return $collection->find();
        } catch (\Exception $e) {
            self::$error = $e->getMessage();

            return 0;
        }
    }

    public static function mongoSingle($document, $id)
    {
        try {
            $collection = self::$connection->{$document};

            if (!$collection) {
                $collection = self::$connection->createCollection($document);
            }

            return $collection->findOne(['_id' => $id]);
        } catch (\Exception $e) {
            self::$error = $e->getMessage();

            return 0;
        }
    }

    public static function today()
    {
        return date("Y-m-d H:i:s");
    }

    public static function insertMongoID()
    {
        try {
            return self::$insert_id;
        } catch (\Exception $e) {
            self::$error = 'No se ha logrado capturar el ID del ultimo registro';

            return 0;
        }
    }

    public static function insertMongo($document, $values)
    {
        if ($document == "") {
            throw new \Exception('No ha especificado el document', 301);

            return 0;
        }
        if (empty($values)) {
            throw new \Exception('No ha especificado los valores', 303);

            return 0;
        }

        try {
            $collection = self::$connection->{$document};

            if (!$collection) {
                $collection = self::$connection->createCollection($document);
            }

            $insertOneResult = $collection->insertOne($values + [
                'created_at' => self::today(),
                'updated_at' => self::today(),
                'deleted_at' => null,
            ]);

            self::$queryID = $insertOneResult->getInsertedCount();

            if (self::$queryID) {
                self::$insert_id = $insertOneResult->getInsertedId();

                return self::$queryID;
            } else {
                return 0;
            }
        } catch (\Exception $e) {
            self::$error = $e->getMessage() . ': ' . $e->getCode();
            print_r(self::$error);
            return 0;
        }
    }

    public static function updateMongo($document, $values, $where)
    {
        if ($document == "") {
            throw new \Exception('No ha especificado el document', 401);

            return 0;
        }
        if (empty($values)) {
            throw new \Exception('No ha especificado las modificaciones', 402);

            return 0;
        }
        if (empty($where)) {
            throw new \Exception('No ha especificado el registro a modificar', 403);

            return 0;
        }

        try {
            $collection = self::$connection->{$document};

            if (!$collection) {
                $collection = self::$connection->createCollection($document);
            }

            $updateResult = $collection->updateOne(
                $where,
                ['$set' => $values]
            );

            self::$queryID = $updateResult->getModifiedCount();

            if (self::$queryID) {
                return self::$queryID;
            } else {
                return 0;
            }
        } catch (\Exception $e) {
            self::$error = $e->getMessage() . ': ' . $e->getCode();

            return 0;
        }
    }

    public static function deleteMongo($document, $where)
    {
        if ($document == "") {
            throw new \Exception('No ha especificado el document', 501);

            return 0;
        }
        if (empty($where)) {
            throw new \Exception('No ha especificado el registro a eliminar', 502);

            return 0;
        }

        try {
            $collection = self::$connection->{$document};

            if (!$collection) {
                $collection = self::$connection->createCollection($document);
            }

            $deleteResult = $collection->deleteOne($where);

            self::$queryID = $deleteResult->getDeletedCount();

            if (self::$queryID) {
                return self::$queryID;
            } else {
                return 0;
            }
        } catch (\Exception $e) {
            self::$error = $e->getMessage() . ': ' . $e->getCode();

            return 0;
        }
    }
}
