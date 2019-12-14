<?php
require_once "../classi/Database.php";
require_once "../classi/Frent.php";

require_once "../CredenzialiDB.php";
$nome = $_POST["user"];
$password = $_POST["password"];
try {
    $db = new Database(CredenzialiDB::DB_ADDRESS, CredenzialiDB::DB_USER,
        CredenzialiDB::DB_PASSWORD, CredenzialiDB::DB_NAME);
    
    $frent = new Frent($db);
    
    $admin = $frent->adminLogin($nome, $password);
    
    session_start();
    $_SESSION["admin"] = $admin;
    header("Location: ../pagine_php/approvazione_annunci.php");
    
    
} catch (Eccezione $e) {
    header("Location: ../pagine_php/login_admin.php?error_code=1");
    
    echo $pagina;
}



