<?php
namespace Zeedhi\Framework\HTTP\Logger\Persistence;

use Doctrine\DBAL\Connection;

class SQL implements PersistenceInterface {

    /** @var Connection */
    protected $connection;
    /** @var string */
    protected $dateFormat;

    /** @var int Request log identifier */
    protected $requestId;

    public function __construct(Connection $connection, $dateFormat = 'Y-m-d H:i:s') {
        $this->connection = $connection;
        $this->dateFormat = $dateFormat;
    }

    /**
     * {@inheritdoc}
     */
    public function logRequest($method, $route, $requestType, $userData, $contextData, $content) {
        $params = array(
            'METHOD'          => $method,
            'ROUTE'           => $route,
            'REQUEST_TYPE'    => $requestType,
            'BEGIN_REQUEST'   => $this->getDateTimeString(),
            'USER_DATA'       => $userData,
            'CONTEXT_DATA'    => $contextData,
            'REQUEST_CONTENT' => $content
        );

        $this->connection->executeUpdate(
            'INSERT INTO ZHU_LOG (METHOD, ROUTE, REQUEST_TYPE, BEGIN_REQUEST, USER_DATA, CONTEXT_DATA, REQUEST_CONTENT) VALUES (:METHOD, :ROUTE, :REQUEST_TYPE, :BEGIN_REQUEST, :USER_DATA, :CONTEXT_DATA, :REQUEST_CONTENT)',
            $params
        );

        $this->requestId = $this->connection->lastInsertId();
    }

    /**
     * {@inheritdoc}
     */
    public function logResponse($httpResponseCode, $content) {
        if ($this->requestId === null) {
            throw Exception::requestNotLogged();
        }

        $params = array(
            'REQUEST_ID'         => $this->requestId,
            'HTTP_RESPONSE_CODE' => $httpResponseCode,
            'END_REQUEST'        => $this->getDateTimeString(),
            'RESPONSE_CONTENT'   => $content
        );

        $this->connection->executeUpdate(
            'UPDATE ZHU_LOG SET HTTP_RESPONSE_CODE = :HTTP_RESPONSE_CODE, RESPONSE_CONTENT = :RESPONSE_CONTENT, END_REQUEST = :END_REQUEST WHERE ID = :REQUEST_ID',
        $params);
    }

    protected function getDateTimeString() {
        return (new \DateTime())->format($this->dateFormat);
    }
}