<?php

class surah {
	public $id;
	public $name;
	public $period;

	function __construct($id, $name, $period) {
		$this->id = $id;
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
