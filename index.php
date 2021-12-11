<?php

namespace api;

error_reporting(E_ALL);

// Headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, Content-Language, Accept, Authorization, X-Request-With');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');
header('Cache-Control: no-cache');
header('Pragma: no-cache');

// Only Accept over HTTPS
if (!$_SERVER['HTTPS']) {
	http_response_code(505);
	exit();
}

// Accept OPTION request from browser to test secure line
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') exit();

require 'apiEnv.php';
require 'apiFunctions.php';
require 'apiDataStructure.php';
require 'apiHandle.php';

function api() {
	new api\env('.env');
	
	// Connect database
	$db = new mysqli(
		$_ENV['DATABASE_HOST'],
		$_ENV['DATABASE_USER'],
		$_ENV['DATABASE_PASSWORD'],
		$_ENV['DATABASE_NAME']
	);
	if ($db->connect_error)
		exit($db->connect_error);
	$db->set_charset("utf8");
	nwa\createDatabaseTables($db);

	$request = new api\request();
	
	//Controller
	if (file_exists('controllers/'.$request->controller.'.php')) {
		require 'controllers/'.$request->controller.'.php';
	} else {
		$response = new api\response(404, 'controllerNotFound');
	}
	//Method function
	if (function_exists($request->method)) {
		$response = ($request->method)();
	} else {
		$response = new api\response(405, 'methodNotAllowed');
	}

		// $this->logRequestAndResponseToDb();
		// $this->emailErrorToAdmin();

	return $response;
}

exit(json_encode(api\api(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));












$GLOBALS['nwaApi'] = new nwa\api();



// Convert POST data (JSON) to Object
$_POST = json_decode(file_get_contents('php://input'), true);

// Protect on SQL Injection attacks
if (is_object($_POST)) {
	foreach($_POST as $key => $value) {
		$_POST[$key] = $GLOBALS['db']->real_escape_string($value);
	}
}

	// $GLOBALS['nwaApp'] = array(
	// 	'allowedOrigins' => array(
	// 		'',
	// 		'https://api.nategh.org',
	// 		'https://nategh.nategh.org'
	// 	),
	// 	'allowController' => array(
	// 		'language',
	// 		'account',
	// 		'profile',
	// 		'logout',
	// 		'overview',
	// 		'database'
	// 	)
	// );
// Reject Other Origins
// if (!in_array($_SERVER['HTTP_ORIGIN'], $GLOBALS['nwaApp']['allowedOrigins']))
// 	$GLOBALS['nwaApi']->done(403, 'originNotAllowed');



$GLOBALS['nwaApi']->done(204, 'nothingDone');

/*
$GLOBALS['nwaApp']		Data to handle api permissions
$GLOBALS['nwaDb']		Class: Data to connect database
$GLOBALS['nwaApi']		Class: Main API massages, data, response

api.com/controller
api.com/controller/id
api.com/controller/action/id
*/
