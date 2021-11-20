<?php

function GET() {
	if (!$GLOBALS['nwaApi']->id) $GLOBALS['nwaApi']->done(400, 'langCodeIsNotSet');
		
	$allowedLang = array('enUS', 'ar', 'fa');
	if (!in_array($GLOBALS['nwaApi']->id, $allowedLang)) $GLOBALS['nwaApi']->done(404, 'langCodeNotFound');

	$result = $GLOBALS['db']->query("SELECT * FROM nwaLanguage");
	$i=1;
	while($row = $result->fetch_assoc()) {
		$GLOBALS['nwaApi']->responseData->$i = (object) [
			'selector'=>$row['selector'],
			'attribute'=>$row['attribute'],
			'trans'=>$row[$GLOBALS['nwaApi']->id]
		];
		$i++;
	}

	$GLOBALS['nwaApi']->massage('Test massage from language.');
	// $GLOBALS['nwaApi']->responseData = $response;
	$GLOBALS['nwaApi']->done(200);
}
