<?php
$pagina = file_get_contents("./components/riepilogo_prenotazione.html");
require_once "load_Frent.php";
if (isset($_SESSION["user"])){
    $pagina= str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"),$pagina);
    $pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"),$pagina);
    
    if (isset($_SESSION["prenotazione"]) or isset($_GET["idOccupazione"])){
        if (isset($_SESSION["prenotazione"])){
            $prenotazione = $_SESSION["prenotazione"];
            $id_prenotazione = $frent->insertOccupazione($prenotazione->getIdAnnuncio(),$prenotazione->getNumOspiti() ,
                $prenotazione->getDataInizio(),
                $prenotazione->getDataFine());
            
        }else{
            $id_prenotazione = $_GET["idOccupazione"];
            $frent->get
        }
        
        $prenotazione = $frent->getPrenotazioniGuest(intval($id_prenotazione));
        
        
    }else{
        header("Location: ./404.php");
    }
    echo $pagina;
}else{
    header("Location: ./login.php");
}
