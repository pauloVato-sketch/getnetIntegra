<?php
namespace Util;
use \DateTime;

/**
 * Created by JetBrains PhpStorm.
 * User: paulopereira
 * Date: 26/11/12
 * Time: 13:14
 *
 * @update 31/01/2013
 *     A Classe DateUtil, anteriormente baseada na classe nativa do PHP \DateTime foi alterada para se adequar às
 * classes do Doctrine2. Então, esta passa utilizar a classe \Setforms2\Shared\ORM\DateTime, que oferece as mesmas
 * funcionalidades do \DateTime, alem de corrigir alguns detalhes necessários ao Doctrine.
 */
class Date extends \Zeedhi\Framework\Controller\Simple {

    /**
     * Constante para as funcoes de calculos sobre datas. Define qu
     * e o intervalo usado sera dias.
     */
    const DIA      = "D";
    /**
     * Constante para as funcoes de calculos sobre datas. Define que o intervalo usado sera meses.
     */
    const MES      = "M";
    /**
     * Constante para as funcoes de calculos sobre datas. Define que o intervalo usado sera anos.
     */
    const ANO      = "Y";
    /**
     * Constante para as funcoes de calculos sobre datas. Define que o intervalo usado sera semanas.
     */
    const SEMANA   = "W";
    /**
     * Constante para as funcoes de calculos sobre datas. Define que o intervalo usado sera horas.
     */
    const HORAS    = "H";
    /**
     * Constante para as funcoes de calculos sobre datas. Define que o intervalo usado sera minutos.
     */
    const MINUTOS  = "m";
    /**
     * Constante para as funcoes de calculos sobre datas. Define que o intervalo usado sera segundos.
     */
    const SEGUNDOS = "S";
    /**
     * Constante para as funcoes de calculos sobre datas. Define o formato de retorno do numero do dia na semana.
     * ex.: Segunda-feira = 1; Sabado = 7.
     */
    const DIA_DA_SEMANA = "N";
    /**
     * Constante para as funcoes de calculos sobre datas. Define o formato de retorno do dia do mes, com dois digitos.
     * ex: 01; 31
     */
    const DIA_DO_MES    = "d";

    /**
     * Constante usadas para comparacao de datas. Define a compracao onde a data incial e anteiror a data final.
     */
    const ANTES    = 1;
    /**
     * Constante usadas para comparacao de datas. Define a compracao onde a data incial e posterior a data final.
     */
    const DEPOIS   = 2;
    /**
     * Constante usadas para comparacao de datas. Define a compracao onde a data incial e igual a data final.
     */
    const IGUAL    = 3;

    /**
     * Formato brasileiro de datas (dd/mm/yyyy).
     */
    const FORMATO_BRASILEIRO = "d/m/Y";
    /**
     * Formato Americano de datas (yyyy/mm/dd).
     */
    const FORMATO_AMERICANO  = "Y/m/d";
    /**
     * Formato Americano de datas (mm/dd/yyyy).
     */
    const FORMATO_AMERICANO_2  = "m/d/Y";
    /**
     * Formato brasileiro de datas (dd/mm/yyyy).
     */
    const FORMATO_BRASILEIRO_DATAHORA = "d/m/Y H:i:s";
    /**
     * Formato Americano de datas (yyyy/mm/dd).
     */
    const FORMATO_AMERICANO_DATAHORA  = "Y/m/d h:i:s";

    /**
     * Formato de data adotado no sistema.
     */
    const FORMATO_DATA = self::FORMATO_BRASILEIRO;
    /**
     * Formato de data e hora adotado no sistema.
     */
    const FORMATO_DATAHORA = self::FORMATO_BRASILEIRO_DATAHORA;

    /**
     * Constantes utilizadas pelos operadores de calculos de horas. Definem o formato de entrada das horas.
     */
    const HORA_MINUTO_SEGUNDO = 1;
    const HORA_MINUTO = 2;

    /**
     * Padrao de mascara utilizado pela string de data e hora.
     */
    const DATE_TIME_PATTERN = "/^\d\d\/\d\d\/\d\d\d\d \d\d\:\d\d\:\d\d$/";
    /**
     * Padrao de mascara utilizado pela string de data.
     */
    const DATE_PATTERN      = "/^\d\d\/\d\d\/\d\d\d\d$/";


    /**
     * Adiciona a data passada por parametro um intervalo de tempo. A quantidade do intervalo e o tipo de intervalo
     * (dia, mes, ano...) podem ser definidos por parametro.
     *
     * @param DateTime $data
     * @param $intervalo
     * @param string $tipo
     *
     * @return DateTime
     */
    public static function adicionaIntervalo(DateTime $data, $intervalo = 1, $tipo = self::DIA)
    {
        $dataReturn = clone $data;
        return $dataReturn->add(new \DateInterval("P$intervalo$tipo"));
    }

    /**
     * Remove a data passada por parametro um intervalo de tempo. A quantidade do intervalo e o tipo de intervalo
     * (dia, mes, ano...) podem ser definidos por parametro.
     *
     * @param DateTime $data
     * @param $intervalo
     * @param string $tipo
     *
     * @return DateTime
     */
    public static function subtraiIntervalo(DateTime $data, $intervalo, $tipo = self::DIA)
    {
        $dataReturn = clone $data;
        return $dataReturn->sub(new \DateInterval("P$intervalo$tipo"));
    }

    /**
     * Compara duas datas, dependendo do parametro tipo de comparacao passado.
     *
     * Este metodo pode comparar duas classes, verificanado se a data do primeiro parametro passado e anterior ao da
     * segunda (neste caso, utilizando a constante ANTES), se a primeira e posterior a segunda(constante DEPOIS), ou
     * se as duas sao iguais(IGUAL).
     *
     * @static
     * @param DateTime $data1
     * @param DateTime $data2
     * @param int $tipoComparacao
     * @return bool
     */
    public static function comparaDatas(DateTime $data1, DateTime $data2, $tipoComparacao = self::ANTES)
    {
        if ($tipoComparacao === self::ANTES) {
            return self::getIntervaloEmSegundos($data1, $data2) > 0;
        } else if ($tipoComparacao == self::DEPOIS) {
            return self::getIntervaloEmSegundos($data1, $data2) < 0;
        } else {
            return self::getIntervaloEmSegundos($data1, $data2) === 0;
        }
    }

    /**
     * Retorna o primeiro dia do mes.
     *
     * @static
     * @param DateTime $data
     * @return \Setforms2\Shared\ORM\DateTime
     */
    public static function getPrimeiroDiaDoMes(DateTime $data)
    {
        try {
            $mesEntrada = $data->format("m");
            $anoEntrada = $data->format("Y");
            // Captura o primeiro dia do mes.
            $dataReturn = date(self::FORMATO_DATA, mktime(0, 0, 0, $mesEntrada, 1, $anoEntrada));

            return self::getDataDeString($dataReturn);
        } catch (\Exception $e) {
            Exception::logException($e);
            throw $e;
        }
    }

    /**
     * Retorna o ultimo dia do mes.
     *
     * @static
     * @param DateTime $data
     * @return DateTime
     */
    public static function getUltimoDiaDoMes(DateTime $data)
    {
        try {
            $mesEntrada = $data->format("m");
            $anoEntrada = $data->format("Y");
            // Captura o ultimo dia do mes.
            $dataReturn = date("t/m/Y", mktime(0, 0, 0, $mesEntrada, 1, $anoEntrada));

            return self::getDataDeString($dataReturn);
        } catch (\Exception $e) {
            Exception::logException($e);
            throw $e;
        }
    }

    /**
     * Retorna um objeto do tipo DateTime de uma string passada por parametro. Por padrao, o formato de
     * data utilizado é o formato brasileiro (dd/mm/yyyy).
     *
     * @static
     * @param $data
     * @param string $formato
     * @return \DateTime
     */
    public static function getDataDeString($data, $formato = self::FORMATO_BRASILEIRO, $truncate = false)
    {
        try {
            $data = DateTime::createFromFormat($formato, $data);
            if ($truncate) {
                $data = self::truncateData($data);
            }

            return $data;
        } catch (\Exception $e) {
            Exception::logException($e);
            throw $e;
        }
    }

    /**
     * Retorna as datas entre os dias da data inicial e da data final(O conjunto inclui data inicial e data final).
     *
     * @param DateTime $dataInicial
     * @param DateTime $dataFinal
     * @return array
     */
    public static function getDatasEntre(DateTime $dataInicial, DateTime $dataFinal)
    {
        $intervaloDatas = $dataInicial->diff($dataFinal)->days;
        $datas          = array();

        for ($i = 0; $i <= $intervaloDatas; $i++) {
            $auxData = clone $dataInicial;
            $datas[] = $auxData->add(new \DateInterval("P".$i.self::DIA));
        }

        return $datas;
    }

    /**
     * Data atual do sistema.
     *
     * @return DateTime
     */
    public static function getDataAtual($truncate = false) {
        $now = new DateTime();

        if ($truncate) {
            return self::truncateData($now);
        } else {
            return $now;
        }
    }

    /**
     * @static
     * @param $data1
     * @param $data2
     * @return mixed
     */
    public static function retornaMaiorData($data1, $data2){
        // se a data1 for maior que a data 2, retorna a data1, senao, retorna a data2
        if (self::getIntervaloEmSegundos($data1, $data2) < 0) {
            return $data1;
        } else {
            return $data2;
        }
    }

    /**
     * @static
     * @param $data1
     * @param $data2
     * @return mixed
     */
    public static function retornaMenorData($data1, $data2){
        // se a data1 for menor que a data 2, retorna a data1, senao, retorna a data2
        if (self::getIntervaloEmSegundos($data1, $data2) > 0) {
            return $data1;
        } else {
            return $data2;
        }
    }

    /**
     * Retorna o dia de uma data passada por parametro, sendo esta tipo string ou DateTime.
     *
     * @param String|DateTime $data
     * @param string           $formato Formato da data de entrada. Util somente se o tipo do atributo data for String.
     * @throws \Exception
     * @return string
     */
    public static function getDia($data, $formato = self::FORMATO_BRASILEIRO)
    {
        if (($data instanceof DateTime) === false) {
            try {
                $data = self::getDataDeString($data, $formato);
            } catch (\Exception $e) {
                Exception::logException($e);
                throw $e;
            }
        }

        return $data->format("d");
    }

    /**
     * Retorna o mes de uma data passada por parametro, sendo esta tipo string ou DateTime.
     *
     * @param String|DateTime $data
     * @param string           $formato Formato da data de entrada. Util somente se o tipo do atributo data for String.
     * @throws \Exception
     * @return string
     */
    public static function getMes($data, $formato = self::FORMATO_BRASILEIRO)
    {
        if (($data instanceof DateTime) === false) {
            try {
                $data = self::getDataDeString($data, $formato);
            } catch (\Exception $e) {
                Exception::logException($e);
                throw $e;
            }
        }

        return $data->format("m");
    }

    /**
     * Retorna o ano de uma data passada por parametro, sendo esta tipo string ou DateTime.
     *
     * @param String|DateTime $data
     * @param string           $formato Formato da data de entrada. Util somente se o tipo do atributo data for String.
     * @throws \Exception
     * @return string
     */
    public static function getAno($data, $formato = self::FORMATO_BRASILEIRO)
    {
        if (($data instanceof DateTime) === false) {
            try {
                $data = self::getDataDeString($data, $formato);
            } catch (\Exception $e) {
                Exception::logException($e);
                throw $e;
            }
        }

        return $data->format("Y");
    }


    /**
     * Retorna a quantidade de dias entre um intervalo de datas. Caso a data final seja maior que a data inicial, o
     * valor retornado sera negativo, contendo a quantidade de dias entre as duas datas.
     *
     * @static
     * @param $dataInicial
     * @param $dataFinal
     * @return int
     */
    public static function getQtdeDiasEntre($dataInicial, $dataFinal)
    {
        if (($dataInicial instanceof DateTime) === false) {
            $dataInicial = self::getDataDeString($dataInicial);
        }

        if (($dataFinal instanceof DateTime) === false) {
            $dataFinal = self::getDataDeString($dataFinal);
        }

        /**
         * @var $dataInicial DateTime
         * @var $dataFinal DateTime
         * @var $intervalo \DateInterval
         */
        $intervalo = $dataInicial->diff($dataFinal);
        return (int)$intervalo->format('%R%a');
    }

    /**
     * Retorna a diferenca entre duas datas em segundos.
     *
     * @static
     * @param $dataInicial
     * @param $dataFinal
     * @return int
     */
    public static function getIntervaloEmSegundos($dataInicial, $dataFinal)
    {
        if (($dataInicial instanceof DateTime) === false) {
            $dataInicial = self::getDataDeString($dataInicial);
        }

        if (($dataFinal instanceof DateTime) === false) {
            $dataFinal = self::getDataDeString($dataFinal);
        }

        /**
         * @var $dataInicial DateTime
         * @var $dataFinal DateTime
         * @var $intervalo \DateInterval
         */
        $intervalo = $dataInicial->diff($dataFinal);

        $intervaloDias     = (int)$intervalo->format('%R%a');
        $intervaloHoras    = (int)$intervalo->format('%R%h');
        $intervaloMinutos  = (int)$intervalo->format('%R%i');
        $intervaloSegundos = (int)$intervalo->format('%R%s');

        $intervaloHoras    = $intervaloHoras    + $intervaloDias    * 24;
        $intervaloMinutos  = $intervaloMinutos  + $intervaloHoras   * 60;
        return $intervaloSegundos + $intervaloMinutos * 60;
    }

    /**
     * Retorna a mesma data passada por parametro truncada (sem data, hora e minuto).
     *
     * @static
     * @param DateTime $data
     * @return DateTime
     */
    public static function truncateData(DateTime $data)
    {
        $strData = $data->format(self::FORMATO_BRASILEIRO);
        $strData .= " 00:00:00";
        return DateTime::createFromFormat(self::FORMATO_BRASILEIRO_DATAHORA, $strData);
    }

    /**
     * Recebe como parametro uma data no tipo String e retorna esta data, tambem no tipo String, no formato d/m/Y
     *
     * @static
     * @param $data
     * @param string $formatoEntrada Formato da Data passada no parametro.
     * @param string $formatoRetorno Formato esperado para o retorno da funcao.
     * @return string
     */
    public static function truncateString($data,
                                          $formatoEntrada = self::FORMATO_BRASILEIRO_DATAHORA,
                                          $formatoRetorno = self::FORMATO_BRASILEIRO)
    {
        $data = self::getDataDeString($data, $formatoEntrada);
        return $data->format($formatoRetorno);
    }

    /**
     * Verifica se a String de data passada no primeiro parametro pertence ao formato passado no segundo parametro.
     * Por padrao, o formato de data verificado é o FORMATO_BRASILEIRO (d/m/Y).
     *
     * @static
     * @param $data
     * @param $formato
     * @return bool
     */
    public static function dataPertenceAoFormato($data, $formato)
    {
        return (bool) \DateTime::createFromFormat($formato, $data);
    }


    public static function somarHoras($hora1, $hora2, $formatoHora = self::HORA_MINUTO)
    {
        $separador = "";
        if (preg_match("/\:/", $hora1)) {
            $separador = ":";
        }

        $hora1 = str_replace(":", "", $hora1);
        $hora2 = str_replace(":", "", $hora2);

        $hora1 = self::retornaEmSegundos($hora1, $formatoHora);
        $hora2 = self::retornaEmSegundos($hora2, $formatoHora);

        $result = $hora1 + $hora2;

        $minutos   = $result % 3600;
        $minutos   = floor($minutos / 60);
        $horas     = floor($result / 3600);
        $segundos = $result - ($horas*3600 + $minutos*60);

        $segundos = str_pad($segundos, 2, "0", STR_PAD_LEFT);
        $minutos  = str_pad($minutos, 2, "0", STR_PAD_LEFT);
        $horas    = str_pad($horas, 2, "0", STR_PAD_LEFT);

        return ($formatoHora === self::HORA_MINUTO) ?  $horas.$separador.$minutos :
                $horas.$separador.$minutos.$separador.$segundos;
    }

    /**
     * Soma os valores de duas horas, passadas pelos parametros $hora1 e $hora2.
     *
     * @static
     * @param $hora1
     * @param $hora2
     * @param int $formatoHora
     * @return string
     */
    public static function subtrairHoras($hora1, $hora2, $formatoHora = self::HORA_MINUTO)
    {
        $separador = "";
        if (preg_match("/\:/", $hora1)) {
            $separador = ":";
        }

        $hora1 = str_replace(":", "", $hora1);
        $hora2 = str_replace(":", "", $hora2);

        $hora1 = self::retornaEmSegundos($hora1, $formatoHora);
        $hora2 = self::retornaEmSegundos($hora2, $formatoHora);

        if (($hora1 > 24*60*60  || $hora2 > 24*60*60) && $hora1 < $hora2) {
            throw new \Exception("The first parameter must be greater than the second one.");
        }

        $result = $hora1 - $hora2;


        $minutos   = $result % 3600;
        $minutos   = floor($minutos / 60);
        $horas     = floor($result / 3600);
        $segundos = $result - ($horas*3600 + $minutos*60);

        $segundos = str_pad((60 + $segundos) % 60, 2, "0", STR_PAD_LEFT);
        $minutos  = str_pad((60 + $minutos) % 60, 2, "0", STR_PAD_LEFT);

        if ($horas < 0) {
            $horas    = (24 + $horas) % 24;
        }

        $horas    = str_pad($horas, 2, "0", STR_PAD_LEFT);

        return ($formatoHora === self::HORA_MINUTO) ?  $horas.$separador.$minutos :
            $horas.$separador.$minutos.$separador.$segundos;
    }

    /**
     * Subtrai os valores de duas horas, passadas pelos parametros $hora1 e $hora2.
     *
     * @param $hora
     * @param $formatoHora
     * @return int|string
     */
    private static function retornaEmSegundos($hora, $formatoHora)
    {
        $horas = $minutos = $segundos = 0;
        if ($formatoHora === self::HORA_MINUTO_SEGUNDO) {
            $segundos = substr($hora, -2);
            $minutos = substr($hora, -4, 2);
            $horas = substr($hora, 0, -4);

        } else {
            $minutos = substr($hora, -2);
            $horas = substr($hora, 0, -2);
        }

        return $horas * 3600 + $minutos * 60 + $segundos;
    }

    /**
     * Retorna uma determinada hora passada como parâmetro (no formato 'HH:MI' ou 'HHMI') em formato float.
     * Ex.: getHoraDecimalDeString('08:30') -> 8.5
     *
     * @static
     * @param string $hora
     * @return float
     */
    public static function getHoraFloatDeString($hora)
    {
        $hora = str_replace(":", "", $hora);

        $minutos = substr($hora, -2);
        $horas = substr($hora, 0, -2);
        $horasFloat = floatval($horas);

        $sinal = 1;
        if($horasFloat < 0) {
            $sinal = -1;
        }

        $horasFloat = $sinal * (abs($horasFloat) + (floatval($minutos)/60));

        return $horasFloat;
    }

    /**
     * Retorna uma determinada hora passada como parâmetro (no formato float) em formato string ('HH:MI').
     * Ex.: getHoraDecimalDeString(8.5) -> '08:30'
     *
     * @static
     * @param float $hora
     * @param boolean $comSeparador
     * @return string
     */
    public static function getHoraStringDeFloat($hora, $comSeparador = false)
    {
        $separador = "";
        if($comSeparador) {
            $separador = ":";
        }

        $horafloat = floatval($hora);

        $sinal = 1;
        if($horafloat < 0) {
            $sinal = -1;
        }

        $horafloat = abs($horafloat);

        $minutos = round(($horafloat-floor($horafloat))*60);
        if($minutos >= 60){
            /* Essa situação acontece quando a parte decimal da hora é muito perto de 1.
               Exemplo: 7.99999 retornava 07:60 quando deveria retornar 08:00 */
            $minutos = str_pad(($minutos-60), 2, "0", STR_PAD_LEFT);
            $somahora = 1;
        } else {
            $minutos = str_pad(($minutos), 2, "0", STR_PAD_LEFT);
            $somahora = 0;
        }

        $horas = floor($horafloat)+$somahora;
        $horas = str_pad(($horas), 2, "0", STR_PAD_LEFT);

        $horastring = $horas.$separador.$minutos;
        if($sinal < 0) {
            $horastring = '-'.$horastring;
        }

        return $horastring;
    }

    public static function getDiaSemanaString(){

        $sql = "SELECT TO_CHAR(SYSDATE , 'D') FROM DUAL";

        $dia = \Setforms2\Runtime\Connection::getInstance()->fetchAll($sql);
        $dia = $dia[0];
        $dia = $dia["TO_CHAR(SYSDATE,'D')"];

        switch($dia){
            case "1":
                $diaSemana = tk_get_message_by_name('domingo');
                break;
            case "2":
                $diaSemana = tk_get_message_by_name('segunda_feira');
                break;
            case "3":
                $diaSemana = tk_get_message_by_name('terca_feira');
                break;
            case "4":
                $diaSemana = tk_get_message_by_name('quarta_feira');
                break;
            case "5":
                $diaSemana = tk_get_message_by_name('quinta_feira');
                break;
            case "6":
                $diaSemana = tk_get_message_by_name('sexta_feira');
                break;
            case "7":
                $diaSemana = tk_get_message_by_name('sabado');
                break;

            default:
                $diaSemana = "";
            break;
        }
        return $diaSemana;
    }

}
