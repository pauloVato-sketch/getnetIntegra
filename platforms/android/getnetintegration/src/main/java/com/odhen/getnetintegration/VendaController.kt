package com.odhen.getnetintegration

import android.app.Activity
import android.content.Intent
import android.net.Uri
import android.os.Bundle
import android.util.Log
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.core.os.bundleOf
import com.odhen.deviceintagrationfacade.Controllers.VendaController
import com.odhen.deviceintagrationfacade.Controllers.VendaController.TransacaoListener
import com.odhen.deviceintagrationfacade.Enums.TipoMovimentacao
import com.odhen.deviceintagrationfacade.Models.Venda
import com.odhen.deviceintagrationfacade.Shared.VendaAtual
import com.odhen.deviceintegrationfacade.Interfaces.DeviceIntegrationListener
import org.json.JSONObject
import android.app.Activity.RESULT_CANCELED


class VendaController(val activity: AppCompatActivity) : VendaController, DeviceIntegrationListener {

    val REQUEST_CODE = 1001
    val URI_PAYMENT = "getnet://pagamento/v1/payment"
    val URI_REFUND = "getnet://pagamento/v1/refund"
    val URI_REPRINT = "getnet://pagamento/v1/reprint"
    val RESULT_KEY = "result"
    val AMOUNT_KEY = "amount"
    val TYPE_KEY = "type"
    val CARDBIN_KEY = "cardBin"
    val CARDLASTDIG_KEY = "cardLastDigits"
    val CARDBRAND_KEY = "brand"
    val NSU_KEY = "nsu"
    val DATETIME_KEY = "gmtDateTime"
    val CV_KEY = "cvNumber"
    val NSUHOST_KEY = "nsuLocal"
    val DATEREFUND_KEY = "refundTransactionDate"

    var transacaoListener: TransacaoListener? = null

    override fun chamaVenda(valor: Float, tipoMovimentacao: TipoMovimentacao, transacaoListener: TransacaoListener) {
        this.transacaoListener = transacaoListener

        if (VendaAtual.venda == null) {
            VendaAtual.venda = Venda(valor, tipoMovimentacao)
        } else {
            Log.e("Getnet@chamaVenda", "Outra venda esta em processamento")
            return
        }


        val valorLong = (valor * 100).toLong()
        val paymentType = when (tipoMovimentacao) {
            TipoMovimentacao.CREDITO -> "credit"
            TipoMovimentacao.DEBITO -> "debit"
            TipoMovimentacao.VOUCHER_ALIMENTACAO,
            TipoMovimentacao.VOUCHER_REFEICAO -> "voucher"
            else -> {
                Log.e("Getnet@VendaController", "chamaVenda: Tipo de movimentação inválido")
                return
            }
        }
        val bundle = bundleOf(
                "paymentType" to paymentType,
                "amount" to String.format("%012d", valorLong),
                "currencyPosition" to "CURRENCY_AFTER_AMOUNT",
                "currencyCode" to "986"
        )

        val intent = Intent(Intent.ACTION_VIEW, Uri.parse(URI_PAYMENT))
        intent.putExtras(bundle)
        activity.startActivityForResult(intent, REQUEST_CODE)
    }

    override fun chamaEstorno(valor: Float, cvNumber: String, dataTransacao: String, transacaoListener: TransacaoListener) {
        this.transacaoListener = transacaoListener
        val valorLong = (valor * 100).toLong()

        var dataFormatada = dataTransacao.substring(0, 2) + "/" + dataTransacao.substring(2, 4) + "/" + dataTransacao.substring(4, 8)

        val bundle = bundleOf(
                "amount" to String.format("%012d", valorLong),
                "cvNumber" to cvNumber,
                "transactionDate" to dataFormatada
        )

        val intent = Intent(Intent.ACTION_VIEW, Uri.parse(URI_REFUND))
        intent.putExtras(bundle)
        activity.startActivityForResult(intent, REQUEST_CODE)
    }


    override fun onIntegrationResult(requestCode: Int, resultCode: Int, data: Intent?) {
        Log.d("integrationResult@rq", requestCode.toString())
        Log.d("integrationResult@rs", resultCode.toString())
        Log.d("integrationResult@dt", data.toString())


        if (requestCode == REQUEST_CODE && data != null) {
            val paymentResultCode = data.extras?.getString(RESULT_KEY) ?: ""
            val paymentResult = PaymentResult.from(paymentResultCode)
            if (paymentResult != null) {
                Toast.makeText(activity, paymentResult.descricao, Toast.LENGTH_SHORT).show()
                val paymentData = getPaymentData(data.extras)
                val estadoTransacao = when (paymentResult) {
                    PaymentResult.SUCESSO -> VendaController.EstadoTransacao.SUCESSO
                    PaymentResult.NEGADA -> VendaController.EstadoTransacao.NEGADA
                    PaymentResult.CANCELADA -> VendaController.EstadoTransacao.CANCELADA
                    PaymentResult.FALHA, PaymentResult.DESCONHECIDO -> VendaController.EstadoTransacao.ERRO
                }

                Log.d("TAGG",estadoTransacao.toString() +"/"+paymentData.toString()+"/"+paymentResult.descricao);
                this.transacaoListener?.transacaoConcluida(
                        estadoTransacao,
                        paymentResult.descricao,
                        paymentResultCode,
                        paymentData
                )

                this.transacaoListener = null
                VendaAtual.venda = null
            }

        }
    }

    private fun getPaymentData(result: Bundle?): JSONObject {
        val paymentData = JSONObject()
        if (result != null && VendaAtual.venda != null) {
            paymentData.put("nsu", result.getString(NSU_KEY))
            paymentData.put("date", result.getString(DATETIME_KEY))
            paymentData.put("Value", result.getString(AMOUNT_KEY))
            paymentData.put("CV", result.getString(CV_KEY))
            paymentData.put("OperationType", result.getString(TYPE_KEY))
            paymentData.put("binCard", result.getString(CARDBIN_KEY))
            paymentData.put("lastNumbersCard", result.getString(CARDLASTDIG_KEY))
            paymentData.put("cardBrandName", result.getString(CARDBRAND_KEY))
            paymentData.put("uniqueSequentialNumber", result.getString(NSUHOST_KEY))
        } else if (result != null) {
            paymentData.put("nsu", result.getString(NSU_KEY))
            paymentData.put("date", result.getString(DATEREFUND_KEY))
        }

        return paymentData
    }

}