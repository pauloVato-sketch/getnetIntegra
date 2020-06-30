Configuration(function(ContextRegister, RepositoryFactory) {
	var FindUpdatedEmailTransaction = RepositoryFactory.factory('/FindUpdatedEmailTransaction', 'MEMORY');
	ContextRegister.register('FindUpdatedEmailTransaction', FindUpdatedEmailTransaction);
});