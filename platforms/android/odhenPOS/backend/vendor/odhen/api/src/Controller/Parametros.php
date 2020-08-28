<?php
namespace Odhen\API\Controller;

use Zeedhi\Framework\DTO\Response\Message;
use Zeedhi\Framework\DTO\Response;
use Zeedhi\Framework\DTO\Request;

class Parametros extends \Zeedhi\Framework\Controller\Simple {

	protected $parametrosService;

    public function __construct(\Odhen\API\Service\Parametros $parametrosService) {
    	$this->parametrosService = $parametrosService;
    }

	public function getFiliaisLoginGeral(Request\Filter $request, Response $response){
		try {
			$retorno = $request->getFilterCriteria()->getConditions();
			$CDOPERADOR = $retorno[0]['value'];
			$filiais = $this->filialService->buscaFiliaisByOperador($CDOPERADOR);
		    if (!empty($filiais)) {
		    	return $filiais;
			}
		} catch (\Exception $e) {
			$response->addMessage(new Message($e->getMessage()));
		}
	}

}