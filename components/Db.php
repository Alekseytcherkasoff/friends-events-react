<?php
class Db
{
    private static $connection = null;

    public static function getConnection()
    {
        if (self::$connection === null) {
            $params = include(ROOT . '/config/db_params.php');

            $dsn = "mysql:host={$params['host']};dbname={$params['dbname']};charset=utf8mb4";
            self::$connection = new PDO($dsn, $params['user'], $params['password'], [
                // ошибки SQL приходят исключениями, а не молча возвращают false
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }

        return self::$connection;
    }
}
