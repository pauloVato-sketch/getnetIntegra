<?php
require_once '../scripts/bootstrap.test.php';

use PHPUnit\Framework\TestCase;
use Zeedhi\Framework\DependencyInjection\InstanceManager;

class CaixaTest extends TestCase {

    const CLASS_TO_TEST = '\Odhen\API\Service\Caixa';
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

    public function testAbreCaixa(){  // 2
        $CDFILIAL = "0004";
        $DTABERCAIX = new \DateTime();
        $CDCAIXA = "001";
        $NRORG = 1;
        $CDOPERADOR = "000000000029";
        $DTMOVTURCAIX = new \DateTime();
        $VRMOVIVEND = "500";
        $IDMONGO = "";
        $IDATUTURCAIXA = "I";
        $deveImprimirSuprimento = true;

        $this->connectionMock->setFunctionMock(self::CLASS_TO_TEST, "AbreCaixa", true);  // 2
        $rst = $this->testClass->abreCaixa($CDFILIAL, $DTABERCAIX, $CDCAIXA, $NRORG, $CDOPERADOR, $DTMOVTURCAIX, $VRMOVIVEND, $IDMONGO, $IDATUTURCAIXA, $deveImprimirSuprimento);
        $this->connectionMock->saveRecording();
        $this->assertFalse($rst['error']); //Testa se houve algum erro na abertura do caixa
        $this->assertEquals($rst['message'],'Caixa aberto com sucesso.'); //Testa se a mensagem de abertura está correta

    }

    public function testAbreCaixaAberto(){  // 2
        $CDFILIAL = "0004";
        $DTABERCAIX = new \DateTime();
        $CDCAIXA = "001";
        $NRORG = 1;
        $CDOPERADOR = "000000000029";
        $DTMOVTURCAIX = new \DateTime();
        $VRMOVIVEND = "500";
        $IDMONGO = "";
        $IDATUTURCAIXA = "I";
        $deveImprimirSuprimento = true;

        $this->connectionMock->setFunctionMock(self::CLASS_TO_TEST, "abreCaixaAberto", true);  // 2
        $rst = $this->testClass->abreCaixa($CDFILIAL, $DTABERCAIX, $CDCAIXA, $NRORG, $CDOPERADOR, $DTMOVTURCAIX, $VRMOVIVEND, $IDMONGO, $IDATUTURCAIXA, $deveImprimirSuprimento);
        $this->connectionMock->saveRecording();
        $this->assertTrue($rst['error']); //Testa se houve algum erro na abertura do caixa
        $this->assertEquals($rst['message'],"O caixa " . $CDCAIXA . " se encontra aberto. Operação inválida."); //Testa se a mensagem de abertura está correta
    }

    public function testFechaCaixa () {
        $CLOSEPOSSIMPLE = 'S';
        $DTABERCAIX = null;
        $DTFECHCAIX = new \DateTime();
        $CDFILIAL = "0004";
        $CDCAIXA = "001";
        $NRORG = 1;
        $CDOPERFECH = "000000000029";
        $NRCONFTELA = 1;
        $TIPORECE = array(0);
        $deveImprimirRelatorio = true;

        $this->connectionMock->setFunctionMock(self::CLASS_TO_TEST, "fechaCaixa", true);
        $rst = $this->testClass->fechaCaixa($CLOSEPOSSIMPLE, $DTABERCAIX, $DTFECHCAIX, $CDFILIAL, $CDCAIXA, $NRORG, $CDOPERFECH, $NRCONFTELA, $TIPORECE, $deveImprimirRelatorio);
        $this->connectionMock->saveRecording();
        $this->assertFalse($rst['error']);
        $this->assertEquals($rst['message'], 'Caixa fechado com sucesso.');
    }

    public function testFechaCaixaFechado () {
        $CLOSEPOSSIMPLE = 'S';
        $DTABERCAIX = null;
        $DTFECHCAIX = new \DateTime();
        $CDFILIAL = "0004";
        $CDCAIXA = "001";
        $NRORG = 1;
        $CDOPERFECH = "000000000029";
        $NRCONFTELA = 1;
        $TIPORECE = array(0);
        $deveImprimirRelatorio = true;

        $this->connectionMock->setFunctionMock(self::CLASS_TO_TEST, "fechaCaixaFechado", true);
        $rst = $this->testClass->fechaCaixa($CLOSEPOSSIMPLE, $DTABERCAIX, $DTFECHCAIX, $CDFILIAL, $CDCAIXA, $NRORG, $CDOPERFECH, $NRCONFTELA, $TIPORECE, $deveImprimirRelatorio);
        $this->connectionMock->saveRecording();
        $this->assertTrue($rst['error']);
        $this->assertEquals($rst['message'], "Não existe um caixa aberto para executar o fechamento. Operação inválida.");
    }

    public function testGetEstadoCaixaFechado(){ // 1
        $CDFILIAL = "0004";
        $CDCAIXA = "001";
        $NRORG = 1;

        $this->connectionMock->setFunctionMock(self::CLASS_TO_TEST, "getEstadoCaixaFechado", true); // 1
        $rst = $this->testClass->getEstadoCaixa($CDFILIAL, $CDCAIXA, $NRORG);
        $this->connectionMock->saveRecording();
        $this->assertEquals('fechado', $rst['estado']);
    }

    public function testGetEstadoCaixaAberto(){ // 3
        $CDFILIAL = "0004";
        $CDCAIXA = "001";
        $NRORG = 1;

        $this->connectionMock->setFunctionMock(self::CLASS_TO_TEST, "getEstadoCaixaAberto", true); // 3
        $rst = $this->testClass->getEstadoCaixa($CDFILIAL, $CDCAIXA, $NRORG);
        $this->connectionMock->saveRecording();
        $this->assertEquals('aberto', $rst['estado']);
    }

}