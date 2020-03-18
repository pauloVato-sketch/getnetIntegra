function MemoryStorage() {
	var mapVar = {};

	this.removeItem = function(name) {
		delete mapVar[name];
	};

	this.setLocalVar = function (name, value) {
		mapVar[name] = value;
	};
	
	this.getLocalVar = function (name) {
		return mapVar[name];
	};
}
var memoryStorage = new MemoryStorage();
