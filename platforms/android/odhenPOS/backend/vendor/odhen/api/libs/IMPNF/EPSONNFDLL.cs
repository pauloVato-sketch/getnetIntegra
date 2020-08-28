using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Runtime.InteropServices;

namespace IMPNF
{
    class EPSONNFDLL
    {
        [DllImport("InterfaceEpsonNF.dll")]
        public static extern int IniciaPorta(string porta);

        [DllImport("InterfaceEpsonNF.dll")]
        public static extern int ConfiguraTaxaSerial(int taxa);

        [DllImport("InterfaceEpsonNF.dll")]
        public static extern int ComandoTX(string comando, int tamanhoComando);

        [DllImport("InterfaceEpsonNF.dll")]
        public static extern int AcionaGuilhotina(int tipoCorte);

        [DllImport("InterfaceEpsonNF.dll")]
        public static extern int FormataTX(string texto, int tipoLetra, int italico, int sublinhado, int expandido, int enfatizado);

        [DllImport("InterfaceEpsonNF.dll")]
        public static extern int FechaPorta();

        [DllImport("InterfaceEpsonNF.dll")]
        public static extern int ConfiguraCodigoBarras(int altura, int largura, int posicao, int fonte, int margem);

        [DllImport("InterfaceEpsonNF.dll")]
        public static extern int ImprimeCodigoBarrasCODE128(string texto);

        [DllImport("InterfaceEpsonNF.dll")]
        public static extern int ImprimeCodigoQRCODE(int errorCorrectionLevel, int moduleSize, int codeType, int QRCodeVersion, int encodingModes, string codeQr);

        [DllImport("InterfaceEpsonNF.dll")]
        public static extern int Le_Status();

    }
}
