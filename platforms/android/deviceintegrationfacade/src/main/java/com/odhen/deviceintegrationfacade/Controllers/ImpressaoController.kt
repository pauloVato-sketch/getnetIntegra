package com.odhen.deviceintagrationfacade.Controllers

import android.graphics.Bitmap
import com.odhen.deviceintegrationfacade.Enums.Alinhamento

interface ImpressaoController {

    companion object {
        var instance: ImpressaoController? = null
    }

    val textFontSize: Int?
    val qrCodeSize: Int?
    val barCodeSize: Int?

    fun imprimeTexto(texto: String)
    fun imprimeQrCode(content: String)
    fun imprimeCodBarra(content: String)

    fun reportError():Int?

    fun imprimeImagem(imagem: Bitmap) {
        imprimeImagem(imagem, null, null, null, null)
    }
    fun imprimeImagem(imagem: Bitmap, alinhamento: Alinhamento) {
        imprimeImagem(imagem, alinhamento, null, null, null)
    }
    fun imprimeImagem(imagem: Bitmap, alinhamento: Alinhamento?, offset: Int) {
        imprimeImagem(imagem, alinhamento, offset, null, null)
    }
    fun imprimeImagem(imagem: Bitmap, alinhamento: Alinhamento?, offset: Int?, altura: Int?, largura: Int?)

}