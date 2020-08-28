<?php

namespace Odhen\API\Util;

class MSDEQuery {

    public function getArray() {
        return self::QUERY_MAPPING;
    }

    const QUERY_MAPPING = array(
        'SQL_ABRE_COMANDA'                                => self::SQL_ABRE_COMANDA,
        'SQL_INSERE_VENDAREST'                            => self::SQL_INSERE_VENDAREST,
        'BUSCA_DADOS_IMPRESSORA'                          => self::BUSCA_DADOS_IMPRESSORA,
        'UPDATE_SENHA_DELPHI'                             => self::UPDATE_SENHA_DELPHI,
        'UPDATE_ITEMVENDA_FAMILIA'                        => self::UPDATE_ITEMVENDA_FAMILIA,
        'GET_PROD_SAT'                                    => self::GET_PROD_SAT,
        'GET_PAG_SAT'                                     => self::GET_PAG_SAT,
        'GET_PARAMS_SAT'                                  => self::GET_PARAMS_SAT,
        'GET_HOMOL_SAT'                                   => self::GET_HOMOL_SAT,
        'GET_CRT_SAT'                                     => self::GET_CRT_SAT,
        'BUSCA_DADOS_TEF'                                 => self::BUSCA_DADOS_TEF,
        'INSERT_MOVCAIXA'                                 => self::INSERT_MOVCAIXA,
        'BUSCA_DADOS_OPERADOR'                            => self::BUSCA_DADOS_OPERADOR,
        'BUSCA_DADOS_RECEBIMENTO'                         => self::BUSCA_DADOS_RECEBIMENTO,
        'INSERT_TURCAIXA'                                 => self::INSERT_TURCAIXA,
        'INSERT_MOVCAIXA_OPENPOS'                         => self::INSERT_MOVCAIXA_OPENPOS,
        'UPDATE_DTFECHAMENTO_TURCAIXA'                    => self::UPDATE_DTFECHAMENTO_TURCAIXA,
        'VERIFICA_TIPO_EMISSAO_CAIXA'                     => self::VERIFICA_TIPO_EMISSAO_CAIXA,
        'GET_NFCE_DATA'                                   => self::GET_NFCE_DATA,
        'VERIFICA_ABERTURA_ESTADO_CAIXA'                  => self::VERIFICA_ABERTURA_ESTADO_CAIXA,
        'VERIFICA_FECHAMENTO_CAIXA'                       => self::VERIFICA_FECHAMENTO_CAIXA,
        'GET_NMOPERADOR'                                  => self::GET_NMOPERADOR,
        'GET_VRVENDBRUT'                                  => self::GET_VRVENDBRUT,
        'GET_VRTOTINI'                                    => self::GET_VRTOTINI,
        'GET_NRTRANSACOES'                                => self::GET_NRTRANSACOES,
        'GET_NRPRIMSEQ'                                   => self::GET_NRPRIMSEQ,
        'GET_NRFINALSEQ'                                  => self::GET_NRFINALSEQ,
        'GET_CANCELAMENTOS'                               => self::GET_CANCELAMENTOS,
        'GET_VRDESITVEND'                                 => self::GET_VRDESITVEND,
        'GET_VRACRITVEND'                                 => self::GET_VRACRITVEND,
        'GET_VRMOVIVEND'                                  => self::GET_VRMOVIVEND,
        'GET_IMPOSTOS'                                    => self::GET_IMPOSTOS,
        'GET_PAGAMENTOS'                                  => self::GET_PAGAMENTOS,
        'TOTAL_VENDAS'                                    => self::TOTAL_VENDAS,
        'TOTAL_CREDITO'                                   => self::TOTAL_CREDITO,
        'FORMA_PAGAMENTO'                                 => self::FORMA_PAGAMENTO,
        'GET_SANGRIA_AUTOMATICA'                          => self::GET_SANGRIA_AUTOMATICA,
        'BUSCA_CDATIVASAT'                                => self::BUSCA_CDATIVASAT,
        'GET_CLIENTS'                                     => self::GET_CLIENTS,
        'BUSCA_TIPO_RECE'                                 => self::BUSCA_TIPO_RECE,
        'BUSCA_DADOS_IMPRESSORA_SITEF'                    => self::BUSCA_DADOS_IMPRESSORA_SITEF,
        'ENDERECO_LOJA'                                   => self::ENDERECO_LOJA,
        'INSERT_PRODUCT'                                  => self::INSERT_PRODUCT,
        'INSERT_SMARTPROMO'                               => self::INSERT_SMARTPROMO,
        'GET_SECTOR_CODE'                                 => self::GET_SECTOR_CODE,
        'GET_NRSEQPRODCOM'                                => self::GET_NRSEQPRODCOM,
        'GET_PRODUCT_DETAILS'                             => self::GET_PRODUCT_DETAILS,
        'INSERT_VENDAREST'                                => self::INSERT_VENDAREST,
        'CANCELA_VENDAREST'                               => self::CANCELA_VENDAREST,
        'CANCELA_COMANDAVEN'                              => self::CANCELA_COMANDAVEN,
        'INSERT_COMANDAVEN'                               => self::INSERT_COMANDAVEN,
        'GET_STANDARD_SELLER'                             => self::GET_STANDARD_SELLER,
        'GET_PRODUCT_CONTROL_ID'                          => self::GET_PRODUCT_CONTROL_ID,
        'INSERT_MOVCAIXADLV'                              => self::INSERT_MOVCAIXADLV,
        'GET_LAST_SALE'                                   => self::GET_LAST_SALE,
        'GET_LASTSALE_PRODUCTS'                           => self::GET_LASTSALE_PRODUCTS,
        'GET_LASTSALE_PRODUCT_OBSERVATIONS'               => self::GET_LASTSALE_PRODUCT_OBSERVATIONS,
        'GET_LASTSALE_COMBOS'                             => self::GET_LASTSALE_COMBOS,
        'GET_LASTSALE_COMBO_PRODUCTS'                     => self::GET_LASTSALE_COMBO_PRODUCTS,
        'GET_LASTSALE_PAYMENTS'                           => self::GET_LASTSALE_PAYMENTS,
        'BUSCA_ITCOMANDAVEN'                              => self::BUSCA_ITCOMANDAVEN,
        'DELETA_OBSITCOMANDAEST'                          => self::DELETA_OBSITCOMANDAEST,
        'DELETA_OBSITCOMANDAVEN'                          => self::DELETA_OBSITCOMANDAVEN,
        'DELETA_ITCOMANDAEST'                             => self::DELETA_ITCOMANDAEST,
        'DELETA_ITCOMANDAVEN'                             => self::DELETA_ITCOMANDAVEN,
        'LIBERA_MESA'                                     => self::LIBERA_MESA,
        'DELETA_COMANDAVEN'                               => self::DELETA_COMANDAVEN,
        'DELETA_VENDAREST'                                => self::DELETA_VENDAREST,
        'DELETA_POSVENDAREST'                             => self::DELETA_POSVENDAREST,
        'GET_JUNCAOMESA'                                  => self::GET_JUNCAOMESA,
        'DELETA_JUNCAOMESA'                               => self::DELETA_JUNCAOMESA,
        'DELETA_MESAJUNCAO'                               => self::DELETA_MESAJUNCAO,
        'GET_CONSUMER_BY_ID'                              => self::GET_CONSUMER_BY_ID,
        'GET_CONSUMER_CDIDCONSUMID'                       => self::GET_CONSUMER_CDIDCONSUMID,
        'UPDATE_ITEMVENDA_ESTQ'                           => self::UPDATE_ITEMVENDA_ESTQ,
        'GET_PRODUTO_CONTRESTOQ'                          => self::GET_PRODUTO_CONTRESTOQ,
        'GET_PARAMSESTQ_FILIAL'                           => self::GET_PARAMSESTQ_FILIAL,
        'GET_TANQUEBICOH_BY_BICO'                         => self::GET_TANQUEBICOH_BY_BICO,
        'GET_RELACALMOXESTRUT_PDV'                        => self::GET_RELACALMOXESTRUT_PDV,
        'GET_RELACALMOXESTRUT_LOJA'                       => self::GET_RELACALMOXESTRUT_LOJA,
        'GET_RELACALMOXESTRUT_FILIAL'                     => self::GET_RELACALMOXESTRUT_FILIAL,
        'GET_ITEMS_VENDA_CTRLESTOQ'                       => self::GET_ITEMS_VENDA_CTRLESTOQ,
        'INSERT_LANCTOESTOQ'                              => self::INSERT_LANCTOESTOQ,
        'INSERT_ITLANCTOEST'                              => self::INSERT_ITLANCTOEST,
        'GET_MAX_ITLANCTOEST'                             => self::GET_MAX_ITLANCTOEST,
        'DADOS_CONSUMIDOR'                                => self::DADOS_CONSUMIDOR,
        'BENEFICIOS_CONSUMIDOR'                           => self::BENEFICIOS_CONSUMIDOR,
        'PRODUTOS_CAMPANHA'                               => self::PRODUTOS_CAMPANHA,
        'GET_DADOS_FILIAL'                                => self::GET_DADOS_FILIAL,
        'BUSCA_DADOS_IMPRESSORA_NF'                       => self::BUSCA_DADOS_IMPRESSORA_NF,
        'BUSCA_DADOS_CAIXA'                               => self::BUSCA_DADOS_CAIXA,
        'BUSCA_RECEBIMENTOS'                              => self::BUSCA_RECEBIMENTOS,
        'BUSCA_PRODUTOS'                                  => self::BUSCA_PRODUTOS,
        'GET_INFO_PAYMENT_NFCE'                           => self::GET_INFO_PAYMENT_NFCE,
        'GET_CANCELED_PRODUCTS'                           => self::GET_CANCELED_PRODUCTS,
        'GET_NFCE_TO_PRINT'                               => self::GET_NFCE_TO_PRINT,
        'GET_PARAVEND_DATA_TO_PRINT'                      => self::GET_PARAVEND_DATA_TO_PRINT,
        'BUSCA_DADOS_LOJA'                                => self::BUSCA_DADOS_LOJA,
        'BUSCA_DADOS_MESA'                                => self::BUSCA_DADOS_MESA,
        'BUSCA_DADOS_VENDEDOR'                            => self::BUSCA_DADOS_VENDEDOR,
        'BUSCA_IMPRESSORAS'                               => self::BUSCA_IMPRESSORAS,
        'BUSCA_IMPRESSORAS_POR_AMBIENTE'                  => self::BUSCA_IMPRESSORAS_POR_AMBIENTE,
        'BUSCA_DADOS_IMPRESSORA_PED'                      => self::BUSCA_DADOS_IMPRESSORA_PED,
        'BUSCA_NOME_POR_POSICAO'                          => self::BUSCA_NOME_POR_POSICAO,
        'BUSCA_DADOS_IMPRESSORA_SAT'                      => self::BUSCA_DADOS_IMPRESSORA_SAT,
        'BUSCA_PRODUTOS_SAT'                              => self::BUSCA_PRODUTOS_SAT,
        'VALIDA_FILIAL'                                   => self::VALIDA_FILIAL,
        'VALIDA_LOJA'                                     => self::VALIDA_LOJA,
        'VALIDA_PROD_TAXASERV'                            => self::VALIDA_PROD_TAXASERV,
        'VALIDA_CAIXA'                                    => self::VALIDA_CAIXA,
        'CONFIGURACAO'                                    => self::CONFIGURACAO,
        'VALIDA_MODO_HABILITADO'                          => self::VALIDA_MODO_HABILITADO,
        'VALIDA_VENDEDOR'                                 => self::VALIDA_VENDEDOR,
        'VALIDA_OPERADOR'                                 => self::VALIDA_OPERADOR,
        'OPERADOR_FILIAL'                                 => self::OPERADOR_FILIAL,
        'GET_EMPRESAFILIAL'                               => self::GET_EMPRESAFILIAL,
        'GET_ENDEFILI'                                    => self::GET_ENDEFILI,
        'GET_DADOSEMITENTE_IDE_XML'                       => self::GET_DADOSEMITENTE_IDE_XML,
        'GET_SERIE_NFCE'                                  => self::GET_SERIE_NFCE,
        'GET_PAYMENTS'                                    => self::GET_PAYMENTS,
        'GET_DADOSPRODIMP_XML'                            => self::GET_DADOSPRODIMP_XML,
        'GET_IDTIPORECE'                                  => self::GET_IDTIPORECE,
        'GET_PRODUCTS_TO_PRINT_NFCE'                      => self::GET_PRODUCTS_TO_PRINT_NFCE,
        'GET_CONSUMER_SALE'                               => self::GET_CONSUMER_SALE,
        'VALIDA_SUPERVISOR'                               => self::VALIDA_SUPERVISOR,
        'CONTROLE_ACESSO'                                 => self::CONTROLE_ACESSO,
        'GET_FILIAIS_BY_OPERADOR'                         => self::GET_FILIAIS_BY_OPERADOR,
        'BUSCA_DADOS_FILIAL'                              => self::BUSCA_DADOS_FILIAL,
        'BUSCA_DADOS_CAIXA_PARAM'                         => self::BUSCA_DADOS_CAIXA_PARAM,
        'BUSCA_DADOS_LOJA_PARAM'                          => self::BUSCA_DADOS_LOJA_PARAM,
        'BUSCA_DADOS_VENDEDOR_PARAM'                      => self::BUSCA_DADOS_VENDEDOR_PARAM,
        'BUSCA_DADOS_PARAVEND'                            => self::BUSCA_DADOS_PARAVEND,
        'BUSCA_OBSERVACOES'                               => self::BUSCA_OBSERVACOES,
        'BUSCA_AMBIENTES'                                 => self::BUSCA_AMBIENTES,
        'BUSCA_MESAS'                                     => self::BUSCA_MESAS,
        'BUSCA_GRUPO_PRODUTOS'                            => self::BUSCA_GRUPO_PRODUTOS,
        'BUSCA_SUBGRUPO_PRODUTOS'                         => self::BUSCA_SUBGRUPO_PRODUTOS,
        'BUSCA_PRODUTOS_PARAM'                            => self::BUSCA_PRODUTOS_PARAM,
        'BUSCA_OBSERVACOES_PRODUTO'                       => self::BUSCA_OBSERVACOES_PRODUTO,
        'SQL_PRECOS'                                      => self::SQL_PRECOS,
        'SQL_PRECOS_DIA'                                  => self::SQL_PRECOS_DIA,
        'VAL_TABE'                                        => self::VAL_TABE,
        'GET_IMP_PRODUTOS'                                => self::GET_IMP_PRODUTOS,
        'GRUPOS_PROMO_INT'                                => self::GRUPOS_PROMO_INT,
        'GRUPOS_PROMO_INT_FILI'                           => self::GRUPOS_PROMO_INT_FILI,
        'SMART_PROMO_PRODUCTS'                            => self::SMART_PROMO_PRODUCTS,
        'SMART_PROMO_PRODUCTS_FILI'                       => self::SMART_PROMO_PRODUCTS_FILI,
        'PRODUTOS_PROMO_INT'                              => self::PRODUTOS_PROMO_INT,
        'GET_COMBO_PRODUCTS'                              => self::GET_COMBO_PRODUCTS,
        'BUSCA_PRODUTOS_COMBO'                            => self::BUSCA_PRODUTOS_COMBO,
        'GET_TIPO_RECEBIMENTOS'                           => self::GET_TIPO_RECEBIMENTOS,
        'GET_GRUPO_TIPO_RECEBIMENTOS'                     => self::GET_GRUPO_TIPO_RECEBIMENTOS,
        'CLIENTE_FILIAL'                                  => self::CLIENTE_FILIAL,
        'EXISTE_PRECO_CLIE'                               => self::EXISTE_PRECO_CLIE,
        'TABELA_PRECO_LOJA'                               => self::TABELA_PRECO_LOJA,
        'PARAVEND'                                        => self::PARAVEND,
        'TABELA_VENDA'                                    => self::TABELA_VENDA,
        'ITEM_PRECO'                                      => self::ITEM_PRECO,
        'TIPO_CONSUMIDOR'                                 => self::TIPO_CONSUMIDOR,
        'ITEM_PRECO_DIA'                                  => self::ITEM_PRECO_DIA,
        'GET_SMARTPROMO_PRICE'                            => self::GET_SMARTPROMO_PRICE,
        'BUSCA_DADOS_SAT'                                 => self::BUSCA_DADOS_SAT,
        'ATUALIZA_VENDA_SAT'                              => self::ATUALIZA_VENDA_SAT,
        'CALCULA_TOTAL_COMANDA'                           => self::CALCULA_TOTAL_COMANDA,
        'BUSCA_DADOS_TAXA_SERVICO'                        => self::BUSCA_DADOS_TAXA_SERVICO,
        'BUSCA_ITENS_PEDIDOS'                             => self::BUSCA_ITENS_PEDIDOS,
        'BUSCA_ITENS_PEDIDOS_OBS'                         => self::BUSCA_ITENS_PEDIDOS_OBS,
        'BUSCA_VENDA'                                     => self::BUSCA_VENDA,
        'CANCELA_VENDA'                                   => self::CANCELA_VENDA,
        'CANCELA_MOVCAIXA'                                => self::CANCELA_MOVCAIXA,
        'BUSCA_MOVICLIE'                                  => self::BUSCA_MOVICLIE,
        'CANCELA_MOVICLIE'                                => self::CANCELA_MOVICLIE,
        'GET_DADOS_CAIXA'                                 => self::GET_DADOS_CAIXA,
        'GET_IDCUMUPISCOFIL'                              => self::GET_IDCUMUPISCOFIL,
        'GET_IMPOSTOS_PRODUTO'                            => self::GET_IMPOSTOS_PRODUTO,
        'GET_ENDEFILI_FULL'                               => self::GET_ENDEFILI_FULL,
        'INSERT_VENDA'                                    => self::INSERT_VENDA,
        'GET_CONSUMIDOR_LIMITE_CREDITO'                   => self::GET_CONSUMIDOR_LIMITE_CREDITO,
        'GET_DADOSPROD'                                   => self::GET_DADOSPROD,
        'INSERT_ITEMVENDA'                                => self::INSERT_ITEMVENDA,
        'GET_BOMBA_BY_BICO'                               => self::GET_BOMBA_BY_BICO,
        'GET_NOVO_NRITEMVENDAUXILIAR'                     => self::GET_NOVO_NRITEMVENDAUXILIAR,
        'GET_TIPOCOMBUSTIVEL_BY_PRODUTO'                  => self::GET_TIPOCOMBUSTIVEL_BY_PRODUTO,
        'INSERT_ITEMVENDAAUXILIAR'                        => self::INSERT_ITEMVENDAAUXILIAR,
        'INSERT_ITEMVENDAAUXILIAR_FEATUREGRUPO'           => self::INSERT_ITEMVENDAAUXILIAR_FEATUREGRUPO,
        'UPDATE_MOVBENEFICIOCTR'                          => self::UPDATE_MOVBENEFICIOCTR,
        'INSERT_MOVBENEFICIOCONS'                         => self::INSERT_MOVBENEFICIOCONS,
        'INSERT_ITHRCOMANDA'                              => self::INSERT_ITHRCOMANDA,
        'BUSCA_OCORRENCIA'                                => self::BUSCA_OCORRENCIA,
        'INSERT_OBSITEMVENDA'                             => self::INSERT_OBSITEMVENDA,
        'INSERT_OBSITEMVENDAEST'                          => self::INSERT_OBSITEMVENDAEST,
        'INSERE_ITEMVENDAEST'                             => self::INSERE_ITEMVENDAEST,
        'GET_ALIQIMPFIS_IMPOSTO'                          => self::GET_ALIQIMPFIS_IMPOSTO,
        'INSERT_ITVENDAIMPOS'                             => self::INSERT_ITVENDAIMPOS,
        'INSERT_ITEMVENDA_CANCELADO'                      => self::INSERT_ITEMVENDA_CANCELADO,
        'INSERT_MOVCAIXA_SALE'                            => self::INSERT_MOVCAIXA_SALE,
        'INSERT_MOVCLIE'                                  => self::INSERT_MOVCLIE,
        'GET_PRODESTFEATUREGRU'                           => self::GET_PRODESTFEATUREGRU,
        'GET_NRFEATUREGRUPO_BY_DIMENSOES'                 => self::GET_NRFEATUREGRUPO_BY_DIMENSOES,
        'GET_SALDOCONSUMIDOR'                             => self::GET_SALDOCONSUMIDOR,
        'BUSCA_PARAVEND'                                  => self::BUSCA_PARAVEND,
        'UPDATE_NRSEQVENDA_VND_INTEGRACAOMOV'             => self::UPDATE_NRSEQVENDA_VND_INTEGRACAOMOV,
        'GET_CLIENTEPADRAO'                               => self::GET_CLIENTEPADRAO,
        'GET_SALE_PARAMETERS'                             => self::GET_SALE_PARAMETERS,
        'CREATE_BALANCE'                                  => self::CREATE_BALANCE,
        'UPDATE_BALANCE'                                  => self::UPDATE_BALANCE,
        'INSERT_EXTRATOCONS'                              => self::INSERT_EXTRATOCONS,
        'GET_CDTIPORECE_BY_BANCART'                       => self::GET_CDTIPORECE_BY_BANCART,
        'CAIXA'                                           => self::CAIXA,
        'PRODUTO'                                         => self::PRODUTO,
        'TIPORECE'                                        => self::TIPORECE,
        'DADOS_CAIXA'                                     => self::DADOS_CAIXA,
        'GET_HIERARQUIA_BYCODPARAM'                       => self::GET_HIERARQUIA_BYCODPARAM,
        'GET_PARAMETRO_BYCODPARAMETRO'                    => self::GET_PARAMETRO_BYCODPARAMETRO,
        'GET_HIERARQUIA_BYNRPARAMETRO'                    => self::GET_HIERARQUIA_BYNRPARAMETRO,
        'GET_PARAMETRO_BYRELACIONAMENTOGERAL'             => self::GET_PARAMETRO_BYRELACIONAMENTOGERAL,
        'GET_PARAMETRO_BYDESCRELAC_VALOR'                 => self::GET_PARAMETRO_BYDESCRELAC_VALOR,
        'GET_PARAMETRO_BYNRPARAMETROVALOR'                => self::GET_PARAMETRO_BYNRPARAMETROVALOR,
        'GET_PARAMETRO_BYNRPARAMETROVALOR_BYDESC_BYVALOR' => self::GET_PARAMETRO_BYNRPARAMETROVALOR_BYDESC_BYVALOR,
        'GET_PARAMETRO_BYNRPARAMETROVALOR_BYDESC'         => self::GET_PARAMETRO_BYNRPARAMETROVALOR_BYDESC,
        'GET_SMTP'                                        => self::GET_SMTP,
        'GET_CDTIPORECE_FUNDO'                            => self::GET_CDTIPORECE_FUNDO,
        'SQL_BUSCA_CONTADOR'                              => self::SQL_BUSCA_CONTADOR,
        'EXECUTE_NOVO_CODIGO'                             => self::EXECUTE_NOVO_CODIGO,
        'BUSCA_NRDEPOSICONS'                              => self::BUSCA_NRDEPOSICONS,
        'SQL_GET_CONSUMIDOR_LIMITE_DEBITO'                => self::SQL_GET_CONSUMIDOR_LIMITE_DEBITO,
        'SQL_GET_CONSUMIDOR_SALDO_DEBITO'                 => self::SQL_GET_CONSUMIDOR_SALDO_DEBITO,
        'SQL_DETALHES_FAMILIA'                            => self::SQL_DETALHES_FAMILIA,
        'SQL_BUSCA_LIMITES'                               => self::SQL_BUSCA_LIMITES,
        'SQL_CONSUMO_DIARIO'                              => self::SQL_CONSUMO_DIARIO,
        'SQL_SALDO_EXTRATOCONS'                           => self::SQL_SALDO_EXTRATOCONS,
        'GET_NRSEQMOVEXT'                                 => self::GET_NRSEQMOVEXT,
        'BUSCA_NOMES'                                     => self::BUSCA_NOMES,
        'GET_PERMITE_SALDO_NEGATIVO'                      => self::GET_PERMITE_SALDO_NEGATIVO,
        'GET_FAMSALDOPROD'                                => self::GET_FAMSALDOPROD,
        'CHECK_BALANCE'                                   => self::CHECK_BALANCE,
        'BUSCA_FAMILIA_SALDO_FILIAL'                      => self::BUSCA_FAMILIA_SALDO_FILIAL,
        'PRODUTOS_PROMO_INT_FILI'                         => self::PRODUTOS_PROMO_INT_FILI,
        'UPDATE_VENDA_STATUS_NFCE_CAN'                    => self::UPDATE_VENDA_STATUS_NFCE_CAN,
        'SQL_GET_FILIAL_DETAILS'                          => self::SQL_GET_FILIAL_DETAILS,
        'IS_FIRST_INSERTION'                              => self::IS_FIRST_INSERTION,
        'IS_FIRST_INSERTION_FAMILY'                       => self::IS_FIRST_INSERTION_FAMILY,
        'SQL_SALDO_SALDOCONS'                             => self::SQL_SALDO_SALDOCONS,
        'RESTRICAO_PRODUTO_DIA'                           => self::RESTRICAO_PRODUTO_DIA,
        'GET_TOTAL_VENDA_CREDITO_PESSOAL'                 => self::GET_TOTAL_VENDA_CREDITO_PESSOAL,
        'GET_CONSUMER_SALDOCONS_NMFAMILISALD'             => self::GET_CONSUMER_SALDOCONS_NMFAMILISALD,
        'PERMITE_SALDO_NEGATIVO_CONSUMIDOR'               => self::PERMITE_SALDO_NEGATIVO_CONSUMIDOR,
        'RESTRICAO_SALDO_DIARIO'                          => self::RESTRICAO_SALDO_DIARIO,
        'PRODUTO_CONSUMIDO_DIA'                           => self::PRODUTO_CONSUMIDO_DIA,
        'SQL_GET_PERMITE_SALDO_NEGATIVO'                  => self::SQL_GET_PERMITE_SALDO_NEGATIVO,
        'INSERT_PRICED_OBSERVATION'                       => self::INSERT_PRICED_OBSERVATION,
        'GET_IDTIPCOBRA'                                  => self::GET_IDTIPCOBRA,
        'GET_CLIENTBRANCH_PRICING_TABLE'                  => self::GET_CLIENTBRANCH_PRICING_TABLE,
        'GET_CLIENT_PRICING_TABLE'                        => self::GET_CLIENT_PRICING_TABLE,
        'GET_STORE_PRICING_TABLE'                         => self::GET_STORE_PRICING_TABLE,
        'GET_PARAVEND_PRICING_TABLE'                      => self::GET_PARAVEND_PRICING_TABLE,
        'HOLIDAY_CHECK'                                   => self::HOLIDAY_CHECK,
        'ITEM_DAY_PRICE'                                  => self::ITEM_DAY_PRICE,
        'GET_ALIQUOTA'                                    => self::GET_ALIQUOTA,
        'GET_SALES_TABLE'                                 => self::GET_SALES_TABLE,
        'GET_ITEM_PRICE'                                  => self::GET_ITEM_PRICE,
        'GET_SALDO_ALL_FAMILIES'                          => self::GET_SALDO_ALL_FAMILIES,
        'GET_SALDO_FOR_FAMILIES'                          => self::GET_SALDO_FOR_FAMILIES,
        'ATUALIZA_MOVIMENTACAO_EXTRATO'                   => self::ATUALIZA_MOVIMENTACAO_EXTRATO,
        'GET_VERIFICA_SALDO'                              => self::GET_VERIFICA_SALDO,
        'INSERT_SALDO_CONS'                               => self::INSERT_SALDO_CONS,
        'ATUALIZA_SALDO_CONS'                             => self::ATUALIZA_SALDO_CONS,
        'VERIFICA_CANCELAMENTO'                           => self::VERIFICA_CANCELAMENTO,
        'VERIFICA_SALDO_CANCELAMENTO'                     => self::VERIFICA_SALDO_CANCELAMENTO,
        'UPDATE_MOVCAIXA'                                 => self::UPDATE_MOVCAIXA,
        'SQL_GET_GASTO_DIA_DEBITO_CONSUMIDOR'             => self::SQL_GET_GASTO_DIA_DEBITO_CONSUMIDOR,
        'SQL_GET_GASTO_MES_DEBITO_CONSUMIDOR'             => self::SQL_GET_GASTO_MES_DEBITO_CONSUMIDOR,
        'BUSCA_HORARIO_PRECOS'                            => self::BUSCA_HORARIO_PRECOS,
        'UPDATE_VENDA_NFCE'                               => self::UPDATE_VENDA_NFCE,
        'GET_ITENS_CANCELADOS'                            => self::GET_ITENS_CANCELADOS,
        'GET_CONSULTA_SALDO'                              => self::GET_CONSULTA_SALDO,
        'BUSCA_MOVI_EXTRATOCONS'                          => self::BUSCA_MOVI_EXTRATOCONS,
        'BUSCA_ITENS_SUGESTAO'                            => self::BUSCA_ITENS_SUGESTAO,
        'BUSCA_DATA_ULTIMA_VENDA'                         => self::BUSCA_DATA_ULTIMA_VENDA,
        'INSERT_INUTILIZANFCE'                            => self::INSERT_INUTILIZANFCE,
        'UPDATE_BUSCA_CONTADOR'                           => self::UPDATE_BUSCA_CONTADOR,
        'GET_ULTIMA_VENDA_SAT'                            => self::GET_ULTIMA_VENDA_SAT,
        'UPDATE_VENDA_INSERIDA_SAT'                       => self::UPDATE_VENDA_INSERIDA_SAT,
        'UPDATE_VENDA_ALTERADA_SAT'                       => self::UPDATE_VENDA_ALTERADA_SAT,
        'CHECK_INDEX'                                     => self::CHECK_INDEX,
        'CREATE_BUSCA_CONSUMIDOR'                         => self::CREATE_BUSCA_CONSUMIDOR,
        'UPDATE_MESA_CREDFIDELITY'                        => self::UPDATE_MESA_CREDFIDELITY,
        'DELETA_POSVENDAREST_POS'                         => self::DELETA_POSVENDAREST_POS,
        'DELETA_CONTROLPOSVEN'                            => self::DELETA_CONTROLPOSVEN,
        'BUSCA_FILESERVER'                                => self::BUSCA_FILESERVER,
        'SQL_DADOS_MESA'                                  => self::SQL_DADOS_MESA,
        'GET_TIPORECE_SANGRIA_AUTOMATICA'                 => self::GET_TIPORECE_SANGRIA_AUTOMATICA,
        'SQL_GRUPO_OBS_DESC'                              => self::SQL_GRUPO_OBS_DESC,
        'GET_SANGRIA'                                     => self::GET_SANGRIA,
        'SQL_OBRIGA_FECH'                                 => self::SQL_OBRIGA_FECH,
        'DELETA_ITCOMANDAVENDES'                          => self::DELETA_ITCOMANDAVENDES,
        'INSERT_ITVENDADES'                               => self::INSERT_ITVENDADES,
        'SELECT_ITCOMANDAVENDES'                          => self::SELECT_ITCOMANDAVENDES,
        'GET_OBSERVATION_TYPE'                            => self::GET_OBSERVATION_TYPE,
        'GET_PAYMENT_PARAM_DLV'                           => self::GET_PAYMENT_PARAM_DLV,
        'CHANGE_STATUS_COMANDA'                           => self::CHANGE_STATUS_COMANDA,
        'GET_PARAMS_DELIVERY_SALE'                        => self::GET_PARAMS_DELIVERY_SALE,
        'BUSCA_DADOS_ENTREGA'                             => self::BUSCA_DADOS_ENTREGA,
        'BUSCA_ITPEDIDO_ENTREGA'                          => self::BUSCA_ITPEDIDO_ENTREGA,
        'BUSCA_IMPRESSORA_DELIVERY'                       => self::BUSCA_IMPRESSORA_DELIVERY,
        'BUSCA_TIPORECE_DLV'                              => self::BUSCA_TIPORECE_DLV,
        'GET_RETIRA_BALCAO'                               => self::GET_RETIRA_BALCAO,
        'BUSCA_COUVERT_CONSUMA'                           => self::BUSCA_COUVERT_CONSUMA,
        'GET_NRSEQVENDA'                                  => self::GET_NRSEQVENDA,
        'SELECT_ITCOMANDAEST_SPECIFIC'                    => self::SELECT_ITCOMANDAEST_SPECIFIC,
        'BUSCA_VENDA_INUTILIZADA'                         => self::BUSCA_VENDA_INUTILIZADA,
        'SQL_GET_CONSUMER_BALANCE_API'                    => self::SQL_GET_CONSUMER_BALANCE_API,
        'BUSCA_PRODUTO_SUBGRUPO_API'                      => self::BUSCA_PRODUTO_SUBGRUPO_API,
        'BUSCA_DESCONTO_SUBGRUPO_API'                     => self::BUSCA_DESCONTO_SUBGRUPO_API,
        'IMPRIMEDLV_AUTOMATICO'                           => self::IMPRIMEDLV_AUTOMATICO,
        'GET_CDSENHAPED'                                  => self::GET_CDSENHAPED,
        'GET_CONFTELA_CAIXA'                              => self::GET_CONFTELA_CAIXA,
        'CHECK_FOR_CONFTELA'                              => self::CHECK_FOR_CONFTELA,
        'VALIDA_VENDA'                                    => self::VALIDA_VENDA,
        'LOCAL_CERTIFICADO_EXTERNO'                       => self::LOCAL_CERTIFICADO_EXTERNO,
        'SQL_GET_COMANDAS_AGRUPADAS'                      => self::SQL_GET_COMANDAS_AGRUPADAS,
        'INSERT_USOCUPOMDESCFOS'                          => self::INSERT_USOCUPOMDESCFOS,
        'GET_ITENS_EST'                                   => self::GET_ITENS_EST,
        'GET_OBS_EST'                                     => self::GET_OBS_EST,
        'GET_CDTABEPREC_GERAL'                            => self::GET_CDTABEPREC_GERAL,
        'TABELA_VENDA_GERAL'                              => self::TABELA_VENDA_GERAL,
        'ITEM_PRECO_GERAL'                                => self::ITEM_PRECO_GERAL,
        'ITEM_PRECO_DIA_GERAL'                            => self::ITEM_PRECO_DIA_GERAL,
        'VERIFICA_POS_PROD_COMBINADO'                     => self::VERIFICA_POS_PROD_COMBINADO,
        'CAMPANHA_COMPRE_GANHE'                           => self::CAMPANHA_COMPRE_GANHE,
        'PRODUTOS_COMPRE_GANHE'                           => self::PRODUTOS_COMPRE_GANHE
    );

    const BUSCA_DADOS_IMPRESSORA = "
        SELECT P.NRSEQIMPRLOJA, I.IDMODEIMPRES, P.CDPORTAIMPR, L.CDLOJA,
                UPPER(L.NMLOJA) AS NMLOJA, UPPER(C.NMCAIXA) AS NMCAIXA,
                P.DSIPIMPR, P.DSIPPONTE, C.CDCAIXA, C.NMCAIXA, P.NMIMPRLOJA
        FROM CAIXA C JOIN IMPRLOJA P
                        ON C.CDFILIAL = P.CDFILIAL
                      AND C.CDLOJA = P.CDLOJA
                      AND C.NRSEQIMPRLOJA3 = P.NRSEQIMPRLOJA
                      JOIN IMPRESSORA I
                        ON P.CDIMPRESSORA = I.CDIMPRESSORA
                      JOIN LOJA L
                        ON L.CDLOJA = C.CDLOJA
                      AND L.CDFILIAL = C.CDFILIAL
        WHERE C.CDFILIAL = :CDFILIAL
          AND C.CDCAIXA = :CDCAIXA
    ";

    const GET_PROD_SAT = "
        SELECT VE.CDFILIAL, VE.CDCAIXA, VE.CDCLIENTE, VE.CDCONSUMIDOR,
                VE.VRDESCVENDA, VE.NRSEQVENDA, PR.CDBARPRODUTO, PR.CDARVPROD, IT.CDPRODUTO,
                IT.QTPRODVEND, IT.VRUNITVEND, IT.VRACRITVEND, IT.VRUNITVENDCL,
                IT.VRDESITVEND, PR.CDCLASFISC AS CDCLASFISC, AL.VRPEALIMPFIS, AL.IDTPIMPOSFIS,
                AL.VRALIQIBPT, AL.VRALIQIBPTES, AL.CDCFOPPFIS, AL.VRPERCREDUCAO,
                AL.CDCSTICMS, AL.CDCSTPISCOF, AL.VRALIQPIS, AL.VRALIQCOFINS,
                PR.NMPRODUTO, PR.SGUNIDADE, FI.NRINSJURFILI, VE.NRINSCRCONS,
          IP.VRTOTTRIBIBPT, PR.CDCEST, IT.VRRATTXSERV, IT.VRRATDESCVEN,
          LJ.CDPRODTAXASERV
          FROM ITEMVENDA IT, ALIQIMPFIS AL, PRODUTO PR, FILIAL FI,
                VENDA VE, ITVENDAIMPOS IP, LOJA LJ
          WHERE VE.NRSEQVENDA   = :NRSEQVENDA
            AND VE.CDFILIAL     = :CDFILIAL
            AND VE.CDCAIXA      = :CDCAIXA
            AND VE.NRORG        = :NRORG
            AND VE.CDFILIAL     = IT.CDFILIAL
            AND VE.CDCAIXA      = IT.CDCAIXA
            AND VE.NRSEQVENDA   = IT.NRSEQVENDA
            AND AL.CDFILIAL     = IT.CDFILIAL
            AND AL.CDPRODUTO    = IT.CDPRODUTO
            AND IT.CDPRODUTO    = PR.CDPRODUTO
            AND VE.CDFILIAL     = FI.CDFILIAL
            AND IP.CDFILIAL     = VE.CDFILIAL
            AND IP.CDCAIXA      = VE.CDCAIXA
            AND IP.NRSEQUITVEND = IT.NRSEQUITVEND
        AND IP.NRSEQVENDA   = VE.NRSEQVENDA
        AND FI.CDFILIAL     = LJ.CDFILIAL
        AND VE.CDLOJA       = LJ.CDLOJA
    ";

    const GET_PAG_SAT = "
        SELECT A.NMTIPORECE, A.ENTRADA, B.SAIDA, A.IDTIPORECE, A.CDBANCARTCR,
                A.CDADMINCART, A.QTPARCRECEB, A.CDCREDENCCAR
          FROM (SELECT TR.CDTIPORECE, TR.NMTIPORECE, MV.IDTIPOMOVIVE, TR.IDTIPORECE,
                        TR.CDBANCARTCR, TR.CDADMINCART, MV.QTPARCRECEB, SUM(MV.VRMOVIVEND) AS ENTRADA,
                        TR.CDCREDENCCAR
                  FROM MOVCAIXA MV, TIPORECE TR
                  WHERE MV.NRSEQVENDA = :NRSEQVENDA
                    AND MV.CDFILIAL = :CDFILIAL
                    AND MV.CDCAIXA = :CDCAIXA
                    AND MV.NRORG = :NRORG
                    AND MV.CDTIPORECE = TR.CDTIPORECE
                    AND MV.IDTIPOMOVIVE IN ('E','T')
                GROUP BY TR.CDTIPORECE, TR.NMTIPORECE, MV.IDTIPOMOVIVE, TR.IDTIPORECE,
                        TR.CDBANCARTCR, TR.CDADMINCART, MV.QTPARCRECEB, TR.CDCREDENCCAR) A

        LEFT JOIN

                (SELECT TR.CDTIPORECE, TR.NMTIPORECE, MV.IDTIPOMOVIVE, SUM(MV.VRMOVIVEND) AS SAIDA
                  FROM MOVCAIXA MV, TIPORECE TR
                  WHERE MV.NRSEQVENDA = :NRSEQVENDA
                    AND MV.CDFILIAL = :CDFILIAL
                    AND MV.CDCAIXA = :CDCAIXA
                    AND MV.NRORG = :NRORG
                    AND MV.CDTIPORECE = TR.CDTIPORECE
                    AND MV.IDTIPOMOVIVE IN ('S')
                GROUP BY TR.CDTIPORECE, TR.NMTIPORECE, MV.IDTIPOMOVIVE) B

        ON (A.CDTIPORECE = B.CDTIPORECE)
    ";

    const GET_PARAMS_SAT = "
        SELECT PV.CDVINCSAT, CX.CDVINCSATCX, FI.NRINSJURFILI, FI.CDINSCESTA, EM.IDREGESPTRIBEMP, PV.CDPRODTAXSER
          FROM PARAVEND PV, FILIAL FI, EMPRESA EM, CAIXA CX
         WHERE FI.CDFILIAL = :CDFILIAL
           AND FI.NRORG = :NRORG
           AND CX.CDCAIXA = :CDCAIXA
           AND FI.CDFILIAL = CX.CDFILIAL
           AND FI.CDFILIAL = PV.CDFILIAL
           AND FI.CDEMPRESA = EM.CDEMPRESA
    ";

    const GET_HOMOL_SAT = "
        SELECT CDSAT, IDTPEQUSAT, CDCAIXA
          FROM CAIXA
        WHERE CDCAIXA = :CDCAIXA
          AND CDFILIAL = :CDFILIAL
          AND NRORG = :NRORG
    ";

    const GET_CRT_SAT = "
        SELECT P.CDSITUCRT
          FROM FILIAL F, EMPRESA E, PARAMEMPRESA P
        WHERE F.CDEMPRESA = E.CDEMPRESA
          AND E.CDEMPRESA = P.CDEMPRESA
          AND F.CDFILIAL = :CDFILIAL
    ";

    const BUSCA_DADOS_TEF = "
        SELECT CDLOJATEF, CDTERTEF, DSENDIPSITEF, IDTPTEF
          FROM CAIXA
        WHERE CDFILIAL = :CDFILIAL
          AND CDCAIXA = :CDCAIXA
    ";

    const INSERT_MOVCAIXA = "
        INSERT INTO MOVCAIXA
          (CDFILIAL, CDCAIXA, DTABERCAIX, NRSEQUMOVI,
          NRSEQUMOVIMSDE, DTHRINCMOV, IDTIPOMOVIVE, VRMOVIVEND,
          NRSEQVENDA, NRORG, NRORGINCLUSAO, CDOPERINCLUSAO,
          CDTIPORECE, DTMOVIMCAIXA, CDTPSANGRIA, DSOBSSANGRIACX)
        VALUES
          (:CDFILIAL, :CDCAIXA, :DTABERCAIX, :NRSEQUMOVI,
          :NRSEQUMOVIMSDE, :DTHRINCMOV, :IDTIPOMOVIVE, :VRMOVIVEND,
          :NRSEQVENDA, :NRORG, :NRORGINCLUSAO, :CDOPERINCLUSAO,
          :CDTIPORECE, :DTMOVIMCAIXA, :CDTPSANGRIA, :DSOBSSANGRIACX)
    ";

    const BUSCA_DADOS_OPERADOR = "
        SELECT CDOPERADOR, NMOPERADOR
          FROM OPERADOR
        WHERE CDOPERADOR = :CDOPERADOR
    ";

    const BUSCA_DADOS_RECEBIMENTO = "
        SELECT CDTIPORECE, NMTIPORECE
          FROM TIPORECE
        WHERE CDTIPORECE = :CDTIPORECE
    ";

    const INSERT_TURCAIXA = "
        INSERT INTO TURCAIXA
          (CDFILIAL, CDCAIXA, DTABERCAIX, CDOPERADOR,
          CDOPERABER, DTMOVTURCAIX, CDATIVACAOSAT, NRORG, IDATUTURCAIXA)
        VALUES
          (:CDFILIAL, :CDCAIXA, :DTABERCAIX, :CDOPERADOR,
          :CDOPERADOR, :DTMOVTURCAIX, :CDATIVACAOSAT, :NRORG, :IDATUTURCAIXA)
    ";

    const INSERT_MOVCAIXA_OPENPOS = "
        INSERT INTO MOVCAIXA
        (CDFILIAL, CDCAIXA, DTABERCAIX, NRSEQUMOVI,
        NRSEQUMOVIMSDE, DTHRINCMOV, IDTIPOMOVIVE, VRMOVIVEND,
        CDTIPORECE, DTMOVIMCAIXA, NRORG)
        VALUES
        (:CDFILIAL, :CDCAIXA, :DTABERCAIX, :NRSEQUMOVI,
        :NRSEQUMOVIMSDE, :DTHRINCMOV, :IDTIPOMOVIVE, :VRMOVIVEND,
        :CDTIPORECE, CONVERT(DATE, :DTMOVIMCAIXA, 103), :NRORG)
    ";

    const UPDATE_DTFECHAMENTO_TURCAIXA = "
        UPDATE TURCAIXA
          SET DTFECHCAIX = :DTFECHCAIX,
              CDOPERFECH = :CDOPERFECH,
              CDOPERULTATU = :CDOPERFECH,
              NRORGULTATU = :NRORG,
              IDATUTURCAIXA = :IDATUTURCAIXA
        WHERE CDFILIAL = :CDFILIAL
          AND DTFECHCAIX IS NULL
          AND CDCAIXA = :CDCAIXA
          AND NRORG = :NRORG
    ";

    const VERIFICA_TIPO_EMISSAO_CAIXA = "
        SELECT P.NRSEQIMPRLOJA, I.IDMODEIMPRES, P.CDPORTAIMPR, L.CDLOJA, UPPER(L.NMLOJA) AS NMLOJA, UPPER(C.NMCAIXA) AS NMCAIXA,
               C.CDCODATIVASAT AS CDATIVASAT, C.DSSATHOST, C.IDTPEMISSAOFOS, C.CDSAT, P.DSIPIMPR, P.DSIPPONTE, P.NMIMPRLOJA
          FROM CAIXA C LEFT JOIN IMPRLOJA P
                         ON C.CDFILIAL = P.CDFILIAL
                        AND C.CDLOJA = P.CDLOJA
                        AND C.NRSEQIMPRLOJA3 = P.NRSEQIMPRLOJA
                       LEFT JOIN IMPRESSORA I
                         ON P.CDIMPRESSORA = I.CDIMPRESSORA
                       JOIN LOJA L
                         ON L.CDLOJA = C.CDLOJA
                        AND L.CDFILIAL = C.CDFILIAL
         WHERE C.CDFILIAL = :CDFILIAL
           AND C.CDCAIXA = :CDCAIXA
    ";

    const GET_NFCE_DATA = "
        SELECT F.NRINSJURFILI, F.NRORG, F.NMRAZSOCFILI, EF.SGESTADO, PV.NRCERTDIGNFCE, PV.CDIDTOKENPROD,
          PV.NMARQCERTNFCE, PV.DSSENHACERTNFCE, PV.CDCODSCONSPROD, PV.IDAMBTRABNFCE, PV.CDCODSCONSHOMO,
          PV.CDIDTOKENHOMO, F.CDINSCESTA, PV.CDURLWSNFC
          FROM FILIAL F, ENDEFILI EF, PARAVEND PV
          WHERE F.CDFILIAL = :CDFILIAL
          AND EF.IDTPENDEFILI = 'P'
          AND F.CDFILIAL = EF.CDFILIAL
          AND F.CDFILIAL = PV.CDFILIAL
    ";

    const VERIFICA_ABERTURA_ESTADO_CAIXA = "
        SELECT T.DTABERCAIX, T.DTFECHCAIX, T.IDATUTURCAIXA, T.CDOPERADOR, O.NMOPERADOR
          FROM TURCAIXA T, OPERADOR O
          WHERE T.CDFILIAL = :CDFILIAL
            AND T.CDCAIXA = :CDCAIXA
            AND T.DTFECHCAIX IS NULL
            AND T.CDOPERADOR = O.CDOPERADOR
    ";

    const VERIFICA_FECHAMENTO_CAIXA = "
        SELECT DTMOVTURCAIX
          FROM TURCAIXA
         WHERE CDFILIAL = :CDFILIAL
           AND CDCAIXA = :CDCAIXA
           AND DTFECHCAIX IS NULL
           AND DTMOVTURCAIX = CONVERT(DATE, GETDATE())
    ";

    const GET_NMOPERADOR = "
        SELECT NMOPERADOR
          FROM OPERADOR
          WHERE CDOPERADOR = :CDOPERADOR
    ";

    const GET_VRVENDBRUT ="
        SELECT SUM(ISNULL(A.VENDAS,0)) AS VRVENDBRUT
          FROM (SELECT I.CDFILIAL, I.CDCAIXA, I.NRSEQVENDA,
                      SUM(ROUND(I.QTPRODVEND * (I.VRUNITVEND + ISNULL(I.VRUNITVENDCL,0)),2,1) + ISNULL(I.VRACRITVEND,0)) AS VENDAS
                  FROM ITEMVENDA I JOIN VENDA V ON I.CDFILIAL   = V.CDFILIAL
                                              AND I.CDCAIXA    = V.CDCAIXA
                                              AND I.NRSEQVENDA = V.NRSEQVENDA
        WHERE I.CDFILIAL = :CDFILIAL
          AND I.CDCAIXA = :CDCAIXA
          AND ((:FINAL = 1 AND CONVERT(DATE, V.DTABERTUR, 103) = CONVERT(DATE, :DTABERCAIX, 103))
            OR (:FINAL <> 1 AND V.DTABERTUR = :DTABERCAIX))
          AND V.IDSITUVENDA IN ('O')
        GROUP BY I.CDFILIAL, I.CDCAIXA, I.NRSEQVENDA) A
    ";

    const GET_VRTOTINI = "
        SELECT SUM( CASE WHEN A.VENDAS IS NULL THEN 0 ELSE A.VENDAS END + CASE WHEN B.VENDAS IS NULL THEN 0 ELSE B.VENDAS END ) AS VRTOTINI
          FROM (SELECT I.CDFILIAL, I.CDCAIXA, I.NRSEQVENDA, SUM(ROUND(QTPRODVEND*(VRUNITVEND + CASE WHEN VRUNITVENDCL IS NULL THEN 0 ELSE VRUNITVENDCL END ),2)+ CASE WHEN VRACRITVEND IS NULL THEN 0 ELSE VRACRITVEND END) AS VENDAS
                  FROM ITEMVENDA I JOIN VENDA V ON I.CDFILIAL   = V.CDFILIAL
                                                AND I.CDCAIXA    = V.CDCAIXA
                                                AND I.NRSEQVENDA = V.NRSEQVENDA
                  WHERE I.CDFILIAL   = :CDFILIAL
                    AND I.CDCAIXA    = :CDCAIXA
                    AND V.IDSITUVENDA IN ('O','C')
            GROUP BY I.CDFILIAL, I.CDCAIXA, I.NRSEQVENDA) A
        LEFT JOIN
                (SELECT I.CDFILIAL, I.CDCAIXA, I.NRSEQVENDA, SUM(ROUND(QTPRODVENDC*(VRUNITVENDC+ CASE WHEN VRUNITVENCLC IS NULL THEN 0 ELSE VRUNITVENCLC END ),2)+ CASE WHEN VRACRITVENDC IS NULL THEN 0 ELSE VRACRITVENDC END) AS VENDAS
                  FROM ITVENDACAN I JOIN VENDA V  ON I.CDFILIAL   = V.CDFILIAL
                              AND I.CDCAIXA    = V.CDCAIXA
                              AND I.NRSEQVENDA = V.NRSEQVENDA
                  WHERE I.CDFILIAL   = :CDFILIAL
                    AND I.CDCAIXA    = :CDCAIXA
              GROUP BY I.CDFILIAL, I.CDCAIXA, I.NRSEQVENDA) B
            ON A.NRSEQVENDA = B.NRSEQVENDA
            AND A.CDFILIAL   = B.CDFILIAL
            AND A.CDCAIXA    = B.CDCAIXA
    ";

    const GET_NRTRANSACOES = "
        SELECT COUNT(DISTINCT(V.NRSEQVENDA)) AS NRTRANSACOES
          FROM VENDA V
        WHERE (V.CDFILIAL = :CDFILIAL)
          AND (V.CDCAIXA = :CDCAIXA)
          AND ((:FINAL = 1 AND CONVERT(DATE, V.DTABERTUR, 103) = CONVERT(DATE, :DTABERCAIX, 103))
            OR (:FINAL <> 1 AND V.DTABERTUR = :DTABERCAIX))
          AND (V.IDSITUVENDA = 'O')
    ";

    const GET_NRPRIMSEQ = "
        SELECT MIN(ISNULL(NRNOTAFISCALCE, 0)) AS NRPRIMSEQ
          FROM VENDA
        WHERE CDFILIAL = :CDFILIAL
          AND CDCAIXA = :CDCAIXA
          AND ((:FINAL = 1 AND CONVERT(DATE, DTABERTUR, 103) = CONVERT(DATE, :DTABERCAIX, 103))
            OR (:FINAL <> 1 AND DTABERTUR = :DTABERCAIX))
    ";

    const GET_NRFINALSEQ = "
        SELECT MAX(ISNULL(NRNOTAFISCALCE, 0)) AS NRFINALSEQ
          FROM VENDA
        WHERE CDFILIAL = :CDFILIAL
          AND CDCAIXA = :CDCAIXA
          AND ((:FINAL = 1 AND CONVERT(DATE, DTABERTUR, 103) = CONVERT(DATE, :DTABERCAIX, 103))
            OR (:FINAL <> 1 AND DTABERTUR = :DTABERCAIX))
    ";

    const GET_CANCELAMENTOS = "
        SELECT SUM(CAN.TRANSCANC) AS TRANSCANC, SUM(CAN.VRCANCEL) AS VRCANCEL
          FROM (
            SELECT COUNT(DISTINCT(V.NRSEQVENDA)) AS TRANSCANC,
                ISNULL(SUM(ROUND(I.QTPRODVEND*(I.VRUNITVEND+I.VRUNITVENDCL)+ISNULL(I.VRACRITVEND,0)- ISNULL(I.VRDESITVEND,0),2,1)),0) AS VRCANCEL
              FROM ITEMVENDA I
            JOIN VENDA V
              ON I.CDFILIAL = V.CDFILIAL
              AND I.CDCAIXA = V.CDCAIXA
              AND I.NRSEQVENDA = V.NRSEQVENDA
            WHERE V.CDFILIAL = :CDFILIAL
              AND V.CDCAIXA = :CDCAIXA
              AND ((:FINAL = 1 AND CONVERT(DATE, V.DTABERTUR, 103) = CONVERT(DATE, :DTABERCAIX, 103))
                OR (:FINAL <> 1 AND V.DTABERTUR = :DTABERCAIX))
              AND V.IDSITUVENDA = 'C'
            UNION ALL
            SELECT COUNT(DISTINCT(V.NRSEQVENDA)) AS TRANSCANC,
                ISNULL(SUM(ROUND(I.QTPRODVENDC*(I.VRUNITVENDC+I.VRUNITVENCLC)+ISNULL(I.VRACRITVENDC,0),2,1)),0) AS VRCANCEL
              FROM VENDA V
            JOIN ITVENDACAN I
              ON V.CDFILIAL = I.CDFILIAL
              AND V.CDCAIXA = I.CDCAIXA
              AND V.NRSEQVENDA = I.NRSEQVENDA
            WHERE V.CDFILIAL = :CDFILIAL
              AND V.CDCAIXA = :CDCAIXA
              AND ((:FINAL = 1 AND CONVERT(DATE, V.DTABERTUR, 103) = CONVERT(DATE, :DTABERCAIX, 103))
                OR (:FINAL <> 1 AND V.DTABERTUR = :DTABERCAIX))) CAN
    ";

    const GET_VRDESITVEND = "
        SELECT SUM(ISNULL(I.VRDESITVEND, 0)) AS VRDESITVEND
          FROM ITEMVENDA I
        JOIN VENDA V
          ON I.CDFILIAL = V.CDFILIAL
          AND I.CDCAIXA = V.CDCAIXA
          AND I.NRSEQVENDA = V.NRSEQVENDA
        WHERE V.CDFILIAL = :CDFILIAL
          AND V.CDCAIXA = :CDCAIXA
          AND ((:FINAL = 1 AND CONVERT(DATE, V.DTABERTUR, 103) = CONVERT(DATE, :DTABERCAIX, 103))
            OR (:FINAL <> 1 AND V.DTABERTUR = :DTABERCAIX))
          AND V.IDSITUVENDA = 'O'
    ";

    const GET_VRACRITVEND = "
        SELECT SUM(ISNULL(I.VRACRITVEND,0)) AS VRACRITVEND
          FROM ITEMVENDA I
        JOIN VENDA V
          ON I.CDFILIAL = V.CDFILIAL
          AND I.CDCAIXA = V.CDCAIXA
          AND I.NRSEQVENDA = V.NRSEQVENDA
        WHERE V.CDFILIAL = :CDFILIAL
          AND V.CDCAIXA = :CDCAIXA
          AND ((:FINAL = 1 AND CONVERT(DATE, V.DTABERTUR, 103) = CONVERT(DATE, :DTABERCAIX, 103))
          OR (:FINAL <> 1 AND V.DTABERTUR = :DTABERCAIX))
          AND V.IDSITUVENDA = 'O'
    ";

    const GET_VRMOVIVEND = "
        SELECT SUM(VRMOVIVEND) AS VRMOVIVEND
          FROM MOVCAIXA
        WHERE CDFILIAL = :CDFILIAL
          AND CDCAIXA = :CDCAIXA
          AND ((:FINAL = 1 AND CONVERT(DATE, DTABERCAIX, 103) = CONVERT(DATE, :DTABERCAIX, 103))
            OR (:FINAL <> 1 AND DTABERCAIX = :DTABERCAIX))
          AND IDTIPOMOVIVE = 'A'
    ";

    const GET_IMPOSTOS = "
        SELECT SGIMPOSTO, P.VRPEALPRODIT,
          SUM((QTPRODVEND*VRUNITVEND)-ISNULL(VRDESITVEND, 0)+ISNULL(VRACRITVEND, 0)) AS VRBASE,
          SUM(((QTPRODVEND*VRUNITVEND)-ISNULL(VRDESITVEND, 0)+ISNULL(VRACRITVEND, 0))/100*P.VRPEALPRODIT) AS VRIMPOSTO
        FROM ITEMVENDA I JOIN VENDA V
                        ON I.CDFILIAL   = V.CDFILIAL
                      AND I.CDCAIXA    = V.CDCAIXA
                      AND I.NRSEQVENDA = V.NRSEQVENDA
                      JOIN ITVENDAIMPOS P
                        ON I.CDFILIAL     = P.CDFILIAL
                      AND I.CDCAIXA      = P.CDCAIXA
                      AND I.NRSEQVENDA   = P.NRSEQVENDA
                      AND I.NRSEQUITVEND = P.NRSEQUITVEND
                      JOIN IMPOSTO IM
                        ON P.CDIMPOSTO = IM.CDIMPOSTO
      WHERE I.CDFILIAL = :CDFILIAL
        AND I.CDCAIXA  = :CDCAIXA
        AND (V.DTENTRVENDA = CONVERT(DATE, :DTABERCAIX, 103))
        AND V.IDSITUVENDA = 'O'
      GROUP BY SGIMPOSTO, P.VRPEALPRODIT
    ";

    const GET_PAGAMENTOS = "
        SELECT RC.CDTIPORECE, RC.NMTIPORECE, RC.VRMOVIVEND AS VALOR_TOTAL, RC.VRTROCO
            FROM (SELECT T.CDTIPORECE, T.NMTIPORECE,
                            SUM(ISNULL(S.VRMOVIVEND,0)) AS VRTROCO,
                            SUM(ISNULL(E.VRMOVIVEND,0) - ISNULL(S.VRMOVIVEND,0)) AS VRMOVIVEND
                        FROM TIPORECE T
                        JOIN (SELECT CDFILIAL, CDCAIXA, DTABERCAIX, NRSEQVENDA,
                                                CDTIPORECE, SUM(ISNULL(VRMOVIVEND,0)) AS VRMOVIVEND
                                          FROM MOVCAIXA
                                          WHERE CDFILIAL  = :CDFILIAL
                                            AND CDCAIXA   = :CDCAIXA
                                            AND ((:FINAL = 1 AND CONVERT(DATE, DTABERCAIX, 103) = CONVERT(DATE, :DTABERCAIX, 103))
                                              OR (:FINAL <> 1 AND DTABERCAIX = :DTABERCAIX))
                                            AND IDTIPOMOVIVE IN ('E','T')
                                          GROUP BY CDFILIAL, CDCAIXA, DTABERCAIX, CDTIPORECE, NRSEQVENDA) E
                          ON T.CDTIPORECE  = E.CDTIPORECE
                  LEFT JOIN
                            (SELECT CDFILIAL, CDCAIXA, DTABERCAIX, NRSEQVENDA,
                                    CDTIPORECE, SUM(ISNULL(VRMOVIVEND,0)) AS VRMOVIVEND
                                FROM MOVCAIXA
                              WHERE CDFILIAL = :CDFILIAL
                                AND CDCAIXA  = :CDCAIXA
                                AND ((:FINAL = 1 AND CONVERT(DATE, DTABERCAIX, 103) = CONVERT(DATE, :DTABERCAIX, 103))
                                  OR (:FINAL <> 1 AND DTABERCAIX = :DTABERCAIX))
                                AND IDTIPOMOVIVE = 'S'
                            GROUP BY CDFILIAL, CDCAIXA, DTABERCAIX, CDTIPORECE, NRSEQVENDA) S
                          ON E.CDTIPORECE = S.CDTIPORECE
                        AND E.NRSEQVENDA = S.NRSEQVENDA
          WHERE E.CDFILIAL    = :CDFILIAL
            AND E.CDCAIXA     = :CDCAIXA
        GROUP BY T.CDTIPORECE, T.NMTIPORECE) RC
    ";

    const TOTAL_VENDAS = "
        SELECT
            PR.CDARVPROD AS CDPRODUTO, IT.VRUNITVEND,IT.VRUNITVENDCL,
            ISNULL(PR.NMPRODIMPFIS,PR.NMPRODUTO) AS NMPRODUTO,
            AL.VRPEALIMPFIS, IM.IDTPIMPOSFIS,
            SUM(QTPRODVEND) AS QTPRODVEND,
            SUM(ROUND((IT.QTPRODVEND * (IT.VRUNITVEND + ISNULL(IT.VRUNITVENDCL,0))),2,1)) AS VRPRODUTO,
            SUM(ROUND((IT.QTPRODVEND * (IT.VRUNITVEND + ISNULL(IT.VRUNITVENDCL,0))),2,1) - ISNULL(IT.VRDESITVEND ,0) + ISNULL(IT.VRACRITVEND,0)) AS VRTOTAL,
            SUM(VRACRITVEND) AS VRACRE, SUM(VRDESITVEND) AS VRDESC, CDPRODIMPFIS
        FROM
            LOJA LO, VENDA VE, PRODUTO PR, ITEMVENDA IT, ALIQIMPFIS AL, IMPOSTO IM
        WHERE
                (VE.CDFILIALTUR = :P_CDFILIAL  )
            AND (VE.CDCAIXATUR  = :P_CDCAIXA   )
            AND (VE.DTABERTUR   = :P_DTABERCAIX)
            AND (VE.CDFILIALTUR = IT.CDFILIAL  )
            AND (VE.CDCAIXATUR  = IT.CDCAIXA   )
            AND (VE.NRSEQVENDA  = IT.NRSEQVENDA)
            AND (IT.CDPRODUTO   = PR.CDPRODUTO )
            AND (VE.CDFILIAL    = AL.CDFILIAL  )
            AND (IT.CDPRODUTO   = AL.CDPRODUTO )
            AND (IM.CDIMPOSTO   = AL.CDIMPOSTO )
            AND (LO.CDFILIAL    = VE.CDFILIAL  )
            AND (LO.CDLOJA      = VE.CDLOJA    )
            AND (VE.IDSITUVENDA = 'O')
        GROUP BY
            PR.CDARVPROD, IT.VRUNITVEND, IT.VRUNITVENDCL ,ISNULL(PR.NMPRODIMPFIS,PR.NMPRODUTO), AL.VRPEALIMPFIS, IM.IDTPIMPOSFIS, CDPRODIMPFIS
    ";

    const TOTAL_CREDITO = "
        SELECT SUM(ROUND(E.VRMOVEXTCONS,2)) AS VRCREDITO
          FROM EXTRATOCONS E
        WHERE (E.CDFILIAL = :CDFILIAL)
          AND (E.CDCAIXA = :CDCAIXA)
          AND (E.IDTPMOVEXT = 'C')
          AND ((:FINAL = 1 AND CONVERT(DATE, E.DTABERCAIX, 103) = CONVERT(DATE, :DTABERCAIX, 103))
            OR (:FINAL <> 1 AND E.DTABERCAIX = :DTABERCAIX))
    ";

    const FORMA_PAGAMENTO = "
        SELECT CASE WHEN SUM(M.VRMOVIVEND) IS NULL THEN 0 ELSE SUM(M.VRMOVIVEND) END AS VRSAIDA,
            T.CDTIPORECE, T.NMTIPORECE, '1' AS IDTIPOREG, T.IDTIPORECE, M.NRENVELOPE
        FROM TIPORECE T, MOVCAIXA M
        WHERE (M.IDTIPOMOVIVE = 'G') AND
            (T.IDTIPORECE   <> '7') AND
            (M.CDFILIAL     = :P_CDFILIAL ) AND
            (M.CDCAIXA      = :P_CDCAIXA ) AND
            (M.DTABERCAIX   = :P_DTABERCAIX) AND
            (M.CDTIPORECE   = T.CDTIPORECE)
        GROUP BY T.CDTIPORECE, T.NMTIPORECE, T.IDTIPORECE, M.NRENVELOPE

        UNION ALL
        SELECT 0 AS VRSAIDA, T.CDTIPORECE, T.NMTIPORECE, '5' AS IDTIPOREG, T.IDTIPORECE, '' AS NRENVELOPE
        FROM TIPORECE T, ITMENUCONFTE I
        WHERE T.CDTIPORECE NOT IN (SELECT M.CDTIPORECE
                                  FROM MOVCAIXA M
                                  WHERE M.IDTIPOMOVIVE = 'E' AND
                                        T.IDTIPORECE   <> '7' AND
                                        M.CDFILIAL     = :P_CDFILIAL AND
                                        M.CDCAIXA      = :P_CDCAIXA AND
                                        M.DTABERCAIX   = :P_DTABERCAIX AND
                                        M.CDTIPORECE   = T.CDTIPORECE) AND
                                        (T.IDMOSTRARECE = 'S') AND
                                        (T.IDTIPORECE <> '7') AND
                                        (T.CDTIPORECE = I.CDIDENTBUTON) AND
                                        (I.IDTPBUTONAUX = '6') AND
                                        (I.NRCONFTELA = :P_NRCONFTELA)
        GROUP BY T.CDTIPORECE, T.NMTIPORECE, T.IDTIPORECE

        UNION ALL
        SELECT CASE WHEN SUM(M.VRMOVIVEND) IS NULL THEN 0 ELSE SUM(M.VRMOVIVEND) END AS VRSAIDA, T.CDTIPORECE, T.NMTIPORECE, '4' AS IDTIPOREG, T.IDTIPORECE, M.NRENVELOPE
        FROM TIPORECE T, MOVCAIXA M
        WHERE (M.IDTIPOMOVIVE = 'G') AND
            (T.IDTIPORECE   = '7') AND
            (M.CDFILIAL     = :P_CDFILIAL ) AND
            (M.CDCAIXA      = :P_CDCAIXA ) AND
            (M.DTABERCAIX   = :P_DTABERCAIX) AND
            (M.CDTIPORECE   = T.CDTIPORECE)
        GROUP BY T.CDTIPORECE, T.NMTIPORECE, T.IDTIPORECE, M.NRENVELOPE

        UNION ALL
        SELECT CASE WHEN SUM(M.VRMOVIVEND) IS NULL THEN 0 ELSE SUM(M.VRMOVIVEND) END AS VRSAIDA,
            T.CDTIPORECE, 'Contra Vale Emitido' AS NMTIPORECE, '2' AS IDTIPOREG, T.IDTIPORECE, M.NRENVELOPE
        FROM MOVCAIXA M, TIPORECE T
        WHERE (M.CDFILIAL     = :P_CDFILIAL )  AND
            (M.CDCAIXA      = :P_CDCAIXA )   AND
            (M.DTABERCAIX   = :P_DTABERCAIX) AND
            (M.CDTIPORECE   = T.CDTIPORECE)  AND
            (T.IDTIPORECE   = '7'         )  AND
            (M.IDTIPOMOVIVE = 'S'         )
        GROUP BY T.CDTIPORECE, T.NMTIPORECE, T.IDTIPORECE, M.NRENVELOPE

        UNION ALL
        SELECT CASE WHEN SUM(M.VRMOVIVEND) IS NULL THEN 0 ELSE SUM(M.VRMOVIVEND) END AS VRSAIDA,
            T.CDTIPORECE, 'Suprimento' AS NMTIPORECE, '3' AS IDTIPOREG, T.IDTIPORECE, M.NRENVELOPE
        FROM MOVCAIXA M, TIPORECE T
        WHERE (M.CDFILIAL     = :P_CDFILIAL ) AND
            (M.CDCAIXA      = :P_CDCAIXA ) AND
            (M.DTABERCAIX   = :P_DTABERCAIX) AND
            (M.CDTIPORECE   = T.CDTIPORECE) AND
            (M.IDTIPOMOVIVE = 'U'         )
        GROUP BY T.CDTIPORECE, T.NMTIPORECE, T.IDTIPORECE, M.NRENVELOPE
    ";

    const GET_SANGRIA_AUTOMATICA = "
        SELECT A.CDTIPORECE, A.NMTIPORECE, A.IDTIPORECE,
          CASE WHEN A.VRMOVIVEND IS NULL THEN 0 ELSE A.VRMOVIVEND END -  CASE WHEN B.VRMOVIVEND IS NULL THEN 0 ELSE B.VRMOVIVEND END AS VRMOVIVEND
        FROM (SELECT
            T.CDTIPORECE, T.NMTIPORECE, T.IDTIPORECE, SUM(M.VRMOVIVEND) AS VRMOVIVEND
            FROM
            MOVCAIXA M, TIPORECE T, ITMENUCONFTE I
            WHERE M.CDFILIAL      = :CDFILIAL
            AND M.CDCAIXA       = :CDCAIXA
            AND (M.IDTIPOMOVIVE  IN ('E','T','U'))
            AND M.DTABERCAIX    = :DTABERCAIX
            AND M.CDTIPORECE    = T.CDTIPORECE
            AND T.IDSANGRIAAUTO = 'S'
            AND M.CDFILIAL      = I.CDFILIAL
            AND M.CDTIPORECE    = I.CDIDENTBUTON
            AND I.NRCONFTELA    = :NRCONFTELA
            AND I.DTINIVIGENCIA = :DTINIVIGENCIA
            AND T.IDTIPORECE    <> '7'
            AND T.IDTIPORECE    <> '5'
            GROUP BY
            T.CDTIPORECE, T.NMTIPORECE, T.IDTIPORECE) A LEFT JOIN
            (SELECT
            T.CDTIPORECE, T.NMTIPORECE, T.IDTIPORECE, SUM(M.VRMOVIVEND) AS VRMOVIVEND
            FROM
            MOVCAIXA M, TIPORECE T, ITMENUCONFTE I
            WHERE M.CDFILIAL      = :CDFILIAL
            AND M.CDCAIXA       = :CDCAIXA
            AND M.IDTIPOMOVIVE  = 'S'
            AND M.DTABERCAIX    = :DTABERCAIX
            AND M.CDTIPORECE    = T.CDTIPORECE
            AND T.IDSANGRIAAUTO = 'S'
            AND M.CDTIPORECE    = I.CDIDENTBUTON
            AND I.CDFILIAL      = :FILIALVIGENCIA
            AND I.NRCONFTELA    = :NRCONFTELA
            AND I.DTINIVIGENCIA = :DTINIVIGENCIA
            AND T.IDTIPORECE    <> '7'
            AND T.IDTIPORECE    <> '5'
            GROUP BY
            T.CDTIPORECE, T.NMTIPORECE, T.IDTIPORECE) B
            ON (A.CDTIPORECE = B.CDTIPORECE)
            UNION ALL
            SELECT
            T.CDTIPORECE, T.NMTIPORECE, T.IDTIPORECE, SUM(M.VRMOVIVEND) AS VRMOVIVEND
            FROM
            MOVCAIXA M, TIPORECE T, ITMENUCONFTE I
            WHERE M.CDFILIAL      = :CDFILIAL
            AND M.CDCAIXA       = :CDCAIXA
            AND M.IDTIPOMOVIVE  = 'E'
            AND M.DTABERCAIX    = :DTABERCAIX
            AND M.CDTIPORECE    = T.CDTIPORECE
            AND T.IDSANGRIAAUTO = 'S'
            AND M.CDTIPORECE    = I.CDIDENTBUTON
            AND I.CDFILIAL      = :FILIALVIGENCIA
            AND I.NRCONFTELA    = :NRCONFTELA
            AND I.DTINIVIGENCIA = :DTINIVIGENCIA
            AND ((T.IDTIPORECE   = '7') OR (T.IDTIPORECE = '5'))
        GROUP BY T.CDTIPORECE, T.NMTIPORECE, T.IDTIPORECE
    ";

    const GET_TIPORECE_SANGRIA_AUTOMATICA = "
      SELECT M.CDTIPORECE, T.NMTIPORECE, SUM(M.VRMOVIVEND) AS VRMOVIVEND,
      SUM(M.VRMOVIVEND) AS LABELVRMOVIVEND, T.IDSANGRIAAUTO
      FROM MOVCAIXA M
      JOIN TIPORECE T ON T.CDTIPORECE = M.CDTIPORECE
      WHERE M.CDFILIAL   = :CDFILIAL
      AND M.CDCAIXA    = :CDCAIXA
      AND M.IDTIPOMOVIVE IN ('E','T','U')
      AND T.IDSANGRIAAUTO = 'S'
      AND M.DTABERCAIX = :DTABERCAIX
      GROUP BY M.CDTIPORECE, T.NMTIPORECE, T.IDSANGRIAAUTO
    ";

    const BUSCA_CDATIVASAT = "
      SELECT CDCODATIVASAT AS CDATIVASAT
        FROM CAIXA
        WHERE CDFILIAL = :CDFILIAL
          AND CDCAIXA = :CDCAIXA
          AND NRORG = :NRORG
    ";

    const GET_CLIENTS = "
      SELECT PA.CDCLIENTE, PA.CDFILIAL,
      CASE CL.IDEXBTPNOMECLIE WHEN 'F' THEN CL.NMFANTCLIE ELSE CL.NMRAZSOCCLIE END AS NMFANTCLIE
        FROM CLIENTE CL, PARAVEND PA, CLIENFILIAL CF
      WHERE PA.CDCLIENTE = CL.CDCLIENTE
        AND CL.CDCLIENTE = PA.CDCLIENTE
        AND PA.CDFILIAL = CF.CDFILIAL
        AND PA.CDCLIENTE = CF.CDCLIENTE
        AND CF.IDPERMVENDAFOS = 'S'
        AND PA.IDFILUTSITE = 'S'
      ORDER BY CL.NMFANTCLIE, CL.NMRAZSOCCLIE
    ";

    const BUSCA_TIPO_RECE = "
          SELECT CDTIPORECE
            FROM TIPORECE
          WHERE CDBANCARTCR = :CDBANCARTCR
            AND IDTIPORECE = :IDTIPORECE
      ";

    const BUSCA_DADOS_IMPRESSORA_SITEF = "
        SELECT P.NRSEQIMPRLOJA, I.IDMODEIMPRES, P.CDPORTAIMPR, P.DSIPIMPR,
                P.DSIPPONTE, P.NMIMPRLOJA
          FROM CAIXA C JOIN IMPRLOJA P
                          ON C.CDFILIAL = P.CDFILIAL
                        AND C.CDLOJA = P.CDLOJA
                        AND C.NRSEQIMPRLOJA3 = P.NRSEQIMPRLOJA
                        JOIN IMPRESSORA I
                          ON P.CDIMPRESSORA = I.CDIMPRESSORA
          WHERE C.CDFILIAL = :CDFILIAL
            AND C.CDCAIXA = :CDCAIXA
            AND C.NRORG = :NRORG
    ";

    const ENDERECO_LOJA = "
        SELECT FL.NMFILIAL, MP.NMMUNICIPIO, EF.DSENDEFILI, EF.NMBAIRFILI,
            EF.SGESTADO, EF.NRCEPFILI, EF.NRTELEFILI
          FROM ENDEFILI EF
              JOIN MUNICIPIO MP ON MP.CDMUNICIPIO = EF.CDMUNICIPIO
            JOIN FILIAL FL ON FL.CDFILIAL = EF.CDFILIAL
          WHERE EF.CDFILIAL = :CDFILIAL
              AND EF.IDTPENDEFILI = 'P'
    ";

    const INSERT_PRODUCT = "
      INSERT INTO ITCOMANDAVEN
        (CDFILIAL, NRVENDAREST, NRCOMANDA, NRPRODCOMVEN,
        CDPRODUTO, QTPRODCOMVEN, VRPRECCOMVEN, TXPRODCOMVEN,
        IDSTPRCOMVEN, VRDESCCOMVEN, NRLUGARMESA, DTHRINCOMVEN,
        IDPRODIMPFIS, CDLOJA, VRACRCOMVEN, NRSEQPRODCOM, NRSEQPRODCUP,
        VRPRECCLCOMVEN, CDCAIXACOLETOR, CDPRODPROMOCAO, CDVENDEDOR, DSOBSPEDDIGCMD)
      VALUES
        (:CDFILIAL, :NRVENDAREST, :NRCOMANDA, :NRPRODCOMVEN,
        :CDPRODUTO, :QTPRODCOMVEN, :VRPRECCOMVEN, :TXPRODCOMVEN,
        :IDSTPRCOMVEN, :VRDESCCOMVEN, :NRLUGARMESA, SYSDATE,
        :IDPRODIMPFIS, :CDLOJA, :VRACRCOMVEN, :NRSEQPRODCOM, :NRSEQPRODCUP,
        :VRPRECCLCOMVEN, :CDCAIXACOLETOR, :CDPRODPROMOCAO, :CDVENDEDOR, :DSOBSPEDDIGCMD)
    ";

    const INSERT_SMARTPROMO = "
      INSERT INTO ITCOMANDAEST(
        CDFILIAL, NRVENDAREST, NRCOMANDA, NRPRODCOMVEN,
        CDPRODUTO, QTPROCOMEST, VRPRECCOMEST, VRDESITCOMEST,
        TXPRODCOMVENEST
      )
      VALUES(
        :CDFILIAL, :NRVENDAREST, :NRCOMANDA, :NRPRODCOMVEN,
        :CDPRODUTO, :QTPROCOMEST, :VRPRECCOMEST, :VRDESITCOMEST,
        :TXPRODCOMVENEST
      )
    ";

    const GET_SECTOR_CODE = "
      SELECT CDSETOR
        FROM PRODLOJA
      WHERE CDFILIAL  = :CDFILIAL
        AND CDLOJA    = :CDLOJA
        AND CDPRODUTO = :CDPRODUTO
    ";

    const GET_NRSEQPRODCOM = "
      SELECT CASE WHEN MAX(NRSEQPRODCOM) IS NULL THEN '0' ELSE MAX(NRSEQPRODCOM) END AS NRSEQPRODCOM
        FROM ITCOMANDAVEN
      WHERE CDFILIAL    = :CDFILIAL
        AND NRVENDAREST = :NRVENDAREST
        AND NRCOMANDA   = :NRCOMANDA
        AND NRSEQPRODCOM IS NOT NULL
    ";

    const GET_PRODUCT_DETAILS = "
      SELECT PR.CDPRODUTO, PR.CDARVPROD, PR.NMPRODUTO, PR.IDTIPOCOMPPROD,
             PR.IDPESAPROD, PR.IDIMPRODUVEZ
        FROM PRODUTO PR
      WHERE PR.CDPRODUTO = :CDPRODUTO
    ";

    const INSERT_VENDAREST = "
        INSERT INTO VENDAREST (
            CDFILIAL, NRVENDAREST, CDLOJA, NRMESA, CDVENDEDOR,
            DTHRABERMESA, DTHRFECHMESA, CDOPERADOR, NRPESMESAVEN,
            CDCLIENTE, CDCONSUMIDOR, IDPEDIDOPAGO
        )
        VALUES (
            :CDFILIAL, :NRVENDAREST, :CDLOJA, :NRMESA,
            :CDVENDEDOR, :DTHRABERMESA, null, :CDOPERADOR,
            :NRPESMESAVEN, :CDCLIENTE, :CDCONSUMIDOR, :IDPEDIDOPAGO
        )
    ";

    const CANCELA_VENDAREST = "
        UPDATE VENDAREST
          SET IDPEDIDOPAGO = 'N'
        WHERE CDFILIAL = :CDFILIAL
          AND CDCONSUMIDOR = :CDCONSUMIDOR
          AND NRVENDAREST = :NRVENDAREST
    ";

    const CANCELA_COMANDAVEN = "
        UPDATE COMANDAVEN
          SET IDSTCOMANDA = '4', IDCOMANDAPAGA = 'N'
          WHERE CDFILIAL = :CDFILIAL
          AND NRVENDAREST = :NRVENDAREST
          AND NRCOMANDA = :NRCOMANDA
    ";

    const INSERT_COMANDAVEN = "
        INSERT INTO COMANDAVEN (
            CDFILIAL, NRVENDAREST, NRCOMANDA, CDLOJA,
            DSCOMANDA, IDSTCOMANDA, SGSEXOCON, TXMOTIVCANCE,
            VRACRCOMANDA, CDPAIS, SGESTADO, CDMUNICIPIO,
            NRCEPCONSCOMAND, CDBAIRRO, DSBAIRRO, DSENDECONSCOMAN,
            DSCOMPLENDCOCOM, DSREFENDCONSCOM, IDORGCMDVENDA,
            IDRETBALLOJA, NRCOMANDAEXT, IDSINCAGENDA, DTHRAGENDADA, IDCOMANDAPAGA,
            CDCAMPANHA, VRPONTOBRINDE, VRDESCFIDELIDAD, DSCUPOMPROMO
        )
        VALUES (
            :CDFILIAL, :NRVENDAREST, :NRCOMANDA, :CDLOJA,
            :DSCOMANDA, :IDSTCOMANDA, :SGSEXOCON, :TXMOTIVCANCE,
            :VRACRCOMANDA, :CDPAIS, :SGESTADO, :CDMUNICIPIO,
            :NRCEPCONSCOMAND, :CDBAIRRO, :DSBAIRRO, :DSENDECONSCOMAN,
            :DSCOMPLENDCOCOM, :DSREFENDCONSCOM, :IDORGCMDVENDA,
            :IDRETBALLOJA, :NRCOMANDAEXT, :IDSINCAGENDA, :DTHRAGENDADA, :IDCOMANDAPAGA,
            :CDCAMPANHA, :VRPONTOBRINDE, :VRDESCFIDELIDAD, :DSCUPOMPROMO
        )
    ";

    const GET_STANDARD_SELLER = "
        SELECT V.CDVENDEDOR,L.CDLOJA, V.NMFANVEN, C.CDCAIXA, V.CDOPERADOR, L.NRMESAPADRAO AS NRMESA,
              L.IDPOSOBSPED, L.IDLUGARMESA
          FROM PARAVEND PV
          JOIN VENDEDOR V
            ON PV.CDVENDEDOR = V.CDVENDEDOR
          JOIN CAIXA C
            ON V.CDCAIXA     = C.CDCAIXA
          AND PV.CDFILIAL   = C.CDFILIAL
        JOIN LOJA L
            ON C.CDLOJA      = L.CDLOJA
          AND L.CDFILIAL    = C.CDFILIAL
        WHERE PV.CDFILIAL   = :CDFILIAL
    ";

    const GET_PRODUCT_CONTROL_ID = "
        SELECT IDCONTROPROD
          FROM LOJA
        WHERE CDFILIAL = :CDFILIAL
          AND CDLOJA = :CDLOJA
    ";

    const INSERT_MOVCAIXADLV = "
        INSERT INTO MOVCAIXADLV (
            CDFILIAL, NRVENDAREST, NRSEQMOVDLV, IDTIPOMOVIVEDLV,
            VRMOVIVENDDLV, CDTIPORECE, CDCLIENTE, CDCONSUMIDOR)
        VALUES (
            :CDFILIAL, :NRVENDAREST, :NRSEQMOVDLV, :IDTIPOMOVIVEDLV,
            :VRMOVIVENDDLV, :CDTIPORECE, :CDCLIENTE, :CDCONSUMIDOR
        )
    ";

    const GET_LAST_SALE = "
        SELECT VR.CDCLIENTE, VR.CDCONSUMIDOR, VR.CDFILIAL, CV.NRCOMANDA, VR.NRVENDAREST,
                  CONVERT(VARCHAR(12), VR.DTHRABERMESA, 104) AS DTVENDA,
                  CV.IDSTCOMANDA, CV.IDRETBALLOJA, CV.VRACRCOMANDA, CV.VRDESCOMANDA,
                  M.CDMUNICIBGE, 'BR' CDPAIS , CV.SGESTADO, CV.DSENDECONSCOMAN, CV.DSCOMPLENDCOCOM, CV.NRCEPCONSCOMAND,
                  CV.DSREFENDCONSCOM, CV.DSBAIRRO,
                  CASE CV.IDSTCOMANDA
                        WHEN '1' THEN 1
                        ELSE 0
                  END AS ISOPEN

                    FROM COMANDAVEN CV
                      JOIN VENDAREST VR
                        ON CV.CDFILIAL    = VR.CDFILIAL
                      AND CV.NRVENDAREST = VR.NRVENDAREST

                LEFT JOIN MUNICIPIO M
                    ON CV.CDMUNICIPIO = M.CDMUNICIPIO
                    AND CV.SGESTADO    = M.SGESTADO
                    AND CV.CDPAIS      = M.CDPAIS


                  WHERE CV.NRCOMANDAEXT = :NRCOMANDAEXT

                  ORDER BY VR.DTHRABERMESA DESC
    ";

    const GET_LASTSALE_PRODUCTS = "
        SELECT IT.CDPRODUTO, PR.NMPRODUTO, CASE WHEN PR.DSPRODVENDA IS NULL THEN '' ELSE PR.DSPRODVENDA END AS DESCRIPTION, '' AS CDPRODPROMOCAO,
            IT.VRPRECCOMVEN , IT.QTPRODCOMVEN , IT.VRDESCCOMVEN, IT.VRACRCOMVEN, IT.NRINSCRCONS, IT.NRPRODCOMVEN

          FROM ITCOMANDAVEN IT
          JOIN PRODUTO PR
            ON IT.CDPRODUTO = PR.CDPRODUTO

        WHERE IT.CDFILIAL     = :CDFILIAL
          AND IT.NRVENDAREST  = :NRVENDAREST
          AND IT.NRCOMANDA    = :NRCOMANDA
          AND IT.CDPRODUTO NOT IN (SELECT CASE WHEN CDPRODUTO IS NULL THEN 'VAZIO' ELSE CDPRODUTO END FROM OCORRENCIA)
          AND IT.CDPRODPROMOCAO IS NULL
    ";

    const GET_LASTSALE_PRODUCT_OBSERVATIONS = "
        SELECT DSOCORR, CDOCORR, CDGRPOCOR, VRPRECCOMVEN, CDFILIAL, NRVENDAREST, NRCOMANDA, NRPRODCOMVEN
        FROM (
          SELECT CASE WHEN OC.DSAPELIDOOBS IS NULL THEN OC.DSOCORR ELSE OC.DSAPELIDOOBS END DSOCORR,
              OC.CDOCORR, OC.CDGRPOCOR, 0 VRPRECCOMVEN,
              OBIT.CDFILIAL, OBIT.NRVENDAREST, OBIT.NRCOMANDA, OBIT.NRPRODCOMVEN

          FROM OBSITCOMANDAVEN OBIT
            JOIN OCORRENCIA OC
            ON OBIT.CDGRPOCOR = OC.CDGRPOCOR
              AND OBIT.CDOCORR   = OC.CDOCORR

            WHERE OC.CDPRODUTO IS NULL

          UNION

          SELECT CASE WHEN OC.DSAPELIDOOBS IS NULL THEN OC.DSOCORR ELSE OC.DSAPELIDOOBS END DSOCORR,
              OC.CDOCORR, OC.CDGRPOCOR, CASE WHEN IT.VRPRECCOMVEN IS NULL THEN 0 ELSE IT.VRPRECCOMVEN END VRPRECCOMVEN ,
              OBIT.CDFILIAL, OBIT.NRVENDAREST, OBIT.NRCOMANDA, OBIT.NRPRODCOMVEN

          FROM OBSITCOMANDAVEN OBIT
          JOIN OCORRENCIA OC
            ON OBIT.CDGRPOCOR = OC.CDGRPOCOR
              AND OBIT.CDOCORR   = OC.CDOCORR
          LEFT JOIN (SELECT DISTINCT CDFILIAL, NRCOMANDA, NRVENDAREST, CDPRODUTO, VRPRECCOMVEN
                          FROM ITCOMANDAVEN
                )IT
              ON IT.NRCOMANDA    = OBIT.NRCOMANDA
            AND IT.NRVENDAREST  = OBIT.NRVENDAREST
            AND IT.CDPRODUTO    = OC.CDPRODUTO
              AND IT.CDFILIAL     = OBIT.CDFILIAL

            WHERE OC.CDPRODUTO IS NOT NULL
        ) a

        WHERE NRCOMANDA    = :NRCOMANDA
          AND NRVENDAREST  = :NRVENDAREST
          AND CDFILIAL     = :CDFILIAL
          AND NRPRODCOMVEN = :NRPRODCOMVEN
    ";

    const GET_LASTSALE_COMBOS = "
      SELECT IT.CDPRODPROMOCAO AS CDPRODUTO, IT.CDPRODPROMOCAO, PR.NMPRODUTO, IT.NRSEQPRODCOM,
          SUM(IT.VRPRECCOMVEN) VRPRECCOMVEN, SUM(IT.VRDESCCOMVEN) VRDESCCOMVEN, SUM(IT.VRACRCOMVEN) VRACRCOMVEN

        FROM ITCOMANDAVEN IT
        JOIN PRODUTO PR
          ON IT.CDPRODPROMOCAO = PR.CDPRODUTO

      WHERE IT.CDFILIAL    = :CDFILIAL
        AND IT.NRVENDAREST = :NRVENDAREST
            AND IT.NRCOMANDA   = :NRCOMANDA

          AND IT.CDPRODPROMOCAO IS NOT NULL

      GROUP BY IT.CDFILIAL, IT.NRVENDAREST, IT.NRCOMANDA, IT.CDPRODPROMOCAO, PR.NMPRODUTO, IT.NRSEQPRODCOM
    ";

    const GET_LASTSALE_COMBO_PRODUCTS = "
        SELECT IT.CDPRODUTO, PR.NMPRODUTO, CASE WHEN PR.DSPRODVENDA IS NULL THEN '' ELSE PR.DSPRODVENDA END AS DESCRIPTION,
          IT.VRPRECCOMVEN , IT.QTPRODCOMVEN , IT.VRDESCCOMVEN, IT.VRACRCOMVEN, IT.NRINSCRCONS, IT.NRPRODCOMVEN

        FROM ITCOMANDAVEN IT
        JOIN PRODUTO PR
          ON IT.CDPRODUTO = PR.CDPRODUTO

        WHERE IT.CDFILIAL     = :CDFILIAL
        AND IT.NRVENDAREST    = :NRVENDAREST
        AND IT.NRCOMANDA      = :NRCOMANDA
        AND IT.CDPRODPROMOCAO = :CDPRODPROMOCAO
        AND IT.NRSEQPRODCOM   = :NRSEQPRODCOM
    ";

    const GET_LASTSALE_PAYMENTS = "
        SELECT MV.CDTIPORECE, TR.NMTIPORECE, MV.VRMOVIVENDDLV AS VRMOVIVEND
          FROM MOVCAIXADLV MV
            JOIN TIPORECE TR
                ON MV.CDTIPORECE = TR.CDTIPORECE

        WHERE MV.CDFILIAL    = :CDFILIAL
          AND MV.NRVENDAREST = :NRVENDAREST
          AND MV.IDTIPOMOVIVEDLV = 'E'
    ";

    const BUSCA_ITCOMANDAVEN = "
        SELECT *
          FROM ITCOMANDAVEN
        WHERE CDFILIAL = :CDFILIAL
          AND NRCOMANDA = :NRCOMANDA
          AND NRVENDAREST = :NRVENDAREST
    ";

    const DELETA_OBSITCOMANDAEST = "
        DELETE
        FROM OBSITCOMANDAEST
        WHERE CDFILIAL = :CDFILIAL
          AND NRCOMANDA = :NRCOMANDA
          AND NRVENDAREST = :NRVENDAREST
          AND NRPRODCOMVEN IN (SELECT NRPRODCOMVEN
                                FROM ITCOMANDAVEN
                                WHERE CDFILIAL = :CDFILIAL
                                  AND NRCOMANDA = :NRCOMANDA
                                  AND NRVENDAREST = :NRVENDAREST
                                  AND (NRLUGARMESA = :NRLUGARMESA OR :NRLUGARMESA = 'T'))
    ";

    const DELETA_OBSITCOMANDAVEN = "
        DELETE
          FROM OBSITCOMANDAVEN
        WHERE CDFILIAL = :CDFILIAL
          AND NRCOMANDA = :NRCOMANDA
          AND NRVENDAREST = :NRVENDAREST
          AND NRPRODCOMVEN IN (SELECT NRPRODCOMVEN
                                  FROM ITCOMANDAVEN
                                WHERE CDFILIAL = :CDFILIAL
                                  AND NRCOMANDA = :NRCOMANDA
                                  AND NRVENDAREST = :NRVENDAREST
                                  AND (NRLUGARMESA = :NRLUGARMESA OR :NRLUGARMESA = 'T'))
      ";

    const DELETA_ITCOMANDAEST = "
        DELETE
          FROM ITCOMANDAEST
        WHERE CDFILIAL = :CDFILIAL
          AND NRCOMANDA = :NRCOMANDA
          AND NRVENDAREST = :NRVENDAREST
          AND NRPRODCOMVEN IN (SELECT NRPRODCOMVEN
                                  FROM ITCOMANDAVEN
                                WHERE CDFILIAL = :CDFILIAL
                                  AND NRCOMANDA = :NRCOMANDA
                                  AND NRVENDAREST = :NRVENDAREST
                                  AND (NRLUGARMESA = :NRLUGARMESA OR :NRLUGARMESA = 'T'))
    ";

    const DELETA_ITCOMANDAVEN = "
        DELETE
          FROM ITCOMANDAVEN
        WHERE CDFILIAL = :CDFILIAL
          AND NRCOMANDA = :NRCOMANDA
          AND NRVENDAREST = :NRVENDAREST
          AND (NRLUGARMESA = :NRLUGARMESA OR :NRLUGARMESA = 'T')
    ";

    const LIBERA_MESA = "
        UPDATE MESA
          SET IDSTMESAAUX = 'D'
        WHERE CDFILIAL = :CDFILIAL
          AND NRMESA = (SELECT NRMESA
                          FROM VENDAREST
                          WHERE CDFILIAL = :CDFILIAL
                            AND NRVENDAREST = :NRVENDAREST)
    ";

    const DELETA_COMANDAVEN = "
        DELETE
          FROM COMANDAVEN
        WHERE CDFILIAL = :CDFILIAL
          AND NRCOMANDA = :NRCOMANDA
          AND NRVENDAREST = :NRVENDAREST
    ";

    const DELETA_VENDAREST = "
        DELETE
          FROM VENDAREST
        WHERE CDFILIAL = :CDFILIAL
          AND NRVENDAREST = :NRVENDAREST
    ";

    const DELETA_POSVENDAREST = "
        DELETE
          FROM POSVENDAREST
        WHERE CDFILIAL = :CDFILIAL
          AND NRVENDAREST = :NRVENDAREST
    ";

    const GET_JUNCAOMESA = "
        SELECT JM.NRJUNMESA
            FROM JUNCAOMESA JM, MESAJUNCAO MJ
          WHERE (JM.CDFILIAL = :CDFILIAL )
              AND (MJ.NRMESA   = :NRMESA   )
              AND (JM.CDFILIAL = MJ.CDFILIAL )
              AND (JM.CDLOJA   = MJ.CDLOJA   )
              AND (JM.NRJUNMESA = MJ.NRJUNMESA)
    ";

    const DELETA_JUNCAOMESA = "
        DELETE
          FROM MESAJUNCAO
        WHERE CDFILIAL = :CDFILIAL
          AND NRJUNMESA = :NRJUNMESA
	  ";

	  const DELETA_MESAJUNCAO = "
        DELETE
          FROM JUNCAOMESA
        WHERE CDFILIAL = :CDFILIAL
          AND NRJUNMESA = :NRJUNMESA
    ";

    const GET_CONSUMER_BY_ID = "
        SELECT CO.CDCONSUMIDOR, CO.NRCPFRESPCON, CO.CDCLIENTE,  CO.IDTPVENDACONS,
              CO.NMCONSUMIDOR, CO.DSEMAILCONS, CONVERT(VARCHAR(10), CO.DTNASCCONS, 103) AS DTNASCCONS,
              CO.IDSEXOCONS, CO.IDSITCONSUMI AS IDATIVO, CO.NRCEPCONS, CO.DSENDECONS, CO.NRENDECONS,
              MU.CDMUNICIBGE, MU.CDMUNICIPIO, CO.SGESTADO, PA.NMPAIS, CO.DSCOMPLENDECONS, PA.CDPAIS,
              BA.NMBAIRRO, CO.NRTELECONS, CO.NRCELULARCONS, 1 AS ISPRINCIPAL, BA.CDBAIRRO, CO.DSREFERENDECONS

            FROM CONSUMIDOR CO

          LEFT JOIN PAIS PA
              ON CO.CDPAIS      = PA.CDPAIS

            LEFT JOIN BAIRRO BA
              ON CO.CDPAIS      = BA.CDPAIS
            AND CO.SGESTADO    = BA.SGESTADO
            AND CO.CDMUNICIPIO = BA.CDMUNICIPIO
            AND CO.CDBAIRRO    = BA.CDBAIRRO

          LEFT JOIN MUNICIPIO MU
              ON CO.CDPAIS      = MU.CDPAIS
            AND CO.SGESTADO    = MU.SGESTADO
            AND CO.CDMUNICIPIO = MU.CDMUNICIPIO

          WHERE CDCONSUMIDOR = :CDCONSUMIDOR
          AND CDCLIENTE = :CDCLIENTE
            AND DTULTATUCSUM IN (SELECT MAX(DTULTATUCSUM)
                                    FROM CONSUMIDOR
                        WHERE CDCONSUMIDOR = :CDCONSUMIDOR
                        AND :CDCLIENTE = :CDCLIENTE)
    ";

    const GET_CONSUMER_CDIDCONSUMID = "
        SELECT CDIDCONSUMID, CDSENHACONSMD5, CDSENHACONS, CDCONSUMIDOR, CN.CDCLIENTE, NMCONSUMIDOR
        FROM CONSUMIDOR CN
        JOIN CLIENFILIAL CF ON CF.CDCLIENTE = CN.CDCLIENTE
        WHERE CDIDCONSUMID = :CDIDCONSUMID
    ";

    const UPDATE_ITEMVENDA_ESTQ = "
        UPDATE ITEMVENDA
          SET NRLANCESTQ     = :NRLANCESTQ
        WHERE CDFILIAL       = :CDFILIAL
          AND NRSEQVENDA     = :NRSEQVENDA
          AND CDCAIXA        = :CDCAIXA
          AND NRSEQUITVEND   = :NRSEQUITVEND
          AND NRORG          = :NRORG
      ";

    const UPDATE_ITEMVENDA_FAMILIA = "
      UPDATE ITEMVENDA
        SET CDFAMILISALD     = :CDFAMILISALD
      WHERE CDFILIAL       = :CDFILIAL
        AND NRSEQVENDA     = :NRSEQVENDA
        AND CDCAIXA        = :CDCAIXA
    ";

    const GET_PRODUTO_CONTRESTOQ = "
        SELECT IDCNTRESTOQ
          FROM PRODFILI
         WHERE CDFILIAL  = :CDFILIAL
           AND NRORG     = :NRORG
           AND CDPRODUTO = :CDPRODUTO
    ";

    const GET_PARAMSESTQ_FILIAL = "
        SELECT IDCTRLESTQ,IDUTILALMOX,IDUTILLCESTQ,IDUTILLOTE
          FROM PARAMFILIAL
         WHERE CDFILIAL = :CDFILIAL
           AND NRORG    = :NRORG
    ";

    const GET_TANQUEBICOH_BY_BICO = "
        SELECT TB.CDALMOXARIFE AS CDALMOXARIFADO , TB.CDFILIAL AS CDFILIALALMOXARIFADO
        FROM (
              SELECT MIN(CONVERT(DATE, :DTABASTECIMENTO) - DTATIVACAO) AS DATEDIFF, NRSEQBICO
                FROM VND_TANQUEBICOH
              WHERE NRSEQBICO = :NRSEQBICO
                AND CONVERT(DATE, :DTABASTECIMENTO) >= DTATIVACAO
            GROUP BY NRSEQBICO
          ) DIFFS, VND_TANQUEBICOH TB
        WHERE DIFFS.NRSEQBICO = TB.NRSEQBICO
          AND (CONVERT(DATE, :DTABASTECIMENTO) - TB.DTATIVACAO) = DIFFS.DATEDIFF
    ";

    const GET_RELACALMOXESTRUT_PDV = "
        SELECT CDALMOXARIFE
          FROM VND_RELALMOXARIFADOESTRUT RAE
         WHERE NRORG    = :NRORG
           AND CDFILIAL = :CDFILIAL
           AND CDLOJA   = :CDLOJA
           AND CDCAIXA  = :CDCAIXA
           AND PADRAO   = 'SIM'
    ";

    const GET_RELACALMOXESTRUT_LOJA = "
        SELECT CDALMOXARIFE
          FROM VND_RELALMOXARIFADOESTRUT RAE
         WHERE NRORG    = :NRORG
           AND CDFILIAL = :CDFILIAL
           AND CDLOJA   = :CDLOJA
           AND CDCAIXA IS NULL
           AND PADRAO   = 'SIM'
    ";

    const GET_RELACALMOXESTRUT_FILIAL = "
        SELECT CDALMOXARIFE
          FROM VND_RELALMOXARIFADOESTRUT RAE
         WHERE NRORG    = :NRORG
           AND CDFILIAL = :CDFILIAL
           AND CDLOJA IS NULL
           AND CDCAIXA IS NULL
           AND PADRAO   = 'SIM'
    ";

    const GET_ITEMS_VENDA_CTRLESTOQ = "
        SELECT CASE COUNT(*) WHEN 0 THEN 'N' ELSE 'S' END IDCNTRESTOQ
          FROM PRODFILI PF
        WHERE PF.CDPRODUTO  IN (?)
          AND PF.NRORG       = ?
          AND PF.CDFILIAL    = ?
          AND IDCNTRESTOQ = 'S'
    ";

    const INSERT_LANCTOESTOQ = "
        INSERT INTO LANCTOESTOQ
              (CDFILIAL, NRORG, CDFILIMOVI, IDTPLANCTO,
              DTLANCESTQ, CDOPERADOR, NRLANCESTQ, DSLANCESTQ,
              NRORGINCLUSAO, NRORGULTATU, CDOPERINCLUSAO, CDOPERULTATU)
          VALUES
          (:CDFILIAL, :NRORG, :CDFILIMOVI, :IDTPLANCTO,
            :DTLANCESTQ, :CDOPERADOR, :NRLANCESTQ, :DSLANCESTQ,
            :NRORGINCLUSAO, :NRORGULTATU, :CDOPERINCLUSAO, :CDOPERULTATU)
    ";

    const INSERT_ITLANCTOEST = "
        INSERT INTO ITLANCTOEST
        (CDFILIAL, NRSEQUITEM, NRORG, NRLANCESTQ,
        CDPRODUTO, QTTOTLANCTO, VRTOTLANCTO, VRUNILANCTO,
        IDTIPOMOVI, CDFILIMOVI, CDALMOXARIFE, CDLOCALESTOQ,
        NRLOTEESTQ, NRSUBLOTE, DTLANCMOVI, CDPRODMOVI,
        QTLANCTOEST, VRLANCTOBRUT, VRLANCTOEST, NRORGINCLUSAO,
        NRORGULTATU, CDOPERINCLUSAO, CDOPERULTATU)
        VALUES
        (:CDFILIAL, :NRSEQUITEM, :NRORG, :NRLANCESTQ,
        :CDPRODUTO, :QTTOTLANCTO, :VRTOTLANCTO, :VRUNILANCTO,
        :IDTIPOMOVI, :CDFILIMOVI, :CDALMOXARIFE, :CDLOCALESTOQ,
        :NRLOTEESTQ, :NRSUBLOTE, :DTLANCMOVI, :CDPRODMOVI,
        :QTLANCTOEST, :VRLANCTOBRUT, :VRLANCTOEST, :NRORGINCLUSAO,
        :NRORGULTATU, :CDOPERINCLUSAO, :CDOPERULTATU)
    ";
    // Query nao existia para sql server. Criei a query abaixo de acordo com a do oracle.
    const GET_MAX_ITLANCTOEST = "
        SELECT RIGHT('0000'+CAST(CAST(ROUND(MAX(NRSEQUITEM)+1, 0,-1) AS INT) AS VARCHAR), 4) PROXNRSEQUITEM
          FROM ITLANCTOEST
        WHERE NRORG      = :NRORG
          AND CDFILIAL   = :CDFILIAL
          AND NRLANCESTQ = :NRLANCESTQ
    ";

    const DADOS_CONSUMIDOR = "
        SELECT CO.CDCLIENTE, CO.CDCONSUMIDOR, CO.CDIDCONSUMID
          FROM CONSUMIDOR CO
         WHERE CO.CDIDCONSUMID = :CDIDCONSUMID
    ";

    const BENEFICIOS_CONSUMIDOR = "
        SELECT  MB.CDCAMPANHA, MB.NRSEQBENEFICIO, MB.DTINIVALBENECONS, MB.DTFIMVALBENECONS,
                MB.QTDEBENEFICIO
          FROM MOVBENEFICIOCTR MB
          WHERE MB.CDCLIENTE    = :CDCLIENTE
           AND MB.CDCONSUMIDOR = :CDCONSUMIDOR
           AND QTDEBENEFICIO > 0
    ";

    const PRODUTOS_CAMPANHA = "
        SELECT PB.CDPRODUTO, MB.CDCAMPANHA, MB.NRSEQPRODCAMP, MB.NRSEQBENEFICIO,  MB.IDTPDESBENECONS, MB.QTDEBENEFICIO,
               MB.VRDESBENECONS, BF.DSBENEFICIO
        FROM MOVBENEFICIOCTR MB JOIN PRODBENECAMP PB
                                  ON MB.CDCAMPANHA = PB.CDCAMPANHA
                                 AND MB.NRSEQPRODCAMP = PB.NRSEQPRODCAMP
                                 AND MB.NRSEQBENEFICIO = PB.NRSEQBENEFICIO
                                JOIN BENEFICIOFOS BF
                                  ON BF.NRSEQBENEFICIO = PB.NRSEQBENEFICIO
        WHERE MB.CDCLIENTE = :CDCLIENTE
        AND MB.CDCONSUMIDOR = :CDCONSUMIDOR
        AND PB.NRSEQBENEFICIO = :NRSEQBENEFICIO
        AND PB.CDCAMPANHA = :CDCAMPANHA
    ";

    const GET_DADOS_FILIAL = "
        SELECT F.NRINSJURFILI, UPPER(F.NMRAZSOCFILI) NMRAZSOCFILI, UPPER(F.NMFILIAL) NMFILIAL, UPPER(EF.DSENDEFILI) DSENDEFILI,
               EF.NRENDEFILISEP,UPPER(EF.NMBAIRFILI) NMBAIRFILI,M.CDMUNICIBGE,UPPER(M.NMMUNICIPIO) NMMUNICIPIO, EF.SGESTADO,
               EF.NRCEPFILI, P.CDPAISBACEN,UPPER(P.NMPAIS) NMPAIS,F.CDINSCESTA,F.CDINSCMUNI,E.CDCNAE,PE.CDSITUCRT, ES.CDESTADOIBGE
          FROM FILIAL F, ENDEFILI EF, MUNICIPIO M, PAIS P, PARAMEMPRESA PE, EMPRESA E, ESTADO ES
         WHERE F.CDFILIAL      = :CDFILIAL
           AND F.NRORG         = :NRORG
           AND EF.CDFILIAL     = F.CDFILIAL
           AND EF.NRORG        = F.NRORG
           AND EF.IDTPENDEFILI = 'P'
           AND ES.SGESTADO     = EF.SGESTADO
           AND ES.CDPAIS       = EF.CDPAIS
           AND M.CDMUNICIPIO   = EF.CDMUNICIPIO
           AND M.SGESTADO      = EF.SGESTADO
           AND P.CDPAIS        = EF.CDPAIS
           AND E.CDEMPRESA     = F.CDEMPRESA
           AND E.NRORG         = F.NRORG
           AND PE.CDEMPRESA    = E.CDEMPRESA
           AND PE.NRORG        = E.NRORG
    ";

    const BUSCA_DADOS_IMPRESSORA_NF = "
        SELECT P.NRSEQIMPRLOJA, I.IDMODEIMPRES, P.CDPORTAIMPR, L.CDLOJA,
               UPPER(L.NMLOJA) AS NMLOJA, UPPER(C.NMCAIXA) AS NMCAIXA,
               P.DSIPIMPR, P.DSIPPONTE
          FROM CAIXA C JOIN IMPRLOJA P
                         ON C.CDFILIAL = P.CDFILIAL
                        AND C.CDLOJA = P.CDLOJA
                        AND C.NRSEQIMPRLOJA3 = P.NRSEQIMPRLOJA
                       JOIN IMPRESSORA I
                         ON P.CDIMPRESSORA = I.CDIMPRESSORA
                       JOIN LOJA L
                         ON L.CDLOJA = C.CDLOJA
                        AND L.CDFILIAL = C.CDFILIAL
         WHERE C.CDFILIAL = :CDFILIAL
           AND C.CDCAIXA = :CDCAIXA
           AND C.NRORG = :NRORG
    ";

    const BUSCA_DADOS_CAIXA = "
    SELECT MAX(DTABERCAIX) AS DTABERCAIX, C.IDHABCAIXAVENDA
              FROM TURCAIXA T
          JOIN CAIXA C ON C.CDCAIXA = T.CDCAIXA AND C.CDFILIAL = T.CDFILIAL
             WHERE T.CDFILIAL = :CDFILIAL
               AND T.CDCAIXA = :CDCAIXA
               AND T.NRORG = :NRORG
        GROUP BY C.IDHABCAIXAVENDA
    ";

    const BUSCA_RECEBIMENTOS = "
        SELECT T.DSIMPFISCAL, T.NMTIPORECE, M.VRMOVIVEND, M.IDTIPOMOVIVE, T.IDTIPORECE
          FROM MOVCAIXA M JOIN TIPORECE T
                            ON M.CDTIPORECE = T.CDTIPORECE
         WHERE M.CDFILIAL = :CDFILIAL
           AND M.CDCAIXA = :CDCAIXA
           AND M.DTABERCAIX = :DTABERCAIX
           AND M.NRSEQVENDA = :NRSEQVENDA
           AND M.NRORG = :NRORG
           AND M.IDTIPOMOVIVE IN ('E', 'S')
    ";

    const BUSCA_PRODUTOS = "
        SELECT P.CDARVPROD, P.NMPRODUTO, P.SGUNIDADE, I.QTPRODVEND,
               I.VRUNITVEND, I.VRDESITVEND, I.VRACRITVEND, A.IDTPIMPOSFIS, A.VRPEALIMPFIS
          FROM ITEMVENDA I JOIN PRODUTO P
                             ON I.CDPRODUTO = P.CDPRODUTO
                           JOIN ALIQIMPFIS A
                             ON I.CDFILIAL = A.CDFILIAL
                            AND I.CDPRODUTO = A.CDPRODUTO
         WHERE I.CDFILIAL = :CDFILIAL
           AND I.CDCAIXA = :CDCAIXA
           AND I.NRSEQVENDA = :NRSEQVENDA
           AND I.NRORG = :NRORG
    ";

    const GET_INFO_PAYMENT_NFCE = "
        SELECT TR.NMTIPORECE, TR.IDTIPORECE, MC.IDTIPOMOVIVE, SUM(MC.VRMOVIVEND) AS VRMOVIVEND, X.*
              FROM MOVCAIXA MC, TIPORECE TR, (SELECT COUNT(*) AS QTDEITENS,
                        SUM(ROUND(IV.QTPRODVEND*(IV.VRUNITVEND+ISNULL(IV.VRUNITVENDCL,0)),2,1)+CASE IV.CDPRODUTO
                        WHEN LO.CDPRODTAXASERV THEN IV.VRACRITVEND
                        WHEN LO.CDPRODTAXAENTR THEN IV.VRACRITVEND
                        ELSE 0 END) AS VRTOTITEM,
                        SUM(CASE IV.CDPRODUTO WHEN LO.CDPRODTAXASERV THEN 0
                                              WHEN LO.CDPRODTAXAENTR THEN 0
                                              ELSE IV.VRACRITVEND END) AS VRTOTACRE,
                        SUM(IV.VRDESITVEND) AS VRTOTDESC,
                        ISNULL(VE.VRTXSEVENDA,0) AS VRTXSEVENDA
                    FROM VENDA VE, ITEMVENDA IV, LOJA LO
                    WHERE VE.CDFILIAL   = :CDFILIAL
                      AND VE.CDCAIXA    = :CDCAIXA
                      AND VE.NRSEQVENDA = :NRSEQVENDA
                      AND IV.CDFILIAL   = VE.CDFILIAL
                      AND IV.CDCAIXA    = VE.CDCAIXA
                      AND IV.NRSEQVENDA = VE.NRSEQVENDA
                      AND VE.CDFILIAL   = LO.CDFILIAL
                      AND VE.CDLOJA     = LO.CDLOJA
                    GROUP BY VE.VRTXSEVENDA )X
              WHERE MC.CDFILIAL   = :CDFILIAL
                AND MC.CDCAIXA    = :CDCAIXA
                AND MC.NRSEQVENDA = :NRSEQVENDA
                AND TR.CDTIPORECE = MC.CDTIPORECE
              GROUP BY TR.NMTIPORECE, TR.IDTIPORECE, MC.IDTIPOMOVIVE,
                      X.QTDEITENS, X.VRTOTITEM, X.VRTOTACRE, X.VRTOTDESC, X.VRTXSEVENDA
    ";

    const GET_CANCELED_PRODUCTS = "
        SELECT
          IC.CDFILIAL, IC.CDCAIXA, IC.NRSEQVENDA, IC.NRSEQITVENDC,
          IC.QTPRODVENDC, IC.CDPRODUTO, PR.NMPRODUTO,  PR.SGUNIDADE
        FROM
          ITVENDACAN IC
        JOIN PRODUTO PR
          ON IC.CDPRODUTO  = PR.CDPRODUTO
        WHERE
          IC.CDFILIAL   = :CDFILIAL
          AND IC.CDCAIXA    = :CDCAIXA
          AND IC.NRSEQVENDA = :NRSEQVENDA
    ";

    const GET_NFCE_TO_PRINT = "
        SELECT FI.NMRAZSOCFILI, FI.NRINSJURFILI, FI.NMFILIAL,
        EF.DSENDEFILI, EF.NMBAIRFILI, MU.SGESTADO, MU.NMMUNICIPIO,
        VE.CDFILIAL, VE.CDCAIXA, VE.NRSEQVENDA, VE.IDTPAMBNFCE, VE.IDSTATUSNFCE,
        VE.NRNOTAFISCALCE, VE.CDSERIENFCE, VE.DTEMISSAONFCE, VE.NRACESSONFCE,
        VE.NRPROTOCOLONFCE, VE.CDSENHAPED, VE.DSQRCODENFCE, WE.WSLINKQRCODENFC,
        IP.VRTOTTRIBIBPT, CX.DSOBSCUPFIS, CX.DSMENSCPFCUP, CX.NMCAIXA,
        IL.NRSEQIMPRLOJA, IL.CDPORTAIMPR, IM.IDMODEIMPRES, LJ.CDLOJA,
        LJ.NMLOJA, PV.IDIMPLOGONF, PV.NRPOSNVBEMA, PV.NRPOSNV1EPSON,
        PV.NRPOSNV2EPSON, PV.IDIMPTXSERV, LJ.IDIMPCNPJCLIE, IL.DSIPIMPR,
        IL.DSIPPONTE, PV.IDUMAVIANFCE
          FROM VENDA VE
          JOIN ENDEFILI EF
                  ON EF.CDFILIAL = VE.CDFILIAL
          JOIN MUNICIPIO MU
                  ON EF.CDPAIS = MU.CDPAIS
                  AND EF.SGESTADO = MU.SGESTADO
                  AND EF.CDMUNICIPIO = MU.CDMUNICIPIO
          JOIN FILIAL FI
                  ON FI.CDFILIAL = VE.CDFILIAL
          JOIN WEBSERVICE WE
                  ON WE.CDPAIS = EF.CDPAIS
                  AND WE.SGESTADO = EF.SGESTADO
                  AND WE.IDAMBIENTE = VE.IDTPAMBNFCE
          JOIN ITVENDAIMPOS IP
                  ON IP.CDFILIAL = VE.CDFILIAL
                  AND IP.CDCAIXA = VE.CDCAIXA
                  AND IP.NRSEQVENDA = VE.NRSEQVENDA
          JOIN CAIXA CX
                  ON CX.CDFILIAL = VE.CDFILIAL
                  AND CX.CDCAIXA = VE.CDCAIXA
          JOIN IMPRLOJA IL
                  ON IL.CDFILIAL = CX.CDFILIAL
                  AND IL.CDLOJA = CX.CDLOJA
                  AND IL.NRSEQIMPRLOJA = CX.NRSEQIMPRLOJA3
          JOIN IMPRESSORA IM
                  ON IL.CDIMPRESSORA = IM.CDIMPRESSORA
          JOIN PARAVEND PV
                  ON FI.CDFILIAL = PV.CDFILIAL
          JOIN LOJA LJ
                  ON LJ.CDFILIAL = CX.CDFILIAL
                  AND LJ.CDLOJA = CX.CDLOJA
          WHERE
              VE.NRACESSONFCE = :NRACESSONFCE
              AND FI.CDFILIAL = :CDFILIAL
              AND CX.CDCAIXA = :CDCAIXA
              AND EF.IDTPENDEFILI = 'P'
              AND WE.IDSERVICO = 'NFC'
    ";

    const GET_PARAVEND_DATA_TO_PRINT = "
        SELECT P.CDSITUCRT, PV.IDIMPSALDOCUPOM
        FROM FILIAL F
        JOIN EMPRESA E
          ON E.CDEMPRESA = F.CDEMPRESA
        JOIN PARAMEMPRESA P
          ON P.CDEMPRESA = E.CDEMPRESA
        JOIN PARAVEND PV ON PV.CDFILIAL  = F.CDFILIAL
        WHERE
          F.CDFILIAL  = :CDFILIAL
    ";

    const BUSCA_DADOS_LOJA = "
        SELECT IDLUGARMESA, IDPOSOBSPED, IDUTLCORTEPED
          FROM LOJA
         WHERE CDFILIAL = :CDFILIAL
           AND CDLOJA = :CDLOJA
    ";

    const BUSCA_DADOS_MESA = "
        SELECT M.CDFILIAL, M.CDLOJA, M.NRMESA, S.NMSALA
          FROM MESA M JOIN SALA S
                        ON M.CDFILIAL = S.CDFILIAL
                       AND M.CDSALA = S.CDSALA
         WHERE M.CDFILIAL = :CDFILIAL
           AND M.CDLOJA = :CDLOJA
           AND M.NRMESA = :NRMESA
    ";

    const BUSCA_DADOS_VENDEDOR = "
        SELECT NMFANVEN
          FROM VENDEDOR
         WHERE CDVENDEDOR = :CDVENDEDOR
    ";

    const BUSCA_IMPRESSORAS = "
        SELECT A.NRSEQIMPRPROD AS A_NRSEQIMPRPROD, A.NRSEQIMPRLOJA AS A_NRSEQIMPRLOJA, A.NMIMPRLOJA AS A_NMIMPRLOJA,
               A.CDPORTAIMPR AS A_CDPORTAIMPR, A.IDMODEIMPRES AS A_IDMODEIMPRES, A.DSENDPORTA AS A_DSENDPORTA,
               A.DSIPIMPR AS A_DSIPIMPR, A.DSIPPONTE AS A_DSIPPONTE,

               B.NRSEQIMPRPROD2 AS B_NRSEQIMPRPROD2, B.NRSEQIMPRLOJA AS B_NRSEQIMPRLOJA, B.NMIMPRLOJA AS B_NMIMPRLOJA,
               B.CDPORTAIMPR AS B_CDPORTAIMPR, B.IDMODEIMPRES AS B_IDMODEIMPRES, B.DSENDPORTA AS B_DSENDPORTA,
               B.DSIPIMPR AS B_DSIPIMPR, B.DSIPPONTE AS B_DSIPPONTE,

               C.NRSEQIMPRPUXA AS C_NRSEQIMPRPUXA, C.NRSEQIMPRLOJA AS C_NRSEQIMPRLOJA, C.NMIMPRLOJA AS C_NMIMPRLOJA,
               C.CDPORTAIMPR AS C_CDPORTAIMPR, C.IDMODEIMPRES AS C_IDMODEIMPRES, C.DSENDPORTA AS C_DSENDPORTA,
               C.DSIPIMPR AS C_DSIPIMPR, C.DSIPPONTE AS C_DSIPPONTE

          FROM

        (SELECT P.NRSEQIMPRPROD, L.NRSEQIMPRLOJA, L.NMIMPRLOJA, L.CDPORTAIMPR,
                I.IDMODEIMPRES, M.DSENDPORTA, L.DSIPIMPR, L.DSIPPONTE
           FROM PRODLOJA P JOIN IMPRLOJA L
                             ON P.CDFILIAL = L.CDFILIAL
                            AND P.CDLOJA = L.CDLOJA
                            AND P.NRSEQIMPRPROD = L.NRSEQIMPRLOJA

                           JOIN IMPRESSORA I
                             ON L.CDIMPRESSORA = I.CDIMPRESSORA

                      LEFT JOIN MAPIMPRLOJA M
                             ON L.CDFILIAL = M.CDFILIAL
                            AND L.CDLOJA = M.CDLOJA
                            AND L.CDPORTAIMPR = M.CDPORTAIMPR
          WHERE P.CDFILIAL = :CDFILIAL
            AND P.CDLOJA = :CDLOJA
            AND P.CDPRODUTO = :CDPRODUTO) A

        FULL JOIN

        (SELECT P.NRSEQIMPRPROD2, L.NRSEQIMPRLOJA, L.NMIMPRLOJA, L.CDPORTAIMPR,
                I.IDMODEIMPRES, M.DSENDPORTA, L.DSIPIMPR, L.DSIPPONTE
           FROM PRODLOJA P JOIN IMPRLOJA L
                             ON P.CDFILIAL = L.CDFILIAL
                            AND P.CDLOJA = L.CDLOJA
                            AND P.NRSEQIMPRPROD2 = L.NRSEQIMPRLOJA

                           JOIN IMPRESSORA I
                             ON L.CDIMPRESSORA = I.CDIMPRESSORA

                      LEFT JOIN MAPIMPRLOJA M
                             ON L.CDFILIAL = M.CDFILIAL
                            AND L.CDLOJA = M.CDLOJA
                            AND L.CDPORTAIMPR = M.CDPORTAIMPR
          WHERE P.CDFILIAL = :CDFILIAL
            AND P.CDLOJA = :CDLOJA
            AND P.CDPRODUTO = :CDPRODUTO) B

        ON 'A' = 'A' FULL JOIN

        (SELECT P.NRSEQIMPRPUXA, L.NRSEQIMPRLOJA, L.NMIMPRLOJA, L.CDPORTAIMPR,
                I.IDMODEIMPRES, M.DSENDPORTA, L.DSIPIMPR, L.DSIPPONTE
           FROM PRODLOJA P JOIN IMPRLOJA L
                             ON P.CDFILIAL = L.CDFILIAL
                            AND P.CDLOJA = L.CDLOJA
                            AND P.NRSEQIMPRPUXA = L.NRSEQIMPRLOJA

                           JOIN IMPRESSORA I
                             ON L.CDIMPRESSORA = I.CDIMPRESSORA

                      LEFT JOIN MAPIMPRLOJA M
                             ON L.CDFILIAL = M.CDFILIAL
                            AND L.CDLOJA = M.CDLOJA
                            AND L.CDPORTAIMPR = M.CDPORTAIMPR
          WHERE P.CDFILIAL = :CDFILIAL
            AND P.CDLOJA = :CDLOJA
            AND P.CDPRODUTO = :CDPRODUTO) C

        ON 'A' = 'A'
    ";

    const BUSCA_IMPRESSORAS_POR_AMBIENTE = "
        SELECT I.NRSEQIMPRLOJA, S.IDMODEIMPRES, I.DSIPIMPR, I.DSIPPONTE,
                I.CDPORTAIMPR, M.DSENDPORTA, I.NMIMPRLOJA
          FROM PRODAMBIENTE P JOIN IMPRLOJA I
                                ON P.CDFILIAL = I.CDFILIAL
                                AND P.CDLOJA = I.CDLOJA
                                AND P.NRSEQIMPRLOJA = I.NRSEQIMPRLOJA

                                JOIN IMPRESSORA S
                                  ON I.CDIMPRESSORA = S.CDIMPRESSORA

                          LEFT JOIN MAPIMPRLOJA M
                                  ON I.CDFILIAL = M.CDFILIAL
                                AND I.CDLOJA = M.CDLOJA
                                AND I.CDPORTAIMPR = M.CDPORTAIMPR
        WHERE P.CDFILIAL = :CDFILIAL
          AND P.CDLOJA = :CDLOJA
          AND P.CDPRODUTO = :CDPRODUTO
          AND P.NRCONFTELA = :NRCONFTELA
          AND P.DTINIVIGENCIA = :DTINIVIGENCIA
          AND P.CDAMBIENTE = :CDAMBIENTE
    ";

    const BUSCA_DADOS_IMPRESSORA_PED = "
        SELECT P.NRSEQIMPRLOJA, I.IDMODEIMPRES, P.DSIPIMPR, P.DSIPPONTE,
               P.CDPORTAIMPR, M.DSENDPORTA, P.NMIMPRLOJA
          FROM IMPRLOJA P
          JOIN IMPRESSORA I ON I.CDIMPRESSORA = P.CDIMPRESSORA
          LEFT JOIN MAPIMPRLOJA M ON P.CDFILIAL = M.CDFILIAL
                                 AND P.CDLOJA = M.CDLOJA
                                 AND P.CDPORTAIMPR = M.CDPORTAIMPR
         WHERE P.CDFILIAL      = :CDFILIAL
           AND P.CDLOJA        = :CDLOJA
           AND P.NRSEQIMPRLOJA = :NRSEQIMPRLOJA
    ";

    const BUSCA_NOME_POR_POSICAO = "
        SELECT NRLUGARMESA, DSCONSUMIDOR
          FROM POSVENDAREST
         WHERE CDFILIAL = :CDFILIAL
           AND NRVENDAREST = :NRVENDAREST
           AND NRLUGARMESA IN (:NRLUGARMESA)
           AND DSCONSUMIDOR IS NOT NULL
    ";

    const BUSCA_DADOS_IMPRESSORA_SAT = "
        SELECT P.NRSEQIMPRLOJA, I.IDMODEIMPRES, P.CDPORTAIMPR, L.CDLOJA,
               UPPER(L.NMLOJA) AS NMLOJA, UPPER(C.NMCAIXA) AS NMCAIXA,
               P.DSIPIMPR, P.DSIPPONTE, P.NMIMPRLOJA
          FROM CAIXA C JOIN IMPRLOJA P
                         ON C.CDFILIAL = P.CDFILIAL
                        AND C.CDLOJA = P.CDLOJA
                        AND C.NRSEQIMPRLOJA3 = P.NRSEQIMPRLOJA
                       JOIN IMPRESSORA I
                         ON P.CDIMPRESSORA = I.CDIMPRESSORA
                       JOIN LOJA L
                         ON L.CDLOJA = C.CDLOJA
                        AND L.CDFILIAL = C.CDFILIAL
         WHERE C.CDFILIAL = :CDFILIAL
           AND C.CDCAIXA = :CDCAIXA
           AND C.NRORG = :NRORG
    ";

    const BUSCA_PRODUTOS_SAT = "
        SELECT P.CDARVPROD, P.NMPRODUTO, P.SGUNIDADE, I.QTPRODVEND,
               I.VRUNITVEND, I.VRDESITVEND, I.VRACRITVEND, A.IDTPIMPOSFIS, A.VRPEALIMPFIS,
               L.CDPRODTAXASERV, P.CDPRODUTO, 'A' AS IDSITUITEM, '0' AS IDTIPOCOMPPROD, '1' AS IDIMPPRODUTO,
               I.VRUNITVENDCL
          FROM ITEMVENDA I JOIN PRODUTO P
                             ON I.CDPRODUTO = P.CDPRODUTO
                           JOIN ALIQIMPFIS A
                             ON I.CDFILIAL = A.CDFILIAL
                            AND I.CDPRODUTO = A.CDPRODUTO
                           JOIN LOJA L
                             ON I.CDFILIAL = L.CDFILIAL
                            AND I.CDLOJA = L.CDLOJA
         WHERE I.CDFILIAL = :CDFILIAL
           AND I.CDCAIXA = :CDCAIXA
           AND I.NRSEQVENDA = :NRSEQVENDA
           AND I.NRORG = :NRORG
    ";

    const VALIDA_FILIAL = "
        SELECT F.CDFILIAL, P.CDCLIENTE
          FROM FILIAL F JOIN PARAVEND P
                          ON F.CDFILIAL = P.CDFILIAL
         WHERE F.CDFILIAL = :CDFILIAL
    ";

    const VALIDA_LOJA = "
        SELECT L.IDCOMISVENDA, L.IDTRATTAXASERV, L.CDPRODTAXASERV,
               L.IDCOUVERART, L.CDPRODCOUVER
          FROM LOJA L JOIN CAIXA C ON L.CDFILIAL = C.CDFILIAL
                                  AND L.CDLOJA = C.CDLOJA
         WHERE C.CDFILIAL = :CDFILIAL
           AND C.CDCAIXA = :CDCAIXA
    ";

    const VALIDA_PROD_TAXASERV = "
        SELECT CDPRODUTO
          FROM PRODUTO
         WHERE CDPRODUTO = :CDPRODUTO
    ";

    const VALIDA_CAIXA = "
        SELECT CDFILIAL, CDCAIXA, NRCONFTELA
          FROM CAIXA
         WHERE CDFILIAL = :CDFILIAL
           AND CDCAIXA  = :CDCAIXA
    ";

    const CONFIGURACAO = "
        SELECT count(*) AS ITENS
          FROM ITMENUCONFTE AM
         WHERE AM.CDFILIAL   = :CDFILIAL
           AND AM.NRCONFTELA = :NRCONFTELA
           AND AM.DTINIVIGENCIA = :DTINIVIGENCIA
    ";

    const VALIDA_MODO_HABILITADO = "
        SELECT CDFILIAL, CDCAIXA, IDHABCAIXAVENDA
          FROM CAIXA
         WHERE CDFILIAL = :CDFILIAL
           AND CDCAIXA = :CDCAIXA
           AND IDHABCAIXAVENDA = :IDHABCAIXAVENDA
    ";

    const VALIDA_VENDEDOR = "
        SELECT CDVENDEDOR, CDFILIAL, CDCAIXA, CDOPERADOR,
              NMFANVEN
          FROM VENDEDOR
        WHERE CDVENDEDOR = :CDVENDEDOR
    ";

    const VALIDA_OPERADOR = "
        SELECT CDOPERADOR, CDSENHAOPERWEB, NMOPERADOR
         FROM OPERADOR
        WHERE CDOPERADOR = :CDOPERADOR
    ";

    const OPERADOR_FILIAL = "
        SELECT CDOPERADOR, CDFILIAL
          FROM FILIOPER
         WHERE CDOPERADOR = :CDOPERADOR
           AND CDFILIAL = :CDFILIAL
    ";

    const GET_EMPRESAFILIAL = "
      SELECT F.CDINSCESTA, E.CDEMPRESA, E.CDPAIS, E.SGESTADO, E.CDMUNICIPIO, F.NRINSJURFILI, E.NMRAZSOCEMPR
        FROM FILIAL F, EMPRESA E
       WHERE F.CDFILIAL = :CDFILIAL
         AND F.NRORG    = :NRORG
         AND E.NRORG    = F.NRORG
         AND E.CDEMPRESA = F.CDEMPRESA
    ";

    const GET_ENDEFILI = "
        SELECT SGESTADO, CDMUNICIPIO, CDPAIS
           FROM ENDEFILI
          WHERE CDFILIAL     = :CDFILIAL
            AND IDTPENDEFILI = 'P'
            AND NRORG        = :NRORG
    ";

    const GET_DADOSEMITENTE_IDE_XML = "
        SELECT F.NRINSJURFILI, UPPER(F.NMRAZSOCFILI) NMRAZSOCFILI, UPPER(F.NMFILIAL) NMFILIAL, UPPER(EF.DSENDEFILI) DSENDEFILI,
               EF.NRENDEFILISEP,UPPER(EF.NMBAIRFILI) NMBAIRFILI,M.CDMUNICIBGE,UPPER(M.NMMUNICIPIO) NMMUNICIPIO, EF.SGESTADO,
               EF.NRCEPFILI, P.CDPAISBACEN,UPPER(P.NMPAIS) NMPAIS,F.CDINSCESTA,F.CDINSCMUNI,E.CDCNAE,PE.CDSITUCRT, ES.CDESTADOIBGE,
               LJ.CDPRODTAXASERV AS CDPRODTAXSER, PV.IDAMBTRABNFCE, PV.NRCERTDIGNFCE, PV.CDIDTOKENPROD, PV.NMARQCERTNFCE, PV.DSSENHACERTNFCE, PV.CDCODSCONSPROD,
               PV.CDCODSCONSHOMO, PV.CDIDTOKENHOMO, PV.CDURLWSNFC, LJ.CDPRODTAXAENTR
          FROM FILIAL F, ENDEFILI EF, MUNICIPIO M, PAIS P, PARAMEMPRESA PE, PARAVEND PV,
               EMPRESA E, ESTADO ES, LOJA LJ
         WHERE F.CDFILIAL      = PV.CDFILIAL
           AND F.CDFILIAL      = :CDFILIAL
           AND F.CDFILIAL      = LJ.CDFILIAL
           AND F.NRORG         = LJ.NRORG
           AND F.NRORG         = :NRORG
           AND EF.CDFILIAL     = F.CDFILIAL
           AND EF.NRORG        = F.NRORG
           AND EF.IDTPENDEFILI = 'P'
           AND ES.SGESTADO     = EF.SGESTADO
           AND ES.CDPAIS       = EF.CDPAIS
           AND M.CDMUNICIPIO   = EF.CDMUNICIPIO
           AND M.SGESTADO      = EF.SGESTADO
           AND P.CDPAIS        = EF.CDPAIS
           AND E.CDEMPRESA     = F.CDEMPRESA
           AND E.NRORG         = F.NRORG
           AND PE.CDEMPRESA    = E.CDEMPRESA
           AND PE.NRORG        = E.NRORG
    ";

    const GET_SERIE_NFCE = "
        SELECT CDSERIECX, CDCAIXA
          FROM CAIXA
         WHERE CDFILIAL = :CDFILIAL
           AND CDCAIXA  = :CDCAIXA
           AND NRORG    = :NRORG
    ";

    const GET_PAYMENTS = "
		SELECT TR.NMTIPORECE, MC.VRMOVIVEND, TR.IDTIPORECE, MC.IDTIPOMOVIVE, TR.CDBANCARTCR, TR.CDADMINCART,
            MC.CDNSUHOSTTEF, MC.QTPARCRECEB, TR.CDBANCARTSEFAZ, TR.IDDESABTEF
          FROM TIPORECE TR, MOVCAIXA MC
        WHERE MC.CDFILIAL     = :CDFILIAL
            AND MC.CDCAIXA    = :CDCAIXA
            AND MC.NRSEQVENDA = :NRSEQVENDA
            AND TR.CDTIPORECE = MC.CDTIPORECE
    ";

    const GET_DADOSPRODIMP_XML = "
        SELECT I.CDFILIAL,
            I.CDIMPOSTO, AL.CDCFOPPFIS, P.CDCEST, AL.VRPERCREDUCAO,
            I.CDCAIXA, I.NRSEQVENDA, I.NRSEQUITVEND, I.VRPEALPRODIT, I.VRPERPIS, AL.VRALIQPIS,
            I.VRPERCOFINS, I.CDCSTPRODI, I.CDCSTPRODPC, I.VRBASECALCICMS, P.CDARVPROD,
            P.NMPRODUTO, P.CDCLASFISC, P.SGUNIDADE, I.VRTOTTRIBIBPT, I.VRALIQFCP, I.VRIMPICMSFCP,
            AL.VRPERCREDUCAOEF, AL.VRPEALIMPFISEF, AL.IDTPIMPOSFIS,
            ISNULL(AL.CDCBENEF, P.CDCBENEF) CDCBENEF, I.VRBASECALCREDUZ
          FROM ITVENDAIMPOS I
            JOIN PRODUTO P
            ON P.CDPRODUTO = :CDPRODUTO
            AND P.NRORG    = I.NRORG
          JOIN ALIQIMPFIS AL
            ON AL.CDPRODUTO    = P.CDPRODUTO
            AND AL.CDFILIAL    = I.CDFILIAL
            AND AL.NRORG       = I.NRORG
          WHERE I.CDFILIAL   = :CDFILIAL
            AND I.CDCAIXA      = :CDCAIXA
            AND I.NRSEQVENDA   = :NRSEQVENDA
            AND I.NRSEQUITVEND = :NRSEQUITVEND
            AND I.NRORG        = :NRORG
      ";

      const GET_IDTIPORECE = "
          SELECT CDTIPORECE, NMTIPORECE, IDTIPORECE, CDADMINCART, CDBANCARTCR, IDDESABTEF
            FROM TIPORECE
          WHERE CDTIPORECE = :CDTIPORECE
            AND NRORG      = :NRORG
      ";

      const GET_PRODUCTS_TO_PRINT_NFCE = "
          SELECT IV.NRSEQUITVEND, IV.QTPRODVEND, IV.VRUNITVEND, IV.VRUNITVENDCL,
                 IM.IDTPIMPOSFIS, IP.VRPEALPRODIT, (IV.VRDESITVEND) AS VRTOTDESC,
                 PR.NMPRODUTO, PR.SGUNIDADE, LJ.CDPRODTAXASERV, PR.IDPESAPROD, LJ.CDPRODTAXAENTR,
                 ROUND((IV.QTPRODVEND*(IV.VRUNITVEND + ISNULL(IV.VRUNITVENDCL,0))),2, 1) AS VRTOTITEM,
                 (IV.VRACRITVEND- CASE WHEN IV.VRRATTXSERV IS NULL THEN 0 ELSE IV.VRRATTXSERV END) AS VRTOTACRE,
                 CASE WHEN VE.VRTXSEVENDA IS NULL THEN 0 ELSE VE.VRTXSEVENDA END AS VRTXSEVENDA,
                 PR.CDPRODUTO, CASE WHEN PR.CDBARPRODUTO IS NULL THEN PR.CDARVPROD ELSE PR.CDBARPRODUTO END AS CDARVPROD,
                 SUM(CASE MC.IDTIPOMOVIVE
                     WHEN 'E' THEN MC.VRMOVIVEND
                     WHEN 'T' THEN MC.VRMOVIVEND
                     ELSE 0
                      END) AS VRENTRADA,
                 SUM(CASE MC.IDTIPOMOVIVE
                     WHEN 'S' THEN MC.VRMOVIVEND
                     ELSE 0
                      END) AS VRSAIDA
            FROM VENDA VE
            JOIN ITEMVENDA IV ON IV.CDFILIAL   = VE.CDFILIAL
                             AND IV.CDCAIXA    = VE.CDCAIXA
                             AND IV.NRSEQVENDA = VE.NRSEQVENDA

            JOIN PRODUTO PR  ON PR.CDPRODUTO  = IV.CDPRODUTO
            JOIN MOVCAIXA MC ON MC.CDFILIAL   = VE.CDFILIAL
                            AND MC.CDCAIXA    = VE.CDCAIXA
                            AND MC.NRSEQVENDA = VE.NRSEQVENDA
            JOIN ITVENDAIMPOS IP ON IP.CDFILIAL   = VE.CDFILIAL
                                AND IP.CDCAIXA    = VE.CDCAIXA
                                AND IP.NRSEQVENDA = VE.NRSEQVENDA
                                AND IP.NRSEQUITVEND = IV.NRSEQUITVEND
            JOIN IMPOSTO IM ON IM.CDIMPOSTO  = IP.CDIMPOSTO
                JOIN LOJA LJ    ON LJ.CDFILIAL = VE.CDFILIAL
                               AND LJ.CDLOJA = VE.CDLOJA
          WHERE VE.CDFILIAL   = :CDFILIAL
            AND VE.CDCAIXA    = :CDCAIXA
            AND VE.NRSEQVENDA = :NRSEQVENDA
          GROUP BY
            IV.NRSEQUITVEND, IV.QTPRODVEND, IV.VRUNITVEND, IV.VRUNITVENDCL, IV.VRACRITVEND, IV.VRRATTXSERV, IV.VRDESITVEND, VE.VRTXSEVENDA,
            PR.CDPRODUTO, PR.CDBARPRODUTO, PR.CDARVPROD, PR.NMPRODUTO, PR.SGUNIDADE, IM.IDTPIMPOSFIS, IP.VRPEALPRODIT, LJ.CDPRODTAXASERV, PR.IDPESAPROD, LJ.CDPRODTAXAENTR
          ORDER BY
            IV.NRSEQUITVEND
      ";

      const GET_CONSUMER_SALE = "
          SELECT
            VE.CDFILIAL, VE.CDCAIXA, VE.NRSEQVENDA, VE.DTENTRVENDA, VE.NRNOTAFISCALCE,
            VE.NRACESSONFCE, VE.NRINSCRCONS, VE.NMCONSVEND, VE.CDCLIENTE, VE.CDCONSUMIDOR,
            CO.NMCONSUMIDOR, CO.NRCPFRESPCON, CL.IDTPIJURCLIE, CL.NRINSJURCLIE, CO.VRMAXDEBCONS,
            CO.VRMAXCREDCONS, CO.VRMESUBCONS, CL.NMFANTCLIE, VE.DSENDECONSVENDA, IE.NMBAIRIEST,
            MN.CDMUNICIBGE, MN.NMMUNICIPIO, ES.CDESTADOIBGE, ES.SGESTADO
          FROM
            VENDA VE JOIN FILIAL FI
                      ON VE.CDFILIAL = FI.CDFILIAL
            LEFT JOIN INSCRESTAD IE
                      ON FI.CDINSCESTA = IE.CDINSCESTA
                    AND FI.CDEMPRESA = IE.CDEMPRESA
            LEFT JOIN ESTADO ES
                    ON IE.CDPAIS = ES.CDPAIS
                    AND IE.SGESTADO = ES.SGESTADO
            LEFT JOIN MUNICIPIO MN
                      ON IE.CDPAIS = MN.CDPAIS
                      AND IE.SGESTADO = MN.SGESTADO
                      AND IE.CDMUNICIPIO = MN.CDMUNICIPIO
            LEFT JOIN CLIENTE CL
                      ON VE.CDCLIENTE = CL.CDCLIENTE
            LEFT JOIN CONSUMIDOR CO
                      ON VE.CDCLIENTE = CO.CDCLIENTE
                    AND VE.CDCONSUMIDOR = CO.CDCONSUMIDOR
          WHERE
            (VE.CDFILIAL   = :CDFILIAL  ) AND
            (VE.CDCAIXA    = :CDCAIXA   ) AND
            (VE.NRSEQVENDA = :NRSEQVENDA)
      ";

      const VALIDA_SUPERVISOR = "
          SELECT CDOPERADOR
            FROM OPERGRUPOP
          WHERE CDOPERADOR = :CDOPERADOR
            AND CDGRUPOPER IN (SELECT CDGRUPOPER
                                  FROM AUTGRUPOP
                                WHERE IDAUTGRUPOP = 'SFOS')
      ";

      const CONTROLE_ACESSO = "
          SELECT OP.CDOPERADOR, GP.CDGRUPOPER, GP.DSSENHACAIXA
            FROM GRUPOPER GP, OPERGRUPOP OP
          WHERE OP.CDOPERADOR = :CDOPERADOR
            AND GP.CDGRUPOPER = OP.CDGRUPOPER
      ";

      const GET_FILIAIS_BY_OPERADOR = "
          SELECT DISTINCT  FL.NMFILIAL,'' AS NMLOJA, C.CDFILIAL||'_'||C.CDLOJA  AS FILIPAI,
                F.CDOPERADOR, C.CDFILIAL AS PARENT , F.CDFILIAL, C.CDLOJA,
                'Loja:'|| C.CDLOJA||' - '||LJ.NMLOJA  AS DESCRICAO,
                '' AS FILILOJA, '' AS CDCAIXA, '' AS IDTPTEF
            FROM CAIXA C JOIN FILIOPER F
                          ON C.CDFILIAL = F.CDFILIAL
                          AND F.NRORG = C.NRORG
                        JOIN FILIAL FL
                          ON F.CDFILIAL = FL.CDFILIAL
                          AND F.NRORG = FL.NRORG
                        JOIN LOJA LJ
                          ON C.CDLOJA= LJ.CDLOJA
                          AND C.CDFILIAL = LJ.CDFILIAL
                          AND C.NRORG = LJ.NRORG
                        JOIN OPERADOR O
                          ON F.CDOPERADOR = O.CDOPERADOR
                          AND O.NRORGTRAB = F.NRORG
          WHERE F.CDOPERADOR = :CDOPERADOR
            AND LJ.IDATIVO = 'S'
            AND C.IDATIVO = 'S'

          UNION

          ---PAI----
          SELECT F.NMFILIAL, '' AS NMLOJA, F.CDFILIAL AS FILIPAI, FL.CDOPERADOR, '' AS PARENT,
                F.CDFILIAL AS FILIPAI, '' AS CDLOJA,  'Filial:'||F.CDFILIAL||' - '||F.NMFILIAL AS DESCRICAO,
                '' AS FILILOJA, '' AS CDCAIXA, '' AS IDTPTEF
            FROM FILIOPER FL JOIN FILIAL F
                              ON F.CDFILIAL = FL.CDFILIAL
                              AND F.NRORG = FL.NRORG
                            JOIN LOJA L
                              ON FL.CDFILIAL = L.CDFILIAL
                              AND FL.NRORG = L.NRORG
                            JOIN CAIXA C
                              ON FL.CDFILIAL = C.CDFILIAL
                              AND FL.NRORG = C.NRORG
                            JOIN OPERADOR O
                              ON FL.CDOPERADOR = O.CDOPERADOR
                              AND O.NRORGTRAB = F.NRORG
          WHERE FL.CDOPERADOR = :CDOPERADOR
            AND F.IDATIVO = 'S'

          UNION

          --- PDV ----
          SELECT DISTINCT  FL.NMFILIAL AS NMFILIAL, LJ.NMLOJA AS NMLOJA,
                C.CDFILIAL||'_'||C.CDLOJA||'_'||C.CDCAIXA  AS FILIPAI, F.CDOPERADOR,
                C.CDFILIAL||'_'||C.CDLOJA AS PARENT , F.CDFILIAL,C.CDLOJA AS CDLOJA,
                'PDV:'|| C.CDCAIXA||' - '||C.NMCAIXA AS DESCRICAO,
                'Filial:'||F.CDFILIAL||' - '||FL.NMFILIAL||'  Loja:'||C.CDLOJA||'    PDV:'||C.CDCAIXA||' - '||C.NMCAIXA AS FILILOJA,
                C.CDCAIXA AS CDCAIXA, C.IDTPTEF AS IDTPTEF
            FROM CAIXA C JOIN FILIOPER F
                          ON C.CDFILIAL = F.CDFILIAL
                          AND C.NRORG = F.NRORG
                        JOIN FILIAL FL
                          ON F.CDFILIAL = FL.CDFILIAL
                          AND FL.NRORG = F.NRORG
                        JOIN LOJA LJ
                          ON C.CDLOJA= LJ.CDLOJA
                          AND C.CDFILIAL = LJ.CDFILIAL
                          AND C.NRORG = LJ.NRORG
                        JOIN OPERADOR O
                          ON F.CDOPERADOR = O.CDOPERADOR
                          AND O.NRORGTRAB = F.NRORG
          WHERE F.CDOPERADOR = :CDOPERADOR
            AND C.IDATIVO = 'S'
      ";

    const BUSCA_DADOS_FILIAL = "
        SELECT F.CDFILIAL, P.CDCLIENTE, F.NMFILIAL, F.NRORG, P.IDEXTCONSONLINE, F.NRINSJURFILI
          FROM FILIAL F JOIN PARAVEND P
                          ON P.CDFILIAL = F.CDFILIAL
         WHERE F.CDFILIAL = :CDFILIAL
    ";

    const BUSCA_DADOS_CAIXA_PARAM = "
        SELECT CDFILIAL, CDCAIXA, CDLOJA, IDTPEMISSAOFOS, IDCOLETOR, IDTPTEF, IDHABCAIXAVENDA, CDTERTEF
          FROM CAIXA
         WHERE CDFILIAL = :CDFILIAL
           AND CDCAIXA = :CDCAIXA
    ";

    const BUSCA_DADOS_LOJA_PARAM = "
        SELECT NRMESAPADRAO
          FROM LOJA
         WHERE CDFILIAL = :CDFILIAL
           AND CDLOJA = :CDLOJA
    ";

    const BUSCA_DADOS_VENDEDOR_PARAM = "
        SELECT CDVENDEDOR, NMFANVEN, CDOPERADOR
          FROM VENDEDOR
         WHERE CDVENDEDOR = :CDVENDEDOR
    ";

    const BUSCA_DADOS_PARAVEND = "
        SELECT CDFILIAL, NRATRAPADRAO
          FROM PARAVEND
         WHERE CDFILIAL = :CDFILIAL
    ";

    const BUSCA_OBSERVACOES = "
        SELECT DISTINCT PR.CDOCORR, OC.DSOCORR, '#' + OC.NRCORSINAL AS NRCORSINAL, GR.CDGRPOCOR,
               OC.IDCONTROLAOBS, OC.DSENDEIMGOCORR, OC.CDPRODUTO, OC.IDCONTROLAOBS,
               OC.DSAPELIDOOBS, OC.IDEXIOBSAPPCONS, OCO.CDGRUPOOBRIG, GRO.NMGRUPOBRIG
          FROM GRUPOCOR GR JOIN PRODOCORRE PR
                             ON GR.CDGRPOCOR = PR.CDGRPOCOR
                           JOIN LOJA LJ
                             ON GR.CDGRPOCOR = LJ.CDGRPOCORPED
                           JOIN OCORRENCIA OC
                             ON PR.CDGRPOCOR = OC.CDGRPOCOR
                            AND PR.CDOCORR   = OC.CDOCORR
                            JOIN GRUPOBRIGOCOR GRO
                                   ON GRO.CDGRPOCOR = PR.CDGRPOCOR
                            JOIN PRODOBRIGOCOR OCO
                                   ON OCO.CDGRPOCOR    = PR.CDGRPOCOR
                                  AND OCO.CDOCORR      = PR.CDOCORR
                                  AND OCO.CDGRUPOOBRIG = GRO.CDGRUPOOBRIG
         WHERE LJ.CDFILIAL = :CDFILIAL
           AND LJ.CDLOJA   = :CDLOJA
        UNION
        SELECT DISTINCT PR.CDOCORR, OC.DSOCORR, '#' + OC.NRCORSINAL AS NRCORSINAL, GR.CDGRPOCOR,
               OC.IDCONTROLAOBS, OC.DSENDEIMGOCORR, OC.CDPRODUTO, OC.IDCONTROLAOBS,
               OC.DSAPELIDOOBS, OC.IDEXIOBSAPPCONS, null as CDGRUPOOBRIG, null as NMGRUPOBRIG
          FROM GRUPOCOR GR JOIN PRODOCORRE PR
                             ON GR.CDGRPOCOR = PR.CDGRPOCOR
                           JOIN LOJA LJ
                             ON GR.CDGRPOCOR = LJ.CDGRPOCORPED
                           JOIN OCORRENCIA OC
                             ON PR.CDGRPOCOR = OC.CDGRPOCOR
                            AND PR.CDOCORR   = OC.CDOCORR
         WHERE OC.CDGRPOCOR+PR.CDOCORR NOT IN (SELECT CDGRPOCOR+CDOCORR FROM PRODOBRIGOCOR)
           AND LJ.CDFILIAL = :CDFILIAL
           AND LJ.CDLOJA   = :CDLOJA
    ";

    const BUSCA_AMBIENTES = "
        SELECT IDTPBUTTON, IDTPBUTONAUX, NRBUTTON, NRBUTTONAUX,
               DSBUTTON, CDIDENTBUTON, NRCOLORTEXT, NRCOLORBACK,
               NRPGCONFTELA, NRPGCONFTAUX, DSMESSAGEBUT, DSIMAGEMBUT
          FROM (SELECT AM.IDTPBUTTON, AM.IDTPBUTONAUX, AM.NRBUTTON, AM.NRBUTTONAUX,
                       AM.DSBUTTON, AM.CDIDENTBUTON, AM.NRCOLORTEXT, AM.NRCOLORBACK,
                       AM.NRPGCONFTELA, AM.NRPGCONFTAUX, AM.DSMESSAGEBUT, AM.DSIMAGEMBUT
                  FROM ITMENUCONFTE AM
                 WHERE AM.CDFILIAL = :CDFILIAL
                   AND AM.NRCONFTELA = :NRCONFTELA
                   AND AM.DTINIVIGENCIA = :DTINIVIGENCIA) MESA
         WHERE IDTPBUTTON = '4'
         ORDER BY NRPGCONFTELA, NRBUTTON
    ";

    const BUSCA_MESAS = "
        SELECT IDTPBUTTON, IDTPBUTONAUX, NRBUTTON, NRBUTTONAUX,
               DSBUTTON, CDIDENTBUTON, NRCOLORTEXT, NRCOLORBACK,
               NRPGCONFTELA, NRPGCONFTAUX, DSMESSAGEBUT, DSIMAGEMBUT
          FROM (SELECT AM.IDTPBUTTON, AM.IDTPBUTONAUX, AM.NRBUTTON, AM.NRBUTTONAUX,
                       AM.DSBUTTON, AM.CDIDENTBUTON, AM.NRCOLORTEXT, AM.NRCOLORBACK,
                       AM.NRPGCONFTELA, AM.NRPGCONFTAUX, AM.DSMESSAGEBUT, AM.DSIMAGEMBUT
                  FROM ITMENUCONFTE AM
                 WHERE AM.CDFILIAL  = :CDFILIAL
                   AND AM.NRCONFTELA = :NRCONFTELA
                   AND AM.DTINIVIGENCIA = :DTINIVIGENCIA) MESA
         WHERE IDTPBUTTON = '3'
           AND IDTPBUTONAUX = '4'
           AND CDIDENTBUTON <> :NRMESAPADRAO
        ORDER BY NRPGCONFTELA, NRBUTTON
    ";

    const BUSCA_GRUPO_PRODUTOS = "
      SELECT P.FILESERVERURL, IDTPBUTTON, IDTPBUTONAUX, NRBUTTON, NRBUTTONAUX,
      DSBUTTON, CDIDENTBUTON, NRCOLORTEXT, NRCOLORBACK,
      NRPGCONFTELA, NRPGCONFTAUX, DSMESSAGEBUT, DSIMAGEMBUT,
      DSENDEIMG, DSBUTTONINGLES, DSBUTTONESPANH
      FROM (SELECT AM.IDTPBUTTON, AM.IDTPBUTONAUX, AM.NRBUTTON, AM.NRBUTTONAUX,
                  AM.DSBUTTON, COALESCE(AM.DSBUTTONINGLES, AM.DSBUTTON) AS DSBUTTONINGLES, COALESCE(AM.DSBUTTONESPANH, AM.DSBUTTON) AS DSBUTTONESPANH, AM.CDIDENTBUTON, AM.NRCOLORTEXT, AM.NRCOLORBACK,
                  AM.NRPGCONFTELA, AM.NRPGCONFTAUX, AM.DSMESSAGEBUT, AM.DSIMAGEMBUT,
                  AM.DSENDEIMG
            FROM ITMENUCONFTE AM
            WHERE AM.CDFILIAL   = :CDFILIAL
              AND AM.NRCONFTELA = :NRCONFTELA
              AND AM.DTINIVIGENCIA = :DTINIVIGENCIA) MESA,
      PARAMGERAL P
      WHERE IDTPBUTTON = '2'
      ORDER BY NRPGCONFTELA, NRBUTTON
    ";

    const BUSCA_SUBGRUPO_PRODUTOS = "
        SELECT P.FILESERVERURL, IDTPBUTTON, IDTPBUTONAUX, NRBUTTON, NRBUTTONAUX,
               DSBUTTON, CDIDENTBUTON, NRCOLORTEXT, NRCOLORBACK,
               NRPGCONFTELA, DSBUTTONINGLES, DSBUTTONESPANH, NRPGCONFTAUX, DSMESSAGEBUT, DSIMAGEMBUT,
               DSENDEIMG
          FROM ITMENUCONFTE I, PARAMGERAL P
         WHERE I.IDTPBUTTON = 'I'
           AND I.CDFILIAL = :CDFILIAL
           AND I.NRCONFTELA = :NRCONFTELA
           AND I.DTINIVIGENCIA = :DTINIVIGENCIA
    ";

    const BUSCA_PRODUTOS_PARAM = "
        SELECT P.FILESERVERURL, IDTPBUTTON, IDTPBUTONAUX, NRBUTTON,
               CDIMPOSTO, VRPEALIMPFIS, CDCSTICMS, CDCLASFISC,
               CDCSTPISCOF, CDCFOPPFIS, VRALIQPIS, VRALIQCOFINS,
               NRBUTTONAUX, DSBUTTON,
               DSBUTTONINGLES, DSBUTTONESPANH, CDIDENTBUTON,
               NRCOLORTEXT, NRCOLORBACK, NRPGCONFTELA,
               NRPGCONFTAUX, DSMESSAGEBUT, DSIMAGEMBUT,
               IDSOLLOCIMP, IDPESAPROD, CDARVPROD,
               CDPRODINTE, CDBARPRODUTO, IDTIPOPROD,
               IDTIPOCOMPPROD, IDIMPPRODUTO, DTINIVGPROMOC, DTFINVGPROMOC,
               CDPRODUTO, IDCONTROLAREFIL, MESA.CDFILIAL, NRCONFTELA, DTINIVIGENCIA,
               DSENDEIMG, IDTPBUTONAUX2, NRBUTTONAUX2, NRPGCONFTAUX2, DSPRODVENDA,
               IDPRODBLOQ, DSADICPROD, NRQTDMINOBS, CDPROTECLADO
          FROM (SELECT AL.CDIMPOSTO, AL.VRPEALIMPFIS, AL.CDCSTICMS, AL.CDCSTPISCOF,
                       AL.CDCFOPPFIS, AL.VRALIQPIS, AL.VRALIQCOFINS, PR.CDCLASFISC,
                       AM.IDTPBUTTON, AM.IDTPBUTONAUX, AM.NRBUTTON, AM.NRBUTTONAUX,
                       AM.DSBUTTON,
                       COALESCE(AM.DSBUTTONINGLES, AM.DSBUTTON) AS DSBUTTONINGLES,
                       COALESCE(AM.DSBUTTONESPANH, AM.DSBUTTON) AS DSBUTTONESPANH,
                       AM.CDIDENTBUTON, AM.NRCOLORTEXT, AM.NRCOLORBACK,
                       AM.NRPGCONFTELA, AM.NRPGCONFTAUX, AM.DSMESSAGEBUT,
                       AM.DSIMAGEMBUT, PR.IDSOLLOCIMP, PR.IDPESAPROD, PR.CDARVPROD,
                       PR.CDPRODINTE, PR.CDBARPRODUTO, PR.IDTIPOPROD, PR.IDTIPOCOMPPROD,
                       PR.IDIMPPRODUTO, PR.DTINIVGPROMOC, PR.DTFINVGPROMOC, PR.CDPRODUTO,
                       PR.IDCONTROLAREFIL, AM.CDFILIAL, AM.NRCONFTELA, AM.DTINIVIGENCIA,
                       CASE WHEN AM.DSENDEIMG IS NULL THEN PR.DSENDEIMG ELSE AM.DSENDEIMG END AS DSENDEIMG, AM.IDTPBUTONAUX2,
                       AM.NRBUTTONAUX2, AM.NRPGCONFTAUX2, PR.DSPRODVENDA, PR.DSADICPROD, PR.NRQTDMINOBS, PR.CDPROTECLADO,
                       CASE WHEN PB.CDPRODUTO IS NULL THEN PR.IDPRODBLOQ ELSE 'S' END IDPRODBLOQ

                  FROM ITMENUCONFTE AM

                  JOIN PRODUTO PR
                    ON AM.CDIDENTBUTON = PR.CDPRODUTO
                  JOIN CONFTELA CF
                    ON AM.CDFILIAL = CF.CDFILIAL
                   AND AM.NRCONFTELA = CF.NRCONFTELA
                   AND AM.DTINIVIGENCIA = CF.DTINIVIGENCIA
             LEFT JOIN ALIQIMPFIS AL
                    ON AL.CDFILIAL = :CDFILIAL
                   AND AL.CDPRODUTO = PR.CDPRODUTO
                   LEFT JOIN PRODBLOQVND PB
                        ON PB.CDFILIAL = :CDFILIAL
                        AND PB.CDLOJA = :CDLOJA
                        AND PB.CDPRODUTO = AM.CDIDENTBUTON
                        AND (PB.NRDIASEMANABLOQ = 'T'
                             OR PB.NRDIASEMANABLOQ = :NRDIASEMANABLOQ)

                 WHERE AM.CDFILIAL = :FILIALVIGENCIA
                   AND AM.NRCONFTELA = :NRCONFTELA
                   AND AM.DTINIVIGENCIA = :DTINIVIGENCIA) MESA,

          PARAMGERAL P
          WHERE IDTPBUTTON = '1'
          AND IDTPBUTONAUX IN ('2', 'I')
          ORDER BY NRPGCONFTAUX, NRBUTTONAUX, NRPGCONFTELA, NRBUTTON
    ";

    const BUSCA_OBSERVACOES_PRODUTO = "
        SELECT PR.CDPRODUTO, PR.CDOCORR, OC.DSOCORR, OC.CDGRPOCOR,
               OC.IDCONTROLAOBS
          FROM GRUPOCOR GR, PRODOCORRE PR, LOJA LJ, OCORRENCIA OC
         WHERE LJ.CDFILIAL  = :CDFILIAL
           AND LJ.CDLOJA    = :CDLOJA
           AND GR.CDGRPOCOR = LJ.CDGRPOCORPED
           AND PR.CDGRPOCOR = GR.CDGRPOCOR
           AND GR.CDGRPOCOR = OC.CDGRPOCOR
           AND PR.CDOCORR   = OC.CDOCORR
           AND PR.CDPRODUTO NOT IN (SELECT CDPRODUTO
                                      FROM PRODBLOQVND
                                     WHERE CDFILIAL = :CDFILIAL
                                       AND CDLOJA = :CDLOJA
                                       AND (NRDIASEMANABLOQ = 'T'
                                            OR  NRDIASEMANABLOQ = :NRDIASEMANABLOQ))
    ";

    const SQL_PRECOS = "
        SELECT CDPRODUTO, VRPRECITEM, ISNULL(VRPRECITEMCL, 0) AS VRPRECITEMCL,
               HRINIVENPROD, HRFIMVENPROD, 0 AS VRDESITVEND, 0 AS VRACRITVEND
          FROM ITEMPRECO
         WHERE CDFILIAL = :CDFILIAL
           AND CDTABEPREC = :CDTABEPREC
           AND DTINIVGPREC = CONVERT(DATETIME, :DTINIVGPREC, 120)
    ";

    const SQL_PRECOS_DIA = "
        SELECT VRPRECODIA, IDPERVALORPR, IDDESCACREPR, IDVISUACUPOM, CDTIPOCONSPD, CDPRODUTO
          FROM ITEMPRECODIA
         WHERE CDFILIAL       = :CDFILIAL
           AND CDTABEPREC     = :CDTABEPREC
           AND DTINIVGPREC    = CONVERT(DATETIME, :DTINIVGPREC, 120)
           AND ((NRDIASEMANPR = :NRDIASEMANPR) OR (NRDIASEMANPR = 'T'))
           AND :HORA BETWEEN HRINIPRECDIA AND HRFINPRECDIA
           AND (CONVERT(VARCHAR, GETDATE(), 103) BETWEEN DTINIVALPREC AND DTFINVALPREC)
           AND CDTIPOCONSPD = :CDTIPOCONSPD
    ";

    const VAL_TABE = "
        SELECT DTINIVGPREC
          FROM TABEVEND
         WHERE CDFILIAL = :CDFILIAL
           AND CDTABEPREC = :CDTABEPREC
           AND (:DATE BETWEEN DTINIVGPREC AND DTFINVGPREC)
    ";

    const GET_IMP_PRODUTOS = "
        SELECT PA.CDAMBIENTE, IL.NRSEQIMPRLOJA, IL.NMIMPRLOJA, IL.DSIPIMPR
          FROM PRODAMBIENTE PA JOIN IMPRLOJA IL
                                 ON IL.CDFILIAL      = PA.CDFILIAL
                                AND IL.CDLOJA        = PA.CDLOJA
                                AND IL.NRSEQIMPRLOJA = PA.NRSEQIMPRLOJA
         WHERE PA.CDFILIAL   = :CDFILIAL
           AND PA.CDLOJA     = :CDLOJA
           AND PA.CDPRODUTO  = :CDPRODUTO
           AND PA.NRCONFTELA = :NRCONFTELA
           AND PA.DTINIVIGENCIA = :DTINIVIGENCIA
    ";

    const GRUPOS_PROMO_INT = "
        SELECT G.CDGRUPROMOC, G.NRORDPROMOGRUP, P.NMGRUPROMOC, G.QTPRGRUPPROMOC,
               G.QTPRGRUPROMIN, G.CDGRUPMUTEX, P.DSENDEIMGGRUPROMOC, G.IDIMPGRPROMO
          FROM GRUPROMOCPROD G JOIN GRUPROMOC P
                                 ON G.CDGRUPROMOC = P.CDGRUPROMOC
        WHERE G.CDPRODPROMOCAO = :CDIDENTBUTON
        ORDER BY NRORDPROMOGRUP
    ";

    const GRUPOS_PROMO_INT_FILI = "
        SELECT G.CDGRUPROMOC, G.NRORDPROMOGRUPFIL AS NRORDPROMOGRUP, P.NMGRUPROMOC,
               G.QTPRGRUPPROMFIL AS QTPRGRUPPROMOC, G.QTPRGRUPROMINFIL AS QTPRGRUPROMIN,
               G.CDGRUPMUTEXFIL AS CDGRUPMUTEX, P.DSENDEIMGGRUPROMOC, G.IDIMPGRPROMOFIL AS IDIMPGRPROMO
          FROM GRUPROMOCPRFIL G JOIN GRUPROMOC P
                                  ON G.CDGRUPROMOC = P.CDGRUPROMOC
         WHERE G.CDPRODPROMOCAO = :CDIDENTBUTON
           AND G.CDFILIAL = :CDFILIAL
    ";

    const SMART_PROMO_PRODUCTS = "
        SELECT DISTINCT P.CDPRODPROMOCAO, 'PAD' AS FLD
          FROM GRUPROMOCPROD P
          JOIN GRUPROMOC G ON G.CDGRUPROMOC = P.CDGRUPROMOC
    ";

    const SMART_PROMO_PRODUCTS_FILI = "
        SELECT DISTINCT P.CDPRODPROMOCAO, 'FIL' AS FLD
          FROM GRUPROMOCPRFIL P
          JOIN GRUPROMOC G ON G.CDGRUPROMOC = P.CDGRUPROMOC
         WHERE P.CDFILIAL = :CDFILIAL
    ";

    const PRODUTOS_PROMO_INT = "
        SELECT DP.CDPRODPROMOCAO, PP.CDGRUPROMOC, PP.CDPRODUTO, P.CDARVPROD, P.NMPRODUTO, P.IDIMPPRODUTO,
               AL.CDIMPOSTO, AL.VRPEALIMPFIS, AL.CDCSTICMS, AL.CDCSTPISCOF, P.IDTIPOCOMPPROD,
               AL.CDCFOPPFIS, AL.VRALIQPIS, AL.VRALIQCOFINS, P.CDCLASFISC,
               CASE WHEN DP.IDPERVALORDES IS NULL THEN 'P' ELSE DP.IDPERVALORDES END AS IDPERVALORDES,  '0' AS IDCTRLMODIFY,
               CASE WHEN DP.VRDESPRODPROMOC IS NULL THEN 0 ELSE DP.VRDESPRODPROMOC END AS VRDESPRODPROMOC, DP.IDDESCACRPROMO,
               CASE WHEN DP.IDAPLICADESCPR IS NULL THEN 'T' ELSE DP.IDAPLICADESCPR END AS IDAPLICADESCPR,
               P.DSPRODVENDA, PP.DSENDEIMGPROMO, P.CDPROTECLADO, P.IDPESAPROD,
               PR.FILESERVERURL, DP.IDOBRPRODSELEC AS IDOBRPRODSELEC,
               PP.DSAPELIDOMOB, PP.NRORDPROMOPR, P.DSADICPROD, CASE WHEN DP.IDPRODPRESELEC IS NULL THEN 'N' ELSE DP.IDPRODPRESELEC END AS IDPRODPRESELEC, P.NRQTDMINOBS,
               CASE WHEN PB.CDPRODUTO IS NULL THEN P.IDPRODBLOQ ELSE 'S' END IDPRODBLOQ, P.IDCONTROLAREFIL AS REFIL
          FROM PARAMGERAL PR, PRODUTO P JOIN PRODGRUPROMOC PP
                           ON P.CDPRODUTO = PP.CDPRODUTO
                          AND PP.CDGRUPROMOC = :CDGRUPROMOC
                    LEFT JOIN DESGRUPROMOCPR DP
                           ON DP.CDPRODPROMOCAO = :CDIDENTBUTON
                          AND DP.CDGRUPROMOC = PP.CDGRUPROMOC
                          AND DP.CDPRODUTO = PP.CDPRODUTO
                    LEFT JOIN ALIQIMPFIS AL
                           ON AL.CDFILIAL = :CDFILIAL
                          AND AL.CDPRODUTO = P.CDPRODUTO
                    LEFT JOIN PRODBLOQVND PB
                           ON PB.CDFILIAL = :CDFILIAL
                          AND PB.CDLOJA = :CDLOJA
                          AND PB.CDPRODUTO = P.CDPRODUTO
                          AND (PB.NRDIASEMANABLOQ = 'T'
                               OR PB.NRDIASEMANABLOQ = :NRDIASEMANABLOQ)
        ORDER BY CASE WHEN PP.NRORDPROMOPR IS NULL THEN 99999 ELSE PP.NRORDPROMOPR END, PP.CDPRODUTO
    ";

    const PRODUTOS_PROMO_INT_FILI = "
        SELECT DP.CDPRODPROMOCAO, PP.CDGRUPROMOC, PP.CDPRODUTO, P.CDARVPROD, P.NMPRODUTO, P.IDIMPPRODUTO,
               AL.CDIMPOSTO, AL.VRPEALIMPFIS, AL.CDCSTICMS, AL.CDCSTPISCOF, P.IDTIPOCOMPPROD,
               AL.CDCFOPPFIS, AL.VRALIQPIS, AL.VRALIQCOFINS, P.CDCLASFISC,
               CASE WHEN DP.IDPERVALDESFIL IS NULL THEN 'P' ELSE DP.IDPERVALDESFIL END AS IDPERVALORDES, '0' AS IDCTRLMODIFY,
               CASE WHEN DP.VRDESPRPROMOFIL IS NULL THEN 0 ELSE DP.VRDESPRPROMOFIL END AS VRDESPRODPROMOC, DP.IDDESCACRPROFIL AS IDDESCACRPROMO,
               CASE WHEN DP.IDAPLICADESCPRFI IS NULL THEN 'T' ELSE DP.IDAPLICADESCPRFI END AS IDAPLICADESCPR,
               P.DSPRODVENDA, PP.DSENDEIMGPROMO, P.CDPROTECLADO, P.IDPESAPROD,
               PR.FILESERVERURL, DP.IDOBRPRODSELFIL AS IDOBRPRODSELEC,
               PP.DSAPELIDOMOB, PP.NRORDPROMOPR, P.DSADICPROD, CASE WHEN DP.IDPRODPRESELFIL IS NULL THEN 'N' ELSE DP.IDPRODPRESELFIL END AS IDPRODPRESELEC, P.NRQTDMINOBS,
               CASE WHEN PB.CDPRODUTO IS NULL THEN P.IDPRODBLOQ ELSE 'S' END IDPRODBLOQ, P.IDCONTROLAREFIL AS REFIL
          FROM PARAMGERAL PR, PRODUTO P JOIN PRODGRUPROMOC PP
                           ON P.CDPRODUTO = PP.CDPRODUTO
                          AND PP.CDGRUPROMOC = :CDGRUPROMOC
                    LEFT JOIN DESGRUPROMPRFIL DP
                           ON DP.CDPRODPROMOCAO = :CDIDENTBUTON
                          AND DP.CDGRUPROMOC = PP.CDGRUPROMOC
                          AND DP.CDPRODUTO = PP.CDPRODUTO
                    LEFT JOIN ALIQIMPFIS AL
                           ON AL.CDFILIAL = :CDFILIAL
                          AND AL.CDPRODUTO = P.CDPRODUTO
                    LEFT JOIN PRODBLOQVND PB
                           ON PB.CDFILIAL = :CDFILIAL
                          AND PB.CDLOJA = :CDLOJA
                          AND PB.CDPRODUTO = P.CDPRODUTO
                          AND (PB.NRDIASEMANABLOQ = 'T'
                               OR PB.NRDIASEMANABLOQ = :NRDIASEMANABLOQ)
        ORDER BY CASE WHEN PP.NRORDPROMOPR IS NULL THEN 99999 ELSE PP.NRORDPROMOPR END, PP.CDPRODUTO
    ";

    const GET_COMBO_PRODUCTS = "
        SELECT AM.IDTPBUTTON, AM.IDTPBUTONAUX, AM.NRBUTTON, AM.NRBUTTONAUX,
               AM.DSBUTTON, AM.CDIDENTBUTON, AM.NRCOLORTEXT, AM.NRCOLORBACK,
               AM.NRPGCONFTELA, COALESCE(AM.DSBUTTONINGLES, AM.DSBUTTON) AS DSBUTTONINGLES, COALESCE(AM.DSBUTTONESPANH, AM.DSBUTTON) AS DSBUTTONESPANH, AM.NRPGCONFTAUX, AM.DSMESSAGEBUT, AM.DSIMAGEMBUT,
               'N' AS IDSOLLOCIMP, 'N' AS IDPESAPROD, NULL AS CDARVPROD, NULL AS CDPRODINTE,
               NULL AS CDBARPRODUTO, NULL AS IDTIPOPROD, '0' AS IDTIPOCOMPPROD, '1' AS IDIMPPRODUTO,
               NULL AS DTINIVGPROMOC, NULL AS DTFINVGPROMOC, NULL AS CDPRODUTO, 'N' AS IDCONTROLAREFIL,
               AM.CDFILIAL, AM.NRCONFTELA, AM.DTINIVIGENCIA, AM.DSENDEIMG, AM.IDTPBUTONAUX2,
               AM.NRBUTTONAUX2, AM.NRPGCONFTAUX2, 'N' AS IDPRODBLOQ, AM.NRLIMPRODCOM,
               NULL AS DSPRODVENDA, L.IDTIPCOBRA, P.NRQTDMINOBS, P.CDCLASFISC, AL.CDCFOPPFIS, AL.CDCSTICMS,
               AL.CDCSTPISCOF, AL.VRALIQPIS, AL.VRALIQCOFINS
          FROM ITMENUCONFTE AM JOIN LOJA L
                                 ON L.CDFILIAL = AM.CDFILIAL
                          LEFT JOIN PRODUTO P
                                 ON AM.CDIDENTBUTON = P.CDPRODUTO
                          LEFT JOIN ALIQIMPFIS AL
                                 ON AM.CDFILIAL = AL.CDFILIAL
                                 AND AM.CDIDENTBUTON = AL.CDPRODUTO
         WHERE AM.CDFILIAL = :CDFILIAL
           AND L.CDLOJA = :CDLOJA
           AND AM.NRCONFTELA = :NRCONFTELA
           AND AM.DTINIVIGENCIA = :DTINIVIGENCIA
           AND AM.IDTPBUTTON = 'B'
    ";

    const BUSCA_PRODUTOS_COMBO = "
        SELECT P.CDPRODUTO AS CDPRODUTO, P.IDIMPPRODUTO, 'T' AS IDAPLICADESCPR, 'P' AS IDPERVALORDES,
               CASE WHEN I.DSBUTTON IS NULL THEN P.NMPRODUTO ELSE I.DSBUTTON END AS NMPRODUTO, '.000' AS VRDESPRODPROMOC,
               'COMBO' AS NMGRUPROMOC, CASE WHEN I.CDIDENTBUTON IS NULL THEN P.CDPRODUTO ELSE I.CDIDENTBUTON END AS CDIDENTBUTON,
               CASE WHEN I.DSBUTTON IS NULL THEN P.NMPRODUTO ELSE I.DSBUTTON END AS DSOCORR, P.CDARVPROD AS CDOCORR, P.CDARVPROD,
               P.IDTIPOPROD, P.IDTIPOCOMPPROD, CASE WHEN I.DSBUTTON IS NULL THEN P.NMPRODUTO ELSE I.DSBUTTON END AS NOME, P.DSPRODVENDA AS DESCRIPTION,
               CASE WHEN PB.CDPRODUTO IS NULL THEN P.IDPRODBLOQ ELSE 'S' END AS IDPRODBLOQ, P.CDCLASFISC, AL.CDCFOPPFIS, AL.CDCSTICMS,
               AL.CDCSTPISCOF, AL.VRALIQPIS, AL.VRALIQCOFINS
          FROM PRODUTO P LEFT JOIN ITMENUCONFTE I
                                ON P.CDPRODUTO = I.CDIDENTBUTON
                               AND I.CDFILIAL = :FILIALVIGENCIA
                               AND I.NRCONFTELA = :NRCONFTELA
                               AND I.DTINIVIGENCIA = :DTINIVIGENCIA
                        LEFT JOIN PRODBLOQVND PB
                                ON PB.CDFILIAL = :CDFILIAL
                               AND PB.CDLOJA = :CDLOJA
                               AND PB.CDPRODUTO = P.CDPRODUTO
                               AND (PB.NRDIASEMANABLOQ = 'T'
                                    OR PB.NRDIASEMANABLOQ = :NRDIASEMANABLOQ)
                        LEFT JOIN ALIQIMPFIS AL
                                 ON I.CDFILIAL = AL.CDFILIAL
                                 AND I.CDIDENTBUTON = AL.CDPRODUTO
         WHERE P.IDTIPOPROD = :IDTIPOPROD
    ";

    const GET_TIPO_RECEBIMENTOS = "
        SELECT IT.DSBUTTON, TR.CDTIPORECE, TR.IDTIPORECE, TR.IDMOSTRARECE,
            IT.NRBUTTON, IT.NRCOLORBACK, IT.NRBUTTONAUX, TR.IDSANGRIAAUTO,
            TR.IDDESABTEF, TR.IDUTCONTRREPIQ
          FROM ITMENUCONFTE IT, TIPORECE TR
         WHERE IT.CDFILIAL   = :CDFILIAL
           AND IT.NRCONFTELA = :NRCONFTELA
           AND IT.DTINIVIGENCIA = :DTINIVIGENCIA
           AND IT.IDTPBUTTON = '5'
           AND IT.CDIDENTBUTON = TR.CDTIPORECE
           AND TR.IDMOSTRARECE = 'S'
        ORDER BY IT.NRPGCONFTELA, IT.NRBUTTON, TR.CDTIPORECE
    ";

    const GET_GRUPO_TIPO_RECEBIMENTOS = "
        SELECT
            NRBUTTON, DSBUTTON, NRCOLORBACK
        FROM
            ITMENUCONFTE
        WHERE
            (CDFILIAL   = :CDFILIAL    ) AND
            (NRCONFTELA = :NRCONFTELA  ) AND
            (DTINIVIGENCIA = :DTINIVIGENCIA) AND
            (IDTPBUTTON = '6'          ) AND
            (NRBUTTON IN (:NRBUTTONAUX))
    ";

    const CLIENTE_FILIAL = "
        SELECT CDTABEPREC, CDCFILTABPRE
          FROM CLIENFILIAL
          WHERE CDFILIAL = :CDFILIAL
          AND CDCLIENTE = :CDCLIENTE
    ";

    const EXISTE_PRECO_CLIE = "
        SELECT CDTABEPREC, CDFILTABPREC
          FROM CLIENTE
          WHERE (CDCLIENTE = :CDCLIENTE)
    ";

    const TABELA_PRECO_LOJA = "
        SELECT CDTABEPREC
          FROM LOJA
          WHERE (CDFILIAL = :CDFILIAL)
            AND (CDLOJA   = :CDLOJA)
    ";

    const PARAVEND = "
        SELECT CDTABEPREC, IDPRECDIFCOM
          FROM PARAVEND
          WHERE (CDFILIAL = :CDFILIAL)
    ";

    const TABELA_VENDA = "
        SELECT CONVERT(VARCHAR,DTINIVGPREC,120) AS DTINIVGPREC
          FROM TABEVEND
         WHERE CDFILIAL   = :CDFILIAL
           AND CDTABEPREC = :CDTABEPREC
           AND (CONVERT(VARCHAR, GETDATE(), 103) BETWEEN DTINIVGPREC AND DTFINVGPREC)
    ";

    const ITEM_PRECO = "
        SELECT ISNULL(I.VRPRECITEM, 0) AS PRECO,
               ISNULL(I.VRPRECITEMCL, 0) AS PRECOCLIE,
               ISNULL(I.VRPRESUGITEM, 0) AS PRECOSUGER,
               I.IDPRECVARIA, P.NMPRODUTO, I.HRINIVENPROD, I.HRFIMVENPROD
          FROM ITEMPRECO I JOIN PRODUTO P
                             ON I.CDPRODUTO = P.CDPRODUTO
         WHERE I.CDFILIAL = :CDFILIAL
           AND I.CDTABEPREC = :CDTABEPREC
           AND I.DTINIVGPREC = :DTINIVGPREC
           AND I.CDPRODUTO = :CDPRODUTO
    ";

    const TIPO_CONSUMIDOR = "
        SELECT C.CDTIPOCONS, T.IDPERALTDESCFID, C.CDCCUSCLIE
          FROM CONSUMIDOR C
          JOIN TIPOCONS T ON T.CDTIPOCONS = C.CDTIPOCONS
         WHERE CDCLIENTE = :CDCLIENTE
           AND CDCONSUMIDOR = :CDCONSUMIDOR
    ";

    const ITEM_PRECO_DIA = "
        SELECT VRPRECODIA, IDPERVALORPR, IDDESCACREPR, IDVISUACUPOM, CDTIPOCONSPD
          FROM ITEMPRECODIA
          WHERE CDFILIAL     = :CDFILIAL
            AND CDTABEPREC   = :CDTABEPREC
            AND DTINIVGPREC  = :DTINIVGPREC
            AND CDPRODUTO    = :CDPRODUTO
            AND CDPRPAITABPR = :CDPRPAITABPR
            AND NRDIASEMANPR = :NRDIASEMANPR
            AND CDTIPOCONSPD = :CDTIPOCONSPD
            AND :HORA BETWEEN HRINIPRECDIA AND HRFINPRECDIA
            AND (CONVERT(VARCHAR, GETDATE(), 103) BETWEEN DTINIVALPREC AND DTFINVALPREC)
    ";

    const GET_SMARTPROMO_PRICE = "
      SELECT CASE WHEN IT.VRPRECITEM IS NULL THEN 0 ELSE IT.VRPRECITEM END AS PRECO,
             CASE WHEN IT.VRPRECITEMCL IS NULL THEN 0 ELSE IT.VRPRECITEMCL END AS PRECO,
             DPP.IDPERVALORDES AS TIPO,
             DPP.VRDESPRODPROMOC AS VRDESC,
             0 AS ACRE
        FROM PRODGRUPROMOC PGP
        JOIN GRUPROMOCPROD GPP
          ON (PGP.CDGRUPROMOC = GPP.CDGRUPROMOC)
        JOIN PRODUTO P
          ON (PGP.CDPRODUTO = P.CDPRODUTO)
        JOIN ITEMPRECO IT
          ON (PGP.CDPRODUTO = IT.CDPRODUTO) AND
             (IT.CDPRODUTO = P.CDPRODUTO)
        LEFT JOIN DESGRUPROMOCPR DPP
          ON (PGP.CDGRUPROMOC = DPP.CDGRUPROMOC) AND
             (PGP.CDPRODUTO = DPP.CDPRODUTO) AND
             (GPP.CDPRODPROMOCAO = DPP.CDPRODPROMOCAO)
       WHERE (GPP.CDPRODPROMOCAO = :CDPRODPROMOCAO) AND
             (PGP.CDGRUPROMOC = :CDGRUPROMOC) AND
             (IT.CDPRODUTO = :CDPRODUTO) AND
             (IT.CDFILIAL = :CDFILIAL) AND
             (IT.CDTABEPREC = :CDTABEPREC) AND
             (IT.DTINIVGPREC = :DTINIVGPREC)
        ORDER BY PGP.NRORDPROMOPR
    ";

    const BUSCA_DADOS_SAT = "
        SELECT DSSATHOST, CDCODATIVASAT AS CDATIVASAT, CDSAT
          FROM CAIXA
         WHERE CDFILIAL = :CDFILIAL
           AND CDCAIXA = :CDCAIXA
           AND NRORG = :NRORG
    ";

    const ATUALIZA_VENDA_SAT = "
        UPDATE VENDA
           SET IDSTATUSNFCE = :IDSTATUSNFCE,
               DSQRCODENFCE = :DSQRCODENFCE,
               NRACESSONFCE = :NRACESSONFCE,
               NRNOTAFISCALCE = :NRNOTAFISCALCE,
               NRLANCTONFCE = :NRLANCTONFCE,
               DSARQXMLNFCE = CAST(CAST(:DSARQXMLNFCE AS VARBINARY(MAX)) AS IMAGE),
               CDSERIESAT = :CDSERIESAT,
               IDTPAMBNFCE = :IDTPAMBNFCE,
               DTEMISSAONFCE = :DTEMISSAONFCE,
               CDOPERULTATU = :CDOPERULTATU,
               NRORGULTATU = :NRORG
         WHERE CDFILIAL = :CDFILIAL
           AND CDCAIXA = :CDCAIXA
           AND NRSEQVENDA = :NRSEQVENDA
           AND NRORG = :NRORG
    ";

    const CALCULA_TOTAL_COMANDA = "
        SELECT NRLUGARMESA, NRVENDAREST, NRCOMANDA, SUM(ROUND((VRPRECCOMVEN + 0) * QTPRODCOMVEN, 2, 1) + VRACRCOMVEN - VRDESCCOMVEN) AS TOTAL
          FROM ITCOMANDAVEN
        WHERE CDFILIAL = :CDFILIAL
          AND NRVENDAREST = :NRVENDAREST
          AND NRCOMANDA = :NRCOMANDA
          AND IDSTPRCOMVEN <> 6
        GROUP BY NRLUGARMESA, NRVENDAREST, NRCOMANDA
    ";

    const BUSCA_DADOS_TAXA_SERVICO = "
        SELECT L.IDCOMISVENDA, L.VRCOMISVENDA, L.IDTRATTAXASERV, L.CDPRODTAXASERV,
               P.NMPRODUTO, L.IDTRATTAXAENTR, L.CDPRODTAXAENTR
          FROM LOJA L LEFT JOIN PRODUTO P
                             ON P.CDPRODUTO = L.CDPRODTAXASERV
        WHERE L.CDFILIAL = :CDFILIAL
          AND L.CDLOJA = :CDLOJA
    ";

    const BUSCA_ITENS_PEDIDOS = "
        SELECT I.*, P.NMPRODUTO, C.VRCOMISVENDE, P.IDCOBTXSERV,
               ROUND((I.VRPRECCOMVEN + I.VRPRECCLCOMVEN) * I.QTPRODCOMVEN, 2, 1) + I.VRACRCOMVEN - I.VRDESCCOMVEN AS PRECO
          FROM ITCOMANDAVEN I, PRODUTO P, COMANDAVEN C
        WHERE I.CDFILIAL = :CDFILIAL
          AND I.NRVENDAREST = :NRVENDAREST
          AND I.NRCOMANDA = :NRCOMANDA
          AND I.CDPRODUTO = P.CDPRODUTO
          AND C.CDFILIAL = :CDFILIAL
          AND C.NRVENDAREST = :NRVENDAREST
          AND C.NRCOMANDA = :NRCOMANDA
    ";

    const BUSCA_ITENS_PEDIDOS_OBS = "
        SELECT *
          FROM OBSITCOMANDAVEN
        WHERE CDFILIAL = :CDFILIAL
          AND NRVENDAREST = :NRVENDAREST
          AND NRCOMANDA = :NRCOMANDA
    ";

    const BUSCA_VENDA = "
        SELECT CDFILIAL, CDLOJA, CDCAIXA, DTENTRVENDA, CDCLIENTE, CDCONSUMIDOR,
               DTVENDA, IDIMPVENDA, DTABERTUR, CDFILIALTUR,
               CDCAIXATUR, CDCLIENTE, IDSITUVENDA, NRSEQVENDA,
               NRORG, CDEMPRESA, VRDESCVENDA, CDOPERADOR,
               NRINSJURIEST, IDTPEMISVEND
          FROM VENDA
         WHERE CDFILIAL = :CDFILIAL
           AND CDCAIXA = :CDCAIXA
           AND NRORG = :NRORG
           AND NRSEQVENDA = :NRSEQVENDA
    ";

    const CANCELA_VENDA = "
        UPDATE VENDA
           SET IDIMPVENDA = :IDIMPVENDA,
               IDSITUVENDA = :IDSITUVENDA,
               IDSTATUSNFCE = :IDSTATUSNFCE,
               CDOPERULTATU = :CDOPERULTATU,
               NRORGULTATU = :NRORG,
               CDSUPERVISOR = :CDSUPERVISOR
         WHERE NRSEQVENDA = :NRSEQVENDA
           AND CDFILIAL = :CDFILIAL
           AND CDCAIXA = :CDCAIXA
           AND NRORG = :NRORG
    ";

    const CANCELA_MOVCAIXA = "
        UPDATE MOVCAIXA
           SET IDTIPOMOVIVE = :IDTIPOMOVIVE,
               CDOPERULTATU = :CDOPERULTATU,
               NRORGULTATU = :NRORG
         WHERE CDFILIAL = :CDFILIAL
           AND CDCAIXA = :CDCAIXA
           AND NRORG = :NRORG
           AND NRSEQVENDA = :NRSEQVENDA
    ";

    const BUSCA_MOVICLIE = "
        SELECT CDCLIENTE, NRSEQMOVCLI, NRSEQVENDA, NRSEQUMOVI,
               CDCONSUMIDOR, VRMOVCLI, VRSALDOCONS, DTMOVCLI,
               IDTIPMOCVLI
          FROM MOVICLIE
         WHERE CDFILIAL = :CDFILIAL
           AND CDCAIXA = :CDCAIXA
           AND NRORG = :NRORG
           AND NRSEQVENDA = :NRSEQVENDA
    ";

    const CANCELA_MOVICLIE = "
        UPDATE MOVICLIE
           SET IDTIPMOCVLI = :IDTIPMOCVLI,
               VRSALDOCONS = :VRSALDOCONS,
               CDOPERULTATU = :CDOPERULTATU,
               NRORGULTATU = :NRORG
         WHERE CDFILIAL = :CDFILIAL
           AND CDCAIXA = :CDCAIXA
           AND NRORG = :NRORG
           AND NRSEQVENDA = :NRSEQVENDA
    ";

    const GET_DADOS_CAIXA = "
        SELECT IDTPEMISSAOFOS, IDTPEQUSAT, IDHABCAIXAVENDA
          FROM CAIXA
         WHERE CDFILIAL = :CDFILIAL
           AND CDCAIXA = :CDCAIXA
           AND NRORG = :NRORG
    ";

    const GET_IDCUMUPISCOFIL = "
        SELECT IDCUMUPISCOFIL
          FROM FILIAL
        WHERE CDFILIAL = :CDFILIAL
            AND NRORG = :NRORG
    ";

    const GET_IMPOSTOS_PRODUTO = "
		SELECT VRALIQIBPT, VRALIQIBPTES, IDINCIDEPISCOF, VRPEALIMPFIS
		  FROM ALIQIMPFIS
		 WHERE CDFILIAL = :CDFILIAL
		   AND CDPRODUTO = :CDPRODUTO
		   AND NRORG = :NRORG
    ";

    const GET_ENDEFILI_FULL = "
        SELECT *
           FROM ENDEFILI
          WHERE CDFILIAL     = :CDFILIAL
            AND IDTPENDEFILI = 'P'
            AND NRORG        = :NRORG
    ";

    const INSERT_VENDA = "
        INSERT INTO VENDA
         (CDFILIAL, CDLOJA, CDCAIXA, DTENTRVENDA,
          DTVENDA, DTABERVENDA, DTFECHAVENDA, IDIMPVENDA,
          DTABERTUR, CDFILIALTUR, CDCAIXATUR, CDCLIENTE,
          IDSITUVENDA, NRSEQVENDA, NRORG, CDEMPRESA,
          SGESTADO, VRDESCVENDA, CDOPERADOR, NRINSJURIEST,
          CDCONSUMIDOR, CDCCUSCLIE, NMCONSVEND, NRINSCRCONS,
          IDTPEMISVEND, IDORIGEMVENDA, CDPREFINTGCOLVEN, DTHRREALCONCONS,
          IDTIPOVENDA, IDVENDEBCONS, NRPESMESAVENDA, CDSENHAPED,
          CDVENDEDOR, NRSEQVENDAMSDE, NRVENDAREST,
          NRCOMANDAVND, NRMESA, VRTXSEVENDA, DSCOMANDAVND,
          VRTOTVENDA, DTHRMESAFECHVEN, DSOBSDESC, CDGRPOCORDESC,
          DSOBSFINVEN, DSBAIRRO, IDRETBALLOJA, DSAREAATENDVEN,
          NRCEPCONSVENDA, CDBAIRRO, CDMUNICIPIO,
          DSENDECONSVENDA, VRREPIQUEVENDA)
        VALUES
          (:CDFILIAL, :CDLOJA, :CDCAIXA, :DTENTRVENDA,
          :DTVENDA, :DTABERVENDA, :DTFECHAVENDA, :IDIMPVENDA,
          :DTABERTUR, :CDFILIALTUR, :CDCAIXATUR, :CDCLIENTE,
          :IDSITUVENDA, :NRSEQVENDA, :NRORG, :CDEMPRESA,
          :SGESTADO, :VRDESCVENDA, :CDOPERADOR, :NRINSJURIEST,
          :CDCONSUMIDOR, :CDCCUSCLIE, :NMCONSVEND, :NRINSCRCONS,
          :IDTPEMISVEND, :IDORIGEMVENDA, :CDPREFINTGCOLVEN, :DTHRREALCONCONS,
          :IDTIPOVENDA, :IDVENDEBCONS, :NRPESMESAVENDA, :CDSENHAPED,
          :CDVENDEDOR, :NRSEQVENDA, :NRVENDAREST,
          :NRCOMANDAVND, :NRMESA, :VRTXSEVENDA, :DSCOMANDAVND,
          :VRTOTVENDA, :DTHRMESAFECHVEN, :DSOBSDESC, :CDGRPOCORDESC,
          :DSOBSFINVEN, :DSBAIRRO, :IDRETBALLOJA, :DSAREAATENDVEN,
          :NRCEPCONSVENDA, :CDBAIRRO, :CDMUNICIPIO,
          :DSENDECONSVENDA, :VRREPIQUEVENDA)
    ";

    const GET_CONSUMIDOR_LIMITE_CREDITO = "
        SELECT IDSALDNEGFAM, CDFAMILISALD, CDFILIAL
          FROM FAMSALDOFILI
         WHERE CDFILIAL = :CDFILIAL
    ";

    const GET_DADOSPROD = "
        SELECT P.CDARVPROD, P.NMPRODUTO, P.CDCLASFISC, P.SGUNIDADE,
               ISNULL(A.CDCBENEF, P.CDCBENEF) CDCBENEF
          FROM PRODUTO P, ALIQIMPFIS A
         WHERE P.NRORG = :NRORG
           AND P.CDPRODUTO = :CDPRODUTO
           AND A.CDFILIAL = :CDFILIAL
           AND A.CDPRODUTO = P.CDPRODUTO
    ";

    const INSERT_ITEMVENDA = "
        INSERT INTO ITEMVENDA
          (CDFILIAL, CDLOJA, CDCAIXA, QTPRODVEND, VRUNITVEND,
          VRDESITVEND, CDPRODUTO, NRORG, NRSEQVENDA, NRSEQUITVEND,
          VRACRITVEND, VRUNITVENDCL, NRORDITCUPFIS,
          VRPERCVEND, VRALIQIMPR, NRATRAPRODITVE, VRRATTXSERV,
          VRRATDESCVEN, DTHRPEDIDO, CDCAIXAPEDVEN, CDPRODPROMOCAO,
          NRSEQPRODCOMIT, CDVENDEDOR, IDORIGPEDVEN, QTPRODREFIL,
          DSOBSITEMVENDA, DSOBSPEDDIGITA, CDCLASFISC, DSOBSDESCIT,
          IDORIGEMVENDA, CDGRPOCORDESCIT, CDCBENEFIT, NRPESMESAVENIT,
          CDCAMPCOMPGANHE, DTINIVGCAMPCG)
        VALUES
          (:CDFILIAL, :CDLOJA, :CDCAIXA, :QTPRODVEND, :VRUNITVEND,
          :VRDESITVEND, :CDPRODUTO, :NRORG, :NRSEQVENDA,
          :NRSEQUITVEND, :VRACRITVEND, :VRUNITVENDCL, :NRORDITCUPFIS,
          :VRPERCVEND, :VRALIQIMPR, :NRATRAPRODITVE, :VRRATTXSERV,
          :VRRATDESCVEN, :DTHRPEDIDO, :CDCAIXAPEDVEN, :CDPRODPROMOCAO,
          :NRSEQPRODCOMIT, :CDVENDEDOR, :IDORIGPEDVEN, :QTPRODREFIL,
          :DSOBSITEMVENDA, :DSOBSPEDDIGITA, :CDCLASFISC, :DSOBSDESCIT,
          :IDORIGEMVENDA, :CDGRPOCORDESCIT, :CDCBENEFIT, :NRPESMESAVENIT,
          :CDCAMPCOMPGANHE, :DTINIVGCAMPCG)
    ";

    const GET_BOMBA_BY_BICO = "
        SELECT BICO.NRBOMBA,BICO.CODIGOBICO AS CDBICO, BOMBA.CODIGOBOMBA AS CDBOMBA
          FROM VND_BICO BICO, VND_BOMBA BOMBA
         WHERE BICO.NRSEQBICO  = :NRBICO
           AND BICO.NRBOMBA    = BOMBA.NRBOMBA
    ";

    const GET_NOVO_NRITEMVENDAUXILIAR = "
        SELECT (CASE WHEN MAX(NRITEMVENDAUXILIAR) IS NULL THEN 0 ELSE MAX(NRITEMVENDAUXILIAR) END + 1) AS  NRITEMVENDAUXILIAR
          FROM VND_ITEMVENDAAUXILIAR
    ";

    const GET_TIPOCOMBUSTIVEL_BY_PRODUTO = "
        SELECT PP.NRTIPOCOMBUSTIVEL, TC.CODCOMBUSTIVEL
          FROM VND_PARAMETROSPRODUTO PP
            LEFT JOIN VND_TIPOCOMBUSTIVEL TC
              ON PP.NRTIPOCOMBUSTIVEL = TC.NRTIPOCOMBUSTIVEL
         WHERE PP.CDPRODUTO = :CDPRODUTO
           AND PP.NRORG     = :NRORG
    ";

    const INSERT_ITEMVENDAAUXILIAR = "
        INSERT INTO VND_ITEMVENDAAUXILIAR
            (CDFILIAL,CDCAIXA,NRITEMVENDAUXILIAR,CDFILIALALMOXARIFADO,
            CDALMOXARIFADO,DATACONCLUSAOABASTECIMENTO,NRTIPOCOMBUSTIVEL,NRBOMBA,
            NRBICO,NRORG,NRSEQVENDA,NRSEQUITVEND,
            VRENCERRANTEINICIAL,VRENCERRANTEFINAL)
         VALUES (:CDFILIAL,:CDCAIXA,:NRITEMVENDAUXILIAR,:CDFILIALALMOXARIFADO,
         	:CDALMOXARIFADO,:DATACONCLUSAOABASTECIMENTO,:NRTIPOCOMBUSTIVEL,:NRBOMBA,
         	:NRBICO,:NRORG,:NRSEQVENDA,:NRSEQUITVEND,
         	:VRENCERRANTEINICIAL,:VRENCERRANTEFINAL)
    ";

    const INSERT_ITEMVENDAAUXILIAR_FEATUREGRUPO = "
        INSERT INTO VND_ITEMVENDAAUXILIAR
          (NRITEMVENDAUXILIAR, CDFILIAL, CDCAIXA, NRORG,
          NRSEQVENDA, NRSEQUITVEND, NRFEATUREGRUPO, NRORGINCLUSAO,
          CDOPERINCLUSAO, NRORGULTATU, CDOPERULTATU)
        VALUES
          (:NRITEMVENDAUXILIAR, :CDFILIAL, :CDCAIXA, :NRORG,
          :NRSEQVENDA, :NRSEQUITVEND, :NRFEATUREGRUPO, :NRORGINCLUSAO,
          :CDOPERINCLUSAO, :NRORGULTATU, :CDOPERULTATU)
    ";

    const UPDATE_MOVBENEFICIOCTR = "
        UPDATE MOVBENEFICIOCTR
		   SET QTDEBENEFICIO  = :QTDEBENEFICIO
        WHERE CDCLIENTE      = :CDCLIENTE
          AND CDCONSUMIDOR   = :CDCONSUMIDOR
          AND CDCAMPANHA     = :CDCAMPANHA
          AND NRSEQPRODCAMP  = :NRSEQPRODCAMP
          AND NRSEQPRODCAMP  = :NRSEQPRODCAMP
          AND NRSEQBENEFICIO = :NRSEQBENEFICIO
    ";

    const INSERT_MOVBENEFICIOCONS = "
        INSERT INTO MOVBENEFICIOCONS
          (CDCAMPANHA, NRSEQBENECONS, NRSEQPRODCAMP, NRSEQBENEFICIO, CDCLIENTE,
          CDCONSUMIDOR, DTUTBENECONS, IDIMPTMOV, CDFILIAL, CDCAIXA, NRSEQVENDA, NRSEQUITVEND)
        VALUES
          (:CDCAMPANHA, :NRSEQBENECONS, :NRSEQPRODCAMP, :NRSEQBENEFICIO, :CDCLIENTE,
          :CDCONSUMIDOR, :DTUTBENECONS, :IDIMPTMOV, :CDFILIAL, :CDCAIXA, :NRSEQVENDA, :NRSEQUITVEND)
    ";

    const INSERT_ITHRCOMANDA = "
        INSERT INTO ITHRCOMANDA
          (CDFILIAL, NRSEQUITEM, CDPRODUTO, DTHRPEDIDO, QTPRODCOMVEN,
          CDCAIXA, NRVENDAREST, NRSEQVENDA, IDIMPITHRCOMANDA)
        VALUES
          (:CDFILIAL, :NRSEQUITEM, :CDPRODUTO, :DTHRPEDIDO, :QTPRODCOMVEN,
          :CDCAIXA, :NRVENDAREST, :NRSEQVENDA, :IDIMPITHRCOMANDA)
    ";

    const BUSCA_OCORRENCIA = "
        SELECT IDCONTROLAOBS, CDPRODUTO
          FROM OCORRENCIA
        WHERE CDGRPOCOR = :CDGRPOCOR
          AND CDOCORR = :CDOCORR
    ";

    const INSERT_OBSITEMVENDA = "
        INSERT INTO OBSITEMVENDA
          (CDFILIAL, CDCAIXA, NRSEQVENDA, NRSEQUITVEND,
          CDGRPOCOR, CDOCORR)
        VALUES
          (:CDFILIAL, :CDCAIXA, :NRSEQVENDA, :NRSEQUITVEND,
          :CDGRPOCOR, :CDOCORR)
    ";

    const INSERT_OBSITEMVENDAEST = "
        INSERT INTO OBSITEMVENDAEST
          (CDFILIAL, CDCAIXA, NRSEQVENDA, NRSEQUITVEND,
          CDPRODUTO, CDGRPOCOR, CDOCORR)
        VALUES
          (:CDFILIAL, :CDCAIXA, :NRSEQVENDA, :NRSEQUITVEND,
          :CDPRODUTO, :CDGRPOCOR, :CDOCORR)
    ";

    const INSERE_ITEMVENDAEST = "
        INSERT INTO ITEMVENDAEST
          (CDFILIAL, CDCAIXA, NRSEQVENDA, NRSEQUITVEND,
          CDPRODUTO, QTITVENDAEST, VRUNVENDAEST, VRDESITVENDAEST,
          NRATRAPRODITES, DSOBSPEDDIGITE)
        VALUES
          (:CDFILIAL, :CDCAIXA, :NRSEQVENDA, :NRSEQUITVEND,
          :CDPRODUTO, :QTITVENDAEST, :VRUNVENDAEST, :VRDESITVENDAEST,
          :NRATRAPRODITES, :DSOBSPEDDIGITE)
    ";

    const GET_ALIQIMPFIS_IMPOSTO = "
        SELECT
              AL.VRPEALIMPFIS, AL.CDIMPOSTO, IP.IDTPIMPOSFIS, IP.CDINTIMPOSTO, IP.IDTRATIMPO,
              AL.CDCSTICMS, AL.CDCSTPISCOF, AL.CDCFOPPFIS, AL.VRALIQPIS, AL.VRALIQCOFINS,
              AL.IDMODALBASECALC, AL.VRPERCREDUCAO, AL.VRALIQIBPT, AL.IDINCIDEPISCOF
            FROM
              ALIQIMPFIS AL, IMPOSTO IP
            WHERE (AL.CDFILIAL  = :CDFILIAL)
              AND (AL.CDPRODUTO = :CDPRODUTO)
              AND (AL.CDIMPOSTO = IP.CDIMPOSTO)
    ";

    const INSERT_ITVENDAIMPOS = "
        INSERT INTO ITVENDAIMPOS
          (CDFILIAL, CDCAIXA, NRSEQVENDA, NRSEQUITVEND,
          NRSEQITIMPOS, CDIMPOSTO, VRPEALPRODIT, VRIMPOPRODIT,
          CDIMPOSTOEX, CDCSTPRODI, CDCSTPRODPC, CDCFOPPROD,
          VRPERCOFINS, VRPERPIS, IDMODALBASECALC, VRBASECALCREDUZ,
          VRIMPOPRODREDUZ, VRBASECALCICMS, VRBCREDUZICMS, VRPRBCREDUICMS,
          VRIMPPIS, VRIMPCOFINS, VRTOTTRIBIBPT, VRBCPISCOFINS)
        VALUES (:CDFILIAL, :CDCAIXA, :NRSEQVENDA, :NRSEQUITVEND,
          :NRSEQITIMPOS, :CDIMPOSTO, :VRPEALPRODIT, :VRIMPOPRODIT,
          :CDIMPOSTOEX, :CDCSTPRODI, :CDCSTPRODPC, :CDCFOPPROD,
          :VRPERCOFINS, :VRPERPIS, :IDMODALBASECALC, :VRBASECALCREDUZ,
          :VRIMPOPRODREDUZ, :VRBASECALCICMS, :VRBCREDUZICMS, :VRPRBCREDUICMS,
          :VRIMPPIS, :VRIMPCOFINS, :VRTOTTRIBIBPT, :VRBCPISCOFINS)
    ";

    const INSERT_ITEMVENDA_CANCELADO = "
      INSERT INTO ITVENDACAN
              (CDFILIAL,CDCAIXA,QTPRODVENDC,VRUNITVENDC,VRDESITVENDC,CDPRODUTO,NRORG,NRSEQVENDA,NRSEQITVENDC,VRACRITVENDC,NRORGINCLUSAO,VRUNITVENCLC,NRORDITCUPFISCA, CDGRPOCORCANITE, DSOBSCANITE, CDSUPERVISOR, IDPRODPRODUZC, DTHRPRODCAN)
       VALUES (:CDFILIAL,:CDCAIXA,:QTPRODVENDC,:VRUNITVENDC,:VRDESITVENDC,:CDPRODUTO,:NRORG,:NRSEQVENDA,:NRSEQITVENDC,:VRACRITVENDC,:NRORGINCLUSAO,:VRUNITVENCLC, :NRORDITCUPFISCA, :CDGRPOCORCANITE, :DSOBSCANITE, :CDSUPERVISOR, :IDPRODPRODUZC, :DTHRPRODCAN)
    ";

    const INSERT_MOVCAIXA_SALE = "
        INSERT INTO MOVCAIXA
          (CDFILIAL, CDCAIXA, DTABERCAIX, NRSEQUMOVI,
          DTHRINCMOV, IDTIPOMOVIVE, VRMOVIVEND, QTPARCRECEB,
          VRMOVIVEOUT, VRCOTINDOUT, NRSEQVENDA, NRORG,
          NRORGINCLUSAO, CDOPERINCLUSAO, CDCLIENTE, CDTIPORECE,
          DTMOVIMCAIXA, CDNSUHOSTTEF, NRSEQUMOVIMSDE, NRCONTROLTEF, NRCARTBANCO)
        VALUES
          (:CDFILIAL, :CDCAIXA, :DTABERCAIX, :NRSEQUMOVI,
          :DTHRINCMOV, :IDTIPOMOVIVE, :VRMOVIVEND, :QTPARCRECEB,
          :VRMOVIVEOUT, :VRCOTINDOUT, :NRSEQVENDA, :NRORG,
          :NRORGINCLUSAO, :CDOPERINCLUSAO, :CDCLIENTE, :CDTIPORECE,
          :DTMOVIMCAIXA, :CDNSUHOSTTEF, :NRSEQUMOVI, :NRCONTROLTEF, :NRCARTBANCO)
    ";

    const INSERT_MOVCLIE = "
        INSERT INTO MOVICLIE
          (CDFILIAL, CDCAIXA, DTABERCAIX, NRSEQMOVCLI,
          CDCLIENTE, CDCONSUMIDOR, VRMOVCLI, DTMOVCLI,
          DTMOVIMCLIE, IDTIPMOCVLI, NRSEQVENDA,NRSEQUMOVI,
          CDTIPORECE, VRSALDOCONS, CDOPERADOR, CDOPERECSALD, NRORG,
          DTINCLUSAO, NRORGINCLUSAO, CDOPERINCLUSAO, NRORGULTATU,
          CDOPERULTATU, VRMOVPESCONS,VRSALDPECONS,VRPESUBSIDIO,
          VRTETOSUBSID)
        VALUES
          (:CDFILIAL, :CDCAIXA, :DTABERCAIX, :NRSEQMOVCLI,
          :CDCLIENTE, :CDCONSUMIDOR, :VRMOVCLI, :DTMOVCLI,
          :DTMOVIMCLIE, :IDTIPMOCVLI, :NRSEQVENDA, :NRSEQUMOVI,
          :CDTIPORECE, :VRSALDOCONS, :CDOPERADOR, :CDOPERADOR, :NRORG,
          :DTINCLUSAO, :NRORGINCLUSAO, :CDOPERINCLUSAO, :NRORGULTATU,
          :CDOPERULTATU,:VRMOVPESCONS,:VRSALDPECONS,:VRPESUBSIDIO,
          :VRTETOSUBSID)
    ";

    const GET_PRODESTFEATUREGRU = "
        SELECT CDPRODUTOESTOQUE
          FROM VND_PRODFEATUREGRUPOESTOQUE
         WHERE NRFEATUREGRUPO = :NRFEATUREGRUPO
    ";

    const GET_NRFEATUREGRUPO_BY_DIMENSOES = "
        SELECT PDVFG1.NRFEATUREGRUPO
          FROM ( SELECT NRFEATUREGRUPO, COUNT(NRFEATUREGRUPO) QTDIMENSOES
                   FROM VND_PRODUTODIMVALORFEATUREGRU
                  WHERE NRPRODUTODIMENSAOVALOR IN (?)
               GROUP BY NRFEATUREGRUPO
               ) PDVFG1,
               ( SELECT NRFEATUREGRUPO, COUNT(NRFEATUREGRUPO) QTDIMENSOES
                   FROM VND_PRODUTODIMVALORFEATUREGRU
               GROUP BY NRFEATUREGRUPO
               ) PDVFG2
         WHERE PDVFG1.QTDIMENSOES    = ?
           AND PDVFG2.NRFEATUREGRUPO = PDVFG1.NRFEATUREGRUPO
           AND PDVFG2.QTDIMENSOES    = PDVFG1.QTDIMENSOES
    ";

    const GET_SALDOCONSUMIDOR = "
   		SELECT CDCLIENTE, CDCONSUMIDOR, VRSALDOCONS
   		  FROM MOVICLIE
   		 WHERE NRSEQMOVCLI IN (SELECT MAX(NRSEQMOVCLI)
   		                         FROM MOVICLIE
   		                        WHERE (CHARINDEX(MOVICLIE.CDCLIENTE, :CDCLIENTE) > 0)
   		                       GROUP BY CDCLIENTE)
   		   AND (CHARINDEX(MOVICLIE.CDCLIENTE, :CDCLIENTE) > 0)
   		GROUP BY CDCLIENTE, CDCONSUMIDOR, VRSALDOCONS
    ";

    const BUSCA_PARAVEND = "
        SELECT CDCLIENTE, CDCCUSCLIE, IDAMBTRABNFCE, IDESTTEMPOREAL
          FROM PARAVEND
         WHERE NRORG    = :NRORG
           AND CDFILIAL = :CDFILIAL
    ";

    const UPDATE_NRSEQVENDA_VND_INTEGRACAOMOV = "
         UPDATE VND_INTEGRACAOMOV
            SET NRSEQVENDA = :NRSEQVENDA
         WHERE NRSEQMOV = :NRSEQMOV AND CDFILIAL = :CDFILIAL AND CDCAIXA = :CDCAIXA AND NRORG = :NRORG
     ";

     const GET_CLIENTEPADRAO = "
        SELECT CDCLIENTE, CDCCUSCLIE
          FROM PARAVEND
         WHERE NRORG    = :NRORG
           AND CDFILIAL = :CDFILIAL
    ";

    const GET_SALE_PARAMETERS = "
        SELECT NRACESSONFCE, IDTPAMBNFCE, NRPROTOCOLONFCE, NRINSJURIEST, NRNOTAFISCALCE, NRINSCRCONS, IDSTATUSNFCE
          FROM VENDA
         WHERE CDFILIAL = :CDFILIAL
           AND CDCAIXA = :CDCAIXA
           AND NRSEQVENDA = :NRSEQVENDA
           AND NRORG = :NRORG
    ";

    const CREATE_BALANCE = "
        INSERT INTO SALDOCONS
            (CDCLIENTE, CDCONSUMIDOR, CDFAMILISALD, VRSALDCONFAM)
        VALUES
            (:CDCLIENTE, :CDCONSUMIDOR, :CDFAMILISALD, :VRSALDCONFAM)
    ";

    const UPDATE_BALANCE = "
      UPDATE SALDOCONS
         SET VRSALDCONFAM = :VRSALDCONFAM
      WHERE CDCLIENTE = :CDCLIENTE
        AND CDCONSUMIDOR = :CDCONSUMIDOR
        AND CDFAMILISALD = :CDFAMILISALD
    ";

    const INSERT_EXTRATOCONS = "
        INSERT INTO EXTRATOCONS(
            CDCLIENTE, CDCONSUMIDOR, CDFAMILISALD, DTMOVEXTCONS,
            NRSEQMOVEXT, CDTIPORECE, CDFILIAL, CDCAIXA,
            DSOPEEXTCONS, VRMOVEXTCONS, VRSALDCONEXT, IDTPMOVEXT,
            DTULTATUEXT, NRDEPOSICONS, DTABERCAIX, NRSEQMOVCAIXA,
            NRSEQVENDA
        )
        VALUES(
            :CDCLIENTE, :CDCONSUMIDOR, :CDFAMILISALD, :DTMOVEXTCONS,
            :NRSEQMOVEXT, :CDTIPORECE, :CDFILIAL, :CDCAIXA,
            :DSOPEEXTCONS, :VRMOVEXTCONS, :VRSALDCONEXT, :IDTPMOVEXT,
            NULL, :NRDEPOSICONS, :DTABERCAIX, :NRSEQMOVCAIXA,
            :NRSEQVENDA
        )
    ";

    const GET_CDTIPORECE_BY_BANCART = "
        SELECT TP.CDTIPORECE
          FROM TIPORECE TP, ITMENUCONFTE IT
        WHERE IT.CDIDENTBUTON = TP.CDTIPORECE
          AND IT.NRORG = TP.NRORG
          AND IT.IDTPBUTTON = '5'
          AND IT.CDFILIAL = :CDFILIAL
          AND IT.NRCONFTELA = :NRCONFTELA
          AND IT.DTINIVIGENCIA = :DTINIVIGENCIA
          AND TP.NRORG = :NRORG
          AND TP.IDTIPORECE = :IDTIPORECE
          AND TP.CDBANCARTCR = :CDBANCARTCR
    ";

    const CAIXA = "
        SELECT IDTPEMISSAOFOS
          FROM CAIXA
         WHERE CDFILIAL = :CDFILIAL
           AND CDCAIXA = :CDCAIXA
    ";

    const PRODUTO = "
        SELECT IDTIPOCOMPPROD, IDIMPPRODUTO, NMPRODUTO
          FROM PRODUTO
         WHERE CDPRODUTO = :CDPRODUTO
    ";

    const TIPORECE = "
        SELECT IDTIPORECE
          FROM TIPORECE
         WHERE CDTIPORECE = :CDTIPORECE
    ";

    const DADOS_CAIXA = "
          SELECT IDTPEMISSAOFOS
            FROM CAIXA
          WHERE CDFILIAL = :CDFILIAL
            AND CDCAIXA = :CDCAIXA
    ";

    const GET_HIERARQUIA_BYCODPARAM = "
        SELECT PH.NRPARAMETRO, PH.NRRELHIERARQUIAPARAMETRO FROM VND_PARAMETRO PA
        JOIN VND_RELHIERARQUIAPARAMETRO PH
          ON PA.NRPARAMETRO = PH.NRPARAMETRO
        WHERE PA.CODPARAMETRO = :CODPARAMETRO
    ";

    const GET_PARAMETRO_BYCODPARAMETRO = "
        SELECT NRPARAMETRO, NRPARENT FROM VND_PARAMETRO WHERE CODPARAMETRO = :CODPARAMETRO
    ";

    const GET_HIERARQUIA_BYNRPARAMETRO = "
        SELECT PH.NRPARAMETRO, PH.DESCHIERARQUIA, PH.NRPRIORIDADEHIERARQUIA, PH.HIERARQUIAVALUE FROM VND_RELHIERARQUIAPARAMETRO PH
        LEFT JOIN VND_PARAMETRO PA
          ON PH.NRPARAMETRO = PA.NRPARAMETRO
        WHERE PH.NRPARAMETRO = :NRPARAMETRO ORDER BY PH.NRPRIORIDADEHIERARQUIA ASC
    ";

    const GET_PARAMETRO_BYRELACIONAMENTOGERAL = "
        SELECT PA.NRPARAMETRO, PA.CODPARAMETRO, PA.DESCPARAMETRO, PV.NRPARAMETROVALOR, PV.VALORPARAMETRO, PR.DESCRELACIONAMENTO, PR.VALOR
          FROM VND_PARAMETRO PA
        LEFT JOIN VND_PARAMETROVALOR PV ON PV.NRPARAMETRO = PA.NRPARAMETRO
        LEFT JOIN VND_RELPARAMETROVALOR PR ON PR.NRPARAMETROVALOR = PV.NRPARAMETROVALOR AND PR.NRORG = PV.NRORG
        WHERE PA.CODPARAMETRO = :CODPARAMETRO AND PV.NRORG = :NRORG AND PR.DESCRELACIONAMENTO = 'GERAL'
    ";

    const GET_PARAMETRO_BYDESCRELAC_VALOR = "
        SELECT PA.NRPARAMETRO, PA.CODPARAMETRO, PA.DESCPARAMETRO, PV.NRPARAMETROVALOR, PV.VALORPARAMETRO, PR.DESCRELACIONAMENTO, PR.VALOR
          FROM VND_PARAMETRO PA
        LEFT JOIN VND_PARAMETROVALOR PV ON PV.NRPARAMETRO = PA.NRPARAMETRO
        LEFT JOIN VND_RELPARAMETROVALOR PR ON PR.NRPARAMETROVALOR = PV.NRPARAMETROVALOR AND PR.NRORG = PV.NRORG
        WHERE PA.CODPARAMETRO = :CODPARAMETRO AND PV.NRORG = :NRORG AND PR.DESCRELACIONAMENTO = :DESCRELACIONAMENTO AND PR.VALOR = :VALOR
    ";

    const GET_PARAMETRO_BYNRPARAMETROVALOR = "
        SELECT PA.NRPARAMETRO, PA.CODPARAMETRO, PV.NRPARAMETROVALOR, PV.VALORPARAMETRO, PR.DESCRELACIONAMENTO, PR.VALOR
          FROM VND_PARAMETRO PA
        LEFT JOIN VND_PARAMETROVALOR PV ON PV.NRPARAMETRO = PA.NRPARAMETRO
        LEFT JOIN VND_RELPARAMETROVALOR PR ON PR.NRPARAMETROVALOR = PV.NRPARAMETROVALOR AND PR.NRORG = PV.NRORG
        WHERE PA.CODPARAMETRO = :CODPARAMETRO AND PV.NRORG = :NRORG AND PR.NRPARAMETROVALOR = :NRPARAMETROVALOR AND PR.DESCRELACIONAMENTO <> :DESCRELACIONAMENTO
    ";

    const GET_PARAMETRO_BYNRPARAMETROVALOR_BYDESC_BYVALOR = "
        SELECT PA.NRPARAMETRO, PA.CODPARAMETRO, PV.NRPARAMETROVALOR, PV.VALORPARAMETRO, PR.DESCRELACIONAMENTO, PR.VALOR
          FROM VND_PARAMETRO PA
        LEFT JOIN VND_PARAMETROVALOR PV ON PV.NRPARAMETRO = PA.NRPARAMETRO
        LEFT JOIN VND_RELPARAMETROVALOR PR ON PR.NRPARAMETROVALOR = PV.NRPARAMETROVALOR AND PR.NRORG = PV.NRORG
        WHERE PA.CODPARAMETRO = :CODPARAMETRO AND PV.NRORG = :NRORG AND PR.NRPARAMETROVALOR = :NRPARAMETROVALOR AND PR.DESCRELACIONAMENTO = :DESCRELACIONAMENTO
          AND PR.VALOR = :VALOR
    ";

    const GET_PARAMETRO_BYNRPARAMETROVALOR_BYDESC = "
        SELECT PA.NRPARAMETRO, PA.CODPARAMETRO, PV.NRPARAMETROVALOR, PV.VALORPARAMETRO, PR.DESCRELACIONAMENTO, PR.VALOR
          FROM VND_PARAMETRO PA
        LEFT JOIN VND_PARAMETROVALOR PV ON PV.NRPARAMETRO = PA.NRPARAMETRO
        LEFT JOIN VND_RELPARAMETROVALOR PR ON PR.NRPARAMETROVALOR = PV.NRPARAMETROVALOR AND PR.NRORG = PV.NRORG
        WHERE PA.CODPARAMETRO = :CODPARAMETRO AND PV.NRORG = :NRORG AND PR.NRPARAMETROVALOR = :NRPARAMETROVALOR AND PR.DESCRELACIONAMENTO = :DESCRELACIONAMENTO
    ";

    const GET_SMTP = "
        SELECT DSSMTPAUTVND,
            DSEMAILAUVND,
            NRPORTAAUTVND,
            CDSENHAAUTVNDWEB,
            DSEMAILCLUSO,
            LOWER(DSSMTPCRIPT) AS DSSMTPCRIPT,
            IDTPAUTHSMTP
        FROM PARAMGERAL
    ";

    const GET_CDTIPORECE_FUNDO = "
        SELECT T.CDTIPORECE, T.NMTIPORECE
          FROM TIPORECE T, ITMENUCONFTE I
        WHERE I.CDFILIAL = :CDFILIAL
          AND I.CDIDENTBUTON = T.CDTIPORECE
          AND T.IDTIPORECE = '4'
          AND I.NRCONFTELA = :NRCONFTELA
          AND I.DTINIVIGENCIA = :DTINIVIGENCIA
    ";

    const SQL_BUSCA_CONTADOR = "
        SELECT CDCONTADOR, NRSEQUENCIAL
          FROM NOVOCODIGO
         WHERE CDCONTADOR = :CDCONTADOR
    ";

    const EXECUTE_NOVO_CODIGO = "
        EXECUTE NOVO_CODIGO @P_CONTADOR = :CDCONTADOR, @P_SEQUENCIAL = '', @P_QTDE = :QTDE
    ";

    const SQL_GET_FILIAL_DETAILS = "
        SELECT FL.CDFILIAL, FL.NMRAZSOCFILI, FL.NRINSJURFILI, FL.CDINSCESTA, FL.CDINSCMUNI,
               EF.DSENDEFILI, EF.NMBAIRFILI, MU.NMMUNICIPIO, EF.SGESTADO
          FROM FILIAL FL
          JOIN ENDEFILI EF ON EF.CDFILIAL = FL.CDFILIAL
          JOIN MUNICIPIO MU ON MU.CDMUNICIPIO = EF.CDMUNICIPIO
         WHERE EF.IDTPENDEFILI = 'P'
           AND FL.CDFILIAL = :CDFILIAL
    ";

    const BUSCA_NRDEPOSICONS = "
        SELECT EX.CDCLIENTE, EX.CDCONSUMIDOR, EX.CDFAMILISALD, SA.NMFAMILISALD,
               FA.IDSALDNEGFAM, EX.DTMOVEXTCONS, EX.NRSEQMOVEXT, EX.CDTIPORECE,
               EX.CDFILIAL, EX.CDCAIXA, EX.NRSEQVENDA, EX.DSOPEEXTCONS,
               EX.VRMOVEXTCONS, EX.VRSALDCONEXT, EX.IDTPMOVEXT, EX.NRDEPOSICONS,
               EX.IDIMPEXTCONS, EX.DTABERCAIX, EX.DTULTATUEXT, EX.NRDEPOSICONS,
               EX.NRSEQMOVCAIXA, MO.CDNSUHOSTTEF, TR.NMTIPORECE
          FROM EXTRATOCONS EX, FAMSALDOFILI FA, FAMILIASALDO SA, MOVCAIXA MO, TIPORECE TR
         WHERE EX.CDCLIENTE    = :CDCLIENTE
           AND EX.CDCONSUMIDOR = :CDCONSUMIDOR
           AND EX.NRDEPOSICONS = :NRDEPOSICONS
           AND EX.NRSEQMOVCAIXA LIKE :NRSEQMOVCAIXA
           AND EX.IDTPMOVEXT   = 'C'
           AND EX.CDFILIAL     = FA.CDFILIAL
           AND EX.CDFAMILISALD = FA.CDFAMILISALD
           AND EX.CDFAMILISALD = SA.CDFAMILISALD
           AND MO.CDFILIAL     = EX.CDFILIAL
           AND MO.CDCAIXA      = EX.CDCAIXA
           AND MO.DTABERCAIX   = EX.DTABERCAIX
           AND MO.NRSEQUMOVI   = EX.NRSEQMOVCAIXA
           AND TR.CDTIPORECE   = MO.CDTIPORECE
    ";

    const SQL_GET_CONSUMIDOR_LIMITE_DEBITO = "
        SELECT C.CDCONSUMIDOR, C.NMCONSUMIDOR, C.VRMAXDEBCONS, C.VRLIMDEBCONS,
               C.IDVERSALDCON, C.VRAVIDEBCONS, T.VRMAXCONSDIAD, T.VRMAXCONSMESD
          FROM CONSUMIDOR C
          LEFT JOIN TIPOCONSLOJA T
                 ON T.CDTIPOCONS = C.CDTIPOCONS
                AND T.CDFILIAL = :CDFILIAL
                AND T.CDLOJA = :CDLOJA
         WHERE CDCLIENTE = :CDCLIENTE
           AND CDCONSUMIDOR = :CDCONSUMIDOR
    ";

    const SQL_GET_CONSUMIDOR_SALDO_DEBITO = "
        SELECT ISNULL(VRSALDOCONS, 0) AS VRSALDOCONS
          FROM MOVICLIE
         WHERE CDCLIENTE    = :CDCLIENTE
           AND CDCONSUMIDOR = :CDCONSUMIDOR
           AND DTMOVCLI     = (SELECT MAX(DTMOVCLI)
                                 FROM MOVICLIE
                                WHERE CDCLIENTE    = :CDCLIENTE
                                  AND CDCONSUMIDOR = :CDCONSUMIDOR)
    ";

    const SQL_DETALHES_FAMILIA = "
        SELECT IDSALDNEGFAM, CDFAMILISALD, CDFILIAL
          FROM FAMSALDOFILI
          WHERE CDFILIAL = :CDFILIAL
    ";

    const SQL_BUSCA_LIMITES = "
        SELECT IDVERSALDCON, VRMAXCREDCONS, VRLIMCREDCONS
          FROM CONSUMIDOR
          WHERE CDCLIENTE = :CDCLIENTE
            AND CDCONSUMIDOR = :CDCONSUMIDOR
    ";

    const SQL_CONSUMO_DIARIO = "
        SELECT CASE WHEN SUM(EX.VRMOVEXTCONS) IS NULL THEN 0 ELSE SUM(EX.VRMOVEXTCONS) END AS CONSUMO
          FROM EXTRATOCONS EX
          JOIN TIPORECE TR ON TR.CDTIPORECE = EX.CDTIPORECE
          WHERE EX.CDCLIENTE = :CDCLIENTE
            AND EX.CDCONSUMIDOR = :CDCONSUMIDOR
            AND EX.CDFAMILISALD = :CDFAMILISALD
            AND EX.IDTPMOVEXT = 'V'
            AND TR.IDTIPORECE = '9'
            AND EX.DTMOVEXTCONS > :DTMOVEXTCONS
    ";

    const SQL_SALDO_EXTRATOCONS = "
        SELECT VRSALDCONEXT, CDCLIENTE, CDCONSUMIDOR
          FROM EXTRATOCONS
          WHERE CDCLIENTE = :CDCLIENTE
            AND CDCONSUMIDOR = :CDCONSUMIDOR
            AND CDFAMILISALD = :CDFAMILISALD
          ORDER BY DTMOVEXTCONS DESC
    ";

    const IS_FIRST_INSERTION = "
        SELECT CDCLIENTE, CDCONSUMIDOR, MIN(DTMOVEXTCONS) AS DTMOVEXTCONS
          FROM EXTRATOCONS
         WHERE CDCLIENTE = :CDCLIENTE
           AND CDCONSUMIDOR = :CDCONSUMIDOR
         GROUP BY CDCLIENTE, CDCONSUMIDOR
    ";

    const IS_FIRST_INSERTION_FAMILY = "
        SELECT CDCLIENTE, CDCONSUMIDOR, MIN(DTMOVEXTCONS) AS DTMOVEXTCONS
          FROM EXTRATOCONS
         WHERE CDCLIENTE = :CDCLIENTE
           AND CDCONSUMIDOR = :CDCONSUMIDOR
           AND CDFAMILISALD = :CDFAMILISALD
         GROUP BY CDCLIENTE, CDCONSUMIDOR
    ";

    const SQL_SALDO_SALDOCONS = "
        SELECT VRSALDCONFAM
          FROM SALDOCONS
          WHERE CDCLIENTE = :CDCLIENTE
            AND CDCONSUMIDOR = :CDCONSUMIDOR
            AND CDFAMILISALD = :CDFAMILISALD
    ";

    const GET_NRSEQMOVEXT = "
        SELECT CASE WHEN MAX(NRSEQMOVEXT)+1 IS NULL THEN 1 ELSE MAX(NRSEQMOVEXT)+1 END AS NRSEQMOVEXT
          FROM EXTRATOCONS
          WHERE CDCLIENTE = :CDCLIENTE
            AND CDCONSUMIDOR = :CDCONSUMIDOR
    ";

    const BUSCA_NOMES = "
        SELECT CL.NMRAZSOCCLIE, CD.NMCONSUMIDOR, FS.NMFAMILISALD, CD.NRCPFRESPCON
          FROM CLIENTE CL, CONSUMIDOR CD, FAMILIASALDO FS
          WHERE CL.CDCLIENTE = :CDCLIENTE
            AND CD.CDCONSUMIDOR = :CDCONSUMIDOR
            AND FS.CDFAMILISALD = :CDFAMILISALD
    ";

    const GET_PERMITE_SALDO_NEGATIVO = "
        SELECT FIL.IDSALDNEGFAM, FAM.NMFAMILISALD
          FROM FAMSALDOFILI FIL
        JOIN FAMILIASALDO FAM ON FIL.CDFAMILISALD = FAM.CDFAMILISALD
        WHERE FIL.CDFILIAL = :CDFILIAL AND FIL.CDFAMILISALD = :CDFAMILISALD
    ";

    const GET_FAMSALDOPROD = "
        SELECT F.CDFAMILISALD, F.CDPRODUTO
          FROM FAMSALDOPROD F JOIN FAMSALDOFILI FI
          ON F.CDFILIAL = FI.CDFILIAL
          AND F.CDFAMILISALD = FI.CDFAMILISALD
          WHERE F.CDPRODUTO = :CDPRODUTO
            AND F.CDFILIAL = :CDFILIAL
    ";

    const CHECK_BALANCE = "
        SELECT VRSALDCONFAM
          FROM SALDOCONS
          WHERE CDCLIENTE = :CDCLIENTE
            AND CDCONSUMIDOR = :CDCONSUMIDOR
            AND CDFAMILISALD = :CDFAMILISALD
    ";

    const BUSCA_FAMILIA_SALDO_FILIAL = "
        SELECT FAM.CDFAMILISALD, FAM.NMFAMILISALD
        FROM FAMILIASALDO FAM
        JOIN FAMSALDOFILI FIL ON FIL.CDFAMILISALD = FAM.CDFAMILISALD
        WHERE CDFILIAL = :CDFILIAL
    ";

    const UPDATE_VENDA_STATUS_NFCE_CAN = "
        UPDATE VENDA
          SET DSOBSSTATUSNFCE = :DSOBSSTATUSNFCE, NRPROTOCOLOCANC = :NRPROTOCOLOCANC,
            DTHRPROTOCOCANC = :DTHRPROTOCOCANC, DSRAZAOCANCNFCE = :DSRAZAOCANCNFCE,
            DSARQXMLCANCNFCE = :DSARQXMLCANCNFCE, CDOPERADORCANC = :CDOPERADORCANC
        WHERE CDFILIAL = :CDFILIAL
          AND CDCAIXA = :CDCAIXA
          AND NRSEQVENDA = :NRSEQVENDA
    ";

    const RESTRICAO_PRODUTO_DIA = "
        SELECT QTCONSDIARAL
        FROM PRODRALIMENT
        WHERE CDCLIENTE = :CDCLIENTE
        AND CDCONSUMIDOR = :CDCONSUMIDOR
        AND CDFAMILISALD = :CDFAMILISALD
        AND CDFILIAL = :CDFILIAL
        AND CDPRODUTO = :CDPRODUTO
    ";

    const GET_TOTAL_VENDA_CREDITO_PESSOAL = "
        SELECT
        SUM(VRMOVEXTCONS) AS SUMEXTRATOCONS
        FROM EXTRATOCONS
        WHERE IDTPMOVEXT = 'V'
        AND CAST(DTMOVEXTCONS AS DATE) = CAST(GETDATE() AS DATE)
        AND CDCONSUMIDOR = :CDCONSUMIDOR
        AND CDCLIENTE = :CDCLIENTE
        AND CDFAMILISALD = :CDFAMILISALD
        AND CDFILIAL = :CDFILIAL
    ";

    const GET_CONSUMER_SALDOCONS_NMFAMILISALD = "
        SELECT C.NMCONSUMIDOR, S.VRSALDCONFAM, F.NMFAMILISALD, C.CDCLIENTE, C.CDCONSUMIDOR, F.CDFAMILISALD
        FROM CONSUMIDOR C
        LEFT JOIN SALDOCONS S ON S.CDCONSUMIDOR = C.CDCONSUMIDOR AND S.CDCLIENTE = C.CDCLIENTE
        LEFT JOIN FAMILIASALDO F ON F.CDFAMILISALD = S.CDFAMILISALD
        WHERE C.CDCLIENTE = :CDCLIENTE AND C.CDCONSUMIDOR = :CDCONSUMIDOR
    ";

    const PERMITE_SALDO_NEGATIVO_CONSUMIDOR = "
        SELECT IDSALDNEGRAL
        FROM RALIMENT
        WHERE CDCLIENTE = :CDCLIENTE
        AND CDCONSUMIDOR = :CDCONSUMIDOR
        AND CDFAMILISALD = :CDFAMILISALD
        AND CDFILIAL = :CDFILIAL
    ";

    const RESTRICAO_SALDO_DIARIO = "
        SELECT VRCONSDIARAL
        FROM RALIMENT
        WHERE CDCONSUMIDOR = :CDCONSUMIDOR
        AND CDCLIENTE = :CDCLIENTE
        AND CDFAMILISALD = :CDFAMILISALD
        AND CDFILIAL = :CDFILIAL
    ";

    const PRODUTO_CONSUMIDO_DIA = "
        SELECT SUM(QTPRODVEND) AS PRODUTO_DIA
        FROM ITEMVENDA IT
        JOIN VENDA V ON V.NRSEQVENDA = IT.NRSEQVENDA
        WHERE V.CDCLIENTE = :CDCLIENTE
        AND V.CDCONSUMIDOR = :CDCONSUMIDOR
        AND V.DTENTRVENDA = CAST(GETDATE() AS date)
        AND IT.CDPRODUTO = :CDPRODUTO
        AND IT.CDFILIAL = :CDFILIAL
        AND IT.CDCAIXA = :CDCAIXA
    ";

    const SQL_GET_PERMITE_SALDO_NEGATIVO = "
        SELECT FIL.IDSALDNEGFAM, FAM.NMFAMILISALD
        FROM FAMSALDOFILI FIL
        JOIN FAMILIASALDO FAM ON FIL.CDFAMILISALD = FAM.CDFAMILISALD
            WHERE FIL.CDFILIAL = :CDFILIAL AND FIL.CDFAMILISALD = :CDFAMILISALD
    ";

    const INSERT_PRICED_OBSERVATION = "
      INSERT INTO	OBSITCOMANDAVEN
            (CDFILIAL, NRVENDAREST, NRCOMANDA, NRPRODCOMVEN, CDOCORR, CDGRPOCOR, NRPRODCOMVENOBS, QTPRODCOMVENOBS)
        VALUES
            (:CDFILIAL, :NRVENDAREST, :NRCOMANDA, :NRPRODCOMVEN, :CDOCORR, :CDGRPOCOR, :NRPRODCOMVENOBS, :QTPRODCOMVENOBS)

    ";

    const GET_IDTIPCOBRA = "
      SELECT IDTIPCOBRA

      FROM LOJA

      WHERE CDFILIAL = :CDFILIAL
      AND CDLOJA = :CDLOJA
    ";

    const GET_CLIENTBRANCH_PRICING_TABLE = "
      SELECT CDTABEPREC, CDCFILTABPRE AS CDFILTABPREC
      FROM CLIENFILIAL
      WHERE CDCLIENTE = :CDCLIENTE
      AND CDFILIAL  = :CDFILIAL
    ";

    const GET_CLIENT_PRICING_TABLE = "
      SELECT CDTABEPREC, CDFILTABPREC AS CDFILTABPREC
      FROM CLIENTE
      WHERE CDCLIENTE = :CDCLIENTE
    ";

    const GET_STORE_PRICING_TABLE = "
      SELECT CDTABEPREC, NULL AS CDFILTABPREC
      FROM LOJA
      WHERE CDFILIAL = :CDFILIAL
      AND CDLOJA   = :CDLOJA
    ";

    const GET_PARAVEND_PRICING_TABLE = "
      SELECT ISNULL(CDTABEPREC, CDTABEPRECDLV) AS CDTABEPREC, NULL AS CDFILTABPREC
      FROM PARAVEND
      WHERE CDFILIAL = :CDFILIAL
    ";

    const HOLIDAY_CHECK = "
      SELECT DTFERIFILI
        FROM FERIFILI
       WHERE CDFILIAL   = :CDFILIAL
         AND DTFERIFILI = :DTFERIFILI
    ";

    const ITEM_DAY_PRICE = "
      SELECT VRPRECODIA, IDPERVALORPR, IDDESCACREPR,
        IDVISUACUPOM, CDTIPOCONSPD
      FROM ITEMPRECODIA
      WHERE CDFILIAL    = :CDFILIAL
      AND CDTABEPREC    = :CDTABEPREC
      AND DTINIVGPREC   = :DTINIVGPREC
      AND CDPRODUTO     = :CDPRODUTO
      AND CDPRPAITABPR  = :CDPRPAITABPR
      AND NRDIASEMANPR  = :NRDIASEMANPR
      AND (CDTIPOCONSPD = :CDTIPOCONSPD OR CDTIPOCONSPD = 'T')
      AND (:HORA BETWEEN HRINIPRECDIA AND HRFINPRECDIA)
      ORDER BY CDPRODUTO
    ";

    const GET_ALIQUOTA = "
      SELECT COUNT(*) AS COUNT

      FROM ALIQIMPFIS

      WHERE CDFILIAL  = :CDFILIAL
      AND CDPRODUTO = :CDPRODUTO
    ";

    const GET_SALES_TABLE = "
      SELECT CONVERT(VARCHAR(20), DTINIVGPREC, 'DD/MM/YYYY') AS DTINIVGPREC, DTINIVGPREC AS ORIGINAL
      FROM TABEVEND
      WHERE CDFILIAL = :CDFILIAL
      AND CDTABEPREC = :CDTABEPREC
      AND (GETDATE() BETWEEN DTINIVGPREC AND DTFINVGPREC)
    ";

    const GET_ITEM_PRICE = "
      SELECT IIF(IsNull(VRPRECITEM), 0, VRPRECITEM) AS PRECO,
        IIF(IsNull(VRPRECITEMCL), 0, VRPRECITEMCL) AS PRECOCLIE,
        IIF(IsNull(VRPRESUGITEM), 0, VRPRESUGITEM) AS PRECOSUGER, IDPRECVARIA
      FROM ITEMPRECO
      WHERE CDFILIAL  = :CDFILIAL
      AND CDTABEPREC  = :CDTABEPREC
      AND DTINIVGPREC = CONVERT(DATETIME, :DATAVIG, 'DD/MM/YYYY')
      AND CDPRODUTO   = :CDPRODUTO
      ORDER BY CDPRODUTO
    ";

    //AINDA SENDO USADO SOMENTE EM BASES COM UM CLIENTE, DEPOIS SERÁ FEITO VERIFICANDO O CLIENTE PARA EVITAR UPDATE EM MAIS DE UMA LINHA
    const UPDATE_SENHA_DELPHI = "
          UPDATE CONSUMIDOR
          SET CDSENHACONS = :CDSENHACONS
          WHERE CDIDCONSUMID = :CDIDCONSUMID
    ";

    // Queries de Evolução de Saldo
    const GET_SALDO_ALL_FAMILIES = "
        SELECT C.CDCLIENTE, C.NRORG, C.CDCONSUMIDOR,C.CDFAMILISALD,F.DTMOVEXTCONS, F.VRSALDCONEXT AS VRSALDCONEXT, C.NRORG
          FROM (SELECT C.CDCLIENTE, C.NRORG, C.CDCONSUMIDOR,F.CDFAMILISALD
                  FROM CONSUMIDOR C, FAMILIASALDO F
                 WHERE ((CDCLIENTE = :CDCLIENTE) OR (:TCDCLIENTE = 'T' ))
                   AND ((CDCONSUMIDOR = :CDCONSUMIDOR) OR (:TCDCONSUMIDOR = 'T'))
               ) C
          LEFT JOIN (SELECT E.CDCLIENTE,E.CDCONSUMIDOR,E.CDFAMILISALD,E.DTMOVEXTCONS, E.VRSALDCONEXT, E.NRORG
                       FROM EXTRATOCONS E, (SELECT E.CDCLIENTE,E.CDCONSUMIDOR,E.CDFAMILISALD,D.DTMOVEXTCONS, MAX(E.NRSEQMOVEXT) AS NRSEQMOVEXT, E.NRORG
                                              FROM EXTRATOCONS E, (SELECT CDCLIENTE,CDCONSUMIDOR,CDFAMILISALD,MAX(DTMOVEXTCONS) AS DTMOVEXTCONS, NRORG
                                                                     FROM EXTRATOCONS
                                                                    WHERE ((CDCLIENTE = :CDCLIENTE) OR (:TCDCLIENTE = 'T'))
                                                                      AND ((CDCONSUMIDOR = :CDCONSUMIDOR) OR (:TCDCONSUMIDOR = 'T'))
                                                                      AND (DTMOVEXTCONS < :DTMOVEXTCONS)
                                                                    GROUP BY CDCLIENTE,CDCONSUMIDOR,CDFAMILISALD, NRORG
                                                                  ) D
                                              WHERE E.CDCLIENTE    = D.CDCLIENTE
                                                AND E.CDCONSUMIDOR = D.CDCONSUMIDOR
                                                AND E.CDFAMILISALD = D.CDFAMILISALD
                                                AND E.DTMOVEXTCONS = D.DTMOVEXTCONS
                                                AND E.NRORG        = D.NRORG
                                              GROUP BY E.CDCLIENTE,E.CDCONSUMIDOR,E.CDFAMILISALD,D.DTMOVEXTCONS, E.NRORG
                                           ) DT
                      WHERE E.CDCLIENTE     = DT.CDCLIENTE
                        AND E.CDCONSUMIDOR  = DT.CDCONSUMIDOR
                        AND E.CDFAMILISALD  = DT.CDFAMILISALD
                        AND E.DTMOVEXTCONS  = DT.DTMOVEXTCONS
                        AND E.NRSEQMOVEXT   = DT.NRSEQMOVEXT
                        AND E.NRORG         = DT.NRORG
                    ) F
                 ON C.CDCLIENTE    = F.CDCLIENTE
                AND C.CDCONSUMIDOR = F.CDCONSUMIDOR
                AND C.CDFAMILISALD = F.CDFAMILISALD
                AND C.NRORG        = F.NRORG
              ORDER BY C.CDCLIENTE,C.CDCONSUMIDOR,C.CDFAMILISALD
    ";

    const GET_SALDO_FOR_FAMILIES = "
        SELECT CDCLIENTE, CDCONSUMIDOR, CDFAMILISALD, DTMOVEXTCONS, NRSEQMOVEXT, IDTPMOVEXT, DSOPEEXTCONS, VRMOVEXTCONS, VRSALDCONEXT, 0 AS SALDOFINAL, NRORG
          FROM EXTRATOCONS
         WHERE CDCLIENTE     = :CDCLIENTE
           AND CDCONSUMIDOR  = :CDCONSUMIDOR
           AND CDFAMILISALD  = :CDFAMILISALD
           AND DTMOVEXTCONS >= :DTMOVEXTCONS
           AND NRORG         = :NRORG
         ORDER BY CDFAMILISALD, DTMOVEXTCONS
    ";

    const ATUALIZA_MOVIMENTACAO_EXTRATO = "
        UPDATE EXTRATOCONS
           SET VRSALDCONEXT = :SALDOFINAL
         WHERE CDCLIENTE    = :CDCLIENTE
           AND CDCONSUMIDOR = :CDCONSUMIDOR
           AND CDFAMILISALD = :CDFAMILISALD
           AND DTMOVEXTCONS = :DTMOVEXTCONS
           AND NRSEQMOVEXT  = :NRSEQMOVEXT
           AND NRORG        = :NRORG
    ";

    const GET_VERIFICA_SALDO = "
        SELECT CDCLIENTE
          FROM SALDOCONS
         WHERE CDCLIENTE  = :CDCLIENTE
           AND CDCONSUMIDOR = :CDCONSUMIDOR
           AND CDFAMILISALD = :CDFAMILISALD
           AND NRORG = :NRORG
    ";

    const INSERT_SALDO_CONS = "
        INSERT INTO SALDOCONS (CDCLIENTE, CDCONSUMIDOR, CDFAMILISALD, VRSALDCONFAM, NRORG)
        VALUES (:CDCLIENTE, :CDCONSUMIDOR, :CDFAMILISALD, :VRSALDCONFAM, :NRORG)
    ";

    const ATUALIZA_SALDO_CONS = "
        UPDATE SALDOCONS
           SET VRSALDCONFAM = :VRSALDCONFAM
         WHERE CDCLIENTE = :CDCLIENTE
           AND CDCONSUMIDOR = :CDCONSUMIDOR
           AND CDFAMILISALD = :CDFAMILISALD
           AND NRORG   = :NRORG
    ";

    const VERIFICA_CANCELAMENTO = "
        SELECT NRDEPOSICONS
          FROM EXTRATOCONS
         WHERE CDCLIENTE     = :CDCLIENTE
           AND CDCONSUMIDOR  = :CDCONSUMIDOR
           AND CDFAMILISALD  = :CDFAMILISALD
           AND NRDEPOSICONS  = :NRDEPOSICONS
           AND NRSEQMOVCAIXA = :NRSEQMOVCAIXA
           AND IDTPMOVEXT    = 'D'
    ";

    const VERIFICA_SALDO_CANCELAMENTO = "
        SELECT A.CDFAMILISALD, A.VRSALDCONFAM, A.NMFAMILISALD
          FROM (SELECT F.CDFAMILISALD, S.VRSALDCONFAM, FA.NMFAMILISALD
                  FROM SALDOCONS S, FAMSALDOFILI F, FAMILIASALDO FA
                 WHERE (CDCONSUMIDOR   = :CDCONSUMIDOR)
                   AND (S.CDCLIENTE    = :CDCLIENTE)
                   AND (F.CDFILIAL     = :CDFILIAL)
                   AND (S.CDFAMILISALD = F.CDFAMILISALD)
                   AND (S.CDFAMILISALD = FA.CDFAMILISALD)
                   AND (F.CDFAMILISALD = FA.CDFAMILISALD)

                 UNION ALL

                SELECT F.CDFAMILISALD, 0, FA.NMFAMILISALD
                  FROM FAMSALDOFILI F, FAMILIASALDO FA
                 WHERE (F.CDFAMILISALD = FA.CDFAMILISALD)
                   AND (F.CDFILIAL = :CDFILIAL)
                   AND F.CDFAMILISALD NOT IN (SELECT F.CDFAMILISALD
                                                FROM SALDOCONS S
                                               WHERE (CDCONSUMIDOR = :CDCONSUMIDOR)
                                                 AND (S.CDCLIENTE  = :CDCLIENTE)
                                                 AND (S.CDFAMILISALD = F.CDFAMILISALD))
               ) A
         ORDER BY A.CDFAMILISALD
    ";

    const UPDATE_MOVCAIXA = "
        UPDATE MOVCAIXA
           SET IDTIPOMOVIVE = 'C'
         WHERE CDFILIAL   = :CDFILIAL
           AND CDCAIXA    = :CDCAIXA
           AND DTABERCAIX = :DTABERCAIX
           AND NRSEQUMOVI = :NRSEQUMOVI
           AND NRSEQVENDA IS NULL
           AND IDTIPOMOVIVE = 'E'
    ";

    const SQL_GET_GASTO_DIA_DEBITO_CONSUMIDOR = "
      SELECT CASE WHEN SUM(VRMOVCLI) IS NULL THEN 0 ELSE SUM(VRMOVCLI) END AS CONSUMO
        FROM MOVICLIE
       WHERE DTMOVCLI > :DTMOVCLI
         AND CDCLIENTE = :CDCLIENTE
         AND CDCONSUMIDOR = :CDCONSUMIDOR
         AND IDTIPMOCVLI = 'D'
    ";

    const SQL_GET_GASTO_MES_DEBITO_CONSUMIDOR = "
      SELECT CASE WHEN SUM(VRMOVCLI) IS NULL THEN 0 ELSE SUM(VRMOVCLI) END AS CONSUMO
        FROM MOVICLIE
       WHERE DTMOVCLI > :DTMOVCLI
         AND CDCLIENTE = :CDCLIENTE
         AND CDCONSUMIDOR = :CDCONSUMIDOR
         AND IDTIPMOCVLI = 'D'
    ";

    const BUSCA_HORARIO_PRECOS = "
        SELECT NRDIASEMANPR, HRINIPRECDIA, HRFINPRECDIA
          FROM ITEMPRECODIA
         WHERE CDFILIAL = :CDFILIAL
           AND CDTABEPREC = :CDTABEPREC
           AND DTINIVGPREC = :DTINIVGPREC
           AND (NRDIASEMANPR = :NRDIASEMANPR OR NRDIASEMANPR = 'T')
         GROUP BY NRDIASEMANPR, HRINIPRECDIA, HRFINPRECDIA
         ORDER BY NRDIASEMANPR
    ";

    const UPDATE_VENDA_NFCE = "
        UPDATE VENDA
          SET CDSERIENFCE = :CDSERIENFCE,
            IDTPAMBNFCE = :IDTPAMBNFCE,
            NRACESSONFCE = :NRACESSONFCE,
            NRNOTAFISCALCE = :NRNOTAFISCALCE,
            NRLANCTONFCE = :NRLANCTONFCE,
            IDSITUVENDA = :IDSITUVENDA,
            IDSTATUSNFCE = :IDSTATUSNFCE,
            DTEMISSAONFCE = :DTEMISSAONFCE,
            NRPROTOCOLONFCE = :NRPROTOCOLONFCE,
            DSOBSSTATUSNFCE = :DSOBSSTATUSNFCE,
            IDMODOPERACNFCE = :IDMODOPERACNFCE,
            NRRECIBONFCE = :NRRECIBONFCE,
            CDOPERENVIONFCE = :CDOPERENVIONFCE,
            DTHRPROTOCONFCE = :DTHRPROTOCONFCE,
            IDXMLVALIDNFCE = :IDXMLVALIDNFCE,
            DSQRCODENFCE = :DSQRCODENFCE,
            DSARQXMLNFCE = CAST(CAST(:DSARQXMLNFCE AS VARBINARY(MAX)) AS IMAGE),
            CDVERSXMLNFCVND = :CDVERSXMLNFCVND
        WHERE NRORG = :NRORG
            AND NRSEQVENDA = :NRSEQVENDA
            AND CDFILIAL = :CDFILIAL
            AND CDCAIXA = :CDCAIXA
    ";

    const GET_ITENS_CANCELADOS = "
        SELECT SUM(ROUND(ISNULL(I.QTPRODVENDC, 0)*(ISNULL(I.VRUNITVENDC, 0)+ISNULL(I.VRUNITVENCLC, 0))+ISNULL(I.VRACRITVENDC, 0), 2)) AS VRCANCEL
          FROM VENDA V, ITVENDACAN I
        WHERE V.CDFILIAL = :CDFILIAL
          AND V.CDCAIXA = :CDCAIXA
          AND ((:FINAL = 1 AND CONVERT(DATE, V.DTABERTUR, 103) = CONVERT(DATE, :DTABERCAIX, 103))
          OR (:FINAL <> 1 AND V.DTABERTUR = :DTABERCAIX))
          AND V.CDFILIAL = I.CDFILIAL
          AND V.CDCAIXA = I.CDCAIXA
          AND V.NRSEQVENDA = I.NRSEQVENDA
    ";

    const GET_CONSULTA_SALDO = "
        SELECT A.CDFAMILISALD, A.VRSALDCONFAM, A.NMFAMILISALD
          FROM (SELECT F.CDFAMILISALD, S.VRSALDCONFAM, FA.NMFAMILISALD
                  FROM SALDOCONS S, FAMSALDOFILI F, FAMILIASALDO FA
                 WHERE (CDCONSUMIDOR = :CDCONSUMIDOR)
                   AND (S.CDCLIENTE  = :CDCLIENTE)
                   AND (F.CDFILIAL   = :CDFILIAL)
                   AND (S.CDFAMILISALD = F.CDFAMILISALD)
                   AND (S.CDFAMILISALD = FA.CDFAMILISALD)
                   AND (F.CDFAMILISALD = FA.CDFAMILISALD)
                 UNION ALL
                SELECT F.CDFAMILISALD ,  0, FA.NMFAMILISALD
                  FROM FAMSALDOFILI F, FAMILIASALDO FA
                 WHERE (F.CDFAMILISALD = FA.CDFAMILISALD)
                   AND F.CDFILIAL = :CDFILIAL
                   AND F.CDFAMILISALD NOT IN (SELECT F.CDFAMILISALD
                                                FROM SALDOCONS S
                                               WHERE (CDCONSUMIDOR   = :CDCONSUMIDOR)
                                                 AND (S.CDCLIENTE    = :CDCLIENTE)
                                                 AND (S.CDFAMILISALD = F.CDFAMILISALD))
               ) A
         ORDER BY A.CDFAMILISALD
    ";

    const BUSCA_MOVI_EXTRATOCONS = "
        SELECT CDCLIENTE, CDCONSUMIDOR, CDFAMILISALD, DTMOVEXTCONS,
               NRSEQMOVEXT, CDTIPORECE, CDFILIAL, CDCAIXA, DTABERCAIX,
               NRSEQVENDA, VRMOVEXTCONS, VRSALDCONEXT, NRSEQMOVCAIXA
          FROM EXTRATOCONS
         WHERE CDFILIAL = :CDFILIAL
           AND CDCAIXA = :CDCAIXA
           AND NRSEQVENDA = :NRSEQVENDA
           AND IDTPMOVEXT = 'V'
    ";

    const BUSCA_ITENS_SUGESTAO = "
        SELECT SV.IDSUGESTAOVENDA, SV.CDPRODUTO, SI.CDSUGESTAOITEM, SI.IDSUGESTAOTIPO
          FROM SUGESTAOVENDA SV
        JOIN SUGESTAOITEM SI
          ON SI.IDSUGESTAOVENDA = SV.IDSUGESTAOVENDA
        WHERE SV.CDFILIAL = :CDFILIAL
          AND SV.NRCONFTELA = :NRCONFTELA
          AND SV.CDPRODUTO IN (:ARRAYPRODUTOS)
    ";

    const BUSCA_DATA_ULTIMA_VENDA = "
        SELECT V.CDFILIAL, V.CDCAIXA, V.NRNOTAFISCALCE, V.DTVENDA,
            V.CDSERIENFCE,V.IDTPAMBNFCE,V.IDSTATUSNFCE
        FROM VENDA V
        LEFT JOIN (SELECT MAX(DTVENDA) AS DTVENDA
            FROM VENDA
            WHERE DTVENDA < :DTABERCAIX
            AND CDFILIAL = :CDFILIAL
            AND CDCAIXA = :CDCAIXA) TEMP ON 1 = 1
        WHERE V.CDFILIAL = :CDFILIAL
        AND V.CDCAIXA = :CDCAIXA
        AND V.IDTPEMISVEND = 'N'
        AND V.IDTPAMBNFCE IS NOT NULL
        AND V.DTVENDA >= CASE WHEN TEMP.DTVENDA IS NULL THEN :DTABERCAIX ELSE TEMP.DTVENDA END
        ORDER BY V.NRNOTAFISCALCE
    ";

    const INSERT_INUTILIZANFCE = "
        INSERT INTO INUTILIZANFCE
          (CDFILIAL, CDCAIXA, NRNOTAFISCALCE,
          DTENTRVENDA, CDSERIENFCE, IDTPAMBNFCE,
          IDSTATUSNFCE, DTHRINUTNFCE, NRPROTOINUTNFCE,
          IDTPEMISINUNFCE, DSRAZAOINUTNFCE,
          DSOBSINUTNFCE,IDIMPINUTILIZA)
        VALUES
          (:CDFILIAL, :CDCAIXA, :NRNOTAFISCALCE,
          :DTENTRVENDA, :CDSERIENFCE, :IDTPAMBNFCE,
          :IDSTATUSNFCE, :DTHRINUTNFCE, :NRPROTOINUTNFCE,
          :IDTPEMISINUNFCE, :DSRAZAOINUTNFCE,
          :DSOBSINUTNFCE, :IDIMPINUTILIZA)
    ";

    const UPDATE_BUSCA_CONTADOR = "
        UPDATE NOVOCODIGO SET NRSEQUENCIAL = :NRSEQUENCIAL
        WHERE CDCONTADOR = :CDCONTADOR
        AND NRORG = :NRORG
    ";

    const GET_ULTIMA_VENDA_SAT = "
        SELECT CDFILIAL, CDCAIXA, NRSEQVENDA, DTVENDA, IDIMPVENDA FROM VENDA
            WHERE CDFILIAL=:CDFILIAL
                AND CDCAIXA=:CDCAIXA
                AND NRACESSONFCE LIKE '%'+ :NRACESSONFCE +'%'
    ";

    const UPDATE_VENDA_INSERIDA_SAT = "
        UPDATE VENDA
          SET IDXMLSATTRANS='T'
        WHERE CDFILIAL=:CDFILIAL
          AND CDCAIXA=:CDCAIXA
          AND IDTPEMISVEND='S'
          AND IDXMLSATTRANS='P'
          AND (IDSTATUSNFCE='A' OR IDSTATUSNFCE='C')
          AND NRSEQVENDA<=:NRSEQVENDA
          AND IDIMPVENDA='I'
    ";

    const UPDATE_VENDA_ALTERADA_SAT = "
        UPDATE VENDA
          SET IDIMPVENDA='A', IDXMLSATTRANS='T'
        WHERE CDFILIAL=:CDFILIAL
          AND CDCAIXA=:CDCAIXA
          AND IDTPEMISVEND='S'
          AND IDXMLSATTRANS='P'
          AND (IDSTATUSNFCE='A' OR IDSTATUSNFCE='C')
          AND NRSEQVENDA<=:NRSEQVENDA
          AND IDIMPVENDA <> 'I'
    ";

    const CHECK_INDEX = "
        SELECT 1 FROM SYS.INDEXES WHERE NAME = :INDEXNAME
    ";

    const CREATE_BUSCA_CONSUMIDOR = "
        CREATE INDEX BUSCA_CONSUMIDOR ON CONSUMIDOR (CDCONSUMIDOR, NMCONSUMIDOR, CDIDCONSUMID, NRCPFRESPCON, CDEXCONSUMID ASC)
    ";

    const UPDATE_MESA_CREDFIDELITY = "
        UPDATE COMANDAVEN
          SET VRDESCFID = 0
        WHERE CDFILIAL = :CDFILIAL
          AND NRVENDAREST = :NRVENDAREST
          AND NRCOMANDA = :NRCOMANDA
    ";

    const DELETA_POSVENDAREST_POS = "
        DELETE FROM POSVENDAREST
          WHERE CDFILIAL = :CDFILIAL
            AND NRVENDAREST = :NRVENDAREST
            AND NRLUGARMESA IN (:NRLUGARMESA)
    ";

    const DELETA_CONTROLPOSVEN = "
        DELETE FROM CONTROLPOSVEN
        WHERE CDFILIAL = :CDFILIAL
          AND NRVENDAREST = :NRVENDAREST
          AND CDOPERADOR = :CDOPERADOR
    ";

    const BUSCA_FILESERVER = "
        SELECT FILESERVERURL
          FROM PARAMGERAL
    ";

    const SQL_DADOS_MESA = "
        SELECT V.NRVENDAREST, V.NRPESMESAVEN, C.NRCOMANDA, V.CDVENDEDOR,
               M.IDSTMESAAUX, V.CDCLIENTE, V.CDCONSUMIDOR, M.CDSALA,
               M.NMMESA, M.NRMESA,  V.NRPOSICAOMESA, L.NMRAZSOCCLIE,
               O.NMCONSUMIDOR, O.NRCPFRESPCON, C.VRDESCFID
          FROM COMANDAVEN C, MESA M,
               VENDAREST V LEFT JOIN CLIENTE L
                                  ON V.CDCLIENTE = L.CDCLIENTE
                           LEFT JOIN CONSUMIDOR O
                                  ON V.CDCLIENTE = O.CDCLIENTE
                                 AND V.CDCONSUMIDOR = O.CDCONSUMIDOR
         WHERE V.CDFILIAL    = ?
           AND C.NRCOMANDA   = ?
           AND C.NRVENDAREST = ?
           AND V.CDLOJA      = ?
           AND V.CDFILIAL    = C.CDFILIAL
           AND C.NRVENDAREST = V.NRVENDAREST
           AND V.CDLOJA      = C.CDLOJA
           AND V.CDFILIAL    = M.CDFILIAL
           AND V.CDLOJA      = M.CDLOJA
           AND V.NRMESA      = M.NRMESA
           AND V.DTHRFECHMESA IS NULL
    ";

    const SQL_GRUPO_OBS_DESC = "
        SELECT CDGRPOCORDESC
        FROM LOJA
          WHERE CDFILIAL = :CDFILIAL
            AND CDLOJA = :CDLOJA
    ";

    const GET_SANGRIA = "
        SELECT ISNULL(SUM(M.VRMOVIVEND),0) AS VRSAIDA, T.CDTIPORECE,
          CASE
            WHEN M.IDTIPOMOVIVE = 'U' THEN
              'Suprimento'
            WHEN M.IDTIPOMOVIVE = 'S' AND T.IDTIPORECE = '7' THEN
              'Contra Vale Emitido'
            ELSE
              T.NMTIPORECE
          END AS NMTIPORECE,
          CASE
            WHEN M.IDTIPOMOVIVE = 'G' AND T.IDTIPORECE <> '7' THEN
              '1'
            WHEN M.IDTIPOMOVIVE = 'S' AND T.IDTIPORECE = '7' THEN
              '2'
            WHEN M.IDTIPOMOVIVE = 'U' THEN
              '3'
            WHEN M.IDTIPOMOVIVE = 'G' AND T.IDTIPORECE = '7' THEN
              '4'
            ELSE NULL
          END AS IDTIPOREG,
          M.DTABERCAIX
        FROM MOVCAIXA M
        JOIN TIPORECE T
          ON M.CDTIPORECE = T.CDTIPORECE
        WHERE M.CDFILIAL = :CDFILIAL
          AND M.CDCAIXA = :CDCAIXA
          AND M.DTABERCAIX = :DTABERCAIX
          AND (M.IDTIPOMOVIVE IN ('G', 'U') OR (M.IDTIPOMOVIVE = 'S' AND T.IDTIPORECE = '7'))
        GROUP BY T.CDTIPORECE, T.NMTIPORECE, T.IDTIPORECE, M.DTABERCAIX, M.IDTIPOMOVIVE
    ";

    const SQL_OBRIGA_FECH = "
        SELECT IDOBRIGFECHCAIX
        FROM CAIXA
          WHERE CDFILIAL = :CDFILIAL
            AND CDCAIXA = :CDCAIXA
            AND NRORG = :NRORG
    ";

    const DELETA_ITCOMANDAVENDES = "
        DELETE FROM ITCOMANDAVENDES
         WHERE CDFILIAL = :CDFILIAL
          AND NRVENDAREST = :NRVENDAREST
    ";

   const INSERT_ITVENDADES = "
        INSERT INTO ITVENDADES
            (CDFILIAL, CDCAIXA, NRSEQVENDA, NRSEQITVENDADES, QTPRODVENDADES, CDPRODUTO,
             NRORG, VRPRECCOMVEN, VRDESCCOMVEN, VRACRCOMVEN)
          VALUES
            (:CDFILIAL, :CDCAIXA, :NRSEQVENDA, :NRSEQITVENDADES, :QTPRODVENDADES, :CDPRODUTO,
             :NRORG, :VRPRECCOMVEN, :VRDESCCOMVEN, :VRACRCOMVEN)
    ";

    const GET_OBSERVATION_TYPE = "
        SELECT OC.IDCONTROLAOBS, OC.CDPRODUTO
          FROM OCORRENCIA OC JOIN LOJA LJ
                               ON OC.CDGRPOCOR = LJ.CDGRPOCORPED
        WHERE LJ.CDFILIAL = ?
          AND LJ.CDLOJA = ?
          AND OC.CDOCORR = ?
    ";

    const SELECT_ITCOMANDAVENDES = "
        SELECT * FROM ITCOMANDAVENDES
         WHERE CDFILIAL = :CDFILIAL
          AND NRVENDAREST = :NRVENDAREST
         ORDER BY NRSEQITCOMVENDES
    ";

    const GET_PAYMENT_PARAM_DLV = "
        SELECT CDFILIAL, NRVENDAREST, IDTIPOMOVIVEDLV, CDCLIENTE, CDCONSUMIDOR,
               CDTIPORECE, VRMOVIVENDDLV VRMOVIVEND, NRCARTBANCODLV NRCARTBANCO
        FROM MOVCAIXADLV
        WHERE CDFILIAL = :CDFILIAL
          AND NRVENDAREST = :NRVENDAREST
    ";

    const CHANGE_STATUS_COMANDA = "
        UPDATE COMANDAVEN
        SET IDSTCOMANDA = :IDSTCOMANDA
        WHERE CDFILIAL      = :CDFILIAL
          AND NRVENDAREST   = :NRVENDAREST
          AND NRCOMANDA     = :NRCOMANDA
    ";

    const GET_PARAMS_DELIVERY_SALE = "
        SELECT COM.DSBAIRRO, COM.IDRETBALLOJA, COM.DSAREAATEND, COM.NRCEPCONSCOMAND,
               COM.DSCOMPLENDCOCOM, COM.CDBAIRRO, COM.CDMUNICIPIO, COM.DSENDECONSCOMAN
         FROM COMANDAVEN COM
         WHERE COM.CDFILIAL = :CDFILIAL
           AND COM.NRVENDAREST = :NRVENDAREST
           AND COM.NRCOMANDA = :NRCOMANDA
    ";

    const BUSCA_DADOS_ENTREGA = "
        SELECT DISTINCT P.CDFILIAL, P.CDLOJA, P.NRVENDAREST, DTHRABERMESA, CDCONSUMIDOR, NMCONSUMIDOR,
                        NRTELECONS, P.NRCOMANDA, DSCOMANDA, CDCLIENTE, NRCPFRESPCON, CDMUNICIPIO, CDPAIS, SGESTADO,
                        CDBAIRRO, DSBAIRRO, DSENDECONSCOMAN, DSCOMPLENDCOCOM, NRCEPCONSCOMAND, DSREFENDCONSCOM, DTENTREGA,
                        NRVENDARESTAUX, IDORGCMDVENDA, CDNSUESITEF, IDRETBALLOJA, I.CDSENHAPED, DSAREAATEND, P.VRACRCOMANDA,
                        P.CDOPERADOR, P.NMVENDEDOR, P.NMMUNICIPIO, P.VRDESCOMANDA, DSOBSCOMANDA,
                        NRENDECONSCOMAN, NRCOMANDAEXT, NRTELE2CONS,
                       NRCELULARCONS
          FROM (SELECT CO.CDFILIAL, CO.CDLOJA,
                       CO.IDSTCOMANDA, CO.NRVENDAREST, VE.DTHRABERMESA, CS.CDCONSUMIDOR, CS.NMCONSUMIDOR, CS.NRTELECONS, CO.NRCOMANDA,
                       CO.DSCOMANDA, VE.CDCLIENTE, CS.NRCPFRESPCON,CO.CDMUNICIPIO, CO.CDPAIS, CO.SGESTADO, CO.CDBAIRRO, CO.DSBAIRRO, CO.DSENDECONSCOMAN,
                       CO.DSCOMPLENDCOCOM,CO.NRCEPCONSCOMAND, CAST(CO.DSREFENDCONSCOM AS VARCHAR(2000)) AS DSREFENDCONSCOM, CO.DTENTREGA,
                       VE.NRVENDARESTAUX, CO.IDORGCMDVENDA, VE.CDNSUESITEF, CO.IDRETBALLOJA, CO.DSAREAATEND, M.NMMUNICIPIO, CO.VRACRCOMANDA,
                       VE.CDOPERADOR, VD.NMFANVEN NMVENDEDOR, CO.VRDESCOMANDA, CO.DSOBSCOMANDA,
                       CO.NRENDECONSCOMAN, CO.NRCOMANDAEXT, CS.NRTELE2CONS,
                       CS.NRCELULARCONS
                  FROM COMANDAVEN CO, MUNICIPIO M, VENDAREST VE
                    LEFT JOIN CONSUMIDOR CS
                           ON VE.CDCLIENTE    = CS.CDCLIENTE
                          AND VE.CDCONSUMIDOR = CS.CDCONSUMIDOR
                    LEFT JOIN VENDEDOR VD
                           ON VD.CDVENDEDOR = VE.CDVENDEDOR
                          AND VD.CDFILIAL   = VE.CDFILIAL
                 WHERE CO.NRVENDAREST   = VE.NRVENDAREST
                   AND CO.NRVENDAREST   = :NRVENDAREST
                   AND CO.CDFILIAL      = :CDFILIAL
                   AND CO.CDLOJA        = :CDLOJA
                   AND (CO.CDFILIAL     = VE.CDFILIAL)
                   AND M.CDMUNICIPIO    = CO.CDMUNICIPIO
                   AND CO.SGESTADO      = M.SGESTADO
                   ) P, ITCOMANDAVEN I
        WHERE P.CDFILIAL    = I.CDFILIAL
          AND P.CDLOJA      = I.CDLOJA
          AND P.NRVENDAREST = I.NRVENDAREST
          AND P.NRCOMANDA   = I.NRCOMANDA

    ";

    const BUSCA_ITPEDIDO_ENTREGA = "
        SELECT IT.CDPRODUTO, IT.QTPRODCOMVEN, IT.VRPRECCOMVEN, P.NMPRODUTO, IT.VRDESCCOMVEN,
               IT.DSOBSDESCIT, IT.DSOBSPEDDIGCMD
            FROM ITCOMANDAVEN IT, PRODUTO P
           WHERE CDSENHAPED = :CDSENHAPED
             AND IT.CDPRODUTO = P.CDPRODUTO
             AND IT.NRVENDAREST = :NRVENDAREST
             AND IT.IDSTPRCOMVEN <> 6
           ORDER BY NRPRODCOMVEN

    ";

    const BUSCA_IMPRESSORA_DELIVERY = "
        SELECT IMP.NRSEQIMPRLOJA, I.IDMODEIMPRES, IMP.NMIMPRLOJA, IMP.CDPORTAIMPR, IMP.DSIPPONTE, IMP.DSIPIMPR
            FROM IMPRLOJA IMP,  CAIXA C, IMPRESSORA I
          WHERE IMP.CDFILIAL    = :CDFILIAL
            AND IMP.CDLOJA      = :CDLOJA
            AND C.CDCAIXA       = :CDCAIXA
            AND I.CDIMPRESSORA  = IMP.CDIMPRESSORA
            AND C.CDFILIAL      = IMP.CDFILIAL
            AND C.CDLOJA        = IMP.CDLOJA
            AND ISNULL(C.NRSEQIMPRLOJA7, NRSEQIMPRLOJA3) = IMP.NRSEQIMPRLOJA
    ";

    const BUSCA_TIPORECE_DLV = "
        SELECT TR.CDTIPORECE, TR.NMTIPORECE, TR.VRTAXAADMI, MOV.VRMOVIVENDDLV
         FROM MOVCAIXADLV MOV, TIPORECE TR
           WHERE TR.CDTIPORECE      = MOV.CDTIPORECE
             AND MOV.CDFILIAL       = :CDFILIAL
             AND MOV.NRVENDAREST    = :NRVENDAREST
    ";

    const GET_RETIRA_BALCAO = "
        SELECT V.IDRETBALLOJA, C.IDFINPEDAUTDLV
            FROM VENDA V, CAIXA C
           WHERE V.NRSEQVENDA   = :NRSEQVENDA
             AND V.CDLOJA       = :CDLOJA
             AND V.CDFILIAL     = :CDFILIAL
             AND V.CDCAIXA      = C.CDCAIXA
    ";

    const BUSCA_COUVERT_CONSUMA = "
        SELECT IDCOUVERART, CDPRODCOUVER, IDCONSUMAMIN, CDPRODCONSUM
          FROM LOJA
         WHERE CDFILIAL = :CDFILIAL
           AND CDLOJA = :CDLOJA
    ";

    const GET_NRSEQVENDA = "
        SELECT NRSEQVENDA
            FROM VENDA
            WHERE NRVENDAREST = :NRVENDAREST
              AND CDFILIALTUR = :CDFILIAL
    ";

    const SELECT_ITCOMANDAEST_SPECIFIC = "
        SELECT CDFILIAL, NRVENDAREST, NRCOMANDA, NRPRODCOMVEN,
               CDPRODUTO, QTPROCOMEST, VRPRECCOMEST, VRDESITCOMEST,
               TXPRODCOMVENEST, NRATRAPRODCOES, DSOBSPEDDIGEST
          FROM ITCOMANDAEST
         WHERE CDFILIAL = :CDFILIAL
           AND NRVENDAREST = :NRVENDAREST
           AND NRCOMANDA = :NRCOMANDA
           AND NRPRODCOMVEN = :NRPRODCOMVEN
    ";

    const BUSCA_VENDA_INUTILIZADA = "
        SELECT 1 FROM INUTILIZANFCE
        WHERE CDCAIXA = :CDCAIXA
          AND CDFILIAL = :CDFILIAL
          AND CDSERIENFCE = :CDSERIENFCE
          AND NRNOTAFISCALCE = :NRNOTAFISCALCE
     ";

    const SQL_GET_CONSUMER_BALANCE_API = "
        SELECT SUM(VRSALDCONFAM) AS SALDO
          FROM SALDOCONS
         WHERE CDCLIENTE = ?
           AND CDCONSUMIDOR = ?
         GROUP BY CDCLIENTE, CDCONSUMIDOR, CDFAMILISALD
    ";

    const BUSCA_PRODUTO_SUBGRUPO_API = "
        SELECT CDGRUPPROD, CDSUBGRPROD, CDPRODUTO
          FROM PRSUBGRPROD
         WHERE CHARINDEX(CDPRODUTO, :STRPRODUTOS) > 0
    ";

    const BUSCA_DESCONTO_SUBGRUPO_API = "
      SELECT QTINICIALFX, QTFINALFX, QTPERCDESC
      FROM DESCQTDESUB
      WHERE CDGRUPPROD = :CDGRUPPROD
      AND CDSUBGRPROD = :CDSUBGRPROD
    ";

    const SQL_INSERE_VENDAREST = "
	    INSERT INTO VENDAREST
	        (CDFILIAL, NRVENDAREST, CDLOJA, NRMESA,
	        CDVENDEDOR, DTHRABERMESA, CDOPERADOR, NRPESMESAVEN,
	        CDCLIENTE, CDCONSUMIDOR, NRPOSICAOMESA)
	    VALUES
	        (:CDFILIAL, :NRVENDAREST, :CDLOJA, :NRMESA,
	        :CDVENDEDOR, GETDATE(), :CDOPERADOR, :NRPESMESAVEN,
	        :CDCLIENTE, :CDCONSUMIDOR, :NRPOSICAOMESA)
    ";

    const SQL_ABRE_COMANDA = "
        INSERT INTO COMANDAVEN
            (CDFILIAL, NRVENDAREST, NRCOMANDA, CDLOJA,
            DSCOMANDA, IDSTCOMANDA, SGSEXOCON,
            VRACRCOMANDA, IDORGCMDVENDA, DSCONSUMIDOR, VRDESCFID)
        VALUES
            (:CDFILIAL, :NRVENDAREST, :NRCOMANDA, :CDLOJA,
            :DSCOMANDA, :IDSTCOMANDA, 'M',
            :VRACRCOMANDA, :IDORGCMDVENDA, :DSCONSUMIDOR, 0)
    ";

    const IMPRIMEDLV_AUTOMATICO = "
        SELECT IDIMPAUTENTREG
         FROM CAIXA
        WHERE CDCAIXA  = :CDCAIXA
          AND CDFILIAL = :CDFILIAL
          AND CDLOJA   = :CDLOJA
    ";

    const GET_CDSENHAPED = "
        SELECT CDSENHAPED
         FROM VENDA
        WHERE DTVENDA >= :DTVALIDATE
          AND CDFILIAL = :CDFILIAL
          AND CDLOJA = :CDLOJA
    ";

    const GET_CONFTELA_CAIXA = "
        SELECT CDFILIAL, NRCONFTELA, MAX(DTINIVIGENCIA) AS DTINIVIGENCIA
          FROM CONFTELACAIXA
         WHERE CDFILICAIXA = ?
           AND CDCAIXA = ?
         GROUP BY CDFILIAL, NRCONFTELA
    ";

    const CHECK_FOR_CONFTELA = "
        SELECT TABLE_NAME
          FROM INFORMATION_SCHEMA.TABLES
         WHERE TABLE_NAME = 'CONFTELACAIXA'
    ";

    const VALIDA_VENDA = "
        SELECT NRSEQVENDA
          FROM VENDA
         WHERE CDFILIAL = ?
           AND CDCAIXA = ?
           AND NRSEQVENDA = ?
    ";

    const LOCAL_CERTIFICADO_EXTERNO = "
      SELECT P.FILESERVERURL +
      SUBSTRING(I.DSDIRCERTIFICADO, CHARINDEX(I.DSDIRCERTIFICADO, '>')+1,LEN(I.DSDIRCERTIFICADO)) CERTIFICADO, CDSENHACERTIF, I.DSDIRCERTIFICADO
      FROM INSCRESTAD I, PARAMGERAL P
      WHERE I.CDFILIAL = :CDFILIAL
    ";

    const SQL_GET_COMANDAS_AGRUPADAS = "
        SELECT C.NRCOMANDA, C.NRVENDAREST, V.NRMESA
          FROM COMANDAVEN C JOIN VENDAREST V
            ON V.NRVENDAREST = C.NRVENDAREST
             AND C.CDFILIAL = V.CDFILIAL
             AND C.CDLOJA = V.CDLOJA
         WHERE C.CDFILIAL = :CDFILIAL
           AND C.CDLOJA = :CDLOJA
           AND C.IDSTCOMANDA = '4'
           AND C.DSCOMANDAPRI = (SELECT DSCOMANDA
                                  FROM COMANDAVEN
                                 WHERE CDFILIAL = :CDFILIAL
                                   AND NRCOMANDA = :NRCOMANDA
                                   AND DSCOMANDA = :DSCOMANDA
                                   AND NRVENDAREST = :NRVENDAREST
                                   AND CDLOJA = :CDLOJA)
    ";

    const INSERT_USOCUPOMDESCFOS = "
        INSERT INTO USOCUPOMDESCFOS
            (CDCUPOMDESCFOS, CDFILIAL, CDCAIXA, NRSEQVENDA, NRSEQUITVEND)
        VALUES
            (:CDCUPOMDESCFOS, :CDFILIAL, :CDCAIXA, :NRSEQVENDA, :NRSEQUITVEND)
    ";

    const GET_ITENS_EST = "
        SELECT *
          FROM ITCOMANDAEST
         WHERE CDFILIAL = :CDFILIAL
           AND NRVENDAREST = :NRVENDAREST
           AND NRCOMANDA = :NRCOMANDA
           AND NRPRODCOMVEN = :NRPRODCOMVEN
    ";

    const GET_OBS_EST = "
        SELECT *
          FROM OBSITCOMANDAEST
         WHERE CDFILIAL = :CDFILIAL
           AND NRVENDAREST = :NRVENDAREST
           AND NRCOMANDA = :NRCOMANDA
           AND NRPRODCOMVEN = :NRPRODCOMVEN
           AND CDPRODUTO = :CDPRODUTO
    ";

    const GET_CDTABEPREC_GERAL = "
        SELECT CDTABEPREC
          FROM FILITABGER
         WHERE CDFILIAL = :CDFILIAL
    ";

    const TABELA_VENDA_GERAL = "
        SELECT CONVERT(VARCHAR,DTINIVGPREC,120) AS DTINIVGPREC
          FROM TABEVENDGER
         WHERE CDTABEPREC = :CDTABEPREC
           AND (CONVERT(VARCHAR, GETDATE(), 103) BETWEEN DTINIVGPREC AND DTFINVGPREC)
    ";

    const ITEM_PRECO_GERAL = "
        SELECT ISNULL(I.VRPRECITEM, 0) AS PRECO,
               ISNULL(I.VRPRECITEMCL, 0) AS PRECOCLIE,
               ISNULL(I.VRPRESUGITEM, 0) AS PRECOSUGER,
               I.IDPRECVARIA, P.NMPRODUTO, I.HRINIVENPROD, I.HRFIMVENPROD
          FROM ITEMPRECOGER I JOIN PRODUTO P
                             ON I.CDPRODUTO = P.CDPRODUTO
         WHERE I.CDTABEPREC = :CDTABEPREC
           AND I.DTINIVGPREC = :DTINIVGPREC
           AND I.CDPRODUTO = :CDPRODUTO
    ";

    const ITEM_PRECO_DIA_GERAL = "
        SELECT VRPRECODIA, IDPERVALORPR, IDDESCACREPR, IDVISUACUPOM, CDTIPOCONSPD
          FROM ITEMPRECODIAGER
          WHERE CDTABEPREC   = :CDTABEPREC
            AND DTINIVGPREC  = :DTINIVGPREC
            AND CDPRODUTO    = :CDPRODUTO
            AND CDPRPAITABPR = :CDPRPAITABPR
            AND NRDIASEMANPR = :NRDIASEMANPR
            AND CDTIPOCONSPD = :CDTIPOCONSPD
            AND :HORA BETWEEN HRINIPRECDIA AND HRFINPRECDIA
            AND (CONVERT(VARCHAR, GETDATE(), 103) BETWEEN DTINIVALPREC AND DTFINVALPREC)
    ";

    const VERIFICA_POS_PROD_COMBINADO = "
        SELECT PAGPRODCOM, POSPRODCOM
          FROM PARAVEND
         WHERE CDFILIAL = :CDFILIAL
           AND NRORG = :NRORG
    ";

    const CAMPANHA_COMPRE_GANHE = "
        SELECT C.CDCAMPCOMPGANHE, C.QTCOMPGANHE, V.DTINIVGCAMPCG
          FROM CAMPCOMPGANHE C
          JOIN FICAMPCOMPGANHE F
            ON F.CDCAMPCOMPGANHE = C.CDCAMPCOMPGANHE
           AND F.CDFILIAL = :CDFILIAL
          JOIN VGCAMPCOMPGANHE V
            ON V.CDCAMPCOMPGANHE = C.CDCAMPCOMPGANHE
           AND CONVERT(VARCHAR, GETDATE(), 103) BETWEEN V.DTINIVGCAMPCG AND V.DTFINVGCAMPCG
    ";

    const PRODUTOS_COMPRE_GANHE = "
        SELECT CDCAMPCOMPGANHE, CDPRODUTO
          FROM PRCAMPCOMPGANHE
         WHERE CDCAMPCOMPGANHE = :CDCAMPCOMPGANHE
           AND DTINIVGCAMPCG = :DTINIVGCAMPCG
    ";

 }
