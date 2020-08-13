Configuration(function(ContextRegister, RepositoryFactory, ZHPromise) {
	var OperatorRepository = RepositoryFactory.factory('/OperatorRepository', 'MEMORY', 1, 90000);

	var currentOperator = null;
	OperatorRepository.findOneInMemory = function() {
		if(!currentOperator){
			return OperatorRepository.findOne().then(function(operatorData){
				currentOperator = operatorData;
				return currentOperator;
			});
		}else{
			return ZHPromise.when(currentOperator);
		}
	};

	var oldSave = OperatorRepository.save;

	OperatorRepository.saveInMemory = function(obj) {
		currentOperator = null;
		oldSave(obj);
		return ZHPromise.when(obj);
	};

	OperatorRepository.save = OperatorRepository.saveInMemory;
	ContextRegister.register('OperatorRepository', OperatorRepository, 1, 30000);
});