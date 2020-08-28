<?php
namespace tests\Zeedhi\Framework\HTTP\Logger\Persistence;

use Zeedhi\Framework\HTTP\Logger\Persistence\MultiRowSQL;
use Zeedhi\Framework\HTTP\Logger\Persistence\Exception;
use Doctrine\DBAL\Connection;

class MultiRowSQLTest extends \PHPUnit\Framework\TestCase {

    const UNIQID_REGEX = '/^[a-f0-9]{13}$/';
    const DATE_REGEX = '/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/';

    public function setUp() {
        $this->connection = $this->getMockBuilder(Connection::class)
                                 ->setMethods(array('executeUpdate'))
                                 ->disableOriginalConstructor()
                                 ->getMock();

        $this->persistenceStrategy = new MultiRowSQL($this->connection, 'Y-m-d H:i:s');
    }

    public function testLogRequest() {
        $this->connection->expects($this->once())
                         ->method('executeUpdate')
                         ->with('INSERT INTO ZHU_LOG (TYPE, REQ_ID, METHOD, ROUTE, REQUEST_TYPE, TIMESTAMP, USER_DATA, CONTEXT_DATA, CONTENT) VALUES (:TYPE, :REQ_ID, :METHOD, :ROUTE, :REQUEST_TYPE, :TIMESTAMP, :USER_DATA, :CONTEXT_DATA, :CONTENT)',
                            $this->callback(function($queryParams) {
                                $this->assertCount(9, $queryParams);
                                $this->assertArraySubset(array(
                                    'TYPE'         => 'REQUEST',
                                    'METHOD'       => 'POST',
                                    'ROUTE'        => '/foo',
                                    'REQUEST_TYPE' => 'DataSet',
                                    'USER_DATA'    => '{"userId":1}',
                                    'CONTEXT_DATA' => NULL,
                                    'CONTENT'      => 'Request content'
                                ), $queryParams);
                                $this->assertArrayHasKey('REQ_ID', $queryParams);
                                $this->assertRegExp(self::UNIQID_REGEX, $queryParams['REQ_ID']);
                                $this->assertArrayHasKey('TIMESTAMP', $queryParams);
                                $this->assertRegExp(self::DATE_REGEX, $queryParams['TIMESTAMP']);
                                return true;
                            }));

        $method      = 'POST';
        $route       = '/foo';
        $requestType = 'DataSet';
        $userData    = '{"userId":1}';
        $contextData = null;
        $content     = 'Request content';
        $this->persistenceStrategy->logRequest($method, $route, $requestType, $userData, $contextData, $content);
    }

    public function testLogResponse() {
        $this->connection->expects($this->exactly(2))
                         ->method('executeUpdate');

        $this->persistenceStrategy->logRequest('POST', '/foo', 'DataSet', '{"userId":1}', null, 'Request content');

        $this->connection->expects($this->once())
                         ->method('executeUpdate')
                         ->with('INSERT INTO ZHU_LOG (TYPE, HTTP_RESPONSE_CODE, REQ_ID, METHOD, ROUTE, REQUEST_TYPE, TIMESTAMP, USER_DATA, CONTEXT_DATA, CONTENT) VALUES (:TYPE, :HTTP_RESPONSE_CODE, :REQ_ID, :METHOD, :ROUTE, :REQUEST_TYPE, :TIMESTAMP, :USER_DATA, :CONTEXT_DATA, :CONTENT)',
                            $this->callback(function($queryParams) {
                                $this->assertCount(10, $queryParams);
                                $this->assertArraySubset(array(
                                    'TYPE'               =>  'RESPONSE',
                                    'HTTP_RESPONSE_CODE' =>  200,
                                    'METHOD'             =>  'POST',
                                    'ROUTE'              =>  '/foo',
                                    'REQUEST_TYPE'       =>  'DataSet',
                                    'USER_DATA'          =>  '{"userId":1}',
                                    'CONTEXT_DATA'       =>  NULL,
                                    'CONTENT'            =>  'Response content'
                                ), $queryParams);
                                $this->assertArrayHasKey('REQ_ID', $queryParams);
                                $this->assertRegExp(self::UNIQID_REGEX, $queryParams['REQ_ID']);
                                $this->assertArrayHasKey('TIMESTAMP', $queryParams);
                                $this->assertRegExp(self::DATE_REGEX, $queryParams['TIMESTAMP']);
                                return true;
                            }));

        $httpResponseCode = 200;
        $content          = 'Response content';
        $this->persistenceStrategy->logResponse($httpResponseCode, $content);
    }

    public function testLogResponseWithoutLoggingRequest() {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Can\'t log response because request was not logged');

        $this->persistenceStrategy->logResponse(200, 'Response content');
    }

}