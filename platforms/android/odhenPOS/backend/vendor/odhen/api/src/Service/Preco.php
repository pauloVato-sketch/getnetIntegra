<?php

namespace Odhen\API\Service;

use Odhen\API\Util\Exception;

class Preco {

    protected $entityManager;
    protected $util;
    protected $date;

    public function __construct(
        \Doctrine\ORM\EntityManager $entityManager,
        \Odhen\API\Util\Util $util,
        \Odhen\API\Util\Date $date) {

        $this->entityManager = $entityManager;
        $this->util = $util;
        $this->date = $date;
    }

    public function buscaPreco($CDFILIAL, $CDCLIENTE, $CDPRODUTO, $CDLOJA, $CDCONSUMIDOR, $DISCOUNT = null, $DATETIME = null) {
        $tabelaEscolhida = self::buscaTabelaVigente($CDFILIAL, $CDLOJA, $CDCLIENTE);
        if (!$tabelaEscolhida['error']) {
            $retorno = self::buscaPrecoProduto(
                $tabelaEscolhida["CDFILIAL"],
                $tabelaEscolhida["CDTABEPREC"],
                $CDPRODUTO,
                $tabelaEscolhida['DTINIVGPREC'],
                $CDCLIENTE,
                $CDCONSUMIDOR,
                $DISCOUNT,
                $DATETIME
            );
        }
        else {
            $retorno = $tabelaEscolhida;
        }
        return $retorno;
    }

    public function buscaTabelaVigente($CDFILIAL, $CDLOJA, $CDCLIENTE) {
        // busca tabela de preço por CLIENTEFILIAL
        $params = array(
            "CDFILIAL" => $CDFILIAL,
            "CDCLIENTE" => $CDCLIENTE
        );

        $tabelaClienteFilial = $this->entityManager->getConnection()->fetchAssoc("CLIENTE_FILIAL", $params);
        if ($tabelaClienteFilial["CDTABEPREC"]) {
            // valida a vigência
            $DTINIVGPREC = self::validaVigencia($tabelaClienteFilial['CDCFILTABPRE'], $tabelaClienteFilial["CDTABEPREC"]);
            if ($DTINIVGPREC == false) {
                $retorno = array(
                    'error' => true,
                    'message' => 'Tabela de preços associada ao cliente/filial está inativa.'
                );
            } else {
                $retorno = array(
                	'error' => false,
                    'CDFILIAL' => $tabelaClienteFilial["CDCFILTABPRE"],
                    'CDTABEPREC' => $tabelaClienteFilial["CDTABEPREC"],
                    'DTINIVGPREC' => $DTINIVGPREC
                );
            }
        } else {
            // busca a tabela de preço por CLIENTE
            $params = array(
                'CDCLIENTE' => $CDCLIENTE
            );
            $tabelaCliente = $this->entityManager->getConnection()->fetchAssoc("EXISTE_PRECO_CLIE", $params);
            if ($tabelaCliente["CDTABEPREC"]) {
                // valida a vigência
                $DTINIVGPREC = self::validaVigencia($tabelaCliente["CDFILTABPREC"], $tabelaCliente["CDTABEPREC"]);
                if ($DTINIVGPREC == false) {
                    $retorno = array(
                        'error' => true,
                        'message' => 'Tabela de preços associada ao cliente está inativa.'
                    );
                } else {
                    $retorno = array(
                        'error' => false,
                        'CDFILIAL' => $tabelaCliente["CDFILTABPREC"],
                        'CDTABEPREC' => $tabelaCliente["CDTABEPREC"],
                        'DTINIVGPREC' => $DTINIVGPREC
                    );
                }
            } else {
                // busca tabela de preco por LOJA
                $params = array(
                    'CDFILIAL' => $CDFILIAL,
                    'CDLOJA' => $CDLOJA
                );
                $tabelaLoja = $this->entityManager->getConnection()->fetchAssoc("TABELA_PRECO_LOJA", $params);
                if ($tabelaLoja["CDTABEPREC"]) {
                    // valida a vigência
                    $DTINIVGPREC = self::validaVigencia($CDFILIAL, $tabelaLoja["CDTABEPREC"]);
                    if ($DTINIVGPREC == false) {
                        $retorno = array(
                            'error' => true,
                            'message' => 'Tabela de preços associada à loja está inativa.'
                        );
                    } else {
                        $retorno = array(
                            'error' => false,
                            'CDFILIAL' => $CDFILIAL,
                            'CDTABEPREC' => $tabelaLoja["CDTABEPREC"],
                            'DTINIVGPREC' => $DTINIVGPREC
                        );
                    }
                } else {
                    // busca tabela de preço por FILIAL (padrão)
                    $params = array(
                        'CDFILIAL' => $CDFILIAL
                    );
                    $paravend = $this->entityManager->getConnection()->fetchAssoc("PARAVEND", $params);
                    // se não tiver tabela de preço parametrizada retorna código de erro
                    if ((bool)$paravend["CDTABEPREC"] == false) {
                        $retorno = array(
                            'error' => true,
                            'message' => 'Não há tabela de preços cadastrada.'
                        );
                    } else {
                        // valida a vigência
                        $DTINIVGPREC = self::validaVigencia($CDFILIAL, $paravend["CDTABEPREC"]);
                        if ($DTINIVGPREC == false) {
                            $retorno = array(
                                'error' => true,
                                'message' => 'Tabela de preços está inativa.'
                            );
                        } else {
                            $retorno = array(
                                'error' => false,
                                'CDFILIAL' => $CDFILIAL,
                                'CDTABEPREC' => $paravend["CDTABEPREC"],
                                'DTINIVGPREC' => $DTINIVGPREC
                            );
                        }
                    }
                }
            }
        }
        return $retorno;
    }

    public function validaVigencia($CDFILIAL, $CDTABEPREC) {
        // valida vigência
        $params = array(
            'CDFILIAL'   => $CDFILIAL,
            'CDTABEPREC' => $CDTABEPREC
        );
        $retorno = $this->entityManager->getConnection()->fetchAssoc("TABELA_VENDA", $params);

        if ($retorno) {
            return $retorno["DTINIVGPREC"];
        } else {
            return false;
        }
    }

    private function validaVigenciaGeral($CDTABEPREC){
        $params = array(
            ':CDTABEPREC' => $CDTABEPREC
        );
        $retorno = $this->entityManager->getConnection()->fetchAssoc("TABELA_VENDA_GERAL", $params);

        if ($retorno) {
            return $retorno["DTINIVGPREC"];
        } else {
            return false;
        }
    }

    public function buscaPrecoProduto($CDFILIAL, $CDTABEPREC, $CDPRODUTO, $DTINIVGPREC, $CDCLIENTE, $CDCONSUMIDOR, $DISCOUNT, $DATETIME){
        $retorno = array(
            'error' => false,
            'PRECOCLIE' => 0,
            'PRECOVAR' => 0,
            'PRECOSUGER' => 0,
            'DESC' => 0,
            'ACRE' => 0,
            'PRECO' => 0,
            'HRINIVENPROD' => 0,
            'HRFIMVENPROD' => 0,
            'retirarProduto' => false
        );

        // formata a data de vigência
        if ($this->util->databaseIsOracle()){
            $DTINIVGPREC = $this->date->getDataDeString($DTINIVGPREC, "d-m-Y H:i:s")->format(\Odhen\API\Util\Date::FORMATO_BRASILEIRO);
        } else {
            $DTINIVGPREC = $this->date->getDataDeString($DTINIVGPREC, "Y-m-d H:i:s")->format(\Odhen\API\Util\Date::FORMATO_BRASILEIRO);
        }

        // busca dados do produto
        $params = array(
            'CDFILIAL' => $CDFILIAL,
            'CDTABEPREC' => $CDTABEPREC,
            'DTINIVGPREC' => $DTINIVGPREC,
            'CDPRODUTO' => $CDPRODUTO
        );
        $itemTabPreco = $this->entityManager->getConnection()->fetchAssoc("ITEM_PRECO", $params);
        if ($DISCOUNT) $itemTabPreco['PRECO'] = round(floatval($itemTabPreco['PRECO']) - floatval($DISCOUNT), 2);

        $precoGeral = false;
        // se não tiver preço cadastrado, verifica se existe preço na tabela geral
        if (empty($itemTabPreco)){
            $params = array(
                'CDFILIAL' => $CDFILIAL
            );
            $CDTABEPRECGER = $this->entityManager->getConnection()->fetchAssoc("GET_CDTABEPREC_GERAL", $params);
            if (empty($CDTABEPRECGER)){
                $retorno["error"] = true;
                $retorno["message"] = 'Preço não cadastrado para o produto ' . $CDPRODUTO;
                $retorno["retirarProduto"] = true;
                return $retorno;
            }
            else {
                $DTINIVGPREC = self::validaVigenciaGeral($CDTABEPRECGER['CDTABEPREC']);

                if ($DTINIVGPREC == false){
                    $retorno["error"] = true;
                    $retorno["message"] = 'Preço não cadastrado para o produto ' . $CDPRODUTO;
                    $retorno["retirarProduto"] = true;
                    return $retorno;
                }
                else {

                    if ($this->util->databaseIsOracle()){
                        $DTINIVGPREC = $this->date->getDataDeString($DTINIVGPREC, "d-m-Y H:i:s")->format(\Odhen\API\Util\Date::FORMATO_BRASILEIRO);
                    } else {
                        $DTINIVGPREC = $this->date->getDataDeString($DTINIVGPREC, "Y-m-d H:i:s")->format(\Odhen\API\Util\Date::FORMATO_BRASILEIRO);
                    }

                    $precoGeral = true;
                    $CDTABEPREC = $CDTABEPRECGER['CDTABEPREC'];

                    $params = array(
                        'CDTABEPREC' => $CDTABEPREC,
                        'DTINIVGPREC' => $DTINIVGPREC,
                        'CDPRODUTO' => $CDPRODUTO
                    );
                    $itemTabPreco = $this->entityManager->getConnection()->fetchAssoc("ITEM_PRECO_GERAL", $params);
                    if ($DISCOUNT) $itemTabPreco['PRECO'] = round(floatval($itemTabPreco['PRECO']) - floatval($DISCOUNT), 2);

                    if (empty($itemTabPreco)){
                        $retorno["error"] = true;
                        $retorno["message"] = 'Preço não cadastrado para o produto ' . $CDPRODUTO;
                        $retorno["retirarProduto"] = true;
                        return $retorno;
                    }
                }
            }
        }

        // verifica se o preco está zerado
        if ($itemTabPreco["PRECO"] + $itemTabPreco["PRECOCLIE"] === 0){
            $retorno["error"] = true;
            $retorno["message"] = 'O preço do produto ' . $CDPRODUTO . ' está zerado.';
        } else {
            // caso o produto seja válido, busca preco do produto
            $result       = self::precoDia($CDFILIAL, $CDTABEPREC, $CDPRODUTO, $DTINIVGPREC, $CDCLIENTE, $CDCONSUMIDOR, $DATETIME, $precoGeral);

            $precoDia     = floatval($result["VRPRECODIA"]); //VRPRECODIA
            $perc         = $result["PERC"]; //IDPERVALORPR
            $descAcre     = $result["DESCACRE"]; //IDDESCACREPR
            $idVisuaCupom = $result["IDVISUACUPOM"]; //IDVISUACUPOM

            $retorno["PRECOVAR"]   = floatval($itemTabPreco["IDPRECVARIA"]);
            $retorno["PRECOCLIE"]  = floatval($itemTabPreco["PRECOCLIE"]);
            $retorno["PRECOSUGER"] = floatval($itemTabPreco["PRECOSUGER"]);
            $retorno["HRINIVENPROD"] = floatval($itemTabPreco["HRINIVENPROD"]);
            $retorno["HRFIMVENPROD"] = floatval($itemTabPreco["HRFIMVENPROD"]);

            // verifica se existe preco do dia
            if (empty($precoDia)) {
                $retorno["PRECO"] = floatval($itemTabPreco["PRECO"]);
            } else {
                // acrescenta ou desconta, o valor ou percentual, do preço cadastrado
                $retorno["PRECOVAR"]   = floatval($itemTabPreco["IDPRECVARIA"]);
                $retorno["PRECOCLIE"]  = floatval($itemTabPreco["PRECOCLIE"]);
                $retorno["PRECOSUGER"] = 0;

                // define se é valor (V) ou percentual (P)
                if ($perc == "V") {
                    // define se é desconto (D) ou acréscimo (A)
                    if ($descAcre == "A") {
                        // define se desconto aparece no cupom ou não
                        if ($idVisuaCupom == "N") {
                            $retorno["ACRE"] = 0;
                            $retorno["PRECO"] = floatval(bcadd(str_replace(',','.',strval($itemTabPreco["PRECO"])), str_replace(',','.',strval($precoDia)), '2'));
                        } else {
                            $retorno["ACRE"] = $precoDia;
                            $retorno["PRECO"] = floatval(bcmul(str_replace(',','.',strval($itemTabPreco["PRECO"])), '1', '2'));
                        }
                    } else {
                        // define se desconto aparece no cupom ou não
                        if ($idVisuaCupom == "N") {
                            $retorno["DESC"] = 0;

                            $precoTotal = $itemTabPreco["PRECO"] + $itemTabPreco["PRECOCLIE"];
                            $descPreco = ($itemTabPreco["PRECO"] / $precoTotal) * $precoDia;
                            $descSubsidio = ($itemTabPreco["PRECOCLIE"] / $precoTotal) * $precoDia;

                            $retorno["PRECO"] = floatval(bcsub(str_replace(',','.',strval($itemTabPreco["PRECO"])), str_replace(',','.',strval($descPreco)), '2'));
                            $retorno["PRECOCLIE"] = floatval(bcsub(str_replace(',','.',strval($itemTabPreco["PRECOCLIE"])), str_replace(',','.',strval($descSubsidio)), '2'));
                            if (($retorno["PRECO"] + $retorno["PRECOCLIE"]) < ($precoTotal - $precoDia)){
                                $retorno["PRECO"] += 0.01;
                            }
                        } else {
                            $retorno["DESC"] = $precoDia;
                            $retorno["PRECO"] = floatval(bcmul(str_replace(',','.',strval($itemTabPreco["PRECO"])), '1', '2'));
                        }
                    }
                } else {
                    // define se é desconto (D) ou acréscimo (A)

                    if ($descAcre == "A") {
                        $acrescimoAux = ($itemTabPreco["PRECO"] * $precoDia) / 100;
                        // define se desconto aparece no cupom ou não
                        if ($idVisuaCupom == "N") {
                            $retorno["ACRE"] = 0;
                            $retorno["PRECO"] = floatval(bcmul(str_replace(',','.',strval($itemTabPreco["PRECO"] + $acrescimoAux)), '1', '2'));
                        } else {
                            $retorno["ACRE"] = floatval(bcmul(str_replace(',','.',strval($acrescimoAux)), '1', '2'));
                            $retorno["PRECO"] = floatval(bcmul(str_replace(',','.',strval($itemTabPreco["PRECO"])), '1', '2'));
                        }
                    } else {
                        $itemTabPreco["PRECO"] = floatval(bcmul(str_replace(',','.',strval($itemTabPreco["PRECO"])), '1', '2'));
                        $itemTabPreco["PRECOCLIE"] = floatval(bcmul(str_replace(',','.',strval($itemTabPreco["PRECOCLIE"])), '1', '2'));
                        $descontoAux = floatval($itemTabPreco["PRECO"] * ($precoDia/100));
                        $subsidyAux = floatval($itemTabPreco["PRECOCLIE"] * ($precoDia/100));

                        // define se desconto aparece no cupom ou não
                        if ($idVisuaCupom == "N"){
                            $retorno["DESC"] = 0;
                            $retorno["PRECO"] = floatval(bcsub(str_replace(',','.',strval($itemTabPreco["PRECO"])), str_replace(',','.',strval($descontoAux)), '2'));

                            if ($retorno["PRECO"] < 0.01){
                                $retorno["PRECO"] = 0.01;
                            }

                            $retorno["PRECOCLIE"] = floatval(bcsub(str_replace(',','.',strval($itemTabPreco["PRECOCLIE"])), str_replace(',','.',strval($subsidyAux)), '2'));
                        } else {
                            $retorno["DESC"] = floatval(bcadd(str_replace(',','.',strval($descontoAux)), str_replace(',','.',strval($subsidyAux)), '2'));
                            $retorno["PRECO"] = $itemTabPreco["PRECO"];
                            $retorno["PRECOCLIE"] = $itemTabPreco["PRECOCLIE"];
                            // Garante que o preço não vai ser menor que 0.01 após o desconto.
                            $priceCheck = floatval(bcsub(str_replace(',','.',strval($retorno["PRECO"] + $retorno["PRECOCLIE"])), str_replace(',','.',strval($retorno["DESC"])), '2'));
                            if ($priceCheck <= 0){
                                $retorno["DESC"] = floatval(bcsub(str_replace(',','.',strval($retorno["PRECO"] + $retorno["PRECOCLIE"])), '0.01', '2'));
                            }
                        }
                    }
                }
            }
        }

        return $retorno;
    }

    /**
     * retorno: [VRPRECODIA, PERC, DESCACRE,IDVISUACUPOM]
     */
    public function precoDia($CDFILIAL, $CDTABEPREC, $CDPRODUTO, $DTINIVGPREC, $CDCLIENTE, $CDCONSUMIDOR, $DATETIME, $precoGeral){

        if ($DATETIME == null) $dataAtual = $this->date->getDataAtual();
        else $dataAtual = $DATETIME;
        $diaSeguinte = $this->date->adicionaIntervalo($dataAtual);

        $params = array(
            'CDCLIENTE'    => $CDCLIENTE,
            'CDCONSUMIDOR' => $CDCONSUMIDOR
        );

        // busca o tipo de consumidor do consumidor passado
        $retorno = $this->entityManager->getConnection()->fetchAssoc("TIPO_CONSUMIDOR", $params);
        if ($retorno) {
            $tipoConsumidor = $retorno["CDTIPOCONS"];
        } else {
            $tipoConsumidor = "T";
        }

        $params = array(
            'CDFILIAL'   => $CDFILIAL,
            'DTFERIFILI' => $dataAtual
        );
        $type = array(
            'DTFERIFILI' => \Doctrine\DBAL\Types\Type::DATETIME
        );
        // verifica se é feridado
        $retorno = $this->entityManager->getConnection()->fetchAssoc("HOLIDAY_CHECK", $params, $type);

        if ($retorno) {
            $dia = "F";
        } else {
            // verifica se o dia seguinte é feriado (significa que hoje é véspera de feriado)
            // comentado, Murillo do Madero falou que o desconto do happy hour aplica na véspera também

            /*
            $retorno = $this->entityManager->getConnection()->fetchAssoc (
                self::FERIADO, array($CDFILIAL, $diaSeguinte->format("d/m/Y"))
            );
            if ($retorno) {
                $dia = "V";
            } else {
            */
            // Expected: 1 (for Sunday) through 7 (for Saturday)
            // PHP returns: 1 (for Monday) through 7 (for Sunday)
            // So, this method changes the index for each day, changing from the format of return
            // used by PHP to Delphi format.
            $dia = $dataAtual->format("N") % 7 + 1;
            //}
        }

        // 1 domingo
        // 2 segunda
        // 3 terca
        // 4 quarta
        // 5 quinta
        // 6 sexta
        // 7 sabado
        // F - feriado
        // V - dia antes do feriado (véspera)

        $hora = $dataAtual->format("H:i:s");
        $hora = str_replace(":", "", $hora);
        $hora = substr($hora, 0, 4);

        $params = array(
            'CDFILIAL'     => $CDFILIAL,
            'CDTABEPREC'   => $CDTABEPREC,
            'DTINIVGPREC'  => $DTINIVGPREC,
            'CDPRODUTO'    => $CDPRODUTO,
            'CDPRPAITABPR' => $CDPRODUTO,
            'NRDIASEMANPR' => (string) $dia,
            'CDTIPOCONSPD' => $tipoConsumidor,
            'HORA'         => $hora
        );

        if ($precoGeral){
            $priceQuery = "ITEM_PRECO_DIA_GERAL";
        }
        else {
            $priceQuery = "ITEM_PRECO_DIA";
        }
        $itemPrecoDia = $this->entityManager->getConnection()->fetchAssoc($priceQuery, $params);

        if ($itemPrecoDia) {
            $retorno["PERC"]         = $itemPrecoDia["IDPERVALORPR"];
            $retorno["DESCACRE"]     = $itemPrecoDia["IDDESCACREPR"];
            $retorno["IDVISUACUPOM"] = $itemPrecoDia["IDVISUACUPOM"];
            $retorno["VRPRECODIA"]   = $itemPrecoDia["VRPRECODIA"];
        } else {
            $params = array(
                'CDFILIAL'     => $CDFILIAL,
                'CDTABEPREC'   => $CDTABEPREC,
                'DTINIVGPREC'  => $DTINIVGPREC,
                'CDPRODUTO'    => $CDPRODUTO,
                'CDPRPAITABPR' => $CDPRODUTO,
                'NRDIASEMANPR' => "T",
                'CDTIPOCONSPD' => $tipoConsumidor,
                'HORA'         => $hora
            );

            $itemPrecoDia = $this->entityManager->getConnection()->fetchAssoc($priceQuery, $params);

            if ($itemPrecoDia) {
                $retorno["PERC"]         = $itemPrecoDia["IDPERVALORPR"];
                $retorno["DESCACRE"]     = $itemPrecoDia["IDDESCACREPR"];
                $retorno["IDVISUACUPOM"] = $itemPrecoDia["IDVISUACUPOM"];
                $retorno["VRPRECODIA"]   = $itemPrecoDia["VRPRECODIA"];
            } else {
                // Expected: 1 (for Sunday) through 7 (for Saturday)
                // PHP returns: 1 (for Monday) through 7 (for Sunday)
                // So, this method changes the index for each day, changing from the forat of return
                // used by PHP to Delphi format.
                $dia = (((int)$dataAtual->format("N")) % 7) + 1;

                $params = array(
                    'CDFILIAL'     => $CDFILIAL,
                    'CDTABEPREC'   => $CDTABEPREC,
                    'DTINIVGPREC'  => $DTINIVGPREC,
                    'CDPRODUTO'    => $CDPRODUTO,
                    'CDPRPAITABPR' => $CDPRODUTO,
                    'NRDIASEMANPR' => (string) $dia,
                    'CDTIPOCONSPD' => "T",
                    'HORA'         => $hora
                );

                $itemPrecoDia = $this->entityManager->getConnection()->fetchAssoc($priceQuery, $params);

                if ($itemPrecoDia) {
                    $retorno["PERC"]         = $itemPrecoDia["IDPERVALORPR"];
                    $retorno["DESCACRE"]     = $itemPrecoDia["IDDESCACREPR"];
                    $retorno["IDVISUACUPOM"] = $itemPrecoDia["IDVISUACUPOM"];
                    $retorno["VRPRECODIA"]   = $itemPrecoDia["VRPRECODIA"];
                } else {
                    $retorno = null;
                }
            }
        }

        return $retorno;
    }

    public function subgroupDiscountTableInterface($produtos, $CDFILIAL, $CDLOJA){
        // Produtos do tipo couvert e consumação não levam este desconto.
        $params = array(
            'CDFILIAL' => $CDFILIAL,
            'CDLOJA' => $CDLOJA,
        );
        $exclusoes = $this->entityManager->getConnection()->fetchAssoc("BUSCA_COUVERT_CONSUMA", $params);

        $productsParam = array();
        foreach($produtos as $key => $produto){
            // Não podemos aplicar este desconto em produtos combinados. Sendo assim, a única maneira de identificar produtos combinados
            // após eles terem sido inseridos, é vendo se eles tem NRSEQPRODCOM sem CDPRODPROMOCAO, e sem produtos na ITCOMANDAEST.
            $comboProd = $produto['NRSEQPRODCOM'] != null && $produto['CDPRODPROMOCAO'] == null;
            if ($comboProd){
                $params = array(
                    'CDFILIAL' => $CDFILIAL,
                    'NRVENDAREST' => $produto['NRVENDAREST'],
                    'NRCOMANDA' => $produto['NRCOMANDA'],
                    'NRPRODCOMVEN' => $produto['NRPRODCOMVEN']
                );
                $produtosEST = $this->entityManager->getConnection()->fetchAll("SELECT_ITCOMANDAEST_SPECIFIC", $params);
                if (!empty($produtosEST)) $comboProd = false;
            }

            if (!$comboProd && ($exclusoes['IDCOUVERART'] !== 'S' || $produto['CDPRODUTO'] !== $exclusoes['CDPRODCOUVER']) && ($exclusoes['IDCONSUMAMIN'] !== 'S' || $produto['CDPRODUTO'] !== $exclusoes['CDPRODCONSUM'])){
                $quantidade = floatval($produto['QTPRODCOMVEN']);
                $total = floatval($produto['VRPRECCOMVEN']) + floatval($produto['VRPRECCLCOMVEN']) + (floatval($produto['VRACRCOMVEN']) / $quantidade);
                $desconto = floatval($produto['VRDESCCOMVEN']);
                $productsParam[$key] = array(
                    'INDEX'      => $key,
                    'CDPRODUTO'  => $produto['CDPRODUTO'],
                    'QUANTIDADE' => $quantidade,
                    'VALOR'      => $total,
                    'DESCONTO'   => $desconto / $quantidade
                );
            }
        }
        $descontosSubgrupo = $this->applySubgroupDiscount($productsParam);

        foreach ($descontosSubgrupo as $produto){
            $descontoFinal = $produto['DESCONTO'] * $produto['QUANTIDADE'];
            $produtos[$produto['INDEX']]['VRDESCCOMVEN'] = floatval(bcmul(str_replace(',','.',strval($descontoFinal)), '1', '2'));
        }

        return $produtos;
    }

    public function applySubgroupDiscount($products){
        // Constrói string para query.
        $strProducts = "_";
        foreach ($products as $product){
            $strProducts .= $product['CDPRODUTO'].'_';
        }
        // Busca o código dos subgrupos dos produtos.
        $params = array(
            'STRPRODUTOS' => $strProducts
        );
        $result = $this->entityManager->getConnection()->fetchAll("BUSCA_PRODUTO_SUBGRUPO_API", $params);
        $subgroups = array();
        // Indexa o resultado.
        foreach ($result as $subgroup){
            $subgroups[$subgroup['CDPRODUTO']] = array(
                'CDGRUPPROD' => $subgroup['CDGRUPPROD'],
                'CDSUBGRPROD' => $subgroup['CDSUBGRPROD']
            );
        }
        // Agrupa produtos de grupos semelhantes.
        $subgroupCluster = array();
        foreach ($products as $product){
            if (array_key_exists($product['CDPRODUTO'], $subgroups)){
                $key = $subgroups[$product['CDPRODUTO']]['CDGRUPPROD'].$subgroups[$product['CDPRODUTO']]['CDSUBGRPROD'];
                if (!array_key_exists($key, $subgroupCluster)){
                    // Verifica se existe desconto para este subgrupo.
                    $params = array(
                        'CDGRUPPROD' => $subgroups[$product['CDPRODUTO']]['CDGRUPPROD'],
                        'CDSUBGRPROD' => $subgroups[$product['CDPRODUTO']]['CDSUBGRPROD']
                    );
                    $discount = $this->entityManager->getConnection()->fetchAssoc("BUSCA_DESCONTO_SUBGRUPO_API", $params);
                    if (!empty($discount)){
                        $subgroupCluster[$key] = array(
                            'DISCOUNT' => floatval($discount['QTPERCDESC']),
                            'QTMIN' => intval($discount['QTINICIALFX']),
                            'QTMAX' => intval($discount['QTFINALFX']),
                            'products' => array()
                        );
                        array_push($subgroupCluster[$key]['products'], $product);
                    }
                }
                else {
                    array_push($subgroupCluster[$key]['products'], $product);
                }
            }
        }
        // Aplica os descontos nos grupos.
        foreach ($subgroupCluster as $cluster){
            // Verifica a quantidade total de produtos.
            $quantity = 0;
            foreach ($cluster['products'] as $product){
                $quantity += ceil(floatval($product['QUANTIDADE']));
            }
            foreach ($cluster['products'] as $product){
                $price = floatval(bcsub(str_replace(',','.',strval($product['VALOR'])), str_replace(',','.',strval($product['DESCONTO'])), '2'));
                if ($quantity >= $cluster['QTMIN'] && $quantity <= $cluster['QTMAX']){
                    $newDiscount = floatval(bcmul(str_replace(',','.',strval($price)), str_replace(',','.',strval($cluster['DISCOUNT'] / 100)), '2'));
                    $totalDiscount = $product['DESCONTO'] + $newDiscount;
                    $newTotal = $product['VALOR'] - $totalDiscount;
                    // Valida caso do desconto ser maior que o preço total.
                    if ($newTotal <= 0.01){
                        $totalDiscount = $product['VALOR'] - 0.01;
                        $newTotal = 0.01;
                    }
                    // Altera desconto e o total do array de produtos principal.
                    $products[$product['INDEX']]['DESCONTO'] = $totalDiscount;
                    $products[$product['INDEX']]['TOTAL'] = floatval(bcmul(str_replace(',','.',strval($newTotal)), '1', '2'));
                }
            }
        }

        return $products;
    }

}
