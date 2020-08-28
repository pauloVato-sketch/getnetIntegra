using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Runtime.InteropServices;

namespace TEF {
    class TEFDLL {
        [DllImport("CliSiTef32I.dll")]
        public static extern int ConfiguraIntSiTefInterativoEx(string endSiTef, string idLoja, string idTerminal, int reservado, string parametrosAdicionais);

        [DllImport("CliSiTef32I.dll")]
        public static extern int IniciaFuncaoSiTefInterativo(int funcao, string valor, string cupomFiscal, string dataFiscal, string horario, string operador, string paramAdic);

        [DllImport("CliSiTef32I.dll")]
        public static extern int ContinuaFuncaoSiTefInterativo(ref int comando, ref int tipoCampo, ref int tamMinimo, ref int tamMaximo, byte[] buffer, int tamBuffer, int continua);

        [DllImport("CliSiTef32I.dll")]
        public static extern int FinalizaTransacaoSiTefInterativo(int confirma, string cupomFiscal, string dataFiscal, string horario);

        [DllImport("CliSiTef32I.dll")]
        public static extern int EscreveMensagemPermanentePinPad(string mensagem);
    }
}
