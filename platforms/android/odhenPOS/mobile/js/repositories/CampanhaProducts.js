Configuration(function(ContextRegister, RepositoryFactory) {
    var CampanhaProducts = RepositoryFactory.factory('/CampanhaProducts', 'MEMORY', 1, 20000);
    /*
    var itensInMemory = [];
    CampanhaProducts.findInMemory = function() {
        return ZHPromise.when(itensInMemory);
    };

    //var oldSave = CampanhaProducts.save;

    CampanhaProducts.saveInMemory = function(obj) {
        itensInMemory.push(obj) ;
        oldSave(obj);
        return ZHPromise.when(obj);
    };

    CampanhaProducts.save = CampanhaProducts.saveInMemory;
    */

    ContextRegister.register('CampanhaProducts', CampanhaProducts);
});