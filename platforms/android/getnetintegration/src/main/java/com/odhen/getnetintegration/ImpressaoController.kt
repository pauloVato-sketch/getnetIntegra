package com.odhen.getnetintegration

import android.content.Context
import android.graphics.Bitmap
import android.graphics.Color
import android.net.Uri
import android.util.Log
import com.getnet.posdigital.PosDigital
import com.getnet.posdigital.printer.FontFormat
import com.getnet.posdigital.printer.AlignMode
import com.getnet.posdigital.printer.IPrinterService
import com.getnet.posdigital.printer.PrinterStatus
import com.google.zxing.BarcodeFormat
import com.odhen.deviceintegrationfacade.Enums.Alinhamento
import java.lang.Integer.parseInt
import com.google.zxing.MultiFormatWriter
import java.util.*

class ImpressaoController: com.odhen.deviceintegrationfacade.Controllers.ImpressaoController {
    /*
    *PrinterStatus.OK === 0    -> "OK"
    *PrinterStatus.PRINTING === 1 -> "Imprimindo"
    *PrinterStatus.ERROR_NOT_INIT === 2 -> "Impressora não iniciada"
    *PrinterStatus.ERROR_OVERHEAT === 3 -> "Impressora superaquecida"
    *PrinterStatus.ERROR_BUFOVERFLOW === 4 -> "Fila de impressão muito grande"
    *PrinterStatus.ERROR_PARAM === 5 -> "Parametros incorretos"
    *PrinterStatus.ERROR_LIFTHEAD === 10 -> "Porta da impressora aberta"
    *PrinterStatus.ERROR_LOWTEMP === 11 -> "Temperatura baixa demais para impressão"
    *PrinterStatus.ERROR_LOWVOL === 12 -> "Sem bateria suficiente para impressão"
    *PrinterStatus.ERROR_MOTORERR === 13 -> "Motor de passo com problemas"
    *PrinterStatus.ERROR_NO_PAPER === 15 -> "Sem bonina"
    *PrinterStatus.ERROR_PAPERENDING === 16 -> "Bobina acabando"
    *PrinterStatus.ERROR_PAPERJAM === 17 -> "Bobina travada"
    *PrinterStatus.UNKNOW === 1000 -> "Não foi possível definir o erro"
    * */
    override val textFontSize: Int = FontFormat.SMALL
    override val qrCodeSize = 350
    override val barCodeSize = 500

    private var printer: IPrinterService? = null
    private var printerListener = ImpressaoListener()
    private var code : Int? = 0

    fun initPrinterParams(printer: IPrinterService?) {
        printer?.init()
        printer?.setGray(5)
        printer?.defineFontFormat(this.textFontSize)
        this.printer = printer

    }

    override fun imprimeTexto(texto: String) {
        //Em algumas situações,aparentemente a integração reseta as definições da impressora
        //alterando a fonte e o tom do cinza,de forma que é necessário garantir a inicialização
        printer?.defineFontFormat(this.textFontSize)
        Thread.sleep(350)
        printer?.addText(AlignMode.CENTER, texto)
        printer?.addText(AlignMode.CENTER, "\n\n\n")
        printer?.print(printerListener)
        Thread.sleep(700)
        setCode(printer?.status)


    }

    override fun imprimeQrCode(content: String) {
        //Houve concordância sobre o tamanho das fontes e é a de que o texto com fonte pequena é melhor,
        //porém o qrcode pode sofrer mal-funcionamento nessa fonte,de forma que no caso do qrcode
        // é melhor deixá-lo imprimir na fonte média e depois revertermos a fonte para pequena

        printer?.defineFontFormat(this.textFontSize)

        printer?.addQrCode(AlignMode.CENTER, qrCodeSize, content)
        printer?.addText(AlignMode.LEFT, "\n")
        printer?.print(printerListener)
        Thread.sleep(700)

        setCode(printer?.status)
        printer?.defineFontFormat(this.textFontSize)

    }

    override fun imprimeCodBarra(content: String) {

        //addBarCode only works with manual strings and not with String variables!!
        //so I resorted to using bitmap images to print the barcode

        //printer?.addBarCode(AlignMode.CENTER, content)
        //printer?.addText(AlignMode.LEFT, "\n\n")
        //printer?.print(printerListener)
        //setCode(printer?.status)
        if(content!="") {
            var image = this.createReceiptBarcode(content)
            this.imprimeImagem(image, Alinhamento.CENTRO)
        }
    }

    override fun imprimeImagem(
        imagem: Bitmap,
        alinhamento: Alinhamento?,
        offset: Int?,
        altura: Int?,
        largura: Int?
    ) {
        val alignMode = when(alinhamento){
            Alinhamento.ESQUERDA        -> AlignMode.LEFT
            Alinhamento.DIREITA         -> AlignMode.RIGHT
            Alinhamento.CENTRO, null    -> AlignMode.CENTER
        }
        printer?.addImageBitmap(alignMode, imagem)
        Thread.sleep(250)
        printer?.addText(AlignMode.LEFT, "\n\n")
        printer?.print(printerListener)
        Thread.sleep(700)

        setCode(printer?.status)
    }

    override fun reportError():Int? {
        if (getCode() != null){
            return getCode()
        }else{
            return 1000
        }
    }

    fun getCode():Int?{
        return this.code
    }

    fun setCode(value:Int?){

        this.code=value
    }

    private fun createReceiptBarcode(content:String):Bitmap{
        val writer = MultiFormatWriter()
        val finalData: String = Uri.encode(content)

        // Use 1 as the height of the matrix as this is a 1D Barcode.

        // Use 1 as the height of the matrix as this is a 1D Barcode.
        val bm = writer.encode(finalData, BarcodeFormat.CODE_128, 450, 100)
        val bmWidth: Int = bm.getWidth()

        val imageBitmap = Bitmap.createBitmap(bmWidth, 100, Bitmap.Config.ARGB_8888)

        for (i in 0 until bmWidth) {
            // Paint columns of width 1
            val column = IntArray(100)
            Arrays.fill(column, if (bm.get(i, 0)) Color.BLACK else Color.WHITE)
            imageBitmap.setPixels(column, 0, 1, i, 0, 1, 100)
        }

        return imageBitmap
    }

}