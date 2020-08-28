<?php
namespace tests\Zeedhi\Framework\HTTP\Logger\Persistence;

use Zeedhi\Framework\HTTP\Logger\Persistence\SQL;
use Zeedhi\Framework\HTTP\Logger\Persistence\Exception;
use Doctrine\DBAL\Connection;

class SQLTest extends \PHPUnit\Framework\TestCase {

    const DATE_REGEX = '/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/';

    public function setUp() {
        $this->connection = $this->getMockBuilder(Connection::class)
                                 ->setMethods(array('executeUpdate', 'lastInsertId'))
                                 ->disableOriginalConstructor()
                                 ->getMock();

        $this->persistenceStrategy = new SQL($this->connection, 'Y-m-d H:i:s');
    }

    public function testLogRequest() {
        $this->connection->expects($this->once())
                         ->method('lastInsertId')
                         ->willReturn(123);

        $this->connection->expects($this->once())
                         ->method('executeUpdate')
                         ->with('INSERT INTO ZHU_LOG (METHOD, ROUTE, REQUEST_TYPE, BEGIN_REQUEST, USER_DATA, CONTEXT_DATA, REQUEST_CONTENT) VALUES (:METHOD, :ROUTE, :REQUEST_TYPE, :BEGIN_REQUEST, :USER_DATA, :CONTEXT_DATA, :REQUEST_CONTENT)',
                            $this->callback(function($queryParams) {
                                $this->assertCount(7, $queryParams);
                                $this->assertArraySubset(array(
                                    'METHOD'          => 'POST',
                                    'ROUTE'           => '/foo',
                                    'REQUEST_TYPE'    => 'DataSet',
                                    'USER_DATA'       => '{"userId":1}',
                                    'CONTEXT_DATA'    => NULL,
                                    'REQUEST_CONTENT' => 'Request content'
                                ), $queryParams);
                                $this->assertArrayHasKey('BEGIN_REQUEST', $queryParams);
                                $this->assertRegExp(self::DATE_REGEX, $queryParams['BEGIN_REQUEST']);
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
        $this->connection->expects($this->once())
                         ->method('lastInsertId')
                         ->willReturn(123);

        $this->connection->expects($this->exactly(2))
                         ->method('executeUpdate');

        $this->persistenceStrategy->logRequest('POST', '/foo', 'DataSet', '{"userId":1}', null, 'Request content');

        $this->connection->expects($this->once())
                         ->method('executeUpdate')
                         ->with('UPDATE ZHU_LOG SET HTTP_RESPONSE_CODE = :HTTP_RESPONSE_CODE, RESPONSE_CONTENT = :RESPONSE_CONTENT, END_REQUEST = :END_REQUEST WHERE ID = :REQUEST_ID',
                            $this->callback(function($queryParams) {
                                $this->assertCount(4, $queryParams);
                                $this->assertArraySubset(array(
                                    'REQUEST_ID'         => 123,
                                    'HTTP_RESPONSE_CODE' => 200,
                                    'RESPONSE_CONTENT'   => 'Response content'
                                ), $queryParams);
                                $this->assertArrayHasKey('END_REQUEST', $queryParams);
                                $this->assertRegExp(self::DATE_REGEX, $queryParams['END_REQUEST']);
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