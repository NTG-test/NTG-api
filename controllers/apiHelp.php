<?php

class acceptedRequestsHelp {
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
function GET($db, $id) {
	$data = array();
	foreach(glob('controllers/*.php') as $file) {
		array_push(
			$data,
			new acceptedRequestsHelp($file)
		);
	}

	return new api\response(200, $data);
}
