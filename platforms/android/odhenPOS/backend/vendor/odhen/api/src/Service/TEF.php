<?php

namespace Odhen\API\Service;
use Odhen\API\Remote\Printer\Command;
use Odhen\API\Util\Exception;
class TEF {

	const SALVA_LOGS = false;
    const DELPHI_DLL = false;

    protected $entityManager;
    protected $impressaoUtil;
    protected $util;
    protected $systemPath;

    public function __construct(\Doctrine\ORM\EntityManager $entityManager, \Odhen\API\Lib\ImpressaoUtil $impressaoUtil, \Odhen\API\Util\Util $util, \Zeedhi\Framework\DependencyInjection\InstanceManager $instanceManager) {
        $this->entityManager = $entityManager;
        $this->impressaoUtil = $impressaoUtil;
        $this->util = $util;
        $this->systemPath = $instanceManager->getParameter('SYSTEM_PATH');
    }

    /********************* IMPRESSAO TEF ********************/

    public function imprimeCupomTEF($arrTiporece, $CDFILIAL, $CDCAIXA, $NRORG) {
        $result = array(
			'error' => true,
			'message' => ''
		);
		try {
			$params = array(
				'CDFILIAL' => $CDFILIAL,
				'CDCAIXA' => $CDCAIXA,
				'NRORG' => $NRORG
			);
			$dadosImpressora = $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_IMPRESSORA_SITEF", $params);

			if (!empty($dadosImpressora)){
				$printerParams = $this->impressaoUtil->buscaParametrosImpressora($dadosImpressora['IDMODEIMPRES']);

				$comandos = new Command();
				foreach ($arrTiporece as $recebimento) {
                    $viaCliente = str_replace("|", $printerParams['comandoEnter'], $recebimento['STLPRIVIA']);
                    $comandos->text($viaCliente);
                    $comandos->cutPaper();
                    if(isset($recebimento['STLSEGVIA'])) {
                        $viaLojista = str_replace("|", $printerParams['comandoEnter'], $recebimento['STLSEGVIA']);
                        $comandos->text($viaLojista);
                        $comandos->cutPaper();
                    }
				}

				$result = $this->impressaoUtil->requisicaoPonte($dadosImpressora, $comandos);
			} else {
				$result['message'] = 'Impressora não parametrizada para o caixa.';
	        }
		} catch(\Exception $e) {
			Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
			$result['message'] = $e->getMessage();
		}

	    return $result;
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

    /********************* TRANSAÇÃO TEF DLL ********************/

    public function configuraTransacaoTefDll(&$com, $CDFILIAL, $CDCAIXA, $NRORG = '1') {
        try {
            $params = array(
                'CDFILIAL' => $CDFILIAL,
                'CDCAIXA' => $CDCAIXA,
                'NRORG' => $NRORG
            );

            $dadosTef = $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_TEF", $params);
            $retornoConfigura = $com->configuraSitef($dadosTef['DSENDIPSITEF'], $dadosTef['CDLOJATEF'], $dadosTef['CDTERTEF']);

            return self::trataRetornoConfiguracao($retornoConfigura);
        } catch (\Exception $e) {
            return array(
                'error' => true,
                'message' => 'Mensagem: ' . $e->getMessage()
            );
        }
    }

    public function iniciaTransacaoTefDll(&$com, $IDTIPORECE, $totalSale, $CDOPERADOR) {
        try {
            $funcao = self::trataRecebimentoDll($IDTIPORECE);
            $valor = $totalSale;
            $cupomFiscal = '';
            $data = date('Ymd');
            $horario = date('His');
            $operador = $CDOPERADOR;
            if ($funcao == 2) {
                //Restrições do TAA para TEF - Cartão DEBITO
                $paramsAdicionais = '[10;17;18;19;24;27;28;29;31;32;33;40]';//16;
            } elseif ($funcao == 3) {
                //Restrições do TAA para TEF - Cartão CREDITO
                $paramsAdicionais = '[10;24;27;28;29;31;32;33;34;36;40;44;]';
            }

            $retornoInicia = $com->iniciaSiTef($funcao, $valor, $cupomFiscal, $data, $horario, $operador, $paramsAdicionais);
            $retornoInicia = json_decode($retornoInicia, true);
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
            return array(
                'error' => true,
                'message' => 'Mensagem: ' . $e->getMessage()
            );
        }
    }

     public function continuaTransacaoTefDll(&$com, $valorTotal, $IDTIPORECE, $CDTIPORECE) {
        try {
            $comando = 0;
            $tipoCampo = 0;
            $tamMinimo = 0;
            $tamMaximo = 0;
            $buffer = self::preencheBuffer(299);
            $tamBuffer = 300;
            $continua = 0;

            do {
                $retornoContinua = $com->continuaTransacao($comando, $tipoCampo, $tamMinimo, $tamMaximo, $buffer, $tamBuffer, $continua);
                if (self::DELPHI_DLL) {
                    // Transaction with Delphi DLL
                    $retornoContinua = self::trataRetornoDllDelphi($retornoContinua);
                    $bufferMessage = $retornoContinua['bufferConverted'];
                } else {
                    // Transaction with C# DLL
                    $bufferMessage = mb_convert_encoding(json_decode($retornoContinua, true)['bufferConverted'], 'UTF-8', 'UTF-8');
                    $bufferMessage = trim($bufferMessage);

                    $retornoContinua = json_decode($retornoContinua, true);

                    // Had to do this because dll return comes too poluted
                    $temp = strstr($retornoContinua['bufferConverted'], "\u0000", true);
                    if ($temp != false) {
                        $retornoContinua['bufferConverted'] = $temp;
                    }
                }

                if (self::SALVA_LOGS) {
                    self::salvaEtapaTransacao('comando', $retornoContinua['comando']);
                    self::salvaEtapaTransacao('tipoCampo', $retornoContinua['tipoCampo']);
                }

                if ($retornoContinua['retornoDll'] == 10000) {
                    switch ($retornoContinua['comando']) {
                        case 0:
                            switch ($retornoContinua['tipoCampo']) {
                                case 0:
                                    if (!self::DELPHI_DLL) {
                                        $retornoContinua['bufferConverted'] = self::preencheBuffer(300);
                                    }
                                    break;
                                case 15:
                                    //Cartão de débito (todas as combinações)(Descontinuado, não usar)
                                    if (!self::DELPHI_DLL) {
                                        $retornoContinua['bufferConverted'] = self::preencheBuffer(300);
                                    }
                                    break;
                                case 43:
                                    // echo '<p>Débito Magnético:'.$retornoContinua['bufferConverted'].' </p>';
                                    if (!self::DELPHI_DLL) {
                                        $retornoContinua['bufferConverted'] = self::preencheBuffer(300);
                                    }
                                    //Débito Magnético
                                    break;
                                case 100:
                                    // echo '<p>Contém a modalidade de pagamento no formato xxnn xx corresponde ao grupo da modalidade  e  nn  ao  sub-grupo.  Vide  tabela  no  final  deste  documento  descrevendo os possíveis valores de xx e nn.</p>';
                                    break;
                                case 101:
                                    // echo '<p>Contém  o  texto  real  da  modalidade  de  pagamento  que  pode  ser  memorizado  pela aplicação caso exista essa necessidade. Descreve por extenso o par xxnnfornecido em 100</p>';
                                    break;
                                case 102:
                                    // echo '<p>Contém  o  texto  descritivo  da  modalidade  de  pagamento  que  deve  ser  impresso  no cupon fiscal (p/ex: T.E.F., Cheque, etc...)</p>';
                                    break;
                                case 105:
                                    // echo '<p>Contém a data e hora da transação no formato AAAAMMDDHHMMSS</p>';
                                    break;
                                case 121:
                                    //Buffer  contém  a  primeira  via do  comprovante de  pagamento  (via  do  cliente)  a  ser impressa  na  impressora  fiscal. Essa  via,  quando  possível,  é  reduzida  de  forma  a ocupar  poucas  linhas  na  impressora.  Pode  ser  um  comprovante  de  venda  ou administrativo
                                    // echo '<p>Buffer  contém  a  primeira  via do  comprovante de  pagamento  (via  do  cliente)  a  ser impressa  na  impressora  fiscal. Essa  via,  quando  possível,  é  reduzida  de  forma  a ocupar  poucas  linhas  na  impressora.  Pode  ser  um  comprovante  de  venda  ou administrativo</p>';
                                    self::salvaDadoTemporario('comprovanteTef', $retornoContinua['bufferConverted']);
                                    if (!self::DELPHI_DLL) {
                                        $retornoContinua['bufferConverted'] = self::preencheBuffer(300);
                                    }
                                    break;
                                case 122:
                                    //Buffer  contém  a  segunda  via  do  comprovante  de  pagamento  (via  do  caixa)  a  ser impresso   na   impressora   fiscal.   Pode   ser   um   comprovante   de   venda   ou administrativo
                                    // echo '<p>Buffer  contém  a  segunda  via  do  comprovante  de  pagamento  (via  do  caixa)  a  ser impresso   na   impressora   fiscal.   Pode   ser   um   comprovante   de   venda   ou administrativo</p>';
                                    if (!self::DELPHI_DLL) {
                                        $retornoContinua['bufferConverted'] = self::preencheBuffer(300);
                                    }
                                    break;
                                case 123:
                                    /* Tipos:
                                    COMPROVANTE_COMPRAS = "00"COMPROVANTE_VOUCHER = "01"
                                    COMPROVANTE_CHEQUE = "02"COMPROVANTE_PAGAMENTO= "03"COMPROVANTE_GERENCIAL= "04"COMPROVANTE_CB= "05"COMPROVANTE_RECARGA_CELULAR= "06"COMPROVANTE_RECARGA_BONUS= "07"COMPROVANTE_RECARGA_PRESENTE= "08"COMPROVANTE_RECARGA_SP_TRANS= "09"COMPROVANTE_MEDICAMENTOS= "10"
                                    */
                                    // echo '<p>Indica  que  os  comprovantes  que  serão  entregues  na  seqüência  são  de  determinado tipo (no comentario)</p>';
                                    if (!self::DELPHI_DLL) {
                                        $retornoContinua['bufferConverted'] = self::preencheBuffer(300);
                                    }
                                    break;
                                case 131: // Contém as 6 primeiras posições do cartão (bin)
                                    // echo '<p>Contém;  um  índice  que  indica  qual  a  instituição  que  irá  processar  a  transação segundo a tabela presente no final do documento (5 posições):'.$retornoContinua['bufferConverted'].' </p>';
                                    break;
                                case 132:
                                    // Tipo cartão
                                    // echo '<p>Contém um índice que indica qual o tipo do cartão quando esse tipo for identificável, segundo uma tabela a ser fornecida (5 posições):'.$retornoContinua['bufferConverted'].' </p>';
                                    self::salvaDadoTemporario('CDBANCARTCR', $retornoContinua['bufferConverted']);
                                    break;
                                case 133:
                                    // Tipo cartão
                                    // echo 'NSU Sitef: (6 posições): ' . $retornoContinua['bufferConverted'];
                                    self::salvaDadoTemporario('CDNSUHOSTTEF', $retornoContinua['bufferConverted']);
                                    break;
                                case 134:
                                    // Tipo cartão
                                    // echo 'NSU do Host autorizador (15 posições no máximo): ' . $retornoContinua['bufferConverted'];
                                    break;
                                case 135:
                                    // Tipo cartão
                                    // echo 'Contém  o  Código  de  Autorização  para  as  transações  de  crédito  (15  posições  no máximo)'.$retornoContinua['bufferConverted'].' </p>';
                                    break;
                                case 136: // Contém as 6 primeiras posições do cartão (bin)
                                    // echo '<p>Contém as 6 primeiras posições do cartão (bin):'.$retornoContinua['bufferConverted'].' </p>';
                                    break;
                                case 156:
                                    // echo '<p>Nome da instituição:' . $retornoContinua['bufferConverted'] . ' </p>';
                                    break;
                                case 157:
                                    // echo '<p>Código de Estabelecimento:' . $retornoContinua['bufferConverted'] . ' </p>';
                                    break;
                                case 158:
                                    // echo '<p>Código da Rede Autorizadora –Serviço H:' . $retornoContinua['bufferConverted'] . ' </p>';
                                    break;
                                case 161:
                                    // echo '<p>Número Identificador do Cupom do Pagamento:' . $retornoContinua['bufferConverted'] . ' </p>';
                                    break;
                                case 170:
                                    // echo '<p>Venda Parcelada Estabelecimento Habilitada:' . $retornoContinua['bufferConverted'] . ' </p>';
                                    break;
                                case 171:
                                    // echo '<p>Número Mínimo de Parcelas –Parcelada Estabelecimento:' . $retornoContinua['bufferConverted'] . ' </p>';
                                    break;
                                case 172:
                                    // echo '<p>Número Máximo de Parcelas –Parcelada Estabelecimento:' . $retornoContinua['bufferConverted'] . ' </p>';
                                    break;
                                case 173:
                                    // echo '<p>Valor Mínimo Por Parcela –Parcelada Estabelecimento:' . $retornoContinua['bufferConverted'] . ' </p>';
                                    break;
                                case 174:
                                    // echo '<p>Venda Parcelada Administradora Habilitada:' . $retornoContinua['bufferConverted'] . ' </p>';
                                    break;
                                case 175:
                                    // echo '<p>Número Mínimo de Parcelas –Parcelada Administradora:' . $retornoContinua['bufferConverted'] . ' </p>';
                                    break;
                                case 176:
                                    // echo '<p>Número Máximo de Parcelas –Parcelada Administradora:' . $retornoContinua['bufferConverted'] . ' </p>';
                                    break;
                                case 724:
                                    // echo '<p>Venda Crédito Parcelada com Plano Habilitada:' . $retornoContinua['bufferConverted'] . ' </p>';
                                    break;
                                case 725:
                                    // echo '<p>Venda Crédito com Autorização a Vista Habilitada:' . $retornoContinua['bufferConverted'] . ' </p>';
                                    break;
                                case 726:
                                    // echo '<p>Venda Crédito com Autorização Parcela com Plano Habilitada:' . $retornoContinua['bufferConverted'] . ' </p>';
                                    break;
                                case 727:
                                    // echo '<p>VendaBoleto Habilitada:' . $retornoContinua['bufferConverted'] . ' </p>';
                                    break;
                                case 2010:
                                    // echo '<p>Código de resposta do autorizador:' . $retornoContinua['bufferConverted'] . ' </p>';
                                    break;
                                case 2090:
                                    //Tipo do cartão Lido
                                    // echo '<p>Tipo do cartão Lido:'.$retornoContinua['bufferConverted'].' </p>';
                                break;
                                case 2091:
                                    //Tipo do cartão Lido
                                    // echo '<p>Status da última leitura do cartão:'.$retornoContinua['bufferConverted'].' </p>';
                                    break;
                                case 1:
                                case 2:
                                case 504:
                                    if (!self::DELPHI_DLL) {
                                        $retornoContinua['bufferConverted'] = self::preencheBuffer(300, '0');
                                    }
                                    break;
                                case 800:
                                case 801:
                                case 952:
                                case 1900:
                                case 2053:
                                case 2333:
                                case 2362:
                                case 2364:
                                case 2421:
                                case 4100:
                                case 5049:
                                    //Tipo do cartão Lido
                                    // echo '<p>Valor retornoContinua[.tipoCampo] não documentado, buffer:'.$retornoContinua['bufferConverted'].' </p>' ;
                                    break;
                                default:
                                    if ($retornoContinua['tipoCampo'] >= 10 && $retornoContinua['tipoCampo'] <= 99) {
                                        // echo '<p>Tipo do cartão: ' . $retornoContinua['tipoCampo'] . '</p>';
                                    } else {
                                        return array(
                                            'error' => true,
                                            'message' => 'Error retornoContinua[tipoCampo] invalido :' . $retornoContinua['tipoCampo'],
                                            'finishedTransaction' => true
                                        );
                                    }
                                    break;
                            }
                        break;
                        case 1:
                            //Mensagem para o visor do operador
                            if (!self::DELPHI_DLL) {
                                $retornoContinua['bufferConverted'] = self::preencheBuffer(300);
                            }
                            break;
                        case 2:
                            if (!self::DELPHI_DLL) {
                                $retornoContinua['bufferConverted'] = self::preencheBuffer(300);
                            }
                            break;
                        case 3:
                            //"Mensagem para os dois visores"
                            // echo '<p>Mensagem para os dois visores :'.$retornoContinua['bufferConverted'].' </p>';
                            if (!self::DELPHI_DLL) {
                                $message = mb_convert_encoding($retornoContinua['bufferConverted'], 'UTF-8', 'UTF-8');
                                $message = trim($message);
                                $retornoContinua['bufferConverted'] = self::preencheBuffer(300);
                            } else {
                                $message = $retornoContinua['bufferConverted'];
                            }
                            return array(
                                'error' => false,
                                'finishedTransaction'=> false,
                                'message' => $message
                            );
                            break;
                        case 4:
                            break;
                        case 13:
                              //LimpaMensagem;
                            break;
                        case 14:
                            // echo '<p>Deve limpar o texto utilizado como cabeçalho na apresentação do menu</p>';
                            break;
                        case 15:
                            //Cabeçalho a ser apresentado pela aplicação?
                            if (!self::DELPHI_DLL) {
                                $retornoContinua['bufferConverted'] = self::preencheBuffer(300);
                            }
                        break;
                        case 20:
                            //Deve  obter  uma  resposta  do  tipo  SIM/NÃO.  No  retorno  o  primeiro  caráter presente em Buffer deve conter 0 se confirma e 1 se cancela
                            //na primeira vez q caiu aqui foi por erro de leitura.
                            //tem que mandar a pegunta na tela e ver se o cara quer continuar
                            //$confirma = $this->mensagemConfirma();// '0' se ok, '1' se nao ok
                            $confirma = '0';
                            $retornoContinua['bufferConverted'] = self::preencheBuffer(300,$confirma);
                            break;
                        case 21:
                            // echo '<p>Deve apresentar um menude opções e permitir que o usuário selecione uma delas. Na chamada o parâmetro Buffer contém as opções no formato 1:texto;2:texto;...i:Texto;...  A  rotina  da  aplicação  deve  apresentar  as  opções  da forma  que  ela  desejar  (não  sendo  necessário  incluir  os  índices  1,2,  ...)  e  após  a seleção  feita  pelo  usuário,  retornar  em  Buffer  o  índice  i  escolhido  pelo  operador (em ASCII)</p>';
                            // Simula a vista
                            $confirma = '1';
                            $retornoContinua['bufferConverted'] = self::preencheBuffer(300,$confirma);
                            break;
                        case 22:
                            /* Deve aguardar uma tecla do operador. É utilizada quando se deseja que o operador seja avisado de alguma mensagem apresentada na tela */
                        break;
                        case 23:
                            // Este comando indica que a rotina está perguntando para a aplicação se ele deseja interromper  o   processo de coleta de dados ou não. Esse código ocorre quando a CliSiTefestá  acessando  algum  periférico  e  permite  que  a  automação  interrompa esse acesso (por exemplo: aguardando a passagem de um cartão pela leitora ou a digitação de senha pelo cliente)
                            //$retornoContinua['retornoDll'] := -5;
                            //$retornoContinua['comando'] = -1;
                        break;
                        case 30:
                            // Deve  ser  lido  um  campo  cujo  tamanho  está  entre TamMinimo e TamMaximo. O campo lido deve ser devolvido em Buffer
                            return array(
                                'error' => true,
                                'message' =>  'Cartão inválido para este tipo de operação.',
                                'finishedTransaction' => true
                            );
                            break;
                		case 34:
                			//Deve ser lido um campo monetário ou seja, aceita o delimitador de centavos e devolvido no parâmetro Buffer
                            $retornoContinua['bufferConverted'] = self::preencheBuffer(300, '0');
                        break;
                        default:
                            return array(
                                'error' => true,
                                'message' =>  'Error retornoContinua[comando] invalido :' . $retornoContinua['comando'],
                                'finishedTransaction' => true
                            );
                        break;
                    }

                    $comando   = $retornoContinua["comando"];
                    $tipoCampo = $retornoContinua['tipoCampo'];
                    $tamMinimo = $retornoContinua["tamMinimo"];
                    $tamMaximo = $retornoContinua["tamMaximo"];
                    $tamBuffer = self::DELPHI_DLL ? $retornoContinua["tamBuffer"] : 300;
                    $buffer    = $retornoContinua['bufferConverted'];
                    $continua  = $retornoContinua["continua"];
                } elseif ($retornoContinua['retornoDll'] > 0) {
                    // Encerra transação
                    $retornoContinua = $com->continuaTransacao(0, 0, 0, 0, 0, 0, -1);

                    return array(
                        'error' => true,
                        'message' => 'Problema ao realizar transação. Procure um atendente próximo.'
                    );
                }
            } while($retornoContinua['retornoDll'] == 10000);

            if ($retornoContinua['retornoDll'] == 0) {
                $dadosTransacao = self::carregaDadosTransacao();
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
            return array(
                'error' => true,
                'message' => 'Mensagem: ' . $e->getMessage(),
                'finishedTransaction' => true
            );
        }
    }

    private function salvaEtapaTransacao($field, $value) {
        $this->util->createFolder($this->systemPath . 'TEF/LOGS');
    	$fileName = 'saveStep_transaction' . date('d-m-Y_H') . '.txt';

        $impFile = fopen($this->systemPath . 'TEF/LOGS/' . $fileName, 'a');

        $currentDateTime = date('d-m-Y_H-i-s');
        $transactionData = $currentDateTime . ' - ' . $field . ': ' . $value . "\r\n";

        fwrite($impFile, $transactionData);

        fclose($impFile);
    }

    private function salvaDadoTemporario($field, $value) {
        $this->util->createFolder($this->systemPath . 'TEF/TEMP');
        $fileName = 'temp_' . $field . '.txt';
    	$impFile = fopen($this->systemPath . 'TEF/TEMP/' . $fileName, 'w');
        fwrite($impFile, $value);
        fclose($impFile);
    }

    private function carregaDadosTransacao() {
        $tempDir = $this->systemPath . 'TEF/TEMP/temp_';

        $CDBANCARTCRFile = fopen($tempDir . 'CDBANCARTCR.txt', 'r');
        $CDBANCARTCR = fread($CDBANCARTCRFile, filesize($tempDir . 'CDBANCARTCR.txt'));

        $CDNSUHOSTTEFFile = fopen($tempDir . 'CDNSUHOSTTEF.txt', 'r');
        $CDNSUHOSTTEF = fread($CDNSUHOSTTEFFile, filesize($tempDir . 'CDNSUHOSTTEF.txt'));

        $comprovanteTefFile = fopen($tempDir . 'comprovanteTef.txt', 'r');
        $comprovanteTef = fread($comprovanteTefFile, filesize($tempDir . 'comprovanteTef.txt'));

        return array(
            'CDBANCARTCR' => $CDBANCARTCR,
            'CDNSUHOSTTEF' => $CDNSUHOSTTEF,
            'comprovanteTef' => $comprovanteTef,
        );
    }

    private function trataRetornoDllDelphi($retornoContinua) {
        /*
        * Estrutura de retorno da dll:
        * "Resposta=10000#Comando=1#TipoCampo=-1#tamMinimo=0#tamMaximo=0#Buffer=Conectando SiTef#TamBuffer=2001#Continua=0"
        */
        $sitefReturns = explode('#', $retornoContinua);
        foreach ($sitefReturns as &$dllReturn) {
            $dllReturn = explode('=', $dllReturn);
        }
        $retornoContinua = array(
            'retornoDll' => $sitefReturns[0][1],
            'comando' => $sitefReturns[1][1],
            'tipoCampo' => $sitefReturns[2][1],
            'tamMinimo' => $sitefReturns[3][1],
            'tamMaximo' => $sitefReturns[4][1],
            'bufferConverted' => $sitefReturns[5][1],
            'tamBuffer' => $sitefReturns[6][1],
            'continua' => $sitefReturns[7][1]
        );
        return $retornoContinua;
    }

    public function finalizaTransacao(&$com) {
        try {
            $data = date('Ymd');
            $horario = date('His');
            $retornoFinaliza = $com->finalizaTransacao('1', '', $data, $horario);

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

    private function trataRetornoConfiguracao($retornoConfigura) {
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

    function preencheBuffer($tamanho, $string = ''){
        for($i = count($string); $i <= $tamanho; $i++){
            $string .= chr(0);
        }
        return $string;
    }

    private function trataRecebimentoDll($IDTIPORECE) {
        if ($IDTIPORECE == '1') {
            return 3;
        } else {
            return 2;
        }
    }

    public function validaParametrosTef($CDFILIAL, $CDCAIXA) {
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
    	} elseif (empty($dadosTef['DSENDIPSITEF'])) {
    		$retorno = array(
    			'error' => true,
    			'message' => 'Endereço IP do servidor TEF não parametrizado para o caixa.'
    		);
    	} else {
    		$retorno = array(
    			'error' => false,
    			'tefConfigurationParams' => $dadosTef
    		);
    	}

    	return $retorno;
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
}
