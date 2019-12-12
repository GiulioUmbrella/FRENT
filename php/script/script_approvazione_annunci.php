<?php
require_once "../classi/Frent.php";
require_once "../classi/Amministratore.php";

require_once "../CredenzialiDB.php";
session_start();

if (isset($_SESSION["manager"]) and isset($_SESSION["admin"])){
    
    $id = intval($_GET["idAnnuncio"]);
    $status = intval($_GET["approvato"]);
    
    $admin = $_SESSION["admin"];

    try {
        $db = new Database(CredenzialiDB::DB_ADDRESS, CredenzialiDB::DB_USER,
            CredenzialiDB::DB_PASSWORD,CredenzialiDB::DB_NAME);
    
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





