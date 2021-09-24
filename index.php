<?php
	error_reporting(E_ALL);

	//Libraries
	require 'app.php';

	//Cors
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
	header('Access-Control-Allow-Headers: Origin, Content-Type, Content-Language, Accept, Authorization, X-Request-With');
	header('Access-Control-Allow-Credentials: true');
	header('Content-Type: application/json');

	//Accept OPTION request from browser to test secure line
	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') exit();

	//NULL Not set variables
	if (!isset($_SERVER['HTTP_AUTHORIZATION'])) $_SERVER['HTTP_AUTHORIZATION'] = NULL;
	if (!isset($_GET['controller']))            $_GET['controller'] = NULL;
	if (!isset($_GET['action']))                $_GET['action'] = NULL;
	if (!isset($_GET['id']))                    $_GET['id'] = NULL;

	//Database
	$GLOBALS['db'] = mysqli_connect('localhost', $GLOBALS['nwaApp']['dbUsername'], $GLOBALS['nwaApp']['dpPassword'])
	    or exit('nwaMySqldatabaseConnectionError');
	mysqli_select_db($GLOBALS['db'], $GLOBALS['nwaApp']['dbName']);
	mysqli_set_charset($GLOBALS['db'],'utf8');

	//Reqest when done
	function done($statusCode,$responseText) {
        mysqli_query($GLOBALS['db'], "INSERT INTO ntgRequest (status, response, time, controller, method, action, requestedId, remoteAddr, remoteHost, httpOrigin, token) VALUES ('".$statusCode."', '".$responseText."', '".time()."', '".$_GET['controller']."', '".$_SERVER['REQUEST_METHOD']."', '".$_GET['action']."', '".$_GET['id']."', '".$_SERVER['REMOTE_ADDR']."', '".$_SERVER['HTTP_HOST']."', '".$_SERVER["HTTP_ORIGIN"]."', '".$_SERVER['HTTP_AUTHORIZATION']."')");
        if ($statusCode >= 300)
            mail("mohajerinavid@outlook.com",
              "Cleveraj Log ".$statusCode,
              'Ip: '.$_SERVER['REMOTE_ADDR'].'<br>'.
              'Response: '.$responseText.'<br>'.
              'Token: '.$_SERVER['HTTP_AUTHORIZATION'].'<br>'.
              'Origin: '.$_SERVER["HTTP_ORIGIN"],
              'MIME-Version: 1.0\r\nContent-type:text/html;charset=UTF-8\r\nFrom: noreply@cleveraj.com');
        $status = array(
            661 => 'HTTP/1.1 661 ntgMySqldatabaseConnectionError',
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
        header($status[$statusCode]);
        exit($responseText);
    }

 	//Only Accept over HTTPS
 	if (!$_SERVER['HTTPS']) done(505,'requiredSecureHttpsProtocol');

 	//Reject Other Origins
    if (!in_array($_SERVER['HTTP_ORIGIN'], $GLOBALS['nwaApp']['allowOrigin'])) done(403,'originNotAllowed');

	//Convert POST data (JSON) to Object
	$_POST = json_decode(file_get_contents('php://input'), true);

	//Protect on SQL Injection attacks
	if (is_object($_POST)) foreach($_POST as $key => $value) $_POST[$key] = mysqli_real_escape_string($GLOBALS['db'], $value);

    //Authorization export $GLOBALS['ntgUserId'] from token
    if ($_SERVER['HTTP_AUTHORIZATION']) {
    	$result = mysqli_query($GLOBALS['db'], "SELECT * FROM ntgToken WHERE token='".hash('sha256',$_SERVER['HTTP_AUTHORIZATION'])."' LIMIT 1");
    	if ($ntgToken = mysqli_fetch_array($result)) {
        	if ($ntgToken['status'] != 'active') done(401,'tokenDeactivated');
			if ($ntgToken['userId']) {
        		$result = mysqli_query($GLOBALS['db'], "SELECT * FROM ntgUser WHERE id='".$ntgToken['userId']."' LIMIT 1");
        		if ($ntgUser = mysqli_fetch_array($result)) {
            		$GLOBALS['ntgUserId'] = $ntgUser['id'];
        		} else if ($_GET['controller']!='account')	done(404,'tokenUserIdNotFound');
			} else if ($_GET['controller']!='account')	done(404,'tokenUserIdNotFound2');
    	} else done(401,'tokenInvalid');
	} else if (!in_array($_GET['controller'], $GLOBALS['nwaApp']['allowController'])) done(401,'tokenRequired');

	//API List Help, URL: api.com/help
	if ($_GET['controller']=='help') {
		foreach(glob('controller/*.php') as $file) {
			preg_match_all('/function (\w+)/', file_get_contents($file), $functionList);
			$fileName = pathinfo($file)['filename'];
			$response -> $fileName = array();
			foreach ($functionList[1] as $functionName)
				if (in_array($functionName, array('GET', 'PUT', 'POST', 'DELETE', 'OPTIONS')))
					array_push($response -> $fileName, $functionName);
		}
		done(200,json_encode($response));
	}

    //Controller
    if (file_exists('controller/'.$_GET['controller'].'.php'))
        require 'controller/'.$_GET['controller'].'.php';
    else
        done(404,'controllerNotFound');

    //Method function
    if (function_exists($_SERVER['REQUEST_METHOD']))
        $_SERVER['REQUEST_METHOD']();
    else
        done(405,'methodNotAllowed');

    done(204,'nothingDone');


/*
$GLOBALS['db']                  Main Database Connection
$GLOBALS['ntgUserId']           ntg User ID

$_GET['controller']             GET Controller Name
$_GET['action']                 GET Action Name
$_GET['id']                     GET Id Number

$_SERVER['REQUEST_METHOD']      Method
$_SERVER['HTTP_ORIGIN']         Origin
$_SERVER['HTTP_AUTHORIZATION']  token
$_SERVER['REMOTE_ADDR']         User IP
$_SERVER['HTTP_HOST']           User Url
*/
