package com.odhen.getnetintegration

enum class PaymentResult(val value: String, val descricao: String) {
    SUCESSO("0", "Transação efetuada com sucesso"),
    NEGADA("1", "Transação negada pelo servidor (emissor, bandeira, ...)"),
    CANCELADA("2", "Transação cancelada (pelo servidor ou usuário)"),
    FALHA("3", "Falha ao completar a transação (internet, servidor, emissor, ... )"),
    DESCONHECIDO("4", "Erro desconhecido");

    companion object {
        fun from(value: String): PaymentResult? {
            return values().find {
                it.value == value
            }
        }
    }
}