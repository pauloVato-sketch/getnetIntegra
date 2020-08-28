<?php
namespace Zeedhi\Framework\DB\Mongo;

class Mongo {

    /** @var \MongoDB */
    protected $db = null;
    /** @var \MongoClient */
    protected $mongo = null;

    protected $lazyConstructorParams = array();

    /**
     * @param string $server
     * @param string $port
     * @param string $dbName Parameter optional
     * @param array  $options
     */
    public function __construct($server, $port, $dbName = null, $options = array()) {
        $this->mongo = new \MongoDB\Client("mongodb://$server:$port", $options);
        $this->db = $this->mongo->selectDatabase($dbName);
    }
    
    /**
     * Test and return a boolean defining wheter the DataBase is set.
     */
    protected function isDbSetted() {
        return $this->db !== null;
    }

    /**
     * Find in database for documents that match with given criteria.
     *
     * @param string $collectionName
     * @param array  $criteria
     * @param array  $sort
     * @param int    $limit
     *
     * @throws Exception When db name is not setted.
     *
     * @return array
     */
    public function find($collectionName, $criteria = array(), $sort = null, $limit = null) {
        $this->validateDbSetted();
        $collection = $this->getCollection($collectionName);
        $criteria["limit"] = $limit;
        $criteria["sort"] = $sort;
        $cursor = $collection->find($criteria);
        $result = [];
        foreach($cursor as $document){
            $result[] = get_object_vars($document);
        }
        return $result;
    }

    /**
     * Defines condition before find.
     * 
     * Aggregate conditions to Mongo::find, using aggregators it simulates the SQL where clause.
     * 
     * @param string $collectionName
     * @param array $criteria
     *
     * @return array
     * @throws Exception
     */
    public function aggregate($collectionName, array $criteria) {
        $this->validateDbSetted();
        $collection = $this->getCollection($collectionName);
        try {
            $cursor = $collection->aggregate($criteria);
            $result = [];
            foreach($cursor as $document){
                $result[] = get_object_vars($document);
            }
            return $result;
        } catch (\MongoDB\Driver\Exception\Exception $e) {
            throw Exception::aggregateError($e);
        }
    }

    /**
     * Insert the given register in database.
     *
     * @param string $collectionName
     * @param mixed  $object
     *
     * @throws Exception When db name is not setted.
     *
     * @return bool|array Returns an array containing the status of the insertion if the "w" option is set. Otherwise,
     *                    returns TRUE if the inserted array is not empty (a MongoException will be thrown if the
     *                    inserted array is empty).
     */
    public function insert($collectionName, $object) {
        $this->validateDbSetted();
        return $this->getCollection($collectionName)->insertOne($object);
    }

    /**
     * Insert the given registers in database.
     *
     * @param string $collectionName
     * @param array  $objects
     *
     * @throws Exception When db name is not setted.
     *
     * @return bool|array Returns an array containing the status of the insertion if the "w" option is set. Otherwise,
     *                    returns TRUE if the inserted array is not empty (a MongoException will be thrown if any
     *                    inserted array is empty).
     */
    public function insertMany($collectionName, array $objects) {
        $this->validateDbSetted();
        return $this->getCollection($collectionName)->insertMany($objects);
    }

    /**
     * Update a registers matched by criteria with alterations proposed by by update.
     *
     * @param string $collectionName
     * @param array  $criteria
     * @param mixed  $update
     * @param bool   $upsert If true create a new document. Default to false.
     * @param bool   $multi
     *
     * @throws Exception When db name is not setted.
     *
     * @return boolean
     */
    public function update($collectionName, $criteria, $update, $upsert = false, $multi = false) {
        $this->validateDbSetted();
        $options = array(
            "upsert" => $upsert
        );
        if ($multi) {
            return $this->getCollection($collectionName)->updateMany($criteria, $update, $options);
        }
        return $this->getCollection($collectionName)->updateOne($criteria, $update, $options);
    }

    /**
     * Remove records that match with criteria in that collection.
     *
     * @param string $collectionName
     * @param array  $criteria
     * @param bool   $justOne If true delete only the first find record.
     *
     * @throws Exception When db name is not setted.
     *
     * @return bool|array Returns an array containing the status of the removal if the "w" option is set. Otherwise,
     *                    returns TRUE.
     */
    public function remove($collectionName, $criteria, $justOne = true) {
        $this->validateDbSetted();
        if ($justOne) {
            return $this->getCollection($collectionName)->deleteOne($criteria);
        }
        return $this->getCollection($collectionName)->deleteMany($criteria);
    }

    /**
     * Drop the given collection.
     *
     * @param string $collectionName
     *
     * @throws Exception When db name is not setted.
     *
     * @return array Returns the database response.
     * 
     */
    public function dropCollection($collectionName) {
        $this->validateDbSetted();
        return $this->getCollection($collectionName)->drop();
    }

    /**
     * Defines condition before find.
     * 
     * Aggregate conditions to Mongo::find, using aggregators it simulates the SQL where clause.
     * 
     * @param string $collectionName
     * @param array $criteria
     *
     * @return array
     * @throws Exception
     * 
     */
    public function getCollectionNames() {
        $this->validateDbSetted();
        $collectionsInterator = $this->db->listCollections();
        $collectionsNames = [];
        foreach ($collectionsInterator as $collectionInfo) {
            $collectionsNames[] = $collectionInfo->getName();
        }
        return $collectionsNames;
    }
    
    /**
     * Runs a command on Mongo DataBase.
     * 
     * Receive an command with it's options and call it using the Mongo DataBase object previously initialized.
     * 
     * @link https://docs.mongodb.com/php-library/current/reference/method/MongoDBDatabase-command/ Mongo documentation about db->command.
     * 
     * @param array $command
     *
     * @return array $result
     * 
     */
    public function command(array $command) {
        $this->validateDbSetted();
        $cursor = $this->db->command($command);
        $result = [];
        foreach($cursor as $document){
            $result[] = get_object_vars($document);
        }
        return $result;
    }

    /**
     * @param string $collectionName
     *
     * @return \MongoCollection
     */
    public function getCollection($collectionName) {
        return $this->db->selectCollection($collectionName);
    }

    /**
     * Validates if DataBase is set.
     * 
     * Calls a function to check if the DataBase is set and throws an exception if not.
     * 
     * @throws Exception
     * 
     */
    protected function validateDbSetted() {
        if (!$this->isDbSetted()) {
            throw Exception::dbNotSetted();
        }
    }
}