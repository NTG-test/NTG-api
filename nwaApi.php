<?php

class nwaApi {
	public $ip;
	public $token;
	public $controller;
	public $method;
	public $action;
	public $id;
	public $userId;
	public $businessId;

	private $httpResponseCode;
	public $responseData = array();
	private $response;

	//Exit request, create response, log in db
	function done($httpResponseCode, $responseFinalMassage = null) {
		$this->httpResponseCode = $httpResponseCode;

		if (isset($responseFinalMassage)) {
			$this->response = $responseFinalMassage;
		}
		if ($GLOBALS['db']->error) {
			$this->response = 'sqlError: '.$GLOBALS['db']->error;
		}

		if (is_null($this->response)) {
			$this->response = json_encode($this->responseData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		}

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
		
		if ($this->httpResponseCode >= 300) {
			mail(
				'nexnema@gmail.com',
				'NWA API Log '.$this->httpResponseCode,
				'Ip: '.$this->ip.'<br>'.
				'Response: '.json_encode($this->response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT).'<br>'.
				'Token: '.$this->token.'<br>'
				// 'MIME-Version: 1.0\r\nContent-type:text/html;charset=UTF-8\r\nFrom: noreply@cleveraj.com'
			);
		}

		http_response_code($this->httpResponseCode);
		exit($this->response);
	}
}
