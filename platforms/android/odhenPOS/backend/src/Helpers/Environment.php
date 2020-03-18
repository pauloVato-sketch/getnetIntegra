<?php
namespace Helpers;

use Zeedhi\Framework\Session\Session;

class Environment {

	protected $session;

	const USER_INFO = 'USERINFO';

	const LAST_ACCESS = 'LASTACCESS';
	const SESSION_IDLE_TIMEOUT = 28800; // 28800sec = 8h
	const SESSION_STARTED_KEY = 'SESSION_STARTED';
	const SESSION_LIFETIME = 86400; // 86400sec = 24h

	public function __construct(Session $session) {
		$this->session = $session;
	}

	public function startSession($userInfo = null) {
		$this->session->start();
		$this->session->set(self::SESSION_STARTED_KEY, time());
		if (!empty($this->getUserInfo())){
			$this->session->migrate(true);
		}
		$this->setUserInfo($userInfo);
		if (!empty($userInfo)){
			$this->setLastAccess();
		}
	}

	public function handleSessionLifetime() {
		$currentTime = time();
		
		$sessionIdleTimeout = $this->getLastAccess() + self::SESSION_IDLE_TIMEOUT;
		$sessionLifeTimeout = $this->getSessionStarted() + self::SESSION_LIFETIME;

		if ($currentTime >= $sessionIdleTimeout || $currentTime >= $sessionLifeTimeout) {
			$this->session->destroy();
		} else {
			$this->setLastAccess();
		}
	}

	public function setUserInfo($userInfo) {
		if(!empty($userInfo)) {
			$this->session->set(self::USER_INFO, json_encode($userInfo));
		}
	}

	private function setLastAccess() {
		$this->session->set(self::LAST_ACCESS, time());
	}

	public function getUserSessionParameter($parameterName) {
		$userParameters = $this->getUserInfo();
		return $userParameters[$parameterName];
	}

	public function hasUserSessionParameter($parameterName) {
	   return $this->getUserSessionParameter($parameterName) !== NULL;
	}

	public function getUserInfo() {
		return json_decode($this->session->get(self::USER_INFO), true);
	}

	private function getLastAccess() {
		return $this->session->get(self::LAST_ACCESS);
	}

	public function getSessionStarted() {
		$default = time();
		return $this->session->get(self::SESSION_STARTED_KEY, $default);
	}

	public function endSession(){
		$this->session->get(self::USER_INFO); //it should be removed when issue 99032 be solved
		return $this->session->destroy();
	}

}