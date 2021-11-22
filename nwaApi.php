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
	public $response = array();

	//Exit request, create response, log in db
	function done($httpResponseCode, $responseFinalMassage = null) {
		$this->httpResponseCode = $httpResponseCode;

		if (isset($responseFinalMassage)) {
			$this->response = $responseFinalMassage;
		}
		if ($GLOBALS['db']->error) {
			$this->response = 'sqlError: '.$GLOBALS['db']->error;
		}
		
		$GLOBALS['db'] -> query(
			"INSERT INTO nwaRequest (
				statusCode,
				response,
				time,
				controller,
				method,
				action,
				requestedId,
				remoteAddr,
				token
			) VALUES (
				'".$this->statusCode."',
				'".json_encode($this->response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)."',
				'".time()."',
				'".$this->controller."',
				'".$this->method."',
				'".$this->action."',
				'".$this->id."',
				'".$this->ip."',
				'".$this->token."'
				)
				");
				
				if ($this->statusCode >= 300) {
					mail(
						'nexnema@gmail.com',
						'Cleveraj Log '.$this->statusCode,
						'Ip: '.$this->ip.'<br>'.
						'Response: '.json_encode($this->response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT).'<br>'.
						'Token: '.$this->token.'<br>'
						// 'MIME-Version: 1.0\r\nContent-type:text/html;charset=UTF-8\r\nFrom: noreply@cleveraj.com'
					);
				}
				
				http_response_code($this->httpResponseCode);
				exit(json_encode($this->response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
			}
		}
