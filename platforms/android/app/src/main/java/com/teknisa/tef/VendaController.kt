package com.teknisa.tef

import android.app.Activity
import android.content.Intent
import android.net.Uri
import android.os.Bundle
import android.util.Log
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import com.odhen.deviceintagrationfacade.Controllers.VendaController
import com.odhen.deviceintagrationfacade.Controllers.VendaController.TransacaoListener
import com.odhen.deviceintagrationfacade.Controllers.VendaController.EstadoTransacao
import com.odhen.deviceintagrationfacade.Enums.TipoMovimentacao
import com.odhen.deviceintagrationfacade.Models.Venda
import com.odhen.deviceintagrationfacade.Shared.VendaAtual
import com.odhen.deviceintegrationfacade.Interfaces.DeviceIntegrationListener
import org.json.JSONObject
import android.app.Activity.RESULT_CANCELED
import com.teknisa.tef.pos.StoneIntegration
import com.teknisa.tef.TransactionMessenger
import com.teknisa.tef.PaymentData
import android.content.Context
import org.apache.cordova.CordovaActivity
import org.apache.cordova.CordovaPlugin
import java.text.SimpleDateFormat
import java.util.Calendar

class VendaController(stoneCode: String, environment: String, context: Context) : VendaController, DeviceIntegrationListener, TransactionMessenger {

    companion object {
        var stone: StoneIntegration = StoneIntegration()
    }
    var transacaoListener: TransacaoListener? = null
    var lastCvNumber = ""
    var context: Context
    var tipoMovimentacao = 0
    var valor = ""
    var custom_dialog : CustomDialog? = null
    init {
        stone.init(stoneCode, environment, context, this)
        this.context = context
        Log.d("PAGG", "STONECODE: " + stoneCode)
        Log.d("PAGG", "ENVIRONMENT: " + environment)
    }

    override fun chamaVenda(valor: Float, tipoMovimentacao: TipoMovimentacao, transacaoListener: TransacaoListener) {
        Log.d("PAGG", "chamaVenda")
        this.valor = valor.toString()

        this.transacaoListener = transacaoListener

        val pd = PaymentData()
        pd.setPaymentValue(valor.toString())
        when (tipoMovimentacao) {
            TipoMovimentacao.DEBITO -> {
                pd.setPaymentType(2)
                this.tipoMovimentacao = 1
            }
            TipoMovimentacao.CREDITO -> {
                pd.setPaymentType(3)
                this.tipoMovimentacao = 2
            }
            TipoMovimentacao.VOUCHER_ALIMENTACAO, TipoMovimentacao.VOUCHER_REFEICAO -> pd.setPaymentType(627)
            TipoMovimentacao.EXTORNO_CREDITO, TipoMovimentacao.EXTORNO_DEBITO, TipoMovimentacao.EXTORNO_VOUCHER -> pd.setPaymentType(210)
        }
        pd.setInstallmentsNumber(1)

        stone.payment(pd)
    }

    override fun chamaEstorno(valor: Float, cvNumber: String, dataTransacao: String, transacaoListener: TransacaoListener) {
        Log.d("PAGG", "chamaEstorno")

        this.transacaoListener = transacaoListener

        if (!cvNumber.equals("") && !dataTransacao.equals("") && !transacaoListener.equals("")) {
            val pd = PaymentData()
            pd.setPaymentValue(valor.toString())
            pd.setPaymentType(210)
            pd.setInstallmentsNumber(1)
            lastCvNumber = cvNumber

            stone.payment(pd)
        } else {
            stone.continueTransaction(lastCvNumber)
        }
    }

    override fun onIntegrationResult(requestCode: Int, resultCode: Int, data: Intent?) {
        Log.d("PAGG", "result")
    }

    override fun onReturnValue(message: String, status: Int) {
        Log.d("PAGG", "oRV: " + message + "(" + status + ")")

        if (status == 0) {
            transacaoListener?.transacaoConcluida(
                    EstadoTransacao.SUCESSO,
                    message
            )
        } else {
            transacaoListener?.transacaoConcluida(
                    EstadoTransacao.ERRO,
                    message
            )
        }
    }

    override fun onReturnValue(message: String, status: Int, documentId: String, cardNumber: String, cardBrand: String, authorizationCode: String) {
        Log.d("PAGG", "oRV: " + message + "(" + status + ")")
        Log.d("PAGG", "documentID: " + documentId)
        Log.d("PAGG", "cardNumber: " + cardNumber)
        Log.d("PAGG", "cardBrand: " + cardBrand)
        Log.d("PAGG", "authorizationCode: " + authorizationCode)

        val paymentData = JSONObject()

        val df = SimpleDateFormat("yyyyMMdd")
        val date = df.format(Calendar.getInstance().getTime())

        paymentData.put("nsu", documentId)
        paymentData.put("transactionDate", date)
        paymentData.put("binCard", cardNumber.substring(0, 6))
        paymentData.put("lastNumbersCard", cardNumber.substring(13, 4))
        paymentData.put("cardBrandName", cardBrand)
        paymentData.put("customerReceipt", documentId)
        paymentData.put("merchantReceipt", documentId)
        paymentData.put("uniqueSequentialNumber", documentId)
        paymentData.put("PAYMENTINVOICE", authorizationCode)
        paymentData.put("IDTIPORECE", tipoMovimentacao)
        paymentData.put("VRMOVIVEND", valor)

        if (status == 0) {
            transacaoListener?.transacaoConcluida(
                    EstadoTransacao.SUCESSO,
                    message,
                    null,
                    paymentData
            )
        } else {
            transacaoListener?.transacaoConcluida(
                    EstadoTransacao.ERRO,
                    message
            )
        }
    }

    override fun onTagRead(tagSerialNumber: String) {

    }

    override fun promptCommand(message: String, minLength: Int, maxLength: Int, fieldId: Int) {
        Log.d("PAGG", "pc(" + fieldId + "): " + message)

        if (fieldId == 517) {
            /* Caso seja cancelamento de transação */
            val tl = transacaoListener
            if (tl != null)
                VendaController.instance?.chamaEstorno(0.0F, "", "", tl)
        }
    }

    override fun onTransactionMessage(message: String) {
        Log.d("PAGG", "oTC    " + message)

        if(custom_dialog == null){
            custom_dialog = CustomDialog(context)
            message?.let { custom_dialog!!.setMessage(it) }
            custom_dialog?.show()
        }else{
            if (message == "PAGAMENTO FINALIZADO") {
                message?.let { custom_dialog?.setMessage(it) }
                custom_dialog!!.dismiss()
            }else{
                message?.let { custom_dialog?.setMessage(it) }
            }
        }
    }

}