<?php
require_once "../classi/Database.php";
require_once "../classi/Frent.php";
$nome = $_POST["user"];
$password = $_POST["password"];
try {
    $db = new Database("localhost", "root", "","frentdb");

    $frent = new Frent($db);
    
    $admin=$frent->adminLogin($nome,$password);
    
    if ($admin != null) {
        session_start();
        $_SESSION["admin_obj"] = $admin;
        header("Location: ../pagine_php/approvazione_annunci.php");
    } else {
        echo "password errato!.";
    }

} catch (Eccezione $e) {
    echo $e->getMessage();
}



