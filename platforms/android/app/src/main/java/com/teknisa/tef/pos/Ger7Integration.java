package com.teknisa.tef.pos;

import android.content.Context;
import android.content.SharedPreferences;

import com.teknisa.tef.printer.OldGertecPrinter;
import com.teknisa.tef.PaymentData;
import com.teknisa.tef.TransactionMessenger;
import com.teknisa.tef.printer.PrintCallback;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.concurrent.TimeUnit;

import ger7.com.br.pos7api.POS7API;
import ger7.com.br.pos7api.ParamIn;
import ger7.com.br.pos7api.ParamOut;

public class Ger7Integration implements PosIntegration, POS7API.Pos7apiCallback, PrintCallback {

    private POS7API pos7API;
    protected TransactionMessenger transactionMessenger;
    protected OldGertecPrinter printer;
    protected String couponText = "";
    private Context context;
    private static ParamIn lastTransactionObject;
    private boolean cancellingOrder = false;
    private boolean printingCoupon = false;

    @Override
    public void init(JSONArray args, Context context, TransactionMessenger transactionMessenger) {
        init("", "", context, transactionMessenger);
    }

    @Override
    public void init(String stoneCode, String environment, Context context, TransactionMessenger transactionMessenger) {
        this.transactionMessenger = transactionMessenger;
        this.context = context;
        printer = new OldGertecPrinter(context);
        pos7API = new POS7API(context);
        this.transactionMessenger.onReturnValue("", 0);
    }

    @Override
    public void payment(JSONArray args) throws JSONException, InterruptedException {
        JSONObject data = new JSONObject(args.getString(0));
        PaymentData paymentData = new PaymentData(data);

        ParamIn paramIn = new ParamIn();

//        paramIn.setTrsId("1");
        paramIn.setTrsAmount(paymentData.getValue().replace(".", ""));
        paramIn.setTrsInstallments(paymentData.getInstallmentsNumber());
        paramIn.setTrsInstMode(1);
//
        switch (paymentData.getType()) {
            case 2:
                paramIn.setTrsProduct(2);
                break;
            case 3:
                paramIn.setTrsProduct(1);
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
                paramIn.setTrsProduct(4);
                break;
        }

        paramIn.setTrsType(1);
        paramIn.setTrsReceipt(false);

        lastTransactionObject = paramIn;

        pos7API.processTransaction(paramIn, this);
    }

    @Override
    public void payment(PaymentData paymentData) throws JSONException, InterruptedException {

    }

    @Override
    public void continueTransaction(JSONArray args) throws JSONException, InterruptedException {
        JSONObject data = new JSONObject(args.getString(0));
        String buffer   = (String) (!data.get("buffer").equals(null) ? data.get("buffer") : "");
        ParamIn paramIn = new ParamIn();
        lastTransactionObject = paramIn;

        if (cancellingOrder) {
            paramIn.setTrsType(2);
            paramIn.setTrsRefundId(buffer);
            pos7API.processTransaction(paramIn, this);
            cancellingOrder = false;
        } else if (printingCoupon) {
            SharedPreferences sharedPreferences = context.getSharedPreferences("coupons", Context.MODE_PRIVATE);
            String couponText = sharedPreferences.getString(buffer, null);
            if (couponText == null) {
                transactionMessenger.onReturnValue("Transação não encontrada", -1);
            } else {
                try {
                    printer.printText(couponText, 14, "CENTER", "BOLD");
                } catch (Exception e) {
                    e.printStackTrace();
                }
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
    public void continueTransaction(String buffer) {

    }

    public void startPrintCoupon() {
        printingCoupon = true;
        transactionMessenger.promptCommand("Informe o número de autorização da transação que deseja reimprimir", 1, 100, 517);
    }

    public void startCancelOrder() {
        cancellingOrder = true;
        transactionMessenger.promptCommand("Informe o número de autorização da transação a ser cancelada", 1, 100, 517);
    }

    @Override
    public void abortTransaction() {
        transactionMessenger.onReturnValue("Transação abortada", -1);
    }

    @Override
    public void printQrCode(JSONArray args) {

    }

    @Override
    public void printBarCode(JSONArray args) {

    }

    @Override
    public void printText(JSONArray args) {

    }

    @Override
    public void printImage(JSONArray args) {
        try {
            JSONObject data = new JSONObject(args.getString(0));
            boolean isLast = false;
            try { data.getBoolean("last"); } catch (Exception e) { };
            String image = data.get("image").toString().split("data:image/png;base64,")[1];
            printer.printImage(image, this);
        } catch (JSONException e) {
            e.printStackTrace();
        }
    }

    @Override
    public void printCoupon() {
        try {
            printer.printText(couponText, 14, "CENTER", "BOLD");
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    @Override
    public void readRfidCard() {

    }

    @Override
    public void stopReadingRfidCard() {

    }

    @Override
    public void onResult(ParamOut paramOut) {
        int transactionStatus = paramOut.getResponse();
        if (transactionStatus == 0 && paramOut.getResType().equals("1")) {
            String cardBrand = paramOut.getResLabel();
            String documentId = paramOut.getResRrn();
            String cardNumber = paramOut.getResTrack2().substring(0, 16);
            String authorizationCode = paramOut.getResAuthorization();

            couponText = paramOut.getResPrint().replace("ESTAB", "CLIENTE");

            saveCoupon(authorizationCode, couponText);

            transactionMessenger.onReturnValue("Transação efetuada com sucesso!", 0, documentId, cardNumber, cardBrand, authorizationCode);
        } else {
            transactionMessenger.onReturnValue(paramOut.getResDisplay(), transactionStatus);
        }
    }

    private void saveCoupon(String key, String couponText) {
        SharedPreferences sharedPreferences = context.getSharedPreferences("coupons", Context.MODE_PRIVATE);
        SharedPreferences.Editor editor = sharedPreferences.edit();

        long preferencesExpirationDate = sharedPreferences.getLong("expiration_date", -1);

        if (preferencesExpirationDate == -1) {
            editor.putLong("expiration_date", System.currentTimeMillis() + TimeUnit.DAYS.toMillis(7));
        } else if (System.currentTimeMillis() > preferencesExpirationDate) {
            editor.clear();
            editor.putLong("expiration_date", System.currentTimeMillis() + TimeUnit.DAYS.toMillis(7));
        }

        editor.putString(key, couponText);
        editor.apply();
    }

    @Override
    public void onPrintSuccess() {
        transactionMessenger.onReturnValue("", 0);
    }

    @Override
    public void onPrintError(Exception e) {
        transactionMessenger.onReturnValue(e.getMessage(), -1);
    }
}
