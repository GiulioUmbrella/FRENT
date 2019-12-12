<?php
require_once "../classi/Frent.php";
require_once "../classi/Amministratore.php";
session_start();

if (isset($_SESSION["frent"]) and isset($_SESSION["admin"])){
    
    $id = $_GET["idAnnuncio"];
    $status = $_GET["approvato"];

    try {
        
        $res = $_SESSION["frent"]->adminEditStatoApprovazioneAnnuncio($id,boolval($status));

        header("Location: ../pagine_php/approvazione_annunci.php");
    }catch(Eccezione $ex) {
        echo $ex->getMessage();
    }
}else{
    header("Location: ../../html/pag404.html");
//    echo "Session missing";
}





