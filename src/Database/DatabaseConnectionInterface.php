<?php
namespace Adminlocal\EcoRide\Database;

interface DatabaseConnectionInterface
{
    /**
     * Retourne une instance de PDO configurée.
     */
    public function getPdo(): \PDO;
}
