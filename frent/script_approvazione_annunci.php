<?php
require_once "./class_Frent.php";
require_once "./class_Amministratore.php";

require_once "load_Frent.php";
session_start();

if (isset($_SESSION["admin"])){
    
    $id = intval($_GET["idAnnuncio"]);
    $status = intval($_GET["approvato"]);
    
    $admin = $_SESSION["admin"];

    try {
        require_once "load_Frent.php";
        $res = $frent->adminEditStatoApprovazioneAnnuncio($id, $status);

        header("Location: ./approvazione_annunci.php");
    }catch(Eccezione $ex) {
        echo $ex->getMessage();
    }
}else{
    header("Location: ./404.php");
}




