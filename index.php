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
require 'app/app.php';
require 'nwaFunctions.php';
require 'nwaDataStructure.php';
require 'app/dataStructure.php';
require 'nwaApi.php';

// NULL Not set variables
$GLOBALS['nwaApi'] = new nwaApi();
$GLOBALS['nwaApi']->adminMailAddress = 'nexnema@gmail.com';
if (isset($_SERVER['REMOTE_ADDR']))			$GLOBALS['nwaApi']->ip = $_SERVER['REMOTE_ADDR'];
if (isset($_SERVER['HTTP_AUTHORIZATION']))	$GLOBALS['nwaApi']->token = $_SERVER['HTTP_AUTHORIZATION'];
if (isset($_GET['controller']))				$GLOBALS['nwaApi']->controller = $_GET['controller'];
if (isset($_SERVER['REQUEST_METHOD']))		$GLOBALS['nwaApi']->method = $_SERVER['REQUEST_METHOD'];
if (isset($_GET['action']))					$GLOBALS['nwaApi']->action = $_GET['action'];
if (isset($_GET['id']))						$GLOBALS['nwaApi']->id = $_GET['id'];

// Connect database
$GLOBALS['db'] = new mysqli(
	'localhost',
	DB['username'],
	DB['password'],
	DB['name']
);
if ($GLOBALS['db']->connect_error)
	exit($GLOBALS['db']->connect_error);
$GLOBALS['db']->set_charset("utf8");
nwaCreateNwaDatabaseTables();
appCreateNwaDatabaseTables();

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
