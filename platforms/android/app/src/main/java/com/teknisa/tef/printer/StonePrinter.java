package com.teknisa.tef.printer;

import android.content.Context;
import android.graphics.Bitmap;
import android.graphics.Color;
import android.os.RemoteException;

import com.google.zxing.BarcodeFormat;
import com.google.zxing.MultiFormatWriter;
import com.google.zxing.WriterException;
import com.google.zxing.common.BitMatrix;

import com.teknisa.tef.Util;

import org.json.JSONException;

import br.com.stone.posandroid.providers.PosPrintProvider;
import br.com.stone.posandroid.providers.PosPrintReceiptProvider;
import stone.application.enums.ReceiptType;
import stone.application.interfaces.StoneCallbackInterface;
import stone.database.transaction.TransactionObject;

public class StonePrinter implements Printer {

    Context context;
    TransactionObject transactionObject;

    public StonePrinter(final Context mContext, TransactionObject transactionObject) {
        this.context = mContext;
        this.transactionObject = transactionObject;
    }

    @Override
    public void printText(String string, int textSize) throws JSONException, RemoteException, Exception {
        this.printText(string, textSize, "CENTER", "NORMAL");
    }

    @Override
    public void printText(String string, int textSize, String textAlign, String fontWeight) throws JSONException, RemoteException, Exception {
        PosPrintProvider customPosPrintProvider = new PosPrintProvider(context);
        customPosPrintProvider.addLine(string);
        customPosPrintProvider.setConnectionCallback(new StoneCallbackInterface() {
            @Override
            public void onSuccess() {
            }

            @Override
            public void onError() {
            }
        });
        customPosPrintProvider.execute();
    }

    public void printCoupon(TransactionObject transactionObject, ReceiptType receiptType, PrintCallback printCallback) {
        PosPrintReceiptProvider posPrintReceiptProvider = new PosPrintReceiptProvider(context, transactionObject, receiptType);
        posPrintReceiptProvider.setConnectionCallback(new StoneCallbackInterface() {
            @Override
            public void onSuccess() {
                printCallback.onPrintSuccess();
            }

            @Override
            public void onError() {
                String errorName = "Houve um erro desconhecido ao imprimir";
                if (posPrintReceiptProvider.getListOfErrors().size() > 0)
                    errorName = posPrintReceiptProvider.getListOfErrors().get(0).name();

                printCallback.onPrintError(new Exception(errorName));
            }
        });
        posPrintReceiptProvider.execute();
    }

    @Override
    public void printQrCode(String qrCode) {
        try {
            Bitmap bm = encodeAsBitmap(qrCode, BarcodeFormat.QR_CODE, 200, 1000);

            if(bm != null) printImage(bm);
        } catch (Exception e) { }
    }

    @Override
    public void printBarCode(String barCode) {
        try {
            Bitmap bm = encodeAsBitmap(barCode, BarcodeFormat.CODE_128, 300, 100);

            if(bm != null) printImage(bm);
        } catch (Exception e) { }
    }

    Bitmap encodeAsBitmap(String source, BarcodeFormat format, int width, int height) {
        BitMatrix result;

        try {
            result = new MultiFormatWriter().encode(source, format, width, height, null);
        } catch (IllegalArgumentException | WriterException e) {
            // Unsupported format
            return null;
        }

        final int w = result.getWidth();
        final int h = result.getHeight();
        final int[] pixels = new int[w * h];

        for (int y = 0; y < h; y++) {
            final int offset = y * w;
            for (int x = 0; x < w; x++) {
                pixels[offset + x] = result.get(x, y) ? Color.BLACK : Color.WHITE;
            }
        }

        final Bitmap bitmap = Bitmap.createBitmap(w, h, Bitmap.Config.ARGB_8888);
        bitmap.setPixels(pixels, 0, width, 0, 0, w, h);

        return bitmap;
    }

    @Override
    public void printImage(String base64, PrintCallback printCallback) {
        Bitmap bitmap = Util.base64ToBitmap(base64);
        bitmap = Bitmap.createScaledBitmap(bitmap, bitmap.getWidth(), (int) (bitmap.getHeight() * 0.6), false);
        PosPrintProvider customPosPrintProvider = new PosPrintProvider(context);
        customPosPrintProvider.addBitmap(bitmap);
        customPosPrintProvider.setConnectionCallback(new StoneCallbackInterface() {
            @Override
            public void onSuccess() {
                printCallback.onPrintSuccess();
            }

            @Override
            public void onError() {
                String errorName = "Houve um erro desconhecido ao imprimir";
                if (customPosPrintProvider.getListOfErrors().size() > 0)
                    errorName = customPosPrintProvider.getListOfErrors().get(0).name();

                printCallback.onPrintError(new Exception(errorName));
            }
        });
        customPosPrintProvider.execute();
    }

    public void printImage(Bitmap bitmap) {
        bitmap = Bitmap.createScaledBitmap(bitmap, bitmap.getWidth(), (int) (bitmap.getHeight() * 0.6), false);
        PosPrintProvider customPosPrintProvider = new PosPrintProvider(context);
        customPosPrintProvider.addBitmap(bitmap);
        customPosPrintProvider.setConnectionCallback(new StoneCallbackInterface() {
            @Override
            public void onSuccess() { }

            @Override
            public void onError() { }
        });
        customPosPrintProvider.execute();
    }

    private PosPrintReceiptProvider getPosPrintReceiptProviderInstance(final ReceiptType receiptType) {
        return new PosPrintReceiptProvider(
                context,
                transactionObject,
                receiptType
        );
    }

}
