<?php

namespace Odhen\API\Service;

class Parametros {

    protected $entityManager;
    protected $date;
    protected $precoService;
    protected $databaseUtil;

    public function __construct(\Doctrine\ORM\EntityManager $entityManager, \Odhen\API\Util\Date $date, \Odhen\API\Service\Preco $precoService, \Odhen\API\Util\Util $util, \Odhen\API\Util\Database $databaseUtil){
        $this->entityManager = $entityManager;
        $this->date = $date;
        $this->precoService = $precoService;
        $this->util = $util;
        $this->databaseUtil = $databaseUtil;
    }

    const URL_SERVIDOR_IMAGEM = 'http://midia.teknisa.com/files/';
    const TIPO_GRUPO = '2';
    const TIPO_SUB_GRUPO = 'I';
    const OBS_TIPO_ADICIONAR = 'A';

    public function setFileServer($fileServer){
        $this->fileServer = $fileServer;
    }

    public function buscaFiliaisByOperador($CDOPERADOR){
        $params = array(
            ':CDOPERADOR' => $CDOPERADOR
        );
        return $this->entityManager->getConnection()->fetchAll("GET_FILIAIS_BY_OPERADOR", $params);
    }

    public function buscaClientePadrao($CDFILIAL){
        $params = array(
            'CDFILIAL' => $CDFILIAL
        );
        $dadosFilial = $this->entityManager->getConnection()->fetchAll("BUSCA_DADOS_FILIAL", $params);
        return $dadosFilial[0]['CDCLIENTE'];
    }

    public function carregaDados($CDFILIAL, $CDCAIXA, $CDVENDEDOR, $CDOPERADOR){
        $params = array(
            'CDFILIAL' => $CDFILIAL,
            'CDCAIXA' => $CDCAIXA
        );
        $dadosCaixa = $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_CAIXA_PARAM", $params);

        $NRCONFTELA = $this->util->getConfTela($CDFILIAL, $CDCAIXA);
        $CDLOJA = $dadosCaixa['CDLOJA'];

        $params = array(
            'CDFILIAL' => $CDFILIAL
        );
        $dadosFilial = $this->entityManager->getConnection()->fetchAll("BUSCA_DADOS_FILIAL", $params);

        $CDCLIENTE = $dadosFilial[0]['CDCLIENTE'];

        $params = array(
            'CDFILIAL' => $CDFILIAL,
            'CDLOJA' => $CDLOJA
        );
        $dadosLoja = $this->entityManager->getConnection()->fetchAll("BUSCA_DADOS_LOJA_PARAM", $params);

        $NRMESAPADRAO = $dadosLoja[0]['NRMESAPADRAO'];
        if($dadosCaixa['IDHABCAIXAVENDA'] !== 'TAA' && $dadosCaixa['IDHABCAIXAVENDA'] !== 'APC') {
            $params = array(
                'CDVENDEDOR' => $CDVENDEDOR
            );
            $dadosVendedor = $this->entityManager->getConnection()->fetchAll("BUSCA_DADOS_VENDEDOR_PARAM", $params);
            $CDOPERADOR = $dadosVendedor[0]['CDOPERADOR'];
        } else {
            $dadosVendedor = array();
            $dadosVendedor[0]['CDVENDEDOR'] = null;
            $dadosVendedor[0]['NMFANVEN'] = null;
            $dadosParavend[0]['NRATRAPADRAO'] = null;
        }


        $params = array(
            'CDOPERADOR' => $CDOPERADOR
        );
        $dadosOperador = $this->entityManager->getConnection()->fetchAll("BUSCA_DADOS_OPERADOR", $params);

        $params = array(
            'CDFILIAL' => $CDFILIAL
        );
        $dadosParavend = $this->entityManager->getConnection()->fetchAll("BUSCA_DADOS_PARAVEND", $params);

        $dadosFamiliaFilial = $this->entityManager->getConnection()->fetchAll("BUSCA_FAMILIA_SALDO_FILIAL", $params);

        $resultTabelaPadrao = self::buscaTabelaPadrao($CDFILIAL, $CDCLIENTE, $CDLOJA, array());
        if ($resultTabelaPadrao['error']) throw new \Exception($resultTabelaPadrao['message']);
        $precosIndexadosPorProduto = $resultTabelaPadrao['precosIndexadosPorProduto'];
        $horarioDePrecos = $resultTabelaPadrao['horarioDePrecos'];

        $observacoes = self::trataObservacoes($CDFILIAL, $CDLOJA, $precosIndexadosPorProduto);
        $ambientes = self::montaAmbientes($CDFILIAL, $NRCONFTELA['CDFILIAL'], $NRCONFTELA['NRCONFTELA'], $NRCONFTELA['DTINIVIGENCIA'], $NRMESAPADRAO);

        $recebimentos = self::montaRecebimentos($dadosCaixa);
        $grupoRecebimentos = self::montaGrupoRecebimentos($dadosCaixa, $recebimentos);
        $observacoesIndexadasPorProduto = self::montaObservacoes($CDFILIAL, $CDLOJA);

        $cardapio = self::montaCardapio($CDFILIAL, $NRCONFTELA['CDFILIAL'], $NRCONFTELA['NRCONFTELA'], $NRCONFTELA['DTINIVIGENCIA'], $CDLOJA, $CDCLIENTE, $precosIndexadosPorProduto, $observacoesIndexadasPorProduto, null, $dadosFilial[0]['NRORG']);
        $smartPromoProducts = self::montaPromocoes($CDFILIAL, $NRCONFTELA['CDFILIAL'], $NRCONFTELA['NRCONFTELA'], $NRCONFTELA['DTINIVIGENCIA'], $CDLOJA, $precosIndexadosPorProduto, $observacoesIndexadasPorProduto);


        if ($cardapio['error'] == false){
            $cardapio = $cardapio['cardapio'];

            $parametros = array(
                'CDOPERADOR'     => $dadosOperador[0]['CDOPERADOR'],
                'NMOPERADOR'     => $dadosOperador[0]['NMOPERADOR'],
                'CDVENDEDOR'     => $dadosVendedor[0]['CDVENDEDOR'],
                'NMFANVEN'       => $dadosVendedor[0]['NMFANVEN'],
                'NRATRAPADRAO'   => $dadosParavend[0]['NRATRAPADRAO'],
                'CDCAIXA'        => $dadosCaixa['CDCAIXA'],
                'CDLOJA'         => $dadosCaixa['CDLOJA'],
                'IDTPEMISSAOFOS' => $dadosCaixa['IDTPEMISSAOFOS'],
                'IDTPTEF'        => $dadosCaixa['IDTPTEF'],
                'CDTERTEF'        => $dadosCaixa['CDTERTEF'],
                'IDHABCAIXAVENDA'=> $dadosCaixa['IDHABCAIXAVENDA'],
                'FILIALVIGENCIA' => $NRCONFTELA['CDFILIAL'],
                'NRCONFTELA'     => $NRCONFTELA['NRCONFTELA'],
                'DTINIVIGENCIA'  => $NRCONFTELA['DTINIVIGENCIA'],
                'CDFILIAL'       => $dadosFilial[0]['CDFILIAL'],
                'NMFILIAL'       => $dadosFilial[0]['NMFILIAL'],
                'NRINSJURFILI'   => $dadosFilial[0]['NRINSJURFILI'],
                'NRORG'          => $dadosFilial[0]['NRORG'],
                'NRMESAPADRAO'   => $NRMESAPADRAO
            );

            $dados = array(
                'parametros'          => $parametros,
                'cardapio'            => $cardapio,
                'observacoes'         => $observacoes,
                'ambientes'           => $ambientes,
                'recebimentos'        => $recebimentos,
                'grupoRecebimentos'   => $grupoRecebimentos,
                'familiaFilial'       => $dadosFamiliaFilial,
                'horarioDePrecos'     => $horarioDePrecos,
                'smartPromoProducts'  => $smartPromoProducts
            );

            $result = array(
                'error' => false,
                'dados' => $dados
            );
        }
        else $result = $cardapio;

        return $result;
    }

    public function getFileServer(){
        $fileServerRes = $this->entityManager->getConnection()->fetchAssoc("BUSCA_FILESERVER");
        $this->setFileServer($fileServerRes['FILESERVERURL'] ? $fileServerRes['FILESERVERURL'] : "");
    }

    public function trataObservacoes($CDFILIAL, $CDLOJA, $precosIndexadosPorProduto) {
        date_default_timezone_set('America/Sao_Paulo');
        $NRDIASEMANABLOQ = date("w") + 1;

        $params = array(
            'CDFILIAL' => $CDFILIAL,
            'CDLOJA' => $CDLOJA,
            'NRDIASEMANABLOQ' => strval($NRDIASEMANABLOQ)
        );
        $observacoes = $this->entityManager->getConnection()->fetchAll("BUSCA_OBSERVACOES", $params);

        foreach ($observacoes as &$obs){
            // Busca preços das observações.
            if (!empty($obs['CDPRODUTO'])) {
                if ($obs['IDCONTROLAOBS'] == self::OBS_TIPO_ADICIONAR){
                    if (!empty($precosIndexadosPorProduto[$obs['CDPRODUTO']])){
                        $obs['VRPRECITEM'] = $precosIndexadosPorProduto[$obs['CDPRODUTO']]['VRPRECITEM'];
                        $obs['VRPRECITEMCL'] = $precosIndexadosPorProduto[$obs['CDPRODUTO']]['VRPRECITEMCL'];
                    }
                    else {
                        $obs['VRPRECITEM'] = 0;
                        $obs['VRPRECITEMCL'] = 0;
                    }
                }
            }

            /* CALCULATES COMPLEMENTARY COLORS (by Marcus) */
            if (strlen($obs['NRCORSINAL']) === 7){
                $R = hexdec(substr($obs['NRCORSINAL'], 1, 2));
                $G = hexdec(substr($obs['NRCORSINAL'], 3, 2));
                $B = hexdec(substr($obs['NRCORSINAL'], 5, 2));

                /* Squaring the color values. /*/
                $R *= $R;
                $G *= $G;
                $B *= $B;

                /* Note: 65025 = 255 x 255. */
                $Rc = str_pad(dechex(intval(sqrt(65025 - $R))), 2, '0', STR_PAD_LEFT);
                $Gc = str_pad(dechex(intval(sqrt(65025 - $G))), 2, '0', STR_PAD_LEFT);
                $Bc = str_pad(dechex(intval(sqrt(65025 - $B))), 2, '0', STR_PAD_LEFT);

                $obs['NRCORSINALC'] = '#' . $Rc . $Gc . $Bc;
            } else {
                /* Default to black. */
                $obs['NRCORSINALC'] = '#000000';
            }

            $obs['DSENDEIMGOCORR'] = !empty($obs['DSENDEIMGOCORR']) ? self::URL_SERVIDOR_IMAGEM . $obs['DSENDEIMGOCORR'] : null;
        }

        return $observacoes;
    }

    public function montaSugestaoVenda($CDFILIAL, $NRCONFTELA, $arrayProdutos){
        $params = array(
            'CDFILIAL' => $CDFILIAL,
            'NRCONFTELA' => $NRCONFTELA,
            'ARRAYPRODUTOS' => $arrayProdutos
        );
        $paramsType = array(
            'ARRAYPRODUTOS' => \Doctrine\DBAL\Connection::PARAM_STR_ARRAY
        );

        $itensSugestao = $this->entityManager->getConnection()->fetchAll("BUSCA_ITENS_SUGESTAO", $params, $paramsType);
        return $itensSugestao;
    }

    private function montaAmbientes($CDFILIAL, $FILIALVIGENCIA, $NRCONFTELA, $DTINIVIGENCIA, $NRMESAPADRAO){
        $retornoAmbientes = array();
        $params = array(
            'CDFILIAL' => $FILIALVIGENCIA,
            'NRCONFTELA' => $NRCONFTELA,
            'DTINIVIGENCIA' => $DTINIVIGENCIA
        );
        $types = array(
            'DTINIVIGENCIA' => \Doctrine\DBAL\Types\Type::DATETIME
        );
        $ambientes = $this->entityManager->getConnection()->fetchAll("BUSCA_AMBIENTES", $params, $types);

        if (!empty($ambientes)){
            $params = array(
                'CDFILIAL' => $FILIALVIGENCIA,
                'NRCONFTELA' => $NRCONFTELA,
                'DTINIVIGENCIA' => $DTINIVIGENCIA,
                'NRMESAPADRAO' => (isset($NRMESAPADRAO) && $NRMESAPADRAO != '') ? $NRMESAPADRAO : 'XXXX'
            );
            $types = array(
                'DTINIVIGENCIA' => \Doctrine\DBAL\Types\Type::DATETIME
            );
            $mesas = $this->entityManager->getConnection()->fetchAll("BUSCA_MESAS", $params, $types);

            if (!empty($mesas)){
                foreach ($ambientes as $ambienteAtual){
                    $retornoAmbientes[$ambienteAtual['CDIDENTBUTON']] = array(
                        'area' => array(
                            'CDSALA' => $ambienteAtual['CDIDENTBUTON'],
                            'NMSALA' => $ambienteAtual['DSBUTTON'],
                            'NRBUTTON' => $ambienteAtual['NRBUTTON']
                        ),
                        'mesas' => array()
                    );

                    foreach ($mesas as $mesaAtual){
                        if ($mesaAtual['NRPGCONFTAUX'] == $ambienteAtual['NRPGCONFTELA'] && $mesaAtual['NRBUTTONAUX'] == $ambienteAtual['NRBUTTON']){
                            $retornoAmbientes[$ambienteAtual['CDIDENTBUTON']]['mesas'][$mesaAtual['CDIDENTBUTON']] = array(
                                'NRMESA' => $mesaAtual['CDIDENTBUTON'],
                                'NMMESA' => $mesaAtual['DSBUTTON']
                            );
                        }
                    }
                }
            }
        }

        return $retornoAmbientes;
    }

    public function montaPromocoes($CDFILIAL, $FILIALVIGENCIA, $NRCONFTELA, $DTINIVIGENCIA, $CDLOJA, $precosIndexadosPorProduto, $observacoesIndexadasPorProduto){
        $smartPromoProducts = array();

        // Busca todos os produtos que possuem promoções cadastradas para eles.
        // Busca os produtos da tabela padrão primeiro.
        $produtosPai = $this->entityManager->getConnection()->fetchAll("SMART_PROMO_PRODUCTS", array());

        // Busca os produtos da tabela filial, e sobreescreve os da padrão caso necessário, pois eles têm prioridade.
        $params = array(
            'CDFILIAL' => $CDFILIAL
        );
        $produtosPaiFil = $this->entityManager->getConnection()->fetchAll("SMART_PROMO_PRODUCTS_FILI", $params);

        $produtosPai = array_merge($produtosPai, $produtosPaiFil);

        // Processa promoções por produto pai.
        foreach ($produtosPai as $produtoAtual){
            $gruposPromocao = array();
            $params = array(
                'CDIDENTBUTON' => $produtoAtual['CDPRODPROMOCAO'],
                'CDFILIAL' => $CDFILIAL
            );
            $composicaoPadrao = $this->entityManager->getConnection()->fetchAll("GRUPOS_PROMO_INT_FILI", $params);

            if (count($composicaoPadrao) > 0){
                $usaComposicaoPadrao = false;
                $gruposComposicao = $composicaoPadrao;
            }
            else {
                $usaComposicaoPadrao = true;
                $params = array(
                    'CDIDENTBUTON' => $produtoAtual['CDPRODPROMOCAO']
                );
                $gruposComposicao = $this->entityManager->getConnection()->fetchAll("GRUPOS_PROMO_INT", $params);
            }

            foreach ($gruposComposicao as $smartGroup){
                $gruposPromocao[$smartGroup['CDGRUPROMOC']] = array(
                    'grupo' => array(),
                    'produtos' => array(),
                    'CDPRODPROMOCAO' => $produtoAtual['CDPRODPROMOCAO']
                );

                $gruposPromocao[$smartGroup['CDGRUPROMOC']]['grupo'] = array(
                    'CDGRUPROMOC' => $smartGroup['CDGRUPROMOC'],
                    'NMGRUPROMOC' => $smartGroup['NMGRUPROMOC'],
                    'QTPRGRUPPROMOC' => !empty($smartGroup['QTPRGRUPPROMOC']) ? intval($smartGroup['QTPRGRUPPROMOC']) : 0,
                    'QTPRGRUPROMIN' => !empty($smartGroup['QTPRGRUPROMIN']) ? intval($smartGroup['QTPRGRUPROMIN']) : 0,
                    'CDGRUPMUTEX' => $smartGroup['CDGRUPMUTEX'],
                    'DSENDEIMGGRUPROMOC' => !empty($smartGroup['DSENDEIMGGRUPROMOC']) ? self::URL_SERVIDOR_IMAGEM . $smartGroup['DSENDEIMGGRUPROMOC'] : null,
                    'NRORDPROMOGRUP' => $smartGroup['NRORDPROMOGRUP'],
                    'IDIMPGRPROMO' => $smartGroup['IDIMPGRPROMO']
                );

                date_default_timezone_set('America/Sao_Paulo');
                $NRDIASEMANABLOQ = date("w") + 1;

                if ($usaComposicaoPadrao){
                    $params = array(
                        'CDIDENTBUTON' => $produtoAtual['CDPRODPROMOCAO'],
                        'CDGRUPROMOC' => $smartGroup['CDGRUPROMOC'],
                        'CDFILIAL' => $CDFILIAL,
                        'CDLOJA' => $CDLOJA,
                        'NRDIASEMANABLOQ' => strval($NRDIASEMANABLOQ)
                    );
                    $produtosComposicao = $this->entityManager->getConnection()->fetchAll("PRODUTOS_PROMO_INT", $params);
                }
                else {
                    $params = array(
                        'CDIDENTBUTON' => $produtoAtual['CDPRODPROMOCAO'],
                        'CDGRUPROMOC'  => $smartGroup['CDGRUPROMOC'],
                        'CDFILIAL' => $CDFILIAL,
                        'CDLOJA' => $CDLOJA,
                        'NRDIASEMANABLOQ' => strval($NRDIASEMANABLOQ)
                    );
                    $produtosComposicao = $this->entityManager->getConnection()->fetchAll("PRODUTOS_PROMO_INT_FILI", $params);

                }
                $doPrecoPromoc = '';
                foreach ($produtosComposicao as $produtoComposicaoAtual) {
                    $doPrecoPromoc = 0;
                    $subsidyPromoc = 0;
                    $VRDESITVEND = 0;
                    $VRACRITVEND = 0;
                    $HRINIVENPROD = 0;
                    $HRFIMVENPROD = 0;
                    if (!empty($precosIndexadosPorProduto[$produtoAtual['CDPRODPROMOCAO']]['VRPRECITEM'])) {
                        if (!empty($precosIndexadosPorProduto[$produtoComposicaoAtual['CDPRODUTO']]['VRPRECITEM'])) {
                            $doPrecoPromoc = floatval($precosIndexadosPorProduto[$produtoComposicaoAtual['CDPRODUTO']]['VRPRECITEM']);
                            $subsidyPromoc = floatval($precosIndexadosPorProduto[$produtoComposicaoAtual['CDPRODUTO']]['VRPRECITEMCL']);
                            $VRDESITVEND = floatval($precosIndexadosPorProduto[$produtoComposicaoAtual['CDPRODUTO']]['VRDESITVEND']);
                            $VRACRITVEND = floatval($precosIndexadosPorProduto[$produtoComposicaoAtual['CDPRODUTO']]['VRACRITVEND']);
                            $HRINIVENPROD = floatval($precosIndexadosPorProduto[$produtoComposicaoAtual['CDPRODUTO']]['HRINIVENPROD']);
                            $HRFIMVENPROD = floatval($precosIndexadosPorProduto[$produtoComposicaoAtual['CDPRODUTO']]['HRFIMVENPROD']);
                        }
                    }

                    $params = array(
                        'CDFILIAL' => $CDFILIAL,
                        'CDLOJA' => $CDLOJA,
                        'CDPRODUTO' => $produtoComposicaoAtual['CDPRODUTO'],
                        'NRCONFTELA' => $NRCONFTELA,
                        'DTINIVIGENCIA' => $DTINIVIGENCIA
                    );
                    $types = array(
                        'DTINIVIGENCIA' => \Doctrine\DBAL\Types\Type::DATETIME
                    );
                    $promoPrinter = $this->entityManager->getConnection()->fetchAll("GET_IMP_PRODUTOS", $params, $types);

                    if (($produtoComposicaoAtual['VRALIQCOFINS'] != null || $doPrecoPromoc != 0) || $produtoComposicaoAtual['IDIMPPRODUTO'] == '2'){
                        $gruposPromocao[$smartGroup['CDGRUPROMOC']]['produtos'][$produtoComposicaoAtual['CDPRODUTO']] = array(
                            $produtoComposicaoAtual['CDPRODUTO'],
                            $produtoComposicaoAtual['IDIMPPRODUTO'],
                            $produtoComposicaoAtual['IDAPLICADESCPR'],
                            $produtoComposicaoAtual['IDPERVALORDES'],
                            empty($produtoComposicaoAtual['DSAPELIDOMOB']) ? $produtoComposicaoAtual['NMPRODUTO'] : $produtoComposicaoAtual['DSAPELIDOMOB'],
                            $produtoComposicaoAtual['VRDESPRODPROMOC'],
                            $produtoComposicaoAtual['IDDESCACRPROMO'],
                            $doPrecoPromoc,
                            array_key_exists($produtoComposicaoAtual['CDPRODUTO'], $observacoesIndexadasPorProduto) ? $observacoesIndexadasPorProduto[$produtoComposicaoAtual['CDPRODUTO']] : [],
                            empty($produtoComposicaoAtual['IDPRODBLOQ']) ? 'N' : $produtoComposicaoAtual['IDPRODBLOQ'],
                            $promoPrinter,
                            $produtoComposicaoAtual['VRALIQCOFINS'],
                            $produtoComposicaoAtual['VRALIQPIS'],
                            $produtoComposicaoAtual['VRPEALIMPFIS'],
                            $produtoComposicaoAtual['CDIMPOSTO'],
                            $produtoComposicaoAtual['CDCSTICMS'],
                            $produtoComposicaoAtual['CDCSTPISCOF'],
                            $produtoComposicaoAtual['CDCFOPPFIS'],
                            $produtoComposicaoAtual['DSPRODVENDA'],
                            $produtoComposicaoAtual['DSADICPROD'],
                            !empty($produtoComposicaoAtual['DSENDEIMGPROMO']) ? (!empty($produtoComposicaoAtual['FILESERVERURL']) ? $produtoComposicaoAtual['FILESERVERURL'] . $produtoComposicaoAtual['DSENDEIMGPROMO'] : self::URL_SERVIDOR_IMAGEM . $produtoComposicaoAtual['DSENDEIMGPROMO']) : null,
                            $produtoComposicaoAtual['NRORDPROMOPR'] ? $produtoComposicaoAtual['NRORDPROMOPR'] : 99,
                            $produtoComposicaoAtual['IDPRODPRESELEC'],
                            $produtoComposicaoAtual['IDOBRPRODSELEC'],
                            $produtoComposicaoAtual['NRQTDMINOBS'],
                            $produtoComposicaoAtual['CDPROTECLADO'],
                            $produtoComposicaoAtual['IDTIPOCOMPPROD'],
                            $HRINIVENPROD,
                            $HRFIMVENPROD,
                            $produtoComposicaoAtual['CDCLASFISC'],
                            $produtoComposicaoAtual['REFIL'],
                            $produtoComposicaoAtual['CDPRODPROMOCAO'],
                            $subsidyPromoc,
                            $VRDESITVEND,
                            $VRACRITVEND,
                            $produtoComposicaoAtual['IDPESAPROD']
                        );
                    }
                }
            }

            $smartPromoProducts[$produtoAtual['CDPRODPROMOCAO']] = $gruposPromocao;
        }

        return $smartPromoProducts;
    }

    public function montaObservacoes($CDFILIAL, $CDLOJA){
        $NRDIASEMANABLOQ = date("w") + 1;

        $params = array(
            'CDFILIAL' => $CDFILIAL,
            'CDLOJA' => $CDLOJA,
            'NRDIASEMANABLOQ' => $NRDIASEMANABLOQ
        );
        $observacoesProduto = $this->entityManager->getConnection()->fetchAll("BUSCA_OBSERVACOES_PRODUTO", $params);

        $observacoesIndexadasPorProduto = array();
        foreach ($observacoesProduto as $obsAtual) {
            if (!(array_key_exists($obsAtual['CDPRODUTO'], $observacoesIndexadasPorProduto))) {
                $observacoesIndexadasPorProduto[$obsAtual['CDPRODUTO']] = array();
            }
            array_push($observacoesIndexadasPorProduto[$obsAtual['CDPRODUTO']], $obsAtual['CDOCORR']);
        }

        return $observacoesIndexadasPorProduto;
    }

    public function montaCardapio($CDFILIAL, $FILIALVIGENCIA, $NRCONFTELA, $DTINIVIGENCIA, $CDLOJA, $CDCLIENTE, $precosIndexadosPorProduto, $observacoesIndexadasPorProduto, $CDCONSUMIDOR = null, $NRORG){

        $params = array(
            'CDFILIAL' => $FILIALVIGENCIA,
            'CDLOJA' => $CDLOJA,
            'NRCONFTELA' => $NRCONFTELA,
            'DTINIVIGENCIA' => $DTINIVIGENCIA
        );
        $types = array(
            'DTINIVIGENCIA' => \Doctrine\DBAL\Types\Type::DATETIME
        );
        $grupoProdutos = $this->entityManager->getConnection()->fetchAll("BUSCA_GRUPO_PRODUTOS", $params, $types);

        if (!empty($grupoProdutos)){
            $subGrupoProdutos = $this->entityManager->getConnection()->fetchAll("BUSCA_SUBGRUPO_PRODUTOS", $params, $types);

            date_default_timezone_set('America/Sao_Paulo');
            $NRDIASEMANABLOQ = date("w") + 1;

            $params = array(
                'CDFILIAL'        => $CDFILIAL,
                'CDLOJA'          => $CDLOJA,
                'FILIALVIGENCIA'  => $FILIALVIGENCIA,
                'NRCONFTELA'      => $NRCONFTELA,
                'DTINIVIGENCIA'   => $DTINIVIGENCIA,
                'NRDIASEMANABLOQ' => strval($NRDIASEMANABLOQ)
            );
            $types = array(
                'DTINIVIGENCIA' => \Doctrine\DBAL\Types\Type::DATETIME
            );
            $produtos = $this->entityManager->getConnection()->fetchAll("BUSCA_PRODUTOS_PARAM", $params, $types);

            // Define se produtos sem preço serão exibidos no cardápio.
            $CDESCPRODSPREC['CDESCPRODSPREC'] = 'N'; // GR asked for this field, but Daniel said not to do it. Damn, Daniel. :(
            if ($CDESCPRODSPREC['CDESCPRODSPREC'] == 'S') $CDESCPRODSPREC = true;
            else $CDESCPRODSPREC = false;

            $arrayProdutos = self::montaArrayProdutos($grupoProdutos, $subGrupoProdutos, $produtos,
                            $precosIndexadosPorProduto, $observacoesIndexadasPorProduto, $CDLOJA, $CDFILIAL,
                            $FILIALVIGENCIA, $NRCONFTELA, $DTINIVIGENCIA, $CDCLIENTE, $CDCONSUMIDOR, $CDESCPRODSPREC, $NRORG);

            if (!empty($arrayProdutos["error"])){
                $result = $arrayProdutos;
            }
            else {
                $result = array(
                    'error' => false,
                    'cardapio' => $arrayProdutos
                );
            }
        }
        else {
            $result = array(
                'error' => true,
                'message' => 'Não há grupo de produtos na configuração de tela do caixa.'
            );
        }

        return $result;
    }

    public function buscaTabelaPadrao($CDFILIAL, $CDCLIENTE, $CDLOJA, $precos){
        $tabelaPadrao = $this->precoService->buscaTabelaVigente($CDFILIAL, $CDLOJA, $CDCLIENTE);
        if ($tabelaPadrao['error'] == false){
            $params = array(
                'CDFILIAL'    => $tabelaPadrao['CDFILIAL'],
                'CDTABEPREC'  => $tabelaPadrao['CDTABEPREC'],
                'DTINIVGPREC' => $tabelaPadrao['DTINIVGPREC']
            );

            if (empty($precos)){
                $precos = $this->entityManager->getConnection()->fetchAll("SQL_PRECOS", $params);
            }

            $precosDia = self::buscaPrecosDia($tabelaPadrao['CDFILIAL'], $tabelaPadrao['CDTABEPREC'], $tabelaPadrao['DTINIVGPREC']);

            if (!empty($precos)) {
                $precosIndexadosPorProduto = array();
                foreach ($precos as $produto) {
                    $precosIndexadosPorProduto[$produto['CDPRODUTO']] = $produto;
                }

                if (!empty($precosDia)) {
                    foreach ($precosDia as $produtoDia) {
                        $codProdutoDia = $produtoDia['CDPRODUTO'];
                        if (isset($precosIndexadosPorProduto[$codProdutoDia]) && $precosIndexadosPorProduto[$codProdutoDia]['VRPRECITEM'] !== null) {
                            if ($produtoDia['IDVISUACUPOM'] == 'N'){
                                $precosIndexadosPorProduto[$codProdutoDia]['VRDESITVEND'] = 0;
                                $precosIndexadosPorProduto[$codProdutoDia]['VRACRITVEND'] = 0;
                                if ($produtoDia['IDPERVALORPR'] == 'V') {
                                    if ($produtoDia['IDDESCACREPR'] == 'D') {
                                        //Desconto por valor
                                        $precoTotal = $precosIndexadosPorProduto[$codProdutoDia]['VRPRECITEM'] + $precosIndexadosPorProduto[$codProdutoDia]['VRPRECITEMCL'];
                                        if ($precoTotal > 0){
                                            $descontoPreco = ($precosIndexadosPorProduto[$codProdutoDia]['VRPRECITEM'] / $precoTotal) * $produtoDia['VRPRECODIA'];
                                            $descontoSubsidio = ($precosIndexadosPorProduto[$codProdutoDia]['VRPRECITEMCL'] / $precoTotal) * $produtoDia['VRPRECODIA'];
                                        }
                                        else {
                                            $descontoPreco = 0;
                                            $descontoSubsidio = 0;
                                        }

                                        $precoFinal = floatval(bcsub(str_replace(',','.',strval($precosIndexadosPorProduto[$codProdutoDia]['VRPRECITEM'])), str_replace(',','.',strval($descontoPreco)), '2'));
                                        $subsidioFinal = floatval(bcsub(str_replace(',','.',strval($precosIndexadosPorProduto[$codProdutoDia]['VRPRECITEMCL'])), str_replace(',','.',strval($descontoSubsidio)), '2'));
                                        if (($precoFinal + $subsidioFinal) < ($precoTotal - $produtoDia['VRPRECODIA'])){
                                            $precoFinal += 0.01;
                                        }
                                    } else {
                                        //Acrescimo por valor
                                        $precoFinal = $precosIndexadosPorProduto[$codProdutoDia]['VRPRECITEM'] + $produtoDia['VRPRECODIA'];
                                        $subsidioFinal = $precosIndexadosPorProduto[$codProdutoDia]['VRPRECITEMCL'] + $produtoDia['VRPRECODIA'];
                                    }
                                } else {
                                    if ($produtoDia['IDDESCACREPR'] == 'D') {
                                        //Desconto por porcentagem
                                        $precoFinal = $precosIndexadosPorProduto[$codProdutoDia]['VRPRECITEM'] * (1 - $produtoDia['VRPRECODIA']/100);
                                        $subsidioFinal = $precosIndexadosPorProduto[$codProdutoDia]['VRPRECITEMCL'] * (1 - $produtoDia['VRPRECODIA']/100);
                                    } else {
                                        //Acrescimo por porcentagem
                                        $precoFinal = $precosIndexadosPorProduto[$codProdutoDia]['VRPRECITEM'] * (1 + ($produtoDia['VRPRECODIA']/100));
                                        $subsidioFinal = $precosIndexadosPorProduto[$codProdutoDia]['VRPRECITEMCL'] * (1 + $produtoDia['VRPRECODIA']/100);
                                    }
                                }

                                if ($precoFinal > 0) {
                                    $precosIndexadosPorProduto[$codProdutoDia]['VRPRECITEM'] = $precoFinal;
                                }

                                if ($subsidioFinal < 0) $subsidioFinal = 0;
                                $precosIndexadosPorProduto[$codProdutoDia]['VRPRECITEMCL'] = $subsidioFinal;
                            }
                            else {
                                // Define descontos e acréscimos quando forem ser exibidos no cupom.
                                if ($produtoDia['IDPERVALORPR'] == 'V'){
                                    if ($produtoDia['IDDESCACREPR'] == 'D'){
                                        $precosIndexadosPorProduto[$codProdutoDia]['VRDESITVEND'] = $produtoDia['VRPRECODIA'];
                                        $precosIndexadosPorProduto[$codProdutoDia]['VRACRITVEND'] = 0;
                                    }
                                    else {
                                        $precosIndexadosPorProduto[$codProdutoDia]['VRDESITVEND'] = 0;
                                        $precosIndexadosPorProduto[$codProdutoDia]['VRACRITVEND'] = $produtoDia['VRPRECODIA'];
                                    }
                                }
                                else {
                                    if ($produtoDia['IDDESCACREPR'] == 'D'){
                                        $precosIndexadosPorProduto[$codProdutoDia]['VRDESITVEND'] = floatval(bcmul(str_replace(',','.',strval($precosIndexadosPorProduto[$codProdutoDia]['VRPRECITEM']+$precosIndexadosPorProduto[$codProdutoDia]['VRPRECITEMCL'])), str_replace(',','.',strval($produtoDia['VRPRECODIA'])))) / 100;
                                        $precosIndexadosPorProduto[$codProdutoDia]['VRACRITVEND'] = 0;
                                    }
                                    else {
                                        $precosIndexadosPorProduto[$codProdutoDia]['VRDESITVEND'] = 0;
                                        $precosIndexadosPorProduto[$codProdutoDia]['VRACRITVEND'] = floatval(bcmul(str_replace(',','.',strval($precosIndexadosPorProduto[$codProdutoDia]['VRPRECITEM']+$precosIndexadosPorProduto[$codProdutoDia]['VRPRECITEMCL'])), str_replace(',','.',strval($produtoDia['VRPRECODIA'])))) / 100;
                                    }
                                }
                            }
                        }
                    }
                }

                $horarioDePrecos = self::montaHorarioDePrecos($tabelaPadrao['CDFILIAL'], $tabelaPadrao['CDTABEPREC'], $tabelaPadrao['DTINIVGPREC']);

                $result = array(
                    'error' => false,
                    'precosIndexadosPorProduto' => $precosIndexadosPorProduto,
                    'horarioDePrecos' => $horarioDePrecos
                );
            } else {
                $result = array(
                    'error' => true,
                    'message' => 'Não há produtos para a tabela de preços vigente.'
                );
            }
        } else {
            $result = $tabelaPadrao;
        }

        return $result;
    }

    private function buscaPrecosDia($CDFILIAL, $CDTABEPREC, $DTINIVGPREC) {
        $dataAtual   = $this->date->getDataAtual();
        $hora        = $dataAtual->format("H:i:s");
        $hora        = str_replace(":", "", $hora);
        $hora        = substr($hora, 0, 4);

        // Expected: 1 (for Sunday) through 7 (for Saturday)
        // PHP returns: 1 (for Monday) through 7 (for Sunday)
        // So, this method changes the index for each day, changing from the forat of return
        // used by PHP to Delphi format.
        $dia = (((int)$dataAtual->format("N")) % 7) + 1;

        // @toDo: Fix consumer type for different prices
        $params = array(
            'CDFILIAL'    => $CDFILIAL,
            'CDTABEPREC'  => $CDTABEPREC,
            'DTINIVGPREC' => $DTINIVGPREC,
            'HORA'        => $hora,
            'NRDIASEMANPR' => (string) $dia,
            'CDTIPOCONSPD' => 'T'
        );

        $precosDia = $this->entityManager->getConnection()->fetchAll("SQL_PRECOS_DIA", $params);

        return $precosDia;
    }

    private function validaTabela($CDFILIAL, $CDTABEPREC){
        $params = array(
            'CDFILIAL' => $CDFILIAL,
            'CDTABEPREC' => $CDTABEPREC,
            'DATE' => date("d/m/Y")
        );
        $vigenciaTabela = $this->entityManager->getConnection()->fetchAll("VAL_TABE", $params);
        return !empty($vigenciaTabela);
    }

    private function montaHorarioDePrecos($CDFILIAL, $CDTABEPREC, $DTINIVGPREC){

        $data = new \DateTime();

        $params = array(
            "CDFILIAL" => $CDFILIAL,
            "DTFERIFILI" => $data
        );
        $type = array(
            'DTFERIFILI' => \Doctrine\DBAL\Types\Type::DATETIME
        );
        $feriado = $this->entityManager->getConnection()->fetchAssoc("HOLIDAY_CHECK", $params, $type);
        if (!empty($feriado)){
            $dia = "F";
        }
        else {
            $params['DTFERIFILI'] = $data->modify('+1 day');
            $vespera = $this->entityManager->getConnection()->fetchAssoc("HOLIDAY_CHECK", $params, $type);
            $data->modify('-1 day');
            if (!empty($vespera)){
                $dia = "V";
            }
            else {
                $dia = $data->format("N") % 7 + 1;
            }
        }

        $DTINIVGPREC = $this->databaseUtil->databaseIsOracle() ?
            \DateTime::createFromFormat('d-m-Y H:i:s', $DTINIVGPREC) :
            \DateTime::createFromFormat('Y-m-d H:i:s', $DTINIVGPREC);
        $params = array(
            'CDFILIAL'     => $CDFILIAL,
            'CDTABEPREC'   => $CDTABEPREC,
            'NRDIASEMANPR' => (string) $dia,
            'DTINIVGPREC'  => $DTINIVGPREC
        );
        $type = array(
            'DTINIVGPREC' => \Doctrine\DBAL\Types\Type::DATETIME
        );
        $tabelaDeHorarios = $this->entityManager->getConnection()->fetchAll("BUSCA_HORARIO_PRECOS", $params, $type);

        $horarioDePrecos = array();
        foreach ($tabelaDeHorarios as $tipoDia){
            $iniStamp = self::getTimestamp($tipoDia['HRINIPRECDIA']);
            $finStamp = self::getTimestamp($tipoDia['HRFINPRECDIA']);
            if (array_search($iniStamp, $horarioDePrecos) === false){
                array_push($horarioDePrecos, $iniStamp);
            }
            if (array_search($finStamp, $horarioDePrecos) === false){
                array_push($horarioDePrecos, $finStamp);
            }
        }

        usort($horarioDePrecos, function ($a, $b){
            if ($a == $b) return 0;
            return $a < $b ? -1 : 1;
        });

        return $horarioDePrecos;
    }

    private function getTimestamp($time){
        $time = substr($time, 0, 2).":".substr($time, 2, 2);
        $date = date_create_from_format('H:i', $time);
        return strtotime($date->format('Y-m-d H:i'));
    }

    private function montaArrayProdutos($grupoProdutos, $subGrupoProdutos, $produtos, $precosIndexadosPorProduto,
        $observacoesIndexadasPorProduto, $CDLOJA, $CDFILIAL, $FILIALVIGENCIA, $NRCONFTELA, $DTINIVIGENCIA, $CDCLIENTE, $CDCONSUMIDOR, $CDESCPRODSPREC, $NRORG){

        $arrayRetorno = array();

        // Processa os grupos.
        foreach ($grupoProdutos as $grupoAtual){
            $codGrupo = $grupoAtual['NRPGCONFTELA'] . $grupoAtual['NRBUTTON'];

            $arrayRetorno[$codGrupo] = array(
                'grupo' => array(
                    'CODIGO' => $codGrupo,
                    'DESC' => $grupoAtual['DSBUTTON'],
                    'COLOR' => $grupoAtual['NRCOLORBACK'],
                    'DESCING' => $grupoAtual['DSBUTTONINGLES'],
                    'DESCESP' => $grupoAtual['DSBUTTONESPANH'],
                    'DSENDEIMG' => !empty($grupoAtual['DSENDEIMG']) ? $grupoAtual['FILESERVERURL'] . $grupoAtual['DSENDEIMG'] : null,
                    'NRBUTTON' => $grupoAtual['NRBUTTON']
                ),
                'produtos' => array()
            );
        }

        // Processa os subgrupos.
        foreach ($subGrupoProdutos as $subGrupoAtual){
            $codGrupo = $subGrupoAtual['NRPGCONFTAUX'] . $subGrupoAtual['NRBUTTONAUX'];
            $codSubGrupo = $subGrupoAtual['NRPGCONFTELA'] . $subGrupoAtual['NRBUTTON'];

            $arrayRetorno[$codGrupo]['subgrupos'][$codSubGrupo] = array(
                'subgrupo' => array(
                    'CODIGO' => $codSubGrupo,
                    'DESC' => $subGrupoAtual['DSBUTTON'],
                    'COLOR' => $subGrupoAtual['NRCOLORBACK'],
                    'DSENDEIMG' => !empty($subGrupoAtual['DSENDEIMG']) ? $subGrupoAtual['FILESERVERURL'] . $subGrupoAtual['DSENDEIMG'] : null,
                    'DESCING' => $subGrupoAtual['DSBUTTONINGLES'],
                    'DESCESP' => $subGrupoAtual['DSBUTTONESPANH'],
                ),
                'produtos' => array()
            );
        }

        // Busca campanhas Compre e Ganhe.
        $produtosCampanha = self::buscaCampanhaCompreGanhe($CDFILIAL);

        // Processa os produtos.
        foreach ($produtos as $key => $produtoAtual){
            // Define preço do produto.
            if (array_key_exists($produtoAtual['CDPRODUTO'], $precosIndexadosPorProduto)){
                $doPreco = floatval($precosIndexadosPorProduto[$produtoAtual['CDPRODUTO']]['VRPRECITEM']);
                $subsidy = floatval($precosIndexadosPorProduto[$produtoAtual['CDPRODUTO']]['VRPRECITEMCL']);
                $HRINIVENPROD = floatval($precosIndexadosPorProduto[$produtoAtual['CDPRODUTO']]['HRINIVENPROD']);
                $HRFIMVENPROD = floatval($precosIndexadosPorProduto[$produtoAtual['CDPRODUTO']]['HRFIMVENPROD']);
                $VRDESITVEND = floatval($precosIndexadosPorProduto[$produtoAtual['CDPRODUTO']]['VRDESITVEND']);
                $VRACRITVEND = floatval($precosIndexadosPorProduto[$produtoAtual['CDPRODUTO']]['VRACRITVEND']);
            }
            else {
                $preco =  $this->precoService->buscaPreco($CDFILIAL, $CDCLIENTE, $produtoAtual['CDPRODUTO'], $CDLOJA, $CDCONSUMIDOR);
                if ($preco['error'] && !isset($preco['PRECO'])){
                    throw new \Exception($preco["message"], 1);
                }
                $doPreco = $preco['PRECO'];
                $subsidy = $preco['PRECOCLIE'];
                $HRINIVENPROD = $preco['HRINIVENPROD'];
                $HRFIMVENPROD = $preco['HRFIMVENPROD'];
                $VRDESITVEND = $preco['DESC'];
                $VRDESITVEND = $preco['ACRE'];
            }

            // Define as impressoras do produto.
            $params = array(
                'CDFILIAL' => $produtoAtual['CDFILIAL'],
                'CDLOJA' => $CDLOJA,
                'CDPRODUTO' => $produtoAtual['CDPRODUTO'],
                'NRCONFTELA' => $produtoAtual['NRCONFTELA'],
                'DTINIVIGENCIA' => new \DateTime($produtoAtual['DTINIVIGENCIA'])
            );
            $types = array(
                'DTINIVIGENCIA' => \Doctrine\DBAL\Types\Type::DATETIME
            );
            $impressorasProduto = $this->entityManager->getConnection()->fetchAll("GET_IMP_PRODUTOS", $params, $types);

            // Formatação final.
            $HRINIVENPROD = 0;
            $HRFIMVENPROD = 0;

            if (isset($precosIndexadosPorProduto[$produtoAtual['CDPRODUTO']])) {
                $HRINIVENPROD = floatval($precosIndexadosPorProduto[$produtoAtual['CDPRODUTO']]['HRINIVENPROD']);
                $HRFIMVENPROD = floatval($precosIndexadosPorProduto[$produtoAtual['CDPRODUTO']]['HRFIMVENPROD']);
            }
            try {
                $produtoConstruido = array(
                    'CDARVPROD' => $produtoAtual['CDARVPROD'],
                    'CDBARPRODUTO' => $produtoAtual['CDBARPRODUTO'],
                    'CDPRODINTE' => $produtoAtual['CDPRODINTE'],
                    'CDPRODUTO' => $produtoAtual['CDIDENTBUTON'],
                    'NMPRODUTO' => $produtoAtual['DSBUTTON'],
                    'NMPRODUTOINGLES' => $produtoAtual['DSBUTTONINGLES'],
                    'NMPRODUTOESPANH' => $produtoAtual['DSBUTTONESPANH'],
                    'VRALIQCOFINS' => $produtoAtual['VRALIQCOFINS'],
                    'VRALIQPIS' => $produtoAtual['VRALIQPIS'],
                    'VRPEALIMPFIS' => $produtoAtual['VRPEALIMPFIS'],
                    'CDIMPOSTO' => $produtoAtual['CDIMPOSTO'],
                    'CDCSTICMS' => $produtoAtual['CDCSTICMS'],
                    'CDCSTPISCOF' => $produtoAtual['CDCSTPISCOF'],
                    'CDCFOPPFIS' => $produtoAtual['CDCFOPPFIS'],
                    'DTINIVGPROMOC' => $produtoAtual['DTINIVGPROMOC'],
                    'DTFINVGPROMOC' => $produtoAtual['DTFINVGPROMOC'],
                    'GRUPOS' => array(),
                    'OBSERVACOES' => array_key_exists($produtoAtual['CDIDENTBUTON'], $observacoesIndexadasPorProduto) ? $observacoesIndexadasPorProduto[$produtoAtual['CDIDENTBUTON']] : [],
                    'IMPRESSORAS'  => $impressorasProduto,
                    'IDIMPPRODUTO' => $produtoAtual['IDIMPPRODUTO'],
                    'IDPESAPROD' => $produtoAtual['IDPESAPROD'],
                    'IDTIPOCOMPPROD' => $produtoAtual['IDTIPOCOMPPROD'],
                    'IDTIPOPROD' => $produtoAtual['IDTIPOPROD'],
                    'VRPRECITEM' => $doPreco,
                    'IDPRODBLOQ' => empty($produtoAtual['IDPRODBLOQ']) ? 'N' : $produtoAtual['IDPRODBLOQ'],
                    'NRCOLORBACK' => $produtoAtual['NRCOLORBACK'],
                    'IDCONTROLAREFIL' => $produtoAtual['IDCONTROLAREFIL'],
                    'DSENDEIMG' => !empty($produtoAtual['DSENDEIMG']) ? $produtoAtual['FILESERVERURL'] . $produtoAtual['DSENDEIMG'] : null,
                    'DSPRODVENDA' => $produtoAtual['DSPRODVENDA'],
                    'DSADICPROD' => $produtoAtual['DSADICPROD'],
                    'NMGRUPO' => $produtoAtual['IDTPBUTONAUX'] == self::TIPO_GRUPO ? $arrayRetorno[$produtoAtual['NRPGCONFTAUX'] . $produtoAtual['NRBUTTONAUX']]['grupo']['DESC'] : $arrayRetorno[$produtoAtual['NRPGCONFTAUX2'] . $produtoAtual['NRBUTTONAUX2']]['subgrupos'][$produtoAtual['NRPGCONFTAUX'] . $produtoAtual['NRBUTTONAUX']]['subgrupo']['DESC'],
                    'NMGRUPOINGLES' => $produtoAtual['IDTPBUTONAUX'] == self::TIPO_GRUPO ? $arrayRetorno[$produtoAtual['NRPGCONFTAUX'] . $produtoAtual['NRBUTTONAUX']]['grupo']['DESCING'] : $arrayRetorno[$produtoAtual['NRPGCONFTAUX2'] . $produtoAtual['NRBUTTONAUX2']]['subgrupos'][$produtoAtual['NRPGCONFTAUX'] . $produtoAtual['NRBUTTONAUX']]['subgrupo']['DESCING'] ,
                    'NMGRUPOESPANH' => $produtoAtual['IDTPBUTONAUX'] == self::TIPO_GRUPO ? $arrayRetorno[$produtoAtual['NRPGCONFTAUX'] . $produtoAtual['NRBUTTONAUX']]['grupo']['DESCESP'] : $arrayRetorno[$produtoAtual['NRPGCONFTAUX2'] . $produtoAtual['NRBUTTONAUX2']]['subgrupos'][$produtoAtual['NRPGCONFTAUX'] . $produtoAtual['NRBUTTONAUX']]['subgrupo']['DESCESP'] ,
                    'NRQTDMINOBS' => $produtoAtual['NRQTDMINOBS'],
                    'CDPROTECLADO' => $produtoAtual['CDPROTECLADO'],
                    'HRINIVENPROD' => $HRINIVENPROD,
                    'HRFIMVENPROD' => $HRFIMVENPROD,
                    'CDCLASFISC' => $produtoAtual['CDCLASFISC'],
                    'VRPRECITEMCL' => $subsidy,
                    'VRDESITVEND' => $VRDESITVEND,
                    'VRACRITVEND' => $VRACRITVEND,
                    'CAMPANHA' => false,
                    'DTINIVGCAMPCG' => null,
                    'QTCOMPGANHE' => null
                );
                if (!empty($produtosCampanha)){
                    if (in_array($produtoAtual['CDIDENTBUTON'], $produtosCampanha['products'])){
                        $produtoConstruido['CAMPANHA'] = $produtosCampanha['CDCAMPCOMPGANHE'];
                        $produtoConstruido['DTINIVGCAMPCG'] = $produtosCampanha['DTINIVGCAMPCG'];
                        $produtoConstruido['QTCOMPGANHE'] = intval($produtosCampanha['quantity']);
                    }
                }
            } catch (\Exception $e){
                throw new \Exception("Um ou mais produtos estão configurados para um grupo que não existe. Verifique a parametrização do sistema. Garanta que o grupo exista, ou que os produtos fiquem desvinculados do grupo não-existente.\n\nPÁGINA DO GRUPO: " . $produtoAtual['NRPGCONFTAUX'] . "\nNÚMERO: " . $produtoAtual['NRBUTTONAUX']);
            }
            // Insere o produto no grupo ou subgrupo.
            if (($produtoAtual['VRALIQCOFINS'] != null && $doPreco != 0) || !$CDESCPRODSPREC || $produtoAtual['IDTIPOCOMPPROD'] == '3' || $produtoAtual['IDTIPOCOMPPROD'] == '6'){
                if ($produtoAtual['IDTPBUTONAUX'] == self::TIPO_GRUPO) {
                    $codGrupo = $produtoAtual['NRPGCONFTAUX'] . $produtoAtual['NRBUTTONAUX'];
                    $arrayRetorno[$codGrupo]['produtos'][$produtoAtual['CDIDENTBUTON']] = $produtoConstruido;
                }
                else if ($produtoAtual['IDTPBUTONAUX'] == self::TIPO_SUB_GRUPO) {
                    $codSubGrupo = $produtoAtual['NRPGCONFTAUX'] . $produtoAtual['NRBUTTONAUX'];
                    $codGrupo = $produtoAtual['NRPGCONFTAUX2'] . $produtoAtual['NRBUTTONAUX2'];
                    $arrayRetorno[$codGrupo]['subgrupos'][$codSubGrupo]['produtos'][$produtoAtual['CDIDENTBUTON']] = $produtoConstruido;
                }
            }
        }

        // Busca produtos combinados.
        date_default_timezone_set('America/Sao_Paulo');
        $NRDIASEMANABLOQ = date("w") + 1;

        $params = array(
            'CDFILIAL'        => $FILIALVIGENCIA,
            'CDLOJA'          => $CDLOJA,
            'NRCONFTELA'      => $NRCONFTELA,
            'DTINIVIGENCIA'   => $DTINIVIGENCIA,
            'NRDIASEMANABLOQ' => strval($NRDIASEMANABLOQ)
        );
        $types = array(
            'DTINIVIGENCIA' => \Doctrine\DBAL\Types\Type::DATETIME
        );

        $produtosCombo = $this->entityManager->getConnection()->fetchAll("GET_COMBO_PRODUCTS", $params, $types);

        // Processa os produtos combinados.
        if (sizeof($produtosCombo) > 0){
            $formattedCombos = array();

            $i = 0; // Identificador do produto pai.
            foreach ($produtosCombo as $combo){
                // Busca a composição do produto combinado.
                $params = array(
                    'CDFILIAL' => $CDFILIAL,
                    'CDLOJA' => $CDLOJA,
                    'FILIALVIGENCIA' => $FILIALVIGENCIA,
                    'NRCONFTELA' => $NRCONFTELA,
                    'DTINIVIGENCIA' => $DTINIVIGENCIA,
                    'IDTIPOPROD' => intval($combo['NRBUTTON']),
                    'NRDIASEMANABLOQ' => strval($NRDIASEMANABLOQ)
                );
                $types = array(
                    'DTINIVIGENCIA' => \Doctrine\DBAL\Types\Type::DATETIME
                );

                $comboChildProducts = $this->entityManager->getConnection()->fetchAll("BUSCA_PRODUTOS_COMBO", $params, $types);

                // Define preços e observações.
                foreach ($comboChildProducts as &$childProduct){
                    if (!empty($precosIndexadosPorProduto[$childProduct['CDIDENTBUTON']]['VRPRECITEM'])){
                        $childProduct['VRPRECITEM'] = floatval($precosIndexadosPorProduto[$childProduct['CDIDENTBUTON']]['VRPRECITEM']);
                        $childProduct['VRPRECITEMCL'] = floatval($precosIndexadosPorProduto[$childProduct['CDIDENTBUTON']]['VRPRECITEMCL']);
                        $childProduct['VRACRITVEND'] = floatval($precosIndexadosPorProduto[$childProduct['CDIDENTBUTON']]['VRACRITVEND']);
                        $childProduct['VRDESITVEND'] = floatval($precosIndexadosPorProduto[$childProduct['CDIDENTBUTON']]['VRDESITVEND']);
                        $childProduct['HRINIVENPROD'] = floatval($precosIndexadosPorProduto[$childProduct['CDIDENTBUTON']]['HRINIVENPROD']);
                        $childProduct['HRFIMVENPROD'] = floatval($precosIndexadosPorProduto[$childProduct['CDIDENTBUTON']]['HRFIMVENPROD']);
                    }
                    else {
                        $childProduct['VRPRECITEM'] = 0;
                        $childProduct['VRPRECITEMCL'] = 0;
                        $childProduct['VRACRITVEND'] = 0;
                        $childProduct['VRDESITVEND'] = 0;
                    }
                    $childProduct['OBSERVACOES'] = array_key_exists($childProduct['CDIDENTBUTON'], $observacoesIndexadasPorProduto) ? $observacoesIndexadasPorProduto[$childProduct['CDIDENTBUTON']] : array();
                    $childProduct['IDPRODBLOQ'] = empty($childProduct['IDPRODBLOQ']) ? 'N' : $childProduct['IDPRODBLOQ'];
                }

                // Container necessário do produto combinado.
                $comboCode = str_pad($i++, 5, '0');
                $standardGroup = array(
                    $comboCode => array(
                        'grupo' => array(
                            'CDGRUPROMOC' => $comboCode,
                            'NMGRUPROMOC' => '',
                            'QTPRGRUPPROMOC' => $combo['NRLIMPRODCOM'],
                            'QTPRGRUPROMIN' => ''
                        ),
                        'produtos' => $comboChildProducts // Composição (produtos filhos).
                    )
                );

                // Formatação do produto combinado.
                $preparedCombo = array(
                    'CDARVPROD' => $combo['CDARVPROD'],
                    'CDBARPRODUTO' => $combo['CDBARPRODUTO'],
                    'CDPRODINTE' => $combo['CDPRODINTE'],
                    'CDPRODUTO' => $comboCode,
                    'NMPRODUTO' => $combo['DSBUTTON'],
                    'DTFINVGPROMOC' => $combo['DTFINVGPROMOC'],
                    'DTINIVGPROMOC' => $combo['DTINIVGPROMOC'],
                    'NMPRODUTOINGLES' => $combo['DSBUTTONINGLES'],
                    'NMPRODUTOESPANH' => $combo['DSBUTTONESPANH'],
                    'GRUPOS' => $standardGroup, // Itens do produto combinado.
                    'OBSERVACOES' => array(),
                    'IMPRESSORAS'  => array(),
                    'IDIMPPRODUTO' => '2', // Produto pai nunca será impresso.
                    'IDPESAPROD' => $combo['IDPESAPROD'],
                    'IDTIPOCOMPPROD' => 'C',
                    'IDTIPOPROD' => $combo['IDTIPOPROD'],
                    'IDTIPCOBRA' => $combo['IDTIPCOBRA'],
                    'VRPRECITEM' => 0.01,
                    'VRPRECITEMCL' => 0,
                    'IDPRODBLOQ' => $combo['IDPRODBLOQ'],
                    'NRCOLORBACK' => $combo['NRCOLORBACK'],
                    'IDCONTROLAREFIL' => $combo['IDCONTROLAREFIL'],
                    'DSENDEIMG' => null,
                    'DSPRODVENDA' => $combo['DSPRODVENDA'],
                    'DSADICPROD' => null,
                    'NMGRUPO' => 'COMBOS',
                    'NRQTDMINOBS' => $combo['NRQTDMINOBS'],
                    'CDPROTECLADO' => null,
                    'HRINIVENPROD' => 0,
                    'HRFIMVENPROD' => 0,
                    'CDCLASFISC' => $combo['CDCLASFISC'],
                    'CDCFOPPFIS' => $combo['CDCFOPPFIS'],
                    'CDCSTICMS' => $combo['CDCSTICMS'],
                    'CDCSTPISCOF' => $combo['CDCSTPISCOF'],
                    'VRALIQPIS' => $combo['VRALIQPIS'],
                    'VRALIQCOFINS' => $combo['VRALIQCOFINS'],
                    'VRDESITVEND' => 0,
                    'VRACRITVEND' => 0,
                    'CAMPANHA' => false,
                    'DTINIVGCAMPCG' => null,
                    'QTCOMPGANHE' => null
                );
                array_push($formattedCombos, $preparedCombo);
            }

            // Cria um grupo para os produtos combinados.
            if (!empty($formattedCombos)){
                $comboGroup = array(
                    'grupo' => array(
                        'CODIGO' => 'X',
                        'DESC' => 'COMBOS',
                        'COLOR' => '8421376.000',
                        'DSENDEIMG' => null,
                        'NRBUTTON' => null,
                        'DESCING' => null,
                        'DESCESP' => null
                    ),
                    'produtos' => $formattedCombos
                );

                $params = array (
                    'CDFILIAL' => $CDFILIAL,
                    'NRORG' => $NRORG
                );

                $posicaoProdComb = $this->entityManager->getConnection()->fetchAssoc("VERIFICA_POS_PROD_COMBINADO", $params);

                $verifica = false;
                if (!empty($posicaoProdComb)) {
                    $chave = $posicaoProdComb['PAGPRODCOM'] . $posicaoProdComb['POSPRODCOM'];
                    for ($i = $chave; $i >= '101'; $i--) {
                        if (array_key_exists($i, $arrayRetorno)) {
                            $posicao = array_search($i, array_keys($arrayRetorno));
                            $posicao = $chave != $i ?  $posicao + 1 : $posicao;
                            array_splice($arrayRetorno, $posicao, 0, array($comboGroup));
                            $verifica = true;
                            break;
                        }
                    }
                }
                if (!$verifica) {
                    $arrayRetorno['X'] = $comboGroup;
                }

            }

        }

        // Busca todos os grupos promocionais presentes no cardapio com preço atualizado.
        // $gruposPromocionais = self::buscaGruposPromocionais($arrayRetorno, $CDFILIAL, $CDCLIENTE, $CDLOJA, $CDCONSUMIDOR);
        // self::atualizaPrecoGrupPromoc($arrayRetorno, $gruposPromocionais);

        return $arrayRetorno;
    }

    private function buscaCampanhaCompreGanhe($CDFILIAL){
        $params = array(
            'CDFILIAL' => $CDFILIAL
        );
        $campanha = $this->entityManager->getConnection()->fetchAssoc("CAMPANHA_COMPRE_GANHE", $params);

        $result = array();
        if (!empty($campanha)){
            $result['quantity'] = $campanha['QTCOMPGANHE'];
            $result['CDCAMPCOMPGANHE'] = $campanha['CDCAMPCOMPGANHE'];
            $result['DTINIVGCAMPCG'] = $campanha['DTINIVGCAMPCG'];
            $DTINIVGCAMPCG = $this->databaseUtil->databaseIsOracle() ?
                \DateTime::createFromFormat('d-m-Y H:i:s', $campanha['DTINIVGCAMPCG']) :
                \DateTime::createFromFormat('Y-m-d H:i:s.u', $campanha['DTINIVGCAMPCG']);

            $params = array(
                'CDCAMPCOMPGANHE' => $campanha['CDCAMPCOMPGANHE'],
                'DTINIVGCAMPCG' => $DTINIVGCAMPCG
            );
            $types = array(
                'DTINIVGCAMPCG' => \Doctrine\DBAL\Types\Type::DATETIME
            );
            $produtos = $this->entityManager->getConnection()->fetchAll("PRODUTOS_COMPRE_GANHE", $params, $types);

            $result['products'] = array();
            foreach ($produtos as $produto){
                array_push($result['products'], $produto['CDPRODUTO']);
            }
        }

        return $result;
    }

    private function buscaGruposPromocionais($cardapio, $CDFILIAL, $CDCLIENTE, $CDLOJA, $CDCONSUMIDOR) {
    	$gruposPromocionais = array();

    	foreach ($cardapio as &$grupoAtual) {
    		if (!empty($grupoAtual['subgrupos'])) {
    			foreach ($grupoAtual['subgrupos'] as $subGrupoAtual) {
    				foreach ($subGrupoAtual['produtos'] as $produtoSubGrupoAtual) {
    					if ($produtoSubGrupoAtual['IDTIPOCOMPPROD'] == '3' || $produtoAtual['IDTIPOCOMPPROD'] == '6'){
    						foreach ($produtoSubGrupoAtual['GRUPOS'] as $chaveGrupo => &$grupoPromocAtual) {
			    				$chaveExiste = array_key_exists($chaveGrupo, $gruposPromocionais);
			    				if (!$chaveExiste) {
			    					foreach ($grupoPromocAtual['produtos'] as &$prodGrupoPromocAtual) {
			    						$preco =  $this->precoService->buscaPreco($CDFILIAL, $CDCLIENTE, $prodGrupoPromocAtual['CDPRODUTO'], $CDLOJA, $CDCONSUMIDOR);
	                                    $prodGrupoPromocAtual['VRPRECITEM'] = $preco['PRECO'];
			    					}
			    					$gruposPromocionais[$chaveGrupo] = $grupoPromocAtual;
			    				}
			    			}
    					}
    				}
    			}
    		}
            else {
    			foreach ($grupoAtual['produtos'] as &$produtoAtual) {
		    		if ($produtoAtual['IDTIPOCOMPPROD'] == '3' || $produtoAtual['IDTIPOCOMPPROD'] == '6'){
		    			// produto é combo
		    			foreach ($produtoAtual['GRUPOS'] as $chaveGrupo => &$grupoPromocAtual) {
		    				// verifica se grupo promocional já foi inserido no array
		    				$chaveExiste = array_key_exists($chaveGrupo, $gruposPromocionais);
		    				if (!$chaveExiste) {
		    					foreach ($grupoPromocAtual['produtos'] as &$prodGrupoPromocAtual) {
		    						// carrega preço dos produtos do grupo promocional corretamente
		    						$preco =  $this->precoService->buscaPreco($CDFILIAL, $CDCLIENTE, $prodGrupoPromocAtual['CDPRODUTO'], $CDLOJA, $CDCONSUMIDOR);
                                    $prodGrupoPromocAtual['VRPRECITEM'] = $preco['PRECO'];
		    					}
		    					$gruposPromocionais[$chaveGrupo] = $grupoPromocAtual;
		    				}
		    			}
		    		}
    			}
    		}
    	}

    	return $gruposPromocionais;
    }

    private function atualizaPrecoGrupPromoc(&$cardapio, $gruposPromocionais) {
    	foreach ($cardapio as &$grupoAtual) {
    		foreach ($grupoAtual['produtos'] as &$produtoAtual) {
	    		if ($produtoAtual['IDTIPOCOMPPROD'] == '3' || $produtoAtual['IDTIPOCOMPPROD'] == '6') {
	    			// produto é combo
	    			foreach ($produtoAtual['GRUPOS'] as $chaveGrupo => &$grupoPromocAtual) {
	    				// percorre produtos do grupo promocional e atualiza preços
    					foreach ($grupoPromocAtual['produtos'] as $chaveProduto => &$prodGrupoPromocAtual) {
    						$prodGrupoPromocAtual['VRPRECITEM'] = $gruposPromocionais[$chaveGrupo]['produtos'][$chaveProduto]['VRPRECITEM'];
    					}
	    			}
	    		}
			}
		}
    }

    private function montaRecebimentos($dadosCaixa){
        $NRCONFTELA = $this->util->getConfTela($dadosCaixa['CDFILIAL'], $dadosCaixa['CDCAIXA']);

        $params = array(
            'CDFILIAL' => $NRCONFTELA['CDFILIAL'],
            'NRCONFTELA' => $NRCONFTELA['NRCONFTELA'],
            'DTINIVIGENCIA' => $NRCONFTELA['DTINIVIGENCIA']
        );
        $types = array(
            'DTINIVIGENCIA' => \Doctrine\DBAL\Types\Type::DATETIME
        );
        return $this->entityManager->getConnection()->fetchAll("GET_TIPO_RECEBIMENTOS", $params, $types);
    }

    private function montaGrupoRecebimentos($dadosCaixa, $recebimentos){

        $NRCONFTELA = $this->util->getConfTela($dadosCaixa['CDFILIAL'], $dadosCaixa['CDCAIXA']);

        $params = array(
            'CDFILIAL'    => $NRCONFTELA['CDFILIAL'],
            'NRCONFTELA'  => $NRCONFTELA['NRCONFTELA'],
            'DTINIVIGENCIA' => $NRCONFTELA['DTINIVIGENCIA'],
            'NRBUTTONAUX' => array_unique(array_column($recebimentos, 'NRBUTTONAUX'))
        );
        $types = array(
            'CDFILIAL'    => \PDO::PARAM_STR,
            'NRCONFTELA'  => \PDO::PARAM_STR,
            'DTINIVIGENCIA' => \Doctrine\DBAL\Types\Type::DATETIME,
            'NRBUTTONAUX' => \Doctrine\DBAL\Connection::PARAM_STR_ARRAY
        );

        return $this->entityManager->getConnection()->fetchAll("GET_GRUPO_TIPO_RECEBIMENTOS", $params, $types);
    }

}

class Bench {
    private $start;
    private $end;

    public function __construct() {
        $this->start = microtime(true);
    }

    public function end() {
        $this->end = microtime(true);
        return ($this->end - $this->start);
    }
}
