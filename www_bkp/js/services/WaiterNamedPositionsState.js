function WaiterNamedPositionsState() {

	this.mustUnselect = false;

	this.initializeTemplate = function() {
		this.startWith = 0;
		this.flagNextAndPrev = false;
		this.showPrev = false;
		this.showNext = false;
		this.oldNrTotalPosicoes = 0;
		this.oldMaxButtons = 0;
		this.maxButtons = 0;
		this.currentPage = {
			'page': 0,
			'paginated': false
		};
		this.oldCurrentPage = 0;
		this.finishWith = 0;
		this.clientMapping = {};
		this.consumerMapping = {};
		this.positionNamedMapping = {};
		this.numberOfButtons = 0;
		this.pageHistory = [];
		this.oldTotalPosicoes = 0;
		this.currentPositionsObject = null;	
		this.mustUnselect = true;
	};

	this.unselectAllPositions = function() {
		this.mustUnselect = true;
	};

}

Configuration(function(ContextRegister) {
	ContextRegister.register('WaiterNamedPositionsState', WaiterNamedPositionsState);
});