<?php

class nwaEnv
{
    public function __construct()
    {
        if(!file_exists('.env')) {
            throw new \InvalidArgumentException(sprintf('%s does not exist', '.env'));
        }

        if (!is_readable('.env')) {
            throw new \RuntimeException(sprintf('%s file is not readable', '.env'));
        }

        $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {

            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}