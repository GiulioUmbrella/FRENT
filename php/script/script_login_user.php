<?php
require_once ($_SERVER["DOCUMENT_ROOT"])."/php/classi/Database.php";
require_once ($_SERVER["DOCUMENT_ROOT"])."/php/classi/Frent.php";
require_once ($_SERVER["DOCUMENT_ROOT"])."/php/CredenzialiDB.php";

$nome = $_POST["user"];
$password = $_POST["password"];
try {
    $db = new Database(CredenzialiDB::DB_ADDRESS, CredenzialiDB::DB_USER,
        CredenzialiDB::DB_PASSWORD,CredenzialiDB::DB_NAME);

    $frent = new Frent($db);
    
    $user=$frent->login($nome,$password);
    
    if ($user != null) {
        session_start();
        $_SESSION["user"] = $user;
        header("Location: ../html/mio");
    } else {
        echo "password errato!.";
    }

} catch (Eccezione $e) {
    echo $e->getMessage();
}



