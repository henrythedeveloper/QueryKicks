<?php
class Database {
    private static $host = 'localhost';
    private static $dbname = 'query_kicks';
    private static $username = 'root';
    private static $password = '';

    public static function connect() {
        $pdo = new PDO("mysql:host=".self::$host.";dbname=".self::$dbname, self::$username, self::$password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }
}
?>
