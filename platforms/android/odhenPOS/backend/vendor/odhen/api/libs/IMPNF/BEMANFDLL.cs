using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Runtime.InteropServices;

namespace IMPNF
{
    class BEMANFDLL
    {
        [DllImport("MP2032.dll")]
        public static extern int IniciaPorta(string porta);

        [DllImport("MP2032.dll")]
        public static extern int AcionaGuilhotina(int parcialOuTotal); // 0: parcial, 1: total

        [DllImport("MP2032.dll")]
        public static extern int ComandoTX(string comando, int tamanhoComando);

        [DllImport("MP2032.dll")]
        public static extern int FormataTX(string texto, int tipoLetra, int italico, int sublinhado, int expandido, int enfatizado);

        [DllImport("MP2032.dll")]
        public static extern int FechaPorta();

        [DllImport("MP2032.dll")]
        public static extern int ConfiguraCodigoBarras(int altura, int largura, int posicao, int fonte, int margem);

        [DllImport("MP2032.dll")]
        public static extern int ImprimeCodigoBarrasCODE128(string texto);

        [DllImport("MP2032.dll")]
        public static extern int ImprimeCodigoQRCODE(int errorCorrectionLevel, int moduleSize, int codeType, int QRCodeVersion, int encodingModes, string codeQr);

        [DllImport("MP2032.dll")]
        public static extern int Le_Status();
    }
}
