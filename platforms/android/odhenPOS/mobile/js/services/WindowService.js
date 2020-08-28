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
        COMPRE_GANHE_SCREEN: "compreGanhe",

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
