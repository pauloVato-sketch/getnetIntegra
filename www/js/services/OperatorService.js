function OperatorService(OperatorRepository, Query, OperatorValidateSupervisor, FiliaisLogin, ValidateConsumerPass,
						 CaixasLogin, VendedoresLogin, TrocaModoCaixa, FindTefSSLConnectionId, auth, FindPendingPayments) {

	this.login = function(filial, caixa, operador, senha, version, currentMode){

		var query = Query.build()
			.where("filial")
			.equals(filial)
			.where("caixa")
			.equals(caixa)
			.where("operador")
			.equals(operador)
			.where("senha")
			.equals(senha)
			.where("version")
			.equals(version)
			.where("currentMode")
			.equals(currentMode);
		return OperatorRepository.download(query);
	};

	this.getFiliaisLogin = function(filialField) {
		var query = Query.build();
		return FiliaisLogin.downloadSome(query, 1, filialField.itemsPerPage);
	};

	this.getCaixasLogin = function(filial, caixasField) {
		var query = Query.build()
			.where("CDFILIAL")
			.equals(filial);
		return CaixasLogin.downloadSome(query, 1, caixasField.itemsPerPage);
	};

	this.getVendedoresLogin = function(filial, vendedoresField) {
		var query = Query.build()
			.where("CDFILIAL")
			.equals(filial);
		return VendedoresLogin.downloadSome(query, 1, vendedoresField.itemsPerPage);
	};

	this.validateSupervisor = function(supervisor, senha, accessParam) {
		var query = Query.build()
			.where("supervisor")
			.equals(supervisor)
			.where("senha")
			.equals(senha)
			.where("accessParam")
			.equals(accessParam);
		return OperatorValidateSupervisor.download(query);
	};

	this.validateConsumerPass = function(CDCLIENTE, CDCONSUMIDOR, senha) {
		var query = Query.build()
			.where("CDCLIENTE")
			.equals(CDCLIENTE)
			.where("CDCONSUMIDOR")
			.equals(CDCONSUMIDOR)
			.where("senha")
			.equals(senha);
		return ValidateConsumerPass.download(query);
	};

	this.trocaModoCaixa = function(chaveSessao, currentMode) {
		var query = Query.build()
			.where("currentMode")
			.equals(currentMode)
			.where("chaveSessao")
			.equals(chaveSessao);
		return TrocaModoCaixa.download(query);
	};

	this.buscaTefSSLConnectionId = function(IDSERIALDISP) {
		var query = Query.build()
			.where("IDSERIALDISP")
			.equals(IDSERIALDISP);
		return FindTefSSLConnectionId.download(query);
	};

	this.auth = function(email, senha) {
		var query = Query.build()
			.where("email")
			.equals(email)
			.where("senha")
			.equals(senha);
		return auth.download(query);
	};
	
	this.findPendingPayments = function() {
		var query = Query.build();
		return FindPendingPayments.download(query);
	};
}

Configuration(function(ContextRegister) {
	ContextRegister.register("OperatorService", OperatorService);
});
