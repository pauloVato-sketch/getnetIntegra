<?php
namespace tests\Zeedhi\Framework\HTTP\Logger\Processor;

use Zeedhi\Framework\HTTP\Logger\Processor\SkipFilesProcessor;

class SkipFilesProcessorTest extends \PHPUnit\Framework\TestCase {

    protected $processor;

    public function setUp() {
        $routes = array(
            '/uploadRoute' => array(
                'fileField'    => 'uploadField',
                'customFolder' => 'folder'
            )
        );
        $this->processor = new SkipFilesProcessor($routes);
    }

    public function testProcessRequestRow() {
        $request = array(
            'row' => array(
                'id' => 1,
                'uploadField' => array(
                    array(
                        'name' => 'file1.png',
                        'b64File' => '__RANDOM_VALUE_FOR_TEST__'
                    ),
                    array(
                        'name' => 'file2.png',
                        'b64File' => '__RANDOM_VALUE_FOR_TEST__'
                    )
                ),
                'foo' => 'bar'
            )
        );
        $route   = '/uploadRoute';
        $method  = 'POST';

        $result = $this->processor->processRequest($request, $route, $method);

        $expected = array(
            'row' => array(
                'id' => 1,
                'uploadField' => array(
                    array(
                        'name' => 'file1.png',
                    ),
                    array(
                        'name' => 'file2.png',
                    )
                ),
                'foo' => 'bar'
            )
        );
        $this->assertEquals($expected, $result);
    }

    public function testProcessRequestDataSet() {
        $request = array(
            'dataset' => array(
                array(
                    'id' => 1,
                    'uploadField' => array(
                        array(
                            'name' => 'file1.png',
                            'b64File' => '__RANDOM_VALUE_FOR_TEST__'
                        ),
                        array(
                            'name' => 'file2.png',
                            'b64File' => '__RANDOM_VALUE_FOR_TEST__'
                        )
                    ),
                    'foo' => 'bar'
                ),
                array(
                    'id' => 2,
                    'uploadField' => array(
                        array(
                            'name' => 'file1.png',
                            'b64File' => '__RANDOM_VALUE_FOR_TEST__'
                        ),
                        array(
                            'name' => 'file2.png',
                            'b64File' => '__RANDOM_VALUE_FOR_TEST__'
                        )
                    ),
                    'foo' => 'bar'
                )
            )
        );
        $route   = '/uploadRoute';
        $method  = 'POST';

        $result = $this->processor->processRequest($request, $route, $method);

        $expected = array(
            'dataset' => array(
                array(
                    'id' => 1,
                    'uploadField' => array(
                        array(
                            'name' => 'file1.png',
                        ),
                        array(
                            'name' => 'file2.png',
                        )
                    ),
                    'foo' => 'bar'
                ),
                array(
                    'id' => 2,
                    'uploadField' => array(
                        array(
                            'name' => 'file1.png',
                        ),
                        array(
                            'name' => 'file2.png',
                        )
                    ),
                    'foo' => 'bar'
                )
            )
        );
        $this->assertEquals($expected, $result);
    }

    public function testProcessResponse() {
        $response = array(
            'foo' => array(
                array('id' => 1, 'file' => 'path_to_file/582f34a61ec42.png'),
                array('id' => 2, 'file' => 'path_to_file/582f34abae575.png')
            )
        );
        $result = $this->processor->processResponse($response);

        $this->assertEquals($response, $result);
    }

    public function testRequestWithEmptyFile() {
        $request = array(
            'row' => array(
                'id' => 1,
                'foo' => 'bar',
                'notUploadField' => 'qux'
            )
        );
        $route   = '/uploadRoute';
        $method  = 'POST';

        $result = $this->processor->processRequest($request, $route, $method);

        $this->assertEquals($request, $result);
    }

}