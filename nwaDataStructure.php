<?php

function nwaCreateNwaDatabaseTables() {
	$GLOBALS['db']->query(
		"CREATE TABLE IF NOT EXISTS nwaRequest (
		id INT(16) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		time INT(4),
		userId INT(16),
		businessId INT(16),
		httpResponseCode INT(4),
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

	$GLOBALS['db']->query(
		"CREATE TABLE IF NOT EXISTS nwaLanguage (
		id INT(16) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		selector VARCHAR(256),
		attribute VARCHAR(32),
		enUS VARCHAR(256),
		fa VARCHAR(256)
		) CHARSET=utf8 COLLATE utf8_unicode_ci"
	);
}