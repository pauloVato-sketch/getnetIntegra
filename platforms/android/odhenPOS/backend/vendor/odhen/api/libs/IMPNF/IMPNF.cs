using System;
using System.EnterpriseServices;
using System.IO;
using System.Net;
using System.Text;
using System.Text.RegularExpressions;
using System.Drawing;
using System.Runtime.InteropServices;
using System.Threading;

namespace IMPNF
{
    public interface IMPNFInterface
    {
        int IniciaPorta(int codigoImpressora, string porta);
        int ComandoTX(int codigoImpressora, string comando, int tamanhoComando);
        int FormataTX(int codigoImpressora, string texto, int tipoLetra, int italico, int sublinhado, int expandido, int enfatizado, int porta);
        int FechaPorta(int codigoImpressora, int porta);
        int ConfiguraCodigoBarras(int codigoImpressora, int altura, int largura, int posicao, int fonte, int margem);
        int ImprimeCodigoBarrasCODE128(int codigoImpressora, string texto, int porta);
        int ImprimeCodigoQRCODE(int codigoImpressora, int errorCorrectionLevel, int moduleSize, int codeType, int QRCodeVersion, int encodingModes, string codeQr, int porta);
        int Le_Status(int codigoImpressora);
        int AlimentaLinhasKiosk(int porta, int tipoPorta, int qtdLinhas);
        int CortaPapel(int codigoImpressora, int porta);
    }

    public class IMPNFClass : ServicedComponent, IMPNFInterface
    {

        private IntPtr printerInstance;
        static int threadReturn;

        public void initPort(int codigoImpressora, string porta)
        {
            switch (codigoImpressora)
            {
                case 1:
                case 3:
                case 4:
                case 7:
                case 13:
                case 21:
                    threadReturn = BEMANFDLL.IniciaPorta(porta);
                    break;
                case 2:
                case 9:
                case 11:
                case 14:
                case 16:
                case 19:
                    if (porta == "USB")
                    {
                        EPSONNFDLL.ConfiguraTaxaSerial(9600);
                    }
                    else
                    {
                        EPSONNFDLL.ConfiguraTaxaSerial(115200);
                    }
                    threadReturn = EPSONNFDLL.IniciaPorta(porta);
                    break;
                case 18:
                    // inicializa referencia da impressora
                    ELGINi9IntPtrInstance();

                    int port;
                    ELGINi9.PrtPrinterCreator(ref printerInstance, "TP806");
                    port = ELGINi9.PrtPortOpen(printerInstance, "USB");

                    threadReturn = port == 0 ? 1 : 0;
                    break;
                case 20:
                    threadReturn = ELGINKIOSK.KIOSK_OpenUsb();
                    break;
                case 23:
                    threadReturn = 1;
                    break;
                case 31:
                    int pcbNeeded = 0;
                    int pcnReturned = 0;
                    HSK33.Port_EnumUSB(null, 0, ref pcbNeeded, ref pcnReturned);
                    if (pcbNeeded > 0)
                    {
                        byte[] pBuf = new byte[pcbNeeded];
                        if (pBuf != null)
                        {
                            HSK33.Port_EnumUSB(pBuf, pcbNeeded, ref pcbNeeded, ref pcnReturned);
                            String s = Encoding.Default.GetString(pBuf);
                            String[] ss = s.Split(new char[] { '\0' }, StringSplitOptions.RemoveEmptyEntries);
                            bool printerReturn = HSK33.Port_OpenUsb(s);
                            threadReturn = printerReturn ? 1 : 0;
                        }
                    }
                    else
                    {
                        threadReturn = 0;
                    }
                    break;
                default:
                    threadReturn = -1;
                    break;
            }
        }

        public int IniciaPorta(int codigoImpressora, string porta)
        {
            Thread initPortThread = new Thread(() => initPort(codigoImpressora, porta));
            initPortThread.Start();

            bool finished = initPortThread.Join(TimeSpan.FromMilliseconds(3000));
            if (!finished)
            {
                initPortThread.Abort();
                return -2;
            }
            else
            {
                return threadReturn;
            }
        }

        public int ComandoTX(int codigoImpressora, string comando, int tamanhoComando)
        {
            switch (codigoImpressora)
            {
                case 1:
                case 3:
                case 4:
                case 7:
                case 13:
                case 21:
                    return BEMANFDLL.ComandoTX(comando, tamanhoComando);
                case 2:
                case 9:
                case 11:
                case 14:
                case 16:
                case 19:
                    return EPSONNFDLL.ComandoTX(comando, tamanhoComando);
                case 18:
                    return 1;
                case 20:
                    return 1;
                case 23:
                    return 1;
                default:
                    return 0;
            }
        }

        public int FormataTX(int codigoImpressora, string texto, int tipoLetra, int italico, int sublinhado, int expandido, int enfatizado, int porta = 0)
        {
            switch (codigoImpressora)
            {
                case 1:
                case 3:
                case 4:
                case 7:
                case 13:
                case 21:
                    return BEMANFDLL.FormataTX(texto, tipoLetra, italico, sublinhado, expandido, enfatizado);
                case 2:
                case 9:
                case 11:
                case 14:
                case 16:
                case 19:
                    return EPSONNFDLL.FormataTX(texto, tipoLetra, italico, sublinhado, expandido, enfatizado);
                case 18:
                    ELGINi9.PrtPrinterInitialize(printerInstance);
                    ELGINi9.PrtSetTextFont(printerInstance, 1);

                    byte[] data = Encoding.GetEncoding("GB2312").GetBytes(texto);

                    ELGINi9.PrtPrintText(printerInstance, data, 1, 0, 0);
                    return 1;
                case 20:
                    return ELGINKIOSK.KIOSK_S_Textout(porta, 2, texto, 0, 1, 1, 1, 0);
                case 23:
                    texto = "" + (char)29 + (char)232 + (char)0 + (char)60 + texto;
                    RawDataPrint.SendStringToPrinter("TG2480-H", texto);
                    return 1;
                case 31:
                    HSK33.POS_TextOut(texto, tipoLetra, italico, sublinhado, expandido, enfatizado, 1);
                    return 1;
                default:
                    return 0;
            }
        }

        public int FechaPorta(int codigoImpressora, int porta = 0)
        {
            switch (codigoImpressora)
            {
                case 1:
                case 3:
                case 4:
                case 7:
                case 13:
                case 21:
                    return BEMANFDLL.FechaPorta();
                case 2:
                case 9:
                case 11:
                case 14:
                case 16:
                case 19:
                    return EPSONNFDLL.FechaPorta();
                case 18:
                    int port = ELGINi9.PrtPortClose(printerInstance);
                    return port == 0 ? 1 : 0;
                case 20:
                    if (porta != 0)
                    {
                        return ELGINKIOSK.KIOSK_CloseUsb(porta);
                    }
                    else
                    {
                        return -1;
                    }
                case 23:
                    return 1;
                case 31:
                    HSK33.Port_Close();
                    return 1;
                default:
                    return 0;
            }
        }

        public int ConfiguraCodigoBarras(int codigoImpressora, int altura, int largura, int posicao, int fonte, int margem)
        {
            switch (codigoImpressora)
            {
                case 1:
                case 3:
                case 4:
                case 7:
                case 13:
                case 21:
                    return BEMANFDLL.ConfiguraCodigoBarras(altura, largura, posicao, fonte, margem);
                case 2:
                case 9:
                case 11:
                case 14:
                case 16:
                case 19:
                    return EPSONNFDLL.ConfiguraCodigoBarras(altura, largura, posicao, fonte, margem);
                case 18:
                    return 1;
                case 20:
                    return 1;
                case 23:
                    return 1;
                default:
                    return 0;
            }
        }

        public int ImprimeCodigoBarrasCODE128(int codigoImpressora, string texto, int porta = 0)
        {
            switch (codigoImpressora)
            {
                case 1:
                case 3:
                case 4:
                case 7:
                case 13:
                case 21:
                    return BEMANFDLL.ImprimeCodigoBarrasCODE128(texto);
                case 2:
                case 9:
                case 11:
                case 14:
                case 16:
                case 19:
                    return EPSONNFDLL.ImprimeCodigoBarrasCODE128(texto);
                case 18:
                    return 1;
                case 20:
                    ELGINKIOSK.KIOSK_S_SetAlignMode(porta, 2, 1);
                    String textConvertido = "{B" + texto;
                    ELGINKIOSK.KIOSK_S_PrintBarcode(porta, 2, "{B" + texto, 5, 73, 2, 50, 1, 0, textConvertido.Length);
                    ELGINKIOSK.KIOSK_S_SetAlignMode(porta, 2, 0);
                    return 1;
                case 23:
                    texto = "{A" + texto;

                    string stPrint128 = "" +
                                        (char)27 + (char)97 + (char)1 +
                                        (char)29 + (char)72 + (char)0 +
                                        (char)29 + (char)102 + (char)0 +
                                        (char)29 + (char)104 + (char)50 +
                                        (char)29 + (char)119 + (char)2 +
                                        (char)29 + (char)107 + (char)73 + (char)texto.Length + texto +
                                        (char)27 + (char)97 + (char)0;

                    RawDataPrint.SendStringToPrinter("TG2480-H", stPrint128);
                    return 1;
                default:
                    return 0;
            }
        }


        public int ImprimeCodigoQRCODE(int codigoImpressora, int errorCorrectionLevel, int moduleSize, int codeType, int QRCodeVersion, int encodingModes, string codeQr, int porta = 0)
        {
            unsafe
            {
                switch (codigoImpressora)
                {
                    case 1:
                    case 3:
                    case 4:
                    case 7:
                    case 13:
                    case 21:
                        return BEMANFDLL.ImprimeCodigoQRCODE(errorCorrectionLevel, moduleSize, codeType, QRCodeVersion, encodingModes, codeQr);
                    case 2:
                    case 9:
                    case 11:
                    case 14:
                    case 16:
                    case 19:
                        return EPSONNFDLL.ImprimeCodigoQRCODE(errorCorrectionLevel, moduleSize, codeType, QRCodeVersion, encodingModes, codeQr);
                    case 18:
                        int printResult;
                        ELGINi9.PrtPrinterInitialize(printerInstance);

                        printResult = ELGINi9.PrtPrintBarCode(printerInstance, 104, codeQr, 3, 60, 1, 3);

                        // On i9 printer, 0 means succes so I'm returning 1 if 0 and -1 if something wrong happens
                        return printResult == 0 ? 1 : -1;
                    case 20:
                        ELGINKIOSK.KIOSK_S_SetAlignMode(porta, 2, 1);
                        byte[] conf = new byte[] { 0X1D, 0X6F, 0X00, 4, 0X00, 0X02, 0X0A };
                        int confLength = conf.Length - 1;
                        ELGINKIOSK.KIOSK_WriteData(porta, 2, conf, confLength);

                        int tamQrCode = codeQr.Length + 8;
                        conf = new byte[tamQrCode];
                        conf[0] = 0X1D;
                        conf[1] = 0X6B;
                        conf[2] = 0X0B;
                        conf[3] = 0X4C;
                        conf[4] = 0X41;
                        conf[5] = 0X2C;

                        for (int i = 0; i < codeQr.Length; i++)
                        {
                            char letter = codeQr[i];
                            byte value = (byte)letter;
                            conf[i + 6] = value;
                        }

                        confLength = conf.Length - 1;
                        conf[confLength - 1] = 0X00;
                        conf[confLength] = 0X0A;
                        ELGINKIOSK.KIOSK_WriteData(porta, 2, conf, confLength);

                        ELGINKIOSK.KIOSK_S_SetAlignMode(porta, 2, 0);
                        return 1;
                    case 23:
                        //codeQr = "https://www.homologacao.nfce.fazenda.sp.gov.br/NFCeConsultaPublica/Paginas/ConsultaQRCode.aspx?chNFe=35171167945071000138652080000001132000002592&nVersao=100&tpAmb=2&dhEmi=323031372d31312d30335431363a32303a34332d30323a3030&vNF=4.20&vICMS=0.13&digVal=4165366233436a41336754635a5531466255566b674242384932733d&cIdToken=000001&cHashQRCode=06DF9618BD88AE5E8C839E05C6A927D220D6E833";

                        int inTam = codeQr.Length + 3;

                        string stPrintQR = "" +
                                           (char)27 + (char)97 + (char)1 +
                                           (char)29 + (char)40 + (char)107 + (char)3 + (char)0 + (char)49 + (char)65 + (char)0 + //Encoding Scheme
                                           (char)29 + (char)40 + (char)107 + (char)3 + (char)0 + (char)49 + (char)66 + (char)4 + //Size Module
                                           (char)29 + (char)40 + (char)107 + (char)3 + (char)0 + (char)49 + (char)67 + (char)0 + //Qrcode Version
                                           (char)29 + (char)40 + (char)107 + (char)3 + (char)0 + (char)49 + (char)69 + (char)0 + //Correction Level
                                           (char)29 + (char)40 + (char)107 + (char)(inTam % 256) + (char)(inTam / 256) + (char)49 + (char)80 + (char)49 + codeQr +
                                           (char)29 + (char)40 + (char)107 + (char)3 + (char)0 + (char)49 + (char)81 + (char)49 +
                                           (char)27 + (char)97 + (char)0;

                        RawDataPrint.SendStringToPrinter("TG2480-H", stPrintQR);
                        return 1;
                    case 31:
                        HSK33.POS_SetQRCode(codeQr, 0, 4, 0, 2);
                        return 1;
                    default:
                        return 0;
                }
            }
        }

        public int Le_Status(int codigoImpressora)
        {
            switch (codigoImpressora)
            {
                case 1:
                case 3:
                case 4:
                case 7:
                case 13:
                case 21:
                    return BEMANFDLL.Le_Status();
                case 2:
                case 9:
                case 11:
                case 14:
                case 16:
                case 19:
                    return EPSONNFDLL.Le_Status();
                case 18:
                    return 1;
                case 20:
                    return 1;
                case 23:
                    return 1;
                case 31:
                    bool test = HSK33.POS_SelfTest();
                    return test ? 1 : 0;
                default:
                    return 0;
            }
        }

        public int AlimentaLinhasKiosk(int porta, int tipoPorta, int qtdLinhas)
        {
            return ELGINKIOSK.KIOSK_FeedLines(porta, tipoPorta, qtdLinhas);
        }

        public int CortaPapel(int codigoImpressora, int porta = 0)
        {
            switch (codigoImpressora)
            {
                case 1:
                case 3:
                case 4:
                case 7:
                case 13:
                case 21:
                    return BEMANFDLL.AcionaGuilhotina(1);
                case 2:
                case 9:
                case 11:
                case 14:
                case 16:
                case 19:
                    return EPSONNFDLL.AcionaGuilhotina(1);
                case 18:
                    int result;
                    result = ELGINi9.PrtCutPaper(printerInstance, 1, 0);
                    return result == 0 ? 1 : 0;
                case 20:
                    return ELGINKIOSK.KIOSK_CutPaper(porta, 2, 1, 10);
                case 23:
                    string stCorta = "" + (char)27 + (char)105;

                    RawDataPrint.SendStringToPrinter("TG2480-H", stCorta);
                    return 1;
                case 31:
                    bool resultCut = HSK33.POS_FullCutPaper();
                    return resultCut ? 1 : 0;
                default:
                    return -1;
            }
        }

        private void ELGINi9IntPtrInstance()
        {
            printerInstance = new IntPtr();
            printerInstance = IntPtr.Zero;
        }
    }
}
