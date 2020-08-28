Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderAnswerTableRepository = RepositoryFactory.factory('/OrderAnswerTableRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('OrderAnswerTableRepository', OrderAnswerTableRepository);
});