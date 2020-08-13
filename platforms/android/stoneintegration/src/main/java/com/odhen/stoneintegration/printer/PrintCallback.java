package com.odhen.stoneintegration.printer;

public interface PrintCallback {

    void onPrintSuccess();
    void onPrintError(Exception e);

}
