<?php

class nwaPhrase {
	public $selector;
	public $attribute;
	public $phrase;

	function __construct($selector, $attribute, $phrase) {
		$this->selector = $selector;
		$this->attribute = $attribute;
		$this->phrase = $phrase;
	}
}

function GET() {
	if (!$GLOBALS['nwaApi']->id) $GLOBALS['nwaApi']->done(400, 'langCodeIsNotSet');
		
	$allowedLang = array('enUS', 'ar', 'fa');
	if (!in_array($GLOBALS['nwaApi']->id, $allowedLang)) {
		$GLOBALS['nwaApi']->done(404, 'langCodeNotFound');
	}

	$result = $GLOBALS['db']->query("SELECT * FROM nwaLanguage");
	while($row = $result->fetch_assoc()) {
		array_push(
			$GLOBALS['nwaApi']->data,
			$nwaPhrase = new nwaPhrase(
				$row['selector'],
				$row['attribute'],
				$row[$GLOBALS['nwaApi']->id]
			)
		);
	}

	$GLOBALS['nwaApi']->done(200);
}
