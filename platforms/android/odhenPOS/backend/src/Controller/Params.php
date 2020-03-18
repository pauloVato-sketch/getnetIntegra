<?php

namespace Controller;

use Zeedhi\Framework\DTO\Response;
use Zeedhi\Framework\DTO\Request;
use Zeedhi\Framework\DTO\Response\Message;
use Zeedhi\Framework\DTO\Response\Error;
use \Util\Exception;

class Params extends \Zeedhi\Framework\Controller\Simple {

	protected $waiterMessage;
	protected $util;
	protected $instanceManager;
	protected $paramsService;

	public function __construct(
		\Zeedhi\Framework\DependencyInjection\InstanceManager $instanceManager,
		\Util\WaiterMessage $waiterMessage,
		\Util\Util $util,
		\Service\Params $paramsService
	){
		$this->instanceManager = $instanceManager;
		$this->waiterMessage   = $waiterMessage;
		$this->util            = $util;
		$this->paramsService   = $paramsService;
	}

    public function getParams($chave, $ip = null){
        try {
            $this->IMAGESPATH = $this->instanceManager->getParameter('IMAGESPATH');

            $dataset = array(
                'chave' => $chave
            );
            $session = $this->util->getSessionVars($chave);

            $result = $this->paramsService->carregaDados($dataset);
            if ($result['error'] == false) {
                $result = $result['dados'];
                $result['recebimentos'] = $this->handleRecebimento($result['recebimentos'], $chave);

                $paramsData = array(
                    'funcao'               => '1',
                    'ambientes'            => $this->handleAmbientes($result['ambientes']),
                    'grupos'               => $this->handleGrupos($result['grupos']),
                    'clientes'             => $this->handleClientes($result['clientes']),
                    'familias'             => $this->paramsService->getFamilies($session['CDFILIAL']),
                    'consumidores'         => $result['consumidores'],
                    'vendedores'           => $this->handleVendedores($result['vendedores']),
                    'cardapio'             => $this->handleCardapio($result['grupos'], $ip),
                    'grupoRecebimentos'    => $this->handleGrupoRecebimentos($result['grupoRecebimentos'], $result['recebimentos']),
                    'recebimentos'         => $result['recebimentos'],
                    'impressoras'          => $this->handleImpressoras($result['impressoras']),
                    'mensObservacao'       => $this->handleMensObservacao($result['mensObservacao']),
                    'mensCancelamento'     => $this->handleMensCancelamento($result['mensCancelamento']),
                    'mensProducao'         => $this->handleMensProducao($result['mensProducao']),
                    'parametros'           => $this->handleParametros($result['parametros']),
                    'ALL_THE_OBSERVATIONS' => $result['observacoes'],
                    'nextUpdateTime'       => $this->util->getNextUpdateTime($result['horarioDePrecos']),
                    'smartPromoProducts'   => $result['smartPromoProducts'],
                    'mensDescontoObs'       => $this->handleMensDescontoObs($result['mensDescontoObs'])
                );

                $return = array(
                    'error' => false,
                    'dados' => $paramsData
                );
            } else {
                $return = $result;
            }
            return $return;
        } catch (\Exception $e){
            return array(
                'error' => true,
                'message' => false,
                'dump' => $e->getMessage()
            );
        }
    }

	private function handleAmbientes($amb) {
		$ambientes = array();
		foreach ($amb as $ambiente) {
			array_push($ambientes, array(
				'CDSALA' => $ambiente['area']['CDSALA'],
				'NMSALA' => $ambiente['area']['NMSALA'],
				'NRBUTTON' => $ambiente['area']['NRBUTTON']
			));
		}
		usort($ambientes, array("\Controller\Params", "cmp"));
		return $ambientes;
	}

	private static function cmp($a, $b){
			$retorno = strcmp($a["NRBUTTON"], $b["NRBUTTON"]);
			return $retorno;
	}

	private function handleGrupos($grp) {
		$grupos = array();
		foreach ($grp as $grupo) {
			array_push($grupos, array(
				'CDGRUPO' => $grupo['grupo']['CODIGO'],
				'NMGRUPO' => $grupo['grupo']['DESC'],
				'COLOR'  => "#" . $this->util->intToHexa($grupo['grupo']['COLOR']),
				'PRODUTOS' => $grupo['produtos']
			));
		}
		return $grupos;
	}

	private function handleClientes($clientes){
		$retorno = array();
		foreach ($clientes as $cliente) {
			array_push($retorno, array(
				'CDCLIENTE'    => $cliente['CDCLIENTE'],
				'CDFILTABPREC' => $cliente['CDFILTABPREC'],
				'CDTABEPREC'   => $cliente['CDTABEPREC'],
				'NMRAZSOCCLIE' => $cliente['NMRAZSOCCLIE'],
				'NRINSJURCLIE' => $cliente['NRINSJURCLIE']
			));
		}
		return $retorno;
	}

	private function handleVendedores($vendedores){
		$retorno = array();

		foreach ($vendedores as $vendedor) {
			array_push($retorno, array(
				'CDVENDEDOR'    => $vendedor['CDVENDEDOR'],
				'NMFANVEN'      => $vendedor['NMFANVEN'],
				'DESCVENDEDOR'  => $vendedor['CDVENDEDOR'] . " - " . $vendedor['NMFANVEN']
			));
		}
		return $retorno;
	}

	private function handleConsumidores($consumidores){
		$retorno = array();
		foreach ($consumidores as $consumidor) {
			array_push($retorno, array(
				'CDCONSUMIDOR'    => $consumidor['CDCONSUMIDOR'],
				'CDCLIENTE'       => $consumidor['CDCLIENTE'],
				'NMCONSUMIDOR'    => $consumidor['NMCONSUMIDOR'],
				'NMRAZSOCCLIE'    => $consumidor['NMRAZSOCCLIE']
			));
		}
		return $retorno;
	}

	private function handleCardapio($grupos, $ip = null) {
		$retorno = array();
		foreach ($grupos as $grupo) {
			foreach ($grupo['produtos'] as $produto) {
				array_push($retorno, array(
					// product properties
					'CDARVPROD'      => $produto['CDARVPROD'],
					'CDBARPRODUTO'   => $produto['CDBARPRODUTO'],
					'CDPRODINTE'     => $produto['CDPRODINTE'],
					'CDPRODUTO'      => $produto['CDPRODUTO'],
					'DSBUTTON'       => $produto['NMPRODUTO'],
					'DSPRODVENDA'    => $produto['DSPRODVENDA'],
					'GRUPOS'         => $produto['GRUPOS'],
					'OBSERVATIONS'   => $produto['OBSERVACOES'],
					'IMPRESSORAS'    => $produto['IMPRESSORAS'],
					'DTFINVGPROMOC'  => $produto['DTFINVGPROMOC'],
					'DTINIVGPROMOC'  => $produto['DTINIVGPROMOC'],
					'IDIMPPRODUTO'   => $produto['IDIMPPRODUTO'],
					'IDPESAPROD'     => $produto['IDPESAPROD'],
					'IDPRODBLOQ'     => $produto['IDPRODBLOQ'],
					'IDTIPOCOMPPROD' => $produto['IDTIPOCOMPPROD'],
					'IDTIPOPROD'     => $produto['IDTIPOPROD'],
					'IDTIPCOBRA'     => empty($produto['IDTIPCOBRA']) ? null : $produto['IDTIPCOBRA'],
					'PRECO'          => '' . number_format($produto['VRPRECITEM'], 2, ',', '.'),
					'PRITEM'         => $produto['VRPRECITEM'],
                    'VRDESITVEND'    => $produto['VRDESITVEND'],
                    'VRACRITVEND'    => $produto['VRACRITVEND'],
                    'VRPRECITEMCL'   => $produto['VRPRECITEMCL'],
					'COLOR'          => "#" . $this->util->intToHexa($produto['NRCOLORBACK']),
					'IMAGEM'         => self::handleImagePath($this->IMAGESPATH . 'p/' . $produto['CDARVPROD'] . '.jpg', $ip),
					'REFIL'          => $produto['IDCONTROLAREFIL'],
					// group properties
					'CDGRUPO'        => $grupo['grupo']['CODIGO'],
					'IMGGRUPO'       => self::handleImagePathGroup($this->IMAGESPATH . 'g/' . $grupo['grupo']['CODIGO'] . '.jpg', $ip),
					'NMGRUPO'        => $grupo['grupo']['DESC'],
					'color'          => '#333333',
					'NRQTDMINOBS'    => $produto['NRQTDMINOBS'],
					'CDPROTECLADO'   => $produto['CDPROTECLADO'],
					'HRINIVENPROD'   => floatval($produto['HRINIVENPROD']),
					'HRFIMVENPROD'   => floatval($produto['HRFIMVENPROD']),
					'CDCLASFISC'   	 => $produto['CDCLASFISC'],
					'CDCFOPPFIS'   	 => $produto['CDCFOPPFIS'],
					'CDCSTICMS'   	 => $produto['CDCSTICMS'],
					'CDCSTPISCOF'    => $produto['CDCSTPISCOF'],
					'VRALIQPIS'   	 => $produto['VRALIQPIS'],
					'VRALIQCOFINS'   => $produto['VRALIQCOFINS']
				));
			}
		}
		return $retorno;
	}

	public function handleGrupoRecebimentos($grupoRecebimentos, $recebimentos){
		$retorno = array();
		$arrNrbuttonaux = array_unique(array_column($recebimentos, 'CDGRUPO'));
		// monta grupo de recebimento de acordo com os TIPORECE existentes
		foreach ($grupoRecebimentos as $grupo) {
			if (in_array($grupo['NRBUTTON'], $arrNrbuttonaux)){
				array_push($retorno, array(
					'CDGRUPO'  => $grupo['NRBUTTON'],
					'NMGRUPO' => $grupo['DSBUTTON'],
					'COLOR'    => "#" . $this->util->intToHexa($grupo['NRCOLORBACK'])
				));
			}
		}

		return $retorno;
	}

	public function handleRecebimento($recebimentos, $chave){
		$session = $this->util->getSessionVars($chave);
		$IDCOLETOR = $session['IDCOLETOR'];

		$retorno = array();
		foreach ($recebimentos as $recebimento) {
			// caixa com IDCOLETOR === 'C' traz somente recebimentos vinculado a transação eletrônica
			if ($IDCOLETOR !== 'C' || (in_array($recebimento['IDTIPORECE'], array('1', '2')) && $IDCOLETOR === 'C')) {
				array_push($retorno, array(
					'CDTIPORECE' => $recebimento['CDTIPORECE'],
					'IDTIPORECE' => $recebimento['IDTIPORECE'],
					'IDDESABTEF' => $recebimento['IDDESABTEF'],
					'DSBUTTON'   => $recebimento['DSBUTTON'],
					'NRBUTTON'   => $recebimento['NRBUTTON'],
					'COLOR'      => "#" . $this->util->intToHexa($recebimento['NRCOLORBACK']),
					'CDGRUPO'    => $recebimento['NRBUTTONAUX']
				));
			}
		}

		return $retorno;
	}

	private function returnWebUri ($path, $ip) {
		return $ip . '/' . $path;
	}

	private function handleImagePath ($path, $ip) {
		//Retorna imagem padrão caso imagem não exista.
		$path = (file_exists('../../../' . $path)) ? $path : $this->IMAGESPATH . 'no-image.jpg';
		return self::returnWebUri($path, $ip);
	}

	private function handleImagePathGroup ($path, $ip) {
		//Retorna imagem padrão caso imagem não exista.
		$path = (file_exists('../../../' . $path)) ? $path : $this->IMAGESPATH . 'nogroup.png';
		if (file_exists('../../../' . $path)) return self::returnWebUri($path, $ip);
		else return null;
	}

	private function handleImpressoras($impressoras){
		$retorno = array();
		foreach ($impressoras as $impressora) {
			array_push($retorno, array(
				'NRSEQIMPRLOJA' => $impressora['codigo'],
				'NMIMPRLOJA'    => $impressora['impressora']
			));
		}
		return $retorno;
	}

	private function handleMensObservacao($mensObservacao){
		$retorno = array();
		if(is_array($mensObservacao)){
			foreach ($mensObservacao as $observacao) {
				array_push($retorno, array(
					'CDOCORR' => $observacao['codigo'],
					'DSOCORR' => $observacao['mensagem']
				));
			}
		}
		return $retorno;
	}

	private function handleMensCancelamento($mensObservacao){
		$retorno = array();
		foreach ($mensObservacao as $observacao) {
			array_push($retorno, array(
				'CDGRPOCOR' => $observacao['grupo'],
				'CDOCORR'   => $observacao['codigo'],
				'DSOCORR'   => $observacao['mensagem']
			));
		}
		return $retorno;
	}

	private function handleMensProducao($mensProducao){
		$retorno = array();
		foreach ($mensProducao as $observacao) {
			array_push($retorno, array(
				'CDOCORR' => $observacao['codigo'],
				'DSOCORR' => $observacao['mensagem']
			));
		}
		return $retorno;
	}

	private function handleParametros($parametros){

		$retorno = array();
		array_push($retorno, array(
			'NRMESAPADRAO' => $parametros['NRMESAPADRAO'],
			'IDCOMANDAAUT' => $parametros['comandaAuto'],
			'IDCONSUMAMIN' => $parametros['consumacao'],
			'IDLUGARMESA'  => $parametros['controlaPos'],
			'IDCOUVERART'  => $parametros['couvert'],
			'IDCOMISVENDA' => $parametros['taxaServico'],
			'PRECOCONSUMA' => $parametros['valorConsumacao'],
			'CDVENDPADRAO' => $parametros['CDVENDPADRAO'],
			'IDINFVENDCOM' => $parametros['IDINFVENDCOM'],
			'NRATRAPADRAO' => $parametros['NRATRAPADRAO'],
			'VRCOMISVENDA' => $parametros['VRCOMISVENDA'],
			'VRCOMISVENDA2'=> $parametros['VRCOMISVENDA2'],
			'VRCOMISVENDA3'=> $parametros['VRCOMISVENDA3'],
			'VRMAXDESCONTO'=> $parametros['VRMAXDESCONTO'],
			'IDIMPPEDPROD' => $parametros['IDIMPPEDPROD']
		));

		return $retorno;
	}

	public function getConsumersByClient(Request\Filter $request, Response $response){
		try {
			$params    = $request->getFilter();
			$chave     = $params[0]['value'];
			$CDCLIENTE = $params[1]['value'];

			$consumers = $this->paramsService->getConsumersByClient($chave, $CDCLIENTE);

			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('ConsumerRepository', $consumers));

		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

    public function getPagedConsumers(Request\Filter $request, Response $response){
		set_time_limit(120);
        try {
            $params = $request->getFilterCriteria()->getConditions();

            $CDCLIENTE = !empty($params[0]['value']) ? $params[0]['value'] : 'T';

            if (!empty($params[1])) $CDCONSUMIDOR = '%' . $params[1]['value'] . '%';
            else $CDCONSUMIDOR = "%%";

            $page = $request->getFilterCriteria()->getPage();
            $pageSize = $request->getFilterCriteria()->getPageSize();
            $FIRST = $request->getFilterCriteria()->getPageSize() * ($page-1);
            $LAST = ($FIRST + $pageSize);
            if ($page > 1) $FIRST++;

            $consumers = $this->paramsService->getPagedConsumers($CDCLIENTE, $CDCONSUMIDOR, $FIRST, $LAST);

            $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('ParamsCustomerRepository', $consumers));

        } catch (\Exception $e) {
            Exception::logException($e);
            $response->addMessage(new Message($e->getMessage()));
        }
    }

	public function buscaCardapio(Request\Filter $request, Response $response){
		try {
			$params = $request->getFilterCriteria()->getConditions();

			$chave = $params[0]['value'];
			$result = $this->paramsService->buscaCardapio($chave);

			$this->IMAGESPATH = $this->instanceManager->getParameter('IMAGESPATH');
			$cardapio = $this->handleCardapio($result['cardapio']);

            $horarioDePrecos = $result['horarioDePrecos'];
            $nextupdateTime = array($this->util->getNextUpdateTime($horarioDePrecos));
            $smartPromoProducts = $result['smartPromoProducts'];

			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('ParamsMenuRepository', $cardapio));
            $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('ParamsPriceTimeRepository', $nextupdateTime));
            $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('SmartPromoRepository', array(array(json_encode($smartPromoProducts)))));
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	private function handleMensDescontoObs($mensDescontoObs){
		$retorno = array();
		if(is_array($mensDescontoObs)){
			foreach ($mensDescontoObs as $observacao) {
				array_push($retorno, array(
					'CDOCORR' => $observacao['codigo'],
					'DSOCORR' => $observacao['mensagem']
				));
			}
		}
		return $retorno;
	}

}