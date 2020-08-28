<?php

namespace Odhen\API\Lib;

use NFePHP\Common\Soap\CurlSoap;
use NFePHP\Common\Strings\Strings;
use NFePHP\Common\Exception;

class NfceCurlSoap extends CurlSoap {

    protected $errorCurl;
    protected $priKeyPath;
    protected $pubKeyPath;
    protected $certKeyPath;
    protected $timeout;
    protected $proxyIP = '';
    protected $proxyPORT = '';
    protected $proxyUSER = '';
    protected $proxyPASS = '';
    protected $infoCurl = array();
    public $soapDebug = '';

    public function __construct($priKeyPath = '', $pubKeyPath = '', $certKeyPath = '', $timeout = 10, $sslProtocol = 0) {
        $this->priKeyPath = $priKeyPath;
        $this->pubKeyPath = $pubKeyPath;
        $this->certKeyPath = $certKeyPath;
        $this->soapTimeout = $timeout;
        // if ($sslProtocol < 0 || $sslProtocol > 6) {
        //     $msg = "O protocolo SSL pode estar entre 0 e seis, inclusive, mas não além desses números.";
        //     throw new Exception\InvalidArgumentException($msg);
        // }
        $this->sslProtocol = $sslProtocol;
        // if (! is_file($priKeyPath) || ! is_file($pubKeyPath) || ! is_file($certKeyPath) || ! is_numeric($timeout)) {
        //     $msg = "Alguns dos certificados não foram encontrados ou o timeout pode não ser numérico.";
        //     throw new Exception\InvalidArgumentException($msg);
        // }
    }

    public function send($urlservice, $namespace, $header, $body, $method) {
        $data = '<?xml version="1.0" encoding="utf-8"?>'.'<soap12:Envelope ';
        $data .= 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
        $data .= 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" ';
        $data .= 'xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">';
        $data .= '<soap12:Header>'.$header.'</soap12:Header>';
        $data .= '<soap12:Body>'.$body.'</soap12:Body>';
        $data .= '</soap12:Envelope>';
        $data = Strings::clearMsg($data);
        $this->lastMsg = $data;
        $tamanho = strlen($data);
        $parametros = array(
            'Content-Type: application/soap+xml;charset=utf-8',
            'SOAPAction: "'.$method.'"'
        );
        $resposta = $this->zCommCurl($urlservice, $data, $parametros);
        if (empty($resposta)) {
            $msg = "Não houve retorno do Curl.\n $this->errorCurl";
            throw new Exception\RuntimeException($msg);
        }
        $xPos = stripos($resposta, "<");
        $blocoHtml = substr($resposta, 0, $xPos);
        if ($this->infoCurl["http_code"] != '200') {
            $msg = $blocoHtml;
            throw new Exception\RuntimeException($msg);
        }
        $lenresp = strlen($resposta);
        $xPos = stripos($resposta, "<");
        if ($xPos !== false) {
            $xml = substr($resposta, $xPos, $lenresp-$xPos);
        } else {
            $xml = '';
        }
        $result = simplexml_load_string($xml, 'SimpleXmlElement', LIBXML_NOERROR+LIBXML_ERR_FATAL+LIBXML_ERR_NONE);
        if ($result === false) {
            $xml = '';
        }
        if ($xml == '') {
            $msg = "Não houve retorno de um xml verifique soapDebug!!";
            throw new Exception\RuntimeException($msg);
        }
        if ($xml != '' && substr($xml, 0, 5) != '<?xml') {
            $xml = '<?xml version="1.0" encoding="utf-8"?>'.$xml;
        }
        return $xml;
    }

    protected function zCommCurl($url, $data = '', $parametros = array(), $port = 443) {
        $oCurl = curl_init();
        if ($this->proxyIP != '') {
            curl_setopt($oCurl, CURLOPT_HTTPPROXYTUNNEL, 1);
            curl_setopt($oCurl, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
            curl_setopt($oCurl, CURLOPT_PROXY, $this->proxyIP.':'.$this->proxyPORT);
            if ($this->proxyPASS != '') {
                curl_setopt($oCurl, CURLOPT_PROXYUSERPWD, $this->proxyUSER.':'.$this->proxyPASS);
                curl_setopt($oCurl, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
            }
        }
        curl_setopt($oCurl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT, $this->soapTimeout);
        curl_setopt($oCurl, CURLOPT_TIMEOUT, $this->soapTimeout * 6);
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_VERBOSE, 1);
        curl_setopt($oCurl, CURLOPT_HEADER, 1);
        if ($this->sslProtocol !== 0) {
            curl_setopt($oCurl, CURLOPT_SSLVERSION, $this->sslProtocol);
        }
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, 0);
        if ($port == 443) {
            curl_setopt($oCurl, CURLOPT_PORT, 443);
            curl_setopt($oCurl, CURLOPT_SSLCERT, $this->certKeyPath);
            curl_setopt($oCurl, CURLOPT_SSLKEY, $this->priKeyPath);
        } else {
            $agent= 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
            curl_setopt($oCurl, CURLOPT_USERAGENT, $agent);
        }
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        if ($data != '') {
            curl_setopt($oCurl, CURLOPT_POST, 1);
            curl_setopt($oCurl, CURLOPT_POSTFIELDS, $data);
        }
        if (!empty($parametros)) {
            curl_setopt($oCurl, CURLOPT_HTTPHEADER, $parametros);
        }
        $resposta = curl_exec($oCurl);
        $info = curl_getinfo($oCurl);
        $this->zDebug($info, $data, $resposta);
        $this->errorCurl = curl_error($oCurl);
        curl_close($oCurl);
        return $resposta;
    }

    public function setProxy($ipNumber, $port, $user = '', $pass = '') {
        $this->proxyIP = $ipNumber;
        $this->proxyPORT = $port;
        $this->proxyUSER = $user;
        $this->proxyPASS = $pass;
    }

    private function zDebug($info = array(), $data = '', $resposta = '')
    {
        $this->infoCurl["url"] = $info["url"];
        $this->infoCurl["content_type"] = $info["content_type"];
        $this->infoCurl["http_code"] = $info["http_code"];
        $this->infoCurl["header_size"] = $info["header_size"];
        $this->infoCurl["request_size"] = $info["request_size"];
        $this->infoCurl["filetime"] = $info["filetime"];
        $this->infoCurl["ssl_verify_result"] = $info["ssl_verify_result"];
        $this->infoCurl["redirect_count"] = $info["redirect_count"];
        $this->infoCurl["total_time"] = $info["total_time"];
        $this->infoCurl["namelookup_time"] = $info["namelookup_time"];
        $this->infoCurl["connect_time"] = $info["connect_time"];
        $this->infoCurl["pretransfer_time"] = $info["pretransfer_time"];
        $this->infoCurl["size_upload"] = $info["size_upload"];
        $this->infoCurl["size_download"] = $info["size_download"];
        $this->infoCurl["speed_download"] = $info["speed_download"];
        $this->infoCurl["speed_upload"] = $info["speed_upload"];
        $this->infoCurl["download_content_length"] = $info["download_content_length"];
        $this->infoCurl["upload_content_length"] = $info["upload_content_length"];
        $this->infoCurl["starttransfer_time"] = $info["starttransfer_time"];
        $this->infoCurl["redirect_time"] = $info["redirect_time"];
        //coloca as informações em uma variável
        $txtInfo ="";
        foreach ($info as $key => $content) {
            if (is_string($content)) {
                $txtInfo .= strtoupper($key).'='.$content."\n";
            }
        }
        //carrega a variavel debug
        $this->soapDebug = $data."\n\n".$txtInfo."\n".$resposta;
    }

    private function logNFCE($text) {
        //testa se a pasta existe e cria caso não exista
        $folder = __DIR__ . "/../../../../../../../NFCE/LOGS/";
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }
        //--grava o log
        $line = date("d-m-Y H:i:s") . " - " . $text . "\n";
        $date = date("dmY");
        file_put_contents($folder . "error" . $date . ".txt", $line, FILE_APPEND);
    }
}