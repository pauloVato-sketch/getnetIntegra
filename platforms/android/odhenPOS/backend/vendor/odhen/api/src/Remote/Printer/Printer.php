<?php
namespace Odhen\API\Remote\Printer;

use Odhen\API\Remote\Printer\Command;
use \Zeedhi\Framework\Remote\cURLRequest;

class Printer {

    protected $curl;

    public function __construct(){
        $this->curl = new cURLRequest(null, curlRequest::METHOD_POST, array(
            CURLOPT_CONNECTTIMEOUT => 4,
            CURLOPT_TIMEOUT => 18
        ));
        $this->curl->setHeaders(array(
            curlRequest::CONTENT_TYPE_HEADER => curlRequest::CONTENT_TYPE_APPLICATION_JSON
        ));
    }

    public function preparePrint(Command $command) {
        return new Printing($this->curl, $command);
    }

    public function getPrinterStatus($printerType) {
        $result = $this->curl->request('printerStatus', array(
            'printerType' => $printerType
        ));

        $result = json_decode($result, true);

        if ($result['error']) {
            throw new \Exception($result['message']);
        }

        return $result;
    }

}