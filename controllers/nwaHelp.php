<?php

namespace nwa;

class nwaAcceptedRequestHelp {
	private $file;
	public $controller;
	public $methods = array();

	function __construct($file) {
		$this->file = $file;
		$this->getHelpDataForController();
	}

	private function getHelpDataForController() {
		$fileName = pathinfo($this->file)['filename'];
		preg_match_all('/function (\w+)/', file_get_contents($this->file), $functionList);
		$this->controller = $fileName;
		foreach ($functionList[1] as $functionName)
		if (in_array($functionName, array('GET', 'PUT', 'POST', 'DELETE', 'OPTIONS')))
			array_push($this->methods, $functionName);
	}
}


// API supported controllers & methods Help List
function GET() {
	foreach(glob('controllers/*.php') as $file) {
		array_push(
			$GLOBALS['nwaApi']->data,
			$nwaAcceptedRequestHelp = new nwaAcceptedRequestHelp($file)
		);
	}

	$GLOBALS['nwaApi']->done(200);
}

