using System;
using System.Linq;
using System.Text;
using System.Runtime.InteropServices;

namespace SAT
{
    class TANCA
    {
        [DllImport("SAT_TANCA.dll")]
        public static extern IntPtr EnviarDadosVenda(int numSessao, string codigoDeAtivacao, string dadosVenda);

        [DllImport("SAT_TANCA.dll")]
        public static extern IntPtr CancelarUltimaVenda(int numSessao, string codigoDeAtivacao, string chave, string dadosCancelamento);

        [DllImport("SAT_TANCA.dll")]
        public static extern IntPtr ConsultarStatusOperacional(int numSessao, string codigoDeAtivacao);

        [DllImport("SAT_TANCA.dll")]
        public static extern IntPtr ConsultarSAT(int numSessao);

        [DllImport("SAT_TANCA.dll")]
        public static extern IntPtr TrocarCodigoDeAtivacao(int numSessao, string codigoDeAtivacao, int opcao, string novoCodigo, string confNovoCodigo);

        [DllImport("SAT_TANCA.dll")]
        public static extern IntPtr ExtrairLogs(int numSessao, string codigoDeAtivacao);
    }
}
