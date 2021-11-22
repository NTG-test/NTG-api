<?php

namespace app;

function createDatabaseTables() {
	if (!$GLOBALS['db'] -> query('DESCRIBE appCoins')) {
		$GLOBALS['db'] -> query(
			"CREATE TABLE IF NOT EXISTS appCoins (
			id INT(16) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			cmcId INT(16),
			symbol VARCHAR(256),
			name VARCHAR(1024),
			platformCmcId INT(16),
			platformSymbol VARCHAR(128),
			platformContract VARCHAR(1024)
			) CHARSET=utf8 COLLATE utf8_unicode_ci"
		);
	}
}