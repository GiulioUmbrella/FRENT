<?php
require_once "./class_Frent.php";
require_once "./class_Amministratore.php";

require_once "./class_CredenzialiDB.php";
session_start();

if (isset($_SESSION["manager"]) and isset($_SESSION["admin"])){
    
    $id = intval($_GET["idAnnuncio"]);
    $status = intval($_GET["approvato"]);
    
    $admin = $_SESSION["admin"];

    try {
        require_once "components/connessione_admin.php";
        $res = $manager->adminEditStatoApprovazioneAnnuncio($id, $status);

        header("Location: ./approvazione_annunci.php");
    }catch(Eccezione $ex) {
        echo $ex->getMessage();
    }
}else{
    header("Location: ./404.php");
//    echo "Session missing";
}





