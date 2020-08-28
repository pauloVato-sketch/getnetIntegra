<?php
namespace Odhen\API\Controller;

use Zeedhi\Framework\DTO\Response\Message;
use Zeedhi\Framework\DTO\Response;
use Zeedhi\Framework\DTO\Request;

class Rest extends \Zeedhi\Framework\Controller\Simple {

	protected $clientService;
    protected $util;

    public function __construct() {}

	public function testPay(Request\Filter $request, Response $response) {
        $params = $request->getFilterCriteria()->getConditions();

        $dataset = array(
            'valorPedido'           => $params[0]['value'],
            'idLojaSitef'           => $params[1]['value'],
            'codigoLojaSitef'       => "3BBF7CEA464EF962F7D49E64774AD37E2204A8E4F5C57086", //Pegar de algum lugar
            'codigoPedido'          => $params[2]['value'],
            'codigoBandeira'        => $params[3]['value'],
            'validadeCartao'        => $params[4]['value'],
            'numeroCartao'          => $params[5]['value'],
            'codigoSegurancaCartao' => $params[6]['value']
        );

        $PaymentService = new \Payment\PaymentService();
        $paymentResponse = $PaymentService->beginCreditCardPayment($dataset);

        if (!is_null($paymentResponse['error'])) {
            $response->addMessage(new Message($paymentResponse['message']));

            //Pega o retorno e usa para salvar a transação
        } else {
            $response->addMessage(new Message($paymentResponse['message']));
        }
    }
}