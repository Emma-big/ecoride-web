<?php
declare(strict_types=1);

namespace Adminlocal\EcoRide\Helpers;

use MongoDB\Client;

class MongoHelper
{
    private static ?Client $client = null;

    /**
     * Récupère une collection MongoDB en utilisant les variables d'environnement.
     *
     * @param string $collectionName
     * @return \MongoDB\Collection
     */
    public static function getCollection(string $collectionName)
    {
        if (self::$client === null) {
            // Lire l'URI et le nom de base depuis les vars d'environnement
            $uri    = getenv('MONGODB_URI')    ?: 'mongodb://localhost:27017';
            $dbName = getenv('MONGODB_DB_NAME') ?: 'avisDB';
            error_log("DEBUG MongoHelper using URI: {$uri}, DB: {$dbName}");

            require_once BASE_PATH . '/vendor/autoload.php';
            self::$client = new Client($uri);
        }
        // Sélection de la base
        $database = self::$client->selectDatabase(getenv('MONGODB_DB_NAME') ?: 'avisDB');
        // Retourne la collection
        return $database->selectCollection($collectionName);
    }
}
