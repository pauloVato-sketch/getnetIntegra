Configuration(function(ContextRegister, RepositoryFactory) {	
	var SitefPayment = RepositoryFactory.factory('/SitefPayment', 'LOCAL');
	ContextRegister.register('SitefPayment', SitefPayment);
});

