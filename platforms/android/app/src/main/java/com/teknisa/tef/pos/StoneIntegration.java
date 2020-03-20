package com.teknisa.tef.pos;

import android.content.ComponentName;
import android.content.Context;
import android.content.Intent;
import android.content.ServiceConnection;
import android.os.Bundle;
import android.os.IBinder;
import android.os.RemoteException;
import android.util.Log;
import android.widget.Toast;

import com.teknisa.tef.PaymentData;
import com.teknisa.tef.TransactionMessenger;
import com.teknisa.tef.Util;
import com.teknisa.tef.printer.PrintCallback;
import com.teknisa.tef.printer.Printer;
import com.teknisa.tef.printer.StonePrinter;
import com.usdk.apiservice.aidl.UDeviceService;
import com.usdk.apiservice.aidl.constants.RFDeviceName;
import com.usdk.apiservice.aidl.rfreader.OnPassAndActiveListener;
import com.usdk.apiservice.aidl.rfreader.URFReader;

import org.apache.cordova.CallbackContext;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.math.BigInteger;
import java.util.List;

import br.com.stone.posandroid.providers.PosTransactionProvider;
import stone.application.StoneStart;
import stone.application.enums.Action;
import stone.application.enums.InstalmentTransactionEnum;
import stone.application.enums.ReceiptType;
import stone.application.enums.TransactionStatusEnum;
import stone.application.enums.TypeOfTransactionEnum;
import stone.application.interfaces.StoneActionCallback;
import stone.application.interfaces.StoneCallbackInterface;
import stone.database.transaction.TransactionDAO;
import stone.database.transaction.TransactionObject;
import static stone.environment.Environment.PRODUCTION;
import static stone.environment.Environment.SANDBOX;
import stone.providers.ActiveApplicationProvider;
import stone.providers.CancellationProvider;
import stone.providers.ReversalProvider;
import stone.user.UserModel;
import stone.utils.Stone;

public class StoneIntegration implements PosIntegration, StoneActionCallback, PrintCallback {

    private StoneIntegration instance;
    private boolean cancellingOrder = false;
    private boolean printingCoupon = false;
    private CallbackContext callbackContext;
    protected TransactionObject transactionObject;
    private PosTransactionProvider transactionProvider;
    private PaymentData paymentData;
    private StonePrinter printer;
    private TransactionMessenger transactionMessenger;
    private static TransactionObject lastTransactionObject;
    private static Context applicationContext;
    private static final String STONE_CODE = "808832092";

    private UDeviceService deviceService;
    private URFReader rfReader;

    public void init(JSONArray args, Context context, TransactionMessenger transactionMessenger) {
        try {
            JSONObject data = new JSONObject(args.getString(0));
            String stoneCode = data.getString("activationCode");
            String environment = data.getString("environment");
            this.init(stoneCode, environment, context, transactionMessenger);
        } catch (JSONException e) {
            e.printStackTrace();
        }
    }

    public void init(String stoneCode, String environment, Context context, TransactionMessenger transactionMessenger) {
        applicationContext = context;
        this.transactionMessenger = transactionMessenger;

        Stone.setEnvironment(environment.equals("PRODUCTION") ? PRODUCTION : SANDBOX);

        List<UserModel> userList = StoneStart.init(applicationContext);
        Stone.setAppName("ODHENPOS");

        // Quando é retornado null, o SDK ainda não foi ativado
        if (userList == null || !userList.get(0).getStoneCode().equals(stoneCode)) {
            ActiveApplicationProvider activeApplicationProvider = new ActiveApplicationProvider(applicationContext);
            activeApplicationProvider.setDialogMessage("Ativando o Stone Code");
            activeApplicationProvider.setDialogTitle("Aguarde");
            activeApplicationProvider.useDefaultUI(true);
            activeApplicationProvider.setConnectionCallback(new StoneCallbackInterface() {
                public void onSuccess() {
                    transactionMessenger.onReturnValue("", 0);
                }

                public void onError() {
                    transactionMessenger.onReturnValue("", -1);
                    transactionMessenger.onReturnValue("", -1);
                }
            });
            activeApplicationProvider.activate(stoneCode);
        } else {
            transactionMessenger.onReturnValue("", 0);
        }
    }

    private void bindService() {
        Intent service = new Intent("com.usdk.apiservice");
        service.setPackage("com.usdk.apiservice");
        applicationContext.bindService(service, new ServiceConnection() {
            @Override
            public void onServiceConnected(ComponentName name, IBinder service) {
                deviceService = UDeviceService.Stub.asInterface(service);
                try {
                    getRFCardReader();
                } catch (RemoteException e) {
                    e.printStackTrace();
                }
            }

            @Override
            public void onServiceDisconnected(ComponentName name) {

            }
        }, Context.BIND_AUTO_CREATE);
    }

    private URFReader getRFCardReader() throws RemoteException {
        if (rfReader == null) {
            Bundle param = new Bundle();
            param.putString("rfDeviceName", RFDeviceName.INNER);
            rfReader = URFReader.Stub.asInterface(deviceService.getRFReader(param));
        }
        rfReader.searchCardAndActivate(new OnPassAndActiveListener.Stub() {
            @Override
            public void onActivate(byte[] responseData) throws RemoteException {
                byte[] bytesArray = rfReader.getCardSerialNo(responseData);
                BigInteger tagId = new BigInteger(bytesArray);

                transactionMessenger.onTagRead("" + tagId.toString() + "");
                rfReader.stopSearch();
            }
            @Override
            public void onFail(int error ) throws RemoteException {
                // TODO Error handling, error see RFError.
            }
        });

        return rfReader;
    }

    private void cancelTransactionsWithError() {
        ReversalProvider reversalProvider = new ReversalProvider(applicationContext);
        reversalProvider.setDialogMessage("Cancelando transação com erro");
        reversalProvider.isDefaultUI();
        reversalProvider.setConnectionCallback(new StoneCallbackInterface() {
            @Override
            public void onSuccess() {
                // code code code
            }

            @Override
            public void onError() {
                // code code code
            }
        });
    }

    private TransactionObject getTransactionObject() {
        if (this.transactionObject == null)
            return new TransactionObject();
        else return this.transactionObject;
    }

    private StonePrinter getPrinterInstance() {
        if (printer == null) return new StonePrinter(applicationContext, transactionObject);
        else return printer;
    }

    @Override
    public void payment(JSONArray args) throws JSONException, InterruptedException {
        try {
            JSONObject data = new JSONObject(args.getString(0));
            paymentData = new PaymentData(data);

            payment(paymentData);
        } catch (Exception e) {
            Toast.makeText(applicationContext, "error: " + e.getMessage(), Toast.LENGTH_LONG).show();
        }
    }

    @Override
    public void payment(PaymentData paymentData) throws JSONException, InterruptedException {
        transactionObject = getTransactionObject();

        this.cancelTransactionsWithError();

        // Amount of installments
        transactionObject.setInstalmentTransaction(InstalmentTransactionEnum.getAt(paymentData.getInstallmentsNumber() - 1));

        // Payment Method
        TypeOfTransactionEnum transactionType;
        switch (paymentData.getType()) {
            case 2:
                transactionType = TypeOfTransactionEnum.DEBIT;
                break;
            case 3:
                transactionType = TypeOfTransactionEnum.CREDIT;
                break;
            case 110:
                transactionMessenger.promptCommand("1:Reimpressão de comprovante;2:Cancelamento de transação;", 1, 2, 517);
                return;
            case 113:
                startPrintCoupon();
                return;
            case 210:
            case 211:
                startCancelOrder();
                return;
            case 627:
                transactionType = TypeOfTransactionEnum.VOUCHER;
                break;
            default:
                return;
        }
        transactionObject.setTypeOfTransaction(transactionType);
        transactionObject.setCapture(true);
        transactionObject.setAmount(String.valueOf(Util.stringMoneyToCents(paymentData.getValue())));

        transactionObject.setSubMerchantCity("Belo Horizonte"); //Cidade do sub-merchant
        transactionObject.setSubMerchantPostalAddress("30710480"); //CEP do sub-merchant (Apenas números)
        transactionObject.setSubMerchantRegisteredIdentifier("00000000"); // Identificador do sub-merchant
        transactionObject.setSubMerchantTaxIdentificationNumber("33368443000199"); // CNPJ do sub-merchant (apenas números)

        transactionProvider = buildTransactionProvider();
        transactionProvider.setConnectionCallback(this);
        transactionProvider.execute();

        lastTransactionObject = transactionObject;
    }

    private PosTransactionProvider buildTransactionProvider() {
        return new PosTransactionProvider(applicationContext, transactionObject, getSelectedUserModel());
    }

    private UserModel getSelectedUserModel() {
        return Stone.getUserModel(0);
    }

    private void startCancelOrder() {
        cancellingOrder = true;
        transactionMessenger.promptCommand("Informe o número de autorização da transação a ser cancelada", 1, 100, 517);
    }

    @Override
    public void continueTransaction(JSONArray args) throws JSONException {
        JSONObject data = new JSONObject(args.getString(0));
        String buffer   = (String) (!data.get("buffer").equals(null) ? data.get("buffer") : "");

        continueTransaction(buffer);
    }

    @Override
    public void continueTransaction(String buffer) {
        TransactionDAO transactionDAO  = new TransactionDAO(applicationContext);
        transactionObject = transactionDAO.findTransactionWithAuthorizationCode(buffer);
        lastTransactionObject = transactionObject;

        if (cancellingOrder) {
            if (transactionObject == null) {
                transactionMessenger.onReturnValue("Transação não encontrada", -1);
            } else {
                CancellationProvider cancellationProvider = new CancellationProvider(applicationContext, transactionObject);
                cancellationProvider.setConnectionCallback(this);
                cancellationProvider.execute();
            }
            cancellingOrder = false;
        } else if (printingCoupon) {
            if (transactionObject == null) {
                transactionMessenger.onReturnValue("Transação não encontrada", -1);
            } else {
                getPrinterInstance().printCoupon(transactionObject, ReceiptType.CLIENT, this);
            }
            printingCoupon = false;
        } else if (buffer.equals("1")) {
            startPrintCoupon();
        } else if (buffer.equals("2")) {
            startCancelOrder();
        } else if (buffer.equals("")) {
            transactionMessenger.onReturnValue("Menu financeiro finalizado", -1);
        }
    }

    @Override
    public void abortTransaction() {
        cancellingOrder = false;
        printingCoupon = false;
        transactionProvider.cancel(true);
        transactionProvider.setConnectionCallback(this);

        transactionMessenger.onReturnValue("Operação cancelada", -1);
    }

    @Override
    public void printQrCode(JSONArray args) {
        try {
            Printer printer = this.getPrinterInstance();
            JSONObject data = new JSONObject(args.getString(0));
            String seed     = data.get("seed").toString();

            getPrinterInstance().printQrCode(seed);
            transactionMessenger.onReturnValue("", 0);
        } catch (Exception e) {
            Log.d("bipfun", e.getMessage());
            callbackContext.error(e.getMessage());
        }
    }

    @Override
    public void printBarCode(JSONArray args) {
        try {
            Printer printer = this.getPrinterInstance();
            JSONObject data = new JSONObject(args.getString(0));
            String seed     = data.get("seed").toString();
            String isLast   = data.get("isLast").toString();

            getPrinterInstance().printBarCode(seed);

            if (isLast.equals("true")) {
                getPrinterInstance().printText("\n", 72);
            }
            transactionMessenger.onReturnValue("", 0);
        } catch (Exception e) {
            Log.d("bipfun", e.getMessage());
            callbackContext.error(e.getMessage());
        }
    }

    @Override
    public void printText(JSONArray args) {
        try {
            Printer printer   = this.getPrinterInstance();
            JSONObject data   = new JSONObject(args.getString(0));
            String text       = data.get("text").toString();
            int fontSize      = Integer.parseInt(data.get("fontSize").toString());
            String fontWeight = data.get("fontWeight").toString();
            String textAlign  = data.get("textAlign").toString();
            if (text.replace("\n", "").length() > 0)
                getPrinterInstance().printText(Util.removeSpecialCharacters(text), fontSize, textAlign, fontWeight);
        } catch (Exception e) {
            transactionMessenger.onReturnValue("" + e.getMessage(), -1);
        }

        transactionMessenger.onReturnValue("0", 0);
    }

    @Override
    public void printImage(JSONArray args) {
        try {
            Printer printer   = this.getPrinterInstance();
            JSONObject data   = new JSONObject(args.getString(0));
            String base64     = data.get("image").toString().split("data:image/png;base64,")[1];
            getPrinterInstance().printImage(base64, this);
        } catch (Exception e) {
            transactionMessenger.onReturnValue("" + e.getMessage() + "", -1);
        }
    }

    @Override
    public void printCoupon() {
        if (lastTransactionObject != null) {
            StonePrinter printer = this.getPrinterInstance();
            getPrinterInstance().printCoupon(lastTransactionObject, ReceiptType.CLIENT, this);
        }
    }

    @Override
    public void readRfidCard() {
        bindService();
    }

    @Override
    public void stopReadingRfidCard() {
        try {
            if (rfReader != null) rfReader.stopSearch();
        } catch (RemoteException e) {
            e.printStackTrace();
        }
    }

    private void startPrintCoupon() {
        printingCoupon = true;
        transactionMessenger.promptCommand("Informe o número de autorização da transação que deseja reimprimir", 1, 100, 517);
    }

    @Override
    public void onStatusChanged(Action action) {
        String message;
        switch (action.name()) {
            case "TRANSACTION_WAITING_CARD":
                message = "Aproxime, insira ou passe o cartão";
                break;
            case "TRANSACTION_SENDING":
                message = "Enviando transação";
                break;
            case "TRANSACTION_WAITING_PASSWORD":
                message = "Informe a senha do cartão";
                break;
            default:
                return;
        }

        transactionMessenger.onTransactionMessage("" + message + "");
    }

    @Override
    public void onSuccess() {
        if (transactionObject.getTransactionStatus() == TransactionStatusEnum.APPROVED ||
            transactionObject.getTransactionStatus() == TransactionStatusEnum.PARTIAL_APPROVED) {
            String cardBrand = transactionObject.getCardBrand().name();
            String documentId = transactionObject.getTransactionReference();
            String cardNumber = transactionObject.getCardHolderNumber();
            String authorizationCode = transactionObject.getAuthorizationCode();

            transactionMessenger.onReturnValue("Transação efetuada com sucesso!", 0, "" + documentId + "", "" + cardNumber + "", "" + cardBrand + "", "" + authorizationCode + "");
        } else if (transactionObject.getTransactionStatus() == TransactionStatusEnum.CANCELLED) {
            transactionMessenger.onReturnValue("Operação cancelada", 0);
        } else if (transactionObject.getTransactionStatus() == TransactionStatusEnum.DECLINED) {
            String errorMessage = transactionProvider.getMessageFromAuthorize();
            if (errorMessage == null || errorMessage.length() == 0) errorMessage = "Transação não autorizada";
            transactionMessenger.onReturnValue("" + errorMessage + "", -1);
        } else {
            transactionMessenger.onReturnValue("", 0);
        }
    }

    @Override
    public void onError() {
        String errorMessage;
        if (transactionProvider != null)
            errorMessage = transactionProvider.getMessageFromAuthorize();
        else errorMessage = "Houve um erro ao processar a sua solicitação";
        transactionMessenger.onReturnValue("" + errorMessage + "", -1);
    }

    @Override
    public void onPrintSuccess() {
        transactionMessenger.onReturnValue("", 0);
    }

    @Override
    public void onPrintError(Exception e) {
        transactionMessenger.onReturnValue("" + e.getMessage() + "", -1);
    }
}
