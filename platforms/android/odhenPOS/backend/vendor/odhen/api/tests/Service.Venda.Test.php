<?php
require_once '../scripts/bootstrap.test.php';

use PHPUnit\Framework\TestCase;
use Zeedhi\Framework\DependencyInjection\InstanceManager;

class VendaTest extends TestCase {

    const CLASS_TO_TEST = '\Odhen\API\Test\VendaTest';
    const CONNECTION_CLASS = '\Odhen\API\Test\ConnectionMock';

    public function __construct() {
        parent::__construct();
        $this->instanceManager = InstanceManager::getInstance();
        $this->testClass = $this->instanceManager->getService(self::CLASS_TO_TEST);
        $this->connectionMock = $this->instanceManager->getService(self::CONNECTION_CLASS);
        //$this->connectionMock->setClassMock(self::CLASS_TO_TEST);
    }

    const MODEL_VENDA = array(
        'NRORG' => null, // required
        'CDFILIAL' => null, // required
        'CDLOJA' => null, // required
        'CDCAIXA' => null, // required
        'CDVENDEDOR' => null, // required
        'CDOPERADOR' => null, // required
        'DTVENDA' => null, // required
        'TOTALVENDA' => null, // required
        'ITEMVENDA' => array(self::MODEL_ITEMVENDA), // required
        'TIPORECE' => array(self::MODEL_TIPORECE), // required
        'NMCONSVEND' => null,
        'NRINSCRCONS' => null,
        'CDSENHAPED' => null,
        'NRSEQVENDA' => null,
        'VRTROCOVEND' => null,
        'EMAIL' => null,
        'VRDESCVENDA' => null,
        'CDCLIENTE' => null,
        'CDCONSUMIDOR' => null,
        'simulaImpressao' => false, // required
        'simulateSaleValidation' => false, // required
        'fechamento' => false, // required
        'dadosMesa' => null,
        'arrayPosicoes' => null,
        'IDORIGEMVENDA' => null // required
    );

    const MODEL_ITEMVENDA = array(
        'NMPRODUTO' => null, // required
        'CDPRODUTO' => null, // required
        'QTPRODVEND' => null, // required
        'VRUNITVEND' => null, // required
        'VRDESITVEND' => null,
        'VRACRITVEND' => null,
        'IDSITUITEM' => null, // required
        'IDTIPOITEM' => null,
        'IDTIPOCOMPPROD' => null, // required
        'IDIMPPRODUTO' => null, // required
        'TXPRODCOMVEN' => null,
        'CDSUPERVISOR' => null,
        'DSOBSITEMVENDA' => null,
        'DSOBSPEDDIGITA' => null,
        'OBSERVACOES' => array(),
        'CDGRPOCOR' => null,
        'itensCombo' => array()
    );

    const MODEL_TRANSACTION = array(
        'status' => false,
        'data' => array(
            'IDTPTEF' => null,
            'CDNSUHOSTTEF' => null,
            'CDBANCARTCR' => null,
            'STLPRIVIA' => null,
            'STLSEGVIA' => null,
            'PAYMENTCONFIRMATION' => false,
            'REMOVEALLINTEGRATIONS' => false,
            'CDLOJAESITEFFIL' => null,
            'NRCONTROLTEF' => null,
            'DSENDIPSITEF' => null,
            'CDLOJATEF' => null,
            'CDTERTEF' => null
        )
    );

    const MODEL_TIPORECE = array(
        'CDTIPORECE' => null, // required
        'IDTIPORECE' => null, // required
        'VRMOVIVEND' => null, // required
        'TRANSACTION' => self::MODEL_TRANSACTION,
        'IDTPTEF' => null,
        'CDNSUHOSTTEF' => null,
        'CDBANCARTCR' => null,
        'STLPRIVIA' => null,
        'STLSEGVIA' => null,
        'PAYMENTCONFIRMATION' => false,
        'REMOVEALLINTEGRATIONS' => false,
        'CDLOJAESITEFFIL' => null,
        'NRCONTROLTEF' => null,
        'DSENDIPSITEF' => null,
        'CDLOJATEF' => null,
        'CDTERTEF' => null
    );

    public function testVendaBalcaoNFCEComParametrosOpcionais() {

        $ITEMVENDA = array();
        $item = self::MODEL_ITEMVENDA;
        $item['NMPRODUTO'] = 'PEPSI 2 L';
        $item['CDPRODUTO'] = '9010100100';
        $item['QTPRODVEND'] = 1;
        $item['VRUNITVEND'] = 7.5;
        $item['IDSITUITEM'] = 'A';
        $item['IDTIPOCOMPPROD'] = '0';
        $item['IDIMPPRODUTO'] = '1';
        array_push($ITEMVENDA, $item);

        $TRANSACTION = self::MODEL_TRANSACTION;

        $TIPORECE = array();
        $recebimento = self::MODEL_TIPORECE;
        $recebimento['CDTIPORECE'] = "001";
        $recebimento['IDTIPORECE'] = "4";
        $recebimento['VRMOVIVEND'] = 7.5;
        $recebimento['TRANSACTION'] = $TRANSACTION;
        $recebimento['PAYMENTCONFIRMATION'] = false;
        $recebimento['REMOVEALLINTEGRATIONS'] = false;
        array_push($TIPORECE, $recebimento);

        $venda = self::MODEL_VENDA;
        $venda['NRORG'] = 1;
        $venda['CDFILIAL'] = '0004';
        $venda['CDLOJA'] = '1';
        $venda['CDCAIXA'] = '001';
        $venda['CDVENDEDOR'] = '5881';
        $venda['CDOPERADOR'] = '000000005881';
        $venda['DTABERCAIX'] = new \DateTime('2018-01-08T15:45:27');
        $venda['DTVENDA'] = new \DateTime();
        $venda['IDTPEMISSAOFOS'] = 'N';
        $venda['TOTALVENDA'] = 7.5;
        $venda['ITEMVENDA'] = $ITEMVENDA;
        $venda['TIPORECE'] = $TIPORECE;
        $venda['simulaImpressao'] = false;
        $venda['simulateSaleValidation'] = false;
        $venda['IDORIGEMVENDA'] = 'BAL_FOS';

        $this->connectionMock->setFunctionMock(self::CLASS_TO_TEST, "vendaBalcaoNFCEComParametrosOpcionais", true);
        $result = $this->testClass->venda(
            $venda['NRORG'],
            $venda['CDFILIAL'],
            $venda['CDLOJA'],
            $venda['CDCAIXA'],
            $venda['CDVENDEDOR'],
            $venda['CDOPERADOR'],
            $venda['DTABERCAIX'],
            $venda['DTVENDA'],
            $venda['TOTALVENDA'],
            $venda['ITEMVENDA'],
            $venda['TIPORECE'],
            $venda['NMCONSVEND'],
            $venda['NRINSCRCONS'],
            $venda['CDSENHAPED'],
            $venda['NRSEQVENDA'],
            $venda['VRTROCOVEND'],
            $venda['EMAIL'],
            $venda['VRDESCVENDA'],
            $venda['CDCLIENTE'],
            $venda['CDCONSUMIDOR'],
            $venda['simulaImpressao'],
            $venda['simulateSaleValidation'],
            $venda['fechamento'],
            $venda['dadosMesa'],
            $venda['arrayPosicoes'],
            $venda['IDORIGEMVENDA']
        );
        $this->connectionMock->saveRecording();
        $this->assertFalse($result['error']);
    }

    public function testVendaBalcaoNFCESemParametrosOpcionais() {

        $ITEMVENDA = array();
        $item = array();
        $item['CDPRODUTO'] = '9010100100';
        $item['QTPRODVEND'] = 1;
        $item['VRUNITVEND'] = 7.5;
        array_push($ITEMVENDA, $item);

        $TIPORECE = array();
        $recebimento = array();
        $recebimento['CDTIPORECE'] = "001";
        $recebimento['VRMOVIVEND'] = 7.5;
        array_push($TIPORECE, $recebimento);

        $venda = self::MODEL_VENDA;
        $venda['NRORG'] = 1;
        $venda['CDFILIAL'] = '0004';
        $venda['CDLOJA'] = '1';
        $venda['CDCAIXA'] = '001';
        $venda['CDVENDEDOR'] = '5881';
        $venda['CDOPERADOR'] = '000000005881';
        $venda['DTABERCAIX'] = new \DateTime('2018-01-08T15:45:27');
        $venda['DTVENDA'] = new \DateTime();
        $venda['TOTALVENDA'] = 7.5;
        $venda['ITEMVENDA'] = $ITEMVENDA;
        $venda['TIPORECE'] = $TIPORECE;
        $venda['IDORIGEMVENDA'] = 'BAL_FOS';
        
        $this->connectionMock->setFunctionMock(self::CLASS_TO_TEST, "vendaBalcaoNFCESemParametrosOpcionais", true);
        $result = $this->testClass->venda(
            $venda['NRORG'],
            $venda['CDFILIAL'],
            $venda['CDLOJA'],
            $venda['CDCAIXA'],
            $venda['CDVENDEDOR'],
            $venda['CDOPERADOR'],
            $venda['DTABERCAIX'],
            $venda['DTVENDA'],
            $venda['TOTALVENDA'],
            $venda['ITEMVENDA'],
            $venda['TIPORECE'],
            $venda['NMCONSVEND'],
            $venda['NRINSCRCONS'],
            $venda['NRSEQVENDA'],
            $venda['VRTROCOVEND'],
            $venda['EMAIL'],
            $venda['VRDESCVENDA'],
            $venda['CDCLIENTE'],
            $venda['CDCONSUMIDOR'],
            $venda['simulaImpressao'],
            $venda['simulateSaleValidation'],
            $venda['IDORIGEMVENDA']
        );
        $this->connectionMock->saveRecording();
        $this->assertFalse($result['error']);
    }
    public function testVendaBalcaoPromocaoInteligente() {
        //echo 'testa venda balcao passando promocao inteligente e pagando com debito';
        $ITEMVENDA = array(
          array(
            "NMPRODUTO" => "Combo Cheeseburger Madero",
            "CDPRODUTO" => "1010100100",
            "QTPRODVEND" =>  1,
            "VRUNITVEND" =>  35.00,
            "VRDESITVEND" => 0,
            "VRACRITVEND" => 0,
            "IDSITUITEM" => "A",
            "IDTIPOITEM" => NULL,
            "IDTIPOCOMPPROD" => "0",
            "IDIMPPRODUTO" => "2",
            "TXPRODCOMVEN" => "",
            "CDSUPERVISOR" => NULL,
            "DSOBSITEMVENDA" => "",
            "DSOBSPEDDIGITA" => NULL,
            "OBSERVACOES" => array(),
            "CDGRPOCOR" => NULL,
            "itensCombo" => array(
              array(
                "CDPRODUTO" => "1010100100",
                "NMPRODUTO" => "CARNE BOVINA  KG",
                "QTPRODVEND" => 1,
                "VRUNITVEND" => 35.00,
                "VRDESITVEND" => 5.00,
                "VRACRITVEND" => 0,
                "IDSITUITEM" => "A",
                "IDTIPOITEM" => NULL,
                "OBSERVACOES" => array(),
                "IDAPLICADESCPR" => "T",
                "IDPERVALORDES" => "V",
                "CDGRPOCOR" => NULL,
              ),
              array(
                "CDPRODUTO" => "1010200100",
                "NMPRODUTO" => "CARNE SUINA KG",
                "QTPRODVEND" => 1,
                "VRUNITVEND" => 6.00,
                "VRDESITVEND" => 1.00,
                "VRACRITVEND" => 0,
                "IDSITUITEM" => "A",
                "IDTIPOITEM" => NULL,
                "OBSERVACOES" => array(),
                "IDAPLICADESCPR" => "T",
                "IDPERVALORDES" => "V",
                "CDGRPOCOR" => NULL,
              )
            )
          )
        );

        $TRANSACTION = self::MODEL_TRANSACTION;

        $TIPORECE = array(
        array(
            "CDTIPORECE" => "001",
            "IDTIPORECE" => "1",
            "VRMOVIVEND" => 35,
            "TRANSACTION" =>
            array(
              "status" => false,
              "data" => array(
                "IDTPTEF" => NULL,
                "CDNSUHOSTTEF" => NULL,
                "CDBANCARTCR" => NULL,
                "STLPRIVIA" => "",
                "STLSEGVIA" => "",
                "PAYMENTCONFIRMATION" => false,
                "REMOVEALLINTEGRATIONS" => false,
                "CDLOJAESITEFFIL" => NULL,
                "NRCONTROLTEF" => NULL,
                "DSENDIPSITEF" => "",
                "CDLOJATEF" => NULL,
                "CDTERTEF" => NULL,
              )
            ),
            "IDTPTEF" => NULL,
            "CDNSUHOSTTEF" => NULL,
            "CDBANCARTCR" => NULL,
            "STLPRIVIA" => "",
            "STLSEGVIA" => "",
            "PAYMENTCONFIRMATION" => false,
            "REMOVEALLINTEGRATIONS" => false,
            "CDLOJAESITEFFIL" => NULL,
            "NRCONTROLTEF" => NULL,
            "DSENDIPSITEF" => "",
            "CDLOJATEF" => NULL,
            "CDTERTEF" => NULL,
          )
        );
        $venda = self::MODEL_VENDA;
        $venda['NRORG'] = 1;
        $venda['CDFILIAL'] = '0004';
        $venda['CDLOJA'] = '1';
        $venda['CDCAIXA'] = '001';
        $venda['CDVENDEDOR'] = '5881';
        $venda['CDOPERADOR'] = '000000005881';
        $venda['DTABERCAIX'] = new \DateTime('2018-01-11T15:45:27');
        $venda['DTVENDA'] = new \DateTime();
        $venda['IDTPEMISSAOFOS'] = 'N';
        $venda['TOTALVENDA'] = 35.00;
        $venda['ITEMVENDA'] = $ITEMVENDA;
        $venda['TIPORECE'] = $TIPORECE;
        $venda['simulaImpressao'] = false;
        $venda['simulateSaleValidation'] = false;
        $venda['IDORIGEMVENDA'] = 'BAL_FOS';

        $this->connectionMock->setFunctionMock(self::CLASS_TO_TEST, "vendaBalcaoPromocaoInteligente", true);
        $result = $this->testClass->venda(
            $venda['NRORG'],
            $venda['CDFILIAL'],
            $venda['CDLOJA'],
            $venda['CDCAIXA'],
            $venda['CDVENDEDOR'],
            $venda['CDOPERADOR'],
            $venda['DTABERCAIX'],
            $venda['DTVENDA'],
            $venda['TOTALVENDA'],
            $venda['ITEMVENDA'],
            $venda['TIPORECE'],
            $venda['NMCONSVEND'],
            $venda['NRINSCRCONS'],
            $venda['NRSEQVENDA'],
            $venda['VRTROCOVEND'],
            $venda['EMAIL'],
            $venda['VRDESCVENDA'],
            $venda['CDCLIENTE'],
            $venda['CDCONSUMIDOR'],
            $venda['simulaImpressao'],
            $venda['simulateSaleValidation'],
            $venda['IDORIGEMVENDA']
        );
        $this->connectionMock->saveRecording();
        $this->assertFalse($result['error']);
    }

    public function testVendaBalcaoNFCEPromoCombinada() {

        $ITEMVENDA = array();
        $item = self::MODEL_ITEMVENDA;
        $item['CDPRODUTO'] = '9130300100';
        $item['QTPRODVEND'] = 1;
        $item['VRUNITVEND'] = 0.01;
        $item['IDSITUITEM'] = 'A';
        $item['IDTIPOCOMPPROD'] = '0';
        $item['IDIMPPRODUTO'] = "2";
        //I recomend to use pretty JSON on sublime if you have to deal whit this parameter.
        $item['itensCombo'] = json_decode('[{"CDPRODUTO":"9052000102","NMPRODUTO":"CALABRESA G","QTPRODVEND":1,"VRUNITVEND":63.9,"VRDESITVEND":27.02,"VRACRITVEND":0,"IDSITUITEM":"A","IDTIPOITEM":null,"OBSERVACOES":[],"IDAPLICADESCPR":"T","IDPERVALORDES":"V","CDGRPOCOR":null},{"CDPRODUTO":"9050200102","NMPRODUTO":"PZ TD GRANDE 1/2 A 1/2","QTPRODVEND":1,"VRUNITVEND":0.02,"VRDESITVEND":0,"VRACRITVEND":0,"IDSITUITEM":"A","IDTIPOITEM":null,"OBSERVACOES":[],"IDAPLICADESCPR":"T","IDPERVALORDES":"P","CDGRPOCOR":null}]', true);

        array_push($ITEMVENDA, $item);

        $TRANSACTION = self::MODEL_TRANSACTION;

        $TIPORECE = array();
        $recebimento = self::MODEL_TIPORECE;
        $recebimento['CDTIPORECE'] = "001";
        $recebimento['IDTIPORECE'] = "4";
        $recebimento['VRMOVIVEND'] = 0.01;
        $recebimento['TRANSACTION'] = $TRANSACTION;
        $recebimento['PAYMENTCONFIRMATION'] = false;
        $recebimento['REMOVEALLINTEGRATIONS'] = false;
        array_push($TIPORECE, $recebimento);

        $venda = self::MODEL_VENDA;
        $venda['NRORG'] = 1;
        $venda['CDFILIAL'] = '0004';
        $venda['CDLOJA'] = '1';
        $venda['CDCAIXA'] = '001';
        $venda['CDVENDEDOR'] = '5881';
        $venda['CDOPERADOR'] = '000000005881';
        $venda['DTABERCAIX'] = new \DateTime('2018-01-08T15:45:27');
        $venda['DTVENDA'] = new \DateTime();
        $venda['IDTPEMISSAOFOS'] = 'N';
        $venda['TOTALVENDA'] = 0.01;
        $venda['ITEMVENDA'] = $ITEMVENDA;
        $venda['TIPORECE'] = $TIPORECE;
        $venda['simulaImpressao'] = false;
        $venda['simulateSaleValidation'] = false;
        $venda['IDORIGEMVENDA'] = 'BAL_FOS';

        $this->connectionMock->setFunctionMock(self::CLASS_TO_TEST, "vendaBalcaoNFCEPromoCombinada", true);
        $result = $this->testClass->venda(
            $venda['NRORG'],
            $venda['CDFILIAL'],
            $venda['CDLOJA'],
            $venda['CDCAIXA'],
            $venda['CDVENDEDOR'],
            $venda['CDOPERADOR'],
            $venda['DTABERCAIX'],
            $venda['DTVENDA'],
            $venda['TOTALVENDA'],
            $venda['ITEMVENDA'],
            $venda['TIPORECE'],
            $venda['NMCONSVEND'],
            $venda['NRINSCRCONS'],
            $venda['CDSENHAPED'],
            $venda['NRSEQVENDA'],
            $venda['VRTROCOVEND'],
            $venda['EMAIL'],
            $venda['VRDESCVENDA'],
            $venda['CDCLIENTE'],
            $venda['CDCONSUMIDOR'],
            $venda['simulaImpressao'],
            $venda['simulateSaleValidation'],
            $venda['fechamento'],
            $venda['dadosMesa'],
            $venda['arrayPosicoes'],
            $venda['IDORIGEMVENDA']
        );
        $this->connectionMock->saveRecording();
        $this->assertFalse($result['error']);
    }

    public function testVendaBalcaoNFCEDesconto() {

        $ITEMVENDA = array();
        $item = self::MODEL_ITEMVENDA;
        $item['NMPRODUTO'] = 'PEPSI 2 L';
        $item['CDPRODUTO'] = '9010100100';
        $item['QTPRODVEND'] = 1;
        $item['VRUNITVEND'] = 6.5;
        $item['IDSITUITEM'] = 'A';
        $item['IDTIPOCOMPPROD'] = '0';
        $item['IDIMPPRODUTO'] = '1';
        array_push($ITEMVENDA, $item);

        $TRANSACTION = self::MODEL_TRANSACTION;

        $TIPORECE = array();
        $recebimento = self::MODEL_TIPORECE;
        $recebimento['CDTIPORECE'] = "001";
        $recebimento['VRMOVIVEND'] = 6.5;
        $recebimento['TRANSACTION'] = $TRANSACTION;
        $recebimento['PAYMENTCONFIRMATION'] = false;
        $recebimento['REMOVEALLINTEGRATIONS'] = false;
        array_push($TIPORECE, $recebimento);

        $venda = self::MODEL_VENDA;
        $venda['NRORG'] = 1;
        $venda['CDFILIAL'] = '0004';
        $venda['CDLOJA'] = '1';
        $venda['CDCAIXA'] = '001';
        $venda['CDVENDEDOR'] = '0521';
        $venda['CDOPERADOR'] = '000000000521';
        $venda['DTABERCAIX'] = new \DateTime('2018-01-08T15:45:27');
        $venda['DTVENDA'] = new \DateTime();
        $venda['IDTPEMISSAOFOS'] = 'N';
        $venda['TOTALVENDA'] = 6.5;
        $venda['ITEMVENDA'] = $ITEMVENDA;
        $venda['TIPORECE'] = $TIPORECE;
        $venda['simulaImpressao'] = false;
        $venda['simulateSaleValidation'] = false;
        $venda['IDORIGEMVENDA'] = 'BAL_FOS';
        $venda['VRDESCVENDA'] = 1;

        $this->connectionMock->setFunctionMock(self::CLASS_TO_TEST, "vendaBalcaoNFCEDesconto", true);
        $result = $this->testClass->venda(
            $venda['NRORG'],
            $venda['CDFILIAL'],
            $venda['CDLOJA'],
            $venda['CDCAIXA'],
            $venda['CDVENDEDOR'],
            $venda['CDOPERADOR'],
            $venda['DTABERCAIX'],
            $venda['DTVENDA'],
            $venda['TOTALVENDA'],
            $venda['ITEMVENDA'],
            $venda['TIPORECE'],
            $venda['NMCONSVEND'],
            $venda['NRINSCRCONS'],
            $venda['NRSEQVENDA'],
            $venda['VRTROCOVEND'],
            $venda['EMAIL'],
            $venda['VRDESCVENDA'],
            $venda['CDCLIENTE'],
            $venda['CDCONSUMIDOR'],
            $venda['simulaImpressao'],
            $venda['simulateSaleValidation'],
            $venda['IDORIGEMVENDA']
        );
        $this->connectionMock->saveRecording();
        $this->assertFalse($result['error']);
    }
    public function testVendaMesaNFCESemParametrosOpcionais() {

        $TIPORECE = array();
        $recebimento = array();
        $recebimento['CDTIPORECE'] = "001";
        $recebimento['VRMOVIVEND'] = 7.5;
        array_push($TIPORECE, $recebimento);

        $venda = self::MODEL_VENDA;
        $venda['NRORG'] = 1;
        $venda['CDFILIAL'] = '0004';
        $venda['CDLOJA'] = '1';
        $venda['CDCAIXA'] = '001';
        $venda['CDVENDEDOR'] = '5881';
        $venda['CDOPERADOR'] = '000000005881';
        $venda['DTABERCAIX'] = new \DateTime('2018-01-08T15:45:27');
        $venda['DTVENDA'] = new \DateTime();
        $venda['TIPORECE'] = $TIPORECE;
        $venda['IDORIGEMVENDA'] = 'MES_PKR';
        $venda['dadosMesa'] = array(array(
            'NRVENDAREST' => '0000000227',
            'NRCOMANDA' => '0000000227',
            'NRMESA' => '0010',
            'NRPESMESAVEN' => NULL,
            'DSCOMANDA' => NULL
        ));
        $venda['arrayPosicoes'] = array();
        $venda['VRTXSEVENDA'] = null;

        $this->connectionMock->setFunctionMock(self::CLASS_TO_TEST, "vendaMesaNFCESemParametrosOpcionais", true);
        //$this->connectionMock->ignoreChangesToDatabase();
        $result = $this->testClass->vendaMesa(
            $venda['NRORG'],
            $venda['CDFILIAL'],
            $venda['CDLOJA'],
            $venda['CDCAIXA'],
            $venda['CDVENDEDOR'],
            $venda['CDOPERADOR'],
            $venda['DTABERCAIX'],
            $venda['DTVENDA'],
            $venda['TIPORECE'],
            $venda['NMCONSVEND'],
            $venda['NRINSCRCONS'],
            $venda['CDSENHAPED'],
            $venda['VRTROCOVEND'],
            $venda['EMAIL'],
            $venda['VRDESCVENDA'],
            $venda['CDCLIENTE'],
            $venda['CDCONSUMIDOR'],
            $venda['simulaImpressao'],
            $venda['simulateSaleValidation'],
            $venda['dadosMesa'],
		    $venda['arrayPosicoes'],
            $venda['IDORIGEMVENDA'],
            $venda['VRTXSEVENDA']
        );
        $this->connectionMock->saveRecording();
        $this->assertFalse($result['error']);
    }

    public function testVendaMesaTroco() {
        $TIPORECE = array();
        $recebimento = array();
        $recebimento['CDTIPORECE'] = "001";
        $recebimento['VRMOVIVEND'] = 20;
        array_push($TIPORECE, $recebimento);


        $venda = self::MODEL_VENDA;
        $venda['TIPORECE'] = $TIPORECE;
        $venda['NRORG'] = 1;
        $venda['CDFILIAL'] = '0004';
        $venda['CDLOJA'] = '1';
        $venda['CDCAIXA'] = '003';
        $venda['CDVENDEDOR'] = '5881';
        $venda['CDOPERADOR'] = '000000005881';
        $venda['DTABERCAIX'] = new \DateTime('2018-01-16 11:32:28');
        $venda['DTVENDA'] = new \DateTime();
        $venda['IDORIGEMVENDA'] = 'MES_PKR';
        $venda['dadosMesa'] = array(array(
            "CDFILIAL"=> "0004",
            "NRVENDAREST"=> "0000000229",
            "NRCOMANDA"=> "0000000229",
            "NRMESA"=> "0010",
            "NRORG"=> 1,
            "NRPESMESAVEN" => NULL,
            "DSCOMANDA" => NULL
        ));
        $venda['VRTROCOVEND'] = array(
            'CDTIPORECE' => "001" ,
            "VRMOVIVEND"=> 11.75
        );
        $venda['VRTXSEVENDA'] = null;

        $this->connectionMock->setFunctionMock(self::CLASS_TO_TEST, "vendaMesaTroco", true);
        $result = $this->testClass->vendaMesa(
            $venda['NRORG'],
            $venda['CDFILIAL'],
            $venda['CDLOJA'],
            $venda['CDCAIXA'],
            $venda['CDVENDEDOR'],
            $venda['CDOPERADOR'],
            $venda['DTABERCAIX'],
            $venda['DTVENDA'],
            $venda['TIPORECE'],
            $venda['NMCONSVEND'],
            $venda['NRINSCRCONS'],
            $venda['CDSENHAPED'],
            $venda['VRTROCOVEND'],
            $venda['EMAIL'],
            $venda['VRDESCVENDA'],
            $venda['CDCLIENTE'],
            $venda['CDCONSUMIDOR'],
            $venda['simulaImpressao'],
            $venda['simulateSaleValidation'],
            $venda['dadosMesa'],
            $venda['arrayPosicoes'],
            $venda['IDORIGEMVENDA'],
            $venda['VRTXSEVENDA']
        );
        $this->connectionMock->saveRecording();
        $this->assertFalse($result['error']);
    }

    public function testVendaMesaDescontoItems() {
        $TIPORECE = array();
        $recebimento = array();
        $recebimento['CDTIPORECE'] = "001";

        $recebimento['VRMOVIVEND'] = 0.02;
        array_push($TIPORECE, $recebimento);
        $venda = self::MODEL_VENDA;
        $venda['NRORG'] = 1;
        $venda['CDFILIAL'] = '0004';
        $venda['CDLOJA'] = '1';
        $venda['CDCAIXA'] = '003';
        $venda['CDVENDEDOR'] = '5881';
        $venda['CDOPERADOR'] = '000000005881';
        $venda['DTABERCAIX'] = new \DateTime('2018-01-16T11:32:28');
        $venda['DTVENDA'] = new \DateTime();
        $venda['TIPORECE'] = $TIPORECE;
        $venda['IDORIGEMVENDA'] = 'MES_PKR';
        $venda['dadosMesa'] = array(array(
            "CDFILIAL"=> "0004",
            "NRVENDAREST"=> "0000000227",
            "NRCOMANDA"=> "0000000227",
            "NRMESA"=> "0013",
            "NRORG"=> 1,
            "NRPESMESAVEN" => NULL,
            "DSCOMANDA" => NULL
        ));
        $venda['VRTXSEVENDA'] = null;

        $this->connectionMock->setFunctionMock(self::CLASS_TO_TEST, "vendaMesaDescontoItems", true);
        //$this->connectionMock->ignoreChangesToDatabase();
        $result = $this->testClass->vendaMesa(
            $venda['NRORG'],
            $venda['CDFILIAL'],
            $venda['CDLOJA'],
            $venda['CDCAIXA'],
            $venda['CDVENDEDOR'],
            $venda['CDOPERADOR'],
            $venda['DTABERCAIX'],
            $venda['DTVENDA'],
            $venda['TIPORECE'],
            $venda['NMCONSVEND'],
            $venda['NRINSCRCONS'],
            $venda['CDSENHAPED'],
            $venda['VRTROCOVEND'],
            $venda['EMAIL'],
            $venda['VRDESCVENDA'],
            $venda['CDCLIENTE'],
            $venda['CDCONSUMIDOR'],
            $venda['simulaImpressao'],
            $venda['simulateSaleValidation'],
            $venda['dadosMesa'],
            $venda['arrayPosicoes'],
            $venda['IDORIGEMVENDA'],
            $venda['VRTXSEVENDA']
        );
        $this->connectionMock->saveRecording();
        $this->assertFalse($result['error']);
    }

    public function testVendaMesaPosicao() {
        $TIPORECE = array();
        $recebimento = array();
        $recebimento['CDTIPORECE'] = "001";

        $recebimento['VRMOVIVEND'] = 8.25;
        array_push($TIPORECE, $recebimento);
        $venda = self::MODEL_VENDA;
        $venda['NRORG'] = 1;
        $venda['CDFILIAL'] = '0004';
        $venda['CDLOJA'] = '1';
        $venda['CDCAIXA'] = '003';
        $venda['CDVENDEDOR'] = '5881';
        $venda['CDOPERADOR'] = '000000005881';
        $venda['DTABERCAIX'] = new \DateTime('2018-01-16T11:32:28');
        $venda['DTVENDA'] = new \DateTime();
        $venda['TIPORECE'] = $TIPORECE;
        $venda['IDORIGEMVENDA'] = 'MES_PKR';
        $venda['dadosMesa'] = array(array(
            "CDFILIAL"=> "0004",
            "NRVENDAREST"=> "0000000218",
            "NRCOMANDA"=> "0000000218",
            "NRMESA"=> "0013",
            "NRORG"=> 1,
            'NRPESMESAVEN' => NULL,
            'DSCOMANDA' => NULL
        ));
        $venda['arrayPosicoes'] = array('01');
        $venda['VRTXSEVENDA'] = null;

        $this->connectionMock->setFunctionMock(self::CLASS_TO_TEST, "vendaMesaPosicao", true);
        //$this->connectionMock->ignoreChangesToDatabase();
        $result = $this->testClass->vendaMesa(
            $venda['NRORG'],
            $venda['CDFILIAL'],
            $venda['CDLOJA'],
            $venda['CDCAIXA'],
            $venda['CDVENDEDOR'],
            $venda['CDOPERADOR'],
            $venda['DTABERCAIX'],
            $venda['DTVENDA'],
            $venda['TIPORECE'],
            $venda['NMCONSVEND'],
            $venda['NRINSCRCONS'],
            $venda['CDSENHAPED'],
            $venda['VRTROCOVEND'],
            $venda['EMAIL'],
            $venda['VRDESCVENDA'],
            $venda['CDCLIENTE'],
            $venda['CDCONSUMIDOR'],
            $venda['simulaImpressao'],
            $venda['simulateSaleValidation'],
            $venda['dadosMesa'],
            $venda['arrayPosicoes'],
            $venda['IDORIGEMVENDA'],
            $venda['VRTXSEVENDA']
        );
        $this->connectionMock->saveRecording();
        $this->assertFalse($result['error']);
    }


}

