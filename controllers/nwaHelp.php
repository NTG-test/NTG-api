<?php

class nwaAcceptedRequestHelp {
	public $controller;
	public $methods = array();

	public function getHelpDataForController($file) {
		$fileName = pathinfo($file)['filename'];
		preg_match_all('/function (\w+)/', file_get_contents($file), $functionList);
		$this->controller = $fileName;
		foreach ($functionList[1] as $functionName)
		if (in_array($functionName, array('GET', 'PUT', 'POST', 'DELETE', 'OPTIONS')))
			array_push($this->methods, $functionName);
	}
}


// API supported controllers & methods Help List
function GET() {
	foreach(glob('controllers/*.php') as $file) {
		$nwaAcceptedRequestHelp = new nwaAcceptedRequestHelp();
		$nwaAcceptedRequestHelp->getHelpDataForController($file);
		array_push($GLOBALS['nwaApi']->data, $nwaAcceptedRequestHelp);
	}
	$GLOBALS['nwaApi']->done(200);
}

