<?php

namespace Odhen\API\Service;

class PainelSenha {

    protected $instanceManager;
    protected $token = '';

    public function __construct(
        \Zeedhi\Framework\DependencyInjection\InstanceManager $instanceManager){
        $this->instanceManager = $instanceManager;
        // Paramêtros do enviroment para o painel de senha do Madero.
        $this->usePublisher   = $this->instanceManager->getParameter('USE_PUBLISHER');
        $this->email          = $this->instanceManager->getParameter('EMAIL');
        $this->password       = $this->instanceManager->getParameter('PWD');
        $this->auth           = $this->instanceManager->getParameter('ENDPOINT_AUTH');
        $this->acess          = $this->instanceManager->getParameter('ENDPOINT_PSD');
    }


    public function buscaDados($CDFILIAL, $CDLOJA, $CDSENHAPED, $NRSEQVENDA) {
        $result = array ( 
            'error' => true,
            'errmsg' => ''
        );
        if ($this->usePublisher == 'true') {
            $result = $this->verificaParametros();
            if (!$result['error']) { 
                $result = $this->getAcessToken();
                if (!$result['error']) {
                    $this->token = $result['token'];
                    $result = $this->getPassPanelData($CDFILIAL, $CDLOJA, $CDSENHAPED, $NRSEQVENDA);
                    return $result;
                } else {
                    return $result;
                }
            } else {
                return $result;
            }
        }

        return $result;
    }

    private function verificaParametros() {
        if (!empty($this->email) && !empty($this->password) && !empty($this->auth) && !empty($this->acess)) {
            return array ('error' =>  false);
        } 

        return array ( 
            'error' => true,
            'errmsg' => 'Verifique o preenchimento de todos parâmetros da API do Painel de Senhas no environment.'
        );
    }


    // Gera e salva um novo token na session ou Verifica a validade de um anterior e o utiliza novamente.
    private function getAcessToken() {
        $dtAtual = new \Datetime();
        $dtAtualTimeStamp = $dtAtual->getTimestamp();
        if ($this->verifyGenerateAcessToken($dtAtualTimeStamp)) {
            $result = array('error' => false,
                            'token' => $_SESSION['acessTokenPassPainel']);
        } else {
            $params = array (
                'EMAIL' => $this->email,
                'PWD'   => $this->password
            );
            $result = self::callAPIPainelSenha('getToken', $params, $this->auth);
            if (!$result['error']) {
                $_SESSION['acessTokenPassPainel'] = $result['token'];
                $_SESSION['dtValidateToken'] = $dtAtualTimeStamp;
            }
        }
        
        return $result;
    }

    // Verifica se deve gerar um novo token, deve ser gerado um novo token a cada 24 horas.
    private function verifyGenerateAcessToken($dtAtualTimeStamp) {
         if (!empty($_SESSION['acessTokenPassPainel']) && !empty($_SESSION['dtValidateToken'])) {
            if (($_SESSION['dtValidateToken'] + (24 * 3600)) > $dtAtualTimeStamp) {
                return true;
            }
         }
         return false;
    }

    private function getPassPanelData($CDFILIAL, $CDLOJA, $CDSENHAPED, $NRSEQVENDA){

        $params = array(
            'subsidiaryCode' => $CDFILIAL,
            'storeCode'      => $CDLOJA,
            'pagerId'        => $CDSENHAPED,
            'saleCode'       => $NRSEQVENDA
        );
        $result = self::callAPIPainelSenha('getData', $params, $this->acess);
        return $result;
    }


    private function callAPIPainelSenha($method, $params, $URL){
        $postfields = json_encode($params);

        $headers[] = 'content-type: application/json';
        if ($method == 'getData') {
            $headers[] = "D-Authorization: $this->token";
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $URL);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 8);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 4);

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
        $curl_response = json_decode($curl_response, true);


        if ($err !== 0 || !is_array($curl_response)) {
            return array (
                'error' => true,
                'errmsg' => "Ocorreu um erro na chamada da API de Painel de Senhas." . $errmsg
            ); 
        } else if ($method == 'getToken') {
            if (array_key_exists('result_object', $curl_response)) {
                if (is_array($curl_response['result_object']) && array_key_exists('token', $curl_response['result_object'])) {
                    return array (
                        'error' => false,
                        'token' => $curl_response['result_object']['token']
                    );
                } else {
                    return array (
                        'error' => true,
                        'errmsg' => 'Ocorreu um erro no retorno do token de autenticação da API de Painel de Senhas.'
                    ); 
                }
            } else {
                return array (
                    'error' => true,
                    'errmsg' => 'Ocorreu um erro no retorno do token de autenticação da API de Painel de Senhas.'
                ); 
            }
            
        } else if ($method == 'getData') {
             if (array_key_exists('accessCode', $curl_response) && array_key_exists('url', $curl_response) && array_key_exists('qrCodeUrl', $curl_response)) {
                return array (
                    'error' => false,
                    'URL' => $curl_response['url'],
                    'ACCESS_CODE' => $curl_response['accessCode'],
                    'URL_QRCODE' => $curl_response['qrCodeUrl']
                );
            } else {
                return array (
                    'error' => true,
                    'errmsg' => 'Ocorreu um erro no retorno dos dados da nota fiscal da API de Painel de Senhas.'
                ); 
            }
        }

    }


}