<?php
namespace Zeedhi\Framework\HTTP\Logger\Processor;

use Zeedhi\Framework\HTTP\Logger\Exception;

abstract class Processor {

    /**
     * @param array  $request
     * @param string $route
     * @param string $method
     *
     * @return array
     */
    abstract public function processRequest(array $request, $route, $method);

    /**
     * @param array $response
     *
     * @return array
     */
    abstract public function processResponse(array $response);

    /**
     * @throws Exception Skip current request.
     */
    protected function skipCurrentRequest() {
        throw Exception::skipCurrentRequest();
    }

}