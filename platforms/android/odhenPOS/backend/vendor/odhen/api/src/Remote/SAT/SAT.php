<?php
namespace Odhen\API\Remote\SAT;

use Zeedhi\Framework\Remote\cURLRequest;
use Odhen\API\Util\Exception;

class SAT {

    protected $curl;
    protected $util;
    private $satType = null;

    public function __construct(\Odhen\API\Util\Util $util) {
        $this->curl = new cURLRequest(null, curlRequest::METHOD_POST, array(
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 10
        ));
        $this->curl->setHeaders(array(
            cURLRequest::CONTENT_TYPE_HEADER => cURLRequest::CONTENT_TYPE_APPLICATION_JSON
        ));
        $this->util = $util;
    }

    const EVENT_CONSULTAR_SAT = '/consultSAT';
    const EVENT_CONSULTAR_STATUS_OPERACIONAL = '/consultOperationalStatus';
    const EVENT_ENVIAR_DADOS_VENDA = '/sendSaleData';
    const EVENT_CANCELAR_ULTIMA_VENDA = '/cancelLastSale';

    const STATUS_CONSULTA_SAT_ERROR = 'Erro';
    const STATUS_CONSULTA_SAT_OK = '08000';
    const STATUS_CONSULTA_STATUS_OK = '10000';
    const STATUS_VENDA_OK = '06000';
    const STATUS_ESTORNO_OK = '07000';

    public function setSatInfo($dadosSAT) {
        $this->satType = $dadosSAT['CDSAT'];
        $this->curl->setBaseUrl($dadosSAT['DSSATHOST']);
    }

    public function consultarSAT() {
        try {
            $numSessao = mt_rand(1, 999999);
            if (!empty($numSessao)) {
                $params = array(
                    'satInfo' => array(
                        'satType' => $this->satType
                    ),
                    'parameters' => array(
                        'sessionNumber' => $numSessao
                    )
                );

                $respostaSAT = $this->curl->request(self::EVENT_CONSULTAR_SAT, $params);
                $respostaSAT = json_decode($respostaSAT, true);
                $satResponse = $respostaSAT['data'];

                if (!$respostaSAT['error']) {
                    $respostaSAT = explode('|', $respostaSAT['data']);
                    $result = array(
                        'error' => true,
                        'message' => '',
                        'satResponse' => $satResponse
                    );
                    if (!empty($respostaSAT[0])){
                        if ($respostaSAT[0] !== self::STATUS_CONSULTA_SAT_ERROR){
                            if (!empty($respostaSAT[1])) {
                                if ($respostaSAT[1] === self::STATUS_CONSULTA_SAT_OK) {
                                    $result['error'] = false;
                                    $result['numeroSessao'] = $respostaSAT[0];
                                    $result['EEEEE'] = $respostaSAT[1];
                                    $result['message'] = $respostaSAT[2];
                                } else {
                                    $result['message'] = 'Erro ao comunicar com o SAT: ' . $respostaSAT[2];
                                }
                            } else {
                                $result['message'] = 'Erro ao comunicar com o SAT: ' . $respostaSAT[0];
                            }
                        } else {
                            $result['message'] = 'Erro ao comunicar com o SAT: ' . $respostaSAT[1];
                        }
                    } else{
                        $result['message'] = 'Erro ao comunicar com o SAT: SAT não encontrado.';
                    }
                } else {
                    $result = $respostaSAT;
                }
            } else {
                $result = array(
                    'error' => true,
                    'message' => 'Número da sessão SAT vazio.'
                );
            }
        } catch(\Exception $e) {
            Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
            $result = array(
                'error' => true,
                'message' => 'Erro ao consultar o SAT. Mensagem: ' . $e->getMessage()
            );
        }
        return $result;
    }

    public function consultarStatusOperacional($CDATIVASAT) {
        try {
            $numSessao = mt_rand(1, 999999);
            if (!empty($numSessao)) {
                if (!empty($CDATIVASAT)) {
                    $params = array(
                        'satInfo' => array(
                            'satType' => $this->satType
                        ),
                        'parameters' => array(
                            'sessionNumber' => $numSessao,
                            'activationCode' => $CDATIVASAT
                        )
                    );
                    $respostaSAT = $this->curl->request(self::EVENT_CONSULTAR_STATUS_OPERACIONAL, $params);
                    $respostaSAT = json_decode($respostaSAT, true);
                    $satResponse = $respostaSAT['data'];

                    if (!$respostaSAT['error']) {
                        $respostaSAT = explode('|', $respostaSAT['data']);
                        $result = array(
                            'error' => true,
                            'numeroSessao' => $respostaSAT[0],
                            'EEEEE' => $respostaSAT[1],
                            'message' => $respostaSAT[2],
                            'satResponse' => $satResponse
                        );
                        if ($result['EEEEE'] == self::STATUS_CONSULTA_STATUS_OK) {
                            // toDo: fazer validações corretamente
                            $result['cod'] = $respostaSAT[3];
                            $result['mensagemSEFAZ'] = $respostaSAT[4];
                            $result['ConteudoRetorno'] = $respostaSAT[5];
                            for ($i = 6; $i < sizeof($respostaSAT); $i++) {
                                $result['ConteudoRetorno'] = $result['ConteudoRetorno'] . '|' . $respostaSAT[$i];
                            }

                            $result['error'] = false;
                        } else {
                            $result['message'] = 'Erro ao comunicar com o SAT: ' . $result['message'];
                        }
                    } else {
                        $result = $respostaSAT;
                    }
                } else {
                    $result = array(
                        'error' => true,
                        'message' => 'Código do SAT vazio.'
                    );
                }
            } else {
                $result = array(
                    'error' => true,
                    'message' => 'Número da sessão SAT vazio.'
                );
            }
        } catch(\Exception $e) {
            Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
            $result = array(
                'error' => true,
                'message' => 'Erro ao consultar o status operacional do SAT. Mensagem: ' . $e->getMessage()
            );
        }
        return $result;
    }

    public function enviarDadosVenda($CDATIVASAT, $xml) {
        try {
            $numSessao = mt_rand(1, 999999);
            if (!empty($numSessao)) {
                if (!empty($CDATIVASAT)) {
                    if (!empty($xml)) {
                        $params = array(
                            'satInfo' => array(
                                'satType' => $this->satType
                            ),
                            'parameters' => array(
                                'sessionNumber' => $numSessao,
                                'activationCode' => $CDATIVASAT,
                                'saleData' => $xml
                            )
                        );
                        $respostaSAT = $this->curl->request(self::EVENT_ENVIAR_DADOS_VENDA, $params);
                        $respostaSAT = json_decode($respostaSAT, true);
                        $satResponse = $respostaSAT['data'];

                        if (!$respostaSAT['error']) {
                            $respostaSAT = explode('|', $respostaSAT['data']);
                            $result = array(
                                'error' => true,
                                'numeroSessao' => $respostaSAT[0],
                                'EEEEE' => $respostaSAT[1],
                                'CCCC' => $respostaSAT[2],
                                'message' => $respostaSAT[3],
                                'satResponse' => $satResponse
                            );
                            if ($result['EEEEE'] == self::STATUS_VENDA_OK) {
                                $result['cod'] = $respostaSAT[4];
                                $result['mensagemSEFAZ'] = $respostaSAT[5];
                                $result['arquivoCFeBase64'] = $respostaSAT[6];
                                $result['timeStamp'] = $respostaSAT[7];
                                $result['NRACESSONFCE'] = $respostaSAT[8];
                                $result['valorTotalCFe'] = $respostaSAT[9];
                                $result['CPFCNPJValue'] = $respostaSAT[10];
                                $result['DSQRCODENFCE'] = $respostaSAT[11];

                                $result['error'] = false;
                            } else {
                                $result['message'] = 'Erro ao comunicar com o SAT: ' . $result['message'];
                            }
                        } else {
                            $result = $respostaSAT;
                        }
                    } else {
                        $result = array(
                            'error' => true,
                            'message' => 'O documento XML enviado para o SAT está vazio.'
                        );
                    }
                } else {
                    $result = array(
                        'error' => true,
                        'message' => 'Código do SAT vazio.'
                    );
                }
            } else {
                $result = array(
                    'error' => true,
                    'message' => 'Número da sessão SAT vazio.'
                );
            }
        } catch(\Exception $e) {
            Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
            $result = array(
                'error' => true,
                'message' => 'Erro ao enviar os dados da venda para o SAT. Mensagem: ' . $e->getMessage()
            );
        }
        return $result;
    }

    public function cancelarVendaSAT($CDATIVASAT, $chaveCFe, $xml) {
        try {
            $numSessao = mt_rand(1, 999999);
            if (!empty($numSessao)) {
                if (!empty($CDATIVASAT)) {
                    if (!empty($xml)) {
                        $params = array(
                            'satInfo' => array(
                                'satType' => $this->satType
                            ),
                            'parameters' => array(
                                'sessionNumber' => $numSessao,
                                'activationCode' => $CDATIVASAT,
                                'cfeKey' => $chaveCFe,
                                'cancellationData' => $xml
                            )
                        );
                        $respostaSAT = $this->curl->request(self::EVENT_CANCELAR_ULTIMA_VENDA, $params);
                        $respostaSAT = json_decode($respostaSAT, true);
                        $satResponse = $respostaSAT['data'];

                        if (!$respostaSAT['error']) {
                            $respostaSAT = explode('|', $respostaSAT['data']);
                            $result = array(
                                'error' => true,
                                'numeroSessao' => $respostaSAT[0],
                                'EEEEE' => $respostaSAT[1],
                                'CCCC' => $respostaSAT[2],
                                'message' => $respostaSAT[3],
                                'satResponse' => $satResponse
                            );
                            if ($result['EEEEE'] == self::STATUS_ESTORNO_OK) {
                                $result['cod'] = $respostaSAT[4];
                                $result['mensagemSEFAZ'] = $respostaSAT[5];
                                $result['arquivoCFeBase64'] = $respostaSAT[6];
                                $result['timeStamp'] = $respostaSAT[7];
                                $result['NRACESSONFCE'] = $respostaSAT[8];
                                $result['valorTotalCFe'] = $respostaSAT[9];
                                $result['CPFCNPJValue'] = $respostaSAT[10];
                                $result['DSQRCODENFCE'] = $respostaSAT[11];

                                $result['error'] = false;
                            } else {
                                $result['message'] = 'Erro ao comunicar com o SAT: ' . $result['message'];
                            }
                        } else {
                            $result = $respostaSAT;
                        }
                    } else {
                        $result = array(
                            'error' => true,
                            'message' => 'O documento XML enviado para o SAT está vazio.'
                        );
                    }
                } else {
                    $result = array(
                        'error' => true,
                        'message' => 'Código do SAT vazio.'
                    );
                }
            } else {
                $result = array(
                    'error' => true,
                    'message' => 'Número da sessão SAT vazio.'
                );
            }
        } catch(\Exception $e) {
            Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
            $result = array(
                'error' => true,
                'message' => 'Erro ao enviar os dados da venda para o SAT. Mensagem: ' . $e->getMessage()
            );
        }
        return $result;
    }

}