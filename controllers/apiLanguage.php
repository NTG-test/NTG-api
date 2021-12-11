<?php

class phrase {
	public $selector;
	public $attribute;
	public $phrase;

	function __construct($selector, $attribute, $phrase) {
		$this->selector = $selector;
		$this->attribute = $attribute;
		$this->phrase = $phrase;
	}
}

function GET($db, $id) {
	if (!$id) return new api\response(400, 'langCodeIsNotSet');
		
	$allowedLang = array('enUS', 'ar', 'fa');
	if (!in_array($id, $allowedLang)) {
		return new api\response(404, 'langCodeNotFound');
	}

	$data = array();
	$result = $db->query("SELECT * FROM nwaLanguage");
	while($row = $result->fetch_assoc()) {
		array_push(
			$data,
			new phrase(
				$row['selector'],
				$row['attribute'],
				$row[$GLOBALS['nwaApi']->id]
			)
		);
	}
	return new api\response(200, $data);
}
