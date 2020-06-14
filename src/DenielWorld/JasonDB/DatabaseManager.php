<?php

namespace DenielWorld\JasonDB;

use DenielWorld\JasonDB\dbs\DatabaseWrapper;

class DatabaseManager{

    /** @var string */
    private const DEFAULT_DB = "default";

    /** @var \MongoClient */
    private static $mongoClient;

    /**
     * @throws \MongoConnectionException
     */
    public static function init() : void{
        self::$mongoClient = new \MongoClient();
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
}