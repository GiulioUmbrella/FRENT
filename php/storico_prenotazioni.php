<?php
require_once "class_Frent.php";
require_once "class_Utente.php";
require_once "class_CredenzialiDB.php";
require_once "load_Frent.php";
try{
    if (isset($_SESSION["user"])) {
        if (isset($_SESSION["id"])) {
            $annuncio = $frent->getAnnuncio(intval($_SESSION["id"]));
            $pagina = file_get_contents("./components/storico_prenotazioni.html");
            $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
            $pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);
            $pagina = str_replace("<TITOLO/>", $annuncio->getTitolo(), $pagina);
            $pagina = str_replace("<IDANNUNCIO/>", $annuncio->getIdAnnuncio(),$pagina);
            $user = $_SESSION["user"];
            
            $prenotazioni = $frent->getPrenotazioniAnnuncio($annuncio->getIdAnnuncio());
            
            $data_corrente = date("Y-m-d");
            
            $prenotazioniPassate = "";
            $prenotazioneCorrente = "";
            $prenotazioniFuture = "";
            foreach ($prenotazioni as $prenotazione) {
                $numOspiti = $prenotazione->getNumOspiti();
                $dataInizio = $prenotazione->getDataInizio();
                $dataFine = $prenotazione->getDataFine();
                $durata = abs(strtotime($prenotazione->getDataFine()) - strtotime($prenotazione->getDataInizio())) / (3600 * 24);
                $totale = $durata * $annuncio->getPrezzoNotte() * $prenotazione->getNumOspiti();
                $guest = $frent->getUser($prenotazione->getIdUtente());
                $username = $guest->getUserName();
                $mail = $guest->getMail();
                $item = file_get_contents("./components/item_prenotazione_storico.html");
                
                $item = str_replace("<DI/>",$dataInizio,$item);
                $item = str_replace("<DF/>",$dataFine,$item);
                $item = str_replace("<MAIL/>",$mail,$item);
                $item = str_replace("<NO/>",$numOspiti,$item);
                $item = str_replace("<USERNAME/>",$username,$item);
                $item = str_replace("<TOTALE/>",$totale,$item);

                if ($prenotazione->getDataFine() < $data_corrente) {
                    $prenotazioniPassate .= $item;
                } else if ($prenotazione->getDataInizio() > $data_corrente) {
                    $prenotazioniFuture .= $item;
                } else {
                    $prenotazioneCorrente = $item;
                }
            }
            if ($prenotazioneCorrente==""){
                $pagina = str_replace("<PRENOTAZIONECORRENTE/>", "<li><p>Non ci sono prenotazioni in corso.</p></li>", $pagina);
                
            }else{
                $pagina = str_replace("<PRENOTAZIONECORRENTE/>", $prenotazioneCorrente, $pagina);
            }
            if ($prenotazioniFuture==""){
                $pagina = str_replace("<PRENOTAZIONIFUTURE/>", "<li><p>Non ci sono prenotazioni future.</p></li>", $pagina);
                
            }else{
                $pagina = str_replace("<PRENOTAZIONIFUTURE/>", $prenotazioniFuture, $pagina);
            }
            if ($prenotazioniPassate==""){
                $pagina = str_replace("<PRENOTAZIONIPASSATE/>", "<li><p>Non ci sono prenotazioni passate.</p></li>", $pagina);
        
            }else{
                $pagina = str_replace("<PRENOTAZIONIPASSATE/>", $prenotazioniPassate, $pagina);
            }
    
    
            echo $pagina;
        } else {
            header("Location: ./404.php");
        }
    } else {
        header("Location: ./login.php");
    }
    
}catch (Eccezione $ex){
    echo $ex->getMessage();
}
