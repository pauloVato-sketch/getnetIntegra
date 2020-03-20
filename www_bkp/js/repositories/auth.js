Configuration(function(ContextRegister, RepositoryFactory) {
	var auth = RepositoryFactory.factory("/auth", "MEMORY", 1, 90000);
	ContextRegister.register("auth", auth, 1, 30000);
});
