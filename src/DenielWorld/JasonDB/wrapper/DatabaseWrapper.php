<?php

namespace DenielWorld\JasonDB\dbs;

use DenielWorld\JasonDB\wrapper\CollectionWrapper;

class DatabaseWrapper{

    /** @var \MongoDB */
    private $db;

    /**
     * DatabaseWrapper constructor.
     * @param \MongoDB $db
     * @internal
     */
    public function __construct(\MongoDB $db)
    {
        $this->db = $db;
    }

    /**
     * @return \MongoDB
     */
    public function asDatabase() : \MongoDB{
        return $this->db;
    }

    /**
     * @param string $collectionName
     *
     * @return CollectionWrapper
     *
     * The $collectionName collection must be created before you attempt to use getCollection() with it.
     */
    public function createCollection(string $collectionName) : CollectionWrapper {
        return new CollectionWrapper($this->db->createCollection($collectionName));
    }

    /**
     * @param string $collectionName
     *
     * @return CollectionWrapper|null Returns null if collection with that name doesn't exist.
     *
     * Make sure to createCollection() before retrieving it with this method.
     */
    public function getCollection(string $collectionName) : ?CollectionWrapper {
        try {
            $collection = $this->db->selectCollection($collectionName);
        } catch (\Exception $e) {
            return null;
        }

        return new CollectionWrapper($collection);
    }
}