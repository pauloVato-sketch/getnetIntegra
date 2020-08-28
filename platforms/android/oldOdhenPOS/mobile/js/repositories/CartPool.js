Configuration(function(ContextRegister, RepositoryFactory, ZHPromise) {
	var CartPool = RepositoryFactory.factory('/CartPool', 'MEMORY', 1, 10000);
	/*
	var itensInMemory = [];
	CartPool.findInMemory = function() {
		return ZHPromise.when(itensInMemory);
	};

	//var oldSave = CartPool.save;

	CartPool.saveInMemory = function(obj) {
		itensInMemory.push(obj) ;
		oldSave(obj);
		return ZHPromise.when(obj);
	};

	CartPool.save = CartPool.saveInMemory;
	*/

	ContextRegister.register('CartPool', CartPool);
});