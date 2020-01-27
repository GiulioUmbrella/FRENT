<?php
require_once "load_Frent.php";

if (isset($_SESSION["admin"])){
    
    $id = intval($_GET["idAnnuncio"]);
    $status = intval($_GET["approvato"]);
    
    try {
        $res = $frent->adminEditStatoApprovazioneAnnuncio($id, $status);

        header("Location: ./approvazione_annunci.php");
    }catch(Eccezione $ex) {
        echo $ex->getMessage();
    }
}else{
    header("Location: ./404.php");
}





