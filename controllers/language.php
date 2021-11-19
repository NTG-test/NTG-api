<?php

	function GET() {
		if (!$GLOBALS['nwaApi']->id) $GLOBALS['nwaApi']->done(400, 'langCodeIsNotSet');
		$GLOBALS['nwaApi']->done(200, 'lang');

		$allowedLang = array('enUS', 'ar', 'fa');
		if (!in_array($_GET['id'], $allowedLang)) done(404, 'langCodeNotFound');

		$result = mysqli_query($GLOBALS['db'], "SELECT * FROM nwaLanguage");
		$i=1;
		while ($nwaLanguage = mysqli_fetch_array($result)) {
			$response -> $i = (object) [
				'selector'=>$nwaLanguage['selector'],
				'attribute'=>$nwaLanguage['attribute'],
				'trans'=>$nwaLanguage[$_GET['id']]
			];
			$i++;
		}
		done(200, json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	}
