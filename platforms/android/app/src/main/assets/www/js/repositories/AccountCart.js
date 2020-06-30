Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountCart = RepositoryFactory.factory('/AccountCart', 'MEMORY', 1, 20000);
	/*
	var itensInMemory = [];
	AccountCart.findInMemory = function() {
		return ZHPromise.when(itensInMemory);
	};

	//var oldSave = AccountCart.save;

	AccountCart.saveInMemory = function(obj) {
		itensInMemory.push(obj) ;
		oldSave(obj);
		return ZHPromise.when(obj);
	};

	AccountCart.save = AccountCart.saveInMemory;
	*/

	ContextRegister.register('AccountCart', AccountCart);
});