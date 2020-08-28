<?php


namespace Odhen\API\Service;

use Odhen\API\Util\Exception;

class Venda {

	protected $entityManager;
	protected $notaFiscalService;
	protected $estoque;
	protected $getParametro;
	protected $util;
	protected $sat;
	protected $databaseUtil;
	protected $precoService;
	protected $comandaService;
	protected $vendaValidacao;
	protected $vendaNormalizacao;
    protected $consumidorService;
    protected $extratocons;
   	protected $impressaoDelivery;

 	public function __construct(
 		\Doctrine\ORM\EntityManager $entityManager,
 		\Odhen\API\Service\NotaFiscal $notaFiscal,
 		\Odhen\API\Service\Estoque $estoque,
 		\Odhen\API\Util\GetParametro $getParametro,
 		\Odhen\API\Util\Util $util,
 		\Odhen\API\Service\SAT $sat,
 		\Odhen\API\Util\Database $databaseUtil,
 		\Odhen\API\Service\Preco $precoService,
		\Odhen\API\Service\Comanda $comandaService,
		\Odhen\API\Service\VendaValidacao $vendaValidacao,
		\Odhen\API\Service\VendaNormalizacao $vendaNormalizacao,
        \Odhen\API\Service\Consumidor $consumidorService,
        \Odhen\API\Service\Extratocons $extratocons,
    	\Odhen\API\Service\ImpressaoDelivery $impressaoDelivery,
    	\Zeedhi\Framework\DependencyInjection\InstanceManager $instanceManager){

		$this->entityManager = $entityManager;
		$this->notaFiscalService = $notaFiscal;
		$this->estoque = $estoque;
		$this->getParametro = $getParametro;
		$this->util = $util;
		$this->sat = $sat;
		$this->databaseUtil = $databaseUtil;
		$this->precoService = $precoService;
		$this->comandaService = $comandaService;
		$this->vendaValidacao = $vendaValidacao;
		$this->vendaNormalizacao = $vendaNormalizacao;
        $this->consumidorService = $consumidorService;
        $this->extratocons   = $extratocons;
        $this->impressaoDelivery = $impressaoDelivery;
        $this->instanceManager = $instanceManager;
        $this->habilitaLog = $this->instanceManager->getParameter('HABILITA_LOG_VENDA');
	}

	const APROVADA = 'O';
    const ENTRADA = 'E';
    const SINCRONIZA_ESTOQUE = 'S';
	const ITEM_APROVADO = 'A';
	const ITEM_CANCELADO = '6';
	const ITEM_ABASTECIMENTO = 'A';
	const TIPO_NFCE = 'N';
	const TIPO_SAT = 'S';
	const OBS_ADICIONA_PRODUTO = 'A';

	public function venda(
		$NRORG, $CDFILIAL, $CDLOJA, $CDCAIXA,
		$CDVENDEDOR, $CDOPERADOR, $DTABERCAIX, $DTVENDA,
		$TOTALPRODUTOS, $ITEMVENDA, $TIPORECE, $NMCONSVEND,
		$NRINSCRCONS, $NRSEQVENDA, $VRTROCOVEND, $EMAIL,
		$VRDESCVENDA, $CDCLIENTE, $CDCONSUMIDOR, $imprimeCupom,
		$simulateSaleValidation, $IDORIGEMVENDA, $CDSENHAPED, $NRVENDAREST = null,
		$NRCOMANDA = null, $FIDELITYDISCOUNT = 0, $FIDELITYVALUE = 0, $motivoDesconto = null,
		$CDGRPOCORDESC = null, $DSOBSFINVEN = null, $ITVENDADES = null, $REPIQUE = 0) {

		$this->getLogHeader($CDFILIAL, $CDLOJA, $CDCAIXA, $CDOPERADOR, $DTVENDA, $TOTALPRODUTOS);

		try {
			$this->entityManager->getConnection()->beginTransaction();
			$respostaValidacao = $this->vendaValidacao->validaVenda($CDFILIAL, $CDCAIXA);

			$this->geraLog(' -Validação Venda (1): ' . ($respostaValidacao['error'] ? 'com' : 'sem') . ' erro.', $CDFILIAL, $CDCAIXA, $DTVENDA);
			if (!$respostaValidacao['error']){
				$respostaNormalizacao = $this->vendaNormalizacao->normalizaVenda($CDFILIAL, $CDCAIXA, $ITEMVENDA, $TIPORECE, $VRTROCOVEND);
				$IDTPEMISVEND = $respostaNormalizacao['IDTPEMISVEND'];
				$ITEMVENDA = $respostaNormalizacao['ITEMVENDA'];
				$TIPORECE = $respostaNormalizacao['TIPORECE'];
				$VRTROCOVEND = $respostaNormalizacao['VRTROCOVEND'];

				self::adicionaObsCobrada($ITEMVENDA);
				$validacaoVenda = self::montaPrecosProdutos($TOTALPRODUTOS, $ITEMVENDA, $VRDESCVENDA, $CDFILIAL, $CDLOJA, $CDCLIENTE, $CDCONSUMIDOR);
				if (!$validacaoVenda['error']){
					$ITEMVENDA = $validacaoVenda['produtosComPreco'];
					if (!empty($REPIQUE)) {
						$dadosTaxa = $this->buscaDadosTaxaServico($CDFILIAL, $CDLOJA);
						$produtoTaxaServico = self::adicionaProdutoTaxaServico($dadosTaxa, null, null, $REPIQUE, 0);
						if (!empty($produtoTaxaServico)) {
	        				array_push($ITEMVENDA, $produtoTaxaServico);
	        			}
        			}

					$respostaValidacaoValor = $this->vendaValidacao->validaValor($ITEMVENDA, $TOTALPRODUTOS, $VRDESCVENDA);
					$this->geraLog(' -Validação Venda (2): ' . ($respostaValidacaoValor['error'] ? 'com' : 'sem') . ' erro.', $CDFILIAL, $CDCAIXA, $DTVENDA);
					if (!$respostaValidacaoValor['error']){
						$TOTALVENDALIQ = $respostaValidacaoValor['TOTALVENDA'];
						$respostaValidacaoDebitoConsumidor = $this->vendaValidacao->validaVendaDebitoConsumidor($CDFILIAL, $CDLOJA, $CDOPERADOR, $CDCLIENTE, $CDCONSUMIDOR, $TIPORECE, $TOTALPRODUTOS, $respostaValidacaoValor['subsidy']);
						$this->geraLog(' -Validação Débito Consumidor: ' . ($respostaValidacaoDebitoConsumidor['error'] ? 'com' : 'sem') . ' erro.', $CDFILIAL, $CDCAIXA, $DTVENDA);
						if (!$respostaValidacaoDebitoConsumidor['error']){
							$respostaValidacaoCreditoPessoal = $this->vendaValidacao->validaVendacreditoPessoal($TIPORECE, $ITEMVENDA, $CDCLIENTE, $CDCONSUMIDOR, $CDFILIAL, $CDCAIXA);
							$this->geraLog(' -Validação Crédito Pessoal: ' . ($respostaValidacaoCreditoPessoal['error'] ? 'com' : 'sem') . ' erro.', $CDFILIAL, $CDCAIXA, $DTVENDA);
							if (!$respostaValidacaoCreditoPessoal['error']){
								$FAMSALDOPROD = $respostaValidacaoCreditoPessoal['FAMSALDOPROD'];
								$CDSENHAPED = !empty($CDSENHAPED) ? $CDSENHAPED : $this->util->generateCDSENHAPED($CDFILIAL, $NRORG);
								$NRMESA = null;

								$result = $this->trataVenda(
									$NRORG,
									$CDFILIAL,
									$CDLOJA,
									$CDCAIXA,
									$CDVENDEDOR,
									$CDOPERADOR,
									$DTABERCAIX,
									$DTVENDA,
									$IDTPEMISVEND,
									$TOTALVENDALIQ,
									$TOTALPRODUTOS,
									$ITEMVENDA,
									$TIPORECE,
									$NMCONSVEND,
									$NRINSCRCONS,
									$CDSENHAPED,
									$NRSEQVENDA,
									$VRTROCOVEND,
									$EMAIL,
									$VRDESCVENDA,
									$CDCLIENTE,
									$CDCONSUMIDOR,
									$NRVENDAREST,
									$NRCOMANDA,
									$NRMESA,
									$IDORIGEMVENDA,
									$imprimeCupom,
									$simulateSaleValidation,
									null, // VRTXSEVENDA
									1, // NRPESMESAVENDA
									null, // DSCOMANDA
									$FAMSALDOPROD,
									null, // DTHRMESAFECHVEN
                                    $FIDELITYDISCOUNT,
                                    $FIDELITYVALUE,
                            		$motivoDesconto,
                            		$CDGRPOCORDESC,
                                    $respostaValidacaoValor['subsidy'],
                                    $DSOBSFINVEN,
                                    $ITVENDADES,
                                    false, // DELIVERY
                                    0, // VRACRCOMANDA
                                    null, // ITEMVENDAEST
                                    null, // OBSITEMVENDAEST
                                    $REPIQUE
								);

								$this->geraLog(' -Validação Trata Venda: ' . ($result['error'] ? 'com' : 'sem') . ' erro.', $CDFILIAL, $CDCAIXA, $DTVENDA);

								if(!$result['error']) {
									$this->geraLog("\r\n" . '  VENDA REALIZADA COM SUCESSO. NRSEQVENDA: ' . $result["NRSEQVENDA"], $CDFILIAL, $CDCAIXA, $DTVENDA);
								} else {
									$this->geraLog('Erro: ' . $result['message'], $CDFILIAL, $CDCAIXA, $DTVENDA);
								}
							} else {
								$this->geraLog('Erro: ' . $respostaValidacaoCreditoPessoal['message'], $CDFILIAL, $CDCAIXA, $DTVENDA);
								$result = $respostaValidacaoCreditoPessoal;
							}
						} else {
							$this->geraLog('Erro: ' . $respostaValidacaoDebitoConsumidor['message'], $CDFILIAL, $CDCAIXA, $DTVENDA);

							$result = $respostaValidacaoDebitoConsumidor;
						}
					} else {
						$this->geraLog('Erro: ' . $respostaValidacaoValor['message'], $CDFILIAL, $CDCAIXA, $DTVENDA);
						$result = $respostaValidacaoValor;
					}
				} else {
					$this->geraLog('Erro: ' . $validacaoVenda['message'], $CDFILIAL, $CDCAIXA, $DTVENDA);
					$result = $validacaoVenda;
				}
			} else {
				$this->geraLog('Erro: ' . $respostaValidacao['message'], $CDFILIAL, $CDCAIXA, $DTVENDA);
				$result = $respostaValidacao;
			}
			$this->entityManager->getConnection()->commit();
			// Valida se a venda foi inserida no banco.
			if(isset($result['NRSEQVENDA'])){
				$this->validaInsercaoVenda($CDFILIAL, $CDCAIXA, $DTVENDA, $result['NRSEQVENDA']);
			}

		} catch (\Exception $e) {
			Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
			if ($this->entityManager->getConnection()->isTransactionActive()) {
				$this->entityManager->getConnection()->rollBack();
			}
			$result = array(
				'error' => true,
				'message' => $e->getMessage()
			);
 		}
 		return $result;
	}

	public function vendaMesa(
		$NRORG, $CDFILIAL, $CDLOJA, $CDCAIXA,
		$CDVENDEDOR, $CDOPERADOR, $DTABERCAIX, $DTVENDA,
		$TOTALPRODUTOS, $TIPORECE, $NMCONSVEND, $NRINSCRCONS, $CDSENHAPED,
		$VRTROCOVEND, $EMAIL, $VRDESCVENDA, $CDCLIENTE,
		$CDCONSUMIDOR, $imprimeCupom, $simulateSaleValidation, $dadosMesa,
		$arrayPosicoes, $IDORIGEMVENDA, $VRTXSEVENDA, $FIDELITYDISCOUNT = 0, $FIDELITYVALUE = 0,
        $motivoDesconto = null, $CDGRPOCORDESC = null, $DSOBSFINVEN = null, $DELIVERY = false, $VRACRCOMANDA = 0, $REPIQUE = 0) {
		$this->getLogHeader($CDFILIAL, $CDLOJA, $CDCAIXA, $CDOPERADOR, $DTVENDA, $TOTALPRODUTOS);

		try {

			$this->entityManager->getConnection()->beginTransaction();
			$respostaValidacao = $this->vendaValidacao->validaVenda($CDFILIAL, $CDCAIXA);
			$this->geraLog(' -Validação Venda (1): ' . ($respostaValidacao['error'] ? 'com' : 'sem') . ' erro.', $CDFILIAL, $CDCAIXA, $DTVENDA);

			if ($respostaValidacao['error'] == false) {
				$respostaNormalizacao = $this->vendaNormalizacao->normalizaVenda($CDFILIAL, $CDCAIXA, null, $TIPORECE, $VRTROCOVEND);
				$IDTPEMISVEND = $respostaNormalizacao['IDTPEMISVEND'];
				$TIPORECE = $respostaNormalizacao['TIPORECE'];
				$VRTROCOVEND = $respostaNormalizacao['VRTROCOVEND'];
				$validacaoVenda = self::validaVendaComanda($CDFILIAL, $dadosMesa, $arrayPosicoes, $CDLOJA, $VRDESCVENDA, $VRTXSEVENDA, $CDCLIENTE, $DELIVERY, $VRACRCOMANDA, $TOTALPRODUTOS, $REPIQUE);
				$this->geraLog(' -Validação Venda (2): ' . ($validacaoVenda['error'] ? 'com' : 'sem') . ' erro.', $CDFILIAL, $CDCAIXA, $DTVENDA);

				if ($validacaoVenda['error'] == false) {
					$ITEMVENDA = $validacaoVenda['ITEMVENDA'];
					$ITVENDADES = $validacaoVenda['ITVENDADES'];
					$TOTALVENDALIQ = $validacaoVenda['TOTALVENDALIQ'];
					$TOTALVENDABRT = $validacaoVenda['TOTALVENDABRT'];

					// Na venda somente são consideradas as posições que estão sendo recebidas para serem salvas no banco de dados.
					$NRPESMESAVENDA = strval(count(array_unique(array_column($ITEMVENDA, 'NRLUGARMESA'))));
					$NRVENDAREST = $dadosMesa[0]['NRVENDAREST'];
					$NRCOMANDA = $dadosMesa[0]['NRCOMANDA'];
					$NRMESA = $dadosMesa[0]['NRMESA'];
					$DSCOMANDA = $dadosMesa[0]['DSCOMANDA'];
					$DTHRMESAFECHVEN = $dadosMesa[0]['DTHRMESAFECH'];
					$NRSEQVENDA = null;

                    // Valida débito consumidor.
                    $respostaValidacaoDebitoConsumidor = $this->vendaValidacao->validaVendaDebitoConsumidor($CDFILIAL, $CDLOJA, $CDOPERADOR, $CDCLIENTE, $CDCONSUMIDOR, $TIPORECE, $TOTALVENDALIQ, $validacaoVenda['SUBSIDY']);
                    $this->geraLog(' -Validação Débito Consumidor: ' . ($respostaValidacaoDebitoConsumidor['error'] ? 'com' : 'sem') . ' erro.', $CDFILIAL, $CDCAIXA, $DTVENDA);
                    if ($respostaValidacaoDebitoConsumidor['error']){
                        $this->geraLog('Erro: ' . $respostaValidacaoDebitoConsumidor['message'], $CDFILIAL, $CDCAIXA, $DTVENDA);
                        throw new \Exception($respostaValidacaoDebitoConsumidor['message']);
                    }
                    // Valida crédito pessoal.
                    $respostaValidacaoCreditoPessoal = $this->vendaValidacao->validaVendacreditoPessoal($TIPORECE, $ITEMVENDA, $CDCLIENTE, $CDCONSUMIDOR, $CDFILIAL, $CDCAIXA);
    				$this->geraLog(' -Validação Crédito Pessoal: ' . ($respostaValidacaoCreditoPessoal['error'] ? 'com' : 'sem') . ' erro.', $CDFILIAL, $CDCAIXA, $DTVENDA);
    				if ($respostaValidacaoCreditoPessoal['error']){
                        $this->geraLog('Erro: ' . $respostaValidacaoCreditoPessoal['message'], $CDFILIAL, $CDCAIXA, $DTVENDA);
                        throw new \Exception($respostaValidacaoCreditoPessoal['message']);
                    }

                    $result = $this->trataVenda(
						$NRORG,
						$CDFILIAL,
						$CDLOJA,
						$CDCAIXA,
						$CDVENDEDOR,
						$CDOPERADOR,
						$DTABERCAIX,
						$DTVENDA,
						$IDTPEMISVEND,
						$TOTALVENDALIQ,
						$TOTALVENDABRT,
						$ITEMVENDA,
						$TIPORECE,
						$NMCONSVEND,
						$NRINSCRCONS,
						$CDSENHAPED,
						$NRSEQVENDA,
						$VRTROCOVEND,
						$EMAIL,
						0, // desconto já foi tratado
						$CDCLIENTE,
						$CDCONSUMIDOR,
						$NRVENDAREST,
						$NRCOMANDA,
						$NRMESA,
						$IDORIGEMVENDA,
						$imprimeCupom,
						$simulateSaleValidation,
						$VRTXSEVENDA,
						$NRPESMESAVENDA,
						$DSCOMANDA,
						$respostaValidacaoCreditoPessoal['FAMSALDOPROD'],
						$DTHRMESAFECHVEN,
                        $FIDELITYDISCOUNT,
                        $FIDELITYVALUE,
                        $motivoDesconto,
                        $CDGRPOCORDESC,
                        $validacaoVenda['SUBSIDY'],
                        $DSOBSFINVEN,
                        $ITVENDADES,
                        $DELIVERY,
                        $VRACRCOMANDA,
                        $REPIQUE
					);

					$this->geraLog(' -Validação Trata Venda: ' . ($result['error'] ? 'com' : 'sem') . ' erro.', $CDFILIAL, $CDCAIXA, $DTVENDA);

					if (!$result['error']){
						$this->geraLog("\r\n" . '  VENDA REALIZADA COM SUCESSO. NRSEQVENDA: ' . $result["NRSEQVENDA"], $CDFILIAL, $CDCAIXA, $DTVENDA);

						if($DELIVERY){
							if($TOTALVENDABRT > 0){
								$orders = $this->comandaService->liberaComandaDelivery($CDFILIAL, $NRVENDAREST, $NRCOMANDA, $CDLOJA, $result['NRSEQVENDA'], $CDCAIXA);
								$params = array(
									'CDFILIAL' => $CDFILIAL,
									'CDLOJA'   => $CDLOJA,
									'CDCAIXA'  => $CDCAIXA
								);
								$imprimeEntregaAut = $this->entityManager->getConnection()->fetchAssoc("IMPRIMEDLV_AUTOMATICO", $params);

								$resultDlv['error'] = false;
								if($imprimeEntregaAut['IDIMPAUTENTREG'] == 'S'){
									$resultDlv = $this->impressaoDelivery->imprimeDelivery($orders);
								}
								$result['errorDlv'] = $resultDlv['error'];
								if($resultDlv['error']){
									$result['messageDlv'] = $resultDlv['message'];
								}
							}
						} else{
							$this->comandaService->liberaComanda($CDFILIAL, $dadosMesa, $arrayPosicoes, $CDOPERADOR, $CDLOJA, $IDORIGEMVENDA);
						}
					} else {
						$this->geraLog('Erro: ' . $result['message'], $CDFILIAL, $CDCAIXA, $DTVENDA);
					}
				} else {
					$this->geraLog('Erro: ' . $validacaoVenda['message'], $CDFILIAL, $CDCAIXA, $DTVENDA);
					$result = $validacaoVenda;
				}
			} else {
				$this->geraLog('Erro: ' . $respostaValidacao['message'], $CDFILIAL, $CDCAIXA, $DTVENDA);
				$result = $respostaValidacao;
			}

			$this->entityManager->getConnection()->commit();

			// Valida se a venda foi inserida no banco.
			if(isset($result['NRSEQVENDA'])){
            	$this->validaInsercaoVenda($CDFILIAL, $CDCAIXA, $DTVENDA, $result['NRSEQVENDA']);
			}

		} catch (\Exception $e) {
			Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
			if ($this->entityManager->getConnection()->isTransactionActive()) {
				$this->entityManager->getConnection()->rollBack();
			}
			$result = array(
				'error' => true,
				'message' => $e->getMessage()
			);
 		}
 		return $result;
	}

	protected function trataVenda(
		$NRORG, $CDFILIAL, $CDLOJA, $CDCAIXA,
		$CDVENDEDOR, $CDOPERADOR, $DTABERCAIX, $DTVENDA,
		$IDTPEMISVEND, $TOTALVENDALIQ, $TOTALVENDABRT, $ITEMVENDA, $TIPORECE,
		$NMCONSVEND, $NRINSCRCONS, $CDSENHAPED, $NRSEQVENDA,
		$VRTROCOVEND, $EMAIL, $VRDESCVENDA, $CDCLIENTE,
		$CDCONSUMIDOR, $NRVENDAREST, $NRCOMANDA, $NRMESA,
		$IDORIGEMVENDA, $imprimeCupom, $simulateSaleValidation, $VRTXSEVENDA,
		$NRPESMESAVENDA, $DSCOMANDA, $FAMSALDOPROD, $DTHRMESAFECHVEN, $FIDELITYDISCOUNT, $FIDELITYVALUE,
        $motivoDesconto, $CDGRPOCORDESC, $SUBSIDY, $DSOBSFINVEN, $ITVENDADES = null, $DELIVERY = false, $VRACRCOMANDA = 0, $REPIQUE = 0) {

		$VRTXSEVENDA += $VRACRCOMANDA;

		$impostos = $this->calculaImpostoParaImpressao($NRORG, $CDFILIAL, $CDCAIXA, $ITEMVENDA, $TOTALVENDABRT, $VRDESCVENDA, $CDLOJA, $CDCLIENTE, $CDCONSUMIDOR, $VRTXSEVENDA);
		$VRTOTTRIBIBPT = $impostos['VRTOTTRIBIBPT'];
		$impostoEstadual = $impostos['impostoEstadual'];


		$respostaVenda = $this->realizaVenda(
			$NRORG,
			$CDFILIAL,
			$CDLOJA,
			$CDCAIXA,
			$CDVENDEDOR,
			$CDOPERADOR,
			$DTABERCAIX,
			$DTVENDA,
			$IDTPEMISVEND,
			$TOTALVENDABRT,
			$ITEMVENDA,
			$TIPORECE,
			$NMCONSVEND,
			$NRINSCRCONS,
			$CDSENHAPED,
			$NRSEQVENDA,
			$VRTROCOVEND,
			$EMAIL,
			$VRDESCVENDA,
			$VRTOTTRIBIBPT,
			$CDCLIENTE,
			$CDCONSUMIDOR,
			$NRVENDAREST,
			$NRCOMANDA,
			$NRMESA,
			$IDORIGEMVENDA,
			$VRTXSEVENDA,
			$NRPESMESAVENDA,
			$DSCOMANDA,
			$FAMSALDOPROD,
            $TOTALVENDALIQ,
            $DTHRMESAFECHVEN,
            $FIDELITYDISCOUNT,
            $motivoDesconto,
            $CDGRPOCORDESC,
            $SUBSIDY,
            $DSOBSFINVEN,
            $ITVENDADES,
            $DELIVERY,
            $REPIQUE
		);


		$result = array(
			'error' => false,
			'message' => '',
            'messageCurl' => '',
			'NRSEQVENDA' => $respostaVenda['NRSEQVENDA'],
			'mensagemImpressao' => '',
			'dadosImpressao' => array(),
            'resultMail' => null
		);
		if (!$simulateSaleValidation) {
			$valorVenda = $this->calculaValorDaVenda($ITEMVENDA);

            if ($valorVenda['total'] <= 0 && $valorVenda['totalCancelamento'] > 0) {
				$this->cancelaVenda($CDFILIAL, $CDCAIXA, $NRORG, $respostaVenda['NRSEQVENDA'], $CDOPERADOR, 'R', null);
			} else if ($IDTPEMISVEND === self::TIPO_SAT) {
				$respostaTransmissao = $this->sat->vendaSAT($CDFILIAL, $CDCAIXA, $respostaVenda['NRSEQVENDA'], $NRORG, $NRINSCRCONS, $NMCONSVEND, $CDSENHAPED, $CDOPERADOR, $VRTOTTRIBIBPT, $impostoEstadual, $imprimeCupom, $FIDELITYVALUE, $NRMESA);
				$this->geraLog(' -Transmissão SAT: ' . ($respostaTransmissao['error'] ? 'com' : 'sem') . ' erro.', $CDFILIAL, $CDCAIXA, $DTVENDA);
				if (!$respostaTransmissao['error']) {
					$result['dadosImpressao'] = $respostaTransmissao['dadosImpressao'];
					$result['mensagemImpressao'] = $respostaTransmissao['mensagemImpressao'];
					$result['errPainelSenha'] = $respostaTransmissao['errPainelSenha'];
				} else {
					$result = $respostaTransmissao;
				}
			} else if ($IDTPEMISVEND === self::TIPO_NFCE) {
				$respostaTransmissao = $this->notaFiscalService->transmitirNFCe($NRORG, $CDCAIXA, $CDFILIAL, $DTABERCAIX, $CDLOJA, $CDOPERADOR, $IDTPEMISVEND, $respostaVenda['NRSEQVENDA'], $VRTOTTRIBIBPT, $TOTALVENDALIQ, $imprimeCupom, $FIDELITYVALUE, $NRMESA);
				$this->geraLog(' -Transmissão NFCE: ' . ($respostaTransmissao['error'] ? 'com' : 'sem') . ' erro.', $CDFILIAL, $CDCAIXA, $DTVENDA);
				if (!$respostaTransmissao['error']) {
					$result['dadosImpressao'] = $respostaTransmissao['dadosImpressao'];
					$result['mensagemImpressao'] = $respostaTransmissao['mensagemImpressao'];
					$result['errPainelSenha'] = $respostaTransmissao['errPainelSenha'];
					$result['IDSTATUSNFCE'] = $respostaTransmissao['IDSTATUSNFCE'];
					$result['mensagemNfce'] = $respostaTransmissao['mensagemNfce'];
					// Parametro para impressão de nota fiscal no SAAS.
					$result['paramsImpressora'] = $respostaTransmissao['paramsImpressora'];
				} else {
					$this->geraLog('Erro: ' . $respostaTransmissao['message'], $CDFILIAL, $CDCAIXA, $DTVENDA);
					$result = $respostaTransmissao;
				}
			}

            // Insere na EXTRATOCONs Online caso tenha vendido crédito pessoal.
            try {
                if (!empty($respostaVenda['EXTRATOCONSPARAMS'])){
                    foreach($respostaVenda['EXTRATOCONSPARAMS'] as $params){
                        $this->extratocons->insereExtratocons($params);
                    }
                }
            } catch(\Exception $e){
                $result['messageCurl'] = 'Não houve retorno do EXTRATOCONS ONLINE. Saldo do consumidor pode não ter sido atualizado.';
            }

			if (!empty($result['mensagemImpressao'])){
				$result['mensagemImpressao'] = 'Ocorreu um problema na impressão da nota fiscal. ' . $result['mensagemImpressao'] . '<br>';
			}
		}
		if ($result['error']) {
			$respostaCancelamento = $this->cancelaVenda($CDFILIAL, $CDCAIXA, $NRORG, $respostaVenda['NRSEQVENDA'], $CDOPERADOR, 'R', null);
			if ($respostaCancelamento['error']) {
				$result['message'] = $result['message'] . ' - ' . $respostaCancelamento['message'];
			}
		} else if (isset($EMAIL)) {
			$resultMail = $this->util->sendEmailVenda($EMAIL, $respostaTransmissao, $DTVENDA, $IDTPEMISVEND);
            $result['resultMail'] = $resultMail;
        }

		return $result;
	}

	private function validaVendaComanda($CDFILIAL, $dadosMesa, $arrayPosicoes, $CDLOJA, $VRDESCVENDA, $VRTXSEVENDA, $CDCLIENTE, $DELIVERY, $VRACRCOMANDA, $TOTALPRODUTOS, $REPIQUE = 0) {
		$TOTALVENDA = self::buscaTotalComanda($CDFILIAL, $dadosMesa, $arrayPosicoes);
		$ITVENDADES = self::montaItemsDesistencia($CDFILIAL, $dadosMesa[0]['NRVENDAREST']);
		$ITEMVENDA = self::montaItemVenda($CDFILIAL, $dadosMesa, $arrayPosicoes, $CDLOJA, $VRDESCVENDA, $TOTALVENDA, $VRTXSEVENDA, $CDCLIENTE, $DELIVERY, $VRACRCOMANDA, $REPIQUE);
		$valorVenda = $this->calculaValorDaVenda($ITEMVENDA);
		$totalProdutos = $valorVenda['total'];
		$totalCancelamento = $valorVenda['totalCancelamento'];

        $VALORVENDAFRONT = round($TOTALPRODUTOS - $VRDESCVENDA, 2);
		if (count($ITEMVENDA) == 0) {
			$validacaoVenda = array(
				'error' => true,
				'message' => 'Não foram encontrados produtos para esta venda.'
			);
		} else if ($totalProdutos <= 0 && $totalCancelamento <= 0) {
			$validacaoVenda = array(
				'error' => true,
				'message' => 'O total da venda deve ser maior que 0.'
			);
		} else if (round($totalProdutos, 2) != $VALORVENDAFRONT){
			$VALORVENDAFRONT = str_replace('.', ',', number_format($VALORVENDAFRONT, 2));
			$totalProdutos = str_replace('.', ',', number_format($totalProdutos, 2));
            $validacaoVenda = array(
                'error' => true,
                'message' => 'Valor total da venda ('. $VALORVENDAFRONT .') difere da soma dos preços dos produtos (' . $totalProdutos . ').'
            );
        } else {
			$validacaoVenda = array(
				'error' => false,
				'ITEMVENDA' => $ITEMVENDA,
                'ITVENDADES' => $ITVENDADES,
				'TOTALVENDALIQ' => $totalProdutos,
				'TOTALVENDABRT' => $TOTALVENDA,
                'SUBSIDY' => $valorVenda['subsidy']
			);
		}
		return $validacaoVenda;
	}

	private function buscaTotalComanda($CDFILIAL, $dadosMesa, $arrayPosicoes) {
		$total = 0;
		foreach($dadosMesa as $mesaAtual) {
			$params = array(
	            'CDFILIAL' => $CDFILIAL,
	            'NRVENDAREST' => $mesaAtual['NRVENDAREST'],
	            'NRCOMANDA' => $mesaAtual['NRCOMANDA']
			);
			$totalPorPosicao = $this->entityManager->getConnection()->fetchAll("CALCULA_TOTAL_COMANDA", $params);
			$totalPorPosicaoFiltrado = self::filtraItensPorPosicoes($totalPorPosicao, $arrayPosicoes);
			$total += self::totalizaMesa($totalPorPosicaoFiltrado);
		}
		return $total;
	}

	private function totalizaMesa($totalPorPosicao) {
		$total = 0;
		foreach ($totalPorPosicao as $totalAtual) {
			$total += $totalAtual['TOTAL'];
		}
		return $total;
	}

	private function buscaDadosTaxaServico($CDFILIAL, $CDLOJA) {
		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'CDLOJA' => $CDLOJA
		);
		return $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_TAXA_SERVICO", $params);
	}

    private function montaItemVenda($CDFILIAL, $dadosMesa, $arrayPosicoes, $CDLOJA, $VRDESCVENDA, $TOTALVENDA, $VRTXSEVENDA = null, $CDCLIENTE, $DELIVERY, $VRACRCOMANDA = null, $REPIQUE = 0){
    	$produtos = self::buscaItensPedidos($CDFILIAL, $CDLOJA, $dadosMesa, $arrayPosicoes);
    	$observacoes = self::buscaItensPedidosObs($CDFILIAL, $dadosMesa);
		$dadosTaxa = $this->buscaDadosTaxaServico($CDFILIAL, $CDLOJA);
    	$itensTratados = array();

    	$PRODTX = 0; // calcula total dos produtos que entram na taxa de serviço
    	$PRODTXENTREGA = 0; // calcula total dos produtos que entram na taxa de entrega
    	foreach ($produtos as $produto) {
    		if ($produto['IDSTPRCOMVEN'] != '6'){
    			$vrProduto = self::calculaTotalItem($produto);

	    		if ($produto['IDCOBTXSERV'] == 'S'){
	    			$PRODTX += $vrProduto;
	    		}
    			$PRODTXENTREGA += $vrProduto;
    		}
    	}
    	$totalRateioDesconto = 0;
        $totalRateioTaxa = 0;
        $totalRateioTaxaEntrega = 0;
        //Trecho abaixo valida se mesa ainda esta em aberto
		$params = array(
			$CDFILIAL,
			$dadosMesa[0]['NRCOMANDA'],
			$dadosMesa[0]['NRVENDAREST'],
			$CDLOJA
		);

		if(!$DELIVERY){
			$validaMesa = $this->entityManager->getConnection()->fetchAssoc("SQL_DADOS_MESA", $params);
			if ($validaMesa == false) {
				throw new \Exception('Mesa não encontrada.', 1);
			}
		}

    	foreach ($produtos as $produtoAtual) {
    		$valorDescontoAtual = 0;
    		if ($VRDESCVENDA > 0 && $produtoAtual['IDSTPRCOMVEN'] != '5' &&
    			$produtoAtual['IDSTPRCOMVEN'] != '6' && $produtoAtual['IDSTPRCOMVEN'] != '7'){
	    		$valorDescontoAtual = $this->rateiaDesconto($produtoAtual, $VRDESCVENDA, $TOTALVENDA);
	    		$totalRateioDesconto += $valorDescontoAtual;
    		}
    		$novoItem = array();
            $novoItem['NRPRODCOMVEN'] = $produtoAtual['NRPRODCOMVEN'];
    		$novoItem['NMPRODUTO'] = $produtoAtual['NMPRODUTO'];
			$novoItem['CDPRODUTO'] = $produtoAtual['CDPRODUTO'];
			$novoItem['QTPRODVEND'] = $produtoAtual['QTPRODCOMVEN'];
			$novoItem['VRUNITVEND'] = $produtoAtual['VRPRECCOMVEN'];
            $novoItem['VRUNITVENDCL'] = $produtoAtual['VRPRECCLCOMVEN'];
			$novoItem['VRDESITVEND'] = (float)$produtoAtual['VRDESCCOMVEN'] + $valorDescontoAtual;
			$novoItem['VRACRITVEND'] = (float)$produtoAtual['VRACRCOMVEN'];
			if ($dadosTaxa['IDCOMISVENDA'] != 'N' && $dadosTaxa['IDTRATTAXASERV'] === 'A' && $produtoAtual['IDCOBTXSERV'] == 'S' && $produtoAtual['IDSTPRCOMVEN'] != '5' && $produtoAtual['IDSTPRCOMVEN'] != '6' && $produtoAtual['IDSTPRCOMVEN'] != '7'){
				if (isset($VRTXSEVENDA) && $VRTXSEVENDA > 0){
					$taxa = $VRTXSEVENDA / $PRODTX;
				//something wrong happened where
				} else if ($VRTXSEVENDA <= 0) {
					$taxa = 0;
				} else {
					$taxa = floatval($dadosTaxa['VRCOMISVENDA'])/100;
				}
                $acrescimo = floatval(bcmul(str_replace(',','.',strval(self::calculaTotalItem($produtoAtual))), str_replace(',','.',strval($taxa)), '2'));
				$novoItem['VRACRITVEND'] += $acrescimo;
                $totalRateioTaxa += $acrescimo;
			}
			if ($dadosTaxa['IDTRATTAXAENTR'] === 'A' && $produtoAtual['IDSTPRCOMVEN'] != '5' && $produtoAtual['IDSTPRCOMVEN'] != '6' && $produtoAtual['IDSTPRCOMVEN'] != '7'){
				if(isset($VRTXSEVENDA) && $VRACRCOMANDA > 0){
					$taxa = $VRACRCOMANDA / $PRODTXENTREGA;
				}else{
					$taxa = 0;
				}
				$acrescimo = floatval(bcmul(str_replace(',','.',strval(self::calculaTotalItem($produtoAtual))), str_replace(',','.',strval($taxa)), '2'));
				$novoItem['VRACRITVEND'] += $acrescimo;
                $totalRateioTaxaEntrega += $acrescimo;
			}

			$novoItem['IDSITUITEM'] = $produtoAtual['IDSTPRCOMVEN'] !== self::ITEM_CANCELADO ? self::ITEM_APROVADO : self::ITEM_CANCELADO;
			$novoItem['IDTIPOITEM'] = null;
			$novoItem['OBSERVACOES'] = self::filtraObservacoes($observacoes, $produtoAtual);
			$novoItem['IDTIPOCOMPPROD'] = '0';
			$novoItem['IDIMPPRODUTO'] = '1';
			$novoItem['CDGRPOCOR'] = $produtoAtual['CDGRPOCOR'];
			$novoItem['DSOBSITEMVENDA'] = $produtoAtual['TXPRODCOMVEN'];
			$novoItem['DSOBSPEDDIGITA'] = $produtoAtual['DSOBSPEDDIGCMD'];
			$novoItem['CDSUPERVISOR'] = $produtoAtual['CDSUPERVISOR'];
			$novoItem['DTHRINCOMVEN'] = $produtoAtual['DTHRINCOMVEN'];
			$novoItem['NRVENDAREST'] = $produtoAtual['NRVENDAREST'];
            $novoItem['NRCOMANDA'] = $produtoAtual['NRCOMANDA'];
            $novoItem['IDCOBTXSERV'] = $produtoAtual['IDCOBTXSERV'];
            $novoItem['PRECOFINAL'] = self::getItemPrice($novoItem, '0');
            $novoItem['IDPRODPRODUZC'] = $produtoAtual['IDPRODPRODUZ'];
            $novoItem['DSOBSDESCIT'] = $produtoAtual['DSOBSDESCIT'];
            $novoItem['IDORIGEMVENDA'] = $produtoAtual['IDORIGEMVENDA'];
            $novoItem['CDGRPOCORDESCIT'] = $produtoAtual['CDGRPOCORDESCIT'];
            $novoItem['CDVENDEDOR'] = $produtoAtual['CDVENDEDOR'];
            $novoItem['DTHRPRODCANVEN'] = $produtoAtual['DTHRPRODCANVEN'];
            $novoItem['CDPRODPROMOCAO'] = $produtoAtual['CDPRODPROMOCAO'];
            $novoItem['NRLUGARMESA'] = $produtoAtual['NRLUGARMESA'];
            $novoItem['VOUCHER'] = null;
            $novoItem['CDCAMPCOMPGANHE'] = $produtoAtual['CDCAMPCOMPGANHE'];
            $novoItem['DTINIVGCAMPCG'] = $produtoAtual['DTINIVGCAMPCG'];
			array_push($itensTratados, $novoItem);
		}
		// ajuda diferença do desconto quando descontoVenda > descontoTotalRateado
		if ($totalRateioDesconto < $VRDESCVENDA) {
            $diferenca = round($VRDESCVENDA - $totalRateioDesconto, 2);
            $this->ajustaDiferenca($itensTratados, $diferenca, 'VRDESITVEND');
        }

        if ($totalRateioTaxa < $VRTXSEVENDA && $dadosTaxa['IDTRATTAXASERV'] === 'A') {
            $diferenca = round($VRTXSEVENDA - $totalRateioTaxa, 2);
            $this->ajustaDiferenca($itensTratados, $diferenca, 'VRACRITVEND');
        }

		if ($totalRateioTaxaEntrega < $VRACRCOMANDA && $dadosTaxa['IDTRATTAXAENTR'] === 'A') {
            $diferenca = round($VRACRCOMANDA - $totalRateioTaxaEntrega, 2);
            $this->ajustaDiferenca($itensTratados, $diferenca, 'VRACRITVEND', true);
        }

        $produtoTaxaServico = self::adicionaProdutoTaxaServico($dadosTaxa, $produtos[0]['NRVENDAREST'], $produtos[0]['NRCOMANDA'], $REPIQUE, $VRTXSEVENDA);
        if (!empty($produtoTaxaServico)) {
			array_push($itensTratados, $produtoTaxaServico);
        }

		if ($dadosTaxa['IDTRATTAXAENTR'] === 'P' && $dadosTaxa['CDPRODTAXAENTR'] !== null
												 && $VRACRCOMANDA > 0){
			$paramProdutoEntrega = array(
				'CDPRODUTO'		=> $dadosTaxa['CDPRODTAXAENTR']
			);
			$produtoTaxaEntrega = $this->entityManager->getConnection()->fetchAssoc("PRODUTO", $paramProdutoEntrega);
			$itemTaxa = array(
                'NRPRODCOMVEN'   => null,
				'NMPRODUTO'      => $produtoTaxaEntrega['NMPRODUTO'],
				'CDPRODUTO'      => $dadosTaxa['CDPRODTAXAENTR'],
				'QTPRODVEND'     => 1,
				'VRUNITVEND'     => 0,
                'VRUNITVENDCL'   => 0,
				'VRDESITVEND'    => 0,
				'VRACRITVEND'    => floatval(bcdiv(str_replace(',','.',strval($VRACRCOMANDA)), '1', '2')),
				'IDSITUITEM'     => self::ITEM_APROVADO,
				'IDTIPOITEM'     => null,
				'OBSERVACOES'    => array(),
				'IDTIPOCOMPPROD' => '0',
				'IDIMPPRODUTO'   => '1',
				'CDGRPOCOR'      => null,
				'DSOBSITEMVENDA' => null,
				'DSOBSPEDDIGITA' => null,
				'CDSUPERVISOR'   => null,
				'DTHRINCOMVEN'   => new \DateTime(),
				'NRVENDAREST'    => $produtos[0]['NRVENDAREST'],
                'NRCOMANDA'      => $produtos[0]['NRCOMANDA'],
				'IDPRODPRODUZC'  => null,
				'DSOBSDESCIT' 	 => null,
                'IDORIGEMVENDA'  => null,
				'CDGRPOCORDESCIT'=> null,
				'CDVENDEDOR'     => null,
                'CDPRODPROMOCAO' => null,
                'VOUCHER'        => null,
                'CDCAMPCOMPGANHE' => null,
                'DTINIVGCAMPCG' => null
			);
			$itemTaxa['PRECOFINAL'] = self::getItemPrice($itemTaxa, '0');
			array_push($itensTratados, $itemTaxa);
		}
    	return $itensTratados;
	}

	private function rateiaDesconto($produtoAtual, $VRDESCVENDA, $TOTALVENDA) {
		$descontoItemAtual = 0;
		$valorTotalItem = $this->calculaTotalItem($produtoAtual);
		$descontoItemAtual = floatval(bcdiv(str_replace(',','.',strval(($valorTotalItem / $TOTALVENDA) * $VRDESCVENDA)), '1', '2'));
		return $descontoItemAtual;
	}

	private function calculaTotalItem($produtoAtual) {
		if (isset($produtoAtual['VRDESITVEND'])){
			return round(floatval(bcdiv(str_replace(',','.',strval(($produtoAtual['VRUNITVEND'] + $produtoAtual['VRUNITVENDCL']) * $produtoAtual['QTPRODVEND'])), '1', '2')) + $produtoAtual['VRACRITVEND'] - $produtoAtual['VRDESITVEND'], 2);
		} else {
			return round(floatval(bcmul(str_replace(',','.',strval(($produtoAtual['VRPRECCOMVEN'] + $produtoAtual['VRPRECCLCOMVEN']))), str_replace(',','.',strval($produtoAtual['QTPRODCOMVEN'])), '2')) + $produtoAtual['VRACRCOMVEN'] - $produtoAtual['VRDESCCOMVEN'], 2);
		}
	}

	private function ajustaDiferenca(&$produtos, $diferenca, $modo, $txentrega = false){
		$qtdRateio = intval($diferenca / 0.01);
        $i = 0;
		while ($qtdRateio > 0) {
			$qtdRateio = self::alteraValor($produtos, $qtdRateio, $modo, $txentrega);
            $i++;
            if ($i == 1000) throw new \Exception("Erro no rateio do desconto/gorjeta.");
		}
	}

	private function alteraValor(&$produtos, $qtdRateio, $modo, $txentrega){
		foreach ($produtos as &$produtoAtual) {
            if (($modo == 'VRDESITVEND' || $produtoAtual['IDCOBTXSERV'] == 'S' || $txentrega) && $produtoAtual['IDSITUITEM'] != '6'){
    			if (($produtoAtual['IDTIPOCOMPPROD'] == '3' && $produtoAtual['IDIMPPRODUTO'] == '2') || $produtoAtual['IDTIPOCOMPPROD'] == 'C'){
    				foreach ($produtoAtual['itensCombo'] as &$itemComboAtual) {
    					$totalProduto = self::calculaTotalItem($itemComboAtual);
    					if ($itemComboAtual[$modo] >= 0.01 && $totalProduto > 0.01){
    						$itemComboAtual[$modo] = $itemComboAtual[$modo] + 0.01;
    						$itemComboAtual['PRECOFINAL'] = self::getItemPrice($itemComboAtual, $produtoAtual['IDTIPOCOMPPROD']);
    						$qtdRateio--;
    					}
    					if ($qtdRateio == 0) {
    						break 2;
    					}
    				}
    			} else {
    				$totalProduto = self::calculaTotalItem($produtoAtual);
    				if ($produtoAtual[$modo] >= 0.01 && $produtoAtual['IDSITUITEM'] != '6' && $totalProduto > 0.01){
    					$produtoAtual[$modo] = $produtoAtual[$modo] + 0.01;
    					$produtoAtual['PRECOFINAL'] = self::getItemPrice($produtoAtual, $produtoAtual['IDTIPOCOMPPROD']);
    					$qtdRateio--;
    				}
    				if ($qtdRateio == 0) {
    					break;
    				}
    			}
            }
		}

		return $qtdRateio;
	}

    private function filtraObservacoes($observacoes, $produtoAtual) {
    	$arrayFiltrado = array();
    	foreach ($observacoes as $obsAtual) {
    		if (($obsAtual['NRVENDAREST'] == $produtoAtual['NRVENDAREST']) &&
    		   ($obsAtual['NRCOMANDA'] == $produtoAtual['NRCOMANDA']) &&
    		   ($obsAtual['NRPRODCOMVEN'] == $produtoAtual['NRPRODCOMVEN'])) {

    			array_push($arrayFiltrado, $obsAtual);
    		}
    	}
    	return $arrayFiltrado;
    }

    public function buscaItensPedidos($CDFILIAL, $CDLOJA, $dadosMesa, $arrayPosicoes) {
    	$arrayItens = array();
    	foreach ($dadosMesa as $mesaAtual) {
			$params = array(
	            'CDFILIAL' => $CDFILIAL,
	            'NRVENDAREST' => $mesaAtual['NRVENDAREST'],
	            'NRCOMANDA' => $mesaAtual['NRCOMANDA']
			);
			$itensMesaAtual = $this->entityManager->getConnection()->fetchAll("BUSCA_ITENS_PEDIDOS", $params);
            $itensMesaAtual = $this->precoService->subgroupDiscountTableInterface($itensMesaAtual, $CDFILIAL, $CDLOJA);
			$arrayItens = array_merge($arrayItens, $itensMesaAtual);
    	}
    	$arrayItens = self::filtraItensPorPosicoes($arrayItens, $arrayPosicoes);
    	return $arrayItens;
	}

	private function filtraItensPorPosicoes($arrayItens, $arrayPosicoes) {
		if (!empty($arrayPosicoes)) {
			$itensFiltrados = array();
			foreach ($arrayItens as $itemAtual) {
				foreach ($arrayPosicoes as $posicaoAtual) {
					if ($itemAtual['NRLUGARMESA'] == $posicaoAtual) {
						array_push($itensFiltrados, $itemAtual);
					}
				}
			}
		} else {
			$itensFiltrados = $arrayItens;
		}
		return $itensFiltrados;
	}

	private function buscaItensPedidosObs($CDFILIAL, $dadosMesa) {
		$arrayObs = array();
		foreach ($dadosMesa as $mesaAtual) {
			$params = array(
	            'CDFILIAL' => $CDFILIAL,
	            'NRVENDAREST' => $mesaAtual['NRVENDAREST'],
	            'NRCOMANDA' => $mesaAtual['NRCOMANDA']
			);
			$obsMesaAtual = $this->entityManager->getConnection()->fetchAll("BUSCA_ITENS_PEDIDOS_OBS", $params);
			$arrayObs = array_merge($arrayObs, $obsMesaAtual);
		}
		return $arrayObs;
	}

	private function adicionaObsCobrada(&$ITEMVENDA){
		// função utilizada para adicionar as observações cobradas no array normal de produto
		$produtosObs = array();

		foreach ($ITEMVENDA as $item) {
            $IDORIGEMVENDA = !empty($item['IDORIGEMVENDA']) ? $item['IDORIGEMVENDA'] : null;
			// verifica observação dos itens pai
			foreach ($item['OBSERVACOES'] as $obsAtual) {
				$obsCobrada = self::buscaProdutoObs($obsAtual['CDGRPOCOR'], $obsAtual['CDOCORR']);
				if ($obsCobrada) {
					$QTPRODVEND = $item['QTPRODVEND'] * (!empty($obsAtual['QTPRODVEND']) ? $obsAtual['QTPRODVEND'] : 1);
					array_push($produtosObs, self::montaItemObs($obsCobrada, $QTPRODVEND, $IDORIGEMVENDA, $item['CDVENDEDOR']));
				}
			}

			// verifica observação dos itens filhos (promoção ou produto combinado)
			foreach ($item['itensCombo'] as $itensCombo) {
				foreach ($itensCombo['OBSERVACOES'] as $obsAtual) {
					$obsCobrada = self::buscaProdutoObs($obsAtual['CDGRPOCOR'], $obsAtual['CDOCORR']);
					if ($obsCobrada) {
						$QTPRODVEND = $item['QTPRODVEND'] * (!empty($obsAtual['QTPRODVEND']) ? $obsAtual['QTPRODVEND'] : 1);
						array_push($produtosObs, self::montaItemObs($obsCobrada, $QTPRODVEND, $IDORIGEMVENDA, $item['CDVENDEDOR']));
					}
				}
			}
		}

		$ITEMVENDA = array_merge($ITEMVENDA, $produtosObs);
	}

	private function montaItemObs($CDPRODUTO, $QTPRODVEND, $IDORIGEMVENDA, $CDVENDEDOR) {
		return array(
            'CDPRODUTO' => $CDPRODUTO,
    		'QTPRODVEND' => $QTPRODVEND,
    		'QTPRODCOMVEN' => $QTPRODVEND,
    		'VRUNITVEND' => 0, // preços adicionados posteriormente
    		'VRUNITVENDCL' => 0,
    		'REALSUBSIDY' => 0,
			'VRDESITVEND' => 0,
    		'VRACRITVEND' => 0,
    		'IDTIPOCOMPPROD' => '0',
    		'IDIMPPRODUTO' => '1',
    		'IDSITUITEM' => self::ITEM_APROVADO,
    		'IDTIPOITEM' => null,
    		'DSOBSPEDDIGITA' => null,
    		'DSOBSITEMVENDA' => null,
    		'CDSUPERVISOR' => null,
    		'CDGRPOCOR' => null,
    		'OBSERVACOES' => array(),
    		'DSOBSDESCIT' => null,
    		'CDGRPOCORDESCIT' => null,
            'IDORIGEMVENDA' => $IDORIGEMVENDA,
    		'itensCombo' => array(),
    		'CDVENDEDOR' => $CDVENDEDOR,
            'CDPRODPROMOCAO' => null,
            'VOUCHER' => null,
            'CDCAMPCOMPGANHE' => null,
            'DTINIVGCAMPCG' => null
		);
	}

	private function montaPrecosProdutos($TOTALPRODUTOS, $ITEMVENDA, $VRDESCVENDA, $CDFILIAL, $CDLOJA, $CDCLIENTE, $CDCONSUMIDOR) {
		$retorno = array(
			'error' => false,
			'produtosComPreco' => array()
		);

		$totalRateioDesconto = 0;
		foreach ($ITEMVENDA as &$ITEM) {
			if (($ITEM['IDTIPOCOMPPROD'] === '3' && $ITEM['IDIMPPRODUTO'] === '2') || $ITEM['IDTIPOCOMPPROD'] === 'C') {
				// itens do combo
				foreach ($ITEM['itensCombo'] as &$itemComboAtual) {
					$produtoDefinido = self::definePrecoProduto($itemComboAtual, $CDFILIAL, $CDLOJA, $CDCLIENTE, $CDCONSUMIDOR);

					if (!$produtoDefinido['error']){
						$itemComboAtual = $produtoDefinido['produto'];
                        $itemComboAtual['VRACRITVEND'] = floatval(bcmul(str_replace(',','.',strval($itemComboAtual['VRACRITVEND'])), str_replace(',','.',strval($itemComboAtual['QTPRODCOMVEN'])), '2'));
                        $itemComboAtual['VRDESITVEND'] = floatval(bcmul(str_replace(',','.',strval($itemComboAtual['VRDESITVEND'])), str_replace(',','.',strval($itemComboAtual['QTPRODCOMVEN'])), '2'));
						if ($VRDESCVENDA > 0){
							$valorDescontoAtual = $this->rateiaDesconto($itemComboAtual, $VRDESCVENDA, $TOTALPRODUTOS);
							$itemComboAtual['VRDESITVEND'] += $valorDescontoAtual;
				    		$totalRateioDesconto += $valorDescontoAtual;
						}
                        $itemComboAtual['PRECOFINAL'] = self::getItemPrice($itemComboAtual, $ITEM['IDTIPOCOMPPROD']);
                        // Aplica o desconto do voucher.
                        if (!empty($itemComboAtual['VOUCHER'])){
                            if ($itemComboAtual['VOUCHER']['IDTIPODESC'] === "P"){
                                $voucherDiscount = bcmul(strval($itemComboAtual['PRECOFINAL']), strval($itemComboAtual['VOUCHER']['VRDESCCUPOM']/100), 2);
                            }
                            else {
                                $voucherDiscount = $itemComboAtual['VOUCHER']['VRDESCCUPOM'];
                            }
                            $itemComboAtual['VRDESITVEND'] += floatval($voucherDiscount);
                            if ($itemComboAtual['VRDESITVEND'] >= $itemComboAtual['PRECOFINAL']) $itemComboAtual['VRDESITVEND'] = $itemComboAtual['PRECOFINAL'] - 0.01 * $itemComboAtual['QTPRODCOMVEN'];
                            $itemComboAtual['PRECOFINAL'] = self::getItemPrice($itemComboAtual, $ITEM['IDTIPOCOMPPROD']);
                        }
					} else {
						$retorno = $produtoDefinido;
						break 2;
					}
				}
			} else {
				// produto normal
				$produtoDefinido = self::definePrecoProduto($ITEM, $CDFILIAL, $CDLOJA, $CDCLIENTE, $CDCONSUMIDOR);

				if (!$produtoDefinido['error']){
					$ITEM = $produtoDefinido['produto'];
                    $ITEM['VRACRITVEND'] = floatval(bcmul(str_replace(',','.',strval($ITEM['VRACRITVEND'])), str_replace(',','.',strval($ITEM['QTPRODCOMVEN'])), '2'));
                    $ITEM['VRDESITVEND'] = floatval(bcmul(str_replace(',','.',strval($ITEM['VRDESITVEND'])), str_replace(',','.',strval($ITEM['QTPRODCOMVEN'])), '2'));
					if ($VRDESCVENDA > 0){
						$valorDescontoAtual = $this->rateiaDesconto($ITEM, $VRDESCVENDA, $TOTALPRODUTOS);
						$ITEM['VRDESITVEND'] += $valorDescontoAtual;
			    		$totalRateioDesconto += $valorDescontoAtual;
					}
                    $ITEM['PRECOFINAL'] = self::getItemPrice($ITEM, $ITEM['IDTIPOCOMPPROD']);
                    // Aplica o desconto do voucher.
                    if (!empty($ITEM['VOUCHER'])){
                        if ($ITEM['VOUCHER']['IDTIPODESC'] === "P"){
                            $voucherDiscount = bcmul(strval($ITEM['PRECOFINAL']), strval($ITEM['VOUCHER']['VRDESCCUPOM']/100), 2);
                        }
                        else {
                            $voucherDiscount = $ITEM['VOUCHER']['VRDESCCUPOM'];
                        }
                        $ITEM['VRDESITVEND'] += floatval($voucherDiscount);

                        $total = round(floatval(bcdiv(str_replace(',','.',strval(($ITEM['VRUNITVEND'] + $ITEM['VRUNITVENDCL']) * $ITEM['QTPRODVEND'])), '1', '2')) + $ITEM['VRACRITVEND'], 2);
                        if ($ITEM['VRDESITVEND'] >= $total){
                            $ITEM['VRDESITVEND'] = floatval(bcsub(strval($total), strval($ITEM['QTPRODCOMVEN'] * 0.01), 2));
                        }
                        $ITEM['PRECOFINAL'] = self::getItemPrice($ITEM, $ITEM['IDTIPOCOMPPROD']);
                    }
				} else {
					$retorno = $produtoDefinido;
					break;
				}
			}
		}

		if (!$retorno['error']) {
			// ajuda diferença do desconto quando descontoVenda > descontoTotalRateado
			if ($totalRateioDesconto < $VRDESCVENDA) {
				$diferenca = round($VRDESCVENDA - $totalRateioDesconto, 2);
				$this->ajustaDiferenca($ITEMVENDA, $diferenca, 'VRDESITVEND');
			}

			$retorno['produtosComPreco'] = $ITEMVENDA;
		}
		return $retorno;
	}

    private function getItemPrice($ITEM, $IDTIPOCOMPPROD){
    	if ($IDTIPOCOMPPROD != 'C'){
        	return round(floatval(bcdiv(str_replace(',','.',strval(($ITEM['VRUNITVEND'] + $ITEM['VRUNITVENDCL']) * $ITEM['QTPRODVEND'])), '1', '2')) + $ITEM['VRACRITVEND'] - $ITEM['VRDESITVEND'], 2);
    	} else {
    		$preco = floatval(number_format(floatval(bcmul(str_replace(',','.',strval(($ITEM['VRUNITVEND'] + $ITEM['VRUNITVENDCL']))), str_replace(',','.',strval($ITEM['QTPRODCOMVEN'])), '2')) - $ITEM['VRDESITVEND'] + $ITEM['VRACRITVEND'], 2, '.', ''));
	        if ($preco <= 0) $preco = 0.01;
	        return $preco;
    	}
    }

	private function definePrecoProduto($produto, $CDFILIAL, $CDLOJA, $CDCLIENTE, $CDCONSUMIDOR){
		$result = array(
			'error' => false,
			'produto' => null
		);

		if (empty($produto['VRUNITVEND']) || !empty($produto['VOUCHER'])){
			$precoItem = $this->precoService->buscaPreco($CDFILIAL, $CDCLIENTE, $produto['CDPRODUTO'], $CDLOJA, $CDCONSUMIDOR);
			if (!$precoItem['error']) {
				$produto['VRUNITVEND'] = $precoItem['PRECO'];
                $produto['VRUNITVENDCL'] = $precoItem['PRECOCLIE'];
                $produto['VRDESITVEND'] = $precoItem['DESC'];
                $produto['VRACRITVEND'] = $precoItem['ACRE'];

				$result['produto'] = $produto;
			} else {
				$result = $precoItem;
			}
		} else {
			$result['produto'] = $produto;
		}

		return $result;
	}

	public function cancelaVenda($CDFILIAL, $CDCAIXA, $NRORG, $NRSEQVENDA, $CDOPERADOR, $IDSTATUSNFCE, $CDSUPERVISOR){
		// IDSTATUSNFCE - R (recusado) / C (cancelado)
		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'CDCAIXA' => $CDCAIXA,
			'NRORG' => $NRORG,
			'NRSEQVENDA' => $NRSEQVENDA
		);
		$venda = $this->entityManager->getConnection()->fetchAll("BUSCA_VENDA", $params);
		$IDIMPVENDA = $venda[0]['IDIMPVENDA'] == 'I' ? 'I' : 'A';

		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'CDCAIXA' => $CDCAIXA,
			'NRORG' => $NRORG,
			'NRSEQVENDA' => $NRSEQVENDA,
			'CDOPERULTATU' => $CDOPERADOR,
			'IDIMPVENDA' => $IDIMPVENDA,
			'IDSITUVENDA' => 'C',
			'IDSTATUSNFCE' => $IDSTATUSNFCE,
			'CDSUPERVISOR' => $CDSUPERVISOR
		);
		$this->entityManager->getConnection()->executeQuery("CANCELA_VENDA", $params);

        $params = array(
            'CDFILIAL' => $CDFILIAL,
            'CDCAIXA' => $CDCAIXA,
            'NRSEQVENDA' => $NRSEQVENDA
        );
        $movExtrato = $this->entityManager->getConnection()->fetchAll("BUSCA_MOVI_EXTRATOCONS", $params);

        $NRSEQMOVEXT = 1;
        foreach ($movExtrato as $family){

            if ($this->databaseUtil->databaseIsOracle()){
                $DTABERCAIX = \DateTime::createFromFormat('Y-m-d H:i:s', $family['DTABERCAIX']);
            }
            else {
                $DTABERCAIX = \DateTime::createFromFormat('Y-m-d H:i:s.u', $family['DTABERCAIX']);
            }

            $VRSALDCONEXT = floatval($family['VRSALDCONEXT']) + floatval($family['VRMOVEXTCONS']);

            $this->insertExtratoCons(
                $family['CDCLIENTE'],
                $family['CDCONSUMIDOR'],
                $family['CDFAMILISALD'],
                'WTR - Canc. Venda Caixa ' . $CDCAIXA,
                floatval($family['VRMOVEXTCONS']),
                'C',
                $VRSALDCONEXT,
                str_pad($NRSEQMOVEXT++, 3, '0', STR_PAD_LEFT),
                $family['CDTIPORECE'],
                $family['CDFILIAL'],
                $family['CDCAIXA'],
                $NRSEQVENDA,
                $DTABERCAIX,
                null
            );

            $dadosFilial = $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_FILIAL", array('CDFILIAL' => $CDFILIAL));
            if ($dadosFilial['IDEXTCONSONLINE'] === 'S'){
                $params = array(
                    'CDCLIENTE' => $family['CDCLIENTE'],
                    'CDCONSUMIDOR' => $family['CDCONSUMIDOR'],
                    'CDFAMILISALD' => $family['CDFAMILISALD'],
                    'DTMOVEXTCONS' => new \DateTime(),
                    'NRSEQMOVEXT' => str_pad($NRSEQMOVEXT, 3, '0', STR_PAD_LEFT),
                    'CDTIPORECE' => $family['CDTIPORECE'],
                    'IDTPMOVEXT' => 'C',
                    'DSOPEEXTCONS' => 'WTR - Canc. Venda Caixa ' . $CDCAIXA,
                    'VRMOVEXTCONS' => floatval($family['VRMOVEXTCONS']),
                    'VRSALDCONEXT' => $VRSALDCONEXT,
                    'CDFILIAL' => $family['CDFILIAL'],
                    'CDCAIXA' => $family['CDCAIXA'],
                    'NRDEPOSICONS' => null,
                    'NRSEQMOVCAIXA' => null,
                    'IDIMPEXTRATO' => 'S'
                );
                $params['DTMOVEXTCONS'] = $params['DTMOVEXTCONS']->format('d/m/Y H:i:s');
                $this->extratocons->insereExtratocons($params);
            }
            else {
                $this->updateBalance($family['CDCLIENTE'], $family['CDCONSUMIDOR'], $family['CDFAMILISALD'], $VRSALDCONEXT);
            }

        }

		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'CDCAIXA' => $CDCAIXA,
			'CDOPERULTATU' => $CDOPERADOR,
			'NRORG' => $NRORG,
			'NRSEQVENDA' => $NRSEQVENDA,
			'IDTIPOMOVIVE' => 'C'
		);
		$this->entityManager->getConnection()->executeQuery("CANCELA_MOVCAIXA", $params);

		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'CDCAIXA' => $CDCAIXA,
			'NRORG' => $NRORG,
			'NRSEQVENDA' => $NRSEQVENDA
		);
		$MOVICLIE = $this->entityManager->getConnection()->fetchAll("BUSCA_MOVICLIE", $params);

		// Caso venda for do tipo 'DÉBITO CONSUMIDOR'
		if (count($MOVICLIE) > 0) {
			$VRSALDOCONS = $MOVICLIE[0]['VRSALDOCONS'];
			$VRMOVCLI = $MOVICLIE[0]['VRMOVCLI'];
			$VRSALDOCONS = $VRSALDOCONS + $VRMOVCLI;
			$params = array(
				'CDFILIAL' => $CDFILIAL,
				'CDCAIXA' => $CDCAIXA,
				'CDOPERULTATU' => $CDOPERADOR,
				'NRORG' => $NRORG,
				'NRSEQVENDA' => $NRSEQVENDA,
				'IDTIPMOCVLI' => 'X',
				'VRSALDOCONS' => $VRSALDOCONS
			);
			$this->entityManager->getConnection()->executeQuery("CANCELA_MOVICLIE", $params);
		}

		$result = array(
			'error' => false
		);
		return $result;
	}

	private function buscaDadosCaixa($CDFILIAL, $CDCAIXA, $NRORG) {
		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'CDCAIXA' => $CDCAIXA,
			'NRORG' => $NRORG
		);
		return $this->entityManager->getConnection()->fetchAssoc("GET_DADOS_CAIXA", $params);
	}

	private function trunc($val, $f = "2") {
	    if (($p = strpos($val, '.')) !== false) {
	        $val = floatval(substr($val, 0, $p + 1 + $f));
	    }
	    return $val;
	}

	public function calculaImpostoParaImpressao($NRORG, $CDFILIAL, $CDCAIXA, $ITEMVENDA, $TOTALVENDABRT, $VRDESCVENDA, $CDLOJA, $CDCLIENTE, $CDCONSUMIDOR, $VRACRCOMANDA) {
		$params = array(
			'NRORG' => $NRORG,
			'CDFILIAL' => $CDFILIAL
		);
		$IDCUMUPISCOFIL = $this->entityManager->getConnection()->fetchAssoc("GET_IDCUMUPISCOFIL", $params);
		$IDCUMUPISCOFIL = $IDCUMUPISCOFIL['IDCUMUPISCOFIL'];

		$dadosCaixa = $this->buscaDadosCaixa($CDFILIAL, $CDCAIXA, $NRORG);

   		$ICMSTotal = 0;
   		$pis = 0;
   		$cofins = 0;
   		$totalFederal = 0;
   		$totalEstadual = 0;

   		$itensImposto = self::separaItensQueSeraoCobrados($ITEMVENDA);
	    foreach ($itensImposto as $itemAtual) {
	    	$porcDescItem = 0;
			$porcAcreItem = 0;

	    	$TOTALITEM = self::trunc((($itemAtual['VRUNITVEND'] + $itemAtual['VRUNITVENDCL']) * $itemAtual['QTPRODVEND']) -
	    		($dadosCaixa['IDHABCAIXAVENDA'] == 'TAA' ? ($itemAtual['VRDESITVEND'] + $itemAtual['VRACRITVEND']) : 0));
	    	// no FOS, porcDescItem e porcAcreItem apresentam valores sempre zerados (verificar posteriormente)
	    	// if ($TOTALVENDABRT > 0) {
		    //     $porcDescItem = self::trunc(($TOTALITEM * $VRDESCVENDA) / $TOTALVENDABRT);
		    //     $porcAcreItem = self::trunc(($TOTALITEM * $VRACRCOMANDA) / $TOTALVENDABRT);
	    	// }
			$params = array(
				'CDPRODUTO' => $itemAtual['CDPRODUTO'],
				'NRORG' => $NRORG,
				'CDFILIAL' => $CDFILIAL
			);
	    	$impostoProduto = $this->entityManager->getConnection()->fetchAssoc("GET_IMPOSTOS_PRODUTO", $params);
		   	// Real(Nao Cumulativo) = 'S' e Presumido(Cumulativo) = 'N'
	        if ($impostoProduto['IDINCIDEPISCOF'] === 'N'){
		        $VALORPIS = 0.65;
		        $VALORCOFINS = 3;
	        } else {
	         	$VALORPIS = 1.65;
				$VALORCOFINS = 7.60;
	        }

		    $ICMSitem = (($TOTALITEM - $porcDescItem + $porcAcreItem) * ($impostoProduto['VRPEALIMPFIS'] / 100));
		    $ICMSTotal = $ICMSTotal + $ICMSitem;

		    if (($impostoProduto['VRALIQIBPT'] == 0) || (empty($impostoProduto['VRALIQIBPT']))) {
		    	$pis = (($TOTALITEM - $porcDescItem + $porcAcreItem) * ($VALORPIS / 100));
		    	$cofins = (($TOTALITEM - $porcDescItem + $porcAcreItem) * ($VALORCOFINS / 100));
		    	$totalFederal = $totalFederal + $ICMSitem + $pis + $cofins;
		    } else {
		    	$IBPT = (($TOTALITEM - $porcDescItem + $porcAcreItem) * ($impostoProduto['VRALIQIBPT']) / 100);
		    	$totalFederal = $totalFederal + $IBPT;
		    }

		    if (($impostoProduto['VRALIQIBPTES'] == 0) || (empty($impostoProduto['VRALIQIBPTES']))) {
		    	$IBPTES = 0;
		    } else {
		    	$IBPTES = (($TOTALITEM - $porcDescItem + $porcAcreItem) * ($impostoProduto['VRALIQIBPTES']) / 100);
		    }
		    $totalEstadual = $totalEstadual + $IBPTES;
		}

	    return array(
			'VRTOTTRIBIBPT' => self::trunc($totalFederal + $totalEstadual),
			'impostoEstadual' => self::trunc($ICMSTotal)
	    );
	}

	private function separaItensQueSeraoCobrados($itens) {
		$itensImposto = array();
		foreach ($itens as $itemAtual) {
			if ($itemAtual['IDSITUITEM'] == self::ITEM_APROVADO) {
				// se for promoção e será cobrado os itens filhos
				if (($itemAtual['IDTIPOCOMPPROD'] == '3') && ($itemAtual['IDIMPPRODUTO'] == '2')) {
					foreach ($itemAtual['itensCombo'] as $itemComboAtual) {
						array_push($itensImposto, $itemComboAtual);
					}
				} else {
					array_push($itensImposto, $itemAtual);
				}
			}
		}
		return $itensImposto;
	}

	private function separaItensCancelados($itens) {
		$itensImposto = array();
		foreach ($itens as $itemAtual) {
			if ($itemAtual['IDSITUITEM'] == self::ITEM_CANCELADO) {
				// se for promoção e será cobrado os itens filhos
				if (($itemAtual['IDTIPOCOMPPROD'] == '3') && ($itemAtual['IDIMPPRODUTO'] == '2')) {
					foreach ($itemAtual['itensCombo'] as $itemComboAtual) {
						array_push($itensImposto, $itemComboAtual);
					}
				} else {
					array_push($itensImposto, $itemAtual);
				}
			}
		}
		return $itensImposto;
	}

	private function realizaVenda(
		$NRORG, $CDFILIAL, $CDLOJA, $CDCAIXA,
		$CDVENDEDOR, $CDOPERADOR, $DTABERCAIX, $DTVENDA,
		$IDTPEMISVEND, $TOTALVENDABRT, $ITEMVENDA, $TIPORECE,
		$NMCONSVEND, $NRINSCRCONS, $CDSENHAPED, $NRSEQVENDA,
		$VRTROCOVEND, $EMAIL, $VRDESCVENDA, $VRTOTTRIBIBPT,
		$CDCLIENTE, $CDCONSUMIDOR, $NRVENDAREST, $NRCOMANDA,
		$NRMESA, $IDORIGEMVENDA, $VRTXSEVENDA, $NRPESMESAVENDA,
		$DSCOMANDA, $FAMSALDOPROD, $VRTOTVENDA, $DTHRMESAFECHVEN, $FIDELITYDISCOUNT,
        $motivoDesconto, $CDGRPOCORDESC, $SUBSIDY, $DSOBSFINVEN, $ITVENDADES = null, $DELIVERY = false, $REPIQUE = 0) {

		$IDIMPVENDA = 'I'; // I (incluida) - A (alterada) - S (esportada com S)
	    $IDSITUVENDA = self::APROVADA;

	    // Dados consumidor
	    $NMCONSVEND = isset($NMCONSVEND) ? $NMCONSVEND : null;
		$NRINSCRCONS = isset($NRINSCRCONS) ? $NRINSCRCONS : null;
		$CDCLIENTE = isset($CDCLIENTE) ? $CDCLIENTE : null;
	    $CDCONSUMIDOR = isset($CDCONSUMIDOR) ? $CDCONSUMIDOR : null;

	    $connection = $this->entityManager->getConnection();

	    $params = array(
	        'CDFILIAL' => $CDFILIAL,
	        'CDLOJA' => $CDLOJA,
	        'NRORG' => $NRORG
	    );
	    $dadosEmpresa = $this->entityManager->getConnection()->fetchAll("GET_EMPRESAFILIAL", $params);
	    $CDEMPRESA = $dadosEmpresa[0]['CDEMPRESA'];
	  	$NRINSJURFILI = $dadosEmpresa[0]['NRINSJURFILI'];

	    $dadosFilial = $this->entityManager->getConnection()->fetchAll("GET_ENDEFILI_FULL", $params);
	   	$SGESTADO = $dadosFilial[0]['SGESTADO'];

        $dadosPadrao = $this->buscaClientePadrao($CDFILIAL, $NRORG);
        if (empty($CDCLIENTE)){
           $CDCLIENTE = $dadosPadrao['CDCLIENTE'];
        }

        if (empty($CDCONSUMIDOR)){
            $CDCCUSCLIE = $dadosPadrao['CDCCUSCLIE'];
        }
        else {
            $params = array(
                'CDCLIENTE' => $CDCLIENTE,
                'CDCONSUMIDOR' => $CDCONSUMIDOR
            );
            $dadosConsumidor = $this->entityManager->getConnection()->fetchAssoc("TIPO_CONSUMIDOR", $params);
            $CDCCUSCLIE = $dadosConsumidor['CDCCUSCLIE'];
        }

	    //--- Generates a sale code
	    if (empty($NRSEQVENDA)) {
	    	$CDCONTADOR = 'PEDIDOVENDA' . $CDFILIAL . $CDCAIXA;
	    	$NRSEQVENDA = $this->util->geraCodigo($connection, $CDCONTADOR, $NRORG, 1, 10);
	    }

	    $params = array(
	        'CDFILIAL' => $CDFILIAL,
	        'CDLOJA' => $CDLOJA,
	        'NRORG' => $NRORG
	    );
	    if (!empty($CDGRPOCORDESC)){
			$CDGRPOCORDESC = $this->entityManager->getConnection()->fetchAssoc("SQL_GRUPO_OBS_DESC", $params);
			$CDGRPOCORDESC = !empty($CDGRPOCORDESC['CDGRPOCORDESC']) ? $CDGRPOCORDESC['CDGRPOCORDESC'] : null;
		}

		$parametrosVendaDlv =  $this->montaParametrosVendaDlv($CDFILIAL, $NRVENDAREST, $NRCOMANDA);

	    //--- Build the sale params
	    $parametrosDaVenda = $this->montaParametrosDaVenda(
			$CDFILIAL, $CDLOJA, $CDCAIXA, $CDVENDEDOR,
	     	$DTVENDA, $CDEMPRESA, $SGESTADO, $IDIMPVENDA,
	      	$DTABERCAIX, $VRDESCVENDA, $CDCLIENTE, $CDCCUSCLIE,
	       	$IDSITUVENDA, $NRSEQVENDA, $NRORG, $IDTPEMISVEND,
	        $CDOPERADOR, $NRINSJURFILI, $CDCONSUMIDOR, $NMCONSVEND,
	        $NRINSCRCONS, $IDORIGEMVENDA, $CDSENHAPED,
	        $NRVENDAREST, $NRCOMANDA, $NRMESA, $VRTXSEVENDA,
			$NRPESMESAVENDA, $DSCOMANDA, $VRTOTVENDA, $DTHRMESAFECHVEN,
			$motivoDesconto, $CDGRPOCORDESC, $DSOBSFINVEN,
			$parametrosVendaDlv, $REPIQUE
		);

	    //--- Persists the sale
		$this->entityManager->getConnection()->executeQuery("INSERT_VENDA", $parametrosDaVenda[0], $parametrosDaVenda[1]);

	    //--- Saves the sale items
	    $cdprodutoItems = array();

	    foreach($ITEMVENDA as &$produto) {
	    	if ($produto['IDSITUITEM'] == self::ITEM_APROVADO) {
	    		$cdprodesto = null;
	    		$nrfeaturegrupo = null;
	    		if (isset($produto['DIMENSAO'])) {
	    			$dimensoes = $produto['DIMENSAO'];
	    			$nrfeaturegrupo = $this->getNrfeatureGrupoByDimensoes($dimensoes);
    				$cdprodesto = $this->getProdEstoByNrfeaturegrupo($nrfeaturegrupo);
	    		} else {
					$cdprodesto = $produto['CDPRODUTO'];
				}
				$produto['NRFEATUREGRUPO'] = $nrfeaturegrupo;
				$produto['CDPRODESTO'] = $cdprodesto;
				$cdprodutoItems[] = $cdprodesto;
			}
	    }

	    $NRLANCESTQ = null;
	    $PARAVEND = $this->buscaPARAVEND($CDFILIAL, $NRORG);
	    $IDESTTEMPOREAL = $PARAVEND['IDESTTEMPOREAL'];
	    if ($IDESTTEMPOREAL == self::SINCRONIZA_ESTOQUE) {
	  	  	$paramsEstqFili = $this->estoque->getParamsEstqFili($CDFILIAL, $NRORG);
    		if ($paramsEstqFili['IDCTRLESTQ']) {
    			$prodsCntrEstq = $this->estoque->prodsVendCntrlEst($NRORG, $CDFILIAL, $cdprodutoItems);
    			if ($prodsCntrEstq) {
    				$dslancestq = 'LANCTO. REF. À VENDA: ' . $NRSEQVENDA . ' DO CAIXA: ' . $CDCAIXA;
    				$idtplancto = 'S';
    				$NRLANCESTQ = $this->estoque->geraLancamentoEstq($CDFILIAL, $NRORG, $CDOPERADOR, $DTVENDA, $CDFILIAL, $dslancestq, $idtplancto);
	   			}
    		}
	    }

		$NRORDITCUPFIS = 0;
	    foreach ($ITEMVENDA as $item) {
			$NRORDITCUPFIS = $NRORDITCUPFIS + 1;
    		$NRORDITCUPFIS = str_pad($NRORDITCUPFIS, 5, '0', STR_PAD_LEFT);
	    	if ($item['IDSITUITEM'] == self::ITEM_APROVADO) {
	   			$this->insertSaleItem(
	   				$CDFILIAL,
	   				$CDLOJA,
	   				$CDCAIXA,
	   				$NRORG,
	   				$NRSEQVENDA,
	   				$item,
	   				$NRORDITCUPFIS,
	   				$nrfeaturegrupo,
	   				$CDOPERADOR,
	   				$IDTPEMISVEND,
	   				$VRDESCVENDA,
	   				$TOTALVENDABRT,
	   				$item['CDPRODPROMOCAO'],
	   				null,
	   				$item['CDVENDEDOR'],
	   				$VRTOTTRIBIBPT,
	   				$CDCLIENTE,
	   				$CDCONSUMIDOR,
	   				$IDESTTEMPOREAL,
                    $IDORIGEMVENDA
	   			);
			} else {
				$this->insertSaleItemCanc($CDFILIAL, $CDLOJA, $CDCAIXA, $NRORG, $NRSEQVENDA, $item, $NRORDITCUPFIS);
			}
	    }

	    if (!empty($ITVENDADES)){
	    	$this->insertItemsDesistencia($ITVENDADES, $NRSEQVENDA, $CDFILIAL, $CDLOJA, $CDCAIXA, $NRORG, $CDOPERADOR, $connection);
	    }


	    //----- atualiza estoque ------
	    if ($IDESTTEMPOREAL == self::SINCRONIZA_ESTOQUE) {
	    	if ($NRLANCESTQ !== null) {
	    		$dtini = date_create_from_format('d/m/Y H:i:s', $DTVENDA);
	    		$dtini = $dtini->format('d/m/Y');
	    		$dtfin = date_create_from_format('d/m/Y H:i:s', $DTVENDA);
	    		$dtfin = $dtfin->format('d/m/Y');
	    		$this->estoque->atualizaEstoque($CDFILIAL, $CDFILIAL, $NRLANCESTQ, $dtini, $dtfin);
	    	}
	    }

	    $dadosCaixa = $this->buscaDadosCaixa($CDFILIAL, $CDCAIXA, $NRORG);
        $EXTRATOCONSPARAMS = array();
	    foreach ($TIPORECE as $pagamentoAtual) {
	   		if (!empty($pagamentoAtual['NRSEQMOV'])) {
	   			$this->updateIntegracaomov($pagamentoAtual['NRSEQMOV'], $NRSEQVENDA, $CDFILIAL, $CDCAIXA, $NRORG);
	   		}
	   		// para recebimentos por transação, é considerado CDTIPORECE de acordo com a bandeira retornada na integração
	   		if (isset($pagamentoAtual['CDBANCARTCR'])){
                $NRCONFTELA = $this->util->getConfTela($CDFILIAL, $CDCAIXA);
	   			$pagamentoAtual['CDTIPORECE'] = $this->alteraTiporecePorBandeira($pagamentoAtual, $NRCONFTELA['CDFILIAL'], $NRCONFTELA['NRCONFTELA'], $NRCONFTELA['DTINIVIGENCIA'], $NRORG);
	   		}
	   		if($DELIVERY){
	   			$pagamentoAtual = $this->getPagamentoDlv($CDFILIAL, $NRVENDAREST, $pagamentoAtual);
	   		}
	    	$nrseqmovi = $this->insertPayment($pagamentoAtual, $CDFILIAL, $CDCAIXA, $DTABERCAIX, $DTVENDA, $NRSEQVENDA, $NRORG, $CDOPERADOR, $CDCLIENTE);
	    	$idtipoRece = $this->getIdtiporece($pagamentoAtual, $NRORG);

    		$vrmovcli = $pagamentoAtual['VRMOVIVEND'];
            if ($idtipoRece == 'A'){ // Venda com débito consumidor.
                $vrmovcli = floatval(bcsub(str_replace(',','.',strval($vrmovcli)), str_replace(',','.',strval($SUBSIDY)), '2'));
	    		$cdcontadorMovclie = 'MOVCLIE' . $CDCLIENTE;
                $this->util->geraCodigo($connection, $cdcontadorMovclie, $NRORG, 1, 10);
	    		$nrseqmovclie      = $this->util->geraCodigo($connection, $cdcontadorMovclie, $NRORG, 1, 10);
				$cdtiporece        = $pagamentoAtual['CDTIPORECE'];
				$vrsaldocons       = $this->consumidorService->getSaldoConsumidor($CDCLIENTE, $CDCONSUMIDOR);
				$novoSaldoCons = $vrsaldocons + ($vrmovcli * (-1));
				$vrmovpescons = 0;
				$vrsaldpecons = 0;
				$vrpesubsidio = 0;
				$vrtetosubsid = 0;

	    		$this->insertMovclie(
	    			$CDFILIAL,
	    			$CDCAIXA,
	    			$DTABERCAIX,
	    			$nrseqmovclie,
	    			$CDCLIENTE,
	    			$CDCONSUMIDOR,
	    			$vrmovcli,
					$DTVENDA,
					$NRSEQVENDA,
					$nrseqmovi,
					$cdtiporece,
					$novoSaldoCons,
					$CDOPERADOR,
					$NRORG,
					$vrmovpescons,
			    	$vrsaldpecons,
			    	$vrpesubsidio,
			    	$vrtetosubsid
			    );

                if ($SUBSIDY > 0){
                    $nrseqmovclie = $this->util->geraCodigo($connection, $cdcontadorMovclie, $NRORG, 1, 10);
                    $this->insertMovclie(
                        $CDFILIAL,
                        $CDCAIXA,
                        $DTABERCAIX,
                        $nrseqmovclie,
                        $CDCLIENTE,
                        null,
                        $SUBSIDY,
                        $DTVENDA,
                        $NRSEQVENDA,
                        $nrseqmovi,
                        $cdtiporece,
                        $SUBSIDY,
                        $CDOPERADOR,
                        $NRORG,
                        $vrmovpescons,
                        $vrsaldpecons,
                        $vrpesubsidio,
                        $vrtetosubsid
                    );
                }

                $this->consumidorService->atualizaSaldoMoviClie($CDFILIAL, $CDCLIENTE, $CDCONSUMIDOR, $CDOPERADOR);
	    	}
            else if ($idtipoRece == '9'){ // Venda com crédito pessoal.
                $DSOPEEXTCONS = $dadosCaixa['IDHABCAIXAVENDA'] != 'TAA' ? 'WTR - Venda Caixa ' . $CDCAIXA : 'TAA - Venda Caixa ' . $CDCAIXA;
                $NRSEQMOVEXT = 1;

                $dadosFilial = $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_FILIAL", array('CDFILIAL' => $CDFILIAL));
                foreach ($FAMSALDOPROD as $key => $family){
                    $this->updateItemVendaFamilia($CDFILIAL, $CDCAIXA, $NRSEQVENDA, $key);
                    $currentBalance = $this->consumidorService->buscaSaldoExtrato($CDCLIENTE, $CDCONSUMIDOR, $key, $CDFILIAL);
                    $VRSALDCONEXT = 0.000;
                    if (!empty($currentBalance)){
                        $VRSALDCONEXT = round(floatval($currentBalance['VRSALDCONEXT']) - $vrmovcli, 2);
                    }

                    $this->insertExtratoCons($CDCLIENTE, $CDCONSUMIDOR, $key, $DSOPEEXTCONS,
                                             $vrmovcli, 'V', $VRSALDCONEXT, str_pad($NRSEQMOVEXT, 3, '0', STR_PAD_LEFT),
                                             $pagamentoAtual['CDTIPORECE'], $CDFILIAL, $CDCAIXA,
                                             $NRSEQVENDA, $DTABERCAIX, null);

                    if ($dadosFilial['IDEXTCONSONLINE'] === 'S'){
                        $params = array(
                            'CDCLIENTE' => $CDCLIENTE,
                            'CDCONSUMIDOR' => $CDCONSUMIDOR,
                            'CDFAMILISALD' => $key,
                            'DTMOVEXTCONS' => new \DateTime(),
                            'NRSEQMOVEXT' => str_pad($NRSEQMOVEXT, 3, '0', STR_PAD_LEFT),
                            'CDTIPORECE' => $pagamentoAtual['CDTIPORECE'],
                            'IDTPMOVEXT' => 'V',
                            'DSOPEEXTCONS' => $DSOPEEXTCONS,
                            'VRMOVEXTCONS' => $vrmovcli,
                            'VRSALDCONEXT' => $VRSALDCONEXT,
                            'CDFILIAL' => $CDFILIAL,
                            'CDCAIXA' => $CDCAIXA,
                            'NRDEPOSICONS' => null,
                            'NRSEQMOVCAIXA' => null,
                            'IDIMPEXTRATO' => 'S'
                        );
                        $params['DTMOVEXTCONS'] = $params['DTMOVEXTCONS']->format('d/m/Y H:i:s');
                        array_push($EXTRATOCONSPARAMS, $params);
                    }
                    $this->updateBalance($CDCLIENTE, $CDCONSUMIDOR, $key, $VRSALDCONEXT);

                    $NRSEQMOVEXT++;
                }
            }
		}
		// Trata troco, caso exista.
		if (!empty($VRTROCOVEND['VRMOVIVEND'])){
			$this->insertChangePayment($VRTROCOVEND, $CDFILIAL, $CDCAIXA, $DTABERCAIX, $DTVENDA, $NRSEQVENDA, $NRORG, $CDOPERADOR, $CDCLIENTE);
		}

        // CRÉDITO FIDELIDADE
        if (!empty($FIDELITYDISCOUNT)){
            $dadosFilial = $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_FILIAL", array('CDFILIAL' => $CDFILIAL));
            if ($dadosFilial['IDEXTCONSONLINE'] !== 'S'){
                throw new \Exception('A filial não está configurada para acessar o sistema de crédito fidelidade.');
            }
            else {

                $DSOPEEXTCONS = 'WTR - Debito Fidelidade Caixa ' . $CDCAIXA;

                if(!$this->databaseUtil->databaseIsOracle()) {
	                $this->insertExtratoCons($CDCLIENTE, $CDCONSUMIDOR, '010', $DSOPEEXTCONS,
                     	$FIDELITYDISCOUNT, 'E', -1 * $FIDELITYDISCOUNT, '001', '001', $CDFILIAL, $CDCAIXA,
                     	$NRSEQVENDA, $DTABERCAIX, null);
                }

                $VRSALDCONEXT = 0.0;
                $params = array(
                    'CDCLIENTE' => $CDCLIENTE,
                    'CDCONSUMIDOR' => $CDCONSUMIDOR,
                    'CDFAMILISALD' => '010',
                    'DTMOVEXTCONS' => new \DateTime(),
                    'NRSEQMOVEXT' => '001',
                    'CDTIPORECE' => '001',
                    'IDTPMOVEXT' => 'E',
                    'DSOPEEXTCONS' => $DSOPEEXTCONS,
                    'VRMOVEXTCONS' => $FIDELITYDISCOUNT,
                    'VRSALDCONEXT' => $VRSALDCONEXT,
                    'CDFILIAL' => $CDFILIAL,
                    'CDCAIXA' => $CDCAIXA,
                    'NRDEPOSICONS' => null,
                    'NRSEQMOVCAIXA' => null,
                    'IDIMPEXTRATO' => 'S'
                );
                $params['DTMOVEXTCONS'] = $params['DTMOVEXTCONS']->format('d/m/Y H:i:s');
                $this->extratocons->insereExtratocons($params);
            }
        }

	    return array(
	    	'NRSEQVENDA' => $NRSEQVENDA,
            'EXTRATOCONSPARAMS' => $EXTRATOCONSPARAMS
    	);
	}

	private function updateItemVendaFamilia($CDFILIAL, $CDCAIXA, $NRSEQVENDA, $CDFAMILISALD) {
		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'CDCAIXA' =>$CDCAIXA,
			'NRSEQVENDA' =>$NRSEQVENDA,
			'CDFAMILISALD' => $CDFAMILISALD
		);
		$this->entityManager->getConnection()->executeQuery("UPDATE_ITEMVENDA_FAMILIA", $params);
	}

	private function defineTipoProduto($CDPRODPROMOCAO, $IDTIPOCOMPPROD, $IDIMPPRODUTO) {
		$tipoProduto = 'normal';
		if (!empty($CDPRODPROMOCAO)) {
			$tipoProduto = 'filho';
		} else if (($IDTIPOCOMPPROD == '3' && $IDIMPPRODUTO == '2') || $IDTIPOCOMPPROD == 'C') {
			$tipoProduto = 'promocaoInsereFilhos';
		} else if (($IDTIPOCOMPPROD == '3') && ($IDIMPPRODUTO == '1')) {
			$tipoProduto = 'promocaoInserePai';
		}
		return $tipoProduto;
	}

	private function insertSaleItem($CDFILIAL, $CDLOJA, $CDCAIXA, $NRORG, $NRSEQVENDA, $item, $NRORDITCUPFIS, &$nrfeaturegrupo, $CDOPERADOR, $IDTPEMISVEND, $VRDESCVENDA, $TOTALVENDABRT, $CDPRODPROMOCAO, $NRSEQPRODCOMIT, $CDVENDEDOR, $VRTOTTRIBIBPT, $CDCLIENTE, $CDCONSUMIDOR, $IDESTTEMPOREAL, $IDORIGEMVENDA) {
		$connection = $this->entityManager->getConnection();
		if (empty($item['IDTIPOCOMPPROD'])) {
			$item['IDTIPOCOMPPROD'] = null;
		}
		if (empty($item['IDIMPPRODUTO'])) {
			$item['IDIMPPRODUTO'] = null;
		}
		$tipoProduto = $this->defineTipoProduto($CDPRODPROMOCAO, $item['IDTIPOCOMPPROD'], $item['IDIMPPRODUTO']);

        if ($tipoProduto == 'promocaoInsereFilhos' || $tipoProduto == 'promocaoInserePai'){
            $CDCONTADOR = 'ITEMVENDAPROMO' . $CDFILIAL . $CDCAIXA . $NRSEQVENDA;
            $NRSEQPRODCOMIT = $this->util->geraCodigo($connection, $CDCONTADOR, $NRORG, 1, 3);
        }

		// se for promoção e será cobrado os itens filhos
		if ($tipoProduto == 'promocaoInsereFilhos') {
			foreach ($item['itensCombo'] as $currentComboItem) {
				$NRORDITCUPFIS = $NRORDITCUPFIS + 1;
				$NRORDITCUPFIS = str_pad($NRORDITCUPFIS, 5, '0', STR_PAD_LEFT);
				$this->insertSaleItem(
					$CDFILIAL,
					$CDLOJA,
					$CDCAIXA,
					$NRORG,
					$NRSEQVENDA,
					$currentComboItem,
					$NRORDITCUPFIS,
					$nrfeaturegrupo,
					$CDOPERADOR,
					$IDTPEMISVEND,
					$VRDESCVENDA,
					$TOTALVENDABRT,
					$item['CDPRODUTO'],
					$NRSEQPRODCOMIT,
					$CDVENDEDOR,
					$VRTOTTRIBIBPT,
					$CDCLIENTE,
					$CDCONSUMIDOR,
					$IDESTTEMPOREAL,
                    $IDORIGEMVENDA
				);
			}
		} else {
			$CDCONTADOR = 'ITEMVENDA' . $CDFILIAL . $CDCAIXA . $NRSEQVENDA;
			$NRSEQUITVEND = $this->util->geraCodigo($connection, $CDCONTADOR, $NRORG, 1, 3);

			$cdproduto = $item['CDPRODUTO'];
			$qtprodvend = $item['QTPRODVEND'];
			$vrunitvend = $item['VRUNITVEND'];
            $vrunitvendcl = $item['VRUNITVENDCL'];
			$idtipoitem = $item['IDTIPOITEM'];
			$idsituitem = $item['IDSITUITEM'];
			$vrdesitvend = $item['VRDESITVEND'];
			//Variável criada para controle de couvert em posições que já houve o recebimento.
			$nrlugarmesa = !empty($item['NRLUGARMESA']) ? $item['NRLUGARMESA'] : null;

			$dadosProdParams = array(
				'CDPRODUTO' => $cdproduto,
				'NRORG' => $NRORG,
				'CDFILIAL' => $CDFILIAL
			);
			$dadosProd = $this->entityManager->getConnection()->fetchAll("GET_DADOSPROD", $dadosProdParams);
			$sgunidade = $dadosProd[0]['SGUNIDADE'];
			$CDCLASFISC = $dadosProd[0]['CDCLASFISC'];
			$cdarvprod = $dadosProd[0]['CDARVPROD'];
			$CDCBENEFIT = $dadosProd[0]['CDCBENEF'];
			$vrpercvend = '0'; // VERIFICAR COMO CALCULA ESSE CAMPO
			$vraliqimpr = '0'; // VERIFICAR COMO CALCULA ESSE CAMPO4
			$nratraproditve = '0'; // VERIFICAR COMO CALCULA ESSE CAMPO
			$vrrattxserv = '0'; // VERIFICAR COMO CALCULA ESSE CAMPO
			$vrratdescven = '0'; // VERIFICAR COMO CALCULA ESSE CAMPO
			$dthrpedido = date('Y-m-d H:i:s'); // verificar se é o horário do front ou não (em caso de Sync pode dar diferença de horário)
			$baseCalcItVend = ($qtprodvend * ($vrunitvend + $vrunitvendcl)) - $item['VRDESITVEND'] + $item['VRACRITVEND'];
			$saleItemParams = self::buildSaleItemParams($CDFILIAL, $CDLOJA, $CDCAIXA, $qtprodvend, $vrunitvend, $vrunitvendcl, $item['VRDESITVEND'], $cdproduto, $NRORG, $NRSEQVENDA, $NRSEQUITVEND, $item['VRACRITVEND'], $NRORDITCUPFIS, $vrpercvend, $vraliqimpr, $nratraproditve, $vrrattxserv, $vrratdescven, $dthrpedido, $CDPRODPROMOCAO, $NRSEQPRODCOMIT, $CDVENDEDOR, $item['DSOBSITEMVENDA'], $item['DSOBSPEDDIGITA'], $CDCLASFISC, $item['DSOBSDESCIT'], $item['CDGRPOCORDESCIT'], $item['IDORIGEMVENDA'],
				$CDCBENEFIT, $nrlugarmesa, $item['CDCAMPCOMPGANHE'], $item['DTINIVGCAMPCG']);

			$this->entityManager->getConnection()->executeQuery("INSERT_ITEMVENDA", $saleItemParams[0], $saleItemParams[1]);

            if ($IDORIGEMVENDA == 'MES_PKR' || $IDORIGEMVENDA == 'CMD_PKC'){
                $params = array(
                    'CDFILIAL' => $CDFILIAL,
                    'NRVENDAREST' => $item['NRVENDAREST'],
                    'NRCOMANDA' => $item['NRCOMANDA'],
                    'NRPRODCOMVEN' => $item['NRPRODCOMVEN']
                );
                $itensComandaEst = $this->entityManager->getConnection()->fetchAll("GET_ITENS_EST", $params);

                foreach ($itensComandaEst as $itens){
                    $params = array(
                        'CDFILIAL' => $CDFILIAL,
                        'CDCAIXA' => $CDCAIXA,
                        'NRSEQVENDA' => $NRSEQVENDA,
                        'NRSEQUITVEND' => $NRSEQUITVEND,
                        'CDPRODUTO' => $itens['CDPRODUTO'],
                        'QTITVENDAEST' => $itens['QTPROCOMEST'],
                        'VRUNVENDAEST' => $itens['VRPRECCOMEST'],
                        'VRDESITVENDAEST' => $itens['VRDESITCOMEST'],
                        'NRATRAPRODITES' => $itens['NRATRAPRODCOES'],
                        'DSOBSPEDDIGITE' => $itens['DSOBSPEDDIGEST']
                    );
                    $this->entityManager->getConnection()->executeQuery("INSERE_ITEMVENDAEST", $params);

                    $params = array(
                        'CDFILIAL' => $CDFILIAL,
                        'NRVENDAREST' => $item['NRVENDAREST'],
                        'NRCOMANDA' => $item['NRCOMANDA'],
                        'NRPRODCOMVEN' => $item['NRPRODCOMVEN'],
                        'CDPRODUTO' => $itens['CDPRODUTO']
                    );
                    $obsComandaEst = $this->entityManager->getConnection()->fetchAll("GET_OBS_EST", $params);

                    foreach ($obsComandaEst as $obs){
                        $params = array(
                            'CDFILIAL' => $CDFILIAL,
                            'CDCAIXA' => $CDCAIXA,
                            'NRSEQVENDA' => $NRSEQVENDA,
                            'NRSEQUITVEND' => $NRSEQUITVEND,
                            'CDPRODUTO' => $obs['CDPRODUTO'],
                            'CDGRPOCOR' => $obs['CDGRPOCOR'],
                            'CDOCORR' => $obs['CDOCORR']
                        );
                        $this->entityManager->getConnection()->executeQuery("INSERT_OBSITEMVENDAEST", $params);
                    }
                }
            }

            // Insere o voucher na USOCUPOMDESCFOS.
            if ($item['VOUCHER']){
                $params = array(
                    'CDCUPOMDESCFOS' => $item['VOUCHER']['CDCUPOMDESCFOS'],
                    'CDFILIAL' => $CDFILIAL,
                    'CDCAIXA' => $CDCAIXA,
                    'NRSEQVENDA' => $NRSEQVENDA,
                    'NRSEQUITVEND' => $NRSEQUITVEND
                );
                $this->entityManager->getConnection()->executeQuery("INSERT_USOCUPOMDESCFOS", $params);
            }

			if (isset($item['DTHRINCOMVEN'])) {
				if (is_a($item['DTHRINCOMVEN'], 'DateTime')) {
					$DTITHRCOMANDA = $item['DTHRINCOMVEN'];
				} else {
					$DTITHRCOMANDA = explode('.', $item['DTHRINCOMVEN'])[0];
				}
			} else {
				$DTITHRCOMANDA = $dthrpedido;
			}
			$NRVENDAREST = isset($item['NRVENDAREST']) ? $item['NRVENDAREST'] : null;
			$NRSEQUITVEND_ITHRCOMANDA = $NRSEQUITVEND;
			$this->insereITHRCOMANDA(
				$CDFILIAL,
				$NRSEQUITVEND_ITHRCOMANDA,
				$cdproduto,
				$DTITHRCOMANDA,
				$qtprodvend,
				$CDCAIXA,
				$NRVENDAREST,
				$NRSEQVENDA
			);

			foreach ($item['OBSERVACOES'] as $currentObs) {
				self::insereOBSITEMVENDA(
					$CDFILIAL,
					$CDCAIXA,
					$NRSEQVENDA,
					$NRSEQUITVEND,
					$currentObs['CDGRPOCOR'],
					$currentObs['CDOCORR']
				);
			}

			if ($item['IDTIPOITEM'] == self::ITEM_ABASTECIMENTO) {

				$dataconclusaoabastecimento = $item['DATACONCLUSAOABASTECIMENTO'];
				$nrbico = $item['NRBICO'];
				$vrencerranteinicial = $item['VRENCERRANTEINICIAL'];
				$vrencerrantefinal = $item['VRENCERRANTEFINAL'];

				$getBombaParams = array(
					'NRBICO' => $nrbico
				);

				$nrbombaResult = $this->entityManager->getConnection()->fetchAll("GET_BOMBA_BY_BICO", $getBombaParams);
				$nrbomba = $nrbombaResult[0]['NRBOMBA'];
				$cdbomba = $nrbombaResult[0]['CDBOMBA'];
				$cdbico = $nrbombaResult[0]['CDBICO'];

				$getTanquebicohParams = array(
					'NRSEQBICO' => $nrbico,
					'DTABASTECIMENTO' => $this->databaseUtil->convertToDateTime($dataconclusaoabastecimento . ':00')
				);
				$tanquebicohResult = $this->entityManager->getConnection()->fetchAll("GET_TANQUEBICOH_BY_BICO", $getTanquebicohParams);
				$CDFILIALalmoxarifado = $tanquebicohResult[0]['CDFILIALALMOXARIFADO'];
				$cdalmoxarifado = $tanquebicohResult[0]['CDALMOXARIFADO'];
				$nritemvendaauxResult = $this->entityManager->getConnection()->fetchAll("GET_NOVO_NRITEMVENDAUXILIAR");
				$nritemvendauxiliar = $nritemvendaauxResult[0]['NRITEMVENDAUXILIAR'];
				$getTipocombustivelParams = array(
					'CDPRODUTO' => $cdproduto,
					'NRORG'     => $NRORG
				);
				$nrtipocombustivelResult = $this->entityManager->getConnection()->fetchAll("GET_TIPOCOMBUSTIVEL_BY_PRODUTO", $getTipocombustivelParams);
				$nrtipocombustivel = null;
				if (isset($nrtipocombustivelResult[0]['NRTIPOCOMBUSTIVEL'])) {
					$nrtipocombustivel = $nrtipocombustivelResult[0]['NRTIPOCOMBUSTIVEL'];
				}

				$auxSaleItemParams = self::buildAuxSaleItemParams($CDFILIAL, $CDCAIXA, $nritemvendauxiliar, $CDFILIALalmoxarifado, $cdalmoxarifado, $dataconclusaoabastecimento, $nrtipocombustivel, $nrbomba, $nrbico, $NRORG, $NRSEQVENDA, $NRSEQUITVEND, $vrencerranteinicial, $vrencerrantefinal);
				$this->entityManager->getConnection()->executeQuery("INSERT_ITEMVENDAAUXILIAR", $auxSaleItemParams);
			}

			if (isset($item['NRFEATUREGRUPO'])) {
				$nrfeaturegrupo = $item['NRFEATUREGRUPO'];
				if ($nrfeaturegrupo != null) {
					$nritemvendaauxResult = $this->entityManager->getConnection()->fetchAll("GET_NOVO_NRITEMVENDAUXILIAR");
					$nritemvendauxiliar = $nritemvendaauxResult[0]['NRITEMVENDAUXILIAR'];
					$auxSaleItemFeatureParams = self::buildAuxSaleItemFeatureParams($nritemvendauxiliar, $CDFILIAL, $CDCAIXA, $NRORG, $NRSEQVENDA, $NRSEQUITVEND, $nrfeaturegrupo, $CDOPERADOR);
					$this->entityManager->getConnection()->executeQuery("INSERT_ITEMVENDAAUXILIAR_FEATUREGRUPO", $auxSaleItemFeatureParams);
				}
			}
			//Insere os possiveis beneficios
			if(!empty($item['beneficios'])){
				$item['beneficios'] = $item['beneficios'][0];
				$CDCAMPANHA = $item['beneficios']['CDCAMPANHA'];
				$NRSEQPRODCAMP = $item['beneficios']['NRSEQPRODCAMP'];
				$NRSEQBENEFICIO = $item['beneficios']['NRSEQBENEFICIO'];
				$QTDEBENEFICIO = $item['beneficios']['QTDEBENEFICIO'];
				$params = array(
					':QTDEBENEFICIO'  => (string)$QTDEBENEFICIO,
					':CDCLIENTE'      => $CDCLIENTE,
					':CDCONSUMIDOR'   => $CDCONSUMIDOR,
					':CDCAMPANHA'     => $CDCAMPANHA,
					':NRSEQUITVEND'   => $NRSEQUITVEND,
					':NRSEQPRODCAMP'  => $NRSEQPRODCAMP,
					':NRSEQBENEFICIO' => $NRSEQBENEFICIO
				);
				$this->entityManager->getConnection()->executeQuery("UPDATE_MOVBENEFICIOCTR", $params);
				$NRSEQBENECONS = $this->util->geraCodigo($this->entityManager->getConnection(), 'MOVBENEFICIOCONS', $NRORG, 1, 10);
				$params = array(
					':CDCAMPANHA'     => $CDCAMPANHA,
					':NRSEQBENECONS'  => $NRSEQBENECONS,
					':NRSEQPRODCAMP'  => $NRSEQPRODCAMP,
					':NRSEQBENEFICIO' => $NRSEQBENEFICIO,
					':CDCLIENTE'      => $CDCLIENTE,
					':CDCONSUMIDOR'   => $CDCONSUMIDOR,
					':DTUTBENECONS'   => $this->databaseUtil->getCurrentDateTime(),
					':IDIMPTMOV'      => 'A',//A de aalterado, quando subir a venda vira N
					':CDFILIAL'       => $CDFILIAL,
					':CDCAIXA'        => $CDCAIXA,
					':NRSEQVENDA'     => $NRSEQVENDA,
					':NRSEQUITVEND'   => $NRSEQUITVEND
				);
				$this->entityManager->getConnection()->executeQuery("INSERT_MOVBENEFICIOCONS", $params);
			}
			// TRATAMENTO DOS IMPOSTOS
			if (!isset($item['IMPOSTOS'])) {
				$this->calculaImposto($CDFILIAL, $CDCAIXA, $NRORG, $NRSEQVENDA, $NRSEQUITVEND, $item, $IDTPEMISVEND, $baseCalcItVend,
				 $VRDESCVENDA, $TOTALVENDABRT, $VRTOTTRIBIBPT);
			}

			// @todo - verificar com Thais
			$NRLANCESTQ = null;
			if ($IDESTTEMPOREAL == self::SINCRONIZA_ESTOQUE) {
				if ($NRLANCESTQ !== null) {
					$keys = array(
						'NRLANCESTQ'   => $NRLANCESTQ,
						'NRSEQVENDA'   => $NRSEQVENDA,
						'NRSEQUITVEND' => $NRSEQUITVEND
					);
					$this->estoque->updateProductStock($item, $NRORG, $CDFILIAL, $CDLOJA, $CDCAIXA, $keys, $DTVENDA, null, null, null, null, $CDOPERADOR);
				}
			}
		}
		// ITEMVENDAEST é inserido apenas após o PAI estiver na ITEMVENDA
		if ($tipoProduto == 'promocaoInserePai') {
			foreach ($item['itensCombo'] as $currentComboItem) {
				$this->insereITEMVENDAEST(
					$CDFILIAL,
					$CDCAIXA,
					$NRSEQVENDA,
					$NRSEQUITVEND,
					$currentComboItem['CDPRODUTO'],
					$currentComboItem['QTPRODVEND'],
					$currentComboItem['VRUNITVEND'],
					$currentComboItem['VRDESITVEND'],
					null,
					null
				);
				foreach ($currentComboItem['OBSERVACOES'] as $currentObs) {
					$this->insereOBSITEMVENDAEST(
						$CDFILIAL,
						$CDCAIXA,
						$NRSEQVENDA,
						$NRSEQUITVEND,
						$currentComboItem['CDPRODUTO'],
						$currentObs['CDGRPOCOR'],
						$currentObs['CDOCORR']
					);
				}
			}
		}
	}

	public function insereITHRCOMANDA($CDFILIAL, $NRSEQUITEM, $CDPRODUTO, $DTHRPEDIDO, $QTPRODCOMVEN, $CDCAIXA, $NRVENDAREST, $NRSEQVENDA, $IDIMPITHRCOMANDA = 'N'){
		if (!is_a($DTHRPEDIDO, 'DateTime')) {
			$DTHRPEDIDO = new \DateTime($DTHRPEDIDO);
		}
		$params = array(
			'CDFILIAL'		   => $CDFILIAL,
			'NRSEQUITEM'	   => substr($NRSEQUITEM,-3),
			'CDPRODUTO'		   => $CDPRODUTO,
			'DTHRPEDIDO'	   => $DTHRPEDIDO,
			'QTPRODCOMVEN'	   => $QTPRODCOMVEN,
			'CDCAIXA'		   => $CDCAIXA,
			'NRVENDAREST'	   => $NRVENDAREST,
			'NRSEQVENDA'	   => $NRSEQVENDA,
			'IDIMPITHRCOMANDA' => $IDIMPITHRCOMANDA
		);
		$type = array(
			'DTHRPEDIDO' => \Doctrine\DBAL\Types\Type::DATETIME
		);

		$this->entityManager->getConnection()->executeQuery("INSERT_ITHRCOMANDA", $params, $type);
	}

    private function buscaProdutoObs($CDGRPOCOR, $CDOCORR) {
		$result = false;
		$params = array(
	    	'CDGRPOCOR' => $CDGRPOCOR,
	    	'CDOCORR' => $CDOCORR
   		);
    	$OCORRENCIA = $this->entityManager->getConnection()->fetchAssoc("BUSCA_OCORRENCIA", $params);
    	if ($OCORRENCIA['IDCONTROLAOBS'] == self::OBS_ADICIONA_PRODUTO) {
    		$result = $OCORRENCIA['CDPRODUTO'];
    	}
    	return $result;
    }

    private function insereOBSITEMVENDA($CDFILIAL, $CDCAIXA, $NRSEQVENDA, $NRSEQUITVEND, $CDGRPOCOR, $CDOCORR) {
    	$params = array(
			'CDFILIAL' => $CDFILIAL,
			'CDCAIXA' => $CDCAIXA,
			'NRSEQVENDA' => $NRSEQVENDA,
			'NRSEQUITVEND' => $NRSEQUITVEND,
			'CDGRPOCOR' => $CDGRPOCOR,
			'CDOCORR' => $CDOCORR
		);
		$this->entityManager->getConnection()->executeQuery("INSERT_OBSITEMVENDA", $params);
    }

	private function insereOBSITEMVENDAEST($CDFILIAL, $CDCAIXA, $NRSEQVENDA, $NRSEQUITVEND, $CDPRODUTO, $CDGRPOCOR, $CDOCORR) {
		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'CDCAIXA' => $CDCAIXA,
			'NRSEQVENDA' => $NRSEQVENDA,
			'NRSEQUITVEND' => $NRSEQUITVEND,
			'CDPRODUTO' => $CDPRODUTO,
			'CDGRPOCOR' => $CDGRPOCOR,
			'CDOCORR' => $CDOCORR
		);
		$this->entityManager->getConnection()->executeQuery("INSERT_OBSITEMVENDAEST", $params);
	}

    private function insereITEMVENDAEST($CDFILIAL, $CDCAIXA, $NRSEQVENDA, $NRSEQUITVEND, $CDPRODUTO, $QTITVENDAEST, $VRUNVENDAEST, $VRDESITVENDAEST, $NRATRAPRODITES, $DSOBSPEDDIGITE) {
		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'CDCAIXA' => $CDCAIXA,
			'NRSEQVENDA' => $NRSEQVENDA,
			'NRSEQUITVEND' => $NRSEQUITVEND,
			'CDPRODUTO' => $CDPRODUTO,
			'QTITVENDAEST' => $QTITVENDAEST,
			'VRUNVENDAEST' => $VRUNVENDAEST,
			'VRDESITVENDAEST' => $VRDESITVENDAEST,
			'NRATRAPRODITES' => $NRATRAPRODITES,
			'DSOBSPEDDIGITE' => $DSOBSPEDDIGITE
		);
	  	$this->entityManager->getConnection()->executeQuery("INSERE_ITEMVENDAEST", $params);
	}

	private function trataMascaraImposto($valor) {
		$valor = str_replace(' ', '', $valor);
		$valor = str_replace(',', '.', $valor);
		return floatval($valor);
	}

	private function calculaImposto($CDFILIAL, $CDCAIXA, $NRORG, $NRSEQVENDA, $NRSEQUITVEND, $item, $IDTPEMISVEND, $baseCalcItVend, $VRDESCVENDA, $TOTALVENDABRT, $VRTOTTRIBIBPT) {
		$getAliqImpFisParams = array(
			'CDFILIAL' => $CDFILIAL,
			'CDPRODUTO'  => $item['CDPRODUTO']
		);
        $aliqImpFisResult = $this->entityManager->getConnection()->fetchAssoc("GET_ALIQIMPFIS_IMPOSTO", $getAliqImpFisParams);
		$aliqImpFisResult['VRALIQPIS'] = $this->trataMascaraImposto($aliqImpFisResult['VRALIQPIS']);
		$aliqImpFisResult['VRALIQCOFINS'] = $this->trataMascaraImposto($aliqImpFisResult['VRALIQCOFINS']);
		$aliqImpFisResult['VRPEALIMPFIS'] = $this->trataMascaraImposto($aliqImpFisResult['VRPEALIMPFIS']);

       	$doDescItem = round($item['VRDESITVEND'], 2);
		$doAcreItem = round($item['VRACRITVEND'], 2);

		$baseValue = floatval(bcmul(str_replace(',','.',strval($item['QTPRODVEND'])), str_replace(',','.',strval(bcadd(str_replace(',','.',strval($item['VRUNITVEND'])), str_replace(',','.',strval($item['VRUNITVENDCL'])), '2'))), '2'));
		$vrBaseCalcIcmsPisCofins = floatval(bcsub(str_replace(',','.',strval(bcadd(str_replace(',','.',strval($baseValue)), str_replace(',','.',strval($doAcreItem)), '2'))), str_replace(',','.',strval($doDescItem)), '2'));
        $reductionPercentage = isset($aliqImpFisResult['VRPERCREDUCAO']) ? floatval($aliqImpFisResult['VRPERCREDUCAO']) : 0;

        //Cálcula ICMS com redução da base de cálculo
        $vrBaseCalcReduz = 0;
        if($reductionPercentage > 0){
			$vrBaseCalcReduz = $aliqImpFisResult['IDTRATIMPO'] == 'A' ?
				$vrBaseCalcIcmsPisCofins - $this->util->roundABNT($this->percentageToAbsolute(floatval(bcsub(str_replace(',','.',strval($baseValue)), str_replace(',','.',strval($doDescItem)), '2')), $reductionPercentage), 2) :
				$vrBaseCalcIcmsPisCofins - $this->util->roundABNT($this->percentageToAbsolute($vrBaseCalcIcmsPisCofins, $reductionPercentage), 2);
			$vrBaseCalcReduz = self::trunc($vrBaseCalcReduz);
        }

		$doVrImpoProdIt = $this->percentageToAbsolute($vrBaseCalcIcmsPisCofins, $aliqImpFisResult['VRPEALIMPFIS']);
		$doVrImpPis = $this->percentageToAbsolute($vrBaseCalcIcmsPisCofins, $aliqImpFisResult['VRALIQPIS']);
        $doVrImpCofins = $this->percentageToAbsolute($vrBaseCalcIcmsPisCofins, $aliqImpFisResult['VRALIQCOFINS']);

        $paramsItvendaimpos = $this->initParamsFor('ITVENDAIMPOS');
		$paramsItvendaimpos['CDFILIAL'] = $CDFILIAL;
		$paramsItvendaimpos['CDCAIXA'] = $CDCAIXA;
		$paramsItvendaimpos['NRSEQVENDA'] = $NRSEQVENDA;
        $paramsItvendaimpos['NRSEQUITVEND'] = $NRSEQUITVEND;
        $paramsItvendaimpos['VRTOTTRIBIBPT'] = $VRTOTTRIBIBPT;
        $paramsItvendaimpos['CDIMPOSTO'] = $aliqImpFisResult['CDIMPOSTO'];
        $paramsItvendaimpos['VRPEALPRODIT'] = $aliqImpFisResult['VRPEALIMPFIS'];
        $paramsItvendaimpos['CDIMPOSTOEX'] = $aliqImpFisResult['CDINTIMPOSTO'];
    	$paramsItvendaimpos['CDCSTPRODI'] = $aliqImpFisResult['CDCSTICMS'];
       	$paramsItvendaimpos['CDCSTPRODPC'] = $aliqImpFisResult['CDCSTPISCOF'];
        $paramsItvendaimpos['CDCFOPPROD'] = $aliqImpFisResult['CDCFOPPFIS'];
        $paramsItvendaimpos['VRPERCOFINS'] = $aliqImpFisResult['VRALIQCOFINS'];
        $paramsItvendaimpos['VRPERPIS'] = $aliqImpFisResult['VRALIQPIS'];
        $paramsItvendaimpos['IDMODALBASECALC'] = $aliqImpFisResult['IDMODALBASECALC'];
        $paramsItvendaimpos['VRBASECALCREDUZ'] = $vrBaseCalcReduz;
        $paramsItvendaimpos['VRIMPOPRODREDUZ'] = $this->util->roundABNT($this->percentageToAbsolute($vrBaseCalcReduz, $aliqImpFisResult['VRPEALIMPFIS']), 2);
		$paramsItvendaimpos['VRBASECALCICMS'] = $aliqImpFisResult['IDTRATIMPO'] == 'A' ? floatval(bcsub(str_replace(',','.',strval($baseValue)), str_replace(',','.',strval($doDescItem)), '2')) : $vrBaseCalcIcmsPisCofins;
        $paramsItvendaimpos['VRBCREDUZICMS'] = $reductionPercentage == 0 ? 0 : floatval(bcsub(str_replace(',','.',strval($vrBaseCalcIcmsPisCofins)), str_replace(',','.',strval($vrBaseCalcReduz)), '2'));
        $paramsItvendaimpos['VRPRBCREDUICMS'] = $reductionPercentage;
        $paramsItvendaimpos['VRIMPOPRODIT'] = $this->util->roundABNT($doVrImpoProdIt, 2);
        $paramsItvendaimpos['VRIMPPIS'] = $this->util->roundABNT($doVrImpPis, 2);
        $paramsItvendaimpos['VRIMPCOFINS'] = $this->util->roundABNT($doVrImpCofins, 2);
        $paramsItvendaimpos['VRBCPISCOFINS'] = $this->util->roundABNT($vrBaseCalcIcmsPisCofins, 2);

   		$this->entityManager->getConnection()->executeQuery("INSERT_ITVENDAIMPOS", $paramsItvendaimpos);
	}

    private function percentageToAbsolute($baseValue, $percentualValue) {
        return $baseValue * $percentualValue * 0.01;
	}

    private function applyPercentage($baseValue, $percentualValue, $mode) {
		if ($mode == "SUM" || $mode == "SUBTRACTION") {
			throw new \Exception("Invalid mode passed to applyPercentage function.");
		}
		$multiplier = $mode == "SUM" ? 1 : -1;
        return $baseValue + ($multiplier * $this->percentageToAbsolute($baseValue, $percentualValue));
    }

    private function initParamsFor($paramsKey) {
        switch ($paramsKey) {
            case 'ITVENDAIMPOS':
                return array(
                    'NRSEQITIMPOS' => '001',
                    'VRIMPOPRODIT' => 0,
                    'CDIMPOSTOEX' => null,
                    'CDCSTPRODI' => 0,
                    'CDCSTPRODPC' => 0,
                    'CDCFOPPROD' => 0,
                    'VRPERCOFINS' => 0,
                    'VRPERPIS' => 0,
                    'IDMODALBASECALC' => 0,
                    'VRBASECALCREDUZ' => 0,
                    'VRIMPOPRODREDUZ' => 0,
                    'VRBASECALCICMS' => 0,
                    'VRBCREDUZICMS' => 0,
                    'VRPRBCREDUICMS' => 0,
                    'VRIMPPIS' => 0,
                    'VRIMPCOFINS' => 0/* ,
                    'VRTOTTRIBIBPT' => 0 */
                );
            case 'PRODITEM':
                return array(
                    'VRPEALIMPFIS' => 0
                );
            default: return array();
        }
    }

	private function insertSaleItemCanc($CDFILIAL, $CDLOJA, $CDCAIXA, $NRORG, $NRSEQVENDA, $item, $NRORDITCUPFISca) {

    	//--- Gets the firm properties
		$cdproduto = $item['CDPRODUTO'];
		$qtprodvendc = $item['QTPRODVEND'];
		$vrunitvendc = $item['VRUNITVEND'];
        $vrunitvenccl = $item['VRUNITVENDCL'];
		$idtipoitemc = $item['IDTIPOITEM'];
		$cdgrpocorcanite = $item['CDGRPOCOR'];
		$dsobscanite =  !empty($item['DSOBSITEMVENDA']) ? substr($item['DSOBSITEMVENDA'], 0, 100) : null;
		$cdsupervisor = $item['CDSUPERVISOR'];
		$IDPRODPRODUZC = $item['IDPRODPRODUZC'];
		$DTHRPRODCAN = $item['DTHRPRODCANVEN'];

		//---- TEMPORARIO
   		$vracritvendc = isset($item['VRACRITVEND']) ? $item['VRACRITVEND'] : 0;
   		//----
		$vrdesitvendc = $item['VRDESITVEND'];
		$CDCONTADOR = 'ITEMVENDACANC' . $CDFILIAL . $CDCAIXA . $NRSEQVENDA;
		$NRSEQITVENDC = $this->util->geraCodigo($this->entityManager->getConnection(), $CDCONTADOR, $NRORG, 1, 3);
		$saleItemCancParams = self::buildSaleItemCancParams($CDFILIAL, $CDCAIXA, $qtprodvendc, $vrunitvendc, $vrunitvenccl, $vrdesitvendc, $cdproduto, $NRORG, $NRSEQVENDA, $NRSEQITVENDC, $vracritvendc, $NRORDITCUPFISca, $cdgrpocorcanite, $dsobscanite, $cdsupervisor, $IDPRODPRODUZC, $DTHRPRODCAN);
		$this->entityManager->getConnection()->executeQuery("INSERT_ITEMVENDA_CANCELADO", $saleItemCancParams[0], $saleItemCancParams[1]);
	}

	private function insertPayment($payment, $CDFILIAL, $CDCAIXA, $DTABERCAIX, $DTVENDA, $NRSEQVENDA, $NRORG, $CDOPERADOR, $CDCLIENTE) {
		$idtipomovive = self::ENTRADA;
		$vrmovivend   = $payment['VRMOVIVEND'];
		$cdtiporece   = $payment['CDTIPORECE'];
  		$qtparcreceb  = isset($payment['NRPARCELAS']) ? $payment['NRPARCELAS'] : 1;
  		$CDNSUHOSTTEF = $payment['CDNSUHOSTTEF'];
  		$NRCONTROLTEF = $payment['NRCONTROLTEF'];
  		$NRCARTBANCO  = substr($payment['NRCARTBANCO'], 0, 10);

		// @toDo: Verificar como os dois campos abaixo são calculados
  		$vrmoviVeout = 0;
  		$vrcotindout = 0;

        $connection = $this->entityManager->getConnection();
  		// Gera código de pagamento
		$cdcontadorMovcaixa = 'MOVCAIXA' . $CDFILIAL . $CDCAIXA . $this->databaseUtil->dateTimeToString($DTABERCAIX);
   	    $nrsequmovi = $this->util->geraCodigo($connection, $cdcontadorMovcaixa, $NRORG, 1, 10);

   		$paymentParams = self::buildPaymentParams(
   			$CDFILIAL,
   			$CDCAIXA,
   			$DTABERCAIX,
   			$nrsequmovi,
   			$DTVENDA,
   			$idtipomovive,
   			$vrmovivend,
   			$qtparcreceb,
   			$vrmoviVeout,
   			$vrcotindout,
   			$NRSEQVENDA,
   			$NRORG,
   			$cdtiporece,
   			$CDOPERADOR,
   			$CDCLIENTE,
   			$CDNSUHOSTTEF,
   			$NRCONTROLTEF,
   			$NRCARTBANCO
   		);
   		$this->entityManager->getConnection()->executeQuery("INSERT_MOVCAIXA_SALE", $paymentParams[0], $paymentParams[1]);

	    return $nrsequmovi;
	}

	private function insertMovclie($CDFILIAL, $CDCAIXA, $DTABERCAIX, $nrseqmovclie, $CDCLIENTE, $CDCONSUMIDOR, $vrmovcli,
		$dtmovclie, $NRSEQVENDA, $nrsequmovi, $cdtiporece, $vrsaldocons, $CDOPERADOR, $NRORG,$vrmovpescons,
	    $vrsaldpecons, $vrpesubsidio, $vrtetosubsid){

		$paramsMovclie = self::buildMoviclieParams($CDFILIAL, $CDCAIXA, $DTABERCAIX, $nrseqmovclie, $CDCLIENTE, $CDCONSUMIDOR, $vrmovcli,
		$dtmovclie, $NRSEQVENDA, $nrsequmovi, $cdtiporece, $vrsaldocons, $CDOPERADOR, $NRORG,$vrmovpescons,
		$vrsaldpecons, $vrpesubsidio, $vrtetosubsid);

		$this->entityManager->getConnection()->executeQuery("INSERT_MOVCLIE", $paramsMovclie[0], $paramsMovclie[1]);
	}

    private function insertChangePayment($VRTROCOVEND, $CDFILIAL, $CDCAIXA, $DTABERCAIX, $DTVENDA, $NRSEQVENDA, $NRORG, $CDOPERADOR, $CDCLIENTE){
    	$connection = $this->entityManager->getConnection();
  		// Gera código de pagamento
		$cdcontadorMovcaixa = 'MOVCAIXA' . $CDFILIAL . $CDCAIXA . $this->databaseUtil->dateTimeToString($DTABERCAIX);
   		$nrsequmovi	= $this->util->geraCodigo($connection, $cdcontadorMovcaixa, $NRORG, 1, 10);
		$paymentParams = self::buildPaymentParams(
			$CDFILIAL,
			$CDCAIXA,
			$DTABERCAIX,
			$nrsequmovi,
			$DTVENDA,
			'S',
			$VRTROCOVEND['VRMOVIVEND'],
			1,
			0,
			0,
			$NRSEQVENDA,
			$NRORG,
			$VRTROCOVEND['CDTIPORECE'],
			$CDOPERADOR,
			$CDCLIENTE,
			null,
			null,
			null
		);

		$this->entityManager->getConnection()->executeQuery("INSERT_MOVCAIXA_SALE", $paymentParams[0], $paymentParams[1]);
    }

	private function getIdtiporece($payment, $NRORG) {
		$cdtiporece = $payment['CDTIPORECE'];
		$idtiporeceParams = array(
			'CDTIPORECE' => $cdtiporece,
			'NRORG' => $NRORG
		);
		$idtiporeceQuery = $this->entityManager->getConnection()->fetchAll("GET_IDTIPORECE", $idtiporeceParams);
		$idtiporeceSAAS = $idtiporeceQuery[0]['IDTIPORECE'];
		return $idtiporeceSAAS;
	}

	private function getProdEstoByNrfeaturegrupo($nrfeaturegrupo) {
		$cdprodesto = null;
		$getProdEstFeatureGruParams = self::buildGetProdEstFeatureGruParams($nrfeaturegrupo);
		$getProdEstFeatureGruResult = $this->entityManager->getConnection()->fetchAll("GET_PRODESTFEATUREGRU", $getProdEstFeatureGruParams);
		if (isset($getProdEstFeatureGruResult[0])) {
			$cdprodesto = $getProdEstFeatureGruResult[0]['CDPRODUTOESTOQUE'];
		}
		return $cdprodesto;
	}

	private function getNrfeatureGrupoByDimensoes($dimensoes) {
		$nrfeaturegrupo = null;
		$qtdimensoes = count($dimensoes);
		$nrprodutodimensaovalorArray = array();
		foreach ($dimensoes as $dimensao) {
			$nrprodutodimensaovalorArray[] = $dimensao['NRPRODUTODIMENSAOVALOR'];
		}
		$getNrfeaturegrupoParams = self::buildGetNrfeaturegrupoParams($nrprodutodimensaovalorArray, $qtdimensoes);

		$params = array($getNrfeaturegrupoParams['NRPRODUTODIMENSAOVALOR'], $getNrfeaturegrupoParams['QTDIMENSOES']);
        $types = array(\Doctrine\DBAL\Connection::PARAM_STR_ARRAY, \PDO::PARAM_INT);
        $dts = $this->entityManager->getConnection()->executeQuery("GET_NRFEATUREGRUPO_BY_DIMENSOES", $params, $types);
        $getNrfeaturegrupoResult = $dts->fetchAll();
		if (isset($getNrfeaturegrupoResult[0])) {
			$nrfeaturegrupo = $getNrfeaturegrupoResult[0]['NRFEATUREGRUPO'];
		}
		return $nrfeaturegrupo;
	}

	private function buscaPARAVEND($CDFILIAL, $NRORG) {
		$params = array(
	    	'CDFILIAL' => $CDFILIAL,
	    	'NRORG' => $NRORG
   		);
    	return $this->entityManager->getConnection()->fetchAssoc("BUSCA_PARAVEND", $params);
	}

	private function montaParametrosDaVenda(
		$CDFILIAL, $CDLOJA, $CDCAIXA, $CDVENDEDOR,
	 	$DTVENDA, $CDEMPRESA, $SGESTADO, $IDIMPVENDA,
	  	$DTABERCAIX, $VRDESCVENDA, $CDCLIENTE, $CDCCUSCLIE,
	   	$IDSITUVENDA, $NRSEQVENDA, $NRORG, $IDTPEMISVEND,
	    $CDOPERADOR, $NRINSJURFILI, $CDCONSUMIDOR, $NMCONSVEND,
	    $NRINSCRCONS, $IDORIGEMVENDA, $CDSENHAPED,
	    $NRVENDAREST, $NRCOMANDA, $NRMESA, $VRTXSEVENDA,
	    $NRPESMESAVENDA, $DSCOMANDA, $VRTOTVENDA, $DTHRMESAFECHVEN,
		$DSOBSDESC, $CDGRPOCORDESC, $DSOBSFINVEN,
		$parametrosVendaDlv, $VRREPIQUEVENDA) {

		$dateTime = new \DateTime();
		$parametrosDaVenda = array(
			'CDFILIAL' 		   => $CDFILIAL,
			'CDLOJA' 		   => $CDLOJA,
			'CDCAIXA' 		   => $CDCAIXA,
			'DTENTRVENDA' 	   => $DTABERCAIX,
			'DTVENDA' 		   => $DTVENDA,
			'DTABERVENDA' 	   => $dateTime,
			'DTFECHAVENDA'     => $dateTime,
			'CDEMPRESA' 	   => $CDEMPRESA,
			'SGESTADO'         => $SGESTADO,
			'IDIMPVENDA' 	   => $IDIMPVENDA,
			'DTABERTUR' 	   => $DTABERCAIX,
			'CDFILIALTUR' 	   => $CDFILIAL,
			'CDCAIXATUR' 	   => $CDCAIXA,
			'VRDESCVENDA' 	   => $VRDESCVENDA,
			'CDCLIENTE' 	   => $CDCLIENTE,
			'CDCCUSCLIE' 	   => $CDCCUSCLIE,
			'IDSITUVENDA' 	   => $IDSITUVENDA,
			'NRSEQVENDA' 	   => $NRSEQVENDA,
			'NRORG' 		   => $NRORG,
			'IDTPEMISVEND'     => $IDTPEMISVEND,
			'CDOPERADOR' 	   => $CDOPERADOR,
			'NRINSJURIEST'	   => $NRINSJURFILI,
			'CDCONSUMIDOR'	   => $CDCONSUMIDOR,
			'NMCONSVEND'	   => $NMCONSVEND,
			'NRINSCRCONS'	   => $NRINSCRCONS,
			'IDORIGEMVENDA'    => $IDORIGEMVENDA,
			'DTHRREALCONCONS'  => $dateTime,
			'CDPREFINTGCOLVEN' => 'TEK',
			'IDTIPOVENDA'	   => 'D',
			'IDVENDEBCONS'	   => 'N',
			'NRPESMESAVENDA'   => $NRPESMESAVENDA,
			'CDSENHAPED'       => $CDSENHAPED,
			'CDVENDEDOR'       => $CDVENDEDOR,
			'NRVENDAREST'      => $NRVENDAREST,
			'NRCOMANDAVND'     => strlen($NRCOMANDA) > 0 ? $NRCOMANDA: null,
			'NRMESA'           => $NRMESA,
			'VRTXSEVENDA'      => !empty($VRTXSEVENDA) ? $VRTXSEVENDA : 0,
			'DSCOMANDAVND'     => !empty($DSCOMANDA) ? $DSCOMANDA : null,
            'VRTOTVENDA'       => $VRTOTVENDA,
            'DTHRMESAFECHVEN'  => $this->databaseUtil->convertToDateDB($DTHRMESAFECHVEN),
            'DSOBSDESC'		   => !empty($DSOBSDESC) ? substr($DSOBSDESC, 0, 100) : null,
            'CDGRPOCORDESC'    => $CDGRPOCORDESC,
            'DSOBSFINVEN'      => $DSOBSFINVEN,
            'DSBAIRRO'		   => isset($parametrosVendaDlv['DSBAIRRO'])? $parametrosVendaDlv['DSBAIRRO']:null,
            'IDRETBALLOJA'	   => isset($parametrosVendaDlv['IDRETBALLOJA'])? $parametrosVendaDlv['IDRETBALLOJA']:null,
            'DSAREAATENDVEN'   => isset($parametrosVendaDlv['DSAREAATEND'])? $parametrosVendaDlv['DSAREAATEND']:null,
            'NRCEPCONSVENDA'   => isset($parametrosVendaDlv['NRCEPCONSCOMAND'])? $parametrosVendaDlv['NRCEPCONSCOMAND']:null,
            'DSCOMPLENDCOCOM'  => isset($parametrosVendaDlv['DSCOMPLENDCOCOM'])? $parametrosVendaDlv['DSCOMPLENDCOCOM']:null,
            'CDBAIRRO'	   	   => isset($parametrosVendaDlv['CDBAIRRO'])? $parametrosVendaDlv['CDBAIRRO']:null,
            'CDMUNICIPIO'	   => isset($parametrosVendaDlv['CDMUNICIPIO'])? $parametrosVendaDlv['CDMUNICIPIO']:null,
            'DSENDECONSVENDA'  => isset($parametrosVendaDlv['DSENDECONSCOMAN'])? $parametrosVendaDlv['DSENDECONSCOMAN']:null,
            'VRREPIQUEVENDA'   => $VRREPIQUEVENDA
 	    );

		$type = array(
			'DTENTRVENDA' => \Doctrine\DBAL\Types\Type::DATE,
			'DTVENDA' => \Doctrine\DBAL\Types\Type::DATETIME,
			'DTABERVENDA' => \Doctrine\DBAL\Types\Type::DATETIME,
			'DTFECHAVENDA' => \Doctrine\DBAL\Types\Type::DATETIME,
			'DTABERTUR' => \Doctrine\DBAL\Types\Type::DATETIME,
			'DTHRREALCONCONS' => \Doctrine\DBAL\Types\Type::DATETIME,
			'DTHRMESAFECHVEN' => \Doctrine\DBAL\Types\Type::DATETIME
		);

		return array($parametrosDaVenda, $type);
	}

	private function buildSaleItemParams($CDFILIAL, $CDLOJA, $CDCAIXA, $QTPRODVEND, $VRUNITVEND, $VRUNITVENDCL, $VRDESITVEND, $CDPRODUTO, $NRORG, $NRSEQVENDA, $NRSEQUITVEND,
		$VRACRITVEND, $NRORDITCUPFIS, $VRPERCVEND, $VRALIQIMPR, $NRATRAPRODITVE, $VRRATTXSERV, $VRRATDESCVEN, $DTHRPEDIDO, $CDPRODPROMOCAO, $NRSEQPRODCOMIT,
		$CDVENDEDOR, $DSOBSITEMVENDA, $DSOBSPEDDIGITA, $CDCLASFISC, $DSOBSDESCIT, $CDGRPOCORDESCIT, $IDORIGEMVENDA, $CDCBENEFIT, $NRLUGARMESA, $CDCAMPCOMPGANHE, $DTINIVGCAMPCG) {

		$saleItemParams = array(
			'CDFILIAL'       => $CDFILIAL,
			'CDLOJA'         => $CDLOJA,
			'CDCAIXA'        => $CDCAIXA,
			'QTPRODVEND'     => $QTPRODVEND,
			'VRUNITVEND'     => $VRUNITVEND,
			'VRDESITVEND'    => $VRDESITVEND,
			'CDPRODUTO'      => $CDPRODUTO,
			'NRORG'          => $NRORG,
			'NRSEQVENDA'     => $NRSEQVENDA,
			'NRSEQUITVEND'   => $NRSEQUITVEND,
			'VRACRITVEND'    => $VRACRITVEND,
			'VRUNITVENDCL'   => $VRUNITVENDCL,
			'NRORDITCUPFIS'	 => $NRORDITCUPFIS,
			'VRPERCVEND'	 => $VRPERCVEND,
			'VRALIQIMPR'	 => $VRALIQIMPR,
			'NRATRAPRODITVE' => $NRATRAPRODITVE,
			'VRRATTXSERV'	 => $VRRATTXSERV,
			'VRRATDESCVEN'	 => $VRRATDESCVEN,
			'DTHRPEDIDO'	 => new \DateTime($DTHRPEDIDO),
			'CDCAIXAPEDVEN'	 => $CDCAIXA,
			'CDPRODPROMOCAO' => $CDPRODPROMOCAO,
			'NRSEQPRODCOMIT' => $NRSEQPRODCOMIT,
			'CDVENDEDOR'     => $CDVENDEDOR,
			'IDORIGPEDVEN'   => 'MOB', // @TODO - MELHORAR ISSO
			'DSOBSITEMVENDA' => $DSOBSITEMVENDA,
			'DSOBSPEDDIGITA' => $DSOBSPEDDIGITA,
			'QTPRODREFIL'    => 0,
			'CDCLASFISC'     => $CDCLASFISC,
			'DSOBSDESCIT'    => !empty($DSOBSDESCIT) ? substr($DSOBSDESCIT, 0, 100) : null,
			'CDGRPOCORDESCIT' => $CDGRPOCORDESCIT,
            'IDORIGEMVENDA'  => $IDORIGEMVENDA,
            'CDCBENEFIT'	 => $CDCBENEFIT,
            'NRPESMESAVENIT' => $NRLUGARMESA,
            'CDCAMPCOMPGANHE' => $CDCAMPCOMPGANHE,
            'DTINIVGCAMPCG' => $DTINIVGCAMPCG
   		);
   		$type = array(
   			'DTHRPEDIDO' => \Doctrine\DBAL\Types\Type::DATETIME,
            'DTINIVGCAMPCG' => \Doctrine\DBAL\Types\Type::DATETIME
   		);

   		return array($saleItemParams, $type);
	}

	private function buildSaleItemCancParams($CDFILIAL, $CDCAIXA, $QTPRODVENDC, $VRUNITVENDC, $VRUNITVENCCL, $VRDESITVENDC, $CDPRODUTO, $NRORG, $NRSEQVENDA, $NRSEQITVENDC, $VRACRITVENDC, $NRORDITCUPFISCA, $cdgrpocorcanite, $dsobscanite, $cdsupervisor, $IDPRODPRODUZC, $DTHRPRODCAN) {
		$saleItemCancParams = array(
			'CDFILIAL'    	  => $CDFILIAL,
			'CDCAIXA'      	  => $CDCAIXA,
			'QTPRODVENDC'     => $QTPRODVENDC,
			'VRUNITVENDC'     => $VRUNITVENDC,
			'VRDESITVENDC'    => $VRDESITVENDC,
			'CDPRODUTO'       => $CDPRODUTO,
			'NRORG'           => $NRORG,
			'NRSEQVENDA'      => $NRSEQVENDA,
			'NRSEQITVENDC'    => $NRSEQITVENDC,
			'VRACRITVENDC'    => $VRACRITVENDC,
			'NRORGINCLUSAO'	  => $NRORG,
			'VRUNITVENCLC'    => $VRUNITVENCCL,
			'NRORDITCUPFISCA' => $NRORDITCUPFISCA,
			'CDGRPOCORCANITE' => $cdgrpocorcanite,
			'DSOBSCANITE'	  => $dsobscanite,
			'CDSUPERVISOR'	  => $cdsupervisor,
			'IDPRODPRODUZC'   => $IDPRODPRODUZC,
			'DTHRPRODCAN'	  => $this->databaseUtil->convertToDateDB($DTHRPRODCAN)
   		);

   		$type = array(
			'DTHRPRODCAN' => \Doctrine\DBAL\Types\Type::DATETIME
		);
   		return array($saleItemCancParams, $type);
	}

	private function buildMoviclieParams($CDFILIAL, $CDCAIXA, $DTABERCAIX, $NRSEQMOVCLI, $CDCLIENTE, $CDCONSUMIDOR, $VRMOVCLI,
		$DTMOVCLI, $NRSEQVENDA, $NRSEQUMOVI, $CDTIPORECE, $VRSALDOCONS, $CDOPERADOR, $NRORG, $VRMOVPESCONS,
	    $VRSALDPECONS, $VRPESUBSIDIO, $VRTETOSUBSID) {

		$movclieParams = array(
			'CDFILIAL' 	   	 => $CDFILIAL,
			'CDCAIXA' 	   	 => $CDCAIXA,
			'DTABERCAIX'   	 => $DTABERCAIX,
			'NRSEQMOVCLI' 	 => $NRSEQMOVCLI,
			'CDCLIENTE'      => $CDCLIENTE,
			'CDCONSUMIDOR'   => $CDCONSUMIDOR,
			'VRMOVCLI' 	     => $VRMOVCLI,
			'DTMOVCLI'       => $this->databaseUtil->convertToDateTime($DTMOVCLI),
			'DTMOVIMCLIE'    => $this->databaseUtil->convertToDate($DTMOVCLI),
			'IDTIPMOCVLI'    => 'D',
			'NRSEQVENDA'     => $NRSEQVENDA,
			'NRSEQUMOVI'     => $NRSEQUMOVI,
			'CDTIPORECE'     => $CDTIPORECE,
			'VRSALDOCONS'    => $VRSALDOCONS,
			'CDOPERADOR'     => $CDOPERADOR,
			'NRORG'          => $NRORG,
			'DTINCLUSAO'     => $DTMOVCLI,
			'NRORGINCLUSAO'  => $NRORG,
			'CDOPERINCLUSAO' => $CDOPERADOR,
			'NRORGULTATU'    => $NRORG,
			'VRMOVPESCONS' 	 => $VRMOVPESCONS,
			'VRSALDPECONS' 	 => $VRSALDPECONS,
			'VRPESUBSIDIO' 	 => $VRPESUBSIDIO,
			'VRTETOSUBSID' 	 => $VRTETOSUBSID,
			'CDOPERULTATU'   => $CDOPERADOR,
			'DTINCLUSAO'     => $this->databaseUtil->convertToDate($this->databaseUtil->getCurrentDate())
		);

        $type = array(
            'DTABERCAIX' => \Doctrine\DBAL\Types\Type::DATETIME,
            'DTMOVCLI' => \Doctrine\DBAL\Types\Type::DATETIME,
            'DTMOVIMCLIE' => \Doctrine\DBAL\Types\Type::DATE,
            'DTINCLUSAO' => \Doctrine\DBAL\Types\Type::DATE
        );
        return array($movclieParams, $type);
	}

	private function buildPaymentParams($CDFILIAL, $CDCAIXA, $DTABERCAIX, $NRSEQUMOVI, $DTVENDA, $IDTIPOMOVIVE, $VRMOVIVEND, $QTPARCRECEB, $VRMOVIVEOUT, $VRCOTINDOUT, $NRSEQVENDA, $NRORG, $CDTIPORECE, $CDOPERADOR, $CDCLIENTE, $CDNSUHOSTTEF, $NRCONTROLTEF, $NRCARTBANCO) {
		$paymentParams = array(
			'CDFILIAL' 	   	 => $CDFILIAL,
			'CDCAIXA' 	   	 => $CDCAIXA,
			'DTABERCAIX'   	 => $DTABERCAIX,
			'NRSEQUMOVI'   	 => $NRSEQUMOVI,
			'DTHRINCMOV'	 => $DTVENDA,
			'IDTIPOMOVIVE' 	 => $IDTIPOMOVIVE,
			'VRMOVIVEND'   	 => $VRMOVIVEND,
			'QTPARCRECEB'    => $QTPARCRECEB,
			'VRMOVIVEOUT'    => $VRMOVIVEOUT,
			'VRCOTINDOUT'    => $VRCOTINDOUT,
			'NRSEQVENDA'   	 => $NRSEQVENDA,
			'NRORG'			 => $NRORG,
			'CDTIPORECE'  	 => $CDTIPORECE,
			'DTMOVIMCAIXA'	 => $DTABERCAIX,
			'CDOPERINCLUSAO' => $CDOPERADOR,
			'CDCLIENTE' 	 => $CDCLIENTE,
			'NRORGINCLUSAO'  => $NRORG,
			'CDNSUHOSTTEF'   => $CDNSUHOSTTEF,
			'NRCONTROLTEF'   => $NRCONTROLTEF,
			'NRCARTBANCO'	 => $NRCARTBANCO
		);
		$type = array(
			'DTABERCAIX' => \Doctrine\DBAL\Types\Type::DATETIME,
			'DTHRINCMOV' => \Doctrine\DBAL\Types\Type::DATETIME,
			'DTMOVIMCAIXA' => \Doctrine\DBAL\Types\Type::DATE
		);

		return array($paymentParams, $type);
	}

	private function buildAuxSaleItemParams($CDFILIAL, $CDCAIXA, $nritemvendauxiliar, $CDFILIALalmoxarifado, $cdalmoxarifado, $dataconclusaoabastecimento, $nrtipocombustivel, $nrbomba, $nrbico, $NRORG, $NRSEQVENDA, $nrsequitvend, $vrencerranteinicial, $vrencerrantefinal) {
		$saleItemAuxParams = array(
			'CDFILIAL'    		  	     => $CDFILIAL,
			'CDCAIXA'      		 	     => $CDCAIXA,
			'NRITEMVENDAUXILIAR' 	     => $nritemvendauxiliar,
			'CDFILIALALMOXARIFADO'   	 => $CDFILIALalmoxarifado,
			'CDALMOXARIFADO'   		 	 => $cdalmoxarifado,
			'DATACONCLUSAOABASTECIMENTO' => $this->databaseUtil->convertToDateTime($dataconclusaoabastecimento),
			'NRTIPOCOMBUSTIVEL' 		 => $nrtipocombustivel,
			'NRBOMBA'	   				 => $nrbomba,
	        'NRBICO'      				 => $nrbico,
			'NRORG'        				 => $NRORG,
			'NRSEQVENDA'   				 => $NRSEQVENDA,
			'NRSEQUITVEND'				 => $nrsequitvend,
			'VRENCERRANTEINICIAL' 		 => $vrencerranteinicial,
			'VRENCERRANTEFINAL' 		 => $vrencerrantefinal
		);
		return $saleItemAuxParams;
	}

	private function buildGetNrfeaturegrupoParams($nrprodutodimensaovalorArray, $qtdimensoes) {
		$getNrfeaturegrupoParams = array(
			'NRPRODUTODIMENSAOVALOR' => $nrprodutodimensaovalorArray,
			'QTDIMENSOES' 			 => $qtdimensoes
		);
		return $getNrfeaturegrupoParams;
	}

	private function buildGetProdEstFeatureGruParams($nrfeaturegrupo) {
		$getProdEstFeatureGruParams = array(
			'NRFEATUREGRUPO' => $nrfeaturegrupo
		);
		return $getProdEstFeatureGruParams;
	}

   	private function buildAuxSaleItemFeatureParams($nritemvendauxiliar, $CDFILIAL, $CDCAIXA, $NRORG, $NRSEQVENDA, $nrsequitvend, $nrfeaturegrupo, $CDOPERADOR) {
		$auxSaleItemFeatureParams = array(
			'NRITEMVENDAUXILIAR' => $nritemvendauxiliar,
			'CDFILIAL' 			 => $CDFILIAL,
			'CDCAIXA'        	 => $CDCAIXA,
			'NRORG'    	     	 => $NRORG,
			'NRSEQVENDA'     	 => $NRSEQVENDA,
			'NRSEQUITVEND'   	 => $nrsequitvend,
			'NRFEATUREGRUPO' 	 => $nrfeaturegrupo,
			'NRORGINCLUSAO'	 	 => $NRORG,
			'NRORGULTATU'    	 => $NRORG,
			'CDOPERINCLUSAO' 	 => $CDOPERADOR,
			'CDOPERULTATU'	 	 => $CDOPERADOR
		);
		return $auxSaleItemFeatureParams;
	}

    private function updateIntegracaomov($nrseqmov, $NRSEQVENDA, $CDFILIAL, $CDCAIXA, $NRORG) {
		$params = self::buildIntegracaomovParams($nrseqmov, $NRSEQVENDA, $CDFILIAL, $CDCAIXA, $NRORG);
		$this->entityManager->getConnection()->executeQuery("UPDATE_NRSEQVENDA_VND_INTEGRACAOMOV", $params);
    }

    private function buildIntegracaomovParams($nrseqmov, $NRSEQVENDA, $CDFILIAL, $CDCAIXA, $NRORG) {
		return array(
			'NRSEQMOV'   => $nrseqmov,
            'CDFILIAL'   => $CDFILIAL,
            'CDCAIXA'    => $CDCAIXA,
            'NRSEQVENDA' => $NRSEQVENDA,
            'NRORG'      => $NRORG
		);
	}

    private function buscaClientePadrao($CDFILIAL, $NRORG){
        $params = array(
            'CDFILIAL' => $CDFILIAL,
            'NRORG' => $NRORG
        );
        return $this->entityManager->getConnection()->fetchAssoc("GET_CLIENTEPADRAO", $params);
    }

	private function calculaValorDaVenda($items) {
		$produtosCobrados = self::separaItensQueSeraoCobrados($items);
		$total = 0;
        $subsidy = 0;
		foreach ($produtosCobrados as $item) {
			$total += self::calculaTotalItem($item);
            $subsidyCalc = self::calculateSubsidy($item['VRUNITVEND'], $item['VRUNITVENDCL'], bcdiv(str_replace(',','.',strval($item['VRDESITVEND'])), str_replace(',','.',strval($item['QTPRODVEND'])), '2'));
            $subsidy += floatval(bcmul(str_replace(',','.',strval($item['QTPRODVEND'])), str_replace(',','.',strval($subsidyCalc)), '2'));
		}
		$produtosCancelados = self::separaItensCancelados($items);
		$totalCancelamento = 0;
		foreach ($produtosCancelados as $item) {
			$totalCancelamento += self::calculaTotalItem($item);
		}
		return array(
			'total' => $total,
            'subsidy' => $subsidy,
            'totalCancelamento' => $totalCancelamento
		);
	}

    private function calculateSubsidy($VRPRECITEM, $VRPRECITEMCL, $VRDESITVEND){
        if ($VRPRECITEMCL > 0 && $VRDESITVEND > 0){

            $finalPrice = floatval(bcsub(str_replace(',','.',strval($VRPRECITEM + $VRPRECITEMCL)), str_replace(',','.',strval($VRDESITVEND)), '2'));
            if ($finalPrice <= 0.01) return 0;

            $totalPrice = $VRPRECITEM + $VRPRECITEMCL;
            $discount = ($VRPRECITEMCL / $totalPrice) * $VRDESITVEND;

            return floatval(bcsub(str_replace(',','.',strval($VRPRECITEMCL)), str_replace(',','.',strval($discount)), '2'));
        }
        else return $VRPRECITEMCL;
    }

	public function cancelaCupom($CDFILIAL, $CDCAIXA, $NRORG, $NRSEQVENDA, $CDOPERADOR, $CDSUPERVISOR){
		$params = array(
            'CDFILIAL'   => $CDFILIAL,
            'CDCAIXA'    => $CDCAIXA,
            'NRORG'      => $NRORG,
            'NRSEQVENDA' => $NRSEQVENDA
        );
		$venda = $this->entityManager->getConnection()->fetchAssoc("GET_SALE_PARAMETERS", $params);

		if ($venda){
			$IDTPEMISVEND = $this->vendaNormalizacao->normalizaTipoEmissao($CDFILIAL, $CDCAIXA, $NRORG);
			if ($IDTPEMISVEND == self::TIPO_SAT){
				$cancelaCupom = $this->sat->cancelaVendaSAT($CDFILIAL, $CDCAIXA, $NRORG, $NRSEQVENDA, $venda);
			} else {
				if ($venda['IDSTATUSNFCE'] == 'A'){
					$cancelaCupom = $this->notaFiscalService->cancelaVendaNFCE($CDFILIAL, $CDCAIXA, $NRORG, $NRSEQVENDA, $venda, 'Venda Cancelada no Sistema.', $CDOPERADOR);
				} else {
					$cancelaCupom = array(
						'error' => false,
						'message' => '',
						'mensagemNfce' => 'A venda será cancelada apenas no sistema. Para cancelamento na SEFAZ, realize a rotina pelo Gestão de Vendas.',
						'dadosImpressao' => array()
					);
				}
			}

			if (!$cancelaCupom['error']){
				$this->cancelaVenda($CDFILIAL, $CDCAIXA, $NRORG, $NRSEQVENDA, $CDOPERADOR, 'C', $CDSUPERVISOR);
			}
			$result = $cancelaCupom;
		}
        else {
            $result = array(
                'error'   => true,
                'message' => 'Venda não encontrada.'
            );
        }

		return $result;
	}

    public function createBalance($CDCLIENTE, $CDCONSUMIDOR, $CDFAMILISALD, $VRSALDCONFAM){
        $params = array(
            ':CDCLIENTE' => $CDCLIENTE,
            ':CDCONSUMIDOR' => $CDCONSUMIDOR,
            ':CDFAMILISALD' => $CDFAMILISALD,
            ':VRSALDCONFAM' => $VRSALDCONFAM
        );
        $this->entityManager->getConnection()->executeQuery("CREATE_BALANCE", $params);
    }

    public function updateBalance($CDCLIENTE, $CDCONSUMIDOR, $CDFAMILISALD, $VRSALDCONFAM){
		$params = array(
            'CDCLIENTE' => $CDCLIENTE,
            'CDCONSUMIDOR' => $CDCONSUMIDOR,
            'CDFAMILISALD' => $CDFAMILISALD
		);
		$balance = $this->entityManager->getConnection()->fetchAll("CHECK_BALANCE", $params);
		if (empty($balance)){
			$params = array(
				'CDCLIENTE' => $CDCLIENTE,
				'CDCONSUMIDOR' => $CDCONSUMIDOR,
				'CDFAMILISALD' => $CDFAMILISALD,
				'VRSALDCONFAM' => $VRSALDCONFAM
			);
			$this->entityManager->getConnection()->executeQuery("CREATE_BALANCE", $params);
		} else {
			$params = array(
				':CDCLIENTE' => $CDCLIENTE,
				':CDCONSUMIDOR' => $CDCONSUMIDOR,
				':CDFAMILISALD' => $CDFAMILISALD,
				':VRSALDCONFAM' => $VRSALDCONFAM
			);
			$this->entityManager->getConnection()->executeQuery("UPDATE_BALANCE", $params);
		}
    }

	public function permiteSaldoNegativo($CDFILIAL, $CDFAMILISALD){
        $params = array(
            ':CDFILIAL' => $CDFILIAL,
            ':CDFAMILISALD' => $CDFAMILISALD
        );

        $result = $this->entityManager->getConnection()->fetchAssoc("GET_PERMITE_SALDO_NEGATIVO", $params);
        return $result;
	}

    public function insertExtratoCons($CDCLIENTE, $CDCONSUMIDOR, $CDFAMILISALD, $DSOPEEXTCONS, $VRMOVEXTCONS, $IDTPMOVEXT, $VRSALDCONEXT, $NRSEQMOVEXT,
    								  $CDTIPORECE = null, $CDFILIAL = null, $CDCAIXA = null, $NRSEQVENDA = null, $DTABERCAIX, $NRSEQMOVCAIXA){
			$date = new \DateTime();
			$params = array(
            ':CDCLIENTE'    => $CDCLIENTE,
            ':CDCONSUMIDOR' => $CDCONSUMIDOR,
            ':CDFAMILISALD' => $CDFAMILISALD,
            ':DSOPEEXTCONS' => $DSOPEEXTCONS,
            ':VRMOVEXTCONS' => $VRMOVEXTCONS,
            ':IDTPMOVEXT'   => $IDTPMOVEXT,
            ':VRSALDCONEXT' => $VRSALDCONEXT,
            ':CDTIPORECE'   => $CDTIPORECE,
			':CDFILIAL'     => $CDFILIAL,
			':CDCAIXA'      => $CDCAIXA,
			':NRSEQVENDA'   => $NRSEQVENDA,
			':NRSEQMOVCAIXA'   => $NRSEQMOVCAIXA,
			':NRDEPOSICONS'   => null,
			':NRSEQMOVEXT'   => str_pad($NRSEQMOVEXT, 3, '0', STR_PAD_LEFT),
			':DTABERCAIX'   => $DTABERCAIX,
			':DTMOVEXTCONS' => $date
		);
		$type = array(
            ':DTABERCAIX' => \Doctrine\DBAL\Types\Type::DATETIME,
            ':DTMOVEXTCONS' => \Doctrine\DBAL\Types\Type::DATETIME
        );
        $this->entityManager->getConnection()->executeQuery("INSERT_EXTRATOCONS", $params, $type);
    }

    public function alteraTiporecePorBandeira($pagamentoAtual, $CDFILIAL, $NRCONFTELA, $DTINIVIGENCIA, $NRORG){
    	$params = array(
			'CDFILIAL' => $CDFILIAL,
    		'NRCONFTELA' => $NRCONFTELA,
            'DTINIVIGENCIA' => $DTINIVIGENCIA,
			'NRORG' => $NRORG,
			'IDTIPORECE' => $pagamentoAtual['IDTIPORECE'],
			'CDBANCARTCR' => $pagamentoAtual['CDBANCARTCR']
    	);
        $types = array(
            ':DTINIVIGENCIA' => \Doctrine\DBAL\Types\Type::DATETIME
        );
    	$tiporece = $this->entityManager->getConnection()->fetchAssoc("GET_CDTIPORECE_BY_BANCART", $params, $types);

    	return $tiporece ? $tiporece['CDTIPORECE'] : $pagamentoAtual['CDTIPORECE'];
    }

    public function montaItemsDesistencia($CDFILIAL, $NRVENDAREST){
    	$params = array(
    		'NRVENDAREST' => $NRVENDAREST,
    		'CDFILIAL' => $CDFILIAL
    	);
    	$ITVENDADES = $this->entityManager->getConnection()->fetchAll("SELECT_ITCOMANDAVENDES", $params);
    	return $ITVENDADES;
    }

    public function insertItemsDesistencia($itensDesistencia, $NRSEQVENDA, $CDFILIAL, $CDLOJA, $CDCAIXA, $NRORG, $CDOPERADOR, $connection) {
		$chave = $CDFILIAL.$CDCAIXA.$NRSEQVENDA;

		foreach ($itensDesistencia as $itemDesistencia) {
	    	$NRSEQITVENDADES = $this->util->geraCodigo($connection, $chave, $NRORG, 1, 3);
			$params = array(
				'CDFILIAL'			=> $CDFILIAL,
				'CDCAIXA'			=> $CDCAIXA,
				'NRORG'				=> $NRORG,
				'NRSEQVENDA'        => $NRSEQVENDA,
				'NRSEQITVENDADES'   => $NRSEQITVENDADES,
				'QTPRODVENDADES'    => $itemDesistencia['QTPRODITCOMVENDES'],
				'CDPRODUTO'         => $itemDesistencia['CDPRODUTO'],
				'VRPRECCOMVEN'      => $itemDesistencia['VRPRECCOMVEN'],
				'VRDESCCOMVEN'      => $itemDesistencia['VRDESCCOMVEN'],
				'VRACRCOMVEN'       => $itemDesistencia['VRACRCOMVEN']
			);

			$this->entityManager->getConnection()->executeQuery("INSERT_ITVENDADES", $params);
			// Trata dos acréscimos que contém preço.
			if (array_key_exists('CDOCORR', $itemDesistencia)) {
				if (!empty($itemDesistencia['CDOCORR'])) {
					foreach ($itemDesistencia['CDOCORR'] as $CDOCORR) {
						$params = array(
							$CDFILIAL,
							$CDLOJA,
							$CDOCORR
						);
						$obs_type = $this->entityManager->getConnection()->fetchAssoc("GET_OBSERVATION_TYPE", $params);
						if ($obs_type['IDCONTROLAOBS'] === 'A' && !empty($obs_type['CDPRODUTO'])) {
							// Busca o preço do acréscimo.
							$r_retornaPreco = $this->precoService->buscaPreco($CDFILIAL, null, $obs_type['CDPRODUTO'], $CDLOJA, null);
							$preco     = floatval($r_retornaPreco['PRECO']);
							$desconto  = floatval($r_retornaPreco['DESC']);
							$acrescimo = floatval($r_retornaPreco['ACRE']);

							// Valida o preço.
							if (!empty($preco) && $preco > 0) {
								$NRSEQITVENDADES = $this->util->geraCodigo($connection, $chave, $NRORG, 1, 3);
								$params = array(
									'CDFILIAL'			=> $CDFILIAL,
									'CDCAIXA'			=> $CDCAIXA,
									'NRORG'				=> $NRORG,
									'NRSEQVENDA'        => $NRSEQVENDA,
									'NRSEQITVENDADES'   => $NRSEQITVENDADES,
									'QTPRODVENDADES'    => $itemDesistencia['QTPRODITCOMVENDES'],
									'CDPRODUTO'         => $obs_type['CDPRODUTO'],
									'VRPRECCOMVEN'      => $preco,
									'VRDESCCOMVEN'      => $desconto,
									'VRACRCOMVEN'       => $acrescimo
								);
								$this->entityManager->getConnection()->executeQuery("INSERT_ITVENDADES", $params);
							}
						}
					}
				}
			}
		}
    }

    public function getPagamentoDlv($CDFILIAL, $NRVENDAREST, $pagamentoAtual){
    	$params = array(
    		'CDFILIAL' => $CDFILIAL,
    		'NRVENDAREST' => $NRVENDAREST
    	);
		$pagamentoDlv = $this->entityManager->getConnection()->fetchAssoc("GET_PAYMENT_PARAM_DLV", $params);
    	return $pagamentoAtual + $pagamentoDlv;
    }

    public function montaParametrosVendaDlv($CDFILIAL, $NRVENDAREST, $NRCOMANDA){
    	$params = array(
    		'CDFILIAL'		=> $CDFILIAL,
    		'NRVENDAREST'	=> $NRVENDAREST,
    		'NRCOMANDA'		=> $NRCOMANDA
    	);
    	$paramsDlv = $this->entityManager->getConnection()->fetchAssoc("GET_PARAMS_DELIVERY_SALE", $params);
    	return $paramsDlv;
    }

	private function geraLog($texto, $CDFILIAL, $CDCAIXA, $DTVENDA) {
		if ($this->habilitaLog){
			$paths = ['LOG/', 'vendaApi/', 'Filial - ' . $CDFILIAL . '/', 'Caixa - ' . $CDCAIXA . '/'];
			$logDir = dirname(__DIR__) . '/../../../../../../';

			foreach($paths as $path) {
				$logDir = $logDir . $path;

				if(!is_dir($logDir)) {
					mkdir($logDir, 0700);
				}
			}

			$DTVENDA = $DTVENDA->format('Y-m-d H-i-s');
			file_put_contents($logDir . $DTVENDA . '.txt', $texto . "\r\n", FILE_APPEND);
		}
	}

	private function getLogHeader($CDFILIAL, $CDLOJA, $CDCAIXA, $CDOPERADOR, $DTVENDA, $VRMOVIVEND) {
		$VRMOVIVEND = number_format($VRMOVIVEND, 2, ',', '' );
		$logHeaderUp = '| LOG DE VENDA - Filial: ' . $CDFILIAL . ' - Loja: ' . $CDLOJA . ' - Caixa: ' . $CDCAIXA . ' - Operador: ' . $CDOPERADOR . ' |';
		$logHeaderDown = '| Valor: R$' . $VRMOVIVEND . ' - Data: ' . $DTVENDA->format('Y-m-d H:i:s');
		$logHeaderDown = str_pad($logHeaderDown, strlen($logHeaderUp) - 1, " ", STR_PAD_RIGHT) . '|';
		$logHeaderBox = "" . str_pad(null, strlen($logHeaderUp), "-", STR_PAD_LEFT);
		$this->geraLog($logHeaderBox, $CDFILIAL, $CDCAIXA, $DTVENDA);
		$this->geraLog($logHeaderUp, $CDFILIAL, $CDCAIXA, $DTVENDA);
		$this->geraLog($logHeaderDown, $CDFILIAL, $CDCAIXA, $DTVENDA);
		$this->geraLog($logHeaderBox . "\r\n", $CDFILIAL, $CDCAIXA, $DTVENDA);
	}

    private function validaInsercaoVenda($CDFILIAL, $CDCAIXA, $DTVENDA, $NRSEQVENDA){
        if ($this->habilitaLog){
            $params = array(
                $CDFILIAL,
                $CDCAIXA,
                $NRSEQVENDA
			);
            $NRSEQVENDA = $this->entityManager->getConnection()->fetchAssoc("VALIDA_VENDA", $params);
            if (!empty($NRSEQVENDA)){
                $this->geraLog("\r\n" . '  REGISTRO DO BANCO: ' . $NRSEQVENDA['NRSEQVENDA'], $CDFILIAL, $CDCAIXA, $DTVENDA);
            }
            else {
                $this->geraLog("\r\n" . '  VENDA NÃO ENCONTRADA NO BANCO!', $CDFILIAL, $CDCAIXA, $DTVENDA);
            }
        }
    }

    // Quando o produto taxa de serviço está parametrizado, o valor do repique vai para ele independente da parametrização de taxa de serviço.
    private function adicionaProdutoTaxaServico($dadosTaxa, $NRVENDAREST, $NRCOMANDA, $REPIQUE, $VRTXSEVENDA) {

    	$itemTaxa = [];
    	if ($dadosTaxa['IDTRATTAXASERV'] === 'P' && $dadosTaxa['CDPRODTAXASERV'] !== null && $dadosTaxa['IDCOMISVENDA'] != 'N' && ($VRTXSEVENDA > 0 || !empty($REPIQUE))) {

			$itemTaxa = array(
                'NRPRODCOMVEN'   => null,
				'NMPRODUTO'      => $dadosTaxa['NMPRODUTO'],
				'CDPRODUTO'      => $dadosTaxa['CDPRODTAXASERV'],
				'QTPRODVEND'     => 1,
				'VRUNITVEND'     => 0,
                'VRUNITVENDCL'   => 0,
				'VRDESITVEND'    => 0,
				'VRACRITVEND'    => floatval(bcdiv(str_replace(',','.',strval($VRTXSEVENDA)), '1', '2') + $REPIQUE),
				'IDSITUITEM'     => self::ITEM_APROVADO,
				'IDTIPOITEM'     => null,
				'OBSERVACOES'    => array(),
				'IDTIPOCOMPPROD' => '0',
				'IDIMPPRODUTO'   => '1',
				'CDGRPOCOR'      => null,
				'DSOBSITEMVENDA' => null,
				'DSOBSPEDDIGITA' => null,
				'CDSUPERVISOR'   => null,
				'DTHRINCOMVEN'   => new \DateTime(),
				'NRVENDAREST'    => $NRVENDAREST,
                'NRCOMANDA'      => $NRCOMANDA,
				'IDPRODPRODUZC'  => null,
				'DSOBSDESCIT' 	 => null,
                'IDORIGEMVENDA'  => null,
				'CDGRPOCORDESCIT'=> null,
				'CDVENDEDOR'     => null,
                'CDPRODPROMOCAO' => null,
                'REALSUBSIDY'    => 0,
                'VOUCHER'		 => null,
                'CDCAMPCOMPGANHE' => null,
                'DTINIVGCAMPCG'  => null
			);
			$itemTaxa['PRECOFINAL'] = self::getItemPrice($itemTaxa, '0');
		}

		return $itemTaxa;
    }

    public function getProductDetails($CDPRODUTO){
        $params = array(
            'CDPRODUTO' => $CDPRODUTO
        );

        return $this->entityManager->getConnection()->fetchAssoc('GET_PRODUCT_DETAILS', $params);
    }

}
