<?php

namespace Odhen\API\Lib;

use NFePHP\NFe\Tools;
use NFePHP\NFe\Common\Config;
use NFePHP\Common\Soap\SoapCurl;
use NFePHP\NFe\Factories\Contingency;

class NfceTools extends Tools {

    public function __construct($configJson, Certificate $certificate) {
        $this->pathwsfiles = realpath(
            __DIR__ . '/../../storage'
        ).'/';
        //valid config json string
        $this->config = Config::validate($configJson);
        
        $this->version($this->config->versao);
        $this->setEnvironmentTimeZone($this->config->siglaUF);
        $this->certificate = $certificate;
        $this->setEnvironment($this->config->tpAmb);
        $this->contingency = new Contingency();
        $this->soap = new SoapCurl($certificate);
        // __construct sobrescrevido devido ao proxy não setado
        $this->soap->proxy();
    }

}