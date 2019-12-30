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
            
            $prenotazioni = $frent->getOccupazioniAnnuncio($annuncio->getIdAnnuncio());
            
            $data_corrente = date("Y-m-d");
            
            $prenotazioniPassate = "";
            $prenotazioneCorrente = "";
            $prenotazioniFuture = "";
            foreach ($prenotazioni as $prenotazione) {
                $numOspiti = $prenotazione->getNumOspiti();
                $dataInizio = $prenotazione->getDataInizio();
                $dataFine = $prenotazione->getDataFine();
                $totale = (strtotime($dataFine) - strtotime($dataInizio)) / (24 * 3600) * $annuncio->getPrezzoNotte();
                $guest = $frent->getUser($prenotazione->getIdUtente());
                $username = $guest->getUserName();
                $mail = $guest->getMail();
                $p = "<li>
            <div class=\"corpo_lista lista_storico_prenotazioni\">
                <ul>
                    <li><span xml:lang=\"en\" lang='en' class=\"intestazione_campo\">Username:</span>
                        <a href=\"mailto:".$mail."\" tabindex=\"9\" title=\"manda una mail al guest\">$username</a></li>
                    <li class=\"intestazione_campo\">Numero ospiti: $numOspiti</li>
                    <li class=\"intestazione_campo\">Data inizio: $dataInizio</li>
                    <li class=\"intestazione_campo\">Data fine: $dataFine</li>
                    <li class=\"intestazione_campo\">Totale prenotazione: $totale&euro;</li>
                </ul>
            </div>
        </li>";
                if ($prenotazione->getDataFine() < $data_corrente) {
                    $prenotazioniPassate .= $p;
                } else if ($prenotazione->getDataInizio() > $data_corrente) {
                    $prenotazioniFuture .= $p;
                } else {
                    $prenotazioneCorrente = $p;
                }
            }
            if ($prenotazioneCorrente==""){
                $pagina = str_replace("<PRENOTAZIONECORRENTE/>", "<li><p>Non ci sono prenotazioni attuali!</p></li>", $pagina);
                
            }else{
                $pagina = str_replace("<PRENOTAZIONECORRENTE/>", $prenotazioneCorrente, $pagina);
            }
            if ($prenotazioniFuture==""){
                $pagina = str_replace("<PRENOTAZIONIFUTURE/>", "<li><p>Non ci sono prenotazioni future!</p></li>", $pagina);
                
            }else{
                $pagina = str_replace("<PRENOTAZIONIFUTURE/>", $prenotazioniFuture, $pagina);
            }
            if ($prenotazioniPassate==""){
                $pagina = str_replace("<PRENOTAZIONIPASSATE/>", "<li><p>Non ci sono prenotazioni passate!</p></li>", $pagina);
        
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
