package com.teknisa.tef.printer;

public interface PrintCallback {

    void onPrintSuccess();
    void onPrintError(Exception e);

}
