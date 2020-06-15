<?php

namespace DenielWorld\JasonDB\task;

use DenielWorld\JasonDB\DatabaseManager;
use pocketmine\scheduler\AsyncTask;

abstract class AsyncDBTask extends AsyncTask{

    public function onRun() : void
    {
        DatabaseManager::init();
    }

}