<?php

namespace Odhen\API\Service;

use Odhen\API\Remote\Printer\Command;

class ImpressaoPedido {

    protected $entityManager;
    protected $impressaoUtil;
    protected $instanceManager;
	protected $util;

    public function __construct(
        \Doctrine\ORM\EntityManager $entityManager,
        \Odhen\API\Lib\ImpressaoUtil $impressaoUtil,
        \Zeedhi\Framework\DependencyInjection\InstanceManager $instanceManager,
        \Odhen\API\Util\Util $util
    ) {
        $this->entityManager = $entityManager;
        $this->impressaoUtil = $impressaoUtil;
        $this->comandos = array();
        $this->instanceManager = $instanceManager;
        $this->util 	     = $util;
    }

    public function imprimePedido($CDFILIAL, $CDLOJA, $produtos, $CDVENDEDOR, $DSCOMANDA, $NRMESA, $CDSENHAPED,
                                  $tipoVenda, $toGoString, $multiplasComandas, $ultimaComanda, $NRSEQVENDA, $CDCAIXA){
        $params = array(
            'CDFILIAL' => $CDFILIAL,
            'CDLOJA' => $CDLOJA
        );
        $dadosLoja = $this->entityManager->getConnection()->fetchAll("BUSCA_DADOS_LOJA", $params);
        $IDLUGARMESA = $dadosLoja[0]['IDLUGARMESA'];
        $IDPOSOBSPED = $dadosLoja[0]['IDPOSOBSPED'];
        $IDUTLCORTEPED = $dadosLoja[0]['IDUTLCORTEPED'];

        // TipoVenda B (balcão) do TAA não tem mesa.
        if (!empty($NRMESA)) {
            $params = array(
                'CDFILIAL' => $CDFILIAL,
                'CDLOJA' => $CDLOJA,
                'NRMESA' => $NRMESA
            );
            $dadosMesa = $this->entityManager->getConnection()->fetchAll("BUSCA_DADOS_MESA", $params);
            $NMSALA = $dadosMesa[0]['NMSALA'];
        }
        else $NMSALA = null;

        $params = array(
            'CDVENDEDOR' => $CDVENDEDOR
        );
        $dadosVendedor = $this->entityManager->getConnection()->fetchAll("BUSCA_DADOS_VENDEDOR", $params);

        if (!empty($dadosVendedor[0]['NMFANVEN'])) $NMFANVEN = $dadosVendedor[0]['NMFANVEN'];
        else $NMFANVEN = "";

        // Verifica parametro IDIMPRODUVEZ dos produtos para replicá-los para impressão.
        $produtos = $this->verificaImpUmaVez($produtos);
        // Reorganiza o array para que os produtos que serão impressos separados (IDIMPRODUVEZ) fiquem no final.
        $produtos = $this->alteraOrdemImpressao($produtos);

        $produtosIndexadosPorImpressora = self::indexaProdutosPorImpressora($CDFILIAL, $CDLOJA, $produtos);
        $impressoraProducao = $produtosIndexadosPorImpressora['impressoraProducao'];
        $impressoraProducao2 = $produtosIndexadosPorImpressora['impressoraProducao2'];
        $impressoraPuxa = $produtosIndexadosPorImpressora['impressoraPuxa'];
        $result = array(
            'error' => false
        );
        foreach ($impressoraProducao as $impressoraAtual){
            $cutByPosition = $IDUTLCORTEPED === 'S';
            $respostaPedido = self::montaPedido($impressoraAtual, $DSCOMANDA, $NRMESA, $NMSALA, $CDVENDEDOR,
             $NMFANVEN, $CDSENHAPED, $IDLUGARMESA, $IDPOSOBSPED, $tipoVenda, $toGoString, $cutByPosition, $multiplasComandas, $ultimaComanda, $NRSEQVENDA, $CDCAIXA, $CDFILIAL, $produtos);
            if ($respostaPedido['error']) $result = $respostaPedido;
        }
        foreach ($impressoraProducao2 as $impressoraAtual){
            $cutByPosition = $IDUTLCORTEPED === 'S';
            $respostaPedido = self::montaPedido($impressoraAtual, $DSCOMANDA, $NRMESA, $NMSALA, $CDVENDEDOR,
             $NMFANVEN, $CDSENHAPED, $IDLUGARMESA, $IDPOSOBSPED, $tipoVenda, $toGoString, $cutByPosition, $multiplasComandas, $ultimaComanda, $NRSEQVENDA, $CDCAIXA, $CDFILIAL, $produtos);
            if ($respostaPedido['error']) $result = $respostaPedido;
        }
        foreach ($impressoraPuxa as $impressoraAtual){
            $respostaPedido = self::montaPedido($impressoraAtual, $DSCOMANDA, $NRMESA, $NMSALA, $CDVENDEDOR,
             $NMFANVEN, $CDSENHAPED, $IDLUGARMESA, $IDPOSOBSPED, $tipoVenda, $toGoString, false, $multiplasComandas, $ultimaComanda, $NRSEQVENDA, $CDCAIXA, $CDFILIAL, $produtos);
            if ($respostaPedido['error']) $result = $respostaPedido;
        }

        if($ultimaComanda){
            $issaas = $this->util->isSaas();
            $result['paramsImpressora'] = [];
            foreach ($this->comandos as $comando) {
                if($issaas){
                    array_push($result['paramsImpressora'],
                            array(  'saas'      => true,
                                    'impressora'=> $comando['impressora'],
                                    'comandos'  => $comando['comandos']->getCommands(),
                                    'error'     => false)
                            );
                    $respostaPonte['error'] = false;
                }else{
                    $resultImp = $this->impressaoUtil->impressaoPedidos($comando['impressora'], $comando['comandos'], $DSCOMANDA);
                    if($resultImp['error']){
                        $result = $resultImp;
                    }
                }
            }
            $this->comandos = array();
        }
        return $result;
    }

    private function montaPedido($impressoraAtual, $DSCOMANDA, $NRMESA, $NMSALA, $CDVENDEDOR, $NMFANVEN, $CDSENHAPED, $IDLUGARMESA, $IDPOSOBSPED, $tipoVenda, $toGoString, $cutByPosition, $multiplasComandas, $ultimaComanda, $NRSEQVENDA, $CDCAIXA, $CDFILIAL, $produtos) {

        $posicoes = array_unique(array_column($impressoraAtual['produtos'], 'NRLUGARMESA'));

        if(!isset($this->comandos[$impressoraAtual['NRSEQIMPRLOJA']])){
            $this->comandos[$impressoraAtual['NRSEQIMPRLOJA']]['comandos'] = new Command();
            $this->comandos[$impressoraAtual['NRSEQIMPRLOJA']]['impressora'] = $impressoraAtual;
        }

        if($cutByPosition){
            foreach ($posicoes as $posicao) {
                $retornoImprime =  $this->_imprimePedido($impressoraAtual, $DSCOMANDA, $NRMESA, $NMSALA, $CDVENDEDOR, $NMFANVEN, $CDSENHAPED,
                    $IDLUGARMESA, $IDPOSOBSPED, $tipoVenda, $toGoString, array($posicao), $this->comandos[$impressoraAtual['NRSEQIMPRLOJA']]['comandos'], $multiplasComandas, $NRSEQVENDA, $CDCAIXA, $CDFILIAL, $produtos);
                if($retornoImprime['error']){
                    return $retornoImprime;
                }
            }
        } else {
            $retornoImprime = $this->_imprimePedido($impressoraAtual, $DSCOMANDA, $NRMESA, $NMSALA, $CDVENDEDOR, $NMFANVEN, $CDSENHAPED,
                $IDLUGARMESA, $IDPOSOBSPED, $tipoVenda, $toGoString, $posicoes, $this->comandos[$impressoraAtual['NRSEQIMPRLOJA']]['comandos'], $multiplasComandas, $NRSEQVENDA, $CDCAIXA, $CDFILIAL, $produtos);
        }
        if($retornoImprime['error']){
            return $retornoImprime;
        }else{
            if($multiplasComandas){
                if($ultimaComanda){
                    foreach ($this->comandos as $comando) {
                        $comando['comandos']->cutPaper();
                    }
                }
                return array('error' => false);
            }else{
                return array('error' => false);
            }
        }
    }

    private function _imprimePedido($impressoraAtual, $DSCOMANDA, $NRMESA, $NMSALA, $CDVENDEDOR, $NMFANVEN, $CDSENHAPED, $IDLUGARMESA, $IDPOSOBSPED, $tipoVenda, $toGoString, $posicoes, $comandos, $multiplasComandas, $NRSEQVENDA, $CDCAIXA, $CDFILIAL, $dadosPedido) {
        $produtos = $impressoraAtual['produtos'];
        $tipoImpressao = $impressoraAtual['tipoImpressao'];

        $printerParams = $this->impressaoUtil->buscaParametrosImpressora($impressoraAtual['IDMODEIMPRES']);
        $printerParams['letterType'] = $printerParams['tipoLetra'];
        //da pra otimizar
        $arrNmPosicao = $this->buscaNomePorPosicao($produtos, $posicoes);
        $textHeader = '';
        $textHeader = $this->printHeader($textHeader, $printerParams, $tipoVenda, $DSCOMANDA, $NRMESA, $NMSALA,
                                         $CDVENDEDOR, $NMFANVEN, $tipoImpressao, $toGoString, $comandos);

        //command header
        $printerParams['bold'] = false;
        $comandos->text($textHeader, $printerParams);

        // Define footer.
        $textFooter = $printerParams['comandoEnter'];
        $textFooter .= $this->printFooter($textFooter, $printerParams, $CDSENHAPED);

        foreach ($posicoes as $posicao) {
            //imprime a posição no pedido
            $textPosicao = '';
            if (($IDLUGARMESA === 'S') && ($tipoVenda === 'M')) {
                $this->impressaoUtil->checaEnter($textPosicao, $printerParams);
                $strPosicao = 'POSICAO ' . $posicao;

                if (isset($arrNmPosicao[$posicao])){
                    $strPosicao .= ' - ' . $arrNmPosicao[$posicao]['DSCONSUMIDOR'];
                    if (strlen($strPosicao) > $printerParams['largura']) {
                        $strPosicao = $this->impressaoUtil->quebraLinha($strPosicao, $printerParams, true);
                    }
                }
                $textPosicao .= $strPosicao . $printerParams['comandoEnter'];
            }
            //command posição
            $printerParams['bold'] = false;
            if($textPosicao != '') {
                $comandos->text($textPosicao, $printerParams);
            }

            $textProdutos = '';
            $ordemImp = 0;
            foreach ($produtos as $key => $produtoAtual){
                //imprime produto
                if ($posicao == $produtoAtual['NRLUGARMESA']){
                    if (!empty($produtoAtual['ORDEMIMP']) && ($produtoAtual['ORDEMIMP'] - $ordemImp >= 1) && ($ordemImp != 0)) {
                        // Quando acabar os produtos da promoção, é impressa uma linha.
                        $printerParams['bold'] = false;
                        $this->impressaoUtil->imprimeLinhaCommand($printerParams, $comandos);

                        $ordemImp = 0;
                        $textProdutos = '';
                    }

                    // Caso o pedido seja impresso separado, imprime o rodapé e cabeçalho novamente.
                    if ($produtoAtual['IDIMPRODUVEZ'] === 'S'){
                        $comandos->text($textFooter, $printerParams);
                        $comandos->text($textHeader, $printerParams);
                    }

                    // Cabeçalho da promoção inteligente.
                    if ($produtoAtual['IDIMPPROMOCAO'] === 'S') {
                        $printerParams['bold'] = false;
                        $this->impressaoUtil->imprimeLinhaCommand($printerParams, $comandos);

                        $textProdutos .= $this->impressaoUtil->centraliza($printerParams, $produtoAtual['NMPRODPROMOCAO']) . $printerParams['comandoEnter'];
                        if (!empty($produtoAtual['TXPRODCOMVEN'])){
                            // Imprime observação do produto pai.
                            $textProdutos .= $this->impressaoUtil->centraliza($printerParams, '* ' . $produtoAtual['TXPRODCOMVEN'] . $printerParams['comandoEnter']);
                            $produtoAtual['TXPRODCOMVEN'] = null;
                        }
                        $printerParams['bold'] = true;
                        $comandos->text($textProdutos, $printerParams);
                        $textProdutos = '';

                        $printerParams['bold'] = false;
                        $this->impressaoUtil->imprimeLinhaCommand($printerParams, $comandos);

                        // Variável usada para imprimir a última linha da promoção inteligente.
                        $ordemImp = bcdiv(str_replace(',','.',strval($produtoAtual['ORDEMIMP'])), '1', '0');
                    }

                    // Verifica se é para imprimir observações antes do produto.
                    if ($IDPOSOBSPED === 'A'){
                        if (($produtoAtual['TXPRODCOMVEN'] !== " ") && ($produtoAtual['TXPRODCOMVEN'] != '')){
                            $textProdutos .= '   *  ' . $produtoAtual['TXPRODCOMVEN'] . $printerParams['comandoEnter'];
                        }

                        if ($produtoAtual['ATRASOPROD']){
                            $textProdutos .= '   *  Segura de ' . $produtoAtual['ATRASOPROD'] . ' minutos' . $printerParams['comandoEnter'];
                        }
                    }

                    // Imprime a linha do produto, exceto se for cabeçalho da promoção.
                    if ($produtoAtual['IDIMPPROMOCAO'] !== 'S') {
                        $textProdutos .= ' ---> ' . $produtoAtual['QTPRODCOMVEN'] . ' ' . self::removeAcentos($produtoAtual['NMPRODUTO']) . $printerParams['comandoEnter'];
                    }

                    // Imprime observação depois do produto.
                    if ($IDPOSOBSPED === 'D'){
                        if (($produtoAtual['TXPRODCOMVEN'] !== " ") && ($produtoAtual['TXPRODCOMVEN'] != '')){
                            $textProdutos .= '   *  ' . $produtoAtual['TXPRODCOMVEN'] . $printerParams['comandoEnter'];
                        }

                        if ($produtoAtual['ATRASOPROD']){
                            $textProdutos .= '   *  Segura de ' . $produtoAtual['ATRASOPROD'] . ' minutos' . $printerParams['comandoEnter'];
                        }
                    }

                    // Imprime indicação de que o produto é para viagem.
                    if (!empty($produtoAtual['IDORIGEMVENDA'])) {
                        $textProdutos .= '   *  Para viagem ' . $printerParams['comandoEnter'];
                    }

                    //tira do array pra nao precisar checar na proxima observacao
                    unset($produtos[$key]);
                    //command produto
                    if ($textProdutos != ''){
                        $printerParams['bold'] = true;
                        $printerParams['letterType'] = '2';
                        $comandos->text($textProdutos, $printerParams);
                        $textProdutos = '';
                    }
                    $printerParams['letterType'] = $printerParams['tipoLetra'];
                }
            }
            //sem tracinho :<
            //if($posicao == posicoes[count(posicoes)-1])
            //$this->impressaoUtil->imprimeLinhaCommand($printerParams, $comandos);
        }
        $textFooter = '';
        $textFooter = $this->printFooter($textFooter, $printerParams, $CDSENHAPED);
        $printerParams['bold'] = false;
        $comandos->text($textFooter, $printerParams);
        $barCodeOptions = array(
            'height'   => 50,
            'width'    => 1,
            'position' => 0,
            'font'     => 0,
            'margin'   => 25
        );
        if (!empty($NRSEQVENDA))
            $comandos->barCode($CDCAIXA.$CDFILIAL.$NRSEQVENDA, $barCodeOptions);
        else
            $comandos->barCode($CDFILIAL.$dadosPedido[0]["NRVENDAREST"].$dadosPedido[0]["NRCOMANDA"], $barCodeOptions);
        $text = $printerParams['comandoEnter'];
        $text .= $printerParams['comandoEnter'];
        $text .= $printerParams['comandoEnter'];
        $text .= $printerParams['comandoEnter'];
        $comandos->text($text, $printerParams);
        if(!$multiplasComandas){

            $comandos->cutPaper();
        }
        return array('error' => false);
    }

    private function indexaProdutosPorImpressora($CDFILIAL, $CDLOJA, $produtos) {
        $impressoraProducao = array();
        $impressoraProducao2 = array();
        $impressoraPuxa = array();
        foreach ($produtos as $produtoAtual) {
            if (empty($produtoAtual['ORDEM'])) {
                $produtoAtual['ORDEM'] = 0;
            }
            if ($produtoAtual['IDTIPOCOMPPROD'] == '3') {
                $produtoAtual['IDIMPPROMOCAO'] = 'S';
                $params = array(
                    'CDFILIAL' => $CDFILIAL,
                    'CDLOJA' => $CDLOJA,
                    'CDPRODUTO' => $produtoAtual['CDPRODUTO']
                );
                $impressoras = $this->entityManager->getConnection()->fetchAll("BUSCA_IMPRESSORAS", $params);
                self::buscaImpressoraPorProduto($impressoras, $produtoAtual, $CDFILIAL, $CDLOJA, $impressoraProducao, $impressoraProducao2, $impressoraPuxa);
            } else {
                $params = array(
                    'CDFILIAL' => $CDFILIAL,
                    'CDLOJA' => $CDLOJA,
                    'CDPRODUTO' => $produtoAtual['CDPRODUTO']
                );
                $impressoras = $this->entityManager->getConnection()->fetchAll("BUSCA_IMPRESSORAS", $params);
                self::buscaImpressoraPorProduto($impressoras, $produtoAtual, $CDFILIAL, $CDLOJA, $impressoraProducao, $impressoraProducao2, $impressoraPuxa);
            }
        }
        return array(
            'impressoraProducao' => $impressoraProducao,
            'impressoraProducao2' => $impressoraProducao2,
            'impressoraPuxa' => $impressoraPuxa
        );
    }

    private function buscaImpressoraPorAmbiente($CDFILIAL, $CDLOJA, $produtoAtual) {
    	$impressoraPorAmbiente = array();
    	if (!empty($produtoAtual['CDSALA']) && empty($produtoAtual['NRSEQIMPRLOJA'])) {
			$params = array(
                'CDFILIAL' => $CDFILIAL,
                'CDLOJA' => $CDLOJA,
                'CDPRODUTO' => $produtoAtual['CDPRODUTO'],
                'NRCONFTELA' => $produtoAtual['NRCONFTELA'],
                'DTINIVIGENCIA' => new \DateTime($produtoAtual['DTINIVIGENCIA']),
                'CDAMBIENTE' => $produtoAtual['CDSALA']
            );
            $types = array(
                'DTINIVIGENCIA' => \Doctrine\DBAL\Types\Type::DATETIME
            );
            $retornoQuery = $this->entityManager->getConnection()->fetchAll("BUSCA_IMPRESSORAS_POR_AMBIENTE", $params, $types);
            if (sizeof($retornoQuery) === 1) {
            	$impressoraPorAmbiente = $retornoQuery;
            }
    	}
    	return $impressoraPorAmbiente;
    }

    private function buscaImpressoraPorProduto($impressoras, $produtoAtual, $CDFILIAL, $CDLOJA, &$impressoraProducao, &$impressoraProducao2, &$impressoraPuxa){
        if (!empty($produtoAtual['NRSEQIMPRLOJA'])) {
            $params = array(
                'CDFILIAL' => $CDFILIAL,
                'CDLOJA' => $CDLOJA,
                'NRSEQIMPRLOJA' => $produtoAtual['NRSEQIMPRLOJA']
            );
            $dadosImpressora = $this->entityManager->getConnection()->fetchAll("BUSCA_DADOS_IMPRESSORA_PED", $params);
            if (!empty($impressoraProducao[$dadosImpressora[0]['NRSEQIMPRLOJA']])) {
                array_push($impressoraProducao[$dadosImpressora[0]['NRSEQIMPRLOJA']]['produtos'], $produtoAtual);
            } else {
                $impressoraProducao[$dadosImpressora[0]['NRSEQIMPRLOJA']] = array(
                    'NRSEQIMPRLOJA' => $dadosImpressora[0]['NRSEQIMPRLOJA'],
                    'IDMODEIMPRES' => $dadosImpressora[0]['IDMODEIMPRES'],
                    'tipoImpressao' => 'P',
                    'CDPORTAIMPR' => $dadosImpressora[0]['CDPORTAIMPR'],
                    'DSENDPORTA' => $dadosImpressora[0]['DSENDPORTA'],
                    'DSIPIMPR' => $dadosImpressora[0]['DSIPIMPR'],
                    'DSIPPONTE' => $dadosImpressora[0]['DSIPPONTE'],
                    'NMIMPRLOJA' => $dadosImpressora[0]['NMIMPRLOJA'],
                    'produtos' => array($produtoAtual)
                );
            }
        } else {
        	$impressoraPorAmbiente = $this->buscaImpressoraPorAmbiente($CDFILIAL, $CDLOJA, $produtoAtual);
        	if (!empty($impressoraPorAmbiente[0]['NRSEQIMPRLOJA'])) {
        		if (!empty($impressoraProducao[$impressoraPorAmbiente[0]['NRSEQIMPRLOJA']])) {
	                array_push($impressoraProducao[$impressoraPorAmbiente[0]['NRSEQIMPRLOJA']]['produtos'], $produtoAtual);
	            } else {
	                $impressoraProducao[$impressoraPorAmbiente[0]['NRSEQIMPRLOJA']] = array(
	                    'NRSEQIMPRLOJA' => $impressoraPorAmbiente[0]['NRSEQIMPRLOJA'],
	                    'IDMODEIMPRES' => $impressoraPorAmbiente[0]['IDMODEIMPRES'],
	                    'tipoImpressao' => 'P',
	                    'CDPORTAIMPR' => $impressoraPorAmbiente[0]['CDPORTAIMPR'],
	                    'DSENDPORTA' => $impressoraPorAmbiente[0]['DSENDPORTA'],
	                    'DSIPIMPR' => $impressoraPorAmbiente[0]['DSIPIMPR'],
	                    'DSIPPONTE' => $impressoraPorAmbiente[0]['DSIPPONTE'],
                        'NMIMPRLOJA' => $impressoraPorAmbiente[0]['NMIMPRLOJA'],
	                    'produtos' => array($produtoAtual)
	                );
	            }
	        } else if (!empty($impressoras[0]['A_NRSEQIMPRPROD'])) {
	            if (!empty($impressoraProducao[$impressoras[0]['A_NRSEQIMPRPROD']])) {
	                array_push($impressoraProducao[$impressoras[0]['A_NRSEQIMPRPROD']]['produtos'], $produtoAtual);
	            } else {
	                $impressoraProducao[$impressoras[0]['A_NRSEQIMPRPROD']] = array(
	                    'NRSEQIMPRLOJA' => $impressoras[0]['A_NRSEQIMPRPROD'],
	                    'IDMODEIMPRES' => $impressoras[0]['A_IDMODEIMPRES'],
	                    'tipoImpressao' => 'P',
	                    'CDPORTAIMPR' => $impressoras[0]['A_CDPORTAIMPR'],
	                    'DSENDPORTA' => $impressoras[0]['A_DSENDPORTA'],
	                    'DSIPIMPR' => $impressoras[0]['A_DSIPIMPR'],
	                    'DSIPPONTE' => $impressoras[0]['A_DSIPPONTE'],
                        'NMIMPRLOJA' => $impressoras[0]['A_NMIMPRLOJA'],
	                    'produtos' => array($produtoAtual)
	                );
	            }
	        }
	    }

        if (!empty($impressoras[0]['B_NRSEQIMPRPROD2'])) {
            if (!empty($impressoraProducao2[$impressoras[0]['B_NRSEQIMPRPROD2']])) {
                array_push($impressoraProducao2[$impressoras[0]['B_NRSEQIMPRPROD2']]['produtos'], $produtoAtual);
            } else {
                $impressoraProducao2[$impressoras[0]['B_NRSEQIMPRPROD2']] = array(
                    'NRSEQIMPRLOJA' => $impressoras[0]['B_NRSEQIMPRPROD2'],
                    'IDMODEIMPRES' => $impressoras[0]['B_IDMODEIMPRES'],
                    'tipoImpressao' => 'P',
                    'CDPORTAIMPR' => $impressoras[0]['B_CDPORTAIMPR'],
                    'DSENDPORTA' => $impressoras[0]['B_DSENDPORTA'],
                    'DSIPIMPR' => $impressoras[0]['B_DSIPIMPR'],
                    'DSIPPONTE' => $impressoras[0]['B_DSIPPONTE'],
                    'NMIMPRLOJA' => $impressoras[0]['B_NMIMPRLOJA'],
                    'produtos' => array($produtoAtual)
                );
            }
        }

        if (!empty($impressoras[0]['C_NRSEQIMPRPUXA'])) {
            if (!empty($impressoraPuxa[$impressoras[0]['C_NRSEQIMPRPUXA']])) {
                array_push($impressoraPuxa[$impressoras[0]['C_NRSEQIMPRPUXA']]['produtos'], $produtoAtual);
            } else {
                $impressoraPuxa[$impressoras[0]['C_NRSEQIMPRPUXA']] = array(
                    'NRSEQIMPRLOJA' => $impressoras[0]['C_NRSEQIMPRPUXA'],
                    'IDMODEIMPRES' => $impressoras[0]['C_IDMODEIMPRES'],
                    'tipoImpressao' => 'U',
                    'CDPORTAIMPR' => $impressoras[0]['C_CDPORTAIMPR'],
                    'DSENDPORTA' => $impressoras[0]['C_DSENDPORTA'],
                    'DSIPIMPR' => $impressoras[0]['C_DSIPIMPR'],
                    'DSIPPONTE' => $impressoras[0]['C_DSIPPONTE'],
                    'NMIMPRLOJA' => $impressoras[0]['C_NMIMPRLOJA'],
                    'produtos' => array($produtoAtual)
                );
            }
        }
        return array(
            'impressoraProducao' => $impressoraProducao,
            'impressoraProducao2' => $impressoraProducao2,
            'impressoraPuxa' => $impressoraPuxa
        );
    }

    private function printHeader($text, $printerParams, $tipoVenda, $DSCOMANDA, $NRMESA,
                                 $NMSALA, $CDVENDEDOR, $NMFANVEN, $tipoImpressao, $toGoString, $comandos) {
        // Monta o cabeçalho do pedido.
        //********************************************************************************
        $text .= $this->impressaoUtil->imprimeLinha($printerParams);
        $title = $tipoImpressao == 'P' ? 'PEDIDO' : 'RELATORIO DO PUXA';
        $text .= $this->impressaoUtil->centraliza($printerParams, $title) . $printerParams['comandoEnter'];
        $data = new \DateTime('NOW');
        $text .= $this->impressaoUtil->centraliza($printerParams, 'DATA: ' . $data->format('d/m/Y H:i:s')) . $printerParams['comandoEnter'];
        $text .= $this->impressaoUtil->imprimeLinha($printerParams);

        if (!empty($NRMESA)) {
            $printerParams['bold'] = false;
            $comandos->text($text, $printerParams);
            $text = '';

            $text .= $this->impressaoUtil->centraliza($printerParams, 'MESA '. $NRMESA, $printerParams['largura']) . $printerParams['comandoEnter'];
            $text .= $this->impressaoUtil->imprimeLinha($printerParams);

            $printerParams['bold'] = true;
            $comandos->text($text, $printerParams);
            $text = '';
        }
        if ($tipoVenda == 'C') {
            $text .= 'COMANDA.: ' . $DSCOMANDA . $printerParams['comandoEnter'];
        }
        if ($tipoVenda == 'M') {
            $text .= 'AMBIENTE: ' . $NMSALA . $printerParams['comandoEnter'];
        }
        if ($tipoVenda == 'B') {
            $text .= 'ORIGEM: TERMINAL DE AUTO ATENDIMENTO' . $printerParams['comandoEnter'];
        }
        $text .= 'GARCOM..: ' . $CDVENDEDOR . ' - ' . self::removeAcentos($NMFANVEN) . $printerParams['comandoEnter'];

        if($toGoString != ''){
            $text .= $toGoString . $printerParams['comandoEnter'];
        }
        $text .= $this->impressaoUtil->imprimeLinha($printerParams);
        //********************************************************************************
        return $text;
    }

    private function printFooter($text, $printerParams, $CDSENHAPED) {
        // Monta rodapé.
        //********************************************************************************
        $text .= $this->impressaoUtil->imprimeLinha($printerParams);
        $text .= $this->impressaoUtil->centraliza($printerParams, 'SENHA ' . $CDSENHAPED) . $printerParams['comandoEnter'];
        $text .= $this->impressaoUtil->imprimeLinha($printerParams);
        //********************************************************************************
        return $text;
    }

    private function buscaNomePorPosicao($produtos, $posicoes){
        if(isset($produtos[0]['CDFILIAL'])) {
            $params = array(
                'CDFILIAL' => $produtos[0]['CDFILIAL'],
                'NRVENDAREST' => $produtos[0]['NRVENDAREST'],
                'NRLUGARMESA' => $posicoes
            );
            $type = array(
                'NRLUGARMESA' => \Doctrine\DBAL\Connection::PARAM_STR_ARRAY
            );
            $nmPosicao = $this->entityManager->getConnection()->fetchAll("BUSCA_NOME_POR_POSICAO", $params, $type);
            return array_column($nmPosicao, null, 'NRLUGARMESA');
        } else {
            return array();
        }
    }

    private function removeAcentos($texto) {
        $table = array(
            'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a',
            'é' => 'e', 'ê' => 'e', 'í' => 'i', 'ó' => 'o', 'ô' => 'o',
            'õ' => 'o', 'ú' => 'u', 'ü' => 'u', 'ç' => 'c', 'Á' => 'A',
            'À' => 'A', 'Ã' => 'A', 'Â' => 'A', 'É' => 'E', 'Ê' => 'E',
            'Í' => 'I', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ú' => 'U',
            'Ü' => 'U', 'Ç' => 'C_'
        );
        return preg_replace("[^a-zA-Z0-9_]", "", strtr($texto, $table));
    }

    private function verificaImpUmaVez($arrayProdutos){
        foreach ($arrayProdutos as $key => $product) {
            if ($product['IDIMPRODUVEZ'] === 'S' && $product['QTPRODCOMVEN'] > 1 && $product['IDPESAPROD'] === 'N') {
                $quantidade = $product['QTPRODCOMVEN'];
                $arrayProdutos[$key]['QTPRODCOMVEN'] = 1;
                $product['QTPRODCOMVEN'] = 1;
                for ($i = 0; $i < $quantidade - 1; $i++){
                    array_splice($arrayProdutos, $key, 0, array($product));
                }
            }
        }
        return $arrayProdutos;
    }

    private function alteraOrdemImpressao($produtos){
        $separados = array();
        $juntos = array();
        foreach ($produtos as $produto){
            if ($produto['IDIMPRODUVEZ'] === 'S'){
                array_push($separados, $produto);
            }
            else {
                array_push($juntos, $produto);
            }
        }

        return array_merge($juntos, $separados);
    }

}
