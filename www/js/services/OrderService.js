function OrderService(Query, OrderRequestLoginRepository, OrderGetAccessRepository, OrderAllowUserAccessRepository, OrderControlUserAccessRepository, OrderLoginUserRepository, OrderReturnAccess, OrderReturnTablesRepository, OrderCallWaiterRepository, OrderGetCallRepository,OrderAnswerTableRepository, OrderCheckAccess, OrderCheckBlockedUsers, OrderBlockedIps, NewConsumerRepository, ConsumerLoginRepository){

	this.login = function(DSEMAILCONS, password){
		var query = Query.build()
						.where('DSEMAILCONS').equals(DSEMAILCONS)
						.where('password').equals(password);
		return ConsumerLoginRepository.download(query);
	};

	this.requestLogin = function(nome, mesa, frontVersion, ip){
		var query = Query.build()
						.where('nome').equals(nome)
						.where('mesa').equals(mesa)
						.where('frontVersion').equals(frontVersion)
						.where('ip').equals(ip);
		return OrderRequestLoginRepository.download(query);
	};

	this.getAccess = function(){
		var query = Query.build();
		return OrderGetAccessRepository.download(query);
	};

	this.getCall = function(){
		var query = Query.build();
		return OrderGetCallRepository.download(query);
	};

	this.returnTables = function(){
		var query = Query.build();
		return OrderReturnTablesRepository.download(query);
	};

	this.allowUserAccess = function(chave, nracessouser){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('NRACESSOUSER').equals(nracessouser);

		return OrderAllowUserAccessRepository.download(query);
	};

	this.controlUserAccess = function(nracessouser, status, chave){
		var query = Query.build()
						.where('NRACESSOUSER').equals(nracessouser)
						.where('status').equals(status)
						.where('CHAVE').equals(chave);

		return OrderControlUserAccessRepository.download(query);
	};

	this.checkBlockedUsers = function () {
		return OrderCheckBlockedUsers.download(Query.build());
	};

	this.getBlockedIps = function (chave) {
		var query = Query.build()
						.where('chave').equals(chave);
		return OrderBlockedIps.download(query);
	};

	this.loginUser = function(nracessouser, ip){
		var query = Query.build()
						.where('NRACESSOUSER').equals(nracessouser)
						.where('ip').equals(ip);

		return OrderLoginUserRepository.download(query);
	};

	 this.verificaAcesso = function(user){
			var query = Query.build()
						.where('NMUSUARIO').equals(user);

		return OrderReturnAccess.download(query);
	};

	this.callWaiter = function(nracessouser, callType){
		var query = Query.build()
						.where('nracessouser').equals(nracessouser)
						.where('tipoChamada').equals(callType);

		return OrderCallWaiterRepository.download(query);
	};

	this.answerTable = function(nracessouser){
		var query = Query.build()
						.where('nracessouser').equals(nracessouser);
		return OrderAnswerTableRepository.download(query);
	};

	this.checkAccess = function (chave, nrcomanda, nrvendarest) {
		var query = Query.build()
						.where('chave').equals(chave)
						.where('nrcomanda').equals(nrcomanda)
						.where(nrvendarest).equals(nrvendarest);
		return OrderCheckAccess.download(query);
	};

	this.newConsumer = function(NMCONSUMIDOR, DSEMAILCONS, NRCELULARCONS, CDSENHACONSMD5, CDIDCONSUMID){
		var query = Query.build()
						.where('NMCONSUMIDOR').equals(NMCONSUMIDOR)
						.where('DSEMAILCONS').equals(DSEMAILCONS)
						.where('NRCELULARCONS').equals(NRCELULARCONS)
						.where('CDSENHACONSMD5').equals(CDSENHACONSMD5)
						.where('CDIDCONSUMID').equals(CDIDCONSUMID);
		return NewConsumerRepository.download(query);
	};

}

Configuration(function(ContextRegister) {
	ContextRegister.register('OrderService', OrderService);
});