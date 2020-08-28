<?php

namespace Odhen\API\Service;
use Odhen\API\Util\Exception;

class SAT {

    protected $entityManager;
    protected $util;
    protected $satXml;
    protected $satRequest;
    protected $databaseUtil;
    protected $impressaoSAT;

    public function __construct(
        \Doctrine\ORM\EntityManager $entityManager,
        \Odhen\API\Util\Util $util,
        \Odhen\API\Lib\SatXml $satXml,
        \Odhen\API\Remote\SAT\SAT $satRequest,
        \Odhen\API\Util\Database $databaseUtil,
        \Odhen\API\Service\ImpressaoSAT $impressaoSAT) {

        $this->entityManager = $entityManager;
        $this->util = $util;
        $this->satXml = $satXml;
        $this->satRequest = $satRequest;
        $this->databaseUtil = $databaseUtil;
        $this->impressaoSAT = $impressaoSAT;
    }

    private function validaVenda($CDFILIAL, $CDCAIXA, $NRSEQVENDA, $NRORG) {
        if (!empty($CDFILIAL)) {
            if (!empty($CDCAIXA)) {
                if (!empty($NRSEQVENDA)) {
                    if (!empty($NRORG)) {
                        $result = array(
                            'error' => false
                        );
                    } else {
                        $result = array(
                            'error' => true,
                            'message' => 'Código da organização vazio.'
                        );
                    }
                } else {
                    $result = array(
                        'error' => true,
                        'message' => 'Código da venda vazia.'
                    );
                }
            } else {
                $result = array(
                    'error' => true,
                    'message' => 'Código do caixa vazio.'
                );
            }
        } else {
            $result = array(
                'error' => true,
                'message' => 'Código da filial vazio.'
            );
        }
        return $result;
    }

    private function validaDadosSAT($dadosSAT) {
        // @todo: descobrir a descição desses campos
        if (!empty($dadosSAT)) {
            if (!empty($dadosSAT['DSSATHOST'])) {
                if (!empty($dadosSAT['CDATIVASAT'])) {
                    if (isset($dadosSAT['CDSAT'])) {
                        $result = array(
                            'error' => false
                        );
                    } else {
                        $result = array(
                            'error' => true,
                            'message' => 'CDSAT não parametrizado.'
                        );
                    }
                } else {
                    $result = array(
                        'error' => true,
                        'message' => 'CDATIVASAT não parametrizado.'
                    );
                }
            } else {
                $result = array(
                    'error' => true,
                    'message' => 'DSSATHOST não parametrizado.'
                );
            }
        } else {
            $result = array(
                'error' => true,
                'message' => 'Configuração do SAT não encontrada para o caixa.'
            );
        }
        return $result;
    }

	public function vendaSAT($CDFILIAL, $CDCAIXA, $NRSEQVENDA, $NRORG, $NRINSCRCONS, $NMCONSVEND, $CDSENHAPED, $CDOPERADOR, $impostoFederal, $impostoEstadual, $imprimeCupom, $FIDELITYVALUE, $NRMESA) {

		$validacaoVenda = self::validaVenda($CDFILIAL, $CDCAIXA, $NRSEQVENDA, $NRORG);
		if (!$validacaoVenda['error']) {
			$dadosSAT = self::carregaDadosSAT($CDFILIAL, $CDCAIXA, $NRORG);
            $respostaValidacaoSAT = self::validaDadosSAT($dadosSAT);
            if (!$respostaValidacaoSAT['error']) {
                $this->satRequest->setSatInfo($dadosSAT);
                $respostaConsulta = $this->satRequest->consultarSAT();
			    if (!$respostaConsulta['error']) {
					$CDATIVASAT = $dadosSAT['CDATIVASAT'];
					$respostaStatus = $this->satRequest->consultarStatusOperacional($CDATIVASAT);
					if (!$respostaStatus['error']) {
						$respostaXML = $this->satXml->montaXML($CDFILIAL, $CDCAIXA, $NRSEQVENDA, $NRORG, $impostoFederal);
						if (!$respostaXML['error']) {
							$respostaVenda = $this->satRequest->enviarDadosVenda($CDATIVASAT, $respostaXML['xml']);
							if (!$respostaVenda['error']) {
								$respostaCriacaoArquivo = $this->satXml->criaArquivoSAT(
									$CDFILIAL,
									$CDCAIXA,
									$respostaVenda['arquivoCFeBase64'],
									$respostaVenda['NRACESSONFCE'],
                                    'ADe'
								);
								if (!$respostaCriacaoArquivo['error']) {
									$DTEMISSAONFCE = new \DateTime();
									$resultadoAtualizacao = self::atualizaVendaSAT(
										$CDFILIAL,
										$CDCAIXA,
										$NRSEQVENDA,
										$NRORG,
										$respostaVenda['DSQRCODENFCE'],
										$respostaVenda['NRACESSONFCE'],
										$respostaCriacaoArquivo['DSARQXMLNFCE'],
										$respostaCriacaoArquivo['NRNOTAFISCALCE'],
										$respostaCriacaoArquivo['NRLANCTONFCE'],
										$respostaCriacaoArquivo['CDSERIESAT'],
										$respostaCriacaoArquivo['IDTPAMBNFCE'],
										$DTEMISSAONFCE,
										$CDOPERADOR
									);
									if (!$resultadoAtualizacao['error']) {
										if ($imprimeCupom) {
											$resultadoImpressaoCupom = array('error' => false);
										} else {
											$resultadoImpressaoCupom = $this->impressaoSAT->imprimeCupomNF(
												$CDFILIAL,
												$CDCAIXA,
												$NRORG,
												$NRSEQVENDA,
												$respostaCriacaoArquivo['NRNOTAFISCALCE'],
												$respostaCriacaoArquivo['CDSERIESAT'],
												$DTEMISSAONFCE,
												$respostaVenda['NRACESSONFCE'],
												$NRINSCRCONS,
												$NMCONSVEND,
												$CDSENHAPED,
												$respostaVenda['DSQRCODENFCE'],
												$impostoFederal,
												$impostoEstadual,
                                                $FIDELITYVALUE,
                                                array(),
                                                $NRMESA
											);
										}
										$result = array(
											'dadosImpressao' => isset($resultadoImpressaoCupom['dadosImpressao']) ? $resultadoImpressaoCupom['dadosImpressao'] : array(),
											'error' => false,
											'xml' => $respostaCriacaoArquivo['DSARQXMLNFCE'],
                                            'mensagemImpressao' => '',
                                            'DSQRCODE' => wordwrap(substr($respostaVenda['NRACESSONFCE'], 3), 4, ' ', true),
                                            'errPainelSenha' => !empty($resultadoImpressaoCupom['errPainelSenha']) ? $resultadoImpressaoCupom['errPainelSenha'] : ''
										);
                                        if ($resultadoImpressaoCupom['error']) {
										  $result['mensagemImpressao'] = $resultadoImpressaoCupom['message'];
                                        }
									} else {
										$result = $resultadoAtualizacao;
									}
								} else {
									$result = $respostaCriacaoArquivo;
								}
							} else {
								$result = $respostaVenda;
							}
						} else {
							$result = $respostaXML;
						}
					} else {
						$result = $respostaStatus;
					}
				} else {
                    $result = $respostaConsulta;
				}
			} else {
                $result = $respostaValidacaoSAT;
			}
		} else {
			$result = $validacaoVenda;
		}

        return $result;
    }

    private function carregaDadosSAT($CDFILIAL, $CDCAIXA, $NRORG) {
        $params = array(
            'CDFILIAL' => $CDFILIAL,
            'CDCAIXA' => $CDCAIXA,
            'NRORG' => $NRORG
        );
        return $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_SAT", $params);
    }

    private function atualizaVendaSAT($CDFILIAL, $CDCAIXA, $NRSEQVENDA, $NRORG, $DSQRCODENFCE, $NRACESSONFCE, $DSARQXMLNFCE, $NRNOTAFISCALCE,
        $NRLANCTONFCE, $CDSERIESAT, $IDTPAMBNFCE, $DTEMISSAONFCE, $CDOPERADOR) {
        try {
            $params = array(
                'IDSTATUSNFCE'   => 'A',
                'DSQRCODENFCE'   => $DSQRCODENFCE,
                'NRACESSONFCE'   => $NRACESSONFCE,
                'NRNOTAFISCALCE' => $NRNOTAFISCALCE,
                'NRLANCTONFCE'   => $NRLANCTONFCE,
                'DSARQXMLNFCE'   => $DSARQXMLNFCE,
                'CDSERIESAT'     => $CDSERIESAT,
                'IDTPAMBNFCE'    => $IDTPAMBNFCE,
                'DTEMISSAONFCE'  => $DTEMISSAONFCE,
                'CDFILIAL'       => $CDFILIAL,
                'CDCAIXA'        => $CDCAIXA,
                'NRSEQVENDA'     => $NRSEQVENDA,
                'NRORG'          => $NRORG,
                'CDOPERULTATU'   => $CDOPERADOR
            );
            $type = array(
                'DTEMISSAONFCE' => \Doctrine\DBAL\Types\Type::DATETIME
            );

            if (!$this->util->databaseIsOracle()){
                $type['DSARQXMLNFCE'] = \Doctrine\DBAL\Types\Type::BINARY;
            }

            $this->entityManager->getConnection()->executeQuery("ATUALIZA_VENDA_SAT", $params, $type);

            $result = array(
                'error' => false
            );
        } catch (\Exception $e) {
            Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
            $result = array(
                'error' => true,
                'message' => $e->getMessage()
            );
        }
        return $result;
    }

    public function cancelaVendaSAT($CDFILIAL, $CDCAIXA, $NRORG, $NRSEQVENDA, $venda){
        $dadosSAT = self::carregaDadosSAT($CDFILIAL, $CDCAIXA, $NRORG);
        $this->satRequest->setSatInfo($dadosSAT);

        $respostaValidacaoSAT = self::validaDadosSAT($dadosSAT);
        if (!$respostaValidacaoSAT['error']) {
            $xml = $this->satXml->montaXMLCancelamento($CDFILIAL, $CDCAIXA, $NRORG, $venda['NRACESSONFCE']);
            if(!$xml['error']){
                $cancelaCupom = $this->satRequest->cancelarVendaSAT($dadosSAT['CDATIVASAT'], $venda['NRACESSONFCE'], $xml['xml']);
                if (!$cancelaCupom['error']){
                    $respostaCriacaoArquivo = $this->satXml->criaArquivoSAT(
                        $CDFILIAL,
                        $CDCAIXA,
                        $cancelaCupom['arquivoCFeBase64'],
                        $cancelaCupom['NRACESSONFCE'],
                        'ADc'
                    );
                    $cancelaCupom['dadosImpressao'] = array();
                    if (!$respostaCriacaoArquivo['error']){
                        $imprimeCupomCancelado = $this->impressaoSAT->imprimeCupomCanceladoSAT($CDFILIAL, $CDCAIXA, $NRORG, $respostaCriacaoArquivo, $cancelaCupom, $venda);
                        if ($imprimeCupomCancelado['error']){
                            $cancelaCupom['mensagemImpressao'] = $imprimeCupomCancelado['message'];
                        } else {
                            $cancelaCupom['dadosImpressao'] = $imprimeCupomCancelado['dadosImpressao'];
                        }
                    } else {
                        $cancelaCupom['mensagemNfce'] = $respostaCriacaoArquivo['message'];
                    }
                }
                return $cancelaCupom;
            }else{
                return $xml;
            }
        }else{
            return $respostaValidacaoSAT;
        }
    }
}