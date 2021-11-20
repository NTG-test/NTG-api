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

	public $responseData;

	private $massage = array();
	private static $status = array(
		// 661 => 'HTTP/1.1 661 nwaMySqldatabaseConnectionError',
		// 662 => 'HTTP/1.1 661 nwaMySqldatabaseError',
		100 => 'HTTP/1.1 100 Continue',
		101 => 'HTTP/1.1 101 Switching Protocols',
		200 => 'HTTP/1.1 200 OK',
		201 => 'HTTP/1.1 201 Created',
		202 => 'HTTP/1.1 202 Accepted',
		203 => 'HTTP/1.1 203 Non-Authoritative Information',
		204 => 'HTTP/1.1 204 No Content',
		205 => 'HTTP/1.1 205 Reset Content',
		206 => 'HTTP/1.1 206 Partial Content',
		300 => 'HTTP/1.1 300 Multiple Choices',
		301 => 'HTTP/1.1 301 Moved Permanently',
		302 => 'HTTP/1.1 302 Found',
		303 => 'HTTP/1.1 303 See Other',
		304 => 'HTTP/1.1 304 Not Modified',
		305 => 'HTTP/1.1 305 Use Proxy',
		307 => 'HTTP/1.1 307 Temporary Redirect',
		400 => 'HTTP/1.1 400 Bad Request',
		401 => 'HTTP/1.1 401 Unauthorized',
		402 => 'HTTP/1.1 402 Payment Required',
		403 => 'HTTP/1.1 403 Forbidden',
		404 => 'HTTP/1.1 404 Not Found',
		405 => 'HTTP/1.1 405 Method Not Allowed',
		406 => 'HTTP/1.1 406 Not Acceptable',
		407 => 'HTTP/1.1 407 Proxy Authentication Required',
		408 => 'HTTP/1.1 408 Request Time-out',
		409 => 'HTTP/1.1 409 Conflict',
		410 => 'HTTP/1.1 410 Gone',
		411 => 'HTTP/1.1 411 Length Required',
		412 => 'HTTP/1.1 412 Precondition Failed',
		413 => 'HTTP/1.1 413 Request Entity Too Large',
		414 => 'HTTP/1.1 414 Request-URI Too Large',
		415 => 'HTTP/1.1 415 Unsupported Media Type',
		416 => 'HTTP/1.1 416 Requested Range Not Satisfiable',
		417 => 'HTTP/1.1 417 Expectation Failed',
		500 => 'HTTP/1.1 500 Internal Server Error',
		501 => 'HTTP/1.1 501 Not Implemented',
		502 => 'HTTP/1.1 502 Bad Gateway',
		503 => 'HTTP/1.1 503 Service Unavailable',
		504 => 'HTTP/1.1 504 Gateway Time-out',
		505 => 'HTTP/1.1 505 HTTP Version Not Supported'
	);

	function massage(string $massage) {
		array_push($this->massage, $massage);
	}
	private function checkSqlErrors() {
		if ($GLOBALS['db']->error) {
			array_push($this->massage, 'sqlError: '.$GLOBALS['db']->error);
		}
	}

	//Exit request, create response, log in db
	function done($statusCode, $responseFinalMassage = null) {
		if (isset($responseFinalMassage)) {
			array_push($this -> massage, $responseFinalMassage);
		}
		$this -> checkSqlErrors();

		$response = (object) [];
		$response -> status = (object) [
			'statusCode' => $statusCode,
			'status' => self::$status[$statusCode],
			'timestamp' => time(),
			'responseTime' => 0,
			'massage' => $this -> massage
		];
		$response -> data = $this -> responseData;

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
				'".$statusCode."',
				'".json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)."',
				'".time()."',
				'".$GLOBALS['nwaApi']->controller."',
				'".$GLOBALS['nwaApi']->method."',
				'".$GLOBALS['nwaApi']->action."',
				'".$GLOBALS['nwaApi']->id."',
				'".$GLOBALS['nwaApi']->ip."',
				'".$GLOBALS['nwaApi']->token."'
			)
		");
	
		if ($statusCode >= 300) {
			mail(
				'nexnema@gmail.com',
				'Cleveraj Log '.$statusCode,
				'Ip: '.$GLOBALS['nwaApi']->ip.'<br>'.
				'Response: '.json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT).'<br>'.
				'Token: '.$GLOBALS['nwaApi']->token.'<br>'
				// 'MIME-Version: 1.0\r\nContent-type:text/html;charset=UTF-8\r\nFrom: noreply@cleveraj.com'
			);
		}

		header(self::$status[$statusCode]);
		exit(json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	}
}
