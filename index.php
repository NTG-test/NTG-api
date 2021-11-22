<?php
error_reporting(E_ALL);

// Headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, Content-Language, Accept, Authorization, X-Request-With');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');
header('Cache-Control: no-cache');
header('Pragma: no-cache');

// Accept OPTION request from browser to test secure line
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') exit();

// Libraries
require '../nwaDb.php'; //must be ENV file
// nwaDb.php example: const DB=array('username'=>'***','password'=>'***','name'=>'***');
require 'nwaEnv.php';
(new nwaEnv('.env'))->load();
require 'app/app.php';
require 'nwaFunctions.php';
require 'nwaDataStructure.php';
require 'app/dataStructure.php';
require 'nwaApi.php';

// Connect database
$GLOBALS['db'] = new mysqli(
	$_ENV['DATABASE_HOST'],
	$_ENV['DATABASE_USER'],
	$_ENV['DATABASE_PASSWORD'],
	$_ENV['DATABASE_NAME']
);
if ($GLOBALS['db']->connect_error)
	exit($GLOBALS['db']->connect_error);
$GLOBALS['db']->set_charset("utf8");
nwaCreateNwaDatabaseTables();
appCreateNwaDatabaseTables();

// NULL Not set variables
$GLOBALS['nwaApi'] = new nwaApi();

// Only Accept over HTTPS
if (!$_SERVER['HTTPS'])
	$GLOBALS['nwaApi']->done(505, 'requiredSecureHttpsProtocol');

// Reject Other Origins
// if (!in_array($_SERVER['HTTP_ORIGIN'], $GLOBALS['nwaApp']['allowedOrigins']))
// 	$GLOBALS['nwaApi']->done(403, 'originNotAllowed');

// Convert POST data (JSON) to Object
$_POST = json_decode(file_get_contents('php://input'), true);

// Protect on SQL Injection attacks
if (is_object($_POST)) {
	foreach($_POST as $key => $value) {
		$_POST[$key] = $GLOBALS['db']->real_escape_string($value);
	}
}

//Controller
if (file_exists('controllers/'.$GLOBALS['nwaApi']->controller.'.php')) {
	require 'controllers/'.$GLOBALS['nwaApi']->controller.'.php';
} else {
	$GLOBALS['nwaApi']->done(404, 'controllerNotFound');
}
//Method function
if (function_exists($GLOBALS['nwaApi']->method)) {
	($GLOBALS['nwaApi']->method)();
} else {
	$GLOBALS['nwaApi']->done(405, 'methodNotAllowed');
}

$GLOBALS['nwaApi']->done(204, 'nothingDone');

/*
$GLOBALS['nwaApp']		Data to handle api permissions
$GLOBALS['nwaDb']		Class: Data to connect database
$GLOBALS['nwaApi']		Class: Main API massages, data, response
DB						Main Database Connection

api.com/controller
api.com/controller/id
api.com/controller/action/id
*/
