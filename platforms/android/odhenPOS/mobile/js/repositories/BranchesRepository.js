Configuration(function(ContextRegister, RepositoryFactory) {
	var BranchesRepository = RepositoryFactory.factory('/BranchesRepository', 'MEMORY');
	ContextRegister.register('BranchesRepository', BranchesRepository);
});