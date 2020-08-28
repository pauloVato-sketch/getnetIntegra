<?php

namespace Odhen\API\Service;

class ClientSitef {

    /********************************************************************/
    /********************* PARAMETROS DIRETORIOS TEF ********************/
    /********************************************************************/

    protected $requestPath;
    protected $requestFile;
    protected $responsePath;
    protected $responseFile;
    protected $processedPath;
    protected $canceledPath;

    /******************************* END *******************************/

    protected $entityManager;
    protected $impressaoUtil;
    protected $util;
    protected $systemPath;

    public function __construct(\Doctrine\ORM\EntityManager $entityManager, \Odhen\API\Lib\ImpressaoUtil $impressaoUtil, \Odhen\API\Util\Util $util, \Zeedhi\Framework\DependencyInjection\InstanceManager $instanceManager) {
        $this->entityManager = $entityManager;
        $this->impressaoUtil = $impressaoUtil;
        $this->util = $util;
        $this->systemPath = $instanceManager->getParameter('SYSTEM_PATH');

        $this->requestPath = $this->systemPath . 'TEF/Request';
        $this->requestFile = $this->systemPath . 'TEF/Request/IntPos.001';
        $this->responsePath = $this->systemPath . 'TEF/Response';
        $this->responseFile = $this->systemPath . 'TEF/Response/IntPos.001';
        $this->processedPath = $this->systemPath . 'TEF/Processed';
        $this->canceledPath = $this->systemPath . 'TEF/Canceled';
    }

    /********************* TRANSAÇÃO CLIENTE SITEF ********************/

    public function iniciaTransacaoClientSitef($CDTIPORECE, $IDTIPORECE, $NRSEQVENDA, $valorTotal) {
        $retorno = self::preparaDiretoriosProjeto();

        if ($retorno['error'] == false) {
            // String arquivo de comunicação SiTef
            $dadosArquivo = self::montaDadosArquivo($IDTIPORECE, $NRSEQVENDA, $valorTotal);

            // Cria arquivo de transacao para ser processado pelo cliente sitef
            $retorno = self::criaArquivoTransacao($dadosArquivo);

            if ($retorno['error'] == false) {
                // Valida dados de retorno do arquivo de transacao gerado pelo cliente sitef
                $retorno = self::trataArquivoReposta($CDTIPORECE);

                if ($retorno['error'] == false) {
                    if ($retorno['canceled'] == false) {
                        $dados = $retorno['dados'];
                        $dados['NRSEQVENDA'] = $NRSEQVENDA;
                    } else {
                        $dados = array('vazio');
                    }

                    // Move arquivo de resposta para pasta de arquivos processados com um novo contador
                    $retorno = self::armazenaArquivoResposta($NRSEQVENDA, $retorno['canceled']);
                    if ($retorno['error'] == false) {
                        return array(
                            'error'    => false,
                            'canceled' => $retorno['canceled'],
                            'dados'    => $dados,
                            'message'  => 'Comunicação TEF realizada com sucesso.'
                        );
                    }
                }
            }
        }

        return $retorno;
    }

    private function preparaDiretoriosProjeto() {
        $folders = array();
        array_push($folders, $this->requestPath);
        array_push($folders, $this->responsePath);
        array_push($folders, $this->processedPath);
        array_push($folders, $this->canceledPath);

        foreach ($folders as $folder) {
            if (!$this->util->createFolder($folder)) {
                return array(
                    'error' => true,
                    'message' => 'Erro ao criar diretórios da transação TEF. Verifique a permissão das pastas.'
                );
            }
        }

        return array(
            'error' => false
        );
    }

    private function montaDadosArquivo($IDTIPORECE, $NRSEQVENDA, $valorTotal) {
        $dadosArquivo = "";

        // Parametros principais para criação do arquivo de comunicação
        // 000-000 : HEADER
        $header = '000-000 = ' . 'CRT' . "\n";
        $dadosArquivo .= $header;

        // 001-000 : IDENTIFICAÇÃO
        $identificacao = '001-000 = ' . $NRSEQVENDA . "\n";
        $dadosArquivo .= $identificacao;

        // 002-000 : DOCUMENTO FISCAL VINCULADO
        $notaFiscal = '002-000 = ' . '0' . "\n";
        $dadosArquivo .= $notaFiscal;

        // 003-000 : VALOR TOTAL
        $valorTotal = '003-000 = ' . $valorTotal . "\n";
        $dadosArquivo .= $valorTotal;

        // 004-000 : MOEDA
        $moeda = '004-000 = ' . '0' . "\n";
        $dadosArquivo .= $moeda;

        // Handling request file params
        if ($IDTIPORECE == 'debit') {
            // 210-023 : FORMA DE PAGAMENTO
            // 04 = Débito
            $dadosArquivo .= '210-023 = 04' . "\n";

            // 210-016 : TIPO DE VENDA EFETUADA COM CARTÃO (debito)
            // 1 = a vista
            $dadosArquivo .= '210-016 = 1' . "\n";
        } elseif ($IDTIPORECE == 'credit') {
            // 05 = Crédito
            $dadosArquivo .= '210-023 = 05' . "\n";
        }

        // 999-999 : REGISTRO FINAL
        $dadosArquivo .= '999-999 = 0' . "\n";

        return $dadosArquivo;
    }

    private function criaArquivoTransacao($dadosArquivo) {
        try {
            $impFile = fopen($this->requestFile, 'w');
            fwrite($impFile, $dadosArquivo);
            fclose($impFile);
            return array(
                'error' => false
            );
        } catch(Exception $e) {
            return array(
                'error' => true,
                'message' => 'Erro ao criar arquivo de transação TEF. Erro: ' . $e->getMessage()
            );
        }
    }

    private function trataArquivoReposta($CDTIPORECE) {
        try {
            if (file_exists($this->responseFile) == true) {
                $impFile = fopen($this->responseFile, 'r');
                $dadosArquivo = fread($impFile, filesize($this->responseFile));

                // Cria array indexado por campos da documentação Cliente SiTef
                $dadosArquivo = self::indexaArrayDoArquivo($dadosArquivo);

                // 009-000 : STATUS DA TRANSAÇÃO
                if (trim($dadosArquivo['009-000']) != '0') {
                    if (!empty($dadosArquivo['030-000'])) {
                        $message = $dadosArquivo['030-000'];
                    } else {
                        $message = 'A transação foi negada';
                    }

                    $retorno = array(
                        'error' => false,
                        'canceled' => true,
                        'message' => $message
                    );
                } else {
                    $dados = array();
                    $dados['CDNSUHOSTTEF'] = $dadosArquivo['012-000'];

                    if (empty($dadosArquivo['010-003'])) {
                        $dadosArquivo['010-003'] = null;
                    }

                    $dados['CDBANCARTCR'] = $dadosArquivo['010-003'];
                    $dados['CDTIPORECE'] = self::trataTipoRece($CDTIPORECE, $dados['CDBANCARTCR']);

                    $retorno = array(
                        'error'    => false,
                        'canceled' => false,
                        'dados'    => $dados
                    );
                }
                fclose($impFile);
            } else {
                // Verifica se arquivo de resposta foi criado a cada segundo
                sleep(1);
                $retorno = self::trataArquivoReposta($CDTIPORECE);
            }
            return $retorno;
        } catch(Exception $e) {
            return array(
                'error' => true,
                'message' => 'Erro ao ler arquivo de transação TEF. Erro: ' . $e->getMessage()
            );
        }
    }

    private function trataTipoRece($CDTIPORECE, $CDBANCARTCR, $IDTIPORECE) {
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

    private function armazenaArquivoResposta($NRSEQVENDA, $isCanceled) {
        try {
            // Cria arquivo com extensao da venda
            $arquivo = 'IntPos' . '.' . $NRSEQVENDA;

            // Cria caminho para novo arquivo
            if ($isCanceled == false) {
                $caminhoFinal = $this->processedPath . '\\' . $arquivo;
            } else {
                $caminhoFinal = $this->canceledPath . '\\' . $arquivo;
            }

            // Move arquivo para pasta de processador
            rename($this->responseFile, $caminhoFinal);

            return array(
                'error' => false,
                'canceled' => $isCanceled
            );
        } catch(Exception $e) {
            $this->util->removeAllFiles($this->systemPath . 'TEF/Response');
            return array(
                'error' => true,
                'message' => 'Erro ao armazenar arquivo de transação TEF. Erro: ' . $e->getMessage()
            );
        }
    }

    private function indexaArrayDoArquivo($dadosArquivo) {
        // Separa dados arquivo por linhas
        $dadosArquivo = explode("\n", $dadosArquivo);

        $_dadosArquivo = array();

        // Separa posição para arquivo de impressao SiTef
        $_dadosArquivo['029'] = array();

        foreach ($dadosArquivo as $parametroSitef) {
            $dadosParametro = explode('=', $parametroSitef);
            $dadosParametro[0] = str_replace(' ', '', $dadosParametro[0]);

            // Verifica se posição indexada é vazia
            if (strlen($dadosParametro[0]) > 0) {

                // Verifica se posição indexada é para impressão
                $dadosVerificao = explode('-',$dadosParametro[0]);
                if ($dadosVerificao[0] == '029') {
                    $_dadosArquivo['029'][$dadosVerificao[1]] = $dadosParametro[1];
                } else {
                    $_dadosArquivo[$dadosParametro[0]] = $dadosParametro[1];
                }
            }
        }

        return $_dadosArquivo;
    }

    /********************* FIM TRANSAÇÃO CLIENTE TEF ********************/

    /********************* IMPRESSAO TEF ********************/

    public function imprimeCupomTEF($CDFILIAL, $CDCAIXA, $NRORG, $NRSEQVENDA, $useClientSiTef, $comprovanteTef) {
        $params = array(
            'CDFILIAL' => $CDFILIAL,
            'CDCAIXA' => $CDCAIXA,
            'NRORG' => $NRORG
        );

        $dadosImpressora = $this->entityManager->getConnection()->fetchAll("BUSCA_DADOS_IMPRESSORA_SITEF", $params);

        if (!empty($dadosImpressora)) {
            $IDMODEIMPRES = $dadosImpressora[0]['IDMODEIMPRES'];
            $CDPORTAIMPR = $dadosImpressora[0]['CDPORTAIMPR'];

            $retorno = $this->impressaoUtil->iniciarPorta($IDMODEIMPRES, $CDPORTAIMPR);
            if ($retorno['error'] == false) {
                if ($useClientSiTef) {
                    $retorno = self::carregaDadosImpressaoTEF($IDMODEIMPRES, $NRSEQVENDA);
                } else {
                    // TEF Data from dll
                    $retorno = array(
                        'error' => false,
                        'dados' => $comprovanteTef
                    );
                }

                if ($retorno['error'] == false) {
                    $printerParams = $this->impressaoUtil->buscaParametrosImpressora($IDMODEIMPRES);

                    // Build tef text structure to print
                    $text = $printerParams['comandoEnter'] . $printerParams['comandoEnter'] . $printerParams['comandoEnter'];
                    $text .= $this->impressaoUtil->imprimeLinha($printerParams) . $printerParams['comandoEnter'];
                    $text .= $retorno['dados'];
                    $text .= $printerParams['comandoEnter'] . $printerParams['comandoEnter'] . $printerParams['comandoEnter'];
                    $text .= $this->impressaoUtil->imprimeLinha($printerParams) . $printerParams['comandoEnter'];

                    $retornoInicia = $this->impressaoUtil->iniciarPorta($IDMODEIMPRES, $CDPORTAIMPR);
                    $this->impressaoUtil->imprimeTexto($text, $IDMODEIMPRES, $retornoInicia['portaUsbKiosk']);
                    $this->impressaoUtil->cortaPapel($IDMODEIMPRES, $retornoInicia['portaUsbKiosk']);
                    $this->impressaoUtil->fechaPorta($IDMODEIMPRES, $retornoInicia['portaUsbKiosk']);
                    $retorno = array(
                        'error' => false
                    );
                }
            }
        } else {
            $retorno = array(
                'error' => true,
                'message' => 'Impressora SAT não parametrizada para o caixa.'
            );
        }

        return $retorno;
    }

    private function carregaDadosImpressaoTEF($IDMODEIMPRES, $NRSEQVENDA) {
        $filePath = $this->processedPath . '\\IntPos.' . $NRSEQVENDA;

        if (file_exists($filePath)) {
            $impFile = fopen($filePath, 'r');
            $dadosArquivo = fread($impFile, filesize($filePath));

            $dadosArquivo = self::indexaArrayDoArquivo($dadosArquivo);

            $corpoImpressao = self::montaDadosImpressaoTEF($IDMODEIMPRES, $dadosArquivo['029'], $dadosArquivo['028-001']);

            fclose($impFile);

            return array(
                'error' => false,
                'dados' => $corpoImpressao
            );
        } else {
            return array(
                'error' => true,
                'message' => 'Não foi possível abrir o arquivo da transação TEF para impressão do comprovante.'
            );
        }
    }

    private function montaDadosImpressaoTEF($IDMODEIMPRES, $dadosImpressao, $qtdLinhas) {
        $corpoImpressao = "";
        $printerParams = $this->impressaoUtil->buscaParametrosImpressora($IDMODEIMPRES);

        $corpoImpressao .= $printerParams['comandoEnter'];

        foreach ($dadosImpressao as $index => $linhaImpressao) {
            if (floatval($index) <= $qtdLinhas) {
                $linhaImpressao = str_replace('"', '', $linhaImpressao);
                $linhaImpressao = $this->impressaoUtil->centraliza($printerParams, $linhaImpressao);
                $corpoImpressao .= $linhaImpressao . $printerParams['comandoEnter'];
            }
        }

        return $corpoImpressao;
    }

    /********************* FIM IMPRESSAO TEF ********************/

    /*
    @TODO CANCELAMENTO TEF
    stlFile.Add('000-000 = CNC');
    stlFile.Add('001-000 = ' + stSeq);
    stlFile.Add('003-000 = ' + UltValor);
    stlFile.Add('004-000 = 0');
    stlFile.Add('010-000 = ' + UltRede);
    stlFile.Add('012-000 = ' + UltNSU);
    stlFile.Add('022-000 = ' + UltDataTrans);
    stlFile.Add('023-000 = ' + UltHoraTrans);
    stlFile.add('701-000 = ForSale v5, 55, 1, 0');
    stlFile.add('706-000 = 2');
    stlFile.add('716-000 = TEKNISA SOFTWARE LTDA.');
    stlFile.Add('999-999 = 0');
    */
}
