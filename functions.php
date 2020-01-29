<?php
// Vars
static $pdo = null;

function getPDO() {
    global $pdo;
    $dbUsername = 'cfptportfolio';
    $dbPassword = 'cfpti2020';
    $dbHostname = 'localhost';
    $dbName = 'cfpti_portfolio';

    if ($pdo === null) {
        try {
            $pdo = new PDO('mysql:dbname=' . $dbName . ';host=' . $dbHostname, $dbUsername, $dbPassword, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
            ]);
            return $pdo;
        } catch (PDOException $e) {
            return null;
        }
    }

    return $pdo;
}
?>