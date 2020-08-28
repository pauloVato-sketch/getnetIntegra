<?php
namespace Zeedhi\Framework\HTTP\Logger\Processor;

class CompositeProcessor extends Processor {

    /** @var Processor */
    protected $firstProcessor;
    /** @var Processor */
    protected $secondProcessor;

    public function __construct(Processor $firstProcessor, Processor $secondProcessor) {
        $this->firstProcessor = $firstProcessor;
        $this->secondProcessor = $secondProcessor;
    }

    public function processRequest(array $request, $route, $method) {
        return $this->secondProcessor->processRequest(
            $this->firstProcessor->processRequest($request, $route, $method),
            $route, $method
        );
    }

    public function processResponse(array $response) {
        return $this->secondProcessor->processResponse(
            $this->firstProcessor->processResponse($response)
        );
    }

}