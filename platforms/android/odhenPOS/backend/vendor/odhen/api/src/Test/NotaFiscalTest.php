<?php

namespace Odhen\API\Test;

use Odhen\API\Service\NotaFiscal;

class NotaFiscalTest extends NotaFiscal {

    public function __construct(
        \Doctrine\ORM\EntityManager $entityManager,
		\Odhen\API\Util\Util $util,
		\Odhen\API\Util\Database $databaseUtil,
		\Odhen\API\Service\ImpressaoNFCE $impressaoNFCE,
        \Zeedhi\Framework\DependencyInjection\InstanceManager $instanceManager) {

        parent::__construct($entityManager, $util, $databaseUtil, $impressaoNFCE, $instanceManager);
        $this->nfcePath = './mocks/NFCE/';
    }

    public function getConfigNFCEElement($fileName, $elementName, $nfceParams = array()) {
        $result = array();
        $pathFile = $this->nfcePath . DIRECTORY_SEPARATOR . "CONFIG" . DIRECTORY_SEPARATOR . $fileName . ".json";
        try {
            $fileContent = file_get_contents($pathFile);
            $arrContent = json_decode($fileContent, true);
            $result['error'] = false;
            $result['content'] = $arrContent[$elementName];
        } catch(\Exception $e){
            try{
                $this->criaDiretoriosNfce();
                $fileContent = file_get_contents(__DIR__. DIRECTORY_SEPARATOR. '..'. DIRECTORY_SEPARATOR .'Util'. DIRECTORY_SEPARATOR . "nfceDefaultConfig.mock.json");
                $arrContent = json_decode($fileContent, true);
                if(!empty($nfceParams)){
                    $missingParameters = array();
                    foreach($nfceParams as $key => $nfceParam) {
                        if($nfceParam === NULL || $nfceParam === '') {
                            array_push($missingParameters, $key);
                        }
                    }

                    if($missingParameters) {
                        $result['error'] = true;
                        $result['message'] = 'Não foi possível utilizar o NFCE. ' . $this->nfceMissingParameterMessage($missingParameters);
                    } else {
                        $arrContent['razaosocial']   = $nfceParams['NMRAZSOCFILI'];
                        $arrContent['siglaUF']       = $nfceParams['SGESTADO'];
                        $arrContent['cnpj']          = $nfceParams['NRINSJURFILI'];
                        if ($nfceParams['IDAMBTRABNFCE'] == '2') { //2 = homologacao, senao producao
							$arrContent['tokenNFCe']     = $nfceParams['CDCODSCONSHOMO'];
							$arrContent['tokenNFCeId']   = $nfceParams['CDIDTOKENHOMO'];
						} else {
							$arrContent['tokenNFCe']     = $nfceParams['CDCODSCONSPROD'];
							$arrContent['tokenNFCeId']   = $nfceParams['CDIDTOKENPROD'];
						}
                        $arrContent['certPfxName']   = $nfceParams['NMARQCERTNFCE'];
                        $arrContent['certPassword']  = $nfceParams['DSSENHACERTNFCE'];

                        file_put_contents($this->nfcePath . DIRECTORY_SEPARATOR . "CONFIG" . DIRECTORY_SEPARATOR . $fileName . ".json",  json_encode($arrContent));
                        //cria arquivo XML tambem
                        $XMLfileContent = file_get_contents(__DIR__. DIRECTORY_SEPARATOR. '..'. DIRECTORY_SEPARATOR .'Util'. DIRECTORY_SEPARATOR . "nfe_ws3_mod65.xml");
                        file_put_contents($this->nfcePath . DIRECTORY_SEPARATOR . "CONFIG" . DIRECTORY_SEPARATOR . "nfe_ws3_mod65.xml", $XMLfileContent);

						$result['error'] = false;
						$result['content'] = $arrContent[$elementName];
					}
                }else{
                    $result['error'] = true;
                    $result['message'] = 'Parametros de configuração do NFCE não encontrados no banco';
                }
            } catch(\Exception $e){
                $result['error'] = true;
                $result['message'] = 'Falha ao gerar o arquivo de configuração do NFCE.';
            }
        }
        return $result;
    }

    protected function transmitirXML($nfeTools, $signedXMLName, $IDAMBTRABNFCE) {
        return array(
		    'bStat' => true,
            'versao' => "3.10",
            'tpAmb' => "2",
            'verAplic' => "SVRSnfce201712041453",
            'cStat' => "104",
            'xMotivo' => "Lote processado",
            'cUF' => "26",
            'dhRecbto' => "2018-01-11T14:32:24-03:00",
            'tMed' => "",
            'nRec' => "",
            'prot' => array(array(
                'chNFe' => "26180128643028000166651000000000711000001978",
                'dhRecbto' => "2018-01-11T14:32:24-03:00",
                'nProt' => "",
                'digVal' => "k3Qu2KOxGhUoeJQ9jBT3+w56x7s=",
                'cStat' => "464",
                'xMotivo' => "Rejeicao: Codigo de Hash no QR-Code difere do calculado"
            ))
        );
	}

}