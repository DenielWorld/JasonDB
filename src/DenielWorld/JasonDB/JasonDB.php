<?php

namespace DenielWorld\JasonDB;

use pocketmine\plugin\PluginBase;

class JasonDB extends PluginBase{

    public function onEnable()
    {
        self::init();
    }

    /**
     * @throws \MongoConnectionException
     *
     * This is the first thing that has to be run to activate the DatabaseManager.
     * If you are handling this asynchronously, directly run DatabaseManager::init();
     */
    public static function init()
    {
        DatabaseManager::init();
    }

}