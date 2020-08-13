function RegisterService(RegisterOpen, RegisterClose, RegisterClosingPayments, Query, OperatorValidateSupervisor) {
	this.closingOnLogin = false;

	this.openRegister = function(chave, VRMOVIVEND){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('VRMOVIVEND').equals(VRMOVIVEND);
		return RegisterOpen.download(query);
	};

	this.closeRegister = function(chave, TIPORECE){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('TIPORECE').equals(TIPORECE);
		return RegisterClose.download(query);
	};

	this.getClosingPayments = function(chave){
		var query = Query.build()
						.where('chave').equals(chave);
		return RegisterClosingPayments.download(query);
	};

	this.setClosingOnLogin = function(value){
		this.closingOnLogin = value;
	};

	this.getClosingOnLogin = function(value){
		return this.closingOnLogin;
	};
}

Configuration(function(ContextRegister) {
	ContextRegister.register('RegisterService', RegisterService);
});