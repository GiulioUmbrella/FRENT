<?php
require "../php/classi/Database.php";

$db = new Database("localhost", "root", "", "frentdb");

$nome = $_POST["user"];
$password = $_POST["password"];
//try {
    $db->connect();
    $res = $db->queryProcedure('admin_login("'.$nome.'","'.$password.'");');
    if (count($res) != 0) {
        session_start();
        $_SESSION["db"] = $db;
        echo "Logged in successfully";
        header("Location: ../html/approvazione_annunci.html");
    } else {
        echo "password errato!.";
    }

//} catch (Eccezione $e) {
//    echo $e->getMessage();
//}



