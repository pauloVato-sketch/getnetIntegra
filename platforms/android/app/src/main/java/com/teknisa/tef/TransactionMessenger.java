package com.teknisa.tef;

import android.app.Activity;

public interface TransactionMessenger {

    void onReturnValue(String message, int status);
    void onReturnValue(String message, int status, String documentId, String cardNumber, String cardBrand, String authorizationCode);
    void onTagRead(String tagSerialNumber);
    void promptCommand(String message, int minLength, int maxLength, int fieldId);
    void onTransactionMessage(String message);

}
