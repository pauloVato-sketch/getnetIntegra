<?php

namespace Odhen\API\Service;

use Odhen\API\Util\Exception;

class Login {

    protected $entityManager;
    protected $util;

    public function __construct(\Doctrine\ORM\EntityManager $entityManager, \Odhen\API\Util\Util $util){
        $this->entityManager = $entityManager;
        $this->util = $util;
    }

    public function validaLoginCaixa($CDFILIAL, $CDCAIXA, $CDOPERADOR, $CDSENHOPER_DIGITADA, $IDHABCAIXAVENDA = null){
        try {
            $result = self::validaFilial($CDFILIAL);
            if (!$result['error']){
                self::validaLoja($CDFILIAL, $CDCAIXA);
                self::validaCaixa($CDFILIAL, $CDCAIXA);
                if (!empty($IDHABCAIXAVENDA)) {
                    $result = self::validaModalidadeCaixa($CDFILIAL, $CDCAIXA, $IDHABCAIXAVENDA);
                }
                if (!$result['error']){
                    $result = $this->util->delphiValidaPers($CDFILIAL, $CDCAIXA);
                    if (!$result['error']){
                        if ($IDHABCAIXAVENDA == 'TAA'){
                            $result = array(
                                'error' => false,
                                'CDOPERADOR' => $CDOPERADOR
                            );
                        }
                        if (!$result['error']) {
                            $result = self::validaOperador($CDOPERADOR, $CDFILIAL, $CDSENHOPER_DIGITADA);
                        }
                    }
                }
            }

            return $result;
        } catch (\Exception $e) {
            Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
            return array(
                'error' => true,
                'message' => $e->getMessage()
            );
        }
    }

    private function validaFilial($CDFILIAL) {
        $params = array(
            'CDFILIAL' => $CDFILIAL
        );
        $filial = $this->entityManager->getConnection()->fetchAll("VALIDA_FILIAL", $params);
        if (!empty($filial)) {
            if ($filial[0]['CDCLIENTE'] != null) {
                $result = array(
                    'error' => false
                );
            } else {
                $result = array(
                    'error' => true,
                    'message' => 'Filial não possui cliente padrão.'
                );
            }
        } else {
            $result = array(
                'error' => true,
                'message' => 'Filial não encontrada.'
            );
        }
        return $result;
    }

    private function validaLoja($CDFILIAL, $CDCAIXA){
        $params = array(
            'CDFILIAL' => $CDFILIAL,
            'CDCAIXA' => $CDCAIXA
        );
        $loja = $this->entityManager->getConnection()->fetchAssoc("VALIDA_LOJA", $params);
        if (empty($loja)) throw new \Exception("Loja não encontrada.");

        if ($loja['IDCOMISVENDA'] === 'S' && $loja['IDTRATTAXASERV'] === 'P'){
            if (empty($loja['CDPRODTAXASERV'])) throw new \Exception("Produto da taxa de serviço não parametrizado.");

            $params = array(
                'CDPRODUTO' => $loja['CDPRODTAXASERV']
            );
            $prodTaxa = $this->entityManager->getConnection()->fetchAssoc("VALIDA_PROD_TAXASERV", $params);
            if (empty($prodTaxa)) throw new \Exception("Produto da taxa de serviço não existe.");
        }

        if ($loja['IDCOUVERART'] === 'S'){
            if (empty($loja['CDPRODCOUVER'])) throw new \Exception("Produto do couvert não parametrizado.");

            $params = array(
                'CDPRODUTO' => $loja['CDPRODCOUVER']
            );
            $prodTaxa = $this->entityManager->getConnection()->fetchAssoc("VALIDA_PROD_TAXASERV", $params);
            if (empty($prodTaxa)) throw new \Exception("Produto do couvert não existe.");
        }
    }

    private function validaCaixa($CDFILIAL, $CDCAIXA) {
        $params = array(
            'CDFILIAL' => $CDFILIAL,
            'CDCAIXA' => $CDCAIXA
        );
        $caixa = $this->entityManager->getConnection()->fetchAll("VALIDA_CAIXA", $params);
        if (!empty($caixa)) {
            $NRCONFTELA = $this->util->getConfTela($CDFILIAL, $CDCAIXA);
            if (empty($NRCONFTELA)) throw new \Exception("A configuração de tela do caixa não foi encontrada.");

            $params = array(
                'CDFILIAL' => $NRCONFTELA['CDFILIAL'],
                'NRCONFTELA' => $NRCONFTELA['NRCONFTELA'],
                'DTINIVIGENCIA' => $NRCONFTELA['DTINIVIGENCIA']
            );
            $types = array(
                'DTINIVIGENCIA' => \Doctrine\DBAL\Types\Type::DATETIME
            );
            $itens = $this->entityManager->getConnection()->fetchAssoc("CONFIGURACAO", $params, $types);
            if ($itens['ITENS'] == 0){
                throw new \Exception("A configuração de tela do caixa não foi encontrada.");
            }
        } else {
            throw new \Exception("Caixa não encontrado.");
        }
    }

    private function validaModalidadeCaixa($CDFILIAL, $CDCAIXA, $IDHABCAIXAVENDA) {
        $params = array(
            'CDFILIAL' => $CDFILIAL,
            'CDCAIXA' => $CDCAIXA,
            'IDHABCAIXAVENDA' => $IDHABCAIXAVENDA
        );
        $modo = $this->entityManager->getConnection()->fetchAll("VALIDA_MODO_HABILITADO", $params);
        if (!empty($modo)) {
            $result = array(
                'error' => false
            );
        } else {
            $result = array(
                'error' => true,
                'message' => 'O caixa não está habilitado para a modalidade selecionada.'
            );
        }
        return $result;
    }

    public function validaOperador($CDOPERADOR, $CDFILIAL, $CDSENHOPER_DIGITADA){
        $params = array(
            'CDOPERADOR' => $CDOPERADOR
        );
        $operador = $this->entityManager->getConnection()->fetchAssoc("VALIDA_OPERADOR", $params);

        try {

            // OPERATOR VALIDATIONS:
            if (empty($operador)) throw new \Exception('Operador não encontrado.');
            if ($operador['CDSENHAOPERWEB'] == null) throw new \Exception('O operador não possui senha cadastrada.');

            $passwordResult = $this->util->validaSenha($CDSENHOPER_DIGITADA, $operador['CDSENHAOPERWEB']);
            if (!$passwordResult) throw new \Exception('Senha inválida.');

            $params = array(
                'CDOPERADOR' => $CDOPERADOR,
                'CDFILIAL' => $CDFILIAL
            );
            $operFilial = $this->entityManager->getConnection()->fetchAll("OPERADOR_FILIAL", $params);
            if (empty($operFilial)) throw new \Exception('Operador não vinculado à filial informada.');

            // RESULT:
            $result = array(
                'error' => false,
                'CDOPERADOR' => $CDOPERADOR
            );
        } catch (\Exception $e){
            Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
            $result = array(
                'error' => true,
                'message' => $e->getMessage()
            );
        }
        return $result;
    }

    public function overthrow($P){
        return $this->util->overthrow($P);
    }

}