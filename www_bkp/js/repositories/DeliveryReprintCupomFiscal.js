Configuration(function(ContextRegister, RepositoryFactory) {
	var DeliveryReprintCupomFiscal = RepositoryFactory.factory('/DeliveryReprintCupomFiscal', 'MEMORY', 1, 20000);
	ContextRegister.register('DeliveryReprintCupomFiscal', DeliveryReprintCupomFiscal);
});