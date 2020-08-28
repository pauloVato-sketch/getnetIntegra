<?php
namespace Service;
use \Util\Exception;

class GeneralFunctions {

	protected $entityManager;
	protected $notaFiscal;
	protected $impressaoNFCE;
	protected $caixaApi;
	protected $databaseUtil;
	protected $impressaoSAT;
	protected $vendaAPI;

	public function __construct(\Doctrine\ORM\EntityManager $entityManager, \Odhen\API\Service\NotaFiscal $notaFiscal,
	\Odhen\API\Service\ImpressaoNFCE $impressaoNFCE, \Odhen\API\Service\Caixa $caixaApi, \Odhen\API\Util\DataBase $databaseUtil,
	\Odhen\API\Service\ImpressaoSAT $impressaoSAT, \Odhen\API\Service\Venda $vendaAPI){
		$this->entityManager = $entityManager;
		$this->notaFiscal = $notaFiscal;
		$this->impressaoNFCE = $impressaoNFCE;
		$this->caixaApi = $caixaApi;
		$this->databaseUtil = $databaseUtil;
		$this->impressaoSAT = $impressaoSAT;
		$this->vendaAPI = $vendaAPI;
	}

	public function reprintSaleCoupon($session, $reprintType, $NRNOTAFISCALCE){
		$CDFILIAL = $session['CDFILIAL'];
		$CDCAIXA = $session['CDCAIXA'];
		$NRORG = $session['NRORG'];
		$DTEMISSAONFCE = new \DateTime();
		$DTEMISSAONFCE = $this->databaseUtil->databaseIsOracle() ? $DTEMISSAONFCE->format('Y-m-d') : $DTEMISSAONFCE;

		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'CDCAIXA' => $CDCAIXA,
			'NRORG' => $NRORG,
			'IDTPEMISVEND' =>  'N',
			'DTEMISSAONFCE' => $DTEMISSAONFCE
		);

		if ($session['IDTPEMISSAOFOS'] === 'FNC') {
			$CDSERIENFCE = $this->entityManager->getConnection()->fetchAssoc('GET_SERIE_NFCE', $params);
			$params['CDSERIENFCE'] = !empty($CDSERIENFCE['CDSERIECX']) ? $CDSERIENFCE['CDSERIECX'] : $CDSERIENFCE['CDCAIXA'];
		} else {
			$params['CDSERIENFCE'] = 'SAT';
			$params['IDTPEMISVEND'] = 'S';
		}

		$type = $this->databaseUtil->databaseIsOracle() ? array() : array('DTEMISSAONFCE' => \Doctrine\DBAL\TypeS\Type::DATETIME);

		if ($reprintType == 'U'){
			$saleCode = $this->entityManager->getConnection()->fetchAssoc('GET_ULTIMA_VENDA_FISCAL', $params, $type);
			$NRNOTAFISCALCE = !empty($saleCode['NRNOTAFISCALCE']) ? $saleCode['NRNOTAFISCALCE'] : null;
		}
		$params['NRNOTAFISCALCE'] = $NRNOTAFISCALCE;
		$venda = $this->entityManager->getConnection()->fetchAssoc('GET_VENDA', $params, $type);
		if (!empty($venda)){
			$params['NRSEQVENDA'] = $venda['NRSEQVENDA'];
			$NRMESA = $venda['NRMESA'];

			$FIDELITYVALUE = $this->getDiscountFidelity($CDFILIAL, $CDCAIXA, $venda['CDCLIENTE'], $venda['CDCONSUMIDOR'], '010', $venda['NRSEQVENDA']);
			if ($session['IDTPEMISSAOFOS'] === 'FNC') {
				$params['IDTPAMBNFCE'] = $this->entityManager->getConnection()->fetchAssoc('SQL_FILIAL_DETAILS', array('CDFILIAL' => $CDFILIAL))['IDAMBTRABNFCE'];
				$nfceInfo = array(
					'IDSTATUSNFCE' => $venda['IDSTATUSNFCE'],
					'tpAmb' => $params['IDTPAMBNFCE'],
					'nrseqvenda' => $venda['NRSEQVENDA'],
					'DTHRPROTOCONFCE' => new \DateTime($venda['DTHRPROTOCONFCE']),
					'VRTOTTRIBIBPT' => $this->entityManager->getConnection()->fetchAssoc('GET_VRTOTTRIBIBPT_ITVENDAIMPOS', $params)['VRTOTTRIBIBPT'],
					'nrAcessoNFCE' => $venda['NRACESSONFCE']
				);

				$endeFiliResult = $this->notaFiscal->validaEnderecoFilial($CDFILIAL, $NRORG);
				$dadosEmitente = $this->notaFiscal->validaDadosEmitenteXML($CDFILIAL, $NRORG);
				$filialInfo = array(
					'CDFILIAL' => $CDFILIAL,
					'CDLOJA' => $session['CDLOJA'],
					'CDCAIXA' => $CDCAIXA,
					'SGESTADO' => $endeFiliResult['dadosEndereco']['SGESTADO'],
					'NRMESA'   => $NRMESA,
					'NRORG'	   => $NRORG
				);
				$filialInfo = array_merge($filialInfo, $dadosEmitente['dadosEmitente']);
				$nrseqvenda = array('nrseqvenda' => $venda['NRSEQVENDA']);
				$productsNFCE = $this->notaFiscal->getInfoProducts($params, $nrseqvenda);
				$infoConsumer = $this->notaFiscal->getInfoConsumer($params, $nrseqvenda);

				$result = $this->impressaoNFCE->imprimeDanfeNFCE($nfceInfo, $filialInfo, $productsNFCE, $infoConsumer, false, $FIDELITYVALUE);
			} else {
                $ITEMVENDA = $this->entityManager->getConnection()->fetchAll("BUSCA_PRODUTOS_SAT", $params);
                $impostos = $this->vendaAPI->calculaImpostoParaImpressao($NRORG, $CDFILIAL, $CDCAIXA, $ITEMVENDA, $venda['VRTOTVENDA'], $venda['VRDESCVENDA'], $session['CDLOJA'], $venda['CDCLIENTE'], $venda['CDCONSUMIDOR'], $venda['VRTXSEVENDA']);
				$VRTOTTRIBIBPT = $impostos['VRTOTTRIBIBPT'];
				$impostoEstadual = $impostos['impostoEstadual'];

				$result = $this->impressaoSAT->imprimeCupomNF($CDFILIAL, $CDCAIXA, $NRORG, $venda['NRSEQVENDA'], $NRNOTAFISCALCE, $venda['CDSERIESAT'], $DTEMISSAONFCE, $venda['NRACESSONFCE'], $venda['NRINSCRCONS'], $venda['NMCONSVEND'], $venda['CDSENHAPED'], $venda['DSQRCODENFCE'], $VRTOTTRIBIBPT, $impostoEstadual, $FIDELITYVALUE, $ITEMVENDA, $NRMESA);
			}
		} else {
			$result = array(
				'error' => true,
				'message' => 'Operação bloqueada. Venda não encontrada.'
			);
		}

		return $result;
	}

	public function selectUnblockedProducts($session, $filter, $FIRST, $LAST){
		$CDFILIAL = $session['CDFILIAL'];
		$NRORG = $session['NRORG'];
        $FILIALVIGENCIA = $session['FILIALVIGENCIA'];
		$NRCONFTELA = $session['NRCONFTELA'];
        $DTINIVIGENCIA = new \DateTime($session['DTINIVIGENCIA']);
		$CDLOJA = $session['CDLOJA'];

		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'NRORG' => $NRORG,
            'FILIALVIGENCIA' => $FILIALVIGENCIA,
			'NRCONFTELA' => $NRCONFTELA,
            'DTINIVIGENCIA' => $DTINIVIGENCIA,
			'CDLOJA' => $CDLOJA,
			'FILTER' => $filter['value'],
			'FIRST' => $FIRST,
			'LAST' => $LAST
		);
        $types = array(
            'DTINIVIGENCIA' => \Doctrine\DBAL\Types\Type::DATETIME
        );
		return $this->entityManager->getConnection()->fetchAll('GET_PRODUTOS_DESBLOQUEADOS', $params, $types);
	}

	public function selectBlockedProducts($session, $filter, $FIRST, $LAST){
		$CDFILIAL = $session['CDFILIAL'];
		$NRORG = $session['NRORG'];
        $FILIALVIGENCIA = $session['FILIALVIGENCIA'];
		$NRCONFTELA = $session['NRCONFTELA'];
        $DTINIVIGENCIA = new \DateTime($session['DTINIVIGENCIA']);
		$CDLOJA = $session['CDLOJA'];
		$CDOPERADOR = $session['CDOPERADOR'];

		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'NRORG' => $NRORG,
            'FILIALVIGENCIA' => $FILIALVIGENCIA,
			'NRCONFTELA' => $NRCONFTELA,
            'DTINIVIGENCIA' => $DTINIVIGENCIA,
			'CDLOJA' => $CDLOJA,
			'CDOPERADOR' => $CDOPERADOR,
			'FILTER' => $filter['value'],
			'FIRST' => $FIRST,
			'LAST' => $LAST
		);
        $types = array(
            'DTINIVIGENCIA' => \Doctrine\DBAL\Types\Type::DATETIME
        );
		return $this->entityManager->getConnection()->fetchAll('GET_PRODUTOS_BLOQUEADOS', $params, $types);
	}

	public function blockProducts($session, $arrProducts){
		try {
			$this->entityManager->getConnection()->beginTransaction();

            $arrProducts = array_unique($arrProducts);

			$params = array(
				'CDFILIAL' => $session['CDFILIAL'],
				'CDLOJA' =>  $session['CDLOJA'],
				'CDPRODUTO' => '',
				'DTHRBLOQUEIO' => new \DateTime('NOW'),
				'CDOPERADOR' => $session['CDOPERADOR']
			);
			$types = array(
				'DTHRBLOQUEIO' => \Doctrine\DBAL\TypeS\Type::DATETIME
			);

			foreach ($arrProducts as $product){
				$params['CDPRODUTO'] = $product;
				$this->entityManager->getConnection()->executeQuery('INSERT_PRODUTOS_BLOQUEADOS', $params, $types);
			}

			$this->entityManager->commit();

			return array('error' => false);

		} catch(\Exception $e){
			Exception::logException($e);

			$this->entityManager->rollback();

			throw new \Exception ($e->getMessage(), 1);
		}
	}

	public function unblockProducts($session, $arrProducts){
		try {
			$params = array(
				'CDFILIAL' => $session['CDFILIAL'],
				'CDLOJA' =>  $session['CDLOJA'],
				'CDPRODUTO' => array_values($arrProducts),
				'CDOPERADOR' => $session['CDOPERADOR']
			);
			$types = array(
				'CDPRODUTO' => \Doctrine\DBAL\Connection::PARAM_STR_ARRAY
			);

			$this->entityManager->getConnection()->executeQuery('DELETE_PRODUTOS_BLOQUEADOS', $params, $types);

			return array('error' => false);

		} catch(\Exception $e){
			Exception::logException($e);

			throw new \Exception ($e->getMessage(), 1);
		}
	}

	public function impressaoLeituraX($session, &$dadosImpressao){
		$result = $this->caixaApi->imprimeLeituraX($session['CDFILIAL'], null, $session['CDCAIXA'], $session['CDOPERADOR'], $session['NRORG'], false, false, $dadosImpressao);
		if ($result['error']){
			$result['message'] = 'Ocorreu um problema na impressão da Leitura X. <br><br>' . $result['message'];
		}
		return $result;
	}

	public function getNrControlTef($CDNSUHOSTTEF) {
		$params = array(
			'CDNSUHOSTTEF' => $CDNSUHOSTTEF
		);
		$result = array(
			'error' => true,
			'message' => '',
			'data' => ''
		);

		$NRCONTROLTEF = $this->entityManager->getConnection()->fetchAssoc('GET_NRCONTROLTEF', $params);

		if(!empty($NRCONTROLTEF)) {
			if ($NRCONTROLTEF['IDSTATUSNFCE'] != 'C'){
				$result['message'] = 'Não foi possível realizar o estorno do TEF. A venda relacionada precisa estar cancelada.';
			} else {
				$result['error'] = false;
				$result['data'] = $NRCONTROLTEF;
			}
		} else {
			$result['message'] = 'Não há transações com o NSU fornecido.';
		}

		return $result;
	}

	public function ultimasVendasDesc($session, $filter) {
		$CDFILIAL = $session['CDFILIAL'];
		$CDCAIXA = $session['CDCAIXA'];
		$NRORG = $session['NRORG'];

		$estadoCaixa = $this->caixaApi->getEstadoCaixa($CDFILIAL, $CDCAIXA, $NRORG);
		$DTABERCAIX = $this->caixaApi->convertToDateDB($estadoCaixa['DTABERCAIX']);
		$DTABERCAIX = $this->databaseUtil->databaseIsOracle() ? $DTABERCAIX->format('Y-m-d H:i:s') : $DTABERCAIX;

		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'CDCAIXA' => $CDCAIXA,
			'DTABERCAIX' => $DTABERCAIX,
			'FILTER' => $filter
		);

		$types = $this->databaseUtil->databaseIsOracle() ? array() : array('DTABERCAIX' => \Doctrine\DBAL\TypeS\Type::DATETIME);

		$produtos = $this->entityManager->getConnection()->fetchAll('GET_VENDAS', $params, $types);

		foreach ($produtos as &$produto) {
			$produto['VRTOTVENDA'] = number_format($produto['VRTOTVENDA'], 2, ',', '.');
			$produto['DTVENDA'] = date_format(date_create($produto['DTVENDA']), 'd/m/Y H:i:s');
		}

		return $produtos;
	}

	public function ultimosPagementosDesc($session, $FIRST, $LAST) {
		$params = array(
			'CDFILIAL' => $session['CDFILIAL'],
			'CDCAIXA'  => $session['CDCAIXA'],
			'FIRST'    => $FIRST,
			'LAST'     => $LAST
		);

		$pagamentos = $this->entityManager->getConnection()->fetchAll('GET_PAGAMENTOS_TEF', $params);

		foreach ($pagamentos as &$pagamento) {
			$pagamento['VRMOVIVEND'] = number_format($pagamento['VRMOVIVEND'], 2, ',', '.');
			$pagamento['DTHRINCMOV'] = date_format(date_create($pagamento['DTHRINCMOV']), 'd/m/Y H:i:s');
			$pagamento['LABELGRID'] = $pagamento['NMTIPORECE'] . ' - ' . $pagamento['DTHRINCMOV'] . ' - ' . $pagamento['VRMOVIVEND'];
			$pagamento['LABELFIELD'] = $pagamento['NMTIPORECE'] . ' - ' . $pagamento['VRMOVIVEND'];
		}

		return $pagamentos;
	}

	public function getDiscountFidelity($CDFILIAL, $CDCAIXA, $CDCLIENTE, $CDCONSUMIDOR, $CDFAMILISALD, $NRSEQVENDA) {
		$params = array(
			'CDFILIAL'		=> $CDFILIAL,
			'CDCAIXA'  		=> $CDCAIXA,
			'CDCLIENTE'    	=> $CDCLIENTE,
			'CDCONSUMIDOR'  => $CDCONSUMIDOR,
			'CDFAMILISALD'  => $CDFAMILISALD,
			'NRSEQVENDA'    => $NRSEQVENDA
		);

		$FIDELITYVALUE = $this->entityManager->getConnection()->fetchAssoc('GET_DISCOUNT_FIDELITY', $params);
		return !empty($FIDELITYVALUE['VRMOVEXTCONS']) ? floatval($FIDELITYVALUE['VRMOVEXTCONS']) : 0;
	}

	public function tipoRecebimento($session) {
		$params = array(
			'CDFILIAL'   => $session['CDFILIAL'],
			'CDCAIXA'    => $session['CDCAIXA'],
			'DTABERCAIX' => null
		);


		$DTABERCAIX = $this->entityManager->getConnection()->fetchAssoc('GET_REGISTER_OPENING_DATE', $params);

		$DTABERCAIX = !empty($DTABERCAIX) ? $DTABERCAIX['DTABERCAIX'] : null;

		if ($DTABERCAIX == null) throw new \Exception ('Não foi possível encontrar a data de abertura do caixa.', 1);

		$params['DTABERCAIX'] = $this->caixaApi->convertToDateDB($DTABERCAIX);
		$types = array(
			'DTABERCAIX' => \Doctrine\DBAL\TypeS\Type::DATETIME
		);

		$result = $this->entityManager->getConnection()->fetchAll('GET_REGISTER_CLOSING_PAY_N', $params, $types);
		return $result;
	}

	public function tipoSangria() {
		return $this->entityManager->getConnection()->fetchAll('GET_TIPOSANGRIA');
	}
}