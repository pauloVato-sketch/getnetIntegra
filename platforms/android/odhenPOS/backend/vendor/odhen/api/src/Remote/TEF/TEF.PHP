<?php

namespace Odhen\API\Remote\TEF;

use Zeedhi\Framework\Remote\cURLRequest;
use Odhen\API\Remote\TEF\Transaction;
use Odhen\API\Util\Exception;

class TEF
{
  protected $curl;
  protected $util;
  protected $entityManager;
  const CREDIT = '1';
  const DEBIT = '2';
  public function __construct(cURLRequest $curl, \Odhen\API\Util\Util $util, \Doctrine\ORM\EntityManager $entityManager, \Zeedhi\Framework\DependencyInjection\InstanceManager $instanceManager)
  {
    $this->curl = $curl;
    $this->entityManager = $entityManager;
    $this->util = $util;
    $this->curl->setHeaders(array(
      cURLRequest::CONTENT_TYPE_HEADER => cURLRequest::CONTENT_TYPE_APPLICATION_JSON
    ));
    $this->systemPath = $instanceManager->getParameter('SYSTEM_PATH');
  }
  const EVENT_CONFIGURA_TEF = '/configure';
  const EVENT_INICIA_TRANSACAO = '/start-transaction';
  const EVENT_CONTINUA_TRANSACAO = '/continue-transaction';
  const EVENT_FINALIZA_TRANSACAO = '/finish-transaction';
  const SALVA_LOGS = true;

  public function configuraTransacaoTef($CDFILIAL, $CDCAIXA, $NRORG = '1')
  {
    try {
      $params = array(
        'CDFILIAL' => $CDFILIAL,
        'CDCAIXA' => $CDCAIXA,
        'NRORG' => $NRORG
      );
      $dadosTef = $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_TEF", $params);
      $transaction = new Transaction();
      $transaction->setEndSitef($dadosTef['DSENDIPSITEF']);
      $transaction->setStoreId($dadosTef['CDLOJATEF']);
      $transaction->setTerminalId($dadosTef['CDTERTEF']);
      $paramsTEF = array(
        'endSitef' => $transaction->getEndSitef(),
        'storeId' => $transaction->getStoreId(),
        'terminalId' => $transaction->getTerminalId()
      );
      $this->setClientIpCurl();
      $respostaTEF = $this->curl->request(self::EVENT_CONFIGURA_TEF, $paramsTEF);
      $respostaTEF = json_decode($respostaTEF, true);
      if (!$respostaTEF['error']) {
        return $this->trataRetornoConfiguracao($respostaTEF['result']);
      } else {
        return $respostaTEF;
      }
    } catch (\Exception $e) {
      Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
      return array(
        'error' => true,
        'message' => 'Mensagem: ' . $e->getMessage()
      );
    }
  }

  public function iniciaCancelamentoTef($typeCancellation, $totalSale, $CDOPERADOR)
  {
    try {
      $funcao = $typeCancellation === self::CREDIT ? 210 : 211;
      $data = date('Ymd');
      $horario = date('His');
      $paramsInicia = array(
        'startFunction' => $funcao,
        'value' => $totalSale,
        'text' => '',
        'date' => $data,
        'time' => $horario,
        'operator' => $CDOPERADOR,
        'additionalParams' => null
      );
      $this->setClientIpCurl();
      $retornoInicia = $this->curl->request(self::EVENT_INICIA_TRANSACAO, $paramsInicia);
      $retornoInicia = json_decode($retornoInicia, true);
      $retornoInicia = json_decode($retornoInicia['result'], true);
      if ($retornoInicia['retornoDll'] != 10000) {
        return array(
          'error' => true,
          'message' => 'Falha ao iniciar transação. Código de retorno: ' . $retornoInicia['retornoDll']
        );
      } else {
        return array(
          'error' => false,
          'retornoInicia' => $retornoInicia
        );
      }
    } catch (\Exception $e) {
      Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
      return array(
        'error' => true,
        'message' => 'Mensagem: ' . $e->getMessage()
      );
    }
  }

  public function iniciaTransacaoTef($IDTIPORECE, $totalSale, $CDOPERADOR)
  {
    try {
      $funcao = $this->trataRecebimentoDll($IDTIPORECE);
      $valor = $totalSale;
      $cupomFiscal = '';
      $data = date('Ymd');
      $horario = date('His');
      $operador = $CDOPERADOR;
      if ($funcao == 2) {
        //Restrições do TAA para TEF - Cartão DEBITO
        $paramsAdicionais = '[10;17;18;19;24;27;28;29;31;32;33;40]'; //16;
      } elseif ($funcao == 3) {
        //Restrições do TAA para TEF - Cartão CREDITO
        $paramsAdicionais = '[10;24;27;28;29;31;32;33;36;40;44]';
      }
      $paramsInicia = array(
        'startFunction' => $funcao,
        'value' => $valor,
        'text' => $cupomFiscal,
        'date' => $data,
        'time' => $horario,
        'operator' => $operador,
        'additionalParams' => $paramsAdicionais
      );
      $this->setClientIpCurl();
      $retornoInicia = $this->curl->request(self::EVENT_INICIA_TRANSACAO, $paramsInicia);
      $retornoInicia = json_decode($retornoInicia, true);
      $retornoInicia = json_decode($retornoInicia['result'], true);
      if ($retornoInicia['retornoDll'] != 10000) {
        return array(
          'error' => true,
          'message' => 'Falha ao iniciar transação. Código de retorno: ' . $retornoInicia['retornoDll']
        );
      } else {
        return array(
          'error' => false,
          'retornoInicia' => $retornoInicia
        );
      }
    } catch (\Exception $e) {
      Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
      return array(
        'error' => true,
        'message' => 'Mensagem: ' . $e->getMessage()
      );
    }
  }

  private function trataRecebimentoDll($IDTIPORECE)
  {
    if ($IDTIPORECE == '1') {
      return 3;
    } else {
      return 2;
    }
  }

  private function trataRetornoConfiguracao($retornoConfigura)
  {
    switch ($retornoConfigura) {
      case 0:
        $retorno = array(
          'error' => false,
          'message' => 'Não ocorreu erro'
        );
        break;
      case 1:
        $retorno = array(
          'error' => true,
          'message' => 'Endereço IP inválido ou não resolvido.'
        );
        break;
      case 2:
        $retorno = array(
          'error' => true,
          'message' => 'Código da loja inválido.'
        );
        break;
      case 3:
        $retorno = array(
          'error' => true,
          'message' => 'Código de terminal invalido.'
        );
        break;
      case 6:
        $retorno = array(
          'error' => true,
          'message' => 'Erro na inicialização do Tcp/Ip.'
        );
        break;
      case 7:
        $retorno = array(
          'error' => true,
          'message' => 'Falta de memória.'
        );
        break;
      case 8:
        $retorno = array(
          'error' => true,
          'message' => 'Não encontrou a CliSiTef ou ela está com problemas.'
        );
        break;
      case 10:
        $retorno = array(
          'error' => true,
          'message' => 'Erro de acesso na pasta CliSiTef (Possível falta de permissão para escrita).'
        );
        break;
      default:
        $retorno = array(
          'error' => true,
          'message' => 'Falha ao configurar o Cliente SiTef.'
        );
        break;
    }
    return $retorno;
  }

  public function continuaTransacaoTef($valorTotal, $IDTIPORECE, $CDTIPORECE, $DATECANCEL, $DOCNUMBER, $CDFILIAL, $CDCAIXA)
  {
    try {
      $errorMessage = '';
      $comando = 0;
      $tipoCampo = 0;
      $tamMinimo = 0;
      $tamMaximo = 0;
      $bufferParam = '';
      $tamBuffer = 300;
      $continua = 0;
      do {
        $paramsContinua = array(
          'command'           => $comando,
          'fieldType'         => $tipoCampo,
          'minSize'           => $tamMinimo,
          'maxSize'           => $tamMaximo,
          'bufferParam'       => $bufferParam,
          'sizeBuffer'        => $tamBuffer,
          'continueCommand'   => $continua
        );
        $this->setClientIpCurl();
        $retornoContinua = $this->curl->request(self::EVENT_CONTINUA_TRANSACAO, $paramsContinua);
        $retornoContinua = json_decode($retornoContinua, true);
        $bufferMessage = mb_convert_encoding(json_decode($retornoContinua['result'], true)['bufferConverted'], 'UTF-8');
        $bufferMessage = trim($bufferMessage);
        $retornoContinua = json_decode($retornoContinua['result'], true);
        $retornoContinua['bufferConverted'] = $bufferMessage;
        if ($retornoContinua['retornoDll'] == 10000) {
          switch ($retornoContinua['comando']) {
            case 0:
              switch ($retornoContinua['tipoCampo']) {
                case 0:
                  $retornoContinua['bufferConverted'] = '';
                  break;
                case 15:
                  $retornoContinua['bufferConverted'] = '';
                  break;
                case 43:
                  $retornoContinua['bufferConverted'] = '';
                  break;
                case 121:
                  self::salvaDadoTemporario($CDFILIAL . $CDCAIXA . 'comprovanteTef', $retornoContinua['bufferConverted']);
                  $retornoContinua['bufferConverted'] = '';
                  break;
                case 122:
                  $retornoContinua['bufferConverted'] = '';
                  break;
                case 123;
                  $retornoContinua['bufferConverted'] = '';
                  break;
                case 132:
                  self::salvaDadoTemporario($CDFILIAL . $CDCAIXA . 'CDBANCARTCR', $retornoContinua['bufferConverted']);
                  break;
                case 133:
                  self::salvaDadoTemporario($CDFILIAL . $CDCAIXA . 'CDNSUHOSTTEF', $retornoContinua['bufferConverted']);
                  break;
                case 1:
                case 2:
                case 504:
                  $retornoContinua['bufferConverted'] = '0';
                  break;
                case 953: 
                  $retornoContinua["bufferConverted"] = '';
                  break;
                default:
                  break;
              }
              break;
            case 1:
              $retornoContinua['bufferConverted'] = '';
              break;
            case 2:
              $retornoContinua['bufferConverted'] = '';
              break;
            case 3:
              $message = mb_convert_encoding($retornoContinua['bufferConverted'], 'UTF-8', 'UTF-8');
              $message = $errorMessage . '! ' . trim($message);
              $retornoContinua['bufferConverted'] = '';
              return array(
                'error' => false,
                'finishedTransaction' => false,
                'message' => $message
              );
              break;
            case 13:
              //LimpaMensagem;
              $retornoContinua['bufferConverted'] = '';
              break;
            case 15:
              //Cabeçalho a ser apresentado pela aplicação?
              $retornoContinua['bufferConverted'] = '';
              break;
            case 20:
              $confirma = '0';
              $retornoContinua['bufferConverted'] = $confirma;
              break;
            case 21:
              $confirma = '1';
              $retornoContinua['bufferConverted'] = $confirma;
              break;
            case 22:
              $message = mb_convert_encoding($retornoContinua['bufferConverted'], 'UTF-8', 'UTF-8');
              $message = trim($message);
              $retornoContinua['bufferConverted'] = '';
              $errorMessage = $message;
              break;
            case 23:
              $retornoContinua['bufferConverted'] = '';
              break;
            case 30:
              switch ($retornoContinua['tipoCampo']) {
                case 515:
                  $retornoContinua['bufferConverted'] = $DATECANCEL;
                  break;

                case 516:
                  $retornoContinua['bufferConverted'] = (int) $DOCNUMBER;
                  break;
              }
              break;
            case 34:
              switch ($retornoContinua['tipoCampo']) {
                case 146:
                  $retornoContinua['bufferConverted'] = $valorTotal;
                  break;
              }
              break;
            default:
              break;
          }
          $comando        = $retornoContinua["comando"];
          $tipoCampo      = $retornoContinua['tipoCampo'];
          $tamMinimo      = $retornoContinua["tamMinimo"];
          $tamMaximo      = $retornoContinua["tamMaximo"];
          $tamBuffer      = 300;
          $bufferParam    = $retornoContinua['bufferConverted'];
          $continua       = $retornoContinua["continua"];
        } elseif ($retornoContinua['retornoDll'] > 0) {
          // Encerra transação
          $paramsContinua = array(
            'command'           => 0,
            'fieldType'         => 0,
            'minSize'           => 0,
            'maxSize'           => 0,
            'bufferParam'       => '',
            'sizeBuffer'        => 0,
            'continueCommand'   => -1
          );
          $this->setClientIpCurl();
          $retornoContinua = $this->curl->request(self::EVENT_CONTINUA_TRANSACAO, $paramsContinua);
          return array(
            'error' => true,
            'message' => $errorMessage
          );
        }
      } while ($retornoContinua['retornoDll'] == 10000);

      if ($retornoContinua['retornoDll'] == 0) {
        $dadosTransacao = self::carregaDadosTransacao($CDFILIAL, $CDCAIXA);
        $CDTIPORECE = self::trataTipoRece($CDTIPORECE, $dadosTransacao['CDBANCARTCR'], $IDTIPORECE);

        // Salva log final da transação
        if (self::SALVA_LOGS) {
          self::salvaEtapaTransacao('FINALIZANDO TRANSACAO', '');
          self::salvaEtapaTransacao('CDNSUHOSTTEF', $dadosTransacao['CDNSUHOSTTEF']);
          self::salvaEtapaTransacao('comprovanteTef', $dadosTransacao['comprovanteTef']);
          self::salvaEtapaTransacao('CDBANCARTCR', $dadosTransacao['CDBANCARTCR']);
        }

        return array(
          'error' => false,
          'CDNSUHOSTTEF' => $dadosTransacao['CDNSUHOSTTEF'],
          'COMPROVANTETEF' => $dadosTransacao['comprovanteTef'],
          'CDTIPORECE' => $CDTIPORECE,
          'finishedTransaction' => true
        );
      } else {
        switch ($retornoContinua['retornoDll']) {
          case -2:
            return array(
              'error' => true,
              'canceled' => true,
              'message' => 'Cancelado pelo operador.',
              'finishedTransaction' => true
            );
          case -5:
            return array(
              'error' => true,
              'message' => 'Sem comunicação com o SiTef.',
              'finishedTransaction' => true
            );
            break;
          case -15:
            return array(
              'error' => true,
              'message' => 'Operação cancelada pela automação comercial.',
              'finishedTransaction' => true
            );
            break;
            case -40:
            return array(
              'error' => true,
              'message' => 'Transação negada pelo SiTef.',
              'finishedTransaction' => true
            );
            break;
          case -43:
            return array(
              'error' => true,
              'message' => 'Falha no leitor de cartões.',
              'finishedTransaction' => true
            );
            break;
          default:
            return array(
              'error' => true,
              'message' => 'Erro desconhecido. Erro: ' . $retornoContinua['retornoDll'] . '.',
              'finishedTransaction' => true
            );
            break;
        }
      }
    } catch (\Exception $e) {
      Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
      return array(
        'error' => true,
        'message' => 'Mensagem: ' . $e->getMessage(),
        'finishedTransaction' => true
      );
    }
  }

  public function finalizaTransacao($confirma)
  {
    try {
      $paramsContinua = array(
        'confirm' => $confirma,
        'text' => '',
        'date' => date('Ymd'),
        'time' => date('His')
      );
      $this->setClientIpCurl();
      $this->curl->request(self::EVENT_FINALIZA_TRANSACAO, $paramsContinua);
      return array(
        'error' => false,
        'message' => 'Transação realizada com sucesso.',
        'finishedTransaction' => true
      );
    } catch (\Exception $e) {
      return array(
        'error' => false,
        'message' => 'Transação realizada com sucesso.',
        'finishedTransaction' => true
      );
    }
  }

  private function salvaDadoTemporario($field, $value)
  {
    $this->util->createFolder($this->systemPath . 'TEF/TEMP');
    $fileName = 'temp_' . $field . '.txt';
    $impFile = fopen($this->systemPath . 'TEF/TEMP/' . $fileName, 'w');
    fwrite($impFile, $value);
    fclose($impFile);
  }

  private function carregaDadosTransacao($CDFILIAL, $CDCAIXA)
  {
    $tempDir = $this->systemPath . 'TEF/TEMP/temp_';
    $CDBANCARTCRFILENAME = $tempDir . $CDFILIAL .  $CDCAIXA .'CDBANCARTCR.txt';
    $CDBANCARTCRFile = fopen($CDBANCARTCRFILENAME, 'r');
    $CDBANCARTCR = fread($CDBANCARTCRFile, filesize($CDBANCARTCRFILENAME));

    $CDNSUHOSTTEFFILENAME = $tempDir . $CDFILIAL .  $CDCAIXA .'CDNSUHOSTTEF.txt';
    $CDNSUHOSTTEFFile = fopen($CDNSUHOSTTEFFILENAME, 'r');
    $CDNSUHOSTTEF = fread($CDNSUHOSTTEFFile, filesize($CDNSUHOSTTEFFILENAME));
    
    $COMPROVANTETEFFILENAME = $tempDir . $CDFILIAL .  $CDCAIXA .'comprovanteTef.txt';
    $comprovanteTefFile = fopen($COMPROVANTETEFFILENAME, 'r');
    $comprovanteTef = fread($comprovanteTefFile, filesize($COMPROVANTETEFFILENAME));

    return array(
      'CDBANCARTCR' => $CDBANCARTCR,
      'CDNSUHOSTTEF' => $CDNSUHOSTTEF,
      'comprovanteTef' => $comprovanteTef,
    );
  }

  private function trataTipoRece($CDTIPORECE, $CDBANCARTCR, $IDTIPORECE)
  {
    $params = array(
      'CDBANCARTCR' => $CDBANCARTCR,
      'IDTIPORECE' => $IDTIPORECE
    );
    $tipoRece = $this->entityManager->getConnection()->fetchAssoc("BUSCA_TIPO_RECE", $params);
    if (!empty($tipoRece)) {
      $CDTIPORECE_utilizado = $tipoRece['CDTIPORECE'];
    } else {
      $CDTIPORECE_utilizado = $CDTIPORECE;
    }
    return $CDTIPORECE_utilizado;
  }

  private function salvaEtapaTransacao($field, $value)
  {
    $this->util->createFolder($this->systemPath . 'TEF/LOGS');
    $fileName = 'saveStep_transaction' . date('d-m-Y_H') . '.txt';

    $impFile = fopen($this->systemPath . 'TEF/LOGS/' . $fileName, 'a');

    $currentDateTime = date('d-m-Y_H-i-s');
    $transactionData = $currentDateTime . ' - ' . $field . ': ' . $value . "\r\n";

    fwrite($impFile, $transactionData);

    fclose($impFile);
  }

  public function validaParametrosTef($CDFILIAL, $CDCAIXA)
  {
    $retorno = array();

    $params = array(
      'CDFILIAL' => $CDFILIAL,
      'CDCAIXA' => $CDCAIXA
    );

    $dadosTef = $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_TEF", $params);
    if (empty($dadosTef['CDLOJATEF'])) {
      $retorno = array(
        'error' => true,
        'message' => 'Loja TEF não parametrizado para o caixa.'
      );
    } elseif (empty($dadosTef['CDTERTEF'])) {
      $retorno = array(
        'error' => true,
        'message' => 'Terminal TEF não parametrizado para o caixa.'
      );
    } elseif (isset($dadosTef['IDTPTEF']) && $dadosTef['IDTPTEF'] === '2') {
      $retorno = array(
        'error' => false
      );
    } elseif (empty($dadosTef['DSENDIPSITEF'])) {
      $retorno = array(
        'error' => true,
        'message' => 'Endereço IP do servidor TEF não parametrizado para o caixa.'
      );
    } else {
      $retornoConfigura = $this->configuraTransacaoTef($CDFILIAL, $CDCAIXA);
      if (!!$retornoConfigura['error']) {
        $retorno = $retornoConfigura;
      } else {
        $retorno = array(
          'error' => false,
          'tefConfigurationParams' => $dadosTef
        );
      }
    }
    return $retorno;
  }

  private function setClientIpCurl()
  {
    if (isset($_SERVER['HTTP_CLIENT_IP']))
      $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
      $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_X_FORWARDED']))
      $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
      $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_FORWARDED']))
      $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if (isset($_SERVER['REMOTE_ADDR']))
      $ipaddress = $_SERVER['REMOTE_ADDR'];

    $this->curl->setBaseUrl('http://' . $ipaddress . ':3000');
  }
}
