<?php
require_once "load_Frent.php";

if (!isset($_SESSION["user"])){
    header("Location: ./login.php");
}
try{


if (isset($_GET["id"])){
    $idCommento = intval($_GET["id"]);
    // sfruttando il fatto che la prenotazione ha lo stesso id del commento
    if ($_SESSION["user"]->getIdUtente() !== $frent->getPrenotazione($idCommento)->getIdUtente()){
        $_SESSION["msg"] = htmlentities("Non hai il permesso per effettuare quest'operazione!");
        header("Location: ./error_page.php");
    }
    
    $res_code = $frent->deleteCommento($idCommento);
    if ($res_code === 0) {
        header("Location: ./riepilogo_prenotazione.php?id=$idCommento");
    } else { // $res_code == -1
        $_SESSION["msg"] = htmlentities("L'annuncio non è stato eliminato poiché si è verificato un errore.");
        header("Location: ./error_page.php");
    }
}}catch (Eccezione $e){
    $_SESSION["msg"] = $e->getMessage();
    header("Location: ./error_page.php");
}

    
