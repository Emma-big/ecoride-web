<?php
declare(strict_types=1);

namespace Adminlocal\EcoRide\Helpers;

use MongoDB\Client;

class MongoHelper
{
    private static ?Client $client = null;

    public static function getCollection(string $collectionName)
    {
        if (self::$client === null) {
            self::$client = new Client('mongodb://localhost:27017');
        }
        $database = self::$client->ecoride_db; // nom de ta base
        return $database->$collectionName;     // accès à la collection
    }
}
