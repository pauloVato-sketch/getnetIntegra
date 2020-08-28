<?php
namespace Zeedhi\Framework\HTTP\Logger\Processor;

class DefaultProcessor extends Processor {

    const KEEP_DATA_SET   = 1;
    const COMPACT_DATA_SET = 2;
    const REMOVE_DATA_SET = 4;

    /** @var string[] */
    protected $routesToSkip = array();
    /** @var int */
    protected $responseDataSetPolicy = self::KEEP_DATA_SET;
    /** @var int */
    protected $requestDataSetPolicy  = self::KEEP_DATA_SET;

    /**
     * DefaultProcessor constructor.
     * @param \string[] $routesToSkip
     * @param int       $responseDataSetPolicy
     * @param int       $requestDataSetPolicy
     */
    public function __construct(array $routesToSkip, $responseDataSetPolicy = self::KEEP_DATA_SET, $requestDataSetPolicy = self::KEEP_DATA_SET) {
        $this->routesToSkip = $routesToSkip;
        $this->responseDataSetPolicy = $responseDataSetPolicy;
        $this->requestDataSetPolicy = $requestDataSetPolicy;
    }

    /**
     * @param int $responseDataSetPolicy
     */
    public function setResponseDataSetPolicy($responseDataSetPolicy) {
        $this->responseDataSetPolicy = $responseDataSetPolicy;
    }

    /**
     * @param int $requestDataSetPolicy
     */
    public function setRequestDataSetPolicy($requestDataSetPolicy) {
        $this->requestDataSetPolicy = $requestDataSetPolicy;
    }

    /**
     * @param array  $request
     * @param string $route
     * @param string $method
     *
     * @return array
     *
     * @throws Exception Skip Current Request.
     */
    public function processRequest(array $request, $route, $method) {
        if (in_array($route, $this->routesToSkip)) {
            $this->skipCurrentRequest();
        }

        switch ($this->requestDataSetPolicy) {
            case DefaultProcessor::REMOVE_DATA_SET:
                unset($request['dataset']);
                break;
            case DefaultProcessor::COMPACT_DATA_SET:
                $request['dataset'] = count($request['dataset']);
                break;
        }

        return $request;
    }

    /**
     * @param array $response
     *
     * @return array
     */
    public function processResponse(array $response) {
        switch ($this->responseDataSetPolicy) {
            case DefaultProcessor::REMOVE_DATA_SET:
                unset($response['dataset']);
                break;
            case DefaultProcessor::COMPACT_DATA_SET:
                if (isset($response['dataset'])) {
                    foreach($response['dataset'] as $name => $data) {
                        $response['dataset'][$name] = count($data);
                    }
                }

                break;
        }

        return $response;
    }
}