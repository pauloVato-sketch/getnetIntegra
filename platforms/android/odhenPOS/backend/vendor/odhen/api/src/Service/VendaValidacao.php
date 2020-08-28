<?php

namespace Odhen\API\Service;
use Odhen\API\Util\Exception;

class VendaValidacao {

    protected $entityManager;
    protected $consumidorApi;

    public function __construct(\Doctrine\ORM\EntityManager $entityManager, \Odhen\Api\Service\Consumidor $consumidorApi) {
        $this->entityManager = $entityManager;
        $this->consumidorApi = $consumidorApi;
    }

    public function validaVenda($CDFILIAL, $CDCAIXA) {
        try {
            $params = array(
                'CDFILIAL' => $CDFILIAL,
                'CDCAIXA' => $CDCAIXA
            );
            $dadosCaixa = $this->entityManager->getConnection()->fetchAssoc("DADOS_CAIXA", $params);
            if ($dadosCaixa['IDTPEMISSAOFOS'] !== 'ECF' && $dadosCaixa['IDTPEMISSAOFOS'] !== 'SAT' && $dadosCaixa['IDTPEMISSAOFOS'] !== 'FNC') {
                throw new \Exception("Não foi possível definir o campo IDTPEMISVEND.");
            }
            $result = array(
                'error' => false
            );
        } catch(\Exception $e) {
            Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
			$result = array(
				'error' => true,
				'message' => $e->getMessage()
            );
        }
        return $result;
    }
    public function validaValor($ITEMVENDA, $TOTALPRODUTOS, $VRDESCVENDA) {
        $result = array(
            'error' => true,
            'TOTALVENDA' => 0,
            'subsidy' => 0,
            'message' => ''
        );

        try {
            $TOTALVENDA = 0;
            $TOTALSUBSIDY = 0;

            foreach($ITEMVENDA as $ITEM){
                if (($ITEM['IDTIPOCOMPPROD'] === '3' && $ITEM['IDIMPPRODUTO'] === '2') || $ITEM['IDTIPOCOMPPROD'] === 'C'){
                    // promoções e combinados
                    foreach ($ITEM['itensCombo'] as $itemComboAtual) {
                        $TOTALVENDA += $itemComboAtual['PRECOFINAL'];
                        $TOTALSUBSIDY += $itemComboAtual['REALSUBSIDY'];
                    }
                }
                else {
                    // produto normal
                    $TOTALVENDA += $ITEM['PRECOFINAL'];
                    $TOTALSUBSIDY += $ITEM['REALSUBSIDY'];
                }
            }

            $VALORVENDAFRONT = round($TOTALPRODUTOS - $VRDESCVENDA, 2);
            if (round($TOTALVENDA, 2) == $VALORVENDAFRONT){
                $result['error'] = false;
                $result['TOTALVENDA'] = $TOTALVENDA;
                $result['subsidy'] = $TOTALSUBSIDY;
            }else{
                $VALORVENDAFRONT = str_replace('.', ',', number_format($VALORVENDAFRONT, 2));
                $TOTALVENDA = str_replace('.', ',', number_format($TOTALVENDA, 2));
                $result['message'] = 'Valor total da venda ('. $VALORVENDAFRONT .') difere da soma dos preços dos produtos (' . $TOTALVENDA . ').';
            }
        } catch(\Exception $e) {
            Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
            $result['message'] = $e->getMessage();
        }

        return $result;
    }

    public function validaVendacreditoPessoal($TIPORECE, $ITEMVENDA, $CDCLIENTE, $CDCONSUMIDOR, $CDFILIAL, $CDCAIXA){
        $result = array(
            'error' => false,
            'FAMSALDOPROD' => array()
        );
        $totalCredito = 0;

        foreach ($TIPORECE as $recebimento) {
            if ($recebimento['IDTIPORECE'] == '9'){
                $totalCredito += $recebimento['VRMOVIVEND'];
            }
        }

        if ($totalCredito > 0) {
            $result = self::validaFamiliaCreditoPessoal($ITEMVENDA, $totalCredito, $CDCLIENTE, $CDCONSUMIDOR, $CDFILIAL, $CDCAIXA);
        }

        return $result;
    }

    public function validaVendaDebitoConsumidor($CDFILIAL, $CDLOJA, $CDOPERADOR, $CDCLIENTE, $CDCONSUMIDOR, $TIPORECE, $TOTALVENDA, $TOTALSUBSIDY){
        $result = array(
            'error' => false,
        );

        if (in_array('A', array_column($TIPORECE, 'IDTIPORECE'))){
            $TIPORECE = array_filter($TIPORECE, function($i){
                return $i['IDTIPORECE'] == "A";
            });

            $this->consumidorApi->atualizaSaldoMoviClie($CDFILIAL, $CDCLIENTE, $CDCONSUMIDOR, $CDOPERADOR);

            $validaValorLimite = $this->consumidorApi->validaValorLimiteDebitoConsumidor($CDFILIAL, $CDLOJA, $CDCLIENTE, $CDCONSUMIDOR, $TIPORECE[0]['VRMOVIVEND']-$TOTALSUBSIDY);
            if (!!$validaValorLimite['error']){
                return $validaValorLimite;
            }

            $validaValorMaximoDiario = $this->consumidorApi->validaMaximoDiarioDebitoConsumidor($CDFILIAL, $CDLOJA, $CDCLIENTE, $CDCONSUMIDOR, $TIPORECE[0]['VRMOVIVEND']-$TOTALSUBSIDY);
            if (!!$validaValorMaximoDiario['error']){
                return $validaValorMaximoDiario;
            }
        }

        return $result;
    }

    public function validaFamiliaCreditoPessoal($ITEMVENDA, $totalCredito, $CDCLIENTE, $CDCONSUMIDOR, $CDFILIAL, $CDCAIXA) {
        $result = array(
            'error' => true,
            'message' => ''
        );

        try {
            $FAMSALDOPROD = self::preparaFamiliaProdutos($ITEMVENDA, $CDFILIAL);

            foreach ($FAMSALDOPROD as $key => $family) {
                $valueByFamily = array_sum(array_column($family, 'PRECOFINAL'));
                $currentBalance = $this->consumidorApi->buscaSaldoExtrato($CDCLIENTE, $CDCONSUMIDOR, $key, $CDFILIAL);
                $VRSALDCONEXT = $totalCredito * -1;
                if (!empty($currentBalance)){
                    $VRSALDCONEXT += floatval($currentBalance['VRSALDCONEXT']);
                }
                $permiteSaldoNegativoFamilia = $this->permiteSaldoNegativoFamilia($CDFILIAL, $key);
                if ($VRSALDCONEXT < 0 && !$permiteSaldoNegativoFamilia){
                    $result['message'] = "Não foi possível concluir a compra. Saldo insuficiente para realizar a compra.";
                    return $result;
                }
                $permiteSaldoNegativoConsumidor = $this->consumidorApi->restricaoSaldoNegativoConsumidor($CDCLIENTE, $CDCONSUMIDOR, $key, $CDFILIAL);
                if ($VRSALDCONEXT < 0 && !$permiteSaldoNegativoConsumidor){
                    $result['message'] = "Não foi possível concluir a compra. Restriçao alimentar. Saldo insuficiente.";
                    return $result;
                }
                $limiteGastoDiario = $this->consumidorApi->restricaoSaldoDiario($CDCLIENTE, $CDCONSUMIDOR, $key, $CDFILIAL);
                if (floatVal($limiteGastoDiario) > 0){
                    $validaLimiteGastoDiario = $this->consumidorApi->restricaoGastoDiario($CDCLIENTE, $CDCONSUMIDOR, $key, $CDFILIAL, $limiteGastoDiario, $valueByFamily);
                    if ($validaLimiteGastoDiario) {
                        $result['message'] = "Não foi possível concluir a compra. Restriçao alimentar. Gasto excedido na familia ".$permiteSaldoNegativoFamilia['NMFAMILISALD'];
                        return $result;
                    }
                }
                foreach ($family as $product){
                    $limiteProduto = $this->consumidorApi->restricaoProdutoDia($CDCLIENTE, $CDCONSUMIDOR, $key, $CDFILIAL, $product['CDPRODUTO']);
                    if (floatval($limiteProduto) > 0){
                        $validaQuantidadeProdutoDiario = $this->consumidorApi->validaQuantidadeProdutoDia($CDCLIENTE, $CDCONSUMIDOR, $CDFILIAL, $CDCAIXA, $product['CDPRODUTO'], $limiteProduto, $product['QTPRODVEND']);
                        if ($validaQuantidadeProdutoDiario){
                            $result['message'] = "Não foi possível concluir a compra. Restriçao alimentar. Quantidade excedida no produto ".$product['CDPRODUTO'];
                            return $result;
                        }
                    }
                }
            }
            $result['error'] = false;
            $result['FAMSALDOPROD'] = $FAMSALDOPROD;
        } catch(\Exception $e){
            Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
            $result['message'] = $e->getMessage();
        }

        return $result;
    }

    private function preparaFamiliaProdutos($ITEMVENDA, $CDFILIAL){
        $FAMSALDOPROD = array();
        foreach ($ITEMVENDA as $item){
            if (($item['IDTIPOCOMPPROD'] == '3' && $item['IDIMPPRODUTO'] == '2') || $item['IDTIPOCOMPPROD'] == 'C'){
                foreach ($item['itensCombo'] as $comboItem){
                    $familiaProduto = $this->getFamiliaProduto($comboItem, $CDFILIAL);
                    if (!empty($FAMSALDOPROD[$familiaProduto['CDFAMILISALD']])){
                        array_push($FAMSALDOPROD[$familiaProduto['CDFAMILISALD']], $familiaProduto);
                    }
                    else {
                        $FAMSALDOPROD[$familiaProduto['CDFAMILISALD']] = array($familiaProduto);
                    }
                }
            }
            else {
                $familiaProduto = $this->getFamiliaProduto($item, $CDFILIAL);
                if (!empty($FAMSALDOPROD[$familiaProduto['CDFAMILISALD']])){
                    array_push($FAMSALDOPROD[$familiaProduto['CDFAMILISALD']], $familiaProduto);
                }
                else {
                    $FAMSALDOPROD[$familiaProduto['CDFAMILISALD']] = array($familiaProduto);
                }
            }
        }
        return $FAMSALDOPROD;
    }

    private function getFamiliaProduto($produto, $CDFILIAL){
		$params = array(
			'CDPRODUTO' => $produto['CDPRODUTO'],
            'CDFILIAL' => $CDFILIAL
		);
		$familiaProduto = $this->entityManager->getConnection()->fetchAssoc("GET_FAMSALDOPROD", $params);
        if (empty($familiaProduto)) throw new \Exception("Não foi possível concluir a compra. O produto ".$produto['NMPRODUTO']." não possui familia.");

        $familiaProduto['PRECOFINAL'] = $produto['PRECOFINAL'];
        $familiaProduto['QTPRODVEND'] = $produto['QTPRODVEND'];
		return $familiaProduto;
    }

    public function permiteSaldoNegativoFamilia($CDFILIAL, $CDFAMILISALD){
        $params = array(
            ':CDFILIAL' => $CDFILIAL,
            ':CDFAMILISALD' => $CDFAMILISALD
        );

        $result = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_PERMITE_SALDO_NEGATIVO", $params);
        return $result['IDSALDNEGFAM'] == 'S';
    }

    public function verificaCancelamento($CDCLIENTE, $CDCONSUMIDOR, $CDFAMILISALD, $NRDEPOSICONS, $NRSEQMOVCAIXA){
        $params = array(
            'CDCLIENTE'     => $CDCLIENTE,
            'CDCONSUMIDOR'  => $CDCONSUMIDOR,
            'CDFAMILISALD'  => $CDFAMILISALD,
            'NRDEPOSICONS'  => $NRDEPOSICONS,
            'NRSEQMOVCAIXA' => $NRSEQMOVCAIXA
        );
        $result = $this->entityManager->getConnection()->fetchAssoc("VERIFICA_CANCELAMENTO", $params);
        return $result ? $result : array();
    }

}
