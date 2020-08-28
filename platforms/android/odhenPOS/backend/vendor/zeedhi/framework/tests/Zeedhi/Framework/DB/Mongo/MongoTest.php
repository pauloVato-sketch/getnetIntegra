<?php
namespace tests\Zeedhi\Framework\DB\Mongo;

use Zeedhi\Framework\DB\Mongo\Mongo;

class MongoTest extends \PHPUnit\Framework\TestCase {

    const THE_BASS = 'THE_BASS';
    const MONGO_HOST = '192.168.122.55';
    const MONGO_PORT = '27019';
    const MONGO_DB_NAME = 'lambda';

    protected static $objects = array(
        array("key" => 0, "text" => "zero"),
        array("key" => 1, "text" => "um"),
        array("key" => 2, "text" => "dois"),
        array("key" => 3, "text" => "tres"),
        array("key" => 4, "text" => "quatro"),
        array("key" => 5, "text" => "cinco"),
        array("key" => 6, "text" => "seis")
    );

    protected $extensionLoaded;
    protected $mongoDbServerOnline = true;
    /** @var Mongo */
    protected $mongo;

    protected function setUp() {
        $this->extensionLoaded = extension_loaded('mongo');
        if (!$this->extensionLoaded) {
            $this->markTestSkipped('The tests require mongo extension');
        }

        if ($this->mongoDbServerOnline) {
            try {
                $this->mongo = new Mongo(self::MONGO_HOST, self::MONGO_PORT, self::MONGO_DB_NAME);
                foreach (self::$objects as $object) {
                    $this->mongo->insert(self::THE_BASS, $object);
                }
            } catch (\Exception $e) {
                $this->mongoDbServerOnline = false;
            }
        }

        if (!$this->mongoDbServerOnline) {
            $this->markTestSkipped('The tests require mongo db server be online');
        }
    }

    protected function tearDown() {
        if ($this->extensionLoaded && $this->mongoDbServerOnline) {
            $this->mongo->dropCollection(self::THE_BASS);
        }
    }

    public function testSave() {
        $object = array("key" => 7, "text" => "sete");
        $this->mongo->insert(self::THE_BASS, $object);
        $result = $this->mongo->find(self::THE_BASS, array("key" => 7));
        $foundObject = array(
            "key" => $result[0]["key"],
            "text" => $result[0]["text"]
        );
        $this->assertEquals($object, $foundObject);
    }

    public function testFind() {
        $result = $this->mongo->find(self::THE_BASS, array("key" => 3));
        $foundObject = array(
            "key" => $result[0]["key"],
            "text" => $result[0]["text"]
        );
        $this->assertEquals(self::$objects[3], $foundObject);
    }

    public function testUpdate() {
        $object = array('$set' => array('text' => "three"));
        $this->mongo->update(self::THE_BASS, array("key" => 3), $object, true);
        $result = $this->mongo->find(self::THE_BASS, array("key" => 3));
//        $this->assertEquals($object, $foundObject); cant be used because of "_id" added by mongo.
        $foundObject = array(
            "key" => $result[0]["key"],
            "text" => $result[0]["text"]
        );
        foreach($object['$set'] as $column => $value) {
            $this->assertTrue(isset($foundObject[$column]));
            $this->assertEquals($foundObject[$column], $value);
        }
    }

    public function testRemove() {
        $this->mongo->remove(self::THE_BASS, array("key" => 3));
        $result = $this->mongo->find(self::THE_BASS, array("key" => 3));
        $this->assertTrue(empty($result));
    }

    public function testUpdateMultiples () {
        $criteria = array(
            'key' => array('$lt' => 3)
        );
        $update = array(
            '$set' => array(
                'text' => 'menor que 3'
            )
        );
        $upsert = false;
        $multi = true;
        $result = $this->mongo->update(self::THE_BASS, $criteria, $update, $upsert, $multi);
        $this->assertEquals($result->getModifiedCount(), 3);
    }

    public function testAggregate() {
        $object = array("key" => 7, "text" => "seis");
        $this->mongo->insert(self::THE_BASS, $object);
        $criteria = array(
            array(
                '$group' => array(
                    "_id" =>'$text',
                    "id_sum" => array('$sum' => '$key')
                )
            )
        );
        $result = $this->mongo->aggregate(self::THE_BASS, $criteria);
        usort($result, function($a, $b) { return $a['id_sum'] - $b['id_sum']; });
        $resultExpected = array(
            array('_id' => 'zero',   'id_sum' => 0),
            array('_id' => 'um',     'id_sum' => 1),
            array('_id' => 'dois',   'id_sum' => 2),
            array('_id' => 'tres',   'id_sum' => 3),
            array('_id' => 'quatro', 'id_sum' => 4),
            array('_id' => 'cinco',  'id_sum' => 5),
            array('_id' => 'seis',   'id_sum' => 13),
        );

        $this->assertEquals($result, $resultExpected);
    }

    public function testFailAggregate() {
        $object = array("key" => 7, "text" => "seis");
        $this->mongo->insert(self::THE_BASS, $object);
        $criteria = array(
            array(
                '$group' => array(
                    "_id" => array('$exists' => '$text'),
                    "id_sum" => array('$sum' => '$key')
                )
            )
        );

        $this->setExpectedException('\Zeedhi\Framework\DB\Mongo\Exception', "Error while executing aggregate: Unrecognized expression '\$exists'");
        $this->mongo->aggregate(self::THE_BASS, $criteria);
    }

    public function testDbNotSetted() {
        $this->setExpectedException('MongoDB\Exception\InvalidArgumentException', '$databaseName is invalid: ');
        $mongo = new Mongo(self::MONGO_HOST, self::MONGO_PORT);
        $mongo->find(self::THE_BASS);
    }

    public function testExecuteCommand() {
        $buildInfo = $this->mongo->command(array('buildinfo' => 1))[0];

        $this->assertArrayHasKey('version', $buildInfo);
        $this->assertRegExp('#^[0-9]+\.[0-9]+\.[0-9]+$#', $buildInfo['version']);
    }

    public function testGetCollectionNames() {
        $collectionNames = $this->mongo->getCollectionNames();

        $this->assertCount(1, $collectionNames);
        $this->assertContains(self::THE_BASS, $collectionNames);
    }
}