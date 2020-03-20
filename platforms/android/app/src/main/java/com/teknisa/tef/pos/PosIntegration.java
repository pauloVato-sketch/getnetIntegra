package com.teknisa.tef.pos;

import android.content.Context;

import com.teknisa.tef.PaymentData;
import com.teknisa.tef.TransactionMessenger;

import org.json.JSONArray;
import org.json.JSONException;

public interface PosIntegration {

    void init(JSONArray args, Context context, TransactionMessenger transactionMessenger);
    void init(String stoneCode, String environment, Context context, TransactionMessenger transactionMessenger);
    void payment(JSONArray args) throws JSONException, InterruptedException;
    void payment(PaymentData paymentData) throws JSONException, InterruptedException;
    void continueTransaction(JSONArray args) throws JSONException, InterruptedException;
    void continueTransaction(String buffer);
    void abortTransaction();
    void printQrCode(JSONArray args);
    void printBarCode(JSONArray args);
    void printText(JSONArray args);
    void printImage(JSONArray args);
    void printCoupon();
    void readRfidCard();
    void stopReadingRfidCard();

}
