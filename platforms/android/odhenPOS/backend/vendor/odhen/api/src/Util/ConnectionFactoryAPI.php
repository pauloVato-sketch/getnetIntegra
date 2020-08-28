<?php
namespace Odhen\API\Util;

use Doctrine\DBAL\DriverManager;

class ConnectionFactoryAPI extends \Zeedhi\Framework\ORM\ConnectionFactory{

    public static function factoryWithEncryptedPassword(array $connectionParams, $salt, $config = null, $eventManager = null, $session= null) {
        if ($connectionParams['isEncrypted']){
            $connectionParams["password"] = parent::decrypt($connectionParams["password"], $salt);
        }
        $auth = null;
        if($session) {
          $auth = $session->get('AUTH');
        }
        if($auth) {
            $nrOrg = $auth['NRORG'];
            if ($nrOrg && isset($connectionParams['useVpd']) && $connectionParams['useVpd'] == true) {
                if (isset($connectionParams['vpdWithWallet']) && $connectionParams['vpdWithWallet'] == true) {
                    unset($connectionParams['host']);
                    unset($connectionParams['port']);
                    $connectionParams['user'] = '/';
                    $connectionParams['password'] = '';
                    $connectionParams['dbname'] = 'USR_ORG_' . $nrOrg;
                    $connectionParams['sessionMode'] = -2147483648;
                } else {
                    $connectionParams['user'] = 'USR_ORG_' . $nrOrg;
                    $connectionParams['password'] = $connectionParams['vpdPassword'];
                    if (isset($connectionParams['isWallet']) && $connectionParams['isWallet'] == true) {
                        $connectionParams['dbname'] = isset($connectionParams['vpdDBNAME'])
                            ? $connectionParams['vpdDBNAME']
                            : $connectionParams['vpdDBName'];
                        $connectionParams['dbname'] = $connectionParams['vpdDBNAME'];
                        $connectionParams['host'] = $connectionParams['vpdHost'];
                        $connectionParams['port'] = $connectionParams['vpdPort'];
                        $connectionParams['service'] = $connectionParams['vpdService'];
                        unset($connectionParams['sessionMode']);
                    }
                }
            }
        }
        return DriverManager::getConnection($connectionParams, $config, $eventManager);
    }
}
