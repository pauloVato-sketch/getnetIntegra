<?php
namespace Odhen\API\Util;

use Zeedhi\Framework\DependencyInjection\InstanceManager;

class Exception extends \Exception {

    public static function invalidDataBase($databaseDriver) {
        return new static('Invalid DataBase driver "'.$databaseDriver.'"');
    }



    const SALE_FOLDER = 'VENDAS_';
    const EXCEPTION_FOLDER = 'EXCEPTIONS_';
    const LOG_TYPES = array(
        'SALE_LOG' => self::SALE_FOLDER,
        'EXCEPTION_LOG' => self::EXCEPTION_FOLDER 
    );

    public static function logException($exception, $logType) {
        try {
            $logPath = InstanceManager::getInstance()->getParameter('LOG_PATH');
            $dateTime = new \DateTime();

            // Cria a pasta de Log caso não existir
            if(!realpath($logPath)){
                mkdir($logPath);
            }

            $logFolder = $logPath . DIRECTORY_SEPARATOR . $logType;

            if(!realpath($logFolder)){
                mkdir($logFolder);
            }
            // nome do arquivo de log
            $logFile = $logType . $dateTime->format('d_m_Y') . '.txt';
            // log a ser escrito
            $logText = "[" . $dateTime->format("d/m/Y H:i:s") . "]" . 
                PHP_EOL ."log: " . $exception->getMessage() . PHP_EOL .
                PHP_EOL;
            // executa escrita
            file_put_contents($logFolder . DIRECTORY_SEPARATOR . $logFile, $logText, FILE_APPEND);                    
        } catch (\Exception $e) {
            // falha ao salvar log
        }
    }

}