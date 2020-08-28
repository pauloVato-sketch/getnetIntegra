<?php

namespace Odhen\API\Service;

class Operador {

	protected $entityManager;

	public function __construct(\Doctrine\ORM\EntityManager $entityManager)  {
		$this->entityManager = $entityManager;
	}

	public function buscaControleAcesso($CDOPERADOR) {
		$params = array(
			'CDOPERADOR' => $CDOPERADOR
		);
		$supervisor = $this->entityManager->getConnection()->fetchAll("VALIDA_SUPERVISOR", $params);
		if (!empty($supervisor)) {
			$operadorSupervisor = 'S';
		} else {
			$operadorSupervisor = 'N';
		}

		$params = array(
			'CDOPERADOR' => $CDOPERADOR
		);
		$controleAcesso = $this->entityManager->getConnection()->fetchAll("CONTROLE_ACESSO", $params);
		if (!empty($controleAcesso)) {
			$arrayControle = self::constroiControleAcesso($controleAcesso);
			$result = array(
				'error' => false,
				'controleAcesso' => $arrayControle,
				'operadorSupervisor' => $operadorSupervisor
			);
		} else {
			$result = array(
				'error' => true,
				'message' => 'Controle de acesso não encontrado para o operador.'
			);
		}
		return $result;
	}

    private  function constroiControleAcesso($controleAcesso) {
        /*
        {' 1|Trocar Operador'}
        {' 2|Devolução Item'}
        {' 3|Cancela Cupom'}
        {' 4|Cancela Último Item'}
        {' 5|Cancela Item Genérico'}
        {' 6|Cancela Mesa/Comanda'}
        {' 7|Consulta Saldo'}
        {' 8|Cupom com Desconto'}
        {' 9|Produto Combinado'}
        {'10|Consulta Preço'}
        {'11|Relatórios Fiscais'}
        {'12|Administração TEF'}
        {'13|Sangria'}
        {'14|Suprimento'}
        {'15|Leitura X'}
        {'16|Redução Z'}
        {'17|Abrir Gaveta'}
        {'18|Observação do Pedido'}
        {'19|Fechar Caixa'}
        {'20|Bloquear Caixa'}
        {'21|Liberar Mesa'}
        {'22|Retirar Consumação'}
        {'23|Retirar Couvert'}
        {'24|Retirar Taxa de Serviço'}
        {'25|Agrupar Mesas'}
        {'26|Cancelar Agrupamento/Separar Mesa'}
        {'27|Transferir Mesa'}
        {'28|Transferir Produto'}
        {'29|Relatório de Produtos'}
        {'30|Pesquisar Produtos'}
        {'31|Importar Dados'}
        {'32|Posicao Mesa'}
        {'33|Reservar Mesa'}
        {'34|Alterar Nr. Pessoas'}
        {'35|Parcial Conta'}
        {'36|Abertura do caixa'}
        {'41|Bloqueio/Desbloqueio de Produto'}
        {'42|Reimpressão Fiscal'}
        {'43|Bloqueio de Dispositivo'}
        {'44|Mensagem de Produção'}
        */

        $trocarOperador = 'N';
        $devolucaoItem = 'N';
        $cancelaCupom = 'N';
        $cancelaUltimoItem = 'N';
        $cancelaItemGenerico = 'N';
        $cancelaMesaComanda = 'N';
        $consultaSaldo = 'N';
        $cupomDesconto = 'N';
        $produtoCombinado = 'N';
        $consultaPreco = 'N';
        $relatoriosFiscais = 'N';
        $administracaoTEF = 'N';
        $sangria = 'N';
        $suprimento = 'N';
        $leituraX = 'N';
        $reducaoZ = 'N';
        $abrirGaveta = 'N';
        $observacaoPedido = 'N';
        $fecharCaixa = 'N';
        $bloquearCaixa = 'N';
        $liberarMesa = 'N';
        $retirarConsumacao = 'N';
        $retirarCouvert = 'N';
        $retirarTaxaServico = 'N';
        $agruparMesas = 'N';
        $cancelarAgrupamento = 'N';
        $transferirMesa = 'N';
        $transferirProduto = 'N';
        $relatorioProdutos = 'N';
        $pesquisarProdutos = 'N';
        $importarDados = 'N';
        $posicaoMesa = 'N';
        $reservarMesa = 'N';
        $alterarQtPessoas = 'N';
        $parcialConta = 'N';
        $aberturaCaixa  = 'N';
        $bloqueiaProduto  = 'N';
        $reimpressaoFiscal = 'N';
        $bloqueiaDispositivo = 'N';
        $mensagemProducao = 'N';

        foreach ($controleAcesso as $item) {
            if ($trocarOperador != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 0, 1);
                if ($trocarOperador != 'C' || $currentControl == 'S') {
                    $trocarOperador = $currentControl;
                }
            }
            if ($devolucaoItem != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 1, 1);
                if ($devolucaoItem != 'C' || $currentControl == 'S') {
                    $devolucaoItem = $currentControl;
                }
            }
            if ($cancelaCupom != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 2, 1);
                if ($cancelaCupom != 'C' || $currentControl == 'S') {
                    $cancelaCupom = $currentControl;
                }
            }
            if ($cancelaUltimoItem != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 3, 1);
                if ($cancelaUltimoItem != 'C' || $currentControl == 'S') {
                    $cancelaUltimoItem = $currentControl;
                }
            }
            if ($cancelaItemGenerico != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 4, 1);
                if ($cancelaItemGenerico != 'C' || $currentControl == 'S') {
                    $cancelaItemGenerico = $currentControl;
                }
            }
            if ($cancelaMesaComanda != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 5, 1);
                if ($cancelaMesaComanda != 'C' || $currentControl == 'S') {
                    $cancelaMesaComanda = $currentControl;
                }
            }
            if ($consultaSaldo != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 6, 1);
                if ($consultaSaldo != 'C' || $currentControl == 'S') {
                    $consultaSaldo = $currentControl;
                }
            }
            if ($cupomDesconto != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 7, 1);
                if ($cupomDesconto != 'C' || $currentControl == 'S') {
                    $cupomDesconto = $currentControl;
                }
            }
            if ($produtoCombinado != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 8, 1);
                if ($produtoCombinado != 'C' || $currentControl == 'S') {
                    $produtoCombinado = $currentControl;
                }
            }
            if ($consultaPreco != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 9, 1);
                if ($consultaPreco != 'C' || $currentControl == 'S') {
                    $consultaPreco = $currentControl;
                }
            }
            if ($relatoriosFiscais != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 10, 1);
                if ($relatoriosFiscais != 'C' || $currentControl == 'S') {
                    $relatoriosFiscais = $currentControl;
                }
            }
            if ($administracaoTEF != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 11, 1);
                if ($administracaoTEF != 'C' || $currentControl == 'S') {
                    $administracaoTEF = $currentControl;
                }
            }
            if ($sangria != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 12, 1);
                if ($sangria != 'C' || $currentControl == 'S') {
                    $sangria = $currentControl;
                }
            }
            if ($suprimento != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 13, 1);
                if ($suprimento != 'C' || $currentControl == 'S')  {
                    $suprimento = $currentControl;
                }
            }
            if ($leituraX != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 14, 1);
                if ($leituraX != 'C' || $currentControl == 'S') {
                    $leituraX = $currentControl;
                }
            }
            if ($reducaoZ != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 15, 1);
                if ($reducaoZ != 'C' || $currentControl == 'S') {
                    $reducaoZ = $currentControl;
                }
            }
            if ($abrirGaveta != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 16, 1);
                if ($abrirGaveta != 'C' || $currentControl == 'S') {
                    $abrirGaveta = $currentControl;
                }
            }
            if ($observacaoPedido != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 17, 1);
                if ($observacaoPedido != 'C' || $currentControl == 'S') {
                    $observacaoPedido = $currentControl;
                }
            }
            if ($fecharCaixa != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 18, 1);
                if ($fecharCaixa != 'C' || $currentControl == 'S') {
                    $fecharCaixa = $currentControl;
                }
            }
            if ($bloquearCaixa != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 19, 1);
                if ($bloquearCaixa != 'C' || $currentControl == 'S') {
                    $bloquearCaixa = $currentControl;
                }
            }
            if ($liberarMesa != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 20, 1);
                if ($liberarMesa != 'C' || $currentControl == 'S') {
                    $liberarMesa = $currentControl;
                }
            }
            if ($retirarConsumacao != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 21, 1);
                if ($retirarConsumacao != 'C' || $currentControl == 'S') {
                    $retirarConsumacao = $currentControl;
                }
            }
            if ($retirarCouvert != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 22, 1);
                if ($retirarCouvert != 'C' || $currentControl == 'S') {
                    $retirarCouvert = $currentControl;
                }
            }

            if ($retirarTaxaServico != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 23, 1);
                if ($retirarTaxaServico != 'C' || $currentControl == 'S') {
                    $retirarTaxaServico = $currentControl;
                }
            }

            if ($agruparMesas != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 24, 1);
                if ($agruparMesas != 'C' || $currentControl == 'S') {
                    $agruparMesas = $currentControl;
                }
            }

            if ($cancelarAgrupamento != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 25, 1);
                if ($cancelarAgrupamento != 'C' || $currentControl == 'S') {
                    $cancelarAgrupamento = $currentControl;
                }
            }

            if ($transferirMesa != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 26, 1);
                if ($transferirMesa != 'C' || $currentControl == 'S') {
                    $transferirMesa = $currentControl;
                }
            }

            if ($transferirProduto != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 27, 1);
                if ($transferirProduto != 'C' || $currentControl == 'S') {
                    $transferirProduto = $currentControl;
                }
            }

            if ($relatorioProdutos != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 28, 1);
                if ($relatorioProdutos != 'C' || $currentControl == 'S') {
                    $relatorioProdutos = $currentControl;
                }
            }
            if ($pesquisarProdutos != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 29, 1);
                if ($pesquisarProdutos != 'C' || $currentControl == 'S') {
                    $pesquisarProdutos = $currentControl;
                }
            }
            if ($importarDados != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 30, 1);
                if ($importarDados != 'C' || $currentControl == 'S') {
                    $importarDados = $currentControl;
                }
            }
            if ($posicaoMesa != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 31, 1);
                if ($posicaoMesa != 'C' || $currentControl == 'S') {
                    $posicaoMesa = $currentControl;
                }
            }
            if ($reservarMesa != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 32, 1);
                if ($reservarMesa != 'C' || $currentControl == 'S') {
                    $reservarMesa = $currentControl;
                }
            }
            if ($alterarQtPessoas != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 33, 1);
                if ($alterarQtPessoas != 'C' || $currentControl == 'S') {
                    $alterarQtPessoas = $currentControl;
                }
            }
            if ($parcialConta != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 34, 1);
                if ($parcialConta != 'C' || $currentControl == 'S') {
                    $parcialConta = $currentControl;
                }
            }
            if ($aberturaCaixa != 'S') {
                $currentControl = substr($item['DSSENHACAIXA'], 35, 1);
                if ($aberturaCaixa != 'C' || $currentControl == 'S') {
                    $aberturaCaixa = $currentControl;
                }
            }
            if ($bloqueiaProduto != 'S') {
                $currentControl = !substr($item['DSSENHACAIXA'], 40, 1) ? 'S' : substr($item['DSSENHACAIXA'], 40, 1);
                if ($bloqueiaProduto != 'C' || $currentControl == 'S') {
                    $bloqueiaProduto = $currentControl;
                }
            }

            if ($reimpressaoFiscal != 'S') {
                $currentControl = !substr($item['DSSENHACAIXA'], 41, 1) ? 'S' : substr($item['DSSENHACAIXA'], 41, 1);
                if ($reimpressaoFiscal != 'C' || $currentControl == 'S') {
                    $reimpressaoFiscal = $currentControl;
                }
            }

            if ($bloqueiaDispositivo != 'S') {
                $currentControl = !substr($item['DSSENHACAIXA'], 42, 1) ? 'S' : substr($item['DSSENHACAIXA'], 42, 1);
                if ($bloqueiaDispositivo != 'C' || $currentControl == 'S') {
                    $bloqueiaDispositivo = $currentControl;
                }
            }

             if ($mensagemProducao != 'S') {
                $currentControl = !substr($item['DSSENHACAIXA'], 43, 1) ? 'S' : substr($item['DSSENHACAIXA'], 43, 1);
                if ($mensagemProducao != 'C' || $currentControl == 'S') {
                    $mensagemProducao = $currentControl;
                }
            }
        }

        return array(
            'trocarOperador' => $trocarOperador,
            'devolucaoItem' => $devolucaoItem,
            'cancelaCupom' => $cancelaCupom,
            'cancelaUltimoItem' => $cancelaUltimoItem,
            'cancelaItemGenerico' => $cancelaItemGenerico,
            'cancelaMesaComanda' => $cancelaMesaComanda,
            'consultaSaldo' => $consultaSaldo,
            'cupomDesconto' => $cupomDesconto,
            'produtoCombinado' => $produtoCombinado,
            'consultaPreco' => $consultaPreco,
            'relatoriosFiscais' => $relatoriosFiscais,
            'administracaoTEF' => $administracaoTEF,
            'sangria' => $sangria,
            'suprimento' => $suprimento,
            'leituraX' => $leituraX,
            'reducaoZ' => $reducaoZ,
            'abrirGaveta' => $abrirGaveta,
            'observacaoPedido' => $observacaoPedido,
            'fecharCaixa' => $fecharCaixa,
            'bloquearCaixa' => $bloquearCaixa,
            'liberarMesa' => $liberarMesa,
            'retirarConsumacao' => $retirarConsumacao,
            'retirarCouvert' => $retirarCouvert,
            'retirarTaxaServico' => $retirarTaxaServico,
            'agruparMesas' => $agruparMesas,
            'cancelarAgrupamento' => $cancelarAgrupamento,
            'transferirMesa' => $transferirMesa,
            'transferirProduto' => $transferirProduto,
            'relatorioProdutos' => $relatorioProdutos,
            'pesquisarProdutos' => $pesquisarProdutos,
            'importarDados' => $importarDados,
            'posicaoMesa' => $posicaoMesa,
            'reservarMesa' => $reservarMesa,
            'alterarQtPessoas' => $alterarQtPessoas,
            'parcialConta' => $parcialConta,
            'aberturaCaixa' => $aberturaCaixa,
            'bloqueiaProduto' => $bloqueiaProduto,
            'reimpressaoFiscal' => $reimpressaoFiscal,
            'bloqueiaDispositivo' => $bloqueiaDispositivo,
            'mensagemProducao' => $mensagemProducao
        );
    }

}