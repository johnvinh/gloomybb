<?php
require_once 'config.php';

$pdo = null;
$host = DB_HOST;
$dbname = DB_NAME;
$user = DB_USER;
$password = DB_PASSWORD;

// Connect to the database
$dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_AUTOCOMMIT => false
];

function get_pdo(): PDO
{
    try {
        $pdo = new PDO($GLOBALS['dsn'], $GLOBALS['user'], $GLOBALS['password'], $GLOBALS['options']);
    } catch (PDOException $e) {
        throw new PDOException($e->getMessage(), (int)$e->getCode());
    }
    return $pdo;
}