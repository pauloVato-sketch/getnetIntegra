function MemoryStorageAsync(ZHPromise) {
	var mapVar = {};

	this.removeItem = function(name) {
		delete mapVar[name];
		return ZHPromise.when();
	};

	this.setLocalVar = function (name, value) {
		mapVar[name] = value;
		return ZHPromise.when(value);
	};
	
	this.getLocalVar = function (name) {
		return ZHPromise.when(mapVar[name]);
	};
}
