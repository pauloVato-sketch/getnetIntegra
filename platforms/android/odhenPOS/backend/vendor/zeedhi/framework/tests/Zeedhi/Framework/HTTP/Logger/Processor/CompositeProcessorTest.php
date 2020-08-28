<?php
namespace tests\Zeedhi\Framework\HTTP\Logger\Processor;

use Zeedhi\Framework\HTTP\Logger\Processor\Processor;
use Zeedhi\Framework\HTTP\Logger\Processor\CompositeProcessor;

class CompositeProcessorTest extends \PHPUnit\Framework\TestCase {

    protected $firstProcessor;
    protected $secondProcessor;
    protected $compositeProcessor;

    public function setUp() {
        $this->firstProcessor  = $this->getMockBuilder(Processor::class)
                                      ->setMethods(array('processRequest', 'processResponse'))
                                      ->disableOriginalConstructor()
                                      ->getMock();

        $this->secondProcessor = $this->getMockBuilder(Processor::class)
                                      ->setMethods(array('processRequest', 'processResponse'))
                                      ->disableOriginalConstructor()
                                      ->getMock();

        $this->compositeProcessor = new CompositeProcessor($this->firstProcessor, $this->secondProcessor);
    }

    public function testProcessRequest() {
        $originalRequest = array(
            'dataset' => array(array(
                    '_id' => 0,
                    'foo' => 'FIELD_THAT_WILL_BE_REMOVED_ON_FIRST_PROCESSOR',
                    'bar' => 'FIELD_THAT_WILL_HAVE_AN_STRING_APPENDED'
                ), array(
                    '_id' => 1,
                    'foo' => 'FIELD_THAT_WILL_BE_REMOVED_ON_FIRST_PROCESSOR',
                    'bar' => 'FIELD_THAT_WILL_HAVE_AN_STRING_APPENDED'
                )));

        $route = '/route';
        $method = 'POST';

        $requestWithFirstProcess = array(
            'dataset' => array(array(
                    '_id' => 0,
                    'bar' => 'FIELD_THAT_WILL_HAVE_AN_STRING_APPENDED'
                ), array(
                    '_id' => 1,
                    'bar' => 'FIELD_THAT_WILL_HAVE_AN_STRING_APPENDED'
                )));
        $this->firstProcessor->expects($this->once())
                             ->method('processRequest')
                             ->with($originalRequest, $route, $method)
                             ->will($this->returnValue($requestWithFirstProcess));

        $finalRequest = array(
            'dataset' => array(array(
            '_id' => 0,
            'bar' => 'FIELD_THAT_WILL_HAVE_AN_STRING_APPENDED_TEST'
        ), array(
            '_id' => 1,
            'bar' => 'FIELD_THAT_WILL_HAVE_AN_STRING_APPENDED_TEST'
        )));
        $this->secondProcessor->expects($this->once())
                              ->method('processRequest')
                              ->with($requestWithFirstProcess, $route, $method)
                              ->will($this->returnValue($finalRequest));

        $result = $this->compositeProcessor->processRequest($originalRequest, $route, $method);

        $this->assertEquals($finalRequest, $result);
    }

    public function testProcessResponse() {
        $originalResponse = array(
            'foo' => array(
                array('id' => 0),
                array('id' => 1),
                array('id' => 2),
                array('id' => 3)
            )
        );

        $requestWithFirstProcess = array(
            'foo' => 4
        );

        $this->firstProcessor->expects($this->once())
                             ->method('processResponse')
                             ->with($originalResponse)
                             ->will($this->returnValue($requestWithFirstProcess));

        $finalResponse = array(
            'foo' => 4,
            'rowCount' => 4
        );
        $this->secondProcessor->expects($this->once())
                              ->method('processResponse')
                              ->with($requestWithFirstProcess)
                              ->will($this->returnValue($finalResponse));

        $result = $this->compositeProcessor->processResponse($originalResponse);

        $this->assertEquals($finalResponse, $result);
    }

}