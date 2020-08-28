<?php
namespace Odhen\API\Util;

class Database {

    protected $util;
    protected $tipoBanco;

    public function __construct(\Odhen\API\Util\Util $util) {
        $this->util = $util;
        $this->tipoBanco = $this->util->getXMLParameter('connection_params');
        $this->tipoBanco = $this->tipoBanco['driver'];
    }

    const ORACLE = 'oci8';

    public function databaseIsOracle() {
        return $this->tipoBanco == self::ORACLE;
    }

    public function convertToDate($dateString) {
        if (is_string($dateString)) {
            return \DateTime::createFromFormat('d/m/Y', explode(' ', $dateString)[0]);
        } else {
            return $dateString;
        }
    }

    public function convertToDateFromIso($dateString) {
        if (is_string($dateString)) {
            return new \DateTime($dateString);
        } else {
            return $dateString;
        }
    }

    public function convertToDateTime($dateString) {
        if(is_string($dateString)){
            if (self::databaseIsOracle()) {
                $date = \DateTime::createFromFormat('d/m/Y H:i:s', $dateString);
                return $date->format('d/m/Y H:i:s');
            } else {
                return \DateTime::createFromFormat('d/m/Y H:i:s', $dateString);
            }
        }else{
            return $dateString;
        }
    }

    public function convertToDateGeneric($format, $dateString) {
        if (is_string($dateString)) {
            return \DateTime::createFromFormat($format, $dateString);
        } else {
            return $dateString;
        }
    }

    public function convertToDateDB($dateString) {
        if (is_string($dateString)) {
            if (self::databaseIsOracle()) {
                return \DateTime::createFromFormat('Y-m-d H:i:s', $dateString);
            } else {
                return \DateTime::createFromFormat('Y-m-d H:i:s.u', $dateString);
            }
        }else{
            return $dateString;
        }
    }

    public function getCurrentDateTime() {
        $date = new \DateTime;
        return $date->format('d/m/Y H:i:s');
    }

    public function getCurrentDate() {
        $date = new \DateTime;
        if (self::databaseIsOracle()) {
            return $date->format('d/m/Y H:i:s');
        } else {
            return $date->format('d/m/Y');
        }
    }

    public function dateTimeToString($date){
        if (is_a($date, 'DateTime')) {
            return $date->format('d/m/Y H:i:s');
        } else {
            return $date;
        }
    }

    public function dateToString($date){
        if (is_a($date, 'DateTime')) {
            if (self::databaseIsOracle() ) {
                return $date->format('d/m/Y H:i:s');
            } else {
                return $date->format('d/m/Y');
            }
        } else {
            return $date;
        }
    }

}