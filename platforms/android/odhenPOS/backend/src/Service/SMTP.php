<?php

namespace Service;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Mime as Mime;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;

class SMTP {

	protected $entityManager;

	public function __construct(\Doctrine\ORM\EntityManager $entityManager) {
		$this->entityManager = $entityManager;
	}

	public function getSMTP(){
		return $this->entityManager->getConnection()->fetchAll("GET_SMTP");
	}

	public function sendEmail($name_addressee, $email_addressee, $sender, $subject, $content) {
		// Get data from server

		$SMTP       = $this->getSMTP();
		$username   = $SMTP[0]['DSEMAILAUVND'];
		$server     = $SMTP[0]['DSSMTPAUTVND'];
		$password   = $SMTP[0]['CDSENHAAUTVNDWEB'];
		$port       = $SMTP[0]['NRPORTAAUTVND'];
		$encryption = 'null';

		$message = self::generateEmail($subject, $content);

		$html = new MimePart($message);
		$html->type = "text/html; charset=UTF-8";
		$body = new MimeMessage();

		// E-mail config.
		$body->setParts(array($html));
		$message = new Message();
		$message->setEncoding('UTF-8');
		$message->addTo($email_addressee, $name_addressee)
				->addFrom($username, $sender)
				->setSubject($subject)
				->setBody($body);

		$transport = new SmtpTransport();
		$options   = new SmtpOptions(array(
			'name'              => $server,
			'host'              => $server,
			'port'              => $port,
			'connection_class'  => 'login',
			'connection_config' => array(
				'username' => $username,
				'password' => $password,
				'encryption' => $encryption
			)
		));
		$transport->setOptions($options);

		$transport->send($message);

	}

	public function generateEmail($subject, $content) {
		return
			'<!DOCTYPE html>'.
			'<head>'.
			'<style>
			.TabelaEmail {
				margin:0px;padding:0px;
				width:100%;
				box-shadow: 10px 10px 5px #888888;
				border:1px solid #000000;

				-moz-border-radius-bottomleft:0px;
				-webkit-border-bottom-left-radius:0px;
				border-bottom-left-radius:0px;

				-moz-border-radius-bottomright:0px;
				-webkit-border-bottom-right-radius:0px;
				border-bottom-right-radius:0px;

				-moz-border-radius-topright:0px;
				-webkit-border-top-right-radius:0px;
				border-top-right-radius:0px;

				-moz-border-radius-topleft:0px;
				-webkit-border-top-left-radius:0px;
				border-top-left-radius:0px;
			}.TabelaEmail table{
				border-collapse: collapse;
					border-spacing: 0;
				width:100%;
				height:100%;
				margin:0px;padding:0px;
			}.TabelaEmail tr:last-child td:last-child {
				-moz-border-radius-bottomright:0px;
				-webkit-border-bottom-right-radius:0px;
				border-bottom-right-radius:0px;
			}
			.TabelaEmail table tr:first-child td:first-child {
				-moz-border-radius-topleft:0px;
				-webkit-border-top-left-radius:0px;
				border-top-left-radius:0px;
			}
			.TabelaEmail table tr:first-child td:last-child {
				-moz-border-radius-topright:0px;
				-webkit-border-top-right-radius:0px;
				border-top-right-radius:0px;
			}.TabelaEmail tr:last-child td:first-child{
				-moz-border-radius-bottomleft:0px;
				-webkit-border-bottom-left-radius:0px;
				border-bottom-left-radius:0px;
			}.TabelaEmail tr:hover td{
				background-color:#fcddbf;
			}
			.TabelaEmail td{
				vertical-align:middle;
					background:-o-linear-gradient(bottom, #f4c395 5%, #fcddbf 100%);    background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #f4c395), color-stop(1, #fcddbf) );
				background:-moz-linear-gradient( center top, #f4c395 5%, #fcddbf 100% );
				filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#f4c395", endColorstr="#fcddbf");  background: -o-linear-gradient(top,#f4c395,fcddbf);

				background-color:#f4c395;

				height: 40px;
				border:1px solid #000000;
				border-width:0px 1px 1px 0px;
				text-align:left;
				padding:7px;
				font-size:10px;
				font-family:Arial;
				font-weight:normal;
				color:#000000;
			}.TabelaEmail tr:last-child td{
				border-width:0px 1px 0px 0px;
			}.TabelaEmail tr td:last-child{
				border-width:0px 0px 1px 0px;
			}.TabelaEmail tr:last-child td:last-child{
				border-width:0px 0px 0px 0px;
			}
			.TabelaEmail tr:first-child td{
					background:-o-linear-gradient(bottom, #E95F28 5%, #E95F28 100%);    background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #E95F28), color-stop(1, #E95F28) );
				background:-moz-linear-gradient( center top, #E95F28 5%, #E95F28 100% );
				filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#E95F28", endColorstr="#E95F28");  background: -o-linear-gradient(top,#E95F28,E95F28);

				background-color:#E95F28;
				border:0px solid #000000;
				text-align:center;
				border-width:0px 0px 1px 1px;
				font-size:15px;
				font-family:Arial;
				font-weight:bold;
				color:#ffffff;
			}
			.TabelaEmail tr:first-child:hover td{
				background:-o-linear-gradient(bottom, #E95F28 5%, #bf5f00 100%);    background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #E95F28), color-stop(1, #bf5f00) );
				background:-moz-linear-gradient( center top, #E95F28 5%, #bf5f00 100% );
				filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#E95F28", endColorstr="#bf5f00");  background: -o-linear-gradient(top,#E95F28,bf5f00);

				background-color:#E95F28;
			}
			.TabelaEmail tr:first-child td:first-child{
				border-width:0px 0px 1px 0px;
			}
			.TabelaEmail tr:first-child td:last-child{
				border-width:0px 0px 1px 1px;
			}
			</style>'.
			'<meta http-equiv="content-type" content="text/html; charset=UTF-8">'.
			'</head>'.
			'<body>'.
			'<center>'.
			'<div class="TabelaEmail" style="width: 500; height: 300">'.
			'<table>'.
			'<tr>'.
			'<td valign="top" align="center">'.
			'<strong>'.
			'<font face="Arial" size="4"> '.$subject.' </font>'.
			'</strong>'.
			'</td>'.
			'</tr>'.
			'<tr>'.
			'<td>'.
			'<font face="Arial" size="2"> <center>'.$content.'</center> </font>'.
			// '<center><img src="http://www.odhen.com/wp-content/uploads/2015/05/odhen.png" style="width: 250px;"></center>'.
			'</td>'.
			'</tr>'.
			'</table>'.
			'</div>'.
			'</center>'.
			'</body>'.
			'</html>'
		;
	}

}