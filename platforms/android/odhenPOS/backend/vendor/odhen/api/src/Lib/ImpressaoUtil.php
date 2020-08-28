<?php

namespace Odhen\API\Lib;

use Odhen\API\Remote\Printer\Command;
use Odhen\API\Util\Exception;

class ImpressaoUtil {

    protected $util;
    protected $instanceManager;
    protected $printer;
    protected $impressaoDelphi;
    protected $entityManager;

    public function __construct(
        \Odhen\API\Util\Util $util,
        \Zeedhi\Framework\DependencyInjection\InstanceManager $instanceManager,
        \Odhen\API\Remote\Printer\Printer $printer,
        \Odhen\API\Lib\ImpressaoDelphi $impressaoDelphi,
        \Doctrine\ORM\EntityManager $entityManager
    )  {

        $this->util = $util;
        $this->instanceManager = $instanceManager;
        $this->utilizaImpressaoPonte = $this->instanceManager->getParameter('UTILIZA_IMPRESSAO_PONTE');
        $this->utilizaImpressaoPHP = $this->instanceManager->getParameter('UTILIZA_IMPRESSAO_PHP');
        $this->ponteUrl = $this->instanceManager->getParameter('PONTE_URL');
        $this->printer = $printer;
        $this->impressaoDelphi = $impressaoDelphi;
        $this->entityManager = $entityManager;
    }

    public function buscaParametrosImpressora($IDMODEIMPRES) {
        /*
        1 - Bematech Mp-20
        2 - Epson Tm-T88IIP
        3 - Bematech Mp-2100
        4 - Bematech Mp-4000
        5 - Sewoo - Chile
        6 - IBM-4610 - Colombia
        7 - Bematech MP-100 - Peru
        8 - Daruma DR600
        9 - Epson TM-U22A
        10 - Daruma DR700
        11 - Epson M244A
        12 - Zebra Tlp 2844
        13 - Bematech MP-4200
        14 - Epson TM-T88V
        16 - Epson TM-T20
        17 - Sweda SI-300S
        18 - Elgin I9
        19 - Epson TM-T88IV Ethernet
        20 - Elgin Kiosk BKT681
        21 - Bematech MP-4200 - Compartilhada
        26 - Poynt
        23 - Custom TG2480H
        25 - GPOS700
        38 - Getnet
        39 - Pagseguro
        40 - Stone
        */

        switch ($IDMODEIMPRES) {
            case '1':
                // Bematech Mp-20
                $params = array(
                    'largura' => 36,
                    'larguraCupom' => 36,
                    'tipoLetra' => 3,
                    'comandoEnter' => chr(13) . chr(10),
                    'comandoCortePapel' => chr(27) . chr(119),
                    'impressaoFront' => false
                );
                break;
            case '2':
                // Epson Tm-T88IIP
                $params = array(
                    'largura' => 42 ,
                    'larguraCupom' => 42 ,
                    'tipoLetra' => 3,
                    'comandoEnter' => chr(10),
                    'comandoCortePapel' => chr(27) . chr(119),
                    'impressaoFront' => false
                );
                break;
            case '3':
                // Bematech Mp-2100
                $params = array(
                    'largura' => 48+17,
                    'larguraCupom' => 48+17,
                    'tipoLetra' => 3,
                    'comandoEnter' => chr(10),
                    'comandoCortePapel' => chr(27) . chr(119),
                    'impressaoFront' => false
                );
                break;
            case '4':
                // Bematech Mp-4000
                $params = array(
                    'largura' => 64,
                    'larguraCupom' => 64,
                    'tipoLetra' => 1,
                    'comandoEnter' => chr(10),
                    'comandoCortePapel' => chr(27) . chr(109),
                    'impressaoFront' => false
                );
                break;
            case '5':
                // Sewoo - Chile
                $params = array(
                    'largura' => 42,
                    'larguraCupom' => 42,
                    'tipoLetra' => 3,
                    'comandoEnter' => chr(10),
                    'comandoCortePapel' => chr(27) . chr(109),
                    'impressaoFront' => false
                );
                break;
            case '6':
                // IBM-4610 - Colombia
                $params = array(
                    'largura' => 42,
                    'larguraCupom' => 42,
                    'tipoLetra' => 3,
                    'comandoEnter' => chr(10),
                    'comandoCortePapel' => chr(27) . chr(109),
                    'impressaoFront' => false
                );
                break;
            case '7':
                // Bematech MP-100 - Peru
                $params = array(
                    'largura' => 40,
                    'larguraCupom' => 40,
                    'tipoLetra' => 3,
                    'comandoEnter' => chr(10),
                    'comandoCortePapel' => chr(27) . chr(119),
                    'impressaoFront' => false
                );
                break;
            case '8':
                // Daruma DR600
                $params = array(
                    'largura' => 48,
                    'larguraCupom' => 48,
                    'tipoLetra' => 3,
                    'comandoEnter' => chr(13) . chr(10),
                    'comandoCortePapel' => chr(27) . chr(109),
                    'impressaoFront' => false
                );
                break;
            case '9':
                // Epson TM-U22A
                $params = array(
                    'largura' => 40,
                    'larguraCupom' => 40,
                    'tipoLetra' => 3,
                    'comandoEnter' => chr(10),
                    'comandoCortePapel' => chr(27) . chr(119),
                    'impressaoFront' => false
                );
                break;
            case '10':
                // Daruma DR700
                $params = array(
                    'largura' => 48,
                    'larguraCupom' => 48,
                    'tipoLetra' => 2,
                    'comandoEnter' => chr(13) . chr(10),
                    'comandoCortePapel' => chr(27) . chr(109),
                    'impressaoFront' => false
                );
                break;
            case '11':
                // Epson M244A
                $params = array(
                    'largura' => 40,
                    'larguraCupom' => 40,
                    'tipoLetra' => 3,
                    'comandoEnter' => chr(10),
                    'comandoCortePapel' => chr(27) . chr(109),
                    'impressaoFront' => false
                );
                break;
            case '12':
                // Zebra Tlp 2844
                $params = array(
                    'largura' => 48,
                    'larguraCupom' => 48,
                    'tipoLetra' => 3,
                    'comandoEnter' => chr(10),
                    'comandoCortePapel' => chr(27) . chr(119),
                    'impressaoFront' => false
                );
                break;
            case '13':
                // Bematech MP-4200
                $params = array(
                    'largura' => 50,
                    'larguraCupom' => 64,
                    'tipoLetra' => 2,
                    'comandoEnter' => chr(10),
                    'comandoCortePapel' => chr(27) . chr(109),
                    'impressaoFront' => false
                );
                break;
            case '14':
                // Epson TM-T88V
                $params = array(
                    'largura' => 42,
                    'larguraCupom' => 56,
                    'tipoLetra' => 2,
                    'comandoEnter' => chr(10),
                    'comandoCortePapel' => chr(27) . chr(109),
                    'impressaoFront' => false
                );
                break;
            case '16':
                // Epson TM-T20
                $params = array(
                    'largura' => 48,
                    'larguraCupom' => 64,
                    'tipoLetra' => 2,
                    'comandoEnter' => chr(10),
                    'comandoCortePapel' => chr(27) . chr(109),
                    'impressaoFront' => false
                );
                break;
            case '17':
                // Sweda SI-300S
                $params = array(
                    'largura' => 42,
                    'larguraCupom' => 42,
                    'tipoLetra' => 2,
                    'comandoEnter' => chr(10),
                    'comandoCortePapel' => chr(27) . chr(109),
                    'impressaoFront' => false
                );
                break;
            case '18':
                // Elgin I9
                $params = array(
                    'largura' => 48,
                    'larguraCupom' => 48,
                    'tipoLetra' => 2,
                    'comandoEnter' => chr(10),
                    'comandoCortePapel' => chr(27) . chr(109),
                    'impressaoFront' => false
                );
                break;
            case '19':
                // Epson TM-T88IV Ethernet
                $params = array(
                    'largura' => 42,
                    'larguraCupom' => 56,
                    'tipoLetra' => 2,
                    'comandoEnter' => chr(10),
                    'comandoCortePapel' => chr(27) . chr(109),
                    'impressaoFront' => false
                );
                break;
            case '20':
                // 20 - Elgin Kiosk BKT681
                $params = array(
                    'largura' => 59,
                    'larguraCupom' => 59,
                    'tipoLetra' => 2,
                    'comandoEnter' => chr(13) . chr(10),
                    'comandoCortePapel' => null,
                    'impressaoFront' => false
                );
                break;
            case '21':
                // Bematech MP-4200 - Compartilhada
                $params = array(
                    'largura' => 42,
                    'larguraCupom' => 64,
                    'tipoLetra' => 2,
                    'comandoEnter' => chr(10),
                    'comandoCortePapel' => chr(27) . chr(109),
                    'impressaoFront' => false
                );
                break;
            case '23':
                $params = array(
                    'largura' => 43,
                    'larguraCupom' => 43,
                    'tipoLetra' => 1,
                    'comandoEnter' => chr(13) . chr(10),
                    'comandoCortePapel' => chr(27) . chr(105),
                    'impressaoFront' => false
                );
                break;
            case '25':
                // 25 - GPOS700 - Gertec
                $params = array(
                    'largura' => 38,
                    'larguraCupom' => 38,
                    'tipoLetra' => 1,
                    'comandoEnter' => "\n",
                    'comandoCortePapel' => null,
                    'impressaoFront' => true
                );
                break;
            case '26':
                // 26 - Poynt
                $params = array(
                    'largura' => 32,
                    'larguraCupom' => 32,
                    'tipoLetra' => 1,
                    'comandoEnter' => '\n',
                    'comandoCortePapel' => null,
                    'impressaoFront' => true
                );
                break;
            case '27':
                // 27 - CieloLio v2
                $params = array(
                    'largura' => 37,
                    'larguraCupom' => 37,
                    'tipoLetra' => 1,
                    'comandoEnter' => chr(10),
                    'comandoCortePapel' => null,
                    'impressaoFront' => true
                );
                break;
            case '38':
                // 38 - Getnet
                $params = array(
                    'largura' => 37,
                    'larguraCupom' => 37,
                    'tipoLetra' => 1,
                    'comandoEnter' => chr(10),
                    'comandoCortePapel' => null,
                    'impressaoFront' => true
                );
                break;
            case '39':
                // 39 - PagSeguro
                $params = array(
                    'largura' => 37,
                    'larguraCupom' => 37,
                    'tipoLetra' => 1,
                    'comandoEnter' => chr(10),
                    'comandoCortePapel' => null,
                    'impressaoFront' => true
                );
                break;
            case '40':
                // 40 - Stone
                $params = array(
                    'largura' => 37,
                    'larguraCupom' => 37,
                    'tipoLetra' => 1,
                    'comandoEnter' => chr(10),
                    'comandoCortePapel' => null,
                    'impressaoFront' => true
                );
                break;    

            default:
                $params = array(
                    'largura' => 49,
                    'larguraCupom' => 49,
                    'tipoLetra' => 3,
                    'comandoEnter' => chr(13) . chr(10),
                    'comandoCortePapel' => chr(27) . chr(119),
                    'impressaoFront' => false
                );
                break;
        }
        return $params;
    }

    public function imprimeLinhaCommand($printerParams, $commands, $text = '-'){
        $line = $this->imprimeLinha($printerParams, $text);
        $commands->text($line, $printerParams);
    }
    public function imprimeLinha($printerParams, $text = '-') {
        //Função criada para não deixar imprimir duas linhas em sequencia
        $linha = self::replicate($text, $printerParams['largura']) . $printerParams['comandoEnter'];
        return $linha;
    }

    public function checaEnter(&$texto, $printerParams) {
        //Função criada para não deixar dois enters seguidos
        if(substr($texto, -(strlen($printerParams['comandoEnter']))) != $printerParams['comandoEnter']){
            $texto .= $printerParams['comandoEnter'];
        }
    }

    public function quebraLinha($texto, $printerParams, $cortaPalavra){
        return wordwrap($texto, $printerParams['largura'], $printerParams['comandoEnter'], $cortaPalavra);
    }

    public function preencheLinha($printerParams, $text, $caracter, $largura = null) {
        if (empty($largura)) {
            $largura = $printerParams['largura'];
        }
        $nrEspaços =  $largura-strlen($text);
        if ($nrEspaços > 0){
            $linha = $text.self::replicate($caracter, $nrEspaços) . $printerParams['comandoEnter'];
        }else{
            $linha = $text;
        }
        return $linha;
    }

    public function preenche($largura, $text, $caracter) {
        $nrEspaços =  $largura - strlen($text);
        if ($nrEspaços > 0){
            $linha = $text.self::replicate($caracter, $nrEspaços);
        }else{
            $linha = $text;
        }
        return $linha;
    }

    public function alinhaInicioFim($printerParams, $textoInicio, $textoFim) {
        $largura = $printerParams['largura'];

        $numEspacos = $largura - (strlen($textoInicio) + strlen($textoFim));
        $espaco = self::replicate(' ', $numEspacos);

        return $this->quebraLinha($textoInicio . $espaco . $textoFim, $printerParams, false);
    }

    public function replicate($char, $numero) {
        $result = '';
        for ($i = 0; $i < $numero; ++$i){
            $result .= $char;
        }
        return $result;
    }

    public function centraliza($printerParams, $string, $largura = null) {
        $result = '';
        $tamanhoString = strlen($string);

        if ($largura == null) {
            $largura = $printerParams['largura'];
        }

        if ($tamanhoString <= $largura){
            $espacos = floor($largura / 2);
            $len = floor($tamanhoString / 2);
            $result = self::replicate(' ', $espacos - $len) . $string;
        } else if ($tamanhoString > $largura){
            $arrString = explode(' ', $string);
            $linha = '';

            foreach ($arrString as $key => $splitedString) {
                $len = strlen($splitedString);
                if ($len === $largura) {
                    $result .= $splitedString;
                    if (isset($arrString[$key + 1])) {
                        $result .= $printerParams['comandoEnter'];
                    }
                } else if ($len < $largura) {
                    $linha .= $splitedString;
                    if (isset($arrString[$key + 1])) {
                        if ((strlen($linha) + 1 + strlen($arrString[$key + 1])) >= $largura){
                            $result .= self::replicate(' ', floor(($largura - strlen($linha))/2)) . $linha . $printerParams['comandoEnter'];
                            $linha = '';
                        } else {
                            $linha .= ' ';
                        }
                    } else {
                        $result .= self::replicate(' ', floor(($largura - strlen($linha))/2)) . $linha;
                    }
                } else {
                    // busca string da última linha
                    $centraliza = substr($splitedString, -($len % $largura));
                    // 'comandoEnter' nas linhas que não serão centralizadas
                    $result .= self::quebraLinha(explode($centraliza, $splitedString)[0], $printerParams, true) . $printerParams['comandoEnter'];

                    if (isset($arrString[$key + 1])) {
                        if ((strlen($centraliza) + 1 + strlen($arrString[$key + 1])) >= $largura){
                            $result .= self::replicate(' ', floor(($largura - strlen($centraliza))/2)) . $centraliza . $printerParams['comandoEnter'];
                        } else {
                            $linha = $centraliza . ' ';
                        }
                    } else {
                        $result .= self::replicate(' ', floor(($largura - strlen($centraliza))/2)) . $centraliza;
                    }
                }
            }
        }

        return $result;
    }

    public function formataMp20($IDMODEIMPRES, $string) {
        if ($IDMODEIMPRES == '1') {
            $printerParams = self::buscaParametrosImpressora($IDMODEIMPRES);
            //Quantidade de espaços a serem dados
            for ($i = 0; $i < 4; $i++) {
                $string .= $printerParams['comandoEnter'];
            }
        }
        return $string;
    }

    public function imprimeNaoFiscal($text, $dadosImpressora, $sequencialArquivo = false) {
        try {
            $printerProperties = $this->buscaParametrosImpressora($dadosImpressora['IDMODEIMPRES']);
            $text = self::formataMp20($dadosImpressora['IDMODEIMPRES'], $text);

            if ($this->utilizaImpressaoPonte) {
                $comandos = new Command();
                $printerProperties['letterType'] = $printerProperties['tipoLetra'];
                $comandos->text($text, $printerProperties);
                $comandos->cutPaper();
                $issaas = $this->util->isSaas();
                if($issaas){
                    $result = array(
                        'saas'      => true,
                        'impressora'=> $dadosImpressora,
                        'comandos'  => $comandos->getCommands(),
                        'error'     => false);
                }else{
                    $result = $this->requisicaoPonte($dadosImpressora, $comandos);
                    $result['saas'] = false;
                }
            } else {
                $folder = $this->buscaDiretorio();
                if ($this->util->createFolder($folder)) {
                    if ($this->deveFazerMapeamento($dadosImpressora['DSENDPORTA'], $dadosImpressora['DSIPIMPR'])) {
                        $this->fazMapeamento($dadosImpressora['DSENDPORTA'], $dadosImpressora['CDPORTAIMPR']);
                    }
                    if ($this->utilizaImpressaoPHP) {
                        // impressao via PHP usando portas do Windows
                        $result = $this->impressaoPhp($dadosImpressora, $printerProperties, $text);
                    } else {
                        // impressao via Delphi usando portas do Windows
                        $fileName = $this->defineNomeArquivoImpressao($dadosImpressora, $sequencialArquivo, $folder);
                        $this->impressaoDelphi->imprimeArquivo($fileName, $text);
                        $result = array('error' => false);
                    }
                } else {
                    $result = array(
                        'error' => true,
                        'message' => 'Não foi possível criar o diretório de impressão, verifique as permissões.'
                    );
                }
            }
        } catch(\Exception $e) {
            Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
            $this->util->logImpressao($e->getMessage());
            $result = array(
                'error' => true,
                'message' => 'Não foi possível imprimir. Verifique a conexão com a impressora.',
                'exceptionMessage' => $e->getMessage()
            );
        }
        return $result;
    }

    private function buscaDiretorio(){
        return $this->instanceManager->getParameter('SYSTEM_PATH') . "IMP/FILES";
    }

    private function defineNomeArquivoImpressao($dadosImpressora, $sequencialArquivo, $folder) {
        // cria arquivo de impressão
        $fileName = $folder . $dadosImpressora['NRSEQIMPRLOJA'] . $sequencialArquivo . '.txt';
        $contFile = 0;
        while (file_exists($fileName)) {
            $fileName = $folder . $dadosImpressora['NRSEQIMPRLOJA'] . $sequencialArquivo . $contFile . '.txt';
            $contFile = $contFile + 1;
        }
        return $fileName;
    }

    private function impressaoPhp($dadosImpressora, $printerProperties, $text) {
        $porta = !empty($dadosImpressora['DSIPIMPR']) ? $dadosImpressora['DSIPIMPR'] : $dadosImpressora['CDPORTAIMPR'];
        $text .= $printerProperties['comandoCortePapel'];
        $printerStream = fopen($porta, 'w');
        fwrite($printerStream, $text);
        fclose($printerStream);
        return array('error' => false);
    }

    private function configuracaoPonte($dadosImpressora, Command $comandos) {
        $porta = !empty($dadosImpressora['DSIPIMPR']) ? $dadosImpressora['DSIPIMPR'] : $dadosImpressora['CDPORTAIMPR'];
        $impressora = $this->printer;
        $impressao = $impressora->preparePrint($comandos);
        $propriedadesImpressora = array(
            'printerType' => $dadosImpressora['IDMODEIMPRES'],
            'kioskPort' => 0,
            'port' => $porta
        );
        $impressao->setPrinterProperties($propriedadesImpressora);
        $urlPonte = !empty($dadosImpressora['DSIPPONTE']) ?  $dadosImpressora['DSIPPONTE'] : $this->ponteUrl;
        $impressao->setUrl($urlPonte);
        return $impressao;
    }

    public function impressaoPedidos($dadosImpressora, Command $comandos, $nrPedido = '0') {
        try {
            $result = array('error'=> false);
            if ($this->utilizaImpressaoPonte) {
                $impressao = $this->configuracaoPonte($dadosImpressora, $comandos);
                $result = $impressao->sendPrint();
            }else{
                $text = '';
                $retorno = array();

                foreach ($comandos->getCommands() as $key => $comando) {
                    switch ($comando['name']) {
                        case 'text':
                            $text .= $comando['parameters']['text'];
                            $retorno['error'] = false;
                        break;
                        case 'cutPaper':
                            $retorno = $this->imprimeNaoFiscal($text, $dadosImpressora, $nrPedido.'P');
                            $text = '';
                        break;
                        default: $retorno['error'] = false;
                    }
                    if ($retorno['error']) {
                        $result = $retorno;
                    }
                }
            }
        } catch(\Exception $e) {
            Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
            $this->util->logImpressao($e->getMessage());
            $result = array(
                'error' => true,
                'message' => $dadosImpressora['NMIMPRLOJA'] . ': ' . $e->getMessage()
            );
        }
        return $result;
    }
    public function requisicaoPonte($dadosImpressora, Command $comandos) {
        try {
            $impressao = $this->configuracaoPonte($dadosImpressora, $comandos);
            $result = $impressao->sendPrint();
        } catch(\Exception $e) {
            Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
            $this->util->logImpressao($e->getMessage());
            $result = array(
                'error' => true,
                'message' => $dadosImpressora['NMIMPRLOJA'] . ': ' . $e->getMessage()
            );
        }
        return $result;
    }

    public function requisicaoPonteTest($dadosImpressora) {
        try {
            $impressao = $this->configuracaoPonte($dadosImpressora, new Command());
            $result = $impressao->testPrint();
        } catch(\Exception $e) {
            Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
            $this->util->logImpressao($e->getMessage());
            $result = array(
                'error' => true,
                'message' => $dadosImpressora['NMIMPRLOJA'] . ': ' . $e->getMessage()
            );
        }
        return $result;
    }

    private function fazMapeamento($DSENDPORTA, $CDPORTAIMPR) {
        if (!empty($DSENDPORTA)) {
            $comandoMapeamento = 'NET USE ' . $CDPORTAIMPR . ' \\\\' . $DSENDPORTA . ' /PERSISTENT:YES';
            $handle = new \COM('WScript.Shell');
            $handle->Run($comandoMapeamento, 0, false);
        }
    }

    private function deveFazerMapeamento($DSENDPORTA, $DSIPIMPR) {
        return !empty($DSENDPORTA) && empty($DSIPIMPR);
    }

    private function instanciaDllImpNF() {
        try {
            $com = new \COM('IMPNF.IMPNFClass');
            $result = array(
                'error' => false,
                'com' => $com
            );
        } catch(\Exception $e) {
            Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
            $this->util->logImpressao($e->getMessage());
            $result = array(
                'error' => true,
                'message' => 'Não foi possível instanciar a IMPNF.dll, certifique-se que ela está instalada e verifique os logs de impressão.'
            );
        }
        return $result;
    }

    public function iniciarPorta($IDMODEIMPRES, $porta) {
        try {
            $com = self::instanciaDllImpNF();
            if ($com['error'] == false) {
                $com = $com['com'];
                $result = $com->IniciaPorta($IDMODEIMPRES, $porta);
                if ($IDMODEIMPRES != 20 && $IDMODEIMPRES != 21) {
                    if ($result == 1) {
                        if ($IDMODEIMPRES == '13') {
                            // comando para modo ESC/BEMA e UTF8
                            $comando = chr(29) . chr(249) . chr(53) . '0';
                            $com->ComandoTX($IDMODEIMPRES, $comando, strlen($comando));
                            // comando para modo ESC/BEMA e UTF8
                            $comando = chr(29) . chr(249) . chr(55) . '8';
                            $com->ComandoTX($IDMODEIMPRES, $comando, strlen($comando));
                            // comandos para modo condensado
                            $comando = chr(29) . chr(51) . chr(18) . '0';
                            $com->ComandoTX($IDMODEIMPRES, $comando, strlen($comando));
                        }
                        $result = array(
                            'error' => false,
                            'portaUsbKiosk' => 0
                        );
                    } else if ($result == -1) {
                        $result = array(
                            'error' => true,
                            'message' => 'O modelo da impressora não é aceito.',
                            'portaUsbKiosk' => 0
                        );
                    } else {
                        $result = array(
                            'error' => true,
                            'message' => 'Não foi possível iniciar a porta da impressora não fiscal.',
                            'portaUsbKiosk' => 0
                        );
                    }
                } else {
                    if ($result != -1) {
                        $result = array(
                            'error' => false,
                            'portaUsbKiosk' => $result
                        );
                    } else {
                        $result = array(
                            'error' => true,
                            'message' => 'Não foi possível iniciar a porta da impressora do totem.'
                        );
                    }
                }
            } else {
                $result = $com;
            }
        } catch(\Exception $e) {
            Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
            $this->util->logImpressao($e->getMessage());
            $result = array(
                'error' => true,
                'message' => 'Não foi possível iniciar a porta da impressora e verifique os logs de impressão.'
            );
        }
        return $result;
    }

    public function imprimeTexto($texto, $IDMODEIMPRES, $portaUsbKiosk = 0, $italico = 0, $sublinhado = 0, $expandido = 0, $enfatizado = 0, $tipoLetra = null) {
        $com = self::instanciaDllImpNF();
        if ($com['error'] == false) {
            $com = $com['com'];
            if ($tipoLetra == null) {
                $printerParams = self::buscaParametrosImpressora($IDMODEIMPRES);
                $tipoLetra = $printerParams['tipoLetra'];
            }
            $result = $com->FormataTX($IDMODEIMPRES, $texto, $tipoLetra, $italico, $sublinhado, $expandido, $enfatizado, $portaUsbKiosk);
            if ($result == 1) {
                $result = array(
                    'error' => false
                );
            } else if ($result == -1) {
                $result = array(
                    'error' => true,
                    'message' => 'O modelo da impressora não é aceito.'
                );
            } else {
                $result = array(
                    'error' => true,
                    'message' => 'Não foi possível imprimir na impressora não fiscal.'
                );
            }
        } else {
            $result = $com;
        }
        return $result;
    }

    public function cortaPapel($IDMODEIMPRES, $portaUsbKiosk = 0) {
        $com = self::instanciaDllImpNF();
        if ($com['error'] == false) {
            $com = $com['com'];

            switch ($IDMODEIMPRES) {
                case 20:
                    $result = $com->CortaPapel($IDMODEIMPRES, $portaUsbKiosk);
                    break;
                default:
                    $printerParams = self::buscaParametrosImpressora($IDMODEIMPRES);
                    $result = $com->FormataTX($IDMODEIMPRES, $printerParams['comandoCortePapel'], 1, 0, 0, 0, 0);
                    if ($result == 1) {
                        $result = array(
                            'error' => false
                        );
                    } else if ($result == -1) {
                        $result = array(
                            'error' => true,
                            'message' => 'O modelo da impressora não é aceito.'
                        );
                    } else {
                        $result = array(
                            'error' => true,
                            'message' => 'Não foi possível cortar papel na impressora não fiscal.'
                        );
                    }
                    break;
            }
        } else {
            $result = $com;
        }
        return $result;
    }

    public function fechaPorta($IDMODEIMPRES, $portaUsbKiosk = 0) {
        $com = self::instanciaDllImpNF();
        if ($com['error'] == false) {
            $com = $com['com'];
            if ($IDMODEIMPRES != 18) {
                $result = $com->FechaPorta($IDMODEIMPRES, $portaUsbKiosk);
            } else {
                $result = array(
                    'error' => false
                );
            }
            if ($result == 1) {
                $result = array(
                    'error' => false
                );
            } else if ($result == -1) {
                $result = array(
                    'error' => true,
                    'message' => 'O modelo da impressora não é aceito.'
                );
            } else {
                $result = array(
                    'error' => true,
                    'message' => 'Não foi possível fechar a porta da impressora não fiscal.'
                );
            }
        } else {
            $result = $com;
        }
        return $result;
    }

    public function configuraCodigoBarras($IDMODEIMPRES, $altura, $largura, $posicao, $fonte, $margem) {
        $com = self::instanciaDllImpNF();
        if ($com['error'] == false) {
            $com = $com['com'];
            $result = $com->ConfiguraCodigoBarras($IDMODEIMPRES, $altura, $largura, $posicao, $fonte, $margem);
            if ($result == 1) {
                $result = array(
                    'error' => false
                );
            } else if ($result == -1) {
                $result = array(
                    'error' => true,
                    'message' => 'O modelo da impressora não é aceito.'
                );
            } else {
                $result = array(
                    'error' => true,
                    'message' => 'Não foi possível configurar o código de barras na impressora não fiscal.'
                );
            }
        } else {
            $result = $com;
        }
        return $result;
    }

    public function imprimeCodigoBarrasCODE128($IDMODEIMPRES, $texto, $portaUsbKiosk = 0) {
        $com = self::instanciaDllImpNF();
        if ($com['error'] == false) {
            $com = $com['com'];
            $result = $com->ImprimeCodigoBarrasCODE128($IDMODEIMPRES, $texto, $portaUsbKiosk);
            if ($result == 1) {
                $result = array(
                    'error' => false
                );
            } else if ($result == -1) {
                $result = array(
                    'error' => true,
                    'message' => 'O modelo da impressora não é aceito.'
                );
            } else {
                $result = array(
                    'error' => true,
                    'message' => 'Não foi possível imprimir o código de barras na impressora não fiscal.'
                );
            }
        } else {
            $result = $com;
        }
        return $result;
    }

    public function imprimeQrCode($IDMODEIMPRES, $texto, $portaUsbKiosk = 0) {
        $com = self::instanciaDllImpNF();
        if ($com['error'] == false) {
            $com = $com['com'];
            $result = $com->ImprimeCodigoQRCODE($IDMODEIMPRES, 1, 4, 0, 10, 1, $texto, $portaUsbKiosk);
            if ($result == 1) {
                $result = array(
                    'error' => false
                );
            } else if ($result == -1) {
                $result = array(
                    'error' => true,
                    'message' => 'O modelo da impressora não é aceito.'
                );
            } else {
                $result = array(
                    'error' => true,
                    'message' => 'Não foi possível imprimir o código de barras na impressora não fiscal.'
                );
            }
        } else {
            $result = $com;
        }
        return $result;
    }

    public function verificaStatusImpressora($IDMODEIMPRES) {
        $com = self::instanciaDllImpNF();
        if ($com['error'] == false) {
            $com = $com['com'];
            /*
            Retornos possíveis da função Le_Status:
            0: Erro de comunicação
            5: Impressora com pouco papel
            9: Tampa aberta
            24: Impressora "ONLINE"
            32: Impressora sem papel
            */
            $result = $com->Le_Status($IDMODEIMPRES);
        } else {
            $result = $com;
        }
        return $result;
    }

    public function formataNumero($numeroFloat, $numeroCasas = 2) {
        return str_replace('.', ',', number_format($numeroFloat, $numeroCasas));
    }

    public function getDadosImpressora($CDFILIAL, $CDCAIXA){
        $paramsToPrinterData = array(
            'CDFILIAL' => $CDFILIAL,
            'CDCAIXA' => $CDCAIXA
        );
        $dadosImpressora = $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_IMPRESSORA", $paramsToPrinterData);

        $result = array(
            'error' => false,
            'dadosImpressora' => $dadosImpressora
        );
        return $result;
    }

    public function printPersonalCreditVoucher($CDFILIAL, $CDCAIXA, $creditDetails, $TIPORECE, $TROCO){
        $result = array();
        $dadosImpressora = $this->getDadosImpressora($CDFILIAL, $CDCAIXA);
        if (!$dadosImpressora['error']){
            $dadosImpressora = $dadosImpressora['dadosImpressora'];
            $printerParams = $this->buscaParametrosImpressora($dadosImpressora['IDMODEIMPRES']);
            $printerParams['largura'] = $printerParams['larguraCupom'];

            $receipt = "";

            $filialDetails = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_FILIAL_DETAILS", array('CDFILIAL' => $CDFILIAL));

            if (!empty($filialDetails)){
                $receipt .= $this->centraliza($printerParams, strtoupper($filialDetails['NMRAZSOCFILI'])) . $printerParams['comandoEnter'];
                $receipt .= $this->centraliza($printerParams, strtoupper($filialDetails['DSENDEFILI'])) . $printerParams['comandoEnter'];
                $location = $filialDetails['NMBAIRFILI'] . ' - ' . $filialDetails['NMMUNICIPIO'] . ' - ' . $filialDetails['SGESTADO'];
                $receipt .= $this->centraliza($printerParams, strtoupper($location)) . $printerParams['comandoEnter'];
                $CNPJ = substr($filialDetails['NRINSJURFILI'], 0, 2) . '.' . substr($filialDetails['NRINSJURFILI'], 2, 3) . '.' . substr($filialDetails['NRINSJURFILI'], 5, 3) . '/' . substr($filialDetails['NRINSJURFILI'], 8, 4) . '-' .substr($filialDetails['NRINSJURFILI'], 12, 2);
                $receipt .= $this->centraliza($printerParams, 'CNPJ: ' . $CNPJ) . $printerParams['comandoEnter'];
                $receipt .= $this->centraliza($printerParams, 'IE: ' . $filialDetails['CDINSCESTA']) . $printerParams['comandoEnter'];
                $receipt .= $this->centraliza($printerParams, 'IM: ' . $filialDetails['CDINSCMUNI']) . $printerParams['comandoEnter'];
            }

            $receipt .= $this->imprimeLinha($printerParams);
            $receipt .= $this->centraliza($printerParams, 'COMPRA DE CREDITO') . $printerParams['comandoEnter'];
            $data = new \DateTime('NOW');
            $receipt .= $this->centraliza($printerParams, 'DATA: ' . $data->format('d/m/Y H:i:s')) . $printerParams['comandoEnter'];
            $receipt .= $this->imprimeLinha($printerParams);
            foreach ($creditDetails as $creditData) {
                $receipt .= 'Nr. Deposito: ' . $creditData['NRDEPOSICONS'] . $printerParams['comandoEnter'] . $printerParams['comandoEnter'];
                $receipt .= 'Consumidor..: ' . $creditData['NMCONSUMIDOR'] . $printerParams['comandoEnter'];
                $receipt .= 'Familia.....: ' . $creditData['NMFAMILISALD'] . $printerParams['comandoEnter'];

                foreach ($TIPORECE as $recebimento){
                    if($recebimento['CDFAMILISALD'] == $creditData['CDFAMILISALD']) {
                        if (sizeof($TIPORECE) > 1) $receipt .= $printerParams['comandoEnter'];
                        $receipt .= $recebimento['DSBUTTON'] . $printerParams['comandoEnter'];
                        $receipt .= 'Valor.......: R$ ' . $this->formataNumero(floatval($recebimento['VRMOVIVEND']), 2) . $printerParams['comandoEnter'];
                        if ($recebimento['IDTIPORECE'] == '4' && $TROCO > 0){
                            $receipt .= 'Troco.......: R$ ' . $this->formataNumero(floatval($TROCO), 2) . $printerParams['comandoEnter'];
                            if (sizeof($TIPORECE) > 1) $receipt .= $printerParams['comandoEnter'];
                        }
                    }
                }

                if (sizeof($TIPORECE) > 1){
                    $receipt .= 'TOTAL.......: R$ ' . $this->formataNumero(floatval($creditData['VRSALDCONFAM']), 2) . $printerParams['comandoEnter'];
                }
                $receipt .= 'Saldo Final : R$ ' . $this->formataNumero(floatval($creditData['VRSALDCONEXT']), 2) . $printerParams['comandoEnter'];

                $receipt .= $this->imprimeLinha($printerParams);
            }

            if (!$printerParams['impressaoFront']){
                $comandos = new Command();
                $comandos->text($receipt);
                $comandos->cutPaper();
                $comandos->text($receipt);
                $comandos->cutPaper();

                $respostaPonte = $this->requisicaoPonte($dadosImpressora, $comandos);
                if ($respostaPonte['error']){
                    $result['error'] = true;
                    $result['message'] = $respostaPonte['message'];
                }
                else {
                    $result['error'] = false;
                    $result['message'] = null;
                }
            }
            else {
                $result['error'] = false;
                $result['message'] = array('RECEIPT' => $receipt);
            }
        }
        else {
            $result['error'] = true;
            $result['message'] = "Caixa sem impressora cadastrada.";
        }

        return $result;
    }

    public function montaTextoCreditoPessoal($printerParams, $dadosConsumidor) {
        $text = '';
        $text .= $this->centraliza($printerParams, $printerParams['comandoEnter']);
        $text .= $this->centraliza($printerParams, $printerParams['comandoEnter']);
        $text .= $this->centraliza($printerParams, $printerParams['comandoEnter']);
        $text .= $this->centraliza($printerParams, $printerParams['comandoEnter']);
        $text .= $this->centraliza($printerParams, $printerParams['comandoEnter']);
        $text .= $this->centraliza($printerParams, $printerParams['comandoEnter']);
        $text .= $this->centraliza($printerParams, $printerParams['comandoEnter']);
        $text .= $this->centraliza($printerParams, $printerParams['comandoEnter']);
        $printerParams['largura'] = $printerParams['larguraCupom'];
        $text .= 'Cliente: ' . strtoupper($dadosConsumidor['CDCLIENTE']) . $printerParams['comandoEnter'];
        $text .= 'Consumidor: ' . $dadosConsumidor['CDCONSUMIDOR'] . $printerParams['comandoEnter'];
        $text .= 'Nome: ' . strtoupper($dadosConsumidor['NMCONSUMIDOR']) . $printerParams['comandoEnter'];
        $text .= 'Saldo Total: .......: R$ ' . $this->formataNumero(floatval($dadosConsumidor['VRSALDCONFAM']), 2) . $printerParams['comandoEnter'];
        $text .= $this->imprimeLinha($printerParams);
        $text .= $this->centraliza($printerParams, 'VENDA CREDITO PESSOAL') . $printerParams['comandoEnter'];
        $data = new \DateTime('NOW');
        $text .= $this->centraliza($printerParams, 'DATA: ' . $data->format('d/m/Y H:i:s')) . $printerParams['comandoEnter'];
        $text .= $this->centraliza($printerParams, $printerParams['comandoEnter']);
        $text .= $this->centraliza($printerParams, $printerParams['comandoEnter']);
        $text .= $this->centraliza($printerParams, $printerParams['comandoEnter']);
        $text .= $this->centraliza($printerParams, $printerParams['comandoEnter']);
        $text .= $this->centraliza($printerParams, $printerParams['comandoEnter']);
        $text .= $this->centraliza($printerParams, $printerParams['comandoEnter']);
        $text .= $this->centraliza($printerParams, $printerParams['comandoEnter']);
        $text .= $this->centraliza($printerParams, $printerParams['comandoEnter']);
        $text .= $this->centraliza($printerParams, $printerParams['comandoEnter']);
        $text .= $this->centraliza($printerParams, $printerParams['comandoEnter']);
        $text .= $this->centraliza($printerParams, $printerParams['comandoEnter']);
        return $text;
    }

    public function montaTextoDebitoConsumidor($dadosConsumidor, $CDFILIAL, $CDCAIXA) {
        $params = array(
            'CDCLIENTE' => $dadosConsumidor['CDCLIENTE'],
            'CDCONSUMIDOR' => $dadosConsumidor['CDCONSUMIDOR']
        );
        $dadosImpressora = $this->getDadosImpressora($CDFILIAL, $CDCAIXA);
        $dadosImpressora = $dadosImpressora['dadosImpressora'];
        $printerParams = $this->buscaParametrosImpressora($dadosImpressora['IDMODEIMPRES']);
        $printerParams['largura'] = $printerParams['larguraCupom'];
        $receipt = "";
        $filialDetails = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_FILIAL_DETAILS", array('CDFILIAL' => $CDFILIAL));
        $receipt .= $this->centraliza($printerParams, strtoupper($filialDetails['NMRAZSOCFILI'])) . $printerParams['comandoEnter'];
        $receipt .= $this->centraliza($printerParams, strtoupper($filialDetails['DSENDEFILI'])) . $printerParams['comandoEnter'];
        $location = $filialDetails['NMBAIRFILI'] . ' - ' . $filialDetails['NMMUNICIPIO'] . ' - ' . $filialDetails['SGESTADO'];
        $receipt .= $this->centraliza($printerParams, strtoupper($location)) . $printerParams['comandoEnter'];
        $CNPJ = substr($filialDetails['NRINSJURFILI'], 0, 2) . '.' . substr($filialDetails['NRINSJURFILI'], 2, 3) . '.' . substr($filialDetails['NRINSJURFILI'], 5, 3) . '/' . substr($filialDetails['NRINSJURFILI'], 8, 4) . '-' .substr($filialDetails['NRINSJURFILI'], 12, 2);
        $receipt .= $this->centraliza($printerParams, 'CNPJ: ' . $CNPJ) . $printerParams['comandoEnter'];
        $receipt .= $this->centraliza($printerParams, 'IE: ' . $filialDetails['CDINSCESTA']) . $printerParams['comandoEnter'];
        $receipt .= $this->centraliza($printerParams, 'IM: ' . $filialDetails['CDINSCMUNI']) . $printerParams['comandoEnter'];
        $receipt .= $this->imprimeLinha($printerParams);
        $receipt .= $this->centraliza($printerParams, 'VENDA - DEBITO CONSUMIDOR' . $printerParams['comandoEnter']);
        $data = new \DateTime('NOW');
        $receipt .= $this->centraliza($printerParams, 'DATA: ' . $data->format('d/m/Y H:i:s')) . $printerParams['comandoEnter'];
        $receipt .= $this->imprimeLinha($printerParams);
        $receipt .= 'CLIENTE: ' . strtoupper($dadosConsumidor['CDCLIENTE'] . ' ' . $dadosConsumidor['NMFANTCLIE'] ) . $printerParams['comandoEnter'];
        $receipt .= 'MATRÍCULA: ' . $dadosConsumidor['CDCONSUMIDOR'] . $printerParams['comandoEnter'];
        $receipt .= 'NOME: ' . strtoupper($dadosConsumidor['NMCONSUMIDOR']) . $printerParams['comandoEnter'];
        $receipt .= $this->imprimeLinha($printerParams);
        $receipt .= 'SENHA PEDIDO: ' . strtoupper($dadosConsumidor['NRSEQVENDA']) . $printerParams['comandoEnter'];
        $saldoAtual  = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_CONSUMIDOR_SALDO_DEBITO", $params)['VRSALDOCONS'];
        $receipt .= 'SALDO ATUAL: ' . strtoupper($saldoAtual) . $printerParams['comandoEnter'];
        if (!$printerParams['impressaoFront']){
            $comandos = new Command();
            $comandos->text($receipt);
            $comandos->cutPaper();

            $respostaPonte = $this->requisicaoPonte($dadosImpressora, $comandos);
            if ($respostaPonte['error']){
                $result['error'] = true;
                $result['message'] = $respostaPonte['message'];
            }
            else {
                $result['error'] = false;
                $result['message'] = null;
            }
        }
        else {
            $result['error'] = false;
            $result['message'] = array('RECEIPT' => $receipt);
        }
        return $result;
    }




}
