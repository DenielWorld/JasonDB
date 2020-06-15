<?php

namespace DenielWorld\JasonDB\wrapper;

class CollectionWrapper{

    /** @var \MongoCollection */
    private $collection;

    /** @var null|string */
    private $lastError;

    /**
     * CollectionWrapper constructor.
     * @param \MongoCollection $collection
     * @internal
     */
    public function __construct(\MongoCollection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return null|string Since all the exceptions are caught for safety purposes, the last error gets logged and can be retrieved with this method.
     */
    public function getLastError() : ?string{
        return $this->lastError;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function setNested(string $key, $value) : void{
        $vars = explode(".", $key); $mainKey = array_shift($vars);
        $doc = $this->collection->findOne(["key" => $mainKey], ["data"]);
        $base = array_shift($vars);

        if(!isset($doc[$base])){
            $doc[$base] = [];
        }

        $base =& $doc[$base];

        while(count($vars) > 0){
            $baseKey = array_shift($vars);
            if(!isset($base[$baseKey])){
                $base[$baseKey] = [];
            }
            $base =& $base[$baseKey];
        }

        $base = $value;
        $this->set($mainKey, $doc);
    }

    /**
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getNested(string $key, $default = null){
        $vars = explode(".", $key); $mainKey = array_shift($vars);
        $doc = $this->collection->findOne(["key" => $mainKey], ["data"]);
        $base = array_shift($vars);

        if(isset($doc[$base])){
            $base = $doc[$base];
        }else{
            return $default;
        }

        while(count($vars) > 0){
            $baseKey = array_shift($vars);
            if(is_array($base) and isset($base[$baseKey])){
                $base = $base[$baseKey];
            }else{
                return $default;
            }
        }

        return $base;
    }

    /**
     * @param string $key
     */
    public function removeNested(string $key) : void{
        $vars = explode(".", $key); $mainKey = array_shift($vars);
        $doc = $this->collection->findOne(["key" => $mainKey], ["data"]);
        $base = array_shift($vars);

        if(!isset($doc[$base])){
            return;
        }

        $base =& $doc[$base];

        while(count($vars) > 0){
            $baseKey = array_shift($vars);
            if(!isset($base[$baseKey])){
                return;
            }
            $base =& $base[$baseKey];
        }

        unset($base);
        try {
            $this->collection->update(["key" => $mainKey], ['$set' => ["data" => $doc]]);
        } catch (\MongoCursorException $exception){
            $this->lastError = $exception->__toString();
            //Preventing the DB from shitting itself lmao
        }
    }

    /**
     * @param string|int $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function get($key, $default = false){
        return $this->collection->findOne(["key" => $key], ["data"]) ?? $default;
    }

    /**
     * @param string|int $key
     * @param mixed $value
     */
    public function set($key, $value) : void{
        if(is_null($this->collection->findOne(["key" => $key]))) {
            try {
                $this->collection->insert(["key" => $key, "data" => $value]);
            } catch (\MongoException $exception){
                $this->lastError = $exception->__toString();
                //Preventing the DB from shitting itself lmao
            }
        } else {
            try {
                $this->collection->update(["key" => $key], ['$set' => ["data" => $value]]);
            } catch (\MongoCursorException $exception){
                $this->lastError = $exception->__toString();
                //Preventing the DB from shitting itself lmao
            }
        }
    }

    /**
     * @param mixed $value
     */
    public function setAll($value) : void{
        try {
            $this->collection->update([], ['$set' => ["data" => $value]], ["multiple" => true]);
        } catch (\MongoCursorException $exception){
            $this->lastError = $exception->__toString();
            //Preventing the DB from shitting itself lmao
        }
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function exists(string $key) : bool{
        return is_array($this->collection->findOne(["key" => $key])) or !is_null($this->getNested($key));
    }

    /**
     * @param string $key
     */
    public function remove(string $key) : void{
        try {
        $this->collection->remove(["key" => $key]);
        } catch (\MongoCursorException $exception){
            $this->lastError = $exception->__toString();
            //Preventing the DB from shitting itself lmao
        }
    }

    /**
     * @param bool $keys
     *
     * @return mixed[]
     */
    public function getAll(bool $keys = false) : array{
        return ($keys ? (array)$this->collection->find([], ["key"]) : array_combine((array)$this->collection->find([], ["key"]), (array)$this->collection->find([], ["data"])));
    }
}