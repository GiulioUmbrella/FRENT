<?php
require_once "../classi/Frent.php";
session_start();

if (isset($_SESSION["frent"]) and isset($_SESSION["admin_obj"])){

    require_once "../classi/Amministratore.php";
    require_once "../classi/Frent.php";
    $id = $_GET["idAnnuncio"];
    $status = $_GET["approvato"];

    try {

        $res = $_SESSION["frent"]->adminEditStatoApprovazioneAnnuncio($id,boolval($status));

        header("Location: ../pagine_php/approvazione_annunci.php");
    }catch(Eccezione $ex) {
        echo $ex->getMessage();
    }
}else{
    echo "Session missing";
}





