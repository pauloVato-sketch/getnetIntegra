using System;
using System.EnterpriseServices;
using System.IO;
using System.Net;
using System.Text;
using System.Text.RegularExpressions;
using System.Runtime.InteropServices;

namespace SAT
{
    public interface SATInterface
    {
        string SendSaleData(int satType, int sessionNumber, string activationCode, string saleData);
        string CancelLastSale(int satType, int sessionNumber, string activationCode, string key, string cancellationData);
        string ConsultSAT(int satType, int sessionNumber);
        string ChangeActivationCode(int satType, int sessionNumber, string activationCode, int option, string newCode, string confNewCode);
        string ExtractLogs(int satType, int sessionNumber, string activationCode);
    }

    /*
        0 = Dimep D-SAT
        1 = Bematech RB-1000/RB-2000
        2 = Sweda SS-1000
        3 = Elgin MFE
        4 = Elgin Linker I/II
        5 = Tanca TS-1000
    */
    enum SatType: int {
        DIMEP = 0,
        BEMATECH = 1,
        SWEDA = 2,
        ELGIN_MFE = 3,
        ELGIN_LINKER = 4,
        TANCA = 5
    }

    public class SATClass : ServicedComponent, SATInterface
    {
        public string SendSaleData(int satType, int sessionNumber, string activationCode, string saleData)
        {
            switch((SatType)satType)
            {
                case SatType.DIMEP:
                    return FormatResultToString(DIMEP.EnviarDadosVenda(sessionNumber, activationCode, saleData));
                case SatType.BEMATECH:
                    return FormatResultToString(BEMASAT.EnviarDadosVenda(sessionNumber, activationCode, saleData));
                case SatType.SWEDA:
                    return FormatResultToString(SWEDA.EnviarDadosVenda(sessionNumber, activationCode, saleData));
                case SatType.ELGIN_MFE:
                    return FormatResultToString(ELGIN_MFE.EnviarDadosVenda(sessionNumber, activationCode, saleData));
                case SatType.ELGIN_LINKER:
                    return FormatResultToString(ELGIN.EnviarDadosVenda(sessionNumber, activationCode, saleData));
                case SatType.TANCA:
                    return FormatResultToString(TANCA.EnviarDadosVenda(sessionNumber, activationCode, saleData));
                default:
                    return "-1";
            }
        }

        public string CancelLastSale(int satType, int sessionNumber, string activationCode, string key, string cancellationData)
        {
            switch((SatType)satType)
            {
                case SatType.DIMEP:
                    return FormatResultToString(DIMEP.CancelarUltimaVenda(sessionNumber, activationCode, key, cancellationData));
                case SatType.BEMATECH:
                    return FormatResultToString(BEMASAT.CancelarUltimaVenda(sessionNumber, activationCode, key, cancellationData));
                case SatType.SWEDA:
                    return FormatResultToString(SWEDA.CancelarUltimaVenda(sessionNumber, activationCode, key, cancellationData));
                case SatType.ELGIN_MFE:
                    return FormatResultToString(ELGIN_MFE.CancelarUltimaVenda(sessionNumber, activationCode, key, cancellationData));
                case SatType.ELGIN_LINKER:
                    return FormatResultToString(ELGIN.CancelarUltimaVenda(sessionNumber, activationCode, key, cancellationData));
                case SatType.TANCA:
                    return FormatResultToString(TANCA.CancelarUltimaVenda(sessionNumber, activationCode, key, cancellationData));
                default:
                    return "-1";
            }
        }

        public string ConsultOperationalStatus(int satType, int sessionNumber, string activationCode)
        {
            switch ((SatType)satType)
            {
                case SatType.DIMEP:
                    return FormatResultToString(DIMEP.ConsultarStatusOperacional(sessionNumber, activationCode));
                case SatType.BEMATECH:
                    return FormatResultToString(BEMASAT.ConsultarStatusOperacional(sessionNumber, activationCode));
                case SatType.SWEDA:
                    return FormatResultToString(SWEDA.ConsultarStatusOperacional(sessionNumber, activationCode));
                case SatType.ELGIN_MFE:
                    return FormatResultToString(ELGIN_MFE.ConsultarStatusOperacional(sessionNumber, activationCode));
                case SatType.ELGIN_LINKER:
                    return FormatResultToString(ELGIN.ConsultarStatusOperacional(sessionNumber, activationCode));
                case SatType.TANCA:
                    return FormatResultToString(TANCA.ConsultarStatusOperacional(sessionNumber, activationCode));
                default:
                    return "-1";
            }
        }

        public string ConsultSAT(int satType, int sessionNumber)
        {
            switch ((SatType)satType)
            {
                case SatType.DIMEP:
                   return FormatResultToString(DIMEP.ConsultarSAT(sessionNumber));
                case SatType.BEMATECH:
                   return FormatResultToString(BEMASAT.ConsultarSAT(sessionNumber));
                case SatType.SWEDA:
                   return FormatResultToString(SWEDA.ConsultarSAT(sessionNumber));
                case SatType.ELGIN_MFE:
                    return FormatResultToString(ELGIN_MFE.ConsultarSAT(sessionNumber));
                case SatType.ELGIN_LINKER:
                    return FormatResultToString(ELGIN.ConsultarSAT(sessionNumber));
                case SatType.TANCA:
                    return FormatResultToString(TANCA.ConsultarSAT(sessionNumber));
                default:
                    return "-1";
            }
        }

        public string ChangeActivationCode(int satType, int sessionNumber, string activationCode, int option, string newCode, string confNewCode)
        {
            switch ((SatType)satType)
            {
                case SatType.DIMEP:
                    return FormatResultToString(DIMEP.TrocarCodigoDeAtivacao(sessionNumber, activationCode, option, newCode, confNewCode));
                case SatType.BEMATECH:
                    return FormatResultToString(BEMASAT.TrocarCodigoDeAtivacao(sessionNumber, activationCode, option, newCode, confNewCode));
                case SatType.SWEDA:
                    return FormatResultToString(SWEDA.TrocarCodigoDeAtivacao(sessionNumber, activationCode, option, newCode, confNewCode));
                case SatType.ELGIN_MFE:
                    return FormatResultToString(ELGIN_MFE.TrocarCodigoDeAtivacao(sessionNumber, activationCode, option, newCode, confNewCode));
                case SatType.ELGIN_LINKER:
                    return FormatResultToString(ELGIN.TrocarCodigoDeAtivacao(sessionNumber, activationCode, option, newCode, confNewCode));
                case SatType.TANCA:
                    return FormatResultToString(TANCA.TrocarCodigoDeAtivacao(sessionNumber, activationCode, option, newCode, confNewCode));
                default:
                    return "-1";
            }
        }

        public string ExtractLogs(int satType, int sessionNumber, string activationCode)
        {
            switch ((SatType)satType)
            {
                case SatType.DIMEP:
                    return FormatResultToString(DIMEP.ExtrairLogs(sessionNumber, activationCode));
                case SatType.BEMATECH:
                    return FormatResultToString(BEMASAT.ExtrairLogs(sessionNumber, activationCode));
                case SatType.SWEDA:
                    return FormatResultToString(SWEDA.ExtrairLogs(sessionNumber, activationCode));
                case SatType.ELGIN_MFE:
                    return FormatResultToString(ELGIN_MFE.ExtrairLogs(sessionNumber, activationCode));
                case SatType.ELGIN_LINKER:
                    return FormatResultToString(ELGIN.ExtrairLogs(sessionNumber, activationCode));
                case SatType.TANCA:
                    return FormatResultToString(TANCA.ExtrairLogs(sessionNumber, activationCode));
                default:
                    return "-1";
            }
        }

        private string FormatResultToString(IntPtr response){
            Encoding unicodeClass = Encoding.Unicode;
            Encoding utf8Class = Encoding.UTF8;

            string unicodeStringResult = Marshal.PtrToStringAnsi(response);
            byte[] unicodeByteResult = unicodeClass.GetBytes(unicodeStringResult);
            byte[] utf8ByteResult = Encoding.Convert(unicodeClass, utf8Class, unicodeByteResult);

            return utf8Class.GetString(utf8ByteResult);
        }
    }
}