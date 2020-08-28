<?php
namespace Zeedhi\Framework\HTTP\Logger\Persistence;

interface PersistenceInterface {

    /**
     * logRequest
     *
     * @param  string $method         Request method
     * @param  string $route          Request route
     * @param  string $requestType    Request type
     * @param  string $userData       User associated data
     * @param  string $contextData    Context associetad data
     * @param  string $content        Request data
     */
    public function logRequest($method, $route, $requestType, $userData, $contextData, $content);

    /**
     * logResponse
     *
     * @param  string $httpResponseCode
     * @param  string $content          Response
     */
    public function logResponse($httpResponseCode, $content);
}