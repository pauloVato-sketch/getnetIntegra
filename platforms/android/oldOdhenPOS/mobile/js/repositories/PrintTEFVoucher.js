Configuration(function(ContextRegister, RepositoryFactory) {
	var PrintTEFVoucher = RepositoryFactory.factory('/PrintTEFVoucher', 'MEMORY', 1, 30000);
	ContextRegister.register('PrintTEFVoucher', PrintTEFVoucher);
});