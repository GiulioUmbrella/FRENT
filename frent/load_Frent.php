<?php
/**
 * Modulo per caricare le classi del Database e Frent.
 * Operazioni svolte:
 * - caricamento classi
 * - apertura di un'istanza con il database (variabile $db) reperendo le credenziali dal file apposito
 * - apertura di un'istanza con le funzionalità di Frent offerte dall'omonimima classe (variabile $frent)
 * - apertura della sessione php e conseguemente reperimento dell'utente o dell'amministratore, se presente
 */
require_once "./class_Database.php";
require_once "./class_CredenzialiDB.php";
require_once "./class_Frent.php";

$db = new Database(
    CredenzialiDB::DB_ADDRESS,
    CredenzialiDB::DB_USER,
    CredenzialiDB::DB_PASSWORD,
    CredenzialiDB::DB_NAME
);

session_start();
if(isset($_SESSION["user"])) {
    $auth_user = $_SESSION["user"];
} else if(isset($_SESSION["admin"])) {
    $auth_user = $_SESSION["admin"];
}
try {
    $frent = (isset($auth_user)) ? new Frent($db, $auth_user) : new Frent($db);
}catch (Eccezione $ex){
    $_SESSION["msg"]= $ex->getMessage();
    header("Location: ./error_page.php");
}
