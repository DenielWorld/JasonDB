<?php

namespace DenielWorld\JasonDB;

use DenielWorld\JasonDB\dbs\DatabaseWrapper;

class DatabaseManager{

    /** @var string */
    private const DEFAULT_DB = "default";

    /** @var \MongoDB\Driver\Manager */
    private static $mongoClient;

    /** @var array<string, \MongoDB\Driver\Manager> */
    private static $additionalMongoClients = [];

    /**
     * @throws \Exception Don't blame me for this that's how they document it smh.
     */
    public static function init() : void{
        self::$mongoClient = new \MongoDB\Driver\Manager("mongodb://localhost:27017", ["connect" => TRUE]);
    }

    /**
     * @param string $dbName If the database with this name does not exist, it will be created and then returned.
     *
     * @return DatabaseWrapper
     */
    public static function getDatabase(string $dbName) : DatabaseWrapper{
        return new DatabaseWrapper(self::$mongoClient->{$dbName});
    }

    /**
     * If you do not want to make an additional DB, you can always make use of the default one.
     *
     * @return DatabaseWrapper
     */
    public static function getDefaultDatabase() : DatabaseWrapper{
        return self::getDatabase(self::DEFAULT_DB);
    }

    /**
     * @param string $clientName
     * @param string $dbName
     *
     * @return DatabaseWrapper
     *
     * @throws \UnexpectedValueException If the $clientName does not have an associated additional client to it.
     */
    public static function getDatabaseFrom(string $clientName, string $dbName) : DatabaseWrapper{
        if(isset(self::$additionalMongoClients[$clientName]))
            return new DatabaseWrapper(self::$additionalMongoClients[$clientName]->{$dbName});

        throw new \UnexpectedValueException("$clientName is expected to have an associated additional client");
    }

    /**
     * @param string $clientName
     * @param string $uri
     * @param array $uriOptions
     *
     * This can be used to make additional MongoDB connections elsewhere. For example, the default connection connects...
     * ...to the localhost. Please only make use of this if you know what you are doing.
     */
    public static function createAdditionalClient(string $clientName, string $uri, array $uriOptions = ["connect" => TRUE]) : void{
        self::$additionalMongoClients[$clientName] = new \MongoDB\Driver\Manager($uri, $uriOptions);
    }
}