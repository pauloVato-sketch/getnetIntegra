using System;
using System.Linq;
using System.Text;
using System.Runtime.InteropServices;

namespace SAT
{
    class DIMEP
    {
        [DllImport("dllsat_DIMEP.dll")]
        public static extern IntPtr EnviarDadosVenda(int numSessao, string codigoDeAtivacao, string dadosVenda);

        [DllImport("dllsat_DIMEP.dll")]
        public static extern IntPtr CancelarUltimaVenda(int numSessao, string codigoDeAtivacao, string chave, string dadosCancelamento);

        [DllImport("dllsat_DIMEP.dll")]
        public static extern IntPtr ConsultarStatusOperacional(int numSessao, string codigoDeAtivacao);

        [DllImport("dllsat_DIMEP.dll")]
        public static extern IntPtr ConsultarSAT(int numSessao);

        [DllImport("dllsat_DIMEP.dll")]
        public static extern IntPtr TrocarCodigoDeAtivacao(int numSessao, string codigoDeAtivacao, int opcao, string novoCodigo, string confNovoCodigo);

        [DllImport("dllsat_DIMEP.dll")]
        public static extern IntPtr ExtrairLogs(int numSessao, string codigoDeAtivacao);
    }
}