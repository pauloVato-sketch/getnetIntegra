<?php

namespace Util;

class MSDEQuery {

    public function getArray() {
        return self::QUERY_MAPPING;
    }

    const QUERY_MAPPING = array(
        'SQL_BUSCA_OPERADOR'                       => self::SQL_BUSCA_OPERADOR,
        'SQL_CLIENTE_PADRAO'                       => self::SQL_CLIENTE_PADRAO,
        'SQL_UTILIZA_COMANDA_FOS'                  => self::SQL_UTILIZA_COMANDA_FOS,
        'SQL_BUSCA_MESA'                           => self::SQL_BUSCA_MESA,
        'SQL_COMANDA_FOS'                          => self::SQL_COMANDA_FOS,
        'SQL_ABRE_COMANDA'                         => self::SQL_ABRE_COMANDA,
        'SQL_MAX_COMANDA_FOS'                      => self::SQL_MAX_COMANDA_FOS,
        'INSERT_NOVO_CODIGO'                       => self::INSERT_NOVO_CODIGO,
        'UPDATE_NOVO_CODIGO'                       => self::UPDATE_NOVO_CODIGO,
        'SQL_VERIFICA_NUMEROS'                     => self::SQL_VERIFICA_NUMEROS,
        'SQL_VALIDA_CLIENTE'                       => self::SQL_VALIDA_CLIENTE,
        'SQL_INSERE_VENDA_REST'                    => self::SQL_INSERE_VENDA_REST,
        'SQL_CHECK_VENDAREST'                      => self::SQL_CHECK_VENDAREST,
        'SQL_VAL_VENDA_BALCAO'                     => self::SQL_VAL_VENDA_BALCAO,
        'SQL_INSERE_VENDAREST'                     => self::SQL_INSERE_VENDAREST,
        'SQL_GETCLIENTEPADRAO'                     => self::SQL_GETCLIENTEPADRAO,
        'SQL_LOG_ABERTURA'                         => self::SQL_LOG_ABERTURA,
        'SQL_GET_CONSUMER'                         => self::SQL_GET_CONSUMER,
        'SQL_UPDATE_STATUS_MESA'                   => self::SQL_UPDATE_STATUS_MESA,
        'GET_JUNCAOMESA'                           => self::GET_JUNCAOMESA,
        'GET_NRMESAJUNCAO'                         => self::GET_NRMESAJUNCAO,
        'GET_NRVENDAREST_NRCOMANDA'                => self::GET_NRVENDAREST_NRCOMANDA,
        'UPDATE_PRICE_ON_ITCOMANDAVEN'             => self::UPDATE_PRICE_ON_ITCOMANDAVEN,
        'UPDATE_VENDAREST'                         => self::UPDATE_VENDAREST,
        'SQL_GET_UNGROUPED_TABLES'                 => self::SQL_GET_UNGROUPED_TABLES,
        'SQL_GET_NRJUNMESA'                        => self::SQL_GET_NRJUNMESA,
        'SQL_INS_JUNCAOMESA'                       => self::SQL_INS_JUNCAOMESA,
        'SQL_INS_MESAJUNCAO'                       => self::SQL_INS_MESAJUNCAO,
        'SQL_BUSCA_NRCOMANDA'                      => self::SQL_BUSCA_NRCOMANDA,
        'SQL_FIX_NOVOCODIGO'                       => self::SQL_FIX_NOVOCODIGO,
        'SQL_PERMITE_ACESSO'                       => self::SQL_PERMITE_ACESSO,
        'SQL_VALIDA_MESA_BYNRACESSOUSER'           => self::SQL_VALIDA_MESA_BYNRACESSOUSER,
        'SQL_CHECK_ACCESS'                         => self::SQL_CHECK_ACCESS,
        'SQL_ALTERA_QTD_PESSOAS'                   => self::SQL_ALTERA_QTD_PESSOAS,
        'SQL_DIFFERENT_POSITIONS'                  => self::SQL_DIFFERENT_POSITIONS,
        'SQL_BUSCA_MENSAGEM_IMPRESSA'              => self::SQL_BUSCA_MENSAGEM_IMPRESSA,
        'SQL_DESCONTO_POR_FILIAL'                  => self::SQL_DESCONTO_POR_FILIAL,
        'SQL_DESCONTO_GRUPO_FILIAL'                => self::SQL_DESCONTO_GRUPO_FILIAL,
        'SQL_DESCONTO_POR_PRODUTO'                 => self::SQL_DESCONTO_POR_PRODUTO,
        'SQL_VALIDA_DESCONTO_PRODUTO'              => self::SQL_VALIDA_DESCONTO_PRODUTO,
        'SQL_INSERE_CHAMADA'                       => self::SQL_INSERE_CHAMADA,
        'SQL_MAX_CHAMADA'                          => self::SQL_MAX_CHAMADA,
        'SQL_GET_CALL'                             => self::SQL_GET_CALL,
        'SQL_ATENDE_CHAMADA'                       => self::SQL_ATENDE_CHAMADA,
        'SQL_ITENS_COMANDAS'                       => self::SQL_ITENS_COMANDAS,
        'SQL_VERIFICA_JUNCAO'                      => self::SQL_VERIFICA_JUNCAO,
        'SQL_MUDA_STATUS'                          => self::SQL_MUDA_STATUS,
        'SQL_DELETA_ITCOMANDAVEN'                  => self::SQL_DELETA_ITCOMANDAVEN,
        'SQL_DELETA_COMANDA_VEN'                   => self::SQL_DELETA_COMANDA_VEN,
        'SQL_DELETA_VENDA_REST'                    => self::SQL_DELETA_VENDA_REST,
        'SQL_DELETA_POS_VENDA_REST'                => self::SQL_DELETA_POS_VENDA_REST,
        'SQL_DEL_MESA_JUN'                         => self::SQL_DEL_MESA_JUN,
        'SQL_DEL_JUN_MESA'                         => self::SQL_DEL_JUN_MESA,
        'SQL_AMBIENTE'                             => self::SQL_AMBIENTE,
        'SQL_GET_PROD_PES'                         => self::SQL_GET_PROD_PES,
        'SQL_ITENS_COMBINADOS'                     => self::SQL_ITENS_COMBINADOS,
        'SQL_ITEM'                                 => self::SQL_ITEM,
        'SQL_ALTERA_QUANTIDADE'                    => self::SQL_ALTERA_QUANTIDADE,
        'SQL_CANCELA_ITEM'                         => self::SQL_CANCELA_ITEM,
        'SQL_ACR_COM_VEN'                          => self::SQL_ACR_COM_VEN,
        'SQL_GET_GROUPED_TABLES'                   => self::SQL_GET_GROUPED_TABLES,
        'SQL_SELECIONA_ITEM'                       => self::SQL_SELECIONA_ITEM,
        'SQL_ITENS_COMANDA'                        => self::SQL_ITENS_COMANDA,
        'SQL_INSERE_ITEM_POSICAO'                  => self::SQL_INSERE_ITEM_POSICAO,
        'SQL_SELECIONA_VALORES'                    => self::SQL_SELECIONA_VALORES,
        'SQL_AJUSTA_VALORES'                       => self::SQL_AJUSTA_VALORES,
        'SQL_DELETA_PRODUTOS'                      => self::SQL_DELETA_PRODUTOS,
        'SQL_VALIDA_VALORES'                       => self::SQL_VALIDA_VALORES,
        'SQL_VALIDA_VALORES_ITEM'                  => self::SQL_VALIDA_VALORES_ITEM,
        'SQL_VALIDA_VALORES_ORIG'                  => self::SQL_VALIDA_VALORES_ORIG,
        'SQL_NUM_JUNC_MESA'                        => self::SQL_NUM_JUNC_MESA,
        'SQL_NUM_VEND_REST'                        => self::SQL_NUM_VEND_REST,
        'SQL_DEL_VEND_REST'                        => self::SQL_DEL_VEND_REST,
        'SQL_DEL_COMANDA_VEN'                      => self::SQL_DEL_COMANDA_VEN,
        'SQL_DEL_ITCOMANDA_VEN'                    => self::SQL_DEL_ITCOMANDA_VEN,
        'SQL_NUM_MESAS_AGRUPADAS'                  => self::SQL_NUM_MESAS_AGRUPADAS,
        'SQL_GET_DELAYED_PRODUCTS'                 => self::SQL_GET_DELAYED_PRODUCTS,
        'SQL_GET_SMARTPROMO'                       => self::SQL_GET_SMARTPROMO,
        'SQL_GET_CONSUMER_BALANCE'                 => self::SQL_GET_CONSUMER_BALANCE,
        'SQL_GET_IDVERSALDCON'                     => self::SQL_GET_IDVERSALDCON,
        'SQL_CHECK_TABLE_EXISTS'                   => self::SQL_CHECK_TABLE_EXISTS,
        'SQL_GET_CONSUMER_PARAMS'                  => self::SQL_GET_CONSUMER_PARAMS,
        'SQL_BUSCA_CLIENTES'                       => self::SQL_BUSCA_CLIENTES,
        'SQL_BUSCA_CONSUMIDORES'                   => self::SQL_BUSCA_CONSUMIDORES,
        'SQL_BUSCA_FAMILIAS'                       => self::SQL_BUSCA_FAMILIAS,
        'SQL_BUSCA_VENDEDORES'                     => self::SQL_BUSCA_VENDEDORES,
        'SQL_MENS_PRODUCAO'                        => self::SQL_MENS_PRODUCAO,
        'SQL_OBSERVACAO'                           => self::SQL_OBSERVACAO,
        'SQL_OBSERVACAO_CAN'                       => self::SQL_OBSERVACAO_CAN,
        'SQL_BUSCA_IMPRESSORAS'                    => self::SQL_BUSCA_IMPRESSORAS,
        'SQL_GET_ALL_CONSUMERS_BY_CLIENT'          => self::SQL_GET_ALL_CONSUMERS_BY_CLIENT,
        'SQL_GET_CONSUMERS_BY_CLIENT'              => self::SQL_GET_CONSUMERS_BY_CLIENT,
        'SQL_VERIFICA_IP_BLOQUEADO'                => self::SQL_VERIFICA_IP_BLOQUEADO,
        'SQL_BUSCA_IPS_BLOQUEADOS'                 => self::SQL_BUSCA_IPS_BLOQUEADOS,
        'SQL_LISTA_COMANDA'                        => self::SQL_LISTA_COMANDA,
        'SQL_CONSULTA_MESA'                        => self::SQL_CONSULTA_MESA,
        'SQL_BUSCA_AGRUPADAS'                      => self::SQL_BUSCA_AGRUPADAS,
        'SQL_ULTIMA_VENDA'                         => self::SQL_ULTIMA_VENDA,
        'SQL_DATA_ABERTURA'                        => self::SQL_DATA_ABERTURA,
        'SQL_BUSCA_DADOS_MESA'                     => self::SQL_BUSCA_DADOS_MESA,
        'GET_TABLES_WITH_DELAYED_ITEMS'            => self::GET_TABLES_WITH_DELAYED_ITEMS,
        'SQL_GET_VENDAREST'                        => self::SQL_GET_VENDAREST,
        'SQL_ATUALIZA_CONSUMACAO_MINIMA'           => self::SQL_ATUALIZA_CONSUMACAO_MINIMA,
        'SQL_RETIRA_ITEM'                          => self::SQL_RETIRA_ITEM,
        'SQL_BUSCA_VALORCOMANDA'                   => self::SQL_BUSCA_VALORCOMANDA,
        'SQL_GET_CLIENT_DATA'                      => self::SQL_GET_CLIENT_DATA,
        'SQL_GET_CONSUMER_BY_EMAIL'                => self::SQL_GET_CONSUMER_BY_EMAIL,
        'SQL_GET_CONSUMER_DETAILS'                 => self::SQL_GET_CONSUMER_DETAILS,
        'SQL_INSERT_CONSUMER'                      => self::SQL_INSERT_CONSUMER,
        'SQL_ALTERA_STATUS_ACESSOFM'               => self::SQL_ALTERA_STATUS_ACESSOFM,
        'SQL_IDACESSOUSER'                         => self::SQL_IDACESSOUSER,
        'BUSCA_MESAS_AGRUPADAS'                    => self::BUSCA_MESAS_AGRUPADAS,
        'BUSCA_NRMESA'                             => self::BUSCA_NRMESA,
        'VERIFICA_COUVERT'                         => self::VERIFICA_COUVERT,
        'SQL_INSERE_COUVERT'                       => self::SQL_INSERE_COUVERT,
        'SQL_UPDATE_COUVERT'                       => self::SQL_UPDATE_COUVERT,
        'SQL_DADOS_MESA_POS'                       => self::SQL_DADOS_MESA_POS,
        'SQL_VR_ACRESCIMO'                         => self::SQL_VR_ACRESCIMO,
        'SQL_ITENS_DETALHADOS'                     => self::SQL_ITENS_DETALHADOS,
        'SQL_ITENS_ITCOMANDAEST'                   => self::SQL_ITENS_ITCOMANDAEST,
        'DELETE_ORIG'                              => self::DELETE_ORIG,
        'DELETE_ITCOMANDAVEN'                      => self::DELETE_ITCOMANDAVEN,
        'SQL_INSERE_ITEM_COMANDA_ORIG'             => self::SQL_INSERE_ITEM_COMANDA_ORIG,
        'SQL_DELETA_PRODUTO_ORIGINAL'              => self::SQL_DELETA_PRODUTO_ORIGINAL,
        'SQL_INSERE_ITEM_COMANDA'                  => self::SQL_INSERE_ITEM_COMANDA,
        'SQL_DELETA_CMD_VEN_ORIG'                  => self::SQL_DELETA_CMD_VEN_ORIG,
        'SQL_GET_PRODUCT_NAME'                     => self::SQL_GET_PRODUCT_NAME,
        'SQL_VAL_IMPR_LOJA'                        => self::SQL_VAL_IMPR_LOJA,
        'SQL_UPDATA_MENS'                          => self::SQL_UPDATA_MENS,
        'SQL_GET_TXPRODCOMVEN'                     => self::SQL_GET_TXPRODCOMVEN,
        'SQL_ALTERA_STATUS_COMANDA'                => self::SQL_ALTERA_STATUS_COMANDA,
        'SQL_ALTERA_STATUS_MESA'                   => self::SQL_ALTERA_STATUS_MESA,
        'SQL_PRODUTOS_LOJA'                        => self::SQL_PRODUTOS_LOJA,
        'SQL_UPDATE_MOVCAIXAMOB'                   => self::SQL_UPDATE_MOVCAIXAMOB,
        'SQL_BUSCA_TRANSACOES'                     => self::SQL_BUSCA_TRANSACOES,
        'SQL_UPDATE_DSBANDEIRA'                    => self::SQL_UPDATE_DSBANDEIRA,
        'SQL_GET_TIPORECE'                         => self::SQL_GET_TIPORECE,
        'SQL_UPDATE_MOVCAIXAMOB_CDTIPORECE'        => self::SQL_UPDATE_MOVCAIXAMOB_CDTIPORECE,
        'SQL_INSERT_MOVCAIXAMOB'                   => self::SQL_INSERT_MOVCAIXAMOB,
        'SQL_PROD_LOJA'                            => self::SQL_PROD_LOJA,
        'SQL_INSERE_ITEM_COMANDA_VEN'              => self::SQL_INSERE_ITEM_COMANDA_VEN,
        'SQL_GET_NRPEDIDOFOS'                      => self::SQL_GET_NRPEDIDOFOS,
        'SQL_STATUS_MESA'                          => self::SQL_STATUS_MESA,
        'SQL_PESQUISA_ITENS_COMANDA'               => self::SQL_PESQUISA_ITENS_COMANDA,
        'SQL_DELETA_ITENS_COMANDA_ORIGEM'          => self::SQL_DELETA_ITENS_COMANDA_ORIGEM,
        'SQL_DELETA_COMANDA_ORIGEM'                => self::SQL_DELETA_COMANDA_ORIGEM,
        'SQL_DELETA_VENDA_ORIGEM'                  => self::SQL_DELETA_VENDA_ORIGEM,
        'SQL_UPDATE_MESAS'                         => self::SQL_UPDATE_MESAS,
        'SQL_DELETA_MESA_JUNCAO'                   => self::SQL_DELETA_MESA_JUNCAO,
        'SQL_DELETA_JUNCAO_MESA'                   => self::SQL_DELETA_JUNCAO_MESA,
        'SQL_UPDATE_ITPEDIDOFOSREL'                => self::SQL_UPDATE_ITPEDIDOFOSREL,
        'SQL_UPDATE_PEDIDOFOS'                     => self::SQL_UPDATE_PEDIDOFOS,
        'SQL_GET_MAIN_PRODUCT'                     => self::SQL_GET_MAIN_PRODUCT,
        'SQL_GET_PRODUCT_COMPOSITION'              => self::SQL_GET_PRODUCT_COMPOSITION,
        'SQL_ACESSOS'                              => self::SQL_ACESSOS,
        'SQL_ACESSOS_AUT'                          => self::SQL_ACESSOS_AUT,
        'SQL_BUSCA_VENDEDOR_OPERADOR'              => self::SQL_BUSCA_VENDEDOR_OPERADOR,
        'SQL_DADOS_CAIXA'                          => self::SQL_DADOS_CAIXA,
        'SQL_BD_VERSION'                           => self::SQL_BD_VERSION,
        'SQL_FILIAL_DETAILS'                       => self::SQL_FILIAL_DETAILS,
        'SECRET_QUERY'                             => self::SECRET_QUERY,
        'GET_ESITEF_DETAILS'                       => self::GET_ESITEF_DETAILS,
        'SQL_VALIDA_COMANDA'                       => self::SQL_VALIDA_COMANDA,
        'UPDATE_COMISSAO_VENDA'                    => self::UPDATE_COMISSAO_VENDA,
        'SQL_TEMPO_PERMANENCIA'                    => self::SQL_TEMPO_PERMANENCIA,
        'SQL_PRODUTOS_PARCIAL'                     => self::SQL_PRODUTOS_PARCIAL,
        'SQL_PRODUTOS_PARCIAL_NUMERO_PRODUTOS'     => self::SQL_PRODUTOS_PARCIAL_NUMERO_PRODUTOS,
        'SQL_POSICAO_PARCIAL'                      => self::SQL_POSICAO_PARCIAL,
        'SQL_VAL_IMPRE_LOJA'                       => self::SQL_VAL_IMPRE_LOJA,
        'SQL_GET_MONEY_CURRENCY'                   => self::SQL_GET_MONEY_CURRENCY,
        'SQL_GET_OBS_TYPE'                         => self::SQL_GET_OBS_TYPE,
        'SQL_BUSCA_VENDEDOR'                       => self::SQL_BUSCA_VENDEDOR,
        'SQL_VALIDA_PROD'                          => self::SQL_VALIDA_PROD,
        'SQL_GET_PRODUTO'                          => self::SQL_GET_PRODUTO,
        'SQL_INS_ITCOMANDAVEN'                     => self::SQL_INS_ITCOMANDAVEN,
        'SQL_INS_ITCOMANDAEST'                     => self::SQL_INS_ITCOMANDAEST,
        'SQL_ACR_COMANDA_VEN'                      => self::SQL_ACR_COMANDA_VEN,
        'SQL_GET_ALIQUOTA'                         => self::SQL_GET_ALIQUOTA,
        'SQL_CHECK_ORDERCODE'                      => self::SQL_CHECK_ORDERCODE,
        'SQL_INSERE_WAITER_ORDERS'                 => self::SQL_INSERE_WAITER_ORDERS,
        'SQL_BUSCA_PRODBLOQ'                       => self::SQL_BUSCA_PRODBLOQ,
        'SQL_GET_CDGRPOCORPED'                     => self::SQL_GET_CDGRPOCORPED,
        'SQL_INS_OBSITCOMANDAVEN'                  => self::SQL_INS_OBSITCOMANDAVEN,
        'SQL_INS_OBSITCOMANDAEST'                  => self::SQL_INS_OBSITCOMANDAEST,
        'SQL_CHECK_REFIL'                          => self::SQL_CHECK_REFIL,
        'SQL_UPDATE_REFIL_QTTY'                    => self::SQL_UPDATE_REFIL_QTTY,
        'SQL_GET_CODE'                             => self::SQL_GET_CODE,
        'SQL_CHECK_CODE'                           => self::SQL_CHECK_CODE,
        'SQL_INSERT_CODE'                          => self::SQL_INSERT_CODE,
        'SQL_CHECK_FOR_REFIL'                      => self::SQL_CHECK_FOR_REFIL,
        'SQL_ITENS_DETALHADOS_SEM_COMBO'           => self::SQL_ITENS_DETALHADOS_SEM_COMBO,
        'SQL_ITENS_ORIGINAIS'                      => self::SQL_ITENS_ORIGINAIS,
        'GET_REGISTER_OPENING_DATE'                => self::GET_REGISTER_OPENING_DATE,
        'GET_REGISTER_CLOSING_PAYMENTS'            => self::GET_REGISTER_CLOSING_PAYMENTS,
        'SQL_SET_STATUS_MESA'                      => self::SQL_SET_STATUS_MESA,
        'SQL_BUSCA_DADOS'                          => self::SQL_BUSCA_DADOS,
        'SQL_ACESSO_AUT'                           => self::SQL_ACESSO_AUT,
        'SQL_INSERE_ACESSO'                        => self::SQL_INSERE_ACESSO,
        'SQL_NRACESSOUSER'                         => self::SQL_NRACESSOUSER,
        'SQL_BUSCA_NOME_MESA'                      => self::SQL_BUSCA_NOME_MESA,
        'SQL_UPDATE_ACESSO'                        => self::SQL_UPDATE_ACESSO,
        'CLIENTE_FILIAL'                           => self::CLIENTE_FILIAL,
        'TABELA_VENDA'                             => self::TABELA_VENDA,
        'TIPO_CONSUMIDOR'                          => self::TIPO_CONSUMIDOR,
        'EXISTE_PRECO_CLIE'                        => self::EXISTE_PRECO_CLIE,
        'TABELA_PRECO_LOJA'                        => self::TABELA_PRECO_LOJA,
        'PRECO_PARAVEND'                           => self::PRECO_PARAVEND,
        'GET_TIPCOBRA'                             => self::GET_TIPCOBRA,
        'SQL_NRJUNCAOMESA'                         => self::SQL_NRJUNCAOMESA,
        'SQL_DELETA_MESAJUNCAO'                    => self::SQL_DELETA_MESAJUNCAO,
        'SQL_DELETA_JUNCAOMESA'                    => self::SQL_DELETA_JUNCAOMESA,
        'SQL_SEPARA'                               => self::SQL_SEPARA,
        'SQL_AGRUPADA'                             => self::SQL_AGRUPADA,
        'SQL_SET_TABLE'                            => self::SQL_SET_TABLE,
        'GET_CLIENTE_ALL_POSITION'                 => self::GET_CLIENTE_ALL_POSITION,
        'INSERT_POSVENDAREST'                      => self::INSERT_POSVENDAREST,
        'DELETE_POSVENDAREST'                      => self::DELETE_POSVENDAREST,
        'UPDATE_POSVENDAREST'                      => self::UPDATE_POSVENDAREST,
        'TRANSFER_POSVENDAREST'                    => self::TRANSFER_POSVENDAREST,
        'SQL_BUSCA_JSON_TRANSACAO'                 => self::SQL_BUSCA_JSON_TRANSACAO,
        'SQL_UPDATE_EMAIL_CLIENTE'                 => self::SQL_UPDATE_EMAIL_CLIENTE,
        'SQL_MOVER_TRANSACOES'                     => self::SQL_MOVER_TRANSACOES,
        'SQL_UPDATE_CANCEL_TRANSACTION'            => self::SQL_UPDATE_CANCEL_TRANSACTION,
        'SQL_BUSCA_TRANSACOES_TEMPO'               => self::SQL_BUSCA_TRANSACOES_TEMPO,
        'SQL_BUSCA_TRANSACOES_ENTRADA_MOVCAIXA'    => self::SQL_BUSCA_TRANSACOES_ENTRADA_MOVCAIXA,
        'SQL_BUSCA_TRANSACOES_SAIDA_MOVCAIXA'      => self::SQL_BUSCA_TRANSACOES_SAIDA_MOVCAIXA,
        'SQL_BUSCA_TRANSACOES_MESA_MOVCAIXAMOB'    => self::SQL_BUSCA_TRANSACOES_MESA_MOVCAIXAMOB,
        'SQL_BUSCA_TRANSACOES_POSICAO_MOVCAIXAMOB' => self::SQL_BUSCA_TRANSACOES_POSICAO_MOVCAIXAMOB,
        'SQL_BUSCA_TRANSACOES_MOVCAIXADLV'         => self::SQL_BUSCA_TRANSACOES_MOVCAIXADLV,
        'SQL_BUSCA_PAGAMENTO_MESA'                 => self::SQL_BUSCA_PAGAMENTO_MESA,
        'SQL_BUSCA_LINHA_CANCELAMENTO'             => self::SQL_BUSCA_LINHA_CANCELAMENTO,
        'SQL_REBUILD_KDS'                          => self::SQL_REBUILD_KDS,
        'SQL_CHANGE_POSITIONS_QUANTITY'            => self::SQL_CHANGE_POSITIONS_QUANTITY,
        'SQL_BUSCA_COMANDAS'                       => self::SQL_BUSCA_COMANDAS,
        'SQL_VAL_TRANS'                            => self::SQL_VAL_TRANS,
        'SQL_VALIDA_MESA_ABERTA'                   => self::SQL_VALIDA_MESA_ABERTA,
        'INS_PEDIDOALT'                            => self::INS_PEDIDOALT,
        'SQL_GET_OBSERVATIONS_EST'                 => self::SQL_GET_OBSERVATIONS_EST,
        'SQL_DELETE_OBSITCOMANDAEST'               => self::SQL_DELETE_OBSITCOMANDAEST,
        'SQL_UPDATE_ITCOMANDAVENEST'               => self::SQL_UPDATE_ITCOMANDAVENEST,
        'SQL_DADOS_ITEM_COMANDA'                   => self::SQL_DADOS_ITEM_COMANDA,
        'SQL_UPD_POS_MESA'                         => self::SQL_UPD_POS_MESA,
        'SQL_GET_NRSEQCOM'                         => self::SQL_GET_NRSEQCOM,
        'SQL_BUSCA_QUANTIDADE'                     => self::SQL_BUSCA_QUANTIDADE,
        'SQL_DEL_PROD'                             => self::SQL_DEL_PROD,
        'SQL_VALIDA_POSICAO'                       => self::SQL_VALIDA_POSICAO,
        'SQL_GET_NRMESA'                           => self::SQL_GET_NRMESA,
        'SQL_UPD_POS_KDS'                          => self::SQL_UPD_POS_KDS,
        'GET_ITPEDIDOFOS_BY_ITPEDIDOFOSREL'        => self::GET_ITPEDIDOFOS_BY_ITPEDIDOFOSREL,
        'SQL_DADOS_USUARIO'                        => self::SQL_DADOS_USUARIO,
        'SQL_NRVENDAREST'                          => self::SQL_NRVENDAREST,
        'SQL_NRCOMANDA'                            => self::SQL_NRCOMANDA,
        'SQL_GET_DADOS_MESA'                       => self::SQL_GET_DADOS_MESA,
        'SQL_BUSCAVENDAREST'                       => self::SQL_BUSCAVENDAREST,
        'SQL_GET_NRJUNMESA_ABERTURA'               => self::SQL_GET_NRJUNMESA_ABERTURA,
        'SQL_DADOS_COMANDA'                        => self::SQL_DADOS_COMANDA,
        'SQL_DADOS_COMANDA_ABERTURA'               => self::SQL_DADOS_COMANDA_ABERTURA,
        'SQL_VALIDA_SUPERVISOR'                    => self::SQL_VALIDA_SUPERVISOR,
        'SQL_EXISTE_OPERADOR'                      => self::SQL_EXISTE_OPERADOR,
        'SQL_VALIDA_MESA_BYNRMESA'                 => self::SQL_VALIDA_MESA_BYNRMESA,
        'SQL_INSERE_LOG'                           => self::SQL_INSERE_LOG,
        'SQL_BUSCA_NOME_OPERADOR'                  => self::SQL_BUSCA_NOME_OPERADOR,
        'SQL_NOVO_CODIGO'                          => self::SQL_NOVO_CODIGO,
        'SQL_GET_OCORR_TEXTS'                      => self::SQL_GET_OCORR_TEXTS,
        'SQL_BUSCA_VALORCOMANDA_CONSUMACAO'        => self::SQL_BUSCA_VALORCOMANDA_CONSUMACAO,
        'INSERT_KDSOPERACAOTEMP'                   => self::INSERT_KDSOPERACAOTEMP,
        'SQL_GET_VENDA_BY_NRNOTAFISCALCE'          => self::SQL_GET_VENDA_BY_NRNOTAFISCALCE,
        'SQL_BUSCA_DADOS_IMPRESSORA'               => self::SQL_BUSCA_DADOS_IMPRESSORA,
        'SQL_GET_MOVCAIXA_BY_NRSEQVENDA'           => self::SQL_GET_MOVCAIXA_BY_NRSEQVENDA,
        'SQL_GET_IDUTLSENHAOPER'                   => self::SQL_GET_IDUTLSENHAOPER,
        'SQL_GET_COMISSAO_VENDA'                   => self::SQL_GET_COMISSAO_VENDA,
        'SQL_GET_NMCONSUMIDOR'                     => self::SQL_GET_NMCONSUMIDOR,
        'SQL_FILIAIS'                              => self::SQL_FILIAIS,
        'SQL_CAIXAS'                               => self::SQL_CAIXAS,
        'SQL_VENDEDORES'                           => self::SQL_VENDEDORES,
        'SQL_ITCOMANDAVEN_NRPRODCOMVEN'            => self::SQL_ITCOMANDAVEN_NRPRODCOMVEN,
        'SQL_GET_NRCPFRESPCON'                     => self::SQL_GET_NRCPFRESPCON,
        'SQL_GET_PRODUCT_DESC'                     => self::SQL_GET_PRODUCT_DESC,
        'SQL_UPDATE_WAITER_ORDERS'                 => self::SQL_UPDATE_WAITER_ORDERS,
        'SQL_GET_FILIAL_DETAILS'                   => self::SQL_GET_FILIAL_DETAILS,
        'SQL_UPDATE_ITCOMANDAVEN_ADIANTAMENTO'     => self::SQL_UPDATE_ITCOMANDAVEN_ADIANTAMENTO,
        'SQL_CONSUMER_CDEXCONSUMID'                => self::SQL_CONSUMER_CDEXCONSUMID,
        'SQL_CONSUMER_CDIDCONSUMID'                => self::SQL_CONSUMER_CDIDCONSUMID,
        'SQL_CONSUMER_NRCPFRESPCON'                => self::SQL_CONSUMER_NRCPFRESPCON,
        'SQL_EXECUTE_NOVO_CODIGO'                  => self::SQL_EXECUTE_NOVO_CODIGO,
        'GET_SERIE_NFCE'                           => self::GET_SERIE_NFCE,
        'GET_ULTIMA_VENDA_FISCAL'                  => self::GET_ULTIMA_VENDA_FISCAL,
        'GET_VENDA'                                => self::GET_VENDA,
        'GET_VRTOTTRIBIBPT_ITVENDAIMPOS'           => self::GET_VRTOTTRIBIBPT_ITVENDAIMPOS,
        'GET_PRODUTOS_DESBLOQUEADOS'               => self::GET_PRODUTOS_DESBLOQUEADOS,
        'GET_PRODUTOS_BLOQUEADOS'                  => self::GET_PRODUTOS_BLOQUEADOS,
        'INSERT_PRODUTOS_BLOQUEADOS'               => self::INSERT_PRODUTOS_BLOQUEADOS,
        'DELETE_PRODUTOS_BLOQUEADOS'               => self::DELETE_PRODUTOS_BLOQUEADOS,
        'BUSCA_NOMEPRODBLOQ'                       => self::BUSCA_NOMEPRODBLOQ,
        'SQL_BUSCA_CARTOES'                        => self::SQL_BUSCA_CARTOES,
        'BUSCA_DADOS_SSL'                          => self::BUSCA_DADOS_SSL,
        'SQL_ALTERA_HR_FECHAMENTO_MESA'            => self::SQL_ALTERA_HR_FECHAMENTO_MESA,
        'BUSCA_VENDA_REALIZADA'                    => self::BUSCA_VENDA_REALIZADA,
        'GET_NRCONTROLTEF'                         => self::GET_NRCONTROLTEF,
        'FILTRAR_PRODUTOS'                         => self::FILTRAR_PRODUTOS,
        'TRANSFER_POSITION'                        => self::TRANSFER_POSITION,
        'GET_POSITION'                             => self::GET_POSITION,
        'GET_POSITION_CLIENT'                      => self::GET_POSITION_CLIENT,
        'BUSCA_NOME_POR_POSICAO_NULL'              => self::BUSCA_NOME_POR_POSICAO_NULL,
        'GET_VENDAS'                               => self::GET_VENDAS,
        'VENDEDORES_OPERADORES_ATIVOS'             => self::VENDEDORES_OPERADORES_ATIVOS,
        'SQL_COMANDA_ITENS'                        => self::SQL_COMANDA_ITENS,
        'UPDATE_ITCOMANDAVEN_TRANSFER'             => self::UPDATE_ITCOMANDAVEN_TRANSFER,
        'GET_PAGAMENTOS_TEF'                       => self::GET_PAGAMENTOS_TEF,
        'GET_POSITION_CONTROL'                     => self::GET_POSITION_CONTROL,
        'GET_LOCKED_POSITIONS'                     => self::GET_LOCKED_POSITIONS,
        'INSERT_POSITION_CONTROL'                  => self::INSERT_POSITION_CONTROL,
        'DELETE_POSITION_CONTROL'                  => self::DELETE_POSITION_CONTROL,
        'RESET_POSITION_CONTROL'                   => self::RESET_POSITION_CONTROL,
        'GET_NRPOSICAOMESA'                        => self::GET_NRPOSICAOMESA,
        'UPDATE_COMANDAVEN_DESCFID'                => self::UPDATE_COMANDAVEN_DESCFID,
        'UPDATE_POSVENDAREST_DESCFID'              => self::UPDATE_POSVENDAREST_DESCFID,
        'SQL_POSVENDAREST_POSICAO'                 => self::SQL_POSVENDAREST_POSICAO,
        'UPDATE_PORCENTAGEM_COMISSAO'              => self::UPDATE_PORCENTAGEM_COMISSAO,
        'GET_DISCOUNT_FIDELITY'                    => self::GET_DISCOUNT_FIDELITY,
        'GET_DISCOUNT_OBSERVATIONS'                => self::GET_DISCOUNT_OBSERVATIONS,
        'GET_REGISTER_CLOSING_PAY_N'               => self::GET_REGISTER_CLOSING_PAY_N,
        'GET_TIPOSANGRIA'                          => self::GET_TIPOSANGRIA,
        'SQL_GRUPO_OBS_DESC'                       => self::SQL_GRUPO_OBS_DESC,
        'SQL_CAMPANHA'                             => self::SQL_CAMPANHA,
        'BUSCA_DADOS_CAMPANHA'                     => self::BUSCA_DADOS_CAMPANHA,
        'SQL_PROMOCAO_APLICADESCFIL'               => self::SQL_PROMOCAO_APLICADESCFIL,
        'SQL_PROMOCAO_APLICADESC'                  => self::SQL_PROMOCAO_APLICADESC,
        'UPDATE_DESCONTO_PROMOCAO'                 => self::UPDATE_DESCONTO_PROMOCAO,
        'BUSCA_DESCONTO_SUBGRUPO'                  => self::BUSCA_DESCONTO_SUBGRUPO,
        'SQL_ULTILIZA_COUVERT'                     => self::SQL_ULTILIZA_COUVERT,
        'GET_DELIVERY_ORDERS'                      => self::GET_DELIVERY_ORDERS,
        'GET_DELIVERY_ORDERS_CONTROL'              => self::GET_DELIVERY_ORDERS_CONTROL,
        'GET_PAYMENT_DLV'                          => self::GET_PAYMENT_DLV,
        'GET_ORDER_PARAMS_DLV'                     => self::GET_ORDER_PARAMS_DLV,
        'GET_INFO_DELIVERY_ORDER'                  => self::GET_INFO_DELIVERY_ORDER,
        'UPDATE_SAIDA_ENTREGADOR'                  => self::UPDATE_SAIDA_ENTREGADOR,
        'UPDATE_SAIDA_COMANDA'                     => self::UPDATE_SAIDA_COMANDA,
        'UPDATE_ENTREGADOR_VENDAREST'              => self::UPDATE_ENTREGADOR_VENDAREST,
        'GET_VENDEDORES'                           => self::GET_VENDEDORES,
        'UPDATE_IMP_VENDA'                         => self::UPDATE_IMP_VENDA,
        'GET_VENDEDORES_PEDIDOS_DLV'               => self::GET_VENDEDORES_PEDIDOS_DLV,
        'UPDATE_ENTREGADOR_CHEGADA'                => self::UPDATE_ENTREGADOR_CHEGADA,
        'UPDATE_CHEGADA_COMANDA'                   => self::UPDATE_CHEGADA_COMANDA,
        'GET_ORDERS_ENTREGADOR_DELIVERY'           => self::GET_ORDERS_ENTREGADOR_DELIVERY,
        'GET_ORIGEM_VENDA_DLV'                     => self::GET_ORIGEM_VENDA_DLV,
        'GET_TAXA_ENTREGA'                         => self::GET_TAXA_ENTREGA,
        'SQL_DELETA_ITCOMANDAVENDES'               => self::SQL_DELETA_ITCOMANDAVENDES,
        'SQL_DESISTE_ITEM'                         => self::SQL_DESISTE_ITEM,
        'GET_TIPORECEBE_DELIVERY'                  => self::GET_TIPORECEBE_DELIVERY,
        'GET_PRODUTOS_PEDIDODLV'                   => self::GET_PRODUTOS_PEDIDODLV,
        'INSERT_MOVCAIXADLV_PEDIDO'                => self::INSERT_MOVCAIXADLV_PEDIDO,
        'DELETE_MOVCAIXADLV'                       => self::DELETE_MOVCAIXADLV,
        'GET_CLIENTE_CONSUMIDOR_DLV'               => self::GET_CLIENTE_CONSUMIDOR_DLV,
        'UPDATE_COMANDA_PENDENTE'                  => self::UPDATE_COMANDA_PENDENTE,
        'GET_NRNOTAFISCALCE'                       => self::GET_NRNOTAFISCALCE,
        'CONCLUDE_ORDERDLV'                        => self::CONCLUDE_ORDERDLV,
        'SQL_ATUALIZA_GORJETA'                     => self::SQL_ATUALIZA_GORJETA,
        'LIMPA_FIDELIDADE'                         => self::LIMPA_FIDELIDADE,
        'SQL_PARAMETROS_CATRACA'                   => self::SQL_PARAMETROS_CATRACA,
        'SQL_PREPARE_FIX_NOVOCODIGO'               => self::SQL_PREPARE_FIX_NOVOCODIGO,
        'UPDATE_VRCOMISVENDE'                      => self::UPDATE_VRCOMISVENDE,
        'BUSCA_MESA_PRINCIPAL'                     => self::BUSCA_MESA_PRINCIPAL,
        'SQL_BUSCA_PAIS'                           => self::SQL_BUSCA_PAIS,
        'SQL_BUSCA_ESTADO'                         => self::SQL_BUSCA_ESTADO,
        'SQL_BUSCA_MUNICIPIO'                      => self::SQL_BUSCA_MUNICIPIO,
        'SQL_BUSCA_BAIRRO'                         => self::SQL_BUSCA_BAIRRO,
        'SQL_TIPO_CONS'                            => self::SQL_TIPO_CONS,
        'SQL_ADD_CONSUMER'                         => self::SQL_ADD_CONSUMER,
        'VERIFICA_POSICOES_PAGAS'                  => self::VERIFICA_POSICOES_PAGAS,
        'SQL_GET_IDSENHACUP'                       => self::SQL_GET_IDSENHACUP,
        'SQL_GET_CDSENHAPED'                       => self::SQL_GET_CDSENHAPED,
        'SQL_DELETE_MOVCAIXAMOB'                   => self::SQL_DELETE_MOVCAIXAMOB,
        'SQL_GET_PENDING_PAYMENTS'                 => self::SQL_GET_PENDING_PAYMENTS,
        'SQL_BUSCA_MOVCAIXAMOB'                    => self::SQL_BUSCA_MOVCAIXAMOB
    );

    const SQL_GET_OCORR_TEXTS = "
        SELECT DSOCORR
          FROM OCORRENCIA
         WHERE CDGRPOCOR = :CDGRPOCOR
           AND CDOCORR IN (:CDOCORR)
    ";

    const SQL_BUSCA_OPERADOR = "
        SELECT NMOPERADOR
          FROM OPERADOR
         WHERE CDOPERADOR = :CDOPERADOR
    ";

    const SQL_CLIENTE_PADRAO = "
        SELECT P.CDCLIENTE, C.NMFANTCLIE
          FROM PARAVEND P JOIN CLIENTE C
                            ON C.CDCLIENTE = P.CDCLIENTE
         WHERE P.CDFILIAL = :CDFILIAL";

    //:P_CDFILIAL
    //:P_CDLOJA
    const SQL_UTILIZA_COMANDA_FOS = "
        SELECT COUNT(*) AS NRREGISTROS
          FROM COMANDAFOS
         WHERE CDFILIAL = ?
           AND CDLOJA = ?
    ";

    const SQL_BUSCA_MESA = "
        SELECT NRMESA
          FROM VENDAREST
         WHERE NRVENDAREST = ?
    ";

    //:P_CDFILIAL
    //:P_CDLOJA
    //:P_DSCOMANDA
    const SQL_COMANDA_FOS = "
        SELECT CDFILIAL, IDSITCOMANDAFOS
          FROM COMANDAFOS
         WHERE CDFILIAL = :CDFILIAL
           AND CDLOJA = :CDLOJA
           AND DSCOMANDAFOS = :DSCOMANDAFOS
    ";

    //P_CDFILIAL
    //P_NRVENDAREST
    //P_NRCOMANDA
    //P_CDLOJA
    //P_DSCOMANDA
    //P_IDSTCOMANDA
    //P_SGSEXOCON
    //P_VRACRCOMANDA
    //P_IDORGCMDVENDA
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

    //:P_CDFILIAL
    //:P_CDLOJA
    //:P_DSCOMANDA
    //:P_CDFILIAL
    const SQL_MAX_COMANDA_FOS = "
        SELECT MIN(DSCOMANDAFOS) AS DSCOMANDAFOS
          FROM COMANDAFOS
         WHERE CDFILIAL = ?
           AND CDLOJA   = ?
           AND DSCOMANDAFOS > ?
           AND IDSITCOMANDAFOS = 'A'
           AND DSCOMANDAFOS NOT IN (SELECT DISTINCT DSCOMANDA
                                      FROM COMANDAVEN
                                     WHERE CDFILIAL = ?)
    ";

    //:P_CDCONTADOR
    //:P_NRSEQUENCIAL
    const INSERT_NOVO_CODIGO = "
        INSERT INTO NOVOCODIGO (CDCONTADOR, NRSEQUENCIAL)
        VALUES (?, ?)";

    //:P_NRSEQUENCIAL
    //:P_CDCONTADOR
    const UPDATE_NOVO_CODIGO = "
        UPDATE NOVOCODIGO
           SET NRSEQUENCIAL = ?
         WHERE CDCONTADOR = ?
    ";

    //:P_CDFILIAL
    //:P_CDLOJA
    const SQL_VERIFICA_NUMEROS = "
        SELECT MIN(DSCOMANDAFOS) AS DSCOMANDAFOS
          FROM COMANDAFOS
         WHERE CDFILIAL = ?
           AND CDLOJA = ?
           AND IDSITCOMANDAFOS = 'A'
    ";

    const SQL_VALIDA_CLIENTE = "
        SELECT CDCLIENTE
          FROM CLIENTE
         WHERE CDCLIENTE = ?
    ";

    const SQL_INSERE_VENDA_REST = "
        INSERT INTO VENDAREST
            (CDFILIAL, NRVENDAREST, CDLOJA, NRMESA,
            CDVENDEDOR, DTHRABERMESA, CDOPERADOR, NRPESMESAVEN,
            CDCLIENTE, CDCONSUMIDOR)
        VALUES
            (:CDFILIAL, :NRVENDAREST, :CDLOJA, :NRMESA,
            :CDVENDEDOR, GETDATE(), :CDOPERADOR, :NRPESMESAVEN,
            :CDCLIENTE, :CDCONSUMIDOR)
    ";

    const SQL_CHECK_VENDAREST = "
        SELECT *
          FROM VENDAREST
         WHERE CDFILIAL = ?
           AND CDLOJA = ?
           AND NRMESA = ?
    ";

    const SQL_VAL_VENDA_BALCAO = "
        SELECT NRMESAPADRAO
          FROM LOJA
         WHERE CDFILIAL = ?
           AND CDLOJA = ?
           AND NRMESAPADRAO  = ?
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

    const SQL_GETCLIENTEPADRAO = "
        SELECT CDCLIENTE FROM PARAVEND WHERE CDFILIAL = ?
    ";

    const SQL_LOG_ABERTURA = "
        SELECT NRVENDAREST
          FROM VENDAREST
         WHERE CDFILIAL = ?
           AND NRVENDAREST = ?
    ";

    const SQL_GET_CONSUMER = "
        SELECT NMCONSUMIDOR
          FROM CONSUMIDOR
         WHERE CDCLIENTE = ?
           AND CDCONSUMIDOR = ?
    ";

    const SQL_UPDATE_STATUS_MESA = "
        UPDATE MESA
           SET IDSTMESAAUX = 'D'
         WHERE CDFILIAL = ?
           AND CDLOJA = ?
           AND NRMESA = ?
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

    const GET_NRMESAJUNCAO = "
        SELECT M.NRMESA, V.NRVENDAREST, C.NRCOMANDA
          FROM MESAJUNCAO M
          JOIN VENDAREST V
            ON V.NRMESA = M.NRMESA
          JOIN COMANDAVEN C
            ON C.NRVENDAREST = V.NRVENDAREST
         WHERE M.CDFILIAL  = :CDFILIAL
           AND M.NRJUNMESA = :NRJUNMESA
    ";

    const GET_NRVENDAREST_NRCOMANDA = "
        SELECT C.CDFILIAL, C.NRVENDAREST, C.NRCOMANDA, V.NRMESA,
               V.NRORG, V.NRPESMESAVEN, V.CDVENDEDOR, C.DSCOMANDA, V.DTHRMESAFECH
          FROM VENDAREST V JOIN COMANDAVEN C
                             ON V.NRVENDAREST = C.NRVENDAREST
         WHERE V.CDFILIAL = :CDFILIAL
           AND V.NRMESA = :NRMESA
    ";

    const UPDATE_PRICE_ON_ITCOMANDAVEN = "
        UPDATE ITCOMANDAVEN
           SET VRPRECCOMVEN = :VRPRECCOMVEN, VRDESCCOMVEN = :VRDESCCOMVEN, VRACRCOMVEN = :VRACRCOMVEN,
               DSOBSDESCIT = :DSOBSDESCIT, CDGRPOCORDESCIT = :CDGRPOCORDESCIT, VRPRECCLCOMVEN = :VRPRECCLCOMVEN,
               IDDESCMANUAL = :IDDESCMANUAL
         WHERE CDFILIAL = :CDFILIAL
           AND NRORG = :NRORG
           AND NRVENDAREST  = :NRVENDAREST
           AND NRCOMANDA    = :NRCOMANDA
           AND NRPRODCOMVEN = :NRPRODCOMVEN
    ";

    const UPDATE_VENDAREST = "
        UPDATE VENDAREST
           SET CDCLIENTE   = :CDCLIENTE, CDCONSUMIDOR = :CDCONSUMIDOR
         WHERE CDFILIAL    = :CDFILIAL
           AND NRVENDAREST = :NRVENDAREST
    ";

    const SQL_GET_UNGROUPED_TABLES = "
        SELECT NRMESA
          FROM MESA
         WHERE CDFILIAL = :CDFILIAL
           AND CDLOJA   = :CDLOJA
           AND NRORG    = :NRORG
           AND (CHARINDEX(NRMESA, :PARAM) > 0)
           AND NRMESA NOT IN (SELECT NRMESA
                                FROM MESAJUNCAO
                               WHERE CDFILIAL = :CDFILIAL
                                 AND CDLOJA   = :CDLOJA
                                 AND NRORG    = :NRORG
                                 AND (CHARINDEX(NRMESA, :PARAM) > 0))
    ";

    const SQL_GET_NRJUNMESA = "
        SELECT NRJUNMESA
          FROM MESAJUNCAO
         WHERE CDFILIAL = :CDFILIAL
           AND CDLOJA = :CDLOJA
           AND (CHARINDEX(NRMESA, :PARAM) > 0)
    ";

    const SQL_INS_JUNCAOMESA = "
        INSERT
          INTO JUNCAOMESA
               (CDFILIAL, CDLOJA, NRJUNMESA)
        VALUES (?, ?, ?)
    ";

    const SQL_INS_MESAJUNCAO = "
        INSERT
          INTO MESAJUNCAO
               (CDFILIAL, CDLOJA, NRJUNMESA, NRMESA)
        VALUES (?, ?, ?, ?)
    ";

    const SQL_BUSCA_NRCOMANDA = "
        SELECT CV.NRCOMANDA, CV.NRVENDAREST, VR.NRMESA, VR.NRPESMESAVEN
          FROM COMANDAVEN CV JOIN VENDAREST VR
                               ON CV.CDFILIAL = VR.CDFILIAL
                              AND CV.NRVENDAREST = VR.NRVENDAREST
                              AND CV.NRORG = VR.NRORG
         WHERE CV.CDFILIAL = :CDFILIAL
           AND CV.NRORG = :NRORG
           AND CHARINDEX(VR.NRMESA, :PARAM) > 0
    ";

    const SQL_PREPARE_FIX_NOVOCODIGO = "
        SELECT NRSEQUENCIAL
          FROM NOVOCODIGO
         WHERE CDCONTADOR = :CDCONTADOR
           AND LEN(CDCONTADOR) > 16
           AND NRORG = :NRORG
    ";

    const SQL_FIX_NOVOCODIGO = "
        UPDATE NOVOCODIGO
           SET NRSEQUENCIAL = :NRSEQUENCIAL
         WHERE CDCONTADOR = :CDCONTADOR
           AND LEN(CDCONTADOR) > 16
           AND NRORG = :NRORG
    ";

    const SQL_PERMITE_ACESSO = "
        UPDATE ACESSOFM
           SET IDACESSOUSER = 'A',
               CDFILIAL = ?,
               CDLOJA = ?,
               CHAVEGARCOM = ?
         WHERE NRACESSOUSER = ?
    ";

    const SQL_VALIDA_MESA_BYNRACESSOUSER = "
        SELECT M.NRMESA, M.IDSTMESAAUX, A.CHAVEGARCOM, A.IDACESSOUSER
          FROM MESA M, ACESSOFM A
         WHERE M.NRMESA = A.NRMESA
           AND M.CDFILIAL = ?
           AND M.CDLOJA = ?
           AND A.NRACESSOUSER = ?
    ";

    const SQL_CHECK_ACCESS = "
        SELECT NRACESSOUSER
        FROM ACESSOFM
        WHERE NMUSUARIO = ?
    ";

    const SQL_ALTERA_QTD_PESSOAS = "
        UPDATE VENDAREST
           SET NRPESMESAVEN  = :NRPESMESAVEN,
               NRPOSICAOMESA = :NRPOSICAOMESA
         WHERE CDFILIAL      = :CDFILIAL
           AND NRVENDAREST   = :NRVENDAREST
    ";

    const SQL_DIFFERENT_POSITIONS = "
        SELECT MAX(NRLUGARMESA) AS DIFFPOS
          FROM ITCOMANDAVEN
         WHERE CDFILIAL = ?
           AND NRVENDAREST = ?
           AND NRCOMANDA = ?
    ";

    const SQL_BUSCA_MENSAGEM_IMPRESSA = "
        SELECT C.TXMOTIVCANCE
          FROM VENDAREST V, COMANDAVEN C
         WHERE V.CDFILIAL    = ?
           AND C.NRCOMANDA   = ?
           AND C.NRVENDAREST = ?
           AND V.CDLOJA      = ?
           AND V.CDFILIAL    = C.CDFILIAL
           AND V.NRVENDAREST = C.NRVENDAREST
           AND V.CDLOJA      = C.CDLOJA
           AND V.DTHRFECHMESA IS NULL
    ";

    const SQL_DESCONTO_POR_FILIAL = "
        SELECT IDPERVALDESFIL AS IDPERVALOR, VRDESPRPROMOFIL AS VRDESCONTO, IDAPLICADESCPRFI, IDDESCACRPROFIL AS IDDESCACRPROMO
          FROM DESGRUPROMPRFIL
          WHERE CDFILIAL       = ?
            AND CDPRODPROMOCAO = ?
            AND CDPRODUTO      = ?
    ";

    const SQL_DESCONTO_GRUPO_FILIAL = "
        SELECT *
          FROM GRUPROMOCPRFIL
          WHERE CDFILIAL       = ?
            AND CDPRODPROMOCAO = ?
    ";

    const SQL_DESCONTO_POR_PRODUTO = "
        SELECT DE.IDPERVALORDES AS IDPERVALOR, DE.VRDESPRODPROMOC AS VRDESCONTO, IDAPLICADESCPR, IDDESCACRPROMO
          FROM DESGRUPROMOCPR DE
         WHERE DE.CDPRODPROMOCAO = ?
           AND DE.CDPRODUTO      = ?
    ";

    const SQL_VALIDA_DESCONTO_PRODUTO = "
        SELECT IC.CDFILIAL, IC.NRVENDAREST, IC.NRCOMANDA, IC.CDPRODUTO
          FROM VENDAREST VE, COMANDAVEN CO, ITCOMANDAVEN IC
          WHERE VE.CDFILIAL    = ?
            AND VE.NRVENDAREST = ?
            AND VE.CDFILIAL    = CO.CDFILIAL
            AND VE.CDFILIAL    = IC.CDFILIAL
            AND VE.NRVENDAREST = CO.NRVENDAREST
            AND VE.NRVENDAREST = IC.NRVENDAREST
            AND IC.CDPRODUTO   = ?
    ";

    const SQL_INSERE_CHAMADA = "
        INSERT INTO CHAMADAWAITER
        (NRACESSOUSER, NRCHAMADA, IDTIPOCHAMADA)
        VALUES
        (?, ?, ?)
    ";

    const SQL_MAX_CHAMADA ="
        SELECT ISNULL(MAX(NRCHAMADA), '0000000001') AS NRCHAMADA
          FROM CHAMADAWAITER
         WHERE NRACESSOUSER = ?
    ";

    const SQL_GET_CALL ="
        SELECT NMUSUARIO, A.NRMESA, IDTIPOCHAMADA,
        DATEDIFF ( second , C.DTULTATU , GETDATE() ) AS TEMPO, C.NRACESSOUSER, M.NMMESA
          FROM CHAMADAWAITER AS C, ACESSOFM AS A, MESA AS M
         WHERE C.NRACESSOUSER = A.NRACESSOUSER
           AND A.NRMESA = M.NRMESA
           AND A.IDACESSOUSER = 'A'
           AND C.IDSTCHAMADA = 'P'
    ";

    const SQL_ATENDE_CHAMADA = "
        UPDATE CHAMADAWAITER
           SET IDSTCHAMADA = 'A'
         WHERE NRACESSOUSER = ?
    ";

    const SQL_ITENS_COMANDAS = "
        SELECT IT.NRPRODCOMVEN, IT.QTPRODCOMVEN, IT.VRPRECCOMVEN, CONVERT(VARCHAR,IT.TXPRODCOMVEN) AS TXPRODCOMVEN,
               IT.IDSTPRCOMVEN, ISNULL(IT.VRDESCCOMVEN,0) AS VRDESCCOMVEN, PR.CDARVPROD, PR.NMPRODUTO,
               IT.CDPRODUTO, IT.DTHRINCOMVEN, PR.IDCOBTXSERV, IT.NRCOMANDA, ISNULL(IT.VRPRECCLCOMVEN,0) AS VRPRECCLCOMVEN,
               IT.NRVENDAREST, ISNULL(IT.VRACRCOMVEN,0) AS VRACRCOMVEN, IT.NRSEQPRODCOM
          FROM ITCOMANDAVEN IT, PRODUTO PR
         WHERE IT.CDFILIAL = ?
           AND (CHARINDEX(IT.NRCOMANDA, ?) > 0)
           AND IT.CDPRODUTO = PR.CDPRODUTO
           AND IT.IDSTPRCOMVEN <> '7'
      ";

    const SQL_VERIFICA_JUNCAO = "
        SELECT NRJUNMESA
          FROM MESAJUNCAO
         WHERE CDFILIAL = :CDFILIAL
           AND CDLOJA   = :CDLOJA
           AND NRMESA   = :NRMESA
    ";

    const SQL_MUDA_STATUS = "
        UPDATE MESA
           SET IDSTMESAAUX = :IDSTMESAAUX
         WHERE (CDFILIAL = :CDFILIAL)
           AND (CDLOJA   = :CDLOJA)
           AND (NRMESA   = :NRMESA)
    ";

    const SQL_DELETA_ITCOMANDAVEN = "
        DELETE FROM ITCOMANDAVEN
              WHERE (CDFILIAL    = :CDFILIAL)
                AND (NRVENDAREST = :NRVENDAREST)
                AND (NRCOMANDA   = :NRCOMANDA)
    ";

    const SQL_DELETA_COMANDA_VEN = "
        DELETE FROM COMANDAVEN
              WHERE (CDFILIAL    = :CDFILIAL)
                AND (NRVENDAREST = :NRVENDAREST)
                AND (NRCOMANDA   = :NRCOMANDA)
    ";

    const SQL_DELETA_VENDA_REST = "
        DELETE FROM VENDAREST
              WHERE (CDFILIAL    = :CDFILIAL)
                AND (NRVENDAREST = :NRVENDAREST)
    ";

    const SQL_DELETA_POS_VENDA_REST = "
        DELETE FROM POSVENDAREST
              WHERE CDFILIAL    = :CDFILIAL
                AND NRVENDAREST = :NRVENDAREST
                AND NRORG       = :NRORG
    ";

    const SQL_DEL_MESA_JUN = "
        DELETE FROM MESAJUNCAO
              WHERE CDFILIAL  = :CDFILIAL
                AND CDLOJA    = :CDLOJA
                AND NRJUNMESA = :NRJUNMESA
    ";

    const SQL_DEL_JUN_MESA = "
        DELETE FROM JUNCAOMESA
              WHERE CDFILIAL  = :CDFILIAL
                AND CDLOJA    = :CDLOJA
                AND NRJUNMESA = :NRJUNMESA
    ";

    const SQL_AMBIENTE = "
        SELECT M.CDSALA, S.NMSALA
          FROM MESA M JOIN SALA S
                        ON (M.CDFILIAL = S.CDFILIAL)
                       AND (M.CDSALA   = S.CDSALA)
         WHERE (M.CDFILIAL = ?)
           AND (M.CDLOJA   = ?)
           AND (M.NRMESA   = ?)
    ";

    const SQL_GET_PROD_PES = "
        SELECT IDPESAPROD
          FROM PRODUTO
         WHERE CDPRODUTO = ?
    ";

    const SQL_ITENS_COMBINADOS = "
        SELECT PR.NMPRODUTO, PJ.IDIMPCANITEM, PJ.NRSEQIMPRPROD, PJ.NRSEQIMPRPROD2, IT.*,
               IP1.IDMODEIMPRES, IL1.NRSEQIMPRLOJA, IL1.CDPORTAIMPR, MP1.DSENDPORTA,
               IL1.DSIPIMPR, IL1.DSIPPONTE, IP2.IDMODEIMPRES AS IDMODEIMPRES2,
               IL2.NRSEQIMPRLOJA AS NRSEQIMPRLOJA2, IL2.CDPORTAIMPR AS CDPORTAIMPR2,
               MP2.DSENDPORTA AS DSENDPORTA2, IL2.DSIPIMPR AS DSIPIMPR2,
               IL2.DSIPPONTE AS DSIPPONTE2
          FROM PRODUTO PR,
               ITCOMANDAVEN IT LEFT JOIN PRODLOJA PJ
                                      ON IT.CDPRODUTO = PJ.CDPRODUTO
                                     AND IT.CDFILIAL  = PJ.CDFILIAL
                                     AND IT.CDLOJA    = PJ.CDLOJA

                               LEFT JOIN IMPRLOJA IL1
                                      ON PJ.CDFILIAL      = IL1.CDFILIAL
                                     AND PJ.CDLOJA        = IL1.CDLOJA
                                     AND PJ.NRSEQIMPRPROD = IL1.NRSEQIMPRLOJA

                               LEFT JOIN IMPRLOJA IL2
                                      ON PJ.CDFILIAL       = IL2.CDFILIAL
                                     AND PJ.CDLOJA         = IL2.CDLOJA
                                     AND PJ.NRSEQIMPRPROD2 = IL2.NRSEQIMPRLOJA

                               LEFT JOIN IMPRESSORA IP1
                                      ON IL1.CDIMPRESSORA = IP1.CDIMPRESSORA

                               LEFT JOIN IMPRESSORA IP2
                                      ON IL2.CDIMPRESSORA = IP2.CDIMPRESSORA

                               LEFT JOIN MAPIMPRLOJA MP1
                                      ON IL1.CDFILIAL    = MP1.CDFILIAL
                                     AND IL1.CDLOJA      = MP1.CDLOJA
                                     AND IL1.CDPORTAIMPR = MP1.CDPORTAIMPR

                              LEFT JOIN MAPIMPRLOJA MP2
                                      ON IL2.CDFILIAL    = MP2.CDFILIAL
                                     AND IL2.CDLOJA      = MP2.CDLOJA
                                     AND IL2.CDPORTAIMPR = MP2.CDPORTAIMPR
         WHERE IT.CDFILIAL       = ?
           AND IT.NRCOMANDA      = ?
           AND IT.NRVENDAREST    = ?
           AND IT.CDPRODPROMOCAO = ?
           AND IT.NRSEQPRODCOM   = ?
           AND IT.CDPRODUTO      = PR.CDPRODUTO
      ";

    const SQL_ITEM = "
        SELECT IT.CDPRODUTO, IT.QTPRODCOMVEN, IT.VRPRECCOMVEN, PR.CDPORIMPPROD,
               PR.IDIMPCANITEM, PT.NMPRODUTO, VE.NRMESA, IT.DSCOMANDAORI,
               IT.NRPRODCOMORI, IT.NRCOMANDAORI, PR.NRSEQIMPRPROD, IT.CDLOJA,
               IT.CDFILIAL, IP1.IDMODEIMPRES, IL1.NRSEQIMPRLOJA, IL1.CDPORTAIMPR,
               MP1.DSENDPORTA, IL1.DSIPIMPR, IL1.DSIPPONTE, PR.NRSEQIMPRPROD2,
               IP2.IDMODEIMPRES AS IDMODEIMPRES2, IL2.NRSEQIMPRLOJA AS NRSEQIMPRLOJA2,
               IL2.CDPORTAIMPR AS CDPORTAIMPR2, MP2.DSENDPORTA AS DSENDPORTA2,
               IL2.DSIPIMPR AS DSIPIMPR2, IL2.DSIPPONTE AS DSIPPONTE2
          FROM PRODUTO PT, VENDAREST VE,
               ITCOMANDAVEN IT LEFT JOIN PRODLOJA PR
                                      ON IT.CDPRODUTO = PR.CDPRODUTO
                                     AND IT.CDFILIAL  = PR.CDFILIAL
                                     AND IT.CDLOJA    = PR.CDLOJA

                               LEFT JOIN IMPRLOJA IL1
                                      ON PR.CDFILIAL      = IL1.CDFILIAL
                                     AND PR.CDLOJA        = IL1.CDLOJA
                                     AND PR.NRSEQIMPRPROD = IL1.NRSEQIMPRLOJA

                               LEFT JOIN IMPRLOJA IL2
                                      ON PR.CDFILIAL       = IL2.CDFILIAL
                                     AND PR.CDLOJA         = IL2.CDLOJA
                                     AND PR.NRSEQIMPRPROD2 = IL2.NRSEQIMPRLOJA

                               LEFT JOIN IMPRESSORA IP1
                                      ON IL1.CDIMPRESSORA = IP1.CDIMPRESSORA

                               LEFT JOIN IMPRESSORA IP2
                                      ON IL2.CDIMPRESSORA = IP2.CDIMPRESSORA

                               LEFT JOIN MAPIMPRLOJA MP1
                                      ON IL1.CDFILIAL    = MP1.CDFILIAL
                                     AND IL1.CDLOJA      = MP1.CDLOJA
                                     AND IL1.CDPORTAIMPR = MP1.CDPORTAIMPR

                              LEFT JOIN MAPIMPRLOJA MP2
                                      ON IL2.CDFILIAL    = MP2.CDFILIAL
                                     AND IL2.CDLOJA      = MP2.CDLOJA
                                     AND IL2.CDPORTAIMPR = MP2.CDPORTAIMPR
        WHERE IT.CDFILIAL      = ?
           AND IT.NRCOMANDA     = ?
           AND IT.NRVENDAREST   = ?
           AND IT.NRPRODCOMVEN  = ?
           AND IT.CDPRODUTO     = PT.CDPRODUTO
           AND IT.CDFILIAL      = VE.CDFILIAL
           AND IT.NRVENDAREST   = VE.NRVENDAREST
    ";

    const SQL_ALTERA_QUANTIDADE = "
        UPDATE ITCOMANDAVEN
           SET QTPRODCOMVEN  = ?
         WHERE (CDFILIAL     = ?)
           AND (NRVENDAREST  = ?)
           AND (NRCOMANDA    = ?)
           AND (NRPRODCOMVEN = ?)
    ";

    const SQL_CANCELA_ITEM = "
        UPDATE ITCOMANDAVEN
           SET TXPRODCOMVEN  = :TXPRODCOMVEN,
               CDGRPOCOR = :CDGRPOCOR,
               CDOCORR = :CDOCORR,
               IDSTPRCOMVEN = :IDSTPRCOMVEN,
               CDSUPERVISOR = :CDSUPERVISOR,
               IDPRODPRODUZ = :IDPRODPRODUZ,
               DTHRPRODCANVEN = :DTHRPRODCANVEN
         WHERE CDFILIAL     = :CDFILIAL
           AND NRCOMANDA    = :NRCOMANDA
           AND NRVENDAREST  = :NRVENDAREST
           AND NRPRODCOMVEN = :NRPRODCOMVEN
    ";

    const SQL_ACR_COM_VEN = "
        SELECT CDFILIAL, NRCOMANDA, VRACRCOMANDA
          FROM COMANDAVEN
         WHERE CDFILIAL    = ?
           AND NRCOMANDA   = ?
           AND NRVENDAREST = ?
    ";

    const SQL_GET_GROUPED_TABLES = "
        SELECT J.NRMESA, C.NRCOMANDA, V.NRVENDAREST, V.NRPESMESAVEN
          FROM MESAJUNCAO J, COMANDAVEN C, VENDAREST V
         WHERE NRJUNMESA IN (SELECT NRJUNMESA
                               FROM MESAJUNCAO
                              WHERE NRMESA = ?)
         AND J.NRMESA = V.NRMESA
         AND V.NRVENDAREST = C.NRVENDAREST
    ";

    const SQL_SELECIONA_ITEM = "
        SELECT
          ISNULL(IM.DSBUTTON, ISNULL(PR.NMPRODIMPFIS,PR.NMPRODUTO)) DSBUTTON, IT.CDFILIAL, IT.NRVENDAREST, IT.NRCOMANDA,
          MIN(IT.NRPRODCOMVEN) AS NRPRODCOMVEN, SUM(IT.QTPRODCOMVEN) AS QTPRODCOMVEN, IT.VRDESCCOMVEN AS VRDESCCOMVEN,
          IT.VRACRCOMVEN AS VRACRCOMVEN, IT.VRPRECCOMVEN AS VRPRECCOMVEN, PR.CDARVPROD, IT.CDPRODUTO, IT.IDSTPRCOMVEN,
          IT.NRMESADSCOMORIT, CONVERT(VARCHAR, IT.TXPRODCOMVEN) TXPRODCOMVEN, PR.IDCOBTXSERV,
          ISNULL(IT.NRLUGARMESA,'001') AS NRLUGARMESA, V.DTHRABERMESA, PR.CDBARPRODUTO AS CDPRODIMPFIS,
          IT. NRSEQPRODCOM, IT.NRSEQPRODCUP, IT.CDPRODPROMOCAO, GROUPS.DSBUTTON AS GRUPO,
          CONVERT(CHAR,IT.DTHRINCOMVEN,103) AS DATA, CONVERT(CHAR,IT.DTHRINCOMVEN,108) AS HORA, IT.NRPRODORIG, IT.IDDIVIDECONTA
        FROM
          PRODUTO PR, VENDAREST V,
          ITCOMANDAVEN IT LEFT JOIN ITMENUCONFTE IM
                 ON (IT.CDPRODUTO = IM.CDIDENTBUTON
                    AND IT.CDFILIAL = IM.CDFILIAL
                AND IM.NRCONFTELA = ?
                AND IM.IDTPBUTTON = '1')
                          LEFT JOIN (SELECT DSBUTTON, (NRPGCONFTELA + NRBUTTON) AS CDGRUPO
                                       FROM ITMENUCONFTE
                                      WHERE IDTPBUTTON = '2'
                                        AND CDFILIAL = ?
                                        AND NRCONFTELA = ?) AS GROUPS
                                 ON ((IM.NRPGCONFTAUX + IM.NRBUTTONAUX) = GROUPS.CDGRUPO)
        WHERE (IT.CDFILIAL = ?)
          AND (IT.NRCOMANDA = ?)
          AND (IT.NRVENDAREST = ?)
          AND (IT.NRPRODCOMVEN = ?)
          AND (IT.CDPRODUTO = PR.CDPRODUTO)
          AND (IT.IDSTPRCOMVEN <> '6' AND IT.IDSTPRCOMVEN <> '7')
          AND (IT.CDPRODUTO <> 'X')
          AND (IT.CDFILIAL    = V.CDFILIAL)
          AND (IT.NRVENDAREST = V.NRVENDAREST)
          AND (PR.IDPESAPROD = 'N')
          AND IT.CDPRODPROMOCAO IS NULL
        GROUP BY
          IM.DSBUTTON, IT.CDFILIAL, IT.NRVENDAREST, IT.NRCOMANDA, ISNULL(PR.NMPRODIMPFIS,PR.NMPRODUTO),
          IT.VRDESCCOMVEN, IT.VRACRCOMVEN, IT.VRPRECCOMVEN, PR.CDARVPROD, IT.CDPRODUTO, IT.IDSTPRCOMVEN,
          IT.NRMESADSCOMORIT, PR.IDCOBTXSERV, IT.NRLUGARMESA , V.DTHRABERMESA, CONVERT(VARCHAR, IT.TXPRODCOMVEN),
          PR.CDBARPRODUTO, IT.NRPRODCOMVEN, IT.QTPRODCOMVEN, IT.VRDESCCOMVEN, IT.VRACRCOMVEN, IT. NRSEQPRODCOM,
          IT.NRSEQPRODCUP, IT.CDPRODPROMOCAO, IM.NRBUTTON, GROUPS.DSBUTTON,CONVERT(CHAR,IT.DTHRINCOMVEN,103),
          CONVERT(CHAR,IT.DTHRINCOMVEN,108), IT.NRPRODORIG, IT.IDDIVIDECONTA

        UNION

        SELECT
          ISNULL(IM.DSBUTTON, ISNULL(PR.NMPRODIMPFIS,PR.NMPRODUTO)) DSBUTTON, IT.CDFILIAL, IT.NRVENDAREST,
          IT.NRCOMANDA, IT.NRPRODCOMVEN AS NRPRODCOMVEN, IT.QTPRODCOMVEN AS QTPRODCOMVEN, IT.VRDESCCOMVEN AS VRDESCCOMVEN,
          IT.VRACRCOMVEN AS VRACRCOMVEN, IT.VRPRECCOMVEN AS VRPRECCOMVEN, PR.CDARVPROD, IT.CDPRODUTO,
          IT.IDSTPRCOMVEN, IT.NRMESADSCOMORIT, CONVERT(VARCHAR, IT.TXPRODCOMVEN) TXPRODCOMVEN, PR.IDCOBTXSERV,
          ISNULL(IT.NRLUGARMESA,'001') AS NRLUGARMESA, V.DTHRABERMESA, PR.CDBARPRODUTO AS CDPRODIMPFIS,
          IT.NRSEQPRODCOM, IT.NRSEQPRODCUP, IT.CDPRODPROMOCAO, GROUPS.DSBUTTON AS GRUPO,
          CONVERT(CHAR,IT.DTHRINCOMVEN,103) AS DATA, CONVERT(CHAR,IT.DTHRINCOMVEN,108) AS HORA, IT.NRPRODORIG, IT.IDDIVIDECONTA
        FROM
          PRODUTO PR, VENDAREST V,
          ITCOMANDAVEN IT LEFT JOIN ITMENUCONFTE IM
                 ON (IT.CDPRODUTO = IM.CDIDENTBUTON
                    AND IT.CDFILIAL = IM.CDFILIAL
                AND IM.NRCONFTELA = ?
                AND IM.IDTPBUTTON = '1')
                          LEFT JOIN (SELECT DSBUTTON, (NRPGCONFTELA + NRBUTTON) AS CDGRUPO
                                       FROM ITMENUCONFTE
                                      WHERE IDTPBUTTON = '2'
                                        AND CDFILIAL = ?
                                        AND NRCONFTELA = ?) AS GROUPS
                                 ON ((IM.NRPGCONFTAUX + IM.NRBUTTONAUX) = GROUPS.CDGRUPO)
        WHERE (IT.CDFILIAL = ?)
          AND (IT.NRCOMANDA = ?)
          AND (IT.NRVENDAREST = ?)
          AND (IT.NRPRODCOMVEN = ?)
          AND (IT.CDPRODUTO = PR.CDPRODUTO)
          AND (IT.IDSTPRCOMVEN <> '6' AND IT.IDSTPRCOMVEN <> '7')
          AND (IT.CDPRODUTO <> 'X')
          AND (IT.CDFILIAL    = V.CDFILIAL)
          AND (IT.NRVENDAREST = V.NRVENDAREST)
          AND (PR.IDPESAPROD = 'S')
          AND ((IM.NRPGCONFTAUX + IM.NRBUTTONAUX) = GROUPS.CDGRUPO)
          AND IT.CDPRODPROMOCAO IS NULL
        GROUP BY
          IM.DSBUTTON, IT.CDFILIAL, IT.NRVENDAREST, IT.NRCOMANDA, ISNULL(PR.NMPRODIMPFIS,PR.NMPRODUTO),
          IT.VRDESCCOMVEN, IT.VRACRCOMVEN, IT.VRPRECCOMVEN, PR.CDARVPROD, IT.CDPRODUTO, IT.IDSTPRCOMVEN,
          IT.NRMESADSCOMORIT, PR.IDCOBTXSERV, IT.NRLUGARMESA , V.DTHRABERMESA, CONVERT(VARCHAR, IT.TXPRODCOMVEN),
          PR.CDBARPRODUTO, IT.NRPRODCOMVEN, IT.QTPRODCOMVEN, IT.VRDESCCOMVEN, IT.VRACRCOMVEN, IT. NRSEQPRODCOM,
          IT.NRSEQPRODCUP, IT.CDPRODPROMOCAO, IM.NRBUTTON, GROUPS.DSBUTTON, CONVERT(CHAR,IT.DTHRINCOMVEN,103),
          CONVERT(CHAR,IT.DTHRINCOMVEN,108), IT.NRPRODORIG, IT.IDDIVIDECONTA
    ";

    const SQL_ITENS_COMANDA = "
        SELECT
          ISNULL(IM.DSBUTTON, ISNULL(PR.NMPRODIMPFIS,PR.NMPRODUTO)) DSBUTTON, IT.CDFILIAL, IT.NRVENDAREST, IT.NRCOMANDA,
          MIN(IT.NRPRODCOMVEN) AS NRPRODCOMVEN, SUM(IT.QTPRODCOMVEN) AS QTPRODCOMVEN, IT.VRDESCCOMVEN AS VRDESCCOMVEN,
          IT.VRACRCOMVEN AS VRACRCOMVEN, IT.VRPRECCOMVEN AS VRPRECCOMVEN, PR.CDARVPROD, IT.CDPRODUTO, IT.IDSTPRCOMVEN,
          IT.NRMESADSCOMORIT, CONVERT(VARCHAR, IT.TXPRODCOMVEN) TXPRODCOMVEN, PR.IDCOBTXSERV,
          ISNULL(IT.NRLUGARMESA,'001') AS NRLUGARMESA, V.DTHRABERMESA, PR.CDBARPRODUTO AS CDPRODIMPFIS,
          IT. NRSEQPRODCOM, IT.NRSEQPRODCUP, IT.CDPRODPROMOCAO, GROUPS.DSBUTTON AS GRUPO,
          CONVERT(CHAR,IT.DTHRINCOMVEN,103) AS DATA, CONVERT(CHAR,IT.DTHRINCOMVEN,108) AS HORA
        FROM
          PRODUTO PR, VENDAREST V,
          ITCOMANDAVEN IT LEFT JOIN ITMENUCONFTE IM
                 ON (IT.CDPRODUTO = IM.CDIDENTBUTON
                    AND IT.CDFILIAL = IM.CDFILIAL
                AND IM.NRCONFTELA = ?
                AND IM.IDTPBUTTON = '1')
                          LEFT JOIN (SELECT DSBUTTON, (NRPGCONFTELA + NRBUTTON) AS CDGRUPO
                                       FROM ITMENUCONFTE
                                      WHERE IDTPBUTTON = '2'
                                        AND CDFILIAL = ?
                                        AND NRCONFTELA = ?) AS GROUPS
                                 ON ((IM.NRPGCONFTAUX + IM.NRBUTTONAUX) = GROUPS.CDGRUPO)
        WHERE (IT.CDFILIAL = ?)
          AND (IT.NRCOMANDA = ?)
          AND (IT.NRVENDAREST = ?)
          AND (IT.NRPRODCOMVEN = ?)
          AND (IT.CDPRODUTO = PR.CDPRODUTO)
          AND (IT.IDSTPRCOMVEN <> '6' AND IT.IDSTPRCOMVEN <> '7')
          AND (IT.CDPRODUTO <> 'X')
          AND (IT.CDFILIAL    = V.CDFILIAL)
          AND (IT.NRVENDAREST = V.NRVENDAREST)
          AND (PR.IDPESAPROD = 'N')
          AND IT.CDPRODPROMOCAO IS NULL
        GROUP BY
          IM.DSBUTTON, IT.CDFILIAL, IT.NRVENDAREST, IT.NRCOMANDA, ISNULL(PR.NMPRODIMPFIS,PR.NMPRODUTO),
          IT.VRDESCCOMVEN, IT.VRACRCOMVEN, IT.VRPRECCOMVEN, PR.CDARVPROD, IT.CDPRODUTO, IT.IDSTPRCOMVEN,
          IT.NRMESADSCOMORIT, PR.IDCOBTXSERV, IT.NRLUGARMESA , V.DTHRABERMESA, CONVERT(VARCHAR, IT.TXPRODCOMVEN),
          PR.CDBARPRODUTO, IT.NRPRODCOMVEN, IT.QTPRODCOMVEN, IT.VRDESCCOMVEN, IT.VRACRCOMVEN, IT. NRSEQPRODCOM,
          IT.NRSEQPRODCUP, IT.CDPRODPROMOCAO, IM.NRBUTTON, GROUPS.DSBUTTON,CONVERT(CHAR,IT.DTHRINCOMVEN,103),
          CONVERT(CHAR,IT.DTHRINCOMVEN,108)

        UNION

        SELECT
          ISNULL(IM.DSBUTTON, ISNULL(PR.NMPRODIMPFIS,PR.NMPRODUTO)) DSBUTTON, IT.CDFILIAL, IT.NRVENDAREST,
          IT.NRCOMANDA, IT.NRPRODCOMVEN AS NRPRODCOMVEN, IT.QTPRODCOMVEN AS QTPRODCOMVEN, IT.VRDESCCOMVEN AS VRDESCCOMVEN,
          IT.VRACRCOMVEN AS VRACRCOMVEN, IT.VRPRECCOMVEN AS VRPRECCOMVEN, PR.CDARVPROD, IT.CDPRODUTO,
          IT.IDSTPRCOMVEN, IT.NRMESADSCOMORIT, CONVERT(VARCHAR, IT.TXPRODCOMVEN) TXPRODCOMVEN, PR.IDCOBTXSERV,
          ISNULL(IT.NRLUGARMESA,'001') AS NRLUGARMESA, V.DTHRABERMESA, PR.CDBARPRODUTO AS CDPRODIMPFIS,
          IT.NRSEQPRODCOM, IT.NRSEQPRODCUP, IT.CDPRODPROMOCAO, GROUPS.DSBUTTON AS GRUPO,
          CONVERT(CHAR,IT.DTHRINCOMVEN,103) AS DATA, CONVERT(CHAR,IT.DTHRINCOMVEN,108) AS HORA
        FROM
          PRODUTO PR, VENDAREST V,
          ITCOMANDAVEN IT LEFT JOIN ITMENUCONFTE IM
                 ON (IT.CDPRODUTO = IM.CDIDENTBUTON
                    AND IT.CDFILIAL = IM.CDFILIAL
                AND IM.NRCONFTELA = ?
                AND IM.IDTPBUTTON = '1')
                          LEFT JOIN (SELECT DSBUTTON, (NRPGCONFTELA + NRBUTTON) AS CDGRUPO
                                       FROM ITMENUCONFTE
                                      WHERE IDTPBUTTON = '2'
                                        AND CDFILIAL = ?
                                        AND NRCONFTELA = ?) AS GROUPS
                                 ON ((IM.NRPGCONFTAUX + IM.NRBUTTONAUX) = GROUPS.CDGRUPO)
        WHERE (IT.CDFILIAL = ?)
          AND (IT.NRCOMANDA = ?)
          AND (IT.NRVENDAREST = ?)
          AND (IT.NRPRODCOMVEN = ?)
          AND (IT.CDPRODUTO = PR.CDPRODUTO)
          AND (IT.IDSTPRCOMVEN <> '6' AND IT.IDSTPRCOMVEN <> '7')
          AND (IT.CDPRODUTO <> 'X')
          AND (IT.CDFILIAL    = V.CDFILIAL)
          AND (IT.NRVENDAREST = V.NRVENDAREST)
          AND (PR.IDPESAPROD = 'S')
          AND ((IM.NRPGCONFTAUX + IM.NRBUTTONAUX) = GROUPS.CDGRUPO)
          AND IT.CDPRODPROMOCAO IS NULL
        GROUP BY
          IM.DSBUTTON, IT.CDFILIAL, IT.NRVENDAREST, IT.NRCOMANDA, ISNULL(PR.NMPRODIMPFIS,PR.NMPRODUTO),
          IT.VRDESCCOMVEN, IT.VRACRCOMVEN, IT.VRPRECCOMVEN, PR.CDARVPROD, IT.CDPRODUTO, IT.IDSTPRCOMVEN,
          IT.NRMESADSCOMORIT, PR.IDCOBTXSERV, IT.NRLUGARMESA , V.DTHRABERMESA, CONVERT(VARCHAR, IT.TXPRODCOMVEN),
          PR.CDBARPRODUTO, IT.NRPRODCOMVEN, IT.QTPRODCOMVEN, IT.VRDESCCOMVEN, IT.VRACRCOMVEN, IT. NRSEQPRODCOM,
          IT.NRSEQPRODCUP, IT.CDPRODPROMOCAO, IM.NRBUTTON, GROUPS.DSBUTTON, CONVERT(CHAR,IT.DTHRINCOMVEN,103),
          CONVERT(CHAR,IT.DTHRINCOMVEN,108)
    ";

    const SQL_INSERE_ITEM_POSICAO = "
       INSERT INTO
            ITCOMANDAVEN
          (CDFILIAL, NRVENDAREST, NRCOMANDA, NRPRODCOMVEN, CDPRODUTO, QTPRODCOMVEN, VRPRECCOMVEN, TXPRODCOMVEN, IDSTPRCOMVEN,
          VRDESCCOMVEN, NRLUGARMESA, DTHRINCOMVEN, IDPRODIMPFIS, CDLOJA, VRACRCOMVEN, NRSEQPRODCOM, NRSEQPRODCUP, CDCAIXACOLETOR,
          VRPRECCLCOMVEN, CDPRODPROMOCAO, CDVENDEDOR, CDSENHAPED, DSOBSPEDDIGCMD, IDORIGPEDCMD, IDDIVIDECONTA, NRPRODORIG, IDPRODREFIL,
          QTITEMREFIL, NRPEDIDOFOS)
              SELECT
          CDFILIAL, NRVENDAREST, NRCOMANDA, ?, CDPRODUTO, ?, VRPRECCOMVEN, TXPRODCOMVEN, IDSTPRCOMVEN, ?, ?, DTHRINCOMVEN, IDPRODIMPFIS,
          CDLOJA, ?, NRSEQPRODCOM, NRSEQPRODCUP, CDCAIXACOLETOR, VRPRECCLCOMVEN, CDPRODPROMOCAO, CDVENDEDOR, CDSENHAPED, DSOBSPEDDIGCMD,
          IDORIGPEDCMD, 'S', ?, IDPRODREFIL, QTITEMREFIL, NRPEDIDOFOS
              FROM
          ITCOMANDAVEN
              WHERE
          CDFILIAL = ? AND NRVENDAREST = ? AND NRCOMANDA = ? AND NRPRODCOMVEN = ?
    ";

    const SQL_SELECIONA_VALORES = "
        SELECT SUM(ISNULL(VRDESCCOMVEN,0)) AS VRDESCCOMVEN, SUM(ISNULL(VRACRCOMVEN,0)) AS VRACRCOMVEN,
                 SUM(ISNULL(QTPRODCOMVEN,0)) AS QTPRODCOMVEN
        FROM ITCOMANDAVEN
       WHERE (CDFILIAL      = ? )
         AND (NRVENDAREST   = ? )
         AND (NRCOMANDA     = ? )
         AND (IDDIVIDECONTA = 'S' )
         AND ((NRPRODORIG = ? ) OR (?  = 'T'))
    ";

    const SQL_AJUSTA_VALORES = "
      UPDATE ITCOMANDAVEN
          SET QTPRODCOMVEN = QTPRODCOMVEN + ?, VRDESCCOMVEN = VRDESCCOMVEN + ?, VRACRCOMVEN = VRACRCOMVEN + ?
        WHERE CDFILIAL = ?
          AND NRVENDAREST  = ?
          AND NRCOMANDA    = ?
          AND NRPRODCOMVEN = ?
    ";

    const SQL_DELETA_PRODUTOS =  "
      DELETE FROM ITCOMANDAVEN
          WHERE CDFILIAL = ?
              AND NRVENDAREST = ?
              AND NRCOMANDA = ?
              AND NRPRODORIG = ?
              AND IDDIVIDECONTA = 'S'
    ";

    const SQL_VALIDA_VALORES = "
        SELECT NRPRODCOMVEN, (ROUND(QTPRODCOMVEN * (VRPRECCOMVEN + VRPRECCLCOMVEN),2,2) - ISNULL(VRDESCCOMVEN,0) + ISNULL(VRACRCOMVEN,0)) AS VRTOTAL
          FROM ITCOMANDAVEN
         WHERE (CDFILIAL      = ?   )
           AND (NRVENDAREST   = ?   )
           AND (NRCOMANDA     = ?   )
           AND (NRPRODORIG    = ?   )
           AND (IDDIVIDECONTA = 'S' )
    ";

    const SQL_VALIDA_VALORES_ITEM = "
        SELECT SUM(ISNULL(VRDESCCOMVEN,0)) AS VRDESCCOMVEN, SUM(ISNULL(VRACRCOMVEN,0)) AS VRACRCOMVEN,
             SUM(ISNULL(QTPRODCOMVEN,0)) AS QTPRODCOMVEN
        FROM ITCOMANDAVEN
        WHERE (CDFILIAL       = ? )
          AND (NRVENDAREST    = ? )
          AND (NRCOMANDA      = ? )
          AND (NRPRODORIG  = ? )
          AND (IDDIVIDECONTA  = 'S' )
    ";

    const SQL_VALIDA_VALORES_ORIG = "
        SELECT SUM(ISNULL(VRDESCCOMVEN,0)) AS VRDESCCOMVEN, SUM(ISNULL(VRACRCOMVEN,0)) AS VRACRCOMVEN,
             SUM(ISNULL(QTPRODCOMVEN,0)) AS QTPRODCOMVEN
        FROM ITCMDVENORIG
        WHERE   (CDFILIAL     = ? )
            AND (NRVENDAREST  = ? )
            AND (NRCOMANDA    = ? )
            AND (NRPRODCOMVEN  = ? )
    ";

    const SQL_NUM_JUNC_MESA = "
        SELECT NRJUNMESA
           FROM MESAJUNCAO
          WHERE CDFILIAL = ?
            AND CDLOJA   = ?
            AND NRMESA   = ?
    ";

    const SQL_NUM_VEND_REST = "
        SELECT NRVENDAREST
           FROM VENDAREST
          WHERE CDFILIAL = ?
            AND CDLOJA   = ?
            AND NRMESA   = ?
    ";

    const SQL_DEL_VEND_REST = "
        DELETE FROM VENDAREST
              WHERE CDFILIAL  = ?
                AND CDLOJA    = ?
                AND NRMESA = ?
    ";

    const SQL_DEL_COMANDA_VEN = "
        DELETE FROM COMANDAVEN
              WHERE CDFILIAL  = ?
                AND CDLOJA    = ?
                AND NRVENDAREST = ?
    ";

    const SQL_DEL_ITCOMANDA_VEN = "
        DELETE FROM ITCOMANDAVEN
              WHERE CDFILIAL  = ?
                AND CDLOJA    = ?
                AND NRVENDAREST = ?
    ";

    const SQL_NUM_MESAS_AGRUPADAS = "
        SELECT NRMESA
          FROM MESAJUNCAO
         WHERE CDFILIAL = ?
           AND CDLOJA   = ?
           AND NRJUNMESA   = ?
   ";

    const SQL_GET_DELAYED_PRODUCTS = "
        SELECT I.CDFILIAL, I.NRPEDIDOFOS, I.NRITPEDIDOFOS,
               V.NRVENDAREST, V.NRCOMANDA, V.NRPRODCOMVEN,
               V.CDPRODUTO, P.NMPRODUTO AS DSBUTTON, V.NRSEQPRODCOM,
               V.NRLUGARMESA AS 'POSITION', V.NRLUGARMESA AS 'POS',
               SUBSTRING(CONVERT(VARCHAR(255), V.DTHRINCOMVEN, 108), 1, 5) AS DTHRINCOMVEN,
               V.CDPRODPROMOCAO
          FROM ITPEDIDOFOSREL R JOIN ITPEDIDOFOS I
                                  ON I.CDFILIAL = R.CDFILIAL
                                 AND I.NRPEDIDOFOS = R.NRPEDIDOFOS
                                 AND I.NRITPEDIDOFOS = R.NRITPEDIDOFOS
                                JOIN ITCOMANDAVEN V
                                  ON V.CDFILIAL = R.CDFILIAL
                                 AND V.NRVENDAREST = R.NRVENDAREST
                                 AND V.NRCOMANDA = R.NRCOMANDA
                                 AND V.NRPRODCOMVEN = R.NRPRODCOMVEN
                                 AND I.CDPRODUTO = V.CDPRODUTO
                                JOIN SETOR S
                                  ON S.CDSETOR = I.CDSETOR
                                JOIN PRODUTO P
                                  ON P.CDPRODUTO = I.CDPRODUTO
         WHERE S.IDTIPOSETOR = 'E'
           AND I.IDLIBERADO = 'N'
           AND I.NRATRAPRODITPE IS NOT NULL
           AND V.NRATRAPRODCOVE IS NOT NULL
           AND I.DTHREXIBICAOPROD > GETDATE()
           AND V.CDFILIAL = :CDFILIAL
           AND V.NRVENDAREST = :NRVENDAREST
           AND V.NRCOMANDA = :NRCOMANDA
           AND V.IDSTPRCOMVEN <> '6'
    ";

    const SQL_GET_SMARTPROMO = "
        SELECT NMPRODUTO
        FROM PRODUTO
        WHERE CDPRODUTO = :CDPRODUTO
    ";

    const SQL_GET_CONSUMER_BALANCE = "
        SELECT SUM(VRSALDCONFAM) AS SALDO
          FROM SALDOCONS
         WHERE CDCLIENTE = ?
           AND CDCONSUMIDOR = ?
         GROUP BY CDCLIENTE, CDCONSUMIDOR, CDFAMILISALD
    ";

    const SQL_GET_IDVERSALDCON = "
        SELECT IDVERSALDCON
          FROM CONSUMIDOR
         WHERE CDCLIENTE = ?
           AND CDCONSUMIDOR = ?
    ";

    const SQL_CHECK_TABLE_EXISTS = "
        SELECT CASE WHEN (
            SELECT 1
                FROM INFORMATION_SCHEMA.TABLES
                WHERE TABLE_TYPE='BASE TABLE'
                AND TABLE_NAME= 'WAITERORDERS') = 1
                THEN 1 ELSE 0 END AS TABLE_EXISTS
    ";

    const SQL_GET_CONSUMER_PARAMS = "
        SELECT PV.IDEXCONSATFIL, PG.IDEXCONSATGER, PG.CDPICTPROD, PG.IDINFPRODPRODUZ
          FROM PARAVEND PV, PARAMGERAL PG
          WHERE PV.CDFILIAL = ?
    ";

    const SQL_BUSCA_CLIENTES = "
        SELECT CL.CDCLIENTE, CL.NRINSJURCLIE, CL.NMRAZSOCCLIE, CL.CDFILTABPREC,
               CL.CDTABEPREC
          FROM CLIENFILIAL CF, CLIENTE CL
         WHERE CF.CDFILIAL = ?
           AND CF.CDCLIENTE = CL.CDCLIENTE
    ";

    const SQL_BUSCA_CONSUMIDORES = "
        SELECT D.* FROM(
            SELECT ROW_NUMBER() OVER (ORDER BY C.CDCONSUMIDOR) AS ROWNUMBER, C.CDCLIENTE, CL.NMRAZSOCCLIE,
                   C.CDCONSUMIDOR, C.NMCONSUMIDOR, C.CDIDCONSUMID, ISNULL(C.NRCPFRESPCON, '') AS NRCPFRESPCON,
                   C.IDSITCONSUMI, T.IDSOLSENHCONS, C.CDSENHACONS
              FROM CONSUMIDOR C
              JOIN CLIENTE CL ON C.CDCLIENTE = CL.CDCLIENTE
              JOIN TIPOCONS T ON T.CDTIPOCONS = C.CDTIPOCONS
             WHERE (UPPER(C.CDCONSUMIDOR) LIKE :CDCONSUMIDOR
                OR UPPER(C.NMCONSUMIDOR) LIKE :CDCONSUMIDOR
                OR ISNULL(C.CDIDCONSUMID, '') LIKE :CDCONSUMIDOR
                OR ISNULL(C.NRCPFRESPCON, '') LIKE :CDCONSUMIDOR
                OR ISNULL(C.CDEXCONSUMID, '') LIKE :CDCONSUMIDOR)
               AND (T.IDDESABPESQCON = 'N' OR :CONSBLOBQ = 'S')
               AND (CL.CDCLIENTE = :CDCLIENTE OR 'T' = :CDCLIENTE)
               AND (:ALLCONSU = 'T' OR C.IDSITCONSUMI = '1')
               AND C.CDCLIENTE IN (SELECT CDCLIENTE FROM CLIENFILIAL WHERE CDFILIAL = :CDFILIAL)
        ) D
        WHERE D.ROWNUMBER BETWEEN :FIRST AND :LAST
    ";

    const SQL_BUSCA_FAMILIAS = "
        SELECT FF.CDFAMILISALD, FS.NMFAMILISALD, FF.IDSALDNEGFAM, FF.IDPERMCARGACRED
          FROM FAMSALDOFILI FF
          JOIN FAMILIASALDO FS ON FS.CDFAMILISALD = FF.CDFAMILISALD
         WHERE FF.CDFILIAL = :CDFILIAL
    ";

    const SQL_BUSCA_VENDEDORES = "
        SELECT V.CDVENDEDOR, V.NMFANVEN
          FROM VENDEDOR V, OPERADOR O
         WHERE V.CDOPERADOR = O.CDOPERADOR
           AND V.CDFILIAL = ?
         ORDER BY V.CDVENDEDOR
    ";

    const SQL_MENS_PRODUCAO = "
        SELECT O.CDOCORR, O.DSOCORR
          FROM LOJA L, OCORRENCIA O
         WHERE L.CDFILIAL       = ?
           AND L.CDLOJA         = ?
           AND L.CDGROBENVMENSP = O.CDGRPOCOR
         ORDER BY O.CDOCORR
    ";

    const SQL_OBSERVACAO = "
        SELECT O.CDOCORR, O.DSOCORR
          FROM LOJA L, OCORRENCIA O
         WHERE L.CDFILIAL       = ?
           AND L.CDLOJA         = ?
           AND L.CDGRPOCORPED = O.CDGRPOCOR
         ORDER BY O.CDOCORR
    ";

    const SQL_OBSERVACAO_CAN = "
        SELECT O.CDGRPOCOR, O.CDOCORR, O.DSOCORR
          FROM LOJA L, OCORRENCIA O
         WHERE L.CDFILIAL       = ?
           AND L.CDLOJA         = ?
           AND L.CDGRPOCORCAN = O.CDGRPOCOR
         ORDER BY O.CDOCORR
    ";

    const SQL_BUSCA_IMPRESSORAS = "
        SELECT L.NRSEQIMPRLOJA, L.NMIMPRLOJA, L.CDPORTAIMPR, I.IDMODEIMPRES,
               L.DSIPIMPR, L.DSIPPONTE
          FROM IMPRLOJA L, IMPRESSORA I
         WHERE L.CDFILIAL = ?
           AND L.CDLOJA = ?
           AND L.CDIMPRESSORA = I.CDIMPRESSORA
         ORDER BY NMIMPRLOJA
    ";

    const SQL_GET_ALL_CONSUMERS_BY_CLIENT = "
        SELECT A.CDCLIENTE,  A.CDCONSUMIDOR,  A.NMCONSUMIDOR,
            A.VRLIMDEBCONS,  A.IDSITCONSUMI,  A.CDTIPOCONS,
            A.CDCCUSCLIE,    A.CDIDCONSUMID,  A.VRAVIDEBCONS,
            A.CDEXCONSUMID,  A.IDVERSALDCON,  A.IDCONSUMIDOR,
            A.CDVENDEDOR,    A.VRMAXDEBCONS,  A.VRLIMCREDCONS,
            A.VRMAXCREDCONS, A.VRAVICREDCONS, A.IDUTSUBTCCLI,
            A.VRPESUBTCCLI,  A.NRRGCONSUMID,  A.NRCPFRESPCON,
            A.NRTELECONS,    A.NRTELE2CONS,   A.IDTPVENDACONS,
            A.NMFANTCLIE,    A.CDFILIAL,      A.NMFILIAL,
            A.IDCLIENPRIN,   A.IDIMPCPFCUPOM, A.IDCADCONFLIBCON,
            A.DSENDECONS,    A.CDRESERVAPH,   A.DTCHECKINPH,
            A.DTCHECKOUTPH,  CONVERT(VARCHAR, A.DSOBSHOSPEDEPH) AS DSOBSHOSPEDEPH
          FROM(
             SELECT CO.CDCLIENTE,     CO.CDCONSUMIDOR,  CO.NMCONSUMIDOR,
                    CO.VRLIMDEBCONS,  CO.IDSITCONSUMI,  CO.CDTIPOCONS,
                    CO.CDCCUSCLIE,    CO.CDIDCONSUMID,  CO.VRAVIDEBCONS,
                    CO.CDEXCONSUMID,  CO.IDVERSALDCON,  CO.IDCONSUMIDOR,
                    CO.CDVENDEDOR,    CO.VRMAXDEBCONS,  CO.VRLIMCREDCONS,
                    CO.VRMAXCREDCONS, CO.VRAVICREDCONS, CO.NRRGCONSUMID,
                    CO.NRCPFRESPCON,  CO.NRTELECONS,    CO.NRTELE2CONS,
                    T.IDUTSUBTCCLI,   T.VRPESUBTCCLI,   CO.IDTPVENDACONS,
                    C.NMFANTCLIE,     F.CDFILIAL,       F.NMFILIAL,
                    CF.IDCLIENPRIN,   CO.IDIMPCPFCUPOM, CO.IDCADCONFLIBCON,
                    CO.DSENDECONS,    CO.CDRESERVAPH,   CO.DTCHECKINPH,
                    CO.DTCHECKOUTPH,  CONVERT(VARCHAR, CO.DSOBSHOSPEDEPH) AS DSOBSHOSPEDEPH
               FROM CLIENFILIAL CF, CLIENTE C, FILIAL F,
                    CONSUMIDOR CO LEFT JOIN TIPOCONSCLI T ON (CO.CDCLIENTE = T.CDCLIENTE)
                                                         AND (CO.CDTIPOCONS = T.CDTIPOCONS)
               JOIN TIPOCONS TC ON TC.CDTIPOCONS = CO.CDTIPOCONS
              WHERE (CF.CDCLIENTE  = CO.CDCLIENTE)
                AND (C.CDCLIENTE   = CO.CDCLIENTE)
                AND (C.CDCLIENTE = CF.CDCLIENTE)
                AND (CF.CDFILIAL = F.CDFILIAL)
                AND (CF.CDFILIAL   = ?)
                AND (CO.CDCLIENTE  = ?)
                AND (CF.IDPERMVENDAFOS = 'S')
                AND (CO.IDSITCONSUMI   = '1')
                AND (TC.IDDESABPESQCON = 'N')) A
        UNION
        SELECT B.CDCLIENTE,     B.CDCONSUMIDOR,  B.NMCONSUMIDOR,
               B.VRLIMDEBCONS,  B.IDSITCONSUMI,  B.CDTIPOCONS,
               B.CDCCUSCLIE,    B.CDIDCONSUMID,  B.VRAVIDEBCONS,
               B.CDEXCONSUMID,  B.IDVERSALDCON,  B.IDCONSUMIDOR,
               B.CDVENDEDOR,    B.VRMAXDEBCONS,  B.VRLIMCREDCONS,
               B.VRMAXCREDCONS, B.VRAVICREDCONS, B.IDUTSUBTCCLI,
               B.VRPESUBTCCLI,  B.NRRGCONSUMID,  B.NRCPFRESPCON,
               B.NRTELECONS,    B.NRTELE2CONS,   B.IDTPVENDACONS,
               B.NMFANTCLIE,    B.CDFILIAL,      B.NMFILIAL, B.IDCLIENPRIN,
               B.IDIMPCPFCUPOM, B.IDCADCONFLIBCON, B.DSENDECONS,
               B.CDRESERVAPH,   B.DTCHECKINPH,   B.DTCHECKOUTPH,
               CONVERT(VARCHAR, B.DSOBSHOSPEDEPH) AS DSOBSHOSPEDEPH
          FROM(
             SELECT CO.CDCLIENTE,     CO.CDCONSUMIDOR,  CO.NMCONSUMIDOR,
                    CO.VRLIMDEBCONS,  CO.IDSITCONSUMI,  CO.CDTIPOCONS,
                    CO.CDCCUSCLIE,    CO.CDIDCONSUMID,  CO.VRAVIDEBCONS,
                    CO.CDEXCONSUMID,  CO.IDVERSALDCON,  CO.IDCONSUMIDOR,
                    CO.CDVENDEDOR,    CO.VRMAXDEBCONS,  CO.VRLIMCREDCONS,
                    CO.VRMAXCREDCONS, CO.VRAVICREDCONS, CO.NRRGCONSUMID,
                    CO.NRCPFRESPCON,  CO.NRTELECONS,    CO.NRTELE2CONS,
                    T.IDUTSUBTCCLI,   T.VRPESUBTCCLI,   CO.IDTPVENDACONS,
                    C.NMFANTCLIE, F.CDFILIAL, F.NMFILIAL,
                    CF.IDCLIENPRIN, CO.IDIMPCPFCUPOM, CO.IDCADCONFLIBCON,
                    CO.DSENDECONS,    CO.CDRESERVAPH,   CO.DTCHECKINPH,
                    CO.DTCHECKOUTPH,  CONVERT(VARCHAR, CO.DSOBSHOSPEDEPH) AS DSOBSHOSPEDEPH
               FROM CONSUMIDOR CO
               JOIN TIPOCONS TC ON TC.CDTIPOCONS = CO.CDTIPOCONS
               LEFT JOIN TIPOCONSCLI T ON
                 (CO.CDCLIENTE  = T.CDCLIENTE)  AND
                 (CO.CDTIPOCONS = T.CDTIPOCONS), CLIENFILIAL CF, CLIENTE C, FILIAL F
             WHERE (CF.CDCLIENTE = CO.CDCLIENTE)
               AND (C.CDCLIENTE  = CO.CDCLIENTE)
               AND (C.CDCLIENTE  = CF.CDCLIENTE)
               AND (CF.CDFILIAL  = F.CDFILIAL)
               AND (CF.CDFILIAL  = ?)
               AND (CO.CDCLIENTE = ?)
               AND (CF.IDPERMVENDAFOS = 'S')
               AND (CO.IDSITCONSUMI   = '2')
               AND (TC.IDDESABPESQCON = 'N')
               AND (NOT EXISTS(SELECT C.CDCLIENTE
                                 FROM CONSUMIDOR C
                                WHERE C.CDIDCONSUMID = CO.CDIDCONSUMID
                                  AND C.IDSITCONSUMI = '1'))) B
         ORDER BY A.IDCLIENPRIN DESC, A.CDIDCONSUMID
    ";

    const SQL_GET_CONSUMERS_BY_CLIENT = "
        SELECT A.CDCLIENTE,     A.CDCONSUMIDOR,  A.NMCONSUMIDOR,
               A.VRLIMDEBCONS,  A.IDSITCONSUMI,  A.CDTIPOCONS,
               A.CDCCUSCLIE,    A.CDIDCONSUMID,  A.VRAVIDEBCONS,
               A.CDEXCONSUMID,  A.IDVERSALDCON,  A.IDCONSUMIDOR,
               A.CDVENDEDOR,    A.VRMAXDEBCONS,  A.VRLIMCREDCONS,
               A.VRMAXCREDCONS, A.VRAVICREDCONS, A.IDUTSUBTCCLI,
               A.VRPESUBTCCLI,  A.NRRGCONSUMID,  A.NRCPFRESPCON,
               A.NRTELECONS,    A.NRTELE2CONS,   A.IDTPVENDACONS,
               A.NMFANTCLIE,    A.CDFILIAL, A.NMFILIAL,
               A.IDCLIENPRIN,   A.IDIMPCPFCUPOM, A.IDCADCONFLIBCON,
               A.DSENDECONS,    A.CDRESERVAPH,   A.DTCHECKINPH,
               A.DTCHECKOUTPH,  CONVERT(VARCHAR, A.DSOBSHOSPEDEPH) AS DSOBSHOSPEDEPH
         FROM(
            SELECT CO.CDCLIENTE,  CO.CDCONSUMIDOR,     CO.NMCONSUMIDOR,
                   CO.VRLIMDEBCONS,  CO.IDSITCONSUMI,  CO.CDTIPOCONS,
                   CO.CDCCUSCLIE,    CO.CDIDCONSUMID,  CO.VRAVIDEBCONS,
                   CO.CDEXCONSUMID,  CO.IDVERSALDCON,  CO.IDCONSUMIDOR,
                   CO.CDVENDEDOR,    CO.VRMAXDEBCONS,  CO.VRLIMCREDCONS,
                   CO.VRMAXCREDCONS, CO.VRAVICREDCONS, CO.NRRGCONSUMID,
                   CO.NRCPFRESPCON,  CO.NRTELECONS,    CO.NRTELE2CONS,
                   T.IDUTSUBTCCLI,   T.VRPESUBTCCLI,   CO.IDTPVENDACONS,
                   C.NMFANTCLIE, F.CDFILIAL, F.NMFILIAL, CF.IDCLIENPRIN,
                   CO.IDIMPCPFCUPOM, CO.IDCADCONFLIBCON, CO.DSENDECONS,
                   CO.CDRESERVAPH, CO.DTCHECKINPH, CO.DTCHECKOUTPH,
                   CO.DSOBSHOSPEDEPH
              FROM CLIENFILIAL CF, CLIENTE C, FILIAL F,
                   CONSUMIDOR CO LEFT JOIN TIPOCONSCLI T ON (CO.CDCLIENTE  = T.CDCLIENTE)
                                                        AND (CO.CDTIPOCONS = T.CDTIPOCONS)
              JOIN TIPOCONS TC ON TC.CDTIPOCONS = CO.CDTIPOCONS
             WHERE (CF.CDCLIENTE = CO.CDCLIENTE)
               AND (C.CDCLIENTE  = CO.CDCLIENTE)
               AND (C.CDCLIENTE  = CF.CDCLIENTE)
               AND (CF.CDFILIAL  = F.CDFILIAL)
               AND (CF.CDFILIAL  = ?)
               AND (CO.CDCLIENTE = ?)
               AND (CF.IDPERMVENDAFOS = 'S')
               AND (CO.IDSITCONSUMI   = '1')
               AND (TC.IDDESABPESQCON = 'N')) A
         ORDER BY A.IDCLIENPRIN DESC, A.CDIDCONSUMID
    ";

    const SQL_VERIFICA_IP_BLOQUEADO = "
        SELECT
            NRACESSOUSER
          FROM
            ACESSOFM
         WHERE
            DSIP = ? AND
            IDACESSOUSER = 'B' AND
            DTULTATU >= DATEADD(day, -1, GETDATE())
    ";

    const SQL_BUSCA_IPS_BLOQUEADOS = "
        SELECT
            A.NRACESSOUSER, A.NMUSUARIO, A.DTULTATU, A.DSIP, M.NMMESA
          FROM
            ACESSOFM A, MESA M
         WHERE
            A.NRMESA = M.NRMESA AND
            M.CDFILIAL = ? AND
            A.IDACESSOUSER = 'B' AND
            A.DTULTATU >= DATEADD(day, -1, GETDATE())
    ";

    const SQL_LISTA_COMANDA = "
       SELECT C.DSCOMANDA, C.NRVENDAREST, C.NRCOMANDA,
              O.NMCONSUMIDOR,
              C.DSCOMANDA + (CASE WHEN ISNULL(C.DSCONSUMIDOR, O.NMCONSUMIDOR) IS NULL THEN '' ELSE ' - ' END) +
              ISNULL(ISNULL(C.DSCONSUMIDOR, O.NMCONSUMIDOR), '') AS LABELDSCOMANDA
         FROM COMANDAVEN C JOIN VENDAREST V
                             ON C.CDFILIAL = V.CDFILIAL
                             AND C.CDLOJA = V.CDLOJA
                            AND C.NRVENDAREST = V.NRVENDAREST
                      LEFT JOIN CONSUMIDOR O
                             ON V.CDCLIENTE = O.CDCLIENTE
                            AND V.CDCONSUMIDOR = O.CDCONSUMIDOR
        WHERE C.CDFILIAL = ?
          AND C.CDLOJA = ?
          AND (SUBSTRING(C.DSCOMANDA,1,4) <> 'RES_')
          AND (SUBSTRING(C.DSCOMANDA,1,4) <> 'PKR_')
          AND (SUBSTRING(C.DSCOMANDA,1,4) <> 'DLV_')
          AND C.IDSTCOMANDA <> '4'
          AND C.IDSTCOMANDA <> '7'
        ORDER BY C.DSCOMANDA
    ";

    const SQL_CONSULTA_MESA = "
        SELECT B.CDFILIAL, B.NRMESA, B.NMMESA, B.CDSALA,
               B.NMSALA, B.IDSTMESAAUX, B.IDCONTROLE, B.TEMPOCOMSUMIR,
               B.NRPESMESAVEN, B.NRPOSICAOMESA, B.NMVENDEDORABERT
          FROM (SELECT MS.CDFILIAL, MS.NRMESA, MS.NMMESA, SL.CDSALA,
                       IT.DSBUTTON AS NMSALA, MS.IDSTMESAAUX, 'R' AS IDCONTROLE, ' ' AS TEMPOCOMSUMIR,
                       '0' AS NRPESMESAVEN, '0' AS NRPOSICAOMESA,
                       NULL AS NMVENDEDORABERT
                  FROM MESA MS, MESARESERVA RV, RESERVAMESA RM, SALA SL,
                       ITMENUCONFTE IT
                 WHERE MS.CDFILIAL     = ?
                   AND MS.CDLOJA       = ?
                   AND MS.CDFILIAL     = RV.CDFILIAL
                   AND MS.CDLOJA       = RV.CDLOJA
                   AND MS.NRMESA       = RV.NRMESA
                   AND RV.CDFILIAL     = RM.CDFILIAL
                   AND RV.CDLOJA       = RM.CDLOJA
                   AND RV.NRRESMESA    = RM.NRRESMESA
                   AND RM.IDSTRESMESA  = 'P'
                   AND MS.CDFILIAL     = SL.CDFILIAL
                   AND MS.CDSALA       = SL.CDSALA
                   AND MS.CDFILIAL     = IT.CDFILIAL
                   AND IT.NRCONFTELA   = ?
                   AND IT.CDIDENTBUTON = SL.CDSALA
                   AND IT.IDTPBUTTON   = '4'
                   AND (CONVERT(VARCHAR,RM.DTRESMESA,103) = CONVERT(VARCHAR,GETDATE(),103))
                   AND MS.NRMESA IN (SELECT CDIDENTBUTON
                                       FROM ITMENUCONFTE
                                      WHERE CDFILIAL   = ?
                                        AND NRCONFTELA = ?
                                        AND IDTPBUTTON = '3')

                UNION

                SELECT MS.CDFILIAL, MS.NRMESA, MS.NMMESA, SL.CDSALA,
                       IT.DSBUTTON AS NMSALA, MS.IDSTMESAAUX, 'N' AS IDCONTROLE, ' ' AS TEMPOCOMSUMIR,
                       ISNULL(VR.NRPESMESAVEN, '0') AS NRPESMESAVEN, ISNULL(VR.NRPOSICAOMESA, '0') AS NRPOSICAOMESA,
                       VD.NMFANVEN AS NMVENDEDORABERT
                  FROM SALA SL, ITMENUCONFTE IT, MESA MS LEFT JOIN VENDAREST VR
                                               ON MS.CDFILIAL = VR.CDFILIAL
                                              AND MS.CDLOJA   = VR.CDLOJA
                                              AND MS.NRMESA   = VR.NRMESA
                  LEFT JOIN VENDEDOR VD
                    ON VR.CDVENDEDOR = VD.CDVENDEDOR
                 WHERE MS.CDFILIAL     = ?
                   AND MS.CDLOJA       = ?
                   AND MS.CDFILIAL     = SL.CDFILIAL
                   AND MS.CDSALA       = SL.CDSALA
                   AND MS.CDFILIAL     = IT.CDFILIAL
                   AND IT.NRCONFTELA   = ?
                   AND IT.CDIDENTBUTON = SL.CDSALA
                   AND IT.IDTPBUTTON   = '4'
                   AND MS.NRMESA IN (SELECT CDIDENTBUTON
                                       FROM ITMENUCONFTE
                                      WHERE CDFILIAL   = ?
                                        AND NRCONFTELA = ?
                                        AND IDTPBUTTON = '3')
                   AND MS.NRMESA NOT IN (SELECT ISNULL(NRMESAPADRAO, '')
                                           FROM LOJA
                                          WHERE CDFILIAL = ?
                                            AND CDLOJA = ?)
                   AND MS.NRMESA NOT IN (SELECT RV.NRMESA
                                           FROM MESARESERVA RV, RESERVAMESA RM
                                          WHERE RV.CDFILIAL    = ?
                                            AND RV.CDLOJA      = ?
                                            AND RV.CDFILIAL    = RM.CDFILIAL
                                            AND RV.CDLOJA      = RM.CDLOJA
                                            AND RV.NRRESMESA   = RM.NRRESMESA
                                            AND RM.IDSTRESMESA = 'P'
                                            AND (CONVERT(VARCHAR,RM.DTRESMESA,103) = CONVERT(VARCHAR,GETDATE(),103)))) B
    ";

    const SQL_BUSCA_AGRUPADAS = "
        SELECT J.NRJUNMESA, J.NRMESA, M.IDSTMESAAUX
          FROM MESAJUNCAO J, MESA M
         WHERE J.CDFILIAL = ?
           AND J.CDLOJA   = ?
           AND J.CDFILIAL = M.CDFILIAL
           AND J.CDLOJA   = M.CDLOJA
           AND J.NRMESA   = M.NRMESA
    ";

    const SQL_ULTIMA_VENDA = "
        SELECT MAX(IT.DTHRINCOMVEN) AS DTULTIMAVENDA
          FROM ITCOMANDAVEN IT, COMANDAVEN CM, VENDAREST VR
         WHERE IT.CDFILIAL    = CM.CDFILIAL
           AND IT.NRVENDAREST = CM.NRVENDAREST
           AND IT.NRCOMANDA   = CM.NRCOMANDA
           AND CM.CDFILIAL    = VR.CDFILIAL
           AND CM.NRVENDAREST = VR.NRVENDAREST
           AND VR.CDFILIAL    = ?
           AND VR.CDLOJA      = ?
           AND VR.NRMESA      = ?
           AND VR.DTHRFECHMESA IS NULL
     ";

    const SQL_DATA_ABERTURA = "
        SELECT CONVERT(CHAR,DTHRABERMESA,21) AS DTABERTMESA
          FROM VENDAREST
         WHERE CDFILIAL = ?
           AND CDLOJA   = ?
           AND NRMESA   = ?
           AND CONVERT(CHAR,DTHRFECHMESA,103) IS NULL
    ";

    const SQL_BUSCA_DADOS_MESA = "
        SELECT V.NRVENDAREST, C.NRCOMANDA
          FROM VENDAREST V, COMANDAVEN C
         WHERE V.NRVENDAREST = C.NRVENDAREST
           AND V.CDFILIAL = C.CDFILIAL
           AND V.CDFILIAL = ?
           AND V.CDLOJA = ?
           AND V.NRMESA = ?
    ";

    const GET_TABLES_WITH_DELAYED_ITEMS = "
        SELECT DISTINCT R.NRMESA
          FROM ITPEDIDOFOSREL IR JOIN ITPEDIDOFOS I
                                   ON IR.CDFILIAL = I.CDFILIAL
                                  AND IR.NRPEDIDOFOS = I.NRPEDIDOFOS
                                  AND IR.NRITPEDIDOFOS = I.NRITPEDIDOFOS

                                 JOIN SETOR S
                                  ON I.CDSETOR = S.CDSETOR

                                JOIN PRODUTO P
                                  ON I.CDPRODUTO = P.CDPRODUTO

                                JOIN ITCOMANDAVEN T
                                  ON IR.CDFILIAL = T.CDFILIAL
                                 AND IR.NRVENDAREST = T.NRVENDAREST
                                 AND IR.NRCOMANDA = T.NRCOMANDA
                                 AND IR.NRPRODCOMVEN = T.NRPRODCOMVEN

                                JOIN VENDAREST R
                                  ON IR.CDFILIAL = R.CDFILIAL
                                 AND IR.NRVENDAREST = R.NRVENDAREST

         WHERE S.IDTIPOSETOR = 'E'
           AND I.IDLIBERADO = 'N'
           AND T.NRATRAPRODCOVE IS NOT NULL
           AND I.NRATRAPRODITPE IS NOT NULL
           AND I.DTHREXIBICAOPROD > GETDATE()
           AND T.CDFILIAL = :CDFILIAL
           AND T.IDSTPRCOMVEN <> '6'

        UNION

        SELECT DISTINCT R.NRMESA
          FROM ITPEDIDOFOSREL IR JOIN ITPEDIDOFOS I
                                   ON IR.CDFILIAL = I.CDFILIAL
                                  AND IR.NRPEDIDOFOS = I.NRPEDIDOFOS
                                  AND IR.NRITPEDIDOFOS = I.NRITPEDIDOFOS

                                 JOIN SETOR S
                                   ON I.CDSETOR = S.CDSETOR

                                 JOIN PRODUTO P
                                   ON I.CDPRODUTO = P.CDPRODUTO

                                 JOIN ITCOMANDAEST T
                                   ON IR.CDFILIAL = T.CDFILIAL
                                  AND IR.NRVENDAREST = T.NRVENDAREST
                                  AND IR.NRCOMANDA = T.NRCOMANDA
                                  AND IR.NRPRODCOMVEN = T.NRPRODCOMVEN
                                  AND I.CDPRODUTO = T.CDPRODUTO

                                 JOIN ITCOMANDAVEN V
                                   ON IR.CDFILIAL = V.CDFILIAL
                                  AND IR.NRVENDAREST = V.NRVENDAREST
                                  AND IR.NRCOMANDA = V.NRCOMANDA
                                  AND IR.NRPRODCOMVEN = V.NRPRODCOMVEN

                                 JOIN VENDAREST R
                                   ON IR.CDFILIAL = R.CDFILIAL
                                  AND IR.NRVENDAREST = R.NRVENDAREST

         WHERE S.IDTIPOSETOR = 'E'
           AND I.IDLIBERADO = 'N'
           AND T.NRATRAPRODCOES IS NOT NULL
           AND I.NRATRAPRODITPE IS NOT NULL
           AND I.DTHREXIBICAOPROD > GETDATE()
           AND T.CDFILIAL = :CDFILIAL
           AND V.IDSTPRCOMVEN <> '6'
    ";

    const SQL_GET_VENDAREST = "
        SELECT *
          FROM VENDAREST
         WHERE CDFILIAL = ?
           AND NRMESA = ?
    ";

    const SQL_ATUALIZA_CONSUMACAO_MINIMA = "
        UPDATE COMANDAVEN
          SET VRCONSUMAMIN  = ?
          WHERE CDFILIAL    = ?
            AND NRCOMANDA   = ?
            AND NRVENDAREST = ?
    ";

    const SQL_RETIRA_ITEM = "
        DELETE
          FROM ITCOMANDAVEN
          WHERE CDFILIAL    = :CDFILIAL
            AND NRCOMANDA   = :NRCOMANDA
            AND NRVENDAREST = :NRVENDAREST
            AND CDPRODUTO   = :CDPRODUTO
    ";

    const SQL_BUSCA_VALORCOMANDA = "
        SELECT NRLUGARMESA, SUM((VRPRECCOMVEN + VRPRECCLCOMVEN) * QTPRODCOMVEN) AS VRPRECCOMVEN
          FROM ITCOMANDAVEN
         WHERE NRCOMANDA = :NRCOMANDA
           AND NRVENDAREST = :NRVENDAREST
           AND IDSTPRCOMVEN <> '6'
           AND CDPRODUTO NOT IN (:CDPRODUTO)
      GROUP BY NRLUGARMESA
    ";

    const SQL_GET_CLIENT_DATA = "
        SELECT CDCLIENTE, CDCCUSCLIE
          FROM PARAVEND
         WHERE CDFILIAL = :CDFILIAL
    ";

    const SQL_GET_CONSUMER_BY_EMAIL = "
        SELECT CDCLIENTE, CDCONSUMIDOR, CDSENHACONSMD5
          FROM CONSUMIDOR
         WHERE CDCLIENTE = ?
           AND DSEMAILCONS = ?
    ";

    const SQL_GET_CONSUMER_DETAILS = "
        SELECT CO.CDCLIENTE, CO.CDCONSUMIDOR, CO.CDIDCONSUMID, CO.NMCONSUMIDOR, CO.IDTPVENDACONS, CO.CDSENHACONS,
               CO.DSEMAILCONS, CO.NRCELULARCONS, CO.NRTELECONS, CL.NMRAZSOCCLIE, CO.CDIDCONSUMID, ISNULL(CO.NRCPFRESPCON, '') AS NRCPFRESPCON
          FROM CONSUMIDOR CO
          JOIN CLIENTE CL ON CL.CDCLIENTE = CO.CDCLIENTE
         WHERE (CO.CDCLIENTE = :CDCLIENTE OR :CDCLIENTE = 'T')
           AND CO.CDCONSUMIDOR = :code
           AND CO.IDSITCONSUMI = '1'
    ";

    const SQL_INSERT_CONSUMER = "
        INSERT INTO CONSUMIDOR
            (CDCLIENTE, CDCONSUMIDOR, CDSENHACONSMD5, CDIDCONSUMID, NMCONSUMIDOR, DSEMAILCONS, NRCELULARCONS, IDSITCONSUMI, CDCCUSCLIE, IDCONSUMIDOR,
            IDATUCONSUMI, IDTPVENDACONS, IDIMPCPFCUPOM, IDCADCONFLIBCON, IDPERCONSPRODEX, IDTPSELMANHA, IDTPSEALMOCO,
            IDTPSELTARDE, IDCRACHAMESTRE)
        VALUES
            (:CDCLIENTE, :CDCONSUMIDOR, :CDSENHACONSMD5, :CDIDCONSUMID, :NMCONSUMIDOR, :DSEMAILCONS, :NRCELULARCONS, :IDSITCONSUMI, :CDCCUSCLIE,
            :IDCONSUMIDOR, :IDATUCONSUMI, :IDTPVENDACONS, :IDIMPCPFCUPOM, :IDCADCONFLIBCON, :IDPERCONSPRODEX, :IDTPSELMANHA,
            :IDTPSEALMOCO, :IDTPSELTARDE, :IDCRACHAMESTRE)
    ";

    const SQL_ALTERA_STATUS_ACESSOFM = "
        UPDATE ACESSOFM
           SET IDACESSOUSER = ?, CDFILIAL = ?, CDLOJA = ?
         WHERE NRACESSOUSER = ?
    ";

    const SQL_IDACESSOUSER = "
        SELECT IDACESSOUSER
         FROM ACESSOFM
          WHERE NRACESSOUSER = ?
    ";

    const BUSCA_MESAS_AGRUPADAS = "
        SELECT V.NRVENDAREST, C.NRCOMANDA, V.NRMESA, V.NRPESMESAVEN,
               ISNULL(CO.IDSEXOCONS, 'M') AS IDSEXOCONS, MS.IDSTMESAAUX,
               V.NRPOSICAOMESA, V.CDCLIENTE, V.CDCONSUMIDOR
          FROM MESAJUNCAO MJ, COMANDAVEN C, MESA MS, VENDAREST V
          LEFT JOIN CONSUMIDOR CO
                 ON V.CDCONSUMIDOR = CO.CDCONSUMIDOR
                AND V.CDCLIENTE = CO.CDCLIENTE
         WHERE MJ.NRJUNMESA = (SELECT NRJUNMESA
                                 FROM MESAJUNCAO
                                WHERE CDFILIAL = :CDFILIAL
                                  AND CDLOJA = :CDLOJA
                                  AND NRMESA = :NRMESA)
           AND V.NRVENDAREST = C.NRVENDAREST
           AND V.NRMESA = MJ.NRMESA
           AND MS.NRMESA = V.NRMESA
           AND MS.CDFILIAL = V.CDFILIAL
           AND MS.CDLOJA = V.CDLOJA

         UNION

        SELECT V.NRVENDAREST, C.NRCOMANDA, V.NRMESA, V.NRPESMESAVEN,
               ISNULL(CO.IDSEXOCONS, 'M') AS IDSEXOCONS, MS.IDSTMESAAUX,
               V.NRPOSICAOMESA, V.CDCLIENTE, V.CDCONSUMIDOR
          FROM COMANDAVEN C, MESA MS, VENDAREST V
          LEFT JOIN CONSUMIDOR CO
                 ON V.CDCONSUMIDOR = CO.CDCONSUMIDOR
                AND V.CDCLIENTE = CO.CDCLIENTE
         WHERE V.NRVENDAREST = C.NRVENDAREST
           AND V.NRMESA NOT IN (SELECT NRMESA
                                  FROM MESAJUNCAO)
           AND V.CDFILIAL = :CDFILIAL
           AND V.CDLOJA = :CDLOJA
           AND V.NRMESA = :NRMESA
           AND MS.NRMESA = V.NRMESA
           AND MS.CDFILIAL = V.CDFILIAL
           AND MS.CDLOJA = V.CDLOJA
    ";

    /*const BUSCA_MESAS_AGRUPADAS = "
        SELECT V.NRVENDAREST, C.NRCOMANDA, V.NRMESA, V.NRPESMESAVEN, ISNULL(CO.IDSEXOCONS, 'M') AS IDSEXOCONS, MS.IDSTMESAAUX
          FROM MESAJUNCAO MJ, COMANDAVEN C, VENDAREST V, MESA MS
            LEFT JOIN
                CONSUMIDOR CO
            ON
                V.CDCONSUMIDOR = CO.CDCONSUMIDOR
        WHERE MJ.NRJUNMESA = (SELECT NRJUNMESA
                                FROM MESAJUNCAO
                               WHERE NRMESA = ?)
          AND V.NRVENDAREST = C.NRVENDAREST
          AND V.NRMESA      = MJ.NRMESA
          AND MS.NRMESA     = V.NRMESA
          AND MS.CDFILIAL   = V.CDFILIAL
          AND MS.CDLOJA     = V.CDLOJA

        UNION

        SELECT V.NRVENDAREST, C.NRCOMANDA, V.NRMESA, V.NRPESMESAVEN, ISNULL(CO.IDSEXOCONS, 'M') AS IDSEXOCONS, MS.IDSTMESAAUX
        FROM COMANDAVEN C, VENDAREST V, MESA MS
            LEFT JOIN
                CONSUMIDOR CO
            ON
                V.CDCONSUMIDOR = CO.CDCONSUMIDOR
        WHERE V.NRVENDAREST = C.NRVENDAREST
        AND V.NRMESA NOT IN (SELECT NRMESA FROM MESAJUNCAO)
        AND V.NRMESA = ?
        AND MS.NRMESA     = V.NRMESA
        AND MS.CDFILIAL   = V.CDFILIAL
        AND MS.CDLOJA     = V.CDLOJA
    ";*/

    const BUSCA_NRMESA = "
        SELECT NRMESA, V.*
          FROM COMANDAVEN C
          INNER JOIN VENDAREST V
            ON  V.NRVENDAREST = C.NRVENDAREST
            AND V.CDFILIAL = C.CDFILIAL
         WHERE C.CDFILIAL = :CDFILIAL
           AND V.NRVENDAREST = :NRVENDAREST
           AND C.NRCOMANDA = :NRCOMANDA
    ";

    const VERIFICA_COUVERT = "
        SELECT IT.CDPRODUTO, IT.NRLUGARMESA,
               IT.VRPRECCOMVEN + IT.VRPRECCLCOMVEN + IT.VRACRCOMVEN - IT.VRDESCCOMVEN AS PRECOCOUVERT
          FROM ITCOMANDAVEN IT
         WHERE IT.CDFILIAL    = :CDFILIAL
           AND IT.NRCOMANDA   = :NRCOMANDA
           AND IT.NRVENDAREST = :NRVENDAREST
           AND IT.CDPRODUTO   = :CDPRODUTO
    ";

    const SQL_INSERE_COUVERT = "
        INSERT INTO ITCOMANDAVEN(
            CDFILIAL, NRVENDAREST, NRCOMANDA, NRPRODCOMVEN,
            CDPRODUTO, QTPRODCOMVEN, VRPRECCOMVEN,
            IDSTPRCOMVEN, VRDESCCOMVEN, DTHRINCOMVEN, TXPRODCOMVEN,
            NRLUGARMESA, VRACRCOMVEN, CDLOJA,
            NRATRAPRODCOVE, IDORIGPEDCMD, DSOBSPEDDIGCMD, VRPRECCLCOMVEN)
        VALUES(
            ?, ?, ?, ?,
            ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?, ?,
            ?, ?, ?, ?)
    ";

    const SQL_UPDATE_COUVERT = "
        UPDATE ITCOMANDAVEN
          SET QTPRODCOMVEN = ?
          WHERE CDFILIAL     = ?
            AND NRCOMANDA    = ?
            AND NRVENDAREST  = ?
            AND CDPRODUTO    = ?
    ";

    const SQL_DADOS_MESA_POS = "
        SELECT V.NRVENDAREST, V.NRPESMESAVEN, C.NRCOMANDA, V.CDVENDEDOR,
               M.IDSTMESAAUX, V.CDCLIENTE, V.CDCONSUMIDOR, M.CDSALA,
               M.NMMESA, M.NRMESA,  V.NRPOSICAOMESA, L.NMRAZSOCCLIE,
               O.NMCONSUMIDOR, O.NRCPFRESPCON, C.VRDESCFID, VD.NMFANVEN AS NMVENDEDORABERT
          FROM COMANDAVEN C, MESA M,
               VENDAREST V LEFT JOIN CLIENTE L
                                  ON V.CDCLIENTE = L.CDCLIENTE
                           LEFT JOIN CONSUMIDOR O
                                  ON V.CDCLIENTE = O.CDCLIENTE
                                 AND V.CDCONSUMIDOR = O.CDCONSUMIDOR
                           LEFT JOIN VENDEDOR VD
                                  ON V.CDVENDEDOR = VD.CDVENDEDOR
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

    const SQL_VR_ACRESCIMO = "
        SELECT VRACRCOMANDA
          FROM COMANDAVEN
         WHERE CDFILIAL    = ?
           AND NRVENDAREST = ?
           AND NRCOMANDA   = ?
    ";

    const SQL_ITENS_DETALHADOS = "
        SELECT
          ISNULL(IM.DSBUTTON, ISNULL(PR.NMPRODIMPFIS,PR.NMPRODUTO)) DSBUTTON, IT.CDFILIAL, IT.NRVENDAREST, IT.NRCOMANDA,
          MIN(IT.NRPRODCOMVEN) AS NRPRODCOMVEN, SUM(IT.QTPRODCOMVEN) AS QTPRODCOMVEN, IT.VRDESCCOMVEN AS VRDESCCOMVEN,
          IT.VRACRCOMVEN AS VRACRCOMVEN, IT.VRPRECCOMVEN AS VRPRECCOMVEN, PR.CDARVPROD, IT.CDPRODUTO, IT.IDSTPRCOMVEN,
          IT.NRMESADSCOMORIT, CONVERT(VARCHAR, IT.TXPRODCOMVEN) TXPRODCOMVEN, PR.IDCOBTXSERV,
          ISNULL(IT.NRLUGARMESA,'001') AS NRLUGARMESA, V.DTHRABERMESA, PR.CDBARPRODUTO AS CDPRODIMPFIS,
          IT. NRSEQPRODCOM, IT.NRSEQPRODCUP, IT.CDPRODPROMOCAO, GROUPS.DSBUTTON AS GRUPO,
          CONVERT(CHAR,IT.DTHRINCOMVEN,103) AS DATA, CONVERT(CHAR,IT.DTHRINCOMVEN,108) AS HORA, IT.IDDIVIDECONTA,
          IT.NRPRODORIG, V.NRMESA, IT.VRPRECCLCOMVEN, IT.IDORIGEMVENDA, VD.NMFANVEN AS NMVENDEDOR
        FROM
          PRODUTO PR, VENDAREST V LEFT JOIN VENDEDOR VD
          ON V.CDVENDEDOR = VD.CDVENDEDOR,
          ITCOMANDAVEN IT LEFT JOIN ITMENUCONFTE IM
                 ON (IT.CDPRODUTO = IM.CDIDENTBUTON
                    AND IT.CDFILIAL = IM.CDFILIAL
                AND IM.NRCONFTELA = ?
                AND IM.IDTPBUTTON = '1')
                          LEFT JOIN (SELECT DSBUTTON, (NRPGCONFTELA + NRBUTTON) AS CDGRUPO
                                       FROM ITMENUCONFTE
                                      WHERE IDTPBUTTON = '2'
                                        AND CDFILIAL = ?
                                        AND NRCONFTELA = ?) AS GROUPS
                                 ON ((IM.NRPGCONFTAUX + IM.NRBUTTONAUX) = GROUPS.CDGRUPO)
        WHERE (IT.CDFILIAL = ?)
          AND (CHARINDEX(IT.NRCOMANDA, ?) > 0)
          AND (IT.CDPRODUTO = PR.CDPRODUTO)
          AND (IT.IDSTPRCOMVEN <> '6' AND IT.IDSTPRCOMVEN <> '7')
          AND (IT.CDPRODUTO <> 'X')
          AND (IT.CDFILIAL    = V.CDFILIAL)
          AND (IT.NRVENDAREST = V.NRVENDAREST)
          AND (PR.IDPESAPROD = 'N')
          AND ((charindex(IT.NRLUGARMESA, ?) > 0)  or ('T' = ?))
          AND IT.CDPRODPROMOCAO IS NULL
          AND IT.IDPRODREFIL = 'N'
        GROUP BY
          IM.DSBUTTON, IT.CDFILIAL, IT.NRVENDAREST, IT.NRCOMANDA, ISNULL(PR.NMPRODIMPFIS,PR.NMPRODUTO),
          IT.VRDESCCOMVEN, IT.VRACRCOMVEN, IT.VRPRECCOMVEN, PR.CDARVPROD, IT.CDPRODUTO, IT.IDSTPRCOMVEN,
          IT.NRMESADSCOMORIT, PR.IDCOBTXSERV, IT.NRLUGARMESA , V.DTHRABERMESA, CONVERT(VARCHAR, IT.TXPRODCOMVEN),
          PR.CDBARPRODUTO, IT.NRPRODCOMVEN, IT.QTPRODCOMVEN, IT.VRDESCCOMVEN, IT.VRACRCOMVEN, IT. NRSEQPRODCOM,
          IT.NRSEQPRODCUP, IT.CDPRODPROMOCAO, IM.NRBUTTON, GROUPS.DSBUTTON,CONVERT(CHAR,IT.DTHRINCOMVEN,103),
          CONVERT(CHAR,IT.DTHRINCOMVEN,108), IT.IDDIVIDECONTA, IT.NRPRODORIG, V.NRMESA, IT.VRPRECCLCOMVEN, IT.IDORIGEMVENDA, VD.NMFANVEN

        UNION

        SELECT
          ISNULL(IM.DSBUTTON, ISNULL(PR.NMPRODIMPFIS,PR.NMPRODUTO)) DSBUTTON, IT.CDFILIAL, IT.NRVENDAREST,
          IT.NRCOMANDA, IT.NRPRODCOMVEN AS NRPRODCOMVEN, IT.QTPRODCOMVEN AS QTPRODCOMVEN, IT.VRDESCCOMVEN AS VRDESCCOMVEN,
          IT.VRACRCOMVEN AS VRACRCOMVEN, IT.VRPRECCOMVEN AS VRPRECCOMVEN, PR.CDARVPROD, IT.CDPRODUTO,
          IT.IDSTPRCOMVEN, IT.NRMESADSCOMORIT, CONVERT(VARCHAR, IT.TXPRODCOMVEN) TXPRODCOMVEN, PR.IDCOBTXSERV,
          ISNULL(IT.NRLUGARMESA,'001') AS NRLUGARMESA, V.DTHRABERMESA, PR.CDBARPRODUTO AS CDPRODIMPFIS,
          IT.NRSEQPRODCOM, IT.NRSEQPRODCUP, IT.CDPRODPROMOCAO, GROUPS.DSBUTTON AS GRUPO,
          CONVERT(CHAR,IT.DTHRINCOMVEN,103) AS DATA, CONVERT(CHAR,IT.DTHRINCOMVEN,108) AS HORA, IT.IDDIVIDECONTA,
          IT.NRPRODORIG, V.NRMESA, IT.VRPRECCLCOMVEN, IT.IDORIGEMVENDA, VD.NMFANVEN AS NMVENDEDOR
        FROM
          PRODUTO PR, VENDAREST V LEFT JOIN VENDEDOR VD
          ON V.CDVENDEDOR = VD.CDVENDEDOR,
          ITCOMANDAVEN IT LEFT JOIN ITMENUCONFTE IM
                 ON (IT.CDPRODUTO = IM.CDIDENTBUTON
                    AND IT.CDFILIAL = IM.CDFILIAL
                AND IM.NRCONFTELA = ?
                AND IM.IDTPBUTTON = '1')
                          LEFT JOIN (SELECT DSBUTTON, (NRPGCONFTELA + NRBUTTON) AS CDGRUPO
                                       FROM ITMENUCONFTE
                                      WHERE IDTPBUTTON = '2'
                                        AND CDFILIAL = ?
                                        AND NRCONFTELA = ?) AS GROUPS
                                 ON ((IM.NRPGCONFTAUX + IM.NRBUTTONAUX) = GROUPS.CDGRUPO)
        WHERE (IT.CDFILIAL = ?)
          AND (CHARINDEX(IT.NRCOMANDA, ?) > 0)
          AND (IT.CDPRODUTO = PR.CDPRODUTO)
          AND (IT.IDSTPRCOMVEN <> '6' AND IT.IDSTPRCOMVEN <> '7')
          AND (IT.CDPRODUTO <> 'X')
          AND (IT.CDFILIAL    = V.CDFILIAL)
          AND (IT.NRVENDAREST = V.NRVENDAREST)
          AND (PR.IDPESAPROD = 'S')
          AND ((charindex(IT.NRLUGARMESA, ?) > 0)  or ('T' = ?))
          AND ((IM.NRPGCONFTAUX + IM.NRBUTTONAUX) = GROUPS.CDGRUPO)
          AND IT.CDPRODPROMOCAO IS NULL
          AND IT.IDPRODREFIL = 'N'
        GROUP BY
          IM.DSBUTTON, IT.CDFILIAL, IT.NRVENDAREST, IT.NRCOMANDA, ISNULL(PR.NMPRODIMPFIS,PR.NMPRODUTO),
          IT.VRDESCCOMVEN, IT.VRACRCOMVEN, IT.VRPRECCOMVEN, PR.CDARVPROD, IT.CDPRODUTO, IT.IDSTPRCOMVEN,
          IT.NRMESADSCOMORIT, PR.IDCOBTXSERV, IT.NRLUGARMESA , V.DTHRABERMESA, CONVERT(VARCHAR, IT.TXPRODCOMVEN),
          PR.CDBARPRODUTO, IT.NRPRODCOMVEN, IT.QTPRODCOMVEN, IT.VRDESCCOMVEN, IT.VRACRCOMVEN, IT. NRSEQPRODCOM,
          IT.NRSEQPRODCUP, IT.CDPRODPROMOCAO, IM.NRBUTTON, GROUPS.DSBUTTON, CONVERT(CHAR,IT.DTHRINCOMVEN,103),
          CONVERT(CHAR,IT.DTHRINCOMVEN,108), IT.IDDIVIDECONTA, IT.NRPRODORIG, V.NRMESA, IT.VRPRECCLCOMVEN, IT.IDORIGEMVENDA, VD.NMFANVEN

        UNION

        SELECT
          ISNULL(IM.DSBUTTON, PR.NMPRODUTO) AS DSBUTTON, IT.CDFILIAL, IT.NRVENDAREST, IT.NRCOMANDA,
          MIN(IT.NRPRODCOMVEN) AS NRPRODCOMVEN, IT.QTPRODCOMVEN, IT.VRDESCCOMVEN, IT.VRACRCOMVEN,
          IT.VRPRECCOMVEN, PR.CDARVPROD, IT.CDPRODUTO, '3' AS IDSTPRCOMVEN,
          NULL AS NRMESADSCOMORIT, CONVERT(NVARCHAR(100), IT.TXPRODCOMVEN) AS TXPRODCOMVEN, PR.IDCOBTXSERV, ISNULL(IT.NRLUGARMESA, '01') AS NRLUGARMESA,
          VE.DTHRABERMESA, PR.CDBARPRODUTO AS CDPRODIMPFIS, IT.NRSEQPRODCOM, IT.NRSEQPRODCUP, IT.CDPRODPROMOCAO,
          'PROMO' AS GRUPO, CONVERT(CHAR,IT.DTHRINCOMVEN,103) AS DATA, CONVERT(CHAR,IT.DTHRINCOMVEN,108) AS HORA, IT.IDDIVIDECONTA,
          IT.NRPRODORIG, VE.NRMESA, IT.VRPRECCLCOMVEN, IT.IDORIGEMVENDA, VD.NMFANVEN AS NMVENDEDOR
        FROM
          PRODUTO PR, VENDAREST VE LEFT JOIN VENDEDOR VD
          ON VE.CDVENDEDOR = VD.CDVENDEDOR, ITCOMANDAVEN IT LEFT JOIN ITMENUCONFTE IM ON IM.CDIDENTBUTON = IT.CDPRODUTO
        WHERE IT.CDFILIAL = ?
          AND (CHARINDEX(IT.NRCOMANDA, ?) > 0)
          AND IT.CDPRODPROMOCAO IS NOT NULL
          AND IT.CDPRODUTO = PR.CDPRODUTO
          AND ((charindex(IT.NRLUGARMESA, ?) > 0)  or ('T' = ?))
          AND (IT.IDSTPRCOMVEN <> '6' AND IT.IDSTPRCOMVEN <> '7')
          AND IT.CDFILIAL = VE.CDFILIAL
          AND IT.NRVENDAREST = VE.NRVENDAREST
          AND IT.IDPRODREFIL = 'N'
        GROUP BY
          IM.DSBUTTON, PR.NMPRODUTO, IT.CDFILIAL, IT.NRVENDAREST, IT.NRCOMANDA,
          IT.QTPRODCOMVEN, IT.VRPRECCOMVEN, IT.VRDESCCOMVEN, IT.VRACRCOMVEN,
          PR.CDARVPROD, IT.CDPRODUTO, CONVERT(NVARCHAR(100), IT.TXPRODCOMVEN), PR.IDCOBTXSERV, IT.NRLUGARMESA,
          VE.DTHRABERMESA, PR.CDBARPRODUTO, IT.NRSEQPRODCOM, IT.NRSEQPRODCUP, IT.CDPRODPROMOCAO,
          CONVERT(CHAR,IT.DTHRINCOMVEN,103), CONVERT(CHAR,IT.DTHRINCOMVEN,108), IT.IDDIVIDECONTA,
          IT.NRPRODORIG, VE.NRMESA, IT.VRPRECCLCOMVEN, IT.IDORIGEMVENDA, VD.NMFANVEN
    ";

    const SQL_ITENS_ITCOMANDAEST = "
        SELECT ISNULL(PR.NMPRODIMPFIS,PR.NMPRODUTO) AS DSBUTTON, IT.NRPRODCOMVEN,
               IT.CDPRODUTO, IT.QTPROCOMEST AS QUANT, NULL AS PRECO, IT.VRDESITCOMEST AS DESCONTO,
               'E' AS TIPOPROMO, '' AS LISTA, IT.TXPRODCOMVENEST AS TXPRODCOMVEN
          FROM ITCOMANDAEST IT, PRODUTO PR
         WHERE IT.CDFILIAL = ?
           AND IT.NRVENDAREST = ?
           AND IT.NRCOMANDA = ?
           AND IT.NRPRODCOMVEN = ?
           AND PR.CDPRODUTO = IT.CDPRODUTO
    ";

    //Just for testing
    const DELETE_ORIG = "
        DELETE FROM ITCMDVENORIG
    ";

    //Just for testing
    const DELETE_ITCOMANDAVEN = "
        DELETE FROM ITCOMANDAVEN WHERE IDDIVIDECONTA = 'S'
    ";

    const SQL_INSERE_ITEM_COMANDA_ORIG = "
        INSERT INTO
          ITCMDVENORIG
            (CDFILIAL, NRVENDAREST, NRCOMANDA, NRPRODCOMVEN, CDPRODUTO, QTPRODCOMVEN, VRPRECCOMVEN,
            TXPRODCOMVEN, IDSTPRCOMVEN, VRDESCCOMVEN, NRLUGARMESA, DTHRINCOMVEN, IDPRODIMPFIS, CDLOJA, VRACRCOMVEN,
            NRSEQPRODCOM, NRSEQPRODCUP, CDCAIXACOLETOR, VRPRECCLCOMVEN, CDPRODPROMOCAO, CDVENDEDOR, CDSENHAPED,
            DSOBSPEDDIGCMD, IDORIGPEDCMD, NRPEDIDOFOS, IDPRODREFIL, QTITEMREFIL)
        SELECT
            CDFILIAL, NRVENDAREST, NRCOMANDA, NRPRODCOMVEN, CDPRODUTO, QTPRODCOMVEN, VRPRECCOMVEN,
            TXPRODCOMVEN, IDSTPRCOMVEN, VRDESCCOMVEN, NRLUGARMESA, DTHRINCOMVEN, IDPRODIMPFIS, CDLOJA, VRACRCOMVEN,
            NRSEQPRODCOM, NRSEQPRODCUP, CDCAIXACOLETOR, VRPRECCLCOMVEN, CDPRODPROMOCAO, CDVENDEDOR, CDSENHAPED,
            DSOBSPEDDIGCMD, IDORIGPEDCMD, NRPEDIDOFOS, IDPRODREFIL, QTITEMREFIL
        FROM
          ITCOMANDAVEN
        WHERE
          CDFILIAL = ? AND NRVENDAREST = ? AND NRCOMANDA = ? AND NRPRODCOMVEN = ?
    ";

    const SQL_DELETA_PRODUTO_ORIGINAL =  "
      DELETE FROM ITCOMANDAVEN
       WHERE CDFILIAL = ?
         AND NRVENDAREST  = ?
         AND NRCOMANDA    = ?
         AND NRPRODCOMVEN = ?
    ";

    const SQL_INSERE_ITEM_COMANDA = "
      INSERT INTO ITCOMANDAVEN
        (CDFILIAL, NRVENDAREST, NRCOMANDA, NRPRODCOMVEN, CDPRODUTO, QTPRODCOMVEN, VRPRECCOMVEN,
        TXPRODCOMVEN, IDSTPRCOMVEN, VRDESCCOMVEN, NRLUGARMESA, DTHRINCOMVEN, IDPRODIMPFIS, CDLOJA, VRACRCOMVEN, NRSEQPRODCOM,
        NRSEQPRODCUP, CDCAIXACOLETOR, VRPRECCLCOMVEN, CDPRODPROMOCAO, CDVENDEDOR, CDSENHAPED, DSOBSPEDDIGCMD,
        IDORIGPEDCMD, NRPEDIDOFOS, IDPRODREFIL, QTITEMREFIL, IDPRODPRODUZ)

      SELECT CDFILIAL, NRVENDAREST, NRCOMANDA, NRPRODCOMVEN, CDPRODUTO, QTPRODCOMVEN, VRPRECCOMVEN, TXPRODCOMVEN, IDSTPRCOMVEN,
                VRDESCCOMVEN, NRLUGARMESA, DTHRINCOMVEN, IDPRODIMPFIS, CDLOJA, VRACRCOMVEN, NRSEQPRODCOM, NRSEQPRODCUP, CDCAIXACOLETOR,
                VRPRECCLCOMVEN, CDPRODPROMOCAO, CDVENDEDOR, CDSENHAPED, DSOBSPEDDIGCMD, IDORIGPEDCMD, NRPEDIDOFOS, IDPRODREFIL, QTITEMREFIL,
                :IDPRODPRODUZ

      FROM ITCMDVENORIG

      WHERE CDFILIAL       = :CDFILIAL
        AND NRVENDAREST    = :NRVENDAREST
        AND NRCOMANDA      = :NRCOMANDA
        AND ((NRPRODCOMVEN = :NRPRODCOMVEN) OR (:NRPRODCOMVEN = 'T'))
    ";

    const SQL_DELETA_CMD_VEN_ORIG = "
      DELETE FROM ITCMDVENORIG
        WHERE CDFILIAL       = :CDFILIAL
          AND NRVENDAREST    = :NRVENDAREST
          AND NRCOMANDA      = :NRCOMANDA
          AND ((NRPRODCOMVEN = :NRPRODCOMVEN) OR (:NRPRODCOMVEN = 'T'))
    ";

    const SQL_GET_PRODUCT_NAME = "
        SELECT P.NMPRODUTO
          FROM ITPEDIDOFOS I
          JOIN PRODUTO P ON P.CDPRODUTO = I.CDPRODUTO
         WHERE NRPEDIDOFOS = ?
           AND NRITPEDIDOFOS = ?
    ";

    const SQL_VAL_IMPR_LOJA = "
        SELECT I.IDMODEIMPRES, L.CDPORTAIMPR, L.NRSEQIMPRLOJA, M.DSENDPORTA,
               L.DSIPIMPR, L.DSIPPONTE, L.NMIMPRLOJA
          FROM IMPRESSORA I,
               IMPRLOJA L LEFT JOIN MAPIMPRLOJA M
                                 ON L.CDFILIAL = M.CDFILIAL
                                AND L.CDLOJA = M.CDLOJA
                                AND L.CDPORTAIMPR = M.CDPORTAIMPR
         WHERE L.CDFILIAL = ?
           AND L.CDLOJA = ?
           AND L.NRSEQIMPRLOJA = ?
           AND L.CDIMPRESSORA = I.CDIMPRESSORA
    ";

    const SQL_UPDATA_MENS = "
        UPDATE COMANDAVEN
           SET TXMOTIVCANCE = ?
         WHERE CDFILIAL     = ?
           AND NRVENDAREST  = ?
           AND NRCOMANDA    = ?
    ";

    const SQL_GET_TXPRODCOMVEN = "
        SELECT TXPRODCOMVEN
          FROM ITCOMANDAVEN
         WHERE CDFILIAL = ?
           AND NRVENDAREST = ?
           AND NRCOMANDA = ?
           AND NRPRODCOMVEN = ?
    ";

    const SQL_ALTERA_STATUS_COMANDA = "
        UPDATE COMANDAVEN
          SET IDSTCOMANDA   = ?
          WHERE CDFILIAL    = ?
            AND NRCOMANDA   = ?
            AND NRVENDAREST = ?
    ";

    const SQL_ALTERA_STATUS_MESA = "
        UPDATE MESA
           SET IDSTMESAAUX = :IDSTMESAAUX
         WHERE CDFILIAL    = :CDFILIAL
           AND CDLOJA      = :CDLOJA
           AND NRMESA IN (SELECT DISTINCT NRMESA
                             FROM MESA
                            WHERE CDFILIAL = :CDFILIAL
                              AND CDLOJA   = :CDLOJA
                              AND NRMESA   = :NRMESA
                            UNION
                           SELECT NRMESA
                             FROM JUNCAOMESA JM, MESAJUNCAO MJ
                            WHERE JM.CDFILIAL  = :CDFILIAL
                              AND JM.CDFILIAL  = MJ.CDFILIAL
                              AND JM.CDLOJA    = :CDLOJA
                              AND JM.NRJUNMESA = MJ.NRJUNMESA
                              AND JM.CDLOJA    = MJ.CDLOJA
                              AND JM.NRJUNMESA = (SELECT NRJUNMESA
                                                    FROM MESAJUNCAO
                                                   WHERE CDFILIAL = :CDFILIAL
                                                     AND CDLOJA   = :CDLOJA
                                                     AND NRMESA   = :NRMESA))
    ";

    const SQL_PRODUTOS_LOJA = "
        SELECT CDSETOR
          FROM PRODLOJA
          WHERE CDFILIAL  = ?
            AND CDLOJA    = ?
            AND CDPRODUTO = ?
    ";

    const SQL_UPDATE_MOVCAIXAMOB = "
        UPDATE MOVCAIXAMOB
           SET DTHRFIMMOV = GETDATE(),
               NRSEQMOB = :NRSEQMOB,
               NRADMCODE = :NRADMCODE,
               IDADMTASK = :IDADMTASK,
               IDSTMOV = :IDSTMOV,
               TXMOVUSUARIO = :TXMOVUSUARIO,
               TXMOVJSON = :TXMOVJSON,
               CDNSUTEFMOB = :CDNSUTEFMOB,
               TXPRIMVIATEF = REPLACE(CAST(:TXPRIMVIATEF AS VARCHAR(MAX)), '\r\n', CHAR(10)),
               TXSEGVIATEF = REPLACE(CAST(:TXSEGVIATEF AS VARCHAR(MAX)), '\r\n', CHAR(10))
         WHERE NRSEQMOVMOB = :NRSEQMOVMOB
    ";

    const SQL_BUSCA_TRANSACOES = "
        SELECT *
         FROM MOVCAIXAMOB
        WHERE NRSEQMOVMOB = ?
    ";

    const SQL_UPDATE_DSBANDEIRA = "
        UPDATE MOVCAIXAMOB
           SET DSBANDEIRA = ?
         WHERE NRSEQMOVMOB = ?
    ";

    const SQL_GET_TIPORECE = "
        SELECT CDTIPORECE
          FROM TIPORECE
         WHERE CDBANCARTCR = ?
    ";

    const SQL_UPDATE_MOVCAIXAMOB_CDTIPORECE = "
        UPDATE MOVCAIXAMOB
           SET CDTIPORECE = ?
         WHERE NRSEQMOVMOB = ?
    ";

    const SQL_INSERT_MOVCAIXAMOB = "
        INSERT INTO MOVCAIXAMOB(
            CDFILIAL, CDCAIXA, CDVENDEDOR, NRVENDAREST, NRCOMANDA, NRSEQVENDA, NRMESA, NRLUGARMESA, NRSEQMOVMOB, DTHRINCMOV,
            VRMOV, IDTIPMOV, DSBANDEIRA, NRADMCODE, IDADMTASK, NRORG, IDATIVO, DTINCLUSAO, NRORGINCLUSAO, CDOPERINCLUSAO,
            CDTIPORECE, CDNSUTEFMOB, TXPRIMVIATEF, TXSEGVIATEF, IDTPTEF, CDCLIENTE, CDCONSUMIDOR, IDSTMOV, NRCARTBANCO, IDTIPORECE)
        VALUES
           (:CDFILIAL, :CDCAIXA, :CDVENDEDOR, :NRVENDAREST, :NRCOMANDA, :NRSEQVENDA, :NRMESA, :NRLUGARMESA, :NRSEQMOVMOB, :DTHRINCMOV,
            :VRMOV, :IDTIPMOV, :DSBANDEIRA, :NRADMCODE, :IDADMTASK, :NRORG, :IDATIVO, :DTINCLUSAO, :NRORGINCLUSAO, :CDOPERINCLUSAO,
            :CDTIPORECE, :CDNSUTEFMOB, :TXPRIMVIATEF, :TXSEGVIATEF, :IDTPTEF, :CDCLIENTE, :CDCONSUMIDOR, :IDSTMOV, :NRCARTBANCO, :IDTIPORECE)
    ";

    const SQL_PROD_LOJA = "
        SELECT CDSETOR
          FROM PRODLOJA
         WHERE CDFILIAL  = ?
           AND CDLOJA    = ?
           AND CDPRODUTO = ?
    ";

    const SQL_INSERE_ITEM_COMANDA_VEN = "
        INSERT
          INTO ITCOMANDAVEN
               (CDFILIAL, NRVENDAREST, NRCOMANDA, NRPRODCOMVEN,
                CDPRODUTO, QTPRODCOMVEN, VRPRECCOMVEN, TXPRODCOMVEN,
                IDSTPRCOMVEN, VRDESCCOMVEN, NRLUGARMESA, NRMESAORIG,
                CDLOJAORIG, DTHRINCOMVEN, IDPRODIMPFIS, CDLOJA,
                NRSEQPRODCOM, NRSEQPRODCUP, VRACRCOMVEN, DSCOMANDAORI,
                NRCOMANDAORI, NRPRODCOMORI, CDCAIXACOLETOR, VRPRECCLCOMVEN,
                CDPRODPROMOCAO, CDVENDEDOR, NRPEDIDOFOS, CDFILIALPED,
                CDSENHAPED, NRATRAPRODCOVE, IDORIGPEDCMD, DSOBSPEDDIGCMD,
                IDPRODREFIL, QTITEMREFIL, IDDIVIDECONTA, CDSUPERVISOR)
        VALUES (:CDFILIAL, :NRVENDAREST, :NRCOMANDA, :NRPRODCOMVEN,
                :CDPRODUTO, :QTPRODCOMVEN, :VRPRECCOMVEN, :TXPRODCOMVEN,
                :IDSTPRCOMVEN, :VRDESCCOMVEN, :NRLUGARMESA, :NRMESAORIG,
                :CDLOJAORIG, :DTHRINCOMVEN, :IDPRODIMPFIS, :CDLOJA,
                :NRSEQPRODCOM, :NRSEQPRODCUP, :VRACRCOMVEN, :DSCOMANDAORI,
                :NRCOMANDAORI, :NRPRODCOMORI, :CDCAIXACOLETOR, :VRPRECCLCOMVEN,
                :CDPRODPROMOCAO, :CDVENDEDOR, :NRPEDIDOFOS, :CDFILIALPED,
                :CDSENHAPED, :NRATRAPRODCOVE, :IDORIGPEDCMD, :DSOBSPEDDIGCMD,
                :IDPRODREFIL, :QTITEMREFIL, :IDDIVIDECONTA, :CDSUPERVISOR)
    ";

    const SQL_GET_NRPEDIDOFOS = "
        SELECT CDFILIAL, NRPEDIDOFOS
          FROM ITPEDIDOFOSREL
         WHERE CDFILIAL = ?
           AND NRVENDAREST = ?
           AND NRCOMANDA = ?
           AND NRPRODCOMVEN = ?
    ";

    const SQL_STATUS_MESA = "
        SELECT VD.CDFILIAL, VD.CDLOJA, VD.NRMESA, VD.NRVENDAREST,
               VD.CDVENDEDOR, VD.NRPESMESAVEN, VD.DTHRABERMESA, VD.DTHRFECHMESA,
               VD.CDOPERADOR, CO.NRCOMANDA, CO.DSCOMANDA
          FROM VENDAREST VD, MESA MS, COMANDAVEN CO
         WHERE VD.CDFILIAL     = ?
           AND VD.CDLOJA       = ?
           AND VD.NRMESA       = ?
           AND MS.NRMESA       = VD.NRMESA
           AND MS.CDFILIAL     = VD.CDFILIAL
           AND MS.CDLOJA       = VD.CDLOJA
           AND (MS.IDSTMESAAUX = 'O' OR MS.IDSTMESAAUX = 'D')
           AND VD.DTHRFECHMESA IS NULL
           AND VD.CDFILIAL     = CO.CDFILIAL
           AND VD.NRVENDAREST  = CO.NRVENDAREST
    ";

    const SQL_PESQUISA_ITENS_COMANDA = "
        SELECT CDFILIAL, NRVENDAREST, NRCOMANDA, NRPRODCOMVEN,
               CDPRODUTO, QTPRODCOMVEN, VRPRECCOMVEN, TXPRODCOMVEN,
               IDSTPRCOMVEN, VRDESCCOMVEN, NRLUGARMESA, CDGRPOCOR,
               CDOCORR, NRMESAORIG, CDLOJAORIG, DTHRINCOMVEN,
               IDPRODIMPFIS, CDLOJA, NRSEQPRODCOM, NRSEQPRODCUP,
               VRACRCOMVEN, DSCOMANDAORI, NRCOMANDAORI, NRPRODCOMORI,
               CDCAIXACOLETOR, VRPRECCLCOMVEN, NRMESADSCOMORIT, CDVENDEDOR,
               CDPRODPROMOCAO, NRPEDIDOFOS, CDSENHAPED, CDSUPERVISOR,
               NRATRAPRODCOVE, IDORIGPEDCMD, DSOBSPEDDIGCMD,
               IDPRODREFIL, QTITEMREFIL, IDDIVIDECONTA
          FROM ITCOMANDAVEN
         WHERE CDFILIAL    = ?
           AND NRVENDAREST = ?
           AND NRCOMANDA   = ?
    ";

    const SQL_DELETA_ITENS_COMANDA_ORIGEM = "
        DELETE
          FROM ITCOMANDAVEN
         WHERE CDFILIAL    = ?
           AND NRVENDAREST = ?
    ";

    const SQL_DELETA_COMANDA_ORIGEM = "
        DELETE
          FROM COMANDAVEN
         WHERE CDFILIAL    = ?
           AND CDLOJA      = ?
           AND NRVENDAREST = ?
    ";

    const SQL_DELETA_VENDA_ORIGEM = "
        DELETE
          FROM VENDAREST
         WHERE CDFILIAL    = ?
           AND CDLOJA      = ?
           AND NRVENDAREST = ?
    ";

    const SQL_UPDATE_MESAS = "
        UPDATE MESA
           SET IDSTMESAAUX = :IDSTMESAAUX
         WHERE CDFILIAL    = :CDFILIAL
           AND CDLOJA      = :CDLOJA
           AND NRMESA      = :NRMESA
    ";

    CONST SQL_DELETA_MESA_JUNCAO = "
        DELETE
          FROM MESAJUNCAO
         WHERE CDFILIAL  = ?
           AND CDLOJA    = ?
           AND NRJUNMESA = ?
    ";

    CONST SQL_DELETA_JUNCAO_MESA = "
        DELETE
          FROM JUNCAOMESA
         WHERE CDFILIAL  = ?
           AND CDLOJA    = ?
           AND NRJUNMESA = ?
    ";

    const SQL_UPDATE_ITPEDIDOFOSREL = "
        UPDATE ITPEDIDOFOSREL
           SET NRVENDAREST = ?,
               NRCOMANDA = ?,
               NRPRODCOMVEN = ?
         WHERE CDFILIAL = ?
           AND NRVENDAREST = ?
           AND NRCOMANDA = ?
           AND NRPRODCOMVEN = ?
    ";

    const SQL_UPDATE_PEDIDOFOS = "
        UPDATE PEDIDOFOS
           SET NRMESA = ?,
               NRMESAOLD = ?
         WHERE CDFILIAL = ?
           AND NRPEDIDOFOS = ?
    ";

    const SQL_GET_MAIN_PRODUCT = "
        SELECT I.CDFILIAL, I.NRPEDIDOFOS, I.NRITPEDIDOFOS, ISNULL(I.NRTEMPOPRODIT, 0) AS NRTEMPOPRODIT,
               I.NRATRAPRODITPE, I.DTHREXIBICAOPROD AS DTHREXIBATRASO, S.IDTIPOSETOR
          FROM ITPEDIDOFOS I JOIN SETOR S
                               ON I.CDSETOR = S.CDSETOR
         WHERE I.CDFILIAL = ?
           AND I.NRPEDIDOFOS = ?
           AND I.NRITPEDIDOFOS = ?
    ";

    const SQL_GET_PRODUCT_COMPOSITION = "
        SELECT I.CDFILIAL, I.NRPEDIDOFOS, I.NRITPEDIDOFOS, ISNULL(I.NRTEMPOPRODIT, 0) AS NRTEMPOPRODIT,
               I.NRATRAPRODITPE, I.DTHREXIBICAOPROD AS DTHREXIBATRASO, S.IDTIPOSETOR
          FROM ITPEDIDOFOSPAI P JOIN ITPEDIDOFOS I
                                  ON P.CDFILIAL = I.CDFILIAL
                                 AND P.NRPEDIDOFOS = I.NRPEDIDOFOS
                                 AND P.NRITPEDIDOFOS = I.NRITPEDIDOFOS
                                JOIN SETOR S
                                  ON I.CDSETOR = S.CDSETOR
         WHERE P.CDFILIALPAI = ?
           AND P.NRPEDIDOFOSPAI = ?
           AND P.NRITPEDIDOFOSPAI = ?
    ";

    const SQL_ACESSOS = "
        SELECT AF.NRACESSOUSER, AF.NRMESA, AF.NMUSUARIO, AF.IDACESSOUSER,
               '' AS VAZIO, ME.NMMESA, (AF.NMUSUARIO + ' na mesa ') AS USERMESA,
               DATEDIFF ( second , AF.DTULTATU , GETDATE() ) AS TEMPO
          FROM ACESSOFM AF, MESA ME
         WHERE AF.NRMESA = ME.NRMESA
         AND AF.IDACESSOUSER = 'P'

    ";

    const SQL_ACESSOS_AUT = "
        SELECT AF.NRACESSOUSER, AF.NRMESA, AF.NMUSUARIO, AF.IDACESSOUSER,
               '' AS VAZIO, ME.NMMESA, (AF.NMUSUARIO + ' na mesa ' + ME.NMMESA) AS USERMESA,
               DATEDIFF ( second , AF.DTULTATU , GETDATE() ) AS TEMPO
          FROM ACESSOFM AF, MESA ME
         WHERE AF.NRMESA = ME.NRMESA
         AND AF.IDACESSOUSER = 'A'

    ";

    const SQL_BUSCA_VENDEDOR_OPERADOR = "
        SELECT CDVENDEDOR, CDFILIAL, CDCAIXA, NMFANVEN
          FROM VENDEDOR
         WHERE CDOPERADOR = ?
    ";

    const SQL_DADOS_CAIXA = "
        SELECT CX.CDFILIAL, CX.CDCAIXA, CX.CDLOJA, CX.NRCONFTELA, CX.IDLEITURAQRCODE,
               LJ.IDCOMANDAVEN, LJ.IDCOMISVENDA, LJ.VRCOMISVENDA, LJ.NRMESAPADRAO, LJ.IDINFVENDCOM,
               LJ.IDUTIVENDPAD, LJ.CDVENDPADRAO, LJ.IDCOUVERART, LJ.CDPRODCONSUM, LJ.CDPRODCONSUF,
               LJ.IDRATEIOCOMI, LJ.CDCLIENTERAT, LJ.NRMINSEMCONS, LJ.CDPRODCOUVER,
               LJ.IDCONSUMAMIN, LJ.IDLUGARMESA, LJ.IDCONTROPROD, LJ.CDPRODCOMVEN,
               LJ.IDSOLOBSPED, LJ.IDSOLOBSCAN, LJ.CDGRPOCORCAN, LJ.CDGRPOCORPED,
               CX.IDDIGCODPLM, LJ.NMLOJA, LJ.IDINFMESACOM, CX.IDPERABERCOMCXA,
               CX.IDTIPOIMPNF, LJ.IDCOMANDAAUT, LJ.IDINFCONSCOM, LJ.IDUTLSENHAOPER,
               LJ.IDPOSOBSPED, CX.CDPORTAIMPNF, CX.CDPORTAIMPNF2, CX.IDTIPOIMPNF2,
               CX.NRSEQIMPRLOJA1, CX.NRSEQIMPRLOJA2, LJ.IDBLOQCOMPARC, CX.IDTPTEF,
               ISNULL(CX.CDFILICONFTE, CX.CDFILIAL) AS CDFILICONFTE, CX.IDCOLETOR,
               CX.NRORG, CX.IDTPEMISSAOFOS, CX.IDUTILTEF, CX.IDPALFUTRABRCXA,
               CONVERT(FLOAT, CX.VRABERCAIX) AS VRABERCAIX, CX.IDHABCAIXAVENDA, LJ.IDTIPCOBRA,
               IM.IDMODEIMPRES, CX.CDTERTEF, CX.CDLOJATEF, CX.DSENDIPSITEF, CX.IDSOLDIGCONS,
               LJ.IDUTLNMCONSMESA, LJ.IDAGRUPAPEDCOM, CX.IDTPTELAVE, CX.IDSOLICITACPF,
               CX.IDLCDBARBALATOL, CX.NRPOSINICODBARR, CX.NRPOSFINCODBARR, LJ.VRCOMISVENDA2,
               LJ.VRCOMISVENDA3, CX.IDPERCOMVENCPDC, LJ.IDSOLOBSDESC, CX.IDSOLTPSANGRIACX,
               CX.IDCAIXAEXCLUSIVO, LJ.IDMOSTRADESPARC, LJ.IDSOLOBSFINVEN, LJ.VRMAXDESCONTO,
               LJ.IDSOLOBSCAN, CX.IDIMPPEDPROD, CX.IDSINCCAIXADLV, CX.IDUTCXDRIVETHU,
               CX.IDSENHACUP
          FROM LOJA LJ,
               CAIXA CX LEFT JOIN IMPRLOJA IL
                               ON CX.CDFILIAL = IL.CDFILIAL
                              AND CX.CDLOJA = IL.CDLOJA
                              AND CX.NRSEQIMPRLOJA3 = IL.NRSEQIMPRLOJA
                        LEFT JOIN IMPRESSORA IM
                               ON IL.CDIMPRESSORA = IM.CDIMPRESSORA
          WHERE CX.CDFILIAL = ?
            AND CX.CDCAIXA  = ?
            AND CX.CDFILIAL = LJ.CDFILIAL
            AND CX.CDLOJA   = LJ.CDLOJA
    ";

    const SQL_BD_VERSION = "
        SELECT @@VERSION
    ";

    const SQL_FILIAL_DETAILS = "
        SELECT P.NRATRAPADRAO, P.IDUTLQTDPED, P.QTDMAXDIGNSU, P.IDSOLICITANSU,
               P.IDPERDIGCONS, P.CDCLIENTE, P.IDAMBTRABNFCE, F.NRINSJURFILI, P.IDCONSUBDESFOL,
               P.IDUTLSSL, P.IDEXTCONSONLINE, P.IDCTRLPEDVIAGEM, P.IDCONDESCTXSERV,
               P.CDURLWSEXTCONS, CASE WHEN P.NRMAXPESMES IS NULL THEN '99' ELSE P.NRMAXPESMES END NRMAXPESMES
          FROM PARAVEND P, FILIAL F
         WHERE P.CDFILIAL = F.CDFILIAL
            AND P.CDFILIAL = :CDFILIAL
    ";

    const SECRET_QUERY = "
        SELECT CA.IDHABCAIXAVENDA, CA.CDFILIAL, VE.CDCAIXA ,O.CDOPERADOR, VE.CDVENDEDOR, O.CDSENHOPER, VE.CDCAIXA,O.CDSENHAOPERWEB
          FROM VENDEDOR VE JOIN OPERADOR O
                         ON O.CDOPERADOR = VE.CDOPERADOR
                         JOIN CAIXA CA
                         ON CA.CDCAIXA = VE.CDCAIXA
                         JOIN FILIAL F ON VE.CDFILIAL = F.CDFILIAL
                       JOIN OPERGRUPOP OP
                         ON OP.CDOPERADOR = O.CDOPERADOR
                       JOIN GRUPOPER GP
                         ON GP.CDGRUPOPER = OP.CDGRUPOPER
       ORDER BY CA.IDHABCAIXAVENDA
    ";

    const GET_ESITEF_DETAILS = "
        SELECT IDLOJAESITEF, CDLOJAESITEF
          FROM PARAMGERAL
    ";

    const SQL_VALIDA_COMANDA = "
        SELECT C.DSCOMANDA, C.NRVENDAREST, C.NRCOMANDA, C.IDSTCOMANDA,
            V.CDLOJA, V.NRMESA, C.VRDESCOMANDA, C.VRCOMISVENDE,
            C.VRACRCOMANDA, V.CDVENDEDOR, V.CDCLIENTE
          FROM COMANDAVEN C, VENDAREST V
          WHERE C.CDFILIAL    = ?
            AND C.NRCOMANDA   = ?
            AND C.NRVENDAREST = ?
            AND C.CDFILIAL    = V.CDFILIAL
            AND C.NRVENDAREST = V.NRVENDAREST
            AND C.CDLOJA      = V.CDLOJA
            AND C.IDSTCOMANDA <> '4'
    ";

    const UPDATE_COMISSAO_VENDA = "
        UPDATE COMANDAVEN
          SET VRCOMISVENDE   = ?
          WHERE CDFILIAL     = ?
            AND NRVENDAREST  = ?
            AND NRCOMANDA    = ?
    ";

    const SQL_TEMPO_PERMANENCIA = "
        SELECT CONVERT(VARCHAR,VE.DTHRABERMESA,21) AS DATA
          FROM VENDAREST VE, COMANDAVEN CM
          WHERE CM.CDFILIAL    = ?
            AND CM.NRVENDAREST = ?
            AND CM.NRCOMANDA   = ?
            AND VE.CDFILIAL    = CM.CDFILIAL
            AND VE.NRVENDAREST = CM.NRVENDAREST
    ";

    const SQL_PRODUTOS_PARCIAL = "
        SELECT IT.NRVENDAREST, IT.NRCOMANDA, MIN(IT.NRPRODCOMVEN) AS NRPRODCOMVEN, SUM(IT.QTPRODCOMVEN) AS QTPRODCOMVEN,
               SUM(IT.VRDESCCOMVEN) AS VRDESCCOMVEN, ISNULL(PR.NMPRODIMPFIS, PR.NMPRODUTO) AS NMPRODUTO,
               IT.VRPRECCOMVEN, IT.VRPRECCLCOMVEN, PR.CDARVPROD, IT.CDPRODUTO, IT.IDSTPRCOMVEN, IT.NRMESADSCOMORIT,
               SUM(VRACRCOMVEN) AS VRACRCOMVEN, PR.IDCOBTXSERV, ISNULL(IT.NRLUGARMESA, '001') AS NRLUGARMESA, V.DTHRABERMESA,
               PR.CDBARPRODUTO AS CDPRODIMPFIS, ISNULL(VE.VRPERCCOMISVEND, 0) AS VRPERCCOMISVEND, VE.IDVRINCIDECONTA,
               SUM((IT.VRPRECCOMVEN + IT.VRPRECCLCOMVEN) * IT.QTPRODCOMVEN + IT.VRACRCOMVEN - IT.VRDESCCOMVEN) AS PRECO, PR.IDPESAPROD,
               IT.CDPRODPROMOCAO, IT.NRSEQPRODCOM, IT.IDDESCMANUAL
          FROM ITCOMANDAVEN IT
          JOIN PRODUTO PR ON PR.CDPRODUTO = IT.CDPRODUTO
          JOIN VENDAREST V ON V.CDFILIAL = IT.CDFILIAL
                          AND V.NRVENDAREST = IT.NRVENDAREST
          LEFT JOIN VENDEDOR VE ON VE.CDVENDEDOR = IT.CDVENDEDOR
         WHERE (IT.CDFILIAL = ?)
           AND (CHARINDEX(IT.NRCOMANDA, ?) > 0)
           AND (IT.IDSTPRCOMVEN <> '6' AND IT.IDSTPRCOMVEN <> '7')
           AND (IT.CDPRODUTO <> 'X')
           AND (PR.IDPESAPROD = 'N')
           AND ((charindex(IT.NRLUGARMESA, ?) > 0) OR ('T' = ?))
           AND IT.IDPRODREFIL = 'N'
         GROUP BY IT.NRVENDAREST, IT.NRCOMANDA, ISNULL(PR.NMPRODIMPFIS, PR.NMPRODUTO), IT.VRPRECCOMVEN, IT.VRPRECCLCOMVEN,
                  PR.CDARVPROD, IT.CDPRODUTO, IT.IDSTPRCOMVEN, IT.NRMESADSCOMORIT, PR.IDCOBTXSERV, IT.CDPRODPROMOCAO,
                  IT.NRLUGARMESA, V.DTHRABERMESA, PR.CDBARPRODUTO, IT.NRPRODCOMVEN, IT.QTPRODCOMVEN,
                  IT.VRDESCCOMVEN, IT.VRACRCOMVEN, VRPERCCOMISVEND, VE.IDVRINCIDECONTA, PR.IDPESAPROD,
                  IT.CDPRODPROMOCAO, IT.NRSEQPRODCOM, IT.IDDESCMANUAL
         UNION
        SELECT IT.NRVENDAREST, IT.NRCOMANDA, IT.NRPRODCOMVEN AS NRPRODCOMVEN, IT.QTPRODCOMVEN AS QTPRODCOMVEN,
               IT.VRDESCCOMVEN AS VRDESCCOMVEN, ISNULL(PR.NMPRODIMPFIS, PR.NMPRODUTO) AS NMPRODUTO,
               IT.VRPRECCOMVEN, IT.VRPRECCLCOMVEN, PR.CDARVPROD, IT.CDPRODUTO, IT.IDSTPRCOMVEN, IT.NRMESADSCOMORIT,
               VRACRCOMVEN AS VRACRCOMVEN, PR.IDCOBTXSERV, ISNULL(IT.NRLUGARMESA, '001') AS NRLUGARMESA, V.DTHRABERMESA,
               PR.CDBARPRODUTO AS CDPRODIMPFIS, ISNULL(VE.VRPERCCOMISVEND, 0) AS VRPERCCOMISVEND, VE.IDVRINCIDECONTA,
               SUM((IT.VRPRECCOMVEN + IT.VRPRECCLCOMVEN) * IT.QTPRODCOMVEN + IT.VRACRCOMVEN - IT.VRDESCCOMVEN) AS PRECO, PR.IDPESAPROD,
               IT.CDPRODPROMOCAO, IT.NRSEQPRODCOM, IT.IDDESCMANUAL
          FROM ITCOMANDAVEN IT
          JOIN PRODUTO PR ON PR.CDPRODUTO = IT.CDPRODUTO
          JOIN VENDAREST V ON V.CDFILIAL = IT.CDFILIAL
                          AND V.NRVENDAREST = IT.NRVENDAREST
          LEFT JOIN VENDEDOR VE ON VE.CDVENDEDOR = IT.CDVENDEDOR
         WHERE (IT.CDFILIAL = ?)
           AND (CHARINDEX(IT.NRCOMANDA, ?) > 0)
           AND (IT.IDSTPRCOMVEN <> '6' AND IT.IDSTPRCOMVEN <> '7')
           AND (IT.CDPRODUTO <> 'X')
           AND (PR.IDPESAPROD = 'S')
           AND ((charindex(IT.NRLUGARMESA, ?) > 0) OR ('T' = ?))
           AND IT.IDPRODREFIL = 'N'
         GROUP BY IT.NRVENDAREST, IT.NRCOMANDA, ISNULL(PR.NMPRODIMPFIS, PR.NMPRODUTO), IT.VRPRECCOMVEN, IT.VRPRECCLCOMVEN,
                  PR.CDARVPROD, IT.CDPRODUTO, IT.IDSTPRCOMVEN, IT.NRMESADSCOMORIT, PR.IDCOBTXSERV, IT.CDPRODPROMOCAO,
                  IT.NRLUGARMESA, V.DTHRABERMESA, PR.CDBARPRODUTO, IT.NRPRODCOMVEN, IT.QTPRODCOMVEN,
                  IT.VRDESCCOMVEN, IT.VRACRCOMVEN, VRPERCCOMISVEND, VE.IDVRINCIDECONTA, PR.IDPESAPROD,
                  IT.CDPRODPROMOCAO, IT.NRSEQPRODCOM, IT.IDDESCMANUAL
         ORDER BY NRLUGARMESA, IT.NRVENDAREST, IT.NRCOMANDA, NRPRODCOMVEN, QTPRODCOMVEN,
                  VRDESCCOMVEN, NMPRODUTO, IT.VRPRECCOMVEN, PR.CDARVPROD, IT.CDPRODUTO, IT.IDSTPRCOMVEN,
                  IT.NRMESADSCOMORIT, VRACRCOMVEN, PR.IDCOBTXSERV, V.DTHRABERMESA, CDPRODIMPFIS, PRECO
    ";

    const SQL_PRODUTOS_PARCIAL_NUMERO_PRODUTOS = "
        SELECT COUNT(NRPRODCOMVEN) AS NRITENS
          FROM ITCOMANDAVEN
         WHERE CDFILIAL = ?
           AND (CHARINDEX(NRVENDAREST, ?) > 0)
           AND (CHARINDEX(NRCOMANDA, ?) > 0)
           AND ((CHARINDEX(NRLUGARMESA, ?) > 0) OR ('T' = ?))
           AND IDSTPRCOMVEN <> '6'
           AND IDSTPRCOMVEN <> '7'
    ";

    const SQL_POSICAO_PARCIAL = "
        SELECT A.NRLUGARMESA, SUM(A.PRECOTX) AS PRECOTX, SUM(A.PRECONTX) AS PRECONTX
          FROM (
            SELECT I.NRLUGARMESA, SUM(ROUND((I.VRPRECCOMVEN + I.VRPRECCLCOMVEN) * I.QTPRODCOMVEN, 2, 1) + I.VRACRCOMVEN - I.VRDESCCOMVEN) AS PRECOTX, 0 AS PRECONTX
              FROM ITCOMANDAVEN I, PRODUTO P
              WHERE I.CDPRODUTO   = P.CDPRODUTO
                AND I.CDFILIAL    = :CDFILIAL
                AND (CHARINDEX(I.NRVENDAREST, :NRVENDAREST) > 0)
                AND (CHARINDEX(I.NRCOMANDA, :NRCOMANDA) > 0)
                AND I.IDSTPRCOMVEN <> '6'
                AND I.IDSTPRCOMVEN <> '7'
                AND ((I.CDPRODUTO <> :CDPRODUTO) OR (:CDPRODUTO IS NULL))
                AND P.IDCOBTXSERV = 'S'
            GROUP BY I.NRLUGARMESA
            UNION ALL
            SELECT I.NRLUGARMESA, 0 AS PRECOTX, SUM(ROUND((I.VRPRECCOMVEN + I.VRPRECCLCOMVEN) * I.QTPRODCOMVEN, 2, 1) + I.VRACRCOMVEN - I.VRDESCCOMVEN) AS PRECONTX
              FROM ITCOMANDAVEN I, PRODUTO P
              WHERE I.CDPRODUTO   = P.CDPRODUTO
                AND I.CDFILIAL    = :CDFILIAL
                AND (CHARINDEX(I.NRVENDAREST, :NRVENDAREST) > 0)
                AND (CHARINDEX(I.NRCOMANDA, :NRCOMANDA) > 0)
                AND I.IDSTPRCOMVEN <> '6'
                AND I.IDSTPRCOMVEN <> '7'
                AND ((I.CDPRODUTO <> :CDPRODUTO) OR (:CDPRODUTO IS NULL))
                AND P.IDCOBTXSERV = 'N'
            GROUP BY I.NRLUGARMESA
        ) A
        GROUP BY A.NRLUGARMESA
    ";

    const SQL_VAL_IMPRE_LOJA = "
        SELECT I.IDMODEIMPRES, L.CDPORTAIMPR, L.NRSEQIMPRLOJA, M.DSENDPORTA,
               L.DSIPIMPR, L.DSIPPONTE, L.NMIMPRLOJA
          FROM IMPRESSORA I,
               IMPRLOJA L LEFT JOIN MAPIMPRLOJA M
                                 ON L.CDFILIAL = M.CDFILIAL
                                AND L.CDLOJA = M.CDLOJA
                                AND L.CDPORTAIMPR = M.CDPORTAIMPR
          WHERE L.CDFILIAL      = ?
            AND L.CDLOJA        = ?
            AND L.NRSEQIMPRLOJA = ?
            AND L.CDIMPRESSORA  = I.CDIMPRESSORA
    ";

    const SQL_GET_MONEY_CURRENCY = "
        SELECT T.CDTIPORECE
          FROM ITMENUCONFTE I, TIPORECE T
         WHERE I.CDFILIAL = :CDFILIAL
           AND I.NRCONFTELA = :NRCONFTELA
           AND I.CDIDENTBUTON = T.CDTIPORECE
           AND T.IDTIPORECE = '4'
    ";

    const SQL_GET_OBS_TYPE = "
        SELECT OC.IDCONTROLAOBS, OC.CDPRODUTO
          FROM OCORRENCIA OC JOIN LOJA LJ
                               ON OC.CDGRPOCOR = LJ.CDGRPOCORPED
        WHERE LJ.CDFILIAL = ?
          AND LJ.CDLOJA = ?
          AND OC.CDOCORR = ?
    ";

    const SQL_BUSCA_VENDEDOR = "
        SELECT V.CDVENDEDOR
          FROM VENDAREST V, COMANDAVEN C
         WHERE V.NRVENDAREST = V.NRVENDAREST
           AND V.CDFILIAL = ?
           AND C.NRCOMANDA   = ?
           AND V.NRVENDAREST = ?
    ";

    const SQL_VALIDA_PROD = "
        SELECT PR.CDARVPROD, PR.NMPRODUTO, PR.IDPESAPROD, PR.IDTIPOCOMPPROD,
               PR.IDIMPPRODUTO, PR.IDCONTROLAREFIL
          FROM PRODUTO PR
          LEFT JOIN ITMENUCONFTE IM
            ON PR.CDPRODUTO  = IM.CDIDENTBUTON
         WHERE PR.CDPRODUTO  = ?
    ";

    const SQL_GET_PRODUTO = "
        SELECT CDPRODUTO, NMPRODUTO, CDARVPROD, IDTIPOCOMPPROD
          FROM PRODUTO
         WHERE CDPRODUTO = ?
    ";

    const SQL_INS_ITCOMANDAVEN = "
        INSERT INTO ITCOMANDAVEN
			(CDFILIAL, NRVENDAREST, NRCOMANDA, NRPRODCOMVEN,
			CDPRODUTO, QTPRODCOMVEN, VRPRECCOMVEN, TXPRODCOMVEN,
			IDSTPRCOMVEN, VRDESCCOMVEN, NRLUGARMESA, DTHRINCOMVEN,
			IDPRODIMPFIS, CDLOJA, VRACRCOMVEN, NRSEQPRODCOM,
			NRSEQPRODCUP, VRPRECCLCOMVEN, CDCAIXACOLETOR, CDPRODPROMOCAO,
			CDVENDEDOR, CDSENHAPED, NRATRAPRODCOVE, IDORIGEMVENDA,
			IDORIGPEDCMD, DSOBSPEDDIGCMD, IDPRODREFIL, QTITEMREFIL, NRORG)
        VALUES
        	(:CDFILIAL, :NRVENDAREST, :NRCOMANDA, :NRPRODCOMVEN,
			:CDPRODUTO, :QTPRODCOMVEN, :VRPRECCOMVEN, :TXPRODCOMVEN,
			:IDSTPRCOMVEN, :VRDESCCOMVEN, :NRLUGARMESA, :DTHRINCOMVEN,
			:IDPRODIMPFIS, :CDLOJA, :VRACRCOMVEN, :NRSEQPRODCOM,
			:NRSEQPRODCUP, :VRPRECCLCOMVEN, :CDCAIXACOLETOR, :CDPRODPROMOCAO,
			:CDVENDEDOR, :CDSENHAPED, :NRATRAPRODCOVE, :IDORIGEMVENDA,
			:IDORIGPEDCMD, :DSOBSPEDDIGCMD, :IDPRODREFIL, :QTITEMREFIL, :NRORG)
    ";

    const SQL_INS_ITCOMANDAEST = "
        INSERT
          INTO ITCOMANDAEST
               (CDFILIAL, NRVENDAREST, NRCOMANDA, NRPRODCOMVEN,
               CDPRODUTO, QTPROCOMEST, VRPRECCOMEST, VRDESITCOMEST,
               TXPRODCOMVENEST, NRATRAPRODCOES, DSOBSPEDDIGEST)
        VALUES (?, ?, ?, ?,
                ?, ?, ?, ?,
                ?, ?, ?)
    ";

    const SQL_ACR_COMANDA_VEN = "
        SELECT CDFILIAL, NRCOMANDA, VRACRCOMANDA
          FROM COMANDAVEN
         WHERE CDFILIAL    = ?
           AND NRCOMANDA   = ?
           AND NRVENDAREST = ?
    ";

    const SQL_GET_ALIQUOTA = "
        SELECT COUNT(*) AS COUNT
          FROM ALIQIMPFIS
         WHERE CDFILIAL  = ?
           AND CDPRODUTO = ?
    ";

    const SQL_CHECK_ORDERCODE = "
        SELECT CDORDERWAITER, CAST(CAST(DSOPERACAO AS VARBINARY(MAX)) AS VARCHAR(MAX)) AS DSOPERACAO, IDSTORDER, DTHRINCREQ
          FROM WAITERORDERS
         WHERE CDFILIAL = :CDFILIAL
           AND NRVENDAREST = :NRVENDAREST
           AND NRCOMANDA = :NRCOMANDA
           AND CDORDERWAITER = :CDORDERWAITER
    ";

    const SQL_UPDATE_WAITER_ORDERS = "
        UPDATE WAITERORDERS
           SET IDSTORDER = :IDSTORDER,
               DSOPERACAO = :DSOPERACAO
         WHERE CDFILIAL = :CDFILIAL
           AND NRVENDAREST = :NRVENDAREST
           AND NRCOMANDA = :NRCOMANDA
           AND CDORDERWAITER = :CDORDERWAITER
    ";

    const SQL_INSERE_WAITER_ORDERS = "
        INSERT INTO WAITERORDERS
        (CDFILIAL, NRVENDAREST, NRCOMANDA, CDORDERWAITER,
        IDSTORDER, DSOPERACAO, DTHRINCREQ)
        VALUES
        (:CDFILIAL, :NRVENDAREST, :NRCOMANDA, :CDORDERWAITER,
        :IDSTORDER, :DSOPERACAO, :DTHRINCREQ)
    ";

    const SQL_BUSCA_PRODBLOQ = "
        SELECT CDPRODUTO
          FROM PRODBLOQVND
         WHERE CDFILIAL = ?
           AND CDLOJA = ?
           AND CDPRODUTO = ?
    ";

    const SQL_GET_CDGRPOCORPED = "
        SELECT CDGRPOCORPED
          FROM LOJA
         WHERE CDFILIAL = ?
           AND CDLOJA = ?
    ";

    const SQL_INS_OBSITCOMANDAVEN = "
        INSERT INTO OBSITCOMANDAVEN
        (CDFILIAL, NRVENDAREST, NRCOMANDA, NRPRODCOMVEN,
        CDGRPOCOR, CDOCORR)
        VALUES
        (?, ?, ?, ?,
        ?, ?)
    ";

    const SQL_INS_OBSITCOMANDAEST = "
        INSERT INTO OBSITCOMANDAEST
        (CDFILIAL, NRVENDAREST, NRCOMANDA, NRPRODCOMVEN,
        CDPRODUTO, CDGRPOCOR, CDOCORR)
        VALUES
        (?, ?, ?, ?,
        ?, ?, ?)
    ";

    const SQL_CHECK_REFIL = "
        SELECT CDPRODUTO, IDPRODREFIL, QTITEMREFIL
          FROM ITCOMANDAVEN
         WHERE CDFILIAL = ?
           AND NRVENDAREST = ?
           AND NRCOMANDA = ?
           AND CDPRODUTO = ?
           AND NRLUGARMESA = ?
           AND IDPRODREFIL = 'N'
    ";

    const SQL_UPDATE_REFIL_QTTY = "
        UPDATE ITCOMANDAVEN
           SET QTITEMREFIL = :QTITEMREFIL
         WHERE CDFILIAL    = :CDFILIAL
           AND NRVENDAREST = :NRVENDAREST
           AND NRCOMANDA   = :NRCOMANDA
           AND CDPRODUTO   = :CDPRODUTO
           AND NRLUGARMESA = :NRLUGARMESA
           AND IDPRODREFIL = 'N'
    ";

    const SQL_GET_CODE = "
        SELECT IDCHECKIN
          FROM CHECKINMESPOS
         WHERE CDFILIAL = ?
           AND NRVENDAREST = ?
           AND NRCOMANDA = ?
           AND NRPOSICAO = ?
    ";

    const SQL_CHECK_CODE = "
        SELECT IDCHECKIN
          FROM CHECKINMESPOS
         WHERE CDFILIAL = ?
           AND NRVENDAREST = ?
           AND NRCOMANDA = ?
           AND NRPOSICAO = ?
           AND IDCHECKIN = ?
    ";

    const SQL_INSERT_CODE = "
        INSERT INTO CHECKINMESPOS
        (CDFILIAL, NRVENDAREST, NRCOMANDA, NRPOSICAO, IDCHECKIN)
        VALUES
        (?, ?, ?, ?, ?)
    ";

    const SQL_CHECK_FOR_REFIL = "
        SELECT CDPRODUTO
          FROM ITCOMANDAVEN
         WHERE CDFILIAL = :CDFILIAL
           AND NRVENDAREST = :NRVENDAREST
           AND NRCOMANDA = :NRCOMANDA
           AND (CDPRODUTO = :CDPRODUTO
           OR CDPRODPROMOCAO = :CDPRODUTO)
           AND NRLUGARMESA = :NRLUGARMESA
           AND IDPRODREFIL = 'N'
           AND IDSTPRCOMVEN <> 6
    ";

    const SQL_ITENS_DETALHADOS_SEM_COMBO = "
        SELECT
          ISNULL(IM.DSBUTTON, ISNULL(PR.NMPRODIMPFIS,PR.NMPRODUTO)) DSBUTTON, IT.CDFILIAL, IT.NRVENDAREST, IT.NRCOMANDA,
          MIN(IT.NRPRODCOMVEN) AS NRPRODCOMVEN, SUM(IT.QTPRODCOMVEN) AS QTPRODCOMVEN, IT.VRDESCCOMVEN AS VRDESCCOMVEN,
          IT.VRACRCOMVEN AS VRACRCOMVEN, IT.VRPRECCOMVEN AS VRPRECCOMVEN, PR.CDARVPROD, IT.CDPRODUTO, IT.IDSTPRCOMVEN,
          IT.NRMESADSCOMORIT, CONVERT(VARCHAR, IT.TXPRODCOMVEN) TXPRODCOMVEN, PR.IDCOBTXSERV,
          ISNULL(IT.NRLUGARMESA,'001') AS NRLUGARMESA, V.DTHRABERMESA, PR.CDBARPRODUTO AS CDPRODIMPFIS,
          IT. NRSEQPRODCOM, IT.NRSEQPRODCUP, IT.CDPRODPROMOCAO, GROUPS.DSBUTTON AS GRUPO,
          CONVERT(CHAR,IT.DTHRINCOMVEN,103) AS DATA, CONVERT(CHAR,IT.DTHRINCOMVEN,108) AS HORA,
          IT.VRPRECCLCOMVEN, IT.IDORIGEMVENDA
        FROM
          PRODUTO PR, VENDAREST V,
          ITCOMANDAVEN IT LEFT JOIN ITMENUCONFTE IM
                 ON (IT.CDPRODUTO = IM.CDIDENTBUTON
                    AND IT.CDFILIAL = IM.CDFILIAL
                AND IM.NRCONFTELA = ?
                AND IM.IDTPBUTTON = '1')
                          LEFT JOIN (SELECT DSBUTTON, (NRPGCONFTELA + NRBUTTON) AS CDGRUPO
                                       FROM ITMENUCONFTE
                                      WHERE IDTPBUTTON = '2'
                                        AND CDFILIAL = ?
                                        AND NRCONFTELA = ?) AS GROUPS
                                 ON ((IM.NRPGCONFTAUX + IM.NRBUTTONAUX) = GROUPS.CDGRUPO)
        WHERE (IT.CDFILIAL = ?)
          AND (CHARINDEX(IT.NRCOMANDA, ?) > 0)
          AND (IT.CDPRODUTO = PR.CDPRODUTO)
          AND (IT.IDSTPRCOMVEN <> '6' AND IT.IDSTPRCOMVEN <> '7')
          AND (IT.CDPRODUTO <> 'X')
          AND (IT.CDFILIAL    = V.CDFILIAL)
          AND (IT.NRVENDAREST = V.NRVENDAREST)
          AND (PR.IDPESAPROD = 'N')
          AND ((charindex(NRLUGARMESA, ?) > 0) or ('T' = ?))
          AND IT.CDPRODPROMOCAO IS NULL
          AND IT.IDDIVIDECONTA = 'N'
        GROUP BY
          IM.DSBUTTON, IT.CDFILIAL, IT.NRVENDAREST, IT.NRCOMANDA, ISNULL(PR.NMPRODIMPFIS,PR.NMPRODUTO),
          IT.VRDESCCOMVEN, IT.VRACRCOMVEN, IT.VRPRECCOMVEN, PR.CDARVPROD, IT.CDPRODUTO, IT.IDSTPRCOMVEN,
          IT.NRMESADSCOMORIT, PR.IDCOBTXSERV, IT.NRLUGARMESA , V.DTHRABERMESA, CONVERT(VARCHAR, IT.TXPRODCOMVEN),
          PR.CDBARPRODUTO, IT.NRPRODCOMVEN, IT.QTPRODCOMVEN, IT.VRDESCCOMVEN, IT.VRACRCOMVEN, IT. NRSEQPRODCOM,
          IT.NRSEQPRODCUP, IT.CDPRODPROMOCAO, IM.NRBUTTON, GROUPS.DSBUTTON,CONVERT(CHAR,IT.DTHRINCOMVEN,103),
          CONVERT(CHAR,IT.DTHRINCOMVEN,108), IT.VRPRECCLCOMVEN, IT.IDORIGEMVENDA

        UNION

        SELECT
          ISNULL(IM.DSBUTTON, ISNULL(PR.NMPRODIMPFIS,PR.NMPRODUTO)) DSBUTTON, IT.CDFILIAL, IT.NRVENDAREST,
          IT.NRCOMANDA, IT.NRPRODCOMVEN AS NRPRODCOMVEN, IT.QTPRODCOMVEN AS QTPRODCOMVEN, IT.VRDESCCOMVEN AS VRDESCCOMVEN,
          IT.VRACRCOMVEN AS VRACRCOMVEN, IT.VRPRECCOMVEN AS VRPRECCOMVEN, PR.CDARVPROD, IT.CDPRODUTO,
          IT.IDSTPRCOMVEN, IT.NRMESADSCOMORIT, CONVERT(VARCHAR, IT.TXPRODCOMVEN) TXPRODCOMVEN, PR.IDCOBTXSERV,
          ISNULL(IT.NRLUGARMESA,'001') AS NRLUGARMESA, V.DTHRABERMESA, PR.CDBARPRODUTO AS CDPRODIMPFIS,
          IT.NRSEQPRODCOM, IT.NRSEQPRODCUP, IT.CDPRODPROMOCAO, GROUPS.DSBUTTON AS GRUPO,
          CONVERT(CHAR,IT.DTHRINCOMVEN,103) AS DATA, CONVERT(CHAR,IT.DTHRINCOMVEN,108) AS HORA,
          IT.VRPRECCLCOMVEN, IT.IDORIGEMVENDA
        FROM
          PRODUTO PR, VENDAREST V,
          ITCOMANDAVEN IT LEFT JOIN ITMENUCONFTE IM
                 ON (IT.CDPRODUTO = IM.CDIDENTBUTON
                    AND IT.CDFILIAL = IM.CDFILIAL
                AND IM.NRCONFTELA = ?
                AND IM.IDTPBUTTON = '1')
                          LEFT JOIN (SELECT DSBUTTON, (NRPGCONFTELA + NRBUTTON) AS CDGRUPO
                                       FROM ITMENUCONFTE
                                      WHERE IDTPBUTTON = '2'
                                        AND CDFILIAL = ?
                                        AND NRCONFTELA = ?) AS GROUPS
                                 ON ((IM.NRPGCONFTAUX + IM.NRBUTTONAUX) = GROUPS.CDGRUPO)
        WHERE (IT.CDFILIAL = ?)
          AND (CHARINDEX(IT.NRCOMANDA, ?) > 0)
          AND (IT.CDPRODUTO = PR.CDPRODUTO)
          AND (IT.IDSTPRCOMVEN <> '6' AND IT.IDSTPRCOMVEN <> '7')
          AND (IT.CDPRODUTO <> 'X')
          AND (IT.CDFILIAL    = V.CDFILIAL)
          AND (IT.NRVENDAREST = V.NRVENDAREST)
          AND (PR.IDPESAPROD = 'S')
          AND ((charindex(NRLUGARMESA, ?) > 0) or ('T' = ?))
          AND ((IM.NRPGCONFTAUX + IM.NRBUTTONAUX) = GROUPS.CDGRUPO)
          AND IT.CDPRODPROMOCAO IS NULL
          AND IT.IDDIVIDECONTA = 'N'
        GROUP BY
          IM.DSBUTTON, IT.CDFILIAL, IT.NRVENDAREST, IT.NRCOMANDA, ISNULL(PR.NMPRODIMPFIS,PR.NMPRODUTO),
          IT.VRDESCCOMVEN, IT.VRACRCOMVEN, IT.VRPRECCOMVEN, PR.CDARVPROD, IT.CDPRODUTO, IT.IDSTPRCOMVEN,
          IT.NRMESADSCOMORIT, PR.IDCOBTXSERV, IT.NRLUGARMESA , V.DTHRABERMESA, CONVERT(VARCHAR, IT.TXPRODCOMVEN),
          PR.CDBARPRODUTO, IT.NRPRODCOMVEN, IT.QTPRODCOMVEN, IT.VRDESCCOMVEN, IT.VRACRCOMVEN, IT. NRSEQPRODCOM,
          IT.NRSEQPRODCUP, IT.CDPRODPROMOCAO, IM.NRBUTTON, GROUPS.DSBUTTON, CONVERT(CHAR,IT.DTHRINCOMVEN,103),
          CONVERT(CHAR,IT.DTHRINCOMVEN,108), IT.VRPRECCLCOMVEN, IT.IDORIGEMVENDA
    ";

    const SQL_ITENS_ORIGINAIS = "
        SELECT
          ISNULL(IM.DSBUTTON, ISNULL(PR.NMPRODIMPFIS,PR.NMPRODUTO)) DSBUTTON, IT.CDFILIAL, IT.NRVENDAREST, IT.NRCOMANDA,
          MIN(IT.NRPRODCOMVEN) AS NRPRODCOMVEN, SUM(IT.QTPRODCOMVEN) AS QTPRODCOMVEN, IT.VRDESCCOMVEN AS VRDESCCOMVEN,
          IT.VRACRCOMVEN AS VRACRCOMVEN, IT.VRPRECCOMVEN AS VRPRECCOMVEN, PR.CDARVPROD, IT.CDPRODUTO, IT.IDSTPRCOMVEN,
          CONVERT(VARCHAR, IT.TXPRODCOMVEN) TXPRODCOMVEN, PR.IDCOBTXSERV,
          ISNULL(IT.NRLUGARMESA,'001') AS NRLUGARMESA, V.DTHRABERMESA, PR.CDBARPRODUTO AS CDPRODIMPFIS,
          IT. NRSEQPRODCOM, IT.NRSEQPRODCUP, IT.CDPRODPROMOCAO, GROUPS.DSBUTTON AS GRUPO,
          CONVERT(CHAR,IT.DTHRINCOMVEN,103) AS DATA, CONVERT(CHAR,IT.DTHRINCOMVEN,108) AS HORA
        FROM
          PRODUTO PR, VENDAREST V,
          ITCMDVENORIG IT LEFT JOIN ITMENUCONFTE IM
                 ON (IT.CDPRODUTO = IM.CDIDENTBUTON
                    AND IT.CDFILIAL = IM.CDFILIAL
                AND IM.NRCONFTELA = ?
                AND IM.IDTPBUTTON = '1')
                          LEFT JOIN (SELECT DSBUTTON, (NRPGCONFTELA + NRBUTTON) AS CDGRUPO
                                       FROM ITMENUCONFTE
                                      WHERE IDTPBUTTON = '2'
                                        AND CDFILIAL = ?
                                        AND NRCONFTELA = ?) AS GROUPS
                                 ON ((IM.NRPGCONFTAUX + IM.NRBUTTONAUX) = GROUPS.CDGRUPO)
        WHERE IT.CDFILIAL         = ?
            AND (CHARINDEX(IT.NRCOMANDA, ?) > 0)
          AND (IT.CDPRODUTO = PR.CDPRODUTO)
          AND (IT.IDSTPRCOMVEN <> '6' AND IT.IDSTPRCOMVEN <> '7')
          AND (IT.CDPRODUTO <> 'X')
          AND (IT.CDFILIAL    = V.CDFILIAL)
          AND (IT.NRVENDAREST = V.NRVENDAREST)
          AND (PR.IDPESAPROD = 'N')
          AND IT.CDPRODPROMOCAO IS NULL
        GROUP BY
          IM.DSBUTTON, IT.CDFILIAL, IT.NRVENDAREST, IT.NRCOMANDA, ISNULL(PR.NMPRODIMPFIS,PR.NMPRODUTO),
          IT.VRDESCCOMVEN, IT.VRACRCOMVEN, IT.VRPRECCOMVEN, PR.CDARVPROD, IT.CDPRODUTO, IT.IDSTPRCOMVEN,
          PR.IDCOBTXSERV, IT.NRLUGARMESA , V.DTHRABERMESA, CONVERT(VARCHAR, IT.TXPRODCOMVEN),
          PR.CDBARPRODUTO, IT.NRPRODCOMVEN, IT.QTPRODCOMVEN, IT.VRDESCCOMVEN, IT.VRACRCOMVEN, IT. NRSEQPRODCOM,
          IT.NRSEQPRODCUP, IT.CDPRODPROMOCAO, IM.NRBUTTON, GROUPS.DSBUTTON,CONVERT(CHAR,IT.DTHRINCOMVEN,103),
          CONVERT(CHAR,IT.DTHRINCOMVEN,108)

        UNION

        SELECT
          ISNULL(IM.DSBUTTON, ISNULL(PR.NMPRODIMPFIS,PR.NMPRODUTO)) DSBUTTON, IT.CDFILIAL, IT.NRVENDAREST,
          IT.NRCOMANDA, IT.NRPRODCOMVEN AS NRPRODCOMVEN, IT.QTPRODCOMVEN AS QTPRODCOMVEN, IT.VRDESCCOMVEN AS VRDESCCOMVEN,
          IT.VRACRCOMVEN AS VRACRCOMVEN, IT.VRPRECCOMVEN AS VRPRECCOMVEN, PR.CDARVPROD, IT.CDPRODUTO,
          IT.IDSTPRCOMVEN, CONVERT(VARCHAR, IT.TXPRODCOMVEN) TXPRODCOMVEN, PR.IDCOBTXSERV,
          ISNULL(IT.NRLUGARMESA,'001') AS NRLUGARMESA, V.DTHRABERMESA, PR.CDBARPRODUTO AS CDPRODIMPFIS,
          IT.NRSEQPRODCOM, IT.NRSEQPRODCUP, IT.CDPRODPROMOCAO, GROUPS.DSBUTTON AS GRUPO,
          CONVERT(CHAR,IT.DTHRINCOMVEN,103) AS DATA, CONVERT(CHAR,IT.DTHRINCOMVEN,108) AS HORA
        FROM
          PRODUTO PR, VENDAREST V,
          ITCMDVENORIG IT LEFT JOIN ITMENUCONFTE IM
                 ON (IT.CDPRODUTO = IM.CDIDENTBUTON
                    AND IT.CDFILIAL = IM.CDFILIAL
                AND IM.NRCONFTELA = ?
                AND IM.IDTPBUTTON = '1')
                          LEFT JOIN (SELECT DSBUTTON, (NRPGCONFTELA + NRBUTTON) AS CDGRUPO
                                       FROM ITMENUCONFTE
                                      WHERE IDTPBUTTON = '2'
                                        AND CDFILIAL = ?
                                        AND NRCONFTELA = ?) AS GROUPS
                                 ON ((IM.NRPGCONFTAUX + IM.NRBUTTONAUX) = GROUPS.CDGRUPO)
        WHERE IT.CDFILIAL       = ?
          AND (CHARINDEX(IT.NRCOMANDA, ?) > 0)
          AND (IT.CDPRODUTO = PR.CDPRODUTO)
          AND (IT.IDSTPRCOMVEN <> '6' AND IT.IDSTPRCOMVEN <> '7')
          AND (IT.CDPRODUTO <> 'X')
          AND (IT.CDFILIAL    = V.CDFILIAL)
          AND (IT.NRVENDAREST = V.NRVENDAREST)
          AND (PR.IDPESAPROD = 'N')
          AND IT.CDPRODPROMOCAO IS NULL
        GROUP BY
          IM.DSBUTTON, IT.CDFILIAL, IT.NRVENDAREST, IT.NRCOMANDA, ISNULL(PR.NMPRODIMPFIS,PR.NMPRODUTO),
          IT.VRDESCCOMVEN, IT.VRACRCOMVEN, IT.VRPRECCOMVEN, PR.CDARVPROD, IT.CDPRODUTO, IT.IDSTPRCOMVEN,
          PR.IDCOBTXSERV, IT.NRLUGARMESA , V.DTHRABERMESA, CONVERT(VARCHAR, IT.TXPRODCOMVEN),
          PR.CDBARPRODUTO, IT.NRPRODCOMVEN, IT.QTPRODCOMVEN, IT.VRDESCCOMVEN, IT.VRACRCOMVEN, IT. NRSEQPRODCOM,
          IT.NRSEQPRODCUP, IT.CDPRODPROMOCAO, IM.NRBUTTON, GROUPS.DSBUTTON, CONVERT(CHAR,IT.DTHRINCOMVEN,103),
          CONVERT(CHAR,IT.DTHRINCOMVEN,108)
    ";

    const GET_REGISTER_OPENING_DATE = "
        SELECT DTABERCAIX
          FROM TURCAIXA
         WHERE CDFILIAL = :CDFILIAL
           AND CDCAIXA  = :CDCAIXA
           AND DTFECHCAIX IS NULL
    ";

    const GET_REGISTER_CLOSING_PAYMENTS = "
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
        UNION
        SELECT M.CDTIPORECE, T.NMTIPORECE, 0 AS VRMOVIVEND,
               NULL AS LABELVRMOVIVEND, T.IDSANGRIAAUTO
          FROM TIPORECE T
          JOIN MOVCAIXA M ON T.CDTIPORECE = M.CDTIPORECE
         WHERE M.CDFILIAL   = :CDFILIAL
           AND M.CDCAIXA    = :CDCAIXA
           AND M.IDTIPOMOVIVE IN ('E','T','U')
           AND T.IDSANGRIAAUTO = 'N'
           AND M.DTABERCAIX = :DTABERCAIX
         GROUP BY M.CDTIPORECE, T.NMTIPORECE, T.IDSANGRIAAUTO
    ";

    const SQL_SET_STATUS_MESA = "
        UPDATE MESA
           SET IDSTMESAAUX = 'O'
         WHERE CDFILIAL    = ?
           AND CDLOJA      = ?
           AND NRMESA IN (SELECT DISTINCT NRMESA
                             FROM MESA
                            WHERE CDFILIAL = ?
                              AND CDLOJA   = ?
                              AND NRMESA   = ?
                            UNION
                           SELECT NRMESA
                             FROM JUNCAOMESA JM, MESAJUNCAO MJ
                            WHERE JM.CDFILIAL  = ?
                              AND JM.CDFILIAL  = MJ.CDFILIAL
                              AND JM.CDLOJA    = ?
                              AND JM.NRJUNMESA = MJ.NRJUNMESA
                              AND JM.CDLOJA    = MJ.CDLOJA
                              AND JM.NRJUNMESA = (SELECT NRJUNMESA
                                                    FROM MESAJUNCAO
                                                   WHERE CDFILIAL = ?
                                                     AND CDLOJA   = ?
                                                     AND NRMESA   = ?))
    ";

    const SQL_BUSCA_DADOS = "
        SELECT C.NRCOMANDA, V.NRVENDAREST
          FROM COMANDAVEN C, VENDAREST V
         WHERE C.NRVENDAREST = V.NRVENDAREST
           AND C.CDFILIAL = V.CDFILIAL
           AND V.NRMESA = :NRMESA
           AND V.CDFILIAL = :CDFILIAL
    ";

    const SQL_ACESSO_AUT = "
        SELECT IDACESSOUSER, NRACESSOUSER
          FROM ACESSOFM
         WHERE NMUSUARIO = ?
           AND NRMESA = ?
    ";

    const SQL_INSERE_ACESSO = "
        INSERT INTO
            ACESSOFM
               (NRACESSOUSER, NRMESA, NMUSUARIO, DSIP, DTULTATU)
              VALUES
               (?, ?, ?, ?, GETDATE())
    ";

    const SQL_NRACESSOUSER = "
        SELECT NRACESSOUSER
         FROM ACESSOFM
          WHERE NRMESA = ?
           AND NMUSUARIO = ?
           AND IDACESSOUSER = ?
    ";

    const SQL_BUSCA_NOME_MESA = "
        SELECT NMMESA
          FROM MESA
         WHERE NRMESA = ?
    ";

    const SQL_UPDATE_ACESSO = "
        UPDATE ACESSOFM
          SET IDACESSOUSER = 'P',
              DTULTATU = GETDATE()
        WHERE NRACESSOUSER = ?
         AND NRMESA = ?
         AND NMUSUARIO = ?
    ";

    const CLIENTE_FILIAL = "
        SELECT CDTABEPREC, CDCFILTABPRE
          FROM CLIENFILIAL
          WHERE CDFILIAL = ?
          AND CDCLIENTE = ?";

    const TABELA_VENDA = "
        SELECT CONVERT(VARCHAR,DTINIVGPREC,21) AS DTINIVGPREC
          FROM TABEVEND
         WHERE CDFILIAL   = ?
           AND CDTABEPREC = ?
           AND (CONVERT(VARCHAR, GETDATE(), 103) BETWEEN DTINIVGPREC AND DTFINVGPREC)";

    const TIPO_CONSUMIDOR = "
        SELECT CDTIPOCONS
          FROM   CONSUMIDOR
          WHERE (CDCLIENTE    = ?)
            AND (CDCONSUMIDOR = ?)";

    const EXISTE_PRECO_CLIE = "
        SELECT CDTABEPREC, CDFILTABPREC
          FROM CLIENTE
          WHERE (CDCLIENTE = ?)";

    const TABELA_PRECO_LOJA = "
        SELECT CDTABEPREC
          FROM LOJA
          WHERE (CDFILIAL = ?)
            AND (CDLOJA   = ?)";

    const PRECO_PARAVEND = "
        SELECT CDTABEPREC, IDPRECDIFCOM
          FROM PARAVEND
          WHERE (CDFILIAL = ?)";

    const GET_TIPCOBRA = "
        SELECT IDTIPCOBRA FROM LOJA
         WHERE CDFILIAL = ?
           AND CDLOJA = ?
    ";

    const SQL_NRJUNCAOMESA = "
        SELECT NRJUNMESA
          FROM MESAJUNCAO
         WHERE CDFILIAL = ?
           AND CDLOJA   = ?
           AND NRMESA   = ?
    ";

    const SQL_DELETA_MESAJUNCAO = "
        DELETE
          FROM MESAJUNCAO
         WHERE CDFILIAL  = ?
           AND CDLOJA    = ?
           AND NRJUNMESA = ?
    ";

    const SQL_DELETA_JUNCAOMESA = "
        DELETE
          FROM JUNCAOMESA
         WHERE CDFILIAL  = ?
           AND CDLOJA    = ?
           AND NRJUNMESA = ?
    ";

    const SQL_SEPARA = "
        DELETE
          FROM MESAJUNCAO
         WHERE CDFILIAL  = ?
           AND CDLOJA    = ?
           AND NRJUNMESA = ?
           AND NRMESA    = ?
    ";

    const SQL_AGRUPADA = "
        SELECT NRJUNMESA
          FROM MESAJUNCAO
         WHERE CDFILIAL  = ?
           AND CDLOJA    = ?
           AND NRJUNMESA = ?
    ";

    const SQL_SET_TABLE = "
        UPDATE VENDAREST
           SET NRMESA = ?
         WHERE CDFILIAL = ?
           AND NRVENDAREST = ?
    ";

    const GET_CLIENTE_ALL_POSITION = "
        SELECT P.NRLUGARMESA, P.CDCLIENTE, P.CDCONSUMIDOR, P.DSCONSUMIDOR,
               C.NMRAZSOCCLIE, O.NMCONSUMIDOR, O.NRCPFRESPCON, P.NRVENDAREST
          FROM POSVENDAREST P LEFT JOIN CLIENTE C
                                     ON P.CDCLIENTE = C.CDCLIENTE
                                    AND P.NRORG = C.NRORG
                              LEFT JOIN CONSUMIDOR O
                                     ON P.CDCLIENTE = O.CDCLIENTE
                                    AND P.CDCONSUMIDOR = O.CDCONSUMIDOR
                                    AND P.NRORG = O.NRORG
         WHERE P.CDFILIAL = ?
           AND P.NRVENDAREST = ?
           AND (P.NRLUGARMESA IN (?) OR (? = 'T'))
           AND P.NRORG = ?
    ";

    const INSERT_POSVENDAREST = "
        INSERT INTO POSVENDAREST
            (CDFILIAL, NRVENDAREST, NRSEQPOS, NRORG,
            NRLUGARMESA, CDCLIENTE, CDCONSUMIDOR, DSCONSUMIDOR, VRDESCFIDPOS)
        VALUES
            (:CDFILIAL, :NRVENDAREST, :NRSEQPOS, :NRORG,
            :NRLUGARMESA, :CDCLIENTE, :CDCONSUMIDOR, :DSCONSUMIDOR, 0)
    ";

    const DELETE_POSVENDAREST = "
        DELETE FROM POSVENDAREST
        WHERE   CDFILIAL    = ?
            AND NRVENDAREST = ?
            AND NRLUGARMESA IN (?) OR (? = 'T')
            AND NRORG       = ?
    ";

    const UPDATE_POSVENDAREST = "
        UPDATE POSVENDAREST
        SET CDCLIENTE = :CDCLIENTE, CDCONSUMIDOR = :CDCONSUMIDOR, DSCONSUMIDOR = :DSCONSUMIDOR, VRDESCFIDPOS = 0
        WHERE   CDFILIAL    = :CDFILIAL
            AND NRVENDAREST = :NRVENDAREST
            AND NRLUGARMESA = :NRLUGARMESA
            AND NRORG       = :NRORG
    ";

    const TRANSFER_POSVENDAREST = "
        UPDATE POSVENDAREST
           SET NRVENDAREST = :CMDPRINCIPAL,
               NRSEQPOS    = :NRSEQPOS,
               NRLUGARMESA = :POSITION
         WHERE CDFILIAL    = :CDFILIAL
           AND NRVENDAREST = :NRVENDAREST
           AND NRLUGARMESA = :NRLUGARMESA
           AND NRORG       = :NRORG
    ";

    const SQL_BUSCA_JSON_TRANSACAO = "
       SELECT TXMOVJSON,
                NRADMCODE,
                NRSEQMOVMOB,
                DSEMAILCLI
        FROM MOVCAIXAMOB
            WHERE NRSEQMOVMOB = ?
    ";

    const SQL_UPDATE_EMAIL_CLIENTE = "
       UPDATE MOVCAIXAMOB
            SET   DSEMAILCLI = ?
        WHERE NRSEQMOVMOB = ?
    ";

    const SQL_MOVER_TRANSACOES = "
        UPDATE MOVCAIXAMOB
        SET NRLUGARMESA = ?
        WHERE CDFILIAL = ?
          AND (charindex(NRVENDAREST, ?) > 0)
          AND (charindex(NRLUGARMESA, ?) > 0)
          AND IDSTMOV = 1
          AND IDADMTASK = 0
    ";

    const SQL_UPDATE_CANCEL_TRANSACTION = "
       UPDATE MOVCAIXAMOB
            SET IDADMTASK = 1
        WHERE NRSEQMOVMOB = ?
    ";

    const SQL_BUSCA_TRANSACOES_TEMPO = "
        SELECT *
          FROM (
            SELECT D.*, ROW_NUMBER() OVER (ORDER BY DTHRFIMMOV DESC) AS N FROM (
                    SELECT CONVERT(VARCHAR(10),M.DTHRFIMMOV,103) AS DATA,
                           CONVERT(VARCHAR(5),M.DTHRFIMMOV,14) AS HORA, M.VRMOV AS VALOR,
                           I.DSBUTTON AS BANDEIRA, M.IDTIPMOV AS IDTIPORECE, M.IDTPTEF,
                           M.NRADMCODE, M.DSEMAILCLI, M.NRSEQMOVMOB,  M.DTHRFIMMOV,
                           T.CDBANCARTCR, T.IDDESABTEF, V.NRSEQVENDA, V.IDSITUVENDA
                      FROM MOVCAIXAMOB M JOIN TIPORECE T
                                           ON M.CDTIPORECE = T.CDTIPORECE
                                    LEFT JOIN ITMENUCONFTE I
                                           ON I.CDIDENTBUTON = T.CDTIPORECE
                                         JOIN CAIXA C
                                           ON C.CDCAIXA = M.CDCAIXA
                                          AND C.CDFILIAL = M.CDFILIAL
                                    LEFT JOIN VENDA V
                                           ON V.CDCAIXA = M.CDCAIXA
                                          AND V.CDFILIAL = M.CDFILIAL
                                          AND V.NRSEQVENDA = M.NRSEQVENDA
                     WHERE ((M.DTHRFIMMOV BETWEEN ? AND ?) OR M.NRADMCODE = ?)
                       AND M.IDSTMOV = 1
                       AND M.IDADMTASK = 0
                       AND M.CDFILIAL = ?
                       AND M.CDCAIXA = ?
                       AND I.NRCONFTELA = C.NRCONFTELA
                ) D
            ) A
        WHERE A.N BETWEEN ? AND ?
    ";

    const SQL_BUSCA_TRANSACOES_ENTRADA_MOVCAIXA = "
        SELECT SUM(MC.VRMOVIVEND) AS VALORPAGO
          FROM MOVCAIXA MC
          JOIN VENDA V
            ON  (MC.NRSEQVENDA   = V.NRSEQVENDA)
            AND (MC.CDFILIAL     = V.CDFILIAL)
            AND (MC.CDCAIXA      = V.CDCAIXA)
            AND (MC.NRORG        = V.NRORG)
            AND (MC.IDTIPOMOVIVE = 'E')
          JOIN VENDAREST VR
            ON  (V.CDFILIAL    = VR.CDFILIAL)
            AND (V.NRVENDAREST = VR.NRVENDAREST)
            AND (V.NRMESA      = VR.NRMESA)
            AND (V.NRORG       = VR.NRORG)
          WHERE VR.NRVENDAREST IN (:NRVENDAREST)
            AND VR.CDFILIAL    = :CDFILIAL
    ";

    const SQL_BUSCA_TRANSACOES_SAIDA_MOVCAIXA = "
        SELECT SUM(MC.VRMOVIVEND) AS VALORRETIRADA
          FROM MOVCAIXA MC
          JOIN VENDA V
            ON  (MC.NRSEQVENDA   = V.NRSEQVENDA)
            AND (MC.CDFILIAL     = V.CDFILIAL)
            AND (MC.CDCAIXA      = V.CDCAIXA)
            AND (MC.NRORG        = V.NRORG)
            AND (MC.IDTIPOMOVIVE = 'S')
          JOIN VENDAREST VR
            ON  (V.CDFILIAL    = VR.CDFILIAL)
            AND (V.NRVENDAREST = VR.NRVENDAREST)
            AND (V.NRMESA      = VR.NRMESA)
            AND (V.NRORG       = VR.NRORG)
          WHERE VR.NRVENDAREST IN (:NRVENDAREST)
            AND VR.CDFILIAL    = :CDFILIAL
    ";

    const SQL_BUSCA_TRANSACOES_MESA_MOVCAIXAMOB = "
        SELECT SUM(VRMOV) AS VALORPAGO
            FROM MOVCAIXAMOB
        WHERE CDFILIAL = :CDFILIAL
            AND NRVENDAREST IN (:NRVENDAREST)
            AND IDSTMOV = '1'
            AND IDADMTASK = '0'
    ";

    const SQL_BUSCA_TRANSACOES_POSICAO_MOVCAIXAMOB = "
        SELECT 1
          FROM MOVCAIXAMOB
         WHERE CDFILIAL = :CDFILIAL
           AND NRVENDAREST IN (:NRVENDAREST)
           AND (NRLUGARMESA IN (:NRLUGARMESA) OR NRLUGARMESA = 'T')
           AND IDSTMOV = '1'
           AND IDADMTASK = '0'
         UNION
        SELECT 1
          FROM MOVCAIXADLV
         WHERE CDFILIAL = :CDFILIAL
           AND NRVENDAREST IN (:NRVENDAREST)
    ";

    const SQL_BUSCA_TRANSACOES_MOVCAIXADLV = "
        SELECT 1
          FROM MOVCAIXADLV
         WHERE CDFILIAL = :CDFILIAL
           AND NRVENDAREST = :NRVENDAREST
    ";

    const SQL_BUSCA_PAGAMENTO_MESA = "
        SELECT COUNT(NRSEQMOVMOB) AS PAGAMENTOMESA
            FROM MOVCAIXAMOB
        WHERE CDFILIAL = ? AND NRMESA = ? AND NRLUGARMESA = ? AND NRVENDAREST = ? AND IDSTMOV = 1 AND IDADMTASK = 0
    ";

    const SQL_BUSCA_LINHA_CANCELAMENTO = "
        SELECT CDVENDEDOR,
            NRVENDAREST,
            NRCOMANDA,
            NRMESA,
            NRLUGARMESA,
            CDTIPORECE,
            IDTIPMOV,
            VRMOV,
            DSBANDEIRA,
            IDTPTEF,
            NRADMCODE,
            IDADMTASK
        FROM MOVCAIXAMOB
            WHERE CDFILIAL = ?
            AND NRSEQMOVMOB = ?
    ";

    const SQL_REBUILD_KDS = "
        UPDATE PEDIDOFOS
           SET IDREBUILD = 'S'
    ";

    const SQL_CHANGE_POSITIONS_QUANTITY = "
        UPDATE VENDAREST
           SET NRPESMESAVEN = ?,
               NRPOSICAOMESA = ?
         WHERE CDFILIAL = ?
           AND NRVENDAREST = ?
    ";

    const SQL_BUSCA_COMANDAS = "
        SELECT
      DISTINCT A.NRVENDAREST, A.NRCOMANDA, A.DSCOMANDA, A.IDSTCOMANDA,
               A.VRDESCOMANDA, A.VRCOMISVENDE, A.NRPESMESAVEN, A.CDVENDEDOR,
               A.NMFANVEN, A.NRMESAJUNCAO, A.CDGRPOCOR, A.CDOCORR,
               A.NRMESA
         FROM (SELECT CO.NRVENDAREST, CO.NRCOMANDA, CO.DSCOMANDA, CO.IDSTCOMANDA,
                      CO.VRDESCOMANDA, CO.VRCOMISVENDE, VE.CDVENDEDOR, VD.NMFANVEN,
                      VE.NRPESMESAVEN, CO.NRMESAJUNCAO, CO.CDGRPOCOR, CO.CDOCORR,
                      VE.NRMESA
                 FROM VENDAREST VE, COMANDAVEN CO, VENDEDOR VD
                WHERE VE.CDFILIAL    = ?
                  AND VE.CDLOJA      = ?
                  AND VE.NRMESA      = ?
                  AND VE.CDFILIAL    = CO.CDFILIAL
                  AND VE.NRVENDAREST = CO.NRVENDAREST
                  AND VE.CDVENDEDOR  = VD.CDVENDEDOR
                  AND CO.IDSTCOMANDA <> '4'
                  AND VE.DTHRFECHMESA IS NULL

                UNION

               SELECT CO.NRVENDAREST, CO.NRCOMANDA, CO.DSCOMANDA, CO.IDSTCOMANDA,
                      CO.VRDESCOMANDA, CO.VRCOMISVENDE, VE.CDVENDEDOR, VD.NMFANVEN,
                      VE.NRPESMESAVEN, CO.NRMESAJUNCAO, CO.CDGRPOCOR, CO.CDOCORR,
                      VE.NRMESA
                 FROM VENDAREST VE, COMANDAVEN CO, VENDEDOR VD
                WHERE VE.CDFILIAL    = ?
                  AND VE.CDLOJA      = ?
                  AND VE.CDFILIAL    = CO.CDFILIAL
                  AND VE.NRVENDAREST = CO.NRVENDAREST
                  AND VE.CDVENDEDOR  = VD.CDVENDEDOR
                  AND CO.IDSTCOMANDA <> '4'
                  AND VE.DTHRFECHMESA IS NULL
                  AND VE.NRMESA IN (SELECT NRMESA
                                      FROM JUNCAOMESA JM, MESAJUNCAO MJ
                                     WHERE JM.CDFILIAL  = ?
                                       AND JM.CDLOJA    = ?
                                       AND JM.CDFILIAL  = MJ.CDFILIAL
                                       AND JM.NRJUNMESA = MJ.NRJUNMESA
                                       AND JM.CDLOJA    = MJ.CDLOJA
                                       AND JM.NRJUNMESA = (SELECT NRJUNMESA
                                                             FROM MESAJUNCAO
                                                            WHERE CDFILIAL = ?
                                                              AND CDLOJA   = ?
                                                              AND NRMESA   = ?))) A
        ORDER BY A.NRVENDAREST, A.NRCOMANDA
    ";

    const SQL_VAL_TRANS = "
        SELECT M.IDSTMESAAUX
          FROM MESA M
         WHERE M.CDFILIAL = ?
           AND M.NRMESA   = ?
           AND M.CDLOJA   = ?
           AND (M.IDSTMESAAUX = 'D' OR M.IDSTMESAAUX = 'O')
    ";

    const SQL_VALIDA_MESA_ABERTA = "
        SELECT V.CDFILIAL, V.NRVENDAREST, C.NRCOMANDA, V.NRMESA,
               V.NRPESMESAVEN, V.NRPOSICAOMESA, V.CDCLIENTE, V.CDCONSUMIDOR
          FROM VENDAREST V, COMANDAVEN C
         WHERE V.NRMESA = ?
           AND V.CDFILIAL = ?
           AND V.NRVENDAREST = C.NRVENDAREST
    ";

    const INS_PEDIDOALT = "
        INSERT INTO PEDIDOALT(
                CDFILIALOLD, NRVENDARESTOLD, NRCOMANDAOLD, NRPRODCOMVENOLD,
                NRVENDAREST, NRCOMANDA, NRPRODCOMVEN, NRMESA,
                NRSEQPRODCUP, NRLUGARMESAIT)
        VALUES (?, ?, ?, ?,
                ?, ?, ?, ?,
                ?, ?)
    ";

    const SQL_GET_OBSERVATIONS_EST = "
        SELECT CDFILIAL, NRVENDAREST, NRCOMANDA, NRPRODCOMVEN,
               CDPRODUTO, CDGRPOCOR, CDOCORR, NRORG,
               IDATIVO, QTPRODCOMVENOBS
          FROM OBSITCOMANDAEST
         WHERE NRVENDAREST = :OLDNRVENDAREST
           AND NRCOMANDA = :OLDNRCOMANDA
           AND NRPRODCOMVEN = :OLDNRPRODCOMVEN
    ";

    const SQL_DELETE_OBSITCOMANDAEST = "
        DELETE OBSITCOMANDAEST
         WHERE NRVENDAREST = :OLDNRVENDAREST
           AND NRCOMANDA = :OLDNRCOMANDA
           AND NRPRODCOMVEN = :OLDNRPRODCOMVEN
    ";

    const SQL_UPDATE_ITCOMANDAVENEST = "
        UPDATE ITCOMANDAEST
           SET NRVENDAREST  = :NRVENDAREST,
               NRCOMANDA    = :NRCOMANDA,
               NRPRODCOMVEN = :NRPRODCOMVEN
         WHERE NRVENDAREST  = :OLDNRVENDAREST
           AND NRCOMANDA    = :OLDNRCOMANDA
           AND NRPRODCOMVEN = :OLDNRPRODCOMVEN
    ";

    const SQL_DADOS_ITEM_COMANDA = "
        SELECT
      DISTINCT CDFILIAL, NRVENDAREST, NRCOMANDA, NRPRODCOMVEN,
               CDPRODUTO, QTPRODCOMVEN, VRPRECCOMVEN, CONVERT(VARCHAR,TXPRODCOMVEN) AS TXPRODCOMVEN,
               IDSTPRCOMVEN, VRDESCCOMVEN, NRLUGARMESA, CDGRPOCOR,
               CDOCORR, NRMESAORIG, CDLOJAORIG, DTHRINCOMVEN,
               IDPRODIMPFIS, CDLOJA, NRSEQPRODCOM, NRSEQPRODCUP,
               VRACRCOMVEN, DSCOMANDAORI, NRCOMANDAORI, NRPRODCOMORI,
               CDCAIXACOLETOR, VRPRECCLCOMVEN, NRMESADSCOMORIT, CDVENDEDOR,
               CDPRODPROMOCAO, NRPEDIDOFOS, CDSENHAPED, NRATRAPRODCOVE, IDORIGPEDCMD,
               CONVERT(VARCHAR,DSOBSPEDDIGCMD) AS DSOBSPEDDIGCMD, IDPRODREFIL, QTITEMREFIL, IDDIVIDECONTA
          FROM (SELECT
              DISTINCT CDFILIAL, NRVENDAREST, NRCOMANDA, NRPRODCOMVEN,
                       CDPRODUTO, QTPRODCOMVEN, VRPRECCOMVEN, CONVERT(VARCHAR,TXPRODCOMVEN) AS TXPRODCOMVEN,
                       IDSTPRCOMVEN, VRDESCCOMVEN, NRLUGARMESA, CDGRPOCOR,
                       CDOCORR, NRMESAORIG, CDLOJAORIG, DTHRINCOMVEN,
                       IDPRODIMPFIS, CDLOJA, NRSEQPRODCOM, NRSEQPRODCUP,
                       VRACRCOMVEN, DSCOMANDAORI, NRCOMANDAORI, NRPRODCOMORI,
                       CDCAIXACOLETOR, VRPRECCLCOMVEN, NRMESADSCOMORIT, CDVENDEDOR,
                       CDPRODPROMOCAO, NRPEDIDOFOS, CDSENHAPED, NRATRAPRODCOVE, IDORIGPEDCMD,
                       CONVERT(VARCHAR,DSOBSPEDDIGCMD) AS DSOBSPEDDIGCMD, IDPRODREFIL, QTITEMREFIL, IDDIVIDECONTA
                  FROM ITCOMANDAVEN
                 WHERE CDFILIAL     = ?
                   AND NRVENDAREST  = ?
                   AND NRCOMANDA    = ?
                   AND NRPRODCOMVEN = ?

                 UNION

                SELECT
              DISTINCT CDFILIAL, NRVENDAREST, NRCOMANDA, NRPRODCOMVEN,
                       CDPRODUTO, QTPRODCOMVEN, VRPRECCOMVEN, CONVERT(VARCHAR,TXPRODCOMVEN) AS TXPRODCOMVEN,
                       IDSTPRCOMVEN, VRDESCCOMVEN, NRLUGARMESA, CDGRPOCOR,
                       CDOCORR, NRMESAORIG, CDLOJAORIG, DTHRINCOMVEN,
                       IDPRODIMPFIS, CDLOJA, NRSEQPRODCOM, NRSEQPRODCUP,
                       VRACRCOMVEN, DSCOMANDAORI, NRCOMANDAORI, NRPRODCOMORI,
                       CDCAIXACOLETOR, VRPRECCLCOMVEN, NRMESADSCOMORIT, CDVENDEDOR,
                       CDPRODPROMOCAO, NRPEDIDOFOS, CDSENHAPED, NRATRAPRODCOVE, IDORIGPEDCMD,
                       CONVERT(VARCHAR,DSOBSPEDDIGCMD) AS DSOBSPEDDIGCMD, IDPRODREFIL, QTITEMREFIL, IDDIVIDECONTA
                  FROM ITCOMANDAVEN
                 WHERE CDFILIAL     = ?
                   AND NRVENDAREST  = ?
                   AND NRCOMANDA    = ?
                   AND NRSEQPRODCOM = ?
                   AND NRSEQPRODCOM IS NOT NULL) A
    ";

    const SQL_UPD_POS_MESA = "
        UPDATE ITCOMANDAVEN
           SET NRLUGARMESA  = ?
         WHERE CDFILIAL     = ?
           AND NRVENDAREST  = ?
           AND NRCOMANDA    = ?
           AND NRPRODCOMVEN = ?
    ";

    const SQL_GET_NRSEQCOM = "
        SELECT NRSEQPRODCOM
          FROM (SELECT ISNULL(MAX(NRSEQPRODCOM),0) AS NRSEQPRODCOM
                  FROM ITCOMANDAVEN
                 WHERE CDFILIAL    = :CDFILIAL
                   AND NRVENDAREST = :NRVENDAREST
                   AND NRCOMANDA   = :NRCOMANDA
                   AND NRORG       = :NRORG
                   AND NRSEQPRODCOM IS NOT NULL) A
        ";

    const SQL_BUSCA_QUANTIDADE = "
        SELECT QTPRODCOMVEN
          FROM ITCOMANDAVEN
         WHERE CDFILIAL     = ?
           AND NRVENDAREST  = ?
           AND NRCOMANDA    = ?
           AND NRPRODCOMVEN = ?
    ";

    const SQL_DEL_PROD = "
        DELETE
          FROM ITCOMANDAVEN
         WHERE CDFILIAL     = ?
           AND NRVENDAREST  = ?
           AND NRCOMANDA    = ?
           AND NRPRODCOMVEN = ?
    ";

    const SQL_VALIDA_POSICAO = "
        SELECT sum(cast(NRPESMESAVEN as int)) as NRPESMESAVEN
          FROM VENDAREST
         WHERE CDFILIAL = ?
           AND (charindex(NRVENDAREST, ?) > 0)
           AND (charindex(NRMESA, ?) > 0)
    ";

    const SQL_GET_NRMESA = "
        SELECT V.NRMESA
          FROM VENDAREST V JOIN COMANDAVEN C
                             ON V.NRVENDAREST = C.NRVENDAREST
         WHERE V.NRVENDAREST = ?
           AND C.NRCOMANDA   = ?
    ";

    const SQL_UPD_POS_KDS = "
        UPDATE ITPEDIDOFOS
           SET NRLUGARMESAIT = ?
         WHERE CDFILIAL = ?
           AND NRPEDIDOFOS = ?
           AND NRITPEDIDOFOS = ?
    ";

    const GET_ITPEDIDOFOS_BY_ITPEDIDOFOSREL = "
        SELECT R.CDFILIAL, R.NRPEDIDOFOS, R.NRITPEDIDOFOS, R.NRSEQITPEDREL,
               R.QTPRODPEFOS AS QTREL, I.QTPRODPEFOS AS QTPED
          FROM ITPEDIDOFOSREL R JOIN ITPEDIDOFOS I
                                  ON R.CDFILIAL = I.CDFILIAL
                                 AND R.NRPEDIDOFOS = I.NRPEDIDOFOS
                                 AND R.NRITPEDIDOFOS = I.NRITPEDIDOFOS
         WHERE R.CDFILIAL = ?
           AND R.NRVENDAREST = ?
           AND R.NRCOMANDA = ?
           AND R.NRPRODCOMVEN = ?
    ";

    const SQL_DADOS_USUARIO = "
        SELECT NRACESSOUSER, NMUSUARIO, NRMESA, IDACESSOUSER,
               CDFILIAL, CDLOJA, CHAVEGARCOM
          FROM ACESSOFM
         WHERE NRACESSOUSER = ?";

    const SQL_NRVENDAREST = "
        SELECT NRVENDAREST
          FROM VENDAREST
         WHERE CDFILIAL = ?
           AND NRMESA = ?
    ";

    const SQL_NRCOMANDA = "
        SELECT NRCOMANDA
          FROM COMANDAVEN
         WHERE CDFILIAL = ?
           AND NRVENDAREST = ?
    ";

    const SQL_GET_DADOS_MESA = "
        SELECT NRMESA, NRMESA, NMMESA, CDSALA,
               NULL AS NRPESMESAVEN, NULL AS NRVENDAREST, NULL AS NRCOMANDA, NULL AS CDCLIENTE,
               NULL AS CDCONSUMIDOR, NULL AS CDVENDEDOR, NULL AS NMRAZSOCCLIE, NULL AS NMCONSUMIDOR,
               NULL AS NRCPFRESPCON, NULL AS NRPOSICAOMESA
          FROM MESA
         WHERE CDFILIAL = ?
           AND CDLOJA = ?
           AND NRMESA = ?
    ";

    const SQL_BUSCAVENDAREST = "
        SELECT V.NRVENDAREST, C.NRCOMANDA, V.CDVENDEDOR, V.CDOPERADOR, G.NMFANVEN
          FROM VENDAREST V JOIN COMANDAVEN C ON V.CDFILIAL = C.CDFILIAL
                                            AND V.NRVENDAREST = C.NRVENDAREST
                                            LEFT JOIN VENDEDOR G
                                                   ON G.CDVENDEDOR = V.CDVENDEDOR
         WHERE V.CDFILIAL = ?
           AND V.NRMESA = ?
    ";

    const SQL_GET_NRJUNMESA_ABERTURA = "
        SELECT NRJUNMESA
          FROM MESAJUNCAO
         WHERE CDFILIAL = ?
           AND CDLOJA = ?
           AND NRMESA = ?
    ";

    const SQL_DADOS_COMANDA = "
        SELECT C.DSCOMANDA, C.NRVENDAREST, C.NRCOMANDA, C.IDSTCOMANDA,
               V.CDLOJA, V.NRMESA, C.VRDESCOMANDA, C.VRCOMISVENDE,
               C.VRACRCOMANDA, V.CDVENDEDOR, V.CDCLIENTE, V.CDCONSUMIDOR,
               L.NMRAZSOCCLIE, O.NMCONSUMIDOR, C.VRDESCFID
          FROM COMANDAVEN C JOIN VENDAREST V
                              ON C.CDFILIAL    = V.CDFILIAL
                             AND C.NRVENDAREST = V.NRVENDAREST
                             AND C.CDLOJA      = V.CDLOJA
                       LEFT JOIN CLIENTE L
                              ON V.CDCLIENTE = L.CDCLIENTE
                       LEFT JOIN CONSUMIDOR O
                              ON V.CDCLIENTE = O.CDCLIENTE
                             AND V.CDCONSUMIDOR = O.CDCONSUMIDOR
         WHERE C.CDFILIAL    = ?
           AND C.NRCOMANDA   = ?
           AND C.NRVENDAREST = ?
           AND C.CDLOJA      = ?
           AND C.IDSTCOMANDA <> '4'
    ";

    const SQL_DADOS_COMANDA_ABERTURA = "
        SELECT C.DSCOMANDA, C.NRVENDAREST, C.NRCOMANDA, C.IDSTCOMANDA,
               V.CDLOJA, V.NRMESA, C.VRDESCOMANDA, C.VRCOMISVENDE,
               C.VRACRCOMANDA, V.CDVENDEDOR, V.CDCLIENTE, V.CDCONSUMIDOR, CO.NMCONSUMIDOR,
               C.DSCOMANDA + (CASE WHEN ISNULL(C.DSCONSUMIDOR, CO.NMCONSUMIDOR) IS NULL THEN '' ELSE ' - ' END) +
               ISNULL(ISNULL(C.DSCONSUMIDOR, CO.NMCONSUMIDOR), '') AS LABELDSCOMANDA
          FROM COMANDAVEN C JOIN VENDAREST V
                              ON C.CDFILIAL    = V.CDFILIAL
                             AND C.NRVENDAREST = V.NRVENDAREST
                             AND C.CDLOJA      = V.CDLOJA
                       LEFT JOIN CONSUMIDOR CO
                              ON V.CDCLIENTE = CO.CDCLIENTE
                             AND V.CDCONSUMIDOR = CO.CDCONSUMIDOR
        WHERE C.CDFILIAL = :CDFILIAL
          AND C.DSCOMANDA = :DSCOMANDA
          AND C.CDLOJA = :CDLOJA
          AND C.IDSTCOMANDA <> '4'
    ";

    const SQL_VALIDA_SUPERVISOR = "
        SELECT O.CDOPERADOR, O.CDSENHOPER, O.CDSENHAOPERWEB
          FROM OPERADOR O, OPERGRUPOP P
         WHERE O.CDOPERADOR = ?
           AND O.CDOPERADOR = P.CDOPERADOR
           AND P.CDGRUPOPER IN (SELECT CDGRUPOPER FROM AUTGRUPOP WHERE IDAUTGRUPOP = 'SFOS')
    ";

    const SQL_EXISTE_OPERADOR = "
        SELECT CDOPERADOR, CDSENHOPER, NMOPERADOR
          FROM OPERADOR
         WHERE CDOPERADOR = ?
    ";

    const SQL_VALIDA_MESA_BYNRMESA = "
        SELECT NRMESA, IDSTMESAAUX, CDSALA
          FROM MESA
         WHERE CDFILIAL = ?
           AND CDLOJA = ?
           AND NRMESA = ?
    ";

    const SQL_INSERE_LOG = "
        INSERT INTO LOGOPERFOS
            (CDFILIAL, CDCAIXA, IDOPERFOS, CDOPERCAIXA, NMOPERCAIXA,
            CDOPERRESPOP, NMOPERRESPOP, DTHROPERFOS,  DSMOTIVOFOS, DSLIVREFOS)
        VALUES
            (?, ?, ?, ?, ?,
             ?, ?, GETDATE(), ?, ?)
    ";

    const SQL_BUSCA_NOME_OPERADOR = "
        SELECT NMOPERADOR
        FROM OPERADOR
        WHERE CDOPERADOR = ?
    ";

    const SQL_NOVO_CODIGO = "
        SELECT CDCONTADOR, NRSEQUENCIAL
          FROM NOVOCODIGO
         WHERE CDCONTADOR = :CDCONTADOR
    ";

    const SQL_BUSCA_VALORCOMANDA_CONSUMACAO = "
        SELECT NRLUGARMESA, VRPRECCOMVEN
          FROM ITCOMANDAVEN
          WHERE NRCOMANDA = :NRCOMANDA
            AND NRVENDAREST = :NRVENDAREST
            AND IDSTPRCOMVEN <> '6'
            AND CDPRODUTO = :CDPRODUTO
    ";

    const INSERT_KDSOPERACAOTEMP = "
		INSERT INTO KDSOPERACAOTEMP
		(NRSEQOPERACAO, DSOPERACAO, NRORG)
		VALUES
		(:NRSEQOPERACAO, :DSOPERACAO, :NRORG)
	";

    const SQL_GET_VENDA_BY_NRNOTAFISCALCE = "
        SELECT NRSEQVENDA, CDCAIXA
          FROM VENDA
         WHERE CDFILIAL = :CDFILIAL
           AND CDCAIXA = :CDCAIXA
           AND DTABERTUR = :DTABERTUR
           AND NRNOTAFISCALCE = :NRNOTAFISCALCE
           AND IDSITUVENDA = 'O'
    ";

    const SQL_BUSCA_DADOS_IMPRESSORA = "
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

    const SQL_GET_MOVCAIXA_BY_NRSEQVENDA = "
        SELECT T.IDTIPORECE, M.*
        FROM MOVCAIXA M, TIPORECE T
          WHERE M.NRSEQVENDA = :NRSEQVENDA
            AND M.CDFILIAL = :CDFILIAL
            AND M.CDCAIXA = :CDCAIXA
            AND M.NRORG = :NRORG
            AND M.CDTIPORECE = T.CDTIPORECE
    ";

    const SQL_GET_IDUTLSENHAOPER = "
        SELECT IDUTLSENHAOPER
        FROM LOJA
        WHERE CDFILIAL = :CDFILIAL
          AND CDLOJA   = :CDLOJA
    ";

    const SQL_GET_COMISSAO_VENDA = "
        SELECT VRCOMISVENDE, VRCOMISPOR, IDUTILCOUVERT
        FROM COMANDAVEN
         WHERE CDFILIAL     = :CDFILIAL
           AND NRVENDAREST  = :NRVENDAREST
           AND NRCOMANDA    = :NRCOMANDA
    ";

    const SQL_GET_NMCONSUMIDOR = "
        SELECT NMCONSUMIDOR
        FROM CONSUMIDOR
          WHERE CDCLIENTE = :CDCLIENTE
            AND CDCONSUMIDOR = :CDCONSUMIDOR
            AND NRORG = :NRORG
    ";

    const SQL_ITCOMANDAVEN_NRPRODCOMVEN = "
        SELECT *
        FROM ITCOMANDAVEN
          WHERE CDFILIAL = :CDFILIAL
            AND NRVENDAREST = :NRVENDAREST
            AND NRCOMANDA = :NRCOMANDA
            AND NRPRODCOMVEN IN (:NRPRODCOMVEN)
    ";

    const SQL_FILIAIS = "
        SELECT A.FILIAL, A.CDFILIAL, A.NMFILIAL, A.NRINSJURFILI
          FROM (
            SELECT ROW_NUMBER() OVER (ORDER BY CDFILIAL) row, CDFILIAL + ' - ' + NMFILIAL AS FILIAL,
                   CDFILIAL, NMFILIAL, NRINSJURFILI
              FROM FILIAL
             WHERE CDFILIAL +' - '+ NMFILIAL LIKE :FILIAL
          ) A
         WHERE row BETWEEN :P_BEGIN AND :P_END
         ORDER BY row
    ";

    const SQL_CAIXAS = "
        SELECT A.CAIXA, A.CDFILIAL, A.CDCAIXA, A.NMCAIXA
          FROM (
            SELECT ROW_NUMBER()OVER (ORDER BY CDCAIXA) row, CDCAIXA + ' - ' + NMCAIXA AS CAIXA,
                   CDFILIAL, CDCAIXA, NMCAIXA
              FROM CAIXA
             WHERE CDFILIAL = :CDFILIAL
               AND CDCAIXA + ' - ' + NMCAIXA LIKE :CAIXA
               AND IDHABCAIXAVENDA IN ('PKC', 'PKR', 'FOS', 'POS', 'EVB')
        ) A
         WHERE row BETWEEN :P_BEGIN AND :P_END
         ORDER BY row

    ";

    const SQL_VENDEDORES = "
        SELECT B.GARCOM, B.CDFILIAL, B.CDCAIXA, B.CDOPERADOR, B.CDVENDEDOR, B.NMFANVEN FROM(
             SELECT ROW_NUMBER()OVER (ORDER BY CDVENDEDOR) row, A.CDVENDEDOR +' - '+ A.NMFANVEN AS GARCOM, A.CDFILIAL, A.CDOPERADOR, A.CDVENDEDOR, A.CDCAIXA, A.NMFANVEN FROM(
                    SELECT DISTINCT VE.CDVENDEDOR +' - '+ VE.NMFANVEN AS GARCOM, VE.CDFILIAL, O.CDOPERADOR, VE.CDVENDEDOR, VE.CDCAIXA, VE.NMFANVEN
                      FROM VENDEDOR VE JOIN OPERADOR O
                                         ON O.CDOPERADOR = VE.CDOPERADOR
                                       JOIN FILIAL F
                                         ON VE.CDFILIAL = F.CDFILIAL
                                       JOIN OPERGRUPOP OP
                                         ON OP.CDOPERADOR = O.CDOPERADOR
                                       JOIN GRUPOPER GP
                                         ON GP.CDGRUPOPER = OP.CDGRUPOPER
                                       JOIN FILIOPER FP
                                         ON F.CDFILIAL = FP.CDFILIAL
                                        AND O.CDOPERADOR = FP.CDOPERADOR
                     WHERE VE.CDFILIAL = :CDFILIAL
                       AND VE.CDVENDEDOR +' - '+ VE.NMFANVEN LIKE :GARCOM
                ) A
            ) B
            WHERE row BETWEEN :P_BEGIN AND :P_END
         ORDER BY row
    ";

    const SQL_GET_NRCPFRESPCON = "
        SELECT NRCPFRESPCON
        FROM CONSUMIDOR
        WHERE CDCLIENTE = :CDCLIENTE
          AND CDCONSUMIDOR = :CDCONSUMIDOR
          AND NRORG = :NRORG
          AND IDIMPCPFCUPOM = 'S'
    ";

    const SQL_GET_PRODUCT_DESC = "
        SELECT ISNULL(I.DSBUTTON, P.NMPRODUTO) AS DESCPROD
          FROM PRODUTO P LEFT JOIN ITMENUCONFTE I
                                ON P.CDPRODUTO = I.CDIDENTBUTON
                               AND I.CDFILIAL = :CDFILIAL
                               AND I.NRCONFTELA = :NRCONFTELA
                               AND I.IDTPBUTTON = '1'
         WHERE P.CDPRODUTO = :CDPRODUTO
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

    const SQL_UPDATE_ITCOMANDAVEN_ADIANTAMENTO = "
        UPDATE ITCOMANDAVEN
          SET NRINSCRCONS = :NRINSCRCONS, NMCONSVEND = :NMCONSVEND
          WHERE CDFILIAL = :CDFILIAL
            AND NRVENDAREST = :NRVENDAREST
            AND NRCOMANDA = :NRCOMANDA
            AND ((NRLUGARMESA IN (:NRLUGARMESA)) OR (:ALL = 'T'))
            AND (NRLUGARMESA NOT IN (SELECT M.NRLUGARMESA
                              FROM MOVCAIXAMOB M JOIN ITCOMANDAVEN I ON M.CDFILIAL = I.CDFILIAL
                                                                    AND M.NRVENDAREST = I.NRVENDAREST
                                                                    AND M.NRCOMANDA = I.NRCOMANDA
                                                                    AND M.NRLUGARMESA = I.NRLUGARMESA
                              WHERE M.CDFILIAL = :CDFILIAL
                                AND M.NRVENDAREST = :NRVENDAREST
                                AND M.NRCOMANDA = :NRCOMANDA
                            )
                )
    ";

    const SQL_CONSUMER_CDEXCONSUMID = "
        SELECT CO.CDCLIENTE, CO.CDCONSUMIDOR, CO.NMCONSUMIDOR, CL.NMRAZSOCCLIE, CO.CDIDCONSUMID, ISNULL(CO.NRCPFRESPCON, '') AS NRCPFRESPCON
          FROM CONSUMIDOR CO
          JOIN CLIENTE CL ON CL.CDCLIENTE = CO.CDCLIENTE
         WHERE (CO.CDCLIENTE = :CDCLIENTE OR :CDCLIENTE = 'T')
           AND CO.CDEXCONSUMID = :code
           AND CO.IDSITCONSUMI = '1'
    ";

    const SQL_CONSUMER_CDIDCONSUMID = "
        SELECT CO.CDCLIENTE, CO.CDCONSUMIDOR, CO.NMCONSUMIDOR, CL.NMRAZSOCCLIE, CO.CDIDCONSUMID, ISNULL(CO.NRCPFRESPCON, '') AS NRCPFRESPCON
          FROM CONSUMIDOR CO
          JOIN CLIENTE CL ON CL.CDCLIENTE = CO.CDCLIENTE
         WHERE (CO.CDCLIENTE = :CDCLIENTE OR :CDCLIENTE = 'T')
           AND CO.CDIDCONSUMID = :code
           AND CO.IDSITCONSUMI = '1'
    ";

    const SQL_CONSUMER_NRCPFRESPCON = "
        SELECT CO.CDCLIENTE, CO.CDCONSUMIDOR, CO.NMCONSUMIDOR, CL.NMRAZSOCCLIE, CO.CDIDCONSUMID, ISNULL(CO.NRCPFRESPCON, '') AS NRCPFRESPCON
          FROM CONSUMIDOR CO
          JOIN CLIENTE CL ON CL.CDCLIENTE = CO.CDCLIENTE
         WHERE (CO.CDCLIENTE = :CDCLIENTE OR :CDCLIENTE = 'T')
           AND CO.NRCPFRESPCON = :code
           AND CO.IDSITCONSUMI = '1'
    ";

    const SQL_EXECUTE_NOVO_CODIGO = "
        EXECUTE NOVO_CODIGO @P_CONTADOR = :CDCONTADOR, @P_SEQUENCIAL = ''
    ";

    const GET_SERIE_NFCE = "
        SELECT CDSERIECX, CDCAIXA
          FROM CAIXA
         WHERE CDFILIAL = :CDFILIAL
           AND CDCAIXA  = :CDCAIXA
           AND NRORG    = :NRORG
    ";

    const GET_ULTIMA_VENDA_FISCAL = "
        SELECT MAX(NRNOTAFISCALCE) AS NRNOTAFISCALCE
          FROM VENDA
         WHERE CDFILIAL = :CDFILIAL
           AND CDCAIXA = :CDCAIXA
           AND NRORG = :NRORG
           AND (CDSERIENFCE = :CDSERIENFCE OR 'SAT' = :CDSERIENFCE)
           AND IDSITUVENDA <> 'C'
           AND IDTPEMISVEND = :IDTPEMISVEND
           AND CONVERT(DATE,DTEMISSAONFCE,103) = :DTEMISSAONFCE
    ";

    const GET_VENDA = "
        SELECT *
          FROM VENDA
         WHERE CDFILIAL = :CDFILIAL
           AND CDCAIXA = :CDCAIXA
           AND NRORG = :NRORG
           AND NRNOTAFISCALCE = :NRNOTAFISCALCE
           AND (CDSERIENFCE = :CDSERIENFCE OR 'SAT' = :CDSERIENFCE)
           AND IDSITUVENDA <> 'C'
           AND CONVERT(DATE,DTEMISSAONFCE,103) = :DTEMISSAONFCE
    ";

    const GET_VRTOTTRIBIBPT_ITVENDAIMPOS = "
        SELECT DISTINCT VRTOTTRIBIBPT
          FROM ITVENDAIMPOS
        WHERE CDFILIAL = :CDFILIAL
          AND CDCAIXA = :CDCAIXA
          AND NRSEQVENDA = :NRSEQVENDA
          AND NRORG = :NRORG
    ";

    const GET_PRODUTOS_DESBLOQUEADOS = "
        SELECT D.* FROM(
                SELECT ROW_NUMBER() OVER (ORDER BY I.CDIDENTBUTON) AS ROWNUMBER,
                    I.CDIDENTBUTON AS CDPRODUTO, I.DSBUTTON AS NMPRODUTO
                      FROM ITMENUCONFTE I
                    WHERE I.IDTPBUTTON  = '1'
                      AND I.IDTPBUTONAUX = '2'
                      AND I.CDFILIAL = :CDFILIAL
                      AND I.NRORG = :NRORG
                      AND I.NRCONFTELA = :NRCONFTELA
                      AND NOT EXISTS (
                        SELECT 1
                          FROM PRODBLOQVND B
                        WHERE B.CDFILIAL = I.CDFILIAL
                          AND B.CDLOJA = :CDLOJA
                          AND B.CDPRODUTO = I.CDIDENTBUTON )
                      AND (UPPER(I.CDIDENTBUTON) LIKE UPPER(:FILTER)
                      OR UPPER(I.DSBUTTON) LIKE UPPER(:FILTER))
            ) D
        WHERE D.ROWNUMBER BETWEEN :FIRST AND :LAST
    ";

    const GET_PRODUTOS_BLOQUEADOS = "
        SELECT D.* FROM(
                SELECT ROW_NUMBER() OVER (ORDER BY I.CDIDENTBUTON) AS ROWNUMBER,
                    I.CDIDENTBUTON AS CDPRODUTO, I.DSBUTTON AS NMPRODUTO
                      FROM ITMENUCONFTE I
                      JOIN PRODBLOQVND P
                        ON I.CDIDENTBUTON = P.CDPRODUTO
                      WHERE I.IDTPBUTTON  = '1'
                        AND I.IDTPBUTONAUX = '2'
                        AND I.CDFILIAL = :CDFILIAL
                        AND I.NRORG = :NRORG
                        AND I.NRCONFTELA = :NRCONFTELA
                        AND P.CDOPERADOR = :CDOPERADOR
                        AND (UPPER(I.CDIDENTBUTON) LIKE UPPER(:FILTER)
                          OR UPPER(I.DSBUTTON) LIKE UPPER(:FILTER))
            ) D
        WHERE D.ROWNUMBER BETWEEN :FIRST AND :LAST
    ";

    const INSERT_PRODUTOS_BLOQUEADOS = "
        INSERT INTO PRODBLOQVND
          ( CDFILIAL , CDLOJA , CDPRODUTO , DTHRBLOQUEIO , CDOPERADOR , NRDIASEMANABLOQ )
        VALUES
          ( :CDFILIAL , :CDLOJA , :CDPRODUTO , :DTHRBLOQUEIO , :CDOPERADOR, 'T')
    ";

    const DELETE_PRODUTOS_BLOQUEADOS = "
        DELETE FROM PRODBLOQVND
          WHERE CDFILIAL = :CDFILIAL
          AND CDLOJA = :CDLOJA
          AND CDPRODUTO IN (:CDPRODUTO)
          AND CDOPERADOR = :CDOPERADOR
    ";

    const BUSCA_NOMEPRODBLOQ = "
      SELECT I.CDIDENTBUTON AS CDPRODUTO, I.DSBUTTON AS NMPRODUTO
      FROM ITMENUCONFTE I
      JOIN PRODBLOQVND P
        ON I.CDIDENTBUTON = P.CDPRODUTO
      WHERE I.IDTPBUTTON  = '1'
        AND I.IDTPBUTONAUX = '2'
        AND I.CDFILIAL = :CDFILIAL
        AND I.NRORG = :NRORG
        AND I.NRCONFTELA = :NRCONFTELA
        AND I.CDIDENTBUTON IN (:CDPRODUTO)
    ";

    const SQL_BUSCA_CARTOES = "
        SELECT C.CDCLIENTE + '-' + C.CDCONSUMIDOR + '-' + ISNULL(C.CDFAMILISALD, '') AS ID,
               C.CDCLIENTE, C.NMFANTCLIE, C.CDCONSUMIDOR, C.NMCONSUMIDOR,
               C.CDIDCONSUMID, ISNULL(E.VRSALDCONEXT,0) AS VRSALDCONEXT, C.IDSITCONSUMI,
               C.CDFAMILISALD, FA.NMFAMILISALD, C.IDPERTRANSALD, C.CDTIPOCONS, C.NMTIPOCONS
          FROM EXTRATOCONS E
         RIGHT JOIN (SELECT MAX(DTMOVEXTCONS) AS DTMOVEXTCONS, C.CDCLIENTE, C.CDIDCONSUMID,
                            C.CDCONSUMIDOR, C.NMCONSUMIDOR, C.NMFANTCLIE, E.CDFAMILISALD,
                            C.IDPERTRANSALD, C.CDTIPOCONS, C.NMTIPOCONS, C.IDSITCONSUMI
                       FROM EXTRATOCONS E
                      RIGHT JOIN (SELECT CO.CDCLIENTE, CO.CDCONSUMIDOR, CO.NMCONSUMIDOR, CO.CDIDCONSUMID,
                                         CL.NMFANTCLIE, TC.IDPERTRANSALD, CO.CDTIPOCONS, TC.NMTIPOCONS,
                                         CO.IDSITCONSUMI
                                    FROM CONSUMIDOR CO
                                    JOIN CLIENTE CL ON CO.CDCLIENTE = CL.CDCLIENTE
                                    LEFT JOIN TIPOCONS TC ON CO.CDTIPOCONS = TC.CDTIPOCONS
                                   WHERE (CDIDCONSUMID = :SEARCH_VALUE OR CDEXCONSUMID = :SEARCH_VALUE)
                                 ) C ON E.CDCLIENTE = C.CDCLIENTE
                                    AND E.CDCONSUMIDOR = C.CDCONSUMIDOR
                      GROUP BY C.CDCLIENTE, C.CDIDCONSUMID, C.NMCONSUMIDOR, C.CDCONSUMIDOR, C.NMFANTCLIE,
                               E.CDFAMILISALD, C.IDPERTRANSALD, C.CDTIPOCONS, C.NMTIPOCONS, C.IDSITCONSUMI
                    ) C ON E.CDCLIENTE    = C.CDCLIENTE
                       AND E.CDCONSUMIDOR = C.CDCONSUMIDOR
                       AND E.DTMOVEXTCONS = C.DTMOVEXTCONS
                       AND E.CDFAMILISALD = C.CDFAMILISALD
                       AND E.NRSEQMOVEXT  = (SELECT MAX(NRSEQMOVEXT) AS NRSEQMOVEXT
                                               FROM EXTRATOCONS
                                              WHERE CDCLIENTE    = C.CDCLIENTE
                                                AND CDCONSUMIDOR = C.CDCONSUMIDOR
                                                AND DTMOVEXTCONS = C.DTMOVEXTCONS
                                                AND CDFAMILISALD = C.CDFAMILISALD)
          LEFT JOIN FAMILIASALDO FA ON E.CDFAMILISALD = FA.CDFAMILISALD
    ";

    const BUSCA_DADOS_SSL = "
        SELECT IDCODSSL FROM RELACCODSSL WHERE IDSERIALDISP = :IDSERIALDISP
    ";

    const SQL_ALTERA_HR_FECHAMENTO_MESA = "
        UPDATE VENDAREST
          SET DTHRMESAFECH = :DTHRMESAFECH
          WHERE CDFILIAL = :CDFILIAL
            AND NRVENDAREST = :NRVENDAREST
            AND NRMESA = :NRMESA
    ";

    const BUSCA_VENDA_REALIZADA = "
        SELECT CDCAIXA, NRSEQVENDA, DTVENDA
          FROM VENDA
         WHERE CDFILIAL = :CDFILIAL
           AND NRVENDAREST = :NRVENDAREST
           AND NRCOMANDAVND = :NRCOMANDA
    ";

    const GET_NRCONTROLTEF = "
        SELECT M.NRCONTROLTEF, V.IDSTATUSNFCE, M.NRCARTBANCO
         FROM VENDA V, MOVCAIXA M
          WHERE M.NRSEQVENDA = V.NRSEQVENDA
            AND V.CDFILIAL = M.CDFILIAL
            AND V.CDCAIXA = M.CDCAIXA
            AND M.CDNSUHOSTTEF = :CDNSUHOSTTEF
    ";

    const BUSCA_NOME_POR_POSICAO_NULL = "
        SELECT CDCLIENTE, CDCONSUMIDOR, NRLUGARMESA, DSCONSUMIDOR
          FROM POSVENDAREST
         WHERE CDFILIAL = :CDFILIAL
           AND NRVENDAREST = :NRVENDAREST
           AND NRLUGARMESA = :NRLUGARMESA
    ";

    const FILTRAR_PRODUTOS = "
        SELECT D.* FROM(
            SELECT ROW_NUMBER() OVER (ORDER BY PR.CDPRODUTO) AS ROWNUMBER,
               PR.CDPRODUTO, PR.CDARVPROD, ISNULL(IM.DSBUTTON, ISNULL(PR.NMPRODIMPFIS, PR.NMPRODUTO)) AS DSBUTTON,
               PR.CDBARPRODUTO, PR.IDPESAPROD, PR.QTTARAPROD, '' AS CDFAMILISALD, PR.SGUNIDADE, PR.IDTIPOCOMPPROD,
               PR.IDIMPPRODUTO, PR.DTINIVGPROMOC, PR.DTFINVGPROMOC, IT.HRINIVENPROD, IT.HRFIMVENPROD,
               IT.VRPRECITEM, IT.VRPRECITEM AS PRECO, IT.VRPRECITEM AS PRITEM, '0' AS GRUPOS,
               CASE WHEN PB.CDPRODUTO IS NULL THEN PR.IDPRODBLOQ ELSE 'S' END AS IDPRODBLOQ,
               ISNULL(IM.NRPGCONFTAUX+IM.NRBUTTONAUX, '000') AS CDGRUPO, ISNULL(G.NMGRUPO, 'OUTROS') AS NMGRUPO,
               PR.IDTIPOPROD, PR.NRQTDMINOBS, null AS IDTIPCOBRA, PR.CDPRODINTE, PR.CDPROTECLADO, PR.DSPRODVENDA,
               PR.CDCLASFISC, AL.CDCFOPPFIS, AL.CDCSTICMS, AL.VRALIQPIS, AL.CDCSTPISCOF, AL.VRALIQCOFINS, ISNULL(IT.VRPRECITEMCL, 0) AS VRPRECITEMCL, 0 AS VRDESITVEND, 0 AS VRACRITVEND, PR.IDCONTROLAREFIL AS REFIL
            FROM
               PARAVEND PA JOIN TABEVEND TA
                             ON ((PA.CDFILIAL   = :CDFILIAL)
                            AND  (PA.CDFILIAL   = TA.CDFILIAL)
                            AND  (PA.CDTABEPREC = TA.CDTABEPREC)
                            AND  (:DATAATUAL BETWEEN TA.DTINIVGPREC AND TA.DTFINVGPREC))
                           JOIN ITEMPRECO IT
                             ON ((IT.CDFILIAL    = TA.CDFILIAL)
                            AND  (IT.CDTABEPREC  = TA.CDTABEPREC)
                            AND  (IT.DTINIVGPREC = TA.DTINIVGPREC))
                           JOIN PRODUTO PR
                             ON ((PR.CDPRODUTO = IT.CDPRODUTO))
                           LEFT JOIN PRODBLOQVND PB
                             ON PB.CDPRODUTO = PR.CDPRODUTO
                             AND PB.CDFILIAL = PA.CDFILIAL
                             AND PB.CDLOJA = :CDLOJA
                           LEFT JOIN ITMENUCONFTE IM
                             ON IM.CDFILIAL = PA.CDFILIAL
                             AND IM.NRCONFTELA = :NRCONFTELA
                             AND IM.CDIDENTBUTON = PR.CDPRODUTO
                           INNER JOIN ALIQIMPFIS AL
                             ON AL.CDFILIAL = PA.CDFILIAL
                             AND AL.CDPRODUTO = PR.CDPRODUTO
                           LEFT JOIN (SELECT NRPGCONFTELA, NRBUTTON, DSBUTTON AS NMGRUPO
                                        FROM (SELECT AM.IDTPBUTTON, AM.IDTPBUTONAUX, AM.NRBUTTON, AM.NRBUTTONAUX,
                                                      AM.DSBUTTON, AM.CDIDENTBUTON, AM.NRCOLORTEXT, AM.NRCOLORBACK,
                                                      AM.NRPGCONFTELA, AM.NRPGCONFTAUX, AM.DSMESSAGEBUT, AM.DSIMAGEMBUT,
                                                      AM.DSENDEIMG
                                                FROM ITMENUCONFTE AM
                                                WHERE AM.CDFILIAL   = :CDFILIAL
                                                  AND AM.NRCONFTELA = :NRCONFTELA) MESA,
                                          PARAMGERAL P
                                        WHERE IDTPBUTTON = '2') G
                             ON G.NRPGCONFTELA = IM.NRPGCONFTAUX
                             AND G.NRBUTTON = IM.NRBUTTONAUX

            WHERE
               (CDNVPRODUTO = (SELECT
                                  MAX(CDNVPRODUTO) FROM PRODUTO))
            AND ((0 = :SEARCH AND (PR.CDARVPROD LIKE :FILTER
                                OR PR.CDPRODUTO LIKE :FILTER
                                OR PR.CDBARPRODUTO LIKE :FILTER
                                OR PR.CDPRODINTE LIKE :FILTER
                                OR PR.CDPROTECLADO LIKE :FILTER
                                OR ISNULL(IM.DSBUTTON, ISNULL(PR.NMPRODIMPFIS, PR.NMPRODUTO)) LIKE :FILTER))
            OR (1 = :SEARCH AND (PR.CDARVPROD = :FILTER
                              OR PR.CDPRODUTO = :FILTER
                              OR PR.CDBARPRODUTO = :FILTER
                              OR PR.CDPRODINTE = :FILTER
                              OR PR.CDPROTECLADO = :FILTER)))

        ) D
        WHERE D.ROWNUMBER BETWEEN :FIRST AND :LAST
    ";

    const TRANSFER_POSITION = "
        UPDATE ITCOMANDAVEN
           SET NRLUGARMESA = :POSITION
         WHERE NRVENDAREST = :NRVENDAREST
           AND NRCOMANDA = :NRCOMANDA
           AND NRPRODCOMVEN = :NRPRODCOMVEN
    ";

    const GET_POSITION = "
        SELECT V.NRPESMESAVEN, I.IDSTLUGARMESA
          FROM VENDAREST V
          LEFT JOIN ITCOMANDAVEN I ON I.NRVENDAREST = V.NRVENDAREST
                                  AND I.NRCOMANDA = :NRCOMANDA
                                  AND I.NRLUGARMESA = :NRLUGARMESA
         WHERE V.NRVENDAREST = :NRVENDAREST
    ";

    const GET_POSITION_CLIENT = "
        SELECT I.NRLUGARMESA, P.CDCLIENTE, P.CDCONSUMIDOR
          FROM ITCOMANDAVEN I
          LEFT JOIN POSVENDAREST P ON P.NRVENDAREST = I.NRVENDAREST
                                  AND P.NRLUGARMESA = I.NRLUGARMESA
         WHERE I.NRVENDAREST = :NRVENDAREST
           AND I.NRCOMANDA = :NRCOMANDA
           AND I.NRPRODCOMVEN = :NRPRODCOMVEN
    ";

    const GET_VENDAS = "
        SELECT NRNOTAFISCALCE, DTVENDA, VRTOTVENDA
          FROM VENDA
        WHERE CDFILIAL = :CDFILIAL
          AND CDCAIXA = :CDCAIXA
          AND CAST(DTABERTUR AS DATE) = :DTABERCAIX
          AND NRNOTAFISCALCE LIKE :FILTER
          AND IDSITUVENDA <> 'C'
        ORDER BY NRSEQVENDA DESC
    ";

    const VENDEDORES_OPERADORES_ATIVOS = "
        SELECT D.* FROM(
            SELECT ROW_NUMBER() OVER (ORDER BY V.CDVENDEDOR) AS ROWNUMBER,
              V.CDVENDEDOR, V.NMFANVEN, V.CDOPERADOR, V.CDFILIAL, V.CDCAIXA, ' ' AS HABILITADO
            FROM
              VENDEDOR V, OPERADOR P
            WHERE ((V.CDVENDEDOR = :CDVENDEDOR) OR ('T' = :CDVENDEDOR))
              AND (V.CDOPERADOR  = P.CDOPERADOR )
              AND (P.IDOPERATIVO = 'A'          )
              AND (V.CDVENDEDOR LIKE :FILTER
              OR V.NMFANVEN LIKE :FILTER) ) D
        WHERE D.ROWNUMBER BETWEEN :FIRST AND :LAST
    ";

    const SQL_COMANDA_ITENS = "
        SELECT PR.CDARVPROD, PR.NMPRODUTO, PR.CDPRODUTO, IT.NRPRODCOMVEN, IT.CDPRODPROMOCAO, IT.NRSEQPRODCOM,
        PR.CDPRODUTO+'-'+ISNULL(IT.NRSEQPRODCOM, '') AS VALOR
          FROM ITCOMANDAVEN IT, PRODUTO PR
         WHERE IT.CDFILIAL = :CDFILIAL
           AND IT.NRCOMANDA = :NRCOMANDA
           AND IT.CDPRODUTO = PR.CDPRODUTO
           AND IT.IDSTPRCOMVEN <> '6'
      ";

    const UPDATE_ITCOMANDAVEN_TRANSFER = "
        UPDATE ITCOMANDAVEN
           SET NRVENDAREST = :VRCOMANDADEST,
               NRCOMANDA   = :COMANDADEST,
               NRPRODCOMVEN = :MAXNRPRODCOMVEN,
               CDSUPERVISOR = :CDSUPERVISOR
         WHERE CDFILIAL    = :CDFILIAL
           AND NRVENDAREST = :VRCOMANDAATUAL
           AND NRCOMANDA   = :COMANDAATUAL
           AND NRPRODCOMVEN = :NRPRODCOMVEN
           AND CDPRODUTO   = :CDPRODUTO
    ";

    const GET_PAGAMENTOS_TEF = "
        SELECT D.* FROM (
            SELECT ROW_NUMBER() OVER (ORDER BY MC.CDNSUHOSTTEF DESC) AS ROWNUMBER,
                MC.CDNSUHOSTTEF, MC.VRMOVIVEND, MC.DTHRINCMOV, T.NMTIPORECE
              FROM MOVCAIXA MC, TIPORECE T
              WHERE MC.CDTIPORECE = T.CDTIPORECE
                AND CDFILIAL = :CDFILIAL
                AND CDCAIXA = :CDCAIXA
                AND IDTIPOMOVIVE = 'E'
                AND CDNSUHOSTTEF IS NOT NULL
                AND DTHRINCMOV >= (SELECT DTABERCAIX FROM TURCAIXA
                  WHERE CDFILIAL = :CDFILIAL AND CDCAIXA = :CDCAIXA AND DTFECHCAIX IS NULL)

        ) D
        WHERE D.ROWNUMBER BETWEEN :FIRST AND :LAST
    ";

    const GET_POSITION_CONTROL = "
        SELECT NRLUGARMESA
          FROM CONTROLPOSVEN
         WHERE CDFILIAL = :CDFILIAL
           AND NRVENDAREST = :NRVENDAREST
           AND CDOPERADOR <> :CDOPERADOR
    ";

    const GET_LOCKED_POSITIONS = "
        SELECT NRLUGARMESA
          FROM CONTROLPOSVEN
         WHERE CDFILIAL = :CDFILIAL
           AND NRVENDAREST = :NRVENDAREST
           AND CDOPERADOR = :CDOPERADOR
    ";

    const INSERT_POSITION_CONTROL = "
        INSERT INTO CONTROLPOSVEN
            (CDFILIAL, NRVENDAREST, NRLUGARMESA, CDOPERADOR)
        VALUES
            (:CDFILIAL, :NRVENDAREST, :NRLUGARMESA, :CDOPERADOR)
    ";

    const DELETE_POSITION_CONTROL = "
        DELETE FROM CONTROLPOSVEN
         WHERE CDFILIAL = :CDFILIAL
           AND NRVENDAREST = :NRVENDAREST
           AND NRLUGARMESA = :NRLUGARMESA
           AND CDOPERADOR = :CDOPERADOR
    ";

    const RESET_POSITION_CONTROL = "
        DELETE FROM CONTROLPOSVEN
         WHERE CDFILIAL = :CDFILIAL
           AND NRVENDAREST = :NRVENDAREST
           AND (CDOPERADOR = :CDOPERADOR OR 'T' = :CDOPERADOR)
    ";

    const GET_NRPOSICAOMESA = "
        SELECT NRPOSICAOMESA
          FROM VENDAREST
         WHERE CDFILIAL = :CDFILIAL
           AND NRVENDAREST = :NRVENDAREST
    ";

    const UPDATE_COMANDAVEN_DESCFID = "
        UPDATE COMANDAVEN
           SET VRDESCFID    = :VRDESCFID
         WHERE CDFILIAL     = :CDFILIAL
           AND NRVENDAREST  = :NRVENDAREST
           AND NRCOMANDA    = :NRCOMANDA
    ";

    const UPDATE_POSVENDAREST_DESCFID = "
        UPDATE POSVENDAREST
        SET VRDESCFIDPOS = :VRDESCFIDPOS
        WHERE   CDFILIAL    = :CDFILIAL
            AND NRVENDAREST = :NRVENDAREST
            AND NRLUGARMESA = :NRLUGARMESA
    ";

    const SQL_POSVENDAREST_POSICAO = "
        SELECT NRLUGARMESA, CDCLIENTE, CDCONSUMIDOR, VRDESCFIDPOS
          FROM POSVENDAREST
          WHERE CDFILIAL = :CDFILIAL
            AND NRVENDAREST = :NRVENDAREST
            AND NRLUGARMESA IN (:NRLUGARMESA)
            AND CDCONSUMIDOR IS NOT NULL
    ";

    const UPDATE_PORCENTAGEM_COMISSAO = "
        UPDATE COMANDAVEN
          SET VRCOMISPOR    = :VRCOMISPOR, VRCOMISVENDE = :VRCOMISVENDE
          WHERE CDFILIAL    = :CDFILIAL
            AND NRCOMANDA   = :NRCOMANDA
            AND NRVENDAREST = :NRVENDAREST
    ";

    const GET_DISCOUNT_FIDELITY = "
        SELECT VRMOVEXTCONS
            FROM EXTRATOCONS
        WHERE CDCLIENTE = :CDCLIENTE
            AND CDCONSUMIDOR = :CDCONSUMIDOR
            AND CDFAMILISALD = :CDFAMILISALD
            AND CDFILIAL = :CDFILIAL
            AND CDCAIXA = :CDCAIXA
            AND NRSEQVENDA = :NRSEQVENDA
    ";

    const GET_DISCOUNT_OBSERVATIONS = "
        SELECT O.CDOCORR AS codigo, O.DSOCORR AS mensagem
          FROM LOJA L, OCORRENCIA O
         WHERE L.CDFILIAL       = ?
           AND L.CDLOJA         = ?
           AND L.CDGRPOCORDESC  = O.CDGRPOCOR
         ORDER BY O.CDOCORR
    ";

    const GET_REGISTER_CLOSING_PAY_N = "
        SELECT M.CDTIPORECE, T.NMTIPORECE, 0 AS VRMOVIVEND,
               NULL AS LABELVRMOVIVEND, T.IDSANGRIAAUTO
          FROM TIPORECE T
          JOIN MOVCAIXA M ON T.CDTIPORECE = M.CDTIPORECE
         WHERE M.CDFILIAL   = :CDFILIAL
           AND M.CDCAIXA    = :CDCAIXA
           AND M.IDTIPOMOVIVE IN ('E','T','U')
           AND T.IDSANGRIAAUTO = 'N'
           AND CAST(M.DTABERCAIX AS DATETIME) = CAST(:DTABERCAIX AS DATETIME)
         GROUP BY M.CDTIPORECE, T.NMTIPORECE, T.IDSANGRIAAUTO
    ";

    const GET_TIPOSANGRIA = "
        SELECT *
          FROM TIPOSANGRIA
        WHERE CDTPSANGRIA <> '00001'
        ORDER BY CDTPSANGRIA
    ";

    const SQL_GRUPO_OBS_DESC = "
        SELECT CDGRPOCORDESC
        FROM LOJA
          WHERE CDFILIAL = :CDFILIAL
            AND CDLOJA = :CDLOJA
    ";

    const SQL_CAMPANHA = "
        SELECT CDCAMPPROMO FROM CAMPANHAPROMO
    ";

    const BUSCA_DADOS_CAMPANHA = "
        SELECT P.CDCAMPPROMO, P.CDPRODPRIN, P.CDPRODCOMB, P.IDPERCVALOR, P.IDDESCACRE,
               P.VRDESCACRE, P.IDAPLICADESACR, P.CDPRODCOMB2, P.CDPRODCOMB3, C.CDCLIENTE
          FROM CAMPANHAPROMO C, COMBINAPROD P
         WHERE CONVERT(VARCHAR, GETDATE(), 103) BETWEEN C.DTINIVGCAMP AND C.DTFINVGCAMP
           AND :HORA BETWEEN C.HRINICAMP AND C.HRFINCAMP
           AND :HORA BETWEEN P.HRINICOMB AND P.HRFINCOMB
           AND ((DATEPART(dw,GETDATE()) = '1' AND C.IDDIADOMINGO = 'S' AND P.IDDIADOMCOMB = 'S') OR
               (DATEPART(dw,GETDATE()) = '2' AND C.IDDIASEGUNDA = 'S' AND P.IDDIASEGCOMB = 'S') OR
               (DATEPART(dw,GETDATE()) = '3' AND C.IDDIATERCA   = 'S' AND P.IDDIATERCOMB = 'S') OR
               (DATEPART(dw,GETDATE()) = '4' AND C.IDDIAQUARTA  = 'S' AND P.IDDIAQUACOMB = 'S') OR
               (DATEPART(dw,GETDATE()) = '5' AND C.IDDIAQUINTA  = 'S' AND P.IDDIAQUICOMB = 'S') OR
               (DATEPART(dw,GETDATE()) = '6' AND C.IDDIASEXTA   = 'S' AND P.IDDIASEXCOMB = 'S') OR
               (DATEPART(dw,GETDATE()) = '7' AND C.IDDIASABADO  = 'S' AND P.IDDIASABCOMB = 'S'))
           AND CASE WHEN (:CDTIPOCONS IS NULL) THEN
               CASE WHEN C.CDTIPOCONS IS NULL THEN 1 ELSE 0 END
               ELSE
               CASE WHEN C.CDCLIENTE = :CDCLIENTE AND C.CDTIPOCONS = :CDTIPOCONS THEN 1 ELSE 0 END
           END = 1
           AND C.CDCAMPPROMO = P.CDCAMPPROMO
           AND CHARINDEX(P.CDPRODPRIN, :STRPRODUCTS) <> 0
           AND CHARINDEX(P.CDPRODCOMB, :STRPRODUCTS) <> 0
           AND (CHARINDEX(P.CDPRODCOMB2, :STRPRODUCTS) <> 0 OR P.CDPRODCOMB2 IS NULL)
           AND (NOT EXISTS (SELECT 1
                              FROM PRODUTO A
                             WHERE CHARINDEX(A.CDPRODUTO, P.CDPRODCOMB3) <> 0
                               AND CHARINDEX(A.CDPRODUTO, :STRPRODUCTS) = 0) OR P.CDPRODCOMB3 IS NULL)
         ORDER BY P.VRDESCACRE DESC
    ";

    const SQL_PROMOCAO_APLICADESCFIL = "
        SELECT PG.CDPRODUTO
        FROM GRUPROMOCPRFIL GF
          JOIN PRODGRUPROMOC PG
            ON GF.CDGRUPROMOC = PG.CDGRUPROMOC
          WHERE GF.CDPRODPROMOCAO = :CDPRODPROMOCAO
            AND GF.CDFILIAL = :CDFILIAL
            AND GF.IDAPLICADESCFIL = 'S'
    ";

    const SQL_PROMOCAO_APLICADESC = "
        SELECT PG.CDPRODUTO
        FROM GRUPROMOCPROD GP
          JOIN PRODGRUPROMOC PG
            ON GP.CDGRUPROMOC = PG.CDGRUPROMOC
          WHERE GP.CDPRODPROMOCAO = :CDPRODPROMOCAO
            AND GP.IDAPLICADESC = 'S'
    ";

    const UPDATE_DESCONTO_PROMOCAO = "
        UPDATE ITCOMANDAVEN
           SET VRDESCCOMVEN = :VRDESCCOMVEN
         WHERE CDFILIAL = :CDFILIAL
           AND NRORG = :NRORG
           AND NRVENDAREST  = :NRVENDAREST
           AND NRCOMANDA    = :NRCOMANDA
           AND CDPRODUTO = :CDPRODUTO
           AND NRPRODCOMVEN = :NRPRODCOMVEN
           AND CDPRODPROMOCAO = :CDPRODPROMOCAO
           AND NRSEQPRODCOM = :NRSEQPRODCOM
    ";

    const BUSCA_DESCONTO_SUBGRUPO = "
        SELECT QTINICIALFX, QTFINALFX, QTPERCDESC
          FROM DESCQTDESUB
         WHERE CDGRUPPROD = :CDGRUPPROD
           AND CDSUBGRPROD = :CDSUBGRPROD
    ";

    const GET_DELIVERY_ORDERS = "
        SELECT DISTINCT P.CDFILIAL, P.CDLOJA,
                IDSTCOMANDA, P.NRVENDAREST, DTHRABERMESA, CDCONSUMIDOR, NMCONSUMIDOR,
                NRTELECONS, P.NRCOMANDA, SUBSTRING(DSCOMANDA, 5, 100) DSCOMANDA, CDCLIENTE, NRCPFRESPCON,
                CDMUNICIPIO, CDPAIS, SGESTADO, CDBAIRRO, DSBAIRRO, DSENDECONSCOMAN,
                DSCOMPLENDCOCOM,NRCEPCONSCOMAND, DSREFENDCONSCOM, DTENTREGA,
                NRVENDARESTAUX, IDORGCMDVENDA, CDNSUESITEF, IDRETBALLOJA, I.CDSENHAPED,
                P.VRACRCOMANDA, NRENDECONSCOMAN, NRCOMANDAEXT
          FROM (SELECT CO.CDFILIAL, CO.CDLOJA,
                       CO.IDSTCOMANDA, CO.NRVENDAREST, (CONVERT(varchar, DTHRABERMESA, 3) + ' ' + CONVERT(varchar, DTHRABERMESA, 8)) DTHRABERMESA, CS.CDCONSUMIDOR,
                       CS.NMCONSUMIDOR, CS.NRTELECONS, CO.NRCOMANDA, CO.DSCOMANDA, VE.CDCLIENTE,
                       CS.NRCPFRESPCON,CO.CDMUNICIPIO, MU.NMMUNICIPIO, CO.CDPAIS, CO.SGESTADO, CO.CDBAIRRO,
                       CO.DSBAIRRO, CO.DSENDECONSCOMAN, CO.DSCOMPLENDCOCOM, CO.NRCEPCONSCOMAND,
                       CAST(CO.DSREFENDCONSCOM AS VARCHAR(2000)) AS DSREFENDCONSCOM, CO.DTENTREGA,
                       VE.NRVENDARESTAUX, CO.IDORGCMDVENDA, VE.CDNSUESITEF, CO.IDRETBALLOJA, CO.VRACRCOMANDA,
                       CO.NRENDECONSCOMAN, CO.NRCOMANDAEXT
                  FROM COMANDAVEN CO, MUNICIPIO MU, VENDAREST VE
                        LEFT JOIN CONSUMIDOR CS
                               ON (VE.CDCLIENTE    = CS.CDCLIENTE)
                              AND (VE.CDCONSUMIDOR = CS.CDCONSUMIDOR)
                 WHERE (CO.NRVENDAREST  = VE.NRVENDAREST)
                   AND (CO.CDFILIAL     = VE.CDFILIAL)
                   AND (CO.CDPAIS      = MU.CDPAIS)
                   AND (CO.SGESTADO    = MU.SGESTADO)
                   AND (CO.CDMUNICIPIO = MU.CDMUNICIPIO)
                   AND (VE.CDFILIAL     = :CDFILIAL)
                   AND (CO.CDLOJA       = :CDLOJA)
                   AND (CO.IDSTCOMANDA NOT IN ('5','X'))
                   AND (SUBSTRING(CO.DSCOMANDA,1,4) = 'DLV_')) P, ITCOMANDAVEN I
        WHERE P.CDFILIAL    = I.CDFILIAL
          AND P.CDLOJA      = I.CDLOJA
          AND P.NRVENDAREST = I.NRVENDAREST
          AND P.NRCOMANDA   = I.NRCOMANDA
        ORDER BY P.DTHRABERMESA DESC
    ";

    const GET_DELIVERY_ORDERS_CONTROL = "
         SELECT VE.CDFILIAL, VE.CDCAIXA, VE.NRSEQVENDA, CO.IDSTCOMANDA,
               (CONVERT(varchar, VE.DTSAIDAVENDA, 3)+' '+ CONVERT(varchar, VE.DTSAIDAVENDA, 8)) DTSAIDAVENDA,
               (CONVERT(varchar, VE.DTCHEGAVENDA, 3)+' '+ CONVERT(varchar, VE.DTCHEGAVENDA, 8)) DTCHEGAVENDA,
               VD.NRINSJURVEN, VD.NMFANVEN, MU.NMMUNICIPIO, RG.CDREGIAOMUNI, RG.NMREGIAOMUNI,
               VE.DSENDECONSVENDA, VE.SGESTADO, BA.CDBAIRRO, ISNULL(BA.NMBAIRRO, VE.DSBAIRRO) AS NMBAIRRO,
               PA.NMPAIS, ES.NMESTADO, VE.NRCOMANDAVND, VE.CDCLIENTE, VE.CDCONSUMIDOR, CD.NMCONSUMIDOR,
               VE.DSCOMPLENDCOVEN, CONVERT(CHAR,VE.DTVENDA,8) AS DTVENDA, CO.NRVENDAREST,
               CAST(VE.DSREFENDCONSVEN AS VARCHAR(2000)) AS DSREFENDCONSVEN, SUBSTRING(CO.DSCOMANDA, 5, 100) DSCOMANDA, VE.CDLOJA, VE.NRNOTAFISCALCE
            FROM
               VENDA VE LEFT JOIN VENDEDOR VD ON (VE.CDVENDEDOR = VD.CDVENDEDOR),
               CONSUMIDOR CD, PAIS PA, ESTADO ES, MUNICIPIO MU,
               BAIRRO BA LEFT JOIN REGIAOMUNI RG ON (BA.CDPAIS      = RG.CDPAIS)
                                                 AND (BA.SGESTADO    = RG.SGESTADO)
                                                 AND (BA.CDMUNICIPIO = RG.CDMUNICIPIO)
                                                 AND (BA.CDREGIAOMUNI    = RG.CDREGIAOMUNI)
                         RIGHT JOIN COMANDAVEN CO ON (CO.CDPAIS      = BA.CDPAIS)
                                                 AND (CO.SGESTADO    = BA.SGESTADO)
                                                 AND (CO.CDMUNICIPIO = BA.CDMUNICIPIO)
                                                 AND (CO.CDBAIRRO    = BA.CDBAIRRO)
             WHERE (VE.CDLOJA = :CDLOJA)
               AND (VE.CDFILIAL = :CDFILIAL)
               AND (VE.DTCHEGAVENDA IS NULL)
               AND (VE.CDFILIAL = CO.CDFILIAL)
               AND (VE.NRCOMANDAVND = CO.NRCOMANDA)
               AND (VE.DSCOMANDAVND = CO.DSCOMANDA)
               AND (VE.IDSITUVENDA='O')
               AND (CO.IDSTCOMANDA IN ('P','5'))
               AND (CO.CDPAIS      = PA.CDPAIS)
               AND (CO.CDPAIS      = ES.CDPAIS)
               AND (CO.SGESTADO    = ES.SGESTADO)
               AND (CO.CDPAIS      = MU.CDPAIS)
               AND (CO.SGESTADO    = MU.SGESTADO)
               AND (CO.CDMUNICIPIO = MU.CDMUNICIPIO)
               AND (VE.CDCLIENTE = CD.CDCLIENTE)
               AND (VE.CDCONSUMIDOR = CD.CDCONSUMIDOR)
             ORDER BY VE.NRCOMANDAVND
    ";

    const GET_PAYMENT_DLV = "
        SELECT CDFILIAL, NRVENDAREST, IDTIPOMOVIVEDLV, CDCLIENTE, CDCONSUMIDOR,
               CDTIPORECE, VRMOVIVENDDLV VRMOVIVEND, NRCARTBANCODLV
        FROM MOVCAIXADLV
        WHERE CDFILIAL = :CDFILIAL
          AND NRVENDAREST = :NRVENDAREST
    ";

    const GET_ORDER_PARAMS_DLV = "
        SELECT
          min(IT.NRPRODCOMVEN) as NRPRODCOMVEN, SUM(IT.QTPRODCOMVEN) AS QTPRODCOMVEN, IT.VRPRECCOMVEN, sum(IT.VRDESCCOMVEN) as VRDESCCOMVEN,IT.CDFILIAL, IT.NRVENDAREST, IT.NRCOMANDA, IT.CDPRODUTO, IT.IDPRODIMPFIS,
          IT.IDSTPRCOMVEN, IT.NRLUGARMESA,IT.CDGRPOCOR, IT.CDOCORR, IT.NRMESAORIG, IT.CDLOJAORIG,
          PR.CDARVPROD , ISNULL(PR.NMPRODIMPFIS,PR.NMPRODUTO) AS NMPRODUTO,
          VR.CDVENDEDOR, VR.DTHRABERMESA, VR.DTHRFECHMESA,
          CM.VRDESCOMANDA, '0000000000' AS NRCUPOMFIS, VR.CDOPERADOR, VR.CDLOJA,
          IM.IDTPIMPOSFIS, AQ.VRPEALIMPFIS, AQ.CDIMPOSTO,
          'V' AS IDTPFORPGVEN, FL.CDEMPRESA, CM.VRCOMISVENDE AS VRTXSERVICO,
          CM.VRCONSUMAMIN AS VRCONSUMAMIN, IT.CDLOJA AS CDLOJAIT, PR.IDCOBTXSERV,
          'NNN' AS IDCONFIGPROD, AC.VRACRCOMANDA,  GETDATE() AS DTABERCAIX,
          '' AS NRMESA, '' AS NRSEQ, '' AS STCDALIQ, VR.CDCLIENTE, VR.CDCONSUMIDOR,  CM.DSCOMANDA,
          FA.CDFAMILISALD, CONVERT(VARCHAR,'') AS CDSERVFILI, ISNULL(IT.NMCONSVEND,'CONSUMIDOR') AS NMCONSVEND,IT.NRINSCRCONS, PR.SGUNIDADE, OP.NMOPERADOR, IT.CDPRODPROMOCAO,
          IT.VRPRECCLCOMVEN AS VRUNITVENDCL, 0.00 AS ACRESITEM, '000000' AS NRCONTCUPOMFISC, '' AS CDORDALIQIMPR,
          0.00 AS VRALIQIMPR, ISNULL(IT.DSENDEVENDA,'') AS DSENDEVENDA, IT.NRMESADSCOMORIT, CONVERT(VARCHAR, '') AS NRORDEMVND,
          CONVERT(VARCHAR, '') AS NRSERIEVND, CONVERT(VARCHAR, '') AS NRSUBSERIEVND, VR.NRCERVENDAREST, VR.NRCOOVENDAREST,
          '' AS NRSEQPRODCUP, IT.VRACRCOMVEN, VR.NRPESMESAVEN, IT.CDVENDEDOR AS CDVENDEDORITEM, 0.00 AS VRREPIQUEVENDA,
          '' AS NMLOJA, CAST(IT.TXPRODCOMVEN AS VARCHAR(2000)) AS TXPRODCOMVEN, PR.CDBARPRODUTO AS CDPRODIMPFIS,
          '' AS NMCONSUMIDOR, '' AS CDEXCONSUMID, GETDATE() AS DTCHECKINPH, GETDATE() AS DTCHECKOUTPH, '' AS CDRESERVAPH, '' AS CDNATPRODUTO,
          IT.IDORIGPEDCMD, IT.DTHRINCOMVEN, IT.CDCAIXACOLETOR, CONVERT(VARCHAR,IT.DSOBSPEDDIGCMD) AS DSOBSPEDDIGITA, '' AS CDGRPOCORDESC, '' AS DSOBSDESC, '' AS CDSUPERVISOR,
          IT.CDSENHAPED, CONVERT(VARCHAR,CM.DSOBSCOMANDA) AS DSOBSCOMANDA, CM.NRCOMANDAEXT, CM.DTHRAGENDADA, CM.DSCUPOMPROMO, CM.IDEXTCONSAPP, CM.DSAREAATEND, '' DSOBSFINVEN
        FROM
           PRODUTO PR LEFT JOIN FAMSALDOPROD FA
                      ON (PR.CDPRODUTO = FA.CDPRODUTO) AND
                         (:CDFILIAL  = FA.CDFILIAL ),
           VENDAREST VR LEFT JOIN OPERADOR OP
                      ON (VR.CDOPERADOR = OP.CDOPERADOR),
           COMANDAVEN CM, FILIAL FL, IMPOSTO IM,
           ITCOMANDAVEN IT LEFT JOIN ALIQIMPFIS AQ
                           ON (IT.CDFILIAL  = AQ.CDFILIAL ) AND
                              (IT.CDPRODUTO = AQ.CDPRODUTO),
           (SELECT
               SUM(VRACRCOMANDA) AS VRACRCOMANDA
            FROM
               COMANDAVEN CO
            WHERE
               (CO.CDFILIAL = :CDFILIAL)
            AND(CHARINDEX(CO.NRCOMANDA, :NRCOMANDA) <> 0))AC
        WHERE (IT.CDFILIAL    = :CDFILIAL    )
          AND (IT.CDFILIAL    = FL.CDFILIAL    )
          AND (CHARINDEX(IT.NRCOMANDA, :NRCOMANDA) <> 0)
          AND (IT.CDPRODUTO   = PR.CDPRODUTO   )
          AND (IT.IDSTPRCOMVEN NOT IN ('6','7'))
          AND (IT.CDFILIAL    = VR.CDFILIAL    )
          AND (IT.NRVENDAREST = VR.NRVENDAREST )
          AND (IT.CDFILIAL    = CM.CDFILIAL    )
          AND (IT.NRCOMANDA   = CM.NRCOMANDA   )
          AND (AQ.CDIMPOSTO   = IM.CDIMPOSTO   )
          AND (IT.NRVENDAREST = VR.NRVENDAREST)
        GROUP BY
          IT.CDFILIAL, IT.NRVENDAREST, IT.NRCOMANDA, IT.CDPRODUTO, IT.IDPRODIMPFIS,
          IT.IDSTPRCOMVEN, IT.VRPRECCOMVEN, IT.NRLUGARMESA,IT.CDGRPOCOR, IT.CDOCORR, IT.NRMESAORIG, IT.CDLOJAORIG,
          PR.CDARVPROD ,  NMPRODUTO,ISNULL(PR.NMPRODIMPFIS,PR.NMPRODUTO),
          VR.CDVENDEDOR, VR.DTHRABERMESA, VR.DTHRFECHMESA, CM.VRDESCOMANDA,   VR.CDOPERADOR, VR.CDLOJA,
          IM.IDTPIMPOSFIS, AQ.VRPEALIMPFIS, AQ.CDIMPOSTO,  FL.CDEMPRESA,  CM.VRCOMISVENDE ,
          VRCONSUMAMIN,IT.CDLOJA, PR.IDCOBTXSERV, AC.VRACRCOMANDA,  NRMESA,  VR.CDCLIENTE, VR.CDCONSUMIDOR,  CM.DSCOMANDA,
          FA.CDFAMILISALD, ISNULL(IT.NMCONSVEND,'CONSUMIDOR'), IT.NRINSCRCONS, ISNULL(IT.DSENDEVENDA,''), PR.SGUNIDADE, OP.NMOPERADOR, IT.CDPRODPROMOCAO,IT.VRPRECCLCOMVEN,
          IT.NRMESADSCOMORIT, VR.NRCERVENDAREST, VR.NRCOOVENDAREST,IT.VRACRCOMVEN, VR.NRPESMESAVEN, IT.CDVENDEDOR,
          CAST(IT.TXPRODCOMVEN AS VARCHAR(2000)), PR.CDBARPRODUTO, IT.IDORIGPEDCMD, IT.DTHRINCOMVEN, IT.CDCAIXACOLETOR,
          CONVERT(VARCHAR,IT.DSOBSPEDDIGCMD), IT.CDSENHAPED, CM.DSOBSCOMANDA, CM.NRCOMANDAEXT, CM.DTHRAGENDADA, CM.DSCUPOMPROMO, CM.IDEXTCONSAPP, CM.DSAREAATEND

    ";

    const GET_INFO_DELIVERY_ORDER = "
        SELECT C.CDFILIAL, C.NRVENDAREST, C.NRCOMANDA, V.NRMESA,
               V.NRORG, V.NRPESMESAVEN, V.CDVENDEDOR, C.DSCOMANDA, V.DTHRMESAFECH
        FROM VENDAREST V
            JOIN COMANDAVEN C
              ON V.NRVENDAREST = C.NRVENDAREST
        WHERE V.CDFILIAL    = :CDFILIAL
          AND V.NRVENDAREST = :NRVENDAREST
    ";

    const UPDATE_SAIDA_ENTREGADOR = "
        UPDATE VENDA
           SET DTSAIDAVENDA     = :DTALTOPER,
               CDVENDEDOR       = :CDVENDEDOR,
               NRENTREGAPEDVND  = :NRENTREGA
         WHERE (CDFILIAL   = :CDFILIAL)
           AND (CDCAIXA    = :CDCAIXA)
           AND (NRSEQVENDA = :NRSEQVENDA)
    ";

    const UPDATE_SAIDA_COMANDA = "
        UPDATE COMANDAVEN
           SET DTSAIDACMD  = :DTSAIDACMD,
               IDSTCOMANDA = '5'
         WHERE (CDFILIAL    = :CDFILIAL)
           AND (NRCOMANDA   = :NRCOMANDA)
           AND (NRVENDAREST = :NRVENDAREST)
    ";

    const UPDATE_ENTREGADOR_VENDAREST = "
        UPDATE VENDAREST
           SET CDVENDEDOR = :CDVENDEDOR
         WHERE (CDFILIAL    = :CDFILIAL)
           AND (NRVENDAREST = :NRVENDAREST)
    ";

    const GET_VENDEDORES = "
        SELECT CDVENDEDOR, NMFANVEN
          FROM VENDEDOR
         WHERE CDFILIAL = :CDFILIAL
    ";

    const UPDATE_IMP_VENDA = "
        UPDATE VENDA
           SET IDIMPVENDA = 'A'
         WHERE (CDFILIAL    = :CDFILIAL)
           AND (CDCAIXA     = :CDCAIXA)
           AND (NRSEQVENDA  = :NRSEQVENDA)
           AND (IDIMPVENDA  = 'S')
    ";

    const GET_VENDEDORES_PEDIDOS_DLV = "
        SELECT DISTINCT VD.NRINSJURVEN, VD.CDVENDEDOR, VD.NMFANVEN
          FROM VENDEDOR VD, VENDAREST VE, COMANDAVEN CMD
         WHERE VE.CDVENDEDOR    = VD.CDVENDEDOR
           AND VE.CDFILIAL      = VD.CDFILIAL
           AND VE.NRVENDAREST   = CMD.NRVENDAREST
           AND VE.CDFILIAL      = CMD.CDFILIAL
           AND CMD.IDSTCOMANDA  = '5'
           AND VE.CDFILIAL      = :CDFILIAL
           AND VE.CDLOJA        = :CDLOJA
    ";

    const UPDATE_ENTREGADOR_CHEGADA = "
        UPDATE VENDA
           SET DTCHEGAVENDA = :DTCHEGAVENDA
         WHERE (CDFILIAL = :CDFILIAL)
           AND (CDCAIXA = :CDCAIXA)
           AND (NRSEQVENDA = :NRSEQVENDA)
    ";

    const UPDATE_CHEGADA_COMANDA = "
        UPDATE COMANDAVEN
           SET DTCHEGACMD   = :DTCHEGACMD,
               IDSTCOMANDA  = 'X'
         WHERE (CDFILIAL   = :CDFILIAL)
           AND (NRCOMANDA  = :NRCOMANDA)
    ";

    const GET_ORDERS_ENTREGADOR_DELIVERY = "
        SELECT CMD.NRCOMANDA, CS.NMCONSUMIDOR, CMD.IDORGCMDVENDA, VD.NMFANVEN, VE.NRSEQVENDA
           FROM VENDA VE, COMANDAVEN CMD, VENDEDOR VD, CONSUMIDOR CS
           WHERE VE.CDVENDEDOR   = :CDVENDEDOR
             AND VE.CDFILIAL     = :CDFILIAL
             AND VE.CDLOJA       = :CDLOJA
             AND CMD.IDSTCOMANDA = '5'
             AND CMD.NRVENDAREST = VE.NRVENDAREST
             AND VE.CDVENDEDOR   = VD.CDVENDEDOR
             AND CS.CDCONSUMIDOR = VE.CDCONSUMIDOR
    ";

    const GET_ORIGEM_VENDA_DLV = "
        SELECT IDORGCMDVENDA
          FROM COMANDAVEN
         WHERE NRVENDAREST = :NRVENDAREST
    ";

    const GET_TAXA_ENTREGA = "
        SELECT VRACRCOMANDA
          FROM COMANDAVEN
         WHERE NRVENDAREST = :NRVENDAREST
           AND CDFILIAL    = :CDFILIAL
    ";

    const SQL_ULTILIZA_COUVERT = "
        UPDATE COMANDAVEN
           SET IDUTILCOUVERT = :IDUTILCOUVERT
         WHERE CDFILIAL     = :CDFILIAL
           AND NRVENDAREST  = :NRVENDAREST
           AND NRCOMANDA    = :NRCOMANDA
    ";

    const SQL_DELETA_ITCOMANDAVENDES = "
        DELETE FROM ITCOMANDAVENDES
         WHERE CDFILIAL = :CDFILIAL
           AND NRVENDAREST = :NRVENDAREST
    ";

    const SQL_DESISTE_ITEM = "
        INSERT INTO ITCOMANDAVENDES
            (CDFILIAL, CDCAIXA, NRVENDAREST, NRSEQITCOMVENDES, QTPRODITCOMVENDES, CDPRODUTO,
             NRORG, VRPRECCOMVEN, VRDESCCOMVEN, VRACRCOMVEN)
        VALUES
            (:CDFILIAL, :CDCAIXA, :NRVENDAREST, :NRSEQITCOMVENDES, :QTPRODITCOMVENDES, :CDPRODUTO,
             :NRORG, :VRPRECCOMVEN, :VRDESCCOMVEN, :VRACRCOMVEN)
    ";

    const GET_TIPORECEBE_DELIVERY = "
        SELECT MD.CDTIPORECE, TP.NMTIPORECE, MD.VRMOVIVENDDLV, MD.NRSEQMOVDLV
            FROM MOVCAIXADLV MD, TIPORECE TP
        WHERE MD.NRVENDAREST    = :NRVENDAREST
          AND MD.CDFILIAL       = :CDFILIAL
          AND MD.CDTIPORECE     = TP.CDTIPORECE
    ";

    const GET_PRODUTOS_PEDIDODLV = "
        SELECT P.NMPRODUTO, IT.QTPRODCOMVEN, IT.VRPRECCOMVEN,
               (ROUND((IT.VRPRECCOMVEN  * IT.QTPRODCOMVEN), 2, 1) + IT.VRACRCOMVEN - IT.VRDESCCOMVEN) VRPRECCOMVENTOTAL,
               IT.NRPRODCOMVEN, IT.CDPRODPROMOCAO, IT.NRSEQPRODCUP,
               IT.NRSEQPRODCOM, IT.NRVENDAREST, IT.NRCOMANDA, IT.CDSUPERVISOR,
               IT.IDPRODPRODUZ, IT.CDPRODUTO
            FROM ITCOMANDAVEN IT, PRODUTO P
        WHERE IT.CDFILIAL      = :CDFILIAL
          AND IT.NRVENDAREST   = :NRVENDAREST
          AND P.CDPRODUTO      = IT.CDPRODUTO
          AND IT.IDSTPRCOMVEN  <> '6'
    ";

    const INSERT_MOVCAIXADLV_PEDIDO = "
        INSERT INTO MOVCAIXADLV (
            CDFILIAL, NRVENDAREST, NRSEQMOVDLV, IDTIPOMOVIVEDLV,
            CDCLIENTE, CDCONSUMIDOR, CDTIPORECE, VRMOVIVENDDLV
        )
        VALUES (
            :CDFILIAL, :NRVENDAREST, :NRSEQMOVDLV, :IDTIPOMOVIVEDLV,
            :CDCLIENTE, :CDCONSUMIDOR, :CDTIPORECE, :VRMOVIVENDDLV
        )
    ";

    const DELETE_MOVCAIXADLV= "
        DELETE FROM MOVCAIXADLV
         WHERE CDFILIAL     = :CDFILIAL
           AND NRVENDAREST  = :NRVENDAREST
    ";

    const GET_CLIENTE_CONSUMIDOR_DLV = "
        SELECT DISTINCT CDCLIENTE, CDCONSUMIDOR FROM MOVCAIXADLV
         WHERE CDFILIAL     = :CDFILIAL
           AND NRVENDAREST  = :NRVENDAREST
    ";

    const UPDATE_COMANDA_PENDENTE = "
        UPDATE COMANDAVEN
           SET IDSTCOMANDA  = '1'
         WHERE (CDFILIAL   = :CDFILIAL)
           AND (NRCOMANDA  = :NRCOMANDA)
    ";

    const GET_NRNOTAFISCALCE = "
        SELECT NRNOTAFISCALCE
            FROM VENDA
        WHERE NRVENDAREST = :NRVENDAREST
          AND CDFILIAL    = :CDFILIAL
    ";

    const CONCLUDE_ORDERDLV = "
        UPDATE COMANDAVEN
           SET DTCHEGACMD   = :DTCHEGACMD,
               DTSAIDACMD   = :DTSAIDACMD,
               IDSTCOMANDA  = 'X'
         WHERE (CDFILIAL   = :CDFILIAL)
           AND (NRCOMANDA  = :NRCOMANDA)
    ";

    const SQL_ATUALIZA_GORJETA = "
        UPDATE COMANDAVEN
           SET VRCOMISVENDE = :VRCOMISVENDE,
               VRCOMISPOR = :VRCOMISPOR
         WHERE CDFILIAL = :CDFILIAL
           AND NRVENDAREST = :NRVENDAREST
           AND NRCOMANDA = :NRCOMANDA
    ";

    const LIMPA_FIDELIDADE = "
        UPDATE POSVENDAREST
           SET VRDESCFIDPOS = 0
         WHERE CDFILIAL = :CDFILIAL
           AND NRVENDAREST = :NRVENDAREST
    ";

    const SQL_PARAMETROS_CATRACA = "
        SELECT NRTAMARQXML, DSDIRARQXMLINT, IDGERAARQXMLINT
          FROM PARAVEND
         WHERE CDFILIAL = :CDFILIAL
    ";

    const UPDATE_VRCOMISVENDE = "
        UPDATE CDV
            SET CDV.VRCOMISVENDE = ROUND((:PRECOTXTOTAL * (ISNULL(CDV.VRCOMISPOR, LOJ.VRCOMISVENDA) / 100)), 2, 1)
        FROM COMANDAVEN CDV
        JOIN LOJA LOJ
          ON LOJ.CDFILIAL = CDV.CDFILIAL
         AND LOJ.CDLOJA = CDV.CDLOJA
         AND LOJ.NRORG = CDV.NRORG
        WHERE CDV.CDFILIAL = :CDFILIAL
            AND CDV.CDLOJA = :CDLOJA
            AND CDV.NRVENDAREST = :NRVENDAREST
            AND CDV.NRCOMANDA = :NRCOMANDA
    ";

    const BUSCA_MESA_PRINCIPAL = "
        SELECT V.NRMESA, V.NRVENDAREST, C.NRCOMANDA, V.NRPESMESAVEN
          FROM VENDAREST V
          JOIN COMANDAVEN C
            ON C.NRVENDAREST = V.NRVENDAREST
         WHERE V.CDFILIAL = :CDFILIAL
           AND V.CDLOJA = :CDLOJA
           AND V.NRVENDAREST = (SELECT MIN(VEN.NRVENDAREST) AS NRVENDAREST
                                FROM MESAJUNCAO ORG
                                JOIN MESAJUNCAO JUN
                                  ON JUN.CDFILIAL = ORG.CDFILIAL
                                 AND JUN.CDLOJA = ORG.CDLOJA
                                 AND JUN.NRJUNMESA = ORG.NRJUNMESA
                                JOIN VENDAREST VEN
                                  ON VEN.NRMESA = JUN.NRMESA
                               WHERE ORG.CDFILIAL = :CDFILIAL
                                 AND ORG.CDLOJA = :CDLOJA
                                 AND ORG.NRMESA = :NRMESA)
    ";

    const SQL_BUSCA_PAIS = "
        SELECT P.* FROM (
            SELECT ROW_NUMBER() OVER (ORDER BY NMPAIS) AS ROWNUMBER, CDPAIS, NMPAIS
              FROM PAIS
             WHERE NMPAIS LIKE :NMPAIS) P
        WHERE P.ROWNUMBER BETWEEN :FIRST AND :LAST
    ";

    const SQL_BUSCA_ESTADO = "
        SELECT E.* FROM (
            SELECT ROW_NUMBER() OVER (ORDER BY NMESTADO) AS ROWNUMBER, SGESTADO, NMESTADO
              FROM ESTADO
             WHERE CDPAIS = :CDPAIS
               AND NMESTADO LIKE :NMESTADO
               AND CDESTADOIBGE IS NOT NULL) E
        WHERE E.ROWNUMBER BETWEEN :FIRST AND :LAST
    ";

    const SQL_BUSCA_MUNICIPIO = "
        SELECT M.* FROM (
            SELECT ROW_NUMBER() OVER (ORDER BY NMMUNICIPIO) AS ROWNUMBER, CDMUNICIPIO, NMMUNICIPIO
              FROM MUNICIPIO
             WHERE CDPAIS = :CDPAIS
               AND SGESTADO = :SGESTADO
               AND NMMUNICIPIO LIKE :NMMUNICIPIO) M
        WHERE M.ROWNUMBER BETWEEN :FIRST AND :LAST
    ";

    const SQL_BUSCA_BAIRRO = "
        SELECT B.* FROM (
            SELECT ROW_NUMBER() OVER (ORDER BY NMBAIRRO) AS ROWNUMBER, CDBAIRRO, NMBAIRRO
              FROM BAIRRO
             WHERE CDPAIS = :CDPAIS
               AND SGESTADO = :SGESTADO
               AND CDMUNICIPIO = :CDMUNICIPIO
               AND NMBAIRRO LIKE :NMBAIRRO) B
        WHERE B.ROWNUMBER BETWEEN :FIRST AND :LAST
    ";

    const SQL_TIPO_CONS = "
        SELECT CDTIPOCONS, NMTIPOCONS
          FROM TIPOCONS
    ";

    const SQL_ADD_CONSUMER = "
        INSERT INTO CONSUMIDOR(
            CDCLIENTE, CDCONSUMIDOR, NMCONSUMIDOR, IDSEXOCONS,
            DTNASCCONS, NRRGCONSUMID, NRCPFRESPCON, DSENDECONS,
            NRENDECONS, CDPAIS, SGESTADO, CDMUNICIPIO, CDBAIRRO,
            NMBAIRCONS, NRCEPCONS, NRTELECONS, NRTELE2CONS,
            NRCELULARCONS, DSEMAILCONS, CDTIPOCONS, IDTPVENDACONS,
            CDIDCONSUMID, CDEXCONSUMID, NMLOGINCONS, CDSENHACONSMD5,
            IDATUCPFCONS, IDPERCONSPRODEX, IDIMPCPFCUPOM, IDVERSALDCON,
            CDCCUSCLIE, IDSITCONSUMI, IDCONSUMIDOR, IDATUCONSUMI,
            IDCADCONFLIBCON, IDTPSELMANHA, IDTPSEALMOCO, IDTPSELTARDE,
            IDCRACHAMESTRE, NRORG
        )
        VALUES (
            :CDCLIENTE, :CDCONSUMIDOR, :NMCONSUMIDOR, :IDSEXOCONS,
            :DTNASCCONS, :NRRGCONSUMID, :NRCPFRESPCON, :DSENDECONS,
            :NRENDECONS, :CDPAIS, :SGESTADO, :CDMUNICIPIO, :CDBAIRRO,
            :NMBAIRCONS, :NRCEPCONS, :NRTELECONS, :NRTELE2CONS,
            :NRCELULARCONS, :DSEMAILCONS, :CDTIPOCONS, :IDTPVENDACONS,
            :CDIDCONSUMID, :CDEXCONSUMID, :NMLOGINCONS, :CDSENHACONSMD5,
            :IDATUCPFCONS, :IDPERCONSPRODEX, :IDIMPCPFCUPOM, :IDVERSALDCON,
            :CDCCUSCLIE, :IDSITCONSUMI, :IDCONSUMIDOR, :IDATUCONSUMI,
            :IDCADCONFLIBCON, :IDTPSELMANHA, :IDTPSEALMOCO, :IDTPSELTARDE,
            :IDCRACHAMESTRE, :NRORG
        )
    ";

    const VERIFICA_POSICOES_PAGAS = "
        SELECT DISTINCT ITE.NRPESMESAVENIT
          FROM ITEMVENDA ITE
          JOIN VENDA V
            ON ITE.CDFILIAL = V.CDFILIAL
            AND ITE.CDLOJA = V.CDLOJA
            AND ITE.CDCAIXA = V.CDCAIXA
            AND ITE.NRSEQVENDA = V.NRSEQVENDA
          JOIN ITCOMANDAVEN IT
            ON V.CDFILIAL = IT.CDFILIAL
            AND V.CDLOJA = IT.CDLOJA
            AND V.NRVENDAREST = IT.NRVENDAREST
            AND V.NRCOMANDAVND = IT.NRCOMANDA
          WHERE V.CDFILIAL = :CDFILIAL
            AND V.NRCOMANDAVND = :NRCOMANDA
            AND V.NRVENDAREST = :NRVENDAREST
            AND V.CDLOJA = :CDLOJA
    ";

    const SQL_DELETE_MOVCAIXAMOB = "
        DELETE FROM MOVCAIXAMOB
        WHERE (CDFILIAL = :CDFILIAL
            AND NRVENDAREST = :NRVENDAREST
            AND NRCOMANDA = :NRCOMANDA
            AND NRLUGARMESA IN (:NRLUGARMESA)
            AND NRORG = :NRORG)

        OR
            (CDFILIAL = :CDFILIAL
                AND CDNSUTEFMOB IN (:CDNSUTEFMOB)
                AND NRADMCODE IN (:NRADMCODE)
                AND NRORG = :NRORG)
    ";

    const SQL_GET_PENDING_PAYMENTS = "
        SELECT IDTIPORECE, CDNSUTEFMOB AS CDNSUHOSTTEF, NRADMCODE AS NRCONTROLTEF,
            VRMOV AS VRMOVIVEND, NRCARTBANCO, DSBANDEIRA AS CDBANCARTCR, TXPRIMVIATEF AS STLPRIVIA,
            TXSEGVIATEF AS STLSEGVIA, NRADMCODE AS TRANSACTIONDATE
            FROM MOVCAIXAMOB
        WHERE CDFILIAL = :CDFILIAL
            AND CDCAIXA = :CDCAIXA
            AND NRORG = :NRORG
            AND NRVENDAREST = ''
            AND NRCOMANDA = ''
    ";

    const SQL_BUSCA_MOVCAIXAMOB = "
        SELECT I.DSBUTTON, V.NRPESMESAVEN, M.VRMOV, M.DSBANDEIRA, M.CDNSUTEFMOB, M.IDTIPORECE, M.IDTPTEF, M.NRCARTBANCO, M.NRADMCODE,
           M.TXPRIMVIATEF, M.TXSEGVIATEF, M.CDTIPORECE, M.IDTIPORECE, M.IDTPTEF, M.DTHRINCMOV, M.NRLUGARMESA
        FROM MOVCAIXAMOB M, ITMENUCONFTE I, VENDAREST V
        WHERE M.NRVENDAREST = :NRVENDAREST
            AND M.NRCOMANDA = :NRCOMANDA
            AND I.CDIDENTBUTON = M.CDTIPORECE
            AND M.NRVENDAREST = V.NRVENDAREST

    ";

    const SQL_GET_IDSENHACUP = "
        SELECT IDSENHACUP
          FROM CAIXA
         WHERE CDCAIXA = :CDCAIXA
           AND CDFILIAL = :CDFILIAL
           AND CDLOJA = :CDLOJA
    ";

    const SQL_GET_CDSENHAPED = "
        SELECT CDSENHAPED
          FROM ITCOMANDAVEN
         WHERE CDFILIAL = :CDFILIAL
           AND CDLOJA = :CDLOJA
    ";

}

