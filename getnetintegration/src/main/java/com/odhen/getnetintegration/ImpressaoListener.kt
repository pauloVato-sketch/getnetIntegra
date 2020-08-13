package com.odhen.getnetintegration

import android.util.Log
import com.getnet.posdigital.printer.IPrinterCallback
import com.getnet.posdigital.printer.PrinterStatus

class ImpressaoListener: IPrinterCallback.Stub() {

    override fun onSuccess() {
        Log.d("impressaolistener","ok")
    }

    override fun onError(p0: Int) {
        Log.d("impressaolistener","error: $p0")
    }


}