//package com.odhen.POS;
//
//import android.app.Activity;
//import android.graphics.Paint;
//import android.graphics.Typeface;
//import android.os.RemoteException;
//import android.content.Context;
//// import android.webkit.JavascriptInterface;
//
//import br.com.gertec.gedi.GEDI;
//import br.com.gertec.gedi.exceptions.GediException;
//import br.com.gertec.gedi.interfaces.IGEDI;
//import br.com.gertec.gedi.interfaces.IPRNTR;
//import br.com.gertec.gedi.structs.GEDI_PRNTR_st_StringConfig;
//
//// import com.zeedhi.webview.ZhWebView;
//
//import java.util.regex.Pattern;
//
//import org.apache.cordova.CallbackContext;
//import org.json.JSONObject;
//import org.json.JSONException;
//
//import wangpos.sdk4.libbasebinder.Printer;
//import wangpos.sdk4.libbasebinder.Printer.Align;
//import wangpos.sdk4.libbasebinder.RspCode;
//
//public class GertecPrinter extends Activity {
//
//    private boolean bloop;
//    private boolean error;
//    private int returnStatus;
//    private Printer com.odhen.stoneintegration.printer;
//    private String message = "";
//    private String printType;
//    private String printString;
//    // private ZhWebView webview;
//    private IGEDI iGedi = null;
//    private IPRNTR iprntr;
//    private CallbackContext callbackContext;
//
//
//    public GertecPrinter(final Context mContext) {
//        // this.webview = webview;
//
//        new Thread(){
//            @Override
//            public void run() {
//                com.odhen.stoneintegration.printer = new wangpos.sdk4.libbasebinder.Printer(mContext);
//                iGedi = GEDI.getInstance(mContext);
//            }
//        }.start();
//    }
//
//    // @JavascriptInterface
//    public void printText(String string, CallbackContext callbackContext) throws JSONException {
//        GEDI_PRNTR_st_StringConfig config = new GEDI_PRNTR_st_StringConfig(new Paint());
//        Typeface typeface = Typeface.create(Typeface.MONOSPACE, Typeface.NORMAL);
//        iprntr = iGedi.getPRNTR();
//        config.paint.setTextAlign(Paint.Align.LEFT);
//        config.paint.setTypeface(typeface);
//        config.paint.setTextSize(16);
//
//        try {
//            iprntr.Init();
//            iprntr.DrawStringExt(config, string);
//            iprntr.Output();
//
//            callJavaScript(false, "", callbackContext);
//        } catch (GediException e) {
//            callJavaScript(true, e.toString(), callbackContext);
//        }
//    }
//
//    // @JavascriptInterface
//    public void printQrCode(String qrCode, CallbackContext callbackContext) {
//        this.callbackContext = callbackContext;
//        bloop = false;
//        printType = "qr";
//        printString = qrCode;
//        new PrintThread().start();
//    }
//
//    // @JavascriptInterface
//    public void printBarCode(String barCode, CallbackContext callbackContext) {
//        this.callbackContext = callbackContext;
//        bloop = false;
//        printType = "bar";
//        printString = barCode;
//        new PrintThread().start();
//    }
//
//    public void callJavaScript(boolean error, String message, CallbackContext callbackContext) throws JSONException {
//        JSONObject response = new JSONObject();
//        response.put("error", error);
//        response.put("message", message);
//
//        callbackContext.success(response);
//    }
//
//    public class PrintThread extends Thread {
//        @Override
//        public void run () {
//            error = true;
//            int result = 0;
//
//            do {
//                try {
//                    result = com.odhen.stoneintegration.printer.printInit();
//                } catch (RemoteException e) {
//                    message = e.getMessage().toString();
//                }
//
//                checkResult(result);
//
//                if(!error) {
//                    try {
//                        switch(printType) {
//                            case "qr":
//                                result = com.odhen.stoneintegration.printer.printQRCode(printString);
//                                break;
//                            case "bar":
//                                result = com.odhen.stoneintegration.printer.printBarCode(printString, 50, true);
//                                break;
//                        }
//
//                        checkResult(result);
//
//                        if(!error) {
//                            result = com.odhen.stoneintegration.printer.printPaper(0);
//                            checkResult(result);
//                        }
//
//                        if(!error) {
//                            result = com.odhen.stoneintegration.printer.printFinish();
//                            checkResult(result);
//                        }
//                    } catch (Exception e) {
//                        message = e.getMessage().toString();
//                    }
//                } else {
//                    bloop = false;
//                }
//            } while (bloop);
//
//            try {
//                callJavaScript(error, message, callbackContext);
//            } catch (JSONException e) {
//                message = e.getMessage().toString();
//            }
//        }
//
//        private void checkResult(int result) {
//            if (result != RspCode.OK) {
//                message = "Erro! (" + result + ")";
//                bloop = false;
//            } else {
//                error = false;
//                message = "";
//            }
//        }
//    }
//}