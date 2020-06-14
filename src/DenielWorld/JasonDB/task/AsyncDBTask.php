<?php

namespace DenielWorld\JasonDB\task;

use DenielWorld\JasonDB\DatabaseManager;
use pocketmine\scheduler\AsyncTask;

abstract class AsyncDBTask extends AsyncTask{

    /**
     * AsyncDBTask constructor.
     * @throws \MongoConnectionException
     */
    public function __construct()
    {
        DatabaseManager::init();
    }

}