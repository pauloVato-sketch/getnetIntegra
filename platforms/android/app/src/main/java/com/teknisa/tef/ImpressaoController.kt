package com.teknisa.tef

import android.content.Context
import android.graphics.Bitmap
import android.util.Log
import com.odhen.deviceintegrationfacade.Enums.Alinhamento
import java.lang.Integer.parseInt

import com.teknisa.tef.pos.StoneIntegration

class ImpressaoController(): com.odhen.deviceintagrationfacade.Controllers.ImpressaoController {

    override val textFontSize = 12
    override val qrCodeSize = 12
    override val barCodeSize = 12

    var stone = VendaController.stone

    override fun imprimeTexto(texto: String) {
        val jsonArray = org.json.JSONArray("" +
                "[{'text': '" + texto + "\n'," +
                "'fontSize': " + textFontSize + "," +
                "'textAlign': 'CENTER'," +
                "'fontWeight': 'NORMAL'}]"
        )
        stone.printText(jsonArray)
    }

    override fun imprimeQrCode(content: String) {
        val jsonArray = org.json.JSONArray("" +
                "[{'seed': '" + content + "'}]"
        )
        stone.printQrCode(jsonArray)
    }

    override fun imprimeCodBarra(content: String) {
        val jsonArray = org.json.JSONArray("" +
                "[{'seed': '" + content + "', 'isLast': 'false'}]"
        )
        stone.printBarCode(jsonArray)
    }

    override fun imprimeImagem(
            imagem: Bitmap,
            alinhamento: Alinhamento?,
            offset: Int?,
            altura: Int?,
            largura: Int?
    ) {

    }

    override fun reportError():Int? {
        return 1000
    }

    fun getCode():Int?{
        return 0
    }

    fun setCode(value:Int?){
        
    }

}