<?php

class surah {
	public $name;
	public $period;

	function __construct($name, $period) {
		$this->name = $name;
		$this->period = $period;
	}
}

function GET() {
	$result = $GLOBALS['db']->query("SELECT * FROM quranSurah");
	while($row = $result->fetch_assoc()) {
		array_push(
			$GLOBALS['nwaApi']->data,
			$nwaPhrase = new surah(
				$row['id'],
				$row['name'],
				$row['period']
			)
		);
	}

	$GLOBALS['nwaApi']->done(200);
}
