<?php
namespace Service;

use Zeedhi\DTO\Response\Message;
use Zeedhi\DTO\Response\Method;

class RequestLog implements \Zeedhi\Router\Interceptor\SecurityInterceptor {
	private $startTime;
	public function beforeInvoke($route){
		
	}
	public function afterInvoke($request, $respose){
		global $startTime;
		$time = microtime(true) - $startTime;
		$respose->addMethod(new Method("isNaN", array($time)));
	}
}