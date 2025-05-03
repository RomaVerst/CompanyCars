<?php

class Autoloader
{
    public static function register()
    {
        spl_autoload_register(function ($class) {
            $file = $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/local-namespaces/'
                . str_replace('\\', DIRECTORY_SEPARATOR, mb_strtolower($class)) . '.php';

            if (file_exists($file)) {
                require $file;
                return true;
            }
            return false;
        });
    }
}
Autoloader::register();
