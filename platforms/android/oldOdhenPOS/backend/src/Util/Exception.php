<?php
namespace Util;

use Zeedhi\Framework\DependencyInjection\InstanceManager;

class Exception extends \Exception {

    public static function invalidDataBase($databaseDriver) {
        return new static('Invalid DataBase driver "'.$databaseDriver.'"');
    }

    public static function logException($exception) {
        try {
            $logPath = InstanceManager::getInstance()->getParameter('LOG_PATH');
            $dateTime = new \DateTime();

            // Cria a pasta de Log caso não existir
            if(!realpath($logPath)){
                mkdir($logPath);
            }
            // nome do arquivo de log
            $logFile = 'logException_' . $dateTime->format('d_m_Y') . '.txt';
            // log a ser escrito
            $logText = "[" . $dateTime->format("d/m/Y H:i:s") . "]" . 
                PHP_EOL ."log: " . $exception->getMessage() . PHP_EOL .
                PHP_EOL;
            // executa escrita
            file_put_contents($logPath . DIRECTORY_SEPARATOR . $logFile, $logText, FILE_APPEND);                    
        } catch (\Exception $e) {
            // falha ao salvar log
        }
    }

}