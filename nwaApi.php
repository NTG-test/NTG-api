<?php

namespace nwa;

class api {
	public $adminMailAddress;
	public $ip;
	public $token;
	const $controller = $_GET['controller'];;
	public $method;
	public $action;
	public $id;
	public $userId;
	public $businessId;

	private $httpResponseCode;
	public $data = array();
	private $response;

	function __construct() {
		if (isset($_ENV['ADMIN_EMAIL_ADDRESS']))	$this->adminMailAddress = $_ENV['ADMIN_EMAIL_ADDRESS'];
		if (isset($_SERVER['REMOTE_ADDR']))			$this->ip = $_SERVER['REMOTE_ADDR'];
		if (isset($_SERVER['HTTP_AUTHORIZATION']))	$this->token = $_SERVER['HTTP_AUTHORIZATION'];
		if (isset($_GET['controller']))				$this->controller = $_GET['controller'];
		if (isset($_SERVER['REQUEST_METHOD']))		$this->method = $_SERVER['REQUEST_METHOD'];
		if (isset($_GET['action']))					$this->action = $_GET['action'];
		if (isset($_GET['id']))						$this->id = $_GET['id'];
	}

	// Create response, Exit request
	public function done($httpResponseCode, $responseFinalMassage = null) {
		$this->httpResponseCode = $httpResponseCode;
		
		if (isset($responseFinalMassage)) {
			$this->response = $responseFinalMassage;
		}
		if ($GLOBALS['db']->error) {
			$this->response = 'sqlError: '.$GLOBALS['db']->error;
		}
		if (is_null($this->response)) {
			$this->response = json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		}
		
		$this->logRequestAndResponseToDb();
		$this->emailErrorToAdmin();

		http_response_code($this->httpResponseCode);
		exit($this->response);
	}
	
	private function logRequestAndResponseToDb() {
		$GLOBALS['db'] -> query(
			"INSERT INTO nwaRequest (
				httpResponseCode,
				response,
				time,
				controller,
				method,
				action,
				requestedId,
				remoteAddr,
				token
			) VALUES (
				'".$this->httpResponseCode."',
				'".$this->response."',
				'".time()."',
				'".$this->controller."',
				'".$this->method."',
				'".$this->action."',
				'".$this->id."',
				'".$this->ip."',
				'".$this->token."'
			)
		");
	}
	private function emailErrorToAdmin() {
		if ($this->httpResponseCode >= 300) {
			mail(
				$this->adminMailAddress,
				'NWA API Log '.$this->httpResponseCode,
				'Ip: '.$this->ip.'<br>'.
				'Response: '.$this->response.'<br>'.
				'Token: '.$this->token.'<br>'
				// 'MIME-Version: 1.0\r\nContent-type:text/html;charset=UTF-8\r\nFrom: noreply@cleveraj.com'
			);
		}
	}
}
