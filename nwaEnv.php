<?php

namespace nwa;

class env
{
    /**
     * The directory where the .env file can be located.
     *
     * @var string
     */
    private $path;


    public function __construct(string $path)
    {
        if(!file_exists($path)) {
            exit($path.' does not exist');
        }
        $this->path = $path;

        if (!is_readable($this->path)) {
            exit($this->path.' file is not readable');
            throw new \RuntimeException(sprintf('%s file is not readable', $this->path));
        }

        $lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
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