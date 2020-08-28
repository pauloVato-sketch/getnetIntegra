<?php

namespace Odhen\API\Service;

use Odhen\API\Remote\Printer\Command;

class ImpressaoDelivery {

    protected $entityManager;
    protected $impressaoUtil;

    public function __construct(\Doctrine\ORM\EntityManager $entityManager, \Odhen\API\Lib\ImpressaoUtil $impressaoUtil){
        $this->entityManager = $entityManager;
        $this->impressaoUtil = $impressaoUtil;
        $this->comandos = array();
    }

    public function imprimeDelivery($ordersDlv){
        foreach ($ordersDlv as $order) {
            $params = array(
                'CDFILIAL'       => $order['CDFILIAL'],
                'CDLOJA'         => $order['CDLOJA'],
                'NRVENDAREST'    => $order['NRVENDAREST']
            );
            $dadosEntrega = $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_ENTREGA", $params);

            $paramTiporece = array(
                'NRVENDAREST'   => $order['NRVENDAREST'],
                'CDFILIAL'      => $dadosEntrega['CDFILIAL']
            );

            $dadosEntrega['TIPORECEBE'] = $this->entityManager->getConnection()->fetchAll("BUSCA_TIPORECE_DLV", $paramTiporece);

            $paramsItemPedido = array(
                'CDSENHAPED'    => $dadosEntrega['CDSENHAPED'],
                'NRVENDAREST'   => $order['NRVENDAREST']
            );

            $dadosItemPedido = $this->entityManager->getConnection()->fetchAll("BUSCA_ITPEDIDO_ENTREGA", $paramsItemPedido);
            $CDLOJA = $dadosEntrega['CDLOJA'];
            $CDFILIAL = $dadosEntrega['CDFILIAL'];
            $CDCAIXA = $order['CDCAIXA'];
            $impressora = self::buscaImpressoraDlv($CDFILIAL, $CDLOJA, $CDCAIXA);
            self::montaEntrega($impressora, $dadosEntrega, $dadosItemPedido);

            foreach ($this->comandos as $comando) {
                $resultImp = $this->impressaoUtil->impressaoPedidos($comando['impressora'], $comando['comandos']);
                if($resultImp['error']){
                    if(isset($result)){
                        $result['message'] = $result['message'].'   '.$resultImp['message'];
                    }else{
                        $result = $resultImp;
                    }
                }
            }

            $this->comandos = array();
        }

        if(!isset($result['error']) || !$result['error']){
            $result = array(
                'error' => false,
                'message' => ' '
            );
        }else{
            $result['error'] = true;
            $result['message'] = 'Houve erro ao imprimir o relatório de entrega '. $result['message'];
        }

        return $result;
    }

    private function buscaImpressoraDlv($CDFILIAL, $CDLOJA, $CDCAIXA){
        $params = array(
            'CDFILIAL'  => $CDFILIAL,
            'CDLOJA'    => $CDLOJA,
            'CDCAIXA'   => $CDCAIXA
        );
        return $this->entityManager->getConnection()->fetchAssoc("BUSCA_IMPRESSORA_DELIVERY", $params);
    }

    private function montaEntrega($impressora, $dadosEntrega, $dadosItemPedido) {
        if(!isset($this->comandos[$impressora['NRSEQIMPRLOJA']])){
            $this->comandos[$impressora['NRSEQIMPRLOJA']]['comandos'] = new Command();
            $this->comandos[$impressora['NRSEQIMPRLOJA']]['impressora'] = $impressora;
        }
        $this->montaImpressaoDelivery($impressora, $dadosEntrega, $dadosItemPedido);
    }

    private function montaImpressaoDelivery($impressora, $dadosEntrega, $dadosItemPedido) {
        $comandos = $this->comandos[$impressora['NRSEQIMPRLOJA']]['comandos'];

        $printerParams = $this->impressaoUtil->buscaParametrosImpressora($impressora['IDMODEIMPRES']);
        $printerParams['letterType'] = $printerParams['tipoLetra'];

        $textHeader = '';
        $textHeader = $this->printHeader($textHeader, $printerParams, $dadosEntrega, $comandos);

        $printerParams['bold'] = false;
        $comandos->text($textHeader, $printerParams);

        $textBody = '';
        $textBody = $this->printBody($textBody, $printerParams, $dadosEntrega, $comandos);

        $printerParams['bold'] = true;
        $comandos->text($textBody, $printerParams);

        $textFooter = '';
        $textFooter = $this->printFooter($textFooter, $printerParams, $dadosEntrega, $dadosItemPedido);

        $printerParams['bold'] = false;
        $comandos->text($textFooter, $printerParams);
         $barCodeOptions = array(
                    'height'   => 40,
                    'width'    => 0,
                    'position' => 0,
                    'font'     => 0,
                    'margin'   => 25
                );


        $pedido = $this->impressaoUtil->centraliza($printerParams, 'Pedido: '.$dadosEntrega['NRCOMANDA'].$printerParams['comandoEnter']);
        $comandos->text($pedido, $printerParams);
        $comandos->barCode($dadosEntrega['NRCOMANDA'], $barCodeOptions);
        $text = $printerParams['comandoEnter'];
        $text .= $printerParams['comandoEnter'];
        $text .= $printerParams['comandoEnter'];
        $text .= $printerParams['comandoEnter'];
        $text .= $printerParams['comandoEnter'];
        $comandos->text($text, $printerParams);

        $comandos->cutPaper();
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

    private function printHeader($text, $printerParams, $dadosEntrega, $comandos) {
        // Monta o cabeçalho do pedido.
        //********************************************************************************

        $title = 'Relatorio De Entrega';
        $text .= $this->impressaoUtil->centraliza($printerParams, $title) . $printerParams['comandoEnter'];
        $text .= $this->impressaoUtil->imprimeLinha($printerParams);
        $text .= 'Operador: ' . $dadosEntrega['CDOPERADOR'] . $printerParams['comandoEnter'];
        $text .= 'Entregador: ' . $dadosEntrega['NMVENDEDOR'] . $printerParams['comandoEnter'];
        $text .= 'Emissao: ' . date_format(new \DateTime('NOW'), 'd/m/Y H:i:s') . $printerParams['comandoEnter'];
        $text .= 'Pedido: ' . $dadosEntrega['NRCOMANDA'] . $printerParams['comandoEnter'];
        if(!empty($dadosEntrega['DTENTREGA'])){
            $dadosEntrega['DTENTREGA'] = date_format(new \DateTime($dadosEntrega['DTENTREGA']), 'd/m/Y H:i:s');
        }
        $text .= 'Agendamento: ' . $dadosEntrega['DTENTREGA'] . $printerParams['comandoEnter'];

        if($dadosEntrega['IDORGCMDVENDA'] == 'DLV_IFO' || $dadosEntrega['IDORGCMDVENDA'] == 'DLV_SPO'){
            $text .= 'Ifood:   ' . $dadosEntrega['NRCOMANDAEXT'] . $printerParams['comandoEnter'];
        }else if($dadosEntrega['IDORGCMDVENDA'] == 'DLV_UBR'){
            $text .= 'Uber Eats:   ' . $dadosEntrega['NRCOMANDAEXT'] . $printerParams['comandoEnter'];
        }

        $text .= 'Consumidor: ' . $dadosEntrega['NMCONSUMIDOR'] . $printerParams['comandoEnter'];

        if(!$dadosEntrega['NRTELECONS']){
        $dadosEntrega['NRTELECONS'] = $dadosEntrega['NRTELE2CONS']? $dadosEntrega['NRTELE2CONS'] : $dadosEntrega['NRCELULARCONS'];
        }

        $text .= 'Tel. Consumidor: ' . $dadosEntrega['NRTELECONS'] . $printerParams['comandoEnter'];
        $text .= $this->impressaoUtil->imprimeLinha($printerParams);
        //********************************************************************************
        return $text;
    }

    private function printBody($text, $printerParams, $dadosEntrega, $comandos) {
        // Monta o corpo da mensagem.
        //********************************************************************************
        $title = 'ENDERECO';
        $text .= $this->impressaoUtil->centraliza($printerParams, $title) . $printerParams['comandoEnter'];
        $text .= $this->impressaoUtil->imprimeLinha($printerParams);

        $text .= 'QUADRANTE: '.$printerParams['comandoEnter'];
        if($dadosEntrega['DSAREAATEND']){
            $text .= 'Area '.$dadosEntrega['DSAREAATEND'].$printerParams['comandoEnter'];
        }
        $text .= $dadosEntrega['DSENDECONSCOMAN'];
        $text .= $dadosEntrega['NRENDECONSCOMAN']? ' - N '.$dadosEntrega['NRENDECONSCOMAN'].$printerParams['comandoEnter'] : $printerParams['comandoEnter'];

        if($dadosEntrega['DSCOMPLENDCOCOM']){
            $text .= $dadosEntrega['DSCOMPLENDCOCOM'].$printerParams['comandoEnter'];
        }

        $text .= $dadosEntrega['DSBAIRRO'].' - CEP:  '.$dadosEntrega['NRCEPCONSCOMAND'].$printerParams['comandoEnter'];
        $text .= $dadosEntrega['NMMUNICIPIO'].' - '.$dadosEntrega['SGESTADO'].$printerParams['comandoEnter'].$printerParams['comandoEnter'];
        $text .= $this->impressaoUtil->imprimeLinha($printerParams);
        $text .= 'REFERENCIA: '.$dadosEntrega['DSREFENDCONSCOM'].$printerParams['comandoEnter'];
        $text .= $this->impressaoUtil->imprimeLinha($printerParams);
        //********************************************************************************
        return $text;
    }


    private function printFooter($text, $printerParams, $dadosEntrega, $dadosItemPedido) {
        // Monta rodapé.
        //********************************************************************************
        if($dadosEntrega['DSOBSCOMANDA']){
            $text .= "OBS.: ".$dadosEntrega['DSOBSCOMANDA'].$printerParams['comandoEnter'];
            $text .= $this->impressaoUtil->imprimeLinha($printerParams);
        }

        if($dadosEntrega['IDORGCMDVENDA']=='DLV_SPO'){
            $text.= "SpoonRocket: Entregador Automatico, nao chamar.".$printerParams['comandoEnter'];
            $text .= $this->impressaoUtil->imprimeLinha($printerParams);
        }

        $text .= 'Formas de Pagamento: '.$printerParams['comandoEnter'];
        $TOTAL = $dadosEntrega['VRACRCOMANDA'];
        $TOTAL = $TOTAL - $dadosEntrega['VRDESCOMANDA'];
        foreach ($dadosEntrega['TIPORECEBE'] as $TIPORECEBE) {
            $text .= $this->impressaoUtil->alinhaInicioFim($printerParams, $TIPORECEBE['NMTIPORECE'], $this->impressaoUtil->formataNumero($TIPORECEBE['VRMOVIVENDDLV']));
            $text .= $printerParams['comandoEnter'];
        }
        $text .= $this->impressaoUtil->imprimeLinha($printerParams);

        $text .= 'Produtos Vendidos: '.$printerParams['comandoEnter'];
        foreach ($dadosItemPedido as $itemPedido) {
            $text .= '[ ] '.$itemPedido['CDPRODUTO'].' - '.$itemPedido['NMPRODUTO'].$printerParams['comandoEnter'];
            $stringItem = '     '.$itemPedido['QTPRODCOMVEN'].' X '.$this->impressaoUtil->formataNumero($itemPedido['VRPRECCOMVEN']);
            $valorProdCalc = ($itemPedido['QTPRODCOMVEN'] * $itemPedido['VRPRECCOMVEN']) - $itemPedido['VRDESCCOMVEN'];
            $valorProduto = $this->impressaoUtil->formataNumero($valorProdCalc);
            $text .= $this->impressaoUtil->alinhaInicioFim($printerParams, $stringItem, $valorProduto).$printerParams['comandoEnter'];
            $OBSITEMVENDA = self::verificaObsItem($itemPedido['DSOBSDESCIT'], $itemPedido['DSOBSPEDDIGCMD']);
            foreach ($OBSITEMVENDA as $obsItem) {
                $text .= ' * '.$obsItem.$printerParams['comandoEnter'];
            }
            $TOTAL += $valorProdCalc;
        }

        $entregaDesconto = '';
        if(!empty($dadosEntrega['VRDESCOMANDA']) && $dadosEntrega['VRDESCOMANDA'] != '.000'){
                $entregaDesconto = $this->impressaoUtil->alinhaInicioFim($printerParams, 'Desconto:',$this->impressaoUtil->formataNumero($dadosEntrega['VRDESCOMANDA'])).$printerParams['comandoEnter'] ;
        }
        $text .= !empty($entregaDesconto)? $entregaDesconto : $printerParams['comandoEnter'];

        $entregaAcrescimo = '';
        if(!empty($dadosEntrega['VRACRCOMANDA']) && $dadosEntrega['VRACRCOMANDA'] != '.000'){
                $entregaAcrescimo = $this->impressaoUtil->alinhaInicioFim($printerParams, 'Acrescimo/Tx.Entrega:',$this->impressaoUtil->formataNumero($dadosEntrega['VRACRCOMANDA'])).$printerParams['comandoEnter'] ;
        }
        $text .= !empty($entregaAcrescimo)? $entregaAcrescimo : $printerParams['comandoEnter'];

        $text .= $this->impressaoUtil->imprimeLinha($printerParams);
        $text .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'TOTAL', $this->impressaoUtil->formataNumero($TOTAL)).$printerParams['comandoEnter'];

        $text .= $printerParams['comandoEnter'];
        $text .= $printerParams['comandoEnter'];
        $text .= $printerParams['comandoEnter'];
        $text .= $printerParams['comandoEnter'];
        $text .= $printerParams['comandoEnter'];

        //********************************************************************************
        return $text;
    }

    private function verificaObsItem($obsItemVenda, $obsItemVendaDig){
        $obsItemVenda = explode(';', $obsItemVenda);
        $obsItemVendaDig = explode(';', $obsItemVendaDig);
        $obsItem = array_merge($obsItemVenda, $obsItemVendaDig);
        $obsItem = array_unique($obsItem);
        $obsItem = array_filter($obsItem, function($item){
            return(trim($item) != '');
        });
        return $obsItem;
    }

}