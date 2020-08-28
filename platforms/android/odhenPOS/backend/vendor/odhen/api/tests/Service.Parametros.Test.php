<?php
require_once '../scripts/bootstrap.test.php';

use PHPUnit\Framework\TestCase;
use Zeedhi\Framework\DependencyInjection\InstanceManager;

class ParametrosTest extends TestCase {

    const CLASS_TO_TEST = '\Odhen\API\Service\Parametros';
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

    public function testCarregaDadosMesa(){
        $CDFILIAL = '0004';
        $CDCAIXA = '001'; // caixa PKR
        $CDVENDEDOR = '0029';
        $this->connectionMock->setFunctionMock(self::CLASS_TO_TEST, "CarregaDadosMesa", true);
        $result = $this->testClass->carregaDados($CDFILIAL, $CDCAIXA, $CDVENDEDOR, null);
        $this->connectionMock->saveRecording();
        
        $this->assertFalse($result['error']);
        $this->assertArrayHasKey('dados', $result);
        $this->assertArrayHasKey('parametros', $result['dados']);
        $this->assertArrayHasKey('cardapio', $result['dados']);
        $this->assertArrayHasKey('observacoes', $result['dados']);
        $this->assertArrayHasKey('ambientes', $result['dados']);
        $this->assertArrayHasKey('recebimentos', $result['dados']);
        $this->assertArrayHasKey('grupoRecebimentos', $result['dados']);
        $this->assertInternalType('array', $result['dados']['parametros']);
        $this->assertInternalType('array', $result['dados']['cardapio']);
        $this->assertInternalType('array', $result['dados']['observacoes']);
        $this->assertInternalType('array', $result['dados']['ambientes']);
        $this->assertInternalType('array', $result['dados']['recebimentos']);
        $this->assertInternalType('array', $result['dados']['grupoRecebimentos']);
        $this->assertNotEmpty($result['dados']['parametros']);
        $this->assertNotEmpty($result['dados']['cardapio']);
        $this->assertNotEmpty($result['dados']['observacoes']);
        $this->assertNotEmpty($result['dados']['ambientes']);
        $this->assertNotEmpty($result['dados']['recebimentos']);
        $this->assertNotEmpty($result['dados']['grupoRecebimentos']);
        $this->assertEquals($result['dados']['parametros']['CDFILIAL'], $CDFILIAL);
        $this->assertEquals($result['dados']['parametros']['CDCAIXA'], $CDCAIXA);
        $this->assertEquals($result['dados']['parametros']['CDVENDEDOR'], $CDVENDEDOR);
        $this->assertInternalType('string', $result['dados']['parametros']['CDOPERADOR']);
        $this->assertInternalType('string', $result['dados']['parametros']['NMOPERADOR']);
        $this->assertInternalType('string', $result['dados']['parametros']['CDVENDEDOR']);
        $this->assertInternalType('string', $result['dados']['parametros']['NMFANVEN']);
        $this->assertInternalType('string', $result['dados']['parametros']['CDCAIXA']);
        $this->assertInternalType('string', $result['dados']['parametros']['CDLOJA']);
        $this->assertInternalType('string', $result['dados']['parametros']['IDTPEMISSAOFOS']);
        $this->assertInternalType('string', $result['dados']['parametros']['NRCONFTELA']);
        $this->assertInternalType('string', $result['dados']['parametros']['CDFILIAL']);
        $this->assertInternalType('string', $result['dados']['parametros']['NMFILIAL']);
        $this->assertArrayHasKey('NRATRAPADRAO', $result['dados']['parametros']);
        $this->assertArrayHasKey('NRORG', $result['dados']['parametros']);

        // valida formato grupo de produtos
        $this->assertEquals('101', $result['dados']['cardapio']['101']['grupo']['CODIGO']);
        $this->assertEquals('PIZZA TOGO', $result['dados']['cardapio']['101']['grupo']['DESC']);
        $this->assertEquals('255.000', $result['dados']['cardapio']['101']['grupo']['COLOR']);
        $this->assertEquals(null, $result['dados']['cardapio']['101']['grupo']['DSENDEIMG']);
        $this->assertEquals('01', $result['dados']['cardapio']['101']['grupo']['NRBUTTON']);

        // valida formato produto normal
        $this->assertEquals("9130100100", $result['dados']['cardapio']['208']['produtos']['9130100100']['CDARVPROD']);
        $this->assertEquals(null, $result['dados']['cardapio']['208']['produtos']['9130100100']['CDBARPRODUTO']);
        $this->assertEquals(null, $result['dados']['cardapio']['208']['produtos']['9130100100']['CDPRODINTE']);
        $this->assertEquals("9130100100", $result['dados']['cardapio']['208']['produtos']['9130100100']['CDPRODUTO']);
        $this->assertEquals("RASPADINHA SALAO COMBO 1", $result['dados']['cardapio']['208']['produtos']['9130100100']['NMPRODUTO']);
        $this->assertEquals(null, $result['dados']['cardapio']['208']['produtos']['9130100100']['DTFINVGPROMOC']);
        $this->assertEquals(null, $result['dados']['cardapio']['208']['produtos']['9130100100']['DTINIVGPROMOC']);
        $this->assertInternalType('array', $result['dados']['cardapio']['208']['produtos']['9130100100']['GRUPOS']);
        $this->assertInternalType('array', $result['dados']['cardapio']['208']['produtos']['9130100100']['OBSERVACOES']);
        $this->assertInternalType('array', $result['dados']['cardapio']['208']['produtos']['9130100100']['IMPRESSORAS']);
        $this->assertEquals("1", $result['dados']['cardapio']['208']['produtos']['9130100100']['IDIMPPRODUTO']);
        $this->assertEquals("N", $result['dados']['cardapio']['208']['produtos']['9130100100']['IDPESAPROD']);
        $this->assertEquals("0", $result['dados']['cardapio']['208']['produtos']['9130100100']['IDTIPOCOMPPROD']);
        $this->assertEquals(null, $result['dados']['cardapio']['208']['produtos']['9130100100']['IDTIPOPROD']);

        // valida formato produto promoção inteligente
        $this->assertEquals("9050100102", $result['dados']['cardapio']['101']['produtos']['9050100102']['CDARVPROD']);
        $this->assertEquals(null, $result['dados']['cardapio']['101']['produtos']['9050100102']['CDBARPRODUTO']);
        $this->assertEquals(null, $result['dados']['cardapio']['101']['produtos']['9050100102']['CDPRODINTE']);
        $this->assertEquals("9050100102", $result['dados']['cardapio']['101']['produtos']['9050100102']['CDPRODUTO']);
        $this->assertEquals("TD GRD INTEIRA", $result['dados']['cardapio']['101']['produtos']['9050100102']['NMPRODUTO']);
        $this->assertEquals(null, $result['dados']['cardapio']['101']['produtos']['9050100102']['DTFINVGPROMOC']);
        $this->assertEquals(null, $result['dados']['cardapio']['101']['produtos']['9050100102']['DTINIVGPROMOC']);
        $this->assertInternalType('array', $result['dados']['cardapio']['101']['produtos']['9050100102']['GRUPOS']);
        $this->assertInternalType('array', $result['dados']['cardapio']['101']['produtos']['9050100102']['OBSERVACOES']);
        $this->assertInternalType('array', $result['dados']['cardapio']['101']['produtos']['9050100102']['IMPRESSORAS']);
        $this->assertEquals("2", $result['dados']['cardapio']['101']['produtos']['9050100102']['IDIMPPRODUTO']);
        $this->assertEquals("N", $result['dados']['cardapio']['101']['produtos']['9050100102']['IDPESAPROD']);
        $this->assertEquals("3", $result['dados']['cardapio']['101']['produtos']['9050100102']['IDTIPOCOMPPROD']);
        $this->assertEquals(null, $result['dados']['cardapio']['101']['produtos']['9050100102']['IDTIPOPROD']);
        $this->assertEquals(0.02, $result['dados']['cardapio']['101']['produtos']['9050100102']['VRPRECITEM']);
        $this->assertEquals("N", $result['dados']['cardapio']['101']['produtos']['9050100102']['IDPRODBLOQ']);
        $this->assertEquals("255.000", $result['dados']['cardapio']['101']['produtos']['9050100102']['NRCOLORBACK']);
        $this->assertEquals("N", $result['dados']['cardapio']['101']['produtos']['9050100102']['IDCONTROLAREFIL']);
        $this->assertEquals(null, $result['dados']['cardapio']['101']['produtos']['9050100102']['DSENDEIMG']);
        $this->assertEquals(null, $result['dados']['cardapio']['101']['produtos']['9050100102']['DSPRODVENDA']);
        $this->assertEquals(null, $result['dados']['cardapio']['101']['produtos']['9050100102']['DSADICPROD']);
        $this->assertEquals("PIZZA TOGO", $result['dados']['cardapio']['101']['produtos']['9050100102']['NMGRUPO']);

        // valida grupos e produtos da promocao inteligente
        $this->assertEquals("0000000001", $result['dados']['cardapio']['101']['produtos']['9050100102']['GRUPOS']['0000000001']['grupo']['CDGRUPROMOC']);
        $this->assertEquals("MASSA GRD", $result['dados']['cardapio']['101']['produtos']['9050100102']['GRUPOS']['0000000001']['grupo']['NMGRUPROMOC']);
        $this->assertEquals(1, $result['dados']['cardapio']['101']['produtos']['9050100102']['GRUPOS']['0000000001']['grupo']['QTPRGRUPPROMOC']);
        $this->assertEquals("9050200102", $result['dados']['cardapio']['101']['produtos']['9050100102']['GRUPOS']['0000000001']['produtos']['9050200102']['CDPRODUTO']);
        $this->assertEquals("2", $result['dados']['cardapio']['101']['produtos']['9050100102']['GRUPOS']['0000000001']['produtos']['9050200102']['IDIMPPRODUTO']);
        $this->assertEquals("T", $result['dados']['cardapio']['101']['produtos']['9050100102']['GRUPOS']['0000000001']['produtos']['9050200102']['IDAPLICADESCPR']);
        $this->assertEquals("P", $result['dados']['cardapio']['101']['produtos']['9050100102']['GRUPOS']['0000000001']['produtos']['9050200102']['IDPERVALORDES']);
        $this->assertEquals("PZ TD GRANDE 1/2 A 1/2", $result['dados']['cardapio']['101']['produtos']['9050100102']['GRUPOS']['0000000001']['produtos']['9050200102']['NMPRODUTO']);
        $this->assertEquals(".000", $result['dados']['cardapio']['101']['produtos']['9050100102']['GRUPOS']['0000000001']['produtos']['9050200102']['VRDESPRODPROMOC']);
        $this->assertEquals(0.02, $result['dados']['cardapio']['101']['produtos']['9050100102']['GRUPOS']['0000000001']['produtos']['9050200102']['VRPRECITEM']);
        $this->assertEquals("N", $result['dados']['cardapio']['101']['produtos']['9050100102']['GRUPOS']['0000000001']['produtos']['9050200102']['IDPRODBLOQ']);
        $this->assertInternalType('array', $result['dados']['cardapio']['101']['produtos']['9050100102']['GRUPOS']['0000000001']['produtos']['9050200102']['IMPRESSORAS']);
        $this->assertEquals(null, $result['dados']['cardapio']['101']['produtos']['9050100102']['GRUPOS']['0000000001']['produtos']['9050200102']['DSPRODVENDA']);
        $this->assertEquals(null, $result['dados']['cardapio']['101']['produtos']['9050100102']['GRUPOS']['0000000001']['produtos']['9050200102']['DSADICPROD']);
        $this->assertEquals(null, $result['dados']['cardapio']['101']['produtos']['9050100102']['GRUPOS']['0000000001']['produtos']['9050200102']['DSENDEIMGPROMO']);
        $this->assertEquals(99, $result['dados']['cardapio']['101']['produtos']['9050100102']['GRUPOS']['0000000001']['produtos']['9050200102']['NRORDPROMOPR']);
        $this->assertEquals("N", $result['dados']['cardapio']['101']['produtos']['9050100102']['GRUPOS']['0000000001']['produtos']['9050200102']['IDPRODPRESELEC']);
        
        // valida observacoes da promocao inteligente
        $this->assertEquals("07", $result['dados']['cardapio']['101']['produtos']['9050100102']['OBSERVACOES'][0]);
    }

    public function testCarregaDadosComanda(){
        $CDFILIAL = '0004';
        $CDCAIXA = '001'; // caixa PKC
        $CDVENDEDOR = '0029';
        $this->connectionMock->setFunctionMock(self::CLASS_TO_TEST, "CarregaDadosComanda", true);
        $result = $this->testClass->carregaDados($CDFILIAL, $CDCAIXA, $CDVENDEDOR, null);
        $this->connectionMock->saveRecording();

        $this->assertFalse($result['error']);
        $this->assertFalse($result['error']);
        $this->assertArrayHasKey('dados', $result);
        $this->assertArrayHasKey('parametros', $result['dados']);
        $this->assertArrayHasKey('observacoes', $result['dados']);
        $this->assertArrayHasKey('ambientes', $result['dados']);
        $this->assertArrayHasKey('recebimentos', $result['dados']);
        $this->assertArrayHasKey('grupoRecebimentos', $result['dados']);
        $this->assertInternalType('array', $result['dados']['parametros']);
        $this->assertInternalType('array', $result['dados']['observacoes']);
        $this->assertInternalType('array', $result['dados']['ambientes']);
        $this->assertInternalType('array', $result['dados']['recebimentos']);
        $this->assertInternalType('array', $result['dados']['grupoRecebimentos']);
        $this->assertNotEmpty($result['dados']['parametros']);
        $this->assertNotEmpty($result['dados']['observacoes']);
        $this->assertNotEmpty($result['dados']['ambientes']);
        $this->assertNotEmpty($result['dados']['recebimentos']);
        $this->assertNotEmpty($result['dados']['grupoRecebimentos']);
    }

    public function testCarregaDadosBalcao(){
        $CDFILIAL = '0004';
        $CDCAIXA = '001'; // caixa FOS
        $CDVENDEDOR = '0029';
        $this->connectionMock->setFunctionMock(self::CLASS_TO_TEST, "CarregaDadosBalcao", true);
        $result = $this->testClass->carregaDados($CDFILIAL, $CDCAIXA, $CDVENDEDOR, null);
        $this->connectionMock->saveRecording();

        $this->assertFalse($result['error']);
        $this->assertFalse($result['error']);
        $this->assertArrayHasKey('dados', $result);
        $this->assertArrayHasKey('parametros', $result['dados']);
        $this->assertArrayHasKey('observacoes', $result['dados']);
        $this->assertArrayHasKey('ambientes', $result['dados']);
        $this->assertArrayHasKey('recebimentos', $result['dados']);
        $this->assertArrayHasKey('grupoRecebimentos', $result['dados']);
        $this->assertInternalType('array', $result['dados']['parametros']);
        $this->assertInternalType('array', $result['dados']['observacoes']);
        $this->assertInternalType('array', $result['dados']['ambientes']);
        $this->assertInternalType('array', $result['dados']['recebimentos']);
        $this->assertInternalType('array', $result['dados']['grupoRecebimentos']);
        $this->assertNotEmpty($result['dados']['parametros']);
        $this->assertNotEmpty($result['dados']['observacoes']);
        $this->assertNotEmpty($result['dados']['ambientes']);
        $this->assertNotEmpty($result['dados']['recebimentos']);
        $this->assertNotEmpty($result['dados']['grupoRecebimentos']);
    }

}