<?php

namespace Odhen\API\Service;

use \SimpleXMLElement;

class Extratocons {

    protected $entityManager;
    protected $instanceManager;
    protected $sessionHandler;

    public function __construct(\Doctrine\ORM\EntityManager $entityManager, \Zeedhi\Framework\DependencyInjection\InstanceManager $instanceManager, \Helpers\Environment $sessionHandler){
        $this->entityManager = $entityManager;
        $this->instanceManager = $instanceManager;
        $this->sessionHandler = $sessionHandler;
    }

    public function consultaSaldo($CDFILIAL, $CDCLIENTE, $CDCONSUMIDOR, $CDFAMILISALD){
        $params = array(
            'P_TPOPERACAO' => 'CONSULTA_SALDO',
            'P_CDFILIAL' => $CDFILIAL,
            'P_CDCLIENTE' => $CDCLIENTE,
            'P_CDCONSUMIDOR' => $CDCONSUMIDOR,
            'P_CDFAMILISALD' => $CDFAMILISALD,
            'P_DTEXTRATO' => ''
        );
        $result = self::callExtratocons('RecuperaDados', $params);
        if (!empty($result) && !isset($result[0]['VRSALDCONFAM'])){
            $result = $result[0];
        }
        return $result;
    }

    public function consultaCartao($searchValue){
        $params = array(
            'P_TPOPERACAO' => 'CONSULTA_CARTAO',
            'P_CDFILIAL' => '',
            'P_CDCLIENTE' => '',
            'P_CDCONSUMIDOR' => $searchValue,
            'P_CDFAMILISALD' => '',
            'P_DTEXTRATO' => ''
        );
        $result = self::callExtratocons('RecuperaDados', $params);
        return $result;
    }

    public function consultaConsumoFamilia($CDCLIENTE, $CDCONSUMIDOR, $date){
        $params = array(
            'P_TPOPERACAO' => 'CONSULTA_CONSUMO_FAMILIA',
            'P_CDFILIAL' => '',
            'P_CDCLIENTE' => $CDCLIENTE,
            'P_CDCONSUMIDOR' => $CDCONSUMIDOR,
            'P_CDFAMILISALD' => '',
            'P_DTEXTRATO' => $date
        );
        $result = self::callExtratocons('RecuperaDados', $params);
        return $result;
    }

    public function insereExtratocons($params){
        $xml = new SimpleXMLElement('<ExtratoCons><Registro></Registro></ExtratoCons>');
        foreach ($params as $key => $value){
            $xml->Registro->addChild(strtolower($key), $value);
        }

        $params = array(
            'P_XMLEXTCONS' => $xml->asXml()
        );
        self::callExtratocons('InsereDados', $params);
    }

    public function consultaFidelidade($CDCLIENTE, $CDCONSUMIDOR){
        $params = array(
            'P_TPOPERACAO' => 'CONSULTA_SALDO_FID',
            'P_CDFILIAL' => '',
            'P_CDCLIENTE' => $CDCLIENTE,
            'P_CDCONSUMIDOR' => $CDCONSUMIDOR,
            'P_CDFAMILISALD' => '',
            'P_DTEXTRATO' => ''
        );
        $result = self::callExtratocons('RecuperaDados', $params);
        return $result;
    }

    private function callExtratocons($method, $params){
        $postfields = "";
        foreach ($params as $key => $param){
            $postfields .= "&".$key."=".$param;
        }
        $postfields = substr($postfields, 1);

        $URL = $this->sessionHandler->getUserInfo()['CDURLWSEXTCONS'];
        $URL = rtrim($URL, '/') . "/";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $URL.$method);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 20);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        if (!empty($this->instanceManager->getParameter('PROXY_IP'))){
            curl_setopt($curl, CURLOPT_PROXY, $this->instanceManager->getParameter('PROXY_IP').":".$this->instanceManager->getParameter('PROXY_PORT'));
            if (!empty($this->instanceManager->getParameter('PROXY_USER'))){
                curl_setopt($curl, CURLOPT_PROXYUSERPWD, $this->instanceManager->getParameter('PROXY_USER').":".$this->instanceManager->getParameter('PROXY_PASSWORD'));
            }
        }

        $curl_response = curl_exec($curl);
        $err    = curl_errno($curl);
        $errmsg = curl_error($curl);
        curl_close($curl);

        if ($err){
            if ($err === 28){
                throw new \Exception("<br>N&atilde;o foi poss&iacute;vel estabelecer conex&atilde;o com o servidor do EXTRATOCONS ONLINE.");
            }
            else {
                throw new \Exception("<br>Ocorreu um erro na chamada do EXTRATOCONS ONLINE:<br><br>" . $errmsg);
            }
        }

        if ($method == 'RecuperaDados'){
            if (!self::isXml($curl_response)){
                throw new \Exception("<br>O EXTRATOCONS ONLINE retornou uma mensagem inesperada:<br><br>" . $curl_response);
            }
            else {
                $xml = simplexml_load_string($curl_response);
                $json_result = json_encode($xml);

                $index = 0;
                while (strpos($json_result, 'Registro') > -1){
                    $json_result = preg_replace(preg_quote('"Registro"', '/'), $index++, $json_result, 1);
                }

                $result = json_decode($json_result, true);

                return $result['ExtratoCons'];
            }
        }
        else if ($method == 'InsereDados' && $curl_response !== "OK"){
            throw new \Exception("<br>O EXTRATOCONS ONLINE retornou um erro:<br><br>" . $curl_response);
        }
    }

    public function isXml($string){
        return substr($string, 0, 5) == "<?xml";
    }

}