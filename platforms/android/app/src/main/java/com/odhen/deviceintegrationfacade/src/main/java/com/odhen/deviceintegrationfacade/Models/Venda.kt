package com.odhen.deviceintagrationfacade.Models

import com.odhen.deviceintagrationfacade.Enums.EstadoVenda
import com.odhen.deviceintagrationfacade.Enums.TipoMovimentacao
import java.text.SimpleDateFormat
import java.util.*

class Venda (val valor: Float, val tipoMovimentacao: TipoMovimentacao) {

    val id: String
    var estado: EstadoVenda
    init {
        val dateFormat = SimpleDateFormat("yyyy/MM/dd HH:mm:ss")
        val date = Date()
        val orderCode = dateFormat.format(date)

        this.id = orderCode.replace("/", "").replace(":", "").replace(" ", "")
        this.estado = EstadoVenda.AUTENTICACAO
    }
}