package com.odhen.getnetintegration

import android.util.Log
import com.getnet.posdigital.PosDigital
import java.lang.Exception

object PosDigitalCallback: PosDigital.BindCallback {

    override fun onConnected() {
        Log.d("teste","ok")
    }

    override fun onDisconnected() {
        Log.d("teste","naook")
    }

    override fun onError(p0: Exception?) {
        Log.d("teste",p0.toString())
    }
}