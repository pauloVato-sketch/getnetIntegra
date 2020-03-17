function CieloTestService(CieloTest, Query, restEngine) {
	
	this.testConnection = function() {
		var query = Query.build();
		return CieloTest.download(query);
		
		// restEngine.post('/CieloTest');
	};
	
}

Configuration(function(ContextRegister) {
	ContextRegister.register('CieloTestService', CieloTestService);
});