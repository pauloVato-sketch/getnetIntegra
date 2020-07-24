package com.odhen.stoneintegration.printer;

//import android.app.Activity;
//import android.content.Context;
//import android.graphics.Paint;
//import android.graphics.Typeface;
//import android.os.RemoteException;
//import android.webkit.JavascriptInterface;
//////
//import br.com.gertec.gedi.GEDI;
//import br.com.gertec.gedi.exceptions.GediException;
//import br.com.gertec.gedi.interfaces.IGEDI;
//import br.com.gertec.gedi.interfaces.IPRNTR;
//import br.com.gertec.gedi.structs.GEDI_PRNTR_st_StringConfig;
//////
//import org.json.JSONException;
//////
//import wangpos.sdk4.libbasebinder.Printer;
//import wangpos.sdk4.libbasebinder.RspCode;

public class GertecPrinter  /*extends Activity implements Printer */ {

//    private boolean bloop;
//    private boolean error;
//    private int returnStatus;
//    private Printer com.odhen.stoneintegration.printer;
//    private String message = "";
//    private String printType;
//    private String printString;
//    private IGEDI iGedi = null;
//    private IPRNTR iprntr;
//////
//    public static final Paint.Align TEXT_ALIGN_LEFT   = Paint.Align.LEFT;
//    public static final Paint.Align TEXT_ALIGN_CENTER = Paint.Align.CENTER;
//    public static final Paint.Align TEXT_ALIGN_RIGHT  = Paint.Align.RIGHT;
////
//    public static final int FONT_WEIGHT_NORMAL = Typeface.NORMAL;
//    public static final int FONT_WEIGHT_BOLD   = Typeface.BOLD;
//
//    public GertecPrinter(final Context mContext) {
//        com.odhen.stoneintegration.printer = new wangpos.sdk4.libbasebinder.Printer(mContext);
//        iGedi = GEDI.getInstance(mContext.getApplicationContext());
//    }
//
//    @JavascriptInterface
//    public void printText(String string, int textSize) throws JSONException, RemoteException, Exception {
//        this.printText(string, textSize, "CENTER", "NORMAL");
//    }
//
//    @JavascriptInterface
//    public void printText(String string, int textSize, String textAlign, String fontWeight) throws JSONException, RemoteException, Exception {
//        GEDI_PRNTR_st_StringConfig config = new GEDI_PRNTR_st_StringConfig(new Paint());
//        Typeface typeface = Typeface.create(Typeface.MONOSPACE, this.factoryFontWeight(fontWeight));
//        iprntr = iGedi.getPRNTR();
//        config.paint.setTextSize(textSize);
//        config.paint.setTextAlign(this.factoryTextAlign(textAlign));
//        config.paint.setTypeface(typeface);
//
//        try {
//            iprntr.Init();
//            iprntr.DrawStringExt(config, string);
//            iprntr.Output();
//        } catch (GediException e) {
//
//        }
//    }
//
//    @JavascriptInterface
//    public void printQrCode(String qrCode) {
//        bloop = false;
//        printType = "qr";
//        printString = qrCode;
//        printStart();
//    }
//
//    @JavascriptInterface
//    public void printBarCode(String barCode) {
//        bloop = false;
//        printType = "bar";
//        printString = barCode;
//        printStart();
//    }
//
//    private Paint.Align factoryTextAlign(String string) throws Exception {
//        if (string.equals("LEFT")) return TEXT_ALIGN_LEFT;
//        if (string.equals("CENTER")) return TEXT_ALIGN_CENTER;
//        if (string.equals("RIGHT")) return TEXT_ALIGN_RIGHT;
//        throw new Exception("Invalid text align value");
//    }
//
//    private int factoryFontWeight(String string) throws Exception {
//        if (string.equals("NORMAL")) return FONT_WEIGHT_NORMAL;
//        if (string.equals("BOLD")) return FONT_WEIGHT_BOLD;
//        throw new Exception("Invalid font weight value");
//    }
//
//    private void printStart() {
//        error = true;
//        int result = 0;
//
//        do {
//            try {
//                result = com.odhen.stoneintegration.printer.printInit();
//            } catch (RemoteException e) {
//                message = e.getMessage().toString();
//            }
//
//            checkResult(result);
//
//            if(!error) {
//                try {
//                    switch(printType) {
//                        case "qr":
//                            result = com.odhen.stoneintegration.printer.printQRCode(printString);
//                            break;
//                        case "bar":
//                            result = com.odhen.stoneintegration.printer.printBarCode(printString, 50, true);
//                            break;
//                    }
//
//                    checkResult(result);
//
//                    if(!error) {
//                        result = com.odhen.stoneintegration.printer.printPaper(0);
//                        checkResult(result);
//                    }
//
//                    if(!error) {
//                        result = com.odhen.stoneintegration.printer.printFinish();
//                        checkResult(result);
//                    }
//                } catch (Exception e) {
//                    message = e.getMessage().toString();
//                }
//            } else {
//                bloop = false;
//            }
//        } while (bloop);
//    }
//
//    private void checkResult(int result) {
//        if (result != RspCode.OK) {
//            message = "Erro! (" + result + ")";
//            bloop = false;
//        } else {
//            error = false;
//            message = "";
//        }
//    }

}