<?php

class nwaAcceptedRequestHelp {
	private $file;
	public $controller;
	public $methods = array();

	function __construct($file) {
		$this->file = $file;
		$this->getHelpDataForController();
	}

	public function getHelpDataForController() {
		$fileName = pathinfo($this->file)['filename'];
		preg_match_all('/function (\w+)/', file_get_contents($this->file), $functionList);
		$this->controller = $fileName;
		foreach ($functionList[1] as $functionName)
		if (in_array($functionName, array('GET', 'PUT', 'POST', 'DELETE', 'OPTIONS')))
			array_push($this->methods, $functionName);
	}

	function __destruct() {
		echo 999;
	}
}


// API supported controllers & methods Help List
function GET() {
	foreach(glob('controllers/*.php') as $file) {
		$nwaAcceptedRequestHelp = new nwaAcceptedRequestHelp($file);
		array_push(
			$GLOBALS['nwaApi']->data,
			$nwaAcceptedRequestHelp
		);
	}
	$GLOBALS['nwaApi']->done(200);
}

