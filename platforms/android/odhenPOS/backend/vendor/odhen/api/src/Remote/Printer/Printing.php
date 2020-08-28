<?php
namespace Odhen\API\Remote\Printer;

use Odhen\API\Remote\Printer\Command;
use Zeedhi\Framework\Remote\Exception;

class Printing {

    protected $curl;
    protected $printerProperties;
    protected $command;

    public function __construct($curl, Command $command) {
        $this->curl = $curl;
        $this->command = $command;
    }

    public function setPrinterProperties(array $printerProperties) {
        $this->printerProperties = $printerProperties;

        return $this;
    }

    public function setUrl($url) {
    	$this->curl->setBaseUrl($url);
    }

    public function sendPrint() {
        try {
            $result = $this->curl->request('/print', array(
                'printerInfo' => $this->printerProperties,
                'commands' => $this->command->getCommands()
            ));

            $resultJson = json_decode($result, true);

            if ($resultJson['error']) {
                throw new \Exception($resultJson['message']);
            } else if ($resultJson == null) {
            	throw new \Exception($result);
            }

            return $resultJson;
        }  catch(Exception $e) {
            throw new \Exception('Endereço do Periféricos inválido ou se encontra desligado.');
        } catch(\Exception $e) {
            throw $e;
        }
    }

    public function testPrint() {
        try {
            $result = $this->curl->request('/test', array(
                'printerInfo' => $this->printerProperties
            ));

            $resultJson = json_decode($result, true);

            if ($resultJson['error']) {
                throw new \Exception($resultJson['message']);
            } else if ($resultJson == null) {
                throw new \Exception($result);
            }

            return $resultJson;        
        }  catch(Exception $e) {
            throw new \Exception('Endereço do Periféricos inválido ou se encontra desligado.');
        } catch(\Exception $e) {
            throw $e;
        }
    }

}