using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Runtime.InteropServices;

namespace IMPNF
{
    class ELGINi9
    {
        [DllImport("ESC_SDK.dll", CharSet = CharSet.Ansi)]
        public static extern int PrtPrinterCreator(ref IntPtr printer, string model);

        [DllImport("ESC_SDK.dll")]
        public static extern int PrtPrinterDestroy(IntPtr handle);

        [DllImport("ESC_SDK.dll", CharSet = CharSet.Ansi)]
        public static extern int PrtPortOpen(IntPtr printer, string portSetting);

        [DllImport("ESC_SDK.dll")]
        public static extern int PrtPortClose(IntPtr handle);

        [DllImport("ESC_SDK.dll")]
        public static extern int PrtSetTextFont(IntPtr handle, int font);

        [DllImport("ESC_SDK.dll", CharSet = CharSet.Ansi)]
        public static extern int PrtPrintText(IntPtr printer, byte[] text, int alignment, int attribute, int textSize);

        [DllImport("ESC_SDK.dll")]
        public static extern int PrtPrinterInitialize(IntPtr handle);

        [DllImport("ESC_SDK.dll")]
        public static extern int PrtPrintTwoQRCode(IntPtr printer, string data1, int width1, int hAlign1, int vAlign1, string data2, int width2, int hAlign2, int vAlign2);

        [DllImport("ESC_SDK.dll")]
        public static extern int PrtPrintBarCode(IntPtr printer, int bcType, string bcData, int width, int height, int alignment, int hriPosition);

        [DllImport("ESC_SDK.dll")]
        public static extern int PrtSetAlign(IntPtr printer, int align);

        [DllImport("ESC_SDK.dll")]
        public static extern int PrtSelectPrintDirectionInPageMode(IntPtr printer, int direction);

        [DllImport("ESC_SDK.dll")]
        public static extern int PrtSetAbsolutePrintPosition(IntPtr printer, int position);

        [DllImport("ESC_SDK.dll")]
        public static extern int PrtSetAbsoluteVerticalPrintPositionInPageMode(IntPtr printer, int position);

        [DllImport("ESC_SDK.dll")]
        public static extern int PrtCutPaper(IntPtr printer, int cutMode, int distance);

        [DllImport("ESC_SDK.dll")]
        public static extern int PrtPrintSymbol(IntPtr printer, int type, string bcData, int errLevel, int width, int height, int alignment);
    }
}