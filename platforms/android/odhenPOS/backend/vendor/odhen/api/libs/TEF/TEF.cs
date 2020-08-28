using System;
using System.EnterpriseServices;
using System.IO;
using System.Web.Script.Serialization;

namespace TEF
{
    public interface TEFInterface {
        int configuraSitef(string endSiTef, string idLoja, string idTerminal);
        string iniciaSiTef(int funcao, string valor, string cupomFiscal, string dataFiscal, string horario, string operador, string paramsAdicionais);
        string continuaTransacao(int comando, int tipoCampo, int tamMinimo, int tamMaximo, string buffer, int tamBuffer, int continua);
        int finalizaTransacao(int confirma, string cupomFiscal, string dataFiscal, string horario);
        int escreveMensagemPinPad(string mensagem);
    }

    public class retornoInicia
    {
    	public int retornoDll;
        public int funcao;
        public string valor;
        public string cupomFiscal;
        public string dataFiscal;
        public string horario;
        public string operador;

        public retornoInicia(int retornoDll, int funcao, string valor, string cupomFiscal, string dataFiscal, string horario, string operador)
        {
        	this.retornoDll = retornoDll;
            this.funcao = funcao;
            this.valor = valor;
            this.cupomFiscal = cupomFiscal;
            this.dataFiscal = dataFiscal;
            this.horario = horario;
            this.operador = operador;
        }
    }

    public class retornoContinua
    {
        public int retornoDll;
        public int comando;
        public int tipoCampo;
        public int tamMinimo;
        public int tamMaximo;
        public string bufferConverted;
        public int tamBuffer;
        public int continua;

        public retornoContinua(int retornoDll, int comando, int tipoCampo, int tamMinimo, int tamMaximo, string bufferConverted, int tamBuffer, int continua)
        {
            this.retornoDll = retornoDll;
            this.comando = comando;
            this.tipoCampo = tipoCampo;
            this.tamMinimo = tamMinimo;
            this.tamMaximo = tamMaximo;
            this.bufferConverted = bufferConverted;
            this.tamBuffer = tamBuffer;
            this.continua = continua;
        }
    }

    public class TEFClass : ServicedComponent, TEFInterface {
        public int configuraSitef(string endSiTef, string idLoja, string idTerminal)
        {
            return TEFDLL.ConfiguraIntSiTefInterativoEx(endSiTef, idLoja, idTerminal, 0, "[VersaoAutomacaoCielo=TEKNISAS10]");
        }

        public string iniciaSiTef(int funcao, string valor, string cupomFiscal, string dataFiscal, string horario, string operador, string paramsAdicionais)
        {
            int dllReturn = TEFDLL.IniciaFuncaoSiTefInterativo(funcao, valor, cupomFiscal, dataFiscal, horario, operador, paramsAdicionais);
            var retInicia = new retornoInicia(dllReturn, funcao, valor, cupomFiscal, dataFiscal, horario, operador);
            var json = new JavaScriptSerializer().Serialize(retInicia);
            return json;
        }

        public string continuaTransacao(int comando, int tipoCampo, int tamMinimo, int tamMaximo, string buffer, int tamBuffer, int continua) {
            byte[] bufferInput = System.Text.Encoding.ASCII.GetBytes(buffer);
            int bytesLength = (5 * bufferInput.Length) + 20;
            if (bytesLength <= 20000)
            {
                bytesLength = 20000;
            }
            int dllReturn = TEFDLL.ContinuaFuncaoSiTefInterativo(ref comando, ref tipoCampo, ref tamMinimo, ref tamMaximo, bufferInput, tamBuffer, continua);
            string bufferResponse = System.Text.Encoding.ASCII.GetString(bufferInput);
            var retContinua = new retornoContinua(dllReturn, comando, tipoCampo, tamMinimo, tamMaximo, bufferResponse, tamBuffer, continua);
            var json = new JavaScriptSerializer().Serialize(retContinua);
            return json;
        }

        public int finalizaTransacao(int confirma, string cupomFiscal, string dataFiscal, string horario)
        {
            return TEFDLL.FinalizaTransacaoSiTefInterativo(confirma, cupomFiscal, dataFiscal, horario);
        }

        public int escreveMensagemPinPad(string mensagem)
        {
            return TEFDLL.EscreveMensagemPermanentePinPad(mensagem);
        }
    }
}
