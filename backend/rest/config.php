<?php

// Set the reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ (E_NOTICE | E_DEPRECATED));

class Config
{
    public static function DB_NAME()
    {
        return Config::get_env('DB_NAME', 'furniture_store'); 
    }
    public static function DB_PORT()
    {
        return Config::get_env('DB_PORT', '3309');
    }
    public static function DB_USER()
    {
        return Config::get_env('DB_USER', 'root');
    }
    public static function DB_PASSWORD()
    {
        return Config::get_env('DB_PASSWORD', '');
    }
    public static function DB_HOST()
    {
        return Config::get_env('DB_HOST', 'localhost');
    }

    public static function JWT_SECRET() 
    {
        return Config::get_env('JWT_SECRET', '9453cf215a0e5013d123efa8896fcd9079d63a45');
    }

    public static function get_env($name, $default){
       return isset($_ENV[$name]) && trim($_ENV[$name]) != "" ? $_ENV[$name] : $default;
   }

}