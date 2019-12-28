<?php
$pagina = file_get_contents("./components/riepilogo_prenotazione.html");
require_once "load_Frent.php";
if (isset($_SESSION["user"])) {
    $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
    $pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);
//    todo sisteamre il nodo per arrivare al riepilogo prenotazione
    
    
    if (isset($_SESSION["prenotazione"]) or isset($_GET["id"])) {
        $prenotazione = "";
        $id_prenotazione = 0;
        if (isset($_SESSION["prenotazione"])) {
            $prenotazione = $_SESSION["prenotazione"];
            $id_prenotazione = $frent->insertOccupazione($prenotazione->getIdAnnuncio(), $prenotazione->getNumOspiti(),
                $prenotazione->getDataInizio(),
                $prenotazione->getDataFine()
            );
            
        } else {
            $id_prenotazione = $_GET["id"];
        }
        $prenotazione = $frent->getPrenotazioniGuest(intval($id_prenotazione));
        $annuncio = $frent->getAnnuncio($prenotazione->getIdAnnuncio());
        
        $pagina = str_replace("<IDPRENOTAZIONE/>", $prenotazione->getIdOccupazione(), $pagina);
        $pagina = str_replace("<DATAINIZIO/>", $prenotazione->getDataInizio(), $pagina);
        $pagina = str_replace("<DATAFINE/>", $prenotazione->getDataFine(), $pagina);
        $pagina = str_replace("", $prenotazione->getIdOccupazione(), $pagina);
        
        $pagina = str_replace("<NOMEANNUNCIO/>", $annuncio->getTitolo(), $pagina);
        
        
        echo $pagina;
    } else {
        echo "header";
//        header("Location: ./404.php");
    }
} else {
    header("Location: ./login.php");
}
