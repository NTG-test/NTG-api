CREATE TABLE IF NOT EXISTS nwaLanguage (
	id INT(16) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	selector VARCHAR(256),
	attribute VARCHAR(32),
	enUS VARCHAR(256),
	fa VARCHAR(256)
) CHARSET=utf8 COLLATE utf8_unicode_ci;
