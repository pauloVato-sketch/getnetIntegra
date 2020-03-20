package com.odhen.deviceintagrationfacade.Controllers

import com.odhen.deviceintagrationfacade.Enums.TipoMovimentacao
import org.json.JSONObject

interface VendaController {

    enum class EstadoTransacao(value: Int) {
        SUCESSO(0),
        NEGADA(-1),
        CANCELADA(-2),
        ERRO(-3);
    }
    interface TransacaoListener {
        fun transacaoConcluida(estado: EstadoTransacao) {
            transacaoConcluida(estado, null, null, null)
        }
        fun transacaoConcluida(estado: EstadoTransacao, mensagem: String?) {
            transacaoConcluida(estado, mensagem, null, null)
        }
        fun transacaoConcluida(estado: EstadoTransacao, mensagem: String?, codigo: String) {
            transacaoConcluida(estado, mensagem, codigo, null)
        }
        fun transacaoConcluida(
                estado: EstadoTransacao,
                mensagem: String?,
                codigo: String?,
                data: JSONObject?
        )
    }

    companion object {
        var instance: VendaController? = null
    }

    fun chamaVenda(valor: Float, tipoMovimentacao: TipoMovimentacao, transacaoListener: TransacaoListener)
    fun testeComunicacao() {}
    fun chamaEstorno(valor: Float, cvNumber: String, dataTransacao: String,transacaoListener: TransacaoListener)
}