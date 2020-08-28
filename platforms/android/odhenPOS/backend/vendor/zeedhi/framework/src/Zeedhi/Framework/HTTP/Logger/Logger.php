<?php
namespace Zeedhi\Framework\HTTP\Logger;

use Zeedhi\Framework\HTTP\Logger\Persistence\PersistenceInterface;
use Zeedhi\Framework\HTTP\Logger\LoggerInfoProvider;
use Zeedhi\Framework\HTTP\Logger\Processor\Processor;
use Zeedhi\Framework\HTTP\Request;

class Logger {

    /** @var PersistenceInterface */
    protected $persistenceStrategy;
    /** @var PersistenceInterface */
    protected $loggerInfoProvider;
    /** @var Processor */
    protected $processor;

    /** @var string */
    protected $requestType;
    /** @var bool */
    protected $requestSkipped = false;

    /**
     * __construct
     *
     * @param PersistenceInterface $persistenceStrategy
     * @param LoggerInfoProvider   $loggerInfoProvider
     * @param Processor|null       $processor
     */
    public function __construct(PersistenceInterface $persistenceStrategy, LoggerInfoProvider $loggerInfoProvider, Processor $processor = null) {
        $this->persistenceStrategy = $persistenceStrategy;
        $this->loggerInfoProvider  = $loggerInfoProvider;
        $this->processor           = $processor;
    }

    /**
     * @param Request $request
     * @param string  $uri
     * @param string  $method
     * @param string  $requestType
     *
     * @throws Exception
     */
    public function logRequest($request, $uri, $method, $requestType) {
        $this->requestSkipped = false;
        try {
            $requestContent = $this->processRequestContent($request->getContent(), $uri, $method);

            $userData    = $this->loggerInfoProvider->getUserData($request);
            $contextData = $this->loggerInfoProvider->getContextData($request);

            $this->persistenceStrategy->logRequest($method, $uri, $requestType, $userData, $contextData, $requestContent);
        } catch (Exception $e) {
            if ($e->getCode() === Exception::SKIP_EXCEPTION) {
                $this->requestSkipped = true;
            } else {
                throw $e;
            }
        }
    }

    /**
     * @param array $content
     */
    public function logResponse($status, array $content) {
        if (!$this->requestSkipped) {
            $responseContent = $this->processResponseContent($content);
            $this->persistenceStrategy->logResponse($status, $responseContent);
        }
    }

    /**
     * @param string $request
     * @param string $route
     * @param string $method
     *
     * @return string
     */
    protected function processRequestContent($request, $route, $method) {
        $processedRequest = $request;
        if ($this->processor && ($json_decode = json_decode($request, true)) !== null) {
            $processedRequest = json_encode($this->processor->processRequest($json_decode, $route, $method));
        }

        return $processedRequest;
    }

    /**
     * @param array $content
     *
     * @return string
     */
    protected function processResponseContent(array $content) {
        if ($this->processor) {
            $content = $this->processor->processResponse($content);
        }

        return json_encode($content) ?: 'Error encoding response: ' . json_last_error_msg();
    }

}