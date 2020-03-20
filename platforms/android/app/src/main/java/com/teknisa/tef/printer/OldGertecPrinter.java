package com.teknisa.tef.printer;

import android.content.Context;
import android.graphics.Bitmap;
import android.os.Handler;
import android.os.RemoteException;

import com.teknisa.tef.Util;
import com.teknisa.tef.printer.PrintCallback;
import com.teknisa.tef.printer.Printer;

import org.json.JSONException;

public class OldGertecPrinter implements Printer {

    public static wangpos.sdk4.libbasebinder.Printer printer;

    public OldGertecPrinter(Context mContext) {
    }

    private void initializePrinter() throws Exception {
        int[] arr  = new int[1];
        int status = printer.getPrinterStatus(arr);
        status     += (arr[0] * 10) + printer.printInit();
        if (status != 0) throw new Exception(String.valueOf(status));
    }

    @Override
    public void printText(String string, int textSize) throws JSONException, RemoteException, Exception {
        this.printText(string, textSize, "CENTER", "NORMAL");
    }

    @Override
    public void printText(String string, int textSize, String textAlign, String fontWeight) throws JSONException, RemoteException, Exception {
        int status = printer.printInit();
        status += printer.printString(string, textSize, this.factoryTextAlign(textAlign), this.isBold(fontWeight), this.isItalic(fontWeight));
        status += printer.printString("\n\n\n", 16, this.factoryTextAlign(textAlign), this.isBold(fontWeight), this.isItalic(fontWeight));
        status += printer.printPaper(0);
        status += printer.printFinish();

        if (status != 0) throw new Exception(String.valueOf(status));
    }

    @Override
    public void printQrCode(String qrCode) {
        try {
            this.initializePrinter();
            printer.printQRCode(qrCode);
            printer.printFinish();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    @Override
    public void printBarCode(String barCode) {
        try {
            this.initializePrinter();
            printer.printBarCode(barCode, 50, true);
            printer.printFinish();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    @Override
    public void printImage(String base64, PrintCallback printCallback) {
        try {
            Bitmap bitmap = Util.base64ToBitmap(base64);
            this.initializePrinter();
            int status = printer.printImage(bitmap, bitmap.getWidth(), wangpos.sdk4.libbasebinder.Printer.Align.CENTER);
            this.printText("\n\n\n", 36);
            status += printer.printPaper(0);
            status += printer.printFinish();
            if (status != 0) throw new Exception("-1");

            printCallback.onPrintSuccess();
        } catch (Exception e) {
            printCallback.onPrintError(e);
        }
    }

    private wangpos.sdk4.libbasebinder.Printer.Align factoryTextAlign(String string) throws Exception {
        if (string.equals("LEFT")) return wangpos.sdk4.libbasebinder.Printer.Align.LEFT;
        if (string.equals("CENTER")) return wangpos.sdk4.libbasebinder.Printer.Align.CENTER;
        if (string.equals("RIGHT")) return wangpos.sdk4.libbasebinder.Printer.Align.RIGHT;
        throw new Exception("Invalid text align value");
    }

    private boolean isBold(String fontWeight) throws Exception {
        return fontWeight.equals("BOLD");
    }

    private boolean isItalic(String fontWeight) throws Exception {
        return fontWeight.equals("ITALIC");
    }

}
