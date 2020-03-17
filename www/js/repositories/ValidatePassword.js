Configuration(function(ContextRegister, RepositoryFactory) {
    var ValidatePassword = RepositoryFactory.factory('/ValidatePassword', 'MEMORY', 1, 20000);
    ContextRegister.register('ValidatePassword', ValidatePassword);
});