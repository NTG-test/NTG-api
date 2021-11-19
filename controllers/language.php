<?php

	function GET() {
		if (!$GLOBALS['nwaApi']->id) $GLOBALS['nwaApi']->done(400, 'langCodeIsNotSet');
		
		$allowedLang = array('enUS', 'ar', 'fa');
		if (!in_array($GLOBALS['nwaApi']->id, $allowedLang)) $GLOBALS['nwaApi']->done(404, 'langCodeNotFound');
		
		// $result = mysqli_query($GLOBALS['db'], "SELECT * FROM nwaLanguage");
		// $i=1;
		// while ($nwaLanguage = mysqli_fetch_array($result)) {
		// 	$response->$i = (object) [
		// 		'selector'=>$nwaLanguage['selector'],
		// 		'attribute'=>$nwaLanguage['attribute'],
		// 		'trans'=>$nwaLanguage[$_GET['id']]
		// 	];
		// 	$i++;
		// }

		$GLOBALS['nwaApi']->massage('Test massage from language.');
		$GLOBALS['nwaApi']->responseData = $response;
		$GLOBALS['nwaApi']->done(200, 'CMC Data');
	}
