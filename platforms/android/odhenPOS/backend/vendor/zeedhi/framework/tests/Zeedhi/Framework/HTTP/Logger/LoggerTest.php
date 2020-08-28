<?php
namespace tests\Zeedhi\Framework\HTTP\Logger;

use Zeedhi\Framework\HTTP\Logger\Processor\DefaultProcessor;
use Zeedhi\Framework\HTTP\Logger\Exception;
use Zeedhi\Framework\HTTP\Logger\Logger;
use Zeedhi\Framework\HTTP\Logger\LoggerInfoProvider;
use Zeedhi\Framework\HTTP\Logger\Persistence\PersistenceInterface;
use Zeedhi\Framework\HTTP\Request;
use Zeedhi\Framework\Log\Memory;

class LoggerTest extends \PHPUnit\Framework\TestCase {

    const DATE_REGEX = '[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}';

    /** @var Logger */
    protected $logger;
    /** @var Memory  */
    protected $log;
    /** @var DefaultProcessor */
    protected $processor;

    protected function setUp() {
        $this->persistenceStrategy = $this->getMockBuilder(PersistenceInterface::class)
                                          ->setMethods(array('logRequest', 'logResponse'))
                                          ->disableOriginalConstructor()
                                          ->getMock();

        $this->loggerInfoProvider  = $this->getMockBuilder(LoggerInfoProvider::class)
                                          ->setMethods(array('getUserData', 'getContextData'))
                                          ->disableOriginalConstructor()
                                          ->getMock();

        $this->processor = new DefaultProcessor(array('/requestToSkip'));

        $this->logger = new Logger($this->persistenceStrategy, $this->loggerInfoProvider, $this->processor);
    }

    public function testLogRequestAndResponse() {
        $expectedUserData       = 'User data';
        $expectedContextData    = 'Context data';
        $expectedRequestContent = '{"filter":[]}';

        $content = json_encode(array('filter' => array()));
        $request = new Request(array(), array(), array(), $content);
        $this->loggerInfoProvider->expects($this->once())
                                 ->method('getUserData')
                                 ->with($request)
                                 ->willReturn($expectedUserData);

        $this->loggerInfoProvider->expects($this->once())
                                 ->method('getContextData')
                                 ->with($request)
                                 ->willReturn($expectedContextData);

        $this->persistenceStrategy->expects($this->once())
                                  ->method('logRequest')
                                  ->with('POST', '/uri', 'FilterData',
                                    $expectedUserData, $expectedContextData,
                                    $expectedRequestContent);

        $this->persistenceStrategy->expects($this->once())
                                  ->method('logResponse')
                                  ->with(200, '[]');

        $this->logger->logRequest($request, '/uri', 'POST', 'FilterData');
        $this->logger->logResponse(200, array());
    }

    public function testSkipRequest() {
        $this->persistenceStrategy->expects($this->never())
                                  ->method($this->anything());


        $content = json_encode(array('filter' => array()));
        $request = new Request(array(), array(), array(), $content);
        $this->logger->logRequest($request, '/requestToSkip', 'POST', 'FilterData');
        $this->logger->logResponse(200, array());
    }

    public function testCompactDataSetRequest() {
        $expectedUserData       = 'User data';
        $expectedContextData    = 'Context data';

        $request = new Request(array(), array(), array(), json_encode(array('dataset' => array(
            array('foo' => 1,' bar' => 'um'),
            array('foo' => 2,' bar' => 'dois'),
            array('foo' => 3,' bar' => 'tres'),
            array('foo' => 4,' bar' => 'quatro')
        ))));

        $this->loggerInfoProvider->expects($this->once())
                                 ->method('getUserData')
                                 ->with($request)
                                 ->willReturn($expectedUserData);

        $this->loggerInfoProvider->expects($this->once())
                                 ->method('getContextData')
                                 ->with($request)
                                 ->willReturn($expectedContextData);

        $expectedRequestContent = '{"dataset":4}';
        $this->persistenceStrategy->expects($this->once())
                                  ->method('logRequest')
                                  ->with('POST', '/foo', 'DataSet',
                                    $expectedUserData, $expectedContextData,
                                    $expectedRequestContent);

        $this->persistenceStrategy->expects($this->once())
                                  ->method('logResponse')
                                  ->with(200, '{"dataset":{"foo":[1,2,3,4]}}');

        $this->processor->setRequestDataSetPolicy(DefaultProcessor::COMPACT_DATA_SET);

        $this->logger->logRequest($request, '/foo', 'POST', 'DataSet');
        $this->logger->logResponse(200, array(
            'dataset' => array(
                'foo' => array(1, 2, 3, 4)
            )
        ));
    }

    public function testRemoveDataSetRequest() {
        $expectedUserData       = 'User data';
        $expectedContextData    = 'Context data';

        $request = new Request(array(), array(), array(), json_encode(array('dataset' => array(
            array('foo' => 1,' bar' => 'um'),
            array('foo' => 2,' bar' => 'dois'),
            array('foo' => 3,' bar' => 'tres'),
            array('foo' => 4,' bar' => 'quatro')
        ))));

        $this->loggerInfoProvider->expects($this->once())
                                 ->method('getUserData')
                                 ->with($request)
                                 ->willReturn($expectedUserData);

        $this->loggerInfoProvider->expects($this->once())
                                 ->method('getContextData')
                                 ->with($request)
                                 ->willReturn($expectedContextData);

        $expectedRequestContent = '[]';
        $this->persistenceStrategy->expects($this->once())
                                  ->method('logRequest')
                                  ->with('POST', '/foo', 'DataSet',
                                    $expectedUserData, $expectedContextData,
                                    $expectedRequestContent);

        $this->persistenceStrategy->expects($this->once())
                                  ->method('logResponse')
                                  ->with(200, '{"dataset":{"foo":[1,2,3,4]}}');

        $this->processor->setRequestDataSetPolicy(DefaultProcessor::REMOVE_DATA_SET);

        $this->logger->logRequest($request, '/foo', 'POST', 'DataSet');
        $this->logger->logResponse(200, array(
            'dataset' => array(
                'foo' => array(1, 2, 3, 4)
            )
        ));
    }

    public function testCompactDataSetResponse() {
        $expectedUserData       = 'User data';
        $expectedContextData    = 'Context data';

        $request = new Request(array(), array(), array(), json_encode(array('dataset' => array(
            array('foo' => 1, 'bar' => 'um'),
            array('foo' => 2, 'bar' => 'dois'),
            array('foo' => 3, 'bar' => 'tres'),
            array('foo' => 4, 'bar' => 'quatro')
        ))));

        $this->loggerInfoProvider->expects($this->once())
                                 ->method('getUserData')
                                 ->with($request)
                                 ->willReturn($expectedUserData);

        $this->loggerInfoProvider->expects($this->once())
                                 ->method('getContextData')
                                 ->with($request)
                                 ->willReturn($expectedContextData);

        $expectedRequestContent = '{"dataset":[{"foo":1,"bar":"um"},{"foo":2,"bar":"dois"},{"foo":3,"bar":"tres"},{"foo":4,"bar":"quatro"}]}';
        $this->persistenceStrategy->expects($this->once())
                                  ->method('logRequest')
                                  ->with('POST', '/foo', 'DataSet',
                                    $expectedUserData, $expectedContextData,
                                    $expectedRequestContent);

        $this->persistenceStrategy->expects($this->once())
                                  ->method('logResponse')
                                  ->with(200, '{"dataset":{"foo":4}}');

        $this->processor->setResponseDataSetPolicy(DefaultProcessor::COMPACT_DATA_SET);

        $this->logger->logRequest($request, '/foo', 'POST', 'DataSet');
        $this->logger->logResponse(200, array(
            'dataset' => array(
                'foo' => array(1, 2, 3, 4)
            )
        ));
    }

    public function testRemoveDataSetResponse() {
        $expectedUserData       = 'User data';
        $expectedContextData    = 'Context data';

        $request = new Request(array(), array(), array(), json_encode(array('dataset' => array(
            array('foo' => 1, 'bar' => 'um'),
            array('foo' => 2, 'bar' => 'dois'),
            array('foo' => 3, 'bar' => 'tres'),
            array('foo' => 4, 'bar' => 'quatro')
        ))));

        $this->loggerInfoProvider->expects($this->once())
                                 ->method('getUserData')
                                 ->with($request)
                                 ->willReturn($expectedUserData);

        $this->loggerInfoProvider->expects($this->once())
                                 ->method('getContextData')
                                 ->with($request)
                                 ->willReturn($expectedContextData);

        $expectedRequestContent = '{"dataset":[{"foo":1,"bar":"um"},{"foo":2,"bar":"dois"},{"foo":3,"bar":"tres"},{"foo":4,"bar":"quatro"}]}';
        $this->persistenceStrategy->expects($this->once())
                                  ->method('logRequest')
                                  ->with('POST', '/foo', 'DataSet',
                                    $expectedUserData, $expectedContextData,
                                    $expectedRequestContent);

        $this->persistenceStrategy->expects($this->once())
                                  ->method('logResponse')
                                  ->with(200, '[]');

        $this->processor->setResponseDataSetPolicy(DefaultProcessor::REMOVE_DATA_SET);

        $this->logger->logRequest($request, '/foo', 'POST', 'DataSet');
        $this->logger->logResponse(200, array(
            'dataset' => array(
                'foo' => array(1, 2, 3, 4)
            )
        ));
    }

    public function testPersistenceError() {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Database error');

        $expectedUserData    = 'User data';
        $expectedContextData = 'Context data';

        $content = json_encode(array('dataset' => array(
            array('id' => 1),
            array('id' => 2)
        )));
        $request = new Request(array(), array(), array(), $content);

        $this->loggerInfoProvider->expects($this->once())
                                 ->method('getUserData')
                                 ->with($request)
                                 ->willReturn($expectedUserData);

        $this->loggerInfoProvider->expects($this->once())
                                 ->method('getContextData')
                                 ->with($request)
                                 ->willReturn($expectedContextData);

        $expectedRequestContent = '{"dataset":[{"id":1},{"id":2}]}';
        $this->persistenceStrategy->expects($this->once())
                                  ->method('logRequest')
                                  ->with('POST', '/foo', 'Empty',
                                    $expectedUserData, $expectedContextData,
                                    $expectedRequestContent)
                                  ->will($this->throwException(new Exception('Database error')));

        $this->persistenceStrategy->expects($this->never())
                                  ->method('logResponse');

        $this->logger->logRequest($request, '/foo', 'POST', 'Empty');
        $this->logger->logResponse(500, array('error' => 'Invalid request'));
    }

    public function testBadFormattedRequest() {
        $expectedUserData       = 'User data';
        $expectedContextData    = 'Context data';

        $request = new Request(array(), array(), array(), 'a bad formatted request');
        $this->loggerInfoProvider->expects($this->once())
                                 ->method('getUserData')
                                 ->with($request)
                                 ->willReturn($expectedUserData);

        $this->loggerInfoProvider->expects($this->once())
                                 ->method('getContextData')
                                 ->with($request)
                                 ->willReturn($expectedContextData);

        $expectedRequestContent = 'a bad formatted request';
        $this->persistenceStrategy->expects($this->once())
                                  ->method('logRequest')
                                  ->with('POST', '/foo', 'Empty',
                                    $expectedUserData, $expectedContextData,
                                    $expectedRequestContent);

        $this->persistenceStrategy->expects($this->once())
                                  ->method('logResponse')
                                  ->with(500, '{"error":"Invalid request"}');

        $this->logger->logRequest($request, '/foo', 'POST', 'Empty');
        $this->logger->logResponse(500, array('error' => 'Invalid request'));
    }

    public function testBadFormattedResponse() {
        // $this->markTestSkipped("Can't reproduce error while json_encoding the response.");
        $expectedUserData       = 'User data';
        $expectedContextData    = 'Context data';

        $content = json_encode(array('dataset' => array(
            array('id' => 1),
            array('id' => 2)
        )));
        $request = new Request(array(), array(), array(), $content);

        $this->loggerInfoProvider->expects($this->once())
                                 ->method('getUserData')
                                 ->with($request)
                                 ->willReturn($expectedUserData);

        $this->loggerInfoProvider->expects($this->once())
                                 ->method('getContextData')
                                 ->with($request)
                                 ->willReturn($expectedContextData);

        $expectedRequestContent = '{"dataset":[{"id":1},{"id":2}]}';
        $this->persistenceStrategy->expects($this->once())
                                  ->method('logRequest')
                                  ->with('POST', '/foo', 'Empty',
                                    $expectedUserData, $expectedContextData,
                                    $expectedRequestContent);

        $this->persistenceStrategy->expects($this->once())
                                  ->method('logResponse')
                                  ->with(500, 'Error encoding response: Inf and NaN cannot be JSON encoded');

        $this->logger->logRequest($request, '/foo', 'POST', 'Empty');
        $this->logger->logResponse(500, array('error' => INF));
    }
}