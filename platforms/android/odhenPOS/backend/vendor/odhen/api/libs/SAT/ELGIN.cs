using System;
using System.Linq;
using System.Text;
using System.Runtime.InteropServices;

namespace SAT
{
    class ELGIN
    {
        [DllImport("dllsat_ELGIN.dll")]
        public static extern IntPtr EnviarDadosVenda(int numSessao, string codigoDeAtivacao, string dadosVenda);

        [DllImport("dllsat_ELGIN.dll")]
        public static extern IntPtr CancelarUltimaVenda(int numSessao, string codigoDeAtivacao, string chave, string dadosCancelamento);

        [DllImport("dllsat_ELGIN.dll")]
        public static extern IntPtr ConsultarStatusOperacional(int numSessao, string codigoDeAtivacao);

        [DllImport("dllsat_ELGIN.dll")]
        public static extern IntPtr ConsultarSAT(int numSessao);

        [DllImport("dllsat_ELGIN.dll")]
        public static extern IntPtr TrocarCodigoDeAtivacao(int numSessao, string codigoDeAtivacao, int opcao, string novoCodigo, string confNovoCodigo);

        [DllImport("dllsat_ELGIN.dll")]
        public static extern IntPtr ExtrairLogs(int numSessao, string codigoDeAtivacao);
    }

    class ELGIN_MFE: ELGIN
    {
        [DllImport("mfe.dll")]
        public static new extern IntPtr EnviarDadosVenda(int numSessao, string codigoDeAtivacao, string dadosVenda);

        [DllImport("mfe.dll")]
        public static new extern IntPtr CancelarUltimaVenda(int numSessao, string codigoDeAtivacao, string chave, string dadosCancelamento);

        [DllImport("mfe.dll")]
        public static new extern IntPtr ConsultarStatusOperacional(int numSessao, string codigoDeAtivacao);

        [DllImport("mfe.dll")]
        public static new extern IntPtr ConsultarSAT(int numSessao);

        [DllImport("mfe.dll")]
        public static new extern IntPtr TrocarCodigoDeAtivacao(int numSessao, string codigoDeAtivacao, int opcao, string novoCodigo, string confNovoCodigo);

        [DllImport("mfe.dll")]
        public static new extern IntPtr ExtrairLogs(int numSessao, string codigoDeAtivacao);
    }
}
