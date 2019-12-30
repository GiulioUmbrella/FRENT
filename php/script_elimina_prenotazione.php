<?php
require_once "load_Frent.php";

if (isset($_GET["id"])){
    echo "trovato id =".$_GET["id"];
    $res_code=$frent->deleteOccupazione(intval($_GET["id"]));
    if ($res_code == 0)
        header("Location: ./mie_prenotazioni.php");
    elseif ($res_code==-1){
        $_SESSION["msg"] = htmlentities("l'annuncio non è eliminabile perchè ci sono prenotazioni in corso o future");
    }elseif ($res_code==-2){
        $_SESSION["msg"] = htmlentities("Eliminazione fallita!");
    }
    header("Location: ./error_page.php");
}
//header("Location: ./mie_prenotazioni.php");