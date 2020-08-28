<?php
namespace Zeedhi\Framework\HTTP\Logger\Processor;

class SkipFilesProcessor extends Processor {

    /** @var array */
    protected $fileRoutes = array();

    public function __construct(array $fileRoutes) {
        $this->fileRoutes = $fileRoutes;
    }

    /**
     * processRequest
     * Remove base64 files data from log
     *
     * @param  array  $request Request
     * @param  string $route   Request route
     * @param  string $method  Request method
     *
     * @return array
     */
    public function processRequest(array $request, $route, $method) {
        if (isset($this->fileRoutes[$route])) {
            $request = $this->removeFileFields($request, $this->fileRoutes[$route]['fileField']);
        }

        return $request;
    }

    /**
     * processResponse
     *
     * @param array $response
     *
     * @return array
     */
    public function processResponse(array $response) {
        return $response;
    }

    protected function removeFileFields(array $request, $field) {
        if (isset($request['row'])) {
            $request['row'] = $this->removeFieldFromRow($request['row'], $field);
        } else if (isset($request['dataset'])) {
            $request['dataset'] = $this->removeFieldFromRows($request['dataset'], $field);
        }

        return $request;
    }

    protected function removeFieldFromRows($rows, $field) {
        return array_map(function($row) use ($field) {
            return $this->removeFieldFromRow($row, $field);
        }, $rows);
    }

    protected function removeFieldFromRow($row, $field) {
        if (!empty($row[$field])) {
            $row[$field] = array_map(function($file) {
                unset($file['b64File']);
                return $file;
            }, $row[$field]);
        }

        return $row;
    }

}