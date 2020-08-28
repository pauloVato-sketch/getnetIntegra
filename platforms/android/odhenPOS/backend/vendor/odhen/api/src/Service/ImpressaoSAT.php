<?php

namespace Odhen\API\Service;

use Odhen\API\Remote\Printer\Command;
use Odhen\API\Util\Exception;

class ImpressaoSAT {

    protected $entityManager;
    protected $impressaoUtil;
    protected $databaseUtil;
    protected $util;
    protected $painelSenha;

    public function __construct(
        \Doctrine\ORM\EntityManager $entityManager,
        \Odhen\API\Lib\ImpressaoUtil $impressaoUtil,
        \Odhen\API\Util\Database $databaseUtil,
        \Odhen\API\Util\Util $util,
        \Odhen\API\Service\PainelSenha $painelSenha) {

        $this->entityManager = $entityManager;
        $this->impressaoUtil = $impressaoUtil;
        $this->databaseUtil = $databaseUtil;
        $this->util = $util;
        $this->painelSenha   = $painelSenha;
    }

    const MOVIMENTACAO_ENTRADA = 'E';
    const MOVIMENTACAO_SAIDA = 'S';

    public function imprimeCupomNF($CDFILIAL, $CDCAIXA, $NRORG, $NRSEQVENDA, $NRNOTAFISCALCE, $CDSERIESAT, $DTEMISSAONFCE, $NRACESSONFCE, $NRINSCRCONS, $NMCONSVEND, $CDSENHAPED, $DSQRCODENFCE, $impostoFederal, $impostoEstadual, $FIDELITYVALUE, $produtos, $NRMESA) {
        try {
            $params = array(
                'CDFILIAL' => $CDFILIAL,
                'NRORG' => $NRORG
            );
            $dadosFilial = $this->entityManager->getConnection()->fetchAll("GET_DADOS_FILIAL", $params);
            $NMRAZSOCFILI = $dadosFilial[0]['NMRAZSOCFILI'];
            $NRINSJURFILI = $dadosFilial[0]['NRINSJURFILI'];
            $DSENDEFILI = $dadosFilial[0]['DSENDEFILI'];
            $NMBAIRFILI = $dadosFilial[0]['NMBAIRFILI'];
            $NMMUNICIPIO = $dadosFilial[0]['NMMUNICIPIO'];
            $SGESTADO = $dadosFilial[0]['SGESTADO'];
            $CDINSCESTA = $dadosFilial[0]['CDINSCESTA'];
            $NMFILIAL = $dadosFilial[0]['NMFILIAL'];
            $CDINSCMUNI = $dadosFilial[0]['CDINSCMUNI'];

            $params = array(
                'CDFILIAL' => $CDFILIAL,
                'CDCAIXA' => $CDCAIXA,
                'NRORG' => $NRORG
            );
            $dadosImpressora = $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_IMPRESSORA_SAT", $params);
            if (!empty($dadosImpressora)) {
                $IDMODEIMPRES = $dadosImpressora['IDMODEIMPRES'];
                $CDPORTAIMPR = $dadosImpressora['CDPORTAIMPR'];
                $CDLOJA = $dadosImpressora['CDLOJA'];
                $NMLOJA = $dadosImpressora['NMLOJA'];
                $NMCAIXA = $dadosImpressora['NMCAIXA'];

                $printerParams = $this->impressaoUtil->buscaParametrosImpressora($IDMODEIMPRES);
                $printerParams['largura'] = $printerParams['larguraCupom'];

                $params = array(
                    'CDFILIAL' => $CDFILIAL,
                    'CDCAIXA' => $CDCAIXA,
                    'NRORG' => $NRORG
                );
                $dadosCaixa = $this->entityManager->getConnection()->fetchAll("BUSCA_DADOS_CAIXA", $params);
                $DTABERCAIX = $this->databaseUtil->convertToDateDB($dadosCaixa[0]['DTABERCAIX']);
                $IDHABCAIXAVENDA = $dadosCaixa[0]['IDHABCAIXAVENDA'];

                $params = array(
                    'CDFILIAL' => $CDFILIAL,
                    'CDCAIXA' => $CDCAIXA,
                    'DTABERCAIX' => $DTABERCAIX,
                    'NRSEQVENDA' => $NRSEQVENDA,
                    'NRORG' => $NRORG
                );
                $types = array (
                    'DTABERCAIX' => \Doctrine\DBAL\TypeS\Type::DATETIME
                );
                $movimentacao = $this->entityManager->getConnection()->fetchAll("BUSCA_RECEBIMENTOS", $params, $types);

                $params = array(
                    'CDFILIAL' => $CDFILIAL,
                    'CDCAIXA' => $CDCAIXA,
                    'NRSEQVENDA' => $NRSEQVENDA,
                    'NRORG' => $NRORG
                );
                if (empty($produtos)){
                    $produtos = $this->entityManager->getConnection()->fetchAll("BUSCA_PRODUTOS_SAT", $params);
                }

                $troco = 0;
                $recebimentos = array();
                $vendaCreCons = false;
                foreach ($movimentacao as $movAtual) {
                    if ($movAtual['IDTIPOMOVIVE'] == self::MOVIMENTACAO_ENTRADA) {
                        array_push($recebimentos, $movAtual);
                    } else if ($movAtual['IDTIPOMOVIVE'] == self::MOVIMENTACAO_SAIDA) {
                        $troco += $movAtual['VRMOVIVEND'];
                    }
                    if ($movAtual['IDTIPORECE'] == '9') {
                        $vendaCreCons = true;
                    }
                }


                // Realiza a montagem dos dados na nota fiscal da API de painel de senhas do Madero.
                $dadosPainelSenha = $this->painelSenha->buscaDados($CDFILIAL, $CDLOJA, $CDSENHAPED, $NRSEQVENDA);
                $rodapePainelSenha = array();

                if (!$dadosPainelSenha['error']) {
                    $rodapePainelSenha = $this->montaRodapePainelSenha($printerParams, $dadosPainelSenha, $CDSENHAPED);
                }

                $texto = '';
                $texto .= self::cabecalhoCupomNF($printerParams, $NMRAZSOCFILI, $NRINSJURFILI, $DSENDEFILI, $NMBAIRFILI, $NMMUNICIPIO, $SGESTADO, $CDINSCESTA, $CDINSCMUNI);
                $texto .= $this->impressaoUtil->centraliza($printerParams, 'Extrato No. ' . $NRNOTAFISCALCE) . $printerParams['comandoEnter'];
                $texto .= $this->impressaoUtil->centraliza($printerParams, 'CUPOM FISCAL ELETRONICO - SAT') . $printerParams['comandoEnter'];
                $texto .= $this->impressaoUtil->imprimeLinha($printerParams);
                if (empty($NRINSCRCONS)) {
                    $texto .= $this->impressaoUtil->centraliza($printerParams, 'CONSUMIDOR NAO IDENTIFICADO') . $printerParams['comandoEnter'];
                } else {
                    if (!empty($NMCONSVEND)) {
                        $texto .= $this->impressaoUtil->centraliza($printerParams, $NMCONSVEND) . $printerParams['comandoEnter'];
                    }
                    $labelINSCRCONS = strlen($NRINSCRCONS) > 11 ? 'CNPJ: ' : 'CPF: ';
                    $texto .= $this->impressaoUtil->centraliza($printerParams, $labelINSCRCONS . $this->util->aplicaMascaraCpfCnpj($NRINSCRCONS)) . $printerParams['comandoEnter'];
                }
                $texto .= $this->impressaoUtil->centraliza($printerParams, '# | COD | DESC | QTD | UN | VL UNIT R$ | ST | ALIQ | VL ITEM R$') . $printerParams['comandoEnter'];
                $texto .= $this->impressaoUtil->imprimeLinha($printerParams);
                $texto .= self::itensCupomNF($produtos, $printerParams, $recebimentos, $troco);
                $texto .= $this->impressaoUtil->centraliza($printerParams, 'OBSERVACOES DO CONTRIBUINTE') . $printerParams['comandoEnter'];
                $texto .= $this->impressaoUtil->centraliza($printerParams, 'TRIBUTACAO APROXIMADA R$ ' . $this->impressaoUtil->formataNumero($impostoFederal, 2) . ' FEDERAL E R$ ' . $this->impressaoUtil->formataNumero($impostoEstadual, 2) . ' ESTADUAL') . $printerParams['comandoEnter'];
                $texto .= $this->impressaoUtil->centraliza($printerParams, '(CONFORME LEI FEDERAL 12.741/2012) - FONTE IBPT') . $printerParams['comandoEnter'];
                $texto .= $this->impressaoUtil->imprimeLinha($printerParams);
                $texto .= $this->impressaoUtil->centraliza($printerParams, 'SAT No. ' . substr($CDSERIESAT, 0, 3) . '.' . substr($CDSERIESAT, 3, 3) . '.' . substr($CDSERIESAT, 6, 3)) . $printerParams['comandoEnter'];
                $texto .= $this->impressaoUtil->centraliza($printerParams, $DTEMISSAONFCE->format('d/m/Y H:i:s')) . $printerParams['comandoEnter'];
                $texto .= $this->impressaoUtil->centraliza($printerParams, $NRACESSONFCE) . $printerParams['comandoEnter'];


                $this->impressaoUtil->checaEnter($texto, $printerParams);
                $texto .= $this->impressaoUtil->imprimeLinha($printerParams);
                $textoFooter = $this->montaProdutosCancelados('', $NRSEQVENDA, $CDFILIAL, $CDCAIXA, $printerParams);
                $textoFooter .= $this->impressaoUtil->centraliza($printerParams, 'Teknisa Software - www.teknisa.com') . $printerParams['comandoEnter'];
                $textoFooter .= 'Filial: ' . $CDFILIAL . ' ' . $NMFILIAL . $printerParams['comandoEnter'];
                $textoFooter .= 'Loja..: ' . $CDLOJA . ' ' . $NMLOJA . $printerParams['comandoEnter'];
                $textoFooter .= 'Caixa.: ' . $CDCAIXA . ' ' . $NMCAIXA . $printerParams['comandoEnter'];


                if($FIDELITYVALUE > 0) {
                    $texto .= $this->impressaoUtil->centraliza($printerParams, 'Voce recebeu um desconto de R$' . number_format($FIDELITYVALUE, 2, ',', '.') . ' pelo Fidelidade')
                        . $printerParams['comandoEnter'] . $printerParams['comandoEnter'];
                }

                $textoFooter .= $this->impressaoUtil->imprimeLinha($printerParams);
                $textoFooter .= $this->impressaoUtil->centraliza($printerParams, 'TAXA DE SERVICO FACULTATIVA/OPCIONAL') . $printerParams['comandoEnter'];
                $textoFooter .= $this->impressaoUtil->imprimeLinha($printerParams);
                $textoFooter .= $this->impressaoUtil->centraliza($printerParams, 'Consulte o QRCODE deste extrato atraves do app DeOlhoNaNota') . $printerParams['comandoEnter'];
                $textoSenhaMesa = '';

                if(strlen($CDSENHAPED) > 0){
                    $textoSenhaMesa .= 'SENHA: ' . $CDSENHAPED.' ';
                }

                if(strlen($NRMESA) > 0){
                    $textoSenhaMesa .= 'MESA: ' . $NRMESA;
                }

                if ($vendaCreCons) {
                    $params = array(
                        'CDFILIAL' => $CDFILIAL,
                        'CDCAIXA' => $CDCAIXA,
                        'NRSEQVENDA' => $NRSEQVENDA
                    );
                    $infoConsumer = $this->entityManager->getConnection()->fetchAssoc("GET_CONSUMER_SALE", $params);

                    $textoSenhaMesa .= $printerParams['comandoEnter'];
                    $linhaCons = 'Cliente:    ' . $infoConsumer['CDCLIENTE'] . ' - ' . $infoConsumer['NMFANTCLIE'] . $printerParams['comandoEnter'];
                    $textoSenhaMesa .= $this->impressaoUtil->quebraLinha($linhaCons, $printerParams, true);
                    $linhaCons = 'Consumidor: ' . $infoConsumer['CDCONSUMIDOR'] . $printerParams['comandoEnter'];
                    $textoSenhaMesa .= $this->impressaoUtil->quebraLinha($linhaCons, $printerParams, true);
                    $linhaCons = 'Nome:       ' . $infoConsumer['NMCONSUMIDOR'] . $printerParams['comandoEnter'];
                    $textoSenhaMesa .= $this->impressaoUtil->quebraLinha($linhaCons, $printerParams, true);
                    $valor = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_CONSUMER_BALANCE_API", array($infoConsumer['CDCLIENTE'], $infoConsumer['CDCONSUMIDOR']));
                    $valor = !empty($valor) ? $valor['SALDO'] : 0;
                    $valor = $this->impressaoUtil->formataNumero(floatval($valor), 2);
                    $textoSenhaMesa .= 'Saldo Total: R$' . $valor . $printerParams['comandoEnter'];
                }

                $textoSenhaMesa = !empty($textoSenhaMesa)? $printerParams['comandoEnter'] . $printerParams['comandoEnter']. $printerParams['comandoEnter'] . $textoSenhaMesa : $textoSenhaMesa;

                if($IDMODEIMPRES!='27'){
                    $textoSenhaMesa .= $printerParams['comandoEnter'];
                }

                $result = array(
                    'error' => false,
                    'dadosImpressao' => array(),
                    'errPainelSenha' => !empty($rodapePainelSenha['errmsg']) ? $rodapePainelSenha['errmsg'] : ''
                );

                if ($printerParams['impressaoFront']) {
                    $result['dadosImpressao'] = array(
                        'TEXTOPAINELSENHA' => !empty($rodapePainelSenha) ? $rodapePainelSenha : array(),
                        'TEXTOCUPOM1VIA' => $texto,
                        'TEXTOCODIGOBARRAS' => substr($NRACESSONFCE, 3),
                        'TEXTOQRCODE' => $DSQRCODENFCE,
                        'TEXTORODAPE' => $textoFooter . $textoSenhaMesa
                    );
                } else {
                    $comandos = new Command();

                    $comandos->text($texto);
                    $barCodeOptions = array(
                        'height'   => 40,
                        'width'    => 0,
                        'position' => 0,
                        'font'     => 0,
                        'margin'   => 25
                    );
                    $comandos->barCode(substr($NRACESSONFCE, 3, 22), $barCodeOptions);
                    $comandos->barCode(substr($NRACESSONFCE, 25, 22), $barCodeOptions);
                    $comandos->qrCode($DSQRCODENFCE, array(
                        'height' => 60,
                        'width' => 2,
                        'position' => 2,
                        'font' => 0,
                        'margin' => 174
                    ));
                    $comandos->text($textoFooter);
                    if ($textoSenhaMesa != ''){
                        $comandos->text($textoSenhaMesa, array(
                            'italic'     => 0,
                            'underlined' => 0,
                            'expanded'   => 1,
                            'bold'       => 1,
                            'letterType' => 3
                        ));
                    }

                    if (!empty($rodapePainelSenha)) {
                        $comandos->text($rodapePainelSenha['inicio']);
                        $comandos->qrCode($rodapePainelSenha['qrCode'], array(
                            'height' => 30,
                            'width' => 1,
                            'position' => 2,
                            'font' => 0,
                            'margin' => 174
                        ));
                        $comandos->text($rodapePainelSenha['final']);
                    }

                    if($dadosImpressora['IDMODEIMPRES'] != '23' && $IDHABCAIXAVENDA != 'TAA' && $IDHABCAIXAVENDA != 'APC') {
                        $comandos->cutPaper();
                    }
                    $respostaPonte = $this->impressaoUtil->requisicaoPonte($dadosImpressora, $comandos);
                    if ($respostaPonte['error']) {
                        $result['error'] = true;
                        $result['message'] = $respostaPonte['message'];
                    }
                }
            } else {
                $result = array(
                    'error' => true,
                    'message' => 'Impressora SAT não parametrizada para o caixa.'
                );
            }
        } catch (\Exception $e) {
            Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
            $result = array(
                'error' => true,
                'message' => $e->getMessage()
            );
        }

        return $result;
    }

    private function montaProdutosCancelados($texto, $NRSEQVENDA, $CDFILIAL, $CDCAIXA, $printerParams){
        $canceledProducts = $this->getCanceledProducts($NRSEQVENDA, $CDFILIAL, $CDCAIXA);
        $texto .= $this->impressaoUtil->imprimeLinha($printerParams);
        if (!empty($canceledProducts)){
            $this->impressaoUtil->checaEnter($texto, $printerParams);
            $texto .= 'Itens Cancelados: '.$printerParams['comandoEnter'];
            foreach($canceledProducts as $canceled){
                 $texto .= $canceled['NRSEQITVENDC'] . ' ' . $canceled['CDPRODUTO'] . ' ' . $canceled['NMPRODUTO'] . ' - ' . $canceled['QTPRODVENDC'] . ' ' . $canceled['SGUNIDADE'] . $printerParams['comandoEnter'];
            }
            $texto .= $this->impressaoUtil->imprimeLinha($printerParams);
            $texto .= $printerParams['comandoEnter'];
        }
        return $texto;
    }

    private function getCanceledProducts($NRSEQVENDA, $CDFILIAL, $CDCAIXA){
        $canceledProductsParams = array(
            ':CDFILIAL'  => $CDFILIAL,
            ':CDCAIXA'   => $CDCAIXA,
            ':NRSEQVENDA'=> $NRSEQVENDA
        );
        return $this->entityManager->getConnection()->fetchAll("GET_CANCELED_PRODUCTS", $canceledProductsParams);
    }

    private function verificaSeItemTaxaServico($item) {
        return $item['CDPRODUTO'] === $item['CDPRODTAXASERV'];
    }

    private function itensCupomNF($produtos, $printerParams, $recebimentos, $troco) {
        $corpo = '';
        $qtdItens = count($produtos);
        $totalVendaSemDesconto = 0;
        $descontoVenda = 0;

        foreach ($produtos as $index => $item) {
            $itemTaxaServico = $this->verificaSeItemTaxaServico($item);
            $totalVendaSemDesconto += ($item['VRUNITVEND'] * $item['QTPRODVEND']) + $item['VRACRITVEND'];
            $descontoVenda += $item['VRDESITVEND'];

            $nrItem = str_pad($index + 1, 3, '0', STR_PAD_LEFT);
            $newItem = $nrItem . " " . $item['CDARVPROD'] . " " . $item['NMPRODUTO'];
            $qtd = $this->impressaoUtil->formataNumero($item['QTPRODVEND'], 3);
            $finalItem = $qtd . ' ' . $item['SGUNIDADE'] . " X ";
            if ($itemTaxaServico) {
                $valorItemCalculado = $this->impressaoUtil->formataNumero(floatval(bcmul(str_replace(',','.',strval($item['QTPRODVEND'])), str_replace(',','.',strval($item['VRUNITVEND'] + $item['VRUNITVENDCL'])), '2')) + $item['VRACRITVEND'], 2);
                $finalItem .= $valorItemCalculado;
            } else {
                $valorItemCalculado = $this->impressaoUtil->formataNumero(floatval(bcmul(str_replace(',','.',strval($item['QTPRODVEND'])), str_replace(',','.',strval($item['VRUNITVEND'] + $item['VRUNITVENDCL'])), '2')), 2);
                $finalItem .= $this->impressaoUtil->formataNumero($item['VRUNITVEND'], 2);
            }
            $valorImposto = $this->impressaoUtil->formataNumero($item['VRPEALIMPFIS'], 2);
            $impostoItem = $item['IDTPIMPOSFIS'] . ' ' . $valorImposto . '%' . ' ';
            $finalItem .= $impostoItem . ' ' . $valorItemCalculado;
            $textoAlinhado = $this->impressaoUtil->alinhaInicioFim($printerParams, $newItem, $finalItem);
            $corpo .=  $textoAlinhado . $printerParams['comandoEnter'];
            $total = $item['QTPRODVEND'] * round($item['VRUNITVEND'], 2);
            // if ($item['VRDESITVEND'] > 0) {
            //     $corpo .= $this->impressaoUtil->preencheLinha($printerParams, 'Desconto no item ' . $nrItem . ' - '. $this->impressaoUtil->formataNumero($item['VRDESITVEND']), ' ');
            // }
            // if (!$itemTaxaServico && $item['VRACRITVEND'] > 0) {
            //     $corpo .= $this->impressaoUtil->preencheLinha($printerParams, 'Acrescimo no item ' . $nrItem . ' - '. $this->impressaoUtil->formataNumero($item['VRACRITVEND']), ' ');
            // }
        }
        $corpo .= $this->impressaoUtil->preencheLinha($printerParams, 'Vr. Total Pago: = ' . $this->impressaoUtil->formataNumero($totalVendaSemDesconto - $descontoVenda, 2), ' ');
        if($troco > 0){
            $corpo .= $this->impressaoUtil->preencheLinha($printerParams, 'Valor de Troco: = ' . $this->impressaoUtil->formataNumero($troco, 2), ' ');
        }
        $inicio = 'SUBTOTAL R$ ';
        $final = $this->impressaoUtil->formataNumero($totalVendaSemDesconto, 2);
        $textoAlinhado = $this->impressaoUtil->alinhaInicioFim($printerParams, $inicio, $final);
        $corpo .= $textoAlinhado . $printerParams['comandoEnter'];
        if ($descontoVenda > 0) {
            $inicio = 'DESCONTOS R$ ';
            $final = $this->impressaoUtil->formataNumero($descontoVenda, 2);
            $textoAlinhado = $this->impressaoUtil->alinhaInicioFim($printerParams, $inicio, $final);
            $corpo .= $textoAlinhado . $printerParams['comandoEnter'];
        }
        $inicio = 'TOTAL R$ ';
        $final = $this->impressaoUtil->formataNumero($totalVendaSemDesconto - $descontoVenda, 2);
        $textoAlinhado = $this->impressaoUtil->alinhaInicioFim($printerParams, $inicio, $final);
        $corpo .= $textoAlinhado . $printerParams['comandoEnter'];
        foreach ($recebimentos as $recebimentoAtual) {
            $nomeTiporece = $recebimentoAtual['NMTIPORECE'];
            $valorRecebimento = $this->impressaoUtil->formataNumero($recebimentoAtual['VRMOVIVEND'], 2);
            $textoAlinhado = $this->impressaoUtil->alinhaInicioFim($printerParams, $nomeTiporece, $valorRecebimento);
            $corpo .= $textoAlinhado . $printerParams['comandoEnter'];
        }
        $corpo .= $this->impressaoUtil->imprimeLinha($printerParams);
        return $corpo;
    }

    public function montaRodapePainelSenha($printerParams, $dadosPainelSenha, $CDSENHAPED) {
        $rodPainelSenhas['inicio'] = $this->impressaoUtil->imprimeLinha($printerParams);
        $rodPainelSenhas['inicio'] .= $this->impressaoUtil->centraliza($printerParams, 'ACOMPANHE SEU PEDIDO PELO PAINEL DE SENHAS!') . $printerParams['comandoEnter'];

        $rodPainelSenhas['qrCode'] = $this->impressaoUtil->centraliza($printerParams, $dadosPainelSenha['URL_QRCODE']) . $printerParams['comandoEnter'];

        $rodPainelSenhas['final'] = $this->impressaoUtil->centraliza($printerParams, 'Leia o QR Code ou acesse pelo endereço abaixo para') . $printerParams['comandoEnter'];
        $rodPainelSenhas['final'] .= $this->impressaoUtil->centraliza($printerParams, 'acompanhar seu pedido no painel de senhas pelo celular.') . $printerParams['comandoEnter'];

        $rodPainelSenhas['final'] .= $this->impressaoUtil->centraliza($printerParams, 'Endereço: ') . $printerParams['comandoEnter'];
        $rodPainelSenhas['final'] .= $this->impressaoUtil->centraliza($printerParams, $dadosPainelSenha['URL']) . $printerParams['comandoEnter'];
        $rodPainelSenhas['final'] .= $this->impressaoUtil->centraliza($printerParams, 'Código de acesso: ' . $dadosPainelSenha['ACCESS_CODE']) . $printerParams['comandoEnter'];

        return $rodPainelSenhas;
    }

    public function cabecalhoCupomNF($printerParams, $NMRAZSOCFILI, $NRINSJURFILI, $DSENDEFILI, $NMBAIRFILI, $NMMUNICIPIO, $SGESTADO, $CDINSCESTA, $CDINSCMUNI) {
        $cabecalho = '';
        $cabecalho .= $this->impressaoUtil->centraliza($printerParams, $NMRAZSOCFILI) . $printerParams['comandoEnter'];
        $cabecalho .= $this->impressaoUtil->centraliza($printerParams, $DSENDEFILI) . $printerParams['comandoEnter'];
        $cabecalho .= $this->impressaoUtil->centraliza($printerParams, $NMBAIRFILI . ' - ' . $NMMUNICIPIO . ' - ' . $SGESTADO) . $printerParams['comandoEnter'];
        $cabecalho .= $this->impressaoUtil->centraliza($printerParams, 'CNPJ: ' . $this->util->aplicaMascaraCpfCnpj($NRINSJURFILI) . ' IE: ' . $CDINSCESTA . ' IM: '. $CDINSCMUNI) . $printerParams['comandoEnter'];
        $cabecalho .= $this->impressaoUtil->imprimeLinha($printerParams);
        return $cabecalho;
    }

    public function imprimeCupomCanceladoSAT($CDFILIAL, $CDCAIXA, $NRORG, $respostaCriacaoArquivo, $cancelaCupom, $venda){
        $result = array(
            'error' => true,
            'message' => 'Ocorreu um problema na impressão da nota fiscal de cancelamento. ',
            'dadosImpressao' => array()
        );

        try {
            $params = array(
                'CDFILIAL' => $CDFILIAL,
                'NRORG' => $NRORG
            );
            $dadosFilial = $this->entityManager->getConnection()->fetchAssoc("GET_DADOS_FILIAL", $params);
            $NMRAZSOCFILI = $dadosFilial['NMRAZSOCFILI'];
            $NRINSJURFILI = $dadosFilial['NRINSJURFILI'];
            $DSENDEFILI = $dadosFilial['DSENDEFILI'];
            $NMBAIRFILI = $dadosFilial['NMBAIRFILI'];
            $NMMUNICIPIO = $dadosFilial['NMMUNICIPIO'];
            $SGESTADO = $dadosFilial['SGESTADO'];
            $CDINSCESTA = $dadosFilial['CDINSCESTA'];
            $CDINSCMUNI = $dadosFilial['CDINSCMUNI'];

            $valor = $cancelaCupom['valorTotalCFe'];
            $NRACESSONFCE = $cancelaCupom['NRACESSONFCE'];
            $dtEmissao = $cancelaCupom['timeStamp'];
            $NRINSCRCONS = $venda['NRINSCRCONS'];
            $CDSERIESAT = $respostaCriacaoArquivo['CDSERIESAT'];

            $params['CDCAIXA'] = $CDCAIXA;
            $dadosImpressora = $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_IMPRESSORA_SAT", $params);
            if (!empty($dadosImpressora)) {
                $printerParams = $this->impressaoUtil->buscaParametrosImpressora($dadosImpressora['IDMODEIMPRES']);
                $printerParams['largura'] = $printerParams['larguraCupom'];
                // CABEÇALHO
                $cabecalho = '';
                $cabecalho .= self::cabecalhoCupomNF($printerParams, $NMRAZSOCFILI, $NRINSJURFILI, $DSENDEFILI, $NMBAIRFILI, $NMMUNICIPIO, $SGESTADO, $CDINSCESTA, $CDINSCMUNI);

                $cabecalho .= $this->impressaoUtil->centraliza($printerParams, 'Extrato No. ' . $respostaCriacaoArquivo['NRNOTAFISCALCE']) . $printerParams['comandoEnter'];
                $cabecalho .= $this->impressaoUtil->centraliza($printerParams, 'CUPOM FISCAL ELETRONICO - SAT') . $printerParams['comandoEnter'];
                $cabecalho .= $this->impressaoUtil->centraliza($printerParams, 'CANCELAMENTO') . $printerParams['comandoEnter'];
                $cabecalho .= $this->impressaoUtil->imprimeLinha($printerParams);

                $cabecalho .= $this->impressaoUtil->centraliza($printerParams, 'DADOS DO CUPOM FISCAL ELETRONICO CANCELADO') . $printerParams['comandoEnter'];
                if ($NRINSCRCONS) {
                    $dadosConsumidor = 'CPF/CNPJ do Consumidor: ' . $this->util->aplicaMascaraCpfCnpj($NRINSCRCONS);
                    $lengthDadosConsumidor = strlen($dadosConsumidor);
                    if ($lengthDadosConsumidor > $printerParams['largura']){
                        $cabecalho .= $this->impressaoUtil->centraliza($printerParams, $dadosConsumidor) . $printerParams['comandoEnter'];
                    } else if ($lengthDadosConsumidor === $printerParams['largura']) {
                        $cabecalho .= $dadosConsumidor;
                    } else {
                        $cabecalho .= $this->impressaoUtil->preencheLinha($printerParams, $dadosConsumidor, ' ');
                    }
                    $cabecalho .= $this->impressaoUtil->preencheLinha($printerParams, 'TOTAL: ' . $this->impressaoUtil->formataNumero(floatval($valor), 2), ' ') . $printerParams['comandoEnter'];
                }
                // QRCODE
                $qrCode = $NRACESSONFCE . '|' . $dtEmissao . '|' . $valor . '|' . $NRINSCRCONS . '|' . $cancelaCupom['DSQRCODENFCE'];
                // RODAPÉ
                $rodape = '';
                $rodape .= $this->impressaoUtil->centraliza($printerParams, 'SAT No. ' . substr($CDSERIESAT, 0, 3) . '.' . substr($CDSERIESAT, 3, 3) . '.' . substr($CDSERIESAT, 6, 3)) . $printerParams['comandoEnter'];
                $dtEmissao = substr($dtEmissao, 6, 2) . '/' . substr($dtEmissao, 4, 2) . '/' . substr($dtEmissao, 0, 4) . ' - ' .
                    substr($dtEmissao, 8, 2) . ':' . substr($dtEmissao, 10, 2) . ':' . substr($dtEmissao, 12, 2);
                $rodape .= $this->impressaoUtil->centraliza($printerParams, $dtEmissao) . $printerParams['comandoEnter'];
                $rodape .= $this->impressaoUtil->centraliza($printerParams, $NRACESSONFCE) . $printerParams['comandoEnter'];
                $rodape .= $this->impressaoUtil->imprimeLinha($printerParams);
                $rodape .= $this->impressaoUtil->preencheLinha($printerParams, 'Extrato Cancelado: ' . $venda['NRNOTAFISCALCE'], ' ');
                if (intval($printerParams['largura']) > 32){
                    $rodape .= $this->impressaoUtil->preencheLinha($printerParams, 'Chave: ' . $venda['NRACESSONFCE'], ' ');
                } else {
                    $rodape .= $this->impressaoUtil->centraliza($printerParams, 'Chave: ' . $venda['NRACESSONFCE']);
                }

                if ($printerParams['impressaoFront']){
                    $result['error'] = false;
                    $result['dadosImpressao'] = array(
                        'TEXTOCUPOM' => $cabecalho,
                        'TEXTOCODIGOBARRAS' => $NRACESSONFCE,
                        'TEXTOQRCODE' => $qrCode,
                        'TEXTORODAPE' => $rodape
                    );
                } else {
                    $comandos = new Command();
                    $comandos->text($cabecalho);
                    $barCodeOptions = array(
                        'height'   => 40,
                        'width'    => 0,
                        'position' => 0,
                        'font'     => 0,
                        'margin'   => 25
                    );
                    $comandos->barCode(substr($NRACESSONFCE, 3, 22), $barCodeOptions);
                    $comandos->barCode(substr($NRACESSONFCE, 25, 22), $barCodeOptions);
                    $comandos->qrCode($qrCode, array(
                        'height' => 60,
                        'width' => 2,
                        'position' => 2,
                        'font' => 0,
                        'margin' => 174
                    ));
                    $comandos->text($rodape);
                    if($dadosImpressora['IDMODEIMPRES'] != '23') {
                        $comandos->cutPaper();
                    }
                    $respostaPonte = $this->impressaoUtil->requisicaoPonte($dadosImpressora, $comandos);
                    if (!$respostaPonte['error']) {
                        $result['error'] = false;
                    } else {
                        $result['message'] .= $respostaPonte['message'];
                    }
                }
            } else {
                $result['message'] = 'Impressora SAT não parametrizada para o caixa.';
            }
        } catch (\Exception $e) {
            Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
            $result['message'] .= $e->getMessage();
        }

        return $result;
    }

}