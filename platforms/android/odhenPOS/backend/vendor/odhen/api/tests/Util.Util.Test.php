<?php
require_once '../scripts/bootstrap.php';

use PHPUnit\Framework\TestCase;
use Zeedhi\Framework\DependencyInjection\InstanceManager;

class UtilTest extends TestCase {

    const CLASS_TO_TEST = '\Odhen\API\Util\Util';

    protected $testClass;

    public function __construct() {
        parent::__construct();
        $this->instanceManager = InstanceManager::getInstance();
        $this->testClass = $this->instanceManager->getService(self::CLASS_TO_TEST);
    }

    public function testSendEmailVenda () {
        $email_addressee = 'hugo.souza@teknisa.com';
        $content = array(
            'DSQRCODE' => '123'
        );
        $date = new \DateTime();

        $rst = $this->testClass->sendEmailVenda($email_addressee, $content, $date);
        $this->assertFalse($rst['error']);
    }
}