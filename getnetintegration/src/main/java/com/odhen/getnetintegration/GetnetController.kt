package com.odhen.getnetintegration

import android.content.Context
import android.os.Handler
import android.util.Log
import androidx.appcompat.app.AppCompatActivity
import com.getnet.posdigital.PosDigital
import com.getnet.posdigital.printer.IPrinterService
import java.lang.Exception

class GetnetController (activity: AppCompatActivity) {
    private var mainHandler = Handler(activity.mainLooper)

    val impressaoController = ImpressaoController()
    val vendaController = VendaController(activity)

    init {
        PosDigital.register(activity, PosDigitalCallback)
        startService()
    }

    private fun startService() {
        mainHandler.post {
            var printer: IPrinterService? = try {
                val printer = PosDigital.getInstance().printer
                printer.init()
                printer
            } catch (_: Exception) {
                startService()
                null
            }

            if (printer != null) {
                impressaoController.initPrinterParams(printer)
            }
        }
    }

}