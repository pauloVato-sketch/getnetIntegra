<?php
require __DIR__ . '\..\Controller\PaymentService.php';

$params = $_POST;

$banco          = $params['banco'];
$dadosDoBoleto  = $params['dadosBoleto'];
$dadosCliente   = $params['dadosCliente'];
$dadosLoja      = $params['dadosLoja'];
$outputNameFile = $params['nomeArquivoSaida'];

ob_start();

$PaymentService = new \Payment\PaymentService();
$dadosboleto = $PaymentService->generateBillet($banco, $dadosDoBoleto, $dadosCliente, $dadosLoja);

include("../Billet/funcoes/" . $dadosboleto['bankName'] . ".php");
include("../Billet/layout/" . $dadosboleto['bankName'] . ".php");

$html = ob_get_clean();
ob_end_clean();

$htmlFile = fopen(__DIR__ . "\\..\\..\\..\\..\\..\\..\\mobile\\billets\\" . $outputNameFile . '.html', "w");
fwrite($htmlFile, $html);
fclose($htmlFile);