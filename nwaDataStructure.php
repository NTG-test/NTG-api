<?php

function nwaCreateNwaDatabaseTables() {
	if (!$GLOBALS['db']->query('DESCRIBE nwaRequest')) {
		$GLOBALS['db']->query(
			"CREATE TABLE nwaRequest (
			id INT(16) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			time INT(4),
			userId INT(16),
			businessId INT(16),
			statusCode INT(4),
			controller VARCHAR(32),
			method VARCHAR(16),
			action VARCHAR(32),
			requestedId VARCHAR(256),
			remoteAddr VARCHAR(32),
			token VARCHAR(512),
			tokenId INT(16),
			response LONGTEXT
			) CHARSET=utf8 COLLATE utf8_unicode_ci"
		);
		$GLOBALS['nwaApi']->massage('Table nwaRequest created.');
	}
	if (!$GLOBALS['db']->query('DESCRIBE nwaLanguage')) {
		$GLOBALS['db']->query(
			"CREATE TABLE nwaLanguage (
			id INT(16) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			selector VARCHAR(256),
			attribute VARCHAR(32),
			enUS VARCHAR(256),
			fa VARCHAR(256)
			) CHARSET=utf8 COLLATE utf8_unicode_ci"
		);
		$GLOBALS['nwaApi']->massage('Table nwaLanguage created.');
	}
}