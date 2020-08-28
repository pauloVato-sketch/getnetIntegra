<?php
require_once '../scripts/bootstrap.test.php';

use PHPUnit\Framework\TestCase;
use Zeedhi\Framework\DependencyInjection\InstanceManager;

class LoginTest extends TestCase {

    const CLASS_TO_TEST = '\Odhen\API\Service\Login';
    const CONNECTION_CLASS = '\Odhen\API\Test\ConnectionMock';

    protected $testClass;
    protected $instanceManager;
    protected $connectionMock;

    public function __construct() {
        parent::__construct();
        $this->instanceManager = InstanceManager::getInstance();
        $this->testClass = $this->instanceManager->getService(self::CLASS_TO_TEST);
        $this->connectionMock = $this->instanceManager->getService(self::CONNECTION_CLASS);
        //$this->connectionMock->setClassMock(self::CLASS_TO_TEST);
    }

    public function testValidaLoginCaixa () {
        $CDFILIAL = "0004";
        $CDCAIXA = "001";
        $CDVENDEDOR = "0029";
        $CDSENHOPER_DIGITADA = "1";
        $IDHABCAIXAVENDA = NULL;
        $CDOPERADOR = NULL;

        $this->connectionMock->setFunctionMock(self::CLASS_TO_TEST, "validaLoginCaixa", true);
        $rst = $this->testClass->validaLoginCaixa($CDFILIAL, $CDCAIXA, $CDVENDEDOR, $CDSENHOPER_DIGITADA, $IDHABCAIXAVENDA, $CDOPERADOR);
        $this->connectionMock->saveRecording();
        $this->assertFalse($rst['error']);
     }

    public function testValidaOperador() {
        $CDOPERADOR = "000000000029";
        $CDFILIAL = "0004";
        $CDSENHOPER_DIGITADA = "1";

        $this->connectionMock->setFunctionMock(self::CLASS_TO_TEST, "validaOperador", true);
        $rst = $this->testClass->validaOperador($CDOPERADOR, $CDFILIAL, $CDSENHOPER_DIGITADA);
        $this->connectionMock->saveRecording();
        $this->assertFalse($rst['error']);
    }

    public function testValidaLoginFilialInvalida() {
        $CDFILIAL = "1004";
        $CDCAIXA = "001";
        $CDVENDEDOR = "0029";
        $CDSENHOPER_DIGITADA = "1";
        $IDHABCAIXAVENDA = NULL;
        $CDOPERADOR = NULL;

        $this->connectionMock->setFunctionMock(self::CLASS_TO_TEST, "validaLoginFilialInvalida", true);
        $rst = $this->testClass->validaLoginCaixa($CDFILIAL, $CDCAIXA, $CDVENDEDOR, $CDSENHOPER_DIGITADA, $IDHABCAIXAVENDA, $CDOPERADOR);
        $this->connectionMock->saveRecording();
        $this->assertTrue($rst['error']);
        $this->assertArrayHasKey('message', $rst );
        $this->assertEquals($rst['message'], 'Filial não encontrada.');
    }

    public function testValidaLoginFilialSemClientePadrao() {
        $CDFILIAL = "0004";
        $CDCAIXA = "001";
        $CDVENDEDOR = "0029";
        $CDSENHOPER_DIGITADA = "1";
        $IDHABCAIXAVENDA = NULL;
        $CDOPERADOR = NULL;

        $this->connectionMock->setFunctionMock(self::CLASS_TO_TEST, "validaLoginFilialSemClientePadrao", true);
        $rst = $this->testClass->validaLoginCaixa($CDFILIAL, $CDCAIXA, $CDVENDEDOR, $CDSENHOPER_DIGITADA, $IDHABCAIXAVENDA, $CDOPERADOR);
        $this->connectionMock->saveRecording();
        $this->assertTrue($rst['error']);
        $this->assertArrayHasKey('message', $rst );
        $this->assertEquals($rst['message'], 'Filial não possui cliente padrão.');
    }

    public function testValidaLoginVendedorInvalido() {
        $CDFILIAL = "0004";
        $CDCAIXA = "001";
        $CDVENDEDOR = "10029";
        $CDSENHOPER_DIGITADA = "1";
        $IDHABCAIXAVENDA = NULL;
        $CDOPERADOR = '10029';

        $this->connectionMock->setFunctionMock(self::CLASS_TO_TEST, "validaLoginVendedorInvalido", true);
        $rst = $this->testClass->validaLoginCaixa($CDFILIAL, $CDCAIXA, $CDVENDEDOR, $CDSENHOPER_DIGITADA, $IDHABCAIXAVENDA, $CDOPERADOR);
        $this->connectionMock->saveRecording();
        $this->assertTrue($rst['error']);
        $this->assertArrayHasKey('message', $rst );
        $this->assertEquals($rst['message'], 'Vendedor não encontrado.');
    }

    public function testValidaLoginCaixaSenhaInvalida() {
        $CDFILIAL = "0004";
        $CDCAIXA = "001";
        $CDVENDEDOR = "0029";
        $CDSENHOPER_DIGITADA = "12";
        $IDHABCAIXAVENDA = NULL;
        $CDOPERADOR = NULL;

        $this->connectionMock->setFunctionMock(self::CLASS_TO_TEST, "validaLoginCaixaSenhaInvalida", true);
        $rst = $this->testClass->validaLoginCaixa($CDFILIAL, $CDCAIXA, $CDVENDEDOR, $CDSENHOPER_DIGITADA, $IDHABCAIXAVENDA, $CDOPERADOR);
        $this->connectionMock->saveRecording();
        $this->assertTrue($rst['error']);
        $this->assertArrayHasKey('message', $rst );
        $this->assertEquals($rst['message'], "Senha inválida.");
    }

    public function testValidaLoginVendedorNaoAssociadoFilial() {
        $CDFILIAL = "0004";
        $CDCAIXA = "001";
        $CDVENDEDOR = "0029";
        $CDSENHOPER_DIGITADA = "1";
        $IDHABCAIXAVENDA = NULL;
        $CDOPERADOR = NULL;

        $this->connectionMock->setFunctionMock(self::CLASS_TO_TEST, "validaLoginVendedorNaoAssociadoFilial", true);
        $rst = $this->testClass->validaLoginCaixa($CDFILIAL, $CDCAIXA, $CDVENDEDOR, $CDSENHOPER_DIGITADA, $IDHABCAIXAVENDA, $CDOPERADOR);
        $this->connectionMock->saveRecording();
        $this->assertTrue($rst['error']);
        $this->assertArrayHasKey('message', $rst );
        $this->assertEquals($rst['message'], 'Vendedor não possui filial associada.');
    }

    public function testValidaLoginVendedorNaoAssociadoFilialInformada() {
        $CDFILIAL = "0005";
        $CDCAIXA = "001";
        $CDVENDEDOR = "0029";
        $CDSENHOPER_DIGITADA = "1";
        $IDHABCAIXAVENDA = NULL;
        $CDOPERADOR = NULL;

        $this->connectionMock->setFunctionMock(self::CLASS_TO_TEST, "validaLoginVendedorNaoAssociadoFilialInformada", true);
        $rst = $this->testClass->validaLoginCaixa($CDFILIAL, $CDCAIXA, $CDVENDEDOR, $CDSENHOPER_DIGITADA, $IDHABCAIXAVENDA, $CDOPERADOR);
        $this->connectionMock->saveRecording();
        $this->assertTrue($rst['error']);
        $this->assertArrayHasKey('message', $rst );
        $this->assertEquals($rst['message'], 'Vendedor não está associado à filial informada.');
    }

    public function testValidaLoginVendedorNaoAssociadoOperador() {
        $CDFILIAL = "0004";
        $CDCAIXA = "001";
        $CDVENDEDOR = "0029";
        $CDSENHOPER_DIGITADA = "1";
        $IDHABCAIXAVENDA = NULL;
        $CDOPERADOR = NULL;

        $this->connectionMock->setFunctionMock(self::CLASS_TO_TEST, "validaLoginVendedorNaoAssociadoOperador", true);
        $rst = $this->testClass->validaLoginCaixa($CDFILIAL, $CDCAIXA, $CDVENDEDOR, $CDSENHOPER_DIGITADA, $IDHABCAIXAVENDA, $CDOPERADOR);
        $this->connectionMock->saveRecording();
        $this->assertTrue($rst['error']);
        $this->assertArrayHasKey('message', $rst );
        $this->assertEquals($rst['message'], 'Vendedor não possui operador associado.');
    }

    public function testValidaLoginOperadorInvalido() {
        $CDFILIAL = "0004";
        $CDCAIXA = "001";
        $CDVENDEDOR = "0029";
        $CDSENHOPER_DIGITADA = "1";
        $IDHABCAIXAVENDA = NULL;
        $CDOPERADOR = NULL;

        $this->connectionMock->setFunctionMock(self::CLASS_TO_TEST, "validaLoginOperadorInvalido", true);
        $rst = $this->testClass->validaLoginCaixa($CDFILIAL, $CDCAIXA, $CDVENDEDOR, $CDSENHOPER_DIGITADA, $IDHABCAIXAVENDA, $CDOPERADOR);
        $this->connectionMock->saveRecording();
        $this->assertTrue($rst['error']);
        $this->assertArrayHasKey('message', $rst );
        $this->assertEquals($rst['message'], 'Operador não encontrado.');
    }

    public function testValidaLoginOperadorNaoAssociadoFilialInformada() {
        $CDFILIAL = "0005";
        $CDCAIXA = "001";
        $CDVENDEDOR = "0029";
        $CDSENHOPER_DIGITADA = "1";
        $IDHABCAIXAVENDA = NULL;
        $CDOPERADOR = NULL;

        $this->connectionMock->setFunctionMock(self::CLASS_TO_TEST, "validaLoginOperadorNaoAssociadoFilial", true);
        $rst = $this->testClass->validaLoginCaixa($CDFILIAL, $CDCAIXA, $CDVENDEDOR, $CDSENHOPER_DIGITADA, $IDHABCAIXAVENDA, $CDOPERADOR);
        $this->connectionMock->saveRecording();
        $this->assertTrue($rst['error']);
        $this->assertArrayHasKey('message', $rst );
        $this->assertEquals($rst['message'], 'Operador não vinculado à filial informada.');
    }


    public function testValidaLoginOperadorSemSenha() {
        $CDFILIAL = "0004";
        $CDCAIXA = "001";
        $CDVENDEDOR = "0029";
        $CDSENHOPER_DIGITADA = "123";
        $IDHABCAIXAVENDA = NULL;
        $CDOPERADOR = NULL;

        $this->connectionMock->setFunctionMock(self::CLASS_TO_TEST, "validaLoginOperadorSemSenha", true);
        $rst = $this->testClass->validaLoginCaixa($CDFILIAL, $CDCAIXA, $CDVENDEDOR, $CDSENHOPER_DIGITADA, $IDHABCAIXAVENDA, $CDOPERADOR);
        $this->connectionMock->saveRecording();
        $this->assertTrue($rst['error']);
        $this->assertArrayHasKey('message', $rst );
        $this->assertEquals($rst['message'], 'O operador não possui senha cadastrada.');
    }

}
