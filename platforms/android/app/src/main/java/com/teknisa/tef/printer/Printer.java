package com.teknisa.tef.printer;

import android.os.RemoteException;

import org.json.JSONException;

public interface Printer {

    public void printText(String string, int textSize) throws JSONException, RemoteException, Exception;

    public void printText(String string, int textSize, String textAlign, String fontWeight) throws JSONException, RemoteException, Exception;

    public void printQrCode(String qrCode);

    public void printBarCode(String barCode);

    public void printImage(String base64, PrintCallback printCallback);

}
