<?php

namespace Util;

class WaiterMessage {

    public function getMessage($id) {
    	if(!empty($this->messages[$id]))
        return $this->messages[$id];
    	else
    	var_dump($id);
    	return $this->messages['051'];
    }

    private $messages = array(
        '001' => 'Função inválida.',
        '002' => 'Chave não encontrada.',
        '003' => 'Não há comandas abertas.',
        '004' => 'Mesa não encontrada.',
        '005' => 'Mesa não cadastrada para a filial/loja.',
        '006' => 'Não foi realizado nenhum pedido para esta mesa.',
        '007' => 'Operação realizada com sucesso.',
        '008' => 'Ocorreu um problema na gravação do pedido, favor tentar novamente.',
        '009' => 'Garçom não está associado a filial.',
        '010' => 'Garçom não está associado ao caixa.',
        '011' => 'Garçom não associado a um operador.',
        '012' => 'Operador não cadastrado.',
        '013' => 'O operador informado não é um supervisor.',
        '014' => 'Supervisor não habilitado para a filial.',
        '015' => 'A senha não pode ser nula.',
        '016' => 'A conta já foi solicitada.',
        '017' => 'Quantidade inválida.',
        '018' => 'Quantidade inválida.',
        '019' => 'Cancelamento realizado com sucesso.',
        '020' => 'Não há tabela de preços cadastrada.',
        '023' => 'Produto não cadastrado.',
        '024' => 'Produto não pode ser vendido. Não possui imposto cadastrado.',
        '025' => 'Produto não pode ser vendido. Existe mais de um imposto associado a ele.',
        '026' => 'Pedido realizado com sucesso.',
        '028' => 'Não há mesa padrão cadastrada para esta loja.',
        '029' => 'Caixa sem configuração associada, ou a configuração associada está vazia. Verifique no VND a tela Configuração de Terminal de Caixa.',
        '030' => 'Operação bloqueada. Não é permitido cancelar uma comanda agrupada. Comanda deve ser separada do agrupamento.',
        '031' => 'Comanda não habilitada.',
        '032' => 'Comanda inativa.',
        '033' => 'Comanda agrupada com comanda principal.',
        '034' => 'Número limite de abertura de comandas já atingido.',
        '035' => 'A conta foi impressa com sucesso.',
        '036' => 'A quantidade deve ser maior que zero.',
        '037' => 'A conta não foi impressa. A porta de impressão da impressora não foi parametrizada no VND para o caixa.',
        '038' => 'Valor enviado pelo pocket é invalido.',
        '039' => 'Produto não cadastrado.',
        '040' => 'A comanda foi cancelada com sucesso.',
        '042' => 'Abertura de comanda não permitida para este caixa.',
        '043' => 'Pedido não permitido. Mesa pertence a outro garçom.',
        '044' => 'Operação não permitida. Esta comanda está aberta para outro vendedor.',
        '045' => 'Conexão não pôde ser realizada. Tente novamente.',
        '051' => 'Erro de execução na função.',
        '052' => 'Não há solicitação de acesso para esta mesa.',
        '053' => 'Seu acesso ainda não foi autorizado, aguarde a autorização do garçom.',
        '054' => 'A solicitação de acesso já foi atendida.',
        '055' => 'Seu acesso já foi solicitado, favor aguardar a autorização do garçom.',
        '056' => 'Seu acesso já foi autorizado, para utilizar o sistema clique no botão Entrar.',
        '057' => 'Seu acesso foi desconsiderado. Favor entrar em contato com algum garçom.' ,
        '058' => 'Não há impressoras cadastradas para esta loja.',
        '059' => 'Mesa já se encontra aberta.',
        '060' => 'Mesa já se encontra disponível.',
        '066' => 'Operação não permitida. Já foram lançados produtos para esta mesa.',
        '067' => 'Código de grupo de observação não encontrado. Verifique o cadastro de observações.',
        '068' => 'Não foi possível realizar a venda dos produtos cancelados.',
        '200' => 'Filial não encontrada.',
        '201' => 'Caixa não encontrado.',
        '202' => 'Garçom não encontrado.',
        '203' => 'Garçom não está associado a filial.',
        '204' => 'Garçom não está associado ao caixa.',
        '205' => 'Garçom não associado a um operador.',
        '206' => 'Operador não cadastrado.',
        '207' => 'A senha não pode ser nula.',
        '208' => 'Senha inválida.',
        '209' => 'O caixa não está habilitado para executar o Waiter. Favor verificar no cadastro de caixa do Gestão de Vendas.',
        '210' => 'O caixa não foi encontrado no arquivo de personalização do sistema(PER). Entre no site da TEKNISA para regularizar a situação.',
        '211' => 'Erro criptografia filial.',
        '212' => 'Erro criptografia caixa.',
        '213' => 'Supervisor não possui permissão para realizar esta operação.',
        '254' => 'Não foi possível reduzir o número de posições pois existem posições com pedidos associados à elas.',
        '255' => 'Não é possível incluir um pedido sem produtos.',
        '257' => 'Tabela de preços associada ao cliente está inativa.',
        '258' => 'Tabela de preços associada à loja está inativa.',
        '259' => 'Tabela de preços não cadastrada.',
        '260' => 'Tabela de preços está inativa.',
        '261' => 'Preço não cadastrado.',
        '262' => 'Preço zerado.',
        '263' => 'Posição não existente para a mesa informada.',
        '264' => 'Pedido realizado com sucesso.',
        '265' => 'Não foi possível conectar no banco de dados, verifique o arquivo "app.json".',
        '400' => 'Mesa aberta com sucesso.',
        '401' => 'Abertura da mesa cancelada com sucesso.',
        '402' => 'Mensagem enviada com sucesso.',
        '403' => 'Mesa fechada com sucesso.',
        '404' => 'Mesa reaberta com sucesso.',
        '405' => 'Mesas agrupadas com sucesso.',
        '406' => 'Transferência concluída com sucesso.',
        '407' => 'Produto cancelado com sucesso.',
        '408' => 'Mesas separadas com sucesso.',
        '409' => 'Transferência de mesa realizada com sucesso.',
        '410' => 'Pedido realizado com sucesso.',
        '411' => 'Quantidade alterada com sucesso.',
        '412' => 'Validado.',
        '413' => 'Informe a filial.',
        '414' => 'Informe o caixa.',
        '415' => 'Informe o operador.',
        '416' => 'Informe a senha.',
        '417' => 'Parcial impressa com sucesso.',
        '418' => 'A conta já foi solicitada.',
        '419' => 'Mesa já está aberta.',
        '420' => 'Mesa está disponível.',
        '421' => 'Não foi possível abrir a mesa.',
        '422' => 'Informe o supervisor.',
        '423' => 'Informe a senha.',
        '424' => 'Mesa está em recebimento.',
        '425' => 'Não foi realizado nenhum pedido para esta posição.',
        '426' => 'Não há senha cadastrada para este garçom.',
        '427' => 'A comanda informada já está aberta.',
        '428' => 'Erro ao validar comanda.',
        '429' => 'Comanda já foi fechada.',
        '430' => 'Não foi realizado nenhum pedido para esta comanda.',
        '431' => 'Comanda fechada com sucesso.',
        '432' => 'Comanda fechada.',
        '433' => 'Não há mesa padrão cadastrada para esta loja. Informe uma mesa.',
        '434' => 'Informe o nome.',
        '435' => 'Informe a mesa.',
        '436' => 'Não é possível realizar esta operação. Este acesso já foi autorizado. ',
        '437' => '',
        '438' => 'A mesa não está disponível para realizar pedidos.',
        '439' => 'Sua conta foi solicitada, seu acesso será inativado.',
        '440' => 'Produto sem alíquota.',
        '441' => 'O caixa não está habilitado para o modo FastPass.',
        '442' => 'O caixa não está habilitado para o modo Waiter.',
        '443' => 'Produto sem preço.',
        '444' => 'Produto bloqueado.',
        '445' => 'Mesa de origem não disponível para transferências.',
        '446' => 'Mesa de destino não disponível para transferências.',
        '447' => 'Consumidor não cadastrado.',
        '448' => 'Mesa alterada com sucesso.',
        '449' => 'Cadastro realizado com sucesso.',
        '450' => 'Centro de Custo Padrão não está cadastrado.',
        '451' => 'Este e-mail já se encontra cadastrado no sistema.',
        '452' => 'Login e/ou senha inválidos.',
        '453' => 'Serviço de pagamento não encontrado.',
        '454' => 'Não foi possivel efetuar o cancelamento. Já houve o pagamento parcial do item.',
        '455' => 'Não é possível realizar o pagamento. Não há tipo de recebimento cadastrado.',
        '456' => 'Erro ao alterar Cliente/Consumidor por posição.',
        '457' => 'Cliente/Consumidor alterado com sucesso.',
        '458' => 'Comanda não encontrada.',
        '459' => 'Divisão concluída com sucesso.',
        '460' => 'Divisão cancelada com sucesso.',
        '461' => 'O pedido não pode ser realizado pois existe adiantamento pendente.',
        '462' => 'A operação não pode ser realizada pois existe adiantamento pendente.',
        '463' => 'Logout efetuado com sucesso.'
    );
}
