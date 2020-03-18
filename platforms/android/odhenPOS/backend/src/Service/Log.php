<?php

namespace Service;

use Zeedhi\Framework\DTO\Response;
use Zeedhi\Framework\DTO\Request;

class Log {
	protected $util;

	public function __construct(\Util\Util $util) {
		$this->util = $util;
	}

	public function exportLogs(Request\Filter $request, Response $response){
		try {
			$params = $request->getFilterCriteria()->getConditions();
			$logContent = $params[0]['value'];
			$filename   = $params[1]['value'] . '.log';

			$paths = ['LOG/', 'javaLog/'];
			$logDir = dirname(__DIR__) . "/../../../";

			foreach($paths as $path) {
				$logDir = $logDir . $path;

				if(!is_dir($logDir)) {
					mkdir($logDir, 0700);
				}
			}

			$session = $this->util->getSessionVars(null);
			$cdfilial = $session['CDFILIAL'];
			$cdloja = $session['CDLOJA'];
			$cdcaixa = $session['CDCAIXA'];
			$filename = $logDir . $filename;

			file_put_contents($filename, str_replace("\n", "\r\n", $logContent), FILE_APPEND);

			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('ExportLogs', array(
				"error" => false
			)));
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('ExportLogs', array(
				"error" => true,
				"message" => $e->getMessage()
			))); 
		}
	}
}