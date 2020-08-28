<?php

namespace Odhen\API\Lib;

class SatXml {

	protected $entityManager;
	protected $util;

 	public function __construct(
 		\Doctrine\ORM\EntityManager $entityManager,
 		\Odhen\API\Util\Util $util) {

		$this->entityManager = $entityManager;
		$this->util = $util;
	}

	const CNPJ_TEKNISA = '26269316000177';

	const MFE_HOMOLOG_PARAMS = array(
		'CNPJ_SOFT' => '10615281000140',
		'CNPJ_EMIT' => '14200166000166',
		'IE_EMIT' => '1234567890',
		'SIGNAC' => 'CODIGO DE VINCULACAO AC DO MFE-CFE'
	);

	public function montaXML($CDFILIAL, $CDCAIXA, $NRSEQVENDA, $NRORG, $impostoFederal) {
		$produtos = self::buscaProdutosDaVenda($CDFILIAL, $CDCAIXA, $NRSEQVENDA, $NRORG);

		$parametros = self::buscaParametros($CDFILIAL, $CDCAIXA, $NRORG);

		$retornoValidacao = self::validaProdutos($produtos, $parametros['CDPRODTAXSER']);

		if ($retornoValidacao['error'] == false) {
			$recebimentos = self::buscaRecebimentosDaVenda($CDFILIAL, $CDCAIXA, $NRSEQVENDA, $NRORG);
			$xml = self::constroiXML($produtos, $recebimentos, $parametros, $impostoFederal);

			$SATpath = $this->util->getXMLParameter('PATH_NFCE_SAT');
			$year = date('Y');
			$month = date('m');
			$day = date('d');
			$folderName = $SATpath . '/' . $CDFILIAL . '/' . $CDCAIXA . '/' . $year . '-' . $month . '-' . $day . '/LOGS';
			$folderName = str_replace('/', DIRECTORY_SEPARATOR, $folderName);

			$hour = date('H');
			$minutes = date('i');
			$seconds = date('s');
			$fileName = $hour . $minutes . $seconds. '.xml';

			return self::criaDiretorioArquivo($folderName, $fileName, $xml);
		} else {
			return $retornoValidacao;
		}
	}

	private function validaProdutos(&$produtos, $CDPRODTAXSER) {
		$retorno = array(
			'error' => false
		);

		foreach ($produtos as &$produto) {
			if ($produto['CDPRODUTO'] === $CDPRODTAXSER) {
				$aux = $produto['VRUNITVEND'];
				$produto['VRUNITVEND'] = $produto['VRACRITVEND'];
				$produto['VRACRITVEND'] = $aux;
			}

			if (empty($produto['CDCLASFISC'])) {
				$retorno = array(
					'error' => true,
					'message' => 'NCM do produto ' . $produto['CDPRODUTO'] . ' vazio.'
				);
				break;
			} else if (empty($produto['CDCFOPPFIS'])) {
				$retorno = array(
					'error' => true,
					'message' => 'Imposto CFOP do produto ' . $produto['CDPRODUTO'] . ' vazio.'
				);
				break;
			} else if (empty($produto['CDCSTICMS'])) {
				$retorno = array(
					'error' => true,
					'message' => 'CST do ICMS do produto ' . $produto['CDPRODUTO'] . ' vazio.'
				);
				break;
			} else if (empty($produto['CDCSTPISCOF'])) {
				$retorno = array(
					'error' => true,
					'message' => 'CST do PIS/COFINS do produto ' . $produto['CDPRODUTO'] . ' vazio.'
				);
				break;
			} else if (empty($produto['VRALIQPIS']) && $produto['VRALIQPIS'] != 0) {
				$retorno = array(
					'error' => true,
					'message' => 'Alíquota do PIS do produto ' . $produto['CDPRODUTO'] . ' vazio.'
				);
				break;
			} else if (empty($produto['VRALIQCOFINS']) && $produto['VRALIQPIS'] != 0) {
				$retorno = array(
					'error' => true,
					'message' => 'Aliquota do COFINS do produto ' . $produto['CDPRODUTO'] . ' vazio.'
				);
				break;
			}
		}

		return $retorno;
	}

	public function criaArquivoSAT($CDFILIAL, $CDCAIXA, $arquivoCFeBase64, $NRACESSONFCE, $type) {
		$XMLstring = base64_decode($arquivoCFeBase64);
		$XMLobj = simplexml_load_string($XMLstring);
		$XMLide = $XMLobj->infCFe->ide;
		$DOM = new \DOMDocument();
		$DOM->loadXML($XMLstring);
		$DOM->formatOutput = false;
		$xml = $DOM->saveXML($DOM->getElementsByTagName('CFe')->item(0));

		$SATpath = $this->util->getXMLParameter('PATH_NFCE_SAT');
		$year = date('Y');
		$month = date('m');
		$day = date('d');
		$folderName = $SATpath . '/' . $CDFILIAL . '/' . $CDCAIXA . '/' . $year . '-' . $month . '-' . $day;
		$folderName = str_replace('/', DIRECTORY_SEPARATOR, $folderName);
		// type define o tipo do arquivo (venda ou cancelamento) 
		$fileName = $type . $NRACESSONFCE . '.xml';

		$respostaCriacaoDir = self::criaDiretorioArquivo($folderName, $fileName, $xml);
		if ($respostaCriacaoDir['error'] == false) {
			$respostaCriacaoDir['DSARQXMLNFCE'] = $XMLstring;
			$respostaCriacaoDir['NRNOTAFISCALCE'] = (string)$XMLide->nCFe;
			$respostaCriacaoDir['NRLANCTONFCE'] = (string)$XMLide->cNF;
			$respostaCriacaoDir['CDSERIESAT'] = (string)$XMLide->nserieSAT;
			$respostaCriacaoDir['IDTPAMBNFCE'] = (string)$XMLide->tpAmb;
			$result = $respostaCriacaoDir;
		} else {
			$result = $respostaCriacaoDir;
		}
		return $result;
	}

	private function criaDiretorioArquivo($folderName, $fileName, $xml) {
		$createFolder = $this->util->createFolder($folderName);
		if ($createFolder) {
			if (file_put_contents($folderName . DIRECTORY_SEPARATOR . $fileName, $xml)) {
				$result = array(
					'error' => false,
					'xml' => $xml
				);
			} else {
				$result = array(
					'error' => true,
					'message' => 'Não foi possível salvar o arquivo XML do SAT.'
				);
			}
		} else {
			$result = array(
				'error' => true,
				'message' => 'Não foi possível criar a pasta do arquivo XML do SAT.'
			);
		}
		return $result;
	}

	private function buscaProdutosDaVenda($CDFILIAL, $CDCAIXA, $NRSEQVENDA, $NRORG) {
		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'CDCAIXA' => $CDCAIXA,
			'NRSEQVENDA' => $NRSEQVENDA,
			'NRORG' => $NRORG
		);
		$produtos = $this->entityManager->getConnection()->fetchAll("GET_PROD_SAT", $params);
		foreach ($produtos as &$produtoAtual) {
			$produtoAtual['VRRATTXSERV'] = floatval($produtoAtual['VRRATTXSERV']);
			$produtoAtual['VRACRITVEND'] = floatval($produtoAtual['VRACRITVEND']);
			$produtoAtual['VRTOTTRIBIBPT'] = floatval($produtoAtual['VRTOTTRIBIBPT']);
			$produtoAtual['VRRATDESCVEN'] = floatval($produtoAtual['VRRATDESCVEN']);
			$produtoAtual['QTPRODVEND'] = floatval($produtoAtual['QTPRODVEND']);
			$produtoAtual['VRUNITVEND'] = floatval($produtoAtual['VRUNITVEND']);
			$produtoAtual['VRUNITVENDCL'] = floatval($produtoAtual['VRUNITVENDCL']);
			$produtoAtual['VRDESITVEND'] = floatval($produtoAtual['VRDESITVEND']);
			$produtoAtual['VRPEALIMPFIS'] = floatval($produtoAtual['VRPEALIMPFIS']);
			$produtoAtual['VRALIQIBPT'] = floatval($produtoAtual['VRALIQIBPT']);
			$produtoAtual['VRALIQIBPTES'] = floatval($produtoAtual['VRALIQIBPTES']);
			$produtoAtual['VRPERCREDUCAO'] = floatval($produtoAtual['VRPERCREDUCAO']);
			$produtoAtual['VRALIQPIS'] = floatval($produtoAtual['VRALIQPIS']);
			$produtoAtual['VRALIQCOFINS'] = floatval($produtoAtual['VRALIQCOFINS']);
			$produtoAtual['VRDESCVENDA'] = floatval($produtoAtual['VRDESCVENDA']);
			if (!empty($produtoAtual['CDBARPRODUTO'])) {
				$produtoAtual['CDARVPROD'] = $produtoAtual['CDBARPRODUTO'];
			}
			if (empty($produtoAtual['CDCEST'])) {
				$produtoAtual['CDCEST'] = '0000000';
			}
		}
		return $produtos;
	}

	private function buscaRecebimentosDaVenda($CDFILIAL, $CDCAIXA, $NRSEQVENDA, $NRORG) {
		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'CDCAIXA' => $CDCAIXA,
			'NRSEQVENDA' => $NRSEQVENDA,
			'NRORG' => $NRORG
		);
		$recebimentos = $this->entityManager->getConnection()->fetchAll("GET_PAG_SAT", $params);
		foreach ($recebimentos as &$recebimentoAtual) {
			if (empty($recebimentoAtual['ENTRADA'])) {
				$recebimentoAtual['ENTRADA'] = 0;
			} else {
				$recebimentoAtual['ENTRADA'] = floatval($recebimentoAtual['ENTRADA']);
			}
			if (empty($recebimentoAtual['SAIDA'])) {
				$recebimentoAtual['SAIDA'] = 0;
			} else {
				$recebimentoAtual['SAIDA'] = floatval($recebimentoAtual['SAIDA']);
			}
			$recebimentoAtual['VRMOVIVEND'] = $recebimentoAtual['ENTRADA'] - $recebimentoAtual['SAIDA'];
			$recebimentoAtual['QTPARCRECEB'] = floatval($recebimentoAtual['QTPARCRECEB']);
		}
		return $recebimentos;
	}

	private function buscaParametros($CDFILIAL, $CDCAIXA, $NRORG) {
		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'NRORG' => $NRORG,
			'CDCAIXA' => $CDCAIXA
		);
		$parametrosSAT = $this->entityManager->getConnection()->fetchAll("GET_PARAMS_SAT", $params);

		if (!empty($parametrosSAT[0]['CDVINCSATCX'])) {
			$parametrosSAT[0]['CDVINCSAT'] = $parametrosSAT[0]['CDVINCSATCX'];
		}
		unset($parametrosSAT[0]['CDVINCSATCX']);

		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'CDCAIXA' => $CDCAIXA,
			'NRORG' => $NRORG
		);
		$parametrosHomologacao = $this->entityManager->getConnection()->fetchAll("GET_HOMOL_SAT", $params);

		$params = array(
			'CDFILIAL' => $CDFILIAL
		);
		$parametrosCRT = $this->entityManager->getConnection()->fetchAll("GET_CRT_SAT", $params);
		return array_merge($parametrosSAT[0], $parametrosHomologacao[0], $parametrosCRT[0]);
	}

	private function constroiXML($produtos, $recebimentos, $parametros, $impostoFederal) {
		$DOM = new \DOMDocument();
		$DOM->preserveWhiteSpace = false;
		$DOM->formatOutput = false;
		$CFe = $DOM->createElement('CFe');
		$infCFe = $DOM->createElement('infCFe');
		$versaoDadosEnt = $DOM->createAttribute('versaoDadosEnt');
		$versaoDadosEnt->value = '0.07';
		$infCFe->appendChild($versaoDadosEnt);
		$CFe->appendChild($infCFe);
		$DOM->appendChild($CFe);
		$DOM = self::buildIDE($DOM, $parametros);
		$DOM = self::buildEMIT($DOM, $parametros);
		$DOM = self::buildDEST($DOM, $produtos);
		$DOM = self::buildDET($DOM, $parametros, $produtos);
		$DOM = self::buildTOTAL($DOM, $impostoFederal);
		$DOM = self::buildPGTO($DOM, $recebimentos);
		$XML = $DOM->saveXML($CFe);
		return str_replace('"', "'", $XML);
	}

	private function buildIDE($DOM, $parametros) {
		$infCFe = $DOM->getElementsByTagName('infCFe')[0];
		$ide = $DOM->createElement('ide');
		$CNPJ = $DOM->createElement('CNPJ');
		$signAC = $DOM->createElement('signAC');
		$numeroCaixa = $DOM->createElement('numeroCaixa');
		$signACTeknisa = $parametros['CDVINCSAT'];
		if ($parametros['IDTPEQUSAT'] === 'H') {
			if ($parametros['CDSAT'] === '0') {
				$CNPJ->nodeValue = self::CNPJ_TEKNISA;
			} else if ($parametros['CDSAT'] === '1' || $parametros['CDSAT'] === '4') {
				// @todo - constante
				$CNPJ->nodeValue = '16716114000172';
			} else if ($parametros['CDSAT'] === '3') {
				$CNPJ->nodeValue = self::MFE_HOMOLOG_PARAMS['CNPJ_SOFT'];
			} else {
				// @todo - constante
				$CNPJ->nodeValue = '10615281000140';
			}
			if ($parametros['CDSAT'] === '3') {
				$signAC->nodeValue = self::MFE_HOMOLOG_PARAMS['SIGNAC'];
			} else {
				// @todo - constante
				$signAC->nodeValue = 'SGR-SAT SISTEMA DE GESTAO E RETAGUARDA DO SAT';
			}
		} else {
			$CNPJ->nodeValue = self::CNPJ_TEKNISA;
			$signAC->nodeValue = $signACTeknisa;
		}
		$numeroCaixa->nodeValue = $parametros['CDCAIXA'];
		$ide->appendChild($CNPJ);
		$ide->appendChild($signAC);
		$ide->appendChild($numeroCaixa);
		$infCFe->appendChild($ide);
		return $DOM;
	}

	private function buildEMIT($DOM, $parametros) {
		$infCFe = $DOM->getElementsByTagName('infCFe')[0];
		$emit = $DOM->createElement('emit');
		$CNPJ = $DOM->createElement('CNPJ');
		$IE = $DOM->createElement('IE');
		$indRatISSQN = $DOM->createElement('indRatISSQN');
		$NRINSJURFILI = preg_replace('/[^0-9]/', '', $parametros['NRINSJURFILI']);
		$CDINSCESTA = $parametros['CDINSCESTA'];
		if ($parametros['IDTPEQUSAT'] === 'H') {
			if ($parametros['CDSAT'] === '0') {
				$CNPJ->nodeValue = '61099008000141';
			} else if ($parametros['CDSAT'] === '1') {
				$CNPJ->nodeValue = '82373077000171';
			} else if ($parametros['CDSAT'] === '3') {
				$CNPJ->nodeValue = self::MFE_HOMOLOG_PARAMS['CNPJ_EMIT'];
			} else if ($parametros['CDSAT'] === '4') {
				$CNPJ->nodeValue = '14200166000166';
			} else {
				$CNPJ->nodeValue = '53485215000106';
			}
			if ($parametros['CDSAT'] === '2') {
				$IE->nodeValue = '111072115110';
			} else if ($parametros['CDSAT'] === '3') {
				$IE->nodeValue = self::MFE_HOMOLOG_PARAMS['IE_EMIT'];
			} else {
				$IE->nodeValue = '111111111111';
			}
		} else {
			$CNPJ->nodeValue = $NRINSJURFILI;
			$IE->nodeValue = $CDINSCESTA;
		}
		$IDREGESPTRIBEMP = $parametros['IDREGESPTRIBEMP'];
		$cRegTribISSQN = null;
		if ($IDREGESPTRIBEMP != '99') {
			$cRegTribISSQN = $DOM->createElement('cRegTribISSQN');
			$cRegTribISSQN->nodeValue = '1';
		}
		$indRatISSQN->nodeValue = 'N';
		$emit->appendChild($CNPJ);
		$emit->appendChild($IE);
		if (isset($cRegTribISSQN)) {
			$emit->appendChild($cRegTribISSQN);
		}
		$emit->appendChild($indRatISSQN);
		$infCFe->appendChild($emit);
		return $DOM;
	}

	private function buildDEST($DOM, $produtos) {
		$infCFe = $DOM->getElementsByTagName('infCFe')[0];
		$dest = $DOM->createElement('dest');
		$CpfCnpj = isset($produtos[0]['NRINSCRCONS']) ? $produtos[0]['NRINSCRCONS'] : NULL;
		$CPF = null;
		if ($CpfCnpj != null && $CpfCnpj != '') {
			if (strlen($CpfCnpj) == 11) {
				$CPF = $DOM->createElement('CPF');
				$CPF->nodeValue = $CpfCnpj;
			} else {
				$CNPJ = $DOM->createElement('CNPJ');
				$CNPJ->nodeValue = $CpfCnpj;
			}
			if (isset($CPF)) {
				$dest->appendChild($CPF);
			} else {
				$dest->appendChild($CNPJ);
			}
		}
		$infCFe->appendChild($dest);
		return $DOM;
	}

	private function buildDET($DOM, $parametros, $produtos) {
		$infCFe = $DOM->getElementsByTagName('infCFe')[0];
		foreach ($produtos as $key => $product) {
			$det = $DOM->createElement('det');
			$prod = $DOM->createElement('prod');
			$nItem = $DOM->createAttribute('nItem');
			$nItem->value = $key + 1;
			$cProd = $DOM->createElement('cProd');
			$cProd->nodeValue = $product['CDARVPROD'];
			$xProd = $DOM->createElement('xProd');
			$xProd->nodeValue = preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim(htmlspecialchars($product['NMPRODUTO']))));
			$NCM = $DOM->createElement('NCM');
			$NCM->nodeValue = $product['CDCLASFISC'];
			$CFOP = $DOM->createElement('CFOP');
			$CFOP->nodeValue = $product['CDCFOPPFIS'];
			$uCom = $DOM->createElement('uCom');
			$uCom->nodeValue = $product['SGUNIDADE'];
			$qCom = $DOM->createElement('qCom');
			$qCom->nodeValue = number_format($product['QTPRODVEND'], 4, '.', '');
			$vUnCom = $DOM->createElement('vUnCom');

			$vrAcreDesc = $product['VRACRITVEND'] - $product['VRDESITVEND'];
			$vOutro = null;
			$vDesc = null;
			if ($product['CDPRODUTO'] === $product['CDPRODTAXASERV']) {
				$vUnComValue = number_format($vrAcreDesc, 3, '.', '');
			} else {
				$vUnComValue = number_format($product['VRUNITVEND'] + $product['VRUNITVENDCL'], 3, '.', '');
				$vrAcreDesc = number_format($vrAcreDesc, 2, '.', '');
				if ($vrAcreDesc > 0) {
					$vOutro = $DOM->createElement('vOutro');
					$vOutro->nodeValue = $vrAcreDesc;
				} else if ($vrAcreDesc < 0) {
					$vDesc = $DOM->createElement('vDesc');
					$vDesc->nodeValue = number_format($vrAcreDesc * (-1), 2, '.', '');
				}
			}			
			$vUnCom->nodeValue = $vUnComValue;

			$indRegra = $DOM->createElement('indRegra');
			$indRegra->nodeValue = 'T';

			$xTextoDet = null;
			if (($product['CDCSTICMS'] == '60' || $product['CDCSTICMS'] == '90' || $product['CDCSTICMS'] == '900')) {
				$xTextoDet = $DOM->createElement('xTextoDet');
				$xTextoDet->nodeValue = $product['CDCEST'];
			}
			$det->appendChild($nItem);
			$prod->appendChild($cProd);
			$prod->appendChild($xProd);
			$prod->appendChild($NCM);
			$prod->appendChild($CFOP);
			$prod->appendChild($uCom);
			$prod->appendChild($qCom);
			$prod->appendChild($vUnCom);
			$prod->appendChild($indRegra);
			if (isset($vOutro)) {
				$prod->appendChild($vOutro);
			} else if (isset($vDesc)) {
				$prod->appendChild($vDesc);
			}
			if (isset($xTextoDet)) {
				$obsFiscoDet = $DOM->createElement("obsFiscoDet");
				// $obsFiscoDet->appendChild($xCampoDet);
				$obsFiscoDet->appendChild($xTextoDet);
				$xCampoDet = $DOM->createAttribute('xCampoDet');
				$xCampoDet->value = 'Cod. CEST';
				$obsFiscoDet->appendChild($xCampoDet);
				$prod->appendChild($obsFiscoDet);
			}
			$det->appendChild($prod);
			$infCFe->appendChild($det);
			// INSERT <imposto> tag(s)
			$imposto = $DOM->createElement("imposto");
			$porcICMS = $product['IDTPIMPOSFIS'] == 'T' ? $product['VRPEALIMPFIS'] : 0;
			$CDSITUCRT = $parametros['CDSITUCRT'];
			$ICMS = $DOM->createElement('ICMS');
			// INSERT <ICMS> tag(s)
			if ($CDSITUCRT == '1' || $CDSITUCRT == '2') {
				switch ($product['CDCSTICMS']) {
					case '102':
					case '500':
						$ICMSSN102 = $DOM->createElement('ICMSSN102');
						$Orig = $DOM->createElement('Orig');
						$CSOSN = $DOM->createElement('CSOSN');
						$Orig->nodeValue = '0';
						$CSOSN->nodeValue = $product['CDCSTICMS'];
						$ICMSSN102->appendChild($Orig);
						$ICMSSN102->appendChild($CSOSN);
						$ICMS->appendChild($ICMSSN102);
						break;
					case '900':
						$ICMSSN900 = $DOM->createElement('ICMSSN900');
						$Orig = $DOM->createElement('Orig');
						$CSOSN = $DOM->createElement('CSOSN');
						$pICMS = $DOM->createElement('pICMS');
						$Orig->nodeValue = '0';
						$CSOSN->nodeValue = $product['CDCSTICMS'];
						$pICMS->nodeValue = number_format($porcICMS, 2, '.', '');
						$ICMSSN900->appendChild($Orig);
						$ICMSSN900->appendChild($CSOSN);
						$ICMSSN900->appendChild($pICMS);
						$ICMS->appendChild($ICMSSN900);
						break;
				}
			} else if ($CDSITUCRT == '3') {
				switch ($product['CDCSTICMS']) {
					case '20':
						$ICMS00 = $DOM->createElement('ICMS00');
						$Orig = $DOM->createElement('Orig');
						$CST = $DOM->createElement('CST');
						$pICMS = $DOM->createElement('pICMS');
						$Orig->nodeValue = '0';
						$CST->nodeValue = $product['CDCSTICMS'];
						$VRPERCREDUCAO = $product['VRPERCREDUCAO'];
						if ($VRPERCREDUCAO > 0) {
							$porcICMS = $porcICMS - (floatval($VRPERCREDUCAO)/100 * $porcICMS);
						}
						$pICMS->nodeValue = number_format($porcICMS, 2, '.', '');
						$ICMS00->appendChild($Orig);
						$ICMS00->appendChild($CST);
						$ICMS00->appendChild($pICMS);
						$ICMS->appendChild($ICMS00);
						break;
					case '00':
					case '90':
						$ICMS00 = $DOM->createElement('ICMS00');
						$Orig = $DOM->createElement('Orig');
						$CST = $DOM->createElement('CST');
						$pICMS = $DOM->createElement('pICMS');
						$Orig->nodeValue = '0';
						$CST->nodeValue = $product['CDCSTICMS'];
						$VRPERCREDUCAO = $product['VRPERCREDUCAO'];
						$pICMS->nodeValue = number_format($porcICMS, 2, '.', '');
						$ICMS00->appendChild($Orig);
						$ICMS00->appendChild($CST);
						$ICMS00->appendChild($pICMS);
						$ICMS->appendChild($ICMS00);
						break;
					case '40':
					case '41':
					case '50':
					case '60':
						$ICMS40 = $DOM->createElement('ICMS40');
						$Orig = $DOM->createElement('Orig');
						$CST = $DOM->createElement('CST');
						$Orig->nodeValue = '0';
						$CST->nodeValue = $product['CDCSTICMS'];
						$ICMS40->appendChild($Orig);
						$ICMS40->appendChild($CST);
						$ICMS->appendChild($ICMS40);
						break;
				}
			}
			// INSERT <PIS> tag(s)
			// INSERT <COFINS> tag(s)
			$vlrPisProd = 0;
			$vlrCofinsProd = 0;
			$PIS = $DOM->createElement('PIS');
			$COFINS = $DOM->createElement('COFINS');

			// @todo - refatorar cálculo impostos
			switch ($product['CDCSTPISCOF']) {
				case '01':
				case '02':
					// PIS
					$PISAliq = $DOM->createElement('PISAliq');
					$CST = $DOM->createElement('CST');
					$vBC = $DOM->createElement('vBC');
					$pPis = $DOM->createElement('pPIS');
					$CST->nodeValue = $product['CDCSTPISCOF'];
					$VRUNITVEND = $product['VRUNITVEND'];
					$VRUNITVENDCL = $product['VRUNITVENDCL'];
					$QTPRODVEND = $product['QTPRODVEND'];
					$VRACRITVEND = $product['VRACRITVEND'];
					$VRDESITVEND = $product['VRDESITVEND'];
					$vBCvalue = (round($VRUNITVEND + $VRUNITVENDCL, 2) * $QTPRODVEND) + $VRACRITVEND - $VRDESITVEND;
					$vBC->nodeValue = number_format($vBCvalue, 2, '.', '');
					$pPisValue = $product['VRALIQPIS'];
					$pPis->nodeValue = number_format($pPisValue/100, 4, '.', '');
					$PISAliq->appendChild($CST);
					$PISAliq->appendChild($vBC);
					$PISAliq->appendChild($pPis);
					$PIS->appendChild($PISAliq);

					// COFINS
					$COFINSAliq = $DOM->createElement('COFINSAliq');
					$CST = $DOM->createElement('CST');
					$vBC = $DOM->createElement('vBC');
					$pCOFINS = $DOM->createElement('pCOFINS');
					$CST->nodeValue = $product['CDCSTPISCOF'];
					$VRUNITVEND = $product['VRUNITVEND'];
					$VRUNITVENDCL = $product['VRUNITVENDCL'];
					$QTPRODVEND = $product['QTPRODVEND'];
					$VRACRITVEND = $product['VRACRITVEND'];
					$VRDESITVEND = $product['VRDESITVEND'];
					$vBCvalue = (round($VRUNITVEND + $VRUNITVENDCL, 2) * $QTPRODVEND) + $VRACRITVEND - $VRDESITVEND;
					$vBC->nodeValue = number_format($vBCvalue, 2, '.', '');
					$pCOFINSValue = $product['VRALIQCOFINS'];
					$pCOFINS->nodeValue = number_format($pCOFINSValue/100, 4, '.', '');
					$COFINSAliq->appendChild($CST);
					$COFINSAliq->appendChild($vBC);
					$COFINSAliq->appendChild($pCOFINS);
					$COFINS->appendChild($COFINSAliq);
					break;
				case '03':
					$PISQtde = $DOM->createElement('PISQtde');
					$CST = $DOM->createElement('CST');
					$qBCProd = $DOM->createElement('qBCProd');
					$vAliqProd = $DOM->createElement('vAliqProd');
					$CST->nodeValue = $product['CDCSTPISCOF'];
					$qBCProd->nodeValue = $product['QTPRODVEND'];
					$vAliqProd->nodeValue = number_format($product['VRALIQPIS'], 4, '.', '');
					$PISQtde->appendChild($CST);
					$PISQtde->appendChild($vBC);
					$PISQtde->appendChild($pCOFINS);
					$PIS->appendChild($PISQtde);
					break;
				case '04':
				case '06':
				case '07':
				case '08':
				case '09':
					// PIS
					$PISNT = $DOM->createElement('PISNT');
					$CST = $DOM->createElement('CST');
					$CST->nodeValue = $product['CDCSTPISCOF'];
					$PISNT->appendChild($CST);
					$PIS->appendChild($PISNT);

					//COFINS
					$COFINSNT = $DOM->createElement('COFINSNT');
					$CST = $DOM->createElement('CST');
					$CST->nodeValue = $product['CDCSTPISCOF'];
					$COFINSNT->appendChild($CST);
					$COFINS->appendChild($COFINSNT);
					break;
				case '49':
					//PIS
					$PISSN = $DOM->createElement('PISSN');
					$CST = $DOM->createElement('CST');
					$CST->nodeValue = $product['CDCSTPISCOF'];
					$PISSN->appendChild($CST);
					$PIS->appendChild($PISSN);

					//COFINS
					$COFINSSN = $DOM->createElement('COFINSSN');
					$CST = $DOM->createElement('CST');
					$CST->nodeValue = $product['CDCSTPISCOF'];
					$COFINSSN->appendChild($CST);
					$COFINS->appendChild($COFINSSN);
					break;
				case '99':
					// PIS
					$PISOutr = $DOM->createElement('PISOutr');
					$CST = $DOM->createElement('CST');
					$vPIS = $DOM->createElement('vPIS');
					$CST->nodeValue = $product['CDCSTPISCOF'];
					$VRUNITVEND = $product['VRUNITVEND'];
					$VRUNITVENDCL = $product['VRUNITVENDCL'];
					$QTPRODVEND = $product['QTPRODVEND'];
					$VRACRITVEND = $product['VRACRITVEND'];
					$VRDESITVEND = $product['VRDESITVEND'];
					$VRALIQPIS = $product['VRALIQPIS'];
					$vPISvalue = ((round($VRUNITVEND + $VRUNITVENDCL, 2) * $QTPRODVEND) + $VRACRITVEND - $VRDESITVEND) * $VRALIQPIS/100;
					$vPIS->nodeValue = number_format($vPISvalue, 4, '.', '');
					$PISOutr->appendChild($CST);
					$PISOutr->appendChild($vPIS);
					$PIS->appendChild($PISOutr);

					// COFINS
					$COFINSOutr = $DOM->createElement('COFINSOutr');
					$CST = $DOM->createElement('CST');
					$vCOFINS = $DOM->createElement('vCOFINS');
					$CST->nodeValue = $product['CDCSTPISCOF'];
					$VRUNITVEND = $product['VRUNITVEND'];
					$VRUNITVENDCL = $product['VRUNITVENDCL'];
					$QTPRODVEND = $product['QTPRODVEND'];
					$VRACRITVEND = $product['VRACRITVEND'];
					$VRDESITVEND = $product['VRDESITVEND'];
					$VRALIQCOFINS = $product['VRALIQCOFINS'];
					$vCOFINSvalue = ((round($VRUNITVEND + $VRUNITVENDCL, 2) * $QTPRODVEND) + $VRACRITVEND - $VRDESITVEND) * $VRALIQCOFINS/100;
					$vCOFINS->nodeValue = number_format($vCOFINSvalue, 2, '.', '');
					$COFINSOutr->appendChild($CST);
					$COFINSOutr->appendChild($vCOFINS);
					$COFINS->appendChild($COFINSOutr);
					break;
			}
			$imposto->appendChild($ICMS);
			$imposto->appendChild($PIS);
			$imposto->appendChild($COFINS);
			$det->appendChild($imposto);
			$infCFe->appendChild($det);
		}
		return $DOM;
	}

	private function removeProdutoTaxaDoCalculoDeAcrescimo($produtos) {
		$totalAcrescimo = 0;
		foreach($produtos as $produtoAtual) {
			if ($produtoAtual['CDPRODUTO'] !== $produtoAtual['CDPRODTAXASERV']) {
				$totalAcrescimo += $produtoAtual['VRACRITVEND'];
			}
		}
		return $totalAcrescimo;
	}

	private function buildTOTAL($DOM, $impostoFederal) {
		$infCFe = $DOM->getElementsByTagName('infCFe')[0];
		$total = $DOM->createElement('total');
		$vCFeLei12741Value = number_format($impostoFederal, 2, '.', '');
		$vCFeLei12741 = $DOM->createElement('vCFeLei12741');
		$vCFeLei12741->nodeValue = $vCFeLei12741Value;
		$total->appendChild($vCFeLei12741);
		$infCFe->appendChild($total);
		return $DOM;
	}

	private function buildPGTO($DOM, $recebimentos) {
		$infCFe = $DOM->getElementsByTagName('infCFe')[0];
		$pgto = $DOM->createElement('pgto');
		foreach ($recebimentos as $key => $payment) {
			$MP = $DOM->createElement('MP');
			$cMP = $DOM->createElement('cMP');
			$vMP = $DOM->createElement('vMP');
			if ($payment['IDTIPORECE'] == 'A' || $payment['IDTIPORECE'] == 'B') {
				$cMP->nodeValue = '05'; // Crédito Loja
			} else {
				switch ($payment['IDTIPORECE']) {
					case '4':
						$cMP->nodeValue = '01'; // Dinheiro
						break;
					case '3':
						$cMP->nodeValue = '02'; // Cheque
						break;
					case '1':
						$cMP->nodeValue = '03'; //Cartao de Credito
						break;
					case '2':
						$cMP->nodeValue = '04'; //Cartao de Debito
						break;
					case '9':
						$cMP->nodeValue = '05'; //Crédito Loja
						break;
					default:
						$cMP->nodeValue = '99'; // Outros
						break;
				}
			}
			$vMP->nodeValue = number_format($payment['ENTRADA'], 2, '.', '');
			$MP->appendChild($cMP);
			$MP->appendChild($vMP);
			$pgto->appendChild($MP);
		}
		$infCFe->appendChild($pgto);
		return $DOM;
	}

	public function montaXMLCancelamento($CDFILIAL, $CDCAIXA, $NRORG, $NRACESSONFCE) {
		$parametros = self::buscaParametros($CDFILIAL, $CDCAIXA, $NRORG);
		$parametros['NRACESSONFCE'] = $NRACESSONFCE;
		$xml = self::constroiXMLCancelamento($parametros);

		$SATpath = $this->util->getXMLParameter('PATH_NFCE_SAT');
		$year = date('Y');
		$month = date('m');
		$day = date('d');
		$folderName = $SATpath . '/' . $CDFILIAL . '/' . $CDCAIXA . '/' . $year . '-' . $month . '-' . $day . '/LOGS';
		$folderName = str_replace('/', DIRECTORY_SEPARATOR, $folderName);

		$hour = date('H');
		$minutes = date('i');
		$seconds = date('s');
		$fileName = $hour . $minutes . $seconds. '.xml';

		return self::criaDiretorioArquivo($folderName, $fileName, $xml);
	}

	private function constroiXMLCancelamento($parametros) {
		$DOM = new \DOMDocument();
		$DOM->preserveWhiteSpace = false;
		$DOM->formatOutput = false;
		$CFe = $DOM->createElement('CFeCanc');
		$infCFe = $DOM->createElement('infCFe');

		$chCanc = $DOM->createAttribute('chCanc');
		$chCanc->value = $parametros['NRACESSONFCE'];
		$infCFe->appendChild($chCanc);
		$CFe->appendChild($infCFe);
		$DOM->appendChild($CFe);
		$DOM = self::buildIDE($DOM, $parametros);

		$infCFe = $DOM->getElementsByTagName('infCFe')[0];
		// campos preenchidos pelo prórprio SAT
		$emit = $DOM->createElement('emit');
        $dest = $DOM->createElement('dest');
        $total = $DOM->createElement('total');        
		$infCFe->appendChild($emit);
        $infCFe->appendChild($dest);
        $infCFe->appendChild($total);
        
		$XML = $DOM->saveXML($CFe);
		return str_replace('"', "'", $XML);
	}

}