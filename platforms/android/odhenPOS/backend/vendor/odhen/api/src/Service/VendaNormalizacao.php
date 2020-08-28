<?php

namespace Odhen\API\Service;

class VendaNormalizacao {

	protected $entityManager;

    public function __construct(\Doctrine\ORM\EntityManager $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function normalizaVenda($CDFILIAL, $CDCAIXA, $ITEMVENDA, $TIPORECE, $VRTROCOVEND) {

        return array(
            'IDTPEMISVEND' => $this->normalizaTipoEmissao($CDFILIAL, $CDCAIXA),
            'ITEMVENDA' => $this->normalizaItemVenda($ITEMVENDA),
            'TIPORECE' => $this->normalizaTipoRece($TIPORECE),
            'VRTROCOVEND' => $this->normalizaTroco($VRTROCOVEND)
        );
    }

    protected function normalizaTroco($VRTROCOVEND) {
        if (isset($VRTROCOVEND['VRMOVIVEND'])) {
            if ($VRTROCOVEND['VRMOVIVEND'] <= 0) {
                $VRTROCOVEND = array();
            }
        }
        return $VRTROCOVEND;
    }

    public function normalizaTipoEmissao($CDFILIAL, $CDCAIXA) {
        $params = array(
			'CDFILIAL' => $CDFILIAL,
			'CDCAIXA'  => $CDCAIXA
		);
        $dadosCaixa = $this->entityManager->getConnection()->fetchAssoc("CAIXA", $params);
        $IDTPEMISSAOFOS = $dadosCaixa['IDTPEMISSAOFOS'];
		switch($IDTPEMISSAOFOS) {
			case 'ECF':
				return 'E';
			case 'SAT':
				return 'S';
			case 'FNC': // NFCE
				return 'N';
		}
    }

    public function normalizaItemVenda($ITEMVENDA) {
        if (!is_array($ITEMVENDA)) {
            $ITEMVENDA = array();
        }

        foreach($ITEMVENDA as &$itemAtual) {
            if (!isset($itemAtual['itensCombo'])) {
                $itemAtual['itensCombo'] = array();
            } else if (!empty($itemAtual['itensCombo'])) {
                $itemAtual['itensCombo'] = $this->normalizaItemVenda($itemAtual['itensCombo']);
            }
            $params = array(
                'CDPRODUTO' => $itemAtual['CDPRODUTO']
            );
            $dadosProduto = $this->entityManager->getConnection()->fetchAssoc("PRODUTO", $params);
            if (!isset($itemAtual['OBSERVACOES'])) {
                $itemAtual['OBSERVACOES'] = array();
            }
            if (!isset($itemAtual['NMPRODUTO'])) {
                $itemAtual['NMPRODUTO'] = $dadosProduto['NMPRODUTO'];
            }
            if (!isset($itemAtual['IDSITUITEM'])) {
                $itemAtual['IDSITUITEM'] = 'A';
            }
            if (!isset($itemAtual['IDTIPOCOMPPROD'])) {
                $itemAtual['IDTIPOCOMPPROD'] = $dadosProduto['IDTIPOCOMPPROD'];
            }
            if (!isset($itemAtual['IDIMPPRODUTO'])) {
                $itemAtual['IDIMPPRODUTO'] = $dadosProduto['IDIMPPRODUTO'];
            }
            if (!isset($itemAtual['VRDESITVEND'])) {
                $itemAtual['VRDESITVEND'] = 0;
            }
            if (!isset($itemAtual['VRACRITVEND'])) {
                $itemAtual['VRACRITVEND'] = 0;
            }
            if (!isset($itemAtual['IDTIPOITEM'])) {
                $itemAtual['IDTIPOITEM'] = null;
            }
            if (!isset($itemAtual['DSOBSITEMVENDA'])) {
                $itemAtual['DSOBSITEMVENDA'] = null;
            }
            if (!isset($itemAtual['DSOBSPEDDIGITA'])) {
                $itemAtual['DSOBSPEDDIGITA'] = null;
            }
        }
        return $ITEMVENDA;
    }

    protected function normalizaTipoRece($TIPORECE) {
        if (empty($TIPORECE)) {
            $TIPORECE = array();
        }
        foreach($TIPORECE as &$recebimentoAtual) {
            $params = array(
                'CDTIPORECE' => $recebimentoAtual['CDTIPORECE']
            );
            $dadosRecebimento = $this->entityManager->getConnection()->fetchAssoc("TIPORECE", $params);
            if (empty($recebimentoAtual['IDTIPORECE'])) {
                $recebimentoAtual['IDTIPORECE'] = $dadosRecebimento['IDTIPORECE'];
            }
            if (empty($recebimentoAtual['CDNSUHOSTTEF'])) {
                $recebimentoAtual['CDNSUHOSTTEF'] = null;
            }
            if (empty($recebimentoAtual['NRCONTROLTEF'])) {
                $recebimentoAtual['NRCONTROLTEF'] = null;
            }
            if (empty($recebimentoAtual['CDBANCARTCR'])) {
                $recebimentoAtual['CDBANCARTCR'] = null;
            }
        }

        return $TIPORECE;
    }

}