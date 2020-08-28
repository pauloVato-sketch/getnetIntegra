using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Runtime.InteropServices;


namespace IMPNF
{
    class ELGINKIOSK
    {
        [DllImport("KIOSKDLL.dll")]
        public static extern int KIOSK_OpenUsb();

        [DllImport("KIOSKDLL.dll")]
        public static extern int KIOSK_CloseUsb(int port);

        /*
            Function: KIOSK_S_Textout
            Desc: Envia dados para impressora, utilizado para impressão de Texto
            Parametro:  nPort:         identificação da porta de conexão
                        nPortType:     tipo da porta de comunicação
                        pszData:       Dados a serem impressos
                        nOrgx:         define a distancia entre o inicio da impressão e a margem esquerda
                        nWidthTimes:   aumenta a largura
                        nHeightTimes:  aumenta a altura
                        nFontType:    Tipo de fonte(Padrão ou comprimido)
                        nFontStyle:   estilo da fonte(negrito, normal, sublinhado...)
        */
        [DllImport("KIOSKDLL.dll")]
        public static extern int KIOSK_S_Textout(int port, int tipoPorta, string texto, int margemEsquerda, int aumentaLargura, int aumentAltura, int tipoFonte, int estiloFonte);

        /*
           Function: KIOSK_S_PrintBarcode
           Desc: Realiza a impressão de codigo de barras
           Parametro: nPort:            identificação da porta de conexão
                      nPortType:        tipo da porta de comunicação
                      pszBuffer:        dados
                      nOrgx:            distancia entre o codigo de barras e a margin esquerda
                      nType             tipo de codigo de barras
                      nwidth            largura do codigo de barras
                      nHeight           altura do codigo de barras
                      nWRIFontType      tipo de fonte do codigo HRI
                      nHRIFontPosition  posição do codigo HRI
                      nBytesOfBuffer    quantidade de dados enviados
        */
        [DllImport("KIOSKDLL.dll")]
        public static extern int KIOSK_S_PrintBarcode(int port, int tipoPorta, string texto, int margemEsquerda, int tipoCodigoBarras, int largura, int altura, int tipoHRI, int posHRI, int qtdDados);

        /*
         * Function: KIOSK_CutPaper
           Desc:  Realiza o corte do papel
           Parametro: nPort:            identificação da porta de conexão
                      nPortType:        tipo da porta de comunicação
                      nMode:            modo do corte
                      nDistance:        distancia de avanço do papel antes do corte
         */
        [DllImport("KIOSKDLL.dll")]
        public static extern int KIOSK_CutPaper(int port, int tipoPorta, int modoCorte, int distanciaPapel);

        /*
        Function: KIOSK_FeedLines
        Desc: avança o pepel em unidade de linhas
        Parametro:  nPort:            identificação da porta de conexão
                    nPortType:        tipo da porta de comunicação
                    nLines:           Quantidade de linhas avançadas
         */
        [DllImport("KIOSKDLL.dll")]
        public static extern int KIOSK_FeedLines(int port, int tipoPorta, int qtdLinhas);

        /*
        Function: KIOSK_WriteData
        Desc:  Envia comandos diretamente para impressora, utilizado para enviar comandos ESCPOS
        Parametro:  nPort:            identificação da porta de conexão
                    nPortType:        tipo da porta de comunicação
                    pszData           dados a serem enviados
                    nBytesToWrite     quantidade de dados que serão enviados
        */
        [DllImport("KIOSKDLL.dll")]
        public static extern int KIOSK_WriteData(int port, int tipoPorta, byte[] dados, int qtdDados);

        /*
        Function: KIOSK_S_SetAlignMode
        Desc: Define a justificação da impressão
        Parametros: nMode = define a posição sendo:
                    0:direita
                    1:centro
                    2:esquerda
        */
        [DllImport("KIOSKDLL.dll")]
        public static extern int KIOSK_S_SetAlignMode(int port, int tipoPorta, int modo);
    }
}
