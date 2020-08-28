<?php
	require __DIR__.'/../scripts/bootstrap.php';

	$satService = $instanceManager->getService('\Odhen\API\Remote\SAT\SAT');

	$P_FUNCAO = ( isset( $_REQUEST['P_FUNCAO'] ) ? $_REQUEST['P_FUNCAO'] : null );

	$folder = substr(__DIR__, 0, 1) . ":\\TEKNISA\\api\\logs\\";
    if (!file_exists($folder)) {
        mkdir($folder, 0777, true);
    }

    $P_SATINFO = array (
       'CDSAT' => $_REQUEST['P_CDSAT'],
       'DSSATHOST' => $instanceManager->getParameter('URL_NODE_SERVER')
    );

	switch ($P_FUNCAO) {
		case 'ConsultarSAT':
			$satService->setSatType($P_SATINFO);
			$resultSAT = $satService->consultarSAT();
			$resultSAT = str_replace('+', '*', $resultSAT['satResponse']);
			break;

		case 'ConsultarStatusOperacional':
			$P_CDATIVACAOSAT = $_REQUEST['P_CDATIVACAOSAT'];
			$satService->setSatInfo($P_SATINFO);
			$resultSAT = $satService->consultarStatusOperacional($P_CDATIVACAOSAT);
			$resultSAT = str_replace('+', '*', $resultSAT['satResponse']);
			break;

		case 'EnviarDadosVenda':
			$P_CDATIVACAOSAT = $_REQUEST['P_CDATIVACAOSAT'];
			$P_XML           = $_REQUEST['P_XML'];
			$P_XML           = str_replace('*','+',$P_XML);
			$satService->setSatInfo($P_SATINFO);
			$resultSAT = $satService->enviarDadosVenda($P_CDATIVACAOSAT, $P_XML);
			$resultSAT = str_replace('+', '*', $resultSAT['satResponse']);
			break;

		case 'CancelarUltimaVenda':
			$P_CDATIVACAOSAT = $_REQUEST['P_CDATIVACAOSAT'];
			$P_NRACESSO      = $_REQUEST['P_NRACESSO'];
			$P_XML           = $_REQUEST['P_XML'];
			$P_XML           = str_replace('*','+',$P_XML);
			$satService->setSatInfo($P_SATINFO);
			$resultSAT = $satService->cancelarVendaSAT($P_CDATIVACAOSAT, $P_NRACESSO, $P_XML);
			$resultSAT = str_replace('+', '*', $resultSAT['satResponse']);
			break;

		default:
			$resultSAT = "SatService v2 - OK";
			break;
	}

	header('Content-Type: text/plain; charset=utf-8');
	echo $resultSAT;