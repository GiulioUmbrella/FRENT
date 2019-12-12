<?php
require_once "../classi/Frent.php";
require_once "../classi/Amministratore.php";
session_start();

if (isset($_SESSION["manager"]) and isset($_SESSION["admin"])){
    
    $id = intval($_GET["idAnnuncio"]);
    $status = intval($_GET["approvato"]);
    
    $admin = $_SESSION["admin"];

    try {
        $db = new Database("localhost", "root", "","frentdb");
    
        $frent = new Frent($db, $admin);
        
        $res = $frent->adminEditStatoApprovazioneAnnuncio($id, $status);

        header("Location: ../pagine_php/approvazione_annunci.php");
    }catch(Eccezione $ex) {
        echo $ex->getMessage();
    }
}else{
    header("Location: ../../html/pag404.html");
//    echo "Session missing";
}





