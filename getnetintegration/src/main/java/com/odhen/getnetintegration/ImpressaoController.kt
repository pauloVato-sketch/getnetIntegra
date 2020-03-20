package com.odhen.getnetintegration

import android.content.Context
import android.graphics.Bitmap
import android.util.Log
import com.getnet.posdigital.PosDigital
import com.getnet.posdigital.printer.FontFormat
import com.getnet.posdigital.printer.AlignMode
import com.getnet.posdigital.printer.IPrinterService
import com.getnet.posdigital.printer.PrinterStatus
import com.odhen.deviceintegrationfacade.Enums.Alinhamento
import java.lang.Integer.parseInt

class ImpressaoController: com.odhen.deviceintagrationfacade.Controllers.ImpressaoController {
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
    override val textFontSize: Int = FontFormat.MEDIUM
    override val qrCodeSize = 300
    override val barCodeSize = null

    private var printer: IPrinterService? = null
    private var printerListener = ImpressaoListener()
    private var code : Int? = 0

    fun initPrinterParams(printer: IPrinterService?) {
        printer?.setGray(5)
        printer?.defineFontFormat(this.textFontSize)
        printer?.init()
        this.printer = printer
    }

    override fun imprimeTexto(texto: String) {
        //Em algumas situações,aparentemente a integração reseta as definições da impressora
        //alterando a fonte e o tom do cinza,de forma que é necessário garantir a inicialização
        this.initPrinterParams(printer)

        printer?.addText(AlignMode.CENTER, texto)
        printer?.print(printerListener)
        Thread.sleep(1_000)

        setCode(printer?.status)


    }

    override fun imprimeQrCode(content: String) {
        //Houve concordância sobre o tamanho das fontes e é a de que o texto com fonte pequena é melhor,
        //porém o qrcode pode sofrer mal-funcionamento nessa fonte,de forma que no caso do qrcode
        // é melhor deixá-lo imprimir na fonte média e depois revertermos a fonte para pequena

        printer?.defineFontFormat(FontFormat.MEDIUM)

        printer?.addQrCode(AlignMode.CENTER, 240, content)
        printer?.addText(AlignMode.LEFT, "\n\n\n")
        printer?.print(printerListener)
        Thread.sleep(1_000)

        setCode(printer?.status)
        printer?.defineFontFormat(this.textFontSize)

    }

    override fun imprimeCodBarra(content: String) {
        printer?.addBarCode(AlignMode.CENTER, content)
        printer?.addText(AlignMode.LEFT, "\n\n\n")
        printer?.print(printerListener)
        Thread.sleep(1_000)

        setCode(printer?.status)
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
        printer?.addText(AlignMode.LEFT, "\n\n\n")
        printer?.print(printerListener)
        Thread.sleep(1_000)

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

}