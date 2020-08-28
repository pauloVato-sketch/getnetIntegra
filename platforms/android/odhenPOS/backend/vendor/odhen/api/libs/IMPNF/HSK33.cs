using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Runtime.InteropServices;

namespace IMPNF
{
    public class HSK33
    {
        [DllImport("PrinterLibs.dll", CharSet = CharSet.Ansi, CallingConvention = CallingConvention.Cdecl)]
        public static extern bool Port_OpenCom(String pName, int dwBaudrate, int dwParity);

        [DllImport("PrinterLibs.dll", CharSet = CharSet.Ansi, CallingConvention = CallingConvention.Cdecl)]
        public static extern bool Port_OpenTcp(String szIp, UInt16 nPort);

        [DllImport("PrinterLibs.dll", CharSet = CharSet.Ansi, CallingConvention = CallingConvention.Cdecl)]
        public static extern bool Port_OpenUsb(String pName);

        [DllImport("PrinterLibs.dll", CharSet = CharSet.Ansi, CallingConvention = CallingConvention.Cdecl)]
        public static extern bool Port_OpenLpt(String pName);

        [DllImport("PrinterLibs.dll", CharSet = CharSet.Ansi, CallingConvention = CallingConvention.Cdecl)]
        public static extern int Port_Write(byte[] buffer, uint count, uint timeout);

        [DllImport("PrinterLibs.dll", CharSet = CharSet.Ansi, CallingConvention = CallingConvention.Cdecl)]
        public static extern int Port_Read(byte[] buffer, uint count, uint timeout);

        [DllImport("PrinterLibs.dll", CharSet = CharSet.Ansi, CallingConvention = CallingConvention.Cdecl)]
        public static extern void Port_Close();

        [DllImport("PrinterLibs.dll", CharSet = CharSet.Ansi, CallingConvention = CallingConvention.Cdecl)]
        public static extern void Port_EnumCom(byte[] pBuf, int cbBuf, ref int pcbNeeded, ref int pcnReturned);

        [DllImport("PrinterLibs.dll", CharSet = CharSet.Ansi, CallingConvention = CallingConvention.Cdecl)]
        public static extern void Port_EnumLpt(byte[] pBuf, int cbBuf, ref int pcbNeeded, ref int pcnReturned);

        [DllImport("PrinterLibs.dll", CharSet = CharSet.Ansi, CallingConvention = CallingConvention.Cdecl)]
        public static extern void Port_EnumUSB(byte[] pBuf, int cbBuf, ref int pcbNeeded, ref int pcnReturned);


        // 打印
        [DllImport("PrinterLibs.dll", CharSet = CharSet.Unicode, CallingConvention = CallingConvention.Cdecl)]
        public static extern bool POS_TextOut(String pszString, int x, int nWidthScale, int nHeightScale, int nFontType, int nFontStyle, int nEncoding); // 打印文本

        [DllImport("PrinterLibs.dll", CharSet = CharSet.Ansi, CallingConvention = CallingConvention.Cdecl)]
        public static extern bool POS_SetBarcode(String pszBarcodeContent, int x, int nBarcodeUnitWidth, int nBarcodeHeight, int nHriFontType, int nHriFontPosition, int nBarcodeType); // 打印条码

        [DllImport("PrinterLibs.dll", CharSet = CharSet.Ansi, CallingConvention = CallingConvention.Cdecl)]
        public static extern bool POS_SetQRCode(String pszContent, int x, int nQRCodeUnitWidth, int nVersion, int nEcLevel);	//打印QR码

        [DllImport("PrinterLibs.dll", CharSet = CharSet.Ansi, CallingConvention = CallingConvention.Cdecl)]
        public static extern bool POS_PrintPicture(String szFileName, int x, int dstw, int dsth, int nBinaryAlgorithm, int nCompressMethod); // 打印bmp图片

        [DllImport("PrinterLibs.dll", CharSet = CharSet.Ansi, CallingConvention = CallingConvention.Cdecl)]
        public static extern bool POS_SelfTest();


        // 进纸
        [DllImport("PrinterLibs.dll", CharSet = CharSet.Ansi, CallingConvention = CallingConvention.Cdecl)]
        public static extern bool POS_FeedLine();

        [DllImport("PrinterLibs.dll", CharSet = CharSet.Ansi, CallingConvention = CallingConvention.Cdecl)]
        public static extern bool POS_FeedNLine(int nLine);

        [DllImport("PrinterLibs.dll", CharSet = CharSet.Ansi, CallingConvention = CallingConvention.Cdecl)]
        public static extern bool POS_FeedNDot(int nDot);

        // 查询
        [DllImport("PrinterLibs.dll", CharSet = CharSet.Ansi, CallingConvention = CallingConvention.Cdecl)]
        public static extern bool POS_QueryStatus(int type, ref byte status, uint timeout); // 查询打印机状态

        [DllImport("PrinterLibs.dll", CharSet = CharSet.Ansi, CallingConvention = CallingConvention.Cdecl)]
        public static extern bool POS_RTQueryStatus(int type, ref byte status, uint timeout);	// 实时查询打印机状态

        [DllImport("PrinterLibs.dll", CharSet = CharSet.Ansi, CallingConvention = CallingConvention.Cdecl)]
        public static extern bool POS_TicketSucceed(int dwSendIndex, uint timeout); // 单据打印结果查询

        // 设置
        [DllImport("PrinterLibs.dll", CharSet = CharSet.Ansi, CallingConvention = CallingConvention.Cdecl)]
        public static extern bool POS_SetMotionUnit(int nHorizontal, int nVertical);

        [DllImport("PrinterLibs.dll", CharSet = CharSet.Ansi, CallingConvention = CallingConvention.Cdecl)]
        public static extern bool POS_SetLineHeight(int nDistance);

        [DllImport("PrinterLibs.dll", CharSet = CharSet.Ansi, CallingConvention = CallingConvention.Cdecl)]
        public static extern bool POS_SetRightSpacing(int nDistance);



        // 其他
        [DllImport("PrinterLibs.dll", CharSet = CharSet.Ansi, CallingConvention = CallingConvention.Cdecl)]
        public static extern bool POS_Reset();

        [DllImport("PrinterLibs.dll", CharSet = CharSet.Ansi, CallingConvention = CallingConvention.Cdecl)]
        public static extern bool POS_KickOutDrawer(int nID, int nHighLevelTime, int nLowLevelTime); // 开钱箱

        [DllImport("PrinterLibs.dll", CharSet = CharSet.Ansi, CallingConvention = CallingConvention.Cdecl)]
        public static extern bool POS_FeedAndCutPaper(); // 进纸到切刀位置并切纸

        [DllImport("PrinterLibs.dll", CharSet = CharSet.Ansi, CallingConvention = CallingConvention.Cdecl)]
        public static extern bool POS_FullCutPaper(); // 直接全切

        [DllImport("PrinterLibs.dll", CharSet = CharSet.Ansi, CallingConvention = CallingConvention.Cdecl)]
        public static extern bool POS_HalfCutPaper(); // 直接半切

        [DllImport("PrinterLibs.dll", CharSet = CharSet.Ansi, CallingConvention = CallingConvention.Cdecl)]
        public static extern bool POS_Beep(int nBeepCount, int nBeepMillis);
    }
}
