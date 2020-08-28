<?php
namespace Odhen\API\Util;

use Zeedhi\Framework\DTO\Response\Message;
use Zeedhi\Framework\DTO\Response;
use Zeedhi\Framework\DTO\Request;
use Doctrine\DBAL\Connection;
use Setforms2\Runtime\Helper\Procedure;
use Zeedhi\Framework\DB\StoredProcedure\Param;
use Zeedhi\Framework\DB\StoredProcedure\StoredProcedure;
use Zend\Mail\Message as ZendMessage;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Mime as Mime;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;
use Zend\Mail\Protocol\Smtp as SmtpProtocol;

class Util extends \Zeedhi\Framework\Controller\Simple {

    protected $entityManager;
    protected $instanceManager;
    protected $tipoBanco;

    public function __construct(\Doctrine\ORM\EntityManager $entityManager, \Zeedhi\Framework\DependencyInjection\InstanceManager $instanceManager) {
        $this->entityManager   = $entityManager;
        $this->instanceManager = $instanceManager;
        $this->tipoBanco       = $this->instanceManager->getParameter('connection_params')['driver'];
    }

    const ORACLE = 'oci8';

    public function logImpressao($info) {
        $systemPath = $this->instanceManager->getParameter('SYSTEM_PATH');
        //testa se a pasta existe e cria caso não exista
        $folder = $systemPath . "/api/logs/";
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }
        //--grava o log
        $line = date("d-m-Y H:i:s") . " - " . $info . "\n";
        $date = date("dmY");
        file_put_contents($systemPath . "/api/logs/logImpressao" . $date . ".txt", $line, FILE_APPEND);
    }

    public function testConnection(Request\Filter $request, Response $response) {
        try {
            $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('TestConnection', array(array('nothing' => 'nothing'))));
        } catch (\Exception $e) {
            $response->addMessage(new Message($e->getMessage()));
        }
    }

    public function httpRequest($URL, $params) {
        try {
            $paramsString = json_encode($params);
            $header = array(
                'Content-type: application/json; charset=utf-8'
            );

            $cURL = curl_init();
            curl_setopt($cURL, CURLOPT_URL, $URL);
            curl_setopt($cURL, CURLOPT_POSTFIELDS, $paramsString);
            curl_setopt($cURL, CURLOPT_HTTPHEADER, $header);
            curl_setopt($cURL, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
            curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($cURL, CURLOPT_TIMEOUT, 60);
            $httpResponse = curl_exec($cURL);
            $jsonResponse = json_decode($httpResponse, true);
            if ($httpResponse != false) {
                $result = array(
                    'error' => false,
                    'response' => $jsonResponse
                );
            } else {
                $result = array(
                    'error' => true,
                    'message' => 'Não foi possível completar a requisição HTTP. Erro: ' . curl_error($cURL)
                );
            }
        } catch (\Exception $e) {
            $result = array(
                'error' => true,
                'message' => 'Erro ao comunicar com o endereço: ' . $URL . '. Erro: ' . $e->getMessage()
            );
        }
        return $result;
    }

    public function getXMLParameter($key) {
        return $this->instanceManager->getParameter($key);
    }

    public function sendMail($nome_destinatario, $email_destinatario, $mensagem) {
        require_once 'Zend/Mail.php';
        require_once 'Zend/Mail/Transport/Smtp.php';

        $date = date("d-m-Y");
        $drive = substr(__DIR__, 0, 1);

        // @todo - use DB parametrization

        $dominio = self::getXMLParameter('domain');

        $tr = new \Zend_Mail_Transport_Smtp(
            self::getXMLParameter('serverMail'),
            array(
                'auth'     => 'login',
                'username' => self::getXMLParameter('user'),
                'password' => self::getXMLParameter('password')
            )
        );

        \Zend_Mail::setDefaultTransport($tr);

        $mail = new \Zend_Mail('UTF-8');
        $mail->setFrom(self::getXMLParameter('user'), self::getXMLParameter('remetente'));
        $mail->setBodyHtml($mensagem, 'UTF-8');
        $mail->addTo($email_destinatario, $nome_destinatario);
        $mail->setSubject(self::getXMLParameter('assunto'));
        $mail->send();
    }

    public function encrypt($text, $salt = 'setforms') {
        return  trim(
            base64_encode(
                mcrypt_encrypt(
                    MCRYPT_RIJNDAEL_256,
                    $salt,
                    $text,
                    MCRYPT_MODE_ECB,
                    mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)
                )
            )
        );
    }

    public function decrypt($text, $salt = 'setforms') {
        return trim(
            mcrypt_decrypt(
                MCRYPT_RIJNDAEL_256,
                $salt,
                base64_decode($text),
                MCRYPT_MODE_ECB,
                mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)
            )
        );
    }

    public function validaSenha($password, $md5Password) {
        return md5($password) === $md5Password;
    }

    public function delphiValidaSenhaIFM($CDSENHOPER_DIGITADA, $CDSENHOPER, $dllCom) {
        try {
            $dllResponse = $dllCom->ValidaSenha($CDSENHOPER_DIGITADA, $CDSENHOPER);
            return $dllResponse == '1';
        } catch (\Exception $e) {
            throw new \Exception('Nao foi possivel carregar a IFM.dll, certifique-se que ela esta instalada. ' . $e->getMessage());
        }
    }

    public function delphiValidaPers($CDFILIAL, $CDCAIXA) {
        try {

            /* DISABLED FOR NOW (via Daniel). */
            return array('error' => false);

            if ($this->isDisabledCOM()) return array('error' => false);

            $dllCom = new \COM('Ifm.rdmPrint');
            if ($dllCom == '266') {
                $result = array(
                    'error' => true,
                    'message' => 'Nao foi possivel carregar a IFM.dll, certifique-se que ela esta instalada.'
                );
            } else {
                $resultado = $dllCom->ValidaPer($CDFILIAL, $CDCAIXA);
                if ($resultado == null) {
                    $result = array(
                        'error' => true,
                        'message' => 'Nao foi possivel carregar a IFM.dll, certifique-se que ela esta instalada.'
                    );
                } else if ($resultado != '1') {
                    $result = array(
                        'error' => true,
                        'message' => "Caixa não foi encontrado no arquivo de PERS."
                    );
                } else {
                    $result = array(
                        'error' => false
                    );
                }
            }
            return $result;
        } catch (\Exception $e) {
            return array(
                'error' => true,
                'message' => 'Nao foi possivel carregar a IFM.dll, certifique-se que ela esta instalada.'
            );
        }
    }

    const MASTER_PASSWORD = "s5vol0e00";

    public function createFolder($folder) {
        if (!is_dir($folder)) {
            if (mkdir($folder, 0777, true)) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    public function databaseIsOracle() {
        return $this->tipoBanco == self::ORACLE;
    }

    public function geraCodigo($connection, $contador, $NRORG, $qtde, $size, $mockNumber = false) {
        try {
            $params = array(
                'CDCONTADOR' => $contador
            );
            $sequencialAnterior = $connection->fetchAssoc("SQL_BUSCA_CONTADOR", $params);

            if( !method_exists($connection, 'getIsMocking') || (method_exists($connection, 'getIsMocking') && !$connection->getIsMocking())){
                $referencia = "";
                if (self::databaseIsOracle()) {
                    $procedure = new StoredProcedure($connection, 'NOVO_CODIGO_NT');
                }
                else {
                    $procedure = new StoredProcedure($connection, 'NOVO_CODIGO');
                }
                $procedure->addParam(new Param('P_CONTADOR'  , Param::PARAM_INPUT , $contador  , Param::PARAM_TYPE_STR    ));
                $procedure->addParam(new Param('P_SEQUENCIAL', Param::PARAM_OUTPUT, $referencia, Param::PARAM_TYPE_STR, 20));
                if (self::databaseIsOracle()) {
                    $procedure->addParam(new Param('P_QTDE', Param::PARAM_INPUT, $qtde, Param::PARAM_TYPE_INT));
                    $procedure->addParam(new Param('P_NRORG', Param::PARAM_INPUT, $NRORG, Param::PARAM_TYPE_INT));
                }
                $procedure->execute();
            }

            $sequencial = $connection->fetchAssoc("SQL_BUSCA_CONTADOR", $params);

            if (!Empty($sequencialAnterior)) {
                if ($sequencialAnterior['NRSEQUENCIAL'] == $sequencial['NRSEQUENCIAL']) {
                    $sequencial['NRSEQUENCIAL'] = $sequencial['NRSEQUENCIAL'] + 1;
                    $params = array(
                        'CDCONTADOR' => $contador,
                        'NRSEQUENCIAL' => str_pad($sequencial['NRSEQUENCIAL'], 20, '0', STR_PAD_LEFT),
                        'NRORG' => $NRORG
                    );

                    $connection->executeQuery("UPDATE_BUSCA_CONTADOR", $params);
                }
            }

            $intSeq = (int)$sequencial['NRSEQUENCIAL'];
            return str_pad($intSeq, $size, "0", STR_PAD_LEFT);
        } catch(\Exception $e) {
            throw new \Exception('Erro ao executar procedure de novo código.', 1);
        }
    }

    public function updateSaldoCons($CDFILIAL, $CDCLIENTE, $CDCONSUMIDOR, $CDOPERADOR){
        $nrseq = null;
        $connection = $this->entityManager->getConnection();
        $storedProcedure = new StoredProcedure($connection, 'ATUALIZA_SALDO_CONS');

        $date = date("d/m/Y");

        $storedProcedure->addParam(new Param('P_CDFILIAL', Param::PARAM_INPUT, $CDFILIAL, Param::PARAM_TYPE_STR, strlen($CDFILIAL)));
        $storedProcedure->addParam(new Param('P_CDCLIENTE', Param::PARAM_INPUT, $CDCLIENTE, Param::PARAM_TYPE_STR, strlen($CDCLIENTE)));
        $storedProcedure->addParam(new Param('P_CDCONSUMIDOR', Param::PARAM_INPUT, $CDCONSUMIDOR, Param::PARAM_TYPE_STR, strlen($CDCONSUMIDOR)));
        $storedProcedure->addParam(new Param('P_DATA', Param::PARAM_INPUT, $date, Param::PARAM_TYPE_STR, strlen($date)));
        $storedProcedure->addParam(new Param('P_CDOPERADOR', Param::PARAM_INPUT, $CDOPERADOR, Param::PARAM_TYPE_STR, strlen($CDOPERADOR)));
        $storedProcedure->execute();
    }

    /**
     * @param $novocodigo
     * @param $tamanhoColuna
     * @param $numerico
     * @return int|string
     */
    private function preparaValor($novocodigo, $tamanhoColuna, $numerico) {
        /**
         * Valor do "$novoCodigo" vem uma string com "left zero fill" de tamanho 20, eg "00000000000012345678";
         * Por isso o casting para int, e o substr, lembrando de um exemplo onde o tamanho é 10:
         * substr($novocodigo, -1*$tamanhoColuna) => substr("00000000000012345678", -10) => "0012345678"
         * Lembrando que ele vai pegar os 10 ultimos digitos e/ou os 10 digitos menos significativos, que são os
         * 10 digitos a esquerda.
         */
        return $numerico === true ? (int)$novocodigo :  substr($novocodigo, -1*$tamanhoColuna);
    }

    // EMAIL VENDA -------------------------------------------------------------------

    public function sendEmailVenda($email_addressee, $content, $date, $IDTPEMISVEND) {
        // Get data from server
        try {
            $serverUsername =  "Teknisa";
            $subject    = "Cupom Fiscal Eletrônico";

            $name_addressee = "";
            $SMTP          = $this->entityManager->getConnection()->fetchAssoc("GET_SMTP");
            $server        = $SMTP['DSSMTPAUTVND'];
            $username      = $SMTP['DSEMAILAUVND'];
            $password      = $SMTP['CDSENHAAUTVNDWEB'];
            $port          = $SMTP['NRPORTAAUTVND'];
            $encryption    = $SMTP['DSSMTPCRIPT'];
            $autentication = $SMTP['IDTPAUTHSMTP'];

            $type       = "text/html; charset=UTF-8";
            $encoding   = "UTF-8";

            // Body e-mail.
            $allContent = self::generateEmail($subject, $content, $date, $IDTPEMISVEND);
            $html       = new MimePart($allContent);
            $html->type = $type;
            $body       = new MimeMessage();
            $body->setParts(array($html));

            // E-mail config.
            $message = self::setConfigEmail($encoding, $email_addressee, $username, $serverUsername, $subject, $body);

            // E-mail options.
            $options = self::setOptionsEmail($serverUsername, $server, $port, $username, $password, $encryption, $autentication);

            // Send e-mail.
            $transport = new SmtpTransport();
            $transport->setOptions($options);
            $transport->send($message);

            return false;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function generateEmail($subject, $content, $date, $IDTPEMISVEND){
        if ($IDTPEMISVEND == 'N'){
            $link = '<p>Consulte a nota eletrônica no link abaixo:</p>'.
                    '<p style=""><font color="#848999" face="open sans, sans-serif"><br></font></p>'.
                    '<p style="word-break: break-all;"><a href=' . $content['DSQRCODE'] . '>Clique aqui para acessar a nota</a></p>';
        } else {
            $link = '<p>Consulte a nota eletrônica no link abaixo:</p>'.
                    '<p style=""><font color="#848999" face="open sans, sans-serif"><br></font></p>'.
                    '<p style="word-break: break-all;"><a href="https://satsp.fazenda.sp.gov.br/COMSAT/Public/ConsultaPublica/ConsultaPublicaCfe.aspx">Clique aqui para acessar o link</a>' .
                    '<p style=""><font color="#848999" face="open sans, sans-serif"><br></font></p>'.
                    '<p>Chave de Acesso</p><p>' . $content['DSQRCODE'] . '</p>';
        }

        return
            '<!DOCTYPE html>'.
            '<head>'.
            '<meta http-equiv="content-type" content="text/html; charset=UTF-8">'.
            '</head>'.
            '<body>'.
            '<body style="margin: 0px; height: auto; overflow: visible;">'.
                '<div>'.
                    '<div>'.
                        '<div style="font-family: verdana, helvetica, sans-serif; font-size: 10pt; color: rgb(0, 68, 111);">'.
                            '<div>'.
                                '<style>'.
                                    'body {'.
                                        'width: 100%;'.
                                        'background-color: #eeeeee;'.
                                        'margin: 0;'.
                                        'padding: 0;'.
                                        '-webkit-font-smoothing: antialiased;'.
                                        'mso-margin-top-alt: 0px;'.
                                        'mso-margin-bottom-alt: 0px;'.
                                        'mso-padding-alt: 0px 0px 0px 0px;'.
                                    '}'.

                                    'p,'.
                                    'h1,'.
                                    'h2,'.
                                    'h3,'.
                                    'h4 {'.
                                        'margin-top: 0;'.
                                        'margin-bottom: 0;'.
                                        'padding-top: 0;'.
                                        'padding-bottom: 0;'.
                                        'font-family: Open Sans, sans-serif;'.
                                        'text-align: center'.
                                    '}'.

                                    'span.preheader {'.
                                        'display: none;'.
                                        'font-size: 1px;'.
                                    '}'.

                                    'html {'.
                                        'width: 100%;'.
                                    '}'.

                                    'table {'.
                                        'font-size: 14px;'.
                                        'border: 0;'.
                                    '}'.

                                    '@media only screen and (max-width: 640px) {'.
                                        'body[yahoo] .main-section-header {'.
                                            'font-size: 26px !important;'.
                                        '}'.
                                        'body[yahoo] .show {'.
                                            'display: block !important;'.
                                        '}'.
                                        'body[yahoo] .hide {'.
                                            'display: none !important;'.
                                        '}'.
                                        'body[yahoo] .main-image img {'.
                                            'width: 440px !important;'.
                                            'height: auto !important;'.
                                        '}'.
                                        'body[yahoo] .divider img {'.
                                            'width: 440px !important;'.
                                        '}'.
                                        'body[yahoo] .banner img {'.
                                            'width: 440px !important;'.
                                            'height: auto !important;'.
                                        '}'.
                                        'body[yahoo] .container590 {'.
                                            'width: 440px !important;'.
                                        '}'.
                                        'body[yahoo] .container580 {'.
                                            'width: 400px !important;'.
                                        '}'.
                                        'body[yahoo] .half-container590 {'.
                                            'width: 200px !important;'.
                                        '}'.
                                        'body[yahoo] .section-item {'.
                                            'width: 440px !important;'.
                                        '}'.
                                        'body[yahoo] .section-img img {'.
                                            'width: 440px !important;'.
                                            'height: auto !important;'.
                                        '}'.
                                    '}'.

                                    '@media only screen and (max-width: 479px) {'.
                                        'body[yahoo] .main-section-header {'.
                                            'font-size: 22px !important;'.
                                        '}'.
                                        'body[yahoo] .main-image img {'.
                                            'width: 280px !important;'.
                                            'height: auto !important;'.
                                        '}'.
                                        'body[yahoo] .divider {'.
                                            'width: 280px !important;'.
                                        '}'.
                                        'body[yahoo] .align-center {'.
                                            'text-align: center !important;'.
                                        '}'.
                                        'body[yahoo] .banner img {'.
                                            'width: 280px !important;'.
                                            'height: auto !important;'.
                                        '}'.
                                        'body[yahoo] .container590 {'.
                                            'width: 280px !important;'.
                                        '}'.
                                        'body[yahoo] .container580 {'.
                                            'width: 260px !important;'.
                                        '}'.
                                        'body[yahoo] .half-section {'.
                                            'width: 200px !important;'.
                                        '}'.
                                        'body[yahoo] .section-item {'.
                                            'width: 280px !important;'.
                                        '}'.
                                        'body[yahoo] .section-item-iphone {'.
                                            'width: 280px !important;'.
                                        '}'.
                                        'body[yahoo] .section-img img {'.
                                            'width: 280px !important;'.
                                            'height: auto !important;'.
                                        '}'.
                                        'body[yahoo] .section-iphone-img img {'.
                                            'width: 280px !important;'.
                                            'height: auto !important;'.
                                        '}'.
                                        'body[yahoo] .cta-btn img {'.
                                            'width: 260px !important;'.
                                            'height: auto !important;'.
                                        '}'.
                                    '}'.
                                '</style>'.
                            '</div>'.
                            '<div>'.
                                '<div>'.
                                    '<div style="font-family: verdana, helvetica, sans-serif; font-size: 10pt; color: rgb(0, 68, 111);">'.
                                        '<div>'.
                                            '<div>'.
                                                '<div id="zimbraEditorContainer" style="font-family: verdana, helvetica, sans-serif; font-size: 10pt; color: rgb(0, 68, 111);" class="4">'.
                                                    '<div>'.
                                                        '<div style="font-family: verdana, helvetica, sans-serif; font-size: 10pt; color: rgb(0, 68, 111);">'.
                                                            '<table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="eeeeee">'.
                                                                '<tbody>'.
                                                                    '<tr>'.
                                                                        '<td>'.
                                                                            '<table border="0" align="center" width="590" cellpadding="0" cellspacing="0" bgcolor="#ffffff" class="container590">'.
                                                                                '<tbody>'.
                                                                                    '<tr>'.
                                                                                        '<td>'.
                                                                                            '<div valign="top" align="center" style="background-color: #00b1c1;padding: 10px;color: #FFF;border: 1px solid #00b1c1;">'.
                                                                                                '<p>'.
                                                                                                    '<p style="font-size:large;text-transform: uppercase;"> ' . $subject . ' </p>'.
                                                                                                    '<p> ' . $date->format('d/m/Y H:i:s') . ' </p>'.
                                                                                                '</p>'.
                                                                                            '</div>'.
                                                                                        '</td>'.
                                                                                    '</tr>'.
                                                                                '</tbody>'.
                                                                            '</table>'.
                                                                        '</td>'.
                                                                    '</tr>'.
                                                                '</tbody>'.
                                                            '</table>'.
                                                            '<table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="eeeeee">'.
                                                                '<tbody>'.
                                                                    '<tr>'.
                                                                        '<td>'.
                                                                            '<table border="0" align="center" width="590" cellpadding="0" cellspacing="0" bgcolor="ffffff" class="container590">'.
                                                                                '<tbody>'.
                                                                                    '<tr>'.
                                                                                        '<td>'.
                                                                                            '<table border="0" align="center" width="540" cellpadding="0" cellspacing="0" class="container580">'.
                                                                                                '<tbody>'.
                                                                                                    '<tr></tr>'.
                                                                                                '</tbody>'.
                                                                                            '</table>'.
                                                                                        '</td>'.
                                                                                    '</tr>'.
                                                                                '</tbody>'.
                                                                            '</table>'.
                                                                        '</td>'.
                                                                    '</tr>'.
                                                                '</tbody>'.
                                                            '</table>'.
                                                            '<div> </div>'.
                                                            '<table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="eeeeee">'.
                                                                '<tbody>'.
                                                                    '<tr>'.
                                                                        '<td align="center">'.
                                                                            '<table border="0" align="center" width="590" cellpadding="0" cellspacing="0" bgcolor="ffffff" class="container590 bg_color">'.
                                                                                '<tbody>'.
                                                                                    '<tr>'.
                                                                                        '<td>'.
                                                                                            '<table border="0" align="center" width="540" cellpadding="0" cellspacing="0" class="container580">'.
                                                                                                '<tbody>'.
                                                                                                    '<tr>'.
                                                                                                        '<td align="left" bgcolor="#f8f8f8" class="title_color" style="padding-left: 20px; padding-right: 20px; line-height: 24px; padding-bottom: 15px;">'.
                                                                                                            '<div style="line-height: 24px;">'.
                                                                                                                '<p style="color: rgb(132, 137, 153); font-family: &quot;open sans&quot;, sans-serif; font-size: 14px;">&nbsp;</p>'.
                                                                                                                '<p><font color="#848999" face="open sans, sans-serif">' . $link . '</font></p>'.
                                                                                                                '<p style=""><font color="#848999" face="open sans, sans-serif"><br></font></p>'.
                                                                                                            '</div>'.
                                                                                                        '</td>'.
                                                                                                    '</tr>'.
                                                                                                '</tbody>'.
                                                                                            '</table>'.
                                                                                        '</td>'.
                                                                                    '</tr>'.
                                                                                '</tbody>'.
                                                                            '</table>'.
                                                                        '</td>'.
                                                                    '</tr>'.
                                                                '</tbody>'.
                                                            '</table>'.
                                                            '<table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="eeeeee"></table>'.
                                                        '</div>'.
                                                    '</div>'.
                                                '</div>'.
                                            '</div>'.
                                        '</div>'.
                                    '</div>'.
                                '</div>'.
                                '<br>'.
                            '</div>'.
                        '</div>'.
                    '</div>'.
                '</div>'.
            '</body>'.
            '</html>';
    }


    private function setConfigEmail($encoding, $email_addressee, $username, $sender, $subject, $body){
        $message = new ZendMessage();

        // -- for a while
        $name_addressee = $email_addressee;
        // --

        $message->setEncoding($encoding);
        $message->addTo($email_addressee, $name_addressee)
            ->addFrom($username, $sender)
            ->setSubject($subject)
            ->setBody($body);

        return $message;
    }

    private function setOptionsEmail($sender, $server, $port, $username, $password, $encryption, $autentication) {
        $options = array(
            'name'              => $sender,
            'host'              => $server,
            'port'              => $port,
            'connection_class'  => 'login',
            'connection_config' => array(
                'username'    => $username,
                'password'    => $password,
                'ssl'  => $encryption
            )
        );
        switch ($autentication){
            case 'N':
                unset($options['connection_config']);
                $options['connection_class'] = 'smtp';
                break;
            case 'S':
            default :
                break;
        }
        return new SmtpOptions($options);
    }

    // FIM EMAIL VENDA -------------------------------------------------------------------

    public function overthrow($P){
        try {
            $dllCom = new \COM('Ifm.rdmPrint');
            return $dllCom->DescriptografaSenha($P);
        } catch (\Exception $e) {
            throw new \Exception('Nao foi possivel carregar a IFM.dll, certifique-se que ela esta instalada.');
        }
    }

    public function removeMask($str) {
        return str_replace('-','', str_replace('.', '', str_replace('/', '', $str)));
    }

    public function aplicaMascaraCpfCnpj($value) {
        $maskedValue = '';
        if (strlen($value) > 11) {
            $maskedValue = substr($value, 0, 2) . '.' . substr($value, 2, 3) . '.' . substr($value, 5, 3) . '/' . substr($value, 8, 4) . '-' .substr($value, 12, 2);
        } else {
            $maskedValue = substr($value, 0, 3) . '.' . substr($value, 3, 3) . '.' . substr($value, 6, 3) . '-' . substr($value, 9, 2);
        }
        return $maskedValue;
    }

    public function removeAllFiles($path) {
        $files = glob($path . '/*'); // get all file names
        foreach($files as $file) { // iterate files
            if (is_file($file)) {
                unlink($file); // delete file
            }
        }
    }

    /* Converte os valores numéricos retornados pelo banco em float */
    public function float($value) {
        return floatval(number_format($value, 2, '.', ','));
    }

    /***
     * Verifica se deve ou não utililzar o COM.
     */
    public function isDisabledCOM() {
        return $this->isDisabledDlls() || !$this->isWindowsEnvironment();
    }

    /***
     * Verifica se é ambiente Windows.
     */
    public function isWindowsEnvironment() {
        return defined("PHP_WINDOWS_VERSION_BUILD");
    }

    /***
     * Verifica se deve disabilitar as DLL e COM de acordo com o parametro 'disabled_dlls'.
     */
    public function isDisabledDlls() {
        try {
            $parameter = $this->getXMLParameter('disabled_dlls');
        } catch (\Exception $e) {
            $parameter = FALSE;
        }

        return $parameter;
    }

    public function generateCDSENHAPED($CDFILIAL, $NRORG) {
        $connection = $this->entityManager->getConnection();
        $today = date("d/m/Y");
        $CDCONTADOR = 'VENDADIA' . $CDFILIAL . $today;
        return self::geraCodigo($connection, $CDCONTADOR, $NRORG, 1, 5);
    }

	public function truncate($val, $f = 0){
        if ($p = strpos($val, '.')){
            $val = floatval(substr($val, 0, $p + 1 + $f));
        }
        return $val;
    }

    public function truncDec($value, $precision) {
        $pot10 = pow(10, $precision);

        $zeroLimit = 0.1 / $pot10;
		if (abs($value) < $zeroLimit) {
			return 0;
        }

        $result = floor($value * $pot10) / $pot10;
        $toleranceFactor = (1 / $pot10) - (0.01 / pow($pot10, $precision));
		if (abs($value) - abs($result) >= $toleranceFactor) {
            $minValueInPrecision = 1 / $pot10;
            $multiplier = $result > 0 ? 1 : -1;
			$result = $result + ($multiplier * $minValueInPrecision);
		}
		return $result;
	}

    public function roundABNT($valor, $casasdecimais) {
        $pot10 = pow(10, $casasdecimais);

        $trunc = floor($valor * $pot10) / $pot10;
        $valorDescartado = abs($valor) - abs($trunc);

        $limiteDescarte = 0.5 / $pot10;
        $incrementoUltimaCasa = 1 / $pot10;
        if (self::floatEquals($valorDescartado, $limiteDescarte)) {
            return self::isEven($trunc * $pot10) ? $trunc : $trunc + $incrementoUltimaCasa;
        } else if (abs($valorDescartado) < $limiteDescarte) {
            return $trunc;
        } else if (abs($valorDescartado) > $limiteDescarte) {
            return $trunc + $incrementoUltimaCasa;
        }

        return $result;
    }

    public function floatEquals($a, $b) {
        $precision = 5;
        return round($a, $precision) == round($b, $precision);
    }

    public function isEven($a) {
        $intA = round($a, 0);

        if (self::floatEquals($intA, $a)) {
            return $intA % 2 == 0;
        } else {
			throw new \Exception("Argument passed to function 'isEven' is not an integer");
        }
    }

    public function isSaas() {
        try {
            $issaas = $this->instanceManager->getParameter('ISSAAS');
        } catch (\Throwable $th) {
            $issaas = false;
        }
        return $issaas;
    }

    public function generateCDSENHAPEDodhenPOS($nrorg ,$cdloja, $cdfilial, $IDSENHACUP) {
        $connection = $this->entityManager->getConnection();
        if ($IDSENHACUP === 'S') {
            $today = date("d/m/Y");
            $CDCONTADOR = 'VENDADIA' . $cdfilial . $today;
            return self::geraCodigo($connection, $CDCONTADOR, $nrorg, 1, 5);
        } else if ($IDSENHACUP === 'A' || $IDSENHACUP === 'L') {
            //Validacao para começar a zerar a senha após seis horas.
            $sixHoursBefore = new \dateTime(date("Y/m/d H:i:s", strtotime('-6 hours')));
            $params = array(
                'CDFILIAL' => $cdfilial,
                'CDLOJA' => $cdloja,
                'DTVALIDATE' => $sixHoursBefore
            );
            $types = array(
                'DTVALIDATE' => \Doctrine\DBAL\TypeS\Type::DATETIME
            );
            $CDSENHAPED = $connection->fetchAll("GET_CDSENHAPED", $params, $types);
            $CDSENHAPED = array_map("intval", array_column($CDSENHAPED, 'CDSENHAPED'));
            $max = $IDSENHACUP === 'A' ? 99999 : 999;
            $nrPedido = $this->geraSenhaPedido($max, $CDSENHAPED);
            return $nrPedido;
        }
    }

    private function geraSenhaPedido($max, $CDSENHAPED) {
        $randomNrpedido = mt_rand(1, $max);
        if (empty($CDSENHAPED)) return $randomNrpedido;
        if (!in_array($randomNrpedido, $CDSENHAPED)) {
            return $randomNrpedido;
        } else {
            $nrPedido = $this->geraSenhaPedido($max, $CDSENHAPED);
        }
        return $nrPedido;
    }

    public function checkConfTela(){
        $result = $this->entityManager->getConnection()->fetchAssoc("CHECK_FOR_CONFTELA");
        if (empty($result)){
            throw new \Exception("Tabela CONFTELACAIXA não encontrada. Favor aplicar o script para atualizar a modelagem do banco.");
        }
        else {
            if (!self::databaseIsOracle() && $this->instanceManager->getParameter('RECRIA_CONFTELA')){
                $this->entityManager->getConnection()->executeQuery("DELETE_CONFTELACAIXA");
                $this->entityManager->getConnection()->executeQuery("CONFTELA_SCRIPT");
            }
        }
    }

    public function getConfTela($CDFILIAL, $CDCAIXA){
        $NRCONFTELA = $this->entityManager->getConnection()->fetchAssoc("GET_CONFTELA_CAIXA", array($CDFILIAL, $CDCAIXA));

        if ($NRCONFTELA){
            if (self::databaseIsOracle()){
                $NRCONFTELA['DTINIVIGENCIA'] = \DateTime::createFromFormat('Y-m-d H:i:s', $NRCONFTELA['DTINIVIGENCIA']);
            }
            else {
                $NRCONFTELA['DTINIVIGENCIA'] = \DateTime::createFromFormat('Y-m-d H:i:s.u', $NRCONFTELA['DTINIVIGENCIA']);
            }
        }

        return $NRCONFTELA;
    }

}