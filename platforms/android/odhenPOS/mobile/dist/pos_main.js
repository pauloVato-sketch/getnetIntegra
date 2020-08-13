
// FILE: js/overrides.js
Object.keys = _.keys;

// FILE: config.js
var projectConfig = {
	currentMode: 'M',
	serviceUrl: '/odhenPOS/backend/service/index.php',
	frontVersion : '6.0.8'
};

// FILE: js/repositories/AccountCancelProduct.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountCancelProduct = RepositoryFactory.factory('/AccountCancelProduct', 'MEMORY', 1, 20000);
	ContextRegister.register('AccountCancelProduct', AccountCancelProduct);
});

// FILE: js/repositories/AccountCart.js
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

// FILE: js/repositories/AccountChangeClientConsumer.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountChangeClientConsumer = RepositoryFactory.factory('/AccountChangeClientConsumer', 'MEMORY', 1, 20000);
	ContextRegister.register('AccountChangeClientConsumer', AccountChangeClientConsumer);
});

// FILE: js/repositories/AccountGetAccountDetails.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountGetAccountDetails = RepositoryFactory.factory('/AccountGetAccountDetails', 'MEMORY', 1, 20000);
	ContextRegister.register('AccountGetAccountDetails', AccountGetAccountDetails);
});

// FILE: js/repositories/AccountGetAccountItems.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountGetAccountItems = RepositoryFactory.factory('/AccountGetAccountItems', 'MEMORY', 1, 20000);
	ContextRegister.register('AccountGetAccountItems', AccountGetAccountItems);
});

// FILE: js/repositories/AccountGetAccountItemsForTransfer.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountGetAccountItemsForTransfer = RepositoryFactory.factory('/AccountGetAccountItemsForTransfer', 'MEMORY', 1, 20000);
	ContextRegister.register('AccountGetAccountItemsForTransfer', AccountGetAccountItemsForTransfer);
});

// FILE: js/repositories/AccountGetAccountItemsWithoutCombo.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountGetAccountItemsWithoutCombo = RepositoryFactory.factory('/AccountGetAccountItemsWithoutCombo', 'MEMORY', 1, 20000);
	ContextRegister.register('AccountGetAccountItemsWithoutCombo', AccountGetAccountItemsWithoutCombo);
});

// FILE: js/repositories/AccountGetOriginalAccountItems.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountGetOriginalAccountItems = RepositoryFactory.factory('/AccountGetOriginalAccountItems', 'MEMORY', 1, 20000);
	ContextRegister.register('AccountGetOriginalAccountItems', AccountGetOriginalAccountItems);
});

// FILE: js/repositories/AccountGetTableTrasanctions.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountGetTableTrasanctions = RepositoryFactory.factory('/AccountGetTableTrasanctions', 'MEMORY', 1, 20000);
	ContextRegister.register('AccountGetTableTrasanctions', AccountGetTableTrasanctions);
});

// FILE: js/repositories/AccountGetTrasanctions.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountGetTrasanctions = RepositoryFactory.factory('/AccountGetTrasanctions', 'MEMORY', 1, 20000);
	ContextRegister.register('AccountGetTrasanctions', AccountGetTrasanctions);
});

// FILE: js/repositories/AccountLastOrders.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountLastOrders = RepositoryFactory.factory('/AccountLastOrders', 'MEMORY', 1, 20000);
	ContextRegister.register('AccountLastOrders', AccountLastOrders);
});

// FILE: js/repositories/AccountOrder.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountOrder = RepositoryFactory.factory('/AccountOrder', 'MEMORY', 1, 25000);
	ContextRegister.register('AccountOrder', AccountOrder);
});

// FILE: js/repositories/AccountPayment.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountPayment = RepositoryFactory.factory('/AccountPayment', 'MEMORY');
	ContextRegister.register('AccountPayment', AccountPayment);
});

// FILE: js/repositories/AccountPaymentBegin.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountPaymentBegin = RepositoryFactory.factory('/AccountPaymentBegin', 'MEMORY');
	ContextRegister.register('AccountPaymentBegin', AccountPaymentBegin);
});

// FILE: js/repositories/AccountPaymentFinish.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountPaymentFinish = RepositoryFactory.factory('/AccountPaymentFinish', 'MEMORY');
	ContextRegister.register('AccountPaymentFinish', AccountPaymentFinish);
});

// FILE: js/repositories/AccountPaymentTypedCredit.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountPaymentTypedCredit = RepositoryFactory.factory('/AccountPaymentTypedCredit', 'MEMORY');
	ContextRegister.register('AccountPaymentTypedCredit', AccountPaymentTypedCredit);
});

// FILE: js/repositories/AccountSaleCode.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountSaleCode = RepositoryFactory.factory('/AccountSaleCode', 'MEMORY');
	ContextRegister.register('AccountSaleCode', AccountSaleCode);
});

// FILE: js/repositories/AccountSavedCarts.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountSavedCarts = RepositoryFactory.factory('/AccountSavedCarts', 'MEMORY', 1, 20000);
	ContextRegister.register('AccountSavedCarts', AccountSavedCarts);
});

// FILE: js/repositories/AddconsumerRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var AddconsumerRepository = RepositoryFactory.factory('/AddconsumerRepository', 'MEMORY', 1, 20000);
    ContextRegister.register('AddconsumerRepository', AddconsumerRepository);
});

// FILE: js/repositories/auth.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var auth = RepositoryFactory.factory("/auth", "MEMORY", 1, 90000);
	ContextRegister.register("auth", auth, 1, 30000);
});


// FILE: js/repositories/BillActiveBill.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var BillActiveBill = RepositoryFactory.factory('/BillActiveBill', 'MEMORY', 1, 20000);
	ContextRegister.register('BillActiveBill', BillActiveBill);
});

// FILE: js/repositories/BillCancelOpen.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var BillCancelOpen = RepositoryFactory.factory('/BillCancelOpen', 'MEMORY', 1, 20000);
	ContextRegister.register('BillCancelOpen', BillCancelOpen);
});

// FILE: js/repositories/BillOpenBill.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var BillOpenBill = RepositoryFactory.factory('/BillOpenBill', 'MEMORY', 1, 20000);
	ContextRegister.register('BillOpenBill', BillOpenBill);
});

// FILE: js/repositories/BillRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var BillRepository = RepositoryFactory.factory('/BillRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('BillRepository', BillRepository);
});

// FILE: js/repositories/BillValidateBill.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var BillValidateBill = RepositoryFactory.factory('/BillValidateBill', 'MEMORY', 1, 20000);
	ContextRegister.register('BillValidateBill', BillValidateBill);
});

// FILE: js/repositories/BlockProducts.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var BlockProducts = RepositoryFactory.factory('/BlockProducts', 'MEMORY', 1, 20000);
    ContextRegister.register('BlockProducts', BlockProducts);
});

// FILE: js/repositories/BranchesRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var BranchesRepository = RepositoryFactory.factory('/BranchesRepository', 'MEMORY');
	ContextRegister.register('BranchesRepository', BranchesRepository);
});

// FILE: js/repositories/CaixasLogin.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var CaixasLogin = RepositoryFactory.factory('/CaixasLogin', 'ONLINE', 1, 20000);
	ContextRegister.register('CaixasLogin', CaixasLogin);
});

// FILE: js/repositories/CalculaDescontoSubgrupo.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var CalculaDescontoSubgrupo = RepositoryFactory.factory('/CalculaDescontoSubgrupo', 'MEMORY', 1, 20000);
    ContextRegister.register('CalculaDescontoSubgrupo', CalculaDescontoSubgrupo);
});

// FILE: js/repositories/CancelCreditRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var CancelCreditRepository = RepositoryFactory.factory('/CancelCreditRepository', 'MEMORY', 1, 60000);
    ContextRegister.register('CancelCreditRepository', CancelCreditRepository);
});

// FILE: js/repositories/CancelDeliveryOrder.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var CancelDeliveryOrder = RepositoryFactory.factory('/CancelDeliveryOrder', 'MEMORY', 1, 20000);
	ContextRegister.register('CancelDeliveryOrder', CancelDeliveryOrder);
});

// FILE: js/repositories/CancelDeliveryProduct.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var CancelDeliveryProduct = RepositoryFactory.factory('/CancelDeliveryProduct', 'MEMORY', 1, 20000);
	ContextRegister.register('CancelDeliveryProduct', CancelDeliveryProduct);
});

// FILE: js/repositories/CancelSplitedProductsRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var CancelSplitedProductsRepository = RepositoryFactory.factory('/CancelSplitedProductsRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('CancelSplitedProductsRepository', CancelSplitedProductsRepository);
});

// FILE: js/repositories/CarrinhoDesistencia.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var CarrinhoDesistencia = RepositoryFactory.factory('/CarrinhoDesistencia', 'MEMORY', 1, 20000);
    ContextRegister.register('CarrinhoDesistencia', CarrinhoDesistencia);
});

// FILE: js/repositories/CartPool.js
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

// FILE: js/repositories/CartPricesRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var CartPricesRepository = RepositoryFactory.factory('/CartPricesRepository', 'MEMORY', 1, 20000);
    ContextRegister.register('CartPricesRepository', CartPricesRepository);
});

// FILE: js/repositories/ChangeProductDiscount.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var ChangeProductDiscount = RepositoryFactory.factory('/ChangeProductDiscount', 'MEMORY', 1, 20000);
	ContextRegister.register('ChangeProductDiscount', ChangeProductDiscount);
});

// FILE: js/repositories/ChargePersonalCredit.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var ChargePersonalCredit = RepositoryFactory.factory('/ChargePersonalCredit', 'MEMORY', 1, 60000);
    ContextRegister.register('ChargePersonalCredit', ChargePersonalCredit);
});

// FILE: js/repositories/CheckRefilRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var CheckRefilRepository = RepositoryFactory.factory('/CheckRefilRepository', 'MEMORY', 1, 20000);
    ContextRegister.register('CheckRefilRepository', CheckRefilRepository);
});

// FILE: js/repositories/CieloTest.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var CieloTest = RepositoryFactory.factory('/CieloTest', 'MEMORY');
	ContextRegister.register('CieloTest', CieloTest);
});

// FILE: js/repositories/CityRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var CityRepository = RepositoryFactory.factory('/CityRepository', 'MEMORY', 1, 20000);
    ContextRegister.register('CityRepository', CityRepository);
});

// FILE: js/repositories/ConcludeOrderDlv.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var ConcludeOrderDlv = RepositoryFactory.factory('/ConcludeOrderDlv', 'MEMORY', 1, 20000);
	ContextRegister.register('ConcludeOrderDlv', ConcludeOrderDlv);
});

// FILE: js/repositories/ConfigIpRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var ConfigIpRepository = RepositoryFactory.factory('/ConfigIpRepository', 'INDEXEDDB');
	ContextRegister.register('ConfigIpRepository', ConfigIpRepository);
});

// FILE: js/repositories/ConsumerBalanceRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var ConsumerBalanceRepository = RepositoryFactory.factory('/ConsumerBalanceRepository', 'MEMORY', 1, 60000);
    ContextRegister.register('ConsumerBalanceRepository', ConsumerBalanceRepository);
});


// FILE: js/repositories/ConsumerLoginRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var ConsumerLoginRepository = RepositoryFactory.factory('/ConsumerLoginRepository', 'MEMORY');
	ContextRegister.register('ConsumerLoginRepository', ConsumerLoginRepository);
});

// FILE: js/repositories/ConsumerRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var ConsumerRepository = RepositoryFactory.factory('/ConsumerRepository', 'MEMORY');
	ContextRegister.register('ConsumerRepository', ConsumerRepository);
});

// FILE: js/repositories/ConsumerSearchRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var ConsumerSearchRepository = RepositoryFactory.factory('/ConsumerSearchRepository', 'MEMORY', 1, 90000);
    ContextRegister.register('ConsumerSearchRepository', ConsumerSearchRepository);
});

// FILE: js/repositories/ConsumerTypeRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var ConsumerTypeRepository = RepositoryFactory.factory('/ConsumerTypeRepository', 'MEMORY', 1, 20000);
    ContextRegister.register('ConsumerTypeRepository', ConsumerTypeRepository);
});

// FILE: js/repositories/CountryRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var CountryRepository = RepositoryFactory.factory('/CountryRepository', 'MEMORY', 1, 20000);
    ContextRegister.register('CountryRepository', CountryRepository);
});

// FILE: js/repositories/DelayedProductsRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var DelayedProductsRepository = RepositoryFactory.factory('/DelayedProductsRepository', 'MEMORY');
	ContextRegister.register('DelayedProductsRepository', DelayedProductsRepository);
});

// FILE: js/repositories/DeliveryCheckOutOrders.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var DeliveryCheckOutOrders = RepositoryFactory.factory('/DeliveryCheckOutOrders', 'MEMORY');
	ContextRegister.register('DeliveryCheckOutOrders', DeliveryCheckOutOrders);
});

// FILE: js/repositories/DeliveryControlRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var DeliveryControlRepository = RepositoryFactory.factory('/DeliveryControlRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('DeliveryControlRepository', DeliveryControlRepository);
});

// FILE: js/repositories/DeliveryPrint.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var DeliveryPrint = RepositoryFactory.factory('/DeliveryPrint', 'MEMORY', 1, 25000);
	ContextRegister.register('DeliveryPrint', DeliveryPrint);
});

// FILE: js/repositories/DeliveryRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var DeliveryRepository = RepositoryFactory.factory('/DeliveryRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('DeliveryRepository', DeliveryRepository);
});

// FILE: js/repositories/DeliveryReprintCupomFiscal.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var DeliveryReprintCupomFiscal = RepositoryFactory.factory('/DeliveryReprintCupomFiscal', 'MEMORY', 1, 20000);
	ContextRegister.register('DeliveryReprintCupomFiscal', DeliveryReprintCupomFiscal);
});

// FILE: js/repositories/DeliverySendOrders.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var DeliverySendOrders = RepositoryFactory.factory('/DeliverySendOrders', 'MEMORY', 1, 20000);
	ContextRegister.register('DeliverySendOrders', DeliverySendOrders);
});

// FILE: js/repositories/EmptyRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var EmptyRepository = RepositoryFactory.factory('/EmptyRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('EmptyRepository', EmptyRepository);
});

// FILE: js/repositories/ExportLogs.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var ExportLogs = RepositoryFactory.factory('/ExportLogs', 'MEMORY', 1, 20000);
	ContextRegister.register('ExportLogs', ExportLogs);
});

// FILE: js/repositories/FidelityDetailsRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var FidelityDetailsRepository = RepositoryFactory.factory('/FidelityDetailsRepository', 'MEMORY', 1, 30000);
    ContextRegister.register('FidelityDetailsRepository', FidelityDetailsRepository);
});

// FILE: js/repositories/FiliaisLogin.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var FiliaisLogin = RepositoryFactory.factory('/FiliaisLogin', 'ONLINE', 1, 20000);
	ContextRegister.register('FiliaisLogin', FiliaisLogin);
});

// FILE: js/repositories/FilterProducts.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var FilterProducts = RepositoryFactory.factory('/FilterProducts', 'MEMORY', 1, 20000);
    ContextRegister.register('FilterProducts', FilterProducts);
});

// FILE: js/repositories/FindPendingPayments.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var FindPendingPayments = RepositoryFactory.factory('/FindPendingPayments', 'MEMORY', 1, 20000);
	ContextRegister.register('FindPendingPayments', FindPendingPayments);
});

// FILE: js/repositories/FindRowToCancel.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var FindRowToCancel = RepositoryFactory.factory('/FindRowToCancel', 'MEMORY');
	ContextRegister.register('FindRowToCancel', FindRowToCancel);
});

// FILE: js/repositories/FindTefSSLConnectionId.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var FindTefSSLConnectionId = RepositoryFactory.factory('/FindTefSSLConnectionId', 'MEMORY', 1, 25000);
	ContextRegister.register('FindTefSSLConnectionId', FindTefSSLConnectionId);
});

// FILE: js/repositories/FindUpdatedEmailTransaction.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var FindUpdatedEmailTransaction = RepositoryFactory.factory('/FindUpdatedEmailTransaction', 'MEMORY');
	ContextRegister.register('FindUpdatedEmailTransaction', FindUpdatedEmailTransaction);
});

// FILE: js/repositories/GetCampanhaRepo.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var GetCampanhaRepo = RepositoryFactory.factory('/GetCampanhaRepo', 'MEMORY', 1, 20000);
    ContextRegister.register('GetCampanhaRepo', GetCampanhaRepo);
});

// FILE: js/repositories/GetConsumerLimit.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var GetConsumerLimit = RepositoryFactory.factory('/GetConsumerLimit', 'MEMORY', 1, 60000);
	ContextRegister.register('GetConsumerLimit', GetConsumerLimit);
});

// FILE: js/repositories/GetNrControlTef.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var GetNrControlTef = RepositoryFactory.factory('/GetNrControlTef', 'MEMORY', 1, 60000);
    ContextRegister.register('GetNrControlTef', GetNrControlTef);
});


// FILE: js/repositories/GetPayments.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var GetPayments = RepositoryFactory.factory('/GetPayments', 'MEMORY', 1, 20000);
	ContextRegister.register('GetPayments', GetPayments);
});

// FILE: js/repositories/GetTransactionCode.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var GetTransactionCode = RepositoryFactory.factory('/GetTransactionCode', 'MEMORY', 1, 20000);
	ContextRegister.register('GetTransactionCode', GetTransactionCode);
});

// FILE: js/repositories/GroupPriceChart.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var GroupPriceChart = RepositoryFactory.factory('/GroupPriceChart', 'MEMORY');
	ContextRegister.register('GroupPriceChart', GroupPriceChart);
});

// FILE: js/repositories/HomologacaoSitef.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var HomologacaoSitef = RepositoryFactory.factory('/HomologacaoSitef', 'MEMORY', 1, 20000);
    ContextRegister.register('HomologacaoSitef', HomologacaoSitef);
});

// FILE: js/repositories/ImpressaoLeituraX.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var ImpressaoLeituraX = RepositoryFactory.factory('/ImpressaoLeituraX', 'MEMORY', 1, 20000);
    ContextRegister.register('ImpressaoLeituraX', ImpressaoLeituraX);
});

// FILE: js/repositories/ItemSangria.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var ItemSangria = RepositoryFactory.factory('/ItemSangria', 'MEMORY', 1, 20000);
	ContextRegister.register('ItemSangria', ItemSangria);
});

// FILE: js/repositories/LockPositionRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var LockPositionRepository = RepositoryFactory.factory('/LockPositionRepository', 'MEMORY', 1, 20000);
    ContextRegister.register('LockPositionRepository', LockPositionRepository);
});

// FILE: js/repositories/Movcaixadlv.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var Movcaixadlv = RepositoryFactory.factory('/Movcaixadlv', 'MEMORY', 1, 20000);
	ContextRegister.register('Movcaixadlv', Movcaixadlv);
});

// FILE: js/repositories/NeighborhoodRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var NeighborhoodRepository = RepositoryFactory.factory('/NeighborhoodRepository', 'MEMORY', 1, 20000);
    ContextRegister.register('NeighborhoodRepository', NeighborhoodRepository);
});

// FILE: js/repositories/NewConsumerRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var NewConsumerRepository = RepositoryFactory.factory('/NewConsumerRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('NewConsumerRepository', NewConsumerRepository);
});

// FILE: js/repositories/OperatorLogout.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var OperatorLogout = RepositoryFactory.factory('/OperatorLogout', 'MEMORY', 1, 20000);
	ContextRegister.register('OperatorLogout', OperatorLogout);
});

// FILE: js/repositories/OperatorRepository.js
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

// FILE: js/repositories/OperatorValidateSupervisor.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var OperatorValidateSupervisor = RepositoryFactory.factory('/OperatorValidateSupervisor', 'MEMORY', 1, 20000);
	ContextRegister.register('OperatorValidateSupervisor', OperatorValidateSupervisor);
});

// FILE: js/repositories/OrderAllowUserAccessRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderAllowUserAccessRepository = RepositoryFactory.factory('/OrderAllowUserAccessRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('OrderAllowUserAccessRepository', OrderAllowUserAccessRepository);
});

// FILE: js/repositories/OrderAnswerTableRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderAnswerTableRepository = RepositoryFactory.factory('/OrderAnswerTableRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('OrderAnswerTableRepository', OrderAnswerTableRepository);
});

// FILE: js/repositories/OrderBlockedIps.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderBlockedIps = RepositoryFactory.factory('/OrderBlockedIps', 'MEMORY', 1, 20000);
	ContextRegister.register('OrderBlockedIps', OrderBlockedIps);
});

// FILE: js/repositories/OrderCallWaiterRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderCallWaiterRepository = RepositoryFactory.factory('/OrderCallWaiterRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('OrderCallWaiterRepository', OrderCallWaiterRepository);
});

// FILE: js/repositories/OrderCheckAccess.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderCheckAccess = RepositoryFactory.factory('/OrderCheckAccess', 'MEMORY', 1, 20000);
	ContextRegister.register('OrderCheckAccess', OrderCheckAccess);
});

// FILE: js/repositories/OrderCheckBlockedUsers.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderCheckBlockedUsers = RepositoryFactory.factory('/OrderCheckBlockedUsers', 'MEMORY', 1, 20000);
	ContextRegister.register('OrderCheckBlockedUsers', OrderCheckBlockedUsers);
});

// FILE: js/repositories/OrderControlUserAccessRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderControlUserAccessRepository = RepositoryFactory.factory('/OrderControlUserAccessRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('OrderControlUserAccessRepository', OrderControlUserAccessRepository);
});

// FILE: js/repositories/OrderCurrentProductRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderCurrentProductRepository = RepositoryFactory.factory('/OrderCurrentProductRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('OrderCurrentProductRepository', OrderCurrentProductRepository);
});

// FILE: js/repositories/OrderCurrentUser.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderCurrentUser = RepositoryFactory.factory('/OrderCurrentUser', 'MEMORY', 1, 20000);
	ContextRegister.register('OrderCurrentUser', OrderCurrentUser);
});

// FILE: js/repositories/OrderGetAccessRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderGetAccessRepository = RepositoryFactory.factory('/OrderGetAccessRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('OrderGetAccessRepository', OrderGetAccessRepository);
});

// FILE: js/repositories/OrderGetCallRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderGetCallRepository = RepositoryFactory.factory('/OrderGetCallRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('OrderGetCallRepository', OrderGetCallRepository);
});

// FILE: js/repositories/OrderListTablesRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderListTablesRepository = RepositoryFactory.factory('/OrderListTablesRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('OrderListTablesRepository', OrderListTablesRepository);
});

// FILE: js/repositories/OrderLoginUserRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderLoginUserRepository = RepositoryFactory.factory('/OrderLoginUserRepository', 'MEMORY');
	ContextRegister.register('OrderLoginUserRepository', OrderLoginUserRepository);
});

// FILE: js/repositories/OrderProductObservation.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderProductObservation = RepositoryFactory.factory('/OrderProductObservation', 'MEMORY', 1, 20000);
	ContextRegister.register('OrderProductObservation', OrderProductObservation);
});

// FILE: js/repositories/OrderRequestLoginRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderRequestLoginRepository = RepositoryFactory.factory('/OrderRequestLoginRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('OrderRequestLoginRepository', OrderRequestLoginRepository);
});

// FILE: js/repositories/OrderReturnAccess.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderReturnAccess = RepositoryFactory.factory('/OrderReturnAccess', 'MEMORY', 1, 20000);
	ContextRegister.register('OrderReturnAccess', OrderReturnAccess);
});

// FILE: js/repositories/OrderReturnTablesRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderReturnTablesRepository = RepositoryFactory.factory('/OrderReturnTablesRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('OrderReturnTablesRepository', OrderReturnTablesRepository);
});

// FILE: js/repositories/ParamsAreaRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var ParamsAreaRepository = RepositoryFactory.factory('/ParamsAreaRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('ParamsAreaRepository', ParamsAreaRepository);
});

// FILE: js/repositories/ParamsCardsRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var ParamsCardsRepository = RepositoryFactory.factory('/ParamsCardsRepository', 'MEMORY', 1, 60000);
    ContextRegister.register('ParamsCardsRepository', ParamsCardsRepository);
});

// FILE: js/repositories/ParamsClientRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var ParamsClientRepository = RepositoryFactory.factory('/ParamsClientRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('ParamsClientRepository', ParamsClientRepository);
});

// FILE: js/repositories/ParamsCustomerRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var ParamsCustomerRepository = RepositoryFactory.factory('/ParamsCustomerRepository', 'MEMORY', 1, 90000);
	ContextRegister.register('ParamsCustomerRepository', ParamsCustomerRepository);
});

// FILE: js/repositories/ParamsFamilyRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var ParamsFamilyRepository = RepositoryFactory.factory('/ParamsFamilyRepository', 'MEMORY', 1, 15000);
    ContextRegister.register('ParamsFamilyRepository', ParamsFamilyRepository);
});

// FILE: js/repositories/ParamsGroupPriceChart.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var ParamsGroupPriceChart = RepositoryFactory.factory('/ParamsGroupPriceChart', 'MEMORY');
	ContextRegister.register('ParamsGroupPriceChart', ParamsGroupPriceChart);
});

// FILE: js/repositories/ParamsGroupRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var ParamsGroupRepository = RepositoryFactory.factory('/ParamsGroupRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('ParamsGroupRepository', ParamsGroupRepository);
});

// FILE: js/repositories/ParamsMensDescontoObs.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var ParamsMensDescontoObs = RepositoryFactory.factory('/ParamsMensDescontoObs', 'MEMORY', 1, 20000);
	ContextRegister.register('ParamsMensDescontoObs', ParamsMensDescontoObs);
});

// FILE: js/repositories/ParamsMensObsRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var ParamsMensObsRepository = RepositoryFactory.factory('/ParamsMensObsRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('ParamsMensObsRepository', ParamsMensObsRepository);
});

// FILE: js/repositories/ParamsMenuRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var ParamsMenuRepository = RepositoryFactory.factory('/ParamsMenuRepository', 'MEMORY', 1, 45000);
	ContextRegister.register('ParamsMenuRepository', ParamsMenuRepository);
});

// FILE: js/repositories/ParamsObservationsRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var ParamsObservationsRepository = RepositoryFactory.factory('/ParamsObservationsRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('ParamsObservationsRepository', ParamsObservationsRepository);
});

// FILE: js/repositories/ParamsParameterRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var ParamsParameterRepository = RepositoryFactory.factory('/ParamsParameterRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('ParamsParameterRepository', ParamsParameterRepository);
});

// FILE: js/repositories/ParamsPriceChart.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var ParamsPriceChart = RepositoryFactory.factory('/ParamsPriceChart', 'MEMORY');
	ContextRegister.register('ParamsPriceChart', ParamsPriceChart);
});

// FILE: js/repositories/ParamsPriceTimeRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var ParamsPriceTimeRepository = RepositoryFactory.factory('/ParamsPriceTimeRepository', 'MEMORY', 1, 30000);
    ContextRegister.register('ParamsPriceTimeRepository', ParamsPriceTimeRepository);
});

// FILE: js/repositories/ParamsPrinterRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var ParamsPrinterRepository = RepositoryFactory.factory('/ParamsPrinterRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('ParamsPrinterRepository', ParamsPrinterRepository);
});

// FILE: js/repositories/ParamsProdMessageCancelRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var ParamsProdMessageCancelRepository = RepositoryFactory.factory('/ParamsProdMessageCancelRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('ParamsProdMessageCancelRepository', ParamsProdMessageCancelRepository);
});

// FILE: js/repositories/ParamsProdMessageRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var ParamsProdMessageRepository = RepositoryFactory.factory('/ParamsProdMessageRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('ParamsProdMessageRepository', ParamsProdMessageRepository);
});

// FILE: js/repositories/ParamsSellerRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var ParamsSellerRepository = RepositoryFactory.factory('/ParamsSellerRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('ParamsSellerRepository', ParamsSellerRepository);
});

// FILE: js/repositories/PaymentPayAccount.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var PaymentPayAccount = RepositoryFactory.factory('/PaymentPayAccount', 'MEMORY', 1, 60000);
	ContextRegister.register('PaymentPayAccount', PaymentPayAccount);
});

// FILE: js/repositories/PaymentRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var PaymentRepository = RepositoryFactory.factory('/PaymentRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('PaymentRepository', PaymentRepository);
});


// FILE: js/repositories/PedidosEntreguesRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var PedidosEntreguesRepository = RepositoryFactory.factory('/PedidosEntreguesRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('PedidosEntreguesRepository', PedidosEntreguesRepository);
});

// FILE: js/repositories/PositionCodeRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var PositionCodeRepository = RepositoryFactory.factory('/PositionCodeRepository', 'MEMORY', 1, 20000);
    ContextRegister.register('PositionCodeRepository', PositionCodeRepository);
});

// FILE: js/repositories/PositionControlRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var PositionControlRepository = RepositoryFactory.factory('/PositionControlRepository', 'MEMORY', 1, 20000);
    ContextRegister.register('PositionControlRepository', PositionControlRepository);
});

// FILE: js/repositories/PriceChart.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var PriceChart = RepositoryFactory.factory('/PriceChart', 'MEMORY');
	ContextRegister.register('PriceChart', PriceChart);
});

// FILE: js/repositories/PrintTEFVoucher.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var PrintTEFVoucher = RepositoryFactory.factory('/PrintTEFVoucher', 'MEMORY', 1, 30000);
	ContextRegister.register('PrintTEFVoucher', PrintTEFVoucher);
});

// FILE: js/repositories/ProdSenhaPed.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var ProdSenhaPed = RepositoryFactory.factory('/ProdSenhaPed', 'MEMORY', 1, 20000);
    ContextRegister.register('ProdSenhaPed', ProdSenhaPed);
});

// FILE: js/repositories/ProdutosDesistencia.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var ProdutosDesistencia = RepositoryFactory.factory('/ProdutosDesistencia', 'MEMORY', 1, 20000);
    ContextRegister.register('ProdutosDesistencia', ProdutosDesistencia);
});

// FILE: js/repositories/QRCodeSaleRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var QRCodeSaleRepository = RepositoryFactory.factory('/QRCodeSaleRepository', 'MEMORY', 1, 60000);
    ContextRegister.register('QRCodeSaleRepository', QRCodeSaleRepository);
});

// FILE: js/repositories/RegisterClose.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var RegisterClose = RepositoryFactory.factory('/RegisterClose', 'MEMORY', 4, 20000);
	ContextRegister.register('RegisterClose', RegisterClose);
});

// FILE: js/repositories/RegisterClosingPayments.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var RegisterClosingPayments = RepositoryFactory.factory('/RegisterClosingPayments', 'MEMORY', 1, 20000);
	ContextRegister.register('RegisterClosingPayments', RegisterClosingPayments);
});

// FILE: js/repositories/RegisterOpen.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var RegisterOpen = RepositoryFactory.factory('/RegisterOpen', 'MEMORY', 4, 20000);
	ContextRegister.register('RegisterOpen', RegisterOpen);
});

// FILE: js/repositories/RegistersRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var RegistersRepository = RepositoryFactory.factory('/RegistersRepository', 'MEMORY');
	ContextRegister.register('RegistersRepository', RegistersRepository);
});

// FILE: js/repositories/ReleaseProductRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var ReleaseProductRepository = RepositoryFactory.factory('/ReleaseProductRepository', 'MEMORY');
	ContextRegister.register('ReleaseProductRepository', ReleaseProductRepository);
});

// FILE: js/repositories/RemovePayment.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var RemovePayment = RepositoryFactory.factory('/RemovePayment', 'MEMORY', 1, 20000);
	ContextRegister.register('RemovePayment', RemovePayment);
});

// FILE: js/repositories/ReprintSaleCoupon.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var ReprintSaleCoupon = RepositoryFactory.factory('/ReprintSaleCoupon', 'MEMORY', 1, 20000);
	ContextRegister.register('ReprintSaleCoupon', ReprintSaleCoupon);
});

// FILE: js/repositories/SaleCancelRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var SaleCancelRepository = RepositoryFactory.factory('/SaleCancelRepository', 'MEMORY', 1, 60000);
	ContextRegister.register('SaleCancelRepository', SaleCancelRepository);
});

// FILE: js/repositories/SaleTypesRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var SaleTypesRepository = RepositoryFactory.factory('/SaleTypesRepository', 'MEMORY', 1, 20000);
    ContextRegister.register('SaleTypesRepository', SaleTypesRepository);
});

// FILE: js/repositories/SaveLogin.js
Configuration(function(ContextRegister, RepositoryFactory) {	
	var SaveLogin = RepositoryFactory.factory('/SaveLogin', 'LOCAL');
	ContextRegister.register('SaveLogin', SaveLogin);
});

// FILE: js/repositories/SavePayment.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var SavePayment = RepositoryFactory.factory('/SavePayment', 'MEMORY', 1, 20000);
	ContextRegister.register('SavePayment', SavePayment);
});

// FILE: js/repositories/SaveSangria.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var SaveSangria = RepositoryFactory.factory('/SaveSangria', 'MEMORY', 1, 60000);
    ContextRegister.register('SaveSangria', SaveSangria);
});


// FILE: js/repositories/SelectBlockedProducts.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var SelectBlockedProducts = RepositoryFactory.factory('/SelectBlockedProducts', 'MEMORY', 1, 20000);
    ContextRegister.register('SelectBlockedProducts', SelectBlockedProducts);
});

// FILE: js/repositories/SelectComandaProducts.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var SelectComandaProducts = RepositoryFactory.factory('/SelectComandaProducts', 'MEMORY', 1, 20000);
    ContextRegister.register('SelectComandaProducts', SelectComandaProducts);
});

// FILE: js/repositories/SelectProducts.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var SelectProducts = RepositoryFactory.factory('/SelectProducts', 'MEMORY', 1, 20000);
    ContextRegister.register('SelectProducts', SelectProducts);
});

// FILE: js/repositories/SelectVendedores.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var SelectVendedores = RepositoryFactory.factory('/SelectVendedores', 'MEMORY', 1, 20000);
    ContextRegister.register('SelectVendedores', SelectVendedores);
});

// FILE: js/repositories/SellerControl.js
Configuration(function(ContextRegister, RepositoryFactory, ZHPromise) {
	var SellerControl = RepositoryFactory.factory('/SellerControl', 'LOCAL');
	ContextRegister.register('SellerControl', SellerControl);
});

// FILE: js/repositories/SendEmailTransaction.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var SendEmailTransaction = RepositoryFactory.factory('/SendEmailTransaction', 'MEMORY');
	ContextRegister.register('SendEmailTransaction', SendEmailTransaction);
});

// FILE: js/repositories/SessionRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var SessionRepository = RepositoryFactory.factory('/SessionRepository', 'INDEXEDDB');
	ContextRegister.register('SessionRepository', SessionRepository);
});

// FILE: js/repositories/SetDiscountFidelity.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var SetDiscountFidelity = RepositoryFactory.factory('/SetDiscountFidelity', 'MEMORY', 1, 20000);
	ContextRegister.register('SetDiscountFidelity', SetDiscountFidelity);
});

// FILE: js/repositories/SetTableRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var SetTableRepository = RepositoryFactory.factory('/SetTableRepository', 'MEMORY');
	ContextRegister.register('SetTableRepository', SetTableRepository);
});

// FILE: js/repositories/SmartPromoGroups.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var SmartPromoGroups = RepositoryFactory.factory('/SmartPromoGroups', 'MEMORY', 1, 20000);
	ContextRegister.register('SmartPromoGroups', SmartPromoGroups);
});

// FILE: js/repositories/SmartPromoProds.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var SmartPromoProds = RepositoryFactory.factory('/SmartPromoProds', 'MEMORY', 1, 20000);
	ContextRegister.register('SmartPromoProds', SmartPromoProds);
});

// FILE: js/repositories/SmartPromoRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var SmartPromoRepository = RepositoryFactory.factory('/SmartPromoRepository', 'MEMORY', 1, 60000);
    ContextRegister.register('SmartPromoRepository', SmartPromoRepository);
});

// FILE: js/repositories/SmartPromoTray.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var SmartPromoTray = RepositoryFactory.factory('/SmartPromoTray', 'MEMORY', 1, 20000);
	ContextRegister.register('SmartPromoTray', SmartPromoTray);
});

// FILE: js/repositories/SplitProductsRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var SplitProductsRepository = RepositoryFactory.factory('/SplitProductsRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('SplitProductsRepository', SplitProductsRepository);
});

// FILE: js/repositories/SSLConnectionId.js
Configuration(function(ContextRegister, RepositoryFactory) {	
	var SSLConnectionId = RepositoryFactory.factory('/SSLConnectionId', 'LOCAL');
	ContextRegister.register('SSLConnectionId', SSLConnectionId);
});

// FILE: js/repositories/StateRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var StateRepository = RepositoryFactory.factory('/StateRepository', 'MEMORY', 1, 20000);
    ContextRegister.register('StateRepository', StateRepository);
});

// FILE: js/repositories/SubPromoGroups.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var SubPromoGroups = RepositoryFactory.factory('/SubPromoGroups', 'MEMORY', 1, 20000);
    ContextRegister.register('SubPromoGroups', SubPromoGroups);
});

// FILE: js/repositories/SubPromoProds.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var SubPromoProds = RepositoryFactory.factory('/SubPromoProds', 'MEMORY', 1, 20000);
    ContextRegister.register('SubPromoProds', SubPromoProds);
});

// FILE: js/repositories/SubPromoTray.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var SubPromoTray = RepositoryFactory.factory('/SubPromoTray', 'MEMORY', 1, 20000);
    ContextRegister.register('SubPromoTray', SubPromoTray);
});

// FILE: js/repositories/TableActiveTable.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var TableActiveTable = RepositoryFactory.factory('/TableActiveTable', 'MEMORY', 1, 20000);
	ContextRegister.register('TableActiveTable', TableActiveTable);
});

// FILE: js/repositories/TableCancelOpen.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var TableCancelOpen = RepositoryFactory.factory('/TableCancelOpen', 'MEMORY', 1, 20000);
	ContextRegister.register('TableCancelOpen', TableCancelOpen);
});

// FILE: js/repositories/TableChangeStatus.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var TableChangeStatus = RepositoryFactory.factory('/TableChangeStatus', 'MEMORY', 1, 20000);
	ContextRegister.register('TableChangeStatus', TableChangeStatus);
});

// FILE: js/repositories/TableCloseAccount.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var TableCloseAccount = RepositoryFactory.factory('/TableCloseAccount', 'MEMORY', 4, 20000);
	ContextRegister.register('TableCloseAccount', TableCloseAccount);
});

// FILE: js/repositories/TableGetMessageHistory.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var TableGetMessageHistory = RepositoryFactory.factory('/TableGetMessageHistory', 'MEMORY', 1, 20000);
	ContextRegister.register('TableGetMessageHistory', TableGetMessageHistory);
});

// FILE: js/repositories/TableGetPositions.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var TableGetPositions = RepositoryFactory.factory('/TableGetPositions', 'MEMORY', 1, 20000);
	ContextRegister.register('TableGetPositions', TableGetPositions);
});

// FILE: js/repositories/TableGetUpdatedTables.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var TableGetUpdatedTables = RepositoryFactory.factory('/TableGetUpdatedTables', 'MEMORY', 1, 20000);
	ContextRegister.register('TableGetUpdatedTables', TableGetUpdatedTables);
});

// FILE: js/repositories/TableGroup.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var TableGroup = RepositoryFactory.factory('/TableGroup', 'MEMORY', 1, 20000);
	ContextRegister.register('TableGroup', TableGroup);
});

// FILE: js/repositories/TableOpen.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var TableOpen = RepositoryFactory.factory('/TableOpen', 'MEMORY', 1, 20000);
	ContextRegister.register('TableOpen', TableOpen);
});

// FILE: js/repositories/TablePrepareOpening.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var TablePrepareOpening = RepositoryFactory.factory('/TablePrepareOpening', 'MEMORY', 1, 20000);
	ContextRegister.register('TablePrepareOpening', TablePrepareOpening);
});

// FILE: js/repositories/TableReopen.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var TableReopen = RepositoryFactory.factory('/TableReopen', 'MEMORY', 1, 20000);
	ContextRegister.register('TableReopen', TableReopen);
});

// FILE: js/repositories/TableRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var TableRepository = RepositoryFactory.factory('/TableRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('TableRepository', TableRepository);
});

// FILE: js/repositories/TableSelectedTable.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var TableSelectedTable = RepositoryFactory.factory('/TableSelectedTable', 'MEMORY', 1, 20000);
	ContextRegister.register('TableSelectedTable', TableSelectedTable);
});

// FILE: js/repositories/TableSendMessage.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var TableSendMessage = RepositoryFactory.factory('/TableSendMessage', 'MEMORY', 1, 20000);
	ContextRegister.register('TableSendMessage', TableSendMessage);
});

// FILE: js/repositories/TableSetPositions.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var TableSetPositions = RepositoryFactory.factory('/TableSetPositions', 'MEMORY', 1, 20000);
	ContextRegister.register('TableSetPositions', TableSetPositions);
});

// FILE: js/repositories/TableSplit.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var TableSplit = RepositoryFactory.factory('/TableSplit', 'MEMORY', 1, 20000);
	ContextRegister.register('TableSplit', TableSplit);
});

// FILE: js/repositories/TableTransferItem.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var TableTransferItem = RepositoryFactory.factory('/TableTransferItem', 'MEMORY', 4, 5000);
	ContextRegister.register('TableTransferItem', TableTransferItem);
});

// FILE: js/repositories/TableTransferTable.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var TableTransferTable = RepositoryFactory.factory('/TableTransferTable', 'MEMORY', 4, 5000);
	ContextRegister.register('TableTransferTable', TableTransferTable);
});

// FILE: js/repositories/TimestampRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var TimestampRepository = RepositoryFactory.factory('/TimestampRepository', 'MEMORY');
	ContextRegister.register('TimestampRepository', TimestampRepository);
});

// FILE: js/repositories/TipoRecebimento.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var TipoRecebimento = RepositoryFactory.factory('/TipoRecebimento', 'MEMORY', 1, 20000);
    ContextRegister.register('TipoRecebimento', TipoRecebimento);
});

// FILE: js/repositories/TipoSangria.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var TipoSangria = RepositoryFactory.factory('/TipoSangria', 'MEMORY', 1, 20000);
    ContextRegister.register('TipoSangria', TipoSangria);
});

// FILE: js/repositories/TotalCartRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var TotalCartRepository = RepositoryFactory.factory('/TotalCartRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('TotalCartRepository', TotalCartRepository);
});

// FILE: js/repositories/TransactionsMoveTransactions.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var TransactionsMoveTransactions = RepositoryFactory.factory('/TransactionsMoveTransactions', 'MEMORY');
	ContextRegister.register('TransactionsMoveTransactions', TransactionsMoveTransactions);
});

// FILE: js/repositories/TransactionsRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var TransactionsRepository = RepositoryFactory.factory('/TransactionsRepository', 'MEMORY');
	ContextRegister.register('TransactionsRepository', TransactionsRepository);
});

// FILE: js/repositories/TransferCreditRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var TransferCreditRepository = RepositoryFactory.factory('/TransferCreditRepository', 'MEMORY', 1, 60000);
    ContextRegister.register('TransferCreditRepository', TransferCreditRepository);
});

// FILE: js/repositories/TransferPositionRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var TransferPositionRepository = RepositoryFactory.factory('/TransferPositionRepository', 'MEMORY', 1, 30000);
    ContextRegister.register('TransferPositionRepository', TransferPositionRepository);
});

// FILE: js/repositories/TrocaModoCaixa.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var TrocaModoCaixa = RepositoryFactory.factory('/TrocaModoCaixa', 'MEMORY', 1, 30000);
	ContextRegister.register('TrocaModoCaixa', TrocaModoCaixa);
});

// FILE: js/repositories/UltimasVendasDesc.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var UltimasVendasDesc = RepositoryFactory.factory('/UltimasVendasDesc', 'MEMORY', 1, 20000);
    ContextRegister.register('UltimasVendasDesc', UltimasVendasDesc);
});

// FILE: js/repositories/UltimosPagamentosDesc.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var UltimosPagamentosDesc = RepositoryFactory.factory('/UltimosPagamentosDesc', 'MEMORY', 1, 20000);
    ContextRegister.register('UltimosPagamentosDesc', UltimosPagamentosDesc);
});

// FILE: js/repositories/UnblockProducts.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var UnblockProducts = RepositoryFactory.factory('/UnblockProducts', 'MEMORY', 1, 20000);
    ContextRegister.register('UnblockProducts', UnblockProducts);
});

// FILE: js/repositories/UpdateCanceledTransaction.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var UpdateCanceledTransaction = RepositoryFactory.factory('/UpdateCanceledTransaction', 'MEMORY');
	ContextRegister.register('UpdateCanceledTransaction', UpdateCanceledTransaction);
});

// FILE: js/repositories/UpdateComandaProducts.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var UpdateComandaProducts = RepositoryFactory.factory('/UpdateComandaProducts', 'MEMORY', 1, 20000);
    ContextRegister.register('UpdateComandaProducts', UpdateComandaProducts);
});

// FILE: js/repositories/UpdateItemsPrices.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var UpdateItemsPrices = RepositoryFactory.factory('/UpdateItemsPrices', 'MEMORY', 1, 20000);
	ContextRegister.register('UpdateItemsPrices', UpdateItemsPrices);
});

// FILE: js/repositories/UpdateServiceTax.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var UpdateServiceTax = RepositoryFactory.factory('/UpdateServiceTax', 'MEMORY', 1, 20000);
    ContextRegister.register('UpdateServiceTax', UpdateServiceTax);
});

// FILE: js/repositories/UpdateTransactionEmail.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var UpdateTransactionEmail = RepositoryFactory.factory('/UpdateTransactionEmail', 'MEMORY');
	ContextRegister.register('UpdateTransactionEmail', UpdateTransactionEmail);
});

// FILE: js/repositories/UtilitiesRequestsRepository.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var UtilitiesRequestsRepository = RepositoryFactory.factory('/UtilitiesRequestsRepository', 'MEMORY');
	ContextRegister.register('UtilitiesRequestsRepository', UtilitiesRequestsRepository);
});

// FILE: js/repositories/UtilitiesTest.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var UtilitiesTest = RepositoryFactory.factory('/UtilitiesTest', 'MEMORY');
	ContextRegister.register('UtilitiesTest', UtilitiesTest);
});

// FILE: js/repositories/ValidateConsumerPass.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var ValidateConsumerPass = RepositoryFactory.factory('/ValidateConsumerPass', 'MEMORY', 1, 30000);
    ContextRegister.register('ValidateConsumerPass', ValidateConsumerPass);
});

// FILE: js/repositories/ValidatePassword.js
Configuration(function(ContextRegister, RepositoryFactory) {
    var ValidatePassword = RepositoryFactory.factory('/ValidatePassword', 'MEMORY', 1, 20000);
    ContextRegister.register('ValidatePassword', ValidatePassword);
});

// FILE: js/repositories/VendedoresLogin.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var VendedoresLogin = RepositoryFactory.factory('/VendedoresLogin', 'ONLINE', 1, 20000);
	ContextRegister.register('VendedoresLogin', VendedoresLogin);
});


// FILE: js/repositories/VerificaProdutosBloqueados.js
Configuration(function(ContextRegister, RepositoryFactory) {
	var VerificaProdutosBloqueados = RepositoryFactory.factory('/VerificaProdutosBloqueados', 'MEMORY', 1, 20000);
	ContextRegister.register('VerificaProdutosBloqueados', VerificaProdutosBloqueados);
});

// FILE: js/services/WindowService.js
function WindowService(ScreenService) {
	const WINDOWS = {
		// smart promo screens
		PROMO_SCREEN: "smartPromo",
		SUBPROMO_SCREEN: "subPromo",
		CHECK_PROMO_SCREEN: "checkPromo",

		// account screens
		PAYMENT_SCREEN: "accountPaymentNamed",
		PAYMENT_TYPES_SCREEN: "paymentMenu",
		CLOSE_ACCOUNT_SCREEN: "closeAccount",
		SPLIT_PRODUCTS_SCREEN: "splitProducts",
		DELAYED_PRODUCTS_SCREEN: "delayedProducts",
		CANCEL_PRODUCT_SCREEN: "cancelProductComanda",
		CANCEL_PRODUCT_SCREEN2: "cancelProductMesa",
		TRANSFERS_SCREEN: "transfers",
		TRANSACTIONS_SCREEN: "transactions",
		ACCOUNT_DETAILS_SCREEN: "accountDetails",
		CHECK_ORDER_SCREEN: "checkOrderPos",
		CHECK_ORDER_SCREEN2: "checkOrderGrp",
		SEND_MESSAGE_SCREEN: "sendMessage",
		MENU_SCREEN: "menu",

		// tables screens
		TABLES_SCREEN: "tables",
		GROUP_TABLE_SCREEN: "groupTable",
		LOGIN_SCREEN: "login",
		LOGIN_FILIAL_SCREEN: "loginAuth",

		// bill screens
		BILLS_SCREEN: "bills",
		PARENT_DASHBOARD_SCREEN: "parentDashboard",
		ACCOUNT_DETAILS_SCREEN_BILL: "accountDetailsBill",
		BILL_LOGIN_SCREEN: "billLogin",

		// order screens
		ORDER_CHECK_ORDER_SCREEN: "orderCheckOrder",
		ORDER_TEMPORARY_SCREEN: "orderTemp",
		ORDER_LOGIN_SCREEN: "orderLogin",
		ORDER_ACCESS_SCREEN: "orderAccess",
		ORDER_MENU_SCREEN: "orderMenu",
		NEW_CONSUMER_SCREEN: "newConsumer",

		// register screens
		OPEN_REGISTER_SCREEN: "openRegister",
		CLOSE_REGISTER_SCREEN: "closeRegister",

		// delivery screens
		DELIVERY_ORDERS_SCREEN: "delivery",
		DELIVERY_ORDER_DETAIL_SCREEN: "orderDelivery"
	};

	this.openWindow = function(newWindow) {
		return ScreenService.openWindow(WINDOWS[newWindow]);
	}.bind(this);
}

//Modos Waiter
var modosWaiter = {
	mesa: { codigo: "M", nome: "Mesa" },
	comanda: { codigo: "C", nome: "Comanda" },
	order: { codigo: "O", nome: "Order" },
	balcao: { codigo: "B", nome: "Balcão" }
};

var modosCaixa = {
	POS: { nome: "Venda balcão", modos: ["B"] },
	CMD: { nome: "Comanda + venda balcão", modos: ["C", "B"] },
	RES: { nome: "Restaurante + venda balcão", modos: ["M", "B"] },
	FOS: { nome: "Venda balcão + comanda + restaurante", modos: ["M", "B", "C"] },
	PKC: { nome: "Pocket comanda", modos: ["C"] },
	PKR: { nome: "Pocket restaurante", modos: ["M"] },
	TAA: { nome: "Terminal de auto atendimento", modos: [] },
	FKB: { nome: "Microterminal", modos: [] },
	EVB: { nome: "Delivery", modos: ["D"] },
	CTL: { nome: "Controle de Produção", modos: [] },
	CSE: { nome: "Controde de Saída e Entrada de Pedidos", modos: [] }
};

Configuration(function(ContextRegister) {
	ContextRegister.register("WindowService", WindowService);
});


// FILE: js/integrations/IntegrationCappta.js
function IntegrationCappta(){

    var self = this;
    var PAYMENT_CONFIRMATION = false;
    var REMOVE_ALL_INTEGRATIONS = false;
    var MESSAGE_INTEGRATION_FAIL = 'Não foi possível chamar a integração. Sua instância não existe.';    

    var PAYMENTSTATUS_COMPLETED = '0';
    var PAYMENTSTATUS_REFUNDED  = '0';

    var AUTHKEY_PRODUCTION = '0360DAC1E8FC41A3ABF9329866A7AA16';
    var AUTHKEY_HOMOLOGATION = '795180024C04479982560F61B3C2C06E';

    this.integrationPayment = function(operatorData, currentRow){
        if(!!window.ZhCapptaAutomation) {
            var paymentType = currentRow.tiporece.IDTIPORECE === '1' ? 'credit' : 'debit';
            var paymentValue = currentRow.VRMOVIVEND.toFixed(2).replace(',', '').replace('.', '');
            // define se chava utilizada será de produção ou homologação
            var AUTHKEY = self.getAUTHKEY(operatorData.AMBIENTEPRODUCAO);
            currentRow.eletronicTransacion.data.AUTHKEY = AUTHKEY;

            ZhCapptaAutomation.payment(AUTHKEY, paymentType, paymentValue, '1');            
        } else {
            window.returnIntegration(self.invalidIntegrationInstance());
        }
    };

    this.getAUTHKEY = function(AMBIENTEPRODUCAO){
        return AMBIENTEPRODUCAO ? AUTHKEY_PRODUCTION : AUTHKEY_HOMOLOGATION;
    };
    
    this.integrationPaymentResult = function(resolve, javaResult){
        javaResult = JSON.parse(javaResult);
        var integrationResult = self.formatResponse();
        var CDNSUHOSTTEF;

        if (javaResult.responseCode === PAYMENTSTATUS_COMPLETED){
            /* CDNSUHOSTTEF = !!javaResult.acquirerUniqueSequentialNumber ? 
                javaResult.acquirerUniqueSequentialNumber : javaResult.uniqueSequentialNumber;
            em hambiente de homologação, acquirerUniqueSequentialNumber sempre retornou '0' */
            CDNSUHOSTTEF = javaResult.uniqueSequentialNumber;
            integrationResult.error = false;
            integrationResult.data = {
                CDNSUHOSTTEF: CDNSUHOSTTEF,
                NRCONTROLTEF: javaResult.administrativeCode,
                CDBANCARTCR: javaResult.cardBrandId,
                STLPRIVIA: javaResult.customerReceipt,
                STLSEGVIA: javaResult.merchantReceipt,
                PAYMENTCONFIRMATION: PAYMENT_CONFIRMATION,
                REMOVEALLINTEGRATIONS: REMOVE_ALL_INTEGRATIONS
            };
        } else {
            integrationResult.message = javaResult.reason;
        }

        resolve(integrationResult);
    };

    // o cancelamento da cappta é o próprio estorno 
    this.cancelIntegration = function(tiporeceData){
        self.reversalIntegration(tiporeceData);     
    };

    this.cancelIntegrationResult = function(resolve, javaResult){
        self.reversalIntegrationResult(resolve, javaResult);
    };

    this.reversalIntegration = function(tiporeceData){
        if(!!window.ZhCapptaAutomation) {
            ZhCapptaAutomation.sendPaymentReversal(tiporeceData.AUTHKEY, tiporeceData.NRCONTROLTEF);
        } else {
            window.returnIntegration(self.invalidIntegrationInstance());
        }
    };

    this.reversalIntegrationResult = function(resolve, javaResult){
        javaResult = JSON.parse(javaResult);
        var integrationResult = self.formatResponse();

        if (javaResult.responseCode === PAYMENTSTATUS_REFUNDED){
            integrationResult.error = false;
            integrationResult.data = {
                STLPRIVIA: javaResult.customerReceipt,
                STLSEGVIA: javaResult.merchantReceipt
            };
        } else {
            integrationResult.message = javaResult.reason;
        }

        resolve(integrationResult);
    };

    // cappta não completa integração
    this.completeIntegration = function(){
        return true;
    };

    this.completeIntegrationResult = function(resolve, javaResult){
        return true;
    };    

    this.formatResponse = null;

    this.invalidIntegrationInstance = function(){
        return JSON.stringify({
            responseCode: '1',
            reason: MESSAGE_INTEGRATION_FAIL
        });
    };
    
}

Configuration(function(ContextRegister) {
    ContextRegister.register('IntegrationCappta', IntegrationCappta);
});

// FILE: js/integrations/IntegrationCielo.js
function IntegrationCielo(PaymentRepository){

    var self = this;
    var PAYMENT_CONFIRMATION = false;
    var REMOVE_ALL_INTEGRATIONS = false;
    var MESSAGE_INTEGRATION_FAIL = 'Não foi possível chamar a integração. Sua instância não existe.';    

    var PAYMENTSTATUS_COMPLETED = '1';
    var PAYMENTSTATUS_REFUNDED  = '1';

    this.integrationPayment = function(operatorData, currentRow){
        if(!!window.ZhCieloAutomation) {
            PaymentRepository.findOne().then(function(payment){
                var newOrder = {
                    create: false,
                    accountValue: 0
                };
                // cria nova Ordem caso não existir pagamento por integração na venda
                if(_.isEmpty(_.find(payment.TIPORECE, Array('TRANSACTION.status', true)))){
                    newOrder.create = true;
                    newOrder.accountValue = (payment.DATASALE.TOTALVENDA) * 100;
                }
                newOrder = JSON.stringify(newOrder);
                
                var paymentValue = (currentRow.VRMOVIVEND.toFixed(2)) * 100;
                
                ZhCieloAutomation.payment(newOrder, paymentValue, currentRow.tiporece.IDTIPORECE);
            });
        } else {
            window.returnIntegration(self.invalidIntegrationInstance());
        }
    };
    
    this.integrationPaymentResult = function(resolve, javaResult){
        var integrationResult = self.formatResponde();
        javaResult = self.handleJavaResult(javaResult);

        if (javaResult.statusCode === PAYMENTSTATUS_COMPLETED){
            integrationResult.error = false;
            integrationResult.data = {
                CDNSUHOSTTEF: javaResult.cieloCode,
                NRCONTROLTEF: javaResult.orderId,
                CDBANCARTCR: javaResult.brand,
                PAYMENTCONFIRMATION: PAYMENT_CONFIRMATION,
                REMOVEALLINTEGRATIONS: REMOVE_ALL_INTEGRATIONS
            };
        } else {
            integrationResult.message = javaResult.message;
        }

        resolve(integrationResult);
    };

    // o cancelamento da cielo é o próprio estorno 
    this.cancelIntegration = function(tiporeceData){
        self.reversalIntegration(tiporeceData);     
    };

    this.cancelIntegrationResult = function(resolve, javaResult){
        self.reversalIntegrationResult(resolve, javaResult);
    };

    this.reversalIntegration = function(tiporeceData){
        if(!!window.ZhCieloAutomation) {
            ZhCieloAutomation.reversalPayment(tiporeceData.CDNSUHOSTTEF, tiporeceData.NRCONTROLTEF);
        } else {
            window.returnIntegration(self.invalidIntegrationInstance());
        }
    };

    this.reversalIntegrationResult = function(resolve, javaResult){
        var integrationResult = self.formatResponde();
        javaResult = self.handleJavaResult(javaResult);

        if (javaResult.statusCode === PAYMENTSTATUS_REFUNDED){
            integrationResult.error = false;
        } else {
            integrationResult.message = javaResult.message;
        }

        resolve(integrationResult);
    };

    // cielo não completa integração
    this.completeIntegration = function(){
        return true;
    };

    this.completeIntegrationResult = function(resolve, javaResult){
        return true;
    };    

    this.formatResponde = function(){
        return {
            error: true,
            message: '',
            data: {}
        };
    };

    this.invalidIntegrationInstance = function(){
        return {
            statusCode: '2',
            message: MESSAGE_INTEGRATION_FAIL
        };
    };

    this.handleJavaResult = function(javaResult){
        if(typeof(javaResult) === 'string')
            javaResult = JSON.parse(javaResult);

        return javaResult;
    };
}

Configuration(function(ContextRegister) {
    ContextRegister.register('IntegrationCielo', IntegrationCielo);
});

// FILE: js/integrations/IntegrationGetnet.js
function IntegrationGetnet(){
	var self = this;
	var PAYMENT_CONFIRMATION = false;
    var REMOVE_ALL_INTEGRATIONS = false;

    var MESSAGE_INTEGRATION_FAIL = 'Não foi possível chamar a integração. Sua instância não existe.';
    var MESSAGE_NULL_RESPONSE = 'Não foi possível obter o retorno da integração.';

	this.integrationPayment = function(operatorData, currentRow) {

		if(!!window.cordova.plugins.IntegrationService) {
			var params = self.getPaymentFromCurrentRow(currentRow);
			window.cordova.plugins.IntegrationService.payment(params, window.returnIntegration, null);
		} else {
            window.returnIntegration(self.invalidIntegrationInstance());
        }
	};

	this.integrationPaymentResult = function(resolve, javaResult) {

		var integrationResult = self.formatResponse();

        console.log("JavaResult:");
        console.log(javaResult);

		if(javaResult !== null) {
			if (!javaResult.error){
				integrationResult.error = false;
				javaResult = javaResult.data;
				var NRCARTBANCO = javaResult.binCard + javaResult.lastNumbersCard;
                var transactionDate = javaResult.date;
                transactionDate = transactionDate.slice(6, 8) + transactionDate.slice(4, 6) + transactionDate.substring(0, 4);
				integrationResult.data = {
					CDBANCARTCR: javaResult.cardBrandName ? javaResult.cardBrandName : '',
					CDNSUHOSTTEF: javaResult.nsu,
					tiporece: javaResult.OperationType,
					VRMOVIVEND: javaResult.Value,
                    STLPRIVIA : "",
                    STLSEGVIA : "",
                    TRANSACTIONDATE : transactionDate,
                    NRCONTROLTEF: javaResult.CV,
                    IDTIPORECE: javaResult.OperationType,
                    NRCARTBANCO : NRCARTBANCO,
					PAYMENTCONFIRMATION : PAYMENT_CONFIRMATION,
					REMOVEALLINTEGRATIONS : REMOVE_ALL_INTEGRATIONS
				};
			} else {
				integrationResult.message = javaResult.message;
			}
		} else {
			integrationResult.message = MESSAGE_NULL_RESPONSE;
		}
		console.log("resolving integration");
		console.log(integrationResult);
		resolve(integrationResult);
	};

	// rede não completa integração
    this.completeIntegration = function(){
        return true;
    };

    this.completeIntegrationResult = function(resolve, javaResult){
        return true;
    };    

    // o cancelamento da rede é o próprio estorno 
    this.cancelIntegration = function(tiporeceData){
        self.reversalIntegration(tiporeceData);     
    };

    this.cancelIntegrationResult = function(resolve, javaResult){
        self.reversalIntegrationResult(resolve, javaResult);
    };

    this.reversalIntegration = function(tiporeceData){
        console.log("Flamengooo");
        console.log(tiporeceData);
      	if(!!window.cordova.plugins.IntegrationService) {
			var params = self.getRefundFromSaleCancelResult(tiporeceData);
			window.cordova.plugins.IntegrationService.refund(params, window.returnIntegration,null);
		} else {
			window.returnIntegration(self.invalidIntegrationInstance());
		}
	};

  	this.reversalIntegrationResult = function(resolve, javaResult){
		self.integrationPaymentResult(resolve, javaResult);
	};

	this.formatResponse = function(){
		return{
			error:true,
			message:'',
			data:{}
		};
	};

	this.invalidIntegrationInstance = function(){
		return {
            error: true,
            message: MESSAGE_INTEGRATION_FAIL
        };
	};

	this.getPaymentFromCurrentRow = function(currentRow){
		return JSON.stringify(
		{"paymentType": currentRow.tiporece.IDTIPORECE,
		 "paymentValue": currentRow.VRMOVIVEND,
		 "paymentNSU" : "123"
		});
	};

	this.getRefundFromSaleCancelResult = function(tiporeceData){
	    console.log(tiporeceData.TRANSACTIONDATE);
		return JSON.stringify(
		{"refundType": tiporeceData.IDTIPORECE,
		 "refundValue": tiporeceData.VRMOVIVEND,
		 "refundDate" : tiporeceData.TRANSACTIONDATE,
		 "refundCV" : tiporeceData.NRCONTROLTEF
		});
	};

	this.handleJavaResult = function(javaResult){
		if(typeof(javaResult) === 'string'){
			javaResult = JSON.parse(javaResult);
			return javaResult;
		}
	};

}

Configuration(function(ContextRegister) {
	ContextRegister.register('IntegrationGetnet', IntegrationGetnet);
});

// FILE: js/integrations/IntegrationNTK.js
function IntegrationNTK(){
	this.formatResponse = null;

}

Configuration(function(ContextRegister) {
	ContextRegister.register('IntegrationNTK', IntegrationNTK);
});

// FILE: js/integrations/IntegrationRede.js
function IntegrationRede(){
	var self = this;
	var PAYMENT_CONFIRMATION = false;
    var REMOVE_ALL_INTEGRATIONS = false;

    var MESSAGE_INTEGRATION_FAIL = 'Não foi possível chamar a integração. Sua instância não existe.';
    var MESSAGE_NULL_RESPONSE = 'Não foi possível obter o retorno da integração.';

	this.integrationPayment = function(operatorData, currentRow) {
		if(!!window.cordova && !!cordova.plugins.GertecRede) {
			var params = {
				'paymentValue': currentRow.VRMOVIVEND * 100,
				'paymentType': currentRow.tiporece.IDTIPORECE
			};

			cordova.plugins.GertecRede.payment(JSON.stringify(params), window.returnIntegration, function(){});
		} else {
            window.returnIntegration(self.invalidIntegrationInstance());
        }
	};

	this.integrationPaymentResult = function(resolve, javaResult) {
		var integrationResult = self.formatResponse();

		if(javaResult !== null) {
			if (!javaResult.error){
				integrationResult.error = false;
				javaResult = javaResult.data;
				integrationResult.data = {
					CDBANCARTCR: javaResult.flag,
					CDNSUHOSTTEF: javaResult.nsu,
					NRCONTROLTEF: javaResult.AUTO,
					PAYMENTCONFIRMATION : PAYMENT_CONFIRMATION,
					REMOVEALLINTEGRATIONS : REMOVE_ALL_INTEGRATIONS
				};
			} else {
				integrationResult.message = javaResult.message;
			}
		} else {
			integrationResult.message = MESSAGE_NULL_RESPONSE;
		}

		resolve(integrationResult);
	};

	// rede não completa integração
    this.completeIntegration = function(){
        return true;
    };

    this.completeIntegrationResult = function(resolve, javaResult){
        return true;
    };    

    // o cancelamento da rede é o próprio estorno 
    this.cancelIntegration = function(tiporeceData){
        self.reversalIntegration(tiporeceData);     
    };

    this.cancelIntegrationResult = function(resolve, javaResult){
        self.reversalIntegrationResult(resolve, javaResult);
    };

    this.reversalIntegration = function(tiporeceData){
      	if(!!window.cordova && !!cordova.plugins.GertecRede) {
			var params = {};
			cordova.plugins.GertecRede.reversal(JSON.stringify(params), window.returnIntegration, function(){});
		} else {
			window.returnIntegration(self.invalidIntegrationInstance());
		}
	};

  	this.reversalIntegrationResult = function(resolve, javaResult){
		self.integrationPaymentResult(resolve, javaResult);
	};

	this.formatResponse = null;

	this.invalidIntegrationInstance = function(){
		return {
            error: true,
            message: MESSAGE_INTEGRATION_FAIL
        };
	};
}

Configuration(function(ContextRegister) {
	ContextRegister.register('IntegrationRede', IntegrationRede);
});

// FILE: js/integrations/IntegrationSiTEF.js
function IntegrationSiTEF(FiliaisLogin, PaymentRepository, Query, HomologacaoSitef, SSLConnectionId, OperatorRepository, ScreenService, WindowService, templateManager){

	var self = this;
	var PAYMENT_CONFIRMATION = false;
    var REMOVE_ALL_INTEGRATIONS = false;

    var MESSAGE_INTEGRATION_FAIL = 'Não foi possível chamar a integração. Sua instância não existe.';

    var PAYMENT_STATUS_COMPLETED = 0;
    var PAYMENT_STATUS_REFUNDED  = 0;

    var SITEF_YES = "0";
    var SITEF_NO  = "1";

	var PAYMENT_INVOICE = "";

	var PAYMENT_TYPE = {
		pagamento: {
			debito: 2,
			credito: 3,
			mercadoPago: 122
		},
		estorno: {
			mercadoPago: 123,
			credito: 210,
			debito: 211
		},
		geral: {
			testeComunicacao: 111,
			reimpressaoEspecifica: 113,
			reimpressaoUltimo: 114,
			enviaLogs: 121,
			carregaTabelas: 772
		}
	};
	this.paymentTypeConstants = function(){
		return PAYMENT_TYPE;
	};

	this.integrationPayment = function(operatorData, currentRow, resolve) {
		if(!!window.cordova) {
			self.mustCreatePaymentInvoice().then(function (createPaymentInvoice) {
				if(createPaymentInvoice) {
					self.createPaymentInvoice(operatorData, currentRow);
				} else {
					self.callPayment(operatorData, currentRow);
				}
			}.bind(this));
		} else {
            window.returnIntegration(self.invalidIntegrationInstance());
        }
	};

	this.integrationPaymentResult = function(resolve, javaResult, isRefund) {
		var integrationResult = self.formatResponse();
		ScreenService.closePopup();
		javaResult = self.handleJavaResult(javaResult);
		if (!javaResult.error){
			javaResult = javaResult.data;
			var transactionDate = javaResult.transactionDate;
			transactionDate = transactionDate.slice(6, 8) + transactionDate.slice(4, 6) + transactionDate.substring(0, 4);
			var NRCARTBANCO = javaResult.binCard + javaResult.lastNumbersCard;
			integrationResult.error = false;
			integrationResult.data = {
				CDBANCARTCR: javaResult.cardBrandName,
				STLPRIVIA: javaResult.customerReceipt,
				STLSEGVIA: javaResult.merchantReceipt,
				CDNSUHOSTTEF: javaResult.uniqueSequentialNumber,
				TRANSACTIONDATE: transactionDate,
				NRCONTROLTEF: javaResult.PAYMENTINVOICE,
				IDTIPORECE: javaResult.IDTIPORECE,
				NRCARTBANCO: NRCARTBANCO,
				VRMOVIVEND: javaResult.VRMOVIVEND,
				PAYMENTCONFIRMATION : PAYMENT_CONFIRMATION,
				REMOVEALLINTEGRATIONS : REMOVE_ALL_INTEGRATIONS
			};

			resolve(integrationResult);
		} else {
			integrationResult.message = javaResult.message;
			integrationResult.data.IDTPTEF = '5';
			integrationResult.data.errorCode = javaResult.errorCode;
			resolve(integrationResult);
		}
	};

	this.handleJavaResult = function(javaResult){
        if(typeof(javaResult) === 'string')
            javaResult = JSON.parse(javaResult);

        return javaResult;
    };

	// o cancelamento da sitef é o próprio estorno
    this.cancelIntegration = function(tiporeceData){
        self.reversalIntegration(tiporeceData);
    };

    this.cancelIntegrationResult = function(resolve, javaResult){
        self.reversalIntegrationResult(resolve, javaResult);
    };

	// sitef não completa integração
    this.completeIntegration = function(){
        return true;
    };

    this.completeIntegrationResult = function(resolve, javaResult){
        return true;
    };

    this.reversalIntegration = function(tiporeceData){
    	if(tiporeceData.IDTIPORECE === 'H'){
    		self.callReversalIntegration(tiporeceData);
    	}else{
    		tiporeceData.NRCARTBANCO = self.formatCardNumber(tiporeceData.NRCARTBANCO);
	    	ScreenService.showMessage('O cartão <br>' + tiporeceData.NRCARTBANCO + '<br> será estornado')
	    		.then(function(){
	    			self.callReversalIntegration(tiporeceData);
	    		}.bind(this)
	    	);
    	}
	};

	this.callReversalIntegration = function(tiporeceData){
		if(!!window.cordova) {
			tiporeceData.VRMOVIVEND = parseFloat(tiporeceData.VRMOVIVEND);
			OperatorRepository.findOne().then(function(operatorData) {
				self.getParameters(operatorData, tiporeceData.NRCONTROLTEF).then(function(reversalParameters){

					var estornos = PAYMENT_TYPE.estorno;
					switch (tiporeceData.IDTIPORECE) {
						case '1': reversalParameters.paymentType = estornos.credito; break;
						case '2': reversalParameters.paymentType = estornos.debito; break;
						case 'H': reversalParameters.paymentType = estornos.mercadoPago; break;
					}

					reversalParameters.paymentValue = tiporeceData.VRMOVIVEND.toFixed(2);
					reversalParameters.paymentDate = tiporeceData.TRANSACTIONDATE;
					reversalParameters.paymentNSU = tiporeceData.CDNSUHOSTTEF;
					reversalParameters.paymentHour = tiporeceData.NRCONTROLTEF.slice(8);
					reversalParameters.paymentAuth = reversalParameters.paymentVia = "";

					self.initSitefProcess(reversalParameters);
				}.bind(this));
			}.bind(this));
		} else {
			window.returnIntegration(self.invalidIntegrationInstance());
		}
	};

  	this.reversalIntegrationResult = function(resolve, javaResult){
		self.integrationPaymentResult(resolve, javaResult, true);
	};

	this.formatResponse = null;

	this.invalidIntegrationInstance = function(){
		return JSON.stringify({
            paymentTransactionStatus: 1,
            userMessage: MESSAGE_INTEGRATION_FAIL
        });
	};

	this.mustCreatePaymentInvoice = function(){
		return PaymentRepository.findOne().then(function(payment) {
			var filtered = _.filter(payment.TIPORECE, function(tiporece){
				dataPayment = tiporece.TRANSACTION.data;
				return tiporece.TRANSACTION.data.IDTPTEF === '5';
			});

			if(filtered.length === 0) {
				return true;
			} else {
				self.PAYMENT_INVOICE = filtered[0].TRANSACTION.data.NRCONTROLTEF;
				return false;
			}
		});
	};

	this.createPaymentInvoice = function(operatorData, currentRow){
		var now = new Date();
		var year = now.getFullYear().toString();
		var month = self.leftZero(now.getMonth() + 1);
		var day = self.leftZero(now.getDate());
		var hour = self.leftZero(now.getHours());
		var minutes = self.leftZero(now.getMinutes());
		var seconds = self.leftZero(now.getSeconds());

		self.PAYMENT_INVOICE = year + month + day + hour + minutes + seconds;

		self.callPayment(operatorData, currentRow);
	};

	this.leftZero = function(leftZero){
		if(leftZero < 10)
			leftZero = "0" + leftZero;

		return leftZero;
	};

	this.callPayment = function(operatorData, currentRow) {
		currentRow.eletronicTransacion.data.DSENDIPSITEF = operatorData.DSENDIPSITEF;
		currentRow.eletronicTransacion.data.CDLOJATEF = operatorData.CDLOJATEF;
		currentRow.eletronicTransacion.data.CDTERTEF = operatorData.CDTERTEF;
		currentRow.eletronicTransacion.data.NRCARTBANCO = '';
		currentRow.eletronicTransacion.data.IDTIPORECE = '';

		self.getParameters(operatorData, self.PAYMENT_INVOICE).then(function(sitefParams){

			var pagamentos = PAYMENT_TYPE.pagamento;
			switch (currentRow.tiporece.IDTIPORECE) {
				case '1': sitefParams.paymentType = pagamentos.credito; break;
				case '2': sitefParams.paymentType = pagamentos.debito; break;
				case 'H': sitefParams.paymentType = pagamentos.mercadoPago; break;
			}

			sitefParams.paymentValue = currentRow.VRMOVIVEND.toFixed(2);
			sitefParams.paymentDate = self.PAYMENT_INVOICE.slice(0, 8);
			sitefParams.paymentHour = self.PAYMENT_INVOICE.slice(8, 14);
			sitefParams.paymentNSU = sitefParams.paymentAuth = sitefParams.paymentVia = "";

			self.initSitefProcess(sitefParams);
		}.bind(this));

		// Utilizado para homologação
		// HomologacaoSitef.download(Query.build()).then(function(dataTEF){
		// 	ZhSitefAutomation.payment(type, currentRow.VRMOVIVEND.toFixed(2), DSENDIPSITEF, CDLOJATEF, CDTERTEF, operatorData.NMOPERADOR, operatorData.NRINSJURFILI, self.PAYMENT_INVOICE, dataTEF[0]);
		// }.bind(this));
	};

	this.setReversal = function(){
		PaymentRepository.findOne().then(function(payment){
			payment.TIPORECE.forEach(function(tiporece){
				if(tiporece.TRANSACTION.data.IDTPTEF === '5') {
					tiporece.TRANSACTION.data.PAYMENTCONFIRMATION = tiporece.TRANSACTION.data.REMOVEALLINTEGRATIONS = false;
				}
			});

			PaymentRepository.save(payment);
		});
	};

	this.getParameters = function(operatorData, PAYMENTINVOICE){
		return SSLConnectionId.findOne().then(function(sSLConnectionIdResponse) {
			var params = {
				'paymentIp': operatorData.DSENDIPSITEF,
				'paymentTerminal': operatorData.CDTERTEF,
				'paymentStore': operatorData.CDLOJATEF,
				'paymentOperator': operatorData.NMOPERADOR,
				'paymentInvoice': PAYMENTINVOICE,
				'storeCnpj': operatorData.NRINSJURFILI,
				'IDUTLSSL': operatorData.IDUTLSSL,
				'IDCODSSL': ''
			};

			if(sSLConnectionIdResponse){
				params.IDCODSSL = sSLConnectionIdResponse.IDCODSSL;
			}

			return params;
		}.bind(this));
	};

	this.formatCardNumber = function(cardNumber){
		return cardNumber.slice(0, 4) + ' ' + cardNumber.slice(4, 6) +
			'** **** ' + cardNumber.slice(6, 10);
	};

	this.initSitefProcess = function(params){
		ScreenService.hideLoader();
		var sitefWidget = templateManager.containers.login.getWidget("sitefPayment");
		sitefWidget.getField("userInput").isVisible = false;
		sitefWidget.getAction("btnBack").isVisible = false;
		sitefWidget.getAction("btnConfirm").isVisible = false;

        ScreenService.openPopup(sitefWidget).then(function() {
            OperatorRepository.findOne().then(function (operatorData){
                window.setMessage = self.setMessage;
                window.setLabel = self.setLabel;
                window.promptBoolean = self.promptBoolean;
                window.promptCommand = self.promptCommand;
                window.hideUserInterfaces = self.hideUserInterfaces;
                window.showCancelButton = self.showCancelButton;

                if (params.paymentType == 122){
                    if (operatorData.CDURLQRCODE){
                        var qrcode = new QRCode($(".sitef-field")[0], {
                            width : 150,
                            height : 150
                        });
                        qrcode.makeCode(operatorData.CDURLQRCODE);
                    }
                }

                cordova.plugins.GertecSitef.payment(JSON.stringify(params), window.returnIntegration, null);
            }.bind(this));
        }.bind(this));
	};

	this.continueSitefProcess = function(buffer){
		self.hideUserInterfaces();
		cordova.plugins.GertecSitef.continue(buffer, window.returnIntegration, null);
	};

	this.abortSitefProcess = function(){
		cordova.plugins.GertecSitef.abort(window.returnIntegration);
	};

	this.setMessage = function(message) {
 		templateManager.containers.login.getWidget("sitefPayment").getField("userInterface").value(_.toUpper(message));
	};

	this.setLabel = function(label) {
		templateManager.containers.login.getWidget("sitefPayment").label = label;
	};

	this.promptBoolean = function(message) {
		ScreenService.confirmMessage(message, 'question',
			function(){
				self.continueSitefProcess(SITEF_YES);
			},
			function(){
				self.continueSitefProcess(SITEF_NO);
			}
		);
	};

	this.promptCommand = function(message, minLength, maxLength, tipoCampo) {
		self.setMessage(message);
		var widget = templateManager.containers.login.getWidget("sitefPayment");
		widget.currentRow.tipoCampo = tipoCampo;
		widget.getAction("btnConfirm").isVisible = true;
		widget.getAction("btnBack").isVisible = true;

		var field = widget.getField("userInput");
		field.isVisible = true;
		field.minlength = minLength;
		field.maxlength = maxLength;
		field.setValue("");
	};

	this.hideUserInterfaces = function() {
		var widget = templateManager.containers.login.getWidget("sitefPayment");
		widget.getAction("btnBack").isVisible = widget.getAction("btnConfirm").isVisible =
			widget.getField("userInput").isVisible = false;
	};

	this.showCancelButton = function() {
		templateManager.containers.login.getWidget("sitefPayment").getAction("btnBack").isVisible = true;
	};
}

Configuration(function(ContextRegister) {
	ContextRegister.register('IntegrationSiTEF', IntegrationSiTEF);
});

// FILE: js/integrations/IntegrationStone.js
function IntegrationStone(templateManager,ScreenService,OperatorRepository){

    var self = this;
    var PAYMENT_CONFIRMATION = false;
    var REMOVE_ALL_INTEGRATIONS = false;
    var MESSAGE_INTEGRATION_FAIL = 'Não foi possível chamar a integração. Sua instância não existe.';

    this.integrationPayment = function(operatorData, currentRow){

        if(!!window.cordova.plugins) {
            var params = self.getPaymentFromCurrentRow(currentRow);
            window.cordova.plugins.IntegrationService.payment(params,window.returnIntegration,null);
        } else {
            window.returnIntegration(self.invalidIntegrationInstance());
        }
    };

//    this.setMessage = function(message) {
//        templateManager.containers.login.getWidget("pagsegpayment").getField("userInterfacePag").value(_.toUpper(message));
//    };
//
//    this.setLabel = function(label) {
//    	templateManager.containers.login.getWidget("pagsegpayment").label = label;
//    };
//
//    this.promptBoolean = function(message) {
//    	ScreenService.confirmMessage(message, 'question',
//    		function(){
//    			self.continueSitefProcess(SITEF_YES);
//    		},
//    		function(){
//    			self.continueSitefProcess(SITEF_NO);
//    		}
//    	);
//    };
//
//    this.promptCommand = function(message, minLength, maxLength, tipoCampo) {
//    	self.setMessage(message);
//        self.hideUserInterfaces();
//    	var widget = templateManager.containers.login.getWidget("pagsegpayment");
//    	widget.currentRow.tipoCampo = tipoCampo;
//    	widget.getAction("btnConfirm").isVisible = true;
//    	widget.getAction("btnBack").isVisible = true;
//
//    	var field = widget.getField("userInput");
//   		field.isVisible = true;
//   		field.minlength = minLength;
//   		field.maxlength = maxLength;
//   		field.setValue("");
//
//   		self.hideUserInterfaces();
//   	};
//
//    this.hideUserInterfaces = function() {
//    	var widget = templateManager.containers.login.getWidget("pagsegpayment");
//    	widget.getAction("btnBack").isVisible = widget.getAction("btnConfirm").isVisible =
//    		widget.getField("userInput").isVisible = false;
//    };
//
//   this.showCancelButton = function() {
//    	templateManager.containers.login.getWidget("pagsegpayment").getAction("btnBack").isVisible = true;
//   };

    this.integrationPaymentResult = function(resolve, javaResult){
         console.log("JAVARES");
         console.log(javaResult);
         //Realiza validação dos dados,e envia a resposta de volta com o resolve
         var integrationResult = {};
         if(javaResult !== null) {
         	if (!javaResult.error){
            	integrationResult.error = false;
            	javaResult = javaResult.data;
            	integrationResult.data = {
            		CDBANCARTCR: javaResult.flag,
            		CDNSUHOSTTEF: javaResult.nsu,
            		NRCONTROLTEF: javaResult.AUTO,
            		PAYMENTCONFIRMATION : PAYMENT_CONFIRMATION,
            		REMOVEALLINTEGRATIONS : REMOVE_ALL_INTEGRATIONS
            	};
            } else {
                integrationResult.error = javaResult.error;
            	integrationResult.message = javaResult.message;
            }
         } else {
         	integrationResult.message = MESSAGE_NULL_RESPONSE;
         }
         resolve(integrationResult);
    };

    this.cancelIntegration = function(tiporeceData){
        self.reversalIntegration(tiporeceData);
    };

    this.cancelIntegrationResult = function(resolve, javaResult){
        self.reversalIntegrationResult(resolve, javaResult);
    };

    this.reversalIntegration = function(tiporeceData){
        //Verificamos a existência do plugin no momento
        if(!!window.cordova.plugins) {
        	//Definimos os parametros como sendo os dados necessários para realizar o reembolso em forma de string JSON
        	var params = self.getRefundFromSaleCancelResult(tiporeceData);
            //Chama-se a função da integração(KT) com os parametros,a função que é pra onde o código seguirá caso sucesso,e null
            window.cordova.plugins.IntegrationService.refund(params, window.returnIntegration, null);
        } else {
        	window.returnIntegration(self.invalidIntegrationInstance());
        }

    };

    this.reversalIntegrationResult = function(resolve, javaResult){
        var integrationResult = self.formatResponse();
        javaResult = self.handleJavaResult(javaResult);
        console.log("java ESTORNO");
        console.log(javaResult);

        if (javaResult.errorCode == 0){
            integrationResult.error = false;
        } else {
            integrationResult.message = javaResult.message;
        }

        resolve(integrationResult);
    };

    this.completeIntegration = function(){
        return true;
    };

    this.completeIntegrationResult = function(resolve, javaResult){
        return true;
    };

    this.formatResponse = function(){
        return {
            error: true,
            message: '',
            data: {}
        };
    };

    this.invalidIntegrationInstance = function(){
        return {
            statusCode: '2',
            message: MESSAGE_INTEGRATION_FAIL
        };
    };

    this.handleJavaResult = function(javaResult){
        if(typeof(javaResult) === 'string')
            javaResult = JSON.parse(javaResult);

        return javaResult;
    };

    this.getPaymentFromCurrentRow = function (currentRow){
       return JSON.stringify(
            {"paymentType": currentRow.tiporece.IDTIPORECE,
             "paymentValue": currentRow.VRMOVIVEND,
             "paymentNSU": "123"
            }
       );
    };

     this.getRefundFromSaleCancelResult = function (integrations){
        return JSON.stringify(
         	{"refundType" : integrations.IDTIPORECE,
           	 "refundValue": integrations.VRMOVIVEND,
        	 "refundDate" : integrations.TRANSACTIONDATE,
        	 "refundCV"   : integrations.NRCONTROLTEF,
        	 "TRANSACTIONCODE":integrations.TRANSACTIONCODE,
             "TRANSACTIONID":integrations.TRANSACTIONID
        	 }
        );
     };



}

Configuration(function(ContextRegister) {
    ContextRegister.register('IntegrationStone', IntegrationStone);
});



/*var NRCARTBANCO = integrationResponse.data.binCard + integrationResponse.data.lastNumbersCard;
    						var transactionDate = integrationResponse.data.date;
    						transactionDate = transactionDate.slice(6, 8) + transactionDate.slice(4, 6) + transactionDate.substring(0, 4);
    						integrationResponse.data.eletronicTransacion = currentRow.eletronicTransacion;
    						integrationResponse.data.eletronicTransacion.data.CDBANCARTCR = integrationResponse.data.cardBrandName? integrationResponse.data.cardBrandName: '';
    						integrationResponse.data.eletronicTransacion.data.STLPRIVIA = '';
    						integrationResponse.data.eletronicTransacion.data.STLSEGVIA = '';
    						integrationResponse.data.eletronicTransacion.data.CDNSUHOSTTEF = integrationResponse.data.uniqueSequentialNumber;
    						integrationResponse.data.eletronicTransacion.data.TRANSACTIONDATE = transactionDate;
    						integrationResponse.data.eletronicTransacion.data.NRCONTROLTEF= integrationResponse.data.CV;
    						//alterar nome dos campos nas outras integrações
    						integrationResponse.data.eletronicTransacion.data.IDTIPORECE= integrationResponse.data.OperationType;
    						integrationResponse.data.eletronicTransacion.data.NRCARTBANCO = NRCARTBANCO;
    						integrationResponse.data.eletronicTransacion.data.VRMOVIVEND = currentRow.VRMOVIVEND;
    						integrationResponse.data.VRMOVIVEND = currentRow.VRMOVIVEND;
    						integrationResponse.data.tiporece = currentRow.tiporece;
    						*/

// FILE: js/printer/PrinterCieloLio.js
function PrinterCieloLio(){
	
	var self = this;

	var INVALID_PRINTER_INSTANCE = 'Não foi possível chamar a impressora. Sua instância não existe.';

	this.printText = function(text){
		if (!!window.ZhCieloAutomation){
			ZhCieloAutomation.printText(text);
		} else {
			window.returnPrintResult(self.invalidPrinterInstance());
		}
	};

	this.printQRCode = function(qrCode){
		self.printImage(qrCode, 1);
	};

	this.printBarCode = function(barCode){
		self.printImage(barCode, 2);
	};

	this.printImage = function(stringCode, type){
		// impressão de imagem utilizada pelo QRcode e Código de Barras
		if (!!window.ZhCieloAutomation){
			ZhCieloAutomation.printImage(stringCode, type);
		} else {
			window.returnPrintResult(self.invalidPrinterInstance());
		}
	};

	this.printerDelay = function(time){
		var returnObj = self.formatResponse();
		returnObj.error = false;
		window.returnPrintResult(JSON.stringify(returnObj));
	};

	this.reprintTEFVoucher = function(){
		window.returnPrintResult(self.invalidPrinterInstance());
	};

	this.printResult = function(resolve, javaResult){
		var response = self.formatResponse();
		javaResult = JSON.parse(javaResult);

		response.error = javaResult.statusPrinter !== "1";
		response.message = javaResult.message;

		resolve(response);
	};

	this.invalidPrinterInstance = function(){
		return JSON.stringify({
            'error': true,
            'message': INVALID_PRINTER_INSTANCE
        });
    };

	this.formatResponse = null;
	
}

Configuration(function(ContextRegister) {
	ContextRegister.register('PrinterCieloLio', PrinterCieloLio);
});

// FILE: js/printer/PrinterGertec.js
function PrinterGertec(ScreenService){
	
	var self = this;

	var INVALID_PRINTER_INSTANCE = 'Não foi possível chamar a impressora. Sua instância não existe.';

	this.printText = function(text){
		if (!!window.ZhGertecPrinter){
			ZhGertecPrinter.printText(text);
		} else if(!!window.cordova && !!cordova.plugins.GertecPrinter) {
			cordova.plugins.GertecPrinter.printString(text, window.returnPrintResult, function(){});
		} else {
			window.returnPrintResult(self.invalidPrinterInstance());
		}
	};

	this.printQRCode = function(qrCode){
		if (!!window.ZhGertecPrinter){
			ZhGertecPrinter.printQrCode(qrCode);
		} else if(!!window.cordova && !!cordova.plugins.GertecPrinter) {
			cordova.plugins.GertecPrinter.printQrCode(qrCode, window.returnPrintResult, function(){});
		} else {
			window.returnPrintResult(self.invalidPrinterInstance());
		}
	};

	this.printBarCode = function(barCode){
		if (!!window.ZhGertecPrinter){
			ZhGertecPrinter.printBarCode(barCode);
		} else if(!!window.cordova && !!cordova.plugins.GertecPrinter) {
			cordova.plugins.GertecPrinter.printBarCode(barCode, window.returnPrintResult, function(){});
		} else {
			window.returnPrintResult(self.invalidPrinterInstance());
		}
	};

	this.printerDelay = function(time){
		setTimeout(function(){
			var returnObj = self.formatResponse();
			returnObj.error = false;
			window.returnPrintResult(JSON.stringify(returnObj));
		}.bind(this), parseInt(time));	
	};

	this.reprintTEFVoucher = function(){
		window.returnIntegration = _.bind(self.getReprintTextResult, this);

		if (!!window.ZhSitefAutomation){
			ZhSitefAutomation.showAdministrativeMenu();
		} else {
			window.returnIntegration(self.invalidPrinterInstance());
		}
	};

	this.getReprintTextResult = function(javaResult){
		window.returnPrintResult = _.bind(self.reprintResult, this);
		javaResult = JSON.parse(javaResult);
		var comandos = [javaResult.customerReceipt, "", ""];

		if(javaResult.paymentTransactionStatus === 0) {
			comandos.forEach(function(text) {
				self.printText(text);
			}.bind(this));
		} else {
			ScreenService.showMessage("Erro ao tentar encontrar o último comprovante TEF");
		}
	};

	this.reprintResult = function (javaResult) {
		javaResult = JSON.parse(javaResult);

		if(javaResult.paymentTransactionStatus === 1) {
			ScreenService.showMessage("Erro ao imprimir o último comprovante TEF");
		}
	};

	this.printResult = function(resolve, javaResult){
		if (!!window.ZhGertecPrinter)
			javaResult = JSON.parse(javaResult);

		resolve(javaResult);
	};

	this.invalidPrinterInstance = function(){
		return JSON.stringify({
            'error': true,
            'message': INVALID_PRINTER_INSTANCE
        });
    };

	this.formatResponse = null;
	
}

Configuration(function(ContextRegister) {
	ContextRegister.register('PrinterGertec', PrinterGertec);
});

// FILE: js/printer/PrinterGetnet.js
function PrinterGetnet(){

    var self = this;

    var INVALID_PRINTER_INSTANCE = 'Não foi possível chamar a impressora. Sua instância não existe.';

    this.printText = function(text){
        if (!!window.cordova.plugins.IntegrationService){
        	var params = JSON.stringify({"texto":text, "flag":"printText"});
        	console.log("PRINT STRINGG  "+text);
            //Chamada da função de impressão da integração
            //window.returnPrintResult contém a função printResult desse mesmo arquivo
            window.cordova.plugins.IntegrationService.print(params, window.returnPrintResult,null);

        } else {
        	window.returnPrintResult(self.invalidPrinterInstance());
        }
    };

    this.printQRCode = function(qrCode){
        if (!!window.cordova.plugins.IntegrationService){
            var params = JSON.stringify({"qrcode":qrCode,"flag":"qrCode"});
            console.log("CODE STRINGG   "+qrCode);
            window.cordova.plugins.IntegrationService.print(params, window.returnPrintResult,null);
        }else{
            window.returnPrintResult(self.invalidPrinterInstance());
        }
    };

    this.printBarCode = function(barCode){
        if (!!window.cordova.plugins.IntegrationService){
            var params = JSON.stringify({"barcode":barCode, "flag":"barCode"});
            console.log("barr");
            window.cordova.plugins.IntegrationService.print(params, window.returnPrintResult,null);
        }else{
            window.returnPrintResult(self.invalidPrinterInstance());
        }
    };

    this.reprintTEFVoucher = function(){

        window.returnPrintResult(self.invalidPrinterInstance());
    };


    this.printResult = function(resolve, javaResult){
        javaResult = self.codeToString(javaResult);
        resolve(javaResult);
    };

    this.printerDelay = function(){
        setTimeout(function(){
        	var returnObj = self.formatResponse();
        	returnObj.error = false;
        	window.returnPrintResult(JSON.stringify(returnObj));
        }.bind(this),5000);
    };

    this.invalidPrinterInstance = function(){
        return JSON.stringify({
            'error': true,
            'message': INVALID_PRINTER_INSTANCE
        });
    };

    this.formatResponse = null;

    this.codeToString = function(javaResult){
        switch(javaResult.message){
            case 0: javaResult.message = "OK"; break;
            case 1: javaResult.message = "Imprimindo"; break;
            case 2: javaResult.message = "Impressora não iniciada"; break;
            case 3: javaResult.message = "Impressora superaquecida"; break;
            case 4: javaResult.message = "Fila de impressão muito grande"; break;
            case 5: javaResult.message = "Parametros incorretos"; break;
            case 10: javaResult.message = "Porta da impressora aberta"; break;
            case 11: javaResult.message = "Temperatura baixa demais para impressão"; break;
            case 12: javaResult.message = "Sem bateria suficiente para impressão"; break;
            case 13: javaResult.message = "Motor de passo com problemas"; break;
            case 15: javaResult.message = "Sem bonina"; break;
            case 16: javaResult.message = "Bobina acabando"; break;
            case 17: javaResult.message = "Bobina travada"; break;
            case 1000:
            case null: javaResult.message = "Não foi possível definir o erro"; break;
        }
        return javaResult;
    };

}


Configuration(function(ContextRegister) {
	ContextRegister.register('PrinterGetnet', PrinterGetnet);
});

// FILE: js/printer/PrinterPoynt.js
function PrinterPoynt(){
	
	var self = this;

	var INVALID_PRINTER_INSTANCE = 'Não foi possível chamar a impressora. Sua instância não existe.';

	this.printText = function(text){
		if (!!window.RedePoyntPrinterJSInterface){
			// tratamento específico para Poynt
			text = text.split("\\n").join('\n');

			RedePoyntPrinterJSInterface.printText(text);
		} else {
			window.returnPrintResult(self.invalidPrinterInstance());
		}
	};

	this.printQRCode = function(qrCode){
		if (!!window.RedePoyntPrinterJSInterface){
			RedePoyntPrinterJSInterface.printQRCode(qrCode);
		} else {
			window.returnPrintResult(self.invalidPrinterInstance());
		}
	};

	this.printBarCode = function(barCode){
		var response = self.formatResponse();

		response.error = false;
		window.returnPrintResult(JSON.stringify(response));
	};

	this.reprintTEFVoucher = function(){
		return new Promise(function(resolve){
			window.returnPrintResult = _.bind(self.printResult, this, resolve);

			if (!!window.RedePoyntJSInterface){
				RedePoyntJSInterface.onReprint();
			} else {
				window.returnPrintResult(self.invalidPrinterInstance());
			}
		}.bind(this));
	};

	this.printResult = function(resolve, javaResult){
		setTimeout(function(){
			resolve(JSON.parse(javaResult));
		}.bind(this), 1000);
	};

	this.printerDelay = function(time){
		var returnObj = self.formatResponse();
		returnObj.error = false;
		window.returnPrintResult(JSON.stringify(returnObj));
	};

	this.invalidPrinterInstance = function(){
		return JSON.stringify({
            'error': true,
            'message': INVALID_PRINTER_INSTANCE
        });
    };

	this.formatResponse = null;
	
}

Configuration(function(ContextRegister) {
	ContextRegister.register('PrinterPoynt', PrinterPoynt);
});

// FILE: js/printer/PrinterStone.js
function PrinterStone(){

    var self = this;

    var INVALID_PRINTER_INSTANCE = 'Não foi possível chamar a impressora. Sua instância não existe.';

    this.printText = function(text){

        if (!!window.cordova.plugins.IntegrationService){

        	var params = JSON.stringify({"texto":text,
        	                              "flag":"printText"});
            //Chamada da função de impressão da integração
            //window.returnPrintResult contém a função printResult desse mesmo arquivo
            window.cordova.plugins.IntegrationService.print(params, window.returnPrintResult,null);

        } else {
        	window.returnPrintResult(self.invalidPrinterInstance());
        }
    };

    this.printQRCode = function(qrCode){

        var params = JSON.stringify({"qrcode":qrCode,
                                     "flag":"qrCode"});
        window.cordova.plugins.IntegrationService.print(params, window.returnPrintResult,null);

    };

    this.printBarCode = function(barCode){

        var params = JSON.stringify({"barcode":barCode,
                                     "flag":"barCode"});
        window.cordova.plugins.IntegrationService.print(params, window.returnPrintResult,null);

    };

    this.reprintTEFVoucher = function(){

        //window.returnPrintResult(self.invalidPrinterInstance());
    };


    this.printResult = function(resolve, javaResult){
        javaResult = self.codeToString(javaResult);
        resolve(javaResult);
    };

    this.printerDelay = function(){
        setTimeout(function(){
        	var returnObj = self.formatResponse();
        	returnObj.error = false;
        	window.returnPrintResult(JSON.stringify(returnObj));
        }.bind(this),5000);
    };

    this.invalidPrinterInstance = function(){
        return JSON.stringify({
            'error': true,
            'message': INVALID_PRINTER_INSTANCE
        });
    };

    this.formatResponse = null;

    this.codeToString = function(javaResult){
        switch(javaResult.message){
            case 0: javaResult.message = "OK"; break;
            case 1: javaResult.message = "Imprimindo"; break;
            case 2: javaResult.message = "Impressora não iniciada"; break;
            case 3: javaResult.message = "Impressora superaquecida"; break;
            case 4: javaResult.message = "Fila de impressão muito grande"; break;
            case 5: javaResult.message = "Parametros incorretos"; break;
            case 10: javaResult.message = "Porta da impressora aberta"; break;
            case 11: javaResult.message = "Temperatura baixa demais para impressão"; break;
            case 12: javaResult.message = "Sem bateria suficiente para impressão"; break;
            case 13: javaResult.message = "Motor de passo com problemas"; break;
            case 15: javaResult.message = "Sem bonina"; break;
            case 16: javaResult.message = "Bobina acabando"; break;
            case 17: javaResult.message = "Bobina travada"; break;
            case 1000:
            case null: javaResult.message = "Não foi possível definir o erro"; break;
        }
        return javaResult;
    };

}


Configuration(function(ContextRegister) {
	ContextRegister.register('PrinterStone', PrinterStone);
});

// FILE: js/services/PrinterService.js
function PrinterService(OperatorRepository, PrinterPoynt, PrinterCieloLio, PrinterGertec,PrinterGetnet,WindowService) {

	var self = this;

	var PrinterClass = null;
	var PRINTER_TYPE = {
		'25': PrinterGertec,
		'26': PrinterPoynt,
		'27': PrinterCieloLio,
		'28': PrinterGetnet
	};

	var COMMANDS_NOT_FOUND = 'Comandos de impressora não foram adicionados.';
	var NO_PARAMETERIZED_PRINTER = 'Impressora não parametrizada ou inválida para o tipo de Caixa.';
	var PRINT_ERROR = 'Falha na impressão. ';

	this.TEXT_COMMAND = 'printText';
	this.QRCODE_COMMAND = 'printQRCode';
	this.BARCODE_COMMAND = 'printBarCode';
	this.DELAY_COMMAND = 'printerDelay';

	this.printerCommands = Array();

	this.printerCommand = function(type, parameter){
		if (_.isString(parameter) && !_.isEmpty(parameter)){
			self.printerCommands.push({
				'type': type,
				'parameter': parameter
			});
		}
	};

	this.printerSpaceCommand = function(){
		self.printerCommands.push({
			'type': self.TEXT_COMMAND,
			'parameter': '\n         ' +
						 '\n         '
		});
	};

	this.callRecursivePrintCommands = null;

	this.printerInit = function() {
		return OperatorRepository.findOne().then(function(operatorData) {
			if (!_.isEmpty(self.printerCommands)){
				PrinterClass = self.choosePrinter(operatorData.IDMODEIMPRES);
				console.log(PrinterClass);
				if (PrinterClass){
					return new Promise(function(resolve){
						self.callRecursivePrintCommands = _.bind(self.printCommands, self, resolve, PrinterClass);
						self.callRecursivePrintCommands();
					}.bind(this));
				} else {
					return self.invalidPrinterParam();
				}				
			} else {
				return self.invalidPrinterCommands();
			}
		}.bind(this)).then(self.endPrint);
	};

	this.choosePrinter = function(IDMODEIMPRES){
		// seleciona serviço de impressão
		return _.get(PRINTER_TYPE, IDMODEIMPRES);
	};

	window.returnPrintResult = null;

	this.printCommands = function(impressionResolved, PrinterClass){
		// função recursiva utilizada para chamar as funções de impressão
		var currentPrinterCommand = self.printerCommands.shift();		
		new Promise(function(resolve){
		    console.log("currentprintercommand  =   "+currentPrinterCommand);
			window.returnPrintResult = _.bind(PrinterClass.printResult, PrinterClass, resolve);
			PrinterClass[currentPrinterCommand.type](currentPrinterCommand.parameter);
		}.bind(this)).then(function(resolved){
			if (!resolved.error) {
				if (!_.isEmpty(self.printerCommands)){
					// realiza impressão do próximo comando
					self.callRecursivePrintCommands();
				} else {
					// impressão realizada com sucesso
					impressionResolved(resolved);		
				}
			} else {
				// erro ao realizar impressão
				resolved.message = PRINT_ERROR + resolved.message;
				impressionResolved(resolved);

			}
		}.bind(this));
	};

	this.invalidPrinterCommands = function(){
		var result = self.formatResponse();

		result.message = COMMANDS_NOT_FOUND;
		return result;
	};

	this.invalidPrinterParam = function(){
		var result = self.formatResponse();

		result.message = NO_PARAMETERIZED_PRINTER;
		return result;
	};

    this.endPrint = function(result){
		// para qualquer resultado da impressão, se reseta os comandos de impressão e retorna seu resultado
		self.printerCommands = Array();

		return result;
	};

	this.formatResponse = function(){
        return {
            'error': true,
            'message': ''
        };
    };

    PrinterPoynt.formatResponse = self.formatResponse;
    PrinterCieloLio.formatResponse = self.formatResponse;
    PrinterGertec.formatResponse = self.formatResponse;
    PrinterGetnet.formatResponse = self.formatResponse;
}

Configuration(function(ContextRegister) {
	ContextRegister.register('PrinterService', PrinterService);
});

// FILE: js/services/AccountService.js
function AccountService(Query, AccountOrder, AccountCancelProduct, AccountGetAccountDetails, AccountGetAccountItems,
                        AccountGetAccountItemsWithoutCombo, AccountGetAccountItemsForTransfer, FidelityDetailsRepository,
                        AccountPaymentBegin, AccountPaymentFinish, AccountGetTrasanctions, AccountPaymentTypedCredit,
                        AccountGetTableTrasanctions, AccountGetOriginalAccountItems, CheckRefilRepository, GetCampanhaRepo,
                        AccountChangeClientConsumer, SaleCancelRepository, ChangeProductDiscount, CancelCreditRepository,
                        ConsumerSearchRepository, ParamsMenuRepository, VerificaProdutosBloqueados, ParamsCardsRepository,
                        TransferCreditRepository, ConsumerBalanceRepository, FilterProducts, TransferPositionRepository,
                        ValidatePassword, SelectComandaProducts, UpdateComandaProducts, SetDiscountFidelity, ProdutosDesistencia,
                        CalculaDescontoSubgrupo, UpdateServiceTax, OperatorLogout, GetPayments){

    this.order = function (chave, mode, cartPool, nrvendarest, pedidos, orderCode, vendedorAut, saleProdPass) {
        var pedido = JSON.stringify(pedidos);

        var query = Query.build()
                        .where('chave').equals(chave)
                        .where('mode').equals(mode)
                        .where('multiplasComandas').equals(!_.isEmpty(cartPool))
                        .where('nrvendarest').equals(nrvendarest)
                        .where('pedidos').equals(pedido)
                        .where('orderCode').equals(orderCode)
                        .where('vendedorAut').equals(vendedorAut)
                        .where('saleProdPass').equals(saleProdPass);
        return AccountOrder.download(query);
    };

    this.getAccountItems = function (chave, modo, nrcomanda, nrvendarest, posicao) {

        var query = Query.build()
                        .where('chave').equals(chave)
                        .where('modo').equals(modo)
                        .where('nrcomanda').equals(nrcomanda)
                        .where('nrvendarest').equals(nrvendarest)
                        .where('posicao').equals(posicao);
        return AccountGetAccountItems.download(query);
    };

    this.getAccountItemsWithoutCombo = function (chave, modo, nrcomanda, nrvendarest, posicao) {

        var query = Query.build()
                        .where('chave').equals(chave)
                        .where('modo').equals(modo)
                        .where('nrcomanda').equals(nrcomanda)
                        .where('nrvendarest').equals(nrvendarest)
                        .where('posicao').equals(posicao);
        return AccountGetAccountItemsWithoutCombo.download(query);
    };

    this.getAccountOriginalItems = function (chave, modo, nrcomanda, nrvendarest, posicao) {

        var query = Query.build()
                        .where('chave').equals(chave)
                        .where('modo').equals(modo)
                        .where('nrcomanda').equals(nrcomanda)
                        .where('nrvendarest').equals(nrvendarest)
                        .where('posicao').equals(posicao);
        return AccountGetOriginalAccountItems.download(query);
    };

    this.getAccountDetails = function (chave, modo, nrcomanda, nrvendarest, funcao, posicao) {

        var query = Query.build()
                        .where('chave').equals(chave)
                        .where('modo').equals(modo)
                        .where('nrcomanda').equals(nrcomanda)
                        .where('nrvendarest').equals(nrvendarest)
                        .where('funcao').equals(funcao)
                        .where('posicao').equals(posicao);

        return AccountGetAccountDetails.download(query);
    };

    this.cancelProduct = function (chave, modo, nrcomanda, nrvendarest, produto, motivo, cdsupervisor, IDPRODPRODUZ) {
        var productData = JSON.stringify(produto);

        var query = Query.build()
                        .where('chave').equals(chave)
                        .where('modo').equals(modo)
                        .where('nrcomanda').equals(nrcomanda)
                        .where('nrvendarest').equals(nrvendarest)
                        .where('produto').equals(productData)
                        .where('motivo').equals(motivo)
                        .where('supervisor').equals(cdsupervisor)
                        .where('IDPRODPRODUZ').equals(IDPRODPRODUZ);
        return AccountCancelProduct.download(query);
    };

    this.beginPaymentAccount = function(dataset){
        var query = Query.build()
                        .where('chave').equals(dataset.chave)
                        .where('CDVENDEDOR').equals(dataset.CDVENDEDOR)
						.where('NRVENDAREST').equals(dataset.NRVENDAREST)
						.where('NRCOMANDA').equals(dataset.NRCOMANDA)
                        .where('NRMESA').equals(dataset.NRMESA)
                        .where('NRLUGARMESA').equals(dataset.NRLUGARMESA)
                        .where('CDTIPORECE').equals(dataset.CDTIPORECE)
                        .where('IDTIPMOV').equals(dataset.IDTIPMOV)
                        .where('VRMOV').equals(dataset.VRMOV)
                        .where('DSBANDEIRA').equals(dataset.DSBANDEIRA)
                        .where('IDTPTEF').equals(dataset.IDTPTEF);

        return AccountPaymentBegin.download(query);
    };

    this.finishPaymentAccount = function(dataset){
        var query = Query.build()
						.where('NRSEQMOVMOB').equals(dataset.NRSEQMOVMOB)
						.where('NRSEQMOB').equals(dataset.NRSEQMOB)
						.where('DSBANDEIRA').equals(dataset.DSBANDEIRA)
						.where('NRADMCODE').equals(dataset.NRADMCODE)
						.where('IDADMTASK').equals(dataset.IDADMTASK)
						.where('IDSTMOV').equals(dataset.IDSTMOV)
						.where('TXMOVUSUARIO').equals(dataset.TXMOVUSUARIO)
                        .where('TXMOVJSON').equals(dataset.TXMOVJSON)
                        .where('CDNSUTEFMOB').equals(dataset.CDNSUTEFMOB)
                        .where('TXPRIMVIATEF').equals(dataset.TXPRIMVIATEF)
						.where('TXSEGVIATEF').equals(dataset.TXSEGVIATEF)
						.where('NRVENDAREST').equals(dataset.NRVENDAREST)
						.where('NRCOMANDA').equals(dataset.NRCOMANDA)
						.where('chave').equals(dataset.chave);

        return AccountPaymentFinish.download(query);
    };

    this.typedCreditPayment = function(dataset, NRSEQMOVMOB){
        var query = Query.build()
						.where('amount').equals(dataset.amount)
						.where('idAutorizadora').equals(dataset.idAutorizadora)
						.where('dtVencimento').equals(dataset.dtVencimento)
						.where('numCartao').equals(dataset.numCartao)
						.where('codSeguranca').equals(dataset.codSeguranca)
						.where('NRSEQMOVMOB').equals(NRSEQMOVMOB);

        return AccountPaymentTypedCredit.download(query);
    };

    this.getTransactions = function(NRVENDAREST, NRLUGARMESA){
        var query = Query.build()
						.where('NRVENDAREST').equals(NRVENDAREST)
						.where('NRLUGARMESA').equals(NRLUGARMESA);

        return AccountGetTrasanctions.download(query);
    };

    this.getTableTransactions = function(NRMESA, NRLUGARMESA, NRVENDAREST, chave){
        var query = Query.build()
                        .where('NRMESA').equals(NRMESA)
                        .where('NRLUGARMESA').equals(NRLUGARMESA)
                        .where('NRVENDAREST').equals(NRVENDAREST)
                        .where('chave').equals(chave);

        return AccountGetTableTrasanctions.download(query);
    };

    this.checkRefil = function(chave, NRVENDAREST, NRCOMANDA, CDPRODUTO, position){
        var query = Query.build()
                        .where('chave').equals(chave)
                        .where('NRVENDAREST').equals(NRVENDAREST)
                        .where('NRCOMANDA').equals(NRCOMANDA)
                        .where('CDPRODUTO').equals(CDPRODUTO)
                        .where('position').equals(position);
        return CheckRefilRepository.download(query);
    };

    this.setCPF = function(chave, cpf, checkedBox, position){
      var query = Query.build()
                        .where('chave').equals(chave)
                        .where('cpf').equals(cpf)
                        .where('checkedBox').equals(checkedBox)
                        .where('position').equals(position);
        return CheckRefilRepository.download(query);
    };

    this.changeClientConsumer = function(chave, NRVENDAREST, NRCOMANDA, positions, CDCLIENTE, CDCONSUMIDOR, fidelitySearch) {
      var query = Query.build()
                        .where('chave').equals(chave)
                        .where('NRVENDAREST').equals(NRVENDAREST)
                        .where('NRCOMANDA').equals(NRCOMANDA)
                        .where('positions').equals(positions)
                        .where('CDCLIENTE').equals(CDCLIENTE)
                        .where('CDCONSUMIDOR').equals(CDCONSUMIDOR)
                        .where('fidelitySearch').equals(fidelitySearch);
        return AccountChangeClientConsumer.download(query);
    };

    this.saleCancel = function (chave, CODIGOCUPOM, CDSUPERVISOR) {
        var query = Query.build()
                        .where('chave').equals(chave)
                        .where('CODIGOCUPOM').equals(CODIGOCUPOM)
                        .where('CDSUPERVISOR').equals(CDSUPERVISOR);
        return SaleCancelRepository.download(query);
    };

    this.changeProductDiscount = function(NRVENDAREST, NRCOMANDA, VRDESCONTO, TIPODESCONTO, NRPRODCOMVEN, CDSUPERVISOR, motivoDesconto, CDGRPOCORDESC){
        var query = Query.build()
                        .where('NRVENDAREST').equals(NRVENDAREST)
                        .where('NRCOMANDA').equals(NRCOMANDA)
                        .where('VRDESCONTO').equals(VRDESCONTO)
                        .where('TIPODESCONTO').equals(TIPODESCONTO)
                        .where('NRPRODCOMVEN').equals(NRPRODCOMVEN)
                        .where('CDSUPERVISOR').equals(CDSUPERVISOR)
                        .where('motivoDesconto').equals(motivoDesconto)
                        .where('CDGRPOCORDESC').equals(CDGRPOCORDESC);
        return ChangeProductDiscount.download(query);
    };

    this.cancelPersonalCredit = function(chave, CDCLIENTE, CDCONSUMIDOR, NRDEPOSICONS, NRSEQMOVCAIXA, confirmacao){
        var query = Query.build()
                        .where('chave').equals(chave)
                        .where('CDCLIENTE').equals(CDCLIENTE)
                        .where('CDCONSUMIDOR').equals(CDCONSUMIDOR)
                        .where('NRDEPOSICONS').equals(NRDEPOSICONS)
                        .where('NRSEQMOVCAIXA').equals(NRSEQMOVCAIXA)
                        .where('confirmacao').equals(confirmacao);
        return CancelCreditRepository.download(query);
    };

    this.searchConsumer = function(chave, CDCLIENTE, qrcode){
        var query = Query.build()
                        .where('chave').equals(chave)
                        .where('CDCLIENTE').equals(CDCLIENTE)
                        .where('qrcode').equals(qrcode.replace(/[^A-Z0-9-\/. ]/gi, ""));
        return ConsumerSearchRepository.download(query);
    };

    this.updatePrices = function(chave){
        var query = Query.build()
                        .where('chave').equals(chave);
        return ParamsMenuRepository.download(query);
    };

    this.verificaProdutosBloqueados = function(products){
        var query = Query.build()
                        .where('products').equals(products);
        return VerificaProdutosBloqueados.download(query);
    };

    this.cardSearch = function(searchValue){
        var query = Query.build()
                        .where('searchValue').equals(searchValue);
        return ParamsCardsRepository.download(query);
    };

    this.transferPersonalCredit = function(chave, data){
        var query = Query.build()
                        .where('chave').equals(chave)
                        .where('CDCLIENTE').equals(data.CDCLIENTE)
                        .where('CDCONSUMIDOR').equals(data.CDCONSUMIDOR)
                        .where('CDFAMILISALD').equals(data.CDFAMILISALD)
                        .where('CDIDCONSUMID').equals(data.CDIDCONSUMID)
                        .where('VRSALDCONEXT').equals(data.VRSALDCONEXT)
                        .where('destCDCLIENTE').equals(data.destCDCLIENTE)
                        .where('destCDCONSUMIDOR').equals(data.destCDCONSUMIDOR)
                        .where('destCDFAMILISALD').equals(data.destCDFAMILISALD)
                        .where('destCDIDCONSUMID').equals(data.destCDIDCONSUMID)
                        .where('destVRSALDCONEXT').equals(data.destVRSALDCONEXT);
        return TransferCreditRepository.download(query);
    };

    this.getConsumerBalance = function(chave, CDCLIENTE, CDCONSUMIDOR){
        var query = Query.build()
                        .where('chave').equals(chave)
                        .where('CDCLIENTE').equals(CDCLIENTE)
                        .where('CDCONSUMIDOR').equals(CDCONSUMIDOR);
        return ConsumerBalanceRepository.download(query);
    };

    this.filterProducts = function(pesquisa){
        var query = Query.build()
            .where('pesquisa').equals(pesquisa);
        return FilterProducts.download(query);
    };

    this.transferPositions = function(products, position, NRVENDAREST, NRCOMANDA, CDCLIENTE, CDCONSUMIDOR){
        var query = Query.build()
            .where('products').equals(products)
            .where('position').equals(position)
            .where('NRVENDAREST').equals(NRVENDAREST)
            .where('NRCOMANDA').equals(NRCOMANDA)
            .where('CDCLIENTE').equals(CDCLIENTE)
            .where('CDCONSUMIDOR').equals(CDCONSUMIDOR);
        return TransferPositionRepository.download(query);
    };

    this.getFidelityDetails = function(CDCLIENTE, CDCONSUMIDOR){
        var query = Query.build()
            .where('CDCLIENTE').equals(CDCLIENTE)
            .where('CDCONSUMIDOR').equals(CDCONSUMIDOR);
        return FidelityDetailsRepository.download(query);
    };

    this.validatePassword = function(password){
        var query = Query.build()
            .where('password').equals(password);
        return ValidatePassword.download(query);
    };

    this.selectComandaProducts = function(NRCOMANDA){
        var query = Query.build()
            .where('NRCOMANDA').equals(NRCOMANDA);
        return SelectComandaProducts.download(query);
    };

    this.updateComandaProducts = function(comandaAtual,vendaRestComandaAtual, comandaDestino, vendaRestComandaDestino, CDPRODUTO, NRPRODCOMVEN, CDSUPERVISOR){
        var query = Query.build()
            .where('comandaAtual').equals(comandaAtual)
            .where('vendaRestComandaAtual').equals(vendaRestComandaAtual)
            .where('comandaDestino').equals(comandaDestino)
            .where('vendaRestComandaDestino').equals(vendaRestComandaDestino)
            .where('CDPRODUTO').equals(CDPRODUTO)
            .where('NRPRODCOMVEN').equals(NRPRODCOMVEN)
            .where('CDSUPERVISOR').equals(CDSUPERVISOR);
        return UpdateComandaProducts.download(query);
    };

    this.setDiscountFidelity = function(NRVENDAREST, NRCOMANDA, positions, VRDESCFID){
        var query = Query.build()
            .where('NRVENDAREST').equals(NRVENDAREST)
            .where('NRCOMANDA').equals(NRCOMANDA)
            .where('positions').equals(positions)
            .where('VRDESCFID').equals(VRDESCFID);
        return SetDiscountFidelity.download(query);
    };

    this.getCampanha = function(tray){
        tray = JSON.stringify(tray);

        var query = Query.build()
            .where('tray').equals(tray);
        return GetCampanhaRepo.download(query);
    };

    this.calculaDescontoSubgrupo = function(products){
        var query = Query.build()
                        .where('products').equals(products);
        return CalculaDescontoSubgrupo.download(query);
    };

    this.produtosDesistencia = function (itensDesistencia) {
        var query = Query.build()
            .where('itensDesistencia').equals(itensDesistencia);
        return ProdutosDesistencia.download(query);
    };

    this.updateServiceTax = function(NRVENDAREST, NRCOMANDA, TOTALPRODS, VRACRESCIMO, TIPOGORJETA){
        var query = Query.build()
            .where('NRVENDAREST').equals(NRVENDAREST)
            .where('NRCOMANDA').equals(NRCOMANDA)
            .where('TOTALPRODS').equals(TOTALPRODS)
            .where('VRACRESCIMO').equals(VRACRESCIMO)
            .where('TIPOGORJETA').equals(TIPOGORJETA);
        return UpdateServiceTax.download(query);
    };

    this.logout = function(){
        return OperatorLogout.download(Query.build());
    };

    this.getPayments = function(accountData) {
        var query = Query.build()
            .where('DATA').equals(accountData);
        return GetPayments.download(query);
    };

}

Configuration(function(ContextRegister) {
	ContextRegister.register('AccountService', AccountService);
});

// FILE: js/services/AuthService.js
function AuthService(RestEngine) {

	this.logout = function(){
		var params = {
			requestType: "Row",
			row: {}
		};
		var operatorLogoffRoute = '/operator/logout';
		return RestEngine.post(operatorLogoffRoute, params).then(function(response) {
			return response.messages.shift().message;
		});
	};

}

Configuration(function(ContextRegister) {
	ContextRegister.register('AuthService', AuthService);
});

// FILE: js/services/BillService.js
function BillService(Query, BillRepository, BillOpenBill, BillValidateBill, SetTableRepository, BillCancelOpen){

	this.getBills = function (chave){
		var query = Query.build()
						.where('chave').equals(chave);
		return BillRepository.download(query);
	};

	this.openBill = function(chave, DSCOMANDA, CDCLIENTE, CDCONSUMIDOR, NRMESA, CDVENDEDOR, DSCONSUMIDOR){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('DSCOMANDA').equals(DSCOMANDA)
						.where('CDCLIENTE').equals(CDCLIENTE)
						.where('CDCONSUMIDOR').equals(CDCONSUMIDOR)
						.where('nrMesa').equals(NRMESA)
						.where('CDVENDEDOR').equals(CDVENDEDOR)
						.where('DSCONSUMIDOR').equals(DSCONSUMIDOR);
		return BillOpenBill.download(query);
	};

	this.validateBill = function(chave, dsComanda){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('dsComanda').equals(dsComanda);
		return BillValidateBill.download(query);
	};

	this.setTheTable = function(chave, NRMESA, NRVENDAREST){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('NRMESA').equals(NRMESA)
						.where('NRVENDAREST').equals(NRVENDAREST);
		return SetTableRepository.download(query);
	};

	this.cancelOpen = function(chave, nrMesa, NRVENDAREST, NRCOMANDA, NRLUGARMESA){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('nrMesa').equals(nrMesa)
						.where('NRVENDAREST').equals(NRVENDAREST)
						.where('NRCOMANDA').equals(NRCOMANDA);
		return BillCancelOpen.download(query);
	};

}

Configuration(function(ContextRegister){
	ContextRegister.register('BillService', BillService);
});

// FILE: js/services/CieloTestService.js
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

// FILE: js/services/ConsumerService.js
function ConsumerService(AddconsumerRepository, Query){

    this.addConsumer = function(consumerData){
        var dados = JSON.stringify(consumerData);
        var query = Query.build()
                        .where('dados').equals(dados);
        return AddconsumerRepository.download(query);
    };

}

Configuration(function(ContextRegister) {
    ContextRegister.register('ConsumerService', ConsumerService);
});

// FILE: js/services/DeliveryService.js
function DeliveryService(DeliveryRepository, DeliveryControlRepository, PaymentPayAccount, OperatorRepository, Query, OperatorValidateSupervisor, FiliaisLogin, ValidateConsumerPass,
						 CaixasLogin, VendedoresLogin, TrocaModoCaixa, FindTefSSLConnectionId, DeliverySendOrders, PedidosEntreguesRepository,
						 DeliveryCheckOutOrders, DeliveryPrint, Movcaixadlv, DeliveryReprintCupomFiscal, CancelDeliveryProduct, CancelDeliveryOrder,
						 ConcludeOrderDlv ) {

	
	this.getDeliveryOrders = function(params) {
		var query = Query.build()
						.where('CDFILIAL').equals(params.CDFILIAL)
						.where('CDLOJA').equals(params.CDLOJA);
		return DeliveryRepository.download(query);
	};

	this.setDataSourceControl = function(params) {
		var query = Query.build()
						.where('CDFILIAL').equals(params.CDFILIAL)
						.where('CDLOJA').equals(params.CDLOJA);

		return DeliveryControlRepository.download(query);
	};

	this.generatePayment = function(cdfilial, nrvendarest, status, saleCode, datasale, nrcomanda){
		var saleCodeObj = {
			'saleCode': saleCode
		};
		if(datasale.TROCO == undefined){
			datasale.TROCO = 0;
		}
		var dataSale = {
			'DATASALE':{
	            'TOTALVENDA': 0,
	            'FALTANTE': 0,
	            'VALORPAGO': 0,
	            'TROCO': datasale.TROCO,
	            'TOTAL': datasale.TOTAL,
	            'TOTALSUBSIDY': 0,
	            'REALSUBSIDY': 0,
	            // taxa de serviço
	            'VRTXSEVENDA': 0,
	            // desconto
	            'VRDESCONTO': 0,
	            'PCTDESCONTO': 0,
	            'TIPODESCONTO': 'P',
	            'FIDELITYDISCOUNT': 0,
	            'FIDELITYVALUE': 0,
              	'VRCOUVERT': 0
			}
		};
		var query = Query.build()
						.where('DELIVERY').equals(true)
						.where('CDFILIAL').equals(cdfilial)
						.where('NRVENDAREST').equals(nrvendarest)
						.where('saleCode').equals(saleCodeObj.saleCode)
						.where('DATASALE').equals(dataSale)
						.where('IDSTCOMANDA').equals(status)
						.where('NRCOMANDA').equals(nrcomanda);
		return PaymentPayAccount.download(query);
	};

	this.getVendedoresLogin = function(filial, vendedoresField) {
		var query = Query.build()
						.where('CDFILIAL').equals(filial);
		return VendedoresLogin.downloadSome(query, 1, vendedoresField.itemsPerPage);
	};

	this.entregarPedidos = function(pedidos, entregador){
    	return OperatorRepository.findOne().then(function (operatorData){
			var query = Query.build()
							 .where('ORDERS').equals(pedidos)
							 .where('ENTREGADOR').equals(entregador)
							 .where('CDFILIAL').equals(operatorData.CDFILIAL)
							 .where('CDCAIXA').equals(operatorData.CDCAIXA);
			return DeliverySendOrders.download(query);
		});
	};

	this.getPedidosEntregues = function(entregador){
    	return OperatorRepository.findOne().then(function (operatorData){
			var query = Query.build()
							 .where('ENTREGADOR').equals(entregador)
							 .where('CDFILIAL').equals(operatorData.CDFILIAL)
							 .where('CDLOJA').equals(operatorData.CDLOJA);
			return PedidosEntreguesRepository.download(query);
		});
	};

	this.chegadaPedidos = function(pedidos, entregador){
		return OperatorRepository.findOne().then(function (operatorData){
			var query = Query.build()
							 .where('ORDERS').equals(pedidos)
							 .where('ENTREGADOR').equals(entregador)
							 .where('CDFILIAL').equals(operatorData.CDFILIAL)
							 .where('CDLOJA').equals(operatorData.CDLOJA)
							 .where('CDCAIXA').equals(operatorData.CDCAIXA);
			return DeliveryCheckOutOrders.download(query);
		});
	};

	this.printDelivery = function(orders){
		return OperatorRepository.findOne().then(function (operatorData){
			orders = _.map(orders, function(order){
				order.CDCAIXA = operatorData.CDCAIXA;
				return order;
			});
			var query = Query.build()
							.where('ORDERS').equals(orders);

			return DeliveryPrint.download(query);
		});
	};

	this.saveMovcaixadlv = function(params){
		return OperatorRepository.findOne().then(function (operatorData){
			var query = Query.build()
							.where('CDFILIAL').equals(operatorData.CDFILIAL)
							.where('RECEBIMENTOS').equals(params.RECEBIMENTOS)
							.where('NRVENDAREST').equals(params.NRVENDAREST);
			return Movcaixadlv.download(query);
		});	
	};

	this.reprintDeliveryCupomFiscal = function(orders){
		var query = Query.build()
						.where('ORDERS').equals(orders);
		return DeliveryReprintCupomFiscal.download(query);
	};

	this.deletarProduto = function(params){
		params.product = JSON.stringify(params.product);
		var query = Query.build()
						.where('chave').equals(params.saleCode)
						.where('modo').equals(params.modo)
						.where('NRCOMANDA').equals(params.NRCOMANDA)
						.where('NRVENDAREST').equals(params.NRVENDAREST)
						.where('produto').equals(params.product)
						.where('motivo').equals(params.motivo)
						.where('CDSUPERVISOR').equals(params.CDOPERADOR)
						.where('IDPRODPRODUZ').equals(params.IDPRODPRODUZ)
						.where('CDFILIAL').equals(params.CDFILIAL);
		return CancelDeliveryProduct.download(query);
	};

	this.cancelarPedido = function(params){
		var dataSale = {
			'DATASALE':{
	            'TOTALVENDA': 0,
	            'FALTANTE': 0,
	            'VALORPAGO': 0,
	            'TROCO': 0,
	            'TOTAL': 0,
	            'TOTALSUBSIDY': 0,
	            'REALSUBSIDY': 0,
	            // taxa de serviço
	            'VRTXSEVENDA': 0,
	            // desconto
	            'VRDESCONTO': 0,
	            'PCTDESCONTO': 0,
	            'TIPODESCONTO': 'P',
	            'FIDELITYDISCOUNT': 0,
	            'FIDELITYVALUE': 0
			}
		};
		var query = Query.build()
						.where('DELIVERY').equals(true)
						.where('saleCode').equals(params.saleCode)
						.where('DATASALE').equals(dataSale)
						.where('modo').equals(params.modo)
						.where('NRCOMANDA').equals(params.NRCOMANDA)
						.where('NRVENDAREST').equals(params.NRVENDAREST)
						.where('motivo').equals(params.motivo)
						.where('CDSUPERVISOR').equals(params.CDOPERADOR)
						.where('IDPRODPRODUZ').equals(params.IDPRODPRODUZ)
						.where('CDFILIAL').equals(params.CDFILIAL)
						.where('IDSTCOMANDA').equals(params.IDSTCOMANDA);
		return CancelDeliveryOrder.download(query);
	};

	this.concludeOrderDlv = function(params){
		var dataSale = {
			'DATASALE':{
	            'TOTALVENDA': 0,
	            'FALTANTE': 0,
	            'VALORPAGO': 0,
	            'TROCO': 0,
	            'TOTAL': 0,
	            'TOTALSUBSIDY': 0,
	            'REALSUBSIDY': 0,
	            // taxa de serviço
	            'VRTXSEVENDA': 0,
	            // desconto
	            'VRDESCONTO': 0,
	            'PCTDESCONTO': 0,
	            'TIPODESCONTO': 'P',
	            'FIDELITYDISCOUNT': 0,
	            'FIDELITYVALUE': 0
			}
		};
		var query = Query.build()
						.where('DELIVERY').equals(true)
						.where('saleCode').equals(params.saleCode)
						.where('DATASALE').equals(dataSale)
						.where('modo').equals(params.modo)
						.where('NRCOMANDA').equals(params.NRCOMANDA)
						.where('NRVENDAREST').equals(params.NRVENDAREST)
						.where('motivo').equals(params.motivo)
						.where('CDSUPERVISOR').equals(params.CDOPERADOR)
						.where('IDPRODPRODUZ').equals(params.IDPRODPRODUZ)
						.where('CDFILIAL').equals(params.CDFILIAL)
						.where('IDSTCOMANDA').equals(params.IDSTCOMANDA);
		return ConcludeOrderDlv.download(query);
	};

}

Configuration(function(ContextRegister) {
	ContextRegister.register('DeliveryService', DeliveryService);
});

// FILE: js/services/GeneralFunctionsService.js
function GeneralFunctionsService(Query, ReprintSaleCoupon, BlockProducts, UnblockProducts, ImpressaoLeituraX, GetNrControlTef, SaveSangria, ExportLogs) {

	this.reprintSaleCoupon = function(reprintType, saleCode){
		var query = Query.build()
						.where('reprintType').equals(reprintType)
						.where('saleCode').equals(saleCode);
		return ReprintSaleCoupon.download(query);
	};

	this.blockProducts = function(widget){
		var query = Query.build()
			.where('CDPRODUTO').equals(widget.currentRow.selectProducts);
		return BlockProducts.download(query);
	};

	this.unblockProducts = function(widget){
		var query = Query.build()
			.where('CDPRODUTO').equals(widget.currentRow.selectBlockedProducts);
		return UnblockProducts.download(query);
	};

	this.impressaoLeituraX = function(){
		var query = Query.build();
		return ImpressaoLeituraX.download(query);
	};

	this.getNrControlTef = function(CDNSUHOSTTEF){
		var query = Query.build()
			.where('CDNSUHOSTTEF').equals(CDNSUHOSTTEF);
		return GetNrControlTef.download(query);
	};

	this.saveSangria = function(itemsSangria, imprimeSangria){
		var query = Query.build()
			.where('itemsSangria').equals(itemsSangria)
			.where('imprimeSangria').equals(imprimeSangria);
		return SaveSangria.download(query);
	};

	this.exportLogs = function(logContent, logName){
		var query = Query.build()
			.where('logContent').equals(logContent)
			.where('logName').equals(logName);
		return ExportLogs.download(query);
	};
}

Configuration(function(ContextRegister) {
	ContextRegister.register('GeneralFunctionsService', GeneralFunctionsService);
});

// FILE: js/services/IntegrationService.js
function IntegrationService(IntegrationCappta, IntegrationNTK, IntegrationRede, IntegrationSiTEF, IntegrationCielo, IntegrationGetnet,OperatorRepository) {
	var INTEGRATION_TYPE = {
		'2': IntegrationCappta,
		'3': IntegrationNTK,
		'4': IntegrationRede,
		'5': IntegrationSiTEF,
		'7': IntegrationCielo,
		'9': IntegrationGetnet
		};

	var self = this;
	var PAYMENT_CONFIRMATION = false;
    var REMOVE_ALL_INTEGRATIONS = false;
	var IntegrationClass = null;
	var VALUES_NOT_FOUND = 'Tipo do TEF não reconhecido pelo sistema.';
	var MESSAGE_INTEGRATION_FAIL = 'Não foi possível chamar a integração. Sua instância não existe.';
    var MESSAGE_NULL_RESPONSE = 'Não foi possível obter o retorno da integração.';

	this.reversalWaiting = Array();

	this.integrationPayment = function(currentRow) {
		return OperatorRepository.findOne().then(function(operatorData) {
			currentRow.eletronicTransacion.data.IDTPTEF = operatorData.IDTPTEF;

			IntegrationClass = self.chooseIntegration(operatorData.IDTPTEF);
			if (IntegrationClass){
				return new Promise(function(resolve) {
					window.returnIntegration = _.bind(IntegrationClass.integrationPaymentResult, IntegrationClass, resolve);
					IntegrationClass.integrationPayment(operatorData, currentRow);
				}).then(self.buildIntegrationResponse.bind(currentRow));				
			} else {
				return self.invalidIntegrationValues();
			}
		}.bind(this));
	};

	this.buildIntegrationResponse = function(integrationResult) {
		if (!integrationResult.error) {
			for (var i in this.eletronicTransacion.data){
				if (!!integrationResult.data[i]){
					this.eletronicTransacion.data[i] = integrationResult.data[i];
				}
			}
			this.eletronicTransacion.status = true;
			integrationResult.data = this;
		}

		return integrationResult;
	};

	this.chooseIntegration = function(IDTPTEF){
		// seleciona serviço de integração
		return INTEGRATION_TYPE[IDTPTEF];
	};

	this.cancelIntegration = function(tiporeceData){
		return new Promise(function(resolve) {
			IntegrationClass = self.chooseIntegration(tiporeceData.IDTPTEF);
            window.returnIntegration = _.bind(IntegrationClass.cancelIntegrationResult, IntegrationClass, resolve);
            IntegrationClass.cancelIntegration(tiporeceData);
		}.bind(this));
	};

	this.completeIntegration = function(integrations){
    	// pega IDTPTEF da primeira posição pois será o mesmo para qualquer recebimento
    	return new Promise(function(resolve) {
    		IntegrationClass = self.chooseIntegration(integrations[0].IDTPTEF);
    		if (IntegrationClass){
    			window.returnIntegration = _.bind(IntegrationClass.completeIntegrationResult, IntegrationClass, resolve);
    			IntegrationClass.completeIntegration();
    		} else {
    			resolve(self.invalidIntegrationValues());
    		}
    	}.bind(this));
    };

	this.callRecursive = null;

	this.reversalIntegration = function(removePaymentSale, integrations){
        // pega IDTPTEF da primeira posição pois será o mesmo para qualquer recebimento
        return new Promise(function(reversalResolve) {
        	IntegrationClass = self.chooseIntegration(integrations[0].IDTPTEF);
        	if(IntegrationClass){
        		self.reversalWaiting = _.clone(integrations);
            	self.callRecursive = _.bind(this.recursiveReversalIntegration, self, reversalResolve, Array());
            	self.callRecursive(removePaymentSale, IntegrationClass);
            } else {
            	reversalResolve(self.invalidIntegrationValues());
            }
        }.bind(this));
	};

	this.recursiveReversalIntegration = function(reversalResolve, data, removePaymentSale){
		// função recursiva utilizada para estornar todas as transações realizadas na venda
		var integrationToReverse = self.reversalWaiting.shift();
		var toRemove = {
			'CDTIPORECE': null,
			'CDNSUHOSTTEF': null,
			'DTHRINCOMVEN': null
		};
        console.log("Ativa o cyberpunk");
		new Promise(function(resolve){
		    //definimos um parametro do objeto global window como uma função que é a reversalIntegrationResult
		    //com ambiente self e argumento resolve
			window.returnIntegration = _.bind(IntegrationClass.reversalIntegrationResult, IntegrationClass, resolve);
            IntegrationClass.reversalIntegration(integrationToReverse);
		}.bind(this)).then(function(resolved){
			// armazena todos os retornos
			if (!resolved.error) {
				toRemove.CDTIPORECE = integrationToReverse.CDTIPORECE;
				toRemove.CDNSUHOSTTEF = integrationToReverse.CDNSUHOSTTEF;
				toRemove.DTHRINCOMVEN = integrationToReverse.DTHRINCOMVEN;
				resolved.data.REVERSEDNRCONTROLTEF = integrationToReverse.NRCONTROLTEF;
				resolved.data.toRemove = toRemove;	 
				data.push(resolved.data);
				resolved.data = data;		
				if (self.reversalWaiting.length > 0){
					// realiza estorno da próxima transação
					self.callRecursive(removePaymentSale);

				} else {
					// estorno realizado com sucesso
					reversalResolve(resolved);
				}					
			} else {
				// erro ao realizar estorno
				data.push(resolved.data);
				resolved.data = data;
				reversalResolve(resolved);
			}
		}.bind(this));
	};

	this.formatResponse = function(){
        return {
            error: true,
            message: '',
            data: {}
        };
    };

    this.invalidIntegrationValues = function(){
		var result = self.formatResponse();

		result.message = VALUES_NOT_FOUND;
		return result;
	};
    IntegrationCappta.formatResponse = self.formatResponse;
	IntegrationNTK.formatResponse = self.formatResponse;
	IntegrationRede.formatResponse = self.formatResponse;
	IntegrationSiTEF.formatResponse = self.formatResponse;
	IntegrationGetnet.formatResponse = self.formatResponse;

	this.integrationData = function() {
		return {
			IDTPTEF: null,
			CDNSUHOSTTEF: null,
			CDBANCARTCR: null,
			STLPRIVIA: '',
			STLSEGVIA: '',
			PAYMENTCONFIRMATION: false,
			REMOVEALLINTEGRATIONS: false,
			// Cappta
			AUTHKEY: null,
			NRCONTROLTEF: null,
			// SiTEF
			DSENDIPSITEF: '',
			CDLOJATEF: null,
			CDTERTEF: null,
			TRANSACTIONDATE: '',
			NRCARTBANCO: '',
			//PagSeguro
			TRANSACTIONCODE:'',
			TRANSACTIONID:''
		};
	};
}

Configuration(function(ContextRegister) {
	ContextRegister.register('IntegrationService', IntegrationService);
});

// FILE: js/services/OperatorService.js
function OperatorService(OperatorRepository, Query, OperatorValidateSupervisor, FiliaisLogin, ValidateConsumerPass,
						 CaixasLogin, VendedoresLogin, TrocaModoCaixa, FindTefSSLConnectionId, auth, FindPendingPayments) {

	this.login = function(filial, caixa, operador, senha, version, currentMode){

		var query = Query.build()
			.where("filial")
			.equals(filial)
			.where("caixa")
			.equals(caixa)
			.where("operador")
			.equals(operador)
			.where("senha")
			.equals(senha)
			.where("version")
			.equals(version)
			.where("currentMode")
			.equals(currentMode);
		return OperatorRepository.download(query);
	};

	this.getFiliaisLogin = function(filialField) {
		var query = Query.build();
		return FiliaisLogin.downloadSome(query, 1, filialField.itemsPerPage);
	};

	this.getCaixasLogin = function(filial, caixasField) {
		var query = Query.build()
			.where("CDFILIAL")
			.equals(filial);
		return CaixasLogin.downloadSome(query, 1, caixasField.itemsPerPage);
	};

	this.getVendedoresLogin = function(filial, vendedoresField) {
		var query = Query.build()
			.where("CDFILIAL")
			.equals(filial);
		return VendedoresLogin.downloadSome(query, 1, vendedoresField.itemsPerPage);
	};

	this.validateSupervisor = function(supervisor, senha, accessParam) {
		var query = Query.build()
			.where("supervisor")
			.equals(supervisor)
			.where("senha")
			.equals(senha)
			.where("accessParam")
			.equals(accessParam);
		return OperatorValidateSupervisor.download(query);
	};

	this.validateConsumerPass = function(CDCLIENTE, CDCONSUMIDOR, senha) {
		var query = Query.build()
			.where("CDCLIENTE")
			.equals(CDCLIENTE)
			.where("CDCONSUMIDOR")
			.equals(CDCONSUMIDOR)
			.where("senha")
			.equals(senha);
		return ValidateConsumerPass.download(query);
	};

	this.trocaModoCaixa = function(chaveSessao, currentMode) {
		var query = Query.build()
			.where("currentMode")
			.equals(currentMode)
			.where("chaveSessao")
			.equals(chaveSessao);
		return TrocaModoCaixa.download(query);
	};

	this.buscaTefSSLConnectionId = function(IDSERIALDISP) {
		var query = Query.build()
			.where("IDSERIALDISP")
			.equals(IDSERIALDISP);
		return FindTefSSLConnectionId.download(query);
	};

	this.auth = function(email, senha) {
		var query = Query.build()
			.where("email")
			.equals(email)
			.where("senha")
			.equals(senha);
		return auth.download(query);
	};
	
	this.findPendingPayments = function() {
		var query = Query.build();
		return FindPendingPayments.download(query);
	};
}

Configuration(function(ContextRegister) {
	ContextRegister.register("OperatorService", OperatorService);
});


// FILE: js/services/OrderService.js
function OrderService(Query, OrderRequestLoginRepository, OrderGetAccessRepository, OrderAllowUserAccessRepository, OrderControlUserAccessRepository, OrderLoginUserRepository, OrderReturnAccess, OrderReturnTablesRepository, OrderCallWaiterRepository, OrderGetCallRepository,OrderAnswerTableRepository, OrderCheckAccess, OrderCheckBlockedUsers, OrderBlockedIps, NewConsumerRepository, ConsumerLoginRepository){

	this.login = function(DSEMAILCONS, password){
		var query = Query.build()
						.where('DSEMAILCONS').equals(DSEMAILCONS)
						.where('password').equals(password);
		return ConsumerLoginRepository.download(query);
	};

	this.requestLogin = function(nome, mesa, frontVersion, ip){
		var query = Query.build()
						.where('nome').equals(nome)
						.where('mesa').equals(mesa)
						.where('frontVersion').equals(frontVersion)
						.where('ip').equals(ip);
		return OrderRequestLoginRepository.download(query);
	};

	this.getAccess = function(){
		var query = Query.build();
		return OrderGetAccessRepository.download(query);
	};

	this.getCall = function(){
		var query = Query.build();
		return OrderGetCallRepository.download(query);
	};

	this.returnTables = function(){
		var query = Query.build();
		return OrderReturnTablesRepository.download(query);
	};

	this.allowUserAccess = function(chave, nracessouser){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('NRACESSOUSER').equals(nracessouser);

		return OrderAllowUserAccessRepository.download(query);
	};

	this.controlUserAccess = function(nracessouser, status, chave){
		var query = Query.build()
						.where('NRACESSOUSER').equals(nracessouser)
						.where('status').equals(status)
						.where('CHAVE').equals(chave);

		return OrderControlUserAccessRepository.download(query);
	};

	this.checkBlockedUsers = function () {
		return OrderCheckBlockedUsers.download(Query.build());
	};

	this.getBlockedIps = function (chave) {
		var query = Query.build()
						.where('chave').equals(chave);
		return OrderBlockedIps.download(query);
	};

	this.loginUser = function(nracessouser, ip){
		var query = Query.build()
						.where('NRACESSOUSER').equals(nracessouser)
						.where('ip').equals(ip);

		return OrderLoginUserRepository.download(query);
	};

	 this.verificaAcesso = function(user){
			var query = Query.build()
						.where('NMUSUARIO').equals(user);

		return OrderReturnAccess.download(query);
	};

	this.callWaiter = function(nracessouser, callType){
		var query = Query.build()
						.where('nracessouser').equals(nracessouser)
						.where('tipoChamada').equals(callType);

		return OrderCallWaiterRepository.download(query);
	};

	this.answerTable = function(nracessouser){
		var query = Query.build()
						.where('nracessouser').equals(nracessouser);
		return OrderAnswerTableRepository.download(query);
	};

	this.checkAccess = function (chave, nrcomanda, nrvendarest) {
		var query = Query.build()
						.where('chave').equals(chave)
						.where('nrcomanda').equals(nrcomanda)
						.where(nrvendarest).equals(nrvendarest);
		return OrderCheckAccess.download(query);
	};

	this.newConsumer = function(NMCONSUMIDOR, DSEMAILCONS, NRCELULARCONS, CDSENHACONSMD5, CDIDCONSUMID){
		var query = Query.build()
						.where('NMCONSUMIDOR').equals(NMCONSUMIDOR)
						.where('DSEMAILCONS').equals(DSEMAILCONS)
						.where('NRCELULARCONS').equals(NRCELULARCONS)
						.where('CDSENHACONSMD5').equals(CDSENHACONSMD5)
						.where('CDIDCONSUMID').equals(CDIDCONSUMID);
		return NewConsumerRepository.download(query);
	};

}

Configuration(function(ContextRegister) {
	ContextRegister.register('OrderService', OrderService);
});

// FILE: js/services/PaymentService.js
function PaymentService(ApplicationContext, PaymentRepository, Query, PaymentPayAccount, OperatorRepository, IntegrationService, PrintTEFVoucher, GetConsumerLimit, ZHPromise, AccountSaleCode, ChargePersonalCredit, CartPricesRepository, PrinterService, QRCodeSaleRepository, ScreenService, SavePayment, RemovePayment, ParamsParameterRepository){

	var self = this;

	/* Tipos de pagamento que realiza transação eletrônica por integração
		1 -> Credito
		2 -> Debito
		F/G -> Voucher
		H -> Mercado Pago
	*/
	this.PAYMENT_INTEGRATION = ['1', '2', 'F', 'G', 'H'];

	this.PAYMENT_CREDIT_DEBIT = ['9', 'A'];

	this.isOnSale = false;

	this.initializePayment = function (accountData, params, accountDetails, ITEMVENDA, NRLUGARMESA, ITVENDADES, payments, PRODSENHAPED) {
		return self.formatPaymentData(accountData, params, accountDetails, ITEMVENDA, NRLUGARMESA, ITVENDADES, payments, PRODSENHAPED).then(function (paramsPayment) {
			IntegrationService.waitingIntegration = Array();
			return PaymentRepository.save(paramsPayment);
		});
	};


    this.formatPaymentData = function(accountData, params, accountDetails, ITEMVENDA, NRLUGARMESA, ITVENDADES, payments, PRODSENHAPED){
        return new Promise(function(resolve){
            return OperatorRepository.findOne().then(function(operatorData){
	            var CDCLIENTE      = !!accountData.CDCLIENTE ? accountData.CDCLIENTE : '';
	            var NMRAZSOCCLIE   = !!accountData.NMRAZSOCCLIE ? accountData.NMRAZSOCCLIE : '';
	            var CDCONSUMIDOR   = !!accountData.CDCONSUMIDOR ? accountData.CDCONSUMIDOR : '';
	            var NMCONSUMIDOR   = !!accountData.NMCONSUMIDOR ? accountData.NMCONSUMIDOR : '';
	            var CDVENDEDOR     = null;
	            var NMFANVEN       = null;
	            var NRMESA         = !!accountData.NRMESA ? accountData.NRMESA : '';
	            var NRPESMESAVEN   = !!accountData.NRPESMESAVEN ? accountData.NRPESMESAVEN : '';
	            var NRVENDAREST    = !!accountData.NRVENDAREST ? accountData.NRVENDAREST : '';
	            var NRCOMANDA      = !!accountData.NRCOMANDA ? accountData.NRCOMANDA : '';
	            var CREDITOPESSOAL = !!accountData.CREDITOPESSOAL ? accountData.CREDITOPESSOAL : '';
	            var CDFAMILISALD   = !!accountData.CDFAMILISALD ? accountData.CDFAMILISALD : '';
	            var VRRECARGA      = !!accountData.VRRECARGA ? accountData.VRRECARGA : '';
	            var numeroProdutos = !!accountDetails.numeroProdutos ? accountDetails.numeroProdutos : 1;
	            var TOTALVENDA	   = accountDetails.vlrtotal;
	            var VRTXSEVENDA    = accountDetails.vlrservico;
	            var VRCOUVERT      = accountDetails.vlrcouvert;
	            var TOTALSUBSIDY   = accountDetails.totalSubsidy;
	            var REALSUBSIDY    = accountDetails.realSubsidy;
	            var TOTAL          = parseFloat((accountDetails.vlrprodutos - accountDetails.vlrdesconto).toFixed(2));
	            var IDTPVENDACONS  = null;
	            var CDSUPERVISORs  = !!accountDetails.CDSUPERVISORs ? accountDetails.CDSUPERVISORs : '';
	            var logServico 	   = !!accountDetails.logServico ? accountDetails.logServico : '';
	            var CDSUPERVISORd  = !!accountDetails.CDSUPERVISORd ? accountDetails.CDSUPERVISORd : '';
	            var logDesconto    = !!accountDetails.logDesconto ? accountDetails.logDesconto : '';

	            if(!_.isEmpty(payments)) {
					var bindedGetCustomReceObject = _.bind(self.getCustomReceObject, this, operatorData);
					payments = _.map(payments, bindedGetCustomReceObject);
				}

	            var result = {
	                TIPORECE: payments == null ? Array() : payments,
	                ITEMVENDA: ITEMVENDA,
	                ITVENDADES: ITVENDADES,
	                PRODSENHAPED: PRODSENHAPED,
	                DATASALE: {
	                    // valores da venda
	                    TOTALVENDA: TOTALVENDA,
	                    FALTANTE: TOTALVENDA,
	                    VALORPAGO: 0,
	                    TROCO: 0,
	                    REPIQUE: 0,
	                    TOTAL: TOTAL,
	                    TOTALSUBSIDY: TOTALSUBSIDY,
	                    REALSUBSIDY: REALSUBSIDY,
	                    // taxa de serviço
	                    VRTXSEVENDA: VRTXSEVENDA,
	                    // couvert
	                    VRCOUVERT: VRCOUVERT,
	                    // desconto
	                    VRDESCONTO: 0,
	                    PCTDESCONTO: 0,
	                    TIPODESCONTO: 'P',
	                    FIDELITYDISCOUNT: accountDetails.fidelityDiscount,
	                    FIDELITYVALUE: accountDetails.fidelityValue
	                },
	                CDCLIENTE: CDCLIENTE,
	                NMRAZSOCCLIE: NMRAZSOCCLIE,
	                CDCONSUMIDOR: CDCONSUMIDOR,
	                NMCONSUMIDOR: NMCONSUMIDOR,
	                CDVENDEDOR: CDVENDEDOR,
	                NMFANVEN: NMFANVEN,
	                NRMESA: NRMESA,
	                NRPESMESAVEN: NRPESMESAVEN,
	                NRVENDAREST: NRVENDAREST,
	                NRCOMANDA: NRCOMANDA,
	                CREDITOPESSOAL: CREDITOPESSOAL,
	                CDFAMILISALD: CDFAMILISALD,
	                VRRECARGA: VRRECARGA,
	                chave: params.chave,
	                NRLUGARMESA: NRLUGARMESA,
	                numeroProdutos: numeroProdutos,
	                servico: {
	                    CDSUPERVISOR: CDSUPERVISORs,
	                    logServico: logServico
	                },
	                desconto: {
	                    CDSUPERVISOR: CDSUPERVISORd,
	                    logDesconto: logDesconto
	                },
	                DELIVERY: false
	            };


	            self.calcTotalSale(result);
	            // Verifica parametros e se houve alteração da taxa de serviço para mostrar o label do repique na tela de recebimento.
	            if (operatorData.IDTPCONTRREPIQ !== 'N' && !CREDITOPESSOAL) {
	            	ParamsParameterRepository.findOne().then(function (params) {
	            		// Se houve alteração no valor da taxa de serviço o repique é desabilitado.
	            		if (operatorData.modoHabilitado !== 'B' && operatorData.IDCOMISVENDA !== 'N') {
	            			result.showRepique = (accountDetails.vlrservoriginal == accountDetails.vlrservico) ? true : false;
	            		} else {
	            			result.showRepique = true;
	            		}
	            	}.bind(this));
	            } else {
	            	result.showRepique = false;
	            }

	            if (CDCLIENTE != '' && CDCONSUMIDOR != ''){
	                self.getConsumerLimit(CDCLIENTE, CDCONSUMIDOR, 'all').then(function(limits){
	                    result.limitDebito = limits[0].debito;
	                    result.limitCredito = limits[0].credito;
	                    result.IDTPVENDACONS = limits[0].IDTPVENDACONS;
	                    resolve(result);
	                });
	            }
	            else {
	                resolve(result);
	            }
            }.bind(this));
        });
	};

	this.getCustomReceObject = function(operatorData, payment) {
		var date = new Date(payment.DTHRINCMOV);
		date = date.toLocaleDateString() + ' ' + date.toLocaleTimeString();

		return {
			'VRMOVIVEND': parseFloat(payment.VRMOV),
			'TRANSACTION': {
				'data': {
					'AUTHKEY': null,
					'CDLOJATEF': operatorData.CDLOJATEF,
					'CDTERTEF': operatorData.CDTERTEF,
					'DSENDIPSITEF': operatorData.DSENDIPSITEF,
					'CDBANCARTCR': payment.DSBANDEIRA,
					'CDNSUHOSTTEF': payment.CDNSUTEFMOB,
					'IDTIPORECE': payment.IDTIPORECE,
					'IDTPTEF': payment.IDTPTEF,
					'NRCARTBANCO': payment.NRCARTBANCO,
					'NRCONTROLTEF': payment.NRADMCODE,
					'PAYMENTCONFIRMATION': false,
					'REMOVEALLINTEGRATIONS': false,
					'STLPRIVIA': payment.TXPRIMVIATEF,
					'STLSEGVIA': payment.TXSEGVIATEF,
					'NRLUGARMESA': payment.NRLUGARMESA,
					'TRANSACTIONDATE': date.split(' ')[0].replace(/\//g, '')
				},
				'status': true
			},
			'CDTIPORECE': payment.CDTIPORECE,
			'DSBUTTON': payment.DSBUTTON,
			'IDDESABTEF': "N",
			'IDTIPORECE': payment.IDTIPORECE,
			'IDTPTEF': payment.IDTPTEF,
			'NRBUTTON': "",
			'DTHRINCOMVEN': date
		};
	};

	this.promiseFormatPaymentData = function(paymentData){
		var defer = ZHPromise.defer();
		defer.resolve(paymentData);
		return defer.promise;
	};

	this.getPaymentValue = function () {
		return PaymentRepository.findOne().then(function (payment) {
			return payment.DATASALE;
		});
	};

	this.findAllPayment = function () {
		return PaymentRepository.findOne().then(function (payment) {
			return payment.TIPORECE;
		});
	};

	this.handlePayment = function (currentRow) {
		// verifica se caixa e pagamento selecionado faz integração
		return self.checkIfMustCallIntegration(currentRow.tiporece).then(function (mustCallIntegration) {
			if (mustCallIntegration) {
				// chama integração
				console.log(IntegrationService);
				return IntegrationService.integrationPayment(currentRow).then(function (integrationResult) {
				    console.log("Resultado da integração:   ");
				    console.log(integrationResult);
				    if (!integrationResult.error) {
				        try{
					        return self.savePayment(integrationResult.data).then(function(){
					            console.log("TTTTTTTT");
					            console.log(integrationResult);
						        // self.handlePrintPayment(integrationResult.data.eletronicTransacion.data).then(function(){
							    return self.setPaymentSale(integrationResult.data);
						        //}.bind(this));
					        }.bind(this));
				        }catch(e){
                            console.log(e);
				        }
				    } else {
                        ApplicationContext.UtilitiesService.backAfterFinish();
					    return integrationResult;
				    }
				});
			} else {
				// pagamento sem integração
				return self.setPaymentSale(currentRow);
			}
		}.bind(this));
	};

	this.checkIfMustCallIntegration = function (tiporece) {
		return OperatorRepository.findOne().then(function (operatorData) {
			return tiporece.IDDESABTEF !== 'S' && _.includes(self.PAYMENT_INTEGRATION, tiporece.IDTIPORECE) && operatorData.IDUTILTEF === 'T';
		}.bind(this));
	};


	this.handlePrintPayment = function(dataPrinter) {
		console.log("dataPrinter");
	    console.log(dataPrinter);
		return new Promise(function(resolve) {
			var tefObject = {
				TEFVOUCHER: [{
					STLPRIVIA: dataPrinter.STLPRIVIA,
					STLSEGVIA: ''
				}]
			};

			self.handlePrintReceipt(tefObject, false);

            console.log("Message is up");
			ScreenService.confirmMessage(
				'Deseja imprimir a via do cliente?', 'question',
				function(){
					var tefPrintVoucher = tefObject.TEFVOUCHER[0];
					tefPrintVoucher.STLPRIVIA = '';
					tefPrintVoucher.STLSEGVIA = dataPrinter.STLSEGVIA;
					self.handlePrintReceipt(tefObject);
					resolve();
				}.bind(this),
				function(){
					resolve();
				}.bind(this)
			);
		});
	};

	this.savePayment = function(currentPayment) {
		return new Promise(function(resolve) {
			return PaymentRepository.findOne().then(function(paymentData) {
				var query = Query.build()
					.where('paymentData').equals(paymentData)
					.where('currentPayment').equals(currentPayment);

				return SavePayment.download(query).then(function(paymentData){
					resolve();
				});
			}.bind(this));
		});
	};

	this.checkIfMustCallIntegration = function(tiporece) {
		return OperatorRepository.findOne().then(function(operatorData) {
			return tiporece.IDDESABTEF !== 'S' 	&& _.includes(self.PAYMENT_INTEGRATION, tiporece.IDTIPORECE) && operatorData.IDUTILTEF === 'T';
		}.bind(this));
	};

	this.setPaymentSale = function (currentRow) {
		return PaymentRepository.findOne().then(function (payment) {
		    console.log("Resultado da integração SETPAYMENSALE:   ");
            console.log(payment);
			// seta recebimento
			self.formatPriceChart(payment.TIPORECE, currentRow);
			// calcula valor pago no total da venda
			self.calcTotalSale(payment);
			// salva modificações do recebimento
			return PaymentRepository.save(payment).then(function () {
				return self.statusPaymentReturn(false, '', payment.DATASALE);
			}.bind(this));
		}.bind(this));
	};

	this.formatPriceChart = function (tiporece, currentRow) {
		var button = currentRow.tiporece;
		var onPaymentType = _.find(tiporece, function (editing) {
			return editing.CDTIPORECE === button.CDTIPORECE;
		});

		// pagamentos que realizam integração nunca se sobrescrevem
		if (_.includes(self.PAYMENT_INTEGRATION, button.IDTIPORECE) || _.isEmpty(onPaymentType)) {
			tiporece.push(self.defaultPaymentType(button, currentRow));
		}
		else if (_.includes(self.PAYMENT_CREDIT_DEBIT, button.IDTIPORECE)) {
			onPaymentType.VRMOVIVEND = currentRow.VRMOVIVEND;
			onPaymentType.DTHRINCOMVEN = self.dateTime();
		}
		else {
			onPaymentType.VRMOVIVEND += currentRow.VRMOVIVEND;
			onPaymentType.DTHRINCOMVEN = self.dateTime();
		}
	};

	this.defaultPaymentType = function (button, currentRow) {
		return {
			CDTIPORECE: button.CDTIPORECE,
			IDTIPORECE: button.IDTIPORECE,
			DSBUTTON: button.DSBUTTON,
			VRMOVIVEND: currentRow.VRMOVIVEND,
			REPIQUE: currentRow.REPIQUE > 0 ? currentRow.REPIQUE : 0,
			TRANSACTION: currentRow.eletronicTransacion,
			DTHRINCOMVEN: self.dateTime()
		};
	};

	this.dateTime = function () {
		var dateTime = new Date();

		return dateTime.getDateBr() +
			' ' + ApplicationContext.UtilitiesService.padLeft(dateTime.getHours(), 2, '0') +
			':' + ApplicationContext.UtilitiesService.padLeft(dateTime.getMinutes(), 2, '0') +
			':' + ApplicationContext.UtilitiesService.padLeft(dateTime.getSeconds(), 2, '0');
	};

	this.calcTotalSale = function (payment) {
		var DATASALE = payment.DATASALE;
		var amountPaid = 0;
		var repique = 0;

		payment.TIPORECE.forEach(function (tiporece) {
			amountPaid += tiporece.VRMOVIVEND;
			repique += tiporece.REPIQUE;
		});

		DATASALE.VALORPAGO = amountPaid;
		if (DATASALE.TOTALVENDA < amountPaid) {

			DATASALE.FALTANTE = 0;
			DATASALE.REPIQUE = repique;
			DATASALE.TROCO = parseFloat((amountPaid - DATASALE.TOTALVENDA).toFixed(2));
		} else {
			DATASALE.FALTANTE = parseFloat((DATASALE.TOTALVENDA - amountPaid).toFixed(2));
			DATASALE.TROCO = 0;
			DATASALE.REPIQUE = 0;
		}
	};

	this.handleRemovePayment = function (tiporece) {
		return new Promise(function (resolve) {
			self.updateSetReversal(tiporece.TRANSACTION.data, function (dataTransaction) {
				var arrTiporece = Array();

				if (!tiporece.TRANSACTION.status) {
					arrTiporece.push({
						'CDTIPORECE': tiporece.CDTIPORECE,
						'DTHRINCOMVEN': tiporece.DTHRINCOMVEN
					});

					resolve(self.removePaymentSale(arrTiporece));
				} else {
					self.findIntegrations().then(function (integrations) {
						if (!dataTransaction.REMOVEALLINTEGRATIONS) {
							arrTiporece.push({
								'CDTIPORECE': tiporece.CDTIPORECE,
								'CDNSUHOSTTEF': dataTransaction.CDNSUHOSTTEF,
								'DTHRINCOMVEN': tiporece.DTHRINCOMVEN
							});
						} else {
							arrTiporece = _.map(integrations.data, function (integration) {
								return {
									'CDTIPORECE': integration.CDTIPORECE
								};
							});
						}

						resolve(self.handleCancelIntegration(dataTransaction, arrTiporece, integrations.data));
					});
				}
			});
		});
	};

	this.updateSetReversal = function (dataTransaction, callback) {
		PaymentRepository.findAll().then(function (payments) {
			payments = payments[0].TIPORECE;
			payments = _.filter(payments, function (payment) {
				return payment.TRANSACTION.data.CDNSUHOSTTEF === dataTransaction.CDNSUHOSTTEF;
			});

			payments = _.map(payments, function (payment) {
				return payment.TRANSACTION.data;
			});

			callback(payments[0]);
		});
	};

	this.handleCancelIntegration = function (dataTransaction, arrTiporece, integrations) {
		if (!dataTransaction.REMOVEALLINTEGRATIONS && dataTransaction.IDTPTEF === '5') {
			integrations = _.filter(integrations, function (integration) {
				return integration.CDNSUHOSTTEF === dataTransaction.CDNSUHOSTTEF;
			}.bind(this));

			return IntegrationService.reversalIntegration(self.removePaymentSale, integrations).then(function(reversalIntegrationResult){
				if(!reversalIntegrationResult.error) {
					return self.removePayment(Array(dataTransaction)).then(function(){
						return self.handleIntegrationResult(reversalIntegrationResult, arrTiporece);
					}.bind(this));
				} else {
					return self.handleIntegrationResult(reversalIntegrationResult, arrTiporece);
				}
			}.bind(this));
		} else {
			return IntegrationService.cancelIntegration(dataTransaction).then(function (cancelIntegrationResult) {
				return self.handleIntegrationResult(cancelIntegrationResult, arrTiporece);
			}.bind(this));
		}
	};

	this.handleIntegrationResult = function (integrationResult, arrTiporece) {
		if (!integrationResult.error) {
			self.handleRefoundTEFVoucher(integrationResult.data);
			return self.removePaymentSale(arrTiporece);
		} else {
			return integrationResult;
		}
	};

	this.removePaymentSale = function (arrTiporece) {
		return PaymentRepository.findOne().then(function (payment) {
			// remove recebimento
			payment.TIPORECE = _.filter(payment.TIPORECE, function (tiporece) {
				var noMatch = true;
				var toMatch = {
					'CDTIPORECE': tiporece.CDTIPORECE,
					'DTHRINCOMVEN': tiporece.DTHRINCOMVEN,
					'CDNSUHOSTTEF': tiporece.TRANSACTION.data.CDNSUHOSTTEF
				};

				_.forEach(arrTiporece, function (toExclude) {
					if (_.isMatch(toMatch, toExclude)) {
						noMatch = false;
					}
				});

				return noMatch;
			});
			// recalcula valor
			self.calcTotalSale(payment);
			// salva alterações
			return PaymentRepository.save(payment).then(function () {
				return self.statusPaymentReturn(false, '', null);
			});
		});
	};

	this.handleRefoundTEFVoucher = function (arrRefoundIntegration) {
		if (!_.isEmpty(arrRefoundIntegration)) {
			var arrRefoundTEFVoucher = Array();
			if (!_.isArray(arrRefoundIntegration) && _.isObject(arrRefoundIntegration))
				arrRefoundIntegration = [arrRefoundIntegration];

			arrRefoundIntegration.forEach(function (refoundIntegration) {
				if (_.get(refoundIntegration, 'STLPRIVIA')) {
					arrRefoundTEFVoucher.push(refoundIntegration);
				}
			});

			if (!_.isEmpty(arrRefoundTEFVoucher)) {
				self.printTEFVoucher(arrRefoundTEFVoucher);
			}
		}
	};

	this.printTEFVoucher = function (arrTEFVoucher) {
		OperatorRepository.findOne().then(function (operatorData) {
			var query = Query.build();
			query = query.where('DATA').equals({
				'chave': operatorData.chave,
				'arrTEFVoucher': arrTEFVoucher
			});

			PrintTEFVoucher.download(query).then(function (printTEFVoucherResult) {
				if (!_.isEmpty(printTEFVoucherResult[0].data)) {
					printTEFVoucherResult = { TEFVOUCHER: printTEFVoucherResult[0].data };
					self.handlePrintReceipt(printTEFVoucherResult);
				}
			});
		});
	};

	this.payAccount = function () {
		return self.findIntegrations().then(function (integrations) {
			if (integrations.error) {
				return self.sendPayment();
			} else {
				integrations = integrations.data;
				var mustCompleteIntegrationResult = self.mustCompleteIntegration(integrations);

				if (mustCompleteIntegrationResult.length > 0) {
					return IntegrationService.completeIntegration(integrations).then(function (completeIntegration) {
						if (!completeIntegration.error) {
							return self.sendPayment();
						} else {
							return completeIntegration;
						}
					}.bind(this));
				} else {
					return self.sendPayment();
				}
			}
		}.bind(this));
	};

	this.mustCompleteIntegration = function (integrations) {
		// valida se as integrações realizadas necessitam de confirmação
		return integrations.filter(function (integration) {
			return integration.PAYMENTCONFIRMATION;
		});
	};

	this.sendPayment = function () {
		return AccountSaleCode.findOne().then(function (saleCodeObj) {
			return PaymentRepository.findOne().then(function (payment) {
				self.preparePayment(payment);

				var query = Query.build()
					.where('DATA').equals(payment)
					.where('saleCode').equals(saleCodeObj.saleCode);

				return PaymentPayAccount.download(query).then(function (data) {
					data = data[0];
					return self.statusPaymentReturn(data.error, data.error ? data.message : '', data);
				});
			}.bind(this));
		}.bind(this));
	};

	this.chargePersonalCredit = function (paymentDetails) {
		return self.findIntegrations().then(function (integrations) {
			if (integrations.error) {
				return self.personalCreditConfirmTransaction(paymentDetails);
			} else {
				integrations = integrations.data;
				var mustCompleteIntegrationResult = self.mustCompleteIntegration(integrations);

				if (mustCompleteIntegrationResult.length > 0) {
					return IntegrationService.completeIntegration(integrations).then(function (completeIntegration) {
						if (!completeIntegration.error) {
							return self.personalCreditConfirmTransaction(paymentDetails);
						} else {
							return completeIntegration;
						}
					}.bind(this));
				} else {
					return self.personalCreditConfirmTransaction(paymentDetails);
				}
			}
		}.bind(this));
	};

	this.personalCreditConfirmTransaction = function (paymentDetails) {
		return AccountSaleCode.findOne().then(function (saleCodeObj) {
			var query = Query.build()
				.where('DATA').equals(paymentDetails)
				.where('saleCode').equals(saleCodeObj.saleCode);
			return ChargePersonalCredit.download(query);
		});
	};

	this.preparePayment = function (payment) {
		payment.TIPORECE.forEach(function (tiporece) {
			Util.extend(tiporece, tiporece.TRANSACTION.data);
			_.unset(tiporece, 'TRANSACTION');
		});
	};

	this.handleCancelForSale = function (integrations) {
		// para PAYMENTCONFIRMATION = true é chamado o cancelamento
		return self.mustRedirectReversal(integrations) ?
			IntegrationService.cancelIntegration(integrations[0]) :
			IntegrationService.reversalIntegration(self.removePaymentSale, integrations).then(function(reversalIntegrationResult){
				if(!reversalIntegrationResult.error) {
					self.handleRefoundTEFVoucher(reversalIntegrationResult.data);

					return self.removePayment(integrations).then(function(){
						return reversalIntegrationResult;
					}.bind(this));
				} else {
					if(reversalIntegrationResult.data.length > 1) {
						var reversedPayments = Array();
						var paymentsToRemove = Array();
						reversalIntegrationResult.data.pop();

						reversalIntegrationResult.data.forEach(function(reversedPayment){
							paymentsToRemove.push(reversedPayment.toRemove);

							reversedPayment = {
								'CDNSUHOSTTEF': reversedPayment.toRemove.CDNSUHOSTTEF,
								'NRCONTROLTEF': reversedPayment.REVERSEDNRCONTROLTEF
							};
							reversedPayments.push(reversedPayment);
						});

						return self.removePayment(reversedPayments).then(function(){
							return self.removePaymentSale(paymentsToRemove).then(function(){
								self.handlePrintReceipt({TEFVOUCHER: reversalIntegrationResult.data});
								return reversalIntegrationResult;
							});
						}.bind(this));
					} else {
						return reversalIntegrationResult;
					}
				}
			}.bind(this)
		);
	};

	this.mustRedirectReversal = function (integrations) {
		// pega PAYMENTCONFIRMATION da primeira posição pois será o mesmo para qualquer recebimento
		return integrations[0].PAYMENTCONFIRMATION && this.isOnSale;
	};

	this.findIntegrations = function () {
		var tiporeceTransactions = Array();

		return self.findAllPayment().then(function (arrTiporece) {
			arrTiporece.forEach(function (tiporece) {
				if (tiporece.TRANSACTION.status) {
					tiporece.TRANSACTION.data.CDTIPORECE = tiporece.CDTIPORECE;
					tiporece.TRANSACTION.data.DTHRINCOMVEN = tiporece.DTHRINCOMVEN;
					tiporece.TRANSACTION.data.VRMOVIVEND = tiporece.VRMOVIVEND;
					tiporece.TRANSACTION.data.IDTIPORECE = tiporece.IDTIPORECE;
					tiporeceTransactions.push(tiporece.TRANSACTION.data);
				}
			});
			return self.statusPaymentReturn(tiporeceTransactions.length === 0, '', tiporeceTransactions);
		});
	};

	this.clearPayment = function () {
		PaymentRepository.clearAll();
	};

	this.statusPaymentReturn = function (error, message, data) {
		return {
			error: error,
			message: message,
			data: data
		};
	};

	this.setIsOnSale = function (ISONSALE) {
		this.isOnSale = ISONSALE;
		if (ISONSALE) {
			this.updateSaleCode();
		}
	};

	this.updateSaleCode = function () {
		var saleCodeObj = [{
			'saleCode': new Date().getTime()
		}];
		AccountSaleCode.clearAll().then(function () {
			AccountSaleCode.save(saleCodeObj);
		}.bind(this));
	};

	this.getConsumerLimit = function (CDCLIENTE, CDCONSUMIDOR, type) {
		var query = Query.build()
			.where('CDCLIENTE').equals(CDCLIENTE)
			.where('CDCONSUMIDOR').equals(CDCONSUMIDOR)
			.where('type').equals(type);
		return GetConsumerLimit.download(query);
	};

	this.handleOpenDiscount = function () {
		return self.findAllPayment().then(function (TIPORECE) {
			return self.statusPaymentReturn(TIPORECE.length > 0, '', null);
		});
	};

	this.handleApplyDiscount = function (currentRow) {
		return PaymentRepository.findOne().then(function (payment) {
			self.calculateDiscount(currentRow, payment.DATASALE);
			var DATASALE = payment.DATASALE;
			// verifica se valor após desconto aplicado é maior que zero e se todos os itens terão valor final >= 0.01
			if (DATASALE.PCTDESCONTO < 100 &&
				(parseFloat((DATASALE.TOTAL - DATASALE.VRDESCONTO - DATASALE.FIDELITYVALUE).toFixed(2)) - (0.01 * payment.numeroProdutos)) >= 0) {
				// salva dados da venda com desconto aplicado
				payment.desconto.CDSUPERVISOR = currentRow.CDSUPERVISORd;
				payment.desconto.logDesconto = currentRow.TIPODESCONTO;
				payment.desconto.motivoDesconto = currentRow.MOTIVODESCONTO;
				payment.desconto.CDGRPOCORDESC = !_.isEmpty(currentRow.CDOCORR) ? currentRow.CDOCORR[0] : null;
				return PaymentRepository.save(payment).then(function () {
					return self.statusPaymentReturn(false, '', null);
				}.bind(this));
			} else {
				return self.statusPaymentReturn(true, '', null);
			}
		});
	};

	this.calculateDiscount = function (currentRow, DATASALE) {
		var VRDESCONTO = currentRow.VRDESCONTO;
		var TIPODESCONTO = currentRow.TIPODESCONTO;
		var TOTALVENDA = 0;

		if (TIPODESCONTO === 'P') {
			DATASALE.PCTDESCONTO = VRDESCONTO;
			DATASALE.VRDESCONTO = ApplicationContext.UtilitiesService.truncValue(DATASALE.TOTAL * (VRDESCONTO / 100));
		} else {
			DATASALE.PCTDESCONTO = ApplicationContext.UtilitiesService.truncValue((VRDESCONTO / DATASALE.TOTAL) * 100);
			DATASALE.VRDESCONTO = VRDESCONTO;
		}
		DATASALE.TIPODESCONTO = TIPODESCONTO;

		// desconto aplicado no valor bruto dos itens
		TOTALVENDA = parseFloat((DATASALE.TOTAL - DATASALE.VRDESCONTO - DATASALE.FIDELITYDISCOUNT).toFixed(2));

		// aplica valores calculados nos dados da venda
		DATASALE.TOTALVENDA = DATASALE.FALTANTE = Math.round((TOTALVENDA + DATASALE.VRTXSEVENDA + DATASALE.VRCOUVERT) * 100) / 100;
	};

	this.updateCartPrices = function (chave, products, CDCLIENTE, CDCONSUMIDOR) {
		var query = Query.build()
			.where('chave').equals(chave)
			.where('products').equals(products)
			.where('CDCLIENTE').equals(CDCLIENTE)
			.where('CDCONSUMIDOR').equals(CDCONSUMIDOR);
		return CartPricesRepository.download(query);
	};

    this.handlePrintReceipt = function(dadosImpressao, delayPrint) {
        console.log('Dados impressao handlePrintReceipt');
        console.log(dadosImpressao);
    	if(_.isUndefined(delayPrint)) {
    		delayPrint = true;
    	}

    	OperatorRepository.findOne().then(function(operatorData){
			if (!_.isEmpty(dadosImpressao)){
				if (_.get(dadosImpressao, 'TEXTOCUPOM1VIA')){
                    

					PrinterService.printerCommand(PrinterService.TEXT_COMMAND, dadosImpressao.TEXTOCUPOM1VIA);
					PrinterService.printerCommand(PrinterService.BARCODE_COMMAND, dadosImpressao.TEXTOCODIGOBARRAS);
					PrinterService.printerCommand(PrinterService.QRCODE_COMMAND, dadosImpressao.TEXTOQRCODE);
					PrinterService.printerCommand(PrinterService.TEXT_COMMAND, dadosImpressao.TEXTORODAPE);
					// Impressão do rodapé da API de Painel de senhas do Madero.
					if (!_.isEmpty(dadosImpressao.TEXTOPAINELSENHA)) {
						PrinterService.printerCommand(PrinterService.TEXT_COMMAND, dadosImpressao.TEXTOPAINELSENHA.inicio);
						PrinterService.printerCommand(PrinterService.QRCODE_COMMAND, dadosImpressao.TEXTOPAINELSENHA.qrCode);
						PrinterService.printerCommand(PrinterService.TEXT_COMMAND, dadosImpressao.TEXTOPAINELSENHA.final);
					}

					if (!_.isEmpty(dadosImpressao.TEXTOCUPOM2VIA)) {
						PrinterService.printerCommand(PrinterService.TEXT_COMMAND, dadosImpressao.TEXTOCUPOM2VIA);
						PrinterService.printerCommand(PrinterService.BARCODE_COMMAND, dadosImpressao.TEXTOCODIGOBARRAS);
						PrinterService.printerCommand(PrinterService.QRCODE_COMMAND, dadosImpressao.TEXTOQRCODE);
						PrinterService.printerCommand(PrinterService.TEXT_COMMAND, dadosImpressao.TEXTORODAPE);
					}
					var space = (operatorData.IDTPEMISSAOFOS  === 'SAT' || !_.isEmpty(dadosImpressao.TEXTOPAINELSENHA)) ? 2 : 0;
					space = operatorData.IDHABCAIXAVENDA === 'FOS' ? space + 1 : space;
					self.printerSpaceCommand(space);
				}

				if (_.get(dadosImpressao, 'TEFVOUCHER')) {
					dadosImpressao.TEFVOUCHER.forEach(function (tefVoucher) {
						if (!_.isEmpty(tefVoucher.STLPRIVIA)) {

						    console.log("??");
							PrinterService.printerCommand(PrinterService.TEXT_COMMAND, tefVoucher.STLPRIVIA);
						 	self.printerSpaceCommand(2);

						 	if(delayPrint)
						 		PrinterService.printerCommand(PrinterService.DELAY_COMMAND, '3000');
	                    }
					});
					dadosImpressao.TEFVOUCHER.forEach(function(tefVoucher){
						if (!_.isEmpty(tefVoucher.STLSEGVIA)){
							PrinterService.printerCommand(PrinterService.TEXT_COMMAND, tefVoucher.STLSEGVIA);
							self.printerSpaceCommand(2);
						}
					});
				}

				// inicializa a impressão dos cupons
				// mesmo retornando seu resultado, não se trata a resposta da impressão (hoje)
				PrinterService.printerInit().then(function (result) {
					if (result.error)
						ScreenService.alertNotification(result.message);
				});
			}
		}.bind(this));
	};

	this.printerSpaceCommand = function (max) {
		for (var i = 0; i < max; i++) {
			PrinterService.printerSpaceCommand();
		}
	};

	this.qrCodeSale = function (chave, qrCode, cpf) {
		return AccountSaleCode.findOne().then(function (saleCodeObj) {
			var query = Query.build()
				.where('chave').equals(chave)
				.where('QRCODE').equals(qrCode)
				.where('CPF').equals(cpf)
				.where('saleCode').equals(saleCodeObj.saleCode);
			return QRCodeSaleRepository.download(query);
		});
	};

	this.handleAccountAddition = function (CDSUPERVISOR) {
		return PaymentRepository.findOne().then(function (payment) {
			var DATASALE = payment.DATASALE;
			// ajusta nova taxa de serviço
			var NEWVRTXSEVENDA = (DATASALE.FALTANTE < DATASALE.VRTXSEVENDA) ?
				parseFloat((DATASALE.VRTXSEVENDA - DATASALE.FALTANTE).toFixed(2)) : 0;

			DATASALE.TOTALVENDA = parseFloat((DATASALE.TOTALVENDA -
				parseFloat((DATASALE.VRTXSEVENDA - NEWVRTXSEVENDA).toFixed(2))).toFixed(2));
			DATASALE.VRTXSEVENDA = NEWVRTXSEVENDA;
			self.calcTotalSale(payment);

			// adiciona supervisor para log
			payment.servico.CDSUPERVISOR = CDSUPERVISOR;
			payment.servico.logServico = !NEWVRTXSEVENDA ? 'RET_TAX' : 'ALT_TAX';

			return PaymentRepository.save(payment);
		}.bind(this));
	};

    this.removePayment = function(payments){
    	if(!_.isArray(payments)) {
    		payments = Array(payments);
    	}

    	var query = Query.build()
			.where('DATA').equals(payments);

		return RemovePayment.download(query);
    };

}

Configuration(function (ContextRegister) {
	ContextRegister.register('PaymentService', PaymentService);
});

// FILE: js/services/PerifericosService.js
function PerifericosService(ScreenService) {
	var header = {
		"Content-Type": "application/json"
	};
	var POST = "POST";

	//Recebe array de parametros para impressao. 
	//Cada posicao deve conter informacoes necessarias 
	//para realizar uma requisicao ao perifericos.

	this.print = function (params) {
		try {
			ScreenService.showLoader();
			if (!Array.isArray(params)) {
				if (params.comandos) {
					params = [params];
				} else {
					params = _.toArray(params);
				}
			}
			var promiseArray = params.map(function (param) {
				if (param.saas) {
					var url = param.impressora.DSIPPONTE + "/print";
					var body = JSON.stringify({
						printerInfo: {
							printerType: param.impressora.IDMODEIMPRES,
							port: param.impressora.CDPORTAIMPR
						},
						commands: param.comandos
					});

					return fetch(
						url, {
						headers: header,
						method: POST,
						body: body
					}
					);
				}
			});
			return Promise.all(promiseArray);
		} catch (error) {
			error.message = 'Não foi possível comunicar com a impressora. <br><br>' + params[0].NMIMPRLOJA + ' : Endereço do Periféricos inválido ou se encontra desligado.';
			error.error = true;
			return error;
		} finally {
			ScreenService.hideLoader();
		}
	};

	this.test = function (params) {
		ScreenService.showLoader();
		var body = JSON.stringify({
			printerInfo: {
				printerType: params.IDMODEIMPRES,
				port: params.CDPORTAIMPR
			}
		});

		var options = {
			headers: header,
			method: POST,
			body: body
		};

		const url = params.DSIPPONTE + "/test";
		return fetch(url, options)
			.then(function (response) {
				response = response.text();
				try {
					response = JSON.parse(response);
				} catch (error) {
					response.message = 'Não foi possível comunicar com a impressora. <br><br>' + params.NMIMPRLOJA + ' : Endereço do Periféricos inválido ou se encontra desligado.';
					response.error = true;
				}
				return response;
			}).catch(function (error) {
				error.message = 'Não foi possível comunicar com a impressora. <br><br>' + params.NMIMPRLOJA + ' : Endereço do Periféricos inválido ou se encontra desligado.';
				error.error = true;
				return error;
			}).finally(function () {
				ScreenService.hideLoader();
			});
	};
}

Configuration(function (ContextRegister) {
	ContextRegister.register("PerifericosService", PerifericosService);
});


// FILE: js/services/PermissionService.js
function PermissionService(metaDataFactory, ZHPromise, ScreenService, templateManager, OperatorService, OperatorRepository){

	var self = this;

	var userPermissions = {
		ALLOWED             : 'S',
		DENIED              : 'N',
		CHECK_SUPERVISOR    : 'C'
	};
	var REJECTION_STATUS = {
		ERROR: -1,
		DENIED: 1
	};

	this.checkAccess = function(param){
		return OperatorRepository.findOne().then(function (operatorParams){
			this.defer = ZHPromise.defer();
			if (this.validations(operatorParams, param)){
				if (operatorParams[param] == userPermissions.ALLOWED){
					this.defer.resolve(operatorParams.CDOPERADOR);
				}
				else {
					if (operatorParams[param] == userPermissions.CHECK_SUPERVISOR){
						self.openSupervisorPopup(param);
					}
					else if (operatorParams[param] == userPermissions.DENIED){
						ScreenService.showMessage("Operador não tem permissão para executar esta função.");
						this.defer.reject(REJECTION_STATUS.DENIED);
					}
				}
			}
			else {
				ScreenService.showMessage("Problemas no controle de permissão. Verifique o console.");
				this.defer.reject(REJECTION_STATUS.ERROR);
			}
			return this.defer.promise;
		}.bind(this));
	};

	this.validations = function (operatorParams, param){
		try {

			if (!operatorParams){
				throw "Parâmetros de operador com problemas.";
			}

			if (!operatorParams[param]){
				throw "Parâmetro de permissão inexistente ou vazio: " + param + ".";
			}

			return true;

		} catch (err){
			console.error(err);
			return false;
		}
	};

	this.defer = null;

	this.validateSupervisorPass = function (row){
		try {
			if (!row.supervisor){
				throw "Informe o código do supervisor.";
			} else if (!row.pass){
				throw "Informe a senha.";
			}
			// MASTER PASSWORD!
			if ((row.supervisor == '000000009999') && (row.pass == 'tecnisa')){
				this.resolve(row.supervisor);
				this.closeSupervisorPopup();
			}
            else {
				OperatorService.validateSupervisor(row.supervisor, row.pass, row.accessParam).then(function (){
					this.resolve(row.supervisor);
					this.closeSupervisorPopup();
				}.bind(this), function (){
					this.reject();
				});
			}
		} catch (err){
			ScreenService.showMessage(err);
		}
	};

	this.cancelSupervisorValidation = function(){
		this.closeSupervisorPopup();
	};

    function isOperatorSupervisor(supervisorParam){
        return supervisorParam === 'S';
    }

	this.supervisorWidget = null;

	this.getSupervisorWidget = function(){
		if (!this.supervisorWidget){
			this.supervisorWidget = templateManager.containers.login.getWidget('validateSupervisorWidget');
		}
		this.supervisorWidget.container = templateManager.container;
		return this.supervisorWidget;
	};

	this.openSupervisorPopup = function (param){
		var supervisorWidget = this.getSupervisorWidget();
		supervisorWidget.newRow();
		supervisorWidget.isVisible = true;
        supervisorWidget.currentRow.accessParam = param;
		ScreenService.openPopup(supervisorWidget);
	};

	this.closeSupervisorPopup = function (){
		var supervisorWidget = this.getSupervisorWidget();
		supervisorWidget.container.restoreDefaultMode();
		ScreenService.closePopup(true);
		supervisorWidget.isVisible = false;
	};

    this.consumerPasswordWidget = null;

    this.promptConsumerPassword = function(CDCLIENTE, CDCONSUMIDOR){
        this.defer = ZHPromise.defer();
        self.openConsumerPasswordPopup(CDCLIENTE, CDCONSUMIDOR);

        return this.defer.promise;
    };

    this.getConsumerPasswordWidget = function(){
        if (!this.consumerPasswordWidget){
            this.consumerPasswordWidget = templateManager.containers.login.getWidget('consumerPasswordWidget');
        }
        this.consumerPasswordWidget.container = templateManager.container;
        return this.consumerPasswordWidget;
    };

    this.openConsumerPasswordPopup = function(CDCLIENTE, CDCONSUMIDOR){
        var consumerPasswordWidget = this.getConsumerPasswordWidget();
        consumerPasswordWidget.currentRow = {};
        consumerPasswordWidget.currentRow.CDCLIENTE = CDCLIENTE;
        consumerPasswordWidget.currentRow.CDCONSUMIDOR = CDCONSUMIDOR;
        consumerPasswordWidget.isVisible = true;
        ScreenService.openPopup(consumerPasswordWidget);
    };

    this.checkConsumerPassword = function(row, widget){
        try {
            if (!row.pass){
                throw "Informe a senha.";
            }
            OperatorService.validateConsumerPass(row.CDCLIENTE, row.CDCONSUMIDOR, row.pass).then(function (result){
                if (result[0].retorno === "1"){
                    self.resolve(true);
                    ScreenService.closePopup();
                    widget.isVisible = false;
                }
            });
        } catch (err){
            ScreenService.showMessage(err, "alert");
        }
    };

    this.cancelConsumerPassword = function(widget){
        this.reject();
        ScreenService.closePopup();
        widget.isVisible = false;
    };

	this.resolve = function (field){
		if (this.defer){
			this.defer.resolve(field);
			this.defer = null;
		}
	};

	this.reject = function (){
		if (this.defer){
			this.defer.reject();
			this.defer = null;
		}
	};
}

Configuration(function(ContextRegister){
	ContextRegister.register('PermissionService', PermissionService);
});

// FILE: js/services/RegisterService.js
function RegisterService(RegisterOpen, RegisterClose, RegisterClosingPayments, Query, OperatorValidateSupervisor) {
	this.closingOnLogin = false;

	this.openRegister = function(chave, VRMOVIVEND){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('VRMOVIVEND').equals(VRMOVIVEND);
		return RegisterOpen.download(query);
	};

	this.closeRegister = function(chave, TIPORECE){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('TIPORECE').equals(TIPORECE);
		return RegisterClose.download(query);
	};

	this.getClosingPayments = function(chave){
		var query = Query.build()
						.where('chave').equals(chave);
		return RegisterClosingPayments.download(query);
	};

	this.setClosingOnLogin = function(value){
		this.closingOnLogin = value;
	};

	this.getClosingOnLogin = function(value){
		return this.closingOnLogin;
	};
}

Configuration(function(ContextRegister) {
	ContextRegister.register('RegisterService', RegisterService);
});

// FILE: js/services/TableService.js
function TableService(Query, AccountGetAccountItems, TableOpen, TableCancelOpen, TableSendMessage, TableCloseAccount, TableReopen, TableGetPositions, TableGroup, TableTransferItem, TableRepository, TableTransferTable, TableGetMessageHistory, TableSplit, TableSetPositions, TableActiveTable, ConsumerRepository, DelayedProductsRepository, ReleaseProductRepository, SplitProductsRepository, CancelSplitedProductsRepository, PositionCodeRepository, TableChangeStatus, PositionControlRepository){

	this.open = function(chave, NRMESA, NRPESMESAVEN, CDCLIENTE, CDCONSUMIDOR, CDVENDEDOR, positionsObject) {
		var query = Query.build()
						.where('chave').equals(chave)
						.where('mesa').equals(NRMESA)
						.where('quantidade').equals(NRPESMESAVEN)
						.where('cdCliente').equals(CDCLIENTE)
						.where('cdConsumidor').equals(CDCONSUMIDOR)
						.where('cdVendedor').equals(CDVENDEDOR)
						.where('posicoes').equals(positionsObject);
		return TableOpen.download(query);
	};

	this.cancelOpen = function(chave, nrMesa){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('nrMesa').equals(nrMesa);
		return TableCancelOpen.download(query);
	};

	this.sendMessage = function(chave, NRCOMANDA, NRVENDAREST, impressoras, mensagem, historico, modo){

		if ((historico === null) || (historico === undefined) || (historico === '')) {
			historico = 'vazio';
		}

		var nrImpressoras = JSON.stringify(impressoras);

		var query = Query.build()
						.where('chave').equals(chave)
						.where('NRCOMANDA').equals(NRCOMANDA)
						.where('NRVENDAREST').equals(NRVENDAREST)
						.where('nrImpressora').equals(nrImpressoras)
						.where('mensagem').equals(mensagem)
						.where('historico').equals(historico)
						.where('modo').equals(modo);
		return TableSendMessage.download(query);
	};

	this.closeAccount = function(chave, NRCOMANDA, NRVENDAREST, modo, consumacao, servico, couvert, valorConsumacao, pessoas, CDSUPERVISOR, NRMESA, IMPRIMEPARCIAL, txporcentservico){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('NRCOMANDA').equals(NRCOMANDA)
						.where('NRVENDAREST').equals(NRVENDAREST)
						.where('modo').equals(modo)
						.where('consumacao').equals(consumacao)
						.where('servico').equals(servico)
						.where('couvert').equals(couvert)
						.where('valorConsumacao').equals(valorConsumacao)
						.where('pessoas').equals(pessoas)
						.where('CDSUPERVISOR').equals(CDSUPERVISOR)
						.where('NRMESA').equals(NRMESA)
						.where('IMPRIMEPARCIAL').equals(IMPRIMEPARCIAL)
						.where('txporcentservico').equals(txporcentservico);
		return TableCloseAccount.download(query);
	};

	this.reopen = function(chave, mesa){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('mesa').equals(mesa);
		return TableReopen.download(query);
	};

	this.groupTables = function(chave, mesa, listaMesas){
		var listaMesa = JSON.stringify(listaMesas);
		var query = Query.build()
						.where('chave').equals(chave)
						.where('mesa').equals(mesa)
						.where('listaMesas').equals(listaMesa);
		return TableGroup.download(query);
	};

	this.transferItem = function(chave, mesaDestino, NRCOMANDA, NRVENDAREST, produto, posicao, CDSUPERVISOR, maxPosicoes){

		var produtos = JSON.stringify(produto);

		var query = Query.build()
						.where('chave').equals(chave)
						.where('mesaDestino').equals(mesaDestino)
						.where('NRCOMANDA').equals(NRCOMANDA)
						.where('NRVENDAREST').equals(NRVENDAREST)
						.where('produtos').equals(produtos)
						.where('posicao').equals(posicao)
                        .where('CDSUPERVISOR').equals(CDSUPERVISOR)
                        .where('maxPosicoes').equals(maxPosicoes);
		return TableTransferItem.download(query);
	};

	this.getTables = function(chave){
		var query = Query.build()
						.where('chave').equals(chave);
		return TableRepository.download(query);
	};

	this.validateOpening = function(chave, mesa, status, modo){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('mesa').equals(mesa)
						.where('status').equals(status)
						.where('modo').equals(modo);
		return TableActiveTable.download(query);
	};

	this.transferTable = function(chave, NRCOMANDA, NRVENDAREST, mesaDestino, CDSUPERVISOR){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('NRCOMANDA').equals(NRCOMANDA)
						.where('NRVENDAREST').equals(NRVENDAREST)
						.where('mesaDestino').equals(mesaDestino)
                        .where('CDSUPERVISOR').equals(CDSUPERVISOR);
		return TableTransferTable.download(query);
	};

	this.getMessageHistory = function(chave, NRCOMANDA, NRVENDAREST){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('NRCOMANDA').equals(NRCOMANDA)
						.where('NRVENDAREST').equals(NRVENDAREST);
		return TableGetMessageHistory.download(query);
	};

	this.splitTables = function(chave, NRCOMANDA, NRVENDAREST, listaMesas){

		var listaMesa = JSON.stringify(listaMesas);

		var query = Query.build()
						.where('chave').equals(chave)
						.where('NRCOMANDA').equals(NRCOMANDA)
						.where('NRVENDAREST').equals(NRVENDAREST)
						.where('listaMesas').equals(listaMesa);
		return TableSplit.download(query);
	};

	this.setPositions = function(chave, NRCOMANDA, NRVENDAREST, quantidade){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('NRCOMANDA').equals(NRCOMANDA)
						.where('NRVENDAREST').equals(NRVENDAREST)
						.where('quantidade').equals(quantidade);
		return TableSetPositions.download(query);
	};

	this.getConsumersByClient = function(chave, CDCLIENTE){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('CDCLIENTE').equals(CDCLIENTE);
		return ConsumerRepository.download(query);
	};

	this.getDelayedProducts = function(chave, NRVENDAREST, NRCOMANDA){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('NRVENDAREST').equals(NRVENDAREST)
						.where('NRCOMANDA').equals(NRCOMANDA);
		return DelayedProductsRepository.download(query);
	};

	this.releaseTheProduct = function(chave, CDFILIAL, NRVENDAREST, NRCOMANDA, selectedProducts, printer){

		var produtos = JSON.stringify(selectedProducts);

		var query = Query.build()
						.where('chave').equals(chave)
						.where('CDFILIAL').equals(CDFILIAL)
						.where('NRVENDAREST').equals(NRVENDAREST)
						.where('NRCOMANDA').equals(NRCOMANDA)
						.where('produtos').equals(produtos)
						.where('printer').equals(printer);
		return ReleaseProductRepository.download(query);
	};

	this.splitProducts = function(chave, NRVENDAREST, NRCOMANDA, NRPRODCOMVEN, positions){

		var query = Query.build()
						.where('chave').equals(chave)
						.where('NRVENDAREST').equals(NRVENDAREST)
						.where('NRCOMANDA').equals(NRCOMANDA)
						.where('NRPRODCOMVEN').equals(NRPRODCOMVEN)
						.where('NRLUGARMESA').equals(positions);
		return SplitProductsRepository.download(query);
	};

	this.cancelSplitedProducts = function(chave, NRVENDAREST, NRCOMANDA, NRPRODCOMVEN){

		var query = Query.build()
						.where('chave').equals(chave)
						.where('NRVENDAREST').equals(NRVENDAREST)
						.where('NRCOMANDA').equals(NRCOMANDA)
						.where('NRPRODCOMVEN').equals(NRPRODCOMVEN);
		return CancelSplitedProductsRepository.download(query);
	};

	this.generatePositionCode = function (chave, NRVENDAREST, NRCOMANDA, position){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('NRVENDAREST').equals(NRVENDAREST)
						.where('NRCOMANDA').equals(NRCOMANDA)
						.where('position').equals(position);
		return PositionCodeRepository.download(query);
	};

	this.changeTableStatus = function(chave, NRVENDAREST, NRCOMANDA, status){
		// altera o status da mesa para Recebimento
		var query = Query.build()
						.where('chave').equals(chave)
						.where('NRVENDAREST').equals(NRVENDAREST)
						.where('NRCOMANDA').equals(NRCOMANDA)
						.where('STATUS').equals(status);
		return TableChangeStatus.download(query);
	};

    this.positionControl = function (NRVENDAREST, position, unselecting, positions){
        var query = Query.build()
                        .where('NRVENDAREST').equals(NRVENDAREST)
                        .where('position').equals(position)
                        .where('unselecting').equals(unselecting)
                        .where('positions').equals(positions);
        return PositionControlRepository.download(query);
    };

}

Configuration(function(ContextRegister){
	ContextRegister.register('TableService', TableService);
});

// FILE: js/services/TransactionsService.js
function TransactionsService(Query, TransactionsRepository, SendEmailTransaction, UpdateTransactionEmail, FindRowToCancel, UpdateCanceledTransaction, TransactionsMoveTransactions){
	
	this.findTransaction = function(DTHRFIMMOVini, DTHRFIMMOVfim, NRADMCODE){
		var query = Query.build()
						.where('DTHRFIMMOV').equals(DTHRFIMMOVini)
						.where('DTHRFIMMOV').equals(DTHRFIMMOVfim)
						.where('NRADMCODE').equals(NRADMCODE);
		return TransactionsRepository.download(query);
	};
	
	this.sendTransactionEmail = function(NRSEQMOVMOB, DSEMAILCLI){
		var query = Query.build()
						.where('NRSEQMOVMOB').equals(NRSEQMOVMOB)
						.where('DSEMAILCLI').equals(DSEMAILCLI);
		return SendEmailTransaction.download(query);
	};
	
	this.updateTransactionEmail = function(DSEMAILCLI, NRSEQMOVMOB){
		var query = Query.build()
						.where('NRSEQMOVMOB').equals(NRSEQMOVMOB)
						.where('DSEMAILCLI').equals(DSEMAILCLI);
		return UpdateTransactionEmail.download(query);
	};
	this.findRowToCancel = function(chave, NRSEQMOVMOB){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('NRSEQMOVMOB').equals(NRSEQMOVMOB);
		return FindRowToCancel.download(query);
	};
	this.updateCanceledTransaction = function(NRSEQMOVMOB){
		var query = Query.build()
						.where('NRSEQMOVMOB').equals(NRSEQMOVMOB);
		return UpdateCanceledTransaction.download(query);
	};
	
	this.moveTransactions = function(chave, NRVENDAREST, NRCOMANDA, NRLUGARMESA, positions){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('NRVENDAREST').equals(NRVENDAREST)
						.where('NRCOMANDA').equals(NRCOMANDA)
						.where('NRLUGARMESA').equals(NRLUGARMESA)
						.where('positions').equals(positions);
		return TransactionsMoveTransactions.download(query);
		
	};
}

Configuration(function(ContextRegister) {
	ContextRegister.register('TransactionsService', TransactionsService);
});

// FILE: js/services/UtilitiesService.js
function UtilitiesService(OperatorRepository, ScreenService, templateManager, ApplicationContext, eventAggregator, ConfigIpRepository, ZHPromise, PermissionService, WindowService, UtilitiesTest, UtilitiesRequestsRepository, Query, ValidationEngine, SaveLogin,AccountCart) {

	var self = this;

	window.ApplicationContext = ApplicationContext;

	var memoryStorageAsync = new MemoryStorageAsync(ZHPromise);
	var originalClosePopup = ScreenService.closePopup;

	ScreenService.closePopup = function (all) {
		originalClosePopup(all);
		self.onPopUpClose();
	};

	var flow = {
		"menu": function () {
			var menuWidget = templateManager.container.getWidget("menu");
			if (menuWidget) {
				menuWidget.activate();
				templateManager.container.restoreDefaultMode();
			}
		}
	};

	// localforage.setItem = memoryStorageAsync.setLocalVar;
	// localforage.getItem = memoryStorageAsync.getLocalVar;
	// localforage.removeItem = memoryStorageAsync.removeItem;

	this.onPopUpClose = function () {
		var containerName = "";
		if (templateManager.container) {
			containerName = templateManager.container.name;
		}
		var toDo = flow[containerName] || false;
		if (toDo) {
			toDo();
		}
	};


	/* Habilite para começar a fazer o log do backEnd. */
	this.debugEnabled = false;

	/* *************************** */

	/* ****** LOG DE REQUISIÇÕES ****** */
	var pendingRequests = {};

	eventAggregator.onRequestRetry(function (data) {
		ScreenService.changeLoadingMessage("Tentando novamente... (" + data.requestCount + " de " + data.retryCount + ")");
	});

	eventAggregator.onRequestSuccess(function (data) {
		ScreenService.changeLoadingMessage("Aguarde...");
	});

	eventAggregator.onRequestError(function (data) {
		ScreenService.changeLoadingMessage("Aguarde...");
		$('.zh-container-alert').addClass('alert-red');

		$('.zh-footer-alert').click(function () {
			$('.zh-container-alert').removeClass('alert-red');
		});

		// Validação para evitar mensagem durante a configuração de IP.
		if ((templateManager.container.name !== "login") && (templateManager.container.name !== "billLogin") && (data.data.config.data.origin.widgetName !== "serverIpWidget")) {
			var message = '';
			var consoleMessage = _.get(data, 'data.data.error');
			if (consoleMessage) {
				console.log(consoleMessage);
			}
			if (templateManager.container.name == "loginContainer") {
				message = 'Ocorreu um erro ao estabelecer a conexão com o servidor. Certifique-se que o IP foi corretamente configurado e tente novamente.';
				ScreenService.showMessage(message, 'error');
			} else {
				message = 'Ocorreu um erro ao estabelecer a conexão com o servidor. Caso esteja demorando muito para exibir esta mensagem, verifique o sinal da sua rede.';
				if (consoleMessage) {
					message += '<br><br>' + consoleMessage;
				}
				ScreenService.showMessage(message, 'error');
			}
		}
	});

	if (this.debugEnabled) {
		eventAggregator.onRequestStart(function (data) {
			if (data.data.service !== '/UtilitiesRequestsRepository') {
				pendingRequests[data.index] = {
					"start": new Date().getTime()
				};
			}
		});

		eventAggregator.onRequestEnd(function (data) {
			if (data.data.method && pendingRequests[data.index]) {
				var currentRequest = pendingRequests[data.index];
				currentRequest.end = new Date().getTime();
				currentRequest.totalTime = currentRequest.end - currentRequest.start;
				currentRequest.backEndProcess = data.data.method[0].parameters[0] * 1000;
				currentRequest.latencyTime = currentRequest.totalTime - currentRequest.backEndProcess;
				currentRequest.index = data.index;
				self.sendRequestsToBack(pendingRequests);
			}
			delete pendingRequests[data.index];
		});
	}
	/* *************************** */

	/* CPF validation. */
	this.isValidCPF = function (cpf) {
		/* CPF validation. */
		var isValid = false;
		if (cpf) {
			cpf = cpf.replace('.', '').replace('.', '').replace('-', '');
			var total;
			var first, second;

			var invalidCpfs = [
				"00000000000",
				"11111111111",
				"22222222222",
				"33333333333",
				"44444444444",
				"55555555555",
				"66666666666",
				"77777777777",
				"88888888888",
				"99999999999"
			];

			if (invalidCpfs.indexOf(cpf) == -1) {
				total = 0;
				for (i = 1; i <= 9; i++) {
					total += parseInt(cpf.substring(i - 1, i)) * (11 - i);
				}
				first = (total * 10) % 11;
				if (first == 10 || first == 11) first = 0;

				total = 0;
				for (i = 1; i <= 10; i++) {
					total += parseInt(cpf.substring(i - 1, i)) * (12 - i);
				}
				second = (total * 10) % 11;

				if ((second == 10) || (second == 11)) second = 0;
				isValid = !(first != parseInt(cpf.substring(9, 10)) || second != parseInt(cpf.substring(10, 11)));
			} else {
				isValid = false;
			}
		}
		return isValid;
	};

	this.isValidCPForCNPJ = function (code) {
		if (code.length > 11) {
			return self.isValidCNPJ(code);
		} else {
			return self.isValidCPF(code);
		}
	};

	this.isValidCNPJ = function (cnpj) {
		cnpj = cnpj.replace(/[^\d]+/g, '');

		if (cnpj == '') return false;

		if (cnpj.length != 14)
			return false;

		// Elimina CNPJs invalidos conhecidos
		if (cnpj == "00000000000000" ||
			cnpj == "11111111111111" ||
			cnpj == "22222222222222" ||
			cnpj == "33333333333333" ||
			cnpj == "44444444444444" ||
			cnpj == "55555555555555" ||
			cnpj == "66666666666666" ||
			cnpj == "77777777777777" ||
			cnpj == "88888888888888" ||
			cnpj == "99999999999999")
			return false;

		// Valida DVs
		tamanho = cnpj.length - 2;
		numeros = cnpj.substring(0, tamanho);
		digitos = cnpj.substring(tamanho);
		soma = 0;
		pos = tamanho - 7;
		for (i = tamanho; i >= 1; i--) {
			soma += numeros.charAt(tamanho - i) * pos--;
			if (pos < 2)
				pos = 9;
		}
		resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
		if (resultado != digitos.charAt(0))
			return false;

		tamanho = tamanho + 1;
		numeros = cnpj.substring(0, tamanho);
		soma = 0;
		pos = tamanho - 7;
		for (i = tamanho; i >= 1; i--) {
			soma += numeros.charAt(tamanho - i) * pos--;
			if (pos < 2)
				pos = 9;
		}
		resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
		if (resultado != digitos.charAt(1))
			return false;

		return true;
	};

	ValidationEngine.registerValidation('CPF', function (cpfValue) {
		return {
			'valid': self.isValidCPF(cpfValue) || !cpfValue,
			'message': 'CPF inválido'
		};
	});

	this.setServerIp = function (row, widget) {
		if (!(row.ip)) {
			ScreenService.showMessage('Informe o IP.');
		} else {
			var ip = row.ip.split('://').pop();
			var port = (row.porta) ? (":" + row.porta) : "";
			var ipForTest = "http://" + ip + port + self.getBackendPath(ip);

			// URL para testar (HTTP).
			templateManager.updateURL(ipForTest);
			self.testConnection().then(
				function () {
					setIp('http', ip, port).then(function () {
						SaveLogin.clearAll();
						widget.reload();
						ScreenService.closePopup();
					});
				},
				function () {
					// URL para testar (HTTPS).
					ipForTest = "https://" + ip + port + self.getBackendPath(ip);
					templateManager.updateURL(ipForTest);
					self.testConnection().then(
						function () {
							setIp('https', ip, port).then(function () {
								SaveLogin.clearAll();
								widget.reload();
								ScreenService.closePopup();
							});
						},
						function () {
							// Nenhum dos protocolos funcionaram.
							ScreenService.confirmMessage(
								"Não foi possível conectar ao servidor. Deseja manter o ip informado?",
								"question",
								function () {
									setIp('http', ip, port).then(function () {
										SaveLogin.clearAll();
										widget.reload();
										ScreenService.closePopup();
									});
								},
								function () { }
							);
						});
				});
		}
	};

	var setIp = function (protocol, ip, port) {

		var treatedProtocol = protocol + "://";
		var treatedPort = port.replace(":", "");
		var ipCompleto = treatedProtocol + ip + port + self.getBackendPath(ip);
		templateManager.updateURL(ipCompleto);

		var configIp = {
			ipCompleto: ipCompleto,
			ipSemPorta: ip,
			protocol: protocol,
			port: treatedPort,
			ipForBack: treatedProtocol + ip + port
		};

		var defer = ZHPromise.defer();
		ConfigIpRepository.clearAll().then(function () {
			ConfigIpRepository.save(configIp).then(function () {
				defer.resolve();
			});
		});
		return defer.promise;
	};

	this.validateIp = function () {
		var defer = ZHPromise.defer();

		this.checkSetLocalVar().then(function () {
			defer.resolve();
		}, function () {
			defer.reject("Configure o IP do servidor da aplicação.");
		});

		return defer.promise;
	};

	this.checkSetLocalVar = function () {
		var defer = ZHPromise.defer();

		ConfigIpRepository.findOne().then(function (configIp) {
			if (configIp) {
				templateManager.updateURL(configIp.ipCompleto);
				defer.resolve();
			} else {
				defer.reject();
			}
		}, function () {
			defer.reject();
		});

		return defer.promise;
	};

	var __init = function () {
		window.__back__ = function () { };
	};

	this.changeIndex = function (widget) {
		__init(); //remove zeedhi behavior.
		var image = widget.getField('logo-waiter');
		if (projectConfig.currentMode === modosWaiter.order.codigo) {
			ApplicationContext.OrderController.showOrderLogin();
		} else if (projectConfig.currentMode === modosWaiter.comanda.codigo) {
			image.source = image.sourceFastPass;
		} else {
			image.source = image.sourceWaiter;
		}
	};

	this.toCurrency = function (number) {
		if (typeof number == 'string') {
			number = parseInt(number);
		}
		return number.toFixed(2).replace('.', ',');
	};

	this.removeCurrency = function (value) {
		if (typeof value == 'string') {
			value = parseFloat(value.split('.').join('').replace(',', '.').replace('R$', ''));
		}
		return value;
	};

	this.formatFloat = function (floatNumber) {
		return parseFloat(floatNumber).toFixed(2).replace(".", ",");
	};

	this.showServidor = function (widgetToShow) {
		ScreenService.changeFilter(widgetToShow);
	};

	this.backMainScreen = function () {
		OperatorRepository.findAll().then(function (operatorData) {
			modoHabilitado = operatorData[0].modoHabilitado;

			if (modoHabilitado === 'M') {
				WindowService.openWindow('TABLES_SCREEN');
			} else if (modoHabilitado === 'C') {
				WindowService.openWindow('BILLS_SCREEN');
			} else if (modoHabilitado === 'O') {
				WindowService.openWindow('ORDER_MENU_SCREEN');
			} else if (modoHabilitado === 'B') {
				WindowService.openWindow('MENU_SCREEN');
			} else if (modoHabilitado === 'D') {
				WindowService.openWindow('DELIVERY_ORDERS_SCREEN');
			}
		});
	};

	this.backLoginScreen = function () {
		OperatorRepository.findAll().then(function (operatorData) {
			if (operatorData[0].modoHabilitado !== 'O') {
				ScreenService.openWindow(templateManager.containers.zeedhi_project.mainWindow);
			} else {
				WindowService.openWindow('ORDER_LOGIN_SCREEN');
			}
		});
	};

	this.handleBack = function (activePage) {
		if (activePage === 'menu' || activePage === 'sendWaiterless') {
			this.backMainScreen();
		} else if (activePage === 'checkPromo') {
			WindowService.openWindow('PROMO_SCREEN');
		} else if (activePage === 'orderCheckOrder') {
			WindowService.openWindow('ORDER_MENU_SCREEN');
		} else if (activePage === 'orderProduct') {
			WindowService.openWindow('ORDER_MENU_SCREEN');
		} else if (activePage === 'orderCloseAccount') {
			WindowService.openWindow('ORDER_MENU_SCREEN');
		} else {
			WindowService.openWindow('MENU_SCREEN');
		}
	};

	this.showAccessControl = function (widget) {
		ScreenService.changeFilter(widget);
	};

	this.showFunctions = function (widgetToShow) {
		ScreenService.openPopup(widgetToShow);
	};

	this.openPopup = function (widgetToShow) {
		ScreenService.openPopup(widgetToShow);
	};

	this.prepareServerForm = function (widgetToShow) {
		ConfigIpRepository.findOne().then(function (configIp) {
			var data = [];
			if (configIp) {
				data.porta = configIp.port;
				data.ip = configIp.ipSemPorta;
			}

			widgetToShow.setCurrentRow(data);

			ScreenService.openPopup(widgetToShow);
		});
	};

	var waiterFunctionsByMode = {
		'M': [
			'btnAccountDetails',
			'btnSendMessage',
			'btnCloseAccount',
			'btnCancelProduct',
			'btnChangePositions',
			'btnGroupTables',
			'btnTransfer',
			'btnCancelTableOpening',
			'btnReleaseProduct',
			'btnPositionCode',
			'btnAccountPayment',
			'btnAnticipatePayment',
			'btnSplitProducts'
		],
		'C': [
			'btnChangeTable',
			'btnAccountDetails',
			'btnSendMessage',
			'btnCancelProduct',
			'btnTransferProductComanda',
			'btnCloseAccount'
		],
		'B': [
			'btnSendMessage',
			'btnChangeConsumer'
		]
	};

	function getWaiterFunctions(widgetFuntions) {
		var waiterFunctions = [];
		_.each(widgetFuntions.fields, function (currentFunction) {
			waiterFunctions.push(currentFunction.name);
		});
		return waiterFunctions;
	}

	function hideFunctionsByMode(widgetFunctions, currentMode, waiterFunctions) {
		_.each(waiterFunctions, function (currentFunction) {
			var mustShow = _.indexOf(waiterFunctionsByMode[currentMode], currentFunction) >= 0;
			widgetFunctions.getField(currentFunction).isVisible = mustShow;
			widgetFunctions.getField(currentFunction).showOnForm = mustShow;
		});
	}

	function setActionsVisibilityByWaiterMode(widget, currentMode) {
		var actions = widget.actions;
		actions.forEach(function (action) {
			if (action.activeOnMode && !Util.isArray(action.activeOnMode)) {
				action.activeOnMode = Util.parseToArray(action.activeOnMode);
			}
			action.isVisible = !action.activeOnMode || ~action.activeOnMode.indexOf(currentMode);
		});
	}

	this.hideFunctions = function (widget) {
		var widgetFunctions = widget.container.getWidget('functions');
		var waiterFunctions = getWaiterFunctions(widgetFunctions);
		var btnCloseAccount = widgetFunctions.getField('btnCloseAccount');

		OperatorRepository.findOne().then(function (params) {

			var currentMode = params.modoHabilitado;

			setActionsVisibilityByWaiterMode(widget, currentMode);
			hideFunctionsByMode(widgetFunctions, currentMode, waiterFunctions);

			if (currentMode !== 'B') {
				var mustShowPayment = params.IDCOLETOR == 'C';

				//o campo abaixo foi temporariamente fixado em isVisible = false por estar causando confusão nos clientes
				widgetFunctions.getField('btnAnticipatePayment').isVisible = false;
				widgetFunctions.getField('btnAnticipatePayment').showOnForm = mustShowPayment;

				if (currentMode === 'C') {
					if (params.bloqComandaParcial === 'N') {
						btnCloseAccount.isVisible = false;
						btnCloseAccount.showOnForm = false;
					}

					btnCloseAccount.isVisible = !mustShowPayment;
					btnCloseAccount.showOnForm = !mustShowPayment;
					btnCloseAccount.label = 'Receber Comanda';
				} else if (currentMode === 'M') {
					widgetFunctions.getField('btnSplitProducts').isVisible = (params.IDLUGARMESA === 'S');
					widgetFunctions.getField('btnAccountPayment').isVisible = !mustShowPayment;
					widgetFunctions.getField('btnAccountPayment').showOnForm = !mustShowPayment;
					btnCloseAccount.label = 'Fechar Conta';
					if (params.NRATRAPADRAO > 0) {
						widgetFunctions.getField('btnReleaseProduct').isVisible = true;
						widgetFunctions.getField('btnReleaseProduct').showOnForm = true;
					} else {
						widgetFunctions.getField('btnReleaseProduct').isVisible = false;
						widgetFunctions.getField('btnReleaseProduct').showOnForm = false;
					}
				}
			}
			widgetFunctions.container.restoreDefaultMode();
		});
	};

	this.setHeader = function (container) {
		OperatorRepository.findAll().then(function (operatorData) {
			ApplicationContext.AccountController.getAccountData(function (accountData) {
				var inicio = container.label.substr(0, container.label.indexOf('<span')) || container.label;
				if (!_.isEmpty(accountData)) {
					if (operatorData[0].modoHabilitado === 'M') {
						inicio += '<span class="waiter-header-right">' + accountData[0].NMMESA + '</span>';
					} else if (operatorData[0].modoHabilitado === 'C') {
						inicio += '<span class="waiter-header-right"> Comanda ' + accountData[0].DSCOMANDA + ' - Mesa ' + accountData[0].NRMESA + '</span>';
					}
				}
				container.label = inicio;
			});
		});
	};

	this.checkEmail = function (email) {
		/* This regular expression is said to not work in some very specific cases, but it appears to be safe to use in our case. */
		return Boolean(email.match(/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/));
	};

	this.numberLock = function (numberField) {
		if (numberField.length > 4) numberField = numberField.substr(0, 4);
	};

	this.setFirstPositionButtonAsActive = function (widget) {
		var positionsField = widget.getField('positionswidget');
		if (positionsField.template.split("/")[6] == "waiter_position.html") {
			positionsField.position = 0;
		} else {
			positionsField._buttons = [];
			positionsField.position = undefined;
		}
		widget.reload();
	};

	this.setupPoynt = function (loginWidget) {
		templateManager.project.notifications[0].isVisible = false;

		if (self.isPoyntDevice()) {
			templateManager.project.notifications[1].isVisible = true;
			templateManager.container.showHeader = true;
			loginWidget.getField('poynt_email').isVisible = true;
			loginWidget.getField('poynt_phone').isVisible = true;
			loginWidget.getField('poynt_website').isVisible = true;
		} else {
			templateManager.container.showHeader = false;
		}
	};

	this.isPoyntDevice = function () {
		return window.navigator.userAgent.indexOf('Poynt') > -1;
	};

	this.getBackendPath = function (ip) {
		if (ip.indexOf("waiterpoynt.teknisa.com") > - 1) {
			return '/backend/index.php';
		}
		return projectConfig.serviceUrl;
	};

	this.setVersionLabel = function (widget) {
		var versionField = widget.getField('version');
		var version = projectConfig.frontVersion || '1.0.0';
		versionField.label = 'Versão ' + version;
	};

	this.testConnection = function () {
		var query = Query.build();
		return UtilitiesTest.download(query);
	};

	this.sendRequestsToBack = function (requests) {
		var query = Query.build()
			.where('requests').equals(requests);
		return UtilitiesRequestsRepository.download(query);
	};

	this.truncValue = function (value) {
		// trunca float para 2 casas decimais
		return parseFloat((String(value * 100).split('.')[0]) / 100);
	};

	this.floatFormat = function (value, size, toString) {
		if (size == null) size = 2;
		var a = Math.pow(10, size);
		var f = parseFloat(parseInt(value * a) / a);
		return toString ? f.toFixed(size).replace('.', ',') : f;
	};

	this.padLeft = function (value, pad, chr) {
		value = (typeof value) != 'string' ? String(value) : value;

		if (value.length < pad) {
			for (var i = pad - value.length; i > 0; i--) {
				value = chr + value;
			}
		}

		return value;
	};

	this.toNumber = function (field) {
		field.setValue(field.value().replace(/[^0-9 ]/g, ""));
	};

	this.callQRScanner = function () {
		return new Promise(function (resolve) {
			if (!!window.ZhCodeScan) {
				window.scanCodeResult = _.bind(self.qrCodeResult, self, resolve);
				ZhCodeScan.scanCode();
			} else if (!!window.cordova) {
				cordova.plugins.barcodeScanner.scan(
					function (result) {
						result.error = false;
						result.contents = result.text;
						resolve(result);
					},
					function (error) {
						var result = {};
						result.error = true;
						result.message = error;
						resolve(result);
					}
				);
			} else {
				resolve({
					'error': true,
					'message': 'Não foi possível chamar a integração. Sua instância não existe.'
				});
			}
		}.bind(this));
	};

	this.qrCodeResult = function (resolve, result) {
		resolve(JSON.parse(result));
	};

	this.getFloat = function (value) {
		return (typeof value) == 'string' ? parseFloat(value.replace(',', '.')) : value;
	};

	this.loginOnEnter = function (args) {
		if (!!window.cordova) {
			var logo = args.owner.getField('logo-waiter');
			logo.source = logo.source.replace('mobile', 'www');
			logo.sourceWaiter = logo.sourceWaiter.replace('mobile', 'www');
			logo.sourceFastPass = logo.sourceFastPass.replace('mobile', 'www');

			var GertecPrinter = cordova.plugins.GertecPrinter;
			if (!!GertecPrinter)
				GertecPrinter.printerInit();

			var KioskPOS = cordova.plugins.KioskPOS;
			if (!!KioskPOS) {
				self.kioskConfig(KioskPOS);
			} else {
				document.addEventListener('backbutton', function (e) { }, false);
			}
		}
	};

	this.kioskConfig = function (kiosk) {
		kiosk.isSetAsLauncher(function (isSetAsLauncher) {
			kiosk.setLockFlag(isSetAsLauncher);
		});

		var lastTimeBackPress = 0;
		document.addEventListener('backbutton', function (e) {
			e.preventDefault();
			e.stopPropagation();
			if (new Date().getTime() - lastTimeBackPress < 650) {
				kiosk.isInKiosk(function (isInKiosk) {
					var screenName = document.getElementsByClassName("zh-application-content")[0];
					screenName = screenName.baseURI.split("index.html#/")[1];

					if (isInKiosk && screenName === 'login') {
						self.openUnlockPopup();
					}
				});
			} else {
				lastTimeBackPress = new Date().getTime();
			}
		}, false);
	};

	this.openUnlockPopup = function () {
		var unlockDeviceWidget = templateManager.containers.login.getWidget("loginWidget").widgets[4];
		unlockDeviceWidget.newRow();
		unlockDeviceWidget.isVisible = true;
		ScreenService.hideLoader();
		ScreenService.openPopup(unlockDeviceWidget);
	};

	this.handleCloseKeyboard = function () {
		if (!!window.ZhNativeInterface) {
			ZhNativeInterface.closeKeyboard();
		} else if (!!window.cordova && !!window.Keyboard) {
			Keyboard.hide();
		}
	};

	this.validateDate = function (date) {
		date = date.split('/');

		var day = parseInt(date[0]);
		var month = parseInt(date[1]);
		var year = parseInt(date[2]);

		if (month >= 1 && month <= 12) {
			var february = self.leapYear(year) ? 29 : 28;
			var monthLength = [31, february, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

			if (day >= 1 && day <= monthLength[month - 1]) {
				if (year <= (new Date()).getFullYear())
					return true;
			}
		}
		return false;
	};

	this.leapYear = function (year) {
		if (year % 4 === 0) {
			if (year % 100 === 0) {
				if (year % 400 === 0) {
					return true;
				} else {
					return false;
				}
			} else {
				return true;
			}
		} else {
			return false;
		}
	};

	this.validateName = function (field) {
		field.setValue(field.value().replace(/[^A-Za-zÀ-ú'\s]/g, ""));
	};

	this.switchTemplateGroupProp = function (widget) {
		OperatorRepository.findOne().then(function (operatorData) {
			if (operatorData.modoHabilitado === 'C' || operatorData.modoHabilitado === 'O' || operatorData.modoHabilitado === 'B' || operatorData.IDLUGARMESA === 'N') {
				widget.template = '../../../../templates/widget/list-grouped-default.html';
				widget.groupProp = 'GRUPO';
			}
			else {
				widget.template = '../../../../templates/widget/list-grouped-position.html';
				widget.groupProp = 'posicao';
			}
		});
	};

	 this.backAfterFinish = function () {
    	OperatorRepository.findOne().then(function (operatorData) {
    		if (operatorData.modoHabilitado === 'B') {
    			AccountCart.remove(Query.build());
    		}

           	self.backMainScreen();
    	});
    };
}

Configuration(function (ContextRegister) {
	ContextRegister.register('UtilitiesService', UtilitiesService);
});

function WaiterOrdersCatCtrl($scope, templateManager, ScreenService, $rootScope, ApplicationContext) {
	var paging = new checkRows();
	$scope.openRow = function (row) {
		if (!row.DISABLED) {
			if (row && row[$scope.widget.categoryProperty] !== "next" && row[$scope.widget.categoryProperty] !== "prev") {
				$scope.widget.setCurrentRow(row);
				$scope.widget.dataSource.data.forEach(function (object) {
					object.selected = false;
				});
				row.selected = true;
			} else {
				paging[row[$scope.widget.categoryProperty]]();
			}
		}
	};

	function checkRows() {

		var clearVisibility = function (data) {
			data.forEach(function (object) {
				object.visible = false;
			});
			return data;
		},
			currentPage;

		this.init = function () {
			currentPage = 0;
			var init = false;
			var widget = $scope.widget;
			$scope.listener = $scope.$watch('widget.dataSource.data.length', function (data) {
				if (data) {
					widget.dataSource.data = clearVisibility(widget.dataSource.data);
					$scope.listener();
					that.update();
				}
			});
		};

		this.update = function () {

			var checkColumns = function () {
				//iPad: 4 columns, regardless of orientation.
				//return 4;
				//Computer:
				var width = $(window).width();
				return 4;
				/*
				if (width < 335) return 1;
				if (width < 462) return 2;
				if (width < 606) return 3;
				if (width < 750) return 4;
				if (width < 769) return 5;
				if (width < 950) return 4;
				if (width < 1134) return 5;
				return 6;*/
			},
				columns = checkColumns(),
				lines = $scope.widget.lines,
				perPage = columns * lines - 2,
				nextObj = {
					"CDGRUPO": "next",
					"class": "icon-waiter-next",
					"COLOR": "#969696",
					"color-active": "#ffffff",
					"visible": true
				},
				prevObj = {
					"CDGRUPO": "prev",
					"class": "icon-waiter-prev",
					"COLOR": "#969696",
					"color-active": "#ffffff",
					"visible": true
				},
				scopeData,
				firstPosition = currentPage * perPage,
				lastPosition = firstPosition + perPage + (currentPage === 0 ? 2 : 1);

			var i;
			if ($scope.widget.parent.name === 'smartPromo' || $scope.widget.parent.name === 'subPromo') {
				var selection = 0;
				for (i in $scope.widget.dataSource.data) {
					// Controls group visibility.
					$scope.widget.dataSource.data[i].visible = true;
					// Checks to see which group to highlight, based on the initialization function.
					if ($scope.widget.dataSource.data[i].SELECTED) selection = i;
				}
				// Highlights the appropriate group.
				$scope.widget.dataSource.data[selection].selected = true;
			}
			else {
				if ($scope.widget.dataSource.data) {
					scopeData = $scope.widget.dataSource.data.filter(function (object) {
						return object[$scope.widget.categoryProperty] !== "next" && object[$scope.widget.categoryProperty] !== "prev";
					});
					scopeData = clearVisibility(scopeData);
					for (i = firstPosition + (currentPage === 0 ? 0 : 1); i < lastPosition - (currentPage === 0 ? 1 : 0) && i < scopeData.length; i++) {
						scopeData[i].visible = true;
					}
					$scope.openRow(scopeData[firstPosition + (currentPage === 0 ? 0 : 1)]);
					if (lastPosition <= scopeData.length) {
						scopeData.splice(lastPosition, 0, nextObj);
					}
					if (currentPage !== 0) {
						scopeData.splice(firstPosition, 0, prevObj);
					}
					$scope.widget.dataSource.data = scopeData;
				}
			}
		};

		this.next = function () {
			currentPage++;
			that.update();
		};
		this.prev = function () {
			currentPage--;
			that.update();
		};

		var that = this;


	}

	angular.element(document).ready(function () {
		$scope.$watch('$rootScope.searchList', function (newData) {
			ScreenService.filterWidget($scope.widget, $scope.widget.parent.widgets, newData);
		});
		paging.init();
		angular.element(window).bind('resize', function () {
			//paging.update();
		});
	});
	$scope.idealTextColor = idealTextColor;
}

function idealTextColor(bgColor) {
	var nThreshold = 105;
	var bgDelta = 0;
	if (bgColor) {
		var components = getRGBComponents(bgColor);
		bgDelta = (components.R * 0.299) + (components.G * 0.587) + (components.B * 0.114);
	} else {
		bgDelta = 0;
	}
	return ((255 - bgDelta) < nThreshold) ? "#000" : "#fff";
}

function getRGBComponents(color) {
	var r = color.substring(1, 3);
	var g = color.substring(3, 5);
	var b = color.substring(5, 7);

	return {
		R: parseInt(r, 16),
		G: parseInt(g, 16),
		B: parseInt(b, 16)
	};
}

function WaiterFieldListGroupedController($scope, ApplicationContext) {
	$scope.lineClick = function () {
		var fn = $scope.field.click || $scope.field.touchstart;
		if (fn) fn({ data: $scope.row });
	};
}

function WaiterGroupController($scope, ApplicationContext) {
	var imagePath = "bower_components/zeedhi-frontend/assets/images/icons/{0}.svg";
	$scope.selectTable = function (table, positions, abreComanda) {
		if (table.mode === 'list') {
			ApplicationContext.TableController.selectTable(table, positions, abreComanda);
		} else {
			ApplicationContext.TableController.checkTable(table);
		}
	};
	$scope.getImage = function (src) {
		src = !!src ? src : 'empty';
		return imagePath.replace("{0}", src);
	};
	$scope.__getFilter = function (table) {
		var filter = {};
		if ($scope.searchList) {
			filter[$scope.tableField.filterProp] = $scope.searchList;
			$scope._strict_ = false;
		}
		return filter;
	};
	$scope.setTheTable = function (selectedTableData, container) {
		ApplicationContext.TableController.setTheTable(selectedTableData.NRMESA, container);
	};
}




// FILE: js/services/WaiterNamedPositionsState.js
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

// FILE: js/controllers/AccountController.js
function AccountController(ZHPromise, OperatorRepository, AccountCart, AccountGetAccountItems, AccountGetAccountDetails,
	ParamsMenuRepository, TableActiveTable, ParamsObservationsRepository, AccountService, ParamsGroupRepository, SmartPromoRepository,
	ScreenService, Query, $rootScope, AccountSavedCarts, ApplicationContext, templateManager, ParamsProdMessageCancelRepository,
	TableService, BillActiveBill, UtilitiesService, SmartPromoGroups, SmartPromoProds, SmartPromoTray, ParamsParameterRepository,
	OperatorService, TotalCartRepository, AccountLastOrders, TimestampRepository, TransactionsService, PaymentService,
	WindowService, WaiterNamedPositionsState, PermissionService, CartPool, ParamsPriceTimeRepository, metaDataFactory, PrinterService,
	SubPromoGroups, SubPromoProds, SubPromoTray, PaymentRepository, ParamsMensDescontoObs, SellerControl, CarrinhoDesistencia, ProdutosDesistencia,
	BillService, PerifericosService, ProdSenhaPed) {

	var self = this;
	var allObservations = [];
	var observationMap = [];
	var NRSEQMOVMOB = ""; // PK da tabela de pagamento
    var selectControl = [];
    var trayClone;

	var Mode = {
		TABLE: 'M',
		ORDER: 'O',
		BILL: 'C',
		BALCONY: 'B'
	};

	var Param = {
		YES: 'S',
		NO: 'N'
	};

	this.updateObservationsInner = function (callback) {
		ParamsObservationsRepository.findAll().then(function (obs) {
			allObservations = obs;
			if (callback)
				callback();
		});
	};

	// always do this the first time
	this.updateObservationsInner();

	this.loadCartData = function (widget) {
		//widget.container.restoreDefaultMode();
		this.updateObservationsInner();
	};

	var orderCode = "";
	var userKey = null;

	this.buildOrderCode = function () {
		var defer = ZHPromise.defer();
		var resolver = function (operatorData) {
			var millisec = new Date().getTime();
			orderCode = millisec + "K" + operatorData.chave;
			userKey = operatorData.chave;
			defer.resolve();
		};
		if (userKey) {
			resolver({ chave: userKey });
		} else {
			OperatorRepository.findOne().then(resolver, function () {
				defer.reject();
			});
		}
		return defer.promise;
	};

	var cancelObservations = [];

	var updateCancelObservationsInner = function (callback) {
		ParamsProdMessageCancelRepository.findAll().then(function (obs) {
			cancelObservations = obs;
			if (callback)
				callback();
		});
	};

	updateCancelObservationsInner();

	this.log = function (stuff) {
		console.log(stuff);
	};

	this.getAccountData = function (callback) {
		var defer = ZHPromise.defer();
		OperatorRepository.findOne().then(function (operatorData) {
			var accountData;
			if ((operatorData.modoHabilitado === 'M') || (operatorData.modoHabilitado === 'O')) {
				TableActiveTable.findAll().then(function (tableData) {
					defer.resolve(callback(tableData));
				});
			} else if (operatorData.modoHabilitado === 'C') {
				BillActiveBill.findAll().then(function (billData) {
					defer.resolve(callback(billData));
				});
			} else if (operatorData.modoHabilitado === 'B') {
				defer.resolve(callback([]));
			}
		});
		return defer;
	};

	this.validateCart = function (args) {
		AccountCart.findAll().then(function (items) {
			if (items.length > 0) {
				ScreenService.confirmMessage(
					'Deseja cancelar o pedido e voltar para a página principal?',
					'question',
					function () {
						self.accountCartClear();
					},
					function () { }
				);
			} else {
				UtilitiesService.backMainScreen();
			}
		});
	};

	this.setGroupHeader = function (args) {
		var str = "Selecione ";
		var quantMIN = args.row.QTPRGRUPROMIN || 0;
		var quantMAX = args.row.QTPRGRUPPROMOC || 0;
		if (quantMIN >= quantMAX) {
			if (quantMAX <= 0)
				str += " os Produtos";
			else if (quantMAX > 1)
				str += parseInt(quantMAX).toString() + " Produtos";
			else
				str += parseInt(quantMAX).toString() + " Produto";
		} else {
			if (quantMAX > 1)
				str += "entre " + parseInt(quantMIN).toString() + " e " + parseInt(quantMAX).toString() + " Produtos";
			else
				str += "entre " + parseInt(quantMIN).toString() + " e " + parseInt(quantMAX).toString() + " Produto";
		}

		args.owner.container.label = str;
		UtilitiesService.setHeader(args.owner.container);
	};

	this.isVisibleAccountItems = function (container, modoHabilitado, IDLUGARMESA) {
		AccountGetAccountItems.findAll().then(function (accountItems) {
			var closeAccountItemsTable = container.getWidget('closeAccountItemsTable');
			var closeAccountItemsBill = container.getWidget('closeAccountItemsBill');
			//Verificação adicional se é feito o controle de posições no modo modo para troca de template dos itens na parcial de conta do fechamento de mesa.
			closeAccountItemsTable.isVisible = !Util.isEmptyOrBlank(accountItems) && modoHabilitado === 'M' && IDLUGARMESA === 'S';
			closeAccountItemsBill.isVisible = !Util.isEmptyOrBlank(accountItems) && (modoHabilitado === 'C' || (IDLUGARMESA === 'N' && modoHabilitado === 'M'));
			if (closeAccountItemsTable.isVisible) {
				self.refreshItems(closeAccountItemsTable);
			} else if (closeAccountItemsBill.isVisible) {
				self.refreshItems(closeAccountItemsBill);
			}
		});
	};

	this.switchTemplate = function (widget) {
		OperatorRepository.findOne().then(function (operatorData) {
			if (operatorData.modoHabilitado === 'C' || operatorData.modoHabilitado === 'O' || operatorData.modoHabilitado === 'B' || operatorData.IDLUGARMESA === 'N') {
				widget.template = '../../../../templates/widget/list-grouped-default.html';
			}
			else {
				widget.template = '../../../../templates/widget/list-grouped-position.html';
			}
		});
	};

	this.showSmartProds = function (owner) {
		if (owner.currentRow.composicao !== null && owner.currentRow.composicao.length > 0) {
			if (owner.widgetsBackup) owner.widgets = owner.widgetsBackup;
			owner.widgets[0].fields[0].dataSource.data = owner.currentRow.composicao;
			owner.widgets[0].isVisible = true;
		}
		else {
			if (owner.widgets) {
				owner.widgetsBackup = owner.widgets;
				delete owner.widgets;
			}
		}
	};

	this.refreshItems = function (widget) {
		delete widget.dataSource.data;
		widget.currentRow = {};
		AccountGetAccountItems.findAll().then(function (data) {
			for (var i in data) {
				if (parseFloat(data[i].quantidade.replace(',', '.')) != 1) {
					data[i].DSBUTTON = data[i].quantidade + " x " + data[i].DSBUTTON;
				}
			}
			widget.dataSource.data = data;
		});
	};

	this.resetCartLabel = function (cartAction) {
		AccountCart.findAll().then(function (products) {
			if (products.length > 0) {
				cartAction.hint = products.length;
			} else {
				cartAction.hint = '';
			}
		});
	};

	/* Checks to see if the cart is empty, preventing the user from needlessly entering the checkOrder page. */
	this.showCart = function () {
		AccountCart.findAll().then(function (cartData) {
			CartPool.findAll().then(function (cartPool) {
				// valida se tem produtos no carrinho
				if (!(Util.isArray(cartData) && Util.isEmptyOrBlank(cartData)) || !(Util.isArray(cartPool) && Util.isEmptyOrBlank(cartPool))) {
					OperatorRepository.findOne().then(function (operatorData) {
						self.showCheckOrderScreen(operatorData);
					}.bind(this));
				} else {
					ScreenService.showMessage('Não há produtos no carrinho.');
				}
			}.bind(this));
		}.bind(this));
	};

	this.showCheckOrderScreen = function (operatorData) {
		if (operatorData.modoHabilitado === 'O') {
			return WindowService.openWindow('ORDER_CHECK_ORDER_SCREEN');
		} else {
			if (operatorData.modoHabilitado === 'C' || operatorData.modoHabilitado === 'O' || operatorData.modoHabilitado === 'B' || operatorData.IDLUGARMESA === 'N') {
				return WindowService.openWindow('CHECK_ORDER_SCREEN2');
			}
			else {
				return WindowService.openWindow('CHECK_ORDER_SCREEN');
			}
		}
	};

	this.controlVisible = function (widget) {
		ParamsParameterRepository.findAll().then(function (params) {
			OperatorRepository.findOne().then(function (operatorData) {
				var serviceAction = widget.getAction('alterservico');
				var swiservico = widget.getField('swiservico');
				var servico = widget.getField('servico');
				var lblservico = widget.getField('lblservico');
				var lblVendedorAbert = widget.getField('lblVendedorAbert');
				var nmVendedorAbert = widget.getField('NMVENDEDORABERT');
				var btncloseAccount = widget.getAction('BtncloseAccount');

				if (params[0].IDCOMISVENDA === 'N') {
					if (servico) {
						lblservico.disabled = true;
						servico.value = '0,00';
						servico.disabled = true;
						swiservico.isVisible = false;
						swiservico.value(false);
					}
					else {
						widget.getField('vlrservico').value = '0,00';
					}
					serviceAction.isVisible = false;
				}
				else {
					var changeCharge = widget.container.getWidget('changeCharge');

					lblservico.disabled = false;
					servico.disabled = false;
					swiservico.isVisible = true;
					serviceAction.isVisible = true;

					changeCharge.getField('radioChargeChange').applyDefaultValue();
					changeCharge.getField('radioCharge').applyDefaultValue();
					changeCharge.getField('radioCharge').isVisible = true;
					changeCharge.getField('TIPOGORJETA').applyDefaultValue();
					changeCharge.getField('TIPOGORJETA').isVisible = false;
					changeCharge.getField('vlrservico').applyDefaultValue();
					changeCharge.getField('vlrservico').isVisible = false;
					changeCharge.dataSource.data[0].value = null;
				}

				if (params[0].IDCONSUMAMIN === 'N') {
					var consumacao = widget.getField('consumacao');
					widget.getField('lblconsumacao').disabled = true;
					consumacao.disabled = true;
					widget.getField('swiconsumacao').isVisible = false;
					widget.getField('swiconsumacao').value(false);
					consumacao.value = '0,00';
				}
				else {
					widget.getField('lblconsumacao').disabled = false;
					widget.getField('consumacao').disabled = false;

					if (operatorData.modoHabilitado !== 'O') {
						widget.getField('swiconsumacao').isVisible = true;
					}
				}

				if (params[0].IDCOUVERART === 'N') {
					var couvert = widget.getField('couvert');
					widget.getField('lblcouvert').disabled = true;
					couvert.disabled = true;
					widget.getField('swicouvert').isVisible = false;
					widget.getField('swicouvert').value(false);
					couvert.value = '0,00';
				} else {
					widget.getField('lblcouvert').disabled = false;
					widget.getField('couvert').disabled = false;


					if (operatorData.modoHabilitado === 'O') {
						//Modo order não trabalha com estas switches
					} else {
						widget.getField('swicouvert').isVisible = true;
					}
				}

				if (operatorData.modoHabilitado === 'M') {
					lblVendedorAbert.isVisible = true;
					nmVendedorAbert.isVisible = true;
					swiservico.isVisible = true;
				} else if (operatorData.modoHabilitado === 'C') {
					lblVendedorAbert.isVisible = false;
					nmVendedorAbert.isVisible = false;
					swiservico.isVisible = false;
				}
			}.bind(this));
		});
	};

	this.recalcPrice = function (row) {
		var total = row.vlrprodutos;

		if (!row.vlrdesconto) {
			row.desconto = '0,00';
		} else {
			row.desconto = '' + UtilitiesService.formatFloat(row.vlrdesconto);
			total = Math.round((total - row.vlrdesconto) * 100) / 100;
		}

		if (!row.swiservico) {
			row.servico = '0,00';
		} else {
			row.servico = '' + UtilitiesService.formatFloat(row.vlrservico);
			total += row.vlrservico;
		}

		if (!row.swiconsumacao) {
			row.consumacao = '0,00';
		} else {
			row.consumacao = '' + UtilitiesService.formatFloat(row.vlrconsumacao);
			total = total + row.vlrconsumacao;
		}

		if (!row.swicouvert) {
			row.couvert = '0,00';
		} else {
			row.couvert = '' + UtilitiesService.formatFloat(row.vlrcouvert);
			total = total + row.vlrcouvert;
		}

		row.total = '' + UtilitiesService.formatFloat(total);
	};

	this.changeSwitch = function (widget, fieldName, accessName) {
		setTimeout(function () {
			OperatorRepository.findAll().then(function (operatorData) {
				if (operatorData[0][accessName] !== 'S') {
					if (operatorData[0][accessName] === 'C') {

						// bloqueia a mudança pra mudar manualmente caso o supervisor autorize
						widget.currentRow['swi' + fieldName] = !widget.currentRow['swi' + fieldName];

						PermissionService.checkAccess(accessName).then(function (CDSUPERVISOR) {
							widget.currentRow['swi' + fieldName] = !widget.currentRow['swi' + fieldName];
							widget.currentRow.CDSUPERVISOR = CDSUPERVISOR;
							this.recalcPrice(widget.currentRow);
						}.bind(this));
					}
					else if (operatorData[0][accessName] === 'N') {
						// não tem permissão
						ScreenService.showMessage("Você não possui permissão para realizar esta ação.");
						// bloqueia a mudança no switch
						widget.currentRow['swi' + fieldName] = !widget.currentRow['swi' + fieldName];
					}
				}
				else {
					widget.currentRow.CDSUPERVISOR = operatorData[0].CDOPERADOR;
					this.recalcPrice(widget.currentRow);
				}
				templateManager.onUpdate();
			}.bind(this));
		}.bind(this), 1);
	};

	this.openTableAndOrder = function (row, positionsField, container) {
		ApplicationContext.TableController.open(row, null, function () {
			self.order(container.getWidget('checkOrder'), null, null);
		}, positionsField);
	};

	this.orderPRODUTOS = function (PRODUTOS) {
		if (_.isNil(PRODUTOS)) {
			return null;
		} else {
			return PRODUTOS.sort(sortPRODUTOS);
		}
	};

	function sortPRODUTOS(a, b) {
		return a.ID > b.ID ? 1 : -1;
	}

	this.order = Util.buildDebounceMethod(function (widget, returnParam, saleProdPass) {
		SellerControl.findOne().then(function (CDVENDEDOR) {
			CDVENDEDOR = !_.isEmpty(CDVENDEDOR) ? CDVENDEDOR : null;
			OperatorRepository.findOne().then(function (operatorData) {
				if (operatorData.IDCAIXAEXCLUSIVO === 'N' && returnParam === true) SellerControl.clearAll();
				self.updateCart(widget, null).then(function () {
					self.returnRepository(operatorData).then(function (products) {
						isValidPrinterChoice(widget.dataSource.data).then(function () {
							ApplicationContext.OrderController.checkAccess(function () {
								if (widget.dataSource.data.length > 0) {
									var cartProducts = self.recoverSubPromos(products);
									var produtos = [];

									cartProducts.forEach(function (produto) {
										var qtd = ((produto.QTPRODCOMVEN === undefined) ? '1' : produto.QTPRODCOMVEN);

										if (produto.NRSEQIMPRLOJA === null) {
											produto.NRSEQIMPRLOJA = [];
											produto.NRSEQIMPRLOJA[0] = null;
										}

										/* Remove observações e atraso do produto pai antes de enviar. */
										if (!_.isEmpty(produto.PRODUTOS) && produto.IDIMPPRODUTO != '1') {
											produto.ATRASOPROD = 'N';
											produto.TOGO = 'N';
											produto.TXPRODCOMVEN = '';
										}
										else {
											produto.TXPRODCOMVEN = this.obsToText(produto.CDOCORR, produto.DSOCORR_CUSTOM);
										}

										var produtoMontado = {
											CDPRODUTO: produto.CDPRODUTO || null,
											DSBUTTON: produto.DSBUTTON || null,
											QTPRODCOMVEN: qtd || null,
											NRLUGARMESA: produto.POS || null,
											TXPRODCOMVEN: produto.TXPRODCOMVEN || null,
											CDOCORR: produto.CDOCORR || [],
											CUSTOMOBS: produto.DSOCORR_CUSTOM || null,
											VRPRECCOMVEN: produto.PRITEM || null,
											IDIMPPRODUTO: produto.IDIMPPRODUTO || null,
											IDTIPCOBRA: produto.IDTIPCOBRA || null,
											PRODUTOS: this.orderPRODUTOS(produto.PRODUTOS),
											DATA: produto.DATA || null,
											ID: produto.ID || null,
											UNIQUEID: produto.UNIQUEID || null,
											ATRASOPROD: produto.ATRASOPROD || null,
											TOGO: produto.TOGO || null,
											PRINTER: produto.NRSEQIMPRLOJA[0] || null,
											REFIL: produto.refilSet || false,
											NRCOMANDA: produto.NRCOMANDA || null,
											NRVENDAREST: produto.NRVENDAREST || null,
											VRPRECCLCOMVEN: produto.VRPRECITEMCL || null
										};

										produtos.push(produtoMontado);
									}.bind(this));

									this.getAccountData(function (accountData) {
										CartPool.findAll().then(function (cartPool) {
											AccountService.order(operatorData.chave, operatorData.modoHabilitado, cartPool, accountData[0].NRVENDAREST, produtos, orderCode, CDVENDEDOR, saleProdPass).then(function (orderResponse) {
												if (orderResponse[0].erro == '004') {
													TableActiveTable.findAll().then(function (activeTable) {
														ScreenService.confirmMessage("A " + activeTable[0].NMMESA + " não está aberta. Deseja reabrir a mesa e enviar o pedido?").then(
															function () {
																ApplicationContext.TableController.openTable(activeTable[0], widget.container.getWidget('openTable'), false);
															}
														);
													});
												}
												else {
													PerifericosService.print(orderResponse[0].paramsImpressora).then(function () {
														AccountCart.remove(Query.build()).then(function () {
															if (operatorData.continueOrdering) {
																CartPool.clearAll();
																operatorData.continueOrdering = false;
																OperatorRepository.save(operatorData);
																widget.groupProp = 'POSITION';
															}
															widget.dataSource.data = [];
															if (returnParam) {
																UtilitiesService.backMainScreen();
															}
															else {
																self.buildOrderCode().then(function () {
																	self.checkOrderReset(widget);
																}.bind(this));
															}
														}.bind(this));
													});
												}
											}.bind(this));
										}.bind(this));
									}.bind(this));
								} else {
									ScreenService.showMessage("Erro na transmissão do pedido. <br>Tente novamente.", "error");
									WindowService.openWindow('MENU_SCREEN');
								}
							}.bind(this));
						}.bind(this), function (err) {
							ScreenService.showMessage(err);
						});
					}.bind(this));
				}.bind(this));
			}.bind(this));
		}.bind(this));
	}, 1000, true);


	// Função responsável por verificar parametrização (IDSENHACUP) de senhas de produção nos pedidos.
	// S - Sequencial (Diário) ->Sequencial por filial zerado diariamente. / D - Digitada  -> Abre uma tela para operador digitar senha/pager.
	// A - Aleatória (5 dígitos) - Aleatório com cinco digitos. / L - Aleatória (3 dígitos) - Aleatório com três digitos.
	this.verificaSenhaProd = function (widget, returnParam) {
		OperatorRepository.findOne().then(function (operatorData) {
			AccountCart.findAll().then(function (accountCart) {
				modoHabilitado = operatorData.modoHabilitado;
				if (operatorData.IDSENHACUP === 'D') {
					accountCart = self.recoverSubPromos(accountCart);
					if (modoHabilitado === 'B' && Util.isArray(accountCart) && Util.isEmptyOrBlank(accountCart)) {
						ScreenService.showMessage('Não há produtos no carrinho.');
					}
					else {
						ScreenService.openPopup(widget.container.getWidget('setSaleNumber')).then(function () {
							self.handleShowProdPass(widget.container.getWidget('setSaleNumber'), returnParam, modoHabilitado);
						}.bind(this));
					}
				} else if (modoHabilitado !== 'B') {
					this.order(widget, returnParam, saleProdPass = null);
				} else {
					this.openPayment(true);
				}
			}.bind(this));
		}.bind(this));
	};

	this.handleShowProdPass = function (widgetSaleNumber, returnParam, modoHabilitado) {
		if (modoHabilitado !== 'B') {
			widgetSaleNumber.getAction('confirm').label = (returnParam === true) ? 'Concluir' : 'Transmitir';
			widgetSaleNumber.getField('prodSaleNumber').dataSource.data = [{ 'value': returnParam }];
		} else {
			widgetSaleNumber.getAction('confirm').label = 'Receber';
		}

		widgetSaleNumber.getField('prodSaleNumber').clearValue();
	};

	this.applySalePass = function(widget){
		OperatorRepository.findOne().then(function (operatorData) {
			field = widget.container.getWidget('setSaleNumber').getField('prodSaleNumber');
			if (!_.isEmpty(field.value())){
				saleProdPass = field.value();
				if (operatorData.modoHabilitado !== 'B') {
					returnParam = field.dataSource.data[0].value;
					this.order(widget, returnParam, saleProdPass);
				} else {
					ProdSenhaPed.remove(Query.build()).then(function () {
						ProdSenhaPed.save(saleProdPass).then(function () {
							this.openPayment(true);
						}.bind(this));
					}.bind(this));
				}
			} else {
				ScreenService.showMessage('Digite o número da Senha/Pager do pedido para continuar.');
			}
		}.bind(this));
	};

	this.returnRepository = function (operatorData) {
		return AccountCart.findAll().then(function (accountCart) {
			if (operatorData.modoHabilitado !== 'C') return accountCart;
			else {
				return CartPool.findAll().then(function (cart) {
					var products = !_.isEmpty(cart) ? cart : accountCart;
					return products;
				}.bind(this));
			}
		}.bind(this));
	};

	var isValidPrinterChoice = function (products) {
		var prodFunc = products.map(function (product) {
			return handlePrintersProductForRoom(product).then(function (printers) {
				var defer = ZHPromise.defer();

				if (printers.length > 1) {
					if (!product.NMIMPRLOJA || !product.NRSEQIMPRLOJA[0]) {
						defer.reject("Informe a impressora do produto " + product.DSBUTTON + "!");
					} else {
						defer.resolve();
					}
				} else {
					defer.resolve();
				}

				return defer.promise;
			});
		});

		return ZHPromise.all(prodFunc);
	};

	this.getOrder = function (nrmesa, nrcomanda, nrvendarest, callBack) {
		var query = Query.build()
			.where('NRMESA').equals(nrmesa)
			.where('NRCOMANDA').equals(nrcomanda)
			.where('NRVENDAREST').equals(nrvendarest);

		AccountLastOrders.findOne(query).then(function (order) {
			if (callBack) {
				callBack(order);
			}
		});
	};

	this.sortPositions = function (a, b) {
		if (a.NRLUGARMESA < b.NRLUGARMESA)
			return -1;
		if (a.NRLUGARMESA > b.NRLUGARMESA)
			return 1;
		return 0;
	};

	this.prepareCart = function (widget, stripe) {
		OperatorRepository.findOne().then(function (operatorData) {

			var totalProd = 0;

			var __prepareCart = (function () {
				var totalOrderPrice = 0;
				var orderedCart = [];
				widget.dataSource.data.reverse();
				widget.dataSource.data.forEach(function (product) {
					product.QTPRODCOMVEN = product.QTPRODCOMVEN ? product.QTPRODCOMVEN : 1;
					product.QTPRODCOMVEN = parseFloat(String(product.QTPRODCOMVEN).replace(',', '.'));
					var quantidade = product.qtty || product.QTPRODCOMVEN;

					if (operatorData.IDUTLQTDPED || product.IDPESAPROD === 'S') {
						product.DSBUTTONSHOW = quantidade.toFixed(product.IDPESAPROD === 'S' ? 3 : 2).replace('.', ',') +
							" x " + product.DSBUTTON;
					}
					// Transforma array de observação em texto para mostrar ao usuário.
					product.TXPRODCOMVEN = this.obsToText(product.CDOCORR, product.DSOCORR_CUSTOM);

					// Trata produtos da promoção inteligente e produto combinado.
					if (!_.isEmpty(product.PRODUTOS)) {
						if (product.IDIMPPRODUTO !== '1') {
							product.TXPRODCOMVEN = "";
						}
						product.PRODUTOS.reverse();
						product.PRODUTOS.forEach(function (comboProduct) {
							// Agrupa as observações dos filhos e coloca elas como se fossem do pai.
							var obs = this.obsToText(comboProduct.CDOCORR, comboProduct.DSOCORR_CUSTOM);
							if (obs.length > 0) {
								comboProduct.TXPRODCOMVEN = obs;
								product.TXPRODCOMVEN += " " + obs;
								product.TXPRODCOMVEN = product.TXPRODCOMVEN.trimLeft(); // Lazy fix for the line above.
							}
							else {
								comboProduct.TXPRODCOMVEN = "";
							}
							// Marca se o produto tem atraso.
							if (comboProduct.ATRASOPROD === 'Y') {
								product.ATRASOPROD = 'Y';
							}

							if (comboProduct.TOGO === 'Y') {
								product.TOGO = 'Y';
							}
							comboProduct.STRPRICE = (comboProduct.TOTPRICE).toFixed(2).replace('.', ',');
						}.bind(this));

						product.PRECO = UtilitiesService.formatFloat(product.PRITOTITEM);
					}

					totalOrderPrice += quantidade * product.PRITOTITEM;
					product.PRECO = parseFloat(product.PRITOTITEM).toFixed(2).replace('.', ',');
					// Coloca a quantidade na frente do preço.
					if (!(operatorData.modoHabilitado === 'O' || (product.QTPRODCOMVEN !== 1))) {
						product.POSITION_TO_SHOW = parseInt(product.POSITION.split(" ")[1]);
					}

					// Marca se o produto tem atraso.
					if (product.ATRASOPROD === 'Y') {
						product.holdText = 'SEGURA';
					} else {
						product.holdText = '';
					}

					if (product.TOGO === 'Y') {
						product.toGoText = 'PARA VIAGEM';
					} else {
						product.toGoText = '';
					}

					// Insere os produtos num array para que fiquem ordenados.
					orderedCart.splice(0, 0, product);
				}.bind(this));
				templateManager.updateTemplate();

				// STRIPE.
				this.getAccountData(function (accountData) {
					if (!_.isEmpty(accountData)) {
						if (accountData[0].CDCONSUMIDOR && _.get(accountData[0], 'DETALHES.BALANCE')) {
							// Coloca o valor do saldo no stripe.
							stripe.fields[2].label = UtilitiesService.toCurrency(parseFloat(accountData[0].DETALHES.BALANCE));
							stripe.fields[1].isVisible = true;
							stripe.fields[2].isVisible = true;
						}
						else {
							// Se não tiver consumidor associado à mesa/comanda, não mostra o saldo e esconde os labels.
							stripe.fields[1].isVisible = false;
							stripe.fields[2].isVisible = false;
						}
					} else {
						stripe.fields[1].isVisible = false;
						stripe.fields[2].isVisible = false;
					}
					// Coloca o valor total no stripe.
					stripe.fields[4].label = UtilitiesService.toCurrency(totalOrderPrice);
				}.bind(this));
			}).bind(this);

			if (allObservations.length === 0) this.updateObservationsInner(__prepareCart);
			else __prepareCart();

		}.bind(this));
	};

	this.obsToText = function (observations, custom) {
		var obss = [];
		if (observations) {
			obss = observations.map(function (observation) {
				return allObservations.filter(function (obs) {
					return obs.CDOCORR === observation;
				})[0].DSOCORR;
			}) || [];
		}
		if (custom) {
			obss.push(custom);
		}

		if (obss.length > 0)
			return obss.join("; ") + ";";
		else
			return obss.join("; ");
	};

	this.calcProductValue = function (product) {
		var comboProdValue = 0;
		var comboProdSubsidy = 0;
		var prodValue = 0;
		var prodSubsidy = 0;
		var qntProd = 0;
		var obsWithValue = Array();
		var currentObsWithValue = Array();

        try {
            if (_.isEmpty(product.IDTIPCOBRA)){
                // Promoção Inteligente.
                product.PRODUTOS.forEach(function(comboProduct){
                    comboProduct.TOTPRICE = comboProduct.PRICE;

                    currentObsWithValue = self.calcObsValue(comboProduct);
                    if (!_.isEmpty(currentObsWithValue)){
                        comboProduct.TOTPRICE += _.sum(currentObsWithValue);
                        obsWithValue = obsWithValue.concat(currentObsWithValue);
                    }

                    var total = parseFloat((comboProduct.QTPRODCOMVEN * (comboProduct.PRECO + comboProduct.VRPRECITEMCL + comboProduct.VRACRITVEND - comboProduct.VRDESITVEND)).toFixed(2));
                    if (total < 0.01){
                        throw comboProduct;
                    }

                    comboProdValue += total;
                    comboProdSubsidy += parseFloat(comboProduct.VRPRECITEMCL);
                });

                if (product.IDIMPPRODUTO === '1') {
                    prodValue = parseFloat((product.PRITEM + product.VRPRECITEMCL + product.VRACRITVEND - product.VRDESITVEND).toFixed(2));
                    prodSubsidy = product.VRPRECITEMCL;
                    qntProd = 1;
                } else {
                    prodValue = comboProdValue;
                    prodSubsidy = comboProdSubsidy;
                    qntProd = product.PRODUTOS.length;
                }
            }
            else {
                // Produto Combinado.
                var highestPrice = (_.maxBy(product.PRODUTOS, 'PRICE')).PRICE || 0;

                // Quantity adjust.
                var tamanho = product.PRODUTOS.length;
                var specialQuant = 0;
                var quantity = UtilitiesService.floatFormat(1 / tamanho);
                if (quantity * tamanho != 1){
                    specialQuant = quantity + UtilitiesService.floatFormat(1 - quantity * tamanho);
                }

                product.PRODUTOS.forEach(function(comboProduct){
                    comboProduct.VRPRECITEMCL = 0;

                    // Quantity set.
                    if (specialQuant > 0){
                        comboProduct.QTPRODCOMVEN = specialQuant;
                        specialQuant = 0;
                    }
                    else {
                        comboProduct.QTPRODCOMVEN = quantity;
                    }

                    // Price set.
                    if (product.IDTIPCOBRA === 'C'){
                        comboProduct.PRICE = highestPrice;
                        comboProduct.PRECO = highestPrice.toFixed(3);
                        comboProduct.STRPICE = highestPrice.toFixed(2).replace('.', ',');
                    }
                    comboProduct.TOTPRICE = UtilitiesService.floatFormat(comboProduct.PRICE * comboProduct.QTPRODCOMVEN);
                    if (comboProduct.TOTPRICE < 0.01){
                        throw comboProduct;
                    }
                    comboProdValue += comboProduct.TOTPRICE;

                    // Additionals.
                    currentObsWithValue = self.calcObsValue(comboProduct);
                    if (!_.isEmpty(currentObsWithValue)){
                        comboProduct.TOTPRICE += _.sum(currentObsWithValue);
                        obsWithValue = obsWithValue.concat(currentObsWithValue);
                    }
                });

                prodValue = parseFloat(comboProdValue.toFixed(2));
                qntProd = product.PRODUTOS.length;
            }
        }
        catch (errProduct){
            return errProduct;
        }

		currentObsWithValue = self.calcObsValue(product);
		obsWithValue = _.isEmpty(currentObsWithValue) ? obsWithValue : obsWithValue.concat(currentObsWithValue);

		product.EXTRAS = _.sum(obsWithValue);
		product.PRITOTITEM = parseFloat((prodValue + product.EXTRAS).toFixed(2));
		product.VRPRECITEMCL = parseFloat(prodSubsidy.toFixed(2));
		product.REALSUBSIDY = 0;
		product.numeroProdutos = qntProd + obsWithValue.length;
        return null;
	};

	this.calcObsValue = function (product) {
		var obsWithValue = [];
		// seleciona as observações selecionadas que são cobradas
		var obsSelect = _.filter(this.getObservations(product.OBSERVATIONS), function (obsProd) {
			return _.includes(product.CDOCORR, obsProd.CDOCORR) && obsProd.IDCONTROLAOBS === 'A';
		});

		obsSelect.forEach(function (obs) {
			obsWithValue.push(parseFloat(obs.VRPRECITEM) + parseFloat(obs.VRPRECITEMCL));
		});

		return obsWithValue;
	};

	this.showFunctions = function (widgetToShow) {
		ScreenService.closePopup();
		ScreenService.openPopup(widgetToShow);
	};

	this.openPayment = function (allPositions) {
		var position = Array();
		WaiterNamedPositionsState.mustUnselect = false;
		OperatorRepository.findOne().then(function (params) {
			if (params.modoHabilitado !== 'B') {
				self.getAccountData(function (accountData) {
					accountData = accountData[0];
					AccountGetAccountDetails.findOne().then(function(accountDetails) {
						var validateAndPerformOpenPayment = function(positions) {
							AccountService.getPayments(accountData).then(function(payments){
								var consumer = self.getConsumerForPositions(positions, params.modoHabilitado);
								position = consumer.position.sort();

								if(parseInt(accountDetails.NRPESMESAVEN) === position.length)
									allPositions = true;

								if(!_.isEmpty(payments)) {
									var permission = self.validatePositionsPayment(position, payments, allPositions);

									if(!permission.continuePayment) {
										var firstPart = position.length > 1 ? "estas posições. " : "esta posição. ";
										var secondPart = '';

										if(permission.positionsInPayment[0] === 0 && !allPositions) {
											secondPart = 'Todas as posições estão';
										} else {
											secondPart = permission.positionsInPayment.length > 1;
											var positionsInPayment = _.join(permission.positionsInPayment, ', ');
											secondPart = secondPart ? "As posições " + positionsInPayment + " estão" :
												"A posição " + positionsInPayment + " está";
										}

										ScreenService.showMessage("Impossível receber " + firstPart + secondPart + " em recebimento.");
										return;
									} else {
										payments = _.isEmpty(permission.positionsInPayment) ? Array() : _.uniqBy(payments, 'CDNSUTEFMOB');
									}
								}

								if (accountDetails.length === 0 || accountDetails.vlrtotal <= 0) {
									ScreenService.showMessage('A conta já foi paga.');
									return;
								}
								if (!consumer.unique) {
									ScreenService.showMessage('Não é possível realizar o pagamento simultaneamente para posições com cliente/consumidor diferentes.', 'alert');
									return;
								}
								if (params.IDCOLETOR === 'C' && position.length > 1) {
									ScreenService.showMessage('Não é possível realizar o adiantamento para mais de uma posição simultaneamente.', 'alert');
									return;
								}
							    accountData.CREDITOPESSOAL = false;
							    if (!_.isNull(consumer.CDCLIENTE)) {
									accountData.CDCLIENTE = consumer.CDCLIENTE;
									accountData.NMRAZSOCCLIE = consumer.NMRAZSOCCLIE;
									accountData.CDCONSUMIDOR = consumer.CDCONSUMIDOR;
									accountData.NMCONSUMIDOR = consumer.NMCONSUMIDOR;
								}

								if(allPositions)
									position = Array();

								PaymentService.initializePayment(accountData, params, accountDetails, [], position, '', payments, null).then(function() {
									WindowService.openWindow('PAYMENT_TYPES_SCREEN');
								});
							});
						};

						if (allPositions) {
							AccountGetAccountItems.findAll().then(function (accountItems) {
								if (!_.isUndefined(accountItems[0]) && _.isArray(accountItems[0]))
									accountItems = accountItems[0];

								var positionFromItem = function (item) {
									return item.POS;
								};
								ParamsParameterRepository.findOne().then(function (params) {
									var positionsWithItems = params.IDCOUVERART === 'S' ? Array() : _.uniq(accountItems.map(positionFromItem));
									validateAndPerformOpenPayment(positionsWithItems);
								}.bind(this));
							}.bind(this));
						} else {
							var selectedPositions = accountDetails.posicao.map(function (p) { return parseInt(p); });
							validateAndPerformOpenPayment(selectedPositions);
						}
					});
				});
			} else {
				CarrinhoDesistencia.findAll().then(function (carrinhoDesistencia) {
					ProdSenhaPed.findOne().then(function (prodSenhaPed) {
						AccountCart.findAll().then(function (accountCart) {

							accountCart = self.recoverSubPromos(accountCart);

							if (Util.isArray(accountCart) && Util.isEmptyOrBlank(accountCart)) {
								ScreenService.showMessage('Não há produtos no carrinho.');
							} else {
								var vlrtotal = 0;
								var totalSubsidy = 0;
								var numeroProdutos = 0;
								var nomeProduto = '';
								var produtosBloqueados = '';
								var contador = 0;

								var promises = [];

								promises.push(AccountService.verificaProdutosBloqueados(accountCart));
								promises.push(AccountService.calculaDescontoSubgrupo(accountCart));

								Promise.all(promises).then(function (result) {
									var produtosBloqueados = result[0];
									if (!_.isEmpty(produtosBloqueados)) {
										produtosBloqueados.forEach(function (produto) {
											nomeProduto = produto.NMPRODUTO;
											produtosBloqueados += produtosBloqueados == '' ? nomeProduto : ', ' + nomeProduto;
											contador += 1;
										});

										if (contador > 1) {
											ScreenService.showMessage('Produtos ' + produtosBloqueados + ' bloqueados.');
										}
										else {
											ScreenService.showMessage('Produto ' + produtosBloqueados + ' bloqueado.');
										}
									} else {
										accountCart = result[1];
										accountCart.forEach(function (product) {
											var productValue = product.PRITOTITEM * product.QTPRODCOMVEN;
											if (productValue != parseFloat(productValue.toFixed(2))) {
												if (!_.isEmpty(product.IDPESAPROD) && product.IDPESAPROD === 'S') {
													productValue = Math.trunc(productValue * 100) / 100;
												} else {
													productValue = parseFloat(productValue.toFixed(2));
												}
											}
											vlrtotal += productValue;
											totalSubsidy += Math.trunc(product.VRPRECITEMCL * product.QTPRODCOMVEN * 100) / 100;
											numeroProdutos += product.numeroProdutos;
										});

										var accountData = {
											"CREDITOPESSOAL": false
										};

										var accountDetails = {
											'vlrtotal': vlrtotal,
											'totalSubsidy': totalSubsidy,
											'realSubsidy': 0,
											'vlrservico': 0,
											'vlrcouvert': 0,
											'vlrdesconto': 0,
											'fidelityDiscount': 0,
											'fidelityValue': 0,
											'vlrprodutos': vlrtotal,
											'numeroProdutos': numeroProdutos
										};

										PaymentService.initializePayment(accountData, params, accountDetails, accountCart, position, carrinhoDesistencia, null, prodSenhaPed).then(function () {
											WindowService.openWindow('PAYMENT_TYPES_SCREEN');
										});
									}
								});
							}
						});
					});
				});
			}
		});
	};

	this.validatePositionsPayment = function(position, payments, allPositions) {
		var payingPositions = Array();
		var groupedPayments = _.map(_.groupBy(payments, 'CDNSUTEFMOB'), function(value, key) { return value;});
		groupedPayments.forEach(function(groupedPayment){
			groupedPayment = _.map(groupedPayments[0], function(groupedPaymentAux){
			   return parseInt(groupedPaymentAux.NRLUGARMESA);
			});

			payingPositions.push(_.uniq(groupedPayment));
		});

		var permissionPayPosition = false;
		var positionsInPayment = Array();

		if(payingPositions[0] == 0) {
			permissionPayPosition = allPositions;
			positionsInPayment = payingPositions[0];
		} else {
			payingPositions.forEach(function(payingPosition) {
				if(_.isEqual(payingPosition, position) || _.isEmpty(_.intersection(payingPosition, position)))
					permissionPayPosition = true;
				else
					permissionPayPosition = false;

				if(!_.isEmpty(_.intersection(position, payingPosition)))
					positionsInPayment = payingPosition;
			}.bind(this));
		}

		return {
			"continuePayment": permissionPayPosition,
			"positionsInPayment": positionsInPayment
		};
	};

    this.recoverSubPromos = function(cartData){
        var products = [];
        for (var x in cartData){
            if (cartData[x].PRODUTOS.length > 0){
                for (var i in cartData[x].PRODUTOS){
                    if (cartData[x].PRODUTOS[i].PRODUTOS.length > 0){
                        var subProduct = cartData[x].PRODUTOS.splice(i, 1);

                        for (var f in subProduct[0].PRODUTOS){
                            var obs = this.obsToText(subProduct[0].PRODUTOS[f].CDOCORR, subProduct[0].DSOCORR_CUSTOM);
                            if (obs.length > 0) {
                                subProduct[0].PRODUTOS[f].TXPRODCOMVEN = obs;
                                subProduct[0].PRODUTOS[f].TXPRODCOMVEN += " " + obs;
                                subProduct[0].PRODUTOS[f].TXPRODCOMVEN = subProduct[0].PRODUTOS[f].TXPRODCOMVEN.trimLeft();
                            }
                            else subProduct[0].PRODUTOS[f].TXPRODCOMVEN = "";
                        }

						subProduct[0].QTPRODCOMVEN = cartData[x].QTPRODCOMVEN;
						subProduct[0].NRCOMANDA = cartData[x].NRCOMANDA;
						subProduct[0].NRVENDAREST = cartData[x].NRVENDAREST;
						subProduct[0].NRSEQIMPRLOJA = cartData[x].NRSEQIMPRLOJA;
						subProduct[0].IDTIPOCOMPPROD = '3';
						products.push(subProduct[0]);

						cartData[x].PRITOTITEM -= subProduct[0].PRITOTITEM;
						cartData[x].numeroProdutos--;
					}
				}
			}
			products.push(cartData[x]);
		}

		return products;
	};

	this.handleConsumerPositionsOnPayment = function (getPositionFromData, accountDetails) {
		var fieldPosition = templateManager.container.getWidget('accountDetails').getField("positionsField");
		var CDCLIENTE = null;
		var NMRAZSOCCLIE = null;
		var CDCONSUMIDOR = null;
		var NMCONSUMIDOR = null;

		// seleciona pagamento para posição selecionada
		var selectedPositions = getPositionFromData ? accountDetails.posicao.map(function (p) { return parseInt(p); }) :
			fieldPosition.position.map(function (p) { return ++p; });

		if (!_.isEmpty(selectedPositions)) {
			var consumerPositionsData = fieldPosition.dataSource.data[0];

			if (!_.isEmpty(consumerPositionsData.clientMapping)) {
				var allPositions = _.keys(consumerPositionsData.clientMapping);
				var selectedPositionsIndex = _.filter(selectedPositions, function (position) {
					return _.includes(allPositions, position.toString());
				}.bind(this));

				if (selectedPositions.length == selectedPositionsIndex.length) {
					CDCLIENTE = consumerPositionsData.clientMapping[selectedPositions[0]].CDCLIENTE;
					NMRAZSOCCLIE = consumerPositionsData.clientMapping[selectedPositions[0]].NMRAZSOCCLIE;
					if (consumerPositionsData.consumerMapping[selectedPositions[0]]) {
						CDCONSUMIDOR = consumerPositionsData.consumerMapping[selectedPositions[0]].CDCONSUMIDOR;
						NMCONSUMIDOR = consumerPositionsData.consumerMapping[selectedPositions[0]].NMCONSUMIDOR;
					}

					for (var i = 1; i < selectedPositions.length; i++) {
						if (consumerPositionsData.clientMapping[selectedPositions[1]].CDCLIENTE != CDCLIENTE ||
							(consumerPositionsData.consumerMapping[selectedPositions[1]] &&
								consumerPositionsData.consumerMapping[selectedPositions[1]].CDCONSUMIDOR != CDCONSUMIDOR)) {
							CDCLIENTE = null;
							NMRAZSOCCLIE = null;
							CDCONSUMIDOR = null;
							NMCONSUMIDOR = null;
							i = selectedPositions.length;
						}
					}
				}
			}
		}

		return {
			'position': selectedPositions,
			'CDCLIENTE': CDCLIENTE,
			'NMRAZSOCCLIE': NMRAZSOCCLIE,
			'CDCONSUMIDOR': CDCONSUMIDOR,
			'NMCONSUMIDOR': NMCONSUMIDOR
		};
	};

	this.getConsumerForPositions = function (selectedPositions, modoHabilitado) {
		var consumer = {
			position: selectedPositions,
			unique: true,
			CDCLIENTE: null,
			NMRAZSOCCLIE: null,
			CDCONSUMIDOR: null,
			NMCONSUMIDOR: null
		};
		if (modoHabilitado === 'M') {
			var fieldPosition = templateManager.container.getWidget('accountDetails').getField("positionsField");
			var consumerPositionsData = fieldPosition.dataSource.data[0];
			var clientMapping = (consumerPositionsData.clientMapping && !_.isEmpty(consumerPositionsData.clientMapping)) ? consumerPositionsData.clientMapping : [];
			var consumerMapping = (consumerPositionsData.consumerMapping && !_.isEmpty(consumerPositionsData.consumerMapping)) ? consumerPositionsData.consumerMapping : [];
			var validateUniqueValueForKey = function (key, mapping, position) {
				var valueForPosition = mapping[position] ? mapping[position][key] : null;
				return consumer[key] === valueForPosition;
			};
			for (var i = 0; i < selectedPositions.length; i++) {
				var selectedPosition = selectedPositions[i];
				if (i == 0) {
					if (clientMapping[selectedPosition]) {
						consumer.CDCLIENTE = clientMapping[selectedPosition].CDCLIENTE;
						consumer.NMRAZSOCCLIE = clientMapping[selectedPosition].NMRAZSOCCLIE;
					}
					if (consumerMapping[selectedPosition]) {
						consumer.CDCONSUMIDOR = consumerMapping[selectedPosition].CDCONSUMIDOR;
						consumer.NMCONSUMIDOR = consumerMapping[selectedPosition].NMCONSUMIDOR;
					}
				} else {
					var clientIsUnique = validateUniqueValueForKey('CDCLIENTE', clientMapping, selectedPosition);
					var consumerIsUnique = validateUniqueValueForKey('CDCONSUMIDOR', consumerMapping, selectedPosition);
					if (!clientIsUnique || !consumerIsUnique) {
						consumer.unique = false;
						break;
					}
				}
			}
		}
		return consumer;
	};

	this.backPayment = function () {
		OperatorRepository.findOne().then(function (operatorData) {
			var message = '';
			if (operatorData.IDCOLETOR !== 'C') {
				message = 'Pagamento não finalizado. Deseja continuar?';
			}
			else {
				message = 'Deseja sair do adiantamento?';
			}

			ScreenService.confirmMessage(
				message,
				'question',
				function () {
					self.getAccountData(function (accountData) {
						TableService.changeTableStatus(operatorData.chave, accountData[0].NRVENDAREST, accountData[0].NRCOMANDA, 'S').then(function (response) {
							UtilitiesService.backMainScreen();
						}.bind(this));
					}.bind(this));
				}.bind(this),
				function () { }
			);
		}.bind(this));
	};

	var isSmartPromo = function (product) {
		return product.IDTIPOCOMPPROD == '3';
	};

	var buildCartItem = function (product, position, refilSet) {
		return self.getAccountData(function (accountData) {
			return self.getOrderCodeProductID().then(function (id) {
				var time = new Date();
				var dscomanda = '';

				if (!_.isEmpty(accountData)) {
					dscomanda = accountData[0].LABELDSCOMANDA;
				}

				var cartItem = {
					ID: id,
					UNIQUEID: id,
					GRUPO: product.NMGRUPO,
					CDPRODUTO: product.CDPRODUTO,
					DSBUTTON: product.DSBUTTON,
					DSBUTTONSHOW: product.DSBUTTON,
					POSITION: "posição " + position,
					POS: position,
					PRECO: product.PRECO,
					PRITEM: product.PRITEM,
					PRITOTITEM: parseFloat((product.PRITEM + product.VRPRECITEMCL + product.VRACRITVEND - product.VRDESITVEND).toFixed(2)),
					VRPRECITEMCL: product.VRPRECITEMCL,
					REALSUBSIDY: 0,
					VRDESITVEND: product.VRDESITVEND,
					VRACRITVEND: product.VRACRITVEND,
					CDOCORR: [],
					IDIMPPRODUTO: product.IDIMPPRODUTO,
					IDTIPOCOMPPROD: product.IDTIPOCOMPPROD,
					IDTIPCOBRA: product.IDTIPCOBRA,
					IDPESAPROD: product.IDPESAPROD,
					OBSERVATIONS: product.OBSERVATIONS,
					IMPRESSORAS: product.IMPRESSORAS,
					ATRASOPROD: "N",
					TOGO: "N",
					holdText: '',
					toGoText: '',
					PRODUTOS: Array(),
					refilSet: refilSet,
					NRQTDMINOBS: product.NRQTDMINOBS,
					NRCOMANDA: _.get(accountData, '[0].NRCOMANDA') || null,
					NRVENDAREST: _.get(accountData, '[0].NRVENDAREST') || null,
					DSCOMANDA: dscomanda,
					numeroProdutos: 1,
					AGRUPAMENTO: '',
					IDENTIFYKEY: time.getTime(),
                    QTPRODCOMVEN: 1
				};
				if (refilSet) {
					cartItem.PRECO = '0,00';
					cartItem.PRITEM = 0;
					cartItem.PRITOTITEM = 0;
					cartItem.VRACRITVEND = 0;
					cartItem.VRDESITVEND = 0;
					cartItem.VRPRECITEMCL = 0;
				}
				return cartItem;
			});
		});
	};

	this.getOrderCodeProductID = function () {
		return AccountCart.findAll().then(function (cartItems) {
			var nextID = 0;
			cartItems.forEach(function (item) {
				if (item.ID > nextID) {
					nextID = item.ID;
				}
			});
			return nextID + 1;
		});
	};

	var restartDataSourceWidget = function (widget) {
		if (widget.dataSource.data && widget.dataSource.data.length > 0) {
			delete widget.dataSource.data;
		}
		widget.newRow();
		widget.moveToFirst();
	};

	var prepareProductWidget = function (productWidget, cartItem) {

		return handlePrintersProductForRoom(cartItem).then(function (printers) {
			var data = {
				product: cartItem.DSBUTTON,
				position: cartItem.POSITION,
				CDPRODUTO: cartItem.CDPRODUTO,
				CDOCORR: [],
				ATRASOPROD: "N",
				TOGO: "N",
				holdText: '',
				toGoText: '',
				DSOCORR_CUSTOM: '',
				ID: cartItem.ID,
				IDPESAPROD: cartItem.IDPESAPROD,
				NRSEQIMPRLOJA: [],
				NMIMPRLOJA: ""
			};

			var printersField = productWidget.getField('NRSEQIMPRLOJA');
			printersField.dataSource.data = printers;
			printersField.isVisible = printers.length > 1;

			if (printers.length > 0) {
				data.NRSEQIMPRLOJA.push(printers[0].NRSEQIMPRLOJA);
				if (printers.length > 1) {
					data.NMIMPRLOJA = getPrinterName(printers[0].NRSEQIMPRLOJA, printers);
				}
			}

			productWidget.setCurrentRow(data);
			productWidget.container.restoreDefaultMode();

			return data;
		});
	};

	var handlePrintersProductForRoom = function (product) {
		var promiseResult = ZHPromise.when([]);
		if (product.IMPRESSORAS && product.IMPRESSORAS.length > 0) {
			promiseResult = TableActiveTable.findOne().then(function (activeTable) {
				var printersRoom = product.IMPRESSORAS.filter(function (param) {
					if (activeTable) {
						return param.CDAMBIENTE === activeTable.CDSALA;
					}
					else {
						return false;
					}
				});
				return printersRoom;
			});
		}
		return promiseResult;
	};

	var isUsingPositions = function (operatorData) {
		return operatorData.IDLUGARMESA === Param.YES;
	};

	var isTableMode = function (operatorData) {
		return operatorData.modoHabilitado === Mode.TABLE;
	};

	var isBillMode = function (operatorData) {
		return operatorData.modoHabilitado === Mode.BILL;
	};

	var isBalconyMode = function (operatorData) {
		return operatorData.modoHabilitado === Mode.BALCONY;
	};

	var updateWidgetLabel = function (operatorData, cartItem, productWidget) {
		var labelText = '';
		var article = 'a';

		if (isTableMode(operatorData)) {
			if (isUsingPositions(operatorData)) {
				labelText = cartItem.POSITION;
			} else {
				labelText = 'mesa';
			}
		} else if (isBillMode(operatorData)) {
			labelText = 'comanda';
		} else if (isBalconyMode(operatorData)) {
			labelText = 'carrinho';
			article = 'o';
		}

		productWidget.label = '<span class="font-bold">' + cartItem.DSBUTTON + '</span> para ' + article + ' <span class="font-bold">' + labelText + '</span>';
	};

	var updateFieldObservationsDataSource = function (observationField, product) {
		observationField.dataSource.data = this.getObservations(product.OBSERVATIONS);
	}.bind(this);

    this.addToCart = function (productWidget, product, position, actionQtCart, innerCart, refilSet, refilBypass){
        OperatorRepository.findOne().then(function (operatorData){
            //caixa recebedor não coleta
            if (operatorData.IDCOLETOR != 'R'){
                if (product.PRITEM > 0 || product.IDIMPPRODUTO === '2'){
                    /* REFIL MECHANICS */
                    if (product.REFIL === 'S' && !refilBypass){
                        if (operatorData.modoHabilitado !== 'B'){
                        	this.getAccountData(function(accountData){
                        		if (accountData && accountData.length > 0){
	                                AccountService.checkRefil(operatorData.chave, accountData[0].NRVENDAREST, accountData[0].NRCOMANDA, product.CDPRODUTO, position).then(function (refilData){
	                                    if (refilData.length === 0){
	                                        this.addToCart(productWidget, product, position, actionQtCart, innerCart, false, true);
	                                    }
	                                    else {
	                                        ScreenService.confirmMessage(
	                                            'Este produto é um refil?',
	                                            'question',
	                                            function (){
	                                                this.addToCart(productWidget, product, position, actionQtCart, innerCart, true, true);
	                                            }.bind(this),
	                                            function (){
	                                                this.addToCart(productWidget, product, position, actionQtCart, innerCart, false, true);
	                                            }.bind(this)
	                                        );
	                                    }
	                                }.bind(this));
	                            }
                        	}.bind(this));
                        } else {
                            ScreenService.showMessage("Produto refil não pode ser realizado no modo balcão.", "alert");
                        }
                    } else {
                        addItemToCart(productWidget, product, position, actionQtCart, innerCart, refilSet);
                    }
                } else {
                    ScreenService.showMessage("Produto sem preço.", 'alert');
                }
            } else{
                ScreenService.showMessage("Caixa habilitado apenas para modo recebedor.");
            }
        }.bind(this));
    };

    var addItemToCart = function(productWidget, product, position, actionQtCart, innerCart, refilSet){
        if (product.IDPRODBLOQ === 'N'){
            AccountCart.findAll().then(function(cart){
                actionQtCart.hint = cart.length+1;
                innerCart.hint = cart.length+1;
                buildCartItem(product, position, refilSet).then(function (cartItem){
                    restartDataSourceWidget(productWidget);
                    prepareProductWidget(productWidget, cartItem).then(function (dataSource){
                        productWidget.dataSource.data[0].IDPESAPROD = dataSource.IDPESAPROD;
                        cartItem.IDPESAPROD    = dataSource.IDPESAPROD;
                        cartItem.NRSEQIMPRLOJA = dataSource.NRSEQIMPRLOJA;
                        cartItem.NMIMPRLOJA    = dataSource.NMIMPRLOJA;
                        if (dataSource.IDPESAPROD === 'S') cartItem.QTPRODCOMVEN = null;
                        else cartItem.QTPRODCOMVEN = 1;
                        AccountCart.save(cartItem).then(function(){
                            OperatorRepository.findOneInMemory().then(function (operatorData){
                                updateWidgetLabel(operatorData, cartItem, productWidget);
                            });
                            updateFieldObservationsDataSource(productWidget.getField('CDOCORR'), product);
                            productWidget.currentRow.QTPRODCOMVEN = "1";
                            openProductPopUp(productWidget);
                        });
                    });
                });
            });
        } else {
            ScreenService.showMessage("Produto bloqueado.");
        }
    };

    var openProductPopUp = function(productWidget){
        OperatorRepository.findOneInMemory().then(function (operatorData){
            var parentWidget = productWidget.container.getWidget('menu') || productWidget.container.getWidget('smartPromo') || productWidget.container.getWidget('subPromo');

            productWidget.getField('ATRASOPROD').isVisible = operatorData.NRATRAPADRAO > 0;
            productWidget.getField('TOGO').isVisible = operatorData.IDCTRLPEDVIAGEM === 'S';
            if (parentWidget.container.name !== 'menu' || operatorData.IDUTLQTDPED === 'S'){
                productWidget.getField('QTPRODCOMVEN').isVisible = true;
                productWidget.getField('QTPRODCOMVEN').spin = true;
                productWidget.getField('QTPRODCOMVEN').label = "Quantidade (un)";
                productWidget.getField('QTPRODCOMVEN').blockInputEdit = true;
            }
            else {
                productWidget.getField('QTPRODCOMVEN').isVisible = false;
            }
            if (productWidget.currentRow.IDPESAPROD === 'S'){
                productWidget.getField('QTPRODCOMVEN').isVisible = true;
                productWidget.getField('QTPRODCOMVEN').spin = false;
                productWidget.getField('QTPRODCOMVEN').label = "Quantidade (kg)";
                productWidget.currentRow.QTPRODCOMVEN = "";
                productWidget.getField('QTPRODCOMVEN').blockInputEdit = false;
            }
			ScreenService.openPopup(productWidget);

			parentWidget.activate(); //To show the correct action on the button bar.
			parentWidget.container.restoreDefaultMode();
		});
	};

    /* - SMART PROMO MECHANICS - */

    this.defineRepository = function(widget){
        if (widget.container.name === "smartPromo") return SmartPromoTray;
        else return SubPromoTray;
    };

    this.definePreselection = function(products){
        var newTray = [];
        var preselection = _.filter(products, function (product){
            return product.IDPRODPRESELEC === "S" && product.IDPESAPROD === "N";
        });

        preselection.forEach(function (product){
            var currentGroupProducts = _.reduce(newTray, function(count, trayItem){
                if (trayItem.CDGRUPO === product.CDGRUPO){
                    return count + 1;
                }
                else {
                    return count;
                }
            }, 0);

            if (currentGroupProducts < product.QTPRGRUPPROMOC){
                product.quantity = 1;
                newTray.push(product);
            }
        });
        return newTray;
    };

    this.buildAllTrayItems = function(products, tray, firstCicle){
        return new Promise(function(resolve, reject) {
            if (firstCicle) {
                products = self.definePreselection(products);
            }

            if (!_.isEmpty(products)){
                var product = products.shift();
                var promoValues = self.processPromoValues(tray, product);
                self.buildTrayItem(product, promoValues).then(function (trayItem){
                    tray.push(trayItem);
                    if (products.length > 0) {
                        self.buildAllTrayItems(products, tray, false).then(function(resolvedTray){
                            resolve(resolvedTray);
                        }, reject);
                    } else {
                        resolve(tray);
                    }
                });
            }
            else {
                resolve([]);
            }
        });
    };

    this.openPromoScreen = function(product, widget, subPromo){
        self.preparePromoRepositories(product).then(function (repos){

            if (repos == null){
                // Error handling.
                return AccountCart.findAll().then(function (cart){
                    return AccountCart.remove(Query.build()).then(function (){
                        var newCart = cart.filter(function (item){
                            return item.ID !== cart[0].ID;
                        });
                    });
                });
            }

            var PromoGroups;
            var PromoProds;
            var PromoTray;
            var PromoWindow;

            if (!subPromo){
                PromoGroups = SmartPromoGroups;
                PromoProds = SmartPromoProds;
                PromoTray = SmartPromoTray;
                PromoWindow = 'PROMO_SCREEN';
            }
            else {
                PromoGroups = SubPromoGroups;
                PromoProds = SubPromoProds;
                PromoTray = SubPromoTray;
                PromoWindow = 'SUBPROMO_SCREEN';
            }

            var promises = [];

            // GROUPS.
            var PromoGroupsPromise = PromoGroups.remove(Query.build()).then(function (){
                return PromoGroups.save(repos.groups);
            });

            // PRODUCTS.
            var PromoProdsPromise = PromoProds.remove(Query.build()).then(function (){
                return PromoProds.save(repos.products);
            });

            // Clears the tray.
            var PromoTrayPromise = PromoTray.remove(Query.build());

            // Add to tray.
            var PromoTrayAddPromise = self.buildAllTrayItems(repos.products, [], true).then(function(trayItensToAdd){
                return PromoTray.save(trayItensToAdd);
            });

            promises.push(PromoGroupsPromise);
            promises.push(PromoProdsPromise);
            promises.push(PromoTrayPromise);
            promises.push(PromoTrayAddPromise);
            // Resolves all promises.
            ZHPromise.all(promises).then(function (promisesResults){
                // Opens the Smart Promo page.
                WindowService.openWindow(PromoWindow).then(function (){
                    var widgetCategories = templateManager.container.getWidget('categories');
                    widgetCategories.currentRow.DISPLAY = widget.currentRow.DSBUTTON;
                    widgetCategories.currentRow.IDTIPOCOMPPROD = product.IDTIPOCOMPPROD;
                    if (widgetCategories.dataSource.data.length > 0){
                        self.resetGroupHighlight(widgetCategories);
                    }
                }.bind(this));
            }.bind(this));
        });
    };

    this.preparePromoRepositories = function(product){
        return self.getSmartPromoInfo(product).then(function (product){
            return OperatorRepository.findOne().then(function (operatorData){

                if (!product) return; // Error handling.

                var groups = [];
                var products = [];
                /* Builds the groups. */
                for (var idGroup in product.GRUPOS){

                    var currentGroup = product.GRUPOS[idGroup];
                    var currentCategory = currentGroup.grupo;

                    var group = buildSmartGroup(currentCategory);

                    /* Inserts this group into the group array. */
                    groups.push(group);

                    /* Builds the products. */
                    for (var idProd in currentGroup.produtos){
                        var currentProduct = currentGroup.produtos[idProd];
                        var item = buildSmartProduct(currentCategory, currentProduct, operatorData.IDCOLETOR);
                        if (item == null) return null;
                        /* Inserts this product into the group product. */
                        products.push(item);
                    }
                }

                return {
                    groups: groups,
                    products: products,
                };
            });
        });
    };

    this.getSmartPromoInfo = function(product){
        return SmartPromoRepository.findAll().then(function (allProducts){
             // Gets the groups/products.
            var products = JSON.parse(allProducts[0]);
            var groups = products[product.CDPRODUTO];

            // Puts the keys back.
            var productKeys = ["CDPRODUTO", "IDIMPPRODUTO", "IDAPLICADESCPR", "IDPERVALORDES", "NMPRODUTO", "VRDESPRODPROMOC", "IDDESCACRPROMO", "VRPRECITEM", "OBSERVACOES", "IDPRODBLOQ", "IMPRESSORAS", "VRALIQCOFINS", "VRALIQPIS", "VRPEALIMPFIS", "CDIMPOSTO", "CDCSTICMS", "CDCSTPISCOF", "CDCFOPPFIS", "DSPRODVENDA", "DSADICPROD", "DSENDEIMGPROMO", "NRORDPROMOPR", "IDPRODPRESELEC", "IDOBRPRODSELEC", "NRQTDMINOBS", "CDPROTECLADO", "IDTIPOCOMPPROD", "HRINIVENPROD", "HRFIMVENPROD", "CDCLASFISC", "REFIL", "CDPRODPROMOCAO", "VRPRECITEMCL", "VRDESITVEND", "VRACRITVEND", "IDPESAPROD"];
            for (var i in groups){
                for (var p in groups[i].produtos){
                    groups[i].produtos[p] = _.zipObject(productKeys, groups[i].produtos[p]);
                }
            }

            if (product.IDTIPOCOMPPROD === 'C') {
            	return product;
            }
            else if (groups == null || Object.keys(groups).length == 0){
                AccountCart.findAll().then(function (cart){
                    AccountCart.remove(Query.build()).then(function (){
                        var newCart = cart.filter(function (item){
                            return item.ID !== cart[0].ID;
                        });
                        AccountCart.save(newCart).then(function (){
                            ScreenService.showMessage("Promoção não possui composição.", "alert");
                        });
                    });
                });
            }
            else {
                product.GRUPOS = groups;
                return product;
            }
        });
    };

    var buildSmartGroup = function(currentCategory){
        return {
            COLOR:          '#660000',
            CDGRUPO:        currentCategory.CDGRUPROMOC,
            NMGRUPO:        currentCategory.NMGRUPROMOC,
            DISPLAY:        currentCategory.NMGRUPROMOC,
            QTPRGRUPPROMOC: currentCategory.QTPRGRUPPROMOC,
            QTPRGRUPROMIN:  currentCategory.QTPRGRUPROMIN,
            CDGRUPMUTEX:    currentCategory.CDGRUPMUTEX,
            SELECTED:       false,
            DISABLED:       false
        };
    };

    var buildSmartProduct = function(currentCategory, currentProduct, idImpProduto, IDCOLETOR){
    	// Caso o produto seja pre-selecionado, temos que validar ele aqui pois a tela adiciona eles automaticamente.
    	if (currentProduct.IDPRODPRESELEC === 'S'){
    		validaProduto = self.validateProducts(currentProduct, IDCOLETOR);
            if (!_.isEmpty(validaProduto)){
                if (currentProduct.IDOBRPRODSELEC === 'S'){
                    // Se um produto obrigatório estiver inválido, a promoção não pode ser montada.
                    ScreenService.showMessage("Um ou mais produtos obrigatórios dentro da promoção não podem ser pedidos: " + validaProduto);
                    return null;
                }
                // Caso o produto não seja válido, remove a flag de pre-seleção.
                currentProduct.IDPRODPRESELEC = 'N';
            }
    	}

        return {
            COLOR:           '#000066',
            CDGRUPO:         currentCategory.CDGRUPROMOC,
            NMGRUPO:         currentCategory.NMGRUPROMOC,
            CDPRODUTO:       currentProduct.CDPRODUTO,
            IDIMPPRODUTO:    currentProduct.IDIMPPRODUTO,
            IDAPLICADESCPR:  currentProduct.IDAPLICADESCPR,
            IDPERVALORDES:   currentProduct.IDPERVALORDES,
            IDPESAPROD:      currentProduct.IDPESAPROD,
            DSBUTTON:        currentProduct.NMPRODUTO,
            VRDESPRODPROMOC: currentProduct.VRDESPRODPROMOC,
            IDDESCACRPROMO:  currentProduct.IDDESCACRPROMO,
            VRPRECITEM:      currentProduct.VRPRECITEM,
            OBSERVATIONS:    currentProduct.OBSERVACOES,
            IDPRODBLOQ:      currentProduct.IDPRODBLOQ,
            QTPRGRUPPROMOC:  currentCategory.QTPRGRUPPROMOC,
            QTPRGRUPROMIN:   currentCategory.QTPRGRUPROMIN,
            CDGRUPMUTEX:     currentCategory.CDGRUPMUTEX,
            IMPRESSORAS:     currentProduct.IMPRESSORAS,
            IDPRODPRESELEC:  currentProduct.IDPRODPRESELEC,
            IDOBRPRODSELEC:  currentProduct.IDOBRPRODSELEC,
            IDTIPOCOMPPROD:  currentProduct.IDTIPOCOMPPROD,
            HRINIVENPROD:  	 currentProduct.HRINIVENPROD,
            HRFIMVENPROD:  	 currentProduct.HRFIMVENPROD,
            CDCLASFISC:  	 currentProduct.CDCLASFISC,
            CDCFOPPFIS:  	 currentProduct.CDCFOPPFIS,
            CDCSTICMS:  	 currentProduct.CDCSTICMS,
            CDCSTPISCOF:  	 currentProduct.CDCSTPISCOF,
            VRALIQPIS:  	 currentProduct.VRALIQPIS,
            VRALIQCOFINS:  	 currentProduct.VRALIQCOFINS,
            VRDESITVEND:     currentProduct.VRDESITVEND,
            VRACRITVEND:     currentProduct.VRACRITVEND,
            VRPRECITEMCL:    currentProduct.VRPRECITEMCL,
            quantity:        0
        };
    };

    this.addToTray = function (productWidget, product){
        if (product.VRPRECITEM !== null || parseFloat(product.VRPRECITEM) > 0 || product.IDIMPPRODUTO === '1'){
            var PromoTray = self.defineRepository(productWidget);
            PromoTray.findAll().then(function (tray){

                var groupCount = self.promoGroupCount(tray, product.CDGRUPO);

                var handle = _.find(tray, function(item){
                    return item.CDPRODUTO === product.CDPRODUTO;
                });

                productWidget.getAction('clearProducts').isVisible = product.IDPESAPROD === "S";

                if (_.isEmpty(handle) || product.IDPESAPROD === "S"){
                    // Produto não existe na bandeja.
                    var promoValues = self.processPromoValues(tray, product);
                    if (groupCount < product.QTPRGRUPPROMOC){
                        trayClone = null;
                        groupCount++;
                        self.buildTrayItem(product, promoValues).then(function (trayItem){
                            PromoTray.save(trayItem).then(function (){
                                AccountCart.findAll().then(function (cart){
                                    if (productWidget.container.name === "smartPromo" && product.IDTIPOCOMPPROD == '3' && cart[0].CDPRODUTO != product.CDPRODUTO && product.IDIMPPRODUTO != '1'){
                                        self.openPromoScreen(product, productWidget.container.getWidget('products'), true);
                                    }
                                    else {
                                        product.quantity++;
                                        self.updateGroupQuantityHeader(productWidget, groupCount);
                                        if (!_.isEmpty(product.OBSERVATIONS)){
                                            // Se não tiver observações, não abre o popup.
                                            self.openPromoPopup(productWidget, product, promoValues);
                                        }
                                        else {
                                            PromoTray.findAll().then(function (newTray){
                                                self.handleMutex(productWidget.container.getWidget('categories').dataSource.data, product.CDGRUPMUTEX, product.CDGRUPO, newTray);
                                                self.advanceGroup(productWidget.container.getWidget('categories'), newTray);
                                            });
                                        }
                                    }
                                });
                            });
                        });
                    }
                    else {
                        ScreenService.showMessage("Quantidade excedida.");
                    }
                }
                else {
                    // Produto já existe na bandeja.
                    trayClone = angular.copy(tray);

                    if (productWidget.dataSource.data && productWidget.dataSource.data.length > 0) {
                        delete productWidget.dataSource.data;
                    }
                    productWidget.newRow();
                    productWidget.container.restoreDefaultMode();
                    productWidget.moveToFirst();

                    productWidget.newRow();
                    productWidget.setCurrentRow(handle);
                    productWidget.label = '<span class="font-bold">'+product.DSBUTTON+'</span> para a <span class="font-bold">bandeja</span>';

                    productWidget.getField('CDOCORR').dataSource.data = self.getObservations(product.OBSERVATIONS);
                    openProductPopUp(productWidget);
                }
            });
        }
        else ScreenService.showMessage("Produto sem preço.");
    };

    this.clearTrayProduct = function(widget){
        var PromoTray = this.defineRepository(widget);
        PromoTray.findAll().then(function (tray){
            PromoTray.remove(Query.build()).then(function (){
                var newTray = tray.filter(function (item){
                    return item.CDPRODUTO !== widget.currentRow.CDPRODUTO;
                });
                PromoTray.save(newTray).then(function (){
                    var handle = _.find(widget.container.getWidget('products').dataSource.data, function (prod){
                        return prod.CDPRODUTO === widget.currentRow.CDPRODUTO;
                    });
                    handle.quantity = 0;
                    var count = self.promoGroupCount(newTray, widget.currentRow.GDGRUPO);
                    self.updateGroupQuantityHeader(widget, count);
                    ScreenService.closePopup();
                });
            });
        });
    };

    this.promoGroupCount = function(tray, CDGRUPO){
        /* Counts the number of products are in the group. */
        var cont = 0;
        for (var i in tray){
            if (tray[i].CDGRUPO === CDGRUPO){
                if (tray[i].IDPESAPROD === "N"){
                    cont += tray[i].QTPRODCOMVEN;
                }
                else {
                    cont++;
                }
            }
        }
        return cont;
    };

    this.processPromoValues = function(tray, product){
        /* Works out the next ID. */
        var nextID = 0;
        tray.forEach(function (item) {
            if (item.ID > nextID) nextID = item.ID;
        });
        nextID++;

        /* Checks if the delay has been set or not. */
        var setDelay = 'N';
        var delayString = '';
        if (tray.length > 0 && tray[0].ATRASOPROD === 'Y'){
            setDelay = 'Y';
            delayString = 'SEGURA';
        }

        var setToGo = 'N';
        var toGoString = '';
        if (tray.length > 0 && tray[0].TOGO === 'Y'){
            setToGo = 'Y';
            toGoString = 'PARA VIAGEM';
        }

        /* Calculates the discount. */
        var price = parseFloat((product.VRPRECITEM + product.VRPRECITEMCL + product.VRACRITVEND - product.VRDESITVEND).toFixed(2));
        var discount = product.IDDESCACRPROMO == 'D' ? parseFloat(product.VRDESPRODPROMOC) : 0;
        var addition = product.IDDESCACRPROMO == 'A' ? parseFloat(product.VRDESPRODPROMOC) : 0;
        if (product.IDAPLICADESCPR === 'I'){ // I => Only applies discount on the first product of that type.
            for (var j in tray){
                if (tray[j].CDPRODUTO === product.CDPRODUTO){
                    // If the product is already on the tray, we remove its discount.
                    discount = 0;
                    break;
                }
            }
        }

        var strDesconto = '';
        if (discount > 0){
            if (product.IDPERVALORDES === 'P'){
                strDesconto = '-' + discount + '%';
                discount = parseInt(price*discount)/100;
            }
            else if (product.IDPERVALORDES === 'V'){
                strDesconto = '-R$' + discount;
            }
        }

        if (addition > 0 && product.IDPERVALORDES === 'P'){
            addition = parseInt(price*addition)/100;
        }

        price = parseFloat((price - discount + addition).toFixed(2));

        var strPrice = '';
        if (product.IDIMPPRODUTO !== '1')
            strPrice = '' + UtilitiesService.formatFloat(price);

        var originalDiscount = product.VRDESITVEND;
        var fullAddition = parseFloat((product.VRACRITVEND + addition).toFixed(2));
        var fullDiscount = parseFloat((product.VRDESITVEND + discount).toFixed(2));
        var realPrice = parseFloat((product.VRPRECITEM + product.VRPRECITEMCL + product.VRACRITVEND - product.VRDESITVEND).toFixed(2));

        return {
            ID: nextID,
            setDelay: setDelay,
            setToGo: setToGo,
            delayString: delayString,
            toGoString: toGoString,
            price: price,
            strPrice: strPrice,
            discount: discount,
			addition: addition,
            strDesconto: strDesconto,
            originalDiscount: originalDiscount,
            fullDiscount: fullDiscount,
            fullAddition: fullAddition,
            realPrice: realPrice
        };
    };

    this.buildTrayItem = function(product, promoValues){
        return handlePrintersProductForRoom(product).then(function (printers){
            return {
                ID: promoValues.ID,
                CDGRUPO: product.CDGRUPO,
                CDPRODUTO: product.CDPRODUTO,
                IDIMPPRODUTO: product.IDIMPPRODUTO,
                NMGRUPO: product.NMGRUPO,
                DSBUTTON: product.DSBUTTON,
                IDAPLICADESCPR: product.IDAPLICADESCPR,
                IDOBRPRODSELEC: product.IDOBRPRODSELEC,
                IDPERVALORDES: product.IDPERVALORDES,
                VRDESPRODPROMOC: promoValues.discount,
                PRECO: product.VRPRECITEM,
                STRPRICE: promoValues.strPrice,
                STRDESCONTO: promoValues.strDesconto,
                PRITEM: product.VRPRECITEM,
                VRPRECITEMCL: product.VRPRECITEMCL,
                REALSUBSIDY: 0,
                VRDESITVEND: promoValues.fullDiscount,
                VRACRITVEND: promoValues.fullAddition,
                PRICE: promoValues.price,
                TOTPRICE: promoValues.price,
                DISCOUNT: promoValues.discount,
                ADDITION: promoValues.addition,
                ATRASOPROD: promoValues.setDelay,
                holdText: promoValues.delayString,
                TOGO: promoValues.setToGo,
                toGoText: promoValues.toGoString,
                PRODUTOS: [],
                CDOCORR: [],
                DSOCORR_CUSTOM: '',
                TXPRODCOMVEN: null,
                OBSERVATIONS: product.OBSERVATIONS,
                IMPRESSORA: printers.length > 0 ? printers[0].NRSEQIMPRLOJA : null,
                NRQTDMINOBS: product.NRQTDMINOBS,
                CDGRUPMUTEX: product.CDGRUPMUTEX,
                REALPRICE: promoValues.realPrice,
                VRDESCONTO: promoValues.originalDiscount,
                IDPESAPROD: product.IDPESAPROD,
                QTPRGRUPPROMOC: product.QTPRGRUPPROMOC,
                QTPRODCOMVEN: 1
            };
        });
    };

    this.updateGroupQuantityHeader = function(widget, groupCount){
        // Changes the group name to reflect the newly added product.
        var widgetCategories = widget.container.getWidget('categories');
        var oldDisplay = "";
        for (var i in widgetCategories.dataSource.data){
            if (widgetCategories.dataSource.data[i].selected){
                oldDisplay = widgetCategories.dataSource.data[i].DISPLAY;
            }
            widgetCategories.currentRow.oldDisplay = !!oldDisplay.substring(0, oldDisplay.indexOf("(")) ? oldDisplay.substring(0, oldDisplay.indexOf("(")) : oldDisplay;
            if (widgetCategories.dataSource.data[i].selected || widgetCategories.dataSource.data[i].SELECTED){
                widgetCategories.dataSource.data[i].DISPLAY = groupCount > 0 ? widgetCategories.currentRow.oldDisplay + ' (' + groupCount + ')' : widgetCategories.currentRow.oldDisplay;
            }
        }
    };

    this.openPromoPopup = function(productWidget, product, promoValues){
        if (productWidget.dataSource.data && productWidget.dataSource.data.length > 0) {
            delete productWidget.dataSource.data;
        }
        productWidget.newRow();
        productWidget.container.restoreDefaultMode();
        productWidget.moveToFirst();

        product.QTPRODCOMVEN = 1;
        product.ID = promoValues.ID;
        product.ATRASOPROD = promoValues.setDelay;
        product.holdText = promoValues.delayString;
        product.TOGO = promoValues.setToGo;
        product.toGoText = promoValues.toGoString;
        product.CDOCORR = [];
        product.DSOCORR_CUSTOM = '';

        productWidget.newRow();
        productWidget.setCurrentRow(product);
        productWidget.label = '<span class="font-bold">'+product.DSBUTTON+'</span> para a <span class="font-bold">bandeja</span>';

        productWidget.getField('CDOCORR').dataSource.data = this.getObservations(product.OBSERVATIONS);
        openProductPopUp(productWidget);
    };

    this.initSmartPromo = function(widget){
        this.updateObservationsInner(function (){
            SmartPromoGroups.findAll().then(function (groups){
                SmartPromoTray.findAll().then(function (tray){

                    var master = {};
                    var exclusiveGroups = {};
                    var i;

                    // Counts the number of selected products in each group.
                    for (i in tray){
                        if (master[tray[i].CDGRUPO] == null) master[tray[i].CDGRUPO] = 0;

                        if (tray[i].IDPESAPROD === "S") master[tray[i].CDGRUPO]++;
                        else master[tray[i].CDGRUPO] += tray[i].QTPRODCOMVEN;

                        if (tray[i].CDGRUPMUTEX){
                            exclusiveGroups[tray[i].CDGRUPMUTEX] = tray[i].CDGRUPO;
                        }
                    }

                    // Sets the number of products on group names.
                    var selection = 0;
                    for (i in groups){
                    	groups[i].visible = true;

                        if (master[groups[i].CDGRUPO] != null){
                            groups[i].DISPLAY = groups[i].NMGRUPO + " (" + parseInt(master[groups[i].CDGRUPO]).toString() + ")";
                        }
                        else {
                            groups[i].DISPLAY = groups[i].NMGRUPO;
                        }
                        // Controls the highlighted group, to be determined later on.
                        if (widget.dataSource.data[i] && widget.dataSource.data[i].selected) selection = i;

                        if (groups[i].CDGRUPMUTEX && exclusiveGroups[groups[i].CDGRUPMUTEX] && exclusiveGroups[groups[i].CDGRUPMUTEX] !== groups[i].CDGRUPO){
                            groups[i].DISABLED = true;
                        }
                        groups[i].DISPLAY = widget.currentRow.IDTIPOCOMPPROD === 'C' ? widget.currentRow.DISPLAY : groups[i].DISPLAY;
                    }

                    widget.setCurrentRow(groups[selection]); // Determines which group will be selected.
                    groups[selection].SELECTED = true; // Controls the highlighted group, to be determined later on.
                    groups[selection].selected = true;
                    widget.dataSource.data = groups; // Sets the page's datasource with the correct groups.
                });
            });
        });
    };

    this.initSubPromo = function(widget){
        this.updateObservationsInner(function (){
            SubPromoGroups.findAll().then(function (groups){
                groups = _.map(groups, function(group){
                	group.visible = true;
                	return group;
                });
                widget.setCurrentRow(groups[0]); // Determines which group will be selected.
                groups[0].SELECTED = true; // Always highlights the first group.
                groups[0].selected = true;
                widget.dataSource.data = groups; // Sets the page's datasource with the correct groups.
            });
        });
    };

    this.backSmartPromo = function(widget){
        AccountCart.findAll().then(function (cart){
            AccountCart.remove(Query.build()).then(function (){
                var newCart = cart.filter(function (item){
                    return item.ID !== cart[0].ID;
                });

                AccountCart.save(newCart).then(function (){
                    self.resetGroupHighlight(widget);
                    WindowService.openWindow('MENU_SCREEN');
                });
            });
        });
    };

    this.backSubPromo = function(widget){
        SmartPromoTray.findAll().then(function (tray){
            SmartPromoTray.remove(Query.build()).then(function (){
                var newTray = tray.filter(function (item){
                    return item.ID !== tray[0].ID;
                });

                SmartPromoTray.save(newTray).then(function (){
                    self.resetGroupHighlight(widget);
                    WindowService.openWindow('PROMO_SCREEN');
                });
            });
        });
    };

    this.resetGroupHighlight = function(widget){
        // Ensures the first group will be highlighted next time.
        for (var i in widget.dataSource.data){
            widget.dataSource.data[i].selected = false;
        }
        widget.dataSource.data[0].selected = true;
    };

    this.undoPromoAdd = function(args){
        var PromoTray = this.defineRepository(args.owner.widget);
        if (_.isEmpty(trayClone)){
            var product = args.row;
            var widgetCategories = args.owner.widget.container.getWidget('categories');
            var handle = _.find(args.owner.widget.container.getWidget('products').dataSource.data, function (prod){
                return prod.CDPRODUTO == product.CDPRODUTO;
            });
            handle.quantity--;
            PromoTray.findAll().then(function (tray) {
                var cont = self.promoGroupCount(tray, product.CDGRUPO);
                PromoTray.remove(Query.build()).then(function () {
                    var newTray = tray.filter(function (item) {
                        return item.ID !== product.ID;
                    });
                    PromoTray.save(newTray).then(function () {
                        for (var i in widgetCategories.dataSource.data){
                            if (widgetCategories.dataSource.data[i].selected){
                                var apnd = (parseInt(cont)-1 > 0) ? ' (' + (parseInt(cont)-1).toString() + ')' : '';
                                widgetCategories.dataSource.data[i].DISPLAY = widgetCategories.dataSource.data[i].oldDisplay + apnd;
                            }
                        }
                        ScreenService.closePopup();
                    });
                });
            });
        }
        else {
            PromoTray.save(trayClone).then(function (){
                ScreenService.closePopup();
            });
        }
    };

    this.closePromoPopup = function(widget){
        var PromoTray = this.defineRepository(widget);
        self.updatePromoObservations(widget, null).then(function (){
            PromoTray.findAll().then(function (data){
                var obsReturn = self.handleObservations(data, widget);
                if (obsReturn.error){
                    ScreenService.showMessage(obsReturn.message);
                }
                else if (widget.currentRow.IDPESAPROD === 'S' && (_.isEmpty(widget.currentRow.QTPRODCOMVEN) || widget.currentRow.QTPRODCOMVEN == 0)){
                    ScreenService.showMessage("Favor inserir uma quantidade válida para o produto.");
                }
                else if (widget.currentRow.IDPESAPROD === 'S' && widget.currentRow.QTPRODCOMVEN > 999999999){
                    ScreenService.showMessage("Quantidade do produto não pode exceder o limite máximo de 999999999kg.");
                }
                else {
                    if (widget.currentRow.IDPESAPROD === "N"){
                        var groupCount = self.promoGroupCount(data, widget.currentRow.CDGRUPO);
                        self.updateGroupQuantityHeader(widget, groupCount);

                        var handle = _.find(widget.container.getWidget('products').dataSource.data, function (prod){
                            return prod.CDPRODUTO === widget.currentRow.CDPRODUTO;
                        });
                        handle.quantity = widget.currentRow.QTPRODCOMVEN;
                        if (widget.currentRow.QTPRODCOMVEN == 0){
                            PromoTray.remove(Query.build()).then(function (){
                                var newTray = data.filter(function (item) {
                                    return item.ID !== widget.currentRow.ID;
                                });
                                PromoTray.save(newTray);
                                this.handleMutex(widget.container.getWidget('categories').dataSource.data, data[0].CDGRUPMUTEX, data[0].CDGRUPO, newTray);
                            }.bind(this));
                        }
                        else {
                            this.handleMutex(widget.container.getWidget('categories').dataSource.data, widget.currentRow.CDGRUPMUTEX, widget.currentRow.CDGRUPO, data);
                            this.advanceGroup(widget.container.getWidget('categories'), data);
                        }
                        ScreenService.closePopup();
                    }
                    else {
                        ScreenService.closePopup();
                        this.handleMutex(widget.container.getWidget('categories').dataSource.data, widget.currentRow.CDGRUPMUTEX, widget.currentRow.CDGRUPO, data);
                        this.advanceGroup(widget.container.getWidget('categories'), data);
                    }
                }
            }.bind(this));
        }.bind(this));
    };

    this.handleMutex = function(groupData, mutex, currentGroup, tray){
        if (mutex != null){
            // Checks if there are any products from the current group inside the tray.
            var groupProducts = _.filter(tray, function(products){
                return products.CDGRUPO === currentGroup;
            });
            // Gets OTHER groups that have the same mutex.
            var mutexGroups = _.filter(groupData, function(group){
                return group.CDGRUPMUTEX === mutex && group.CDGRUPO !== currentGroup;
            });
            // Blocks or unblocks the group.
            _.forEach(mutexGroups, function(group){
                group.DISABLED = groupProducts.length > 0;
            });
        }
    };

    this.advanceGroup = function(groupWidget, tray){
        var count = this.promoGroupCount(tray, groupWidget.currentRow.CDGRUPO);
        var grupoAtual = groupWidget.currentRow.CDGRUPO;

        if (count == groupWidget.currentRow.QTPRGRUPPROMOC){
            var indexGrupoAtual = _.findIndex(groupWidget.dataSource.data, {'CDGRUPO': grupoAtual});

            if ((groupWidget.dataSource.data.length - 1) != indexGrupoAtual){
                groupWidget.dataSource.data = _.map(groupWidget.dataSource.data, function(a){
                    a.selected = false;
                    return a;
                });

                indexGrupoAtual = this.getNextGroupIndex(groupWidget.dataSource.data, indexGrupoAtual);

                groupWidget.dataSource.data[indexGrupoAtual].selected = true;
                groupWidget.setCurrentRow(groupWidget.dataSource.data[indexGrupoAtual]);
            }
        }
    };

    this.getNextGroupIndex = function(groups, currentIndex){
        for (var i = currentIndex; i < groups.length - 1; i++){
            if (!groups[i+1].DISABLED) return i+1;
        }
        return currentIndex;
    };

    this.confirmSmartPromo = function(widget){
        AccountCart.findAll().then(function (cart){
            SmartPromoTray.findAll().then(function (tray){
                if (validateGroupRequirements(widget.dataSource.data, tray, cart[0].IDTIPCOBRA)){
                    handlePrintersProductForRoom(cart[0]).then(function (printers){
                        cart[0].NRSEQIMPRLOJA = [];
                        if (printers.length > 0){
                            cart[0].NRSEQIMPRLOJA.push(printers[0].NRSEQIMPRLOJA);
                        }
                        self.trataCampanha(tray).then(function (tray){
                            cart[0].PRODUTOS = tray;
                            var calcResult = self.calcProductValue(cart[0]);
                            if (calcResult == null){
                                AccountCart.save(cart[0]).then(function (){
                                    if (cart[0].IDIMPPRODUTO == '1'){
                                        WindowService.openWindow('CHECK_PROMO_SCREEN');
                                    }
                                    else {
                                        self.resetGroupHighlight(widget);
                                        WindowService.openWindow('MENU_SCREEN');
                                    }
                                });
                            }
                            else {
                                ScreenService.showMessage("O valor calculado para o produto " + calcResult.DSBUTTON + " ficou abaixo de R$0,01. Verifique a parametrização.");
                            }
                        });
                    });
                }
            }.bind(this));
        }.bind(this));
    };

    this.confirmSubPromo = function(widget){
        SmartPromoTray.findAll().then(function (smartTray){
            SubPromoTray.findAll().then(function (subTray){
                if (validateGroupRequirements(widget.dataSource.data, subTray, null)){
                    smartTray[0].PRODUTOS = subTray;
                    self.calcProductValue(smartTray[0]);
                    smartTray[0].PRECO = smartTray[0].PRITOTITEM;
                    smartTray[0].PRICE = smartTray[0].PRITOTITEM;
                    smartTray[0].PRITEM = smartTray[0].PRITOTITEM;
                    smartTray[0].TOTPRICE = smartTray[0].PRITOTITEM;
                    smartTray[0].TXPRODCOMVEN = self.obsToText(smartTray[0].CDOCORR, smartTray[0].DSOCORR_CUSTOM);
                    SmartPromoTray.save(smartTray[0]).then(function (){
                        self.resetGroupHighlight(widget);
                        WindowService.openWindow('PROMO_SCREEN');
                    });
                }
            });
        });
    };

    var validateGroupRequirements = function(groups, tray, IDTIPCOBRA){
        if (IDTIPCOBRA === null){
        	if (tray.length == 0){
                ScreenService.showMessage('Favor escolher pelo menos uma opção.');
                return false;
            } else {
	            for (var i in groups){
	                var count = self.promoGroupCount(tray, groups[i].CDGRUPO);
	                var quant = groups[i].QTPRGRUPROMIN;

	                if (count < quant && !groups[i].DISABLED){
	                    var alert = 'Favor escolher mais ' + (quant - count);
	                    if (quant - count > 1) alert += ' produtos do grupo ' + groups[i].NMGRUPO + '.';
	                    else alert += ' produto do grupo ' + groups[i].NMGRUPO + '.';

	                    ScreenService.showMessage(alert);
	                    return false;
	                }
	            }
            }
        }
        else {
            if (tray.length == 0){
                ScreenService.showMessage('Favor escolher pelo menos uma opção.');
                return false;
            }
        }
        return true;
    };

    this.updatePromoProductQuantity = function(quantity){
        if (quantity == null || quantity === ""){
            return null;
        }
        else {
            quantity = String(quantity).replace(',','.');
            if (isNaN(quantity) || quantity <= 0){
                return null;
            }
            else {
                quantity = parseFloat(quantity).toFixed(3);
                return parseFloat(quantity);
            }
        }
    };

    this.updatePromoObservations = function(widget, row){
        var PromoTray = this.defineRepository(widget);
        return PromoTray.findAll().then(function (tray){
            var handle = _.find(tray, function(item){
                return item.CDPRODUTO === widget.currentRow.CDPRODUTO;
            });

            if (widget.currentRow.IDPESAPROD === "N"){
                var originalQuantity = handle.QTPRODCOMVEN;
                handle.QTPRODCOMVEN = parseInt(widget.currentRow.QTPRODCOMVEN);
                var groupCount = self.promoGroupCount(tray, widget.currentRow.CDGRUPO);

                if (groupCount > widget.currentRow.QTPRGRUPPROMOC){
                    widget.currentRow.QTPRODCOMVEN = originalQuantity;
                    return ScreenService.showMessage("Quantidade excedida.");
                }
                if (widget.currentRow.QTPRODCOMVEN == 0 && widget.currentRow.IDOBRPRODSELEC === "S"){
                    widget.currentRow.QTPRODCOMVEN = originalQuantity;
                    return ScreenService.showMessage("Produtos obrigatórios não podem ser retirados da seleção.");
                }
            }

            handle.CDOCORR        = widget.currentRow.CDOCORR || [];
            handle.DSOCORR_CUSTOM = widget.currentRow.DSOCORR_CUSTOM || null;
            handle.TXPRODCOMVEN   = this.obsToText(widget.currentRow.CDOCORR, widget.currentRow.DSOCORR_CUSTOM);
            handle.ATRASOPROD     = widget.currentRow.ATRASOPROD;
            handle.TOGO           = widget.currentRow.TOGO;
            handle.holdText       = (widget.currentRow.ATRASOPROD === 'Y') ? 'SEGURA' : '';
            handle.toGoText       = (widget.currentRow.TOGO === 'Y') ? 'PARA VIAGEM' : '';
            handle.QTPRODCOMVEN   = this.updatePromoProductQuantity(widget.currentRow.QTPRODCOMVEN);

            return PromoTray.save(tray);
        }.bind(this));
    };

    this.togglePromoDelay = function(widget){
        var PromoTray = this.defineRepository(widget);
        PromoTray.findAll().then(function (tray){
            for (var i in tray){
                tray[i].ATRASOPROD = widget.currentRow.ATRASOPROD;
                tray[i].TOGO       = widget.currentRow.TOGO;
                tray[i].holdText   = (widget.currentRow.ATRASOPROD === 'Y') ? 'SEGURA' : '';
                tray[i].toGoText   = (widget.currentRow.TOGO === 'Y') ? 'PARA VIAGEM' : '';
            }
            PromoTray.save(tray);
        }.bind(this));
    };

    this.filterPromoProducts = function(args){
        if (!args.row.DISABLED){
            var PromoTray = this.defineRepository(args.owner);
            this._filterPromoProducts(args).then(function (widget){
                PromoTray.findAll().then(function (tray){
                    tray.forEach(function (trayItem){
                        var handle = _.find(widget.dataSource.data, function (prod){
                            return prod.CDPRODUTO === trayItem.CDPRODUTO;
                        });
                        if (!_.isEmpty(handle)){
                            handle.quantity = 0;
                            if (handle.CDGRUPO == widget.dataSource.data[0].CDGRUPO){
                                if (handle.IDPESAPROD === "S"){
                                    handle.quantity++;
                                }
                                else {
                                    handle.quantity = trayItem.QTPRODCOMVEN;
                                }
                            }
                        }
                    });
                });
            });
        }
    };

    this._filterPromoProducts = function (args){
        for (var i in args.owner.dataSource.data){
            args.owner.dataSource.data[i].SELECTED = false;
        }
        var defer = ZHPromise.defer();
        ScreenService.filterWidget(args.owner, args.owner.parent.widgets);
        this.setGroupHeader(args);
        args.owner.container.getWidget('products').reload().then(function(){
            defer.resolve(args.owner.container.getWidget('products'));
        });
        return defer.promise;
    };

    this.trataCampanha = function(tray){
        return OperatorRepository.findOne().then(function (operatorData){
            var defer = ZHPromise.defer();
            if (operatorData.modoHabilitado === 'B' && operatorData.UTLCAMPANHA){
                return AccountService.getCampanha(tray).then(function (campanha){
                    try {
                        if (!_.isEmpty(campanha)){
                            campanha = campanha[0];
                            if (campanha.IDAPLICADESACR == '1'){
                                tray = self.aplicaDescontoCampanha(tray, campanha, campanha.CDPRODPRIN);
                            }
                            else if (campanha.IDAPLICADESACR == '2'){
                                tray = self.aplicaDescontoCampanha(tray, campanha, campanha.CDPRODCOMB);
                            }
                            else if (campanha.IDAPLICADESACR == '3'){
                                tray = self.aplicaDescontoCampanha(tray, campanha, campanha.CDPRODCOMB2);
                            }
                            else if (campanha.IDAPLICADESACR == '4'){
                                tray = self.rateiaDescontoCampanha(tray, campanha);
                            }
                        }
                        defer.resolve(tray);

                    } catch (err){
                        ScreenService.showMessage(err);
                        defer.reject();
                    } finally {
                        return defer.promise;
                    }
                });
            }
            else {
                defer.resolve(tray);
            }
            return defer.promise;
        });
    };

    this.aplicaDescontoCampanha = function(tray, campanha, produto){
        var valor = null;
        for (var i in tray){
            if (tray[i].CDPRODUTO == produto){
                if (campanha.IDPERCVALOR == 'V'){
                    valor = parseFloat(campanha.VRDESCACRE);
                }
                else {
                    valor = tray[i].REALPRICE * parseFloat(campanha.VRDESCACRE) / 100;
                }

                if (campanha.IDDESCACRE == 'D'){
                    tray[i].DISCOUNT = Math.floor(valor * 100) / 100;
                }
                else {
                    tray[i].ADDITION = Math.floor(valor * 100) / 100;
                }
                var newPrice = parseFloat((tray[i].REALPRICE + tray[i].ADDITION - tray[i].DISCOUNT).toFixed(2));
                tray[i].PRICE = newPrice;
                tray[i].TOTPRICE = newPrice;
                tray[i].VRDESITVEND = parseFloat((tray[i].VRDESCONTO + tray[i].DISCOUNT).toFixed(2));
                tray[i].REALSUBSIDY = 0;
                break;
            }
        }
        return tray;
    };

    this.rateiaDescontoCampanha = function(tray, campanha){
        // Preco total de TOTOS os produtos, considerando descontos e acrescimos da ITEMPRECODIA.
        var totalPrice = tray.reduce(function (total, item){
            return total + item.REALPRICE;
        }, 0);
        // Preco final de TODOS os produtos, considerando o desconto ou acrescimo da campanha.
        var modo = null;
        var finalPrice = null;
        if (campanha.IDDESCACRE == 'D'){
            modo = 'DISCOUNT';
            if (campanha.IDPERCVALOR == 'V'){
                finalPrice = totalPrice - parseFloat(campanha.VRDESCACRE);
            }
            else {
                finalPrice = totalPrice * (1 - parseFloat(campanha.VRDESCACRE) / 100);
            }
        }
        else {
            modo = 'ADDITION';
            if (campanha.IDPERCVALOR == 'V'){
                finalPrice = totalPrice + parseFloat(campanha.VRDESCACRE);
            }
            else {
                finalPrice = totalPrice * (1 + parseFloat(campanha.VRDESCACRE) / 100);
            }
        }
        // Aplica o desconto/acrescimo parcial nos produtos.
        tray = tray.map(function (item){
            var valor = null;
            if (campanha.IDPERCVALOR == 'V'){
                valor = (item.REALPRICE / totalPrice) * parseFloat(campanha.VRDESCACRE);
            }
            else {
                valor = item.REALPRICE * parseFloat(campanha.VRDESCACRE) / 100;
            }
            item[modo] = Math.floor(valor * 100) / 100;
            var newPrice = parseFloat((item.REALPRICE + item.ADDITION - item.DISCOUNT).toFixed(2));
            item.PRICE = newPrice;
            item.TOTPRICE = newPrice;
            item.VRDESITVEND = parseFloat((item.VRDESCONTO + item.DISCOUNT).toFixed(2));
            item.REALSUBSIDY = 0;
            return item;
        });
        // Total do desconto/acrescimo implementado.
        var totalDescAcre = tray.reduce(function (total, item){
            return total + item[modo];
        }, 0);
        // Rateia a diferença entre os descontos/acrescimos, caso exista.
        var diferenca = (totalPrice - finalPrice) - totalDescAcre;
        if (diferenca > 0.01){
            var qtdRateio = parseInt(diferenca/0.01);
            var c = 0;
            var totalProduto = null;
            var newPrice = null;
            while (qtdRateio > 0){
                for (var i in tray){
                    totalProduto = Math.floor((tray[i].REALPRICE + tray[i].ADDITION - tray[i].DISCOUNT) * 100) / 100;
                    if (tray[i][modo] >= 0.01 && totalProduto > 0.01){
                        tray[i][modo] += 0.01;
                        qtdRateio--;
                    }
                    newPrice = parseFloat((tray[i].REALPRICE + tray[i].ADDITION - tray[i].DISCOUNT).toFixed(2));
                    tray[i].PRICE = newPrice;
                    tray[i].TOTPRICE = newPrice;
                    tray[i].VRDESITVEND = parseFloat((tray[i].VRDESCONTO + tray[i].DISCOUNT).toFixed(2));

                    if (qtdRateio == 0) break;
                }

                c++;
                if (c == 1000) throw "Erro no rateio do desconto.";
            }
        }

        return tray;
    };

	this.storeParentObservations = function(widget){
		AccountCart.findAll().then(function (cart){
			cart[0].CDOCORR = widget.currentRow.CDOCORR || [];
			cart[0].DSOCORR_CUSTOM = widget.currentRow.DSOCORR_CUSTOM || null;
			AccountCart.save(cart[0]).then(function (){
				widget.setCurrentRow({'CDOCORR': [], 'DSOCORR_CUSTOM': null});
				WindowService.openWindow('MENU_SCREEN');
			});
		}.bind(this));
	};

	this.checkPromoDatasourceHandler = function (widget) {
		delete widget.dataSource.data;
		AccountCart.findAll().then(function (cart) {
			SmartPromoTray.findAll().then(function (tray) {
				tray.forEach(function (product) {
					product.TXPRODCOMVEN = this.obsToText(product.CDOCORR, product.DSOCORR_CUSTOM);
					if (product.ATRASOPROD === 'Y') product.holdText = 'SEGURA';
					else product.holdText = '';
					if (product.TOGO === 'Y') product.toGoText = 'PARA VIAGEM';
					else product.toGoText = '';
				}.bind(this));
				widget.dataSource.data = tray;

				// Prepares the parent product observation popup.
				widget.widgets[1].getField('CDOCORR').dataSource.data = this.getObservations(cart[0].OBSERVATIONS);
				widget.widgets[1].label = "Observações Adicionais - " + cart[0].DSBUTTON;

				ScreenService.openPopup(widget.widgets[1]);
			}.bind(this));
		}.bind(this));
	};

	this.getObservations = function (arrayCDOCORR) {
		var result = [];
		if (arrayCDOCORR) {
			if (!observationMap[arrayCDOCORR]) {
				observationMap[arrayCDOCORR] = this.cutRepeatValues(arrayCDOCORR).map((function (eachCDOCORR) {
					return this.findFirst(allObservations, function (obs) {
						return obs.CDOCORR === eachCDOCORR;
					});
				}).bind(this));
			}
			result = observationMap[arrayCDOCORR];
		}
		return result;
	};

	this.findFirst = function (arr, test) {
		var result = null;
		arr.some(function (element, i) {
			var testResult = test(element, i, arr);
			if (testResult) {
				result = element;
			}
			return testResult;
		});
		return result;
	};

	this.cutRepeatValues = function (array) {
		return array.filter(function (este, i) {
			return array.indexOf(este) === i;
		});
	};

	this.updateCart = function (widget, stripe) {
		return new Promise(function (resolve) {
			handleOneChoiceOnly(widget.currentRow, 'NRSEQIMPRLOJA');
			widget.dataSource.data.forEach(function (product) {
				if (_.isEmpty(product.PRODUTOS)) {
					product.TXPRODCOMVEN = this.obsToText(product.CDOCORR, product.DSOCORR_CUSTOM);
					product.NMIMPRLOJA = "";
					if (product.NRSEQIMPRLOJA) {
						product.NMIMPRLOJA = getPrinterName(product.NRSEQIMPRLOJA[0], product.IMPRESSORAS);
					}
				}
				else {
					for (var i in product.PRODUTOS) {
						if (product.ATRASOPROD === 'Y') product.PRODUTOS[i].ATRASOPROD = 'Y';
						else product.PRODUTOS[i].ATRASOPROD = 'N';
						if (product.TOGO === 'Y') product.PRODUTOS[i].TOGO = 'Y';
						else product.PRODUTOS[i].TOGO = 'N';
					}
				}
				if (product.ATRASOPROD === 'Y') {
					product.holdText = 'SEGURA';
				} else {
					product.holdText = '';
				}

				if (product.TOGO === 'Y') {
					product.toGoText = 'PARA VIAGEM';
				} else {
					product.toGoText = '';
				}

				self.calcProductValue(product);
			}.bind(this));

			var row = _.clone(widget.currentRow);
			if (stripe) {
				widget.dataSource.data.reverse();
				self.prepareCart(widget, stripe);
			}
			if (!_.isEmpty(widget.currentRow)) {
				var promiseCart = function () {
					return AccountCart.findAll().then(function (cart) {
						cart = _.map(cart, function (itemCart) {
							if (itemCart.IDENTIFYKEY == row.IDENTIFYKEY) {
								itemCart = row;
							}
							return itemCart;
						});
						if (stripe) {
							cart.reverse();
						}
						return AccountCart.remove(Query.build()).then(function () {
							return AccountCart.save(cart);
						}.bind(this));
					});
				};
				var promisePool = function () {
					return CartPool.findAll().then(function (cartPool) {
						cartPool = _.map(cartPool, function (itemCart) {
							if (itemCart.IDENTIFYKEY == row.IDENTIFYKEY) {
								itemCart = row;
							}
							return itemCart;
						});
						if (stripe) {
							cartPool.reverse();
						}
						return CartPool.remove(Query.build()).then(function () {
							return CartPool.save(cartPool);
						}.bind(this));
					});
				};

				Promise.all([promiseCart(), promisePool()]).then(function () {
					resolve();
				}.bind(this));
			} else {
				resolve();
			}
		}.bind(this));
	};

	var getPrinterName = function (NRSEQIMPRLOJA, arrayPrinters) {
		if (arrayPrinters && NRSEQIMPRLOJA) {
			return arrayPrinters.filter(function (printer) {
				return printer.NRSEQIMPRLOJA == NRSEQIMPRLOJA;
			})[0].NMIMPRLOJA || "";
		} else {
			return "";
		}
	};

	this.updatePromoItem = function (widget) {
		var defer = ZHPromise.defer();
		var cart = widget.dataSource.data;
		cart.forEach(function (product) {
			product.TXPRODCOMVEN = this.obsToText(product.CDOCORR, product.DSOCORR_CUSTOM);
		}.bind(this));
		this.savePromo(cart);
		defer.resolve();
		return defer.promise;
	};

	this.togglePromoDelayCheck = function (widget) {
		for (var i in widget.dataSource.data) {
			widget.dataSource.data[i].ATRASOPROD = widget.currentRow.ATRASOPROD;
			widget.dataSource.data[i].holdText = (widget.currentRow.ATRASOPROD === 'Y') ? 'SEGURA' : '';
			widget.dataSource.data[i].TOGO = widget.currentRow.TOGO;
			widget.dataSource.data[i].toGoText = (widget.currentRow.TOGO === 'Y') ? 'PARA VIAGEM' : '';
		}
		this.savePromo(widget.dataSource.data);
	};

	this.updateObservations = function (widget) {
		return AccountCart.findAll().then(function (data) {
			widget.currentRow.QTPRODCOMVEN = parseFloat(String(widget.currentRow.QTPRODCOMVEN).replace(',', '.'));
			handleOneChoiceOnly(widget.currentRow, 'NRSEQIMPRLOJA');
			data[0].CDOCORR = widget.currentRow.CDOCORR || [];
			data[0].DSOCORR_CUSTOM = widget.currentRow.DSOCORR_CUSTOM || null;
			data[0].ATRASOPROD = widget.currentRow.ATRASOPROD;
			data[0].TOGO = widget.currentRow.TOGO;
			data[0].holdText = (widget.currentRow.ATRASOPROD === 'Y') ? 'SEGURA' : '';
			data[0].toGoText = (widget.currentRow.TOGO === 'Y') ? 'PARA VIAGEM' : '';
			data[0].NRSEQIMPRLOJA = widget.currentRow.NRSEQIMPRLOJA || [];
			data[0].TXPRODCOMVEN = self.obsToText(widget.currentRow.CDOCORR, widget.currentRow.DSOCORR_CUSTOM);
			data[0].NMIMPRLOJA = getPrinterName(widget.currentRow.NRSEQIMPRLOJA[0], data[0].IMPRESSORAS);
			if (widget.currentRow.IDPESAPROD === 'S') {
				if (widget.currentRow.QTPRODCOMVEN !== null && !isNaN(widget.currentRow.QTPRODCOMVEN)) {
					data[0].QTPRODCOMVEN = parseFloat(widget.currentRow.QTPRODCOMVEN.toFixed(3));
				}
				else {
					widget.currentRow.QTPRODCOMVEN = "";
					data[0].QTPRODCOMVEN = 1;
				}
			}
			else {
				data[0].QTPRODCOMVEN = parseInt(widget.currentRow.QTPRODCOMVEN);
			}
			self.calcProductValue(data[0]);
			return AccountCart.save(data[0]);
		}.bind(this));
	};

	this.handleObservations = function (data, widget) {
		//@TODO SOME HARD STUFF
		var obsOnScreen = widget.getField('CDOCORR').dataSource.data || [];
		//pega os dados das obervaçoes ja selecionadas
		var ocorrencias = [];
		if (widget.currentRow.CDOCORR.length > 0) {
			ocorrencias = _.filter(allObservations, function (obs) {
				return _.some(widget.currentRow.CDOCORR, function (value) { return value == obs.CDOCORR; });
			});
		}

		//trata grupos
		var gruposOnScreen = obsOnScreen.map(function (grupo) {
			if (grupo.CDGRUPOOBRIG) {
				return grupo.CDGRUPOOBRIG;
			}
		}).filter(function (grupo, index, grupos) {
			return grupo && grupos.indexOf(grupo) === index;
		});

		var grupoInvalido;
		var checando = gruposOnScreen.every(function (grupo) {
			if (!ocorrencias.length) {
				grupoInvalido = grupo;
				return false;
			} else {
				return ocorrencias.some(function (validating, index, ocorrencias) {
					var valid = validating.CDGRUPOOBRIG && validating.CDGRUPOOBRIG == grupo;
					if (!valid) {
						grupoInvalido = grupo;
					}
					return !!valid;
				});
			}
		});
		if (!checando && grupoInvalido) {
			grupoInvalido = _.find(allObservations, { 'CDGRUPOOBRIG': grupoInvalido }).NMGRUPOBRIG;
			return { error: true, message: "Quantidade mínima de observações do grupo " + grupoInvalido + " não foi atingida." };
		}

		var qtdObsFaltando = data[0].NRQTDMINOBS - (_.get(widget.currentRow, "CDOCORR.length") || 0);
		if (!data[0].NRQTDMINOBS || (data[0].NRQTDMINOBS && qtdObsFaltando < 1)) {
			return { error: false };
		} else {
			return { error: true, message: "Quantidade mínima de observação não atingida. Por favor selecione mais " + qtdObsFaltando + ' ' + (qtdObsFaltando > 1 ? "observações" : "observação") + '.' };
		}
	};

	var handleOneChoiceOnly = function (row, field) {
		if (row && row[field]) {
			var fieldLength = row[field].length;
			if (fieldLength > 1) {
				var lastChoice = row[field][fieldLength - 1];
				row[field] = [];
				row[field].push(lastChoice);
			}
		}
	};

	this.undoOrder = function (product, cartAction) {
		AccountCart.findAll().then(function (Cart) {
			AccountCart.remove(Query.build()).then(function () {
				var newCart = Cart.filter(function (item) {
					return item.ID !== product.ID;
				});

				AccountCart.save(newCart).then(function () {
					ScreenService.closePopup();
				});

				cartAction.hint = cartAction.hint - 1;
			});
		});
	};

	this.prepareMenu = function (widgets) {
		ParamsGroupRepository.findAll().then(function (data) {
			widgets[0].dataSource.data = data;
			widgets[0].setCurrentRow(data[0]);
			ParamsMenuRepository.findAll().then(function (data) {
				widgets[1].dataSource.data = data;
				widgets[1].setCurrentRow(data[0]);
			});
		});

	};

	this.updateCancelObservations = function (row, callback) {
		var CDOCORR = row.CDOCORR;
		var custom = row.cancelMotive || "";
		var strObs = '';
		var __processObservations = function () {
			if (CDOCORR) {
				strObs = CDOCORR.map(function (obs) {
					return cancelObservations.filter(function (eachObs) {
						return eachObs.CDOCORR === obs;
					})[0].DSOCORR;
				}).join("; ");
			}
			if (custom) {
				if (strObs) {
					strObs += "; " + custom;
				} else {
					strObs = custom;
				}
			}
			row.TXPRODCOMVENCAN = strObs;
			if (callback) {
				callback();
			}
		};
		if (cancelObservations.length === 0) {
			updateCancelObservationsInner(__processObservations);
		} else {
			__processObservations();
		}
	};

	this.prepareCancelProduct = function (owner) {
		owner.widgets[0].currentRow = owner.currentRow;

		if (owner.currentRow.composicao)
			owner.widgets[0].fields[1].dataSource.data = owner.currentRow.composicao;
		else
			owner.widgets[0].fields[1].dataSource.data = [];
	};

	this.filterProductsFromPosition = function (position, selectProducts, popup, itemsWidget) {
		popup.setCurrentRow({});

		if (position != 1) position = position.owner.data('position') + 1;
		var productData = angular.copy(itemsWidget.dataSource.data);
		selectProducts.dataSource.data = _.sortBy(productData, 'POS').filter(function (p) {
			return p.POS != position;
		});
	};

	this.initTransferPopup = function (popup, itemsWidget) {
		popup.getField('positionsField').position = 0;
		popup.getField('positionsField').dataSource.data[0] = {
			NRPOSICAOMESA: itemsWidget.container.getWidget('accountDetailsTable').getField('NRPESMESAVEN').value()
		};
		this.filterProductsFromPosition(1, popup.getField('selectProducts'), popup, itemsWidget);
		ScreenService.openPopup(popup);
	};

	this.integrityControl = function (widget, popup) {
		selectControl = [];
		var selectedItem = widget.selectedRow;
		if (!selectedItem.__isSelected) selectControl.push(selectedItem.NRPRODCOMVEN);
		widget.dataSource.data.forEach(function (item) {
			if (!_.isEmpty(item.NRSEQPRODCOM)) {
				if (item.NRSEQPRODCOM == selectedItem.NRSEQPRODCOM && !_.isEqual(item, selectedItem)) {
					item.__isSelected = !selectedItem.__isSelected;
				}
			}
			if (item.__isSelected) selectControl.push(item.NRPRODCOMVEN);
		});
	};

	this.handleSelectButtons = function (widget, popup) {
		var removedProduct = null;
		var selectedProducts = popup.fields[1].value();
		selectControl.forEach(function (nrprodcomven) {
			if (!~selectedProducts.indexOf(nrprodcomven)) {
				removedProduct = nrprodcomven;
			}
		});

		if (removedProduct) {
			selectControl.splice(selectControl.indexOf(removedProduct), 1);
			removedProduct = widget.dataSource.data.filter(function (item) {
				return item.NRPRODCOMVEN == removedProduct;
			});
			var smartPromoCode = removedProduct[0].NRSEQPRODCOM;
			if (smartPromoCode) {
				var smartPromoProds = widget.dataSource.data.filter(function (item) {
					return item.NRSEQPRODCOM == smartPromoCode;
				});
				smartPromoProds = smartPromoProds.map(function (item) {
					return item.NRPRODCOMVEN;
				});
				selectedProducts = selectedProducts.filter(function (nrprodcomven) {
					return !~smartPromoProds.indexOf(nrprodcomven);
				});
				popup.fields[1].value(selectedProducts);
				var difference = _.difference(selectControl, selectedProducts);
				difference.forEach(function (diff) {
					selectControl.splice(selectControl.indexOf(diff), 1);
				});
			}
		}
	};

	this.transferPositions = function (widget, position, globalPositions) {
		if (widget.currentRow.selectProducts && widget.currentRow.selectProducts.length > 0) {
			var CDCLIENTE = null;
			var CDCONSUMIDOR = null;
			if (globalPositions.dataSource.data[0].clientMapping[position + 1]) {
				CDCLIENTE = globalPositions.dataSource.data[0].clientMapping[position + 1].CDCLIENTE;
			}
			if (globalPositions.dataSource.data[0].consumerMapping[position + 1]) {
				CDCONSUMIDOR = globalPositions.dataSource.data[0].consumerMapping[position + 1].CDCONSUMIDOR;
			}
			TableActiveTable.findOne().then(function (activeTable) {
				AccountService.transferPositions(widget.currentRow.selectProducts, position + 1, activeTable.NRVENDAREST, activeTable.NRCOMANDA, CDCLIENTE, CDCONSUMIDOR).then(function () {
					ScreenService.closePopup();
					AccountGetAccountDetails.findOne().then(function (accountDetails) {
						self.refreshAccountDetails(widget.container.getWidget('accountDetails').widgets, accountDetails.posicao, true);
					});
				});
			});
		}
		else {
			ScreenService.showMessage("Favor escolher pelo menos 1 produto para ser transferido.");
		}
	};

	this.cancelProduct = function (row, widget, IDPRODPRODUZ) {
		ParamsParameterRepository.findOne().then(function (params) {
			this.updateCancelObservations(row, function () {
				if (!(row.TXPRODCOMVENCAN) && params.IDSOLOBSCAN === 'S') {
					ScreenService.showMessage("Informe um motivo para o cancelamento.");
				} else {
					OperatorRepository.findAll().then(function (data) {
						var chave = data[0].chave;
						var selectedObs = _.get(row, 'CDOCORR', []);
						var motivo = [{
							'CDGRPOCOR': _.get(cancelObservations[0], 'CDGRPOCOR', []),
							'CDOCORR': selectedObs[selectedObs.length - 1] || [], // salva CDOCORR da última observação clicada
							'TXPRODCOMVEN': row.TXPRODCOMVENCAN
						}];
						var produto = [];
						produto.push(row.NRVENDAREST);
						produto.push(row.nrcomanda);
						produto.push(row.NRPRODCOMVEN);
						produto.push(row.CDPRODPROMOCAO);
						produto.push(row.NRSEQPRODCOM);
						produto.push(row.NRSEQPRODCUP);
						produto.push(row.codigo);
						produto.push(row.quantidade);
						produto.push(row.composicao);

						this.getAccountData(function (accountData) {
							AccountService.cancelProduct(chave, data[0].modoHabilitado, accountData[0].NRCOMANDA, accountData[0].NRVENDAREST, produto, motivo, widget._supervisor, IDPRODPRODUZ).then(function (response) {
								if (_.get(response, 'paramsImpressora')) {
									PerifericosService.print(response.paramsImpressora).then(function () {
										self.handleProducts(widget, row);
									});
								} else {
									self.handleProducts(widget, row);
								}
							});
						});
					}.bind(this));
				}
			}.bind(this));
		}.bind(this));
	};

	this.handleProducts = function (widget, row) {
		widget.dataSource.data = widget.dataSource.data.filter(function (item) {

			if (row.CDPRODPROMOCAO === null) {
				// Delete splited products
				if (item.IDDIVIDECONTA == 'S') {
					return item.NRVENDAREST !== row.NRVENDAREST ||
						item.nrcomanda !== row.nrcomanda ||
						item.codigo !== row.codigo ||
						item.NRPRODORIG !== row.NRPRODORIG;

				} else {
					return item.NRVENDAREST !== row.NRVENDAREST ||
						item.nrcomanda !== row.nrcomanda ||
						item.NRPRODCOMVEN !== row.NRPRODCOMVEN ||
						item.codigo !== row.codigo;
				}
			}
			/* If the removed product was a Smart Promo, removes all other products associated with it. */
			else {
				return (item.NRVENDAREST !== row.NRVENDAREST ||
					item.nrcomanda !== row.nrcomanda ||
					item.NRPRODCOMVEN !== row.NRPRODCOMVEN ||
					item.codigo !== row.codigo) &&
					item.NRSEQPRODCOM !== row.NRSEQPRODCOM;
			}
		});

		if (widget.dataSource.data.length === 0) {
			WindowService.openWindow('MENU_SCREEN');
		}
	};

	this.removeFromCart = function (row, widget, stripe) {
		ScreenService.confirmMessage(
			'Remover produto ' + row.DSBUTTON + '?',
			'question',
			function () {
				AccountCart.findAll().then(function (cart) {
					cart = _.filter(cart, function (itemCart) {
						return itemCart.IDENTIFYKEY !== row.IDENTIFYKEY;
					}.bind(this));
					return AccountCart.remove(Query.build()).then(function () {
						return AccountCart.save(cart);
					});
				}.bind(this)).then(function () {
					return CartPool.findAll().then(function (cartPool) {
						cartPool = _.filter(cartPool, function (itemCart) {
							return itemCart.IDENTIFYKEY !== row.IDENTIFYKEY;
						}.bind(this));
						return CartPool.remove(Query.build()).then(function () {
							return CartPool.save(cartPool);
						});
					}.bind(this));
				}).then(function () {
					widget.dataSource.data = _.reverse(_.filter(widget.dataSource.data, function (currentItem) {
						return currentItem.IDENTIFYKEY !== row.IDENTIFYKEY;
					}.bind(this)));
					self.produtosDesistencia([row]);
					if (widget.dataSource.data.length === 0) {
						OperatorRepository.findOne().then(function (operatorData) {
							if (operatorData.modoHabilitado === 'B') {
								CarrinhoDesistencia.remove(Query.build());
							}
							if (operatorData.modoHabilitado === 'O') {
								WindowService.openWindow('ORDER_MENU_SCREEN');
							} else {
								WindowService.openWindow('MENU_SCREEN');
							}
						}.bind(this));
					} else {
						self.prepareCart(widget, stripe);
					}
				}.bind(this));
			}.bind(this),
			function () { }
		);
	};

	this.removePromoItem = function (row, widget) {
		ScreenService.confirmMessage(
			'Remover o item ' + row.DSBUTTON + '?',
			'question',
			function () {
				widget.dataSource.data = widget.dataSource.data.filter(function (itemCart) {
					return itemCart.ID !== row.ID;
				});

				if (row.IDAPLICADESCPR === 'I' && row.DISCOUNT !== 0) {
					for (var i in widget.dataSource.data) {
						if (widget.dataSource.data[i].CDPRODUTO === row.CDPRODUTO) {
							widget.dataSource.data[i].DISCOUNT = row.DISCOUNT;
							widget.dataSource.data[i].ADDITION = row.ADDITION;
							widget.dataSource.data[i].PRICE = row.PRICE;
							widget.dataSource.data[i].STRDESCONTO = row.STRDESCONTO;
							widget.dataSource.data[i].STRPRICE = row.STRPRICE;
							widget.dataSource.data[i].VRDESPRODPROMOC = row.VRDESPRODPROMOC;
							widget.dataSource.data[i].IDDESCACRPROMO = row.IDDESCACRPROMO;

							break;
						}
					}
				}

				this.savePromo(widget.dataSource.data, function () {
					if (widget.dataSource.data.length === 0) {
						WindowService.openWindow('PROMO_SCREEN');
					}
				});
			}.bind(this),
			function () { }
		);
	};

	this.saveCart = function (widgetData, callback) {
		AccountCart.remove(Query.build()).then(function () {
			AccountCart.save(widgetData).then(function () {
				if (callback) {
					callback();
				}
			}.bind(this));
		});
	};

	this.savePromo = function (widgetData, callback) {
		SmartPromoTray.remove(Query.build()).then(function () {
			SmartPromoTray.save(widgetData);
			if (callback) {
				callback();
			}
		});
	};

	this.showAccountDetails = function () {
		OperatorRepository.findAll().then(function (operatorData) {
			var screenToOpen = '';
			if ((operatorData[0].modoHabilitado === 'C') || operatorData[0].modoHabilitado === 'O' || (operatorData[0].IDLUGARMESA === 'N')) {
				screenToOpen = 'ACCOUNT_DETAILS_SCREEN_BILL';
			} else {
				screenToOpen = 'ACCOUNT_DETAILS_SCREEN';
			}
			WindowService.openWindow(screenToOpen);
		});
	};

	// esta função é utilizada na fechar conta
	this.prepareAccountDetails = function (widget, callback) {
		ApplicationContext.OrderController.checkAccess(function () {

			widget.activate();
			OperatorRepository.findAll().then(function (operatorData) {
				this.getAccountData(function (accountData) {

					// prepara parâmetros para chamar a getAccountDetails (traz desconto, consumação, serviço, total, couvert e produtos)
					var chave = operatorData[0].chave;
					var modoHabilitado = operatorData[0].modoHabilitado;
					if (modoHabilitado === 'O') {
						modoHabilitado = 'M';
					}
					var nrComanda = accountData[0].NRCOMANDA;
					var nrVendaRest = accountData[0].NRVENDAREST;

					AccountService.getAccountDetails(chave, modoHabilitado, nrComanda, nrVendaRest, 'M', '').then(function (accountDetailsData) {
						if (accountDetailsData.nothing[0].nothing === 'nothing') {
							var total = accountDetailsData.AccountGetAccountDetails[0].vlrtotal;

							if (total !== 0 && operatorData[0].modoHabilitado === 'O') {
								accountDetailsData.AccountGetAccountDetails[0].total = UtilitiesService.toCurrency(total);
								total = "total: " + UtilitiesService.toCurrency(total);
								accountDetailsData.AccountGetAccountDetails[0].labeltotal = total;
							}

							widget.dataSource.data = accountDetailsData.AccountGetAccountDetails;
							widget.moveToFirst();
							self.isVisibleAccountItems(widget.container, modoHabilitado, operatorData[0].IDLUGARMESA);

							// caso não tenha nenhum pedido realizado, pergunta se deseja cancelar a abertura da mesa
							if (total === 0 && (modoHabilitado === 'M' || modoHabilitado === 'C')) {
								var mode = modoHabilitado === 'M' ? 'mesa' : 'comanda';
								ScreenService.confirmMessage(
									'Não foi realizado nenhum pedido para esta ' + mode + ', deseja cancelar a abertura?',
									'question',
									function () {
										if (modoHabilitado === 'M') {
											TableService.cancelOpen(operatorData[0].chave, accountData[0].NRMESA).then(function () {
												UtilitiesService.backMainScreen();
											});
										} else {
											BillService.cancelOpen(operatorData[0].chave, accountData[0].NRMESA, accountData[0].NRVENDAREST, accountData[0].NRCOMANDA).then(function () {
												UtilitiesService.backMainScreen();
											});
										}
									},
									function () {
										WindowService.openWindow('MENU_SCREEN');
									}
								);
							} else if (modoHabilitado === 'O') { // modo order
								if (total === 0) {
									ScreenService.showMessage('Não foi realizado nenhum pedido para esta mesa, favor solicitar o fechamento ao garçom.');
									UtilitiesService.backMainScreen();
								}
							} else { // caso tenha pedidos, prepara a tela
								ParamsParameterRepository.findOne().then(function (params) {
									accountDetailsData.AccountGetAccountDetails[0].swicouvert = accountDetailsData.AccountGetAccountDetails[0].swiconsumacao = true;
									var currentRow;
									//Verificação de couvert para preparação de tela no modo comanda.
									if (params.IDCOUVERART != "N" && modoHabilitado !== 'M') {
										if (widget.getField('couvert').value() == '0,00') {
											accountDetailsData.AccountGetAccountDetails[0].swicouvert = false;
											currentRow = widget.container.getWidget('closeAccount').currentRow;
											currentRow.vlrcouvert = UtilitiesService.truncValue(parseFloat(accountDetailsData.AccountGetAccountDetails[0].NRPESMESAVEN) * operatorData[0].PRECOCOUVERT);
										}
									} else if (params.IDCOUVERART === "N") {
										accountDetailsData.AccountGetAccountDetails[0].swicouvert = false;
									}
									if (operatorData[0].IDCOMISVENDA != "N") {
										if (widget.getField('servico').value() == '0,00') {
											accountDetailsData.AccountGetAccountDetails[0].swiservico = false;
											currentRow = widget.container.getWidget('closeAccount').currentRow;
											currentRow.vlrservico = Math.trunc(params.VRCOMISVENDA * currentRow.vlrprodcobtaxa) / 100;
											self.recalcPrice(currentRow);
										} else accountDetailsData.AccountGetAccountDetails[0].swiservico = true;
									} else accountDetailsData.AccountGetAccountDetails[0].swiservico = false;
								});
								if (modoHabilitado === 'M') {
									TableActiveTable.findAll().then(function (activeTable) {
										widget.getField('positions').dataSource.data = activeTable;
									});
								}
							}
							if (jQuery.isFunction(callback)) {
								callback();
							}
						} else {
							UtilitiesService.backMainScreen();
						}
					}.bind(this));
				}.bind(this));
			}.bind(this));
		}.bind(this));
	};

	this.refreshAccountDetailsPositionClick = function (widgetsFilhos, args) {
		var p = args.owner.data('position') + 1;
		if (p < 10) p = "0" + p;
		this.refreshAccountDetails(widgetsFilhos, p);
	};

	this.resetAccountScreen = function () {
		this.oldPageDetailsName = '';
		this.oldPosition = null;
	};

	this.resetAccountScreen();

	this.isClosingBill = false;
	this.setCloseBill = function setCloseBill(flag) {
		this.isClosingBill = flag;
	};

	this.setBillAction = function setCloseBillAction(widget) {
		var actionReceber = widget.getAction('receber');
		var actionImprimir = widget.getAction('imprimir');
		if (actionReceber && actionImprimir) {
			if (this.isClosingBill) {
				widget.label = 'Receber Comanda';
				widget.container.label = 'Receber Comanda';
				actionReceber.isVisible = true;
				actionImprimir.isVisible = false;
			} else {
				widget.label = 'Parcial da Conta';
				widget.container.label = 'Parcial da Conta';
				actionReceber.isVisible = false;
				actionImprimir.isVisible = true;
			}
		}
		widget.activate();
	};

	// esta função é utilizada na parcial do waiter
	// usar quando o template for "waiter_position"
	this.refreshAccountDetails = function (widgetsFilhos, position, forceRefresh) {
		// pega as duas tabs
		var pageDetails = widgetsFilhos[0];
		var pageItems = widgetsFilhos[1];

		this.setBillAction(pageDetails);

		// validação para evitar disparar múltiplos eventos
		if ((pageDetails.name !== this.oldPageDetailsName) || (position !== this.oldPosition) || forceRefresh) {
			this.oldPageDetailsName = pageDetails.name;
			this.oldPosition = position;

			if (pageDetails.dataSource.data && pageDetails.dataSource.data.length > 0) {
				delete pageDetails.dataSource.data;
			}
			pageDetails.container.restoreDefaultMode();
			pageDetails.setCurrentRow({});

			this.getAccountData(function (accountData) {
				OperatorRepository.findAll().then(function (params) {
					AccountService.getAccountDetails(params[0].chave, params[0].modoHabilitado, accountData[0].NRCOMANDA, accountData[0].NRVENDAREST, 'M', position).then(function (databack) {
						var accountDetails = databack.AccountGetAccountDetails[0];

						var dataset = {
							NRPESMESAVEN: accountDetails.NRPESMESAVEN,
							consumacao: accountDetails.consumacao,
							vlrconsumacao: accountDetails.vlrconsumacao,
							couvert: accountDetails.couvert,
							vlrcouvert: accountDetails.vlrcouvert,
							permanencia: accountDetails.permanencia,
							produtos: accountDetails.produtos,
							vlrprodutos: accountDetails.vlrprodutos,
							vlrprodcobtaxa: accountDetails.vlrprodcobtaxa,
							desconto: accountDetails.desconto,
							vlrdesconto: accountDetails.vlrdesconto,
							servico: accountDetails.servico,
							vlrservico: accountDetails.vlrservico,
							total: accountDetails.total,
							vlrtotal: accountDetails.vlrtotal,
							valorPago: accountDetails.valorPago,
							totalSubsidy: accountDetails.totalSubsidy,
							swiconsumacao: accountDetails.swiconsumacao,
							swicouvert: accountDetails.swicouvert,
							swiservico: accountDetails.swiservico,
							realSubsidy: accountDetails.realSubsidy,
							fidelityValue: accountDetails.fidelityValue,
							fidelityDiscount: accountDetails.fidelityDiscount,
							numeroProdutos: accountDetails.numeroProdutos,
							posicao: accountDetails.posicao,
							NMVENDEDORABERT: accountDetails.NMVENDEDORABERT
						};
						pageDetails.setCurrentRow(dataset);

						if (pageItems.dataSource.data && pageItems.dataSource.data.length > 0) {
							delete pageItems.dataSource.data;
						}

						AccountService.getTableTransactions(accountData[0].NRMESA, "T", accountData[0].NRVENDAREST, params[0].chave).then(function (tableTransactions) {
							if (!position && templateManager.container.getWidget('accountShort')) {
								if (tableTransactions[0].PAGAMENTOMESA !== 0) {
									templateManager.container.getWidget('accountShort').isVisible = false;
								}
								else {
									templateManager.container.getWidget('accountShort').isVisible = true;
								}
							}
						});

						if (Util.isArray(databack.AccountGetAccountItems) && Util.isEmptyOrBlank(databack.AccountGetAccountItems[0])) {
							databack.AccountGetAccountItems = [];
						} else {
							for (var i in databack.AccountGetAccountItems) {
								if (parseFloat(databack.AccountGetAccountItems[i].quantidade.replace(',', '.')) != 1) {
									databack.AccountGetAccountItems[i].DSBUTTON = databack.AccountGetAccountItems[i].quantidade.toString().replace('.', ',') + " x " + databack.AccountGetAccountItems[i].DSBUTTON;
								}
							}
						}

						if (pageDetails.getField('servicoBtn')) {
							pageDetails.getField('servicoBtn').isVisible = params[0].IDCOMISVENDA == "S";
						}

						pageItems.dataSource.data = databack.AccountGetAccountItems;
						templateManager.updateTemplate();
					}.bind(this));
				}.bind(this));
			}.bind(this));
		}
	};

	// esta função é utilizada no pagamento do waiter
	//Usar quando o template for "waiter_position_multiple"
	this.refreshAccountDetailsMultiplePositions = function (widgetsFilhos, position, positionsField) {
		// pega as duas tabs
		var pageDetails = widgetsFilhos[0];
		var pageItems = widgetsFilhos[1];

		if (Number.isInteger(position)) {
			position = [position];
		}

		var messageWidget = positionsField.widget.widgets[2];
		if (messageWidget && (position === undefined || position.length === 0)) {
			messageWidget.activate();
		}

		if (!positionsField._isStatusChanged && (position !== undefined)) {
			return false;
		}

		if (position === undefined || (Array.isArray(position) && position.length === 0)) {
			pageDetails.isVisible = false;
			pageItems.isVisible = false;
			if (messageWidget) {
				messageWidget.isVisible = true;
				messageWidget.activate();
			}
		} else {
			pageDetails.isVisible = true;
			pageItems.isVisible = true;
			if (messageWidget) {
				messageWidget.isVisible = false;
			}
			pageDetails.activate();
		}

		if (Array.isArray(position)) {
			position = position.length === 0 ? "" : position;
		}
		if (Array.isArray(position)) {
			position = position.map(function (p) {
				return ++p;
			});
		}
		if (Number.isInteger(position)) {
			++position;
			if (position < 10) {
				position = "0" + position;
			}
		}

		if (pageDetails.dataSource.data && pageDetails.dataSource.data.length > 0) {
			pageDetails.dataSource.data = [];
		}
		pageDetails.container.restoreDefaultMode();

		// validação para evitar disparar múltiplos eventos
		if ((pageDetails.name !== this.oldPageDetailsName) || (position !== this.oldPosition)) {
			this.oldPageDetailsName = pageDetails.name;
			this.oldPosition = position;

			if (position !== undefined && position.length > 0) {
				this.getAccountData(function (accountData) {
					OperatorRepository.findAll().then(function (params) {
						AccountService.getAccountDetails(params[0].chave, params[0].modoHabilitado, accountData[0].NRCOMANDA, accountData[0].NRVENDAREST, 'M', position).then(function (databack) {
							pageDetails.currentRow = databack.AccountGetAccountDetails[0];

							if (pageItems.dataSource.data && pageItems.dataSource.data.length > 0) {
								pageItems.dataSource.data = [];
							}
							if (Util.isArray(databack.AccountGetAccountItems) && Util.isEmptyOrBlank(databack.AccountGetAccountItems[0])) {
								databack.AccountGetAccountItems = [];
							}
							pageItems.dataSource.data = databack.AccountGetAccountItems;
							templateManager.updateTemplate();
							positionsField._isStatusChanged = false;
						}.bind(this));
					}.bind(this));
				}.bind(this));
			}
		}
	};

	this.printAccount = function (position) {
		if (!(position)) {
			position = "";
		}
		this.getAccountData(function (accountData) {
			OperatorRepository.findAll().then(function (params) {
				AccountService.getAccountDetails(params[0].chave, params[0].modoHabilitado, accountData[0].NRCOMANDA, accountData[0].NRVENDAREST, 'I', position).then(function (data) {
					if (!position) {
						UtilitiesService.backMainScreen();
					}
					if (_.get(data, 'dadosImpressao.dadosImpressao.paramsImpressora.saas')) {
						PerifericosService.print(data.dadosImpressao.dadosImpressao.paramsImpressora).then(function (response) {
							if (response.error) {
								ScreenService.showMessage(response.message);
							}
						});
					} else {
						self.handlePrintBill(data.dadosImpressao);
					}
				});
			});
		});
	};

	this.handlePrintBill = function (dadosImpressao) {
		if (!_.isEmpty(dadosImpressao)) {
			PrinterService.printerCommand(PrinterService.TEXT_COMMAND, dadosImpressao.dadosImpressao);
			PrinterService.printerSpaceCommand();
			PrinterService.printerInit().then(function (result) {
				if (result.error)
					ScreenService.alertNotification(result.message);
			});
		}
	};

	this.paymentAccount = function (widget, positions) {
		var CDTIPORECE = widget.currentRow.CDTIPORECE;
		var DSBANDEIRA = widget.currentRow.CDBANCARTCR;
		var IDDESABTEF = widget.currentRow.IDDESABTEF;
		var paymentValue = widget.currentRow.lblValorTotal.replace(',', '.');
		var IDTIPORECE = widget.currentRow.IDTIPORECE;
		positions = positions ? positions : "";

		self.getAccountData(function (accountData) {
			OperatorRepository.findAll().then(function (params) {
				if (self.validatePaymentFields(widget, params[0].IDTPTEF)) {
					AccountService.getAccountDetails(params[0].chave, params[0].modoHabilitado, accountData[0].NRCOMANDA, accountData[0].NRVENDAREST, 'P', positions).then(function (data) {
						if (self.isTEF(widget, params[0].IDTPTEF) || self.isSITEF(widget, params[0].IDTPTEF))
							IDTPTEF = params[0].IDTPTEF;
						else
							IDTPTEF = null;

						var dataset = {
							chave: params[0].chave,
							CDVENDEDOR: params[0].CDVENDEDOR,
							NRVENDAREST: accountData[0].NRVENDAREST,
							NRMESA: accountData[0].NRMESA,
							NRLUGARMESA: (data.AccountGetAccountDetails[0].posicao === "" ? "T" : data.AccountGetAccountDetails[0].posicao[0]),
							CDTIPORECE: CDTIPORECE,
							IDTIPMOV: IDTIPORECE,
							VRMOV: parseFloat(paymentValue),
							NRCOMANDA: accountData[0].NRCOMANDA,
							DSBANDEIRA: DSBANDEIRA,
							IDTPTEF: IDTPTEF,
							PRODUCTS: data.AccountGetAccountItems
						};

						if (dataset.NRLUGARMESA != "T" && positions.length > 1) {
							var TRANSFER = [];

							var nrmesa = dataset.NRMESA;
							var nrcomanda = dataset.NRCOMANDA;
							var nrvendarest = dataset.NRVENDAREST;

							dataset.PRODUCTS.forEach(function (product) {
								var produto = {
									NRVENDAREST: product.NRVENDAREST,
									NRCOMANDA: product.nrcomanda,
									NRPRODCOMVEN: product.NRPRODCOMVEN,
									quantidade: product.quantidade
								};

								if (product.POS == parseInt(dataset.NRLUGARMESA)) {
									nrmesa = product.NRMESA;
									nrcomanda = product.nrcomanda;
									nrvendarest = product.NRVENDAREST;
								}

								TRANSFER.push(produto);
							});

							// Transfere os itens das posicoes selecionadas para a primeira posiçao selecionada
							TableService.transferItem(dataset.chave, nrmesa, nrcomanda, nrvendarest, TRANSFER, dataset.NRLUGARMESA, null, positions.length).then(function (response) {

								if (response[0].error) {
									ScreenService.showMessage(response[0].error);
								}
								else {
									TransactionsService.moveTransactions(dataset.chave, dataset.NRVENDAREST, dataset.NRCOMANDA, dataset.NRLUGARMESA, positions).then(function (response) {
										if (response[0].error) {
											ScreenService.showMessage(response[0].error);
										}
										else {
											self.pay(widget, dataset, IDTIPORECE, paymentValue);
										}
									});
								}
							});
						}
						else {
							self.pay(widget, dataset, IDTIPORECE, paymentValue);
						}
					});
				}
			});
		});
	};

	this.pay = function (widget, dataset, IDTIPORECE, paymentValue) {
		AccountService.beginPaymentAccount(dataset).then(function (response) {
			self.NRSEQMOVMOB = response[0].NRSEQMOVMOB;
			// Verifica se a opção marcada é Crédito Digitado.
			if (self.isSITEF(widget, dataset.IDTPTEF)) {
				self.typedCredit(widget, self.NRSEQMOVMOB);
			}
			else if (self.isTEF(widget, dataset.IDTPTEF)) {
				if (window.ZhNativeInterface && ZhNativeInterface.tefPayment) {
					ZhNativeInterface.tefPayment(dataset.IDTPTEF, IDTIPORECE, paymentValue, self.NRSEQMOVMOB);
				}
				else {
					self.tefmock(); // Para teste no computador
					// ScreenService.showMessage('ZhNativeInterface não encontrada.');
				}
			}
			else {
				dataset = {
					NRSEQMOVMOB: self.NRSEQMOVMOB,
					NRSEQMOB: null,
					DSBANDEIRA: null,
					NRADMCODE: null,
					IDADMTASK: '0',
					IDSTMOV: '1',
					TXMOVUSUARIO: null,
					TXMOVJSON: null,
					CDNSUTEFMOB: null,
					TXPRIMVIATEF: null,
					TXSEGVIATEF: null,
					transactionStatus: '1'
				};
				self.finishPayment(dataset);
			}
		});
	};

	this.typedCredit = function (widget, NRSEQMOVMOB) {
		var row = widget.currentRow;
		var paymentValue = widget.currentRow.lblValorTotal.replace(',', '.');

		var dataset = {
			amount: paymentValue,
			idAutorizadora: row.CDBANCARTCR,
			dtVencimento: row.cardExpiration.replace('/', ''),
			numCartao: row.cardNumber.replace(/ /g, ''),
			codSeguranca: row.securityCode
		};

		AccountService.typedCreditPayment(dataset, NRSEQMOVMOB).then(function (response) {
			window.tefResult(response);
		});
	};

	window.tefResult = function (result) {
		var capptaErrors = {
			1: 'Não autenticado/Alguma das informações fornecidas para autenticação não é válida',
			2: 'Cappta Android está sendo inicializado',
			3: 'Formato da requisição recebida pelo Cappta Android é inválido',
			4: 'Operação cancelada pelo operador',
			5: 'Pagamento não autorizado/pendente/não encontrado',
			6: 'Pagamento ou cancelamento negados pela rede adquirente ou falta de conexão com internet',
			7: 'Erro interno no Cappta Android',
			8: 'Erro na comunicação com o Cappta Android'
		};

		var JSONTEF = JSON.parse(result)[0];
		var userMessage = _.get(JSONTEF, 'tef_request_details.user_message');
		if (userMessage == "Transação aceita") {
			var dataset = self.createUpdateTransactionObject(JSONTEF);
			self.finishPaymentCappta(dataset);
		} else {
			var defaultMessage = _.get(capptaErrors, _.get(JSONTEF, 'tef_request_type'), 'Falha na comunicação com o aplicativo Cappta. Verifique se o mesmo está instalado.');
			ScreenService.showMessage(userMessage || defaultMessage);
		}
	};

	this.isNotEmpty = function (value) {
		if (_.isString(value)) {
			return !_.isEmpty(value);
		} else {
			return !_.isNil(value);
		}
	};

	this.createUpdateTransactionObject = function (JSONTEF) {
		var JSONTEFDetails = JSONTEF.tef_request_details;
		var dataset = {};
		dataset.JSONTEFDetails = JSONTEFDetails;
		dataset.NRSEQMOVMOB = self.NRSEQMOVMOB;
		dataset.TXMOVJSON = JSON.stringify(JSONTEF);

		dataset.NRSEQMOB = self.isNotEmpty(JSONTEFDetails.unique_sequential_number) ? JSONTEFDetails.unique_sequential_number : null;
		dataset.DSBANDEIRA = self.isNotEmpty(JSONTEFDetails.card_brand_name) ? JSONTEFDetails.card_brand_name : null;
		dataset.NRADMCODE = self.isNotEmpty(JSONTEFDetails.administrative_code) ? JSONTEFDetails.administrative_code : null;
		dataset.IDADMTASK = self.isNotEmpty(JSONTEFDetails.administrative_task) ? JSONTEFDetails.administrative_task : null;
		dataset.IDSTMOV = self.isNotEmpty(JSONTEFDetails.payment_transaction_status) ? JSONTEFDetails.payment_transaction_status : null;
		dataset.TXMOVUSUARIO = self.isNotEmpty(JSONTEFDetails.user_message) ? JSONTEFDetails.user_message : null;
		dataset.CDNSUTEFMOB = self.isNotEmpty(JSONTEFDetails.unique_sequential_number) ? JSONTEFDetails.unique_sequential_number : null;
		dataset.TXPRIMVIATEF = self.isNotEmpty(JSONTEFDetails.merchant_receipt) ? JSONTEFDetails.merchant_receipt.replace(/'/g, '') : null;
		dataset.TXSEGVIATEF = self.isNotEmpty(JSONTEFDetails.customer_receipt) ? JSONTEFDetails.customer_receipt.replace(/'/g, '') : null;
		dataset.transactionStatus = self.isNotEmpty(JSONTEFDetails.payment_transaction_status) ? JSONTEFDetails.payment_transaction_status : null;

		return dataset;
	};

	this.tefmock = function () {
		// not mocking at the moment
		if (false) {
			var result = [
				{
					"tef_request_type": 4,
					"tef_request_details":
					{
						"payment_transaction_status": 1,
						"acquirer_affiliation_key": "0009448512329101",
						"acquirer_name": "Elavon",
						"card_brand_name": "MAESTRO",
						"acquirer_authorization_code": "SIMULADOR",
						"payment_product": 1,
						"payment_installments": 1,
						"payment_amount": 16,
						"available_balance": null,
						"unique_sequential_number": 21007,
						"acquirer_unique_sequential_number": null,
						"acquirer_authorization_datetime": "2016-07-15 11:25:42",
						"administrative_code": "07520701019",
						"administrative_task": 0,
						"user_message": null,
						"merchant_receipt": "''\r\n'**VIA LOJISTA**'\r\n'ELAVON'\r\n'MAESTRO-DEBITO A VISTA'\r\n'************2979'\r\n'ESTAB000948512329101'\r\n'15/07/16 10:25:50'\r\n'AUT=SIMULADOR DOC=21007'\r\n'VALOR=1,50'\r\n'CONTROLE=07520701019'\r\n'SIMULADO'",
						"customer_receipt": "''\r\n'HOMOLOGA'\r\n'40.841.182/0001-48'\r\n'**VIA CLIENTE**'\r\n'ELAVON'\r\n'MAESTRO-DEBITO A VISTA'\r\n'************2979'\r\n'ESTAB000948512329101'\r\n'15/07/16 10:25:50'\r\n'AUT=SIMULADOR DOC=21007'\r\n'VALOR=1,50'\r\n'CONTROLE=07520701019'\r\n'SIMULADO'",
						"reduced_receipt": "'ELAVON-NL000948512329101'\r\n'MAESTRO-************2679'\r\n'AUT=SIMULADOR DOC=21007'\r\n'VALOR=1,50 CONTROLE=07520701019'"
					}
				}
			];
			var resultString = JSON.stringify(result);
			window.tefResult(resultString);
		} else {
			ScreenService.showMessage('A Webview do Android não foi encontrada.');
		}
	};

	this.finishPaymentCappta = function (dataset) {
		this.getAccountData(function (accountData, operatorData) {
			dataset.NRVENDAREST = accountData[0].NRVENDAREST;
			dataset.NRCOMANDA = accountData[0].NRCOMANDA;
			dataset.chave = operatorData.chave;
			AccountService.finishPaymentAccount(dataset).then(function (response) {
				if (dataset.transactionStatus === 1 && dataset.JSONTEFDetails !== null) {
					ScreenService.openWindow('accountPayment').then(function () {
						var emailPopup = templateManager.container.getWidget("popupEmail");
						emailPopup.currentRow = response[0].payments[0];
						emailPopup.currentRow.RECEIPT = dataset.JSONTEFDetails.customer_receipt;
						emailPopup.currentRow.RECEIPT = emailPopup.currentRow.RECEIPT.replace(/'/g, '');
						ScreenService.openPopup(emailPopup);
					});
				} else if (dataset.transactionStatus === 1 && dataset.JSONTEFDetails === null) {
					ScreenService.showMessage("Pagamento realizado com sucesso.");
					ScreenService.closePopup();
				} else {
					ScreenService.showMessage(dataset.TXMOVUSUARIO);
				}

				var position;
				var currentWidget;

				if (response[0].tableClosed) {
					UtilitiesService.backMainScreen();
				} else {
					if (response[0].NRLUGARMESA == "T") {
						position = "";
						currentWidget = templateManager.container.getWidget('accountDetails');
						this.refreshAccountDetails(currentWidget.widgets, position);
					} else {
						// position = response[0].NRLUGARMESA;
						// position = templateManager.container.getWidget('accountShort').getField("positionswidget").position;
						templateManager.container.getWidget('accountShort').getField("positionswidget")._isStatusChanged = true;
						currentWidget = templateManager.container.getWidget('accountShort');
						//Carrega com nenhuma posição selecionada
						this.refreshAccountDetailsMultiplePositions(currentWidget.widgets, undefined);
					}
				}
			}.bind(self));
		});
	};

	this.finishPayment = function (dataset) {
		AccountService.finishPaymentAccount(dataset).then(function (response) {
			if (dataset.transactionStatus === 1 && dataset.JSONTEFDetails !== null) {
				WindowService.openWindow('PAYMENT_SCREEN').then(function () {
					var emailPopup = templateManager.container.getWidget("popupEmail");
					emailPopup.currentRow = response[0];
					emailPopup.currentRow.RECEIPT = dataset.JSONTEFDetails.customer_receipt;
					emailPopup.currentRow.RECEIPT = emailPopup.currentRow.RECEIPT.replace(/'/g, '');
					ScreenService.openPopup(emailPopup);
				});
			} else if (dataset.transactionStatus === 1 && dataset.JSONTEFDetails === null) {
				ScreenService.showMessage("Transação aceita!");
			} else {
				ScreenService.showMessage(dataset.TXMOVUSUARIO);
			}

			var position;
			var currentWidget;

			if (response[0].NRLUGARMESA === "T") {
				position = "";
				currentWidget = templateManager.container.getWidget('accountDetails');
				this.refreshAccountDetails(currentWidget.widgets, position);
			}
			else {
				templateManager.container.getWidget('accountShort').getField("positionswidget")._isStatusChanged = true;
				currentWidget = templateManager.container.getWidget('accountShort');
				// carrega com nenhuma posição selecionada
				this.refreshAccountDetailsMultiplePositions(currentWidget.widgets, undefined, templateManager.container.getWidget('accountShort').getField("positionswidget"));
			}
		}.bind(self));
	};

	this.validatePaymentValue = function (widget) {
		if (parseFloat(widget.currentRow.lblValorTotal) > parseFloat(widget.getField('lblValorTotal').validations.range.max)) {

			widget.currentRow.lblValorTotal = widget.getField('lblValorTotal').validations.range.max;
			ScreenService.showMessage('Digite um Valor Válido!');
		}
	};

	this.validatePaymentMethod = function (widget) {
		OperatorRepository.findAll().then(function (operatorData) {
			widget.fieldGroups[1].isVisible = self.isSITEF(widget, operatorData[0].IDTPTEF);
		});
		return true;
	};

	this.validatePaymentFields = function (widget, IDTPTEF) {
		if (this.isSITEF(widget, IDTPTEF)) return widget.isValid();
		return !!widget.currentRow.lblValorTotal && !!widget.currentRow.paymentType;
	};

	this.isSITEF = function (widget, IDTPTEF) {
		return (parseFloat(widget.currentRow.CDBANCARTCR || 0) > 0 && widget.currentRow.IDTIPORECE == 1 && IDTPTEF == 1);
	};

	this.isTEF = function (widget, IDTPTEF) {
		return (widget.currentRow.IDDESABTEF == 'N' && (widget.currentRow.IDTIPORECE == 1 || widget.currentRow.IDTIPORECE == 2) && IDTPTEF == 2);
	};

	this.sendTransactionEmail = function (args) {
		var DSEMAILCLI = args.currentRow.DSEMAILCLI;
		var NRSEQMOVMOB = args.currentRow.NRSEQMOVMOB;

		TransactionsService.updateTransactionEmail(DSEMAILCLI, NRSEQMOVMOB);
		TransactionsService.sendTransactionEmail(NRSEQMOVMOB, DSEMAILCLI).then(function (response) {
			ScreenService.closePopup();
			//IMPLEMENTAR RETORNO DE MENSAGEM.
			ScreenService.showMessage("E-mail enviado com sucesso!");
		});
	};

	this.selectedProduct = {};

	this.prepareCheckOrder = function (product, field, widget, listaFilhos, stripe) {
		OperatorRepository.findAll().then(function (operatorData) {
			if (this.selectedProduct !== product) {
				field.dataSource.data = this.getObservations(product.OBSERVATIONS);
				widget.currentRow = product;
				this.selectedProduct = product;
				if (!_.isEmpty(product.PRODUTOS)) {
					listaFilhos.dataSource.data = product.PRODUTOS;
					widget.getField('CDOCORR').isVisible = false;
					widget.getField('DSOCORR_CUSTOM').isVisible = false;
				}
				else {
					listaFilhos.dataSource.data = [];
					widget.getField('CDOCORR').isVisible = true;
					widget.getField('DSOCORR_CUSTOM').isVisible = true;
				}

				/* Define se será mostrado o checkbox de atraso de produtos. */
				if (operatorData[0].modoHabilitado !== 'O') {
					widget.getField('ATRASOPROD').isVisible = (operatorData[0].NRATRAPADRAO > 0);
				}

				widget.getField('TOGO').isVisible = operatorData[0].IDCTRLPEDVIAGEM === 'S';

				if (operatorData[0].IDUTLQTDPED === 'S') {
					widget.getField('QTPRODCOMVEN').isVisible = true;
					widget.getField('QTPRODCOMVEN').spin = true;
					widget.getField('QTPRODCOMVEN').label = "Quantidade (un)";
					widget.getField('QTPRODCOMVEN').blockInputEdit = true;
				} else {
					widget.getField('QTPRODCOMVEN').isVisible = false;
				}
				if (product.IDPESAPROD === 'S') {
					widget.getField('QTPRODCOMVEN').isVisible = true;
					widget.getField('QTPRODCOMVEN').spin = false;
					widget.getField('QTPRODCOMVEN').label = "Quantidade (kg)";
					widget.getField('QTPRODCOMVEN').blockInputEdit = false;
				}

				handlePrintersProductForRoom(product).then(function (printers) {
					var printersField = widget.getField('NRSEQIMPRLOJA');
					printersField.isVisible = printers.length > 1;
					printersField.dataSource.data = printers;
				});
				templateManager.updateTemplate();
			}
		}.bind(this));
	};

	this.openSmartPromoObservationChangePopup = function (product, popup) {
		OperatorRepository.findAll().then(function (operatorData) {
			if (product) {
				popup.fields[0].dataSource.data = this.getObservations(product.OBSERVATIONS);
				popup.currentRow = product;
				ScreenService.openPopup(popup);
			}
		}.bind(this));
	};

	this.saveSmartPromoObservationsChanges = function (widget) {
		widget.dataSource.data.forEach(function (cart) {
			if (!_.isEmpty(cart.PRODUTOS)) {
				var obs = self.obsToText(cart.CDOCORR, cart.DSOCORR_CUSTOM) + " ";
				for (var j in cart.PRODUTOS) {
					obs += cart.PRODUTOS[j].TXPRODCOMVEN + " ";
				}
				cart.TXPRODCOMVEN = obs.replace(/^ +/, ''); // Remove os espaços do inicio.
			}
			self.calcProductValue(cart);
		}.bind(this));

		AccountCart.findAll().then(function (cart) {
			cart = _.map(cart, function (itemCart) {
				if (itemCart.IDENTIFYKEY == widget.currentRow.IDENTIFYKEY) {
					itemCart = widget.currentRow;
				}
				return itemCart;
			});
			cart.reverse();
			AccountCart.remove(Query.build()).then(function () {
				AccountCart.save(cart);
			}.bind(this));
		});

		CartPool.findAll().then(function (cartPool) {
			cartPool = _.map(cartPool, function (itemCart) {
				if (itemCart.IDENTIFYKEY == widget.currentRow.IDENTIFYKEY) {
					itemCart = widget.currentRow;
				}
				return itemCart;
			});
			cartPool.reverse();
			CartPool.remove(Query.build()).then(function () {
				CartPool.save(cartPool);
			}.bind(this));
		});
		widget.dataSource.data.reverse();
		self.prepareCart(widget, widget.container.getWidget('checkOrderStripe'));
		ScreenService.closePopup();

	};

	// Gets the observations from the child product.
	this.prepareUpdatePromo = function (product, field, widget) {
		OperatorRepository.findAll().then(function (operatorData) {
			if (this.selectedProduct !== product) {
				field.dataSource.data = this.getObservations(product.OBSERVATIONS);
				widget.currentRow = product;
				this.selectedProduct = product;
				/* Define se será mostrado o checkbox de atraso de produtos. */
				if (operatorData[0].NRATRAPADRAO > 0) {
					widget.fields[2].isVisible = true;
					widget.fields[1].class = 8;
				}
				else {
					widget.fields[2].isVisible = false;
					widget.fields[1].class = 12;
				}
			}
		}.bind(this));
	};

	// Resets the data present in the widget to prevent duplicate data from causing errors.
	this.checkOrderReset = function (widget) {
		OperatorRepository.findOne().then(function (operatorData) {
			if (operatorData.continueOrdering && !operatorData.newOrders) {
				AccountCart.clearAll();
			}
			widget.dataSource.data = [];
			ScreenService.goBack();
		});
	};

	this.setNewOrders = function (value) {
		return OperatorRepository.findOne().then(function (operatorData) {
			operatorData.newOrders = value;
			return OperatorRepository.save(operatorData);
		});
	};

	this.storeCPF = function (widget) {

	};

	this.setCPF = function (widget) {
		OperatorRepository.findAll().then(function (params) {
			AccountService.setCPF(widget.currentRow.cpfField, widget.currentRow.theCheckBox, widget.getField('positions').position + 1).then(function (result) {

			});
		});
	};

	this.togglePositions = function (widget) {
		widget.getField('positions').isVisible = widget.getField('theCheckBox').value();
		widget.getField('positionProducts').isVisible = widget.getField('theCheckBox').value();
	};

	this.loadPositionOrders = function (widget, args) {
		var position = args.owner.data('position') + 1;
		AccountGetAccountItems.findAll().then(function (items) {
			var positionItems = items.filter(function (item) {
				return item.POS == position;
			});
			widget.getField('positionProducts').value('');
			if (positionItems.length > 0) {
				widget.getField('cpfField').readOnly = false;
				for (var i in positionItems) {
					widget.getField('positionProducts').value(widget.getField('positionProducts').value() + positionItems[i].DSBUTTON + '\n');
				}
			}
			else {
				widget.getField('cpfField').readOnly = true;
			}
		});
	};

	this.closeProductPopup = function (widget) {
		self.updateObservations(widget).then(function () {
			widget.currentRow.QTPRODCOMVEN = parseFloat(widget.currentRow.QTPRODCOMVEN);
			if (widget.currentRow.QTPRODCOMVEN == null || isNaN(widget.currentRow.QTPRODCOMVEN) || widget.currentRow.QTPRODCOMVEN <= 0) {
				ScreenService.showMessage('Favor inserir uma quantidade válida para o produto.');
				return;
			} else {
				AccountCart.findAll().then(function (data) {
					var obsReturn = self.handleObservations(data, widget);

					if (obsReturn.error) {
						ScreenService.showMessage(obsReturn.message);
					} else {
						ScreenService.closePopup();
					}
				}.bind(this));
			}
		});
	};

	this.handlePositionsFieldInit = function (widgetCloseAccount) {
		var positionsField = widgetCloseAccount.getField('positionsField');
		var radioTablePositions = widgetCloseAccount.getField('radioTablePositions');
		widgetCloseAccount.setCurrentRow({});
		radioTablePositions.applyDefaultValue();
		self.setActionLabel(positionsField);
	};

	this.prepareAccountClosingWidget = function (widgetCloseAccount, formWidget, openFidelityPopup, fidelitySearch) {
		var positionsField = widgetCloseAccount.getField('positionsField');
		var radioTablePositions = widgetCloseAccount.getField('radioTablePositions');

		OperatorRepository.findOne().then(function (operatorData) {
			if ((operatorData.modoHabilitado === 'M') && (operatorData.IDLUGARMESA === 'S')) {
				TableActiveTable.findOne().then(function (activeTable) {
					var positionsObject = _.get(activeTable, 'posicoes', {});

					WaiterNamedPositionsState.initializeTemplate();

					positionsField.dataSource.data[0].NRPOSICAOMESA = activeTable.NRPOSICAOMESA;
					positionsField.dataSource.data[0].clientMapping = ApplicationContext.TableController.buildClientMapping(positionsObject);
					positionsField.dataSource.data[0].consumerMapping = ApplicationContext.TableController.buildConsumerMapping(positionsObject);
					positionsField.dataSource.data[0].positionNamedMapping = ApplicationContext.TableController.buildPositionNamedMapping(positionsObject);
					ApplicationContext.TableController.updatePositionsCopy(positionsField);

					if (openFidelityPopup) {
						self.openTableFidelity(formWidget, positionsField, radioTablePositions, fidelitySearch);
					}
				});
			}
		});
	};

	this.handlePositionsRadioChangeAccount = function (widgetCloseAccount) {
		var positionsField = widgetCloseAccount.getField('positionsField');
		var radioTablePositions = widgetCloseAccount.getField('radioTablePositions');

		var topMargin = parseInt($('.zh-widget-accountItemsTable').css('top'));

		TableActiveTable.findOne().then(function (activeTable) {
			if (radioTablePositions.value() === 'P') {
				positionsField.isVisible = true;
				positionsField.dataSource.data[0].NRPOSICAOMESA = activeTable.NRPOSICAOMESA;
				$('.zh-widget-accountItemsTable').css('top', topMargin + 55 + 'px');
			} else {
				positionsField.isVisible = false;
				$('.zh-widget-accountItemsTable').css('top', topMargin - 55 + 'px');
				WaiterNamedPositionsState.unselectAllPositions();
				this.refreshAccountDetails(widgetCloseAccount.widgets, '', positionsField, true);
			}
			self.setActionLabel(positionsField);
		}.bind(self));
	};

	this.handleCloseTablePositionChange = function (positionsField) {
		TableActiveTable.findOne().then(function (activeTable) {
			TableService.positionControl(activeTable.NRVENDAREST, positionsField.newPosition + 1, !~positionsField.position.indexOf(positionsField.newPosition), positionsField.position).then(function (result) {
				if (result[0].message == null) {
					self.showPositionActions(positionsField);
					if (positionsField.position.length > 0) {
						positionsField.widget.fields[0].setValue('P');
						self.refreshAccountDetailsMultiplePositions(positionsField.widget.widgets, positionsField.position, positionsField);
					}
					else {
						positionsField.widget.fields[0].setValue('M');
						self.refreshAccountDetails(positionsField.widget.widgets, '', positionsField, true);
					}
					self.setActionLabel(positionsField);
				}
				else {
					positionsField._buttons[positionsField.newPosition].selected = false;
					positionsField.position.pop(positionsField.newPosition);
					if (positionsField.position.length == 0) {
						self.hidePositionActions(positionsField);
					}
				}
			});
		});
	};

	this.showPositionActions = function (positionsField) {
		OperatorRepository.findOne().then(function (operatorData) {
			positionsField.widget.container.getWidget('accountDetailsTable').getAction('changePositions').isVisible = true;
			positionsField.widget.container.getWidget('accountDetailsTable').getAction('pagar').isVisible = true;
			positionsField.widget.container.getWidget('accountItemsTable').getAction('transfer').isVisible = true;
			positionsField.widget.container.getWidget('accountItemsTable').getAction('pagar').isVisible = true;
			positionsField.widget.container.getWidget('accountDetailsTable').getAction('partialPrint').isVisible = true;
			positionsField.widget.container.getWidget('accountDetailsTable').getField('servicoBtn').isVisible = operatorData.IDCOMISVENDA == "S";
		});
	};

	this.handlePositionsFieldInit = function (widgetCloseAccount) {
		var positionsField = widgetCloseAccount.getField('positionsField');
		var radioTablePositions = widgetCloseAccount.getField('radioTablePositions');
		widgetCloseAccount.setCurrentRow({});
		radioTablePositions.applyDefaultValue();
		self.setActionLabel(positionsField);
	};

	this.prepareAccountClosingWidget = function (widgetCloseAccount, formWidget, openFidelityPopup, fidelitySearch) {
		var positionsField = widgetCloseAccount.getField('positionsField');
		var radioTablePositions = widgetCloseAccount.getField('radioTablePositions');
		positionsField.isVisible = true;

		OperatorRepository.findOne().then(function (operatorData) {
			if ((operatorData.modoHabilitado === 'M') && (operatorData.IDLUGARMESA === 'S')) {
				TableActiveTable.findOne().then(function (activeTable) {
					var positionsObject = _.get(activeTable, 'posicoes', {});

					WaiterNamedPositionsState.initializeTemplate();

					positionsField.dataSource.data[0].NRPOSICAOMESA = activeTable.NRPOSICAOMESA;
					positionsField.dataSource.data[0].clientMapping = ApplicationContext.TableController.buildClientMapping(positionsObject);
					positionsField.dataSource.data[0].consumerMapping = ApplicationContext.TableController.buildConsumerMapping(positionsObject);
					positionsField.dataSource.data[0].positionNamedMapping = ApplicationContext.TableController.buildPositionNamedMapping(positionsObject);
					ApplicationContext.TableController.updatePositionsCopy(positionsField);

					if (openFidelityPopup) {
						self.openTableFidelity(formWidget, positionsField, radioTablePositions, fidelitySearch);
					}
				});
			} else {
				positionsField.isVisible = false;
			}
		});
	};

	this.handlePositionsRadioChangeAccount = function (widgetCloseAccount) {
		var positionsField = widgetCloseAccount.getField('positionsField');
		var radioTablePositions = widgetCloseAccount.getField('radioTablePositions');

		var topMargin = parseInt($('.zh-widget-accountItemsTable').css('top'));

		TableActiveTable.findOne().then(function (activeTable) {
			if (radioTablePositions.value() === 'P') {
				positionsField.isVisible = true;
				positionsField.dataSource.data[0].NRPOSICAOMESA = activeTable.NRPOSICAOMESA;
				$('.zh-widget-accountItemsTable').css('top', topMargin + 55 + 'px');
			} else {
				positionsField.isVisible = false;
				$('.zh-widget-accountItemsTable').css('top', topMargin - 55 + 'px');
				WaiterNamedPositionsState.unselectAllPositions();
				this.refreshAccountDetails(widgetCloseAccount.widgets, '', positionsField, true);
			}
			self.setActionLabel(positionsField);
		}.bind(self));
	};

	this.handleCloseTablePositionChange = function (positionsField) {
		TableActiveTable.findOne().then(function (activeTable) {
			TableService.positionControl(activeTable.NRVENDAREST, positionsField.newPosition + 1, !~positionsField.position.indexOf(positionsField.newPosition), positionsField.position).then(function (result) {
				if (result[0].message == null) {
					self.showPositionActions(positionsField);
					if (positionsField.position.length > 0) {
						positionsField.widget.fields[0].setValue('P');
						self.refreshAccountDetailsMultiplePositions(positionsField.widget.widgets, positionsField.position, positionsField);
					}
					else {
						positionsField.widget.fields[0].setValue('M');
						self.refreshAccountDetails(positionsField.widget.widgets, '', positionsField, true);
					}
					self.setActionLabel(positionsField);
				}
				else {
					positionsField._buttons[positionsField.newPosition].selected = false;
					positionsField.position.pop(positionsField.newPosition);
					if (positionsField.position.length == 0) {
						self.hidePositionActions(positionsField);
					}
				}
			});
		});
	};

	this.showPositionActions = function (positionsField) {
		OperatorRepository.findOne().then(function (operatorData) {
			positionsField.widget.container.getWidget('accountDetailsTable').getAction('changePositions').isVisible = true;
			positionsField.widget.container.getWidget('accountDetailsTable').getAction('pagar').isVisible = true;
			positionsField.widget.container.getWidget('accountItemsTable').getAction('transfer').isVisible = true;
			positionsField.widget.container.getWidget('accountItemsTable').getAction('pagar').isVisible = true;
			positionsField.widget.container.getWidget('accountDetailsTable').getAction('partialPrint').isVisible = true;
			positionsField.widget.container.getWidget('accountDetailsTable').getField('servicoBtn').isVisible = operatorData.IDCOMISVENDA == "S";
		});
	};

	this.hidePositionActions = function (positionsField) {
		positionsField.widget.container.getWidget('accountDetailsTable').setCurrentRow({});
		positionsField.widget.container.getWidget('accountDetailsTable').getAction('changePositions').isVisible = false;
		positionsField.widget.container.getWidget('accountDetailsTable').getAction('pagar').isVisible = false;
		positionsField.widget.container.getWidget('accountDetailsTable').getAction('partialPrint').isVisible = false;
		positionsField.widget.container.getWidget('accountItemsTable').dataSource.data = [];
		positionsField.widget.container.getWidget('accountItemsTable').getAction('transfer').isVisible = false;
		positionsField.widget.container.getWidget('accountItemsTable').getAction('pagar').isVisible = false;
		positionsField.widget.container.getWidget('accountDetailsTable').getField('servicoBtn').isVisible = false;
	};

	this.setActionLabel = function (positionsField) {
		var isMesa = positionsField.widget.currentRow.radioTablePositions === 'M';
		var newLabel;
		var actionPagar1 = positionsField.widget.widgets[0].getAction('pagar');
		var actionPagar2 = positionsField.widget.widgets[1].getAction('pagar');
		var fullTable;

		OperatorRepository.findOne().then(function (operatorData) {
			newLabel = operatorData.IDCOLETOR === 'C' ? 'Adiantar' : 'Receber';

			actionPagar1.label = newLabel;
			actionPagar2.label = newLabel;
			// se true, pagamento é inicializado com todas as posições
			fullTable = isMesa ? "true" : "false";
			actionPagar1.events[0].code = "AccountController.openPayment(" + fullTable + ");";
			actionPagar2.events[0].code = "AccountController.openPayment(" + fullTable + ");";
		});
	};

	this.accountChangeClientConsumer = function (formWidget, positionsField, radioTablePositions) {
		self.changeClientConsumer(formWidget, positionsField, radioTablePositions, true).then(function (changeClientConsumerResponse) {
			fidelitySearch = changeClientConsumerResponse.AccountChangeClientConsumer.fidelitySearch;
			var openFidelityPopup = false;
			if (_.isEmpty(fidelitySearch)) {
				self.finishChangeClientConsumer(formWidget, positionsField, openFidelityPopup, fidelitySearch);
				ScreenService.closePopup();
			}
			else {
				ScreenService.confirmMessage("Deseja utilizar Crédito Fidelidade para este consumidor?", "question",
					function () {
						openFidelityPopup = true;
						self.finishChangeClientConsumer(formWidget, positionsField, openFidelityPopup, fidelitySearch);
					},
					function () {
						self.finishChangeClientConsumer(formWidget, positionsField, openFidelityPopup, fidelitySearch);
						ScreenService.closePopup();
					}
				);
			}
		});
	};

	this.finishChangeClientConsumer = function (formWidget, positionsField, openFidelityPopup, fidelitySearch) {
		var accountDetailsWidget = formWidget.container.getWidget('accountDetails');
		if (accountDetailsWidget) {
			accountDetailsWidget.activate();
			ApplicationContext.TableController.buildPositionsObject(positionsField);
			if (positionsField.widget.fields[0].value() === "M") {
				self.refreshAccountDetails(accountDetailsWidget.widgets, '', true);
			}
			else {
				positionsField._isStatusChanged = true;
				self.refreshAccountDetailsMultiplePositions(positionsField.widget.widgets, positionsField.position, positionsField);
			}
			self.prepareAccountClosingWidget(accountDetailsWidget, formWidget, openFidelityPopup, fidelitySearch);
		}
	};

	this.changeClientConsumer = function (formWidget, positionsField, radioTablePositions, fidelitySearch) {
		var positionsObject = [];
		var currentRow = formWidget.currentRow;

		if (radioTablePositions.value() === 'P') {
			positionsObject = ApplicationContext.TableController.buildPositionsObject(positionsField);
		}
		if (currentRow.CDCLIENTE === '') {
			currentRow.CDCLIENTE = null;
			currentRow.NMRAZSOCCLIE = null;
			currentRow.CDCONSUMIDOR = null;
			currentRow.NMCONSUMIDOR = null;
		}
		if (currentRow.CDCONSUMIDOR === '') {
			currentRow.CDCONSUMIDOR = null;
			currentRow.NMCONSUMIDOR = null;
		}
		return OperatorRepository.findOne().then(function (operatorData) {
			return TableActiveTable.findOne().then(function (activeTable) {
				return AccountService.changeClientConsumer(
					operatorData.chave,
					activeTable.NRVENDAREST,
					activeTable.NRCOMANDA,
					positionsObject,
					currentRow.CDCLIENTE,
					currentRow.CDCONSUMIDOR,
					fidelitySearch
				);
			});
		});
	};

	this.handleModeOrder = function (stripe) {
		// altera actions dependendo do modo habilitado
		OperatorRepository.findOne().then(function (operatorData) {
			var transmitirAction = stripe.getAction('transmitir');
			var pagamentoAction = stripe.getAction('pagamento');
			var cancelarAction = stripe.getAction('cancelar');
			var concluirAction = stripe.getAction('concluir');
			var conferirAction = stripe.getAction('conferir');
			var continueAction = stripe.getAction('continue');

			if (operatorData.modoHabilitado !== 'B') {
				var showContinue = operatorData.IDAGRUPAPEDCOM == 'S' && operatorData.modoHabilitado == 'C';
				continueAction.isVisible = showContinue;

				/* Esconde o botão de transmitir em celulares. */
				pagamentoAction.isVisible = false;
				cancelarAction.isVisible = !operatorData.continueOrdering && !showContinue && operatorData.modoHabilitado == 'C';
				// Essa é a menos importante das actions do meio.
				transmitirAction.isVisible = !showContinue && !cancelarAction.isVisible;
				concluirAction.isVisible = !operatorData.continueOrdering;
				conferirAction.isVisible = operatorData.continueOrdering && operatorData.modoHabilitado == 'C';

				if (!(operatorData.modoHabilitado === 'C' || operatorData.modoHabilitado === 'O' ||
					operatorData.IDLUGARMESA === 'N' || operatorData.modoHabilitado === 'B')) {
					stripe.container.getWidget('checkOrder').groupProp = 'POSITION';
				}
			} else {
				pagamentoAction.label = "Receber";
				transmitirAction.isVisible = false;
				pagamentoAction.isVisible = true;
				cancelarAction.isVisible = true;
				concluirAction.isVisible = false;
				conferirAction.isVisible = false;
			}
		});
	};

	this.cancelOrder = function () {
		ScreenService.confirmMessage(
			'Deseja cancelar o pedido?',
			'question',
			function () {
				self.accountCartClear();
			},
			function () { }
		);
	};

	this.finishPayAccount = function () {
		OperatorRepository.findOne().then(function (operatorData) {
			if (operatorData.modoHabilitado === Mode.BALCONY) {
				AccountCart.remove(Query.build());
			}

			UtilitiesService.backMainScreen();
		});
	};

	this.openAddition = function (accountPaymentNamedWidget) {
		if (accountPaymentNamedWidget.currentRow.vlrprodcobtaxa > 0) {
			PermissionService.checkAccess('retirarTaxaServico').then(function (CDSUPERVISOR) {
				accountPaymentNamedWidget.currentRow.CDSUPERVISOR = CDSUPERVISOR;
				var additionPopup = accountPaymentNamedWidget.container.getWidget('additionPopup');
				ScreenService.openPopup(additionPopup).then(function () {
					accountPaymentNamedWidget.currentRow.TIPOGORJETA = 'V';
					additionPopup.setCurrentRow(accountPaymentNamedWidget.currentRow);
				});
			}.bind(this));
		} else {
			ScreenService.showMessage("Não é possível aplicar taxa de serviço para uma mesa sem produtos pedidos.");
		}
	};

	this.applyAddition = function (additionPopup) {
		if (additionPopup.isValid()) {
			if (ApplicationContext.PaymentController.validValue(additionPopup.getField('vlrservico'), '')) {
				AccountGetAccountDetails.findOne().then(function (accountDetails) {
					var vlrProdLiq = parseFloat((accountDetails.vlrprodutos - accountDetails.vlrdesconto - accountDetails.fidelityDiscount).toFixed(2));
					var vlrservico = UtilitiesService.removeCurrency(additionPopup.getField('vlrservico').value());
					var TIPOGORJETA = additionPopup.getField('TIPOGORJETA').value();
					var VRCOUVERT = parseFloat(accountDetails.vlrcouvert);

					var VRACRESCIMO = TIPOGORJETA === 'V' ? vlrservico : parseFloat((vlrProdLiq * (vlrservico / 100)).toFixed(2));
					if (accountDetails.vlrservico != VRACRESCIMO) {
						accountDetails.CDSUPERVISORs = additionPopup.currentRow.CDSUPERVISOR;
						if (VRACRESCIMO == 0) {
							accountDetails.logServico = 'RET_TAX';
						} else if (VRACRESCIMO > accountDetails.vlrservico) {
							accountDetails.logServico = 'ADD_TAX';
						} else {
							accountDetails.logServico = 'ALT_TAX';
						}
						accountDetails.vlrservico = VRACRESCIMO;
						accountDetails.vlrtotal = VRACRESCIMO + vlrProdLiq + VRCOUVERT;
						// valores a mostra para o usuário
						accountDetails.servico = UtilitiesService.formatFloat(VRACRESCIMO);
						accountDetails.total = UtilitiesService.formatFloat(accountDetails.vlrtotal);

						AccountGetAccountDetails.save(accountDetails);
					}
					self.getAccountData(function (accountData) {
						AccountService.updateServiceTax(accountData[0].NRVENDAREST, accountData[0].NRCOMANDA, vlrProdLiq, vlrservico, TIPOGORJETA).then(function () {
							// atualiza as informações na tela de finaliza a alteração
							additionPopup.container.getWidget('accountDetailsTable').currentRow = accountDetails;
							self.closeAddition(additionPopup);
						});
					});
				});
			}
		}
	};

	this.closeAddition = function (widget) {
		ScreenService.closePopup();
		widget.container.getWidget('accountDetailsTable').activate();
	};

	this.showCheckOrderScreenCartPool = function (operatorData) {
		this.showCheckOrderScreen(operatorData).then(function (something) {
			var updateScreen = function updateScreen() {
				return templateManager.updateTemplate().then(function (something) {
					self.checkCartPool(templateManager.container);
				});
			};
			setTimeout(updateScreen, 800);
		});
	};

	this.checkCartPool = function (checkOrderContainer) {
		var checkOrderWidget = checkOrderContainer.getWidget('checkOrder');
		var stripe = checkOrderContainer.getWidget('checkOrderStripe');

		stripe.getAction('transmitir').isVisible = false;
		stripe.getAction('cancelar').isVisible = true;
		stripe.getAction('concluir').isVisible = true;
		stripe.getAction('conferir').isVisible = false;
		stripe.getAction('continue').isVisible = false;

		AccountCart.findAll().then(function (cart) {
			CartPool.findAll().then(function (cartPool) {
				cart = self.filterCartPool(cart, cartPool);
				cartPool = cartPool.concat(cart);
				CartPool.save(cartPool);
				checkOrderWidget.groupProp = 'DSCOMANDA';
				checkOrderWidget.dataSource.data = cartPool;
				self.prepareCart(checkOrderWidget, stripe);
			});
		});
	};

	this.accountCartClear = function () {
		AccountCart.findAll().then(function (cartAccount) {
			CartPool.findAll().then(function (cartPool) {
				OperatorRepository.findOne().then(function (operatorData) {
					var filteredCart = self.filterCartPool(cartAccount, cartPool);
					self.produtosDesistencia(filteredCart);
					var promises = [];
					var AccountCartClear = AccountCart.remove(Query.build());
					promises.push(AccountCartClear);
					if (operatorData.continueOrdering) {
						self.produtosDesistencia(cartPool);
						var CartPoolClear = CartPool.remove(Query.build());
						promises.push(CartPoolClear);
						operatorData.continueOrdering = false;
						var OperatorRepositorySave = OperatorRepository.save(operatorData);
						promises.push(OperatorRepositorySave);
					}
					ZHPromise.all(promises).then(function () {
						UtilitiesService.backMainScreen();
					});
				});
			});
		});
	};

	this.filterCartPool = function (cart, cartPool) {
		var filteredCart = [];
		cart.forEach(function (cartItem) {
			var founded = false;
			cartPool.forEach(function (cartPoolItem) {
				if (cartPoolItem.IDENTIFYKEY == cartItem.IDENTIFYKEY) {
					founded = true;
				}
			});

			if (!founded)
				filteredCart.push(cartItem);
		});

		return filteredCart;
	};

	this.openDiscount = function (widget) {
		PermissionService.checkAccess('cupomDesconto').then(function (CDSUPERVISOR) {
			var discountPopup = widget.container.getWidget('discountPopup');
			discountPopup.currentRow.CDSUPERVISOR = CDSUPERVISOR;

			ScreenService.openPopup(discountPopup).then(function () {
				self.getDiscount(discountPopup);
			}.bind(this));
		}.bind(this));
	};

	this.getDiscount = function (discountPopup) {
		AccountGetAccountDetails.findOne().then(function (accountDetails) {
			var productField = discountPopup.getField('PRODUCTSONACCOUNT');

			// para este desconto, o valor setado nunca será em porcentagem
			discountPopup.getField('TIPODESCONTO').setValue('V');
			discountPopup.getField('VRDESCONTO').setValue(accountDetails.vlrdesconto);
			productField.reload();
			productField.clearValue();
			self.handleDiscountRadioChange(discountPopup);
		});
	};

	this.handleDiscountRadioChange = function (discountPopup) {
		var valueDiscountField = discountPopup.getField('VRDESCONTO');

		AccountGetAccountDetails.findOne().then(function (accountDetails) {
			if (discountPopup.getField('TIPODESCONTO').value() === 'P') {
				valueDiscountField.label = 'Porcentagem';
				valueDiscountField.range.max = 99.99;
			} else {
				valueDiscountField.label = 'Valor';
				valueDiscountField.range.max = accountDetails.vlrprodutos;
			}
		});
	};

	this.cancelDiscount = function (discountPopup) {
		var valueProductField = discountPopup.getField('PRODUCTSONACCOUNT').value();
		discountPopup.getField('MOTIVODESCONTO').clearValue();
		discountPopup.getField('CDOCORR').clearValue();

		if (!_.isEmpty(valueProductField)) {
			ScreenService.confirmMessage(
				'Deseja limpar o desconto dos produtos selecionados?', 'question',
				function () {
					// chama função que aplica desconto com o valor 0
					self.changeProductDiscount(discountPopup, 0, 'V', valueProductField);
				}.bind(this),
				function () { }
			);
		} else {
			ScreenService.showMessage('Selecione um ou mais produtos para limpar seu desconto.', 'alert');
		}
	};

	this.changeProductDiscount = function (discountPopup, VRDESCONTO, TIPODESCONTO, NRPRODCOMVEN) {
		self.getAccountData(function (accountData) {
			CDGRPOCORDESC = !_.isEmpty(discountPopup.currentRow.CDOCORR) ? discountPopup.currentRow.CDOCORR[0] : null;
			AccountService.changeProductDiscount(accountData[0].NRVENDAREST, accountData[0].NRCOMANDA, VRDESCONTO, TIPODESCONTO, NRPRODCOMVEN, discountPopup.currentRow.CDSUPERVISOR, discountPopup.currentRow.MOTIVODESCONTO, CDGRPOCORDESC).then(function (changeProductDiscountResult) {
				ScreenService.closePopup();
				self.prepareAccountDetails(discountPopup.container.getWidget('closeAccount'));
			});
		});
	};

	this.applyDiscount = function (discountPopup) {
		var valueDiscountField = discountPopup.getField('VRDESCONTO');

		if (discountPopup.isValid()) {
			// valida valor de entrada do desconto
			if (ApplicationContext.PaymentController.validValue(valueDiscountField, '')) {
				discountPopup.currentRow.VRDESCONTO = UtilitiesService.removeCurrency(discountPopup.currentRow.VRDESCONTO);

				// define se será possível aplicar desconto para os produtos selecionados
				self.handleSetDiscount(discountPopup).then(function (_) {
					self.changeProductDiscount(discountPopup, discountPopup.currentRow.VRDESCONTO, discountPopup.currentRow.TIPODESCONTO, discountPopup.currentRow.PRODUCTSONACCOUNT);
				}, function (errorMessage) {
					ScreenService.showMessage(errorMessage, 'alert');
				});
			}
		}
	};

	this.handleSetDiscount = function (discountPopup) {
		return new Promise(function (resolve, reject) {
			ParamsParameterRepository.findOne().then(function (params) {
				var products = discountPopup.getField('PRODUCTSONACCOUNT').dataSource.data;
				var TIPODESCONTO = discountPopup.currentRow.TIPODESCONTO;
				var VRDESCONTO = TIPODESCONTO === 'P' ? parseFloat((discountPopup.currentRow.VRDESCONTO / 100).toFixed(4)) : discountPopup.currentRow.VRDESCONTO;

				// filtra produtos escolhidos no desconto
				products = _.filter(products, function (product) {
					return _.includes(discountPopup.currentRow.PRODUCTSONACCOUNT, product.NRPRODCOMVEN);
				});

				for (var i = 0; i < products.length; i++) {
					var preco = UtilitiesService.truncValue(products[i].VRPRECCOMVEN * products[i].QTPRODCOMVEN);
					var currentDiscount = 0;

					if (TIPODESCONTO === 'P') {
						currentDiscount = UtilitiesService.truncValue(VRDESCONTO * preco);
						percentdesc = VRDESCONTO;
					} else {
						currentDiscount = VRDESCONTO;
						percentdesc = VRDESCONTO / preco * 100;
					}

					if (parseFloat((preco - currentDiscount).toFixed(2)) <= 0) {
						reject('O valor do desconto não pode ser igual ou maior que o valor total da venda.');
						return;
					} else if (percentdesc > params.VRMAXDESCONTO && params.VRMAXDESCONTO > 0) {
						reject('Operação bloqueada. Valor de desconto maior que percentual máximo permitido.');
						return;
					}
				}

				resolve();
			}.bind(this), reject);
		}.bind(this));
	};

	this.changeLabelContainer = function (container) {
		OperatorRepository.findOne().then(function (operatorData) {
			container.label = operatorData.modoHabilitado === 'C' ? "Receber Comanda" : "Receber Mesa";
		});
	};

	this.getConsumerBalance = function (widget) {
		if (widget.currentRow.CDCLIENTE == null || widget.currentRow.CDCONSUMIDOR == null) {
			ScreenService.showMessage("Informe o consumidor.");
			return;
		}

		OperatorRepository.findOne().then(function (params) {
			AccountService.getConsumerBalance(params.chave, widget.currentRow.CDCLIENTE, widget.currentRow.CDCONSUMIDOR).then(function () {
				widget.widgets[0].reload().then(function () {
					ScreenService.openPopup(widget.widgets[0]);
				});
			}.bind(this));
		}.bind(this));
	};

	this.clientCustomerVisibility = function (widget) {
		widget.currentRow.CDCLIENTE = "";
		widget.currentRow.CDCONSUMIDOR = "";
		widget.getField('NMRAZSOCCLIE').clearValue();
		widget.getField('NMCONSUMIDOR').clearValue();

		widget.getAction('qrcode').isVisible = !Util.isDesktop() && !UtilitiesService.isPoyntDevice();
	};

	this.prepareCreditCharge = function (widget) {
		widget.currentRow.CDFAMILISALD = "";
		widget.currentRow.VRRECARGA = null;

		widget.getField('NMFAMILISALD').clearValue();
		widget.getField('VRRECARGA').clearValue();

		var familiesSelect = widget.getField('NMFAMILISALD');
		if (familiesSelect.dataSource.data.length == 1) {
			familiesSelect.widget.currentRow.CDFAMILISALD = familiesSelect.dataSource.data[0].CDFAMILISALD;
			familiesSelect.widget.currentRow.NMFAMILISALD = familiesSelect.dataSource.data[0].NMFAMILISALD;
			familiesSelect.widget.currentRow.IDPERMCARGACRED = familiesSelect.dataSource.data[0].IDPERMCARGACRED;
			familiesSelect.setValue(familiesSelect.dataSource.data[0].NMFAMILISALD);
		}
		self.clientCustomerVisibility(widget);
	};

	this.prepareCancelCredit = function (widget) {
		widget.currentRow.NRDEPOSICONS = "";
		widget.getField('NRDEPOSICONS').clearValue();
		self.clientCustomerVisibility(widget);
	};

	this.cancelPersonalCredit = function (cancelDetails, depositPopup, NRSEQMOVCAIXA) {
		if (this.checkCancelDetails(cancelDetails)) {
			OperatorRepository.findOne().then(function (params) {
				AccountService.cancelPersonalCredit(params.chave, cancelDetails.CDCLIENTE, cancelDetails.CDCONSUMIDOR, cancelDetails.NRDEPOSICONS, NRSEQMOVCAIXA, null).then(function (cancelResult) {
					if (cancelResult.nothing) {
						depositPopup.currentRow.CDCLIENTE = cancelDetails.CDCLIENTE;
						depositPopup.currentRow.CDCONSUMIDOR = cancelDetails.CDCONSUMIDOR;
						depositPopup.currentRow.NRDEPOSICONS = cancelDetails.NRDEPOSICONS;
						depositPopup.currentRow.NRSEQMOVCAIXA = null;
						depositPopup.getField('NMTIPORECE').clearValue();
						depositPopup.getField('NMTIPORECE').dataSource.data = cancelResult.CancelCreditRepository;
						ScreenService.openPopup(depositPopup);
					}
					else {
						if (cancelResult.length == 0) {
							ScreenService.confirmMessage(
								'O consumidor informado não possui saldo suficiente para efetuar este cancelamento. Sendo que se a resposta for sim o saldo do consumidor ficará negativo. Deseja continuar?',
								'question',
								function () {
									AccountService.cancelPersonalCredit(params.chave, cancelDetails.CDCLIENTE, cancelDetails.CDCONSUMIDOR, cancelDetails.NRDEPOSICONS, NRSEQMOVCAIXA, true).then(function (cancelResult) {
										self.handleCancelResult(cancelResult);
									});
								},
								function () { }
							);
						}
						else {
							self.handleCancelResult(cancelResult);
						}
					}
				});
			});
		}
	};

	this.checkCancelDetails = function (cancelDetails) {
		if (cancelDetails.CDCLIENTE == null || cancelDetails.CDCLIENTE.length == 0 ||
			cancelDetails.CDCONSUMIDOR == null || cancelDetails.CDCONSUMIDOR.length == 0 ||
			cancelDetails.NRDEPOSICONS == null) {
			ScreenService.showMessage("Favor preencher todos os campos.");
			return false;
		}
		else if (isNaN(cancelDetails.NRDEPOSICONS) || cancelDetails.VRRECARGA <= 0) {
			ScreenService.showMessage("Favor informar um número de depósito válido.");
			return false;
		}
		else {
			return true;
		}
	};

	this.handleCancelResult = function (cancelResult) {
		if (cancelResult[0].dadosImpressao != null) {
			// Impressão front-end.
		}
		UtilitiesService.backMainScreen();
	};

	this.priceUpdate = function (product, callback) {
		var defer = ZHPromise.defer();
		OperatorRepository.findOne().then(function (operatorData) {
			AccountCart.findAll().then(function (cart) {
				if (cart.length > 0 || operatorData.modoHabilitado == 'M') {
					defer.resolve(callback(false));
				}
				else {
					ParamsPriceTimeRepository.findOne().then(
						function (nextUpdateTime) {
							var d = new Date();
							var currentTime = parseInt(d.getTime() / 1000);
							if (currentTime >= nextUpdateTime.nextUpdateTime) {
								ScreenService.changeLoadingMessage("Atualizando preços. Aguarde...");
								AccountService.updatePrices(operatorData.chave).then(function (updateResult) {
									ScreenService.restoreDefaultLoadingMessage();
									defer.resolve(callback(updateResult.ParamsMenuRepository.filter(function (p) {
										return p.CDPRODUTO == product.CDPRODUTO;
									})));
								});
							}
							else {
								defer.resolve(callback(false));
							}
						},
						function () {
							ScreenService.restoreDefaultLoadingMessage();
						}
					);
				}
			});
		});
		return defer.promise;
	};

	/* ****************************************** */
	/* **  PERSONAL CREDIT TRANSFER FUNCTIONS  ** */
	/* ****************************************** */

	this.prepareTransferCredit = function (widget) {
		widget.currentRow.CDCLIENTE = [];
		widget.currentRow.CDCONSUMIDOR = [];
		widget.currentRow.CDFAMILISALD = [];
		widget.currentRow.CDIDCONSUMID = [];
		widget.currentRow.VRSALDCONEXT = [];
		widget.currentRow.NMCONSUMIDOR = [];
		widget.currentRow.selectedCards = [];
		widget.currentRow.transferClient = null;
		widget.currentRow.transferType = null;

		widget.getField('cardSearchOri').clearValue();
		widget.getField('cardSearchDest').clearValue();
		widget.getField('selectedCards').dataSource.data = [];

		widget.getField('destConsumer').clearValue();
		widget.getField('destType').clearValue();

		self.cleanDestinationValues(widget);
		widget.getField('transferValue').setValue("R$ 0,00");

		if (Util.isDesktop()) {
			widget.getField('qrcodeOrig').isVisible = false;
			widget.getField('qrcodeDest').isVisible = false;
			widget.getField('cardSearchOri').class = 9;
			widget.getField('cardSearchDest').class = 9;
		}
	};

	this.cardSearch = function (widget, searchValue, mode) {
		if (!searchValue) {
			ScreenService.showMessage("Informe um cartão.");
			return;
		}

		AccountService.cardSearch(searchValue).then(function (searchResult) {
			if (!searchResult.length) {
				ScreenService.showMessage("Cartão não encontrado.");
				return;
			}

			if (mode === "ORIG") {
				widget.widgets[0].reload().then(function () {
					if (searchResult.length == 1) {
						widget.widgets[0].dataSource.data[0].__isSelected = true;
					}
					ScreenService.openPopup(widget.widgets[0]);
				});
			}
			else {
				if (searchResult.length == 1) {
					widget.currentRow.destID = searchResult[0].ID;
					widget.currentRow.destConsumer = searchResult[0].NMCONSUMIDOR;
					widget.currentRow.destType = searchResult[0].NMTIPOCONS;
					widget.currentRow.destCDCLIENTE = searchResult[0].CDCLIENTE;
					widget.currentRow.destCDCONSUMIDOR = searchResult[0].CDCONSUMIDOR;
					widget.currentRow.destCDFAMILISALD = searchResult[0].CDFAMILISALD;
					widget.currentRow.destCDIDCONSUMID = searchResult[0].CDIDCONSUMID;
					widget.currentRow.destVRSALDCONEXT = searchResult[0].VRSALDCONEXT;
					widget.currentRow.destCDTIPOCONS = searchResult[0].CDTIPOCONS;
					widget.currentRow.destIDSITCONSUMI = searchResult[0].IDSITCONSUMI;
					widget.currentRow.destIDPERTRANSALD = searchResult[0].IDPERTRANSALD;
					this.selectDestCard(widget);
				}
				else if (searchResult.length > 1) {
					widget.getField('familyDest').reload();
					widget.getField('familyDest').openField();
				}
			}
		}.bind(this));
	};

	this.selectOrigCard = function (selectionWidget, widget, selectedCards) {
		if (_.isEmpty(widget.currentRow.selectedCards)) {
			widget.currentRow.transferClient = null;
			widget.currentRow.transferType = null;
		}

		var selectedFamilies = selectionWidget.dataSource.data.filter(function (selection) {
			return selection.__isSelected;
		}.bind(selectedCards));

		for (var i in selectedFamilies) {
			if (!self.validateOriginCard(widget, selectedFamilies[i])) {
				return;
			}
		}

		var exists;
		selectedFamilies.forEach(function (family) {
			exists = selectedCards.dataSource.data.some(function (card) {
				return card.ID == family.ID;
			});
			if (!exists) {
				family.VRSALDCONEXT = UtilitiesService.formatFloat(family.VRSALDCONEXT);
				selectedCards.dataSource.data.push(family);
				widget.currentRow.CDCLIENTE.push(family.CDCLIENTE);
				widget.currentRow.CDCONSUMIDOR.push(family.CDCONSUMIDOR);
				widget.currentRow.CDFAMILISALD.push(family.CDFAMILISALD);
				widget.currentRow.CDIDCONSUMID.push(family.CDIDCONSUMID);
				widget.currentRow.VRSALDCONEXT.push(family.VRSALDCONEXT);
				widget.currentRow.NMCONSUMIDOR.push(family.NMCONSUMIDOR);
				widget.currentRow.selectedCards.push(family.ID);
			}
			else {
				selectedCards.dataSource.data.forEach(function (card) {
					if (card.ID == family.ID) {
						card.__isSelected = true;
					}
				});
			}
		});

		if (selectedFamilies.length > 0) {
			self.handleOriginChange(widget, true);
			widget.getField('cardSearchOri').clearValue();
			ScreenService.closePopup();
		}
		else {
			ScreenService.showMessage("Marque pelo menos uma opção.");
		}
	};

	this.validateOriginCard = function (widget, family) {
		if (widget.currentRow.transferClient == null) {
			widget.currentRow.transferClient = family.CDCLIENTE;
		}
		else {
			if (widget.currentRow.transferClient != family.CDCLIENTE) {
				ScreenService.showMessage("Só é possível realizar transferências entre cartões de cliente iguais.");
				return false;
			}
		}

		if (widget.currentRow.transferType == null) {
			widget.currentRow.transferType = family.CDTIPOCONS;
		}
		else {
			if (widget.currentRow.transferType != family.CDTIPOCONS) {
				ScreenService.showMessage("Só é possível realizar transferências entre o mesmo tipo de cliente.");
				return false;
			}
		}

		if (family.VRSALDCONEXT <= 0) {
			ScreenService.showMessage("Não é possível transferir de cartões que não possuem saldo.");
			return false;
		}

		if (family.IDSITCONSUMI != '1') {
			ScreenService.showMessage("Um ou mais dos cartões selecionados encontra-se inativo.");
			return false;
		}

		if (family.IDPERTRANSALD != 'S') {
			ScreenService.showMessage("Não é permitido transferir saldo deste tipo de consumidor.");
			return false;
		}

		if (widget.currentRow.destCDCLIENTE != null) {
			if (widget.currentRow.destID == family.ID) {
				ScreenService.showMessage("Cartão/familia de origem não pode ser igual ao de destino.");
				return false;
			}
			if (widget.currentRow.destCDCLIENTE != family.CDCLIENTE) {
				ScreenService.showMessage("Cliente do cartão de origem difere ao do cartão de destino escolhido.");
				return false;
			}
			if (widget.currentRow.destCDTIPOCONS != family.CDTIPOCONS) {
				ScreenService.showMessage("Tipo de consumidor do cartão de origem difere ao do cartão de destino escolhido.");
				return false;
			}
		}

		return true;
	};

	this.handleOriginChange = function (widget, selectControl) {
		var selectedCardsField = widget.getField('selectedCards');
		var newCards = Array();

		selectedCardsField.dataSource.data.forEach(function (card) {
			if (card.__isSelected) {
				newCards.push(card);
			}
		});
		if (selectControl && !!selectedCardsField.selectWidget) {
			selectedCardsField.selectWidget.setSelected(newCards, selectedCardsField);
		} else {
			selectedCardsField.dataSource.data = newCards;
		}

		self.calculateTransferValues(widget);
	};

	this.selectDestCard = function (widget) {
		if (self.validateDestinationCard(widget.currentRow, widget.getField('selectedCards').dataSource.data)) {
			self.calculateTransferValues(widget);
		}
		else {
			self.cleanDestinationValues(widget);
		}
	};

	this.cleanDestinationValues = function (widget) {
		widget.currentRow.destID = null;
		widget.currentRow.destConsumer = null;
		widget.currentRow.destType = null;
		widget.currentRow.destCDCLIENTE = null;
		widget.currentRow.destCDCONSUMIDOR = null;
		widget.currentRow.destCDFAMILISALD = null;
		widget.currentRow.destCDIDCONSUMID = null;
		widget.currentRow.destVRSALDCONEXT = null;
		widget.currentRow.destCDTIPOCONS = null;
		widget.currentRow.destIDSITCONSUMI = null;
		widget.currentRow.destIDPERTRANSALD = null;
		widget.getField('cardSearchDest').clearValue();
		widget.getField('currentBalance').clearValue();
		widget.getField('finalBalance').clearValue();
		self.calculateTransferValues(widget);
	};

	this.validateDestinationCard = function (row, selectedCards) {
		if (_.isEmpty(selectedCards)) {
			row.transferClient = null;
			row.transferType = null;
		}

		for (var i in selectedCards) {
			if (selectedCards[i].ID == row.destID) {
				ScreenService.showMessage("Cartão/familia de destino não pode ser igual ao de origem.");
				return false;
			}
		}

		if (row.transferClient && row.transferClient != row.destCDCLIENTE) {
			ScreenService.showMessage("Cliente do cartão/familia de destino difere do de origem.");
			return false;
		}

		if (row.transferType && row.transferType != row.destCDTIPOCONS) {
			ScreenService.showMessage("Tipo de consumidor do cartão/familia de destino difere do de origem.");
			return false;
		}

		if (row.destIDSITCONSUMI != '1') {
			ScreenService.showMessage("Este cartão encontra-se inativo.");
			return false;
		}

		if (row.destIDPERTRANSALD != 'S') {
			ScreenService.showMessage("Não é permitido transferir saldo para este tipo de consumidor.");
			return false;
		}

		return true;
	};

	this.calculateTransferValues = function (widget) {
		var transferValue = widget.getField('selectedCards').dataSource.data.reduce(function (total, card) {
			return total + parseFloat(card.VRSALDCONEXT.replace(',', '.'));
		}, 0);
		widget.getField('transferValue').setValue("R$ " + UtilitiesService.formatFloat(transferValue));

		if (widget.currentRow.destVRSALDCONEXT != null) {
			var currentBalance = parseFloat(widget.currentRow.destVRSALDCONEXT);
			var finalBalance = parseFloat(currentBalance + transferValue);
			widget.getField('currentBalance').setValue("R$ " + UtilitiesService.formatFloat(currentBalance));
			widget.getField('finalBalance').setValue("R$ " + UtilitiesService.formatFloat(finalBalance));
		}
		else {
			widget.getField('currentBalance').clearValue();
			widget.getField('finalBalance').clearValue();
		}
	};

	this.transferPersonalCredit = function (widget) {
		if (_.isEmpty(widget.currentRow.selectedCards)) {
			ScreenService.showMessage("Escolha o cartão de origem.");
			return;
		}

		if (_.isEmpty(widget.currentRow.destCDCLIENTE)) {
			ScreenService.showMessage("Escolha o cartão de destino.");
			return;
		}

		OperatorRepository.findOne().then(function (params) {
			AccountService.transferPersonalCredit(params.chave, widget.currentRow).then(function () {
				self.prepareTransferCredit(widget);
			}.bind(this));
		}.bind(this));
	};

	this.clearSelectedCards = function (widget) {
		widget.currentRow.CDCLIENTE = [];
		widget.currentRow.CDCONSUMIDOR = [];
		widget.currentRow.CDFAMILISALD = [];
		widget.currentRow.CDIDCONSUMID = [];
		widget.currentRow.VRSALDCONEXT = [];
		widget.currentRow.NMCONSUMIDOR = [];
		widget.currentRow.selectedCards = [];
		widget.currentRow.transferClient = null;
		widget.currentRow.transferType = null;
		widget.getField('selectedCards').dataSource.data = [];
		self.calculateTransferValues(widget);
	};

	this.scanTransferCard = function (widget, mode) {
		UtilitiesService.callQRScanner().then(function (qrCode) {
			if (!qrCode.error) {
				if (_.isEmpty(qrCode.contents)) {
					ScreenService.showMessage("Não foi possível obter os dados do leitor.");
				}
				else {
					qrCode = qrCode.contents.replace(/[^A-Z0-9-\/. ]/gi, "");
					if (mode == "ORIG") {
						widget.getField('cardSearchOri').value(qrCode);
					}
					else {
						widget.getField('cardSearchDest').value(qrCode);
					}
					self.cardSearch(widget, qrCode, mode);
				}
			} else {
				ScreenService.showMessage(qrCode.message, 'alert');
			}
		}.bind(this));
	};

	this.reloadConsumers = function (field) {
		field.reload();
	};

	this.scanProductQrCode = function (widget) {
		UtilitiesService.callQRScanner().then(function (qrCode) {
			if (!qrCode.error) {
				if (_.isEmpty(qrCode.contents)) {
					ScreenService.showMessage("Não foi possível obter os dados do leitor.");
				} else {
					OperatorRepository.findOne().then(function (operatorData) {
						var qrCodeScan = (operatorData.IDLCDBARBALATOL == 'S') ?
							qrCode.contents.substr(operatorData.NRPOSINICODBARR, operatorData.NRPOSFINCODBARR - operatorData.NRPOSINICODBARR) : qrCode.contents;
						self.filterProducts(widget, qrCodeScan).then(function (products) {
							if (!_.isEmpty(products)) {
								if (products.length == 1) {
									widget.dataSource.data = products;
									self.handleSelectedProduct(widget, products[0], widget.container.getWidget('positionsWidget').position + 1);
								} else {
									widget.getField('selectProducts').dataSourceFilter = [
										{
											name: 'CDARVPROD|DSBUTTON',
											operator: '=',
											value: qrCodeScan
										}
									];
									widget.getField('selectProducts').reload();
									widget.getField('selectProducts').openField();
								}
							} else {
								ScreenService.showMessage("Produto não encontrado.");
							}
						}.bind(this));
					}.bind(this));
				}
			} else {
				ScreenService.showMessage(qrCode.message, 'alert');
			}
		}.bind(this));
	};

	this.rowSelectedOnProductFilter = function (args) {
		ScreenService.closePopup();
		var widget = args.owner.container.getWidget('products');
		widget.currentRow = args.row;
		self.handleSelectedProduct(widget, args.row, widget.container.getWidget('positionsWidget').position + 1);
	};

    this.handleSelectedProduct = function (widget, product, position){
        OperatorRepository.findOne().then(function (operatorData){
            self.priceUpdate(product, function (result){
                if (result){
                    widget.reload();
                    product = result[0];
                }

                product.HRINIVENPROD = !product.HRINIVENPROD ? 0 : product.HRINIVENPROD;
                product.HRFIMVENPROD = !product.HRFIMVENPROD ? 0 : product.HRFIMVENPROD;

                var validaProduto = self.validateProducts(product, operatorData.IDCOLETOR);
                if (_.isEmpty(validaProduto)){
                    if (product.GRUPOS){ // Produto do cardápio principal.
                        if (!isSmartPromo(product) && product.IDTIPOCOMPPROD !== 'C'){
                            /* - Produto Normal - */
                            self.addToCart(widget.container.getWidget("addProduct"), product, position, widget.container.getWidget("addProduct").getAction("cart"), widget.container.getWidget("menu").getAction("cart"), false, false);
                            ScreenService.closePopup();
                        }
                        else {
                            /* - Promoção Inteligente - */
                            self.buildPromoItem(product, position, false, false, function (refil){
                                buildCartItem(product, position, refil).then(function (cartItem){
                                    // Fixa a quantidade do produto.
                                    cartItem.QTPRODCOMVEN = 1;
                                    // Adiciona o produto pai no carrinho.
                                    AccountCart.save(cartItem).then(function (){
                                        self.openPromoScreen(product, widget, false);
                                        ScreenService.closePopup();
                                    });
                                });
                            });
                        }
                    }
                    else { // Produto dentro de uma promoção.
                        self.addToTray(widget.container.getWidget("addProduct"), product);
                        ScreenService.closePopup();
                    }
                } else {
                    ScreenService.showMessage(validaProduto);
                }
            });
        });
    };

    this.validateProducts = function(product, IDCOLETOR){
        if (product.IDTIPOCOMPPROD !== 'C') {
            if (product.IDPRODBLOQ == 'S') return "Produto bloqueado.";

            var hora = self.getHour();
            if (!((product.HRINIVENPROD == 0) && (product.HRFIMVENPROD == 0)) && ((hora <= product.HRINIVENPROD) || (hora >= product.HRFIMVENPROD)))
                return "Operação Bloqueada. Produto fora do horário permitido para venda.";

            if (product.VRPRECITEM == 0)
                return "Produto sem preço.";

            if (IDCOLETOR !== 'C'){
                var message = 'Produto não pode ser vendido, pois não possui ';
                var validate = {
                    'CDCLASFISC': "NCM",
                    'CDCFOPPFIS': "CFOP",
                    'CDCSTICMS': "CST do ICMS",
                    'VRALIQPIS': "Aliquota do PIS",
                    'CDCSTPISCOF': "CST do PIS/COFINS",
                    'VRALIQCOFINS': "Aliquota do COFINS"
                };

                for (var indexVaL in validate){
                    if (_.isEmpty(product[indexVaL])) {
                        return message + validate[indexVaL] + ' parametrizado.';
                    }
                }
            }
        }

        return null;
    };

    this.getHour = function(){
        var now = new Date();

        return now.getHours() * 100 + now.getMinutes();
    };

    this.prepareCheckBalance = function(widget){
        widget.getField('NMRAZSOCCLIE').clearValue();
        widget.getField('NMCONSUMIDOR').clearValue();
        widget.getAction('qrcode').isVisible = !Util.isDesktop() && !UtilitiesService.isPoyntDevice();
    };

	this.filterProducts = function(widget, pesquisa){
		if (widget.isValid()) {
			return AccountService.filterProducts({ 0: pesquisa }).then(function (filterProductResult) {
				if (!_.isEmpty(filterProductResult)) {
					return filterProductResult;
				} else {
					ScreenService.showMessage("Produto não encontrado.");
				}
			}.bind(this));
		}
	};

	var t;
	this.timerSearch = function (widget, pesquisa, timer) {
		clearTimeout(t);
		var timerSearch = function () {
			var field = widget.getField('selectProducts');
			var popup = widget;

			field.clearValue();

			field.dataSourceFilter = [
				{
					name: 'CDARVPROD|DSBUTTON',
					operator: '=',
					value: _.isEmpty(popup.currentRow.filterProducts) ? "%%" : "%" + popup.currentRow.filterProducts + "%"
				}
			];
			field.reload().then(function (search) {
				if (!_.isEmpty(search)) {
					if (widget.name === "BlockProductPopup") {
						if (!_.isEmpty(popup.currentRow.filterProducts)) {
							delete field.selectWidget;
							field.openField();
						}
					}
					else {
						var products = search.dataset.FilterProducts;
						if (products.length == 1) {
							popup.currentRow = products[0];
							popup.getField('selectProducts').setValue(products[0].DSBUTTON);
						} else if (products.length > 1) {
							delete field.selectWidget;
							field.openField();
						}
					}
				}
			}.bind(this));
		}.bind(this);
		t = setTimeout(timerSearch, timer);
	};

	this.setMask = function (widget) {
		OperatorRepository.findOne().then(function (operatorData) {
			widget.fields[0].mask.params.mask = operatorData.CDPICTPROD;
			widget.floatingControl = false;
		});
	};

	this.resetValues = function (widget) {
		widget.getField('filterProducts').clearValue();
		widget.getField('selectProducts').clearValue();
		widget.getField('selectProducts').dataSourceFilter = [
			{
				name: 'CDARVPROD|DSBUTTON',
				operator: '=',
				value: "%%"
			}
		];
		widget.getField('selectProducts').reload();
	};

	this.checkAdd = function (widget, product, position) {
		if (!_.isEmpty(widget.getField('selectProducts').value())) {
			this.handleSelectedProduct(widget, product, position);
		} else {
			ScreenService.showMessage('Nenhum produto selecionado.');
		}
	};

	this.handleEnterButton = function (args) {
		var keyCode = args.e.keyCode;
		if (keyCode === 13 || keyCode === 9) {
			UtilitiesService.handleCloseKeyboard();
			var widget = args.owner.field.widget;

			if (widget.name === 'discountPopup') {
				this.applyDiscount(widget);
			} else if (widget.name === 'additionPopup') {
				this.applyAddition(widget);
			}
		}
	};

	this.fidelityReturn = function (consumerPopup, modeWidget) {
		TableActiveTable.findOne().then(function (activeTable) {
			if (modeWidget.fields[0].value() === "P") {
				var positionsData = self.handleConsumerPositionsOnPayment(false, Array());
				consumerPopup.currentRow.CDCLIENTE = positionsData.CDCLIENTE;
				consumerPopup.currentRow.NMRAZSOCCLIE = positionsData.NMRAZSOCCLIE;
				consumerPopup.currentRow.CDCONSUMIDOR = positionsData.CDCONSUMIDOR;
				consumerPopup.currentRow.NMCONSUMIDOR = positionsData.NMCONSUMIDOR;
			}
			else if (activeTable.CDCLIENTE) {
				consumerPopup.currentRow.CDCLIENTE = activeTable.CDCLIENTE;
				consumerPopup.currentRow.NMRAZSOCCLIE = activeTable.NMRAZSOCCLIE;
				consumerPopup.currentRow.CDCONSUMIDOR = activeTable.CDCONSUMIDOR;
				consumerPopup.currentRow.NMCONSUMIDOR = activeTable.NMCONSUMIDOR;
			}
			ScreenService.closePopup();
		});
	};

	this.openTableFidelity = function (widget, positionsField, radioTablePositions, fidelitySearch) {
		ApplicationContext.TableController.restorePositionsCopy(positionsField);
		TableActiveTable.findOne().then(function (activeTable) {
			if (radioTablePositions.value() === 'P') {
				var positionsData = self.handleConsumerPositionsOnPayment(false, Array());
				if (_.isEmpty(positionsData.CDCLIENTE) || _.isEmpty(positionsData.CDCONSUMIDOR)) {
					ApplicationContext.TableController.clearConsumerRow(widget.currentRow);
					ScreenService.showMessage('Favor defina um consumidor para a posição antes de continuar.');
					return;
				}
			}
			else {
				if (_.isEmpty(activeTable.CDCLIENTE) || _.isEmpty(activeTable.CDCONSUMIDOR)) {
					ScreenService.showMessage('Favor defina um consumidor para a mesa antes de continuar.');
					return;
				}
			}

			var applyFidelityPopup = function (fidelityDetails) {
				var fidelityWidget = widget.container.getWidget('fidelityPopup');
				fidelityWidget.setCurrentRow({ 'VSALDODISP': fidelityDetails.VRSALDCONEXT, 'IDPERALTDESCFID': fidelityDetails.IDPERALTDESCFID });
				AccountGetAccountDetails.findOne().then(function (accountDetails) {
					var totalCost = Math.round((accountDetails.vlrprodutos - accountDetails.vlrdesconto) * 100) / 100;
					var fieldFidelityValue = fidelityWidget.getField('SALDOAPLICADO');
					var maxValue = (totalCost < fidelityWidget.currentRow.VSALDODISP) ? totalCost : fidelityWidget.currentRow.VSALDODISP;

					fidelityWidget.currentRow.SALDOAPLICADO = (accountDetails.fidelityDiscount > 0 && accountDetails.fidelityDiscount < maxValue) ?
						accountDetails.fidelityDiscount : maxValue;
					fidelityWidget.currentRow.IDCOMISVENDA = fidelityDetails.IDCOMISVENDA;
					fidelityWidget.currentRow.VRCOMISVENDA = fidelityDetails.VRCOMISVENDA;

					fieldFidelityValue.readOnly = fidelityDetails.IDPERALTDESCFID == 'N';
					fieldFidelityValue.range.max = maxValue;
					ScreenService.openPopup(fidelityWidget);
				});

			}.bind(this);
			if (!_.isEmpty(fidelitySearch)) {
				applyFidelityPopup(fidelitySearch);
			} else {
				AccountService.getFidelityDetails(widget.currentRow.CDCLIENTE, widget.currentRow.CDCONSUMIDOR).then(function (fidelityDetails) {
					applyFidelityPopup(fidelityDetails[0]);
				}.bind(this));
			}
		});
	};

	this.confirmTableFidelity = function (widget) {
		if (widget.isValid()) {
			if (ApplicationContext.PaymentController.validValue(widget.getField('SALDOAPLICADO'), '')) {
				self.getAccountData(function (accountData) {
					AccountGetAccountDetails.findOne().then(function (accountDetails) {
						var SALDOAPLICADO = Math.round(UtilitiesService.removeCurrency(widget.getField('SALDOAPLICADO').value()) * 100) / 100;
						// salva desconto na POSVENDAREST para parcial
						AccountService.setDiscountFidelity(accountData[0].NRVENDAREST, accountData[0].NRCOMANDA, accountDetails.posicao, SALDOAPLICADO).then(function () {
							positionsField = widget.container.getWidget('accountDetails').getField('positionsField');
							if (positionsField.position.length > 0) {
								positionsField._isStatusChanged = true;
								positionsField.widget.fields[0].setValue('P');
								self.refreshAccountDetailsMultiplePositions(positionsField.widget.widgets, positionsField.position, positionsField);
							} else {
								positionsField.widget.fields[0].setValue('M');
								self.refreshAccountDetails(positionsField.widget.widgets, '', positionsField, true);
							}
							ScreenService.closePopup(true);
							widget.container.getWidget('accountDetailsTable').activate();
						}.bind(this));
					}.bind(this));
				}.bind(this));
			}
		}
	};

	this.openBalconyFidelity = function (widget) {
		PaymentRepository.findOne().then(function (paymentData) {
			if (_.isEmpty(paymentData.CDCLIENTE) || _.isEmpty(paymentData.CDCONSUMIDOR)) {
				ScreenService.showMessage('Favor defina um consumidor antes de continuar.');
				return;
			}

			AccountService.getFidelityDetails(paymentData.CDCLIENTE, paymentData.CDCONSUMIDOR).then(function (fidelityDetails) {
				var fidelityWidget = widget.container.getWidget('fidelityPopup');
				fidelityWidget.setCurrentRow({ 'VSALDODISP': fidelityDetails[0].VRSALDCONEXT, 'IDPERALTDESCFID': fidelityDetails[0].IDPERALTDESCFID });

				if (paymentData.DATASALE.FIDELITYDISCOUNT > 0) {
					fidelityWidget.currentRow.SALDOAPLICADO = paymentData.DATASALE.FIDELITYDISCOUNT;
				}
				else {
					if (paymentData.DATASALE.TOTALVENDA < fidelityWidget.currentRow.VSALDODISP) {
						fidelityWidget.currentRow.SALDOAPLICADO = paymentData.DATASALE.TOTALVENDA;
					}
					else {
						fidelityWidget.currentRow.SALDOAPLICADO = fidelityWidget.currentRow.VSALDODISP;
					}
				}

				fidelityWidget.getField('SALDOAPLICADO').readOnly = fidelityDetails[0].IDPERALTDESCFID == 'N';
				ScreenService.openPopup(fidelityWidget);
			});
		});
	};

	this.confirmBalconyFidelity = function (widget) {
		if (widget.isValid()) {
			if (ApplicationContext.PaymentController.validValue(widget.getField('SALDOAPLICADO'), '')) {
				PaymentRepository.findOne().then(function (paymentData) {
					var SALDOAPLICADO = UtilitiesService.removeCurrency(widget.getField('SALDOAPLICADO').value());
					SALDOAPLICADO = Math.round(SALDOAPLICADO * 100) / 100;

					var totalCost = paymentData.DATASALE.TOTAL - paymentData.DATASALE.VRDESCONTO;
					totalCost = Math.round(totalCost * 100) / 100;

					if (SALDOAPLICADO > totalCost) SALDOAPLICADO = totalCost;

					var minCost = 0.01 * paymentData.numeroProdutos;
					var maxDiscount = Math.round((totalCost - minCost) * 100) / 100;

					paymentData.DATASALE.FIDELITYVALUE = SALDOAPLICADO;
					if (SALDOAPLICADO >= maxDiscount) {
						paymentData.DATASALE.TOTALVENDA = minCost;
						paymentData.DATASALE.FALTANTE = minCost;
						paymentData.DATASALE.FIDELITYVALUE = maxDiscount;
					}
					else {
						paymentData.DATASALE.TOTALVENDA = paymentData.DATASALE.TOTAL - paymentData.DATASALE.VRDESCONTO - SALDOAPLICADO;
						paymentData.DATASALE.TOTALVENDA = Math.round(paymentData.DATASALE.TOTALVENDA * 100) / 100;
						paymentData.DATASALE.FALTANTE = paymentData.DATASALE.TOTALVENDA;
					}

					paymentData.DATASALE.FIDELITYDISCOUNT = SALDOAPLICADO;

					PaymentRepository.save(paymentData).then(function () {
						ApplicationContext.PaymentController.attScreen(widget);
						ScreenService.closePopup(true);
					});
				});
			}
		}
	};

	this.buildPromoItem = function (product, position, refil, refilBypass, callback) {
		OperatorRepository.findOne().then(function (operatorData) {
			if (operatorData.IDCOLETOR != 'R') {
				if (product.PRITEM > 0 || product.IDIMPPRODUTO === '2') {
					/* REFIL MECHANICS */
					if (product.REFIL === 'S' && !refilBypass) {
						if (operatorData.modoHabilitado !== 'B') {
							TableActiveTable.findOne().then(function (table) {
								AccountService.checkRefil(operatorData.chave, table.NRVENDAREST, table.NRCOMANDA, product.CDPRODUTO, position).then(function (refilData) {
									if (refilData.length === 0) {
										self.buildPromoItem(product, position, false, true, callback);
									}
									else {
										ScreenService.confirmMessage(
											'Este produto é um refil?',
											'question',
											function () {
												self.buildPromoItem(product, position, true, true, callback);
											}.bind(this),
											function () {
												self.buildPromoItem(product, position, false, true, callback);
											}.bind(this)
										);
									}
								}.bind(this));
							}.bind(this));
						} else {
							ScreenService.showMessage("Produto refil não pode ser realizado no modo balcão.", "alert");
						}
					} else {
						callback(refil);
					}
				} else {
					ScreenService.showMessage("Produto sem preço.", 'alert');
				}
			} else {
				ScreenService.showMessage("Caixa habilitado apenas para modo recebedor.");
			}
		}.bind(this));
	};

	this.checkProducedProduct = function (row, widget) {
		OperatorRepository.findOne().then(function (operatorData) {
			if (operatorData.IDINFPRODPRODUZ == 'S') {
				ScreenService.confirmMessage('O produto ' + row.DSBUTTON + ' já foi produzido?', 'question', function () {
					self.cancelProduct(row, widget, 'S');
				}.bind(this), function () {
					self.cancelProduct(row, widget, null);
				}.bind(this));
			} else {
				self.cancelProduct(row, widget, null);
			}
		});
	};

	this.handleOpenCharge = function (widget) {
		if (widget.currentRow.vlrprodcobtaxa > 0) {
			PermissionService.checkAccess('retirarTaxaServico').then(function (CDSUPERVISOR) {
				var changeCharge = widget.container.getWidget('changeCharge');
				changeCharge.currentRow.CDSUPERVISOR = CDSUPERVISOR;
				ScreenService.openPopup(changeCharge).then(function () {
					self.handleShowChangeCharge(changeCharge);
				}.bind(this));
			}.bind(this));
		} else {
			ScreenService.showMessage("Não é possível aplicar taxa de serviço para uma mesa sem produtos pedidos.");
		}
	};

	this.applyCharge = function (widget) {
		if (widget.isValid()) {
			var currentRow = widget.getParent().currentRow;
			var VRCOMISPOR = 0;

			if (widget.getField('radioChargeChange').value() === 'P') {
				VRCOMISPOR = UtilitiesService.getFloat(widget.getField('radioCharge').value());
			} else {
				var vlrservico = UtilitiesService.getFloat(widget.getField('vlrservico').value());

				if (widget.getField('TIPOGORJETA').value() === 'P') {
					VRCOMISPOR = vlrservico;
				} else {
					VRCOMISPOR = Math.trunc((vlrservico / currentRow.vlrprodcobtaxa) * 10000) / 100;
					VRCOMISPOR = self.roundServiceCharge(VRCOMISPOR, vlrservico, currentRow.vlrprodcobtaxa);
				}
			}
			currentRow.vlrservico = Math.trunc(VRCOMISPOR * currentRow.vlrprodcobtaxa) / 100;
			currentRow.swiservico = currentRow.vlrservico == 0 ? false : true;

			widget.dataSource.data[0].value = VRCOMISPOR;

			self.recalcPrice(currentRow);
			ScreenService.closePopup();
		}
	};

	this.roundServiceCharge = function (VRCOMISPOR, vlrservico, totalProd) {
		var aux = 0.01;
		var newVRCOMISPOR = Math.trunc(VRCOMISPOR * totalProd) / 100;

		if (newVRCOMISPOR >= vlrservico) {
			if (newVRCOMISPOR == vlrservico) {
				return VRCOMISPOR;
			}
			else {
				VRCOMISPOR = Math.round((VRCOMISPOR - aux) * 100) / 100;
				return VRCOMISPOR;
			}
		} else {
			return self.roundServiceCharge(Math.round((VRCOMISPOR + aux) * 100) / 100, vlrservico, totalProd);
		}
	};

	this.handleShowChangeCharge = function (widget) {
		var field = widget.getField('radioCharge');
		var newData = Array();

		ParamsParameterRepository.findOne().then(function (paramsRepoReturn) {
			var vrconsu1 = paramsRepoReturn.VRCOMISVENDA;
			var vrconsu2 = paramsRepoReturn.VRCOMISVENDA2;
			var vrconsu3 = paramsRepoReturn.VRCOMISVENDA3;

			if (vrconsu1 == 0 && vrconsu2 == 0) {
				vrconsu2 = null;
			} else if (vrconsu1 == 0 && vrconsu3 == 0) {
				vrconsu3 = null;
			} else if (vrconsu2 == 0 && vrconsu3 == 0) {
				vrconsu3 = null;
			}

			if (vrconsu1 != null) {
				newData.push({
					'value': vrconsu1,
					'name': UtilitiesService.formatFloat(vrconsu1) + '%'
				});
			}
			if (vrconsu2 != null) {
				newData.push({
					'value': vrconsu2,
					'name': UtilitiesService.formatFloat(vrconsu2) + '%'
				});
			}
			if (vrconsu3 != null) {
				newData.push({
					'value': vrconsu3,
					'name': UtilitiesService.formatFloat(vrconsu3) + '%'
				});
			}

			field.dataSource.data = newData;
			field.defaultOption = 0;
		});
	};

	this.radioChargeChange = function (opcao, field1, field2, field3) {
		if (opcao.value() === 'M') {
			field2.isVisible = field3.isVisible = true;
			field1.isVisible = false;
		} else {
			field1.isVisible = true;
			field2.isVisible = field3.isVisible = false;
		}
	};

	this.limpaDesconto = function (widget) {
		OperatorRepository.findOne().then(function (operatorData) {
			// Limpar campo de observacoes de desconto
			widget.getField('MOTIVODESCONTO').clearValue();
			widget.getField('CDOCORR').clearValue();
			// Controla o fieldGroup FIELDS_OBSERVATION de acordo com parametrizacao
			widget.fieldGroups[1].opened = (operatorData.IDSOLOBSDESC === 'S');
		});
	};

	this.handleShowObsDesc = function (widget) {
		if (!_.isEmpty(widget.row.CDOCORR)) {
			widget.row.CDOCORR = [_.last(widget.row.CDOCORR)];
		}
	};

	this.produtosDesistencia = function (cart) {
		OperatorRepository.findOne().then(function (params) {
			var produtos = [];
			cart.forEach(function (cartItems) {
				if (_.isEmpty(cartItems.PRODUTOS) || cartItems.IDIMPPRODUTO === '1') {
					var produto = {};
					produto.NRVENDA = cartItems.NRVENDAREST || null;
					produto.QTPRODITCOMVENDES = cartItems.QTPRODCOMVEN;
					produto.CDPRODUTO = cartItems.CDPRODUTO;
					produto.VRPRECCOMVEN = cartItems.PRITEM;
					produto.VRDESCCOMVEN = cartItems.VRDESITVEND;
					produto.VRACRCOMVEN = cartItems.VRACRITVEND;
					produto.CDOCORR = cartItems.CDOCORR;
					if (cartItems.IDIMPPRODUTO === '1' && !_.isEmpty(cartItems.PRODUTOS)) {
						produto.CDOCORR = produto.CDOCORR.concat(cartItems.PRODUTOS[0].CDOCORR);
					}
					produtos.push(produto);
				} else {
					cartItems.PRODUTOS.forEach(function (cartItemsProducts) {
						if (_.isEmpty(cartItemsProducts.PRODUTOS)) {
							var produto = {};
							produto.NRVENDA = cartItems.NRVENDAREST || null;
							produto.QTPRODITCOMVENDES = cartItems.QTPRODCOMVEN;
							produto.CDPRODUTO = cartItemsProducts.CDPRODUTO;
							produto.VRPRECCOMVEN = cartItemsProducts.PRITEM;
							produto.VRDESCCOMVEN = cartItemsProducts.VRDESITVEND;
							produto.VRACRCOMVEN = cartItemsProducts.VRACRITVEND;
							produto.CDOCORR = cartItemsProducts.CDOCORR;
							produtos.push(produto);
						} else {
							cartItemsProducts.PRODUTOS.forEach(function (cartItemsProdProducts) {
								var produto = {};
								produto.NRVENDA = cartItems.NRVENDAREST || null;
								produto.QTPRODITCOMVENDES = cartItems.QTPRODCOMVEN;
								produto.CDPRODUTO = cartItemsProdProducts.CDPRODUTO;
								produto.VRPRECCOMVEN = cartItemsProdProducts.PRITEM;
								produto.VRDESCCOMVEN = cartItemsProdProducts.VRDESITVEND;
								produto.VRACRCOMVEN = cartItemsProdProducts.VRACRITVEND;
								produto.CDOCORR = cartItemsProdProducts.CDOCORR;
								produtos.push(produto);
							});
						}
					});
				}
			});
			if (params.modoHabilitado !== 'B') {
				AccountService.produtosDesistencia(produtos);
			} else {
				CarrinhoDesistencia.findAll().then(function (carrinhoDesistencia) {
					if (!carrinhoDesistencia) {
						carrinhoDesistencia = [];
					}
					CarrinhoDesistencia.save(carrinhoDesistencia.concat(produtos));
				});
			}
		});
	};

}

Configuration(function (ContextRegister) {
	ContextRegister.register('AccountController', AccountController);
});

// FILE: js/controllers/OperatorController.js
function OperatorController(OperatorService, TableService, AccountController, UtilitiesService, templateManager, ScreenService,
	TableActiveTable, Query, ApplicationContext, RegisterService, WindowService, OperatorRepository, ParamsAreaRepository,
	ParamsGroupRepository, ParamsClientRepository, ParamsSellerRepository, AccountCart, ParamsMenuRepository, ParamsGroupPriceChart,
	ParamsPriceChart, ParamsPrinterRepository, ParamsProdMessageRepository, ParamsProdMessageCancelRepository, ParamsParameterRepository,
	ParamsObservationsRepository, ZHPromise, FiliaisLogin, CaixasLogin, VendedoresLogin, metaDataFactory, PrinterPoynt, IntegrationService,
	SaveLogin, SSLConnectionId, PermissionService, ParamsMensDescontoObs, CarrinhoDesistencia, AccountService, PaymentService,
	PerifericosService, ProdSenhaPed) {

	var modoMesa = 'M';
	var modoComanda = 'C';
	var modoBalcao = 'B';
	var modoDelivery = 'D';
	var self = this;

	this.showHome = function () {
		webViewInterface.redePoyntHideApp();
	};

	this.loadLoginData = function (widget) {
		UtilitiesService.validateIp().then(
			function () {
				self.setEditWidget(widget, false, ["servidor", "FILIAL"]);
				var filialField = widget.getField("FILIAL");
				OperatorService.getFiliaisLogin(filialField).then(function (filiais) {
					filiais = filiais.dataset.FiliaisLogin || [];
					filialField.dataSource.data = filiais;

					if (_.isEmpty(filiais)) {
						filialField.readOnly = true;
						FiliaisLogin.clearAll();
						ScreenService.showMessage("Nenhuma filial encontrada na base.");
					} else {
						filialField.readOnly = false;

						SaveLogin.findOne().then(function (loginData) {
							if (!_.isEmpty(loginData)) {
								widget.setCurrentRow(loginData);
								widget.fields.forEach(function (field) {
									field.readOnly = false;
								});
							} else {
								widget.setCurrentRow(filiais[0]);
								self.getCaixasLogin(widget, filiais[0].CDFILIAL);
								self.getVendedoresLogin(widget, filiais[0].CDFILIAL);
							}
						});
					}
				});
			},
			function (message) {
				self.setEditWidget(widget, false, ["servidor"]);
				ScreenService.showMessage(message).then(function () {
					UtilitiesService.prepareServerForm(
						widget.container.getWidget("serverIpWidget")
					);
				});
			}
		);
	};

	this.setEditWidget = function (widget, editable, exceptions) {
		exceptions = exceptions || [];
		widget.fields.forEach(function (field) {
			if (_.indexOf(exceptions, field.name) == -1) {
				field.readOnly = !editable;
			}
		});
	};

	this.getCaixasLogin = function (widget, filial) {
		var caixaField = widget.getField("CAIXA");
		if (filial) {
			OperatorService.getCaixasLogin(filial, caixaField).then(function (caixas) {
				caixas = caixas.dataset.CaixasLogin || [];
				if (_.isEmpty(caixas)) {
					caixaField.readOnly = true;
					CaixasLogin.clearAll();
					ScreenService.showMessage("Nenhum caixa encontrado na filial.");
				} else {
					caixaField.readOnly = false;
				}
				if (caixas.length == 1) {
					widget.setCurrentRow(_.merge(widget.currentRow, caixas[0]));
				} else {
					widget.currentRow.CAIXA = null;
					widget.currentRow.CDCAIXA = null;
				}
				caixaField.dataSource.data = caixas;
			});
		} else {
			caixaField.readOnly = true;
			CaixasLogin.clearAll();
		}
	};

	this.getVendedoresLogin = function (widget, filial) {
		var operadorField = widget.getField("OPERADOR");
		if (filial) {
			operadorField.readOnly = false;
		} else {
			operadorField.readOnly = true;
			VendedoresLogin.clearAll();
		}
	};

	this.setVendedorLogin = function (widget) {
		var operador = widget.currentRow.OPERADOR;
		widget.getField("senha").readOnly = widget.getField(
			"entrar"
		).readOnly = !operador;
		widget.activate();
	};

	this.handleFilialChange = function (filialField) {
		var widget = filialField.widget;

		if (!filialField.value()) {
			widget.setCurrentRow({});
			self.setVendedorLogin(widget);
		}

		self.getCaixasLogin(widget, widget.currentRow.CDFILIAL);
		self.getVendedoresLogin(widget, widget.currentRow.CDFILIAL);
	};

	this.login = function (row, errorPopup) {
		var groupMenu = templateManager.project;
		var menus = groupMenu
			.getMenu("APLICACAO")
			.menus.concat(groupMenu.getMenu("CONSUMIDOR").menus);
		row.CDOPERADOR = row.OPERADOR;

		UtilitiesService.validateIp()
			.then(
				function () {
					if (!row.CDFILIAL) throw "Informe a filial.";
					if (!row.CDCAIXA) throw "Informe o caixa.";
					if (!row.CDOPERADOR) throw "Informe o Operador.";
					if (!row.senha) throw "Informe a senha.";

					self.saveLoginData(row);

					OperatorService.login(
						row.CDFILIAL,
						row.CDCAIXA,
						row.CDOPERADOR,
						row.senha,
						projectConfig.frontVersion,
						projectConfig.currentMode
					).then(
						function (data) {
							if (data.OperatorRepository) {
								if (data.OperatorRepository[0].paramsImpressora) {
									PerifericosService.test(data.OperatorRepository[0].paramsImpressora).then(function (response) {
										if (!response.error) {
											self.handleLogin(data, menus);
										} else {
											ScreenService.showMessage(response.message);
										}
									});
								} else {
									self.handleLogin(data, menus);
								}
							} else {
								errorPopup.setCurrentRow({ erro: data[0] });
								ScreenService.openPopup(errorPopup);
							}
						}.bind(this),
						function () {
							throw "Erro na tentativa de login, verifique a configuração de IP.";
						}
					);
				}.bind(this)
			)
			.catch(function (err) {
				ScreenService.showMessage(err);
			});
	};

	this.handleLogin = function (data, menus) {
		var operatorData = data.OperatorRepository[0];
		self.checkSSLConnectionId(operatorData).then(
			function (checkSSLConnectionIdResult) {
				if (!checkSSLConnectionIdResult.error) {
					if (operatorData.IDCOLETOR !== "C") {
						var estadoCaixa = operatorData.estadoCaixa;
						var IDPALFUTRABRCXA = operatorData.IDPALFUTRABRCXA;
						var VRABERCAIX = operatorData.VRABERCAIX;
						var obrigaFechamento = operatorData.obrigaFechamento;
						if (estadoCaixa === "fechado" && IDPALFUTRABRCXA === "S") {
							this.bindedDoLogin = _.bind(this.doLogin, this, data, menus);
							WindowService.openWindow("OPEN_REGISTER_SCREEN");
						} else if (estadoCaixa === "fechado" && IDPALFUTRABRCXA === "N") {
							RegisterService.openRegister(
								operatorData.chave,
								VRABERCAIX
							).then(function () {
								this.doLogin(data, menus);
							});
						} else if (estadoCaixa === "aberto" && obrigaFechamento) {
							RegisterService.setClosingOnLogin(true);
							this.bindedDoLogin = _.bind(this.doLogin, this, data, menus);
							WindowService.openWindow("CLOSE_REGISTER_SCREEN");
						} else {
							this.doLogin(data, menus);
						}
					} else {
						if (operatorData.modoHabilitado === modoBalcao) {
							ScreenService.showMessage(
								"Modo balcão não pode ser coletor.",
								"alert"
							);
						} else if (operatorData.modoHabilitado === modoDelivery) {
							ScreenService.showMessage(
								"Modo delivery não pode ser coletor.",
								"alert"
							);
						} else {
							this.doLogin(data, menus);
						}
					}
				} else {
					ScreenService.showMessage(checkSSLConnectionIdResult.message);
				}
			}.bind(this)
		);
	};

	this.saveLoginData = function (row) {
		var saveRow = _.clone(row);
		saveRow.senha = "";
		SaveLogin.save(saveRow);
	};

	this.bindedDoLogin = this.doLogin;

	this.doLogin = function (data, menus) {
		OperatorRepository.save(data.OperatorRepository).then(function () {
			var operatorData = data.OperatorRepository[0];
			this.handleMenuOptions(operatorData.modoHabilitado, operatorData.IDCOLETOR, menus,
				operatorData.IDHABCAIXAVENDA, operatorData.NMFANVEN, operatorData.CDOPERADOR);

			TableActiveTable.remove(Query.build()).then(function () {
				UtilitiesService.backMainScreen();
				templateManager.project.notifications[0].isVisible = false;
			});

			self.checkPendingPayment(operatorData.IDTPTEF, null);
		}.bind(this));
	};

	this.handleMenuOptions = function (
		modo,
		IDCOLETOR,
		menus,
		IDHABCAIXAVENDA,
		NMOPERADOR,
		CDOPERADOR
	) {
		if (modo === modoMesa) {
			this.handleMesaMenu(menus, IDCOLETOR);
		} else if (modo === modoComanda) {
			this.handleComandaMenu(menus, IDCOLETOR);
		} else if (modo === modoBalcao) {
			this.handleBalcaoMenu(menus);
		} else if (modo === modoDelivery) {
			this.handleBalcaoMenu(menus);
		}

		SaveLogin.findOne().then(function (loginData) {
			buildUserMenuInfo(
				NMOPERADOR,
				CDOPERADOR,
				loginData.CAIXA,
				loginData.FILIAL
			);
		});

		var menusAdministracao = this.searchByName(
			"administracao",
			templateManager.project.groupMenu
		).menus;
		this.searchByName("trocaModo", menusAdministracao).isVisible =
			modosCaixa[IDHABCAIXAVENDA].modos.length > 1;

		if (!!window.cordova) {
			if (!!cordova.plugins.KioskPOS) {
				var toggleLockDevice = this.searchByName(
					"toggleLockDevice",
					menusAdministracao
				);
				toggleLockDevice.isVisible = true;

				cordova.plugins.KioskPOS.isInKiosk(function (isInKiosk) {
					toggleLockDevice.label = isInKiosk ? "Desbloquear Dispositivo" : "Bloquear Dispositivo";
				});
			} else if (!!cordova.plugins.GertecSitef) {
				this.searchByName("deviceSerial", menusAdministracao).isVisible = true;
			}
		}

		var modoVenda = IDCOLETOR !== "C";
		this.searchByName("Fechar Caixa", menus).isVisible = modoVenda;
		this.searchByName("Cancelar Venda", menus).isVisible = modoVenda;
		this.searchByName("Funções Gerais", menus).isVisible = modoVenda;
		this.searchByName("Crédito Pessoal", menus).isVisible = modoVenda;
	};

	this.handleMesaMenu = function (menus) {
		this.searchByName("Dashboard", menus).isVisible = false;
		this.searchByName("Comandas", menus).isVisible = false;
		this.searchByName("Mesas", menus).isVisible = true;
		this.searchByName("Mensagem Produção", menus).isVisible = true;
		this.searchByName("Transações", menus).isVisible = false;
		this.searchByName("Fazer Pedido", menus).isVisible = false;
		this.searchByName("Solicitar Conta", menus).isVisible = false;
		this.searchByName("Chamar Garçom", menus).isVisible = false;
		this.searchByName("Pedidos Realizados", menus).isVisible = false;
	};

	this.handleComandaMenu = function (menus) {
		this.searchByName("Dashboard", menus).isVisible = false;
		this.searchByName("Mesas", menus).isVisible = false;
		this.searchByName("Comandas", menus).isVisible = true;
		this.searchByName("Mensagem Produção", menus).isVisible = true;
		this.searchByName("Transações", menus).isVisible = false;
		this.searchByName("Fazer Pedido", menus).isVisible = false;
		this.searchByName("Solicitar Conta", menus).isVisible = false;
		this.searchByName("Chamar Garçom", menus).isVisible = false;
		this.searchByName("Pedidos Realizados", menus).isVisible = false;
	};

	this.handleBalcaoMenu = function (menus) {
		this.searchByName("Dashboard", menus).isVisible = false;
		this.searchByName("Mesas", menus).isVisible = false;
		this.searchByName("Comandas", menus).isVisible = false;
		this.searchByName("Mensagem Produção", menus).isVisible = false;
		this.searchByName("Transações", menus).isVisible = false;
		this.searchByName("Fazer Pedido", menus).isVisible = false;
		this.searchByName("Solicitar Conta", menus).isVisible = false;
		this.searchByName("Chamar Garçom", menus).isVisible = false;
		this.searchByName("Pedidos Realizados", menus).isVisible = false;
	};

	var buildUserMenuInfo = function (NMOPERADOR, CDOPERADOR, CAIXA, FILIAL) {
		var info = [CDOPERADOR, FILIAL, CAIXA];
		var headerInfo = NMOPERADOR + " - " + CDOPERADOR + " | " + CAIXA;

		ScreenService.buildUserData(NMOPERADOR, info);
		ScreenService.setHeaderInfo(headerInfo);

		templateManager.hideUserData = true;
	};

	this.configureMenu = function () {
		templateManager.project.notifications = false;
		var menus = this.searchByName(
			"APLICACAO",
			templateManager.project.groupMenu
		);
		var menusAdministracao = this.searchByName(
			"administracao",
			templateManager.project.groupMenu
		);
		var menuslogin = menusAdministracao.menus;

		this.searchByName("Mesas", menus).isVisible = false;
		this.searchByName("Comandas", menus).isVisible = false;
		this.searchByName("Mensagem Produção", menus).isVisible = false;
		this.searchByName("Transações", menus).isVisible = false;
		this.searchByName("Fazer Pedido", menus).isVisible = true;
		this.searchByName("Solicitar Conta", menus).isVisible = true;
		this.searchByName("Chamar Garçom", menus).isVisible = true;
		this.searchByName("Pedidos Realizados", menus).isVisible = true;
		menusAdministracao.isVisible = true;
		this.searchByName("Logout", menuslogin).isVisible = false;
		this.searchByName("Sair", menuslogin).isVisible = true;
		this.searchByName("Dashboard", menus).isVisible = false;
	};

	this.searchByName = function (nome, menus) {
		return _.find(menus, { name: nome });
	};

	this.limitField = function (field, length) {
		var value = field.value();
		var modifier = Math.pow(10, length) - 1;
		while (value > modifier) value /= 10;
		field.value(parseInt(value));
	};

	this.logout = function (field) {
		field.windowName = templateManager.containers.zeedhi_project.mainWindow;
		ZHPromise.all([
			OperatorRepository.clearAll(),
			ParamsAreaRepository.clearAll(),
			ParamsGroupRepository.clearAll(),
			ParamsClientRepository.clearAll(),
			ParamsSellerRepository.clearAll(),
			ParamsMenuRepository.clearAll(),
			ParamsGroupPriceChart.clearAll(),
			ParamsPriceChart.clearAll(),
			ParamsPrinterRepository.clearAll(),
			ParamsProdMessageRepository.clearAll(),
			ParamsProdMessageCancelRepository.clearAll(),
			ParamsParameterRepository.clearAll(),
			ParamsObservationsRepository.clearAll(),
			ParamsMensDescontoObs.clearAll(),
			AccountCart.clearAll(),
			AccountService.logout(),
			CarrinhoDesistencia.clearAll(),
			ProdSenhaPed.clearAll()
		]).then(function () {
			templateManager.project.notifications[0].isVisible = false;
			ScreenService.openWindow(
				templateManager.containers.zeedhi_project.mainWindow
			);
		});
	};

	this.openChangeModePopup = function (menu) {
		OperatorRepository.findOne().then(function (operatorData) {
			var popupTrocaModo = self.searchByName("popupTrocaModo", menu.widgets);
			popupTrocaModo = metaDataFactory.widgetFactory(
				popupTrocaModo,
				templateManager.container
			);
			popupTrocaModo.currentRow = {};
			popupTrocaModo.currentRow.chaveSessao = operatorData.chave;
			popupTrocaModo.getField("nome").dataSource.data = _.filter(
				modosWaiter,
				function (modo) {
					return _.some(
						modosCaixa[operatorData.IDHABCAIXAVENDA].modos,
						function (caixaMode) {
							return caixaMode == modo.codigo;
						}
					);
				}
			);
			ScreenService.openPopup(popupTrocaModo);
		});
	};

	this.trocaModo = function (popupTrocaModo) {
		ScreenService.closePopup();
		ScreenService.toggleSideMenu();
		ScreenService.openWindow("login");
		OperatorService.trocaModoCaixa(
			popupTrocaModo.currentRow.chaveSessao,
			popupTrocaModo.currentRow.codigo
		).then(function (loginData) {
			projectConfig.currentMode = popupTrocaModo.currentRow.chaveSessao;
			var groupMenu = templateManager.project;
			var menus = groupMenu
				.getMenu("APLICACAO")
				.menus.concat(groupMenu.getMenu("CONSUMIDOR").menus);
			self.doLogin(loginData, menus);
			CarrinhoDesistencia.remove(Query.build());
			ProdSenhaPed.remove(Query.build());
		});
	};

	this.reprintTEFVoucher = function () {
		PrinterPoynt.reprintTEFVoucher().then(function (result) {
			if (!result.error) {
				ScreenService.toggleSideMenu();
			} else {
				ScreenService.showMessage(
					"Falha ao imprimir comprovante do TEF. " + result.message,
					"alert"
				);
			}
		});
	};

	this.checkPendingPayment = function (IDTPTEF, errorMessage) {
		OperatorService.findPendingPayments().then(function (payments) {
			payments = payments[0];
			if (payments.error) {
				if (!_.isEmpty(payments.message))
					ScreenService.showMessage(payments.message, 'alert');
				else if (_.isEmpty(payments.message) && errorMessage !== null)
					ScreenService.showMessage(errorMessage, 'alert');
			} else {
				payments = payments.data;
				payments.forEach(function (payment) {
					var transactionDate = payment.TRANSACTIONDATE;
					payment.TRANSACTIONDATE = transactionDate.slice(6, 8) + transactionDate.slice(4, 6) + transactionDate.substring(0, 4);
				});

				payments[0].IDTPTEF = IDTPTEF;
				ScreenService.showMessage("Há transações pendentes que serão canceladas.").then(function () {
					IntegrationService.reversalIntegration(self.mochRemovePaymentSale, payments).then(function (reversalIntegrationResult) {
						if (!reversalIntegrationResult.error) {
							PaymentService.removePayment(payments);
							PaymentService.handleRefoundTEFVoucher(reversalIntegrationResult.data);
						} else {
							if (reversalIntegrationResult.data.length > 1) {
								var reversed = _.map(reversalIntegrationResult.data, function (reversal) {
									return _.isUndefined(reversal.toRemove) ? null : reversal.toRemove.CDNSUHOSTTEF;
								});
								reversed = _.compact(reversed);

								payments = _.filter(payments, function (payment) {
									return _.indexOf(reversed, payment.CDNSUHOSTTEF) !== -1;
								}.bind(this));

								PaymentService.removePayment(payments);
							}

							ScreenService.showMessage(reversalIntegrationResult.message, 'alert');
						}
					}.bind(this));
				}.bind(this));
			}
		}.bind(this));
	};

	this.mochRemovePaymentSale = function () {
		return new Promise.resolve(true);
	};

	this.handlePrintText = function (printObject) {
		return Array({
			STLPRIVIA: printObject.customerReceipt,
			STLSEGVIA: printObject.merchantReceipt
		});
	};

	this.checkSSLConnectionId = function (data) {
		var result = {
			error: true,
			message: ""
		};

		return new Promise(function (resolve) {
			if (
				data.IDTPTEF === "5" &&
				(data.IDUTLSSL === "3" || data.IDUTLSSL === "4")
			) {
				OperatorService.buscaTefSSLConnectionId(device.serial).then(
					function (buscaTefSSLConnectionIdResult) {
						if (!_.isEmpty(buscaTefSSLConnectionIdResult)) {
							SSLConnectionId.save(buscaTefSSLConnectionIdResult);
							result.error = false;
							resolve(result);
						} else {
							result.message =
								"Operação bloqueada. Não há um código de conexão SSL parametrizado para este dispositivo.";
							resolve(result);
						}
					}.bind(this)
				);
			} else {
				result.error = false;
				resolve(result);
			}
		});
	};

	this.handleEnterButton = function (args) {
		var keyCode = args.e.keyCode;
		if (keyCode === 13 || keyCode === 9) {
			UtilitiesService.handleCloseKeyboard();
			var field = args.owner.field;
			var widget = field.widget;

			if (widget.name === "serverIpWidget") {
				if (field.name === "ip") {
					if (!Util.isDesktop()) document.getElementById("porta").focus();
				} else if (field.name === "porta") {
					UtilitiesService.setServerIp(
						widget.currentRow,
						templateManager.container.getWidget("loginWidget")
					);
				}
			} else if (widget.name === "loginWidget" && !Util.isDesktop()) {
				this.login(
					widget.currentRow,
					widget.container.getWidget("errorConsole")
				);
			} else if (
				widget.name === "validateSupervisorWidget" ||
				widget.name === "unlockDeviceWidget"
			) {
				if (field.name === "supervisor") {
					if (!Util.isDesktop()) document.getElementById("pass").focus();
				} else if (field.name === "pass") {
					PermissionService.validateSupervisorPass(widget.currentRow);
				}
			} else if (widget.name === "consumerPasswordWidget") {
				PermissionService.checkConsumerPassword(widget.currentRow, widget);
			}
		}
	};

	this.validateSendMessageAccess = function () {
		var permissionName = "mensagemProducao";

		PermissionService.checkAccess(permissionName).then(
			function (CDSUPERVISOR) {
				self.openSendMessageWindow(CDSUPERVISOR);
			}.bind(this),
			function (rejectionStatus) {
				if (rejectionStatus === -1) {
					self.openSendMessageWindow();
				}
			}
		);
	};

	this.openSendMessageWindow = function (CDSUPERVISOR) {
		var windowName = "sendWaiterless";
		var sendMessageWidgetName = "sendMessage";

		ScreenService.openWindow(windowName).then(
			function () {
				ScreenService.toggleSideMenu();

				var sendMessageWidget = templateManager.container.getWidget(
					sendMessageWidgetName
				);
				if (sendMessageWidget) {
					sendMessageWidget.CDSUPERVISOR = CDSUPERVISOR;
				}
			}.bind(this)
		);
	};
}

Configuration(function (ContextRegister) {
	ContextRegister.register("OperatorController", OperatorController);
});


// FILE: js/controllers/OrderController.js
function OrderController(ZHPromise, ApplicationContext, ScreenService, OrderService, OrderGetAccessRepository, OperatorRepository, OrderRequestLoginRepository, ParamsMenuRepository, OrderCurrentProductRepository, Query, OrderProductObservation, AccountCart, OrderCurrentUser, AccountController, AccountGetAccountItems, AccountService, templateManager, TotalCartRepository, OrderCallWaiterRepository, OrderGetCallRepository, OperatorController, TableRepository, OrderBlockedIps, ConfigIpRepository, UtilitiesService, ConsumerLoginRepository, SessionRepository, WindowService){

	this.inProccess = false;

	this.showTemp = function (){
		WindowService.openWindow('ORDER_TEMPORARY_SCREEN');
	};

	this.showOrderCart = function (cart){
		if (cart.length > 0){
			AccountCart.save(cart).then(function (){
				WindowService.openWindow('ORDER_CHECK_ORDER_SCREEN');
			});
		}
		else {
			ScreenService.showMessage("Não há produtos no carrinho.");
		}
	};

	this.confirmOrder = function (widget){
		ScreenService.confirmMessage(
			'Deseja transmitir o pedido?',
			'question',
			function (){
				AccountController.order(widget);
			},
			function (){}
		);
	};

	this.checkSession = function(args){
		SessionRepository.findOne().then(function (consumerDetails){
			args.owner.newRow();
			args.owner.moveToFirst();
			if (consumerDetails){
				args.owner.setCurrentRow(consumerDetails);
			}
		});
	};

	this.login = function(row, tablePopup, setIPPopup){
		if (!row.DSEMAILCONS || !UtilitiesService.checkEmail(row.DSEMAILCONS)){
			ScreenService.showMessage("Favor introduzir um e-mail válido.");
		}
		else if (!row.password || row.password.length === 0){
			ScreenService.showMessage("Favor introduzir a senha.");
		}
		else {
			ConfigIpRepository.findOne().then(function (ipInfo){
				if (ipInfo !== null){
					OrderService.login(row.DSEMAILCONS, row.password).then(function (consumerDetails){
						SessionRepository.save(consumerDetails).then(function (){
							tablePopup.setCurrentRow({});
							ScreenService.openPopup(tablePopup);
						});
					});
				}
				else {
					ScreenService.showMessage("Antes de efetuar o login, configure o IP do servidor.");
					ScreenService.openPopup(setIPPopup);
				}
			});
		}
	};

	//OBS: codigo lixao a frente
	//dava pra fazer uma função mas seria tanto callback dentro de callback que estou deixando assim
	//gabriel s2
	this.prepareOrderCloseAccountLabels = function (widget) {
		AccountController.prepareAccountDetails(widget,
			function(){
				//orderCloseAccount
				var labels = ["Valor dos produtos", "Valor do serviço", "Valor do couvert", "Valor da consumação", "Valor total"];
				var valores = [ 'vlrprodutos', 'vlrservico', 'vlrcouvert', 'vlrconsumacao', 'vlrtotal'];
				var gridData = widget.dataSource.data[0];
				var data = [];
				//monta array novo com base nos dados do data source e nas labels escritas ali em cima
			    valores.some(function(element, index, array){
			    	if(gridData[element] !== null && gridData[element] !== undefined ){
			    		dataObject ={
			    			"LABEL":labels[index]+' - '+UtilitiesService.toCurrency(gridData[element])
			    		};
			    		data.push(dataObject);
			    	}
			    });
				widget.dataSource.data = data;
				//orderAccountDetails
				var orderWidget = widget.container.getWidget('orderAccountDetails');
			    var orderLabels = ["Numero de produtos"];
				var orderValores = ['numeroProdutos'];
				var orderGridData = orderWidget.dataSource.data[0];
				var orderData = [];
				//monta array novo com base nos dados do data source e nas labels escritas ali em cima
			    orderValores.some(function(element, index, array){
			    	if(orderGridData[element] !== null && orderGridData[element] !== undefined ){
			    		dataObject ={
			    			"LABEL":orderLabels[index]+' - '+orderGridData[element]
			    		};
			    		orderData.push(dataObject);
			    	}
			    });
				orderWidget.dataSource.data = orderData;
			});
	};
	//FIM codigo lixao

	this.showOrderLogin = function () {
		WindowService.openWindow('ORDER_LOGIN_SCREEN');
	};

	this.showAccess = function () {
		WindowService.openWindow('ORDER_ACCESS_SCREEN');
	};

	this.showMenu = function () {
		WindowService.openWindow('ORDER_MENU_SCREEN');
	};

	// refaz o preço para ficar no formato quantidade x preço (2x R$5,00)
	this.accountPrice = function (itensData){
		var cont = 0;
		itensData.forEach(function(item){
			preco = item.preco;
			quantidade = item.quantidade;
			itensData[cont].preco = quantidade + ' x ' + preco;
			cont++;
		});
		return itensData;
	};

	this.prepareAccountRequest = function (widget){
		this.checkAccess(function (){
			delete widget.dataSource.data;
			widget.currentRow = {};

			OperatorRepository.findAll().then(function (operatorData){
				AccountController.getAccountData(function (accountData){
					// prepara parâmetros para chamar a getAccountDetails (traz consumação, serviço, total, couvert e produtos)
					var chave = operatorData[0].chave;
					var modoHabilitado = operatorData[0].modoHabilitado;
					// isso é necessário pois dentro da getAccountDetails (parcial), o Order tem que se comportar como modo mesa
					if (modoHabilitado === 'O'){
						modoHabilitado = 'M';
					}
					var nrComanda = accountData[0].NRCOMANDA;
					var nrVendaRest = accountData[0].NRVENDAREST;

					AccountService.getAccountDetails(chave, modoHabilitado, nrComanda, nrVendaRest, 'M', '').then(function (accountDetailsData) {
						// pega os produtos

						if (accountDetailsData.AccountGetAccountItems.length === 0){
							ScreenService.showMessage('Não foi realizado nenhum pedido.');
							WindowService.openWindow('ORDER_MENU_SCREEN');
						}
						else{
							var itensData = accountDetailsData.AccountGetAccountItems;
							// refaz o preço para ficar no formato quantidade x preço (2x R$5,00)
							itensData = this.accountPrice(itensData);

							itensData.forEach(function(item) {
							    var dt = item.DTHRINCOMVEN;
							    var hora = dt.substring(0, 5);
							    var dieMonth = dt.substring(8, 13);
							    var today = new Date();
							    var year = today.getFullYear();
							    item.DTHRINCOMVEN = hora + " - " + dieMonth + "/" + year;
							 });

							widget.dataSource.data = itensData;
							widget.moveToFirst();
						}
					}.bind(this));
				}.bind(this));
			}.bind(this));
		}.bind(this));
	};

	this.checkAccess = function (callBack) {
		//Verifica se a mesa não está fechada ou com a conta solicitada
		//Caso esteja, o usuário não pode continuar usando o Order
		OperatorRepository.findOne().then(function (operatorData) {
			//Só faz isso no modo order
			if (operatorData.modoHabilitado === 'O') {

				var chave = operatorData.chave;

				AccountController.getAccountData(function (accountData) {
					var NRCOMANDA = accountData[0].NRCOMANDA;
					var NRVENDAREST = accountData[0].NRVENDAREST;

					OrderService.checkAccess(chave, NRCOMANDA, NRVENDAREST).then(function (tableData) {
						if (!tableData[0].OK) {
							this.removeAccess();
							this.showLoading();
							WindowService.openWindow('ORDER_LOGIN_SCREEN');
						} else {
							callBack();
						}
					}.bind(this));
				}.bind(this));
			} else {
				callBack();
			}
		}.bind(this));
	};

	this.requestLogin = function (table){
		ConsumerLoginRepository.findOne().then(function (consumerDetails){
			this.checkBlockedUsers(function () {
				OrderCurrentUser.save(consumerDetails);
				AccountCart.remove(Query.build());
				ConfigIpRepository.findOne().then(function (ipInfo){
					OrderService.requestLogin(consumerDetails.NMCONSUMIDOR, table, projectConfig.frontVersion, ipInfo.ipForBack).then(function(requestLoginData){
						if (requestLoginData.OperatorRepository) {
							OperatorController.configureMenu();
							this.showMenu();
						} else {
							this.showAccess();
						}
					}.bind(this),
					function(){
						ScreenService.showMessage("Erro na tentativa de login, verifique sua conexão com a internet.");
					});
				}.bind(this));
			}.bind(this));
		}.bind(this));
	};

	this.removeAccess = function (NRACESSOUSER) {
		OperatorRepository.findOne().then(function (operatorData) {
			//Caso eu informe o NRACESSOUSER, ele faz com o numero informado, senão pega do repositório
			var chave = null;
			if (!NRACESSOUSER) {
				NRACESSOUSER = operatorData.NRACESSOUSER;
				chave = operatorData.chave;
			}

			OrderService.controlUserAccess(NRACESSOUSER, 'I', chave);
		});
	};

	this.controlUserAccess = function(row, status){
		OperatorRepository.findOne().then(function (operatorData){
			OrderService.controlUserAccess(row.nracessouser, status, operatorData.chave).then(function(){
				if (status === 'B'){
					this.goToTablesPage('IP bloqueado com sucesso.');
				}
				else if (status === 'I') {
					var query = Query.build()
									.where('NRMESA').equals(row.mesa);

					TableRepository.findOne(query).then(function(activeTable){
						if (activeTable === null || (activeTable !== null && activeTable.IDSTMESAAUX === "S")){
							ScreenService.showMessage('Não é possível liberar acesso para uma mesa fechada.');
							this.goToTablesPage();
						}
						else {
							this.goToTablesPage('Solicitação desconsiderada com sucesso.');
						}
					}.bind(this));
				}
				else {
					this.goToTablesPage('Ação confirmada.');
				}
			}.bind(this));
		}.bind(this));
	};

	this.goToTablesPage = function (message){
		this.getNotifications().then(function (){
			ScreenService.closePopup();
			WindowService.openWindow('TABLES_SCREEN');
			if (message){
				ScreenService.successNotification(message);
			}
		});
	};

	this.checkBlockedUsers = function (callBack) {
		OrderService.checkBlockedUsers().then(function (dataBack) {
			if (dataBack[0].OK) {
				callBack();
			} else {
				ScreenService.showMessage("Seu IP está bloqueado. Para liberar, chame o garçom.");
			}
		});
	};

	this.getBlockedIps = function (callBack) {
		OperatorRepository.findOne().then(function (operatorData) {
			var chave = operatorData.chave;
			OrderService.getBlockedIps(chave).then(function (IPs) {
				callBack(IPs);
			});
		});
	};

	this.unblockUser = function (row) {
		OperatorRepository.findOne().then(function (operatorData) {
			var chave = operatorData.chave;
			var NRACESSOUSER = row.NRACESSOUSER;
			OrderService.controlUserAccess(NRACESSOUSER, 'I', chave).then(function () {
				ScreenService.showMessage("Desbloqueado com sucesso!");
				ScreenService.closePopup();
			});
		});
	};

	this.prepareOpeningBlock = function (widget, row) {
		var arrayRow = [];
		arrayRow.push(row);
		widget.dataSource.data = arrayRow;
		widget.setCurrentRow(row);
	};

	this.prepareBlockedIps = function (widget) {
		this.getBlockedIps(function (IPs) {
			widget.dataSource.data = IPs;
		});
	};

	//Esta função irá liberar o acesso do consumidor. Ela é chamada pela função TableController.open
	this.completeReleaseAccess = function (nracessouser, tablesWidget) {
		OperatorRepository.findOne().then(function(operatorData){
			OrderService.allowUserAccess(operatorData.chave, nracessouser).then(function(){
				ScreenService.closePopup();
				ScreenService.showMessage('Acesso liberado com sucesso.');
				ApplicationContext.TableController.refreshTables(tablesWidget);
			});
		});
	};

	//Função que monta cardápio no order
	this.loadMenu = function (menuWidget){
		this.checkAccess(function (){
			ParamsMenuRepository.findAll().then(function (products){
				AccountCart.findAll().then(function (cart){
					menuWidget.dataSource.data = products;

					var total = 0;
					for (var i in cart){
						total += cart[i].PRITEM * (cart[i].qtty || 1);
					}

					menuWidget.shoppingCart.items = cart;
					menuWidget.shoppingCart.deliveryFee = 0;
					menuWidget.shoppingCart.subtotal = total;
					menuWidget.shoppingCart.total = total;
				}.bind(this));
			}.bind(this));
		}.bind(this));
	};

	this.loadProductDetails = function(productWidget){
		var obs = AccountController.getObservations(productWidget.productItem.OBSERVATIONS);
		if (productWidget.productItem.detailPages === undefined){
			productWidget.productItem.detailPages = [];
			productWidget.productItem.detailPages.push({
				title: "observações",
				constraints: {
					minSelection: 0
				},
				items: obs
			});
		}
	};

	this.updateTotal = function (widgetData, widget) {
		var QTDPRODUCT = widgetData.QTDPRODUCT;
		var PRITEM = widgetData.PRITEM;
		var TOTAL = parseFloat(QTDPRODUCT) * parseFloat(PRITEM);
		TOTAL = UtilitiesService.toCurrency(TOTAL);
		OrderCurrentProductRepository.findAll().then(function (product) {
			product[0].TOTAL = TOTAL;
			if(product[0].TOTAL === "NaN"){
				var total = parseFloat(product[0].PRITEM) * parseFloat(product[0].QTDPRODUCT);
				total = UtilitiesService.toCurrency(total);
				widget.currentRow.TOTAL = total;
			}else{
				widget.currentRow.TOTAL = TOTAL;
			}
		}.bind(this));
	};

	this.prepareProductDetail = function (productWidget, container) {
	    productWidget.newRow();
	    container.changeMode('lista');
		OrderCurrentProductRepository.findAll().then(function (product) {
	        if(productWidget.dataSource.data && productWidget.dataSource.data.length > 0) {
	            delete productWidget.dataSource.data;
	        }

	        product[0].QTDPRODUCT = 1;

	        var total = parseFloat(product[0].PRITEM) * parseFloat(product[0].QTDPRODUCT);
			total = UtilitiesService.toCurrency(total);
			product[0].TOTAL = total;


			productWidget.setCurrentRow(product[0]);

			// associando as observações ao datasource das observações do produto
			productWidget.getField('CDOCORR').dataSource.data = AccountController.getObservations(product[0].OBSERVATIONS);

			templateManager.updateTemplate();
		}.bind(this));
	};

	this.getData = function(){
		var today = new Date();
	    var dd = today.getDate();
	    var mm = today.getMonth()+1; //January is 0!

	    var yyyy = today.getFullYear();
	    if(dd<10){
	        dd='0'+dd;
	    }
	    if(mm<10){
	        mm='0'+mm;
	    }
	    var todayStr = dd+'/'+mm+'/'+yyyy + " " + new Date().toTimeString().replace(/.*(\d{2}:\d{2}:\d{2}).*/, "$1");
	    return todayStr;
	};

	// OBSOLETE
	// this.addToOrderCart = function(product){
	// 	AccountController.getOrderCodeProductID(function (id){
	// 		var position = '1';
	// 		var cartItems = {
	// 			ID: id,
	// 			UNIQUEID: id,
	// 			GRUPO: product.NMGRUPO,
	// 			CDPRODUTO: product.CDPRODUTO,
	// 			DSBUTTON: product.DSBUTTON,
	// 			POSITION: "posição " + position,
	// 			POS: position,
	// 			PRECO: product.PRECO,
	// 			PRITEM: product.PRITEM,
	// 			IMPRESSORAS: product.IMPRESSORAS,
	// 			CDOCORR: product.CDOCORR,
	// 			OBSERVATIONS: product.OBSERVATIONS,
	// 			QTDPRODUCT: product.QTDPRODUCT || 1,
	// 			DATA: this.getData()
	// 		};
	// 		AccountCart.save(cartItems);
	// 	}.bind(this));
	// };

	this.updateOrderCart = function(widget) {
		var cart = widget.dataSource.data;
		handleOneChoiceOnly(widget.currentRow, 'NRSEQIMPRLOJA');
		cart.forEach(function(product){
			if (!product.PRODUTOS){
				product.TXPRODCOMVEN = this.obsToText(product.CDOCORR, product.DSOCORR_CUSTOM);
				//product.ATRASOPROD = this.formatProductDelay(product.ATRASOPROD);
				product.NMIMPRLOJA = "";
				if (product.NRSEQIMPRLOJA) {
					product.NMIMPRLOJA = getPrinterName(product.NRSEQIMPRLOJA[0], product.IMPRESSORAS);
				}
			}
		}.bind(this));

		this.saveCart(cart);
	};

	this.hideLoading = function () {
		$('.zh-background-loading').removeClass('hideLoading');
		$('.zh-background-loading').addClass('hideLoading');
	};

	this.showLoading = function () {
		$('.zh-background-loading').removeClass('hideLoading');
	};

	this.checkPermission = function () {
		this.hideLoading();
		// além de verificar a pemissão do usuário, esta função recebe a field time regress para construir o cronômetro regressivo na tela
		var tempo = 720;

		// a validação de permissão e ocorrência dos segundos no relógio ocorrem dentro deste set interval
		var interval = setInterval(function() {
			if (!this.inProccess) {
				this.loginUser(interval);
			}
		}.bind(this), 5000);
	};

	this.cancelRequest = function () {
		OrderRequestLoginRepository.findOne().then(function (accessData) {
			var NRACESSOUSER = accessData.NRACESSOUSER;
			this.removeAccess(NRACESSOUSER);
			this.showLoading();
			WindowService.openWindow('ORDER_LOGIN_SCREEN');
		}.bind(this));
	};

	this.setDashboardHeader = function (container) {
		OrderRequestLoginRepository.findOne().then(function (userData) {
			container.label = userData.NMUSUARIO;
			templateManager.updateTemplate();
		});
	};

	this.loginUser = function (interval) {
		this.inProccess = true;
		OrderRequestLoginRepository.findOne().then(function(requestData){
			var userData = requestData;
			ConfigIpRepository.findOne().then(function (ipInfo) {
				OrderService.loginUser(requestData.NRACESSOUSER, ipInfo.ipForBack).then(function(loginResult){
					ScreenService.buildUserData(userData.NMUSUARIO, [userData.NMMESA]);
					if (!loginResult[0]){ // se retorna msg, retorna array, senão retorna objeto (por isso o if ta certo)
						if (loginResult.bloqueado) {
							this.showLoading();
							WindowService.openWindow('ORDER_LOGIN_SCREEN');
						} else {
							// se a requisição deu certo, preenche o repositório TableActiveTable
							OperatorController.configureMenu();
							$('.zh-background-loading').removeClass('hideLoading');
							AccountController.updateObservationsInner();
							this.showLoading();
							WindowService.openWindow('ORDER_MENU_SCREEN');
						}
						// para o interval que fica tentando fazer login
						clearInterval(interval);
					}
					this.inProccess = false;
				}.bind(this));
			}.bind(this));
		}.bind(this));
	};

	/*
	this.startCountdown = function (time) {
		var retorno = '';
    	// Se o tempo não for zerado
    	if((time - 1) >= 0){
        	// Pega a parte inteira dos minutos
        	var min = parseInt(time/60);
        	// Calcula os segundos restantes
        	var seg = time%60;
        	// Formata o número menor que dez, ex: 08, 07, ...
    		if(seg <=9){
         		seg = "0"+seg;
    		}
        	// Cria a variável para formatar no estilo hora/cronômetro
     		var printableTime = min + ':' + seg;
        	// diminui o tempo
     		time--;

    		// Quando o contador chegar a zero faz esta ação
     		retorno = printableTime;
    	}
    	return retorno;
	};
	*/

	//Função que cancela um pedido (esvazia o carrinho)
	this.emptyCart = function(){
		ScreenService.confirmMessage(
			'Deseja cancelar o pedido e voltar para a página inicial?',
			'question',
			function(){
				AccountCart.remove(Query.build()).then(function (){
					WindowService.openWindow('ORDER_MENU_SCREEN');
				});
			},
			function(){}
		);
	};

	// Esta função serve para incrementar o dasource do form-without-scroller.html que é aberto para acessos pendentes
	this.prepareInnerWidgetAccess = function (row, innerWidget){
		var params = {
			'IDACESSOUSER' : row.IDACESSOUSER,
			'NMMESA' : row.NMMESA,
			'NMUSUARIO' : row.NMUSUARIO,
			'NRACESSOUSER' : row.NRACESSOUSER,
			'NRMESA' : row.NRMESA
		};

		innerWidget.dataSource.data = params;
	};

	// Esta função serve para incrementar o dasource do form-without-scroller.html que é aberto para chamadas
	this.prepareInnerWidgetCalls = function (row, widget){

		var params = {};
		params.labelMesa = row.labelMesa;
		params.tempo = row.tempo;
		params.mesa = row.mesa;
		params.NRACESSOUSER = row.nracessouser;
		widget.dataSource.data = params;

	};

	//Esta função carrega o valor total dos itens no carro de compras
	this.calculateCartTotal = function(widget){
		AccountController.buildOrderCode().then(function () {
			AccountCart.findAll().then(function (cart) {
				totalProd = 0;
				cart.forEach(function(product){
					totalProd += parseFloat(product.PRITEM) * parseFloat(product.QTDPRODUCT);
				});

				widget.getField('vrTotalPedido').label = UtilitiesService.toCurrency(totalProd);
			});
		});
	};

	//Função que chama o garçom
	this.callWaiter = function (callType){
		this.checkAccess(function () {
			OperatorRepository.findAll().then(function (dataOperator){
				var nracessouser = dataOperator[0].NRACESSOUSER;
				OrderService.callWaiter(nracessouser, callType);
				if (callType === 'F') {
					ScreenService.showMessage('Conta solicitada com sucesso.');
					WindowService.openWindow('ORDER_MENU_SCREEN');
				} else {
					ScreenService.showMessage('Chamada realizada com sucesso.');
					ScreenService.goBack();
				}
			});
		});
	};

	this.prepareListNotifications = function(widget){
		this.updateNotificationsLabel().then(function (allNotifications){
			widget.dataSource.data = allNotifications;
		});
	};


	this.mergeArrays = function (arrayA, arrayB) {
		arrayB.forEach(function (eachElement) {
			arrayA.push(eachElement);
		});
		return arrayA;
	};

	this.answerTable = function (row) {
		OrderService.answerTable(row.nracessouser).then(function(){
			ScreenService.closePopup();
		}.bind(this));
	};

	this.checkTableStatus = function(row, openTableWidget, tablesWidget){
		row.NRMESA = row.mesa;
		row.NRACESSOUSER = row.nracessouser;
		var query = Query.build().where('NRMESA').equals(row.NRMESA);
		TableRepository.findOne(query).then(function(activeTable){
			if (activeTable.IDSTMESAAUX === "D"){
				// If table is available, show open table popup.
				ApplicationContext.TableController.prepareOpening(row, openTableWidget);

			}
			else if (activeTable.IDSTMESAAUX === "S"){
				this.controlUserAccess(row, 'I');
			}
			else {
				// If table is already open, simply allow access.
				this.completeReleaseAccess(row.NRACESSOUSER, tablesWidget);
			}
		}.bind(this));
	};

	this.getNotifications = function () {
		var defer = ZHPromise.defer();
		OrderGetAccessRepository.clearAll().then(function (){
			OrderGetCallRepository.clearAll().then(function (){
				OrderService.getAccess().then(function (accessList) {
					OrderService.getCall().then(function (callsList) {
						this.updateNotificationsLabel(accessList, callsList).then(function (allNotifications) {
							defer.resolve(allNotifications);
						});
					}.bind(this), function () {
						defer.reject();
					});
				}.bind(this), function () {
					defer.reject();
				});
			}.bind(this));
		}.bind(this));
		return defer.promise;
	};

	this.updateNotificationsLabel = function (getAccess, getCall){
		var defer = ZHPromise.defer();
		var joinNotifications = function (getAccess, getCall){
			var allNotifications = [];

			getAccess.forEach(function (eachAccess){
				allNotifications.push(eachAccess);
			});
			getCall.forEach(function (eachCall){
				allNotifications.push(eachCall);
			});

			ScreenService.setNotificationHint('requests', allNotifications.length);
			defer.resolve(allNotifications);
		};

		if (getAccess && getCall){
			joinNotifications(getAccess, getCall);
		}
		else {
			OrderGetAccessRepository.findAll().then(function (getAccess){
				OrderGetCallRepository.findAll().then(
				function (getCall){
					joinNotifications(getAccess, getCall);
				},
				function () {
					defer.reject();
				});
			}, function () {
				defer.reject();
			});
		}

		return defer.promise;
	};

	this.showNotifications = function (){
		var notificationsWidget = templateManager.containers.tables.widgets[1].widgets[0];
		var blockedIpsWidget = templateManager.containers.tables.widgets[1].widgets[1];
		var widgetToShow = templateManager.containers.tables.widgets[1];

		this.getNotifications().then(function (allNotifications) {
			if (allNotifications.length === 0){
				notificationsWidget.dataSource.data = null;
			}
			else {
				this.prepareListNotifications(notificationsWidget);
			}
			this.prepareBlockedIps(blockedIpsWidget);
			ScreenService.openPopup(widgetToShow);
		}.bind(this));
	};

	this.controlInternalWidgetOpening = function (widget, row) {
		var widgets = widget.widgets;
		if (widgets[0].name !== row.widgetName) {
			var aux = widgets[0];
			widgets[0] = widgets[1];
			widgets[1] = aux;
		}
		widgets[0].currentRow = row;
	};

	this.confirmLogout = function(){

		ScreenService.confirmMessage(
			'Realmente deseja sair?',
			'quastion',
			function () {
				WindowService.openWindow('ORDER_LOGIN_SCREEN');
			},
			function (){
				ScreenService.goBack();
			}
		);

	}.bind(this);

	this.prepareDashboard = function(widget){
		OperatorRepository.findAll().then(function (operatorData){
			this.getAccountData(function (accountData) {

				// prepara parâmetros para chamar a getAccountDetails (traz desconto, consumação, serviço, total, couvert e produtos)
				var chave = operatorData[0].chave;
				var modoHabilitado = operatorData[0].modoHabilitado;
				if (modoHabilitado === 'O') {
					modoHabilitado = 'M';
				}
				var nrComanda = accountData[0].NRCOMANDA;
				var nrVendaRest = accountData[0].NRVENDAREST;

				AccountService.getAccountDetails(chave, modoHabilitado, nrComanda, nrVendaRest, 'M', '').then(function (accountDetailsData) {
						total = UtilitiesService.toCurrency(total);
						accountDetailsData.AccountGetAccountDetails[0].labeltotal = total;
						widget.dataSource.data = accountDetailsData.AccountGetAccountDetails;
				}.bind(this));
			}.bind(this));
		}.bind(this));
	};

	this.toggleCart = function(widget){
		widget.showCart = !widget.showCart;
	};

	this.openNewConsumer = function(setIPPopup){
		ConfigIpRepository.findOne().then(function (ipInfo){
			if (ipInfo !== null){
				WindowService.openWindow('NEW_CONSUMER_SCREEN');
			}
			else {
				ScreenService.showMessage("Antes de efetuar seu cadastro, configure o IP do servidor.");
				ScreenService.openPopup(setIPPopup);
			}
		});
	};

	this.newConsumer = function(row, widget){
		var DSEMAILCONS = row.DSEMAILCONS;
		var NMCONSUMIDOR = row.NMCONSUMIDOR;
		var NRCELULARCONS = (row.NRCELULARCONS !== null) ? row.NRCELULARCONS.replace(/\(|\)|-|\s/g, '') : null; // Removes mask formatting.
		var CDSENHACONSMD5 = row.CDSENHACONSMD5;
		var CDIDCONSUMID = (row.CDIDCONSUMID !== null) ? row.CDIDCONSUMID : '';

		if (!widget.isValid()){
			ScreenService.showMessage("Preencha todos os campos obrigatórios.");
		}
		else if (!UtilitiesService.checkEmail(DSEMAILCONS)){
			ScreenService.showMessage("Favor introduzir um endereço de e-mail válido.");
		}
		else if (CDSENHACONSMD5 !== row.passwordCheck){
			ScreenService.showMessage("As duas senhas devem ser iguais. Favor digite-as novamente.");
		}
		else {
			OrderService.newConsumer(NMCONSUMIDOR, DSEMAILCONS, NRCELULARCONS, CDSENHACONSMD5, CDIDCONSUMID).then(function (){
				WindowService.openWindow('ORDER_LOGIN_SCREEN');
			});
		}
	};

}

Configuration(function(ContextRegister){
	ContextRegister.register('OrderController', OrderController);
});



// FILE: js/controllers/TableController.js
function TableController($rootScope, PermissionService, TableService, TableRepository, OperatorService, OperatorRepository, AccountController, AccountService, ParamsAreaRepository, ScreenService, AccountCart, Query, AccountGetAccountItems, ParamsClientRepository, TableActiveTable, TableSelectedTable, templateManager, AccountSavedCarts, UtilitiesService, ParamsCustomerRepository, BillService, ParamsSellerRepository, ParamsParameterRepository, OperatorController, OrderController, DelayedProductsRepository, TimestampRepository, ApplicationContext, WaiterNamedPositionsState, WindowService, AccountGetAccountDetails, SellerControl, PerifericosService) {

	var fieldCopy;
	var enterTable;
	var self = this;

	this.checkTable = function (table) {
		if (table.checked === 'selecao') {
			table.checked = '';
		} else {
			table.checked = 'selecao';
		}
	};

	this.selectItem = function (item, widget) {
		if (!item.__isSelected) {
			widget.dataSource.addCheckedRows(item);
		} else {
			widget.dataSource.removeCheckedRows(item);
		}
	};

	this.setMaxPosition = function (positionsField, maxPosition) {
		positionsField.dataSource.data[0].NRPOSICAOMESA = maxPosition;
	};

	this.buildPositionsObject = function (positionsField) {
		var positionsObject = [];
		var NRPOSICAOMESA = positionsField.dataSource.data[0].NRPOSICAOMESA;
		var clientMapping = positionsField.dataSource.data[0].clientMapping;
		var consumerMapping = positionsField.dataSource.data[0].consumerMapping;
		var positionNamedMapping = positionsField.dataSource.data[0].positionNamedMapping;
		var currentPosition;
		for (var idx = 0; idx < NRPOSICAOMESA; idx++) {
			currentPosition = idx + 1;
			positionsObject.push({
				'NRLUGARMESA': currentPosition,
				'CDCLIENTE': _.get(clientMapping[currentPosition], 'CDCLIENTE', null),
				'CDCONSUMIDOR': _.get(consumerMapping[currentPosition], 'CDCONSUMIDOR', null),
				'DSCONSUMIDOR': _.get(positionNamedMapping[currentPosition], 'DSCONSUMIDOR', null)
			});
		}
		return positionsObject;
	};

	this.buildClientMapping = function (positionsObject) {
		var clientMapping = {};
		_.each(positionsObject, function (currentPosition) {
			if (_.get(currentPosition, 'CDCLIENTE')) {
				clientMapping[parseInt(currentPosition.NRLUGARMESA)] = {
					'CDCLIENTE': currentPosition.CDCLIENTE,
					'NMRAZSOCCLIE': currentPosition.NMRAZSOCCLIE
				};
			}
		});
		return clientMapping;
	};

	this.buildConsumerMapping = function (positionsObject) {
		var consumerMapping = {};
		_.each(positionsObject, function (currentPosition) {
			if (_.get(currentPosition, 'CDCONSUMIDOR')) {
				consumerMapping[parseInt(currentPosition.NRLUGARMESA)] = {
					'CDCONSUMIDOR': currentPosition.CDCONSUMIDOR,
					'NMCONSUMIDOR': currentPosition.NMCONSUMIDOR
				};
			}
		});
		return consumerMapping;
	};

	this.buildPositionNamedMapping = function (positionsObject) {
		var positionNamedMapping = {};
		_.each(positionsObject, function (currentPosition) {
			if (_.get(currentPosition, 'DSCONSUMIDOR')) {
				positionNamedMapping[parseInt(currentPosition.NRLUGARMESA)] = {
					'DSCONSUMIDOR': currentPosition.DSCONSUMIDOR
				};
			}
		});
		return positionNamedMapping;
	};

	this.open = function (row, tablesWidget, callBack, positionsField) {
		if (row.NRPOSICAOMESA > 0) {
			OperatorRepository.findAll().then(function (operatorData) {
				var chave = operatorData[0].chave;
				var radioFieldValue = positionsField.widget.getField('radioTablePositions').value();
				var positionsObject = [];
				if (radioFieldValue === 'P') {
					positionsObject = self.buildPositionsObject(positionsField);
					row.CDCLIENTE = null;
					row.CDCONSUMIDOR = null;
				}
				if (operatorData[0].IDUTLSENHAOPER == 'C' && !_.isEmpty(row.CDVENDEDOR) && operatorData[0].IDCAIXAEXCLUSIVO === 'N') SellerControl.save(row.CDVENDEDOR);
				TableService.open(chave, row.NRMESA, row.NRPOSICAOMESA, row.CDCLIENTE, row.CDCONSUMIDOR, row.CDVENDEDOR, positionsObject).then(function (openData) {
					ScreenService.closePopup();
					if (row.NRACESSOUSER) {
						// Libera o acesso pendente (Order).
						OrderController.completeReleaseAccess(row.NRACESSOUSER, tablesWidget);
					} else {
						if (!openData.ERROR) {
							this.validateOpening(row.NRMESA, 'O', 'M', function () {
								if (!callBack) {
									WindowService.openWindow('MENU_SCREEN');
								} else {
									callBack();
								}
							});
						} else {
							ScreenService.closePopup(true);
							if (tablesWidget) {
								this.refreshTables(tablesWidget);
							}
						}
					}
				}.bind(this));
			}.bind(this));
		} else {
			ScreenService.showMessage('Quantidade de pessoas inválida.');
		}
	};

	this.cancelOpen = function (menuContainer) {
		ScreenService.confirmMessage(
			'Deseja cancelar a abertura da mesa?',
			'question',
			function () {
				OperatorRepository.findAll().then(function (operatorData) {
					var chave = operatorData[0].chave;
					TableActiveTable.findAll().then(function (tableData) {
						TableService.cancelOpen(chave, tableData[0].NRMESA).then(function () {
							UtilitiesService.backMainScreen();
						});
					}.bind(this));
				}.bind(this));
			}.bind(this),
			function () {
				menuContainer.restoreDefaultMode();
			}
		);
	};

	this.handleTransferWidget = function (widget) {
		OperatorRepository.findOne().then(function (data) {
			var productField = widget.getField('product');
			if (productField) {
				self.prepareTransferList(productField);
			}
			if (widget.getField('positions')) {
				if (data.IDLUGARMESA === 'S') {
					self.positionsTransferControl(widget.getField('positions'));
					widget.getField('lblPos').isVisible = true;
					widget.getField('positions').isVisible = true;
				} else {
					widget.getField('lblPos').isVisible = false;
					widget.getField('positions').isVisible = false;
				}
			}
			if (widget.getField('NRPOSICAOMESA')) {
				widget.getField('NRPOSICAOMESA').isVisible = false;
			}
			if (widget.getField('btnTableListProduto')) {
				widget.getField('btnTableListProduto').label = widget.getField('btnTableListProduto')._label;
			}
		});
	};

	this.positionsTransferControl = function (target) {
		TableSelectedTable.clearAll().then(function () {
			if (target.dataSource && target.dataSource.data && target.dataSource.data[0]) {
				target.dataSource.data[0].NRPOSICAOMESA = "0";
			}
		});
	};

	this.refreshTables = function (tablesWidget) {
		OperatorRepository.findAll().then(function (data) {
			var chave = data[0].chave;
			tablesWidget.getAction('quickProductRelease').isVisible = data[0].NRATRAPADRAO > 0;
			AccountCart.remove(Query.build()).then(function () {
				TableService.getTables(chave).then(function (requestData) {
					OrderController.updateNotificationsLabel(requestData.OrderGetAccessRepository, requestData.OrderGetCallRepository).then(function (allNotifications) {
						tablesWidget.fields[1].dataSource.data = requestData.TableRepository;
						tablesWidget.reload();
						tablesWidget.activate();
					});
				});
			});
		});
	};

	var getMillissecondsTime = function (qtMinutes) {
		return qtMinutes * 60 * 1000;
	};

	this.prepareAreas = function (select, filter, refreshTablesData) {
		ParamsAreaRepository.findAll().then(function (data) {
			TableActiveTable.findAll().then(function (active) {
				var myArea = [];
				if (active.length > 0) {
					myArea = data.filter(function (i) {
						return i.CDSALA === active[0].CDSALA;
					})[0];
				} else {
					myArea = data[0];
				}

				select.dataSource.data = data;
				select.setCurrentRow(myArea);
				filter.dataSource.data = data;
				filter.currentRow = myArea;

				refreshTablesData.activate();
				this.refreshTables(refreshTablesData);
			}.bind(this));
		}.bind(this));
	};

	this.changeArea = function (areaWidget, currentRow) {
		areaWidget.setCurrentRow(currentRow);
		ScreenService.closePopup();
	};

	this.validateOpening = function (nrMesa, status, modo, callBack) {
		OperatorRepository.findAll().then(function (data) {
			var chave = data[0].chave;
			TableService.validateOpening(chave, nrMesa, status, modo).then(function (comeBack) {
				if (callBack) callBack(comeBack[0]);
			});
		});
	};

	var last = 0;

	this.openTable = Util.buildDebounceMethod(function (row, widgetToShow, clearCart) {
		AccountController.buildOrderCode().then(function () {
			var _openTable = (function () {
				var nrMesa = row.NRMESA;
				var status = '';
				OperatorRepository.findAll().then(function (operatorData) {
					var chave = operatorData[0].chave;
					var query = Query.build()
						.where('NRMESA').equals(nrMesa);
					widgetToShow.getField('NRPOSICAOMESA').maxValue = operatorData[0].NRMAXPESMES;
					TableRepository.find(query).then(function (data) {
						var status = data[0].IDSTMESAAUX;
						var modo = 'M'; // "M" de mesa

						if (status !== 'D') { // diferente de disponível
							this.validateOpening(nrMesa, status, modo, function (back) {
								if ((back.IDSTMESAAUX === 'D') || (back.STATUS === 'DISPONIVEL')) {
									this.prepareOpening(row, widgetToShow);
								} else if (back.STATUS === 'SOLICITADA') {
									if (data[0].IDCOLETOR !== 'C') {
										this.showRequestedTableDialog(chave);
									} else {
										ScreenService.confirmMessage(
											'A conta já foi solicitada. Deseja reabrir a mesa?',
											'question',
											function () {
												PermissionService.checkAccess('liberarMesa').then(function () {
													this.handleFilterParameters();
												}.bind(this));
											}.bind(this),
											function () { }
										);
									}
								} else if (back.STATUS === 'RECEBIMENTO') {
									if (data[0].IDCOLETOR !== 'C') {
										if (back.POSITIONCONTROL) {
											self.openAccountPayment();
										}
										else {
											ScreenService.showMessage('Todas as posições da mesa já estão sendo recebidas.');
										}
									}
									else {
										ScreenService.showMessage('Mesa está em recebimento.');
									}
								} else if (back.STATUS === 'PAGA') {
									ScreenService.showMessage('Mesa paga.');
								} else {
									self.handleFilterParameters();
								}
							}.bind(this));
						} else if (status === 'D') {
							this.prepareOpening(row, widgetToShow);
						}
					}.bind(this));
				}.bind(this));
			}).bind(this);
			if (clearCart) {
				AccountCart.remove(Query.build()).then(function () {
					_openTable();
				});
			} else {
				_openTable();
			}
			AccountCart.remove(Query.build()).then(function () { }.bind(this));
		}.bind(this));
	}, 200, true);

	this.handleFilterParameters = function () {
		OperatorRepository.findAll().then(function (data) {
			TableActiveTable.findAll().then(function (activeTable) {
				if (data[0].IDUTLSENHAOPER == 'C' && data[0].IDCAIXAEXCLUSIVO === 'N') {
					ScreenService.openPopup(templateManager.container.getWidget('setCurrentWaiterPopUp'));
				} else if (data[0].IDUTLSENHAOPER == 'S' && data[0].IDCAIXAEXCLUSIVO === 'N') {
					ScreenService.openPopup(templateManager.container.getWidget('setPassWaiterPopUp'));
				} else if (activeTable[0].STATUS === 'SOLICITADA') {
					TableService.reopen(data[0].chave, activeTable[0].NRMESA).then(function () {
						WindowService.openWindow('MENU_SCREEN');
					});
				} else {
					WindowService.openWindow('MENU_SCREEN');
				}
			});
		});
	};

	this.tableClick = function (row, widgetToShow) {
		var time = new Date();
		enterTable = true;
		this.openTable(row, widgetToShow, true);
	};

	this.validateWaiter = function (widget) {
		var field = widget.getField('currentWaiterField');
		if (!_.isEmpty(field.value())) {
			SellerControl.save(widget.currentRow.CDVENDEDOR);
			self.handleOpenTable(field);
		} else {
			ScreenService.showMessage('Nenhum vendedor selecionado.');
		}
	};

	this.validatePassword = function (widget) {
		field = widget.getField('passwordField');
		if (!_.isEmpty(field.value())) {
			AccountService.validatePassword(field.value()).then(function (result) {
				if (!_.isEmpty(result)) {
					self.handleOpenTable(field);
				}
			});
		} else {
			ScreenService.showMessage('Informe a senha.');
		}
	};

	this.handleOpenTable = function (field) {
		TableActiveTable.findAll().then(function (activeTable) {
			if (activeTable[0].STATUS === 'SOLICITADA') {
				OperatorRepository.findAll().then(function (operatorData) {
					TableService.reopen(operatorData[0].chave, activeTable[0].NRMESA).then(function () {
						field.value('');
						WindowService.openWindow('MENU_SCREEN');
					});
				});
			} else {
				field.value('');
				WindowService.openWindow('MENU_SCREEN');
			}
		}.bind(this));
	};

	this.clearAndClose = function (field) {
		field.value('');
		ScreenService.closePopup();
	};

	this.prepareOpening = function (row, widgetToShow) {
		if (widgetToShow.dataSource.data && widgetToShow.dataSource.data.length > 0) {
			delete widgetToShow.dataSource.data;
		}
		widgetToShow.newRow();
		widgetToShow.container.restoreDefaultMode();
		widgetToShow.moveToFirst();

		var data = {
			NRMESA: row.NRMESA,
			NRPESMESAVEN: 2,
			NRPOSICAOMESA: 2,
			__createdLocal: true,
			btnOpenTable: null,
			lblNMMESA: null,
			lblNRPESMESAVEN: null,
			CDCLIENTE: null,
			CDCONSUMIDOR: null,
			CDVENDEDOR: null,
			NMRAZSOCCLIE: null,
			NRACESSOUSER: row.NRACESSOUSER
		};

		widgetToShow.setCurrentRow(data);
		widgetToShow.label = 'Abrir Mesa - ' + row.NMMESA;

		ScreenService.openPopup(widgetToShow);
	};

	this.scanConsumerQrCode = function (widget) {
		if (_.isEmpty(widget.currentRow.CDCLIENTE)) widget.currentRow.CDCLIENTE = null;

		self.callQRScanner().then(function (qrCode) {
			if (!qrCode.error) {
				qrCode = qrCode.contents;

				if (_.isEmpty(qrCode)) {
					ScreenService.showMessage("Não foi possível obter os dados do leitor.");
				}
				else {
					widget.currentRow.NMCONSUMIDOR = "";
					widget.currentRow.CDCLIENTE = "";
					widget.currentRow.CDCONSUMIDOR = "";
					widget.getField('NMCONSUMIDOR').clearValue();
					widget.getField('NMRAZSOCCLIE').clearValue();

					OperatorRepository.findOne().then(function (operatorData) {
						AccountService.searchConsumer(operatorData.chave, widget.currentRow.CDCLIENTE, qrCode).then(function (consumerData) {
							if (_.isEmpty(consumerData)) {
								ScreenService.showMessage("Não foi encontrado nenhum consumidor com este código.");
							} else {
								var clientField = widget.getField('NMRAZSOCCLIE');
								var consumerField = widget.getField('NMCONSUMIDOR');
								clientField.readOnly = false;
								consumerField.readOnly = false;
								consumerField.dataSourceFilter[0].value = consumerData[0].CDCLIENTE;
								widget.currentRow.CDCLIENTE = consumerData[0].CDCLIENTE;
								widget.currentRow.NMRAZSOCCLIE = consumerData[0].NMRAZSOCCLIE;
								widget.currentRow.CDCONSUMIDOR = consumerData[0].CDCONSUMIDOR;
								widget.currentRow.NMCONSUMIDOR = consumerData[0].NMCONSUMIDOR;
								this.updatePositionLabel(consumerField);
							}
						}.bind(this));
					}.bind(this));
				}
			} else {
				ScreenService.showMessage(qrCode.message, 'alert');
			}
		}.bind(this));
	};

	this.callQRScanner = function () {
		return new Promise(function (resolve) {
			if (!!window.ZhCodeScan) {
				window.scanCodeResult = _.bind(self.qrCodeResult, self, resolve);
				ZhCodeScan.scanCode();
			} else if (!!window.cordova) {
				cordova.plugins.barcodeScanner.scan(
					function (result) {
						result.error = false;
						result.contents = result.text;
						resolve(result);
					},
					function (error) {
						var result = {};
						result.error = true;
						result.message = error;
						resolve(result);
					}
				);
			} else {
				resolve({
					'error': true,
					'message': 'Não foi possível chamar a integração. Sua instância não existe.'
				});
			}
		}.bind(this));
	};

	this.qrCodeResult = function (resolve, result) {
		resolve(JSON.parse(result));
	};

	this.prepareClients = function (clientWidget) {
		ParamsClientRepository.findAll().then(function (data) {
			clientWidget.dataSource.data = data;
			ScreenService.openPopup(clientWidget);
		});
	};

	this.prepareCustomers = function (customersSelect, CDCLIENTE, mustUpdateField, positionsField) {
		customersSelect.clearValue();
		customersSelect.widget.currentRow.CDCONSUMIDOR = "";
		ParamsCustomerRepository.clearAll().then(function () {
			CDCLIENTE = !_.isEmpty(CDCLIENTE) ? CDCLIENTE : "";
			customersSelect.dataSourceFilter[0].value = CDCLIENTE;
			if (CDCLIENTE) {
				customersSelect.reload();
				if (mustUpdateField) {
					updateConsumerField(positionsField, customersSelect);
				}
			}
			this.updatePositionLabel(customersSelect);
		}.bind(this));
	};

	this.sendMessage = function (row) {
		var mensagens = "";
		mensagens = row.DSOCORR || [];
		var impressoras = row.NRSEQIMPRLOJA || [];

		var mensagem = "";
		if (row.mensagem !== "") mensagem += row.mensagem + "; ";
		mensagem += mensagens.join("; ");

		if (mensagem === "")
			ScreenService.showMessage("Selecione uma mensagem.");
		else if (impressoras.length === 0)
			ScreenService.showMessage("Selecione uma impressora.");
		else {
			OperatorRepository.findAll().then(function (data) {
				var chave = data[0].chave;
				AccountController.getAccountData(function (accountData) {
					TableService.sendMessage(chave, accountData[0].NRCOMANDA, accountData[0].NRVENDAREST, impressoras, mensagem, row.TXMOTIVCANCE, data[0].modoHabilitado).then(function (result) {
						result = result[0];
						if (_.get(result, '[0].saas')) {
							PerifericosService.print(result);
							UtilitiesService.backMainScreen();
						} else {
							UtilitiesService.backMainScreen();
						}
					});
				});
			}.bind(this));
		}
	};

	this.sendWaiterlessMessage = function (row) {
		var mensagens = "";
		mensagens = row.DSOCORR || [];
		var impressoras = row.NRSEQIMPRLOJA || [];
		mensagens.push(row.mensagem);

		var mensagem = mensagens.join('; ');

		if (mensagem === "")
			ScreenService.showMessage("Selecione uma mensagem.");
		else if (impressoras.length === 0)
			ScreenService.showMessage("Selecione uma impressora.");
		else {
			OperatorRepository.findAll().then(function (data) {
				var chave = data[0].chave;
				TableService.sendMessage(chave, data[0].NMFANVEN, "waiterless", impressoras, mensagem, row.TXMOTIVCANCE, data[0].modoHabilitado).then(function (response) {
					response = response[0];
					if (_.get(response, '[0].saas')) {
						PerifericosService.print(response[0]).then(function (result) {
							UtilitiesService.backMainScreen();
						});
					} else {
						UtilitiesService.backMainScreen();
					}
				});
			}.bind(this));
		}
	};

	this.quickProductRelease = function (event, row, tablesWidget) {
		event.stopPropagation();
		OperatorRepository.findOne().then(function (operatorData) {
			this.validateOpening(row.NRMESA, row.IDSTMESAAUX, 'M', function (back) {
				TableService.getDelayedProducts(operatorData.chave, back.NRVENDAREST, back.NRCOMANDA).then(function (delayedProducts) {
					if (delayedProducts.length > 0) {
						WindowService.openWindow('DELAYED_PRODUCTS_SCREEN');
					} else {
						ScreenService.showMessage('Não existem pedidos para liberar nesta mesa.');
					}
				});
			}.bind(this));
		}.bind(this));
	};

	this.quickPrintAccount = function (event, row, tablesWidget) {
		event.stopPropagation();
		OperatorRepository.findOne().then(function (operatorData) {
			this.validateOpening(row.NRMESA, row.IDSTMESAAUX, 'M', function (back) {
				if (back.IDSTMESAAUX === 'D' || back.STATUS === 'DISPONIVEL') {
					ScreenService.showMessage("Mesa ainda não foi aberta.");
				} else if (back.STATUS === 'SOLICITADA' || back.STATUS === 'RECEBIMENTO') {
					ScreenService.showMessage("A conta já foi solicitada.");
				} else {
					AccountService.getAccountDetails(operatorData.chave, "M", row.NRCOMANDA, row.NRVENDAREST, 'I', "").then(function (backData) {
						this.refreshTables(tablesWidget);
						AccountController.handlePrintBill(backData.dadosImpressao);
						//backend already send it
						//ScreenService.successNotification("Parcial da conta impressa com sucesso.");
					}.bind(this));
				}
			}.bind(this));
		}.bind(this));
	};

	this.quickCloseAccount = function (event, row, tablesWidget) {
		event.stopPropagation();
		OperatorRepository.findOne().then(function (operatorData) {
			this.validateOpening(row.NRMESA, row.IDSTMESAAUX, 'M', function (back) {
				if (back.IDSTMESAAUX === 'D' || back.STATUS === 'DISPONIVEL') {
					ScreenService.showMessage("Mesa ainda não foi aberta.");
				} else if (back.STATUS === 'SOLICITADA' || back.STATUS === 'RECEBIMENTO') {
					ScreenService.showMessage("A conta já foi solicitada.");
				} else {
					AccountService.getAccountDetails(operatorData.chave, 'M', row.NRCOMANDA, row.NRVENDAREST, 'M', '').then(function (accountDetails) {
						if (accountDetails.AccountGetAccountDetails[0].vlrtotal === 0) {
							ScreenService.confirmMessage(
								'Não foi realizado nenhum pedido para esta mesa, deseja cancelar a abertura?',
								'question',
								function () {
									TableService.cancelOpen(operatorData.chave, row.NRMESA).then(function () {
										this.refreshTables(tablesWidget);
									}.bind(this));
								}.bind(this),
								function () { }
							);
						} else {
							TableService.closeAccount(operatorData.chave, row.NRCOMANDA, row.NRVENDAREST, 'M', true, true, true, 0, accountDetails.AccountGetAccountDetails[0].NRPESMESAVEN, null, null, 'I', null).then(function (closeAccountReturn) {
								this.refreshTables(tablesWidget);
								AccountController.handlePrintBill(closeAccountReturn.dadosImpressao);
								//backend already send it
								//ScreenService.successNotification("Mesa fechada com sucesso.");
							}.bind(this));
						}
					}.bind(this));
				}
			}.bind(this));
		}.bind(this));
	};

	this.closeAccount = function (widget, txporcentservico) {
		OperatorRepository.findOne().then(function (data) {
			AccountController.getAccountData(function (accountData) {
				accountData = accountData[0];
				var total = widget.getField('total').value();

				total = UtilitiesService.removeCurrency(total);
				if (total === 0 && (data.modoHabilitado === 'M' || data.modoHabilitado === 'C')) {
					var mode = data.modoHabilitado === 'M' ? 'mesa' : 'comanda';
					ScreenService.confirmMessage(
						'Não foi realizado nenhum pedido para esta ' + mode + ', deseja cancelar a abertura da ' + mode + '?',
						'question',
						function () {
							if (data.modoHabilitado === 'M') {
								TableService.cancelOpen(data.chave, accountData.NRMESA).then(function () {
									UtilitiesService.backMainScreen();
								});
							} else {
								BillService.cancelOpen(data.chave, accountData.NRMESA, accountData.NRVENDAREST, accountData.NRCOMANDA).then(function () {
									UtilitiesService.backMainScreen();
								});
							}
						},
						function () { }
					);
				} else {
					var modo, consumacao, servico, couvert, imprimeParcial;
					if (data.modoHabilitado === 'O') {
						//Mesmo sendo modo order, o parametro passado será 'M'
						modo = 'M';
						consumacao = true;
						servico = true;
						couvert = true;
					} else {
						modo = data.modoHabilitado;
						consumacao = widget.getField('swiconsumacao').value();
						servico = widget.getField('swiservico').value();
						couvert = widget.getField('swicouvert').value();
						imprimeParcial = data.modoHabilitado === 'M' ? 'I' : null;
					}

					TableService.closeAccount(data.chave, accountData.NRCOMANDA, accountData.NRVENDAREST, modo, consumacao, servico,
						couvert, 0, accountData.NRPESMESAVEN, widget.currentRow.CDSUPERVISOR, accountData.NRMESA, imprimeParcial, txporcentservico).then(function (response) {
							if (response.nothing[0].nothing === 'nothing') {
								if (data.modoHabilitado === 'M') {
									if (_.get(response, 'paramsImpressora.saas')) {
										PerifericosService.print(response.paramsImpressora).then(function () {
											self.receivePayment(data.chave, accountData.NRCOMANDA, accountData.NRVENDAREST, data.IDCOLETOR);
											AccountController.handlePrintBill(response.dadosImpressao);
										});
									} else {
										this.receivePayment(data.chave, accountData.NRCOMANDA, accountData.NRVENDAREST, data.IDCOLETOR);
										AccountController.handlePrintBill(response.dadosImpressao);
									}
								} else {
									AccountController.prepareAccountDetails(widget, function () {
										AccountController.openPayment(true);
									});
								}
							} else {
								UtilitiesService.backMainScreen();
							}
						}.bind(this));
				}
			}.bind(this));
		}.bind(this));
	};

	this.receivePayment = function (chave, NRCOMANDA, NRVENDAREST, IDCOLETOR) {
		if (IDCOLETOR !== 'C') {
			ScreenService.confirmMessage(
				'Deseja ir para tela de pagamento?',
				'question',
				function () {
					TableService.changeTableStatus(chave, NRVENDAREST, NRCOMANDA, 'R').then(function (response) {
						this.openAccountPayment();
					}.bind(this));
				}.bind(this),
				function () {
					UtilitiesService.backMainScreen();
				}.bind(this)
			);
		} else
			UtilitiesService.backMainScreen();
	};

	this.openAccountPayment = function () {
		TableActiveTable.findOne().then(function (activeTable) {
			TableService.positionControl(activeTable.NRVENDAREST, null, null, null).then(function (result) {
				WindowService.openWindow('PAYMENT_SCREEN').then(function () {
					var accountPaymentWidget = templateManager.container.getWidget('accountDetails');
					// Métodos onEnter da AccountPaymentNamed.json.
					AccountController.handlePositionsFieldInit(accountPaymentWidget);
					AccountController.prepareAccountClosingWidget(accountPaymentWidget, null, null, null);
					if (result[0].message == null) {
						AccountController.showPositionActions(accountPaymentWidget.container.getWidget('accountDetails').getField('positionsField'));
						AccountController.refreshAccountDetails(accountPaymentWidget.widgets, '');
					}
					else {
						AccountController.hidePositionActions(accountPaymentWidget.container.getWidget('accountDetails').getField('positionsField'));
					}
				}.bind(this));
			}.bind(this));
		}.bind(this));
	};

	this.getMessageHistory = function (widget) {
		if (widget.dataSource.data && widget.dataSource.data.length > 0) {
			delete widget.dataSource.data;
		}

		OperatorRepository.findAll().then(function (data) {
			var chave = data[0].chave;
			AccountController.getAccountData(function (accountData) {
				TableService.getMessageHistory(chave, accountData[0].NRCOMANDA, accountData[0].NRVENDAREST).then(function (data) {
					var placeHolder = '';
					if (!(data[0].TXMOTIVCANCE)) {
						placeHolder = 'nenhuma mensagem';
					}
					var items = [{
						mensagem: '',
						TXMOTIVCANCE: data[0].TXMOTIVCANCE,
						TXMOTIVCANCENADA: placeHolder,
						NMIMPRLOJA: [],
						DSOCORR: []
					}];

					widget.dataSource.data = items;
					widget.setCurrentRow(items[0]);
				});
			}.bind(this));
		}.bind(this));
	};

	this.prepareWaiterlessData = function (widget) {
		widget.setCurrentRow({});
		/*
		if(widget.dataSource.data && widget.dataSource.data.length > 0) {
			delete widget.dataSource.data;
		}

		OperatorRepository.findAll().then(function (data) {
			var chave = data[0].chave;
			var items = [];
			items.push({
				mensagem: '',
				TXMOTIVCANCENADA: data[0].TXMOTIVCANCE,
				TXMOTIVCANCENADA: '',
				NMIMPRLOJA: [],
				DSOCORR: []
			});

			widget.dataSource.data = items;
			widget.setCurrentRow(items[0]);
		}.bind(this));
		*/
	};

	this.showCancelProduct = function (CDSUPERVISOR) {
		AccountController.getAccountData(function (accountData) {
			OperatorRepository.findAll().then(function (params) {
				AccountService.getAccountItems(params[0].chave, params[0].modoHabilitado, accountData[0].NRCOMANDA, accountData[0].NRVENDAREST, '').then(function (dataReturn) {
					if (params[0].modoHabilitado === 'C') {
						WindowService.openWindow('CANCEL_PRODUCT_SCREEN').then(function () {
							templateManager.container.getWidget('cancelProductComanda')._supervisor = CDSUPERVISOR;
						}.bind(this));
					}
					else {
						WindowService.openWindow('CANCEL_PRODUCT_SCREEN2').then(function () {
							templateManager.container.getWidget('cancelProductMesa')._supervisor = CDSUPERVISOR;
						}.bind(this));
					}
				}.bind(this));
			}.bind(this));
		}.bind(this));
	};

	/* ******************** */
	/* GROUPING & SPLITTING */
	/* ******************** */

	this.prepareGrouping = function (selectedWidget) {
		selectedWidget.activate();
		templateManager.updateTemplate();

		if (selectedWidget.dataSource.data && selectedWidget.dataSource.data.length > 0) {
			delete selectedWidget.dataSource.data;
		}

		TableActiveTable.findAll().then(function (activeTable) {
			var query = Query.build()
				.where('CDSALA').equals(activeTable[0].CDSALA)
				.where('IDSTMESAAUX').equals('O')
				.where('agrupada').equals('N')
				.where('NRMESA').notEquals(activeTable[0].NRMESA);

			TableRepository.find(query).then(function (result) {
				selectedWidget.dataSource.data = result;
			});
		});
	};

	this.prepareSplitting = function (selectedWidget) {
		templateManager.updateTemplate();

		if (selectedWidget.dataSource.data && selectedWidget.dataSource.data.length > 0) {
			delete selectedWidget.dataSource.data;
		}

		TableActiveTable.findAll().then(function (activeTable) {
			// find the active table in the table repository
			var query = Query.build()
				.where('NRMESA').equals(activeTable[0].NRMESA);
			TableRepository.find(query).then(function (activeTableInRepo) {

				// find all the tables that are grouped with the active table
				TableRepository.findAll().then(function (tables) {
					// get the tables in the table repository
					tablesToShow = tables.filter(function (currentTable) {
						for (var groupedTable in activeTableInRepo[0].mesasAgrupadas) {
							if (currentTable.NRMESA === activeTableInRepo[0].mesasAgrupadas[groupedTable]) {
								if (currentTable.NRMESA !== activeTable[0].NRMESA) {
									// and put them in a array
									return currentTable;
								}
							}
						}
					});
				}).then(function () {
					selectedWidget.dataSource.data = tablesToShow;
				});
			});
		});
	};

	this.prepareTableList = function (tablesWidget) {
		OperatorRepository.findAll().then(function (params) {
			TableService.getTables(params[0].chave).then(function (result) {
				result.TableRepository.forEach(function (res) {
					res.mode = 'list';
				});
				tablesWidget.dataSource.data = result.TableRepository;
				ScreenService.openPopup(tablesWidget);
			});
		});
	};

	this.selectTable = function (table, positionsField, abreComanda) {
		/* Updates the positions widget with the number of positions on the selected table. */
		OperatorRepository.findAll().then(function (params) {
			if (params[0].modoHabilitado === 'M') {
				positionsField.reload().then(function (data) {
					var fieldMaxPosicoes = positionsField.widget.getField('NRPOSICAOMESA');
					positionsFieldData = positionsField.dataSource.data[0];
					if (parseInt(table.NRPOSICAOMESA) > 0) {
						if (fieldMaxPosicoes) {
							fieldMaxPosicoes.isVisible = false;
						}
						positionsFieldData.NRPOSICAOMESA = table.NRPOSICAOMESA;
					} else {
						if (params[0].IDLUGARMESA === 'S') {
							if (fieldMaxPosicoes) {
								fieldMaxPosicoes.isVisible = true;
								fieldMaxPosicoes.applyDefaultValue();
								positionsFieldData.NRPOSICAOMESA = fieldMaxPosicoes.value();
							} else {
								positionsFieldData.NRPOSICAOMESA = "2";
							}
						} else {
							fieldMaxPosicoes.isVisible = false;
							fieldMaxPosicoes.setValue("1");
							positionsFieldData.NRPOSICAOMESA = positionsFieldData.NRPESMESAVEN = "1";
						}
					}
					positionsField.position = null;

					TableSelectedTable.clearAll().then(function () {
						TableSelectedTable.save(table).then(function () {
							var container = positionsField.widget.container;

							var productWidget = container.getWidget('product');
							if (productWidget.getField('btnTableListProduto')) {
								productWidget.getField('btnTableListProduto').label = table.NMMESA;
							}

							var tableWidget = container.getWidget('table');
							if (tableWidget.getField('btnTableListMesa')) {
								tableWidget.getField('btnTableListMesa').label = table.NMMESA;
							}

							ScreenService.closePopup();
							positionsField.forceReload = true;
							templateManager.updateTemplate();
						});
					});
				});
			} else {
				TableSelectedTable.clearAll().then(function () {
					TableSelectedTable.save(table).then(function () {
						abreComanda.getField('btnTableList').label = table.NMMESA;
						ScreenService.closePopup();
					});
				});
			}
		});
	};

	this.groupTables = function (widget) {
		list = widget.dataSource.data.filter(function (array) {
			return array.checked === 'selecao';
		});

		if (list.length !== 0) {

			var listaMesas = [];
			for (var i in list) {
				listaMesas.push(list[i].NRMESA);
			}

			OperatorRepository.findAll().then(function (data) {
				var chave = data[0].chave;
				TableActiveTable.findAll().then(function (mesa) {
					TableService.groupTables(chave, mesa[0].NRMESA, listaMesas).then(function () {
						UtilitiesService.backMainScreen();
					});
				}.bind(this));
			}.bind(this));

		} else {
			ScreenService.showMessage('Nenhuma mesa foi selecionada.');
		}
	};

	this.splitTables = function (widget) {
		/* Gets the selected tables from the list. */
		list = widget.dataSource.data.filter(function (array) {
			return array.checked === 'selecao';
		});

		if (list.length !== 0) {
			/* Inserts the selected table into an array. */
			var listaMesas = [];
			for (var i in list)
				listaMesas.push(list[i].NRMESA);

			OperatorRepository.findAll().then(function (data) {
				var chave = data[0].chave;
				AccountController.getAccountData(function (accountData) {
					/* Splits the selected tables from the currently active table. */
					TableService.splitTables(chave, accountData[0].NRCOMANDA, accountData[0].NRVENDAREST, listaMesas).then(function () {
						UtilitiesService.backMainScreen();
					});
				}.bind(this));
			}.bind(this));
		} else {
			ScreenService.showMessage('Nenhuma mesa foi selecionada.');
		}
	};


	/* ********** */
	/* TRANSFERS */
	/* ********* */

	this.showTransfers = function (CDSUPERVISOR) {
		AccountController.getAccountData(function (accountData) {
			OperatorRepository.findAll().then(function (params) {
				AccountService.getAccountItems(params[0].chave, params[0].modoHabilitado, accountData[0].NRCOMANDA, accountData[0].NRVENDAREST, '').then(function (dataReturn) {
					WindowService.openWindow('TRANSFERS_SCREEN').then(function () {
						templateManager.container.getWidget('product')._supervisor = CDSUPERVISOR;
						templateManager.container.getWidget('table')._supervisor = CDSUPERVISOR;
					});
				});
			});
		});
	};

	this.prepareTransferList = function (transferList, items) {
		transferList.widget.activate();
		if (items) {
			transferList.dataSource.data = items;
		} else {
			AccountGetAccountItems.findAll().then(function (data) {
				transferList.dataSource.checkedRows = [];
				for (var i in data) {
					if (data[i].quantidade != 1) {
						data[i].DSBUTTON = data[i].quantidade + " x " + data[i].DSBUTTON;
					}
				}
				transferList.dataSource.data = data;
			});
		}
	};

	this.transferItemActionEvent = function (widget) {
		var productField = widget.getField('product');
		var positionsField = widget.getField('positions');
		if (productField) {
			self.transferItem(productField.dataSource.checkedRows, positionsField.position + 1, productField, widget);
		}
	};

	this.transferItem = function (rows, position, listGroupedField, widget) {
		TableSelectedTable.findAll().then(function (mesa) {
			if (mesa.length > 0) {
				if (rows.length > 0) {
					ScreenService.confirmMessage(
						'Deseja transferir o(s) produto(s) selecionado(s) para a ' + mesa[0].NMMESA + '?',
						'question',
						function () {
							OperatorRepository.findAll().then(function (params) {
								/* Stores selected items into an array. */
								var produtos = {};
								rows.forEach(function (row) {
									var produto;
									if (row.CDPRODPROMOCAO === null || row.composicao.length > 0) {
										produto = {
											NRVENDAREST: row.NRVENDAREST,
											NRCOMANDA: row.nrcomanda,
											NRPRODCOMVEN: row.NRPRODCOMVEN,
											quantidade: row.quantidade
										};
										produtos[row.NRPRODCOMVEN] = produto;
									}
									else {
										for (var i in listGroupedField.dataSource.data) {
											if (listGroupedField.dataSource.data[i].NRSEQPRODCOM == row.NRSEQPRODCOM) {
												produto = {
													NRVENDAREST: listGroupedField.dataSource.data[i].NRVENDAREST,
													NRCOMANDA: listGroupedField.dataSource.data[i].nrcomanda,
													NRPRODCOMVEN: listGroupedField.dataSource.data[i].NRPRODCOMVEN,
													quantidade: "1.000"
												};
												produtos[listGroupedField.dataSource.data[i].NRPRODCOMVEN] = produto;
											}
										}
									}
								});
								var transfer = [];
								for (var i in produtos) {
									transfer.push(produtos[i]);
								}
								AccountController.getAccountData(function (accountData) {
									var CDSUPERVISOR = params[0].CDOPERADOR !== widget._supervisor ? widget._supervisor : null;
									var maxPosicoes = widget.currentRow.NRPOSICAOMESA;
									TableService.transferItem(params[0].chave, mesa[0].NRMESA, accountData[0].NRCOMANDA, accountData[0].NRVENDAREST, transfer, position, CDSUPERVISOR, maxPosicoes).then(function () {
										var fieldMaxPosicoes = widget.getField('NRPOSICAOMESA');
										fieldMaxPosicoes.isVisible = false;
										if (accountData[0].NRMESA !== mesa[0].NRMESA) {
											/* Remove the transfered items from the list. */
											transfer.forEach(function (product) {
												listGroupedField.dataSource.data = listGroupedField.dataSource.data.filter(function (item) {
													return item.NRPRODCOMVEN !== product.NRPRODCOMVEN;
												});
											});
											if (listGroupedField.dataSource.data.length === 0) {
												WindowService.openWindow('TABLES_SCREEN');
											}
										} else {
											/* Change the position of the items. */
											rows.forEach(function (row) {
												row.posicao = "posição " + position;
											});
											listGroupedField.dataSource.data = listGroupedField.dataSource.data.filter(function (item) {
												return item !== null;
											});
										}

										//Cleans the selection
										listGroupedField.dataSource.data.map(function (item) {
											item.__isSelected = false;
											return item;
										});
										listGroupedField.dataSource.checkedRows = [];
									});
								}.bind(this));
							}.bind(this));
						}.bind(this),
						function () {
							/* Do nothing. */
						}
					);
				} else {
					ScreenService.showMessage('Nenhum produto foi selecionado.');
				}
			} else {
				ScreenService.showMessage('Mesa não selecionada.');
			}
		}.bind(this));
	};

	this.transferTable = function (widget) {
		TableSelectedTable.findAll().then(function (destinyTable) {
			if (destinyTable.length > 0) {
				ScreenService.confirmMessage(
					'Transferir para a ' + destinyTable[0].NMMESA + '?',
					'question',
					function () {
						OperatorRepository.findAll().then(function (operatorData) {
							var chave = operatorData[0].chave;
							AccountController.getAccountData(function (accountData) {
								if (accountData[0].NRMESA === destinyTable[0].NRMESA) {
									ScreenService.showMessage('Não é possível transferir para a mesa origem.');
								} else {
									var CDSUPERVISOR = operatorData[0].CDOPERADOR !== widget._supervisor ? widget._supervisor : null;
									TableService.transferTable(chave, accountData[0].NRCOMANDA, accountData[0].NRVENDAREST, destinyTable[0].NRMESA, CDSUPERVISOR).then(function (data) {
										UtilitiesService.backMainScreen();
									});
								}
							});
						});
					}.bind(this),
					function () { }
				);
			} else {
				ScreenService.showMessage('Selecione uma mesa.');
			}
		});
	};


	/* ********* */
	/* POSITIONS */
	/* ********* */

	/* Updates the positions widget (top of page). */
	this.preparePositions = function (positions) {
		OperatorRepository.findAll().then(function (params) {
			// está no modo mesa e utiliza rotina de posições
			if (params[0].modoHabilitado === 'M' && params[0].IDLUGARMESA === 'S') {
				positions.isVisible = true;
				TableActiveTable.findAll().then(function (tableData) {
					positions.dataSource.data = tableData;

					if (enterTable) {
						positions.position = 0;
						enterTable = false;
					}
				});
			} else {
				positions.isVisible = false;
			}
		});
	};

	/* Changes the number of people on the table. */
	this.setPositions = function (popupWidget, positionsField, radioTablePositions, menuPositionsWidget) {
		if (popupWidget.currentRow.NRPOSICAOMESA > 0) {
			OperatorRepository.findAll().then(function (data) {
				var chave = data[0].chave;
				/* Gets the currently active table. */
				AccountController.getAccountData(function (accountData) {
					/* Makes changes (back end). */
					TableService.setPositions(chave, accountData[0].NRCOMANDA, accountData[0].NRVENDAREST, popupWidget.currentRow.NRPOSICAOMESA).then(function () {
						// recalculates prices
						AccountController.changeClientConsumer(popupWidget, positionsField, radioTablePositions, false).then(function () {
							self.preparePositions(menuPositionsWidget);
							ScreenService.closePopup();
						});
					}.bind(this));
				}.bind(this));
			}.bind(this));
		} else {
			ScreenService.showMessage('Quantidade de pessoas inválida.');
		}
	};

	/* Shows a widget that allows the user to change the number of people. */
	this.showChangePositions = function (widget) {
		ScreenService.openPopup(widget).then(function () {
			self.blockPopupOnEnterEvent = false;
			self.handleShowPositions(widget, true);
		});
	};

	/* Changes the table associated to the bill - only used in Bill Mode. */
	this.setTheTable = function (NRMESA, container) {
		AccountController.getAccountData(function (accountData) {
			OperatorRepository.findAll().then(function (data) {
				BillService.setTheTable(data[0].chave, NRMESA, accountData[0].NRVENDAREST).then(function (result) {
					var currentLabel = container.label.substr(0, container.label.indexOf('<span')) || container.label;
					container.label = currentLabel + '<span class="waiter-header-right"> Comanda ' + accountData[0].DSCOMANDA + ' - Mesa ' + NRMESA + '</span>';
					ScreenService.closePopup(true);
				});
			});
		});
	};

	/* Delayed Products functions. */
	this.setDelayedProducts = function (widget) {
		OperatorRepository.findOne().then(function (operatorData) {
			DelayedProductsRepository.findAll().then(function (delayedProducts) {
				widget.dataSource.data = delayedProducts;
				widget.selectAll();
				templateManager.updateTemplate();
			});
		});
	};

	this.showPrinters = function (selectedProducts, popup) {
		if (selectedProducts.length > 0) {
			popup.setCurrentRow({});
			ScreenService.openPopup(popup);
		}
		else ScreenService.showMessage('Favor escolher pelo menos 1 produto para liberar.');
	};

	this.releaseMultipleProducts = function (selectedProducts, printer, widget) {
		if (printer && printer[0]) {
			printer = printer[0];
		} else {
			printer = '';
		}
		var selection = [];
		for (var i in selectedProducts) {
			selection.push({
				'CDFILIAL': selectedProducts[i].CDFILIAL,
				'NRPEDIDOFOS': selectedProducts[i].NRPEDIDOFOS,
				'NRITPEDIDOFOS': selectedProducts[i].NRITPEDIDOFOS,
				'NRVENDAREST': selectedProducts[i].NRVENDAREST,
				'NRCOMANDA': selectedProducts[i].NRCOMANDA,
				'NRPRODCOMVEN': selectedProducts[i].NRPRODCOMVEN
			});
		}
		OperatorRepository.findOne().then(function (operatorData) {
			TableService.releaseTheProduct(operatorData.chave, selectedProducts[0].CDFILIAL, selectedProducts[0].NRVENDAREST, selectedProducts[0].NRCOMANDA, selection, printer).then(function (delayedProducts) {
				ScreenService.closePopup();
				if (delayedProducts.length > 0) {
					widget.dataSource.data = delayedProducts;
					templateManager.updateTemplate();
				} else {
					ScreenService.goBack();
				}
			});
		});
	};

	this.selectReleasePrinter = function (widget) {
		if (widget.currentRow.NRSEQIMPRLOJA && !Util.isEmptyOrBlank(widget.currentRow.NRSEQIMPRLOJA)) {
			widget.currentRow.NRSEQIMPRLOJA = Array(widget.currentRow.NRSEQIMPRLOJA.pop());
		}
	};

	this.groupSmartPromo = function (product, widget) {
		for (var i in widget.dataSource.data) {
			if (product.NRSEQPRODCOM !== null && product.NRSEQPRODCOM === widget.dataSource.data[i].NRSEQPRODCOM) {
				if (widget.dataSource.checkedRows.indexOf(widget.dataSource.data[i]) < 0) {
					widget.dataSource.checkedRows.push(widget.dataSource.data[i]);
					widget.dataSource.data[i].__isSelected = true;
					widget.dataSource.updateCheckedRows();
				}
			}
		}
	};

	this.ungroupSmartPromo = function (product, widget) {
		for (var i in widget.dataSource.data) {
			if (product.NRSEQPRODCOM !== null && product.NRSEQPRODCOM === widget.dataSource.data[i].NRSEQPRODCOM) {
				widget.dataSource.checkedRows.splice(widget.dataSource.data.indexOf(widget.dataSource.data[i]), 1);
				widget.dataSource.data[i].__isSelected = false;
				widget.dataSource.updateCheckedRows();
			}
		}
	};

	this.loadProducts = function () {
		var widgetProducts = templateManager.container.getWidget("widgetProducts");
		widgetProducts.dataSource.data = [];
		AccountController.getAccountData(function (accountData) {
			OperatorRepository.findAll().then(function (params) {
				AccountService.getAccountItemsWithoutCombo(params[0].chave, params[0].modoHabilitado, accountData[0].NRCOMANDA, accountData[0].NRVENDAREST, "").then(function (databack) {
					// pageDetails.currentRow = databack.AccountGetAccountDetails[0];

					if (widgetProducts.dataSource.data && widgetProducts.dataSource.data.length > 0) {
						widgetProducts.dataSource.data = [];
					}

					widgetProducts.dataSource.data = databack;
					templateManager.updateTemplate();
				});
			});
		});
	};

	this.loadOriginalProducts = function () {
		var widgetCancel = templateManager.container.getWidget("widgetCancel");
		widgetCancel.dataSource.data = [];
		AccountController.getAccountData(function (accountData) {
			OperatorRepository.findAll().then(function (params) {
				AccountService.getAccountOriginalItems(params[0].chave, params[0].modoHabilitado, accountData[0].NRCOMANDA, accountData[0].NRVENDAREST, "").then(function (databack) {
					// pageDetails.currentRow = databack.AccountGetAccountDetails[0];
					if (databack.length > 0) {
						self.widgetCancelVisibility(true);
						if (widgetCancel.dataSource.data && widgetCancel.dataSource.data.length > 0) {
							widgetCancel.dataSource.data = [];
						}

						widgetCancel.dataSource.data = databack;
						templateManager.updateTemplate();
					} else {

						self.widgetCancelVisibility(false);
					}
				});
			});
		});
	};

	this.widgetCancelVisibility = function (visibility) {

		var widgetCancel = templateManager.container.getWidget("widgetCancel");
		widgetCancel.isVisible = visibility;

	};

    this.splitProductsValidation = function (){

        var widgetProducts = templateManager.container.getWidget("widgetProducts");
        var field = templateManager.container.getWidget("widgetSplit").getField("positionswidget");
        var selectedProducts = templateManager.container.getWidget("widgetProducts").getCheckedRows();

        if (!field.position || field.position.length <= 1 || selectedProducts.length === 0)
            widgetProducts.getAction("dividir").isVisible = false;
        else
            widgetProducts.getAction("dividir").isVisible = true;

        field._isStatusChanged = false;

    };

    this.splitProductPromoIntegrity = function(widget, selectedItem){
        widget.dataSource.data.forEach(function (item){
            if (selectedItem && !_.isEmpty(item.NRSEQPRODCOM)){
                if (item.NRSEQPRODCOM == selectedItem.NRSEQPRODCOM && !_.isEqual(item, selectedItem)){
                    item.__isSelected = !selectedItem.__isSelected;
                }
            }
        });
    };

	this.cancelSplitedProductsValidation = function () {

		var widgetCancel = templateManager.container.getWidget("widgetCancel");
		var selectedProducts = widgetCancel.getCheckedRows();

		if (selectedProducts.length === 0) {
			widgetCancel.getAction("cancelar").isVisible = false;
		} else {
			widgetCancel.getAction("cancelar").isVisible = true;
		}
	};

	this.positionVisibility = function (widget) {
		switch (widget.name) {

			case 'widgetProducts':
				widget.parent.getField("positionswidget").isVisible = true;
				break;

			case 'widgetCancel':
				widget.parent.getField("positionswidget").isVisible = false;
				break;
		}
	};

	this.splitProducts = function (container) {
		var widgetPositions = container.getWidget("widgetSplit");
		var widgetProducts = container.getWidget("widgetProducts");
		var positions = widgetPositions.getField("positionswidget").position;
		var selectedProducts = widgetProducts.getCheckedRows();
		positions = positions.map(function (pos) {
			return ++pos;
		});

		var NRVENDAREST = [];
		var NRCOMANDA = [];
		var NRPRODCOMVEN = [];

		var isValid = true;
		selectedProducts.forEach(function (product) {
			if (positions.length > UtilitiesService.removeCurrency(product.preco) * 100) {
				isValid = false;
			}
			NRVENDAREST.push(product.NRVENDAREST);
			NRCOMANDA.push(product.nrcomanda);
			NRPRODCOMVEN.push(product.NRPRODCOMVEN);
		});

		if (isValid) {
			OperatorRepository.findAll().then(function (operatorData) {
				TableService.splitProducts(operatorData[0].chave, NRVENDAREST, NRCOMANDA, NRPRODCOMVEN, positions).then(function (data) {
					self.loadProducts();
					self.loadOriginalProducts();
				});

			});
			this.splitProductsValidation();
		} else {
			ScreenService.showMessage('Não é possível realizar a divisão de um ou mais produtos para esta seleção de posições, pois o preço total do mesmo irá ficar menor que 1 centavo.');
		}
	};

	this.cancelSplitedProducts = function (container) {
		var widgetProducts = container.getWidget("widgetProducts");
		var widgetCancel = container.getWidget("widgetCancel");
		var selectedProducts = widgetCancel.getCheckedRows();

		var NRVENDAREST = [];
		var NRCOMANDA = [];
		var NRPRODCOMVEN = [];

		selectedProducts.forEach(function (product) {
			NRVENDAREST.push(product.NRVENDAREST);
			NRCOMANDA.push(product.nrcomanda);
			NRPRODCOMVEN.push(product.NRPRODCOMVEN);
		});

		OperatorRepository.findAll().then(function (operatorData) {
			TableService.cancelSplitedProducts(operatorData[0].chave, NRVENDAREST, NRCOMANDA, NRPRODCOMVEN).then(function (data) {
				self.loadOriginalProducts();
				self.loadProducts();
				widgetProducts.activate();
			});
		});
	};

	this.generatePositionCode = function (args) {
		var selectedPosition = args.owner.data('position') + 1;
		OperatorRepository.findAll().then(function (operatorData) {
			TableActiveTable.findAll().then(function (table) {
				TableService.generatePositionCode(operatorData[0].chave, table[0].NRVENDAREST, table[0].NRCOMANDA, selectedPosition);
			});
		});
	};

	this.showRequestedTableDialog = function (chave) {
		ScreenService.showCustomDialog(
			"A conta para esta mesa já foi solicitada.",
			"alert", false,
			[{
				label: "Reabrir Mesa",
				code: function (e, deferred) {
					PermissionService.checkAccess('liberarMesa').then(function () {
						self.handleFilterParameters();
					}.bind(this));
					deferred.resolve(e);
				}
			},
			{
				label: "Receber Mesa",
				code: function (e, deferred) {
					AccountController.getAccountData(function (accountData) {
						TableService.changeTableStatus(chave, accountData[0].NRVENDAREST, accountData[0].NRCOMANDA, 'R').then(function (response) {
							self.openAccountPayment();
						}.bind(this));
					}.bind(this));
					deferred.resolve(e);
				}
			},
			{
				label: "Cancelar",
				code: function (e, deferred) {
					deferred.reject(e);
				}
			}]
		);
	};

	this.handleShowPositions = function (popupWidget, tableAlreadyOpen) {
		OperatorRepository.findOne().then(function (operatorData) {
			// this is to avoid running this function when closing the clients/consumers select component
			if (!this.blockPopupOnEnterEvent) {
				// get fields
				var positionsField = popupWidget.getField('positionsField');
				var spinPositionControl = popupWidget.getField('NRPOSICAOMESA');
				var radioTablePositions = popupWidget.getField('radioTablePositions');
				var NMCONSUMIDOR = popupWidget.getField('NMCONSUMIDOR');
				var consumerSearch = popupWidget.getField('consumerSearch');
				var NMRAZSOCCLIE = popupWidget.getField('NMRAZSOCCLIE');
				var DSCONSUMIDOR = popupWidget.getField('DSCONSUMIDOR');
				var QRCODEACTION = popupWidget.getAction('qrcode');
				var controlPos = 2;

				if (operatorData.IDLUGARMESA === 'N') {
					radioTablePositions.isVisible = false;
					spinPositionControl.isVisible = false;
					controlPos = 1;
				}

				// reset popup
				popupWidget.currentRow.CDCLIENTE = null;
				popupWidget.currentRow.NMRAZSOCCLIE = null;
				popupWidget.currentRow.CDCONSUMIDOR = null;
				popupWidget.currentRow.NMCONSUMIDOR = null;
				popupWidget.currentRow.DSCONSUMIDOR = null;
				popupWidget.currentRow.consumerSearch = null;
				popupWidget.currentRow.IDSITCONSUMI = null;
				popupWidget.currentRow.NRPOSICAOMESA = controlPos;
				popupWidget.currentRow.NRPESMESAVEN = controlPos;
				NMRAZSOCCLIE.readOnly = false;
				NMCONSUMIDOR.readOnly = false;
				if (consumerSearch) consumerSearch.readOnly = false;
				if (DSCONSUMIDOR) {
					DSCONSUMIDOR.isVisible = false;
				}
				positionsField.isVisible = false;
				radioTablePositions.setValue('M');
				positionsField.dataSource.data[0].NRPOSICAOMESA = 2;
				positionsField.dataSource.data[0].clientMapping = {};
				positionsField.dataSource.data[0].consumerMapping = {};
				positionsField.dataSource.data[0].positionNamedMapping = {};

				TableActiveTable.findOne().then(function (tableData) {

					popupWidget.getField('NRPOSICAOMESA').maxValue = operatorData.NRMAXPESMES;

					var permiteDig = operatorData.IDPERDIGCONS == 'S';
					NMRAZSOCCLIE.readOnly = permiteDig;
					NMCONSUMIDOR.readOnly = permiteDig;
					if (QRCODEACTION) {
						QRCODEACTION.isVisible = !Util.isDesktop() && !UtilitiesService.isPoyntDevice();
						QRCODEACTION.readOnly = false;
					}

					if (tableAlreadyOpen && tableData) {
						positionsField.dataSource.data[0].NRPOSICAOMESA = tableData.NRPOSICAOMESA;
						popupWidget.currentRow.NRPOSICAOMESA = tableData.NRPOSICAOMESA;

						if (tableData.posicoes.length > 0) {
							radioTablePositions.setValue('P');
							positionsField.isVisible = true;
							NMCONSUMIDOR.readOnly = true;
							NMRAZSOCCLIE.readOnly = true;
							if (consumerSearch) consumerSearch.readOnly = true;
							if (DSCONSUMIDOR) {
								DSCONSUMIDOR.isVisible = operatorData.IDUTLNMCONSMESA === 'S';
								DSCONSUMIDOR.readOnly = true;
							}
							if (QRCODEACTION) {
								QRCODEACTION.readOnly = true;
							}
							var positionsObject = _.get(tableData, 'posicoes', {});
							positionsField.dataSource.data[0].clientMapping = self.buildClientMapping(positionsObject);
							positionsField.dataSource.data[0].consumerMapping = self.buildConsumerMapping(positionsObject);
							positionsField.dataSource.data[0].positionNamedMapping = self.buildPositionNamedMapping(positionsObject);
							templateManager.updateTemplate();
						} else {
							if (tableData.CDCLIENTE) {
								NMCONSUMIDOR.readOnly = false;
								self.prepareCustomers(NMCONSUMIDOR, tableData.CDCLIENTE);
							}
							if (consumerSearch) consumerSearch.readOnly = false;
							if (DSCONSUMIDOR) {
								DSCONSUMIDOR.isVisible = false;
							}
							popupWidget.currentRow.CDCLIENTE = tableData.CDCLIENTE;
							popupWidget.currentRow.NMRAZSOCCLIE = tableData.NMRAZSOCCLIE;
							popupWidget.currentRow.CDCONSUMIDOR = tableData.CDCONSUMIDOR;
							popupWidget.currentRow.NMCONSUMIDOR = tableData.NMCONSUMIDOR;
						}
					}
					if ((operatorData.modoHabilitado === 'M') && (operatorData.IDLUGARMESA == 'S')) {
						radioTablePositions.isVisible = true;
						spinPositionControl.isVisible = true;
						WaiterNamedPositionsState.initializeTemplate();
					}
				});
			}
			this.blockPopupOnEnterEvent = false;
		}.bind(this));
	};

	this.initConsumerPopup = function (popupWidget, modeWidget) {

		fieldCopy = angular.copy(modeWidget.fields[1].dataSource.data[0]);

		if (!this.blockPopupOnEnterEvent) {
			if (modeWidget.fields[0].value() === "M" || !_.isEmpty(modeWidget.fields[1].position)) {
				var NMCONSUMIDOR = popupWidget.getField('NMCONSUMIDOR');
				var NMRAZSOCCLIE = popupWidget.getField('NMRAZSOCCLIE');
				var QRCODEACTION = popupWidget.getAction('qrcode');
				var consumerSearch = popupWidget.getField('consumerSearch');

				popupWidget.currentRow.CDCLIENTE = null;
				popupWidget.currentRow.NMRAZSOCCLIE = null;
				popupWidget.currentRow.CDCONSUMIDOR = null;
				popupWidget.currentRow.NMCONSUMIDOR = null;
				popupWidget.currentRow.consumerSearch = null;
				popupWidget.currentRow.IDSITCONSUMI = null;
				popupWidget.currentRow.NRPOSICAOMESA = 2;
				NMRAZSOCCLIE.readOnly = false;
				NMCONSUMIDOR.readOnly = false;
				consumerSearch.readOnly = false;

				TableActiveTable.findOne().then(function (tableData) {
					OperatorRepository.findOne().then(function (operatorData) {

						var permiteDig = operatorData.IDPERDIGCONS == 'S';
						NMRAZSOCCLIE.readOnly = permiteDig;
						NMCONSUMIDOR.readOnly = permiteDig;
						consumerSearch.readOnly = permiteDig;

						QRCODEACTION.isVisible = !Util.isDesktop() && !UtilitiesService.isPoyntDevice();

						var positionsObject = _.get(tableData, 'posicoes', {});
						modeWidget.fields[1].dataSource.data[0].clientMapping = self.buildClientMapping(positionsObject);
						modeWidget.fields[1].dataSource.data[0].consumerMapping = self.buildConsumerMapping(positionsObject);
						modeWidget.fields[1].dataSource.data[0].positionNamedMapping = self.buildPositionNamedMapping(positionsObject);

						var positionsData = AccountController.handleConsumerPositionsOnPayment(false, Array());

						if (tableData) {
							popupWidget.currentRow.NRPOSICAOMESA = tableData.NRPOSICAOMESA;

							if (modeWidget.fields[0].value() === "P") {
								popupWidget.currentRow.CDCLIENTE = positionsData.CDCLIENTE;
								popupWidget.currentRow.NMRAZSOCCLIE = positionsData.NMRAZSOCCLIE;
								popupWidget.currentRow.CDCONSUMIDOR = positionsData.CDCONSUMIDOR;
								popupWidget.currentRow.NMCONSUMIDOR = positionsData.NMCONSUMIDOR;
							}
							else if (tableData.CDCLIENTE) {
								popupWidget.currentRow.CDCLIENTE = tableData.CDCLIENTE;
								popupWidget.currentRow.NMRAZSOCCLIE = tableData.NMRAZSOCCLIE;
								popupWidget.currentRow.CDCONSUMIDOR = tableData.CDCONSUMIDOR;
								popupWidget.currentRow.NMCONSUMIDOR = tableData.NMCONSUMIDOR;
							}
						}

						ScreenService.openPopup(popupWidget);
					});
				});
			}
			else {
				ScreenService.showMessage("Escolha uma posição para informar um consumidor.");
			}
		}
		this.blockPopupOnEnterEvent = false;
	};

	this.restorePositions = function (positionsField) {
		positionsField.dataSource.data[0] = fieldCopy;
	};

	this.restorePositionsCopy = function (positionsField) {
		positionsField.dataSource.data[0] = angular.copy(fieldCopy);
	};

	this.updatePositionsCopy = function (positionsField) {
		fieldCopy = angular.copy(positionsField.dataSource.data[0]);
	};

	this.doBlockPopupOnEnterEvent = function () {
		this.blockPopupOnEnterEvent = true;
	};

	this.handlePositionsRadioChange = function (radioTablePositions, positionsField, NMRAZSOCCLIE, NMCONSUMIDOR, DSCONSUMIDOR, quantityField, tableAlreadyOpen) {
		OperatorRepository.findOne().then(function (operatorData) {
			TableActiveTable.findOne().then(function (tableData) {
				var NRPOSICAOMESA;
				if (tableAlreadyOpen && !quantityField) {
					NRPOSICAOMESA = tableData.NRPOSICAOMESA;
				} else {
					NRPOSICAOMESA = quantityField.value();
				}

				NMRAZSOCCLIE.clearValue();
				NMCONSUMIDOR.clearValue();
				NMRAZSOCCLIE.readOnly = true;
				NMCONSUMIDOR.readOnly = true;
				NMCONSUMIDOR.dataSourceFilter[0].value = "";
				if (!_.isEmpty(NMCONSUMIDOR.dataSourceFilter[1])) {
					NMCONSUMIDOR.dataSourceFilter[1].value = "%%";
				}
				self.clearConsumerRow(NMCONSUMIDOR.widget.currentRow);
				var qrCodeButton = positionsField.widget.getAction('qrcode');
				var consumerSearch = positionsField.widget.getField('consumerSearch');
				if (consumerSearch) consumerSearch.clearValue();

				if (radioTablePositions.value() === 'P') {
					positionsField.isVisible = true;
					positionsField.dataSource.data[0].NRPOSICAOMESA = NRPOSICAOMESA;
					WaiterNamedPositionsState.unselectAllPositions();
					if (operatorData.IDUTLNMCONSMESA === 'S' && DSCONSUMIDOR) {
						DSCONSUMIDOR.isVisible = true;
						DSCONSUMIDOR.readOnly = true;
						DSCONSUMIDOR.clearValue();
					}
					if (consumerSearch) consumerSearch.readOnly = true;
					if (qrCodeButton) qrCodeButton.readOnly = true;
				}
				else {
					positionsField.isVisible = false;
					NMRAZSOCCLIE.readOnly = false;
					NMCONSUMIDOR.readOnly = false;
					if (DSCONSUMIDOR) {
						DSCONSUMIDOR.isVisible = false;
					}
					if (consumerSearch) consumerSearch.readOnly = false;
					if (qrCodeButton) qrCodeButton.readOnly = false;
				}
			});
		});
	};

	this.updatePositionsField = function (quantityField, positionsField) {
		if (positionsField.isVisible === true) {
			positionsField.dataSource.data[0].NRPOSICAOMESA = quantityField.value();
		}
	};

	this.handleOpenTablePositionChange = function (positionsField) {
		var qrCodeButton = positionsField.widget.getAction('qrcode');
		var NMRAZSOCCLIE = positionsField.widget.getField('NMRAZSOCCLIE');
		var NMCONSUMIDOR = positionsField.widget.getField('NMCONSUMIDOR');
		var consumerSearch = positionsField.widget.getField('consumerSearch');
		var DSCONSUMIDOR = positionsField.widget.getField('DSCONSUMIDOR');

		if (positionsField.position.length === 0) {
			NMRAZSOCCLIE.clearValue();
			NMCONSUMIDOR.clearValue();
			NMRAZSOCCLIE.readOnly = true;
			NMCONSUMIDOR.readOnly = true;
			if (consumerSearch) {
				consumerSearch.clearValue();
				consumerSearch.readOnly = true;
			}
			if (DSCONSUMIDOR) {
				DSCONSUMIDOR.clearValue();
				DSCONSUMIDOR.readOnly = true;
			}
			if (qrCodeButton) qrCodeButton.readOnly = true;
		} else {
			if (NMRAZSOCCLIE.readOnly === true) {
				NMRAZSOCCLIE.readOnly = false;
				NMCONSUMIDOR.readOnly = false;
			} else {
				if (mustUnselectPositions(positionsField.dataSource.data[0], positionsField.position)) {
					unselectPositions(positionsField, positionsField.newPosition);
				}
			}
			if (consumerSearch) {
				consumerSearch.clearValue();
				consumerSearch.readOnly = false;
			}
			if (DSCONSUMIDOR) {
				DSCONSUMIDOR.readOnly = false;
			}
			if (qrCodeButton) qrCodeButton.readOnly = false;
			updateClientField(positionsField, NMRAZSOCCLIE, NMCONSUMIDOR, DSCONSUMIDOR);
		}
	};

	function updateClientField(positionsField, NMRAZSOCCLIE, NMCONSUMIDOR, DSCONSUMIDOR) {
		var currentRow = positionsField.widget.currentRow;
		var clientMapping = positionsField.dataSource.data[0].clientMapping;
		var positionNamedMapping = positionsField.dataSource.data[0].positionNamedMapping;
		var position = positionsField.position[0] + 1;

		if (clientMapping[position]) {
			currentRow.CDCLIENTE = clientMapping[position].CDCLIENTE;
			currentRow.NMRAZSOCCLIE = clientMapping[position].NMRAZSOCCLIE;
			self.prepareCustomers(NMCONSUMIDOR, clientMapping[position].CDCLIENTE, true, positionsField);
		} else {
			NMRAZSOCCLIE.clearValue();
			NMCONSUMIDOR.clearValue();
			NMCONSUMIDOR.dataSourceFilter[0].value = "";
		}
		if (DSCONSUMIDOR) {
			if (positionNamedMapping[position]) {
				currentRow.DSCONSUMIDOR = positionNamedMapping[position].DSCONSUMIDOR;
			} else {
				DSCONSUMIDOR.clearValue();
			}
		}
	}

	function updateConsumerField(positionsField, NMCONSUMIDOR) {
		var consumerMapping = positionsField.dataSource.data[0].consumerMapping;
		var position = positionsField.position[0] + 1;

		if (consumerMapping[position]) {
			NMCONSUMIDOR.widget.currentRow.CDCONSUMIDOR = consumerMapping[position].CDCONSUMIDOR;
			NMCONSUMIDOR.widget.currentRow.NMCONSUMIDOR = consumerMapping[position].NMCONSUMIDOR;
		} else {
			NMCONSUMIDOR.clearValue();
		}
	}

	function unselectPositions(positionsField, newPosition) {
		positionsField.position.forEach(function (currentPosition) {
			if (currentPosition !== newPosition) {
				positionsField.toggleButtonSelectedStatus(positionsField, currentPosition, true);
			}
		});
	}

	function mustUnselectPositions(data, selectedPositions) {
		var clientMapping = data.clientMapping;
		var consumerMapping = data.consumerMapping;
		var positionNamedMapping = data.positionNamedMapping;
		var mustUnselectPositions = false;
		var clientSelected = null;
		var currentClient = null;
		var currentPosition;

		for (var idx in selectedPositions) {
			currentPosition = selectedPositions[idx] + 1;
			currentClient = {
				'CLIENTE': _.get(clientMapping[currentPosition], 'CDCLIENTE', null),
				'CONSUMIDOR': _.get(consumerMapping[currentPosition], 'CDCONSUMIDOR', null),
				'POSITIONAMED': _.get(positionNamedMapping[currentPosition], 'DSCONSUMIDOR', null)
			};

			if (!clientSelected) {
				clientSelected = currentClient;
			} else if (!_.isMatch(clientSelected, currentClient)) {
				mustUnselectPositions = true;
				break;
			}
		}

		return mustUnselectPositions;
	}

	this.blockPopupOnEnterEvent = false;

	this.updatePositionLabel = function (fieldOnWidget) {
		var positionsField = fieldOnWidget.widget.getField('positionsField');
		if (fieldOnWidget.widget.container.name === "shortAccount") {
			positionsField = fieldOnWidget.widget.container.getWidget('accountDetails').getField('positionsField');
		}
		if (positionsField && positionsField.isVisible === true && positionsField.position.length > 0) {
			var NMRAZSOCCLIE = fieldOnWidget.widget.getField('NMRAZSOCCLIE');
			var NMCONSUMIDOR = fieldOnWidget.widget.getField('NMCONSUMIDOR');
			var DSCONSUMIDOR = fieldOnWidget.widget.getField('DSCONSUMIDOR');

			var textNMRAZSOCCLIE = NMRAZSOCCLIE.value();
			var textNMCONSUMIDOR = NMCONSUMIDOR.value();
			var textDSCONSUMIDOR = DSCONSUMIDOR ? DSCONSUMIDOR.value() : null;

			positionsField.position.forEach(function (currentPosition) {
				currentPosition = currentPosition + 1;

				var newClientMapping = null;
				if (textNMRAZSOCCLIE) {
					newClientMapping = {
						'CDCLIENTE': NMRAZSOCCLIE.widget.currentRow.CDCLIENTE,
						'NMRAZSOCCLIE': textNMRAZSOCCLIE
					};
				}
				positionsField.dataSource.data[0].clientMapping[currentPosition] = newClientMapping;

				var newConsumerMapping = null;
				if (textNMCONSUMIDOR) {
					newConsumerMapping = {
						'CDCONSUMIDOR': NMCONSUMIDOR.widget.currentRow.CDCONSUMIDOR,
						'NMCONSUMIDOR': textNMCONSUMIDOR
					};
				}
				positionsField.dataSource.data[0].consumerMapping[currentPosition] = newConsumerMapping;

				var newpositionNamedMapping = null;
				if (textDSCONSUMIDOR) {
					newpositionNamedMapping = {
						'DSCONSUMIDOR': textDSCONSUMIDOR
					};
				}
				positionsField.dataSource.data[0].positionNamedMapping[currentPosition] = newpositionNamedMapping;
			});

			positionsField.dataSource.data[0].clientChanged = true;
		}
	};

	this.toggleDelayedProduct = function (widget) {
		var selectedItem = widget.selectedRow;
		var itemsToToggle = widget.dataSource.data.forEach(function (item) {
			if (!_.isEmpty(item.NRSEQPRODCOM)) {
				if (item.NRSEQPRODCOM == selectedItem.NRSEQPRODCOM && !_.isEqual(item, selectedItem)) {
					item.__isSelected = !selectedItem.__isSelected;
				}
			}
		});
	};

	var t;
	this.consumerSearch = function (widget) {
		clearTimeout(t);
		var searchConsumer = function () {
			var consumerField = widget.getField('NMCONSUMIDOR');

			consumerField.clearValue();
			consumerField.dataSourceFilter = [
				{
					name: 'CDCLIENTE',
					operator: '=',
					value: _.isEmpty(widget.currentRow.CDCLIENTE) ? "" : widget.currentRow.CDCLIENTE
				},
				{
					name: 'CDCONSUMIDOR',
					operator: '=',
					value: widget.currentRow.consumerSearch
				}
			];
			consumerField.reload().then(function (search) {
				search = search.dataset.ParamsCustomerRepository;
				if (!_.isEmpty(search)) {
					if (search.length == 1) {
						search = search[0];
						widget.currentRow.CDCLIENTE = search.CDCLIENTE;
						widget.currentRow.NMCONSUMIDOR = search.NMCONSUMIDOR;
						widget.currentRow.CDCONSUMIDOR = search.CDCONSUMIDOR;
						widget.currentRow.NMRAZSOCCLIE = search.NMRAZSOCCLIE;
						widget.currentRow.IDSITCONSUMI = search.IDSITCONSUMI;

						consumerField.setValue(search.NMCONSUMIDOR);
						if (consumerField.change) {
							consumerField.change();
						}
					} else {
						self.handleConsumerField(consumerField);
						consumerField.openField();
					}
				}
			}.bind(this));
		}.bind(this);
		t = setTimeout(searchConsumer, 1000);
	};

	this.handleSetConsumer = function (field) {
		var currentRow = field.widget.currentRow;

		if (currentRow.IDSITCONSUMI == '2') {
			ScreenService.showMessage('Operação bloqueada. O consumidor está inativo.', 'alert');
			self.clearConsumerRow(currentRow);
		}
		else if (currentRow.IDSOLSENHCONS === 'S' && currentRow.CDSENHACONS !== null) {
			PermissionService.promptConsumerPassword(currentRow.CDCLIENTE, currentRow.CDCONSUMIDOR).then(
				function () {
					self.updatePositionLabel(field);
				},
				function () {
					currentRow.NMCONSUMIDOR = null;
					currentRow.CDCONSUMIDOR = null;
					currentRow.IDSITCONSUMI = null;
					self.updatePositionLabel(field);
				}
			);
		}
		else {
			self.updatePositionLabel(field);
		}
	};

	this.clearConsumerRow = function (currentRow) {
		currentRow.CDCLIENTE = null;
		currentRow.NMCONSUMIDOR = null;
		currentRow.CDCONSUMIDOR = null;
		currentRow.NMRAZSOCCLIE = null;
		currentRow.IDSITCONSUMI = null;
	};

	this.handleConsumerField = function (consumerField) {
		OperatorRepository.findOne().then(function (operatorData) {
			consumerField.selectWidget.floatingControl = false;
		});
	};

	this.handleEnterButton = function (args) {
		var keyCode = args.e.keyCode;
		if (keyCode === 13 || keyCode === 9) {
			UtilitiesService.handleCloseKeyboard();
			var widget = args.owner.field.widget;

			if (widget.name === 'setPassWaiterPopUp') {
				this.validatePassword(widget, widget.container.getWidget('setPassWaiterPopUp').returnParam);
			}
		}
	};

	this.partialPrint = function (widget) {
		AccountController.getAccountData(function (accountData) {
			OperatorRepository.findOne().then(function (operatorData) {
				AccountGetAccountDetails.findOne().then(function (accountDetails) {
					AccountService.getAccountDetails(operatorData.chave, operatorData.modoHabilitado, accountData[0].NRCOMANDA, accountData[0].NRVENDAREST, 'I', accountDetails.posicao).then(function (data) {
						AccountController.handlePrintBill(data.dadosImpressao);
					}.bind(this));
				}.bind(this));
			}.bind(this));
		}.bind(this));
	};

	/* Atualiza a informação do vendedor que abriu a mesa */
	this.handleVendedorAbertura = function (tableInfoStripe) {
		OperatorRepository.findOne().then(function (params) {
			// Verifica se está no modo mesa
			if (params.modoHabilitado === 'M') {
				TableActiveTable.findOne().then(function (tableData) {
					tableInfoStripe.NMVENDEDORABERT = tableData.NMVENDEDORABERT;
				});
			}
		});
	};
}

Configuration(function (ContextRegister) {
	ContextRegister.register('TableController', TableController);
});

// FILE: js/controllers/AuthController.js
function AuthController(
	OperatorService,
	OperatorController,
	WindowService,
	templateManager,
	ScreenService,
	UtilitiesService,
	PerifericosService,
	RegisterService
) {
	var self = this;
	var modoMesa = "M";
	var modoComanda = "C";
	var modoBalcao = "B";
	var modoDelivery = "D";

	this.setAuthLogin = function (widget) {
		var email = widget.currentRow.EMAIL;
		widget.getField("PASSWORD").readOnly = widget.getField(
			"AUTH"
		).readOnly = !email;
		widget.activate();
	};

	this.auth = function (widget) {
		templateManager.updateURL(
			"https://odhenpos.teknisa.cloud/backend/index.php"
		);
		OperatorService.auth(
			widget.getField("EMAIL").value(),
			widget.getField("PASSWORD").value()
		).then(function (response) {
			WindowService.openWindow("LOGIN_FILIAL_SCREEN").then(function () {
				var widgetLogin = templateManager.container.getWidget(
					"loginAuthWidget"
				);
				widgetLogin.getField("OPERADOR").value(response[0].cdoperador);
				widgetLogin
					.getField("senha")
					.value(widget.getField("PASSWORD").value());
			});
		});
	};

	this.handleFilialChange = function (filialField) {
		var widget = filialField.widget;
		self.getCaixasLogin(widget, filialField.widget.currentRow.CDFILIAL);
	};

	this.getCaixasLogin = function (widget, filial) {
		var caixaField = widget.getField("CAIXA");
		if (filial) {
			OperatorService.getCaixasLogin(filial, caixaField).then(function (caixas) {
				caixas = caixas.dataset.CaixasLogin || [];
				if (_.isEmpty(caixas)) {
					caixaField.readOnly = true;
					ScreenService.showMessage("Nenhum caixa encontrado na filial.");
				} else {
					caixaField.readOnly = false;
				}
				if (caixas.length == 1) {
					widget.setCurrentRow(_.merge(widget.currentRow, caixas[0]));
				} else {
					widget.currentRow.CAIXA = null;
					widget.currentRow.CDCAIXA = null;
				}
				caixaField.dataSource.data = caixas;
			});
		} else {
			caixaField.readOnly = true;
		}
	};

	this.authLogin = function (row) {
		var groupMenu = templateManager.project;
		var menus = groupMenu
			.getMenu("APLICACAO")
			.menus.concat(groupMenu.getMenu("CONSUMIDOR").menus);
		row.CDOPERADOR = row.OPERADOR;

		if (!row.CDFILIAL) throw "Informe a filial.";
		if (!row.CDCAIXA) throw "Informe o caixa.";
		if (!row.CDOPERADOR) throw "Informe o Operador.";
		if (!row.senha) throw "Informe a senha.";

		OperatorController.saveLoginData(row);

		OperatorService.login(
			row.CDFILIAL,
			row.CDCAIXA,
			row.CDOPERADOR,
			row.senha,
			projectConfig.frontVersion,
			projectConfig.currentMode
		).then(
			function (data) {
				if (data.OperatorRepository) {
					var operatorData = data.OperatorRepository[0];
					if (operatorData.paramsImpressora) {
						PerifericosService.test(operatorData.paramsImpressora).then(function (result) {
							if (!result.error) {
								self.handleLogin(operatorData, data, menus);
							} else {
								ScreenService.showMessage(result.message);
							}
						});
					} else {
						self.handleLogin(operatorData, data, menus);
					}
				} else {
					ScreenService.showMessage(data[0]);
				}
			}.bind(this),
			function () {
				throw "Erro na tentativa de login, verifique a configuração de IP.";
			}
		);
	};

	this.handleLogin = function (operatorData, data, menus) {
		OperatorController.checkSSLConnectionId(operatorData).then(
			function (checkSSLConnectionIdResult) {
				if (!checkSSLConnectionIdResult.error) {
					if (operatorData.IDCOLETOR !== "C") {
						var estadoCaixa = operatorData.estadoCaixa;
						var IDPALFUTRABRCXA = operatorData.IDPALFUTRABRCXA;
						var VRABERCAIX = operatorData.VRABERCAIX;
						var obrigaFechamento = operatorData.obrigaFechamento;
						if (estadoCaixa === "fechado" && IDPALFUTRABRCXA === "S") {
							OperatorController.bindedDoLogin = _.bind(
								OperatorController.doLogin,
								OperatorController,
								data,
								menus
							);
							WindowService.openWindow("OPEN_REGISTER_SCREEN");
						} else if (
							estadoCaixa === "fechado" &&
							IDPALFUTRABRCXA === "N"
						) {
							RegisterService.openRegister(
								operatorData.chave,
								VRABERCAIX
							).then(function () {
								OperatorController.doLogin(data, menus);
							});
						} else if (estadoCaixa === "aberto" && obrigaFechamento) {
							RegisterService.setClosingOnLogin(true);
							OperatorController.bindedDoLogin = _.bind(
								OperatorController.doLogin,
								OperatorController,
								data,
								menus
							);
							WindowService.openWindow("CLOSE_REGISTER_SCREEN");
						} else {
							OperatorController.doLogin(data, menus);
						}
					} else {
						if (operatorData.modoHabilitado === modoBalcao) {
							ScreenService.showMessage(
								"Modo balcão não pode ser coletor.",
								"alert"
							);
						} else if (operatorData.modoHabilitado === modoDelivery) {
							ScreenService.showMessage(
								"Modo delivery não pode ser coletor.",
								"alert"
							);
						} else {
							OperatorController.doLogin(data, menus);
						}
					}
				} else {
					ScreenService.showMessage(checkSSLConnectionIdResult.message);
				}
			}.bind(this)
		);
	};

}

Configuration(function (ContextRegister) {
	ContextRegister.register("AuthController", AuthController);
});


// FILE: js/controllers/BillController.js
function BillController(AccountController, OperatorRepository, BillService, ScreenService, AccountCart, Query,
 TableSelectedTable, templateManager, TableController, ParamsParameterRepository, PermissionService, TimestampRepository,
  WindowService, UtilitiesService, CartPool, ZHPromise){

	var self = this;

	this.getBills = function(callBack) {
		OperatorRepository.findAll().then(function(params) {
			BillService.getBills(params[0].chave).then(function(data) {
				if (callBack) {
					callBack(data);
				}
			});
		});
	};

	/* Makes it so that the products are organized by groups instead of by position in Bill Mode. */
	this.formatGroups = function(widget) {
		OperatorRepository.findOne().then(function(data) {
			if (data.modoHabilitado === 'C' || data.IDLUGARMESA === 'N' || data.modoHabilitado === 'B') {
				widget.groupProp = 'GRUPO';
			} else {
				widget.groupProp = 'posicao';
			}
		});
	};

	this.controlPriceVisibility = function(container) {
		var checkOrderWidget = container.getWidget('checkOrder');
		var checkOrderStripeWidget = container.getWidget('checkOrderStripe');
		OperatorRepository.findOne().then(function(data) {
			if (data.modoHabilitado === 'B') {
				checkOrderWidget.detailPriceProp = 'PRECO';
				checkOrderStripeWidget.isVisible = true;
			}else{
				checkOrderWidget.detailPriceProp = '';
				checkOrderStripeWidget.isVisible = false;
			}
		});
	};

	this.continueOrdering = function(container) {
		var promises = [];
		var operatorPromise = OperatorRepository.findOne().then(function(operatorData) {
			operatorData.continueOrdering = true;
			operatorData.newOrders = false;
			return OperatorRepository.save(operatorData);
		});

		var accountCartPromise = AccountCart.findAll().then(function(cart){
			return AccountCart.clearAll().then(function(){
				return CartPool.findAll().then(function(cartPool){
					cart = AccountController.filterCartPool(cart, cartPool);
					return CartPool.save(cartPool.concat(cart));
				});
			});
		});

		promises.push(operatorPromise);
		promises.push(accountCartPromise);
		ZHPromise.all(promises).then(function (){
			UtilitiesService.backMainScreen();
		});
	};

	this.checkOrderOnInit = function(container){
		this.formatGroups(container.getWidget('checkOrder'));
		this.controlPriceVisibility(container);
	};

	this.validateBill = function(row, widgets) {
		AccountController.buildOrderCode().then(function() {
			AccountCart.remove(Query.build()).then(function() {
				OperatorRepository.findOne().then(function(operatorData) {
					if (row.DSCOMANDA) {
						row.DSCOMANDA = this.validateCode(row.DSCOMANDA);
						BillService.validateBill(operatorData.chave, row.DSCOMANDA, false).then(function(data) {
							if (data[0].VAZIO == 'N') { // comanda existe
								openMenu();
							} else { // comanda não existe
								if (operatorData.geraNrComandaAut == 'N') {
									PermissionService.checkAccess('abrirComanda').then(function() {
										this.prepareOpenBill(widgets[0], row);
									}.bind(this));
								} else {
									ScreenService.showMessage('Comanda não encontrada.');
								}
							}
						}.bind(this));
					} else {
						ScreenService.showMessage('Comanda pode ser somente números.');
					}
				}.bind(this));
			}.bind(this));
		}.bind(this));
	};

	this.addBillClick = function(widgetToShow, row) {
		AccountController.buildOrderCode().then(function() {
			PermissionService.checkAccess('abrirComanda').then(function() {
				this.prepareOpenBill(widgetToShow, row);
			}.bind(this));
		}.bind(this));
	};

	this.prepareBills = function(widget) {
		this.checkBarCodeButton(widget.getField('DSCOMANDA'), widget.getField('BTNBARCODE'));
		widget.setCurrentRow({});
		widget.container.restoreDefaultMode();

		OperatorRepository.findOne().then(function(operatorData) {
			widget.getAction('conferir').isVisible = operatorData.continueOrdering;
			widget.getAction('addBill').isVisible = operatorData.geraNrComandaAut == 'S';
		});
	};

	this.prepareBillList = function(widgetToShow) {
		this.getBills(function(data) {
			widgetToShow.dataSource.data = data;
			ScreenService.openPopup(widgetToShow);
		});
	};

	this.selectBill = function(row) {
		AccountController.buildOrderCode().then(function() {
			AccountCart.remove(Query.build()).then(function() {
				OperatorRepository.findOne().then(function(operatorData) {
					BillService.validateBill(operatorData.chave, row.DSCOMANDA, false).then(function(data) {
						ScreenService.closePopup();
						openMenu();
					});
				});
			});
		});
	};

	this.prepareOpenBill = function(billOpeningWidget, row) {
		OperatorRepository.findOne().then(function(operatorData) {
			ParamsParameterRepository.findAll().then(function(dataParams) {

				if (billOpeningWidget.dataSource.data && billOpeningWidget.dataSource.data.length > 0){
					delete billOpeningWidget.dataSource.data;
				}
				billOpeningWidget.newRow();
				billOpeningWidget.moveToFirst();

				var data = {
					__createdLocal: true,
					DSCOMANDA: row.DSCOMANDA,
					CDCLIENTE: null,
					CDCONSUMIDOR: null,
					CDVENDEDOR: null
				};

				billOpeningWidget.setCurrentRow(data);

				if (dataParams[0].NRMESAPADRAO) {
					billOpeningWidget.getField('btnTableList').label = "Mesa (opcional)";
				} else {
					billOpeningWidget.getField('btnTableList').label = "Mesa";
				}

				var messageToShow = '';
				if (operatorData.geraNrComandaAut == 'N'){
					billOpeningWidget.label = "Abrir Comanda - " + row.DSCOMANDA;
				} else {
					billOpeningWidget.label = "Abrir Comanda";
				}

				// Informar consumidor.
				var consumerSearch = billOpeningWidget.getField('consumerSearch');
				var consumidorField = billOpeningWidget.getField('NMCONSUMIDOR');
				var btnReadConsumerQRCode = billOpeningWidget.getAction('btnReadConsumerQRCode');
				
				if (operatorData.infConsAbrComanda == 'N'){
					consumerSearch.isVisible = consumidorField.isVisible = btnReadConsumerQRCode.isVisible = false;
				} else {
					consumerSearch.isVisible = consumidorField.isVisible = btnReadConsumerQRCode.isVisible = true;
				}

				var dsconsumidorField = billOpeningWidget.getField('DSCONSUMIDOR');
				dsconsumidorField.isVisible = operatorData.IDSOLDIGCONS == 'S';

				// Informar mesa.
				if (operatorData.infoMesAbrComanda == 'N'){
					billOpeningWidget.getField('btnTableList').isVisible = false;
				}

				// Informar vendedor.
				billOpeningWidget.getField('VENDEDOR').isVisible = dataParams[0].IDINFVENDCOM == 'S';

				TableSelectedTable.clearAll().then(function() {
					ScreenService.openPopup(billOpeningWidget);
				});

			}.bind(this));
		}.bind(this));
	};

	this.openBill = function(billData) {
		AccountCart.remove(Query.build()).then(function() {
			ParamsParameterRepository.findOne().then(function(dataParams) {
				OperatorRepository.findOne().then(function(operatorData){
					var chave = operatorData.chave;
					TableSelectedTable.findOne().then(function(selectedTable) {
						if(operatorData.infoMesAbrComanda == 'S' && !selectedTable){
							if(dataParams.NRMESAPADRAO){
								self.confirmMesaPadrao(chave, billData, dataParams.NRMESAPADRAO);
							}else{
								ScreenService.showMessage('Selecione uma mesa.');
							}
						}else{
							var NRMESA = selectedTable ? selectedTable.NRMESA: '';
							self.checkAndOpenBill(chave, billData, NRMESA);
						}
					});
				});
			});
		});
	};

	this.confirmMesaPadrao = function(chave, billData, NRMESA){
		return ScreenService.confirmMessage(
		'Mesa não selecionada. Deseja continuar com a mesa padrão?','question',
		function(){self.checkAndOpenBill(chave, billData, NRMESA);}, function(){});
	};

	this.checkAndOpenBill = function(chave, billData, NRMESA){
		if (billData.DSCOMANDA === null) billData.DSCOMANDA = "";
		if (billData.CDCLIENTE === null) billData.CDCLIENTE = "";
		if (billData.CDCONSUMIDOR === null) billData.CDCONSUMIDOR = "";
		if (billData.DSCONSUMIDOR === null) billData.DSCONSUMIDOR = "";
		if (billData.CDVENDEDOR === null) billData.CDVENDEDOR = "";
		BillService.openBill(chave, billData.DSCOMANDA, billData.CDCLIENTE, billData.CDCONSUMIDOR, NRMESA, billData.CDVENDEDOR, billData.DSCONSUMIDOR).then(function(){
			ScreenService.closePopup();
			openMenu();
		});
	};

	this.scanBarCode = function(widget){
		if(!!window.ZhCodeScan) {
			window.scanCodeResult = _.bind(self.scanCodeResultFunction, self, widget);
			ZhCodeScan.scanCode();
		} else if(!!window.cordova && !!cordova.plugins.barcodeScanner) {
			UtilitiesService.callQRScanner().then(function(result){
				self.scanCodeResultFunction(widget, JSON.stringify(result));
			}.bind(this));
		} else {
			ScreenService.showMessage('Não foi possível chamar a integração. Sua instância não existe');
		}
	};

	this.validateCode = function(code){
		if(code.length < 10) {
			var fullStrPad = "0000000000";
			var strPad = fullStrPad.substring(0, 10 - code.length);
			code = strPad + code;
		} else if (code.length > 10) {
			code = code.substring(code.length-10);
		}
		
		return code;
	};

	this.checkBarCodeButton = function (fieldDSCOMANDA, fieldBTNBARCODE) {
		if((!!window.ZhCodeScan) || (!!window.cordova && !!cordova.plugins.barcodeScanner)) {
			fieldDSCOMANDA.class = 10;
			fieldBTNBARCODE.isVisible = true;
		}
	};

	this.scanCodeResultFunction = function(widget, result){
		result = JSON.parse(result);
		if(!result.error) {
			if (result.contents){
				widget.getField('DSCOMANDA').setValue(self.validateCode(result.contents));
				self.validateBill(widget.currentRow, widget.widgets);
			} else {
				ScreenService.showMessage('Operação bloqueada. Comanda inválida.');
			}
		} else {
			ScreenService.showMessage(result.message);
		}
	};

	var openMenu = function(){
		WindowService.openWindow('MENU_SCREEN');
	};

	var t;
    this.consumerSearch = function(){
        clearTimeout(t);
        var searchConsumer = function(){
            var consumerField = ApplicationContext.templateManager.container.getWidget('billOpeningWidget').getField('NMCONSUMIDOR');
            var popup = ApplicationContext.templateManager.container.getWidget('billOpeningWidget');

            consumerField.clearValue();

            consumerField.dataSourceFilter = [
                {
                    name: 'CDCLIENTE',
                    operator: '=',
                    value: _.isEmpty(popup.currentRow.CDCLIENTE) ? "" : popup.currentRow.CDCLIENTE
                },
                {
                    name: 'CDCONSUMIDOR',
                    operator: '=',
                    value: popup.currentRow.consumerSearch
                }
            ];
            consumerField.reload().then(function (search){
                search = search.dataset.ParamsCustomerRepository;
                if (!_.isEmpty(search)){
	                if (search.length == 1){
	                    popup.currentRow.CDCLIENTE = search[0].CDCLIENTE;
	                    popup.currentRow.NMCONSUMIDOR = search[0].NMCONSUMIDOR;
	                    popup.currentRow.CDCONSUMIDOR = search[0].CDCONSUMIDOR;
	                    popup.currentRow.NMRAZSOCCLIE = search[0].NMRAZSOCCLIE;
	                    popup.currentRow.IDSITCONSUMI = search[0].IDSITCONSUMI;
	                    popup.getField('NMCONSUMIDOR').setValue(search[0].NMCONSUMIDOR);
	                } else {
	                	self.applyClientFilter(consumerField);
		                consumerField.openField();
	                }
                }
            }.bind(this));
        }.bind(this);
        t = setTimeout(searchConsumer, 1000);
	};

    this.applyClientFilter = function(consumerField){
    	if (consumerField.dataSourceFilter[0]){
    		consumerField.dataSourceFilter[0].value = consumerField.widget.currentRow.CDCLIENTE;
    	}
    };

    this.handleConsumerField = function(consumerField){
		if (consumerField.selectWidget) {
			consumerField.selectWidget.floatingControl = false;
		}
    };

    this.handleConsumerChange = function(consumerPopup){
    	if (!_.isEmpty(consumerPopup.currentRow.CDCONSUMIDOR)){
    		if (consumerPopup.currentRow.IDSITCONSUMI === '2'){
    			ScreenService.showMessage(MESSAGE.INATIVE_CONSUMER, 'alert');
    			self.clearConsumerPopup(consumerPopup);
    		}
            else {
                consumerPopup.currentRow.CDCLIENTE = consumerPopup.currentRow.CODCLIE;
                consumerPopup.currentRow.NMRAZSOCCLIE = consumerPopup.currentRow.NOMCLIE;
                consumerPopup.getField('NMRAZSOCCLIE').setValue(consumerPopup.currentRow.NOMCLIE);
                if (consumerPopup.currentRow.IDSOLSENHCONS === 'S' && consumerPopup.currentRow.CDSENHACONS !== null){
                    PermissionService.promptConsumerPassword(consumerPopup.currentRow.CDCLIENTE, consumerPopup.currentRow.CDCONSUMIDOR).then(
                        function (){
                            // ...
                        },
                        function (){
                            consumerPopup.currentRow.NMCONSUMIDOR = null;
                            consumerPopup.currentRow.CDCONSUMIDOR = null;
                            consumerPopup.currentRow.IDSITCONSUMI = null;
                        }
                    );
                }
            }
    	}
    };

    this.clearConsumerPopup = function(popup){
        popup.currentRow.CDCLIENTE = "";
        popup.currentRow.NMRAZSOCCLIE = "";
        popup.currentRow.CDCONSUMIDOR = "";
        popup.currentRow.NMCONSUMIDOR = "";
        popup.getField('NMRAZSOCCLIE').clearValue();
        popup.getField('consumerSearch').clearValue();
        popup.getField('NMCONSUMIDOR').clearValue();
        popup.getField('NMCONSUMIDOR').dataSourceFilter = [
            {
                "name": "CDCLIENTE",
                "operator": "=",
                "value": ""
            }
        ];
    };

    this.handleEnterButton = function(args) {
		var keyCode = args.e.keyCode;
		if(keyCode === 9 || keyCode === 13) {
			UtilitiesService.handleCloseKeyboard();
		}
	};

}

Configuration(function(ContextRegister) {
	ContextRegister.register('BillController', BillController);
});

// FILE: js/controllers/CieloTestController.js
function CieloTestController(CieloTestService) {

	this.testCieloMobile = function() {
		/*Testes relacionados a manipulação da string url
		var url = "cielomobile://pagar?urlCallback=appcliente://retornopagamento&mensagem=%7B%22dataTransacao%22:%22141208104222%22,%22valor%22:%221200%22,%22idTransacao%22:%22123412%22,%22referencia%22:%22refer%C3%AAncia%22,%22tipoTransacao%22:1,%22nomeAplicacao%22:%22aplicado%20cliente%22,%22estVenda%22:%22000000000000000004%22%7D";

		var GET = {};
		var query = url.substring(45).split("&");
		for (var i = 0, max = query.length; i < max; i++) {
			if (query[i] === "") continue;// check for trailing & with no param
			var param = query[i].split("=");
			GET[decodeURIComponent(param[0])] = decodeURIComponent(param[1] || "");
		}
		var mensagem = GET['mensagem'];
		console.log(GET);
		*/

		CieloTestService.testConnection();
	};
}

Configuration(function(ContextRegister) {
	ContextRegister.register('CieloTestController', CieloTestController);
});

// FILE: js/controllers/ConsumerController.js
function ConsumerController(ConsumerService, OperatorRepository, StateRepository, CityRepository, NeighborhoodRepository, UtilitiesService, ScreenService) {

var self = this;

	//Prepara os dados para a tela de Cadastro de consumidor.
	this.resetConsumerRegister = function (widget) {
		widget.newRow();
		OperatorRepository.findOne().then(function(data){
            widget.currentRow.CDCLIENTE = data.CDCLIENTE;
			widget.getField('NMFANTCLIE').value(data.NMFANTCLIE);
            widget.getField('NMESTADO').readOnly = true;
            widget.getField('NMMUNICIPIO').readOnly = true;
            widget.getField('NMBAIRRO').readOnly = true;
            widget.currentRow.CDPAIS = null;
            widget.currentRow.SGESTADO = null;
            widget.currentRow.CDMUNICIPIO = null;
            widget.currentRow.CDBAIRRO = null;
            widget.currentRow.CDTIPOCONS = null;
            widget.currentRow.CDTIPOVENDA = null;
		});
	};

	//Valida data de nascimento do consumidor.
	this.validateConsumerBirthday = function (field) {
		if (_.isEmpty(field.value())) return true;
        if (!UtilitiesService.validateDate(field.value())) {
			ScreenService.notificationMessage('Data de nascimento inválida.', 'error', 4000);
            return false;
		}
        return true;
	};

	//Valida CPF do consumidor.
	this.validateConsumerCPF = function (field) {
        if (_.isEmpty(field.value())) return true;
		if (!UtilitiesService.isValidCPF(field.value())){
			ScreenService.notificationMessage('CPF inválido.', 'error', 4000);
            return false;
		}
        return true;
	};

	//Valida E-mail do consumidor.
	this.validateConsumerEmail = function(field) {
        if (_.isEmpty(field.value())) return true;
		if (!UtilitiesService.checkEmail(field.value())){
			ScreenService.notificationMessage('E-mail inválido.', 'error', 4000);
            return false;
		}
        return true;
	};

	// Prepara os dados na tela para envio ao backend.
	this.addConsumer = function(widget) {
        var isValidbirth = self.validateConsumerBirthday(widget.getField('consumerBirth'));
        var isValidCPF = self.validateConsumerCPF(widget.getField('consumerCPF'));
        var isValidEmail = self.validateConsumerEmail(widget.getField('consumerEmail'));

        if (!widget.isValid()){
            ScreenService.showMessage("Favor preencha os campos obrigatórios.");
        }
        else if (widget.currentRow.CDTIPOVENDA == null){
            ScreenService.showMessage("Favor escolha o tipo de venda.");
        }
        else {
            if (isValidbirth && isValidCPF && isValidEmail){
                ConsumerService.addConsumer(widget.currentRow).then(function (){
                    self.resetConsumerRegister(widget);
                    ScreenService.showMessage("Consumidor inserido com sucesso.");
                });
            }
        }
	};

    this.prepareStates = function(statesSelect, citiesSelect, neighborhoodsSelect, CDPAIS, SGESTADO, CDMUNICIPIO, CDBAIRRO){
        statesSelect.clearValue();
        citiesSelect.clearValue();
        neighborhoodsSelect.clearValue();

        statesSelect.readOnly = false;
        statesSelect.widget.currentRow.SGESTADO = "";
        StateRepository.clearAll().then(function (){
            if (_.isEmpty(CDPAIS)){
                statesSelect.readOnly = true;
                citiesSelect.readOnly = true;
                neighborhoodsSelect.readOnly = true;
                CDPAIS = "";
                SGESTADO = "";
                CDMUNICIPIO = "";
                CDBAIRRO = "";
            }

            statesSelect.dataSourceFilter[0].value = CDPAIS;

            if (CDPAIS){
                statesSelect.reload();
            }
        });
    };

    this.prepareCities = function(citiesSelect, neighborhoodsSelect, CDPAIS, SGESTADO, CDMUNICIPIO, CDBAIRRO){
        citiesSelect.clearValue();
        neighborhoodsSelect.clearValue();

        citiesSelect.readOnly = false;
        citiesSelect.widget.currentRow.CDMUNICIPIO = "";
        CityRepository.clearAll().then(function (){
            if (_.isEmpty(SGESTADO)){
                citiesSelect.readOnly = true;
                neighborhoodsSelect.readOnly = true;
                SGESTADO = "";
                CDMUNICIPIO = "";
                CDBAIRRO = "";
            }

            citiesSelect.dataSourceFilter[0].value = CDPAIS;
            citiesSelect.dataSourceFilter[1].value = SGESTADO;

            if (SGESTADO){
                citiesSelect.reload();
            }
        });
    };

    this.prepareNeighborhoods = function(neighborhoodsSelect, CDPAIS, SGESTADO, CDMUNICIPIO){
        neighborhoodsSelect.clearValue();

        neighborhoodsSelect.readOnly = false;
        neighborhoodsSelect.widget.currentRow.CDBAIRRO = "";
        NeighborhoodRepository.clearAll().then(function (){
            if (_.isEmpty(SGESTADO)){
                neighborhoodsSelect.readOnly = true;
                CDMUNICIPIO = "";
            }

            neighborhoodsSelect.dataSourceFilter[0].value = CDPAIS;
            neighborhoodsSelect.dataSourceFilter[1].value = SGESTADO;
            neighborhoodsSelect.dataSourceFilter[2].value = CDMUNICIPIO;

            if (CDMUNICIPIO){
                neighborhoodsSelect.reload();
            }
        });
    };

}

Configuration(function(ContextRegister){
	ContextRegister.register('ConsumerController', ConsumerController);
});

// FILE: js/controllers/CustomGridController.js
function CustomGridController($scope, maskEngine, templateManager, ScreenService, $timeout, ApplicationContext) {

	var controller = this;

	this.currencyFormat = function (value) {
		return maskEngine.currencyFormat(value);
	};

	$scope.widget.shoppingCart = {
		items: [],
		subtotal: 0,
		deliveryFee: 0,
		total: 0
	};

	$scope.widget.setDeliveryFee = function (fee) {
		$scope.widget.shoppingCart.deliveryFee = fee;
		$scope.widget.shoppingCart.total = $scope.widget.shoppingCart.subtotal + $scope.widget.shoppingCart.deliveryFee;
	};

	var actions = [{
		"label": "Cancelar",
		"showAsAction": "back",
		"isVisible": true,
		"showOnForm": true,
		"showOnList": true,
		"hideIcon": true,
		"events": [{
			"name": "ActionEvent",
			"code": "ScreenService.closePopup();",
			"id": 9998
		}]
	}, {
		"label": "OK",
		"showAsAction": "never",
		"isVisible": true,
		"showOnForm": true,
		"showOnList": true,
		"events": [{
			"name": "ActionEvent",
			"code": "args.owner.widget.saveItem(args.owner.widget);",
			"id": 9999
		}]
	}, {
		"icon": "trash",
		"showAsAction": "always|edit|checked_rows|view",
		"isVisible": true,
		"showOnForm": true,
		"showOnList": true,
		"label": "Excluir item",
		"events": [{
			"name": "ActionEvent",
			"code": "args.owner.widget.deleteItem(args.owner.widget)",
			"id": 9999
		}]
	}];

	var popupWidget = metaDataFactory.widgetFactory({
		"id": 9999,
		"name": "product_detail_widget",
		"container": $scope.widget.container,
		"parentWidget": $scope.widget,
		"label": "",
		"template": "widget/product-grid/detail.html",
		"isVisible": true,
		"popupNoBlock": false,

		"fields": [{
			"isReadOnly": false,
			"isVisible": true,
			"isVisibleForm": true,
			"isVisibleGrid": false,
			"template": "field/number.html",
			"label": "Quantidade",
			"name": "qtty",
			"minValue": 1,
			"maxValue": 999,
			"spin": true
		}],
		"dataSource": [],
		"dataSourceFilter": [],
		"events": [],
		"actions": actions
	});

	popupWidget.saveItem = function (widget) {
		widget.productItem.qtty = widget.getField('qtty').value();
		if (controller.checkConstraints(widget)) {
			if (!widget.updating) {
				controller.addItem(widget.productItem, false);
			} else {
				controller.updateItem(widget.productItem);
			}

			ScreenService.closePopup();
		} else {
			ScreenService.showMessage("Verifique os erros nas seleções dos detalhes do produto!", "error");
		}
	};

	popupWidget.deleteItem = function (widget) {
		ScreenService.confirmMessage("Tem certeza que deseja excluir o produto?", "question", function () {
			widget.productItem.qtty = 0;
			controller.updateItem(widget.productItem);
			ScreenService.closePopup();
		});
	};

	this.checkConstraints = function (widget) {
		var constraintsOK = true;
		for (var i = 0; i < widget.productItem.detailPages.length; i++) {
			var page = widget.productItem.detailPages[i];
			var constraints = page.constraints;

			page.constraintError = (constraints.minSelection && (!page.selectItems || page.selectItems.length < constraints.minSelection)) ||
				(constraints.maxSelection && page.selectItems && page.selectItems.length > constraints.maxSelection);
			if (page.constraintError && constraintsOK) {
				constraintsOK = false;
				controller.selectDetailPage(page, widget.productItem);
			}
		}

		return constraintsOK;
	};

	this.showProductDetails = function (item, updating) {
		popupWidget.label = updating ? ScreenService.i18n("Atualizar Produto") : ScreenService.i18n("Incluir Produto");
		popupWidget.productItem = item;
		popupWidget.updating = updating;
		// delete action only when updating
		popupWidget.actions[2].isVisible = updating;
		if ($scope.widget.onProductClick) {
			$scope.widget.onProductClick({
				data: popupWidget
			});
		}
		ScreenService.openPopup(popupWidget);
	};

	function clearItemDetails(item) {
		item.optionsPrice = 0;
		for (var i = 0; i < item.detailPages.length; i++) {
			item.detailPages[i].selectItems = [];
		}
	}

	function getSelectedOptions(item) {
		var items = [];
		if (item.detailPages) {
			for (var i = 0; i < item.detailPages.length; i++) {
				if (item.detailPages[i].selectItems) {
					items = items.concat(item.detailPages[i].selectItems);
				}
			}
		}

		return $.map(items, function (obj) {
			return obj[$scope.widget.observationCodeField];
		}).join('|');
	}

	this.addItem = function (item, details) {
		if (Util.isUndefined(details)) {
			details = (item[$scope.widget.productDetailsField] || []);
		}

		var itemAdded = $.extend(true, {}, item);

		if (details) { // When clicking the item.
			itemAdded.qtty = 1;
			itemAdded.optionsPrice = 0;
			this.showProductDetails(itemAdded, false);
		} else { // When adding the item.
			var existingItem = $.grep($scope.widget.shoppingCart.items, function (element) {
				return element[$scope.widget.productCodeField] == item[$scope.widget.productCodeField] &&
					getSelectedOptions(element) == getSelectedOptions(item);
			});

			if (existingItem.length === 0) { // New item in the cart.
				itemAdded.ID = this.makeItemID();
				itemAdded.qtty = itemAdded.qtty || 1;
				itemAdded.optionsPrice = itemAdded.optionsPrice || 0;
				itemAdded.detailPages = itemAdded.detailPages || [];
				itemAdded.total = (itemAdded[$scope.widget.productPriceField] + itemAdded.optionsPrice) * itemAdded.qtty;
				$scope.widget.shoppingCart.items.push(itemAdded);
			} else { // Similar item already exists in the cart.
				existingItem[0].qtty += item.qtty || 1;
				existingItem[0].total += (item[$scope.widget.productPriceField] + (item.optionsPrice || 0)) * (item.qtty || 1);
			}

			// Transforms the observations into a specific array to be read by the checking screen.
			if (itemAdded.selectedPage) {
				itemAdded[$scope.widget.observationCodeField] = [];
				for (var i in itemAdded.selectedPage.selectItems) {
					itemAdded[$scope.widget.observationCodeField].push(itemAdded.selectedPage.selectItems[i][$scope.widget.observationCodeField]);
				}
			}

			$scope.widget.shoppingCart.subtotal += (item[$scope.widget.productPriceField] + (item.optionsPrice || 0)) * (item.qtty || 1);
			$scope.widget.shoppingCart.total = $scope.widget.shoppingCart.subtotal + $scope.widget.shoppingCart.deliveryFee;
			this.updateCartAction();

			templateManager.updateTemplate();
		}
	};

	this.makeItemID = function () {
		var id = 0;
		$scope.widget.shoppingCart.items.forEach(function (cartItem) {
			if (cartItem.ID > id) id = cartItem.ID;
		});
		return ++id;
	};

	this.updateCartItem = function (item) {
		item.oldQtty = item.qtty;
		item.oldOptionsPrice = item.optionsPrice;
		this.showProductDetails(item, true);
	};

	this.updateItem = function (item) {
		if (item.qtty > 0) {
			item.total = (item[$scope.widget.productPriceField] + item.optionsPrice) * (item.qtty || 1);
		} else {
			$scope.widget.shoppingCart.items.splice($scope.widget.shoppingCart.items.indexOf(item), 1);
		}

		$scope.widget.shoppingCart.subtotal -= (item[$scope.widget.productPriceField] + item.oldOptionsPrice) * item.oldQtty;
		$scope.widget.shoppingCart.subtotal += (item[$scope.widget.productPriceField] + item.optionsPrice) * item.qtty;
		$scope.widget.shoppingCart.total = $scope.widget.shoppingCart.subtotal + $scope.widget.shoppingCart.deliveryFee;
		this.updateCartAction();
		templateManager.updateTemplate();
	};

	this.updateCartAction = function () {
		//$scope.widget.getAction('cart').hint = $scope.widget.shoppingCart.items.length;
		//$scope.widget.getAction('cart').readOnly = $scope.widget.shoppingCart.items.length === 0;
	};

	this.clearShoppingCart = function () {
		ScreenService.confirmMessage("Tem certeza que deseja cancelar o seu pedido?", "question", function () {
			$scope.widget.shoppingCart.items = [];
			$scope.widget.shoppingCart.subtotal = 0;
			$scope.widget.shoppingCart.total = $scope.widget.shoppingCart.subtotal + $scope.widget.shoppingCart.deliveryFee;
			controller.updateCartAction();
			templateManager.updateTemplate();
		});
	};

	this.confirmOrder = function () {
		$scope.widget.getAction('cart').click();
	};

	this.productSearch = function (item) {
		return !$scope.searchList ||
			(item[$scope.widget.productNameField] && item[$scope.widget.productNameField].indexOfLatin($scope.searchList) != -1) ||
			(item[$scope.widget.productDescField] && item[$scope.widget.productDescField].indexOfLatin($scope.searchList) != -1);
	};

	this.clearSearch = function () {
		$scope.searchList = "";
	};

	this.selectDetailPage = function (page, item) {
		item.selectedPage = page;

		var detailContainer = $('.product-detail-pages');
		var tabLine = detailContainer.find('.zh-tab-line');
		$timeout(function () {
			var pageHeaderElement = detailContainer.find('.detail-pages-header > li').eq(item.detailPages.indexOf(page));
			tabLine.width(pageHeaderElement.width());
			tabLine.css('left', pageHeaderElement.position().left);
		});
	};

	this.selectOption = function (option, page, item) {
		if (Util.isUndefined(page.selectItems)) {
			page.selectItems = [];
		}

		var index = this.getOptionIndex(page, option);
		if (index > -1) {
			item.optionsPrice -= page.selectItems[index].price || 0;
			page.selectItems.splice(index, 1);
		} else {
			page.selectItems.push(option);
			item.optionsPrice += option.price || 0;
			if (page.constraints.maxSelection && page.selectItems.length > page.constraints.maxSelection) {
				item.optionsPrice -= page.selectItems[0].price || 0;
				page.selectItems.splice(0, 1);
			}
		}
	};

	this.getOptionIndex = function (page, option) {
		var index = -1;
		if (page.selectItems) {
			for (var i = 0; i < page.selectItems.length; i++) {
				if (option[$scope.widget.parentWidget.observationCodeField] == page.selectItems[i][$scope.widget.parentWidget.observationCodeField]) {
					index = i;
				}
			}
		}

		return index;
	};
}

// FILE: js/controllers/DeliveryController.js
function DeliveryController(DeliveryService, OperatorRepository, ScreenService, WindowService, templateManager) {

    var self = this;

	this.setDataSource = function(widget) {
		OperatorRepository.findOne().then(function(operatorData){
			var params = {
				'CDFILIAL':	operatorData.CDFILIAL,
				'CDLOJA': operatorData.CDLOJA 
			};
			
	        DeliveryService.getDeliveryOrders(params).then(function(response){
            	widget.dataSource.data = response;
            }).catch(function (error) {
                ScreenService.showMessage(error);
            });

		});
    };

	this.setDataSourceControl = function(widget) {
		OperatorRepository.findOne().then(function(operatorData){
			var params = {
                "CDFILIAL": operatorData.CDFILIAL,
                "CDLOJA": operatorData.CDLOJA
            };

	        DeliveryService.setDataSourceControl(params).then(function (response){
            	widget.dataSource.data = response;
            }).catch(function (error) {
                ScreenService.showMessage(error);
            });
		});
    };

    this.newOrder = function(){
		WindowService.openWindow('DELIVERY_ORDER_DETAIL_SCREEN').then(function(){
           var widgetOrder = templateManager.container.getWidget('order');
           widgetOrder.currentRow = {};
           widgetOrder.edit();
       });
    };

    this.openDeliveryDetail = function(widget){
        WindowService.openWindow('DELIVERY_ORDER_DETAIL_SCREEN').then(function(){
            var widgetOrder = templateManager.container.getWidget('order');
            widgetOrder.currentRow = widget.currentRow;

            widgetOrder.getField('SPOONROCKET').isVisible = false;
            if(widgetOrder.currentRow.IDORGCMDVENDA== 'DLV_IFO'){
                widgetOrder.getField('NRCOMANDAEXT').label = 'iFood';
                widgetOrder.getField('NRCOMANDAEXT').isVisible = true;
            }else if(widgetOrder.currentRow.IDORGCMDVENDA == 'DLV_SPO'){
                widgetOrder.getField('NRCOMANDAEXT').label = 'iFood';
                widgetOrder.getField('NRCOMANDAEXT').isVisible = true;
                widgetOrder.getField('SPOONROCKET').isVisible = true;
                widgetOrder.getField('SPOONROCKET').value('Entregador Automatico, não chamar.');
            }else if(widgetOrder.currentRow.IDORGCMDVENDA == 'DLV_UBR'){
                widgetOrder.getField('NRCOMANDAEXT').label = 'Uber Eats';
                widgetOrder.getField('NRCOMANDAEXT').isVisible = true;
            }else{
                widgetOrder.getField('NRCOMANDAEXT').isVisible = false;
            }

            widgetOrder.view();
            if(widgetOrder.currentRow.IDSTCOMANDA == 'P'){
                widgetOrder.getAction('cancelOrder').isVisible = false;
                widgetOrder.getAction('cupomFiscal').isVisible = false;
                widgetOrder.getAction('reprint').isVisible = true;
                widgetOrder.getAction('concludeOrder').isVisible = true;
            }else{
                widgetOrder.getAction('cancelOrder').isVisible = true;
                widgetOrder.getAction('cupomFiscal').isVisible = true;
                widgetOrder.getAction('reprint').isVisible = false;
                widgetOrder.getAction('concludeOrder').isVisible = false;
            }
        });
    };

    this.saidaPedidos = function(widget){
    	var widgetEntregador = widget.container.getWidget('popupEntregadorSaida');
    	var pedidos = widget.getCheckedRows();
    	var pedidosValidos = true;
    	pedidos.forEach(function(pedido){
    		if(pedido.IDSTCOMANDA != 'P'){
    			pedidosValidos = false;
    		}
    	});
    	if(pedidosValidos && pedidos.length > 0){
    		ScreenService.openPopup(widgetEntregador);
    	}else if(pedidos.length == 0){
            ScreenService.showMessage('Operação inválida. Pelo menos um pedido deve ser selecionado para ser entregue.');
        }else{
    		ScreenService.showMessage('Operação inválida. Todos os pedidos selecionados devem estar aguardando entregador.');
    	}
    };

    this.entregarPedidos = function(widget){
        if(widget.isValid()){
            var entregador = widget.currentRow.CDVENDEDOR? widget.currentRow.CDVENDEDOR : null;
            var pedidos = widget.container.getWidget('ordersControl').getCheckedRows();
            DeliveryService.entregarPedidos(pedidos, entregador).then(function(data){
                widget.currentRow = {};
                self.setDataSourceControl(widget.container.getWidget('ordersControl'));
                ScreenService.closePopup();
            }).catch(function (error) {
                ScreenService.showMessage(error);
            });
        }
    };

    this.chegadaEntregador = function(widget){
        var widgetEntregador = widget.container.getWidget('popupEntregadorChegada');
        ScreenService.openPopup(widgetEntregador);
    };

    this.getPedidosEntregues = function(widget){
        var widgetChegada = widget.container.getWidget('popUpChegadaPedidos');
        DeliveryService.getPedidosEntregues(widget.currentRow.CDVENDEDOR).then(function (response){
            widgetChegada.dataSource.data = response;
            ScreenService.closePopup();
            ScreenService.openPopup(widgetChegada);
        });
    };

    this.chegadaPedidos = function(widget){
        var widgetEntregador = widget.container.getWidget('popupEntregadorChegada');
        DeliveryService.chegadaPedidos(widget.dataSource.data, widgetEntregador.currentRow.CDVENDEDOR).then(function(data){
            self.setDataSourceControl(widget.container.getWidget('ordersControl'));
            widgetEntregador.currentRow = {};
            ScreenService.closePopup();
        }).catch(function (error) {
            ScreenService.showMessage(error);
        });
    };

    this.printDelivery = function(widget){
        var orders = widget.container.getWidget('ordersControl').getCheckedRows();
        if(orders.length == 0){
            ScreenService.showMessage('Pelo menos um pedido deve ser selecionado.');
        }else{
            ScreenService.confirmMessage("Deseja imprimir o relatório de entrega dos pedidos selecionados?",'question',
                function(success){
                    DeliveryService.printDelivery(orders).then(function(data){
                        if(!data[0].error){
                            ScreenService.showMessage('Relatório de entrega impresso com sucesso.');
                        }else{
                            ScreenService.showMessage('Houve um problema com a impressão do relatório de entrega.', 'ERROR');
                        }
                    }).catch(function (error) {
                        ScreenService.showMessage(error);
                    });
                }
            );
        }
    };

    this.reprintDeliveryCupomFiscal = function(widget){
        var orders = widget.container.getWidget('ordersControl').getCheckedRows();
        if(orders.length == 0){
            ScreenService.showMessage('Pelo menos um pedido deve ser selecionado.');
        }else{
            ScreenService.confirmMessage("Deseja reimprimir o cupom fiscal dos pedidos selecionados?",'question',
                function(success){
                    DeliveryService.reprintDeliveryCupomFiscal(orders).then(function(data){
                        if(!data[0].error){
                            ScreenService.showMessage('Cupom Fiscal impresso com sucesso.');
                        }else{
                            ScreenService.showMessage(data[0].message, 'ERROR');
                        }
                    }).catch(function (error) {
                        ScreenService.showMessage(error);
                    });
                }
            );
        }
    };

    this.nothing = function(){};

}

Configuration(function(ContextRegister) {
	ContextRegister.register('DeliveryController', DeliveryController);
});

// FILE: js/controllers/GeneralFunctions.js
function GeneralFunctions(OperatorRepository, GeneralFunctionsService, ScreenService, UtilitiesService, PrinterService, 
	PermissionService, PaymentService, ImpressaoLeituraX, IntegrationService, SSLConnectionId, ParamsMenuRepository, 
	FilterProducts, ItemSangria, Query, templateManager, IntegrationSiTEF, OperatorController){

	var self = this;
	var MESSAGE_ADMINISTRATIVE_OK;
	var MESSAGE_NULL_RESPONSE = 'Não foi pissível obter o retorno da integração.';

	var sitefConsts = IntegrationSiTEF.paymentTypeConstants().geral;

	// Reimpressão - Cupom Fiscal

	this.handleReprintPopup = function (widget) {
		widget.getField('radioReprintSaleCoupon').setValue('U');

		self.setSaleCodeLength(widget);
		self.handleReprintType(widget);
	};

	this.setSaleCodeLength = function (widget) {
		OperatorRepository.findOne().then(function (operatorData) {
			widget.getField('saleCode').maxlength = operatorData.IDTPEMISSAOFOS === 'SAT' ? 6 : 9;
		});
	};

	this.handleReprintType = function (widget) {
		var saleCodeField = widget.getField('saleCode');
		var searchSalesField = widget.getField('searchSales');
		var radio = widget.getField('radioReprintSaleCoupon').value();

		saleCodeField.value('');
		searchSalesField.value('');

		if (radio === 'C') {
			saleCodeField.isVisible = true;
			searchSalesField.isVisible = false;
		} else if (radio === 'V') {
			saleCodeField.isVisible = false;
			searchSalesField.isVisible = true;
		} else {
			saleCodeField.isVisible = false;
			searchSalesField.isVisible = false;
		}
	};

	this.reprintSaleCoupon = function (widget) {
		var reprintType = widget.getField('radioReprintSaleCoupon').value();
		var saleCode = widget.getField('saleCode').value();
		var searchSales = widget.getField('searchSales').value();

		saleCode = _.isEmpty(saleCode) ? searchSales : saleCode;


		if ((reprintType === 'U') || (!_.isEmpty(saleCode))) {
			saleCode = UtilitiesService.padLeft(saleCode, widget.getField('saleCode').maxlength, '0');
			GeneralFunctionsService.reprintSaleCoupon(reprintType, saleCode).then(function (result) {
				result = result[0];

				if (!result.error) {
					if (result.paramsImpressora) {
						PerifericosService.print(result.paramsImpressora).then(function () {
							PaymentService.handlePrintReceipt(result.dadosImpressao);
							ScreenService.closePopup();
						});
					} else {
						PaymentService.handlePrintReceipt(result.dadosImpressao);
						ScreenService.closePopup();
					}
				} else {
					ScreenService.showMessage(result.message, 'alert');
				}
			});
		} else {
			ScreenService.showMessage('Código do cupom fiscal inválido.', 'alert');
		}
	};

	// Reimpressão - Cupom TEF

	this.openPopupReprintTef = function (widget) {
		PermissionService.checkAccess('administracaoTEF').then(function () {
			ScreenService.openPopup(widget);
		}.bind(this));
	};

	this.handleReprintTefType = function (widget, setOnEnter) {
		var nsuSitefField = widget.getField('nsuSitef');
		var transactionDateField = widget.getField('transactionDate');
		var transactionAuthField = widget.getField('transactionAuth');
		var transactionViaField = widget.getField('transactionVia');
		var radioReprintTefCoupon = widget.getField('radioReprintTefCoupon');

		if (setOnEnter)
			radioReprintTefCoupon.setValue('U');

		nsuSitefField.clearValue();
		transactionDateField.clearValue();
		transactionViaField.clearValue();

		transactionAuthField.isVisible = radioReprintTefCoupon.value() === 'P' || radioReprintTefCoupon.value() === 'C';
		transactionViaField.isVisible = transactionAuthField.isVisible;
		widget.getField("searchPayments").isVisible = radioReprintTefCoupon.value() === 'P';
		nsuSitefField.isVisible = radioReprintTefCoupon.value() === 'C';
		transactionDateField.isVisible = nsuSitefField.isVisible;
		self.transactionAuthOnChange(widget);

		if (!setOnEnter)
			self.updateFields(widget.fields);
	};

	this.updateFields = function (fields) {
		fields.forEach(function (field) {
			if (field.isVisible && !field.readOnly) {
				field.validations = { "required": {} };
			} else {
				field.validations = "";
			}

			if (field.name !== 'radioReprintTefCoupon') {
				field.clearValue();
				field.reload();
			}
		});
	};

	this.reprintTEFVoucher = function (widget) {
		if (widget.isValid()) {
			window.returnIntegration = _.bind(self.getReprintTextResult, this);

			if (!!window.cordova && cordova.plugins.GertecSitef) {
				self.getSitefParameters().then(function (paymentParams) {
					paymentParams.paymentType = widget.getField('radioReprintTefCoupon').value() === 'U' ? sitefConsts.reimpressaoUltimo : sitefConsts.reimpressaoEspecifica;
					paymentParams = self.handleReprintTef(widget, paymentParams);

					if (paymentParams.paymentType === sitefConsts.reimpressaoEspecifica) {
						IntegrationSiTEF.initSitefProcess(paymentParams);
					} else {
						cordova.plugins.GertecSitef.payment(JSON.stringify(paymentParams), window.returnIntegration, null);
					}
				}.bind(this));
			} else {
				window.returnIntegration(self.invalidPrinterInstance());
			}
		}
	};

	this.handleReprintTef = function (widget, paymentParams) {
		if (widget.getField('radioReprintTefCoupon').value() !== 'U') {
			var row = widget.currentRow;
			paymentParams.paymentDate = row.transactionDate.split(" ")[0].replace('/', '').replace('/', '');
			paymentParams.paymentNSU = row.nsuSitef;
			paymentParams.paymentAuth = _.isEmpty(row.transactionAuth) ? "1" : row.transactionAuth;
			paymentParams.paymentVia = _.isEmpty(row.transactionVia) ? "1" : row.transactionVia;
		} else {
			paymentParams.paymentDate = paymentParams.paymentNSU = paymentParams.paymentAuth = paymentParams.paymentVia = "";
		}

		return paymentParams;
	};

	this.getReprintTextResult = function (javaResult) {
		ScreenService.closePopup();

		if (!javaResult.error) {
			javaResult = javaResult.data;
			javaResult.merchantReceipt = _.isUndefined(javaResult.merchantReceipt) ? "" : javaResult.merchantReceipt;
			javaResult.customerReceipt = _.isUndefined(javaResult.customerReceipt) ? "" : javaResult.customerReceipt;
			PaymentService.printTEFVoucher(self.handlePrintText(javaResult));
		} else {
			ScreenService.showMessage(javaResult.message);
		}
	};

	this.handlePrintText = function (reprintObject) {
		return Array({
			'STLPRIVIA': reprintObject.customerReceipt,
			'STLSEGVIA': reprintObject.merchantReceipt
		});
	};

	this.transactionAuthOnChange = function (widget) {
		var transactionViaField = widget.getField("transactionVia");
		transactionViaField.readOnly = widget.currentRow.transactionAuth !== "2";
		transactionViaField.validations = transactionViaField.readOnly ? "" : { "required": {} };
		widget.currentRow.transactionVia = transactionViaField.readOnly ? "" : widget.currentRow.transactionVia;
		transactionViaField.reload();
	};

	// Teste de Comunicação

	this.sitefComunicateTest = function () {
		PermissionService.checkAccess('administracaoTEF').then(function () {
			ScreenService.showLoader();
			window.returnIntegration = _.bind(self.returnAdministrativeMenu, this, false);

			if (!!window.cordova && cordova.plugins.GertecSitef) {
				MESSAGE_ADMINISTRATIVE_OK = "Teste de Comunicação OK.";

				self.getSitefParameters().then(function (sitefParams) {
					sitefParams.paymentType = sitefConsts.testeComunicacao;
					cordova.plugins.GertecSitef.payment(JSON.stringify(sitefParams), window.returnIntegration, null);
				}.bind(this));
			} else {
				window.returnIntegration(self.invalidIntegrationInstance());
			}
		}.bind(this));
	};

	// Recarga de Tabelas

	this.sitefTableLoad = function () {
		ScreenService.showLoader();
		window.returnIntegration = _.bind(self.returnAdministrativeMenu, this, true);

		if (!!window.cordova && cordova.plugins.GertecSitef) {
			MESSAGE_ADMINISTRATIVE_OK = "Tabelas Carregadas com Sucesso.";

			self.getSitefParameters().then(function (sitefParams) {
				sitefParams.paymentType = sitefConsts.carregaTabelas;
				IntegrationSiTEF.initSitefProcess(sitefParams);
			}.bind(this));
		} else {
			window.returnIntegration(self.invalidIntegrationInstance());
		}
	};

	// Envio de Logs para servidor SiTef

	this.sendSitefLog = function () {
		ScreenService.showLoader();
		window.returnIntegration = _.bind(self.returnAdministrativeMenu, this, true);

		if (!!window.cordova && cordova.plugins.GertecSitef) {
			MESSAGE_ADMINISTRATIVE_OK = "Logs Enviados com Sucesso.";

			self.getSitefParameters().then(function (sitefParams) {
				sitefParams.paymentType = sitefConsts.enviaLogs;
				IntegrationSiTEF.initSitefProcess(sitefParams);
			}.bind(this));
		} else {
			window.returnIntegration(self.invalidIntegrationInstance());
		}
	};

	// Estorno

	this.reversalPayment = function (widget) {
		if (widget.isValid()) {
			if (self.validateValue(widget.getField('VRMOVIVEND'))) {
				if (self.validateDate(widget)) {
					ScreenService.showLoader();
					widget.currentRow.CDNSUHOSTTEF = self.validateNSU(widget);
					var row = widget.currentRow;
					var date = _.clone(row.TRANSACTIONDATE);

					while (date.search('/') != -1) {
						date = date.replace('/', '');
					}

					OperatorRepository.findOne().then(function (operatorData) {
						GeneralFunctionsService.getNrControlTef(row.CDNSUHOSTTEF).then(function (result) {
							result = result[0];

							if (result.error) {
								ScreenService.showMessage(result.message);
								ScreenService.hideLoader();
							} else {
								IntegrationService.reversalIntegration(self.mochRemovePaymentSale,
									Array({
										'IDTIPORECE': row.IDTIPORECE,
										'VRMOVIVEND': parseFloat(row.VRMOVIVEND.split('.').join('').replace(',', '.')),
										'DSENDIPSITEF': operatorData.DSENDIPSITEF,
										'CDLOJATEF': operatorData.CDLOJATEF,
										'CDTERTEF': operatorData.CDTERTEF,
										'TRANSACTIONDATE': date,
										'CDNSUHOSTTEF': row.CDNSUHOSTTEF,
										'NRCONTROLTEF': result.data.NRCONTROLTEF,
										'IDTPTEF': '5',
										'NRCARTBANCO': result.data.NRCARTBANCO
									})
								).then(self.reversalPaymentResult);
							}
						}.bind(this),
							function (err) {
								ScreenService.hideLoader();
							});
					}.bind(this));
				}
			}
		}
	};

	this.mochRemovePaymentSale = function () {
		return new Promise.resolve(true);
	};

	this.reversalPaymentResult = function (javaResult) {
		ScreenService.hideLoader();

		if (!javaResult.error) {
			PaymentService.printTEFVoucher(javaResult.data);
			ScreenService.closePopup();
			ScreenService.showMessage("TEF estornado com sucesso.", 'success');
		} else {
			ScreenService.showMessage(javaResult.userMessage || javaResult.message);
		}
	};

	this.openPopupReversalTef = function (widget) {
		PermissionService.checkAccess('administracaoTEF').then(function () {
			var date = new Date().toLocaleDateString('pt-BR');
			widget.getField("TRANSACTIONDATE").setValue(date);
			widget.getField("IDTIPORECE").setValue("1");
			widget.getField("VRMOVIVEND").setValue("");
			widget.getField("CDNSUHOSTTEF").setValue("");

			ScreenService.openPopup(widget);
		}.bind(this));
	};

	this.validateNSU = function (widget) {
		var CDNSUHOSTTEF = widget.currentRow.CDNSUHOSTTEF;

		while (CDNSUHOSTTEF.length < 9) {
			CDNSUHOSTTEF = '0' + CDNSUHOSTTEF;
		}

		return CDNSUHOSTTEF;
	};

	this.validateValue = function (field) {
		var value = parseFloat(field.value());

		if (isNaN(value)) {
			ScreenService.showMessage('Valor inválido.', 'alert');
			return false;
		}

		return true;
	};

	this.returnAdministrativeMenu = function (closeUI, javaResult) {
		ScreenService.hideLoader();

		if (closeUI)
			ScreenService.closePopup();

		if (!!javaResult.error) {
			ScreenService.showMessage(javaResult.message);
		} else {
			ScreenService.showMessage(MESSAGE_ADMINISTRATIVE_OK, 'SUCCESS');
		}
	};

	this.validateDate = function (widget) {
		var date = _.clone(widget.currentRow.TRANSACTIONDATE);
		date = date.split('/');

		var day = parseInt(date[0]);
		var month = parseInt(date[1]);
		var year = parseInt(date[2]);

		if (month >= 1 && month <= 12) {
			var february = self.leapYear(year) ? 29 : 28;
			var monthLength = [31, february, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

			if (day >= 1 && day <= monthLength[month - 1]) {
				if (year <= (new Date()).getFullYear())
					return true;
			}
		}

		ScreenService.showMessage("Data inválida");
		widget.currentRow.TRANSACTIONDATE = '';
		return false;
	};

	this.leapYear = function (year) {
		if (year % 4 === 0) {
			if (year % 100 === 0) {
				if (year % 400 === 0) {
					return true;
				} else {
					return false;
				}
			} else {
				return true;
			}
		} else {
			return false;
		}
	};

	this.maskDate = function (field) {
		var date = field.value();
		date = date.replace(/\D/g, '');
		var m = date.match(/(\d{0,2})(\d{0,2})(\d{0,4})/);

		if (m[1] > 0) {
			if (m[2] > 0) {
				if (m[3] > 0) {
					date = m[1] + '/' + m[2] + '/' + m[3];
				} else {
					date = m[1] + '/' + m[2];
				}
			}
		}

		field.setValue(date);
	};

	this.getSitefParameters = function () {
		return SSLConnectionId.findOne().then(function (sSLConnectionIdResponse) {
			return OperatorRepository.findOne().then(function (operatorData) {
				var paymentParams = {
					'paymentIp': operatorData.DSENDIPSITEF,
					'paymentTerminal': operatorData.CDTERTEF,
					'paymentStore': operatorData.CDLOJATEF,
					'storeCnpj': operatorData.NRINSJURFILI,
					'IDUTLSSL': operatorData.IDUTLSSL,
					'IDCODSSL': '',
					'paymentValue': '',
					'paymentInvoice': '',
					'paymentHour': '',
					'paymentOperator': '',
					'paymentDate': '',
					'paymentNSU': '',
					'paymentAuth': '',
					'paymentVia': ''
				};

				if (sSLConnectionIdResponse) {
					paymentParams.IDCODSSL = sSLConnectionIdResponse.IDCODSSL;
				}

				return paymentParams;
			}.bind(this));
		}.bind(this));
	};

	this.invalidPrinterInstance = function () {
		return {
			'error': true,
			'message': 'Não foi possível chamar a impressora. Sua instância não existe.'
		};
	};

	this.invalidIntegrationInstance = function () {
		return {
			'error': true,
			'message': 'Não foi possível chamar a integração. Sua instância não existe.'
		};
	};

	this.generalFunctionsOnEnter = function (widget) {
		OperatorRepository.findOne().then(function (operatorData) {
			var showSitefFunctions = operatorData.IDTPTEF === '5';

			widget.getField("reprintOfTEFCoupon").isVisible = showSitefFunctions;
			widget.getField("comunicateTest").isVisible = showSitefFunctions;
			widget.getField("tableLoad").isVisible = showSitefFunctions;
			widget.getField("sendSitefLog").isVisible = showSitefFunctions;

			widget.getField("qrCodeSale").isVisible = operatorData.modoHabilitado === 'B' && operatorData.IDLEITURAQRCODE === 'S';
			widget.getField("reversalPayment").isVisible = showSitefFunctions;
			widget.getField("sendLogs").isVisible = showSitefFunctions;

			var showRedeFunctions = operatorData.IDTPTEF === '4';
			widget.getField("reprintOfRedeTEFCoupon").isVisible = showRedeFunctions;
			widget.getField("redeReversalPayment").isVisible = showRedeFunctions;

			widget.getField("checkPendindPayments").isVisible = operatorData.modoHabilitado === 'B';
		});
	};

	this.blockProducts = function (widget) {
		if (widget.isValid()) {
			if (!_.isEmpty(widget.currentRow.selectProducts)) {
				GeneralFunctionsService.blockProducts(widget).then(function (blockProductResult) {
					if (!blockProductResult[0].error) {
						self.setBlockUnblock(widget.currentRow.selectProducts);
						ScreenService.showMessage("Produto(s) bloqueado(s).");
						widget.getField("selectProducts").clearValue();
						ScreenService.closePopup(widget);
					} else {
						ScreenService.showMessage(blockProductResult[0].message);
					}
				}.bind(this));
			}
			else {
				ScreenService.showMessage("Favor escolher pelo menos um produto.", "alert");
			}
		}
	};

	this.setBlockUnblock = function (products) {
		ParamsMenuRepository.findAll().then(function (menuProducts) {
			menuProducts.forEach(function (menuProduct) {
				products.forEach(function (product) {
					if (menuProduct.CDPRODUTO == product) {
						menuProduct.IDPRODBLOQ = menuProduct.IDPRODBLOQ == 'N' ? 'S' : 'N';
					}
				});
			});

			ParamsMenuRepository.save(menuProducts);
		}.bind(this));
	};

	this.unblockProducts = function (widget) {
		if (widget.isValid()) {
			if (!_.isEmpty(widget.currentRow.selectBlockedProducts)) {
				GeneralFunctionsService.unblockProducts(widget).then(function (unblockProductResult) {
					if (!unblockProductResult[0].error) {
						self.setBlockUnblock(widget.currentRow.selectBlockedProducts);
						ScreenService.showMessage("Produto(s) desbloqueado(s).");
						widget.getField("selectBlockedProducts").clearValue();
						ScreenService.closePopup(widget);
					} else {
						ScreenService.showMessage(unblockProductResult[0].message);
					}
				}.bind(this));
			}
			else {
				ScreenService.showMessage("Favor escolher pelo menos um produto.", "alert");
			}
		}
	};

	// Funcao para verificar acesso de supervisor a função de carregamento de tabelas da SITEF
	this.supervisorCarregamentoTabSitef = function (widget) {
		PermissionService.checkAccess('administracaoTEF').then(function () {
			self.sitefTableLoad();
		});
	};

	// Funcao Generica para verificar acesso de supervisor para determinado nivel de acesso
	this.verificaAcessoSupervisor = function (widget, acesso) {
		PermissionService.checkAccess(acesso).then(function () {
			ScreenService.openPopup(widget);
		}.bind(this));
	};

	// Funcao Generica para limpar Field informada
	this.clearField = function (widget, field) {
		widget.getField(field).clearValue();
	};

	this.impressaoLeituraX = function (widget) {
		PermissionService.checkAccess('leituraX').then(function () {
			ScreenService.confirmMessage('Deseja imprimir o relatório da Leitura X?', 'question', function () {
				if (widget.isValid()) {
					GeneralFunctionsService.impressaoLeituraX().then(function (impressaoLeituraX) {
						impressaoLeituraX = impressaoLeituraX[0];
						if (impressaoLeituraX.error) {
							ScreenService.showMessage(impressaoLeituraX.message);
						} else {
							if (impressaoLeituraX.saas) {
								PerifericosService.print(impressaoLeituraX).then(function () {
									if (!_.isEmpty(impressaoLeituraX.dadosImpressao)) {
										self.openPopupXReport(widget, impressaoLeituraX.dadosImpressao.parcial);
									}
								});
							} else {
								if (!_.isEmpty(impressaoLeituraX.dadosImpressao)) {
									self.openPopupXReport(widget, impressaoLeituraX.dadosImpressao.parcial);
								}
							}
						}
					});
				}
			});
		}.bind(this));
	};

	this.scanSaleCode = function (widget) {
		OperatorRepository.findOne().then(function (operatorData) {
			UtilitiesService.callQRScanner().then(function (qrCode) {
				if (!qrCode.error) {
					qrCode = qrCode.contents;

					if (_.isEmpty(qrCode)) {
						ScreenService.showMessage("Não foi possível obter os dados do leitor.");
					}
					else {
						widget.currentRow.qrCode = qrCode;
						widget.currentRow.chave = operatorData.chave;
						var cpfPopup = widget.container.getWidget('cpfPopup');
						cpfPopup.getField('CPF').clearValue();
						PaymentService.updateSaleCode();
						ScreenService.openPopup(cpfPopup);
					}
				} else {
					ScreenService.showMessage(qrCode.message, 'alert');
				}
			}.bind(this));
		}.bind(this));
	};

	this.qrCodeSale = function (row, generalWidget) {
		var CPF = row.CPF.replace(/[^0-9 ]/g, "");

		if (_.isEmpty(CPF) || UtilitiesService.isValidCPForCNPJ(CPF)) {
			PaymentService.qrCodeSale(generalWidget.currentRow.chave, generalWidget.currentRow.qrCode, CPF).then(function (saleResult) {
				saleResult = saleResult[0];
				if (saleResult.error) {
					ScreenService.showMessage(saleResult.message, 'alert');
					if (_.get(saleResult, 'resetSaleCode')) {
						PaymentService.updateSaleCode();
					}
				}
				else {
					PaymentService.handlePrintReceipt(saleResult.dadosImpressao);
					self.payAccountFinish(saleResult);
					ScreenService.closePopup();
				}
			});
		}
		else {
			ScreenService.showMessage('CPF inválido.');
		}
	};

	this.payAccountFinish = function (payAccount) {
		var message = 'Venda realizada. ';

		if (_.get(payAccount, 'IDSTATUSNFCE') === 'P') {
			message += '<br><br>' + 'NFCE emitido em modo de contigência.';
		}
		if (_.get(payAccount, 'mensagemNfce')) {
			message += '<br>' + _.get(payAccount, 'mensagemNfce');
		}
		if (_.get(payAccount, 'mensagemImpressao')) {
			message += '<br><br>' + _.get(payAccount, 'mensagemImpressao');
		}
		ScreenService.showMessage(message);
	};

	this.openPopupXReport = function (widget, parcial) {
		var widgetReport = widget.container.getWidget('report');
		OperatorRepository.findOne().then(function (operatorData) {
			if (operatorData.IDMODEIMPRES == '25') {
				parcial = _.join(_.split(parcial, ' | '), "\n");
			}
			widgetReport.setCurrentRow({ 'report': parcial });
			ScreenService.openPopup(widgetReport);
		}.bind(this));
	};

	this.printXReport = function () {
		ImpressaoLeituraX.findOne().then(function (impressaoLeituraX) {
			PrinterService.printerCommand(PrinterService.TEXT_COMMAND, impressaoLeituraX.dadosImpressao.parcial);
			PrinterService.printerSpaceCommand();
			PrinterService.printerInit().then(function (result) {
				if (result.error)
					ScreenService.alertNotification(result.message);
			});

			ScreenService.closePopup();
		});
	};

	this.handleEnterButton = function (args) {
		var keyCode = args.e.keyCode;
		if (keyCode === 13 || keyCode === 9) {
			UtilitiesService.handleCloseKeyboard();
			var field = args.owner.field;
			var widget = field.widget;

			if (widget.name === 'reprintSaleCouponPopup') {
				self.reprintSaleCoupon(widget);
			} else if (widget.name === 'unlockDeviceWidget') {
				if (field.name === 'supervisor') {
					if (!Util.isDesktop()) document.getElementById('pass').focus();
				} else if (field.name === 'pass') {
					self.handleUnlockDevice(widget.currentRow);
				}
			} else if (widget.name == 'sitefPayment') {
				IntegrationSiTEF.continueSitefProcess(widget.currentRow.userInput);
			}
		}
	};

	this.setSale = function (row, widget) {
		widget.getParent().getWidget('reprintSaleCouponPopup').getField('searchSales').value(row.NRNOTAFISCALCE);
	};

	this.getChecked = function (widget) {
		if (!_.isEmpty(FilterProducts.checkedRows)) {
			_.forEach(widget.dataSource.data, function (dataRow) {
				_.forEach(FilterProducts.checkedRows, function (checkedRow) {
					if (dataRow.CDPRODUTO == checkedRow.CDPRODUTO) {
						dataRow.__isSelected = checkedRow.__isSelected;
					}
				});
			});

			widget.setCurrentRow(FilterProducts.checkedRows);
		}
	};

	this.clearChecked = function () {
		FilterProducts.checkedRows = Array();
	};

	this.checkControl = function (widget) {
		row = widget.selectedRow;

		if (row.__index == undefined) {
			self.gridCheck(row);
		} else {
			if (row.__index >= 0) {
				self.gridCheck(row);
			}
		}
	};

	this.gridCheck = function (row) {
		if (row.__isSelected) {
			FilterProducts.checkedRows = _.concat(FilterProducts.checkedRows, row);
		} else {
			_.remove(FilterProducts.checkedRows, function (n) {
				return n.CDPRODUTO == row.CDPRODUTO;
			});
		}
	};

	this.updateSelectField = function (field) {
		var data = Array();
		field.dataSource.data = FilterProducts.checkedRows;
		_.forEach(FilterProducts.checkedRows, function (row) {
			data = _.concat(data, row.CDPRODUTO);
		}.bind(this));
		field.setValue(data);
	};

	this.clearFilter = function (widget) {
		widget.setCurrentRow({});
		widget.getField('selectProducts').dataSourceFilter = [];
	};

	this.handleCancelReprintTef = function (widget) {
		widget.fields.forEach(function (field) {
			if (field.name === 'radioReprintTefCoupon') {
				field.setValue('U');
			} else {
				field.clearValue();
				field.isVisible = false;
			}
		});

		ScreenService.closePopup();
	};

	this.hideSearchButton = function (selectWidget) {
		selectWidget.floatingControl.searchAction = false;
		selectWidget.reload();
	};

	this.addSangria = function (widget) {
		ItemSangria.findAll().then(function (item) {
			if (widget.isValid()) {
				var tipoRecebimento = widget.getField('tipoRecebimento').value();
				var valorSangria = widget.getField('valorSangria').value();
				var tipoSangria = !!widget.getField('tipoSangria').value() ? widget.getField('tipoSangria').value() : null;
				var obsSangria = !!widget.getField('obsSangria').value() ? widget.getField('obsSangria').value() : null;
				var CDTIPORECE = widget.currentRow.CDTIPORECE;
				var CDTPSANGRIA = widget.currentRow.CDTPSANGRIA;
				var IDENTIFICADOR = item.length > 0 ? _.maxBy(item, function (a) { return a.IDENTIFICADOR; }).IDENTIFICADOR + 1 : 0;

				var itemAtual = {
					tipoRecebimento: tipoRecebimento,
					valorSangria: valorSangria,
					tipoSangria: tipoSangria,
					obsSangria: obsSangria,
					CDTIPORECE: CDTIPORECE,
					CDTPSANGRIA: CDTPSANGRIA,
					IDENTIFICADOR: IDENTIFICADOR
				};

				item = _.concat(item, itemAtual);
				self.clearPopUpSangria(widget, false);

				ItemSangria.save(item).then(function (a) {
					widget.widgets[0].dataSource.data = a;
				});
			} else {
				ScreenService.showMessage('Nem todos os campos requeridos foram informados.', 'alert');
			}
		});
	};

	this.handleRequires = function (widget) {
		OperatorRepository.findOne().then(function (operatorData) {
			if (operatorData.IDSOLTPSANGRIACX === 'N') {
				widget.getField('tipoSangria').validations = null;
			}
		});
	};

	this.removeItemSangria = function (row) {
		ItemSangria.findAll().then(function (item) {
			ScreenService.confirmMessage(
				'Deseja remover o item?', 'question',
				function () {
					_.remove(item, function (a) {
						return a.IDENTIFICADOR == row.selectedRow.IDENTIFICADOR;
					}.bind(this));
					row.owner.field.getParent().getParent().dataSource.data = item;
					ItemSangria.remove(Query.build()).then(function () {
						return ItemSangria.save(item);
					});
				},
				function () { }
			);
		});
	};

	this.clearPopUpSangria = function (widget, clearItems) {
		widget.getField('tipoRecebimento').clearValue();
		widget.getField('tipoSangria').clearValue();
		widget.getField('obsSangria').clearValue();
		widget.getField('valorSangria').clearValue();

		if (clearItems) {
			var item = [];
			widget.widgets[0].dataSource.data = item;
			ItemSangria.remove(Query.build()).then(function () {
				return ItemSangria.save(item);
			});
		}
	};

	this.saveSangria = function (widget) {
		ItemSangria.findAll().then(function (item) {
			if (widget.widgets[0].dataSource.data.length > 0) {
				ScreenService.confirmMessage(
					'Deseja imprimir o relatório?', 'question',
					function () {
						self.handleReturnSangria(item, true, widget);
					},
					function () {
						self.handleReturnSangria(item, false, widget);
					}
				);
			} else {
				ScreenService.showMessage('Nenhuma sangria foi selecionada.', 'alert');
			}
		});
	};

	this.handleReturnSangria = function (item, imprimeSangria, widget) {
		GeneralFunctionsService.saveSangria(item, imprimeSangria).then(function (retorno) {
			if (!_.isEmpty(retorno)) {
				if (!retorno[0].error) {
					if (!_.isEmpty(retorno[0].dadosImpressao)) {
						self.printSangriaSmartPos(retorno[0].dadosImpressao.sangria);
					} else if (!_.isEmpty(retorno[0].mensagemImpressao)) {
						ScreenService.showMessage(retorno[0].mensagemImpressao, 'alert');
					}
					ScreenService.goBack();
				} else {
					ScreenService.showMessage(retorno[0].message, 'error');
				}
			}
		});
	};

	this.saveRow = function (widget) {
		row = widget.widgets[0];
		row.selectedRow = widget.currentRow;
	};

	this.printSangriaSmartPos = function (sangria) {
		PrinterService.printerCommand(PrinterService.TEXT_COMMAND, sangria);
		PrinterService.printerSpaceCommand();
		PrinterService.printerInit().then(function (result) {
			if (result.error) {
				ScreenService.alertNotification(result.message);
			}
		});
	};

	this.handleToggleLock = function () {
		PermissionService.checkAccess('bloqueiaDispositivo').then(function () {
			self.toggleLock();
		}.bind(this));
	};

	this.handleUnlockDevice = function (row) {
		cordova.plugins.KioskPOS.validateMasterSupervisor(row.supervisor, row.pass,
			function (unlock) {
				if (unlock)
					self.toggleLock();
				else
					ScreenService.showMessage("Senha incorreta.");
			}
		);
	};

	this.toggleLock = function () {
		ScreenService.showCustomDialog("A aplicação será reiniciada.", "alert", "exclamation", [
			{
				label: "OK",
				"default": true,
				code: function () {
					var kiosk = cordova.plugins.KioskPOS;
					kiosk.isInKiosk(function (isInKiosk) {
						if (isInKiosk)
							kiosk.unlockDevice(self.toggleLockResult, self.toggleLockResult);
						else
							kiosk.lockDevice(self.toggleLockResult, self.toggleLockResult);
					});
				}
			}, {
				label: "Cancelar",
				code: null
			}
		]);
	};

	this.toggleLockResult = function (result) {
		result = JSON.parse(result);

		if (result.error) {
			ScreenService.showMessage(result.message);
		}
	};

	this.closeUnlockPopup = function () {
		var unlockDeviceWidget = templateManager.containers.login.getWidget("loginWidget").widgets[4];
		ScreenService.closePopup(true);
		unlockDeviceWidget.isVisible = false;
	};

	this.exportLogs = function (userInteration) {
		if (!!window.cordova && !!cordova.plugins.GertecSitef) {
			userInteration = userInteration === undefined ? true : userInteration;
			if (userInteration) {
				ScreenService.showLoader();
			}

			cordova.plugins.GertecSitef.exportLogs(function (javaResult) {
				if (javaResult.error) {
					if (userInteration) {
						ScreenService.hideLoader();
						ScreenService.showMessage(javaResult.message);
					}
				} else {
					GeneralFunctionsService.exportLogs(javaResult.content, device.serial).then(function (exportLogsResult) {
						if (userInteration) {
							ScreenService.hideLoader();
						}

						exportLogsResult = exportLogsResult[0];
						if (exportLogsResult.error) {
							if (userInteration) {
								ScreenService.showMessage(exportLogsResult.message);
							}
						} else {
							cordova.plugins.GertecSitef.deleteLogs();
							if (userInteration) {
								ScreenService.showMessage("Logs exportados com sucesso.", 'success');
							}
						}
					}.bind(this));
				}
			});
		}
	};

	this.reprintRedeCoupom = function () {
		if (!!window.cordova && !!cordova.plugins.GertecRede) {
			var params = {};
			cordova.plugins.GertecRede.reprint(JSON.stringify(params), self.reprintRedeCoupomResult, function () { });
		} else {
			self.reprintRedeCoupomResult(self.invalidIntegrationInstance());
		}
	};

	this.reprintRedeCoupomResult = function (javaResult) {
		if (javaResult === null) {
			ScreenService.showMessage(MESSAGE_NULL_RESPONSE);
		} else if (javaResult.error) {
			ScreenService.showMessage(javaResult.message);
		}
	};

	this.redeReversalPayment = function () {
		if (!!window.cordova && !!cordova.plugins.GertecRede) {
			var params = {};
			IntegrationService.reversalIntegration(self.mochRemovePaymentSale, Array({ IDTPTEF: '4' })).then(self.redeReversalPaymentResult);
		} else {
			self.redeReversalPaymentResult(self.invalidIntegrationInstance());
		}
	};

	this.redeReversalPaymentResult = function (javaResult) {
		if (javaResult === null) {
			ScreenService.showMessage(MESSAGE_NULL_RESPONSE);
		} else if (javaResult.error) {
			ScreenService.showMessage(javaResult.message);
		} else {
			ScreenService.showMessage("TEF estornado com sucesso.", 'success');
		}
	};

	this.showDeviceSerial = function () {
		ScreenService.showMessage("O serial deste dispositivo é: " + device.serial, 'success');
	};
	
	this.checkPendingPayment = function() {
		var errorMessage = "Não foram encontrados pagamentos pendentes.";

		OperatorRepository.findOne().then(function(operatorData){
			OperatorController.checkPendingPayment(operatorData.IDTPTEF, errorMessage);
		}.bind(this));
	};
}

Configuration(function (ContextRegister) {
	ContextRegister.register('GeneralFunctions', GeneralFunctions);
});

// FILE: js/controllers/MenuFunctionsController.js
function MenuFunctionsController (PermissionService, AccountController, TableController, UtilitiesService, OperatorRepository, TableService, BillService, AccountService, ScreenService, templateManager, WindowService, BillController){
	var selectControl = [];

	this.showParcial = function(){
		PermissionService.checkAccess('parcialConta').then(function (){
			AccountController.showAccountDetails();
		});
	};

	this.showMsgProducao = function(){
		WindowService.openWindow('SEND_MESSAGE_SCREEN');
	};

	this.showFecharConta = function(){
		WindowService.openWindow('CLOSE_ACCOUNT_SCREEN');
	};

	this.showPagarConta = function(){
		OperatorRepository.findAll().then(function(operatorData){
			operatorData = operatorData[0];
			AccountController.getAccountData(function(accountData){
				accountData = accountData[0];

				var chave = operatorData.chave;
				var modoHabilitado = operatorData.modoHabilitado;
				var nrComanda = accountData.NRCOMANDA;
				var nrVendaRest = accountData.NRVENDAREST;
				var IDCOLETOR = operatorData.IDCOLETOR;

				AccountService.getAccountDetails(chave, ((modoHabilitado === 'O') ? 'M' : modoHabilitado), nrComanda, nrVendaRest, 'M', '').then(function (accountDetailsData){
					if (accountDetailsData.nothing[0].nothing === 'nothing') {
						if (!accountDetailsData.AccountGetAccountDetails[0].vlrtotal){
							if (modoHabilitado === 'O'){
								ScreenService.showMessage('Não foi realizado nenhum pedido para esta mesa, favor solicitar o fechamento ao garçom.');
								UtilitiesService.backMainScreen();
							} else if (modoHabilitado === 'M' || modoHabilitado === 'C'){
								if (accountDetailsData.AccountGetAccountDetails[0].vlrpago > 0) {
									ScreenService.showMessage('O adiantamento atingiu o valor máximo da conta.', 'alert');
								} else {
									if (modoHabilitado === 'C'){
										ScreenService.showMessage('Não houve pedido para realizar esta operação.', 'alert');
									} else {
										ScreenService.confirmMessage(
											'Não foi realizado nenhum pedido para esta mesa, deseja cancelar a abertura?',
											'question',
											function(){
												TableService.cancelOpen(chave, accountData.NRMESA).then(function(){
													UtilitiesService.backMainScreen();
												}.bind(this));
											}.bind(this),
											function(){}
										);
									}
								}
							}
						} else {
							// fecha mesa para receber os valores certos na pagar conta
							TableService.closeAccount(chave, nrComanda, nrVendaRest, 'M', true, true, true, 0, accountData.NRPESMESAVEN, null, null, 'N', null).then(function(response) {
								TableService.changeTableStatus(chave, nrVendaRest, nrComanda, 'R').then(function(response){
									TableController.openAccountPayment();
								}.bind(this));
								AccountController.handlePrintBill(response.dadosImpressao);
							}.bind(this));
						}
					} else {
						UtilitiesService.backMainScreen();
					}
				}.bind(this));
			}.bind(this));
		});
	};

	this.showDividirProdutos = function(){
		WindowService.openWindow('SPLIT_PRODUCTS_SCREEN');
	};

	this.showCancelarProduto = function(){
		PermissionService.checkAccess('cancelaItemGenerico').then(function (CDSUPERVISOR){
			TableController.showCancelProduct(CDSUPERVISOR);
		});
	};

	this.showAlterarQtPessoas = function(widget){
		PermissionService.checkAccess('alterarQtPessoas').then(function (){
			OperatorRepository.findOne().then(function(operatorData){
				widget.getField('NRPOSICAOMESA').maxValue = operatorData.NRMAXPESMES;
				TableController.showChangePositions(widget);
			});
		});
	};

	this.showAgrupamentos = function(){
		PermissionService.checkAccess('agruparMesas').then(function (){
			WindowService.openWindow('GROUP_TABLE_SCREEN');
		});
	};

	this.showTransferencias = function(){
		PermissionService.checkAccess('transferirProduto').then(function (CDSUPERVISOR){
			TableController.showTransfers(CDSUPERVISOR);
		});
	};

	this.showCancelarAbertura = function (container){
		PermissionService.checkAccess('cancelaMesaComanda').then(function (){
			TableController.cancelOpen(container);
		});
	};

	this.showChangeTable = function(widget){
		OperatorRepository.findAll().then(function(params){
			TableService.getTables(params[0].chave).then(function(result){
				result.TableRepository.forEach(function(res){
					res.mode = 'list';
				});
				widget.dataSource.data = result.TableRepository;
				ScreenService.openPopup(widget);
			});
		});
	};

	this.showReleaseProduct = function(){
		OperatorRepository.findOne().then(function (operatorData){
			AccountController.getAccountData(function (accountData){
				TableService.getDelayedProducts(operatorData.chave, accountData[0].NRVENDAREST, accountData[0].NRCOMANDA).then(function (delayedProducts){
					if (delayedProducts.length > 0) {
						WindowService.openWindow('DELAYED_PRODUCTS_SCREEN');
					} else {
						ScreenService.showMessage('Não existem pedidos para liberar nesta mesa.');
					}
				});
			});
		});
	};

	this.showGenerateCode = function(widget){
		widget.fields[0].dataSource.data = widget.container.getWidget('positionsWidget').dataSource.data;
		widget.fields[0].position = null;
		ScreenService.openPopup(widget);
	};

	this.openTransferProduct = function(widget){
		PermissionService.checkAccess('transferirProduto').then(function(CDSUPERVISOR){
			widget.currentRow.CDSUPERVISOR = CDSUPERVISOR;
			ScreenService.openPopup(widget);
		});
	};

	this.prepareBillListSelect = function(billField) {
		billField.dataSource.data = Array();
		billField.clearValue();
		BillController.getBills(function(comandas) {
			AccountController.getAccountData(function(accountData) {
				_.remove(comandas, function(comanda) {
					return comanda.DSCOMANDA == accountData[0].DSCOMANDA;
				});
				billField.dataSource.data = comandas;
			});
		});
	};

	this.selectComandaProducts = function(productField) {
		productField.dataSource.data = Array();
		productField.clearValue();
		AccountController.getAccountData(function(accountData) {
			AccountService.selectComandaProducts(accountData[0].NRCOMANDA).then(function(result){
				productField.dataSource.data = result;
			}.bind(this));
		});
	};

	this.updateComandaProducts = function(widget) {
		if (!_.isEmpty(widget.currentRow.selectComandas)) {
			if (!_.isEmpty(widget.getField('selectComandaProducts').value())) {
				BillController.getBills(function(comandas) {
					var comanda = _.filter(comandas, function(c){ return (c.DSCOMANDA == widget.currentRow.DSCOMANDA);});
					AccountController.getAccountData(function(accountData) {
						AccountService.updateComandaProducts(accountData[0].NRCOMANDA, accountData[0].NRVENDAREST, comanda[0].NRCOMANDA, comanda[0].NRVENDAREST, widget.currentRow.CDPRODUTO, widget.currentRow.NRPRODCOMVEN, widget.currentRow.CDSUPERVISOR).then(function(result){
							ScreenService.showMessage("Produtos Transferidos para comanda " + widget.currentRow.selectComandas + ".");
							widget.getField('selectComandas').clearValue();
							widget.getField('selectComandaProducts').clearValue();
							ScreenService.goBack();
						}.bind(this));
					}.bind(this));
				}.bind(this));
			} else {
				ScreenService.showMessage("Selecione ao menos um produto.", 'alert');
			}
		} else {
			ScreenService.showMessage("Selecione a comanda de destino.", 'alert');
		}
	};

	this.handleCheckedPromo = function(field, action) {
		_.forEach(field.dataSource, function(produto) {
			if (!!field.selectedRow.CDPRODPROMOCAO) {
			 	if (field.selectedRow.CDPRODPROMOCAO == produto.CDPRODPROMOCAO && field.selectedRow.NRSEQPRODCOM == produto.NRSEQPRODCOM) {
					produto.__isSelected = action;
				}
			}
		});
	};

	this.controlPromo = function(field) {
		selectControl = _.clone(field.dataSource);
		_.remove(selectControl, function(p) {
			return !p.__isSelected;
		});
	};

	this.removePromoItens = function(fieldRow, field) {
		if (!_.isEqual(fieldRow.row.selectComandaProducts, fieldRow.row.VALOR)) {
	        var removed = _.difference(fieldRow.row.VALOR, fieldRow.row.selectComandaProducts);

	        if (!_.isEmpty(removed)) {
				var removedRow = _.filter(selectControl, function(p){ return (p.CDPRODUTO+'-'+p.NRSEQPRODCOM == _.head(removed));});
				var toRemove = _.remove(selectControl, function(p){
					if (!_.isEmpty(p.CDPRODPROMOCAO) && (!_.isEmpty(removedRow)))
						return (p.CDPRODPROMOCAO == removedRow[0].CDPRODPROMOCAO && p.NRSEQPRODCOM == removedRow[0].NRSEQPRODCOM);
				});

				var valueField = _.clone(field.value());

				if (!_.isEmpty(toRemove)){
					_.forEach(toRemove, function(r){
						_.remove(fieldRow.row.VALOR, function(p){ return (p == r.VALOR);});
					});
				} else {
					_.remove(fieldRow.row.VALOR, function(p){ return (p == removed);});
				}

				_.forEach(toRemove, function(value) {
				  _.remove(valueField, function(p){
				  	return p == value.VALOR;
				  });
				});

				field.value(valueField);
	        }
		}
	};

}

Configuration(function(ContextRegister){
	ContextRegister.register('MenuFunctionsController', MenuFunctionsController);
});

// FILE: js/controllers/OrderDeliveryController.js
function OrderDeliveryController(DeliveryService, ScreenService, WindowService, ParamsPriceChart,
                                 UtilitiesService, OperatorRepository) {
    const ERROR_CUPOM_FISCAL = 'Operação não permitida. Este cupom já foi impresso.';
    const PAYMENT_COMPLETED = 'Venda realizada. ';
    const NFCE_CONTINGENCY = 'NFCE emitido em modo de contigência.';

    const self = this;
    var pagamentos = {};

    this.checkOrder = function(widget){
        setLocalVar("saleCode", new Date().getTime());
        pagamentos = {};
    };

    this.geraNotaFiscal = function(widget){
        ScreenService.confirmMessage("Deseja imprimir o cupom fiscal do pedido selecionado?",'question',
            function(success){
                var currentRow = widget.currentRow;
                var nrvendarest = currentRow.NRVENDAREST;
                var cdfilial = currentRow.CDFILIAL; 
                var status = currentRow.IDSTCOMANDA;
                var nrcomanda = currentRow.DSCOMANDA;
                var saleCode = getLocalVar('saleCode');
                self.getInfoFormasPagamento(widget.container.getWidget('formaPagamentoPopup'));
                var datasale = widget.container.getWidget('formaPagamentoPopup').dataSource.data;
                if(status == 'P'){
                    ScreenService.showMessage(ERROR_CUPOM_FISCAL);
                }else if(currentRow.DATASALE == '' || currentRow.DATASALE.TOTAL > currentRow.DATASALE.PAGO){
                    ScreenService.openPopup(widget.container.getWidget('formaPagamentoPopup'));
                    ScreenService.showMessage('Informe a forma de pagamento');
                }else{
                    DeliveryService.generatePayment(cdfilial, nrvendarest, status, saleCode, datasale, nrcomanda).then(function(response){
                        if(!response[0].error){
                            WindowService.openWindow('DELIVERY_ORDERS_SCREEN').then(function(){
                                var message = PAYMENT_COMPLETED;
                                if (_.get(response[0], 'IDSTATUSNFCE') === 'P') {
                                    message += '<br><br>' + NFCE_CONTINGENCY;
                                }
                                if (_.get(response[0], 'mensagemNfce')) {
                                    var retornoNfce = _.get(response[0], 'mensagemNfce');
                                    if (!~retornoNfce.indexOf("A - ")){
                                        message += '<br>' + _.get(response[0], 'mensagemNfce');
                                    }
                                }
                                if (_.get(response[0], 'mensagemImpressao')) {
                                    message += '<br><br>' + _.get(response[0], 'mensagemImpressao');
                                }
                                if(_.get(response[0], 'errorDlv')){
                                    message += '<br><br>'+_.get(response[0],'messageDlv');
                                }
                                ScreenService.showMessage(message);
                            });
                        }else{
                            ScreenService.showMessage(response[0].message);
                            setLocalVar("saleCode", new Date().getTime());
                        }
                    });
                }
            }
        );
    };

    this.getInfoFormasPagamento = function(widget){
        var widgetOrder = widget.container.getWidget('order');
        //Se o pedido já tiver sido finalizado, não é possivel alterar suas informações de pagamento.
        var comandaImpresso = widgetOrder.currentRow.IDSTCOMANDA != 'P';
        var DATASALE = widgetOrder.currentRow.DATASALE;
        var PRODUTOS = widgetOrder.currentRow.PRODUTOS;
        self.changeActionsDlv(widget, comandaImpresso);
        DATASALE.hasChanged = false;
        DATASALE.TOTAL = parseFloat(widgetOrder.currentRow.VRACRCOMANDA);
        PRODUTOS.forEach(function(produtos){
            DATASALE.TOTAL += parseFloat(produtos.VRPRECCOMVENTOTAL);
        });
        DATASALE.PAGO = 0;
        DATASALE.forEach(function(datasale){
            DATASALE.PAGO += parseFloat(datasale.VRMOVIVENDDLV);
        });
        DATASALE.FALTANTE = DATASALE.TOTAL - DATASALE.PAGO;
        DATASALE.FALTANTE = DATASALE.FALTANTE > 0 ? DATASALE.FALTANTE : 0;
        DATASALE.TROCO = DATASALE.PAGO - DATASALE.TOTAL;
        DATASALE.TROCO = DATASALE.TROCO > 0? DATASALE.TROCO : 0;

        DATASALE.TROCOCURRENCY = UtilitiesService.toCurrency(DATASALE.TROCO);
        DATASALE.FALTANTECURRENCY = UtilitiesService.toCurrency(DATASALE.FALTANTE);
        DATASALE.PAGOCURRENCY = UtilitiesService.toCurrency(DATASALE.PAGO);
        DATASALE.TOTALCURRENCY = UtilitiesService.toCurrency(DATASALE.TOTAL);
        widget.label = 'Formas de Pagamento  -  Total da Comanda: R$'+DATASALE.TOTALCURRENCY;
        widget.dataSource.data = DATASALE;
        if(widget.getField('CDTIPORECE') != 1 || widget.getField('CDTIPORECE') != 2 || widget.getField('CDTIPORECE') != 316 || widget.getField('CDTIPORECE') != 312 || widget.getField('CDTIPORECE') != 300 || widget.getField('CDTIPORECE') != 5 || widget.getField('CDTIPORECE') != 4){
            widget.getField('VRMOVIVENDDLV').range.max = DATASALE.FALTANTE;
        }

        //Backup dos pagamentos antes de modificacoes.
        if(Object.getOwnPropertyNames(pagamentos).length == 0){
            pagamentos = angular.copy(widget.container.getWidget('order').currentRow.DATASALE);
        }
    };

    this.getInfoFooterPayment = function(widget){
        widget.currentRow = widget.container.getWidget('order').currentRow.DATASALE;
        if(widget.currentRow.FALTANTE>0){
            widget.getField('TROCOCURRENCY').isVisible = false;
            widget.getField('FALTANTECURRENCY').isVisible = true;
        }else{
            widget.getField('TROCOCURRENCY').isVisible = true;
            widget.getField('FALTANTECURRENCY').isVisible = false;
        }
    };

    this.getInfoProdutosDlv = function(widget){ 
        var widgetOrder = widget.container.getWidget('order');
        var acrescimoComanda = parseFloat(widgetOrder.currentRow.VRACRCOMANDA);
        //Se o pedido já tiver sido finalizado, não é possivel alterar suas informações da comanda.
        var status = widgetOrder.currentRow.IDSTCOMANDA == 'P';
        self.changeActionsDlv(widget, status);

        widget.dataSource.data = widgetOrder.currentRow.PRODUTOS;
        totalProdutosFooter = 0;    
        widget.dataSource.data.forEach(function(produto){
            totalProdutosFooter += parseFloat(produto.VRPRECCOMVENTOTAL);
        });
        totalProdutosFooter += acrescimoComanda;
        totalProdutosFooter = UtilitiesService.toCurrency(totalProdutosFooter);
        acrescimoComanda = UtilitiesService.toCurrency(acrescimoComanda);
        widget.container.getWidget('produtosFooter').getField('TOTALPRODUTOS').value(totalProdutosFooter);
        widget.container.getWidget('produtosFooter').getField('ACRESCIMOCOMANDA').value(acrescimoComanda);
    };

    this.changeActionsDlv = function(widget, status){
        if(widget.name == 'formaPagamentoPopup'){
            widget.getAction('addPayment').isVisible = status;
            widget.getAction('deletePayment').isVisible = status;
            widget.getAction('Confirmar').isVisible = status;
        }else if(widget.name == 'produtosPopup'){
            widget.getAction('deleteProduct').isVisible = status;
        }
    };

    this.getInfoPagamento = function(widget){
        widget.currentRow = widget.container.getWidget('order').currentRow.DATASALE; 
        widget.currentRow.VRMOVIVENDDLV = parseFloat(widget.container.getWidget('order').currentRow.DATASALE.FALTANTE);
    };
    
    this.getPaymentTypes = function(args){
        var field = args.owner;
        ParamsPriceChart.findAll().then(function(paymentTypes){
            field.field.dataSource.data = paymentTypes;
        }.bind(this));
    };

    this.adicionarFormaPagamento = function(widget){
        var widgetPagamentos = widget.container.getWidget('formaPagamentoPopup');
        var pagamento = {
            'CDTIPORECE': widget.currentRow.CDTIPORECE,
            'NMTIPORECE': widget.currentRow.DSBUTTON,
            'VRMOVIVENDDLV': UtilitiesService.removeCurrency(widget.currentRow.VRMOVIVENDDLV),
            'NRSEQMOVDLV': new Date().getTime()
        };
        if(widget.isValid()){
            widgetPagamentos.dataSource.data.push(pagamento);
            widget.container.getWidget('order').currentRow.DATASALE = widgetPagamentos.dataSource.data;
            ScreenService.closePopup();
        }
    };

    this.deletarFormaPagamento = function(widget){
        var widgetPagamentos = widget.container.getWidget('formaPagamentoPopup');
        widgetPagamentos.dataSource.data = widgetPagamentos.dataSource.data.filter(function(recebimento){
            return recebimento.NRSEQMOVDLV != widget.getField("NRSEQMOVDLV").value();
        });
        widget.container.getWidget('order').currentRow.DATASALE = widgetPagamentos.dataSource.data;
       self.getInfoFormasPagamento(widget);
    };

    this.salvarFormaPagamento = function(widget){
        var params = {};
        params.NRVENDAREST = widget.container.getWidget('order').getField('NRVENDAREST').value();
        params.RECEBIMENTOS = widget.dataSource.data;
        if(widget.dataSource.data.PAGO >= widget.dataSource.data.TOTAL){
            DeliveryService.saveMovcaixadlv(params).then(function(result){
                pagamentos = angular.copy(widget.container.getWidget('order').currentRow.DATASALE);
                ScreenService.closePopup();
            });
        }else{
            ScreenService.showMessage('Valor total da comanda não alcançado. Valor a Pagar: R$'+UtilitiesService.toCurrency(widget.dataSource.data.FALTANTE));
        }
    };

    this.backPayments = function(widget){
        widget.container.getWidget('order').currentRow.DATASALE = angular.copy(pagamentos);
        ScreenService.closePopup();
    };

    this.abrirPopupNovoPagamento = function (args){
        if(args.owner.widget.dataSource.data.FALTANTE <= 0){
            ScreenService.showMessage('Valor total da comanda já atingido.');
        }else{
            ScreenService.openPopup(args.owner.widget.container.getWidget('novoPagamentoPopup'));
        }
    };

    this.cancelarNovaFormaPagamento = function(widget){
        widget.currentRow.DSBUTTON = '';
        widget.currentRow.VRMOVIVENDDLV = '';
        ScreenService.closePopup();
    };

    this.reprint = function(reprintWidget){
        var orderWidget = reprintWidget.parent;
        switch (reprintWidget.currentRow.name) {
            case "reprintDlv": self.printDeliveryRow(orderWidget); break;
            case "reprintCupomF": self.printDeliveryRowCf(orderWidget); break;
            default: break;
        }
        ScreenService.closePopup();
    };

    this.printDeliveryRow = function(widget){
        var order = [{
            'CDFILIAL': widget.currentRow.CDFILIAL,
            'CDLOJA': widget.currentRow.CDLOJA,
            'NRVENDAREST': widget.currentRow.NRVENDAREST,
            'CDCAIXA': widget.currentRow.CDCAIXA
        }];
        DeliveryService.printDelivery(order).then(function(data){
            if(!data[0].error){
                ScreenService.showMessage('Relatório de entrega impresso com sucesso.');
            }else{
                ScreenService.showMessage('Houve um problema com a impressão do relatório de entrega.', 'ERROR');
            }
        }).catch(function (error) {
            ScreenService.showMessage(error);
        });
    };

    this.confirmDeleteProduct = function(widget){
        OperatorRepository.findOne().then(function (operatorParams){
            if (operatorParams.IDINFPRODPRODUZ == 'S') {
                ScreenService.confirmMessage('O produto selecionado já foi produzido?', 'question', function () {
                    self.deletarProduto(widget, operatorParams.CDOPERADOR, 'S', operatorParams.CDFILIAL);
                }, function () {
                    self.deletarProduto(widget, operatorParams.CDOPERADOR, null, operatorParams.CDFILIAL);
                });
            } else {
                self.deletarProduto(widget, operatorParams.CDOPERADOR, null, operatorParams.CDFILIAL);
            }
        });
    };

    this.deletarProduto = function(widget, CDOPERADOR, IDPRODPRODUZ, CDFILIAL){
        var widgetOrder = widget.container.getWidget('order');
        var params = widgetOrder.currentRow;
        params.CDOPERADOR = CDOPERADOR;
        params.IDPRODPRODUZ = IDPRODPRODUZ;
        params.CDFILIAL = CDFILIAL;
        params.saleCode = getLocalVar('saleCode');
        //temporario
        widget.currentRow.composicao = null;
        params.product = {
            'NRVENDAREST': widget.currentRow.NRVENDAREST, 
            'nrcomanda': widget.currentRow.DSCOMANDA, 
            'NRPRODCOMVEN': widget.currentRow.NRPRODCOMVEN, 
            'CDPRODPROMOCAO': widget.currentRow.CDPRODPROMOCAO, 
            'NRSEQPRODCOM': widget.currentRow.NRSEQPRODCOM, 
            'NRSEQPRODCUP': widget.currentRow.NRSEQPRODCUP, 
            'codigo': widget.currentRow.CDPRODUTO, 
            'quantidade': widget.currentRow.QTPRODCOMVEN, 
            'composicao': widget.currentRow.composicao
        };
        params.motivo = [];
        params.motivo.push('Cancelamento Delivery');
        ScreenService.confirmMessage("Deseja cancelar o produto selecionado?",'question',
            function(success){
                DeliveryService.deletarProduto(params).then(function(data){
                    if(!data[0].error){
                        if(data[0].funcao == 1){
                            pagamentos = {};
                            widgetOrder.currentRow.DATASALE = [];
                            self.getInfoFormasPagamento(widget.container.getWidget('formaPagamentoPopup'));
                            ScreenService.showMessage('Produto cancelado com sucesso.');
                        }else{
                            ScreenService.showMessage(data[0].message);
                        }
                        if(data[0].products.length > 0){
                            widgetOrder.currentRow.PRODUTOS = data[0].products;
                            self.getInfoProdutosDlv(widget);

                        }else{
                             ScreenService.showMessage('Todos os produtos foram cancelados. O pedido será cancelado.').then(function() {
                                 self.cancelarPedido(widgetOrder, CDOPERADOR, IDPRODPRODUZ, CDFILIAL, true);
                             });
                        }
                    }else{
                        ScreenService.showMessage('Houve um problema com o cancelamento do produto.', 'ERROR');
                    }
                }).catch(function (error) {
                    ScreenService.showMessage(error);
                });
            }
        );  
    };

    this.cancelProductionOrder = function(widget){
        OperatorRepository.findOne().then(function (operatorParams){
            if (operatorParams.IDINFPRODPRODUZ == 'S') {
                ScreenService.confirmMessage('O pedido selecionado já foi produzido?', 'question', function () {
                    self.cancelarPedido(widget, operatorParams.CDOPERADOR, 'S', operatorParams.CDFILIAL, false);
                }, function () {
                    self.cancelarPedido(widget, operatorParams.CDOPERADOR, null, operatorParams.CDFILIAL, false);
                });
            } else {
                self.cancelarPedido(widget, operatorParams.CDOPERADOR, null, operatorParams.CDFILIAL, false);
            }
        });
    };

    this.cancelarPedido = function(widget, CDOPERADOR, IDPRODPRODUZ, CDFILIAL, CANCELADIRETO){
        var params = widget.currentRow;
        params.saleCode = getLocalVar('saleCode');
        params.motivo = [];
        params.motivo.push('Cancelamento Delivery');
        params.operador = CDOPERADOR;
        params.IDPRODPRODUZ = IDPRODPRODUZ;
        params.CDFILIAL = CDFILIAL;
        params.nrvendarest= params.NRVENDAREST;
        params.status= params.DSCOMANDA;
        ScreenService.closePopup();
        if(CANCELADIRETO){
            DeliveryService.cancelarPedido(params).then(function(data){
                if(!data[0].error){
                    ScreenService.goBack().then(function(){
                        ScreenService.showMessage('Pedido cancelado com sucesso.');
                    });
                }else{
                    ScreenService.closePopup().then(function(){
                        ScreenService.showMessage('Houve um problema com o cancelamento do produto.', 'ERROR');
                    });
                }
            }).catch(function (error) {
                ScreenService.showMessage(error);
            });
        }else{
            ScreenService.confirmMessage("Deseja cancelar o pedido selecionado?",'question',
                function(success){
                    DeliveryService.cancelarPedido(params).then(function(data){
                        if(!data[0].error){
                            ScreenService.goBack().then(function(){
                                ScreenService.showMessage('Pedido cancelado com sucesso.');
                            });
                        }else{
                            ScreenService.showMessage('Houve um problema com o cancelamento do produto.', 'ERROR');
                        }
                    }).catch(function (error) {
                        ScreenService.showMessage(error);
                    });
                }
            );  
        }
    };

    this.printDeliveryRowCf = function(widget){
        var order = [{
            'CDFILIAL': widget.currentRow.CDFILIAL,
            'CDLOJA': widget.currentRow.CDLOJA,
            'NRVENDAREST': widget.currentRow.NRVENDAREST,
            'NRSEQVENDA': widget.currentRow.NRSEQVENDA,
            'CDCAIXA': widget.currentRow.CDCAIXA
        }];
        var param = [];
        param.push(order);
        DeliveryService.reprintDeliveryCupomFiscal(param).then(function(data){
            if(!data[0].error){
                ScreenService.showMessage('Cupom Fiscal impresso com sucesso.');
            }else{
                ScreenService.showMessage(data[0].message, 'ERROR');
            }
        }).catch(function (error) {
            ScreenService.showMessage(error);
        });
    };

    this.concludeOrder = function(widget){
        DeliveryService.concludeOrderDlv(widget.currentRow).then(function(data){
            if(!data[0].error){
                ScreenService.goBack();
                ScreenService.showMessage('Pedido finalizado com sucesso.');
            }else{
                ScreenService.showMessage(data[0].message, 'ERROR');
            }
        }).catch(function (error) {
            ScreenService.showMessage(error);
        });
    };

}

Configuration(function(ContextRegister) {
    ContextRegister.register('OrderDeliveryController', OrderDeliveryController);
});

// FILE: js/controllers/PaymentController.js
function PaymentController(ScreenService, UtilitiesService, PaymentService, AccountController, PaymentRepository, IntegrationService, ParamsClientRepository, GetConsumerLimit, Query,
	PermissionService, OperatorRepository, PrinterService, ParamsGroupPriceChart, ParamsPriceChart, GroupPriceChart, PriceChart, AccountService, WindowService, TableController, CarrinhoDesistencia,
	PerifericosService, ProdSenhaPed) {

	// define por IDTIPORECE se pagamento tem valor máximo e se possibilita editar valor de pagamento
	var PAYMENT_TYPE = {
		'1': { max: true },
		'2': { max: true },
		'3': { max: false },
		'4': { max: false },
		'5': { max: false },
		'6': { max: false },
		'7': { max: false },
		'8': { max: false },
		'9': { max: true },
		'A': { max: true },
		'B': { max: false },
		'C': { max: false },
		'E': { max: true },
		'F': { max: true },
		'G': { max: true },
		'H': { max: true }
	};
	var MESSAGE = {
		VR_MIN: 'Valor inválido.',
		VR_MAX: 'Valor máximo excedido.',
		VR_MAX_DISCOUNT: 'Operação bloqueada. Valor máximo para desconto é de R$',
		END_PAYMENT: 'Deseja finalizar a venda?',
		ADD_PAYMENT: 'Recebimento adicionado.',
		ATT_PAYMENT: 'Recebimento alterado.',
		FAIL_PAYMENT: 'Operação bloqueada. Valor total da venda não atingido.',
		BLOCK_PAYMENT: 'Operação bloqueada. Valor total da venda já atingido.',
		REMOVE_PAYMENT: 'Deseja remover o pagamento?',
		PAYMENT_COMPLETED: 'Venda realizada. ',
		CANCEL_INTEGRATION: 'Transações eletrônicas pendentes. Deseja cancelá-las?',
		NFCE_CONTINGENCY: 'NFCE emitido em modo de contigência.',
		VR_MAX_SALDO: 'O cliente não possui saldo disponivel.',
		INFORM_CLIENT: 'Favor selecionar o consumidor antes de receber com crédito pessoal.',
		VR_SALDO_LIM: 'Operação bloqueada. O limite de crédito do consumidor será excedido. Consumo diário disponível: R$ ',
		LOW_CREDIT: 'O saldo restante deste consumidor é de: R$ ',
		NO_DISCOUNT: 'Não há desconto a ser removido.',
		NO_CREDIT: "Consumidor sem saldo disponível.",
		REMOVE_DISCOUNT: 'Deseja remover desconto já aplicado?',
		BLOCK_DISCOUNT: 'Operação bloqueada. Não é possível informar desconto enquanto existir recebimentos aplicados.',
		INATIVE_CONSUMER: 'Operação bloqueada. O Consumidor está inativo.',
		CONFIRM_PRINT: 'Deseja imprimir o cupom fiscal?',
		NO_ADDITION: 'Operação bloqueada. Esta venda não possui Gorjeta.',
		ALTER_ADDITION: 'Deseja ajustar a Gorjeta?',
		PRINT_VIA_CLI: 'Deseja imprimir a via do cliente?',
		NOT_ENOUGH_DEBIT: 'Saldo insuficiente para realizar a venda somente via débito consumidor. Favor complementar com outro tipo de recebimento.<br><br>Limite disponível: R$ ',
		NOT_ONLY_DEBIT: 'Operação bloqueada. Débito Consumidor não pode ser utilizado com outros tipos de recebimento.',
		BLOCK_PERSONAL_CREDIT: 'Operação bloqueada. Este tipo de recebimento não pode efetuar compra de crédito pessoal.',
		BLOCK_CREDIT_MULTIPLE: 'Crédito Fidelidade não pode ser utilizado junto com Crédito Pessoal.',
		DEBIT_WITH_DISCOUNT: 'Não é possível realizar uma venda de débito com desconto aplicado. Favor remover o desconto aplicado e tente novamente.'
	};

	var self = this;

	this.receivePayment = function (widget, tiporece) {
		PaymentRepository.findOne().then(function (paymentData) {
			var toPay = paymentData.DATASALE.FALTANTE;

			if (!toPay) {
				ScreenService.showMessage(MESSAGE.BLOCK_PAYMENT, 'alert');
			} else {
				if (!paymentData.CREDITOPESSOAL || !_.includes(["A", "9"], tiporece.IDTIPORECE)) {
					switch (tiporece.IDTIPORECE) {
						// debito pessoal
						case 'A':
							self.receivePersonalDebit(paymentData, widget, tiporece, toPay);
							break;
						// credito pessoal
						case '9':
							self.receivePersonalCredit(paymentData, widget, tiporece, toPay);
							break;
						default:
							self.openPaymentPopup(widget.container.getWidget('paymentPopup'), tiporece, toPay, false);
					}
				} else {
					ScreenService.showMessage(MESSAGE.BLOCK_PERSONAL_CREDIT);
				}
			}
		});
	};

	this.receivePersonalDebit = function (paymentData, widget, tiporece, toPay) {
		if (!paymentData.CDCLIENTE || !paymentData.CDCONSUMIDOR) {
			ScreenService.showMessage(MESSAGE.INFORM_CLIENT, 'alert');
			return;
		}
		if (!_.isEmpty(paymentData.TIPORECE)) {
			ScreenService.showMessage(MESSAGE.NOT_ONLY_DEBIT, 'alert');
			return;
		}
		if (paymentData.DATASALE.PCTDESCONTO > 0 || paymentData.DATASALE.VRDESCONTO > 0) {
			ScreenService.showMessage(MESSAGE.DEBIT_WITH_DISCOUNT, 'alert');
			return;
		}

		PaymentService.getConsumerLimit(paymentData.CDCLIENTE, paymentData.CDCONSUMIDOR, 'debito').then(function (consumerLimit) {
			var SALDO_ATUAL = consumerLimit[0].SALDO_ATUAL;
			var LIMITE_ATUAL = consumerLimit[0].LIMITE_ATUAL;
			var CONSUMO_DIA = consumerLimit[0].CONSUMO_DIA;
			var CONSUMO_MES = consumerLimit[0].CONSUMO_MES;
			var VRLIMDEBCONS = consumerLimit[0].VRLIMDEBCONS;
			var VRMAXDEBCONS = consumerLimit[0].VRMAXDEBCONS;
			var VRMAXCONSDIAD = consumerLimit[0].VRMAXCONSDIAD;
			var VRMAXCONSMESD = consumerLimit[0].VRMAXCONSMESD;
			var VRAVIDEBCONS = consumerLimit[0].VRAVIDEBCONS;
			var IDPERCOMVENCPDC = consumerLimit[0].IDPERCOMVENCPDC;

			var totalDebito = parseFloat((paymentData.DATASALE.TOTALVENDA - paymentData.DATASALE.REALSUBSIDY).toFixed(2));

			if (!IDPERCOMVENCPDC && VRMAXCONSMESD && totalDebito + CONSUMO_MES > VRMAXCONSMESD) {
				ScreenService.showMessage('Operação bloqueada. O consumidor ' + consumerLimit[0].CDCONSUMIDOR + ' - ' + consumerLimit[0].NMCONSUMIDOR + ' não pode exceder o valor máximo de consumo mensal por tipo de consumidor/loja - R$ ' + UtilitiesService.toCurrency(VRMAXCONSMESD) + '. Favor verificar parametrização no sistema.');
			}
			else if (!IDPERCOMVENCPDC && VRMAXCONSDIAD && totalDebito + CONSUMO_DIA > VRMAXCONSDIAD) {
				ScreenService.showMessage('Operação bloqueada. O consumidor ' + consumerLimit[0].CDCONSUMIDOR + ' - ' + consumerLimit[0].NMCONSUMIDOR + ' não pode exceder o valor máximo de consumo diário por tipo de consumidor/loja - R$ ' + UtilitiesService.toCurrency(VRMAXCONSDIAD) + '. Favor verificar parametrização no sistema.');
			}
			else if (!IDPERCOMVENCPDC && VRMAXDEBCONS && totalDebito + CONSUMO_DIA > VRMAXDEBCONS) {
				ScreenService.showMessage('Operação bloqueada. O consumidor ' + consumerLimit[0].CDCONSUMIDOR + ' - ' + consumerLimit[0].NMCONSUMIDOR + ' não pode exceder o valor de consumo diário - R$ ' + UtilitiesService.toCurrency(VRMAXDEBCONS) + '. Favor verificar parametrização no sistema.');
			}
			else if (!IDPERCOMVENCPDC && VRLIMDEBCONS && SALDO_ATUAL - totalDebito < VRLIMDEBCONS) {
				ScreenService.showMessage('Operação bloqueada. Saldo do consumidor não pode ficar inferior a R$ ' + UtilitiesService.toCurrency(VRLIMDEBCONS) + '. Saldo atual : R$ ' + UtilitiesService.toCurrency(SALDO_ATUAL) + '.', 'alert');
			}
			else {
				var toPay = paymentData.DATASALE.TOTALVENDA;
				if (LIMITE_ATUAL != null) {
					var paymentDiff = parseFloat((LIMITE_ATUAL - toPay + paymentData.DATASALE.REALSUBSIDY).toFixed(2));
					if (paymentDiff < 0) {
						toPay = LIMITE_ATUAL;
						ScreenService.showMessage(MESSAGE.NOT_ENOUGH_DEBIT + UtilitiesService.toCurrency(LIMITE_ATUAL), 'alert');
					}
				}

				if (toPay > 0) {
					self.openPaymentPopup(widget.container.getWidget('paymentPopup'), tiporece, toPay, true);
				}
			}
		});
	};

	this.receivePersonalCredit = function (paymentData, widget, tiporece, toPay) {
		if (paymentData.DATASALE.FIDELITYDISCOUNT > 0) {
			ScreenService.showMessage(MESSAGE.BLOCK_CREDIT_MULTIPLE, 'alert');
			return;
		}
		if (paymentData.CDCLIENTE && paymentData.CDCONSUMIDOR) {
			PaymentService.getConsumerLimit(paymentData.CDCLIENTE, paymentData.CDCONSUMIDOR, 'credito').then(function (consumerLimit) {
				var limiteDisponivel = consumerLimit[0].limiteDisponivel;
				if (limiteDisponivel && limiteDisponivel - paymentData.DATASALE.TOTALVENDA < 0) {
					ScreenService.showMessage(MESSAGE.VR_SALDO_LIM + UtilitiesService.toCurrency(limiteDisponivel), 'alert');
				}
				else if (consumerLimit[0].saldoDisponivel <= 0) {
					ScreenService.showMessage(MESSAGE.NO_CREDIT);
				}
				else {
					var saldoDisponivel = consumerLimit[0].saldoDisponivel;
					if (saldoDisponivel < paymentData.DATASALE.FALTANTE) {
						toPay = saldoDisponivel;
						ScreenService.showMessage(MESSAGE.LOW_CREDIT + UtilitiesService.toCurrency(saldoDisponivel), 'alert');
					}
					self.openPaymentPopup(widget.container.getWidget('paymentPopup'), tiporece, toPay, false);
				}
			});
		} else {
			ScreenService.showMessage(MESSAGE.INFORM_CLIENT);
		}
	};

	this.openPaymentPopup = function (paymentPopup, tiporece, toPay, locked) {
		self.setPaymentPopupProperty(paymentPopup, tiporece, toPay, locked).then(function () {
			ScreenService.openPopup(paymentPopup);
		}.bind(this));
	};

	this.setPaymentPopupProperty = function (openPopup, tiporece, toPay, locked) {
		var fieldValue = openPopup.getField('VRMOVIVEND');
		var fieldNSU = openPopup.getField('CDNSUHOSTTEF');

		openPopup.label = tiporece.DSBUTTON;
		openPopup.currentRow = self.currentRowDefaultValue(tiporece);
		fieldValue.range.max = (PAYMENT_TYPE[tiporece.IDTIPORECE].max) ? toPay : null;
		fieldValue.setValue(toPay);
		fieldValue.readOnly = locked;

		return OperatorRepository.findOne().then(function (operatorData) {
			fieldNSU.maxlength = operatorData.QTDMAXDIGNSU || 10;

			if (!self.showFieldNSU(tiporece, operatorData)) {
				fieldValue.class = "6 center-align-field";
				fieldNSU.isVisible = false;
			} else {
				fieldValue.class = 6;
				fieldNSU.isVisible = true;
			}
		}.bind(this));
	};

	this.currentRowDefaultValue = function (tiporece) {
		return {
			tiporece: tiporece,
			eletronicTransacion: { status: false, data: IntegrationService.integrationData() }
		};
	};

	this.showFieldNSU = function (tiporece, operatorData) {
		// validação para mostrar field NSU nos recebimentos do tipo POS
		return tiporece.IDDESABTEF === 'S' && operatorData.IDSOLICITANSU === 'S' &&
			_.includes(PaymentService.PAYMENT_INTEGRATION, tiporece.IDTIPORECE);
	};

	this.setPayment = function (widget) {
		var currentRow = _.clone(widget.currentRow);
		var widgetPayment = widget.container.getWidget('paymentMenu');

		if (widget.isValid()) {
			currentRow.VRMOVIVEND = UtilitiesService.getFloat(currentRow.VRMOVIVEND);
			widget.getField('VRMOVIVEND').setValue(currentRow.VRMOVIVEND);
			currentRow.eletronicTransacion.data.CDNSUHOSTTEF = currentRow.CDNSUHOSTTEF;

			if (self.validValue(widget.getField('VRMOVIVEND'), '')) {
				ScreenService.showLoader();
				PaymentService.handlePayment(currentRow).then(function (handlePaymentResult) {
				    console.log("Result do Payment:");
				    console.log(handlePaymentResult);
					ScreenService.hideLoader();
					if (!handlePaymentResult.error) {
						self.paymentFinish(widgetPayment, handlePaymentResult.data);
					} else {
						self.handleSetPaymentError(handlePaymentResult);
					}
				}.bind(this));
			}
		}
	};

	this.handleSetPaymentError = function (handlePaymentResult) {
		ScreenService.showMessage(handlePaymentResult.message, 'alert').then(function () {
			handlePaymentResult = handlePaymentResult.data;

			if (!_.isEmpty(handlePaymentResult) && handlePaymentResult.IDTPTEF === '5' && handlePaymentResult.errorCode === -43) {
				GeneralFunctions.sendSitefLog();
			}
		}.bind(this));
	};

	this.handleConsumerDebit = function (widget) {
		if (widget.currentRow.tiporece.IDTIPORECE == 'A') {
			GetConsumerLimit.findOne().then(function (limits) {
				var leftover = parseFloat((limits.SALDO_ATUAL - widget.currentRow.VRMOVIVEND).toFixed(2));
				if (limits.VRAVIDEBCONS && leftover <= limits.VRAVIDEBCONS) {
					ScreenService.showMessage('Consumidor : ' + limits.NMCONSUMIDOR + '<br>Saldo Empresa : R$ ' + UtilitiesService.toCurrency(leftover) + '<br>Favor comprar novos créditos.', 'alert').then(function () {
						self.setPayment(widget);
					});
				}
				else {
					self.setPayment(widget);
				}
			});
		}
		else {
			self.setPayment(widget);
		}
	};

	this.validValue = function (field, customMessage) {
		var value = field.value();
		value = UtilitiesService.removeCurrency(value);
		var min = field.range.min;
		var max = field.range.max;

		if ((value < min) || isNaN(value)) {
			ScreenService.showMessage(MESSAGE.VR_MIN, 'alert');
			return false;
		} else if (typeof max === 'number') {
			if (value > max) {
				ScreenService.showMessage(_.isEmpty(customMessage) ? MESSAGE.VR_MAX : customMessage, 'alert');
				return false;
			}
		}

		return true;
	};

	this.paymentFinish = function (widgetPayment, DATASALE) {
		self.attStripeData(widgetPayment);
		ScreenService.closePopup();

		if (!DATASALE.FALTANTE && !DATASALE.TROCO) {
			self.verifyFinishPayment(widgetPayment.container.getWidget('consumerCPFPopup'));
		}
	};

	this.verifyFinishPayment = function (widget) {
		PaymentService.getPaymentValue().then(function (DATASALE) {
			if (!DATASALE.FALTANTE) {
				PaymentRepository.findOne().then(function (payment) {
					if (!payment.CREDITOPESSOAL) {
						self.payAccount();
					}
					else {
						PaymentService.chargePersonalCredit(payment).then(function (paymentResult) {
							if (paymentResult[0].dadosImpressao != null) {
								PrinterService.printerCommand(PrinterService.TEXT_COMMAND, paymentResult[0].dadosImpressao.RECEIPT);
								PrinterService.printerSpaceCommand();
								PrinterService.printerInit().then(function (result) {
									if (result.error)
										ScreenService.alertNotification(result.message);
								});
							}
							PaymentRepository.clearAll().then(function () {
								AccountController.finishPayAccount();
							});
						});
					}
				}.bind(this));
			} else {
				ScreenService.showMessage(MESSAGE.FAIL_PAYMENT, 'alert');
			}
		});
	};

	this.prepareCPFPopup = function (consumerCPFPopup) {
		OperatorRepository.findOne().then(function (operatorData) {
			PaymentRepository.findOne().then(function (paymentData) {
				if (operatorData.IDSOLICITACPF === 'S' || operatorData.IDCOLETOR !== 'C' || operatorData.IDSOLDIGCONS === 'S') {
					consumerCPFPopup.container.getWidget('paymentMenu').getAction('info').isVisible = true;
					consumerCPFPopup.getField('NRINSCRCONS').isVisible = operatorData.IDSOLICITACPF === 'S';
					consumerCPFPopup.getField('EMAIL').isVisible = operatorData.IDCOLETOR !== 'C';
					consumerCPFPopup.getField('NOMECONS').isVisible = operatorData.IDSOLDIGCONS === 'S';
					consumerCPFPopup.getField('NMFANVEN').isVisible = (operatorData.modoHabilitado === 'B' && operatorData.IDUTLSENHAOPER === 'C');
					consumerCPFPopup.getField("DSOBSFINVEN").isVisible = operatorData.IDSOLOBSFINVEN === 'S';

					consumerCPFPopup.currentRow.NRINSCRCONS = paymentData.NRINSCRCONS;
					consumerCPFPopup.currentRow.EMAIL = paymentData.EMAIL;
					consumerCPFPopup.currentRow.NOMECONS = paymentData.NOMECONS;
					consumerCPFPopup.currentRow.CDVENDEDOR = paymentData.CDVENDEDOR;
					consumerCPFPopup.currentRow.NMFANVEN = paymentData.NMFANVEN;
					consumerCPFPopup.currentRow.DSOBSFINVEN = paymentData.DSOBSFINVEN;

					ScreenService.openPopup(consumerCPFPopup);
				} else {
					consumerCPFPopup.container.getWidget('paymentMenu').getAction('info').isVisible = false;
				}
			});
		});
	};

	this.handleShowPaymentList = function (widget) {
		PaymentService.findAllPayment().then(function (arrTiporece) {
			if (!_.isEmpty(arrTiporece)) {
				self.handlePaymentScreen(widget, 'paymentList');
			} else {
				ScreenService.showMessage('Lista de recebimentos está vazia.', 'alert');
			}
		});
	};

	this.handlePaymentScreen = function (widget, nameWidgetToShow) {
		// alterna entre widgets do fluxo de pagamento
		widget.isVisible = false;

		var widgetToShow = widget.container.getWidget(nameWidgetToShow);
		widgetToShow.isVisible = true;
		widgetToShow.activate();
	};

	this.closeConsumerPopup = function (widget) {
		ScreenService.closePopup();
		widget.container.getWidget('paymentMenu').activate();
	};

	this.handleConsumerPopup = function (popup) {
		var clientField = popup.getField('NMRAZSOCCLIE');
		var searchField = popup.getField('consumerSearch');
		var consumerField = popup.getField('NMCONSUMIDOR');

		self.clearConsumerPopup(popup);

		OperatorRepository.findOne().then(function (operatorData) {
			if (operatorData.modoHabilitado == 'B') {
				popup.container.getWidget("paymentMenu").getAction("setconsumer").isVisible = true;
			}
			else {
				popup.container.getWidget("paymentMenu").getAction("setconsumer").isVisible = false;
			}

			var permiteDig = operatorData.IDPERDIGCONS == 'S';

			clientField.readOnly = permiteDig;
			searchField.readOnly = permiteDig;
			consumerField.readOnly = permiteDig;

			popup.getAction("qrcode").isVisible = !Util.isDesktop();
		});
	};

	this.attPaymentList = function (widget) {
		PaymentService.findAllPayment().then(function (arrTiporece) {
			widget.dataSource.data = _.map(arrTiporece, function (tiporece) {
				tiporece.VRMOVIVEND = UtilitiesService.toCurrency(tiporece.VRMOVIVEND);
				return tiporece;
			});

			if (_.isEmpty(widget.dataSource.data)) {
				self.handlePaymentScreen(widget, 'paymentMenu');
			}
		});
	};

	this.setPaymentRow = function (widget) {
		widget.container.getWidget('cancelPayment').currentRow = widget.currentRow;
	};

	this.removePayment = function (widget) {
		ScreenService.confirmMessage(
			MESSAGE.REMOVE_PAYMENT, 'question',
			function () {
				PaymentService.handleRemovePayment(widget.currentRow).then(function (handleRemovePaymentResult) {
					if (!handleRemovePaymentResult.error) {
						var widgetPaymentList = widget.container.getWidget('paymentList');
						self.attStripeData(widgetPaymentList);
						self.attPaymentList(widgetPaymentList);
					} else {
						ScreenService.showMessage(handleRemovePaymentResult.message, 'alert');
					}
				}.bind(this));
			},
			function () { }
		);
	};

	this.attScreen = function (widget) {
		self.attStripeData(widget.container.getWidget('paymentMenu'));
		ScreenService.closePopup();
		widget.container.getWidget('paymentMenu').activate();
	};

	this.setConsumerInfo = function (consumerCPFInfoWidget) {
		var isValid = true;

		var NRINSCRCONS = consumerCPFInfoWidget.currentRow.NRINSCRCONS || null;
		var EMAIL = consumerCPFInfoWidget.currentRow.EMAIL || null;
		var NOMECONS = consumerCPFInfoWidget.currentRow.NOMECONS || null;
		var DSOBSFINVEN = consumerCPFInfoWidget.currentRow.DSOBSFINVEN || null;
		var CDVENDEDOR = null;
		var NMFANVEN = null;
		var vendedores = _.filter(consumerCPFInfoWidget.getField('NMFANVEN').dataSource.data, function (o) { return o.__isSelected; });

		if (vendedores.length > 0) {
			CDVENDEDOR = vendedores[0].CDVENDEDOR;
			NMFANVEN = vendedores[0].NMFANVEN;
		}

		if (NRINSCRCONS) {
			NRINSCRCONS = NRINSCRCONS.replace(/[^0-9 ]/g, "");
			isValid = UtilitiesService.isValidCPForCNPJ(NRINSCRCONS);
			if (!isValid) {
				ScreenService.showMessage('CPF/CNPJ inválido.', 'alert');
				return;
			}
		}

		if (EMAIL) {
			isValid = UtilitiesService.checkEmail(EMAIL);
			if (!isValid) {
				ScreenService.showMessage('E-mail inválido.', 'alert');
				return;
			}
		}

		if (isValid) {
			PaymentRepository.findOne().then(function (paymentData) {
				paymentData.NRINSCRCONS = NRINSCRCONS;
				paymentData.EMAIL = EMAIL;
				paymentData.NOMECONS = NOMECONS;
				paymentData.CDVENDEDOR = CDVENDEDOR;
				paymentData.NMFANVEN = NMFANVEN;
				paymentData.DSOBSFINVEN = DSOBSFINVEN;
				PaymentRepository.save(paymentData);
				ScreenService.closePopup();
			});
		}
	};

	this.payAccount = function () {
		PaymentService.payAccount().then(function (payAccountResult) {
			if (!_.isEmpty(_.get(payAccountResult, 'data.paramsImpressora'))) {
				PerifericosService.print(payAccountResult.data.paramsImpressora).then(function (result) {
				/*var notaTEF = JSON.stringify({"codigoBarras" : payAccountResult.data.dadosImpressao.TEXTOCODIGOBARRAS,
				                              "cupomPriVia"  : payAccountResult.data.dadosImpressao.TEXTOCUPOM1VIA,
				                              "cupomSegVia"  : payAccountResult.data.dadosImpressao.TEXTOCUPOM2VIA,
				                              "qrcode"       : payAccountResult.data.dadosImpressao.TEXTOQRCODE,
				                              "rodape"       : payAccountResult.data.dadosImpressao.TEXTORODAPE,
				                              "flag" : "printPayment"});*/
				//var result = window.cordova.plugins.IntegrationService.print(notaTEF,true,null);
					if (!payAccountResult.error) {
						if (result) {
							self.handlePrintNote(payAccountResult);
						} else self.payAccountFinish(payAccountResult);
					} else {
						ScreenService.showMessage(payAccountResult.message, 'error');
						if (_.get(payAccountResult, 'data.resetSaleCode')) {
							PaymentService.updateSaleCode();
						}
					}
                });
			} else {
				if (!payAccountResult.error) {
					if (!_.isEmpty(payAccountResult.data.dadosImpressao)) {
						payAccountResult.data.dadosImpressao.TEFVOUCHER = [];
						self.handlePrintNote(payAccountResult);
					} else self.payAccountFinish(payAccountResult);
				} else {
					ScreenService.showMessage(payAccountResult.message, 'error');
					if (_.get(payAccountResult, 'data.resetSaleCode')) {
						PaymentService.updateSaleCode();
					}
				}

			}
		}.bind(this));
	};

	this.payAccountFinish = function (payAccount) {
		CarrinhoDesistencia.remove(Query.build());
		ProdSenhaPed.remove(Query.build());
		var message = MESSAGE.PAYMENT_COMPLETED;

        if (_.get(payAccount, 'data.messageCurl')) {
            message += '<br><br>' + _.get(payAccount, 'data.messageCurl');
        }
		if (_.get(payAccount, 'data.IDSTATUSNFCE') === 'P') {
			message += '<br><br>' + MESSAGE.NFCE_CONTINGENCY;
		}
		if (_.get(payAccount, 'data.mensagemNfce')) {
			var retornoNfce = _.get(payAccount, 'data.mensagemNfce');
			if (!~retornoNfce.indexOf("100 - ")) {
				message += '<br><br>' + _.get(payAccount, 'data.mensagemNfce');
			}
		}
		if (_.get(payAccount, 'data.mensagemImpressao')) {
			message += '<br><br>' + _.get(payAccount, 'data.mensagemImpressao');
		}
		ScreenService.showMessage(message);

		if (_.get(payAccount, 'data.IDSTMESAAUX') === 'R') {
			TableController.openAccountPayment();
		} else {
			AccountController.finishPayAccount();
		}
	};

	this.attStripeData = function (widget) {
		var stripeWidget = widget.container.getWidget('paymentStripe');

		PaymentRepository.findOne().then(function (paymentData) {
			stripeWidget.getField('limitDebitoLabel').isVisible = _.get(paymentData, "limitDebito.LIMITE_ATUAL");
			stripeWidget.getField('limitDebito').isVisible = _.get(paymentData, "limitDebito.LIMITE_ATUAL");
			stripeWidget.getField('limitCreditoLabel').isVisible = _.get(paymentData, "limitCredito[0].VRLIMDEBCONS");
			stripeWidget.getField('limitCredito').isVisible = _.get(paymentData, "limitCredito[0].VRLIMDEBCONS");
			stripeWidget.currentRow = {
				TOTALVENDA: UtilitiesService.toCurrency(paymentData.DATASALE.TOTALVENDA),
				VALORPAGO: UtilitiesService.toCurrency(paymentData.DATASALE.VALORPAGO),
				FALTANTE: UtilitiesService.toCurrency(paymentData.DATASALE.FALTANTE),
				TROCO: UtilitiesService.toCurrency(paymentData.DATASALE.TROCO),
				limitDebito: UtilitiesService.toCurrency(UtilitiesService.removeCurrency((_.get(paymentData, "limitDebito.LIMITE_ATUAL", 0)) || 0)),
				limitCredito: UtilitiesService.toCurrency(UtilitiesService.removeCurrency((_.get(paymentData, "limitCredito[0].VRLIMDEBCONS", 0)) || 0) / 100)
			};
		});

		widget.activate();
	};

	this.initButtons = function (container) {
		var paymentGroupType = container.getWidget('categories');

		PaymentRepository.findOne().then(function (paymentData) {
			if (_.isEmpty(paymentData.IDTPVENDACONS) || paymentData.CREDITOPESSOAL) {
				ParamsGroupPriceChart.findAll().then(function (paymentGroups) {
					ParamsPriceChart.findAll().then(function (paymentTypes) {
						GroupPriceChart.save(paymentGroups).then(function () {
							PriceChart.save(paymentTypes).then(function () {
								paymentGroupType.reload();
							}.bind(this));
						}.bind(this));
					}.bind(this));
				}.bind(this));
			}
			else {
				this.setPaymentTypes(paymentData.IDTPVENDACONS, container);
			}
		}.bind(this));
	};

	this.cancelForSale = function (widget) {
		// cancela venda
		PaymentService.findIntegrations().then(function (integrations) {
			if (integrations.error) {
				self.backPayment();
			} else {
				ScreenService.confirmMessage(
					MESSAGE.CANCEL_INTEGRATION, 'question',
					function () {
						PaymentService.handleCancelForSale(integrations.data).then(function (handleCancelForSaleResult) {
							if (!handleCancelForSaleResult.error) {
								self.backPayment();
							} else {
								self.attStripeData(widget.container.getWidget('paymentMenu'));
								ScreenService.showMessage(handleCancelForSaleResult.message, 'alert');
							}
						});
					}.bind(this),
					function () { }
				);
			}
		});
	};

	this.backPayment = function () {
		PaymentService.clearPayment();
		ScreenService.goBack();
	};

	this.openDiscount = function (paymentWidget) {
		PaymentService.handleOpenDiscount().then(function (handleOpenDiscountResult) {
			if (!handleOpenDiscountResult.error) {
				PermissionService.checkAccess('cupomDesconto').then(function (CDSUPERVISOR) {
					var discountPopup = paymentWidget.container.getWidget('discountPopup');
					discountPopup.currentRow.CDSUPERVISORd = CDSUPERVISOR;

					ScreenService.openPopup(discountPopup).then(function () {
						self.getDiscount(discountPopup);
					}.bind(this));
				}.bind(this));
			} else {
				ScreenService.showMessage(MESSAGE.BLOCK_DISCOUNT, 'alert');
			}
		});
	};

	this.clearConsumerPopup = function (popup) {
		popup.currentRow.CDCLIENTE = "";
		popup.currentRow.NMRAZSOCCLIE = "";
		popup.currentRow.CDCONSUMIDOR = "";
		popup.currentRow.NMCONSUMIDOR = "";
		popup.getField('NMRAZSOCCLIE').clearValue();
		popup.getField('consumerSearch').clearValue();
		popup.getField('NMCONSUMIDOR').clearValue();
		popup.getField('NMCONSUMIDOR').dataSourceFilter = [
			{
				"name": "CDCLIENTE",
				"operator": "=",
				"value": ""
			}
		];
	};

	this.openConsumerPopup = function (popup) {
		PaymentService.findAllPayment().then(function (payments) {
			if (_.isEmpty(payments)) {
				PaymentRepository.findOne().then(function (paymentData) {
					if (_.isEmpty(paymentData.CDCLIENTE)) {
						self.clearConsumerPopup(popup);
					}
					popup.getField('consumerSearch').clearValue();

					ParamsClientRepository.findAll().then(function (clients) {
						if (!_.isEmpty(paymentData.CDCLIENTE)) {
							popup.currentRow.CDCLIENTE = paymentData.CDCLIENTE;
							popup.currentRow.NMRAZSOCCLIE = paymentData.NMRAZSOCCLIE;
							popup.getField('NMRAZSOCCLIE').setValue(paymentData.NMRAZSOCCLIE);
						}
						else {
							if (clients.length == 1) {
								popup.currentRow.CDCLIENTE = clients[0].CDCLIENTE;
								popup.currentRow.NMRAZSOCCLIE = clients[0].NMRAZSOCCLIE;
								popup.getField('NMRAZSOCCLIE').setValue(clients[0].NMRAZSOCCLIE);
								popup.getField('NMCONSUMIDOR').dataSourceFilter = [
									{
										"name": "CDCLIENTE",
										"operator": "=",
										"value": ""
									}
								];
							}
						}
						if (!_.isEmpty(paymentData.CDCONSUMIDOR)) {
							popup.currentRow.CDCONSUMIDOR = paymentData.CDCONSUMIDOR;
							popup.currentRow.NMCONSUMIDOR = paymentData.NMCONSUMIDOR;
							popup.getField('NMCONSUMIDOR').setValue(paymentData.NMCONSUMIDOR);
						}
						ScreenService.openPopup(popup);
					});
				});
			}
			else {
				ScreenService.showMessage("Não é possível informar o consumidor com recebimentos lançados.");
			}
		});
	};

	this.prepareCustomers = function (currentRow, clientField) {
		var consumerField = clientField.widget.getField('NMCONSUMIDOR');
		consumerField.clearValue();
		if (!_.isEmpty(currentRow.CDCLIENTE)) {
			consumerField.dataSourceFilter[0].value = currentRow.CDCLIENTE;
			if (consumerField.dataSourceFilter[1]) {
				consumerField.dataSourceFilter[1].value = "";
			}
			var searchField = clientField.widget.getField('consumerSearch');
			if (searchField) searchField.clearValue();
		}
		else {
			currentRow.CDCLIENTE = "";
			consumerField.dataSourceFilter[0].value = "";
		}
	};

	var t;
	this.consumerSearch = function () {
		clearTimeout(t);
		var searchConsumer = function () {
			var consumerField = ApplicationContext.templateManager.container.getWidget('setConsumerPopUp').getField('NMCONSUMIDOR');
			var popup = ApplicationContext.templateManager.container.getWidget('setConsumerPopUp');

			consumerField.clearValue();

			consumerField.dataSourceFilter = [
				{
					name: 'CDCLIENTE',
					operator: '=',
					value: _.isEmpty(popup.currentRow.CDCLIENTE) ? "" : popup.currentRow.CDCLIENTE
				},
				{
					name: 'CDCONSUMIDOR',
					operator: '=',
					value: popup.currentRow.consumerSearch
				}
			];
			consumerField.reload().then(function (search) {
				search = search.dataset.ParamsCustomerRepository;
				if (!_.isEmpty(search)) {
					if (search.length == 1) {
						popup.currentRow.CDCLIENTE = search[0].CDCLIENTE;
						popup.currentRow.NMCONSUMIDOR = search[0].NMCONSUMIDOR;
						popup.currentRow.CDCONSUMIDOR = search[0].CDCONSUMIDOR;
						popup.currentRow.NMRAZSOCCLIE = search[0].NMRAZSOCCLIE;
						popup.currentRow.IDSITCONSUMI = search[0].IDSITCONSUMI;
						popup.getField('NMCONSUMIDOR').setValue(search[0].NMCONSUMIDOR);
					} else {
						self.modifyConsumerPopup(consumerField);
						consumerField.openField();
					}
				}
			}.bind(this));
		}.bind(this);
		t = setTimeout(searchConsumer, 1000);
	};

	this.modifyConsumerPopup = function (consumerField) {
		delete consumerField.selectWidget;
		if (consumerField.dataSourceFilter[0]) {
			consumerField.dataSourceFilter[0].value = consumerField.widget.currentRow.CDCLIENTE;
		}
	};

	this.setAccountConsumer = function (popup, clear) {
		if (_.isEmpty(popup.currentRow.CDCLIENTE) || clear) {
			popup.currentRow.CDCLIENTE = null;
			popup.currentRow.NMRAZSOCCLIE = null;
		}
		if (_.isEmpty(popup.currentRow.CDCONSUMIDOR) || clear) {
			popup.currentRow.CDCONSUMIDOR = null;
			popup.currentRow.NMCONSUMIDOR = null;
		}
		OperatorRepository.findOne().then(function (operatorData) {
			PaymentRepository.findOne().then(function (paymentData) {
				PaymentService.updateCartPrices(operatorData.chave, paymentData.ITEMVENDA, popup.currentRow.CDCLIENTE, popup.currentRow.CDCONSUMIDOR).then(function (result) {
					paymentData.ITEMVENDA = result.CartPricesRepository;
					paymentData.DATASALE.TOTALVENDA = result.nothing.valorVenda;
					paymentData.DATASALE.TOTAL = result.nothing.valorVenda;
					paymentData.DATASALE.TOTALSUBSIDY = result.nothing.subsidioTotal;
					paymentData.DATASALE.REALSUBSIDY = result.nothing.subsidioReal;
					paymentData.DATASALE.FALTANTE = result.nothing.valorVenda;
					paymentData.DATASALE.VRDESCONTO = 0;
					paymentData.DATASALE.PCTDESCONTO = '0';
					paymentData.DATASALE.TIPODESCONTO = 'P';
					paymentData.DATASALE.FIDELITYDISCOUNT = 0;
					paymentData.DATASALE.FIDELITYVALUE = 0;
					paymentData.numeroProdutos = result.nothing.numeroProdutos;
					paymentData.CDCLIENTE = popup.currentRow.CDCLIENTE;
					paymentData.NMRAZSOCCLIE = popup.currentRow.NMRAZSOCCLIE;
					paymentData.CDCONSUMIDOR = popup.currentRow.CDCONSUMIDOR;
					paymentData.NMCONSUMIDOR = popup.currentRow.NMCONSUMIDOR;
					PaymentRepository.save(paymentData).then(function () {
						if (operatorData.IDEXTCONSONLINE !== 'S' || _.isEmpty(paymentData.CDCLIENTE) || _.isEmpty(paymentData.CDCONSUMIDOR)) {
							self.attScreen(popup);
							self.setPaymentTypes(result.nothing.IDTPVENDACONS, popup.container);
						}
						else {
							ScreenService.confirmMessage("Deseja utilizar Crédito Fidelidade para este consumidor?", "question",
								function () {
									self.attStripeData(popup.container.getWidget('paymentMenu'));
									self.setPaymentTypes(result.nothing.IDTPVENDACONS, popup.container);
									AccountController.openBalconyFidelity(popup);
								},
								function () {
									self.attScreen(popup);
									self.setPaymentTypes(result.nothing.IDTPVENDACONS, popup.container);
								}
							);
						}
					}.bind(this));
				}.bind(this));
			}.bind(this));
		}.bind(this));
	};

	this.getDiscount = function (discountPopup) {
		var VRDESCONTO = 0;
		var TIPODESCONTO = '';
		// seta desconto já inserido
		PaymentService.getPaymentValue().then(function (DATASALE) {
			TIPODESCONTO = DATASALE.TIPODESCONTO;
			VRDESCONTO = TIPODESCONTO === 'P' ? DATASALE.PCTDESCONTO : DATASALE.VRDESCONTO;

			discountPopup.getField('TIPODESCONTO').setValue(TIPODESCONTO);
			discountPopup.getField('VRDESCONTO').setValue(VRDESCONTO);
			self.handleDiscountRadioChange(discountPopup);
		});
	};

	this.handleDiscountRadioChange = function (discountPopup) {
		PaymentService.getPaymentValue().then(function (DATASALE) {
			if (discountPopup.getField('TIPODESCONTO').value() === 'P') {
				discountPopup.getField('VRDESCONTO').label = 'Porcentagem';
				discountPopup.getField('VRDESCONTO').range.max = 100 * (DATASALE.FALTANTE / DATASALE.TOTALVENDA);
			} else if (discountPopup.getField('TIPODESCONTO').value() === 'V') {
				discountPopup.getField('VRDESCONTO').label = 'Valor';
				discountPopup.getField('VRDESCONTO').range.max = DATASALE.TOTAL;
			}
		});
	};

	this.setDiscount = function (discountPopup) {
		if (discountPopup.isValid()) {
			self.getMaxDiscountMessage(discountPopup).then(function (customMessage) {
				if (self.validValue(discountPopup.getField('VRDESCONTO'), customMessage)) {
					var currentRow = discountPopup.currentRow;

					currentRow.VRDESCONTO = UtilitiesService.getFloat(currentRow.VRDESCONTO);
					PaymentService.handleApplyDiscount(currentRow).then(function (handleApplyDiscountResult) {
						if (!handleApplyDiscountResult.error) {
							self.attScreen(discountPopup);
						} else {
							ScreenService.showMessage(customMessage, 'alert');
						}
					});
				}
			}.bind(this));
		}
	};

	this.getMaxDiscountMessage = function (discountPopup) {
		return PaymentRepository.findOne().then(function (paymentData) {
			return MESSAGE.VR_MAX_DISCOUNT + (paymentData.DATASALE.TOTAL - (0.01 * paymentData.numeroProdutos) - paymentData.DATASALE.FIDELITYVALUE).toFixed(2).replace('.', ',') + '.';
		});
	};

	this.cancelDiscount = function (discountPopup) {
		PaymentService.getPaymentValue().then(function (DATASALE) {
			if (!!DATASALE.VRDESCONTO) {
				ScreenService.confirmMessage(
					MESSAGE.REMOVE_DISCOUNT, 'question',
					function () {
						var currentRow = discountPopup.currentRow;
						// zera desconto
						currentRow.TIPODESCONTO = 'P';
						currentRow.VRDESCONTO = 0;
						currentRow.MOTIVODESCONTO = null;
						currentRow.CDOCORR = null;
						PaymentService.handleApplyDiscount(currentRow).then(function (handleApplyDiscountResult) {
							self.attScreen(discountPopup);
						});
					}.bind(this),
					function () { }
				);
			} else {
				ScreenService.showMessage(MESSAGE.NO_DISCOUNT, 'alert');
			}
		});
	};

	this.receivePersonalCredit = function (creditDetails) {
		if (this.checkCreditDetails(creditDetails)) {
			OperatorRepository.findOne().then(function (params) {
				var accountData = {
					"CDCLIENTE": creditDetails.CDCLIENTE,
					"CDCONSUMIDOR": creditDetails.CDCONSUMIDOR,
					"CDFAMILISALD": creditDetails.CDFAMILISALD,
					"VRRECARGA": creditDetails.VRRECARGA,
					"CREDITOPESSOAL": true
				};

				var accountDetails = {
					"vlrtotal": creditDetails.VRRECARGA,
					"vlrservico": 0,
					"vlrprodutos": creditDetails.VRRECARGA,
					"vlrdesconto": 0,
					"fidelityDiscount": 0,
					"numeroProdutos": 0
				};

				PaymentService.initializePayment(accountData, params, accountDetails, null, null, null, null, null).then(function () {
					WindowService.openWindow('PAYMENT_TYPES_SCREEN');
				});
			});
		}
	};

	this.checkCreditDetails = function (creditDetails) {
		if (creditDetails.CDCLIENTE == null || creditDetails.CDCLIENTE.length == 0 ||
			creditDetails.CDCONSUMIDOR == null || creditDetails.CDCONSUMIDOR.length == 0 ||
			creditDetails.CDFAMILISALD == null || creditDetails.CDFAMILISALD.length == 0 ||
			creditDetails.VRRECARGA == null) {
			ScreenService.showMessage("Favor preencher todos os campos.");
			return false;
		}
		else if (creditDetails.IDPERMCARGACRED == "N") {
			ScreenService.showMessage("Esta familia não está habilitada para receber carga de crédito.");
			return false;
		}
		else if (isNaN(creditDetails.VRRECARGA)) {
			ScreenService.showMessage("Favor informar um valor de recarga válido.");
			return false;
		}
		else if (creditDetails.VRRECARGA <= 0) {
			ScreenService.showMessage("Valor da recarga tem que ser maior que 0.");
			return false;
		}
		else {
			return true;
		}
	};

	this.setPaymentTypes = function (mode, container) {
		ParamsGroupPriceChart.findAll().then(function (paymentGroups) {
			ParamsPriceChart.findAll().then(function (paymentTypes) {
                OperatorRepository.findOne().then(function (operatorData){
    				var filteredPayments = self.paymentTypeFilter(mode, paymentGroups, paymentTypes, operatorData.IDCONSUBDESFOL);

                    GroupPriceChart.clearAll().then(function () {
                        PriceChart.clearAll().then(function () {
                            GroupPriceChart.save(filteredPayments.newPaymentGroups).then(function () {
                                PriceChart.save(filteredPayments.newPaymentTypes).then(function () {
                                    container.getWidget('categories').reload().then(function () {
                                        container.getWidget('pricechart').reload().then(function () {
                                            // Stops a bug where all the payment types get loaded in at the same time.
                                            if (!_.isEmpty(container.getWidget('categories').dataSource.data)) {
                                                var firstGroup = container.getWidget('categories').dataSource.data[0];
                                                var filter = [{
                                                    name: container.getWidget('categories').valueField,
                                                    operator: '=',
                                                    value: firstGroup[container.getWidget('categories').valueField]
                                                }];
                                                container.getWidget('pricechart').dataSource.filter(filter).then(function (filtered) {
                                                    container.getWidget('pricechart').dataSource.data = filtered;
                                                });
                                            }
                                        });
                                    });
                                });
                            });
                        });
                    });
                }.bind(this));
            }.bind(this));
        }.bind(this));
    };

	this.paymentTypeFilter = function (mode, paymentGroups, paymentTypes, IDCONSUBDESFOL) {
		var arrTypes = self.handlePaymentTypes(mode, IDCONSUBDESFOL);

		var newPaymentTypes = _.filter(paymentTypes, function (paymentType) {
			return _.includes(arrTypes, paymentType.IDTIPORECE);
		}.bind(this));

		var arrGroups = _.uniq(_.map(newPaymentTypes, function (newPaymentType) { return newPaymentType.CDGRUPO; }));
		var newPaymentGroups = _.filter(paymentGroups, function (newPaymentGroup) {
			return _.includes(arrGroups, newPaymentGroup.CDGRUPO);
		}.bind(this));

		// prepara novo dataSource (falha do zeedhi obriga a realizar este procedimento)
		newPaymentGroups = _.map(newPaymentGroups, function (newPaymentGroup) {
			newPaymentGroup.selected = false;
			newPaymentGroup.visible = true;
			return newPaymentGroup;
		}.bind(this));
		if (!_.isEmpty(newPaymentGroups)) {
			newPaymentGroups[0].selected = true;
		}

		return {
			'newPaymentTypes': newPaymentTypes,
			'newPaymentGroups': newPaymentGroups
		};
	};

    this.handlePaymentTypes = function (mode, IDCONSUBDESFOL){
        // À Vista
        var cash = Array('1', '2', '3', '4', '5', '6', '7', '8', 'B', 'C', 'E', 'F', 'G', 'H');
        // Débito Consumidor
        var consumer = Array('A');
        // Crédito Pessoal
        var personal = Array('9');

        var arrTypes = Array();
        mode = !_.isEmpty(mode) ? String(mode) : '7';
        switch (mode) {
            case '1': // Débito Consumidor (so utiliza A)
                arrTypes = consumer;
                break;
            case '2': // Crédito Pessoal (so utiliza 9)
                arrTypes = personal;
                break;
            case '3': // A Vista (todos os idtiporece menos 9 e A)
                if (IDCONSUBDESFOL === 'S'){
                    arrTypes = _.concat(consumer, cash);
                }
                else {
                    arrTypes = cash;
                }
                break;
            case '4': // Débito Consumidor/Crédito Pessoal (so utiliza 9 e A)
                arrTypes = _.concat(consumer, personal);
                break;
            case '5': // Débito Consumidor/A Vista (so nao utiliza o 9)
                arrTypes = _.concat(consumer, cash);
                break;
            case '6': // Crédito Pessoal/A Vista (so nao utiliza o A)
                arrTypes = _.concat(personal, cash);
                break;
            case '7': // Todos (tudo)
                arrTypes = _.concat(personal, consumer, cash);
                break;
            default:
                arrTypes = cash;
        }

        return arrTypes;
    };

	window.scanCodeResult = null;

	this.openQRScanner = function (widget) {
		if (_.isEmpty(widget.currentRow.CDCLIENTE)) widget.currentRow.CDCLIENTE = null;

		self.callQRScanner().then(function (qrCode) {
			if (!qrCode.error) {
				qrCode = qrCode.contents;

				if (_.isEmpty(qrCode)) {
					ScreenService.showMessage("Não foi possível obter os dados do leitor.");
				}
				else {
					self.clearConsumerPopup(widget);
					OperatorRepository.findOne().then(function (operatorData) {
						AccountService.searchConsumer(operatorData.chave, widget.currentRow.CDCLIENTE, qrCode).then(function (consumerData) {
							if (_.isEmpty(consumerData)) {
								ScreenService.showMessage("Não foi encontrado nenhum consumidor com este código.");
							} else {
								if (consumerData.length == 1) {
									widget.currentRow.CDCLIENTE = consumerData[0].CDCLIENTE;
									widget.currentRow.NMRAZSOCCLIE = consumerData[0].NMRAZSOCCLIE;
									widget.currentRow.CDCONSUMIDOR = consumerData[0].CDCONSUMIDOR;
									widget.currentRow.NMCONSUMIDOR = consumerData[0].NMCONSUMIDOR;
								} else {
									var consumerField = widget.getField('NMCONSUMIDOR');
									consumerField.dataSource.data = consumerData;
									consumerField.readOnly = false;
									self.modifyConsumerPopup(consumerField);
									consumerField.openField();
								}
							}
						});
					});
				}
			} else {
				ScreenService.showMessage(qrCode.message, 'alert');
			}
		}.bind(this));
	};

	this.callQRScanner = function () {
		return new Promise(function (resolve) {
			if (!!window.ZhCodeScan) {
				window.scanCodeResult = _.bind(self.qrCodeResult, self, resolve);
				ZhCodeScan.scanCode();
			} else if (!!window.cordova) {
				cordova.plugins.barcodeScanner.scan(
					function (result) {
						result.error = false;
						result.contents = result.text;
						resolve(result);
					},
					function (error) {
						var result = {};
						result.error = true;
						result.message = error;
						resolve(result);
					}
				);
			} else {
				resolve({
					'error': true,
					'message': 'Não foi possível chamar a integração. Sua instância não existe.'
				});
			}
		}.bind(this));
	};

	this.qrCodeResult = function (resolve, result) {
		resolve(JSON.parse(result));
	};

	this.handleConsumerField = function (consumerField) {
		OperatorRepository.findOne().then(function (operatorData) {
			consumerField.selectWidget.floatingControl = false;
			if (operatorData.IDPERDIGCONS == 'S') {
				consumerField.readOnly = true;
			}
		});
	};

	this.handleConsumerChange = function (consumerPopup) {
		if (!_.isEmpty(consumerPopup.currentRow.CDCONSUMIDOR)) {
			if (consumerPopup.currentRow.IDSITCONSUMI === '2') {
				ScreenService.showMessage(MESSAGE.INATIVE_CONSUMER, 'alert');
				self.clearConsumerPopup(consumerPopup);
			}
			else {
				consumerPopup.currentRow.CDCLIENTE = consumerPopup.currentRow.CODCLIE;
				consumerPopup.currentRow.NMRAZSOCCLIE = consumerPopup.currentRow.NOMCLIE;
				consumerPopup.getField('NMRAZSOCCLIE').setValue(consumerPopup.currentRow.NOMCLIE);
				if (consumerPopup.currentRow.IDSOLSENHCONS === 'S' && consumerPopup.currentRow.CDSENHACONS !== null) {
					PermissionService.promptConsumerPassword(consumerPopup.currentRow.CDCLIENTE, consumerPopup.currentRow.CDCONSUMIDOR).then(
						function () {
							// ...
						},
						function () {
							consumerPopup.currentRow.NMCONSUMIDOR = null;
							consumerPopup.currentRow.CDCONSUMIDOR = null;
							consumerPopup.currentRow.IDSITCONSUMI = null;
						}
					);
				}
			}
		}
	};

	this.accountAddition = function (widget) {
		self.canHandleAccountAddition().then(function (canHandleAccountAddition) {
			if (!canHandleAccountAddition.error) {
				ScreenService.confirmMessage(
					MESSAGE.ALTER_ADDITION, 'question',
					function () {
						PermissionService.checkAccess('retirarTaxaServico').then(function (CDSUPERVISOR) {
							PaymentService.handleAccountAddition(CDSUPERVISOR).then(function () {
								self.attStripeData(widget);
							}.bind(this));
						}.bind(this));
					}.bind(this),
					function () { }
				);
			} else {
				ScreenService.showMessage(canHandleAccountAddition.message, 'alert');
			}
		}.bind(this));
	};

	this.canHandleAccountAddition = function () {
		var resultFormat = {
			'error': true,
			'message': ''
		};

		return PaymentService.getPaymentValue().then(function (DATASALE) {
			if (!DATASALE.VRTXSEVENDA) {
				resultFormat.message = MESSAGE.NO_ADDITION;
			} else if (!DATASALE.FALTANTE) {
				resultFormat.message = MESSAGE.BLOCK_PAYMENT;
			} else {
				resultFormat.error = false;
			}

			return resultFormat;
		});
	};

	this.handleEnterButton = function (args) {
		var keyCode = args.e.keyCode;
		if (keyCode === 9 || keyCode === 13) {
			UtilitiesService.handleCloseKeyboard();
			var widget = args.owner.field.widget;

			if (widget.name === 'discountPopup')
				this.setDiscount(widget);
			else if (widget.name === 'paymentPopup')
				this.setPayment(widget);
			else if (widget.name === 'consumerCPFPopup')
				this.setConsumerInfo(widget);
		}
	};

	this.handlePrintNote = function (payAccountResult) {
		ScreenService.confirmMessage(MESSAGE.CONFIRM_PRINT, 'question', function () {
			self.isCardSale(payAccountResult);
		}.bind(this), function () {
			payAccountResult.data.dadosImpressao.TEXTOCUPOM1VIA = null;
			self.isCardSale(payAccountResult);
		}.bind(this));
	};

	this.isCardSale = function (payAccountResult) {
		if (!_.isEmpty(payAccountResult.data.dadosImpressao.TEFVOUCHER)) {
			self.handleReceiptCustomer(payAccountResult);
		} else {
			PaymentService.handlePrintReceipt(payAccountResult.data.dadosImpressao);
			self.payAccountFinish(payAccountResult);
		}
	};

	this.handleReceiptCustomer = function (payAccountResult) {
		ScreenService.confirmMessage(
			MESSAGE.PRINT_VIA_CLI, 'question', function () {
				PaymentService.handlePrintReceipt(payAccountResult.data.dadosImpressao);
				self.payAccountFinish(payAccountResult);
			}.bind(this), function () {
				payAccountResult.data.dadosImpressao.TEFVOUCHER = _.map(payAccountResult.data.dadosImpressao.TEFVOUCHER, function (n) {
					n.STLPRIVIA = null;
					return n;
				});
				PaymentService.handlePrintReceipt(payAccountResult.data.dadosImpressao);
				self.payAccountFinish(payAccountResult);
			}.bind(this)
		);
	};
}

Configuration(function (ContextRegister) {
	ContextRegister.register('PaymentController', PaymentController);
});

// FILE: js/controllers/RechargeController.js
function RechargeController(Query, ScreenService, templateManager, eventAggregator, UtilitiesService, WindowService) {
// function RechargeController(Query, ScreenService, templateManager, RechargeService, ProfileService, LoginRepository, RechargeValueRepository, eventAggregator, BankDataRepository, LoginController, UtilitiesService, RechargeCardRepository, WindowService) {

	/* Gets the consumer's authorized store families with zero value, and updates the recharge grid. */
	this.getRechargeGrid = function(rechargeGrid){
		rechargeGrid.dataSource.data = [];
		RechargeValueRepository.clearAll().then(function(){
			LoginRepository.findOne().then(function (consumerData){
				RechargeService.getRechargeGrid(consumerData.CDFILIAL, consumerData.NRCPFRESPCON, consumerData.CDCLIENTE).then(function (grid){
					var valorTotal = {CDCONSUMIDOR: null, CDFAMILISALD: null, NMCONSUMIDOR: "Valor Total",
									  NMFAMILISALD: null, NRCPFRESPCON: null, PARENT: null,
									  PRIMARY: null, VRSALDCONFAM: "0,00"};
					grid.push(valorTotal);
					rechargeGrid.dataSource.data = grid;
				});
			});
		});
	};

	/* Opens the popup for the recharge value. */
	this.openRechargeValuePopup = function(row, widget) {
		if(row.NMCONSUMIDOR === null){
			delete widget.dataSource.data;
			widget.newRow();
			/* If a recharge value was already chosen, restores this value to the popup. */
			if (parseFloat(row.VRSALDCONFAM).toFixed(2) > 0){
				widget.setCurrentRow(row);
				widget.moveToFirst();
			}
			else {
				widget.moveToFirst();
			}
			widget.label = row.NMFAMILISALD;
			widget.currentRow.PARENT = row.PARENT;
			ScreenService.openPopup(widget);
		}
	};

	/* Updates the recharge grid with the value. */
	this.updateRechargeGrid = function(value, dataSource, fieldName, widget) {
		fieldParent = widget.currentRow.PARENT;
		value = value.toFixed(2);
		if (value > 0) {
			/* Updates the grid with the recharge value entered in the popup.
			   The for is needed to find the right family in the grid. */
			for (var i in dataSource){
				if (dataSource[i].NMFAMILISALD === fieldName && dataSource[i].PARENT === fieldParent){
					oldVRSALDCONFAM = dataSource[i].VRSALDCONFAM;
					dataSource[i].VRSALDCONFAM = parseFloat(value.replace(',','.')).toFixed(2).replace('.',',');
					if(oldVRSALDCONFAM == "0,00"){
						//Add to Valor Total
						dataSource[dataSource.length - 1].VRSALDCONFAM = (parseFloat(dataSource[dataSource.length - 1].VRSALDCONFAM) + parseFloat(value)).toFixed(2).replace('.',',');
					} else {
						/*If there was a value in the field, the value is subtracted from
						  Valor Total and then the new value is set. */
						dataSource[dataSource.length - 1].VRSALDCONFAM = (parseFloat(dataSource[dataSource.length - 1].VRSALDCONFAM) - parseFloat(oldVRSALDCONFAM));
						dataSource[dataSource.length - 1].VRSALDCONFAM = (parseFloat(dataSource[dataSource.length - 1].VRSALDCONFAM) + parseFloat(value)).toFixed(2).replace('.',',');
					}
					templateManager.updateTemplate();
				}
			}
			ScreenService.closePopup();
		} else {
			ScreenService.showMessage('O valor da recarga deve ser maior que R$ 0,00.');
		}
	};

	/* Sets the total recharge value for the label at the end of the recharge wizard. */
	this.setRechargeLabel = function(widget, fieldsWidget) {
		fieldsWidget.newRow();
		fieldsWidget.currentRow.$error = {};

		RechargeValueRepository.findAll().then(function (rechargeValues){
			valorTotal = rechargeValues[rechargeValues.length - 1].VRSALDCONFAM;
			widget.getField('vrTotalPedido').label = 'R$ ' + valorTotal;

			var row = fieldsWidget.currentRow;
			var storedCardDetails = getLocalVar('cardDetails');
			if (storedCardDetails){
				if (storedCardDetails.nrCartao) row.cardNumber = storedCardDetails.nrCartao;
				if (storedCardDetails.dtVencimento) row.cardExpiration = storedCardDetails.dtVencimento;
			}

			row.valorPedido = parseFloat(valorTotal).toFixed(2).replace('.', '').replace(/ /g, '');
			fieldsWidget.setCurrentRow(row);
			templateManager.updateTemplate();
		});
	};

	this.isValidCardNumber = function (field, row) {
		var cardNumber;
		if (field.field) {
			//É um elemento
			cardNumber = field.val().replace(/ /g, '').replace(/_/g, '');
		} else {
			//Não é elemento, busca na row
			cardNumber = row.cardNumber.replace(/ /g, '');
		}

		var valid = true;

		if (cardNumber.length > 1) {
			if (!cardNumber.startsWith(this.visaNumbers) && !cardNumber.startsWith(this.masterNumbers)) {
				valid = false;
			}
		}

		return valid;
	};

	this.setInvalidCard = function (fieldName, widget) {
		this.cleanFieldErrors(fieldName, widget);
		widget.currentRow.$error[fieldName] = [{"message" : "cartão não aceito"}];
	};

	this.cleanFieldErrors = function (fieldName, widget) {
		if (widget.currentRow.$error[fieldName]) {
			delete widget.currentRow.$error[fieldName];
		}
	};

	this.isExpiredCard = function (date) {
		var valid = true;
		var year = date.substr(date.length - 2);
		var month = date.substring(0, 2);

		var today = new Date();
		var currentMonth = ((today.getMonth() + 1).toString()).fixLength(2, "0");
		var currentYear = (today.getFullYear().toString().substr(2)).fixLength(2, "0");

		if (year < currentYear) {
			valid = false;
		} else if (year == currentYear) {
			if (month < currentMonth) {
				valid = false;
			}
		}

		return valid;
	};

	this.isValidCardExpiration = function (date) {
		var month = date.substring(0, 2);

		return month.between(1, 12);
	};

	String.prototype.fixLength = function (length, completeWhiteSpacesWith) {
		var dif = length - this.length;
		var str = "";
		if (dif > 0) {
			for (var i = 0; i < dif; i++) {
				str += completeWhiteSpacesWith;
			}
		}
		str += this;
		return str;
	};

	this.validateCardExpirationField = function (row, widget, fieldName) {
		var cardExpiration = row.cardExpiration || "";
		var date = cardExpiration.replace('/', '');

		if (!this.isValidCardExpiration(date) && date) {
			this.cleanFieldErrors(fieldName, widget);
			widget.currentRow.$error[fieldName] = [{"message" : "data inválida"}];
		} else if (!this.isExpiredCard(date) && date) {
			this.cleanFieldErrors(fieldName, widget);
			widget.currentRow.$error[fieldName] = [{"message" : "data vencida"}];
		}
	};

	String.prototype.between = function (first, second) {
		return this >= first && this <= second;
	};

	String.prototype.startsWith = function (array){
		for (var i = 0; i < array.length; i++) {
			if (this.slice(0, array[i].length) == array[i]) {
				return true;
			}
		}
		return false;
	};

	this.validateField = function (creditCardWidget) {
		var cardNumberField = creditCardWidget.getField('cardNumber');

		this.cleanFieldErrors(cardNumberField.name, cardNumberField.widget);
		if (!cardNumberField.widget.currentRow[cardNumberField.name]) {
			cardNumberField.widget.currentRow.$error[cardNumberField.name] = [
				{
					"message": "Obrigatório",
					"class": "cardNumberField-require"
				}
			];
		}
	};

	this.setCardFlag = function (creditCardWidget) {
		var cardFlagField = creditCardWidget.getField('cardFlag');
		var cardImageField = creditCardWidget.getField('cardImage');

		switch(cardFlagField.value()) {
			case "1":
				cardImageField.source = "images/cards/visa.png";
				break;
			case "2":
				cardImageField.source = "images/cards/mastercard.png";
				break;
			case "3":
				cardImageField.source = "images/cards/american_express.png";
				break;
			case "33":
				cardImageField.source = "images/cards/diners.png";
				break;
			case "41":
				cardImageField.source = "images/cards/elo.png";
				break;
			case "224":
				cardImageField.source = "images/cards/alelo.png";
				break;
			case "225":
				cardImageField.source = "images/cards/alelo.png";
				break;
			case "280":
				cardImageField.source = "images/cards/sodexo.png";
				break;
			case "281":
				cardImageField.source = "images/cards/sodexo.png";
				break;
			default:
				cardImageField.source = "images/cards/default.png";
				break;
		}
	};

	this.isValidFieldsForSubmit = function (widget) {
		widget.currentRow.$error = {};
		var fields = [
			'cardNumber',
			'cardExpiration',
			'securityCode'
		];

		for (var i = 0; i < fields.length; i++) {
			if (!widget.currentRow[fields[i]]) {
				widget.currentRow.$error[fields[i]] = [{"message" : "obrigatório", "class": "field-require"}];
			}
		}

		this.validateCardExpirationField(widget.currentRow, widget, widget.getField('cardExpiration').name);

		return Object.keys(widget.currentRow.$error).length === 0;
	};

	this.doBilletRecharge = function () {
		var row = templateManager.container.widgets[1].currentRow;
		LoginRepository.findOne().then(function (consumerData) {
			RechargeValueRepository.findAll().then(function (rechargeValues) {
				var query = Query.build()
								.where('CDBANCO').equals(row.selectedBank);
				BankDataRepository.findOne(query).then(function (bank) {
					RechargeService.doBilletRecharge(consumerData.CDFILIAL, consumerData.CDCLIENTE, row.selectedBank, rechargeValues, bank).then(function (billetBack) {
						var url = UtilitiesService.billetUrl + billetBack[0].NRPEDCRECONS + '.html';
						this.openPrintTab(url);
						//RechargeService.billetEmail(url, consumerData.CDCLIENTE, consumerData.CDCONSUMIDOR);
						RechargeValueRepository.remove(Query.build()).then(function (){
							WindowService.openWindow('PARENT_DASHBOARD_SCREEN');
						});
					}.bind(this));
				}.bind(this));
			}.bind(this));
		}.bind(this));
	};

	this.openPrintTab = function (HTMLpath) {
	   // var wind = window.open(HTMLpath);
		//wind.print();
		window.open(HTMLpath);
	};

	/* Recharge procedure. */
	this.doCardRecharge = function() {
		if (this.isValidFieldsForSubmit(templateManager.container.widgets[1])) {
			var row = templateManager.container.widgets[1].currentRow;

			var codigoPedido = '01921902919210';

			var valorPedido = row.valorPedido;
			var codigoBandeira = templateManager.container.getWidget('creditCard').getField('cardFlag').value();
			//if (codigoBandeira === "") codigoBandeira = templateManager.container.widgets[1].fields[1].dataSource.data[0].value;

			var nrCartaoOrig = row.cardNumber;
			var dtVencimentoOrig = row.cardExpiration;

			var nrCartao = row.cardNumber.replace(/ /g, '');
			var dtVencimento = row.cardExpiration.replace('/', '');
			var codSeguranca = row.securityCode;

			LoginRepository.findOne().then(function (consumerData){
				RechargeValueRepository.findAll().then(function (rechargeValues){
					rechargeValues.pop(); //pulling out valorTotal because there's already valorPedido
					RechargeService.doCardRecharge(valorPedido, codigoPedido, codigoBandeira, dtVencimento, nrCartao, codSeguranca, consumerData.CDCLIENTE, consumerData.CDCONSUMIDOR, rechargeValues).then(function (paymentResponse){
						if (paymentResponse[0].RESULT === true){
							RechargeValueRepository.remove(Query.build()).then(function (){
								var cardDetails = {
									"nrCartao": nrCartaoOrig,
									"dtVencimento": dtVencimentoOrig
								};
								setLocalVar("cardDetails", cardDetails);
							});
							WindowService.openWindow('PARENT_DASHBOARD_SCREEN');
						}
					});
				});
			});
		}
	};

	/* Gets the consumer's details. This function call is identical to the one the Profile Page. */
	this.getConsumerDetails = function(widget) {
		LoginRepository.findOne().then(function (consumerData){
			ProfileService.getConsumerDetails(consumerData.CDCLIENTE, consumerData.CDCONSUMIDOR, consumerData.CDFILIAL).then(function(consumerDetails){
				widget.dataSource.data = consumerDetails;
				widget.moveToFirst();
				templateManager.updateTemplate();
			});
		});
	};

	eventAggregator.onRequestError(function(data){
		if (~data.data.config.url.indexOf('RechargeCardRepository')){
			ScreenService.showMessage('Não foi possivel conectar com a Instituição Financeira. Tente novamente mais tarde.');
		}
	});

}

Configuration(function(ContextRegister) {
	ContextRegister.register('RechargeController', RechargeController);
});


// FILE: js/controllers/RegisterController.js
function RegisterController(OperatorController, OperatorRepository, ScreenService, UtilitiesService, RegisterService, RegisterClosingPayments, WindowService, PrinterService, RegisterOpen, RegisterClose, GeneralFunctions, CarrinhoDesistencia, Query, PerifericosService) {

	var self = this;

	this.openRegister = function (widget) {
		var rowChangeFunds = widget.currentRow;
		rowChangeFunds.VRMOVIVEND = UtilitiesService.getFloat(rowChangeFunds.VRMOVIVEND);
		if (rowChangeFunds.VRMOVIVEND == null || isNaN(rowChangeFunds.VRMOVIVEND)) {
			ScreenService.showMessage('Fundo de troco inválido.');
		} else if (rowChangeFunds.VRMOVIVEND < 0) {
			ScreenService.showMessage('Fundo de troco não pode ser menor que 0.');
		} else {
			OperatorRepository.findOne().then(function (operatorData) {
				RegisterService.openRegister(operatorData.chave, rowChangeFunds.VRMOVIVEND).then(function (registerOpen) {
					registerOpen = registerOpen[0];

					if (_.get(registerOpen, 'dadosImpressao.paramsImpressora')) {
						PerifericosService.print(registerOpen.dadosImpressao.paramsImpressora).then(function () {
							self.handleOpenRegister(false);
						});

					} else {
						if (!_.isEmpty(registerOpen.dadosImpressao)) {
							var openRegisterText = registerOpen.dadosImpressao.open;
							var registerWidget = widget.container.getWidget('report');

							if (operatorData.IDMODEIMPRES == '25') {
								openRegisterText = _.join(_.split(openRegisterText, ' | '), "\n");
							}
							registerWidget.setCurrentRow({ 'report': openRegisterText });
							ScreenService.openPopup(registerWidget);
						} else {
							self.handleOpenRegister(false);
						}
					}
				});
			});
		}
	};

	this.printOpenRegister = function () {
		RegisterOpen.findOne().then(function (registerOpen) {
			PrinterService.printerCommand(PrinterService.TEXT_COMMAND, registerOpen.dadosImpressao.open);
			self.printerSpaceCommand(2);
			PrinterService.printerInit().then(function (result) {
				if (result.error)
					ScreenService.alertNotification(result.message);
			});

			self.handleOpenRegister(true);
		});
	};

	this.printerSpaceCommand = function (max) {
		for (var i = 0; i < max; i++) {
			PrinterService.printerSpaceCommand();
		}
	};

	this.handleOpenRegister = function (closePopup) {
		if (closePopup) {
			ScreenService.closePopup();
		}

		OperatorRepository.findOne().then(function (operatorData) {
			if (operatorData.IDTPTEF === '5' && operatorData.IDCOLETOR === 'N' && !Util.isDesktop()) {
				GeneralFunctions.sitefTableLoad();
				GeneralFunctions.exportLogs(false);
			}


		}.bind(this));
		OperatorController.bindedDoLogin();
	};

	this.closeRegister = function (paymentGrid) {
		OperatorRepository.findOne().then(function (operatorData) {
			var TIPORECE = paymentGrid.dataSource.data;
			if ((TIPORECE.length === 0) || (validPayments(TIPORECE))) {
				RegisterService.closeRegister(operatorData.chave, TIPORECE).then(function (registerClose) {
					CarrinhoDesistencia.remove(Query.build());
					registerClose = registerClose[0];
					if (_.get(registerClose, 'dadosImpressao.paramsImpressora')) {
						PerifericosService.print(registerClose.dadosImpressao.paramsImpressora).then(function () {
							self.handleCloseRegister(false);
						});
					} else {
						if (!_.isEmpty(registerClose.dadosImpressao)) {
							var registerWidget = paymentGrid.container.getWidget('report');
							if (operatorData.IDMODEIMPRES == '25') {
								_.forEach(registerClose.dadosImpressao, function (value, key) {
									registerClose.dadosImpressao[key] = _.join(_.split(value, ' | '), "\n");
								}.bind(this));
							}
							registerWidget.setCurrentRow({
								'report': _.join(_.values(registerClose.dadosImpressao),
									"\n\n******************************\n\n")
							});
							ScreenService.openPopup(registerWidget);
						} else {
							self.handleCloseRegister(false);
						}
					}
				});
			}
		});
	};

	this.printCloseRegister = function () {
		RegisterClose.findOne().then(function (registerClose) {
			for (var i in registerClose.dadosImpressao) {
				PrinterService.printerCommand(PrinterService.TEXT_COMMAND, registerClose.dadosImpressao[i]);
				PrinterService.printerCommand(PrinterService.TEXT_COMMAND, '******************************');
			}
			self.printerSpaceCommand(2);
			PrinterService.printerInit().then(function (result) {
				if (result.error)
					ScreenService.alertNotification(result.message);
			});

			self.handleCloseRegister(true);
		}.bind(this));
	};

	this.handleCloseRegister = function (closePopup) {
		if (closePopup) {
			ScreenService.closePopup();
		}
		if (RegisterService.getClosingOnLogin()) {
			RegisterService.setClosingOnLogin(false);
			WindowService.openWindow('OPEN_REGISTER_SCREEN');
		} else {
			UtilitiesService.backLoginScreen();
		}
	};

	function validPayments(payments) {
		var invalidPayment;
		return payments.every(function (payment) {
			var isValid = payment.IDSANGRIAAUTO == 'S' || (payment.IDSANGRIAAUTO == 'N' && payment.LABELVRMOVIVEND);
			if (!isValid) {
				ScreenService.showMessage('O tipo de recebimento ' + payment.NMTIPORECE + ' deve ter o valor preenchido.');
			}
			return isValid;
		});
	}
	this.getClosingPayments = function (widget) {
		OperatorRepository.findOne().then(function (operatorData) {
			RegisterService.getClosingPayments(operatorData.chave).then(function (data) {
				if (data.length === 0) {
					RegisterClosingPayments.clearAll();
				}
				if (RegisterService.getClosingOnLogin()) {
					ScreenService.showMessage("Existe movimentação para o dia anterior, o caixa deve ser fechado. Informe os valores movimentados para cada tipo de recebimento.");
				}
				widget.reload();
			});
		});
	};

	this.openPopupPaymentValue = function (widget) {
		var row = widget.currentRow;

		if (row.IDSANGRIAAUTO !== 'S') {
			var widgetPopup = widget.container.getWidget('paymentValue');

			widgetPopup.getField('LABELVRMOVIVEND').setValue(row.VRMOVIVEND);
			ScreenService.openPopup(widgetPopup);
		}
	};

	this.savePaymentValue = function (widgetEdit, paymentGrid) {
		var VRMOVIVEND = UtilitiesService.getFloat(widgetEdit.currentRow.LABELVRMOVIVEND);
		if ((typeof VRMOVIVEND !== 'number') || (isNaN(VRMOVIVEND)) || (VRMOVIVEND < 0)) {
			ScreenService.showMessage('Informe um valor válido.');
		} else {
			VRMOVIVEND = Math.abs(VRMOVIVEND);
			paymentGrid.currentRow.LABELVRMOVIVEND = UtilitiesService.toCurrency(VRMOVIVEND);
			paymentGrid.currentRow.VRMOVIVEND = VRMOVIVEND;
			ScreenService.closePopup();
            setTimeout(
                function(){
                    paymentGrid.redraw(true);
                }.bind(paymentGrid), 600);
		}
	};

	this.handleShowSideMenu = function (container) {
		OperatorRepository.findOne().then(function (operatorData) {
			if (_.get(operatorData, 'obrigaFechamento', false) === true) {
				container.showMenu = false;
			} else {
				container.showMenu = true;
			}
		});
	};

	this.handleEnterButton = function (args) {
		var keyCode = args.e.keyCode;
		if (keyCode === 9 || keyCode === 13) {
			UtilitiesService.handleCloseKeyboard();
			var widget = args.owner.field.widget;

			if (widget.name === 'openRegisterWidget')
				this.openRegister(widget);
			else if (widget.name === 'paymentValue')
				this.savePaymentValue(widget, widget.container.widgets[0]);
		}
	};
}

Configuration(function (ContextRegister) {
	ContextRegister.register('RegisterController', RegisterController);
});

// FILE: js/controllers/SaleCancelController.js
function SaleCancelController(AccountService, OperatorRepository, PermissionService, ScreenService, UtilitiesService, PaymentService, IntegrationService, IntegrationCappta, PrinterService, templateManager){

	var self = this;

	this.saleCancel = function(widget){
		if (_.get(widget, 'currentRow.CODIGOCUPOM')){
			OperatorRepository.findOne().then(function(operatorData){
				widget.currentRow.CODIGOCUPOM = UtilitiesService.padLeft(widget.currentRow.CODIGOCUPOM, widget.getField('CODIGOCUPOM').maxlength, '0');
				AccountService.saleCancel(operatorData.chave, widget.currentRow.CODIGOCUPOM, widget.CDSUPERVISOR).then(function(saleCancelResult){
					saleCancelResult = saleCancelResult[0];
					if (!saleCancelResult.error) {
						self.clearScreen(widget);


						UtilitiesService.backMainScreen();		
						ScreenService.showMessage(saleCancelResult.message, 'success').then(function(){
							self.handleSaleCancel(saleCancelResult.data);
						}.bind(this));						
					} else {
						ScreenService.showMessage(saleCancelResult.message, 'alert');
					}
				});
			});
		}
	};

	this.openSaleCancel = function(windowName){
		PermissionService.checkAccess('cancelaCupom').then(function(CDSUPERVISOR){
			self.showSaleCancel(windowName, CDSUPERVISOR);
		}.bind(this));
	};

	this.showSaleCancel = function(windowName, CDSUPERVISOR) {
		ScreenService.openWindow(windowName).then(function(){
			templateManager.container.getWidget('saleCancelWidget').CDSUPERVISOR = CDSUPERVISOR;
			ScreenService.toggleSideMenu();
		}.bind(this));
	};

	this.clearScreen = function(widget){
		widget.currentRow = {};
		OperatorRepository.findOne().then(function(operatorData){
			widget.getField('CODIGOCUPOM').maxlength = operatorData.IDTPEMISSAOFOS === 'SAT' ? 6 : 9;
		});
	};

	this.handleSaleCancel = function(saleCancelResult){
		if (!_.isEmpty(saleCancelResult.dadosImpressao)){
			self.printSaleCancel(saleCancelResult.dadosImpressao).then(function(response){
				self.handleTransactionRefound(saleCancelResult);
			}.bind(this));
		} else {
			self.handleTransactionRefound(saleCancelResult);
		}
	};

	this.printSaleCancel = function(dadosImpressao){
		PrinterService.printerCommand(PrinterService.TEXT_COMMAND, dadosImpressao.TEXTOCUPOM);
		PrinterService.printerCommand(PrinterService.BARCODE_COMMAND, dadosImpressao.TEXTOCODIGOBARRAS);
		PrinterService.printerCommand(PrinterService.QRCODE_COMMAND, dadosImpressao.TEXTOQRCODE);
		PrinterService.printerCommand(PrinterService.TEXT_COMMAND, dadosImpressao.TEXTORODAPE);
		PrinterService.printerSpaceCommand();
		
		return PrinterService.printerInit().then(function(result){
			if(result.error)
				ScreenService.alertNotification(result.message);
		});
	};

	this.handleTransactionRefound = function(saleCancelResult){
		var dataTEF = saleCancelResult.dataTEF;
		console.log(dataTEF);
		dataTEF = _.filter(dataTEF, function(tiporece) {
			return PaymentService.checkIfMustCallIntegration(tiporece);

		}.bind(this));
		OperatorRepository.findOne().then(function(operatorData){
			if (!_.isEmpty(dataTEF) && operatorData.IDUTILTEF === 'T' && (!!window.cordova || !!window.ZhCieloAutomation)){
				self.tefRefound(dataTEF, operatorData);
			}
		});
	};

	this.tefRefound = function(dataTEF, operatorData){
		// monta dados para estorno
		dataTEF = _.map(dataTEF, function(tiporece){
			tiporece.IDTPTEF = operatorData.IDTPTEF;
			console.log(tiporece);
			var transactionDate;
			switch(tiporece.IDTPTEF) {
				case '2':{
					tiporece.AUTHKEY = IntegrationCappta.getAUTHKEY(operatorData.AMBIENTEPRODUCAO);
					break;
				}
				case '5':{
					tiporece.DSENDIPSITEF = operatorData.DSENDIPSITEF;
					tiporece.CDLOJATEF = operatorData.CDLOJATEF;
					tiporece.CDTERTEF = operatorData.CDTERTEF;
					transactionDate = tiporece.DTHRINCMOV.split(" ")[0].replace('-', '').replace('-', '');
					tiporece.TRANSACTIONDATE = transactionDate.slice(6, 8) + transactionDate.slice(4, 6) + transactionDate.substring(0, 4);
				} break;
				case '8':{
					transactionDate = tiporece.DTHRINCMOV.split(" ")[0].replace('-', '').replace('-', '');
					tiporece.TRANSACTIONDATE = transactionDate.slice(6, 8) + transactionDate.slice(4, 6) + transactionDate.substring(0, 4);
                } break;

			}
			return tiporece;
		}.bind(self));

		IntegrationService.reversalIntegration(self.mochRemovePaymentSale, dataTEF).then(function(reversalIntegrationResult){
			// chama impressão do comprovante de cancelamento TEF
			if (!reversalIntegrationResult.error){
				ScreenService.showMessage("TEF estornado com sucesso.", 'success');

				if(operatorData.IDTPTEF !== '4') 
					PaymentService.printTEFVoucher(reversalIntegrationResult.data);
			} else {
				ScreenService.showMessage(reversalIntegrationResult.message).then(function(){
					PaymentService.handleRefoundTEFVoucher(reversalIntegrationResult.data);
				});
			}
		}.bind(this));
	};

	this.mochRemovePaymentSale = function(){
		return new Promise.resolve(true);
	};
}

Configuration(function(ContextRegister){
	ContextRegister.register('SaleCancelController', SaleCancelController);
});

// FILE: js/controllers/TransactionsController.js
function TransactionsController(AccountController, OperatorRepository, AccountService, ValidationEngine, TransactionsService, templateManager, ScreenService, UtilitiesService, TransactionsRepository, WindowService){

	var self = this;
	var NRSEQMOVMOB = "";
	var NRSEQMOVMOBToFind = "";

	this.setChaveOnDataSourceFilter = function(widget) {
		OperatorRepository.findAll().then(function (params) {

			widget.dataSourceFilter[3].value = params[0].chave;
			widget.reload();
		});
	};

	this.findTransaction = function(widget){

		var NRADMCODE =  widget.currentRow.NRADMCODE;
		var widgetTransaction = templateManager.container.getWidget('transaction');
		var DTHRFIMMOVini = widget.currentRow.DTHRMOVFIM;
		var DTHRFIMMOVfim = widget.currentRow.DTHRMOVFIM;
		OperatorRepository.findAll().then(function (params) {
			if(!NRADMCODE){
				DTHRFIMMOVini = !DTHRFIMMOVini ? moment(new Date()).format('DD/MM/YYYY') + " 00:00:00" : DTHRFIMMOVini + " 00:00:00";
				DTHRFIMMOVfim = !DTHRFIMMOVfim ? moment(new Date()).format('DD/MM/YYYY') + " 23:59:59" : DTHRFIMMOVfim + " 23:59:59";
				widgetTransaction.dataSourceFilter[0].value = DTHRFIMMOVini;
				widgetTransaction.dataSourceFilter[1].value = DTHRFIMMOVfim;
				widgetTransaction.dataSourceFilter[2].value = '';
				widgetTransaction.dataSourceFilter[3].value = params[0].chave;

			}else{
				widgetTransaction.dataSourceFilter[0].value = '';
				widgetTransaction.dataSourceFilter[1].value = '';
				widgetTransaction.dataSourceFilter[2].value = NRADMCODE;
				widgetTransaction.dataSourceFilter[3].value = params[0].chave;
			}
			templateManager.updateTemplate();
			widgetTransaction.activate();
			widgetTransaction.reload();
		});
	};

	this.sendTransactionEmail = function(widget, args){

		var NRSEQMOVMOB = (widget.currentRow.NRSEQMOVMOB = args.owner.widget.container.getWidget('transaction').currentRow.NRSEQMOVMOB);
		var DSEMAILCLI = typeof widget.currentRow.DSEMAILCLI == "string" ? widget.currentRow.DSEMAILCLI.toLowerCase() : widget.currentRow.DSEMAILCLI;
		var TRANSACTIONEMAIL = args.owner.widget.container.getWidget('transaction').currentRow.DSEMAILCLI;

		if(ValidationEngine.mail(DSEMAILCLI, "").valid){
			if(DSEMAILCLI != TRANSACTIONEMAIL){
				TRANSACTIONEMAIL = DSEMAILCLI;
				TransactionsService.updateTransactionEmail(DSEMAILCLI,NRSEQMOVMOB).then(function(success){
					args.owner.widget.container.getWidget('transaction').reload();
					TransactionsService.sendTransactionEmail(NRSEQMOVMOB, DSEMAILCLI).then(function(){
						ScreenService.showMessage("Email enviado com sucesso.");
					});
				});
			}else{
				TransactionsService.sendTransactionEmail(NRSEQMOVMOB, DSEMAILCLI).then(function(){
					ScreenService.showMessage("Email enviado com sucesso.");
				});
			}
		}else{
			ScreenService.showMessage(ValidationEngine.mail(DSEMAILCLI, "").message);
		}

		widget.isVisible = false;
		ScreenService.closePopup();

	};

	this.widgetEmailVisibility = function(widget){
		widget.isVisible = false;
		ScreenService.closePopup();
	};

	this.confirmTransactionEmail = function(widget,args){
		var popupEmail = args.owner.widget.container.getWidget('popupEmail');
		popupEmail.currentRow.DSEMAILCLI = typeof widget.currentRow.DSEMAILCLI == "string" ? widget.currentRow.DSEMAILCLI.toLowerCase() : widget.currentRow.DSEMAILCLI;
		popupEmail.isVisible = true;
		ScreenService.openPopup(popupEmail);
	};

	this.cancelTransaction = function(widget){

		var cancelPayment = 1;
		var NRSEQMOVMOB = widget.currentRow.NRSEQMOVMOB;
		self.NRSEQMOVMOBToFind = NRSEQMOVMOB;
		self.NRSEQMOVMOB = NRSEQMOVMOB;

		OperatorRepository.findAll().then(function (params) {
			TransactionsService.findRowToCancel(params[0].chave, NRSEQMOVMOB).then(function(rowToCancelData){

				var dataset = {
					chave : params[0].chave,
					CDVENDEDOR : params[0].CDVENDEDOR,
					NRVENDAREST : rowToCancelData[0].NRVENDAREST,
					NRMESA : rowToCancelData[0].NRMESA,
					NRLUGARMESA : rowToCancelData[0].NRLUGARMESA,
					VRMOV : (rowToCancelData[0].VRMOV *-1) ,
					NRADMCODE : rowToCancelData[0].NRADMCODE,
					DSBANDEIRA : rowToCancelData[0].DSBANDEIRA,
					CDTIPORECE : rowToCancelData[0].CDTIPORECE,
					IDTPTEF : rowToCancelData[0].IDTPTEF,
					NRCOMANDA : rowToCancelData[0].NRCOMANDA,
					IDTIPMOV : rowToCancelData[0].IDTIPMOV
				};
				AccountService.beginPaymentAccount(dataset.chave, dataset.CDVENDEDOR, dataset.NRVENDAREST, dataset.NRCOMANDA, dataset.NRMESA, dataset.NRLUGARMESA, dataset.CDTIPORECE, dataset.IDTIPMOV, dataset.VRMOV, dataset.DSBANDEIRA, dataset.IDTPTEF).then(function(response){

					self.NRSEQMOVMOB = response[0].NRSEQMOVMOB;

					if(widget.currentRow.IDTPTEF > 1){ // Se for TEF (não cancela caso for pagamento digitado (SITEF))
						if(window.ZhNativeInterface){
							var administrativeCode = widget.currentRow.NRADMCODE;
							var paymentId = response[0].NRSEQMOVMOB;
							var administrativeTask = "1"; // "1" para cancelamento
							var administrativePassword = "";
							var paymentType = widget.currentRow.IDTPTEF;
							ZhNativeInterface.tefAdministrativeTask(paymentType, administrativeTask, administrativeCode, administrativePassword, paymentId);
						} else {
							self.tefmock(); // Para teste no computador
							// ScreenService.showMessage('ZhNativeInterface não encontrada.');
						}
					} else {
						if(widget.currentRow.IDTPTEF == 1){
							// cancelamento SITEF
							ScreenService.showMessage("Esse método de pagamento não suporta o cancelamento da transação !");
						} else {

							var dataset = {
								NRSEQMOVMOB : self.NRSEQMOVMOB,
								NRSEQMOB : null,
								DSBANDEIRA : null,
								NRADMCODE :  null,
								IDADMTASK : '1',
								IDSTMOV : '1',
								TXMOVUSUARIO : null,
								TXMOVJSON : null,
								CDNSUTEFMOB : null,
								TXPRIMVIATEF : null,
								TXSEGVIATEF : null,
								transactionStatus : '1',
							};

							self.finishPayment(dataset);
						}
					}
				});
			});
		});
	};

	this.cancelTransactionCappta = function(widget){

		var cancelPayment = 1;
		var NRSEQMOVMOB = widget.currentRow.NRSEQMOVMOB;
        self.NRSEQMOVMOBToFind = NRSEQMOVMOB;
		self.NRSEQMOVMOB = NRSEQMOVMOB;

        OperatorRepository.findOne().then(function (params) {
            if (widget.currentRow.IDTPTEF > 1) { // Se for TEF (não cancela caso for pagamento digitado (SITEF))
                if (window.ZhNativeInterface && ZhNativeInterface.tefAdministrativeTask) {
                    var administrativeCode = widget.currentRow.NRADMCODE;
                    var paymentId = self.NRSEQMOVMOB;
                    var administrativeTask = "1"; // "1" para cancelamento
                    var administrativePassword = "";
                    var paymentType = widget.currentRow.IDTPTEF;
                    try {
                    	ZhNativeInterface.tefAdministrativeTask(paymentType, administrativeTask, administrativeCode, administrativePassword, paymentId);
                	} catch(error) {
                		ScreenService.showMessage('Falha na comunicação com o aplicativo Cappta. Verifique se o mesmo está instalado.');
                		console.log(error);
                	}
                } else {
                    self.tefmock(); // Para teste no computador
                    // ScreenService.showMessage('ZhNativeInterface não encontrada.');
                }
            } else {
                if(widget.currentRow.IDTPTEF == 1){
                    // cancelamento SITEF
                    ScreenService.showMessage("Esse método de pagamento não suporta o cancelamento da transação !");
                } else {

                    var dataset = {
                        NRSEQMOVMOB : self.NRSEQMOVMOB,
                        NRSEQMOB : null,
                        DSBANDEIRA : null,
                        NRADMCODE :  null,
                        IDADMTASK : '1',
                        IDSTMOV : '1',
                        TXMOVUSUARIO : null,
                        TXMOVJSON : null,
                        CDNSUTEFMOB : null,
                        TXPRIMVIATEF : null,
                        TXSEGVIATEF : null,
                        transactionStatus : '1',
                    };

                    self.finishPayment(dataset);
                }
            }
		});
	};

	this.finishPayment = function(dataset){
        AccountService.finishPaymentAccount(dataset).then(function(response){
            if(dataset.transactionStatus === 1 && dataset.JSONTEFDetails !== null){
                ScreenService.openWindow('transactions').then(function(){
                    var emailPopup = templateManager.container.getWidget("sendEmail");
                    emailPopup.currentRow = response[0];
                    emailPopup.currentRow.RECEIPT = dataset.JSONTEFDetails.customer_receipt;
                    emailPopup.currentRow.RECEIPT = emailPopup.currentRow.RECEIPT.replace(/'/g, '');
                    ScreenService.openPopup(emailPopup);
                });
                TransactionsService.updateCanceledTransaction(self.NRSEQMOVMOBToFind).then(function(){
                    self.findTransaction(templateManager.container.getWidget("transactionsFilter"));
                });
            } else if(dataset.transactionStatus === 1 && dataset.JSONTEFDetails === null) {
                ScreenService.showMessage("Cancelamento Feito com Sucesso!");
                TransactionsService.updateCanceledTransaction(self.NRSEQMOVMOBToFind).then(function(){
                    self.findTransaction(templateManager.container.getWidget("transactionsFilter"));
                });
            } else {
                ScreenService.showMessage(dataset.TXMOVUSUARIO);
            }
            if(templateManager.container.name == 'transactions'){
                templateManager.container.widgets[0].reload();
            }
        });
    };

	window.tefAdministrativeResult = function(result) {
    	var capptaErrors = {
			1: 'Não autenticado/Alguma das informações fornecidas para autenticação não é válida',
			2: 'Cappta Android está sendo inicializado',
			3: 'Formato da requisição recebida pelo Cappta Android é inválido',
			4: 'Operação cancelada pelo operador',
			5: 'Pagamento não autorizado/pendente/não encontrado',
			6: 'Pagamento ou cancelamento negados pela rede adquirente ou falta de conexão com internet',
			7: 'Erro interno no Cappta Android',
			8: 'Erro na comunicação com o Cappta Android'
    	};

		var JSONTEF = JSON.parse(result)[0];
		var userMessage = _.get(JSONTEF, 'tef_request_details.user_message');
    	if (userMessage == "Estorno realizado" ) {
			var dataset = self.createUpdateTransactionObject(JSONTEF);
        	self.finishPayment(dataset);
        } else {
        	var defaultMessage = _.get(capptaErrors, _.get(JSONTEF, 'tef_request_type'), 'Falha na comunicação com o aplicativo Cappta. Verifique se o mesmo está instalado.');
        	ScreenService.showMessage(userMessage || defaultMessage);
        }
	};

	this.isNotEmpty = function(value) {
		if (_.isString(value)) {
			return !_.isEmpty(value);
		} else {
			return !_.isNil(value);
		}
	};

	this.createUpdateTransactionObject = function(JSONTEF) {
		var JSONTEFDetails = JSONTEF.tef_request_details;
		var dataset = {};
		dataset.JSONTEFDetails = JSONTEFDetails;
		dataset.NRSEQMOVMOB = self.NRSEQMOVMOB;
		dataset.TXMOVJSON = JSON.stringify(JSONTEF);

		dataset.NRSEQMOB = self.isNotEmpty(JSONTEFDetails.unique_sequential_number) ? JSONTEFDetails.unique_sequential_number : null;
		dataset.DSBANDEIRA = self.isNotEmpty(JSONTEFDetails.card_brand_name) ? JSONTEFDetails.card_brand_name : null;
		dataset.NRADMCODE = self.isNotEmpty(JSONTEFDetails.administrative_code) ? JSONTEFDetails.administrative_code : null;
		dataset.IDADMTASK = self.isNotEmpty(JSONTEFDetails.administrative_task) ? JSONTEFDetails.administrative_task : null;
		dataset.IDSTMOV = 0;//transação cancelada
		dataset.TXMOVUSUARIO = self.isNotEmpty(JSONTEFDetails.user_message) ? JSONTEFDetails.user_message : null;
		dataset.CDNSUTEFMOB = self.isNotEmpty(JSONTEFDetails.unique_sequential_number) ? JSONTEFDetails.unique_sequential_number : null;
		dataset.TXPRIMVIATEF = self.isNotEmpty(JSONTEFDetails.merchant_receipt) ? JSONTEFDetails.merchant_receipt.replace(/'/g, '') : null;
		dataset.TXSEGVIATEF = self.isNotEmpty(JSONTEFDetails.customer_receipt) ? JSONTEFDetails.customer_receipt.replace(/'/g, '') : null;
		dataset.transactionStatus = self.isNotEmpty(JSONTEFDetails.payment_transaction_status) ? JSONTEFDetails.payment_transaction_status : null;

		return dataset;
	};

	this.tefmock = function() {
		// not mocking at the moment
    	if (false) {
			var result =
				[
				    {
				        "tef_request_type": 4,
				        "tef_request_details": {
				            "payment_transaction_status": 1,
				            "acquirer_affiliation_key": "0009448512329101",
				            "acquirer_name": "Elavon",
				            "card_brand_name": "MAESTRO",
				            "acquirer_authorization_code": "SIMULADOR",
				            "payment_product": 1,
				            "payment_installments": 1,
				            "payment_amount": 16,
				            "available_balance": null,
				            "unique_sequential_number": 21007,
				            "acquirer_unique_sequential_number": null,
				            "acquirer_authorization_datetime": "2016-07-15 11:25:42",
				            "administrative_code": "07520701019",
				            "administrative_task": 1,
				            "user_message": null,
				            "merchant_receipt": "''\r\n'**VIALOJISTA**'\r\n'ELAVON'\r\n'MAESTRO-DEBITOAVISTA'\r\n'************2979'\r\n'ESTAB000948512329101'\r\n'15/07/1610: 25: 50'\r\n'AUT=SIMULADORDOC=21007'\r\n'VALOR=1,50'\r\n'CONTROLE=07520701019'\r\n'CAPPTACARTOES'",
				            "customer_receipt": "''\r\n'HOMOLOGA'\r\n'40.841.182/0001-48'\r\n'**VIACLIENTE**'\r\n'ELAVON'\r\n'MAESTRO-DEBITOAVISTA'\r\n'************2979'\r\n'ESTAB000948512329101'\r\n'15/07/1610: 25: 50'\r\n'AUT=SIMULADORDOC=21007'\r\n'VALOR=1,50'\r\n'CONTROLE=07520701019'\r\n'CAPPTACARTOES'",
				            "reduced_receipt": "'ELAVON-NL000948512329101'\r\n'MAESTRO-************2679'\r\n'AUT=SIMULADORDOC=21007'\r\n'VALOR=1,50CONTROLE=07520701019'"
				        }
				    }
				];

			var resultString = JSON.stringify(result);

			window.tefAdministrativeResult(resultString);
    	} else {
    		ScreenService.showMessage('A Webview do Android não foi encontrada.');
    	}
	};

	this.openFilterTransactionsPopup = function(){

		var popupTransactionsFilter = templateManager.container.getWidget('transactionsFilter');
		popupTransactionsFilter.isVisible = true;
		ScreenService.openPopup(popupTransactionsFilter);

	};
	this.closeFilterTransactionsPopup = function(){

		var popupTransactionsFilter = templateManager.container.getWidget('transactionsFilter');
		popupTransactionsFilter.isVisible = false;

		ScreenService.closePopup();
	};

	this.clearField = function(widget, fieldID){

		var NRADMCODE = widget.currentRow.NRADMCODE;
		var DTHRMOVFIM = widget.currentRow.DTHRMOVFIM;

		if(fieldID == 1){
			// widget.currentRow.NRADMCODE = '';
			widget.getField('NRADMCODE').applyDefaultValue();
		}else{
			if(fieldID == 2)
			widget.getField('DTHRMOVFIM').applyDefaultValue();
		}

		templateManager.updateTemplate();
	};


}


Configuration(function(ContextRegister) {
	ContextRegister.register('TransactionsController', TransactionsController);
});

// FILE: js/controllers/WaiterListGroupedController.js
function WaiterListGroupedController ($scope) {

	$scope.getCategoriesByPosition = function(cart, groupProperty) {
		var result = [];
		if(cart && cart.length) {
			var groupedCart = {};
			for (var i in cart){
				if (!groupedCart[cart[i][groupProperty]]){
					groupedCart[cart[i][groupProperty]] = [cart[i]];
				}
				else {
					groupedCart[cart[i][groupProperty]].push(cart[i]);
				}
			}
			for (var k in groupedCart){
				result.push(parseInt(k));
			}
		}
		return result.sort(function(a,b){ return a - b; }).map(function (item) { return 'posição ' + item; });
	};

    $scope.getCategoriesByGroup = function(cart, groupProperty) {
        var result = [];
        if(cart && cart.length) {
            var groupedCart = {};
            for (var i in cart){
                if (!groupedCart[cart[i][groupProperty]]){
                    groupedCart[cart[i][groupProperty]] = [cart[i]];
                }
                else {
                    groupedCart[cart[i][groupProperty]].push(cart[i]);
                }
            }
            for (var k in groupedCart){
                result.push(k);
            }
        }
        var final = result.sort(function(a,b){
            if (a > b) return 1;
            if (a < b) return -1;
            return 0;
        }).map(function (item) { return item; });
        return final;
    };

    $scope.listGroupedFieldSelect = function(row, field){
        if (!row.__isSelected){
            field.dataSource.addCheckedRows(row);
        }
        else {
            field.dataSource.removeCheckedRows(row);
        }
    };

}

// FILE: js/controllers/WaiterNamedPositions.js
function WaiterNamedPositions($scope, $timeout, ApplicationContext, $rootScope, templateManager, ScreenService) {

	function unselectAllPositions() {
		if (_.get($scope, 'field._buttons')) {
			$scope.field._buttons.forEach(function(currentButton) {
				currentButton.selected = false;
			});
			$scope.field.position = [];
		}
	}

	$scope.init = function() {
		this.stateService = ApplicationContext.WaiterNamedPositionsState;

		$scope.startWith              = this.stateService.startWith;
		$scope.flagNextAndPrev        = this.stateService.flagNextAndPrev;
		$scope.showPrev               = this.stateService.showPrev;
		$scope.showNext               = this.stateService.showNext;
		$scope.oldNrTotalPosicoes     = this.stateService.oldNrTotalPosicoes;
		$scope.oldMaxButtons          = this.stateService.oldMaxButtons;
		$scope.maxButtons             = this.stateService.maxButtons;
		$scope.currentPage            = this.stateService.currentPage;
		$scope.oldCurrentPage         = this.stateService.oldCurrentPage;
		$scope.finishWith             = this.stateService.finishWith;
		$scope.clientMapping          = this.stateService.clientMapping;
		$scope.consumerMapping        = this.stateService.consumerMapping;
		$scope.positionNamedMapping   = this.stateService.positionNamedMapping;
		$scope.numberOfButtons        = this.stateService.numberOfButtons;
		$scope.pageHistory            = this.stateService.pageHistory;
		$scope.oldTotalPosicoes       = this.stateService.oldTotalPosicoes;
		$scope.currentPositionsObject = this.stateService.currentPositionsObject;

		if (this.stateService.mustUnselect) {
			unselectAllPositions();
			this.stateService.mustUnselect = false;
		}
	};

	$scope.init();

	$scope.$on('$destroy', function() {
		this.stateService = ApplicationContext.WaiterNamedPositionsState;

		this.stateService.startWith              = $scope.startWith;
		this.stateService.flagNextAndPrev        = $scope.flagNextAndPrev;
		this.stateService.showPrev               = $scope.showPrev;
		this.stateService.showNext               = $scope.showNext;
		this.stateService.oldNrTotalPosicoes     = $scope.oldNrTotalPosicoes;
		this.stateService.oldMaxButtons          = $scope.oldMaxButtons;
		this.stateService.maxButtons             = $scope.maxButtons;
		this.stateService.currentPage            = $scope.currentPage;
		this.stateService.oldCurrentPage         = $scope.oldCurrentPage;
		this.stateService.finishWith             = $scope.finishWith;
		this.stateService.clientMapping          = $scope.clientMapping;
		this.stateService.consumerMapping        = $scope.consumerMapping;
		this.stateService.positionNamedMapping   = $scope.positionNamedMapping;
		this.stateService.numberOfButtons        = $scope.numberOfButtons;
		this.stateService.pageHistory            = $scope.pageHistory;
		this.stateService.oldTotalPosicoes       = $scope.oldTotalPosicoes;
		this.stateService.currentPositionsObject = $scope.currentPositionsObject;
	});

	$scope.$watch(function(scope){return scope.currentPositionsObject;}, function() {
		$timeout(paginatePositions);
	}, true);

	$scope.prev = function() {
        if ($scope.pageHistory == undefined) $scope.pageHistory = [0];
		$scope.startWith = $scope.pageHistory.pop();
		$scope.currentPage.page--;
		$scope.currentPage.paginated = false;
		$scope.flagNextAndPrev = true;
		$scope.buildNamedPositions(false, true);
		handleNavigationButtons();
	};

	$scope.next = function() {
        if ($scope.pageHistory)	$scope.pageHistory.push($scope.startWith);
		$scope.startWith = $scope.finishWith;
		$scope.currentPage.page++;
		$scope.currentPage.paginated = false;
		handleNavigationButtons();
		$scope.buildNamedPositions(false, true);
	};

	$scope.buildNamedPositions = function(useMaxButtons, isChangingPage) {
		var nrTotalPosicoes = $scope.field.dataSource.data[0].NRPOSICAOMESA;
		var clientChanged = $scope.field.dataSource.data[0].clientChanged;
		if (clientChanged || useMaxButtons || isChangingPage || nrTotalPosicoes != $scope.oldTotalPosicoes) {
			if (clientChanged) {
				$scope.field.dataSource.data[0].clientChanged = false;
			}
			if ($scope.currentPage && (clientChanged || nrTotalPosicoes != $scope.oldTotalPosicoes)) {
				$scope.currentPage.paginated = false;
			}
			var isSubtracting = $scope.oldTotalPosicoes > nrTotalPosicoes;
			$scope.oldTotalPosicoes = nrTotalPosicoes;

			$scope.field = $scope.field || {};
			$scope.field._buttons = $scope.field._buttons || [];
			if (!$scope.startWith) {
				$scope.startWith = 0;
			}
			var result = [];
			var idx = $scope.startWith;
			var numberOfButtons = 0;
			for (idx; idx < nrTotalPosicoes; idx++) {
				numberOfButtons++;
				result.push(idx);
				if (_.findIndex($scope.field._buttons, {'index': idx}) === -1) {
					$scope.field._buttons.push({'index': idx, 'selected': false});
				}
				$scope.field._isStatusChanged = false;
				if (useMaxButtons && numberOfButtons == $scope.maxButtons + 1) {
					break;
				}
			}
			$scope.numberOfButtons = numberOfButtons;
			$scope.finishWith = idx;
			if (useMaxButtons) {
				$scope.finishWith = $scope.finishWith + 1;
			}

			if (typeof $scope.widget.position == "number") {
				$scope.checkActualPosition($scope.widget.position, result);
			}
			$scope.currentPositionsObject = result;

			if (isChangingPage || isSubtracting) {
				handleNavigationButtons(isSubtracting);
			}
		}

		return $scope.currentPositionsObject;
	};

	function paginatePositions() {
		// only paginates if there are buttons to paginate
        if ($scope.currentPage == null){
            $scope.currentPage = {
                page: 0,
                paginated: false
            };
        }
		if ($('#odh-named-positions'+$scope.field.id).width()) {
			var nrTotalPosicoes = $scope.field.dataSource.data[0].NRPOSICAOMESA;
			$scope.maxButtons = calculateHowManyButtonsFit();
			if (!$scope.currentPage.paginated) {
			 // && (nrTotalPosicoes != $scope.oldNrTotalPosicoes) || ($scope.maxButtons != $scope.oldMaxButtons) || ($scope.currentPage.page != $scope.oldCurrentPage)) {
				$scope.oldCurrentPage = $scope.currentPage.page;
				$scope.oldNrTotalPosicoes = nrTotalPosicoes;
				$scope.oldMaxButtons = $scope.maxButtons;

				if ($scope.maxButtons && (nrTotalPosicoes > $scope.maxButtons)) {
					$scope.buildNamedPositions(true, false);
					handleNavigationButtons();
					$scope.currentPage.paginated = true;
					templateManager.updateTemplate();
				}
			}
		}
	}

	function handleNavigationButtons(isSubtracting) {
		var nrTotalPosicoes = $scope.field.dataSource.data[0].NRPOSICAOMESA;
		if ($scope.startWith > 0) {
			$scope.showPrev = true;
		} else {
			$scope.showPrev = false;
		}
		var maxButtons = $scope.maxButtons;
		if (isSubtracting) {
			maxButtons++;
		}
		if (nrTotalPosicoes > $scope.startWith + maxButtons) {
			$scope.showNext = true;
		} else {
			$scope.showNext = false;
		}
	}

	function calculateHowManyButtonsFit() {
		// this function must run only after the buttons appear on the screen (after the buildNamedPositions)
		var widgetWidth = $('#odh-named-positions'+$scope.field.id).width() - 100;
		var buttonsToCalculate = $('#odh-named-positions'+$scope.field.id +'>.odh-button-space');
		var totalButtonWidth = 0;
		var buttonToBreak = 0;
		for (var idx = 0; idx < buttonsToCalculate.length; idx++) {
			var currentButton = $(buttonsToCalculate[idx]);

			if (currentButton.width) {
				totalButtonWidth += currentButton.width();
			}
			buttonToBreak = idx;
			if (totalButtonWidth > widgetWidth) {
				break;
			}
		}
		var maxButtons = totalButtonWidth <= widgetWidth ? buttonsToCalculate.length - 2 : buttonToBreak - 2;
		if (maxButtons < 0) {
			maxButtons = 0;
		}
		return maxButtons;
	}

	$scope.getPositionName = function(position) {
		var clientMapping = $scope.field.dataSource.data[0].clientMapping;
		var consumerMapping = $scope.field.dataSource.data[0].consumerMapping;
		var positionNamedMapping = $scope.field.dataSource.data[0].positionNamedMapping;

		if (position > 0) {
			if (positionNamedMapping[position]){
				return ' - ' + buildPositionName(positionNamedMapping[position].DSCONSUMIDOR);
			} else if (consumerMapping[position]) {
				return ' - ' + buildPositionName(consumerMapping[position].NMCONSUMIDOR);
			} else if (clientMapping[position]) {
				return ' - ' + buildPositionName(clientMapping[position].NMRAZSOCCLIE);
			} else {
				return '';
			}
		} else {
			return '';
		}
	};

	function buildPositionName(name) {
		if (name && name.length > 10) {
			return name.substr(0, 9) + '...';
		} else {
			return name;
		}
	}

	$scope.checkActualPosition = function(currentPosition, result) {
		var isOnScreen = result.some(function(position) {
			return currentPosition == position;
		});

		if (!isOnScreen && !$scope.flagNextAndPrev) {
			$scope.next();
			$scope.flagNextAndPrev = false;
		}
	};

	$scope.select = Util.buildDebounceMethod(function(widget, product, position) {
        widget.currentRow = product;
        if (product.IDTIPORECE) {
            ApplicationContext.PaymentController.receivePayment(widget, product);
        }
        else {
            ApplicationContext.AccountController.handleSelectedProduct(widget, product, position);
        }
    }, 450, true);

	$scope.toggleButtonSelectedStatus = function(field, buttonIndex, ignoreCustomFunction) {
		field.newPosition = buttonIndex;
		if (!field.toggleButtonSelectedStatus) {
			field.toggleButtonSelectedStatus = $scope.toggleButtonSelectedStatus;
		}

		var qtPositionsInArray = parseInt(field.dataSource.data[0].NRPOSICAOMESA);

		field._buttons[buttonIndex].selected = !field._buttons[buttonIndex].selected;

		field.position = [];
		for (var p = 0; p < qtPositionsInArray; p++) {
			if ($scope.isButtonSelected(field, p)) {
				field.position.push(p);
			}
		}

		field._isStatusChanged = true;
		if (field) {
			if (field.forceFunction && field.customFunction) {
				var customFunction = _.get(ApplicationContext, field.customFunction);
				if (customFunction && !ignoreCustomFunction) {
					customFunction(field);
				}
			}
		}
	};

	$scope.isButtonSelected = function(field, buttonIndex){
        if (_.isEmpty(field._buttons)){
            $scope.buildNamedPositions(true, false);
        }
		return field._buttons[buttonIndex].selected;
	};
}

// FILE: js/controllers/WaiterOrdersProdCtrl.js
function WaiterOrdersProdCtrl($scope, ApplicationContext, $rootScope, templateManager) {
	$scope.searchList = $rootScope.searchList;

	$scope.init = function() {
		$scope.startWith = 0;
		$scope.flagNextAndPrev = false;
	};

	$scope.$watch('$rootScope.searchList', function(newData) {
		$scope.searchList = newData;
		if (newData) {
			$scope.widget.dataSource.filter({}).then(function(filtered) {
				$scope.widget.dataSource.data = filtered;
			});
		}
	});

	$scope.finishWith = 0;

	$scope.$watch($scope.getNrTotalButtons, function() {
		templateManager.updateTemplate();
	});

	$scope.getNrTotalButtons = function() {
		var widgetWidth = $('.zh-positions-container').width();
		var buttonSize = $('.odh-button-space:last').width();
        if ($('.lower-margin').width() != null){
            widgetWidth = $('.col-xs-12.col-sm-12.col-md-12').width();
        }
		if (buttonSize === null || buttonSize === 0) buttonSize = 52;
		var buttons = 0;
		while (buttons*buttonSize < widgetWidth) buttons++;
		buttons -= 2;
		if(buttons < 3){
			buttons = 3;
		}
		return buttons;
	};

	$scope.getQtNavegators = function(startWith, nrTotalPosicoes, nrTotalButtons) {
		var qt = 0;
		if (startWith > 0) {
			qt++;
			nrTotalButtons--;
		}
		if ((nrTotalPosicoes - startWith) > nrTotalButtons) {
			qt++;
		}
		return qt;
	};

	$scope.prev = function() {
		var newFinishWith = $scope.startWith - 1;
		var positionsOnScreen = $scope.getNrTotalButtons() - 2;
		var newStartWith = newFinishWith - positionsOnScreen + 1;
		$scope.startWith = (newStartWith <= 1) ? 0 : newStartWith;
		$scope.flagNextAndPrev = true;
	};

	$scope.next = function() {
		$scope.startWith = $scope.finishWith + 1;
		$scope.flagNextAndPrev = true;
	};

	$scope.getQtPositionsInScreen = function(startWith, nrTotalPosicoes) {
		return $scope.getNrTotalButtons() - $scope.getQtNavegators(startWith, nrTotalPosicoes, $scope.getNrTotalButtons());
	};

	$scope.lastNrTotalPosicoes = null;
    $scope.lastStartWith = null;
    $scope.lastResult = null;

    $scope.buildPositionsArray = function(nrTotalPosicoes, startWith) {
        if ((nrTotalPosicoes !== $scope.lastNrTotalPosicoes) || (startWith !== $scope.lastStartWith) ||($scope.field && $scope.field.forceReload)) {
            if($scope.field && $scope.field.forceReload){
            	$scope.field.forceReload = false;
            }
            $scope.lastNrTotalPosicoes = nrTotalPosicoes;
            $scope.lastStartWith = startWith;
			$scope.field = $scope.field || {};
			$scope.field._buttons = $scope.field._buttons || [];

			if (!startWith) startWith = 0;

			var result = [];
			var qtPositionsInArray = $scope.getQtPositionsInScreen(startWith, nrTotalPosicoes);

			var i = 0;
			for (i = startWith; i < (startWith + qtPositionsInArray) && i < nrTotalPosicoes; i++) {
				result.push(i);
				$scope.field._buttons.push({'index': i, 'selected': false});
				$scope.field._isStatusChanged = false;
			}

			$scope.finishWith = i - 1;

			/* Check when positions must be checked */
			if (typeof $scope.widget.position == "number") {
				$scope.checkActualPosition($scope.widget.position, result);
			}

			$scope.lastResult = result;
            return result;
        } else {
            return $scope.lastResult || [];
        }
    };

	$scope.getPositionName = function(position) {
		var clientMapping = $scope.field.dataSource.data[0].clientMapping;
		var consumerMapping = $scope.field.dataSource.data[0].consumerMapping;

		if (position > 0) {
			if (consumerMapping[position]) {
				return ' - ' + buildPositionName(consumerMapping[position].NMCONSUMIDOR);
			} else if (clientMapping[position]) {
				return ' - ' + buildPositionName(clientMapping[position].NMRAZSOCCLIE);
			} else {
				return '';
			}
		} else {
			return '';
		}
	};

	function buildPositionName(name) {
		if (name.length > 10) {
		   name = name.substr(0, 9) + '...';
		}
		return name;
	}

	$scope.clientMapping = {};
	$scope.consumerMapping = {};

	$scope.checkActualPosition = function(currentPosition, result) {
		var isOnScreen = result.some(function(position) {
			return currentPosition == position;
		});

		if (!isOnScreen && !$scope.flagNextAndPrev) {
			$scope.next();
			$scope.flagNextAndPrev = false;
		}
	};
	$scope.idealTextColor = idealTextColor;

    $scope.select = Util.buildDebounceMethod(function(widget, product, position) {
        widget.currentRow = product;
        if (product.IDTIPORECE) {
            ApplicationContext.PaymentController.receivePayment(widget, product);
        }
        else {
			ApplicationContext.AccountController.handleSelectedProduct(widget, product, position);
        }
    }, 450, true);

	$scope.toggleButtonSelectedStatus = function(field, buttonIndex, ignoreCustomFunction) {
		field.newPosition = buttonIndex;
		if (!field.toggleButtonSelectedStatus) {
			field.toggleButtonSelectedStatus = $scope.toggleButtonSelectedStatus;
		}

		var qtPositionsInArray = parseInt(field.dataSource.data[0].NRPOSICAOMESA);

		field._buttons[buttonIndex].selected = !field._buttons[buttonIndex].selected;

		field.position = [];
		for (var p = 0; p < qtPositionsInArray; p++) {
			if ($scope.isButtonSelected(field, p)) {
				field.position.push(p);
			}
		}

		/** poo-taa-rea **/
		field._isStatusChanged = true;
		if (field) {
			if (field.touchstart) {
				field.touchstart();
			} else if (field.click) {
				field.click();
			}

			if (field.forceFunction && field.customFunction) {
				var customFunction = _.get(ApplicationContext, field.customFunction);
				if (customFunction && !ignoreCustomFunction) {
					customFunction(field);
				}
			}
		}
	};

	$scope.isButtonSelected = function(field, buttonIndex){
		return field._buttons[buttonIndex].selected;
	};

	$scope.onLongTabProduct = function(widget, product){
		if (product.CDPRODUTO){
			var popupDetalhes = widget.container.getWidget('popupDetalhesProduto');
			popupDetalhes.currentRow = product;
			ApplicationContext.ScreenService.openPopup(popupDetalhes);
		}
	};
}