<?php
namespace Odhen\API\Service;

use Zeedhi\Framework\DTO\Response\Message;
use Zeedhi\Framework\DTO\Response;
use Zeedhi\Framework\DTO\Request;

require __DIR__ .'/../Lib/nusoap.php';

class SoftwareExpress {

    protected $nusoapClient;
    /********************************************************************/
    /*************** CREDIT CARD SOFTWARE EXPRESS PARAMS ****************/
    /********************************************************************/

    // TO USE IN PRODUCTION
    protected $endpoint = "https://esitef.softwareexpress.com.br/e-sitef/Payment2?wsdl";
    protected $proxy_host = null;
    protected $proxy_port = null;
    protected $proxy_user = null;
    protected $proxy_pass = null;

    // TO USE INSIDE TEKNISA
    /*
    protected $endpoint = "https://esitef-homologacao.softwareexpress.com.br/e-sitef-hml/Payment2?wsdl";
    protected $proxy_host = "192.168.122.3";
    protected $proxy_port = "8080";
    protected $proxy_user = "teknisa";
    protected $proxy_pass = "teknisa";
    */

    protected $wsdl = true;
    protected $timeout = 0;
    protected $response_timeout = 300;

    /******************************* END *******************************/

    protected $nit; //Transaction number, set in runtime

    public function __construct(){
    }

    public function generateBillet($banco, $dadosBoleto, $dadosCliente, $dadosLoja, $obs, $outputNameFile) {
        try {
            ob_start();

            $dadosboleto = $this->handleGenerateBillet($banco, $dadosBoleto, $dadosCliente, $dadosLoja, $obs);

            include(__DIR__ . "../Billet/funcoes/" . $dadosboleto['bankName'] . ".php");
            include(__DIR__ . "../Billet/layout/" . $dadosboleto['bankName'] . ".php");

            $html = ob_get_clean();
            ob_end_clean();

            // $path = realpath(__DIR__ . '/../../../../../../mobile');
            // $path = $path . '/billets';
            // if (!file_exists($path)) {
            //     mkdir($path, 0777, true);
            // }
            // $htmlFile = fopen($path . '/' . $outputNameFile . '.html', "w");
            // fwrite($htmlFile, $html);
            // fclose($htmlFile);

            return array(
                'error'          => null,
                'billetContent'  => $html
            );
        } catch (\Exception $e) {
            return array(
                'error' => $e->getMessage()
            );
        }
    }

    private function handleGenerateBillet ($banco, $dadosBoleto, $dadosCliente, $dadosLoja, $obs) {
        /*
        237 - Bradesco
        001 - Banco do Brasil
        341 - Itaú
        033 - Santander
        399 - HSBC
        */
        if ($banco == '237') {
            $dadosboleto["carteira"] = '06';  // Código da Carteira: pode ser 06 ou 03
            $dadosboleto["bankName"] = 'bradesco';
        } else if ($banco == '001') {
            $dadosboleto["carteira"] = '17';
            $dadosboleto["bankName"] = 'bb';
        } else if ($banco == '341') {
            $dadosboleto["carteira"] = '109';
            $dadosboleto["bankName"] = 'itau';
        } else if ($banco == '033') {
            $dadosboleto["carteira"] = '102';
            $dadosboleto["bankName"] = 'santander';
        } else if ($banco == '399') {
            $dadosboleto["carteira"] = 'CNR';
            $dadosboleto["bankName"] = 'hsbc';
        }

        $prazoPagamento = $dadosBoleto['prazoPagamento']; //prazo para pagamento a partir do dia da geração
        $valorBoleto    = $dadosBoleto['valor']; //Valor total do boleto
        $nossoNumero    = $dadosBoleto['nossoNumero']; //Número de registro da empresa no banco
        $nrDocumento    = $dadosBoleto['numeroDocumento']; //Número que identifica o boleto

        $nomeCliente    = $dadosCliente['nomeCliente']; //Nome do cliente
        $endereco1      = $dadosCliente['endereco1']; //Endereço do cliente
        $endereco2      = $dadosCliente['endereco2']; //Complemento do endereco do cliente (Cidade/estado/etc)

        $nomeFantasia   = $dadosLoja['nomeFantasia']; //Nome fantasia da loja
        $site           = $dadosLoja['siteCliente']; //Site para contato da loja
        $cpfCnpj        = $dadosLoja['cpfCnpj']; //Cpf ou CNPJ da loja
        $enderecoLoja   = $dadosLoja['endereco']; //Endereço da loja
        $cidade_uf      = $dadosLoja['cidadeEstado']; //Formato: Cidade/Estado
        $razaoSocial    = $dadosLoja['razaoSocial']; // Razão social da loja

        $agenciaLoja    = $dadosLoja['conta']['agencia']; //Agencia da loja
        $agenciaDvLoja  = $dadosLoja['conta']['digitoAgencia']; //Dígito verificador da agencia
        $contaLoja      = $dadosLoja['conta']['conta']; //Conta da loja
        $contaDvLoja    = $dadosLoja['conta']['digitoConta']; //Dígito verificador da conta

        $data_venc = date("d/m/Y", time() + ($prazoPagamento * 86400));  // Prazo de X dias OU informe data: "13/04/2006";
        $valor_boleto = number_format(str_replace(",", ".", $valorBoleto), 2, ',', '');

        $dadosboleto["nosso_numero"] = $nossoNumero;  // Nosso numero sem o DV - REGRA: Máximo de 11 caracteres!
        $dadosboleto["numero_documento"] = $nrDocumento;    // Num do pedido ou do documento
        $dadosboleto["data_vencimento"] = $data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
        $dadosboleto["data_documento"] = date("d/m/Y"); // Data de emissão do Boleto
        $dadosboleto["data_processamento"] = date("d/m/Y"); // Data de processamento do boleto (opcional)
        $dadosboleto["valor_boleto"] = $valor_boleto;   // Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula

        // DADOS DO SEU CLIENTE
        $dadosboleto["sacado"] = $nomeCliente;
        $dadosboleto["endereco1"] = $endereco1;
        $dadosboleto["endereco2"] = $endereco2;

        // INFORMACOES PARA O CLIENTE
        $dadosboleto["demonstrativo1"] = $obs;
        $dadosboleto["demonstrativo2"] = "";
        $dadosboleto["demonstrativo3"] = "";
        $dadosboleto["instrucoes1"] = "- Favor nao receber apos o vencimento";
        $dadosboleto["instrucoes2"] = $site;
        $dadosboleto["instrucoes3"] = "";
        $dadosboleto["instrucoes4"] = "";

        // DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
        $dadosboleto["quantidade"] = "001";
        $dadosboleto["valor_unitario"] = $valor_boleto;
        $dadosboleto["aceite"] = "";
        $dadosboleto["especie"] = "R$";
        $dadosboleto["especie_doc"] = "DS";

        // Bradesco: todos os campos
        // BB: Só utiliza agencia e conta, sem os dígitos
        // Itaú: não usa dígito da agência
        $dadosboleto["agencia"] = $agenciaLoja; // Num da agencia, sem digito
        $dadosboleto["agencia_dv"] = $agenciaDvLoja; // Digito do Num da agencia
        $dadosboleto["conta"] = $contaLoja;  // Num da conta, sem digito
        $dadosboleto["conta_dv"] = $contaDvLoja;     // Digito do Num da conta

        // DADOS PERSONALIZADOS - Bradesco
        $dadosboleto["conta_cedente"] = ""; // ContaCedente do Cliente, sem digito (Somente Números)
        $dadosboleto["conta_cedente_dv"] = ""; // Digito da ContaCedente do Cliente

        // DADOS PERSONALIZADOS - BB
        /*
        #################################################
        DESENVOLVIDO PARA CARTEIRA 18
        ALERTA: A CARTEIRA FOI TROCADA DE 18 PARA 17 (15/10/2015).

        - Carteira 18 com Convenio de 8 digitos
          Nosso número: pode ser até 9 dígitos

        - Carteira 18 com Convenio de 7 digitos
          Nosso número: pode ser até 10 dígitos

        - Carteira 18 com Convenio de 6 digitos
          Nosso número:
          de 1 a 99999 para opção de até 5 dígitos
          de 1 a 99999999999999999 para opção de até 17 dígitos

        #################################################
        */
        $dadosboleto["convenio"] = $dadosLoja['convenio'];  // Num do convênio - REGRA: 6 ou 7 ou 8 dígitos
        $dadosboleto["contrato"] = $dadosLoja['contrato']; // Num do seu contrato
        $dadosboleto["formatacao_convenio"] = strlen($dadosLoja['convenio']); // REGRA: 8 p/ Convênio c/ 8 dígitos, 7 p/ Convênio c/ 7 dígitos, ou 6 se Convênio c/ 6 dígitos

        // FORMATACAO NOSSO NUMERO
        // REGRA: Usado apenas p/ Convênio c/ 6 dígitos: informe 1 se for NossoNúmero de até 5 dígitos ou 2 para opção de até 17 dígitos
        if (strlen($dadosLoja['convenio']) == 6) {
            if (strlen($nossoNumero) <= 5) {
                $dadosboleto["formatacao_nosso_numero"] = '1';
            } else {
                $dadosboleto["formatacao_nosso_numero"] = '2';
            }
        }
        // FIM DADOS PERSONALIZADOS BB

        // SEUS DADOS
        $dadosboleto["identificacao"] = $nomeFantasia;
        $dadosboleto["cpf_cnpj"] = $cpfCnpj;
        $dadosboleto["endereco"] = $enderecoLoja;
        $dadosboleto["cidade_uf"] = $cidade_uf;
        $dadosboleto["cedente"] = $razaoSocial;


        /*
        033 - Santander
        399 - HSBC
        */
        if ($banco == '033') {
            // santander
            $dadosboleto["codigo_cliente"] = $dadosBoleto["codigoCedente"];
            $dadosboleto["ponto_venda"] = $agenciaLoja;
            $dadosboleto["carteira_descricao"] = "COBRANÇA SIMPLES - CSR";
        } else if ($banco == '399') {
            $dadosboleto["codigo_cedente"] = $dadosBoleto["codigoCedente"];
        } else if ($banco == '237') {
            $dadosboleto["conta_cedente"] = substr($dadosBoleto["codigoCedente"],0,7); // ContaCedente do Cliente, sem digito (Somente Números)
            $dadosboleto["conta_cedente_dv"] = substr($dadosBoleto["codigoCedente"],7);
        }

        return $dadosboleto;
    }

    public function configEndpoint($endpoint, $wsdl = true, $timeout = 0, $response_timeout = 300) {
        $this->endpoint = $endpoint;
        $this->wsdl = $wsdl;
        $this->timeout = $timeout;
        $this->response_timeout = $response_timeout;
    }

    public function configProxy($proxy_host, $proxy_port, $proxy_user, $proxy_pass) {
        $this->proxy_host = $proxy_host;
        $this->proxy_port = $proxy_port;
        $this->proxy_user = $proxy_user;
        $this->proxy_pass = $proxy_pass;
    }

    public function beginCreditCardPayment($dataset) {
        try {
            $amount         = $dataset['valorPedido']; //Valor do pedido
            $merchantId     = $dataset['idLojaSitef']; //lojaSitef
            $merchantKey    = $dataset['codigoLojaSitef']; //Código da loja
            $orderId        = $dataset['codigoPedido']; //Chave única construída a partir da chave primária do pedido
            $idAutorizadora = $dataset['codigoBandeira']; //Bandeira do cartão
            $dtVencimento   = $dataset['validadeCartao']; //Data de vencimento do cartão
            $numCartao      = $dataset['numeroCartao']; //Número do cartão
            $codSeguranca   = $dataset['codigoSegurancaCartao']; //Código de seguraça do cartão         

            $client = new \nusoap_client($this->endpoint, $this->wsdl, $this->proxy_host, $this->proxy_port, $this->proxy_user, $this->proxy_pass, $this->timeout, $this->response_timeout);
            if ($client->fault) {
                //Verifica se o cliente foi encontrado
                return array('message' => $client, 'error' => '01'); // WebService indisponível
            } else {
                $err = $client->getError();
                if ($err) {
                    //Verifica se há erros na conexão com o cliente
                    return array('message' => $err, 'error' => '02'); // Erro na conexão com o WebService
                } else {
                    //Se tudo deu certo, inicia o processo da transação
                    $WebService = $client->getProxy();

                    $transactionOK = $this->checkTransaction($WebService, $amount, $merchantId, $orderId);

                    if ($transactionOK['status']) {
                        $responseService = $this->doPayment($WebService, $idAutorizadora, $dtVencimento, $numCartao, $codSeguranca);
                        $paymentResponse = $this->getPaymentStatus($responseService);
                        $responseStatus = $this->getStatusMsg($paymentResponse["transactionStatus"], $paymentResponse['responseCode']);

                        if ($paymentResponse['transactionStatus'] == "CON") {
                            //Pagamento realizado com sucesso
                            $dataSitef = isset($paymentResponse['date']) ?
                            substr($paymentResponse['date'],0,2).'/'.substr($paymentResponse['date'],2,2).'/'.substr($paymentResponse['date'],4) : date("d/m/Y H:i:s");

                            return array(
                                "nsu"             => $paymentResponse["esitefUSN"],
                                'numeroTransacao' => $this->nit,
                                "status"          => $paymentResponse["transactionStatus"],
                                "dataSitef"       => $dataSitef,
                                "cupom"           => $paymentResponse["customerReceipt"],
                                "tipoPagamento"   => $paymentResponse["acquirer"],
                                "message"         => $responseStatus,
                                "cardUsed"        => $this->buildCardUsedMessage($idAutorizadora, $numCartao),
                                "error"           => null
                            );
                        } else {
                            //Pagamento não realizado
                            return array('message' => $responseStatus, 'error' => '04'); // Erro no pagamento
                        }
                    } else {
                        return array('message' => $transactionOK['message'], 'error' => '05'); // Erro no início da transação
                    }
                }
            }
        } catch (\Exception $e) {
            return array('message' => $e->getMessage(), 'error' => '03'); // Erro de execução no WebService
        }
    }

    private function checkTransaction ($WebService, $amount, $merchantId, $orderId) {
        $transactionRequest = array(
            'transactionRequest' => array(
                'amount' => $amount,
                'merchantId' => $merchantId,
                'orderId' => $orderId
            )
        );

        if ($WebService == null){
            throw new \Exception('Ocorreu um erro com a transação. Não foi possível instanciar o webservice. Favor entrar em contato com o administrador do sistema.');
        }
        $responseFromWebService = $WebService->beginTransaction($transactionRequest);
        $responseFromWebService = $responseFromWebService['transactionResponse'];

        if ($responseFromWebService['responseCode'] != 0) {
            //Retornou erro ao iniciar transação com o WebService
            $message = $this->getBeginTransactionErrorMessage($responseFromWebService['responseCode']);
            return array('status' => false, 'message' => $message);
        } else {
            //Obteve resposta positiva do WebService e vai seguir com a transação
            $this->nit = $responseFromWebService['nit'];
            return array('status' => true);
        }
    }

    private function getBeginTransactionErrorMessage ($errorCode) {
        $error = array(
            0    => 'Sucesso.',
            1    => 'Requisição nula ou vazia.',
            3    => 'Campo amount inválido ou ultrapassa o tamanho limite.',
            4    => 'Campo merchantUSN inválido.',
            5    => 'Campo merchantId nulo ou vazio.',
            131  => 'Campo amount nulo ou negativo.',
            132  => 'Campo merchantUSN ultrapassa o limite de tamanho.',
            133  => 'Campo orderId ultrapassa o limite de tamanho.',
            151  => 'Campo merchantId inválido.',
            153  => 'Loja está inativa no e-SiTef.',
            1000 => 'Erro inesperado no e-SiTef. Favor entrar em contato com o suporte.'
        );

        return $error[intval($errorCode)];
    }

    private function buildCardUsedMessage ($codigoBandeira, $numeroCartao) {
        $ultimosDigCartao = substr($numeroCartao, -4);
        $nomeBandeira = self::getNameBandeira($codigoBandeira);

        return $nomeBandeira . ' terminado em ' . $ultimosDigCartao;
    }

    private function getNameBandeira ($codigoBandeira) {
        $bandeiras = array(
            0     => 'Outro Tipo',
            1     => 'Visa',
            2     => 'Mastercard',
            3     => 'American Express',
            4     => 'Multicheck',
            5     => 'Hipercard',
            6     => 'Aura',
            33    => 'Diners',
            41    => 'Elo',
            201   => 'Mastercard Private Label',
            223   => 'Alelo Cultura',
            224   => 'Alelo Refeição',
            225   => 'Alelo Alimentação',
            279   => 'Sodexo Vale Cultura',
            280   => 'Sodexo Vale Alimentação',
            281   => 'Sodexo Vale Refeição'
        );

        return $bandeiras[intval($codigoBandeira)];
    }

    private function doPayment ($WebService, $idAutorizadora, $dtVencimento, $numCartao, $codSeguranca) {
        $paymentRequest = array(
            'paymentRequest' => array(
                'authorizerId' => $idAutorizadora,
                'autoConfirmation' => 'true',
                'cardExpiryDate' => $dtVencimento,
                'cardNumber' => $numCartao,
                'cardSecurityCode' => $codSeguranca,
                'installmentType' => '4',
                'installments' => '1',
                'nit' => $this->nit
            )
        );

        return $WebService->doPayment($paymentRequest);
    }

    private function getPaymentStatus ($paymentResponse) {
        while(!isset($paymentResponse['paymentResponse'])) {

            $getStatusRequest = array(
                'nit' => $this->nit,
                'merchantKey' => $merchantKey
            );

            $paymentResponse = $payment->getStatus($getStatusRequest);

            //Aguarda 90 segundos para consultar status da transacao novamente
            sleep(90);
        }

        return $paymentResponse['paymentResponse'];
    }

    private function getMsgByResponseCode ($responseCode) {
        switch ($responseCode) {
            case 1:
                $msg = "Requisição nula ou vazia.";
                break;
            case 9:
                $msg = "Data de validade do cartão expirada.";
                break;
            case 30:
                $msg = "Transação já realizada.";
                break;
            case 255:
                $msg = "Pagamento negado.";
                break;
            default:
                $msg = "";
        }

        return $msg;
    }

    private function getStatusMsg($statusTransacao, $responseCode) {
        $descricaoStatus = '';

        if ($statusTransacao !== "CON") {
            $descricaoStatus = "Transação não realizada!<br>";
        }

        if ($statusTransacao != "NOV" && $statusTransacao != "AGU" && $statusTransacao != "PEN") {
            switch ($statusTransacao) {
                case "INV":
                    //$descricaoStatus .= "Transação não foi criada com sucesso, algum parâmetro foi enviado incorretamente para iniciar a transação.";
                    break;
                case "EXP":
                    $descricaoStatus .= "Transação expirou.";
                    break;
                case "CAN":
                    $descricaoStatus .= "Transação foi cancelada pelo usuário.";
                    break;
                case "ABA":
                    $descricaoStatus .= "Transação foi abandonada pelo usuário.";
                    break;
                case "NEG":
                    $descricaoStatus .= "Pagamento negado pela Instituição Financeira.";
                    break;
                case "EST":
                    $descricaoStatus .= "Pagamento cancelado pelo BackOffice";
                    break;
                case "TNE":
                    $descricaoStatus .= "Transação não encontrada no sistema. Código NIT incorreto.";
                    break;
                case "BLQ":
                    $descricaoStatus .= "Transação bloqueada por excesso de tentativas.";
                    break;
                case "ERR":
                    $descricaoStatus .= "Erro de comunicação com a autorizadora. Tente novamente.";
                    break;
                case "CON":
                    $descricaoStatus = "Transação concluída com sucesso!";
                    break;
            }
        }

        $descricaoStatus .= $this->getMsgByResponseCode($responseCode);
        return $descricaoStatus;
    }
}