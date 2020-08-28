<?php
namespace Zeedhi\Framework\HTTP\Logger\Persistence;

use Doctrine\DBAL\Connection;

class MultiRowSQL implements PersistenceInterface {

    const DEFAULT_EMPTY_CONTENT = '##ZHU_LOG_EMPTY_CONTENT##';

    /** @var Connection */
    protected $connection;
    /** @var string */
    protected $dateFormat;

    /** @var string */
    protected $requestId;
    /** @var string */
    protected $method;
    /** @var string */
    protected $route;
    /** @var string */
    protected $requestType;
    /** @var string */
    protected $userData;
    /** @var string */
    protected $contextData;

    public function __construct(Connection $connection, $dateFormat = 'Y-m-d H:i:s') {
        $this->connection = $connection;
        $this->dateFormat = $dateFormat;
    }

    /**
     * {@inheritdoc}
     */
    public function logRequest($method, $route, $requestType, $userData, $contextData, $content) {
        $this->requestId   = uniqid();
        $this->method      = $method;
        $this->route       = $route;
        $this->requestType = $requestType;
        $this->userData    = $userData;
        $this->contextData = $contextData;
        $content = strlen($content) === 0 ? self::DEFAULT_EMPTY_CONTENT : $content;

        $params = array(
            'TYPE'               => 'REQUEST',
            'REQ_ID'             => $this->requestId,
            'METHOD'             => $this->method,
            'ROUTE'              => $this->route,
            'REQUEST_TYPE'       => $this->requestType,
            'TIMESTAMP'          => $this->getDateTimeString(),
            'USER_DATA'          => $this->userData,
            'CONTEXT_DATA'       => $this->contextData,
            'CONTENT'            => $content
        );

        $this->connection->executeUpdate(
            'INSERT INTO ZHU_LOG (TYPE, REQ_ID, METHOD, ROUTE, REQUEST_TYPE, TIMESTAMP, USER_DATA, CONTEXT_DATA, CONTENT) VALUES (:TYPE, :REQ_ID, :METHOD, :ROUTE, :REQUEST_TYPE, :TIMESTAMP, :USER_DATA, :CONTEXT_DATA, :CONTENT)',
            $params);
    }

    /**
     * {@inheritdoc}
     */
    public function logResponse($httpResponseCode, $content) {
        if ($this->requestId === null) {
            throw Exception::requestNotLogged();
        }

        $params = array(
            'TYPE'               => 'RESPONSE',
            'HTTP_RESPONSE_CODE' => $httpResponseCode,
            'REQ_ID'             => $this->requestId,
            'METHOD'             => $this->method,
            'ROUTE'              => $this->route,
            'REQUEST_TYPE'       => $this->requestType,
            'TIMESTAMP'          => $this->getDateTimeString(),
            'USER_DATA'          => $this->userData,
            'CONTEXT_DATA'       => $this->contextData,
            'CONTENT'            => $content
        );

        $this->connection->executeUpdate(
            'INSERT INTO ZHU_LOG (TYPE, HTTP_RESPONSE_CODE, REQ_ID, METHOD, ROUTE, REQUEST_TYPE, TIMESTAMP, USER_DATA, CONTEXT_DATA, CONTENT) VALUES (:TYPE, :HTTP_RESPONSE_CODE, :REQ_ID, :METHOD, :ROUTE, :REQUEST_TYPE, :TIMESTAMP, :USER_DATA, :CONTEXT_DATA, :CONTENT)',
            $params);
    }

    /**
     * @return string
     */
    protected function getDateTimeString() {
        return (new \DateTime())->format($this->dateFormat);
    }
}