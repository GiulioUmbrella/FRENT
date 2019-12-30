<?php
require_once "load_Frent.php";

if (isset($_SESSION["user"])){
    
    if (isset($_GET["id"])){
        $idAnnuncio = intval($_GET["id"]);
        if ($_SESSION["user"]->getIdUtente()!=$frent->getAnnuncio($idAnnuncio)->getIdHost()){
            $_SESSION["msg"] = htmlentities("Non hai il permesso per effettuare quest'operazione!");
            header("Location: ./error_page.php");
        }
        
        $res_code = $frent->deleteAnnuncio($idAnnuncio);
        if ($res_code == 0)
            header("Location: ./miei_annunci.php");
        elseif ($res_code==-1){
            $_SESSION["msg"] = htmlentities("l'annuncio non è eliminabile perchè ci sono prenotazioni in corso o future");
        }elseif ($res_code==-2){
            $_SESSION["msg"] = htmlentities("Eliminazione fallita!");
        }
        header("Location: ./error_page.php");
    }
}
else{
    header("Location: ./login.php");
}
