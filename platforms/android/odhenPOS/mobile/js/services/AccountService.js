function AccountService(Query, AccountOrder, AccountCancelProduct, AccountGetAccountDetails, AccountGetAccountItems,
                        AccountGetAccountItemsWithoutCombo, AccountGetAccountItemsForTransfer, FidelityDetailsRepository,
                        AccountPaymentBegin, AccountPaymentFinish, AccountGetTrasanctions, AccountPaymentTypedCredit,
                        AccountGetTableTrasanctions, AccountGetOriginalAccountItems, CheckRefilRepository, GetCampanhaRepo,
                        AccountChangeClientConsumer, SaleCancelRepository, ChangeProductDiscount, CancelCreditRepository,
                        ConsumerSearchRepository, ParamsMenuRepository, VerificaProdutosBloqueados, ParamsCardsRepository,
                        TransferCreditRepository, ConsumerBalanceRepository, FilterProducts, TransferPositionRepository,
                        ValidatePassword, SelectComandaProducts, UpdateComandaProducts, SetDiscountFidelity, ProdutosDesistencia,
                        CalculaDescontoSubgrupo, UpdateServiceTax, OperatorLogout, GetPayments, VoucherRepository){

    this.order = function (chave, mode, cartPool, nrvendarest, pedidos, orderCode, vendedorAut, saleProdPass) {
        var pedido = JSON.stringify(pedidos);

        var query = Query.build()
                        .where('chave').equals(chave)
                        .where('mode').equals(mode)
                        .where('multiplasComandas').equals(!_.isEmpty(cartPool))
                        .where('nrvendarest').equals(nrvendarest)
                        .where('pedidos').equals(pedido)
                        .where('orderCode').equals(orderCode)
                        .where('vendedorAut').equals(vendedorAut)
                        .where('saleProdPass').equals(saleProdPass);
        return AccountOrder.download(query);
    };

    this.getAccountItems = function (chave, modo, nrcomanda, nrvendarest, posicao) {

        var query = Query.build()
                        .where('chave').equals(chave)
                        .where('modo').equals(modo)
                        .where('nrcomanda').equals(nrcomanda)
                        .where('nrvendarest').equals(nrvendarest)
                        .where('posicao').equals(posicao);
        return AccountGetAccountItems.download(query);
    };

    this.getAccountItemsWithoutCombo = function (chave, modo, nrcomanda, nrvendarest, posicao) {

        var query = Query.build()
                        .where('chave').equals(chave)
                        .where('modo').equals(modo)
                        .where('nrcomanda').equals(nrcomanda)
                        .where('nrvendarest').equals(nrvendarest)
                        .where('posicao').equals(posicao);
        return AccountGetAccountItemsWithoutCombo.download(query);
    };

    this.getAccountOriginalItems = function (chave, modo, nrcomanda, nrvendarest, posicao) {

        var query = Query.build()
                        .where('chave').equals(chave)
                        .where('modo').equals(modo)
                        .where('nrcomanda').equals(nrcomanda)
                        .where('nrvendarest').equals(nrvendarest)
                        .where('posicao').equals(posicao);
        return AccountGetOriginalAccountItems.download(query);
    };

    this.getAccountDetails = function (chave, modo, nrcomanda, nrvendarest, funcao, posicao, updateDiscount) {

        if (updateDiscount == null) updateDiscount = false;

        var query = Query.build()
                        .where('chave').equals(chave)
                        .where('modo').equals(modo)
                        .where('nrcomanda').equals(nrcomanda)
                        .where('nrvendarest').equals(nrvendarest)
                        .where('funcao').equals(funcao)
                        .where('posicao').equals(posicao)
                        .where('updateDiscount').equals(updateDiscount);

        return AccountGetAccountDetails.download(query);
    };

    this.cancelProduct = function (chave, modo, nrcomanda, nrvendarest, produto, motivo, cdsupervisor, IDPRODPRODUZ) {
        var productData = JSON.stringify(produto);

        var query = Query.build()
                        .where('chave').equals(chave)
                        .where('modo').equals(modo)
                        .where('nrcomanda').equals(nrcomanda)
                        .where('nrvendarest').equals(nrvendarest)
                        .where('produto').equals(productData)
                        .where('motivo').equals(motivo)
                        .where('supervisor').equals(cdsupervisor)
                        .where('IDPRODPRODUZ').equals(IDPRODPRODUZ);
        return AccountCancelProduct.download(query);
    };

    this.beginPaymentAccount = function(dataset){
        var query = Query.build()
                        .where('chave').equals(dataset.chave)
                        .where('CDVENDEDOR').equals(dataset.CDVENDEDOR)
						.where('NRVENDAREST').equals(dataset.NRVENDAREST)
						.where('NRCOMANDA').equals(dataset.NRCOMANDA)
                        .where('NRMESA').equals(dataset.NRMESA)
                        .where('NRLUGARMESA').equals(dataset.NRLUGARMESA)
                        .where('CDTIPORECE').equals(dataset.CDTIPORECE)
                        .where('IDTIPMOV').equals(dataset.IDTIPMOV)
                        .where('VRMOV').equals(dataset.VRMOV)
                        .where('DSBANDEIRA').equals(dataset.DSBANDEIRA)
                        .where('IDTPTEF').equals(dataset.IDTPTEF);

        return AccountPaymentBegin.download(query);
    };

    this.finishPaymentAccount = function(dataset){
        var query = Query.build()
						.where('NRSEQMOVMOB').equals(dataset.NRSEQMOVMOB)
						.where('NRSEQMOB').equals(dataset.NRSEQMOB)
						.where('DSBANDEIRA').equals(dataset.DSBANDEIRA)
						.where('NRADMCODE').equals(dataset.NRADMCODE)
						.where('IDADMTASK').equals(dataset.IDADMTASK)
						.where('IDSTMOV').equals(dataset.IDSTMOV)
						.where('TXMOVUSUARIO').equals(dataset.TXMOVUSUARIO)
                        .where('TXMOVJSON').equals(dataset.TXMOVJSON)
                        .where('CDNSUTEFMOB').equals(dataset.CDNSUTEFMOB)
                        .where('TXPRIMVIATEF').equals(dataset.TXPRIMVIATEF)
						.where('TXSEGVIATEF').equals(dataset.TXSEGVIATEF)
						.where('NRVENDAREST').equals(dataset.NRVENDAREST)
						.where('NRCOMANDA').equals(dataset.NRCOMANDA)
						.where('chave').equals(dataset.chave);

        return AccountPaymentFinish.download(query);
    };

    this.typedCreditPayment = function(dataset, NRSEQMOVMOB){
        var query = Query.build()
						.where('amount').equals(dataset.amount)
						.where('idAutorizadora').equals(dataset.idAutorizadora)
						.where('dtVencimento').equals(dataset.dtVencimento)
						.where('numCartao').equals(dataset.numCartao)
						.where('codSeguranca').equals(dataset.codSeguranca)
						.where('NRSEQMOVMOB').equals(NRSEQMOVMOB);

        return AccountPaymentTypedCredit.download(query);
    };

    this.getTransactions = function(NRVENDAREST, NRLUGARMESA){
        var query = Query.build()
						.where('NRVENDAREST').equals(NRVENDAREST)
						.where('NRLUGARMESA').equals(NRLUGARMESA);

        return AccountGetTrasanctions.download(query);
    };

    this.getTableTransactions = function(NRMESA, NRLUGARMESA, NRVENDAREST, chave){
        var query = Query.build()
                        .where('NRMESA').equals(NRMESA)
                        .where('NRLUGARMESA').equals(NRLUGARMESA)
                        .where('NRVENDAREST').equals(NRVENDAREST)
                        .where('chave').equals(chave);

        return AccountGetTableTrasanctions.download(query);
    };

    this.checkRefil = function(chave, NRVENDAREST, NRCOMANDA, CDPRODUTO, position){
        var query = Query.build()
                        .where('chave').equals(chave)
                        .where('NRVENDAREST').equals(NRVENDAREST)
                        .where('NRCOMANDA').equals(NRCOMANDA)
                        .where('CDPRODUTO').equals(CDPRODUTO)
                        .where('position').equals(position);
        return CheckRefilRepository.download(query);
    };

    this.setCPF = function(chave, cpf, checkedBox, position){
      var query = Query.build()
                        .where('chave').equals(chave)
                        .where('cpf').equals(cpf)
                        .where('checkedBox').equals(checkedBox)
                        .where('position').equals(position);
        return CheckRefilRepository.download(query);
    };

    this.changeClientConsumer = function(chave, NRVENDAREST, NRCOMANDA, positions, CDCLIENTE, CDCONSUMIDOR, fidelitySearch) {
      var query = Query.build()
                        .where('chave').equals(chave)
                        .where('NRVENDAREST').equals(NRVENDAREST)
                        .where('NRCOMANDA').equals(NRCOMANDA)
                        .where('positions').equals(positions)
                        .where('CDCLIENTE').equals(CDCLIENTE)
                        .where('CDCONSUMIDOR').equals(CDCONSUMIDOR)
                        .where('fidelitySearch').equals(fidelitySearch);
        return AccountChangeClientConsumer.download(query);
    };

    this.saleCancel = function (chave, CODIGOCUPOM, CDSUPERVISOR) {
        var query = Query.build()
                        .where('chave').equals(chave)
                        .where('CODIGOCUPOM').equals(CODIGOCUPOM)
                        .where('CDSUPERVISOR').equals(CDSUPERVISOR);
        return SaleCancelRepository.download(query);
    };

    this.changeProductDiscount = function(NRVENDAREST, NRCOMANDA, VRDESCONTO, TIPODESCONTO, NRPRODCOMVEN, CDSUPERVISOR, motivoDesconto, CDGRPOCORDESC){
        var query = Query.build()
                        .where('NRVENDAREST').equals(NRVENDAREST)
                        .where('NRCOMANDA').equals(NRCOMANDA)
                        .where('VRDESCONTO').equals(VRDESCONTO)
                        .where('TIPODESCONTO').equals(TIPODESCONTO)
                        .where('NRPRODCOMVEN').equals(NRPRODCOMVEN)
                        .where('CDSUPERVISOR').equals(CDSUPERVISOR)
                        .where('motivoDesconto').equals(motivoDesconto)
                        .where('CDGRPOCORDESC').equals(CDGRPOCORDESC);
        return ChangeProductDiscount.download(query);
    };

    this.cancelPersonalCredit = function(chave, CDCLIENTE, CDCONSUMIDOR, NRDEPOSICONS, NRSEQMOVCAIXA, confirmacao){
        var query = Query.build()
                        .where('chave').equals(chave)
                        .where('CDCLIENTE').equals(CDCLIENTE)
                        .where('CDCONSUMIDOR').equals(CDCONSUMIDOR)
                        .where('NRDEPOSICONS').equals(NRDEPOSICONS)
                        .where('NRSEQMOVCAIXA').equals(NRSEQMOVCAIXA)
                        .where('confirmacao').equals(confirmacao);
        return CancelCreditRepository.download(query);
    };

    this.searchConsumer = function(chave, CDCLIENTE, qrcode){
        var query = Query.build()
                        .where('chave').equals(chave)
                        .where('CDCLIENTE').equals(CDCLIENTE)
                        .where('qrcode').equals(qrcode.replace(/[^A-Z0-9-\/. ]/gi, ""));
        return ConsumerSearchRepository.download(query);
    };

    this.updatePrices = function(chave){
        var query = Query.build()
                        .where('chave').equals(chave);
        return ParamsMenuRepository.download(query);
    };

    this.verificaProdutosBloqueados = function(products){
        var query = Query.build()
                        .where('products').equals(products);
        return VerificaProdutosBloqueados.download(query);
    };

    this.cardSearch = function(searchValue){
        var query = Query.build()
                        .where('searchValue').equals(searchValue);
        return ParamsCardsRepository.download(query);
    };

    this.transferPersonalCredit = function(chave, data){
        var query = Query.build()
                        .where('chave').equals(chave)
                        .where('CDCLIENTE').equals(data.CDCLIENTE)
                        .where('CDCONSUMIDOR').equals(data.CDCONSUMIDOR)
                        .where('CDFAMILISALD').equals(data.CDFAMILISALD)
                        .where('CDIDCONSUMID').equals(data.CDIDCONSUMID)
                        .where('VRSALDCONEXT').equals(data.VRSALDCONEXT)
                        .where('destCDCLIENTE').equals(data.destCDCLIENTE)
                        .where('destCDCONSUMIDOR').equals(data.destCDCONSUMIDOR)
                        .where('destCDFAMILISALD').equals(data.destCDFAMILISALD)
                        .where('destCDIDCONSUMID').equals(data.destCDIDCONSUMID)
                        .where('destVRSALDCONEXT').equals(data.destVRSALDCONEXT);
        return TransferCreditRepository.download(query);
    };

    this.getConsumerBalance = function(chave, CDCLIENTE, CDCONSUMIDOR){
        var query = Query.build()
                        .where('chave').equals(chave)
                        .where('CDCLIENTE').equals(CDCLIENTE)
                        .where('CDCONSUMIDOR').equals(CDCONSUMIDOR);
        return ConsumerBalanceRepository.download(query);
    };

    this.filterProducts = function(pesquisa){
        var query = Query.build()
            .where('pesquisa').equals(pesquisa);
        return FilterProducts.download(query);
    };

    this.transferPositions = function(products, position, NRVENDAREST, NRCOMANDA, CDCLIENTE, CDCONSUMIDOR){
        var query = Query.build()
            .where('products').equals(products)
            .where('position').equals(position)
            .where('NRVENDAREST').equals(NRVENDAREST)
            .where('NRCOMANDA').equals(NRCOMANDA)
            .where('CDCLIENTE').equals(CDCLIENTE)
            .where('CDCONSUMIDOR').equals(CDCONSUMIDOR);
        return TransferPositionRepository.download(query);
    };

    this.getFidelityDetails = function(CDCLIENTE, CDCONSUMIDOR){
        var query = Query.build()
            .where('CDCLIENTE').equals(CDCLIENTE)
            .where('CDCONSUMIDOR').equals(CDCONSUMIDOR);
        return FidelityDetailsRepository.download(query);
    };

    this.validatePassword = function(password){
        var query = Query.build()
            .where('password').equals(password);
        return ValidatePassword.download(query);
    };

    this.selectComandaProducts = function(NRCOMANDA){
        var query = Query.build()
            .where('NRCOMANDA').equals(NRCOMANDA);
        return SelectComandaProducts.download(query);
    };

    this.updateComandaProducts = function(comandaAtual,vendaRestComandaAtual, comandaDestino, vendaRestComandaDestino, CDPRODUTO, NRPRODCOMVEN, CDSUPERVISOR){
        var query = Query.build()
            .where('comandaAtual').equals(comandaAtual)
            .where('vendaRestComandaAtual').equals(vendaRestComandaAtual)
            .where('comandaDestino').equals(comandaDestino)
            .where('vendaRestComandaDestino').equals(vendaRestComandaDestino)
            .where('CDPRODUTO').equals(CDPRODUTO)
            .where('NRPRODCOMVEN').equals(NRPRODCOMVEN)
            .where('CDSUPERVISOR').equals(CDSUPERVISOR);
        return UpdateComandaProducts.download(query);
    };

    this.setDiscountFidelity = function(NRVENDAREST, NRCOMANDA, positions, VRDESCFID){
        var query = Query.build()
            .where('NRVENDAREST').equals(NRVENDAREST)
            .where('NRCOMANDA').equals(NRCOMANDA)
            .where('positions').equals(positions)
            .where('VRDESCFID').equals(VRDESCFID);
        return SetDiscountFidelity.download(query);
    };

    this.getCampanha = function(tray){
        tray = JSON.stringify(tray);

        var query = Query.build()
            .where('tray').equals(tray);
        return GetCampanhaRepo.download(query);
    };

    this.calculaDescontoSubgrupo = function(products){
        var query = Query.build()
                        .where('products').equals(products);
        return CalculaDescontoSubgrupo.download(query);
    };

    this.produtosDesistencia = function (itensDesistencia) {
        var query = Query.build()
            .where('itensDesistencia').equals(itensDesistencia);
        return ProdutosDesistencia.download(query);
    };

    this.updateServiceTax = function(NRVENDAREST, NRCOMANDA, TOTALPRODS, VRACRESCIMO, TIPOGORJETA){
        var query = Query.build()
            .where('NRVENDAREST').equals(NRVENDAREST)
            .where('NRCOMANDA').equals(NRCOMANDA)
            .where('TOTALPRODS').equals(TOTALPRODS)
            .where('VRACRESCIMO').equals(VRACRESCIMO)
            .where('TIPOGORJETA').equals(TIPOGORJETA);
        return UpdateServiceTax.download(query);
    };

    this.logout = function(){
        return OperatorLogout.download(Query.build());
    };

    this.getPayments = function(accountData) {
        var query = Query.build()
            .where('DATA').equals(accountData);
        return GetPayments.download(query);
    };

    this.checkVoucher = function(CDPRODUTO, NRVENDAREST, NRCOMANDA, CDCUPOMDESCFOS){
        var query = Query.build()
                        .where('CDPRODUTO').equals(CDPRODUTO)
                        .where('NRVENDAREST').equals(NRVENDAREST)
                        .where('NRCOMANDA').equals(NRCOMANDA)
                        .where('CDCUPOMDESCFOS').equals(CDCUPOMDESCFOS);
        return VoucherRepository.download(query);
    };

}

Configuration(function(ContextRegister) {
	ContextRegister.register('AccountService', AccountService);
});