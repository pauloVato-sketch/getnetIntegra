<?php
namespace Util;

use Zeedhi\Framework\DTO\Response\Message;
use Zeedhi\Framework\DTO\Response;
use Zeedhi\Framework\DTO\Request;

use Zeedhi\Framework\DB\StoredProcedure\StoredProcedure;
use Zeedhi\Framework\DB\StoredProcedure\Param;

class Util extends \Zeedhi\Framework\Controller\Simple {
	protected $entityManager;
	protected $sessionService;
	protected $session;

    public function __construct(\Doctrine\ORM\EntityManager $entityManager, \Helpers\Environment $sessionService) {
        $this->entityManager = $entityManager;
        $this->sessionService = $sessionService;
    }

    public function getParams ($params) {

        $treatedParams = array();
        foreach ($params as $param) {

            if (!empty($param['value'])) {
                if ($param['value'] === '=') {
                    $param['value'] = '';
                }
            }
            $treatedParams[$param['columnName']] = $param['value'];
        }

        return $treatedParams;
    }

    public function checkVersion($frontVersion){
        $originalPath = __DIR__. '/../../version.json';
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $originalPath);
        $json = file_get_contents($path);
        $configData = json_decode($json, true);
        $backVersion = $configData['backVersion'];

        if ($backVersion === $frontVersion) $versionOk = true;
        else $versionOk = false;

        return array(
            'versionOk' => $versionOk,
            'frontVersion' => $frontVersion,
            'backVersion' => $backVersion
        );
    }

    public function newCode($counterName, $size = '20'){
        $nrseq = null;
        $connection = $this->entityManager->getConnection();
        $storedProcedure = new StoredProcedure($connection, 'NOVO_CODIGO');

        $storedProcedure->addParam(new Param('P_CONTADOR', Param::PARAM_INPUT, $counterName, Param::PARAM_TYPE_STR, strlen($counterName)));
        $storedProcedure->addParam(new Param('P_SEQUENCIAL', Param::PARAM_OUTPUT, $nrseq, Param::PARAM_TYPE_STR, 20));
        $storedProcedure->execute();
    }

    public function intToHexa($number) {
        $number = intval($number);
        $number = dechex($number);
        $number = str_pad($number, 6, '0', STR_PAD_LEFT);
        $b = substr($number, 0, 2);
        $g = substr($number, 2, 2);
        $r = substr($number, 4, 2);
        $number = $r . $g . $b;
        return $number;
    }

    public function strToFloat($strValue) {
        $strValue = str_replace(",", ".", $strValue);
        return (float)$strValue;
    }

    public function testConnection(Request\Filter $request, Response $response) {
        try {
            $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing'))));
        } catch (\Exception $e) {
            Exception::logException($e);
            $response->addMessage(new Message($e->getMessage()));
        }
    }

    public function saveRequests(Request\Filter $request, Response $response) {
        try {
            $params = $request->getFilterCriteria()->getConditions();
            $times  = $params[0]['value'];
            $header = getallheaders();
            $times['userId'] = $header['User-Id'];
            self::log(json_encode($times));
        } catch (\Exception $e) {
            Exception::logException($e);
            $response->addMessage(new Message($e->getMessage()));
        }
    }

    public function criaXMLCatraca($comanda, $evento, $caminho){

$xml = <<<XML
<?xml version="1.0" standalone="yes" ?>
<DATAPACKET Version="2.0">
<METADATA>
<FIELDS>
<FIELD attrname="ID_COMANDA" fieldtype="string" WIDTH="20" />
<FIELD attrname="EVENTO" fieldtype="string" WIDTH="1" />
</FIELDS>
<PARAMS />
</METADATA>
<ROWDATA>
<ROW RowState="4" ID_COMANDA="{$comanda}" EVENTO="{$evento}" />
</ROWDATA>
</DATAPACKET>
XML;

        $dom = new \DOMDocument();
        $dom->loadXML($xml);
        file_put_contents($caminho . '/' . $comanda . ".xml", $dom->saveXML());

    }

    public function getNewCode($CDCONTADOR, $size = 20){
        $params = array(
            ':CDCONTADOR' => $CDCONTADOR
        );
        $code = $this->entityManager->getConnection()->fetchAssoc("SQL_NOVO_CODIGO", $params);
        $code = substr($code['NRSEQUENCIAL'], strlen($code['NRSEQUENCIAL']) - $size);
        return $code;
    }
    const FILLER = "0";

    public function zeroFill($var, $size) {
        return str_pad($var, $size, self::FILLER, STR_PAD_LEFT);
    }

    public function removeAcentos($texto) {
        $table = array('á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a',
            'é' => 'e', 'ê' => 'e', 'í' => 'i', 'ó' => 'o', 'ô' => 'o',
            'õ' => 'o', 'ú' => 'u', 'ü' => 'u', 'ç' => 'c', 'Á' => 'A',
            'À' => 'A', 'Ã' => 'A', 'Â' => 'A', 'É' => 'E', 'Ê' => 'E',
            'Í' => 'I', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ú' => 'U',
            'Ü' => 'U', 'Ç' => 'C_');
        return preg_replace("[^a-zA-Z0-9_]", "", strtr($texto, $table));
    }

    public function addSessionVar($value) {
        $this->sessionService->startSession($value);
    }

    public function setUserInfo($userInfo) {
        $this->sessionService->setUserInfo($userInfo);
    }

    public function getSessionVars($chave) {
        return $this->sessionService->getUserInfo($chave);
    }

    public function endSession() {
        return $this->sessionService->endSession();
    }

    public static function encrypt($text) {
        return md5('odhen'.$text.'7833');
    }

    public function log($info){
        $line = date("d-m-Y H:i:s") . " - " . $info . "\n";
        $date = date("dmY");
        file_put_contents(__DIR__ . '/../../../../CDL/php_log_' . $date . ".txt", $line, FILE_APPEND);
    }

    public function replicate($char, $numero){
        $result = $char;
        for ($i = 1; $i < $numero; ++$i){
            $result .= $char;
        }
        return $result;
    }

    public function toRight($leftText, $rightText, $lineWidth){
        $spaceToFill = $lineWidth - (strlen($leftText) + strlen($rightText));
        $result = $leftText . self::replicate(' ', $spaceToFill);
        $result .= $rightText;
        return $result;
    }

    public function vazio($string){
        if (Empty($string)) {
            $string = ' ';
        }
        return $string;
    }

    public function truncate($val, $f="0") {
        if(($p = strpos($val, '.')) !== false) {
            $val = floatval(substr($val, 0, $p + 1 + $f));
        }
        return $val;
    }

    public function logFOS($filial, $caixa, $idOperFos, $cdOperador, $cdOperRespOp, $dsMotivoFos, $dsLivreFos){

        $params = array(
            $cdOperador
        );

        $answer = $this->entityManager->getConnection()->fetchAssoc("SQL_BUSCA_NOME_OPERADOR", $params);

        if ($cdOperRespOp != "") {

            $params = array(
                $cdOperRespOp
            );

            $response = $this->entityManager->getConnection()->fetchAssoc("SQL_BUSCA_NOME_OPERADOR", $params);

            $nmOperRespOp = $response['NMOPERADOR'];
        } else {
            $cdOperRespOp = $cdOperador;
            $nmOperRespOp = $answer['NMOPERADOR'];
        }

        $params = array(
            $filial,
            $caixa,
            $idOperFos,
            $cdOperador,
            $answer['NMOPERADOR'],
            $cdOperRespOp,
            $nmOperRespOp,
            $dsMotivoFos,
            substr($dsLivreFos, 0, 250)
        );

        $this->entityManager->getConnection()->executeQuery("SQL_INSERE_LOG", $params);
    }

    public function formataPreco($valor){
        return number_format($this->truncate($valor, 2), 2, ",", "");
    }

    public function getNextUpdateTime($updateTimes){
        $date = date_create_from_format('H:i', date("H:i"));
        $currentTime = strtotime($date->format('Y-m-d H:i'));

        $nextUpdateTime = null;
        foreach ($updateTimes as $updateTime){
            if ($currentTime < $updateTime){
                $nextUpdateTime = $updateTime;
                break;
            }
        }

        return array(
            'nextUpdateTime' => !empty($nextUpdateTime) ? $nextUpdateTime : strtotime($date->format('Y-m-d 23:59')) + 60
        );
    }


    public function adjustDifference(&$array, $difference, $type, $propDesconto){
        $apportionmentAmount = intval($difference / 0.01);
        $i = 0;
        while ($apportionmentAmount > 0) {
            $apportionmentAmount = self::changeValue($array, $apportionmentAmount, $type, $propDesconto);
            $i++;
            if ($i == 1000) throw new \Exception("Erro no rateio do desconto/gorjeta.");
        }
    }

    private function changeValue(&$array, $apportionmentAmount, $type, $propDesconto){
        foreach ($array as &$arrayValue) {
            if ($type == 'prodObject'){
                $vrProd = self::calculaTotalItem($arrayValue);
                if ($vrProd > 0.01){
                    $arrayValue[$propDesconto] += 0.01;
                }
            } else {
                $arrayValue += 0.01;
            }
            $apportionmentAmount--;
            if ($apportionmentAmount == 0) {
                break;
            }
        }
        return $apportionmentAmount;
    }

    public function calculaTotalItem($produtoAtual){
        if (isset($produtoAtual['VRDESITVEND'])){
            return round(floatval(bcmul(str_replace(',','.',strval($produtoAtual['VRUNITVEND'] + $produtoAtual['VRUNITVENDCL'])), str_replace(',','.',strval($produtoAtual['QTPRODVEND'])), '2')) + $produtoAtual['VRACRITVEND'] - $produtoAtual['VRDESITVEND'], 2);
        } else {
            return round(floatval(bcmul(str_replace(',','.',strval($produtoAtual['VRPRECCOMVEN'] + $produtoAtual['VRPRECCLCOMVEN'])), str_replace(',','.',strval($produtoAtual['QTPRODCOMVEN'])), '2')) + $produtoAtual['VRACRCOMVEN'] - $produtoAtual['VRDESCCOMVEN'], 2);
        }
    }

    // rotina criada para aplicar o desconto da promoção inteira (todo o desconto somado) em certos produtos da promoção
    // colocada no Util pois modo Mesa e Balcão compartilha desta rotina em diferentes pontos
    public function validaDescontoDiferenciado($CDFILIAL, $CDPRODPROMOCAO, &$composicao, $propDesconto){
        $descontoAplicado = false;
        $params = array(
            'CDFILIAL' => $CDFILIAL,
            'CDPRODPROMOCAO' => $CDPRODPROMOCAO
        );
        $prodPromocao = $this->entityManager->getConnection()->fetchAll("SQL_PROMOCAO_APLICADESCFIL", $params);
        if (empty($prodPromocao)){
            $prodPromocao = $this->entityManager->getConnection()->fetchAll("SQL_PROMOCAO_APLICADESC", $params);
        }

        // valida se a promoção controla desconto diferenciado
        if (!empty($prodPromocao)){
            $prodPromocao = array_unique(array_column($prodPromocao, 'CDPRODUTO'));

            // valida se algum produto pedido está para ter desconto diferenciado
            if (array_intersect(array_column($composicao, 'CDPRODUTO'), $prodPromocao)){
                $prodComDesconto = $prodSemDesconto = array();
                foreach ($composicao as $produto) {
                    if (in_array($produto['CDPRODUTO'], $prodPromocao)){
                        $prodComDesconto[] = $produto;
                    } else {
                        $prodSemDesconto[] = $produto;      
                    }
                }
                // valida se o desconto já não está rateado
                if (!empty($prodSemDesconto)){
                    $descontoAplicado = $this->aplicaDescontoDiferenciado($prodComDesconto, $prodSemDesconto, $propDesconto);
                    if ($descontoAplicado){
                        // merge dos produtos modificados
                        $composicao = array_merge($prodComDesconto, $prodSemDesconto);                      
                    }
                }
            }
        }

        return $descontoAplicado;
    }

    private function aplicaDescontoDiferenciado(&$prodComDesconto, &$prodSemDesconto, $propDesconto){
        $valorDesconto = 0;
        foreach ($prodSemDesconto as $produto) {
            $valorDesconto += $produto[$propDesconto];
        }
        
        $vrProdComDesc = 0;
        foreach ($prodComDesconto as $produto) {
            $vrProdComDesc += round($this->calculaTotalItem($produto) - 0.01, 2);
        }

        // valida se existe desconto a ser rateado e se o valor do desconto é menor que o valor permitido para rateio 
        if ($valorDesconto > 0 && $vrProdComDesc >= $valorDesconto){
            foreach ($prodSemDesconto as &$produto) {
                $produto[$propDesconto] = 0;
            }

            $porcVrDesc = $valorDesconto / $vrProdComDesc;
            $descontoAplicado = 0;

            // aplica rateio desconto nos produtos
            foreach ($prodComDesconto as &$produto) {
                $descPorProd = floatval(bcmul(str_replace(',','.',strval($porcVrDesc)), str_replace(',','.',strval($this->calculaTotalItem($produto))), '2'));

                $produto[$propDesconto] += $descPorProd;
                $descontoAplicado += $descPorProd;
            }
            if ($descontoAplicado < $valorDesconto){
                // ajusta casas decimais
                $this->adjustDifference($prodComDesconto, round($valorDesconto - $descontoAplicado, 2), 'prodObject', $propDesconto);
            }

            return true;
        } else {
            return false;
        }
    }

}