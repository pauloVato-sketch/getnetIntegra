package com.odhen.POS

import android.content.Intent
import android.util.Log
import com.odhen.deviceintagrationfacade.Controllers.ImpressaoController
import com.odhen.deviceintagrationfacade.Controllers.VendaController
import com.odhen.deviceintagrationfacade.Enums.TipoMovimentacao
import com.odhen.deviceintagrationfacade.Models.PaymentData
import com.odhen.deviceintagrationfacade.Models.RefundData
import com.odhen.deviceintagrationfacade.Shared.VendaAtual
import com.odhen.deviceintegrationfacade.Interfaces.DeviceIntegrationListener
import org.apache.cordova.CallbackContext
import org.apache.cordova.CordovaArgs
import org.apache.cordova.CordovaPlugin
import org.json.JSONArray
import org.json.JSONException
import org.json.JSONObject
import java.lang.Integer.parseInt

class IntegrationService: CordovaPlugin() {

    class IntegrationInstance(val callbackContext: CallbackContext):
            VendaController.TransacaoListener {

        override fun transacaoConcluida(
                estado: VendaController.EstadoTransacao,
                menssagem: String?,
                codigo: String?,
                data: JSONObject?
        ) {
            val error = estado !== VendaController.EstadoTransacao.SUCESSO
            try {
                val data = data?:"{}"
                val returnObj = JSONObject()
                returnObj.put("error", error)
                returnObj.put("message", menssagem ?: "")
                returnObj.put("data", data)

                callbackContext.success(returnObj)
            } catch (e: JSONException) {}
        }
    }

    override fun pluginInitialize() {
        super.pluginInitialize()
        val mainActivity = cordova.activity as MainActivity

        /* CIELO LIO */
        //ImpressaoController.instance = com.odhen.cielointegration.ImpressaoController(this)
        //VendaController.instance = com.odhen.cielointegration.VendaController(this)

        /* GERTEC - SITEF */
        //ImpressaoController.instance = com.odhen.gertecintegration.ImpressaoController(this)
        //VendaController.instance = com.odhen.sitefintegration.VendaController(this)

        /* GERTEC - REDE */
        //ImpressaoController.instance = com.odhen.gertecintegration.ImpressaoController(this)
        //VendaController.instance = com.odhen.redeintegration.VendaGertec(this)

        /* INGENICO - SITEF */
        //ImpressaoController.instance = com.odhen.ingenicointegration.ImpressaoController(this)
        //VendaController.instance = com.odhen.sitefintegration.VendaController(this)

        /* GETNET */
        val getnetController = com.odhen.getnetintegration.GetnetController(mainActivity)
        ImpressaoController.instance = getnetController.impressaoController
        VendaController.instance = getnetController.vendaController
        mainActivity.deviceIntegrationListener = getnetController.vendaController


    }

    @Throws(JSONException::class)
    override fun execute(action: String, args: JSONArray, callbackContext: CallbackContext): Boolean {
        val integrationInstance = IntegrationInstance(callbackContext)

        //Alteração de vários ifs para um when com as ações que podem ser realizadas pela máquina
        return when (action) {
            "payment"->{
                payment(args, integrationInstance)
                true}
            "refund"->{
                refund(args, integrationInstance)
                true}
            "print"->{
                print(args, integrationInstance)
                true}

            else->false
        }
    }

    @Throws(JSONException::class, InterruptedException::class)
    fun payment(args: JSONArray, integrationInstance: IntegrationInstance)  {
        val data = JSONObject(args.getString(0))
        val paymentData = PaymentData(
            Integer.parseInt(data.get("paymentType").toString()),
            data.get("paymentValue").toString(),
            data.get("paymentNSU").toString()
        )
        val paymentType =  when(paymentData.paymentType) {
            1 -> TipoMovimentacao.CREDITO
            2 -> TipoMovimentacao.DEBITO
            else -> {
                return
            }
        }
        //logManager.write("----------     INÍCIO DA TRANSAÇÃO     ----------")

        VendaController.instance?.chamaVenda(
                paymentData.paymentValue.toFloat(),
                paymentType,
                integrationInstance
        )
    }
    @Throws(JSONException::class, InterruptedException::class)
    fun refund(args: JSONArray,integrationInstance: IntegrationInstance){
        val data = JSONObject(args.getString(0))
        val refundData = RefundData(
                Integer.parseInt(data.get("refundType").toString()),
                data.get("refundValue").toString(),
                data.get("refundDate").toString(),
                data.get("refundCV").toString()
        )

        //Código inutilizado
        /*val refundType =  when(refundData.refundType) {
            1 -> TipoMovimentacao.CREDITO
            2 -> TipoMovimentacao.DEBITO
            else -> {
                return
            }
        }*/


        VendaController.instance?.chamaEstorno(
                refundData.refundValue.toFloat(),
                refundData.refundCV,
                refundData.refundDate,
                integrationInstance
        )

    }

    @Throws(JSONException::class, InterruptedException::class)
    fun print(args: JSONArray, integrationInstance: IntegrationInstance){
        val data = JSONObject(args.getString(0))

        /*
        Pelo fato de as impressões serem diferentes e virem de diferentes funções dos arquivos de impressão,
        foi criado um when com essas ações que ficam na flag,de forma a facilitar a distribuição do código
        */

        when(data.get("flag").toString()){
            "printText"->{
                /*Paliativo:Às vezes a primeira impressão não sai,como no caso da abertura do caixa.
                Suspeito que os parâmetros não estão sendo inicializados no momento certo,gerando inconsistências
                Dessa forma,imprimi um vazio antes de realizar as impressões,para tentar burlar isso
                */
               ImpressaoController.instance?.imprimeTexto("")


                Log.d("TAGG",String.format("        \n%s\n",data.get("texto").toString()))
                ImpressaoController.instance?.imprimeTexto(data.get("texto").toString())

                /*É necessário que façamos o success no callback,senão o PrinterGetnet.js
                não consegue resolver a promise feita,
                ou ainda podemos implementar o error no PrinterGetnet.js*/
                if(ImpressaoController.instance?.reportError()!=0){

                    integrationInstance.callbackContext.success(    this.getJsonFromPrinter(
                            ImpressaoController.instance?.reportError(),true
                    ))
                }else {

                    integrationInstance.callbackContext.success(    this.getJsonFromPrinter(
                            ImpressaoController.instance?.reportError(), false
                    ))
                }
            }
            "qrCode"->{
                //Paliativo
//                ImpressaoController.instance?.imprimeTexto("                ")
                Log.d("TAGG",String.format("        \n%s\n",data.get("qrcode").toString()))
                ImpressaoController.instance?.imprimeQrCode(data.get("qrcode").toString())
                if(ImpressaoController.instance?.reportError()!=0){

                    integrationInstance.callbackContext.success(    this.getJsonFromPrinter(
                            ImpressaoController.instance?.reportError(),true
                    ))
                }else {

                    integrationInstance.callbackContext.success(    this.getJsonFromPrinter(
                            ImpressaoController.instance?.reportError(),false
                    ))
                }

            }
            "barCode"->{
                //Paliativo
//                ImpressaoController.instance?.imprimeTexto("                ")
                Log.d("TAGG",String.format("        \n%s\n",data.get("barcode").toString()))
                ImpressaoController.instance?.imprimeCodBarra(data.get("barcode").toString())
                if(ImpressaoController.instance?.reportError()!=0){

                    integrationInstance.callbackContext.success(    this.getJsonFromPrinter(
                            ImpressaoController.instance?.reportError(),true
                    ))
                }else {

                    integrationInstance.callbackContext.success(    this.getJsonFromPrinter(
                            ImpressaoController.instance?.reportError(),false
                    ))
                }

            }
            else->{
                integrationInstance.callbackContext.error("Erro ao tentar imprimir")
            }
        }

    }

    private fun getJsonFromPrinter(code :Int?,error:Boolean):JSONObject{

        return JSONObject("{ error : ${error}, message   : $code }")
    }


}