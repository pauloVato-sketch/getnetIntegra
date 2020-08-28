<?php

namespace Odhen\API\Service;

use Odhen\API\Util\Exception;

class Fidelidade {

    protected $entityManager;
    protected $databaseUtil;

    public function __construct(\Doctrine\ORM\EntityManager $entityManager, \Odhen\API\Util\Database $databaseUtil) {
        $this->entityManager = $entityManager;
        $this->databaseUtil = $databaseUtil;
    }

    public function validaFidelidade($CDIDCONSUMID) {
        try {
            $connection = $this->entityManager->getConnection();

            $params = array(
                'CDIDCONSUMID' => $CDIDCONSUMID
            );

            $consumidor = $this->entityManager->getConnection()->fetchAssoc("DADOS_CONSUMIDOR", $params);
            if (!empty($consumidor)) {
                $params = array(
                    'CDCLIENTE'    => $consumidor['CDCLIENTE'],
                    'CDCONSUMIDOR' => $consumidor['CDCONSUMIDOR']
                );
                $beneficios = $this->entityManager->getConnection()->fetchAll("BENEFICIOS_CONSUMIDOR", $params);
                $produtos = array();
                foreach ($beneficios as $beneficioAtual) {
                    $validacaoPeriodo = self::validaPeriodoCampanha($beneficioAtual['DTINIVALBENECONS'], $beneficioAtual['DTFIMVALBENECONS']);
                    if ($validacaoPeriodo['error'] == false) {
                        $params = array(
                            'CDCLIENTE' => $consumidor['CDCLIENTE'],
                            'CDCONSUMIDOR' => $consumidor['CDCONSUMIDOR'],
                            'NRSEQBENEFICIO' => $beneficioAtual['NRSEQBENEFICIO'],
                            'CDCAMPANHA' => $beneficioAtual['CDCAMPANHA']
                        );
                        $produtosCampanha = $this->entityManager->getConnection()->fetchAll("PRODUTOS_CAMPANHA", $params);

                        $produtos = array_merge($produtos, $produtosCampanha);
                    } else {
                        return $validacaoPeriodo;
                    }
                }
                if (!empty($produtos)) {
                    return array(
                        'error' => false,
                        'produtos' => $produtos,
                        'consumidor' => $consumidor
                    );
                } else {
                    return array(
                        'error' => true,
                        'message' => 'Cartão não possui beneficios disponíveis no sistema.'
                    );
                }
            } else {
                // Cartão não é válido
                return array(
                    'error' => true,
                    'message' => 'Cartão inválido.'
                );
            }
        } catch(\Exception $e) {
            Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
            return array(
                'error' => true,
                'message' => $e->getMessage()
            );
        }
    }


    private function validaPeriodoCampanha($DTINIVALBENECONS, $DTFIMVALBENECONS) {
        try {
            $DTINIVALBENECONS = $this->databaseUtil->convertToDateGeneric('Y-m-d H:i:s.u', $DTINIVALBENECONS);
            $DTFIMVALBENECONS = $this->databaseUtil->convertToDateGeneric('Y-m-d H:i:s.u', $DTFIMVALBENECONS);
            if ($DTINIVALBENECONS > $DTFIMVALBENECONS) {
                return array(
                    'error' => true,
                    'message' => 'Data do início do beneficio maior que a data de término.'
                );
            } elseif ($DTINIVALBENECONS == $DTFIMVALBENECONS) {
                return array(
                    'error' => true,
                    'message' => 'Data do início do beneficio igual a data de término.'
                );
            } else {
                $currentDate = new \DateTime("now");
                if ($currentDate > $DTFIMVALBENECONS) {
                    return array(
                        'error' => true,
                        'message' => 'Cartão fidelidade vencido.'
                    );
                } else {
                    return array(
                        'error' => false
                    );
                }
            }
        } catch(\Exception $e) {
            Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
            return array(
                'error' => true,
                'message' => $e->getMessage()
            );
        }
    }
}