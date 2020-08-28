<?php

namespace Odhen\API\Service;

class ImpressaoNF {

    protected $entityManager;
    protected $impressaoUtil;
    protected $databaseUtil;

    public function __construct(
        \Doctrine\ORM\EntityManager $entityManager,
        \Odhen\API\Lib\ImpressaoUtil $impressaoUtil,
        \Odhen\API\Util\Database $databaseUtil) {

        $this->entityManager = $entityManager;
        $this->impressaoUtil = $impressaoUtil;
        $this->databaseUtil = $databaseUtil;
    }

    const MOVIMENTACAO_ENTRADA = 'E';
    const MOVIMENTACAO_SAIDA = 'S';

    public function imprimeCupomNF($CDFILIAL, $CDCAIXA, $NRORG, $NRSEQVENDA, $NRNOTAFISCALCE, $CDSERIESAT, $DTEMISSAONFCE, $NRACESSONFCE, $NRINSCRCONS, $NMCONSVEND, $CDSENHAPED, $DSQRCODENFCE, $impostoFederal, $impostoEstadual) {
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
        $dadosImpressora = $this->entityManager->getConnection()->fetchAll("BUSCA_DADOS_IMPRESSORA_NF", $params);
        if (!empty($dadosImpressora)) {
            $IDMODEIMPRES = $dadosImpressora[0]['IDMODEIMPRES'];
            $CDPORTAIMPR = $dadosImpressora[0]['CDPORTAIMPR'];
            $CDLOJA = $dadosImpressora[0]['CDLOJA'];
            $NMLOJA = $dadosImpressora[0]['NMLOJA'];
            $NMCAIXA = $dadosImpressora[0]['NMCAIXA'];

            $resposta = $this->impressaoUtil->iniciarPorta($IDMODEIMPRES, $CDPORTAIMPR);
            $resposta['error'] = false;
            if ($resposta['error'] == false) {
                $params = array(
                    'CDFILIAL' => $CDFILIAL,
                    'CDCAIXA' => $CDCAIXA,
                    'NRORG' => $NRORG
                );
                $dadosCaixa = $this->entityManager->getConnection()->fetchAll("BUSCA_DADOS_CAIXA", $params);
                $DTABERCAIX = $dadosCaixa[0]['DTABERCAIX'];

                $params = array(
                    'CDFILIAL' => $CDFILIAL,
                    'CDCAIXA' => $CDCAIXA,
                    'DTABERCAIX' => $DTABERCAIX,
                    'NRSEQVENDA' => $NRSEQVENDA,
                    'NRORG' => $NRORG
                );
                $types = array (
                    'DTABERCAIX' => \Doctrine\DBAL\TypeS\Type::DATE
                );
                $movimentacao = $this->entityManager->getConnection()->fetchAll("BUSCA_RECEBIMENTOS", $params, $types);

                $params = array(
                    'CDFILIAL' => $CDFILIAL,
                    'CDCAIXA' => $CDCAIXA,
                    'NRSEQVENDA' => $NRSEQVENDA,
                    'NRORG' => $NRORG
                );
                $produtos = $this->entityManager->getConnection()->fetchAll("BUSCA_PRODUTOS", $params);

                $troco = 0;
                $recebimentos = array();
                foreach ($movimentacao as $movAtual) {
                    if ($movAtual['IDTIPOMOVIVE'] == self::MOVIMENTACAO_ENTRADA) {
                        array_push($recebimentos, $movAtual);
                    } else if ($movAtual['IDTIPOMOVIVE'] == self::MOVIMENTACAO_SAIDA) {
                        $troco += $movAtual['VRMOVIVEND'];
                    }
                }

                $printerParams = $this->impressaoUtil->buscaParametrosImpressora($IDMODEIMPRES);
                $texto = '';
                $texto .= self::cabecalhoCupomNF($printerParams, $CDFILIAL, $CDCAIXA, $DTABERCAIX, $NMRAZSOCFILI, $NRINSJURFILI, $DSENDEFILI, $NMBAIRFILI, $NMMUNICIPIO, $SGESTADO, $CDINSCESTA, $CDINSCMUNI);
                $texto .= $this->impressaoUtil->centraliza($printerParams, 'Extrato No. ' . $NRNOTAFISCALCE) . $printerParams['comandoEnter'];
                $texto .= $this->impressaoUtil->centraliza($printerParams, 'CUPOM FISCAL ELETRONICO - SAT') . $printerParams['comandoEnter'];
                $texto .= $this->impressaoUtil->imprimeLinha($printerParams);
                if (empty($NRINSCRCONS)) {
                    $texto .= $this->impressaoUtil->centraliza($printerParams, 'CONSUMIDOR NAO IDENTIFICADO') . $printerParams['comandoEnter'];
                } else {
                    if (!empty($NMCONSVEND)) {
                        $texto .= $this->impressaoUtil->centraliza($printerParams, $NMCONSVEND) . $printerParams['comandoEnter'];
                    }
                    if (strlen($NRINSCRCONS) > 8) {
                    	// CPF
                    	$texto .= $this->impressaoUtil->centraliza($printerParams, 'CPF: ' . substr($NRINSCRCONS, 0, 3) . '.' . substr($NRINSCRCONS, 3, 3) . '.' . substr($NRINSCRCONS, 6, 3) . '-' . substr($NRINSCRCONS, 9, 2)) . $printerParams['comandoEnter'];
                    } else {
                    	// CNPJ
                    	$texto .= $this->impressaoUtil->centraliza($printerParams, 'CNPJ: ' . $NRINSCRCONS) . $printerParams['comandoEnter'];
                    }
                }
                $texto .= $this->impressaoUtil->centraliza($printerParams, '# | COD | DESC | QTD | UN | VL UNIT R$ | ST | ALIQ | VL ITEM R$') . $printerParams['comandoEnter'];
                $texto .= $this->impressaoUtil->imprimeLinha($printerParams);
                $texto .= self::itensCupomNF($produtos, $printerParams, $recebimentos, $troco);
                $texto .= $this->impressaoUtil->centraliza($printerParams, 'OBSERVACOES DO CONTRIBUINTE') . $printerParams['comandoEnter'];
                $texto .= $this->impressaoUtil->centraliza($printerParams, 'TRIBUTACAO APROXIMADA R$ ' . self::formataNumero($impostoFederal, 2) . ' FEDERAL E R$ ' . self::formataNumero($impostoEstadual, 2) . ' ESTADUAL') . $printerParams['comandoEnter'];
                $texto .= $this->impressaoUtil->centraliza($printerParams, '(CONFORME LEI FEDERAL 12.741/2012) - FONTE IBPT') . $printerParams['comandoEnter'];
                $texto .= $this->impressaoUtil->imprimeLinha($printerParams);
                $texto .= $this->impressaoUtil->centraliza($printerParams, 'SAT No. ' . substr($CDSERIESAT, 0, 3) . '.' . substr($CDSERIESAT, 3, 3) . '.' . substr($CDSERIESAT, 6, 3)) . $printerParams['comandoEnter'];
                $texto .= $this->impressaoUtil->centraliza($printerParams, $DTEMISSAONFCE->format('d/m/Y H:i:s')) . $printerParams['comandoEnter'];
                $texto .= $this->impressaoUtil->centraliza($printerParams, $NRACESSONFCE) . $printerParams['comandoEnter'];
                $texto .= $this->impressaoUtil->preencheLinha($printerParams, '', ' ');
                $this->impressaoUtil->imprimeTexto($texto, $IDMODEIMPRES);
                $this->impressaoUtil->configuraCodigoBarras($IDMODEIMPRES, 40, 0, 0, 0, 25);
                $this->impressaoUtil->imprimeCodigoBarrasCODE128($IDMODEIMPRES, substr($NRACESSONFCE, 4, 22));
                $this->impressaoUtil->imprimeCodigoBarrasCODE128($IDMODEIMPRES, substr($NRACESSONFCE, 26, 22));
                $this->impressaoUtil->configuraCodigoBarras($IDMODEIMPRES, 60, 2, 2, 0, 174);
                $this->impressaoUtil->imprimeQrCode($IDMODEIMPRES, $DSQRCODENFCE);
                $texto = $printerParams['comandoEnter'];
                $texto .= 'Filial: ' . $CDFILIAL . ' ' . $NMFILIAL . $printerParams['comandoEnter'];
                $texto .= 'Loja..: ' . $CDLOJA . ' ' . $NMLOJA . $printerParams['comandoEnter'];
                $texto .= 'Caixa.: ' . $CDCAIXA . ' ' . $NMCAIXA . $printerParams['comandoEnter'] . $printerParams['comandoEnter'];
                $texto .= $this->impressaoUtil->centraliza($printerParams, 'Consulte o QRCODE deste extrato atraves do app DeOlhoNaNota') . $printerParams['comandoEnter'] . $printerParams['comandoEnter'];
                $this->impressaoUtil->imprimeTexto($texto, $IDMODEIMPRES);
                $texto = $this->impressaoUtil->centraliza($printerParams, 'SENHA: ' . $CDSENHAPED, 24) . $printerParams['comandoEnter'];
                $this->impressaoUtil->imprimeTexto($texto, $IDMODEIMPRES, 0, 0, 1, 1, 3);
                $texto = $printerParams['comandoEnter'] . $printerParams['comandoEnter'] . $printerParams['comandoEnter'] . $printerParams['comandoEnter'];
                $this->impressaoUtil->imprimeTexto($texto, $IDMODEIMPRES);
                $this->impressaoUtil->cortaPapel($IDMODEIMPRES);
                $this->impressaoUtil->fechaPorta($IDMODEIMPRES);

                $result = array(
                    'error' => false
                );
            } else {
                $result = $resposta;
            }
        } else {
            $result = array(
                'error' => true,
                'message' => 'Impressora SAT não parametrizada para o caixa.'
            );
        }
        return $result;
    }

    private function itensCupomNF($produtos, $printerParams, $recebimentos, $troco) {
        $corpo = '';
        $qtdItens = count($produtos);
        $totalVendaSemDesconto = 0;
        $descontoVenda = 0;
        foreach ($produtos as $index => $item) {
            $totalVendaSemDesconto += ($item['VRUNITVEND'] * $item['QTPRODVEND']) + $item['VRACRITVEND'];
            $descontoVenda += $item['VRDESITVEND'];

            $nrItem = str_pad($index + 1, 3, '0', STR_PAD_LEFT);
            $newItem = $nrItem . " " . $item['CDARVPROD'] . " " . $item['NMPRODUTO'];

            if ($item['SGUNIDADE'] != 'UN') {
                $qtd = self::formataNumero($item['QTPRODVEND'], 3);
            } else {
                $qtd = intval($item['QTPRODVEND']);
            }
            $valorImposto = $item['VRPEALIMPFIS'] > 0 ?  self::formataNumero($item['VRPEALIMPFIS'], 2) : $item['VRPEALIMPFIS'];
            $impostoItem = $item['IDTPIMPOSFIS'] . ' ' . $valorImposto . '%';
            $valorItemCalculado = self::formataNumero($item['QTPRODVEND'] * $item['VRUNITVEND'], 2);
            $finalItem = $qtd . ' ' . $item['SGUNIDADE'] . " X " . self::formataNumero($item['VRUNITVEND'], 2) . ' ' . $impostoItem . ' ' . $valorItemCalculado;
            $textoAlinhado = $this->impressaoUtil->alinhaInicioFim($printerParams, $newItem, $finalItem);
            $corpo .=  $textoAlinhado . $printerParams['comandoEnter'];
            $total = $item['QTPRODVEND'] * round($item['VRUNITVEND'], 2);
            if ($item['VRDESITVEND'] > 0) {
                $corpo .= $this->impressaoUtil->preencheLinha($printerParams, 'Desconto no item ' . $nrItem . ' - '. $item['VRDESITVEND'], ' ');
            }
        }
        $corpo .= $this->impressaoUtil->preencheLinha($printerParams, 'Vr. Total Pago: = ' . self::formataNumero($totalVendaSemDesconto - $descontoVenda, 2), ' ');
        $corpo .= $this->impressaoUtil->preencheLinha($printerParams, 'Valor de Troco: = ' . self::formataNumero($troco, 2), ' ');
        $inicio = 'SUBTOTAL R$ ';
        $final = self::formataNumero($totalVendaSemDesconto, 2);
        $textoAlinhado = $this->impressaoUtil->alinhaInicioFim($printerParams, $inicio, $final);
        $corpo .= $textoAlinhado . $printerParams['comandoEnter'];
        if ($descontoVenda > 0) {
            $corpo .= 'DESCONTOS R$ ' . self::formataNumero($descontoVenda, 2) . $printerParams['comandoEnter'];
        }
        $inicio = 'TOTAL R$ ';
        $final = self::formataNumero($totalVendaSemDesconto - $descontoVenda, 2);
        $textoAlinhado = $this->impressaoUtil->alinhaInicioFim($printerParams, $inicio, $final);
        $corpo .= $textoAlinhado . $printerParams['comandoEnter'];
        foreach ($recebimentos as $recebimentoAtual) {
            $nomeTiporece = $recebimentoAtual['DSIMPFISCAL'] ? $recebimentoAtual['DSIMPFISCAL'] : $recebimentoAtual['NMTIPORECE'];
            $valorRecebimento = self::formataNumero($recebimentoAtual['VRMOVIVEND'], 2);
            $textoAlinhado = $this->impressaoUtil->alinhaInicioFim($printerParams, $nomeTiporece, $valorRecebimento);
            $corpo .= $textoAlinhado . $printerParams['comandoEnter'];
        }
        $corpo .= $this->impressaoUtil->imprimeLinha($printerParams);
        return $corpo;
    }

    private function formataNumero($numeroFloat, $numeroCasas) {
    	return str_replace('.', ',', number_format($numeroFloat, $numeroCasas));
    }

    private function cabecalhoCupomNF($printerParams, $CDFILIAL, $CDCAIXA, $DTABERCAIX, $NMRAZSOCFILI, $NRINSJURFILI, $DSENDEFILI, $NMBAIRFILI, $NMMUNICIPIO, $SGESTADO, $CDINSCESTA, $CDINSCMUNI) {
        $cabecalho = '';
        $cabecalho .= $this->impressaoUtil->centraliza($printerParams, $NMRAZSOCFILI) . $printerParams['comandoEnter'];
        $cabecalho .= $this->impressaoUtil->centraliza($printerParams, $DSENDEFILI) . $printerParams['comandoEnter'];
        $cabecalho .= $this->impressaoUtil->centraliza($printerParams, $NMBAIRFILI . ' - ' . $NMMUNICIPIO . ' - ' . $SGESTADO) . $printerParams['comandoEnter'];
        $cabecalho .= $this->impressaoUtil->centraliza($printerParams, 'CNPJ: ' . $NRINSJURFILI . ' IE: ' . $CDINSCESTA . ' IM: '. $CDINSCMUNI) . $printerParams['comandoEnter'];
        $cabecalho .= $this->impressaoUtil->imprimeLinha($printerParams);
        return $cabecalho;
    }

}