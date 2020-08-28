<?php
namespace Controller;

use Zeedhi\Framework\DTO\Response;
use Zeedhi\Framework\DTO\Request;
use Zeedhi\Framework\DataSource\DataSet;

class Menu {

    private function getGroupMenu(){
        $containers = json_decode(file_get_contents(__DIR__ . '/../../../mobile/json/containers.json'), true);
        return $containers['zeedhi_project']['groupMenu'];
    }

	public function buildMenu(Request $request, Response $response){
		try {
            $groupMenu = $this->getGroupMenu();
            $response->addDataSet(new DataSet('menu', $groupMenu));
		} catch (\Exception $e) {
			$response->addDataSet(new DataSet('branch', array('error' => true, 'message'=>$e->getMessage())));
		}
    }
    
    public function requestToken(Request $request, Response $response) {
        try {
            $token = 'token';
            $response->addDataSet(new DataSet('token', array($token)));
        } catch (\Exception $e){
            $response->setError(new Response\Error($e->getMessage(), Response\Message::TYPE_ERROR));
        }
    }
}