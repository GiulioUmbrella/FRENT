<?php
//pagina totalmente funzionante e valida
require_once "./class_Database.php";
require_once "./class_Frent.php";
require_once "./class_Occupazione.php";
require_once "./class_CredenzialiDB.php";

require_once "load_Frent.php";
$pagina = file_get_contents("./components/mie_prenotazioni.html");
if (isset($_SESSION["user"])) {
    require_once "./load_header.php";
//    $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
    $pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);
    
    $content = "";
    // pescare le prenotazioni correnti
    
    $occupazioni = $frent->getPrenotazioniGuest();
//    $occupazioni = array();
    $i = 5;
    $prenotazioni_future = " <h1>Le mie prenotazioni future</h1><ul id=\"prenotazioni_future\">";
    $prenotazioni_passate = "<h1>Le mie prenotazioni Passate</h1><ul id=\"prenotazioni_passate\">";
    $prenotazioni_correnti = "  <h1>Le mie prenotazioni correnti</h1><ul id=\"prenotazioni_correnti\">";
    $data_corrente = date("Y-m-d");
    
    $numPrenotazioniPassate = 0;
    $numPrenotazioniCorrenti = 0;
    $numPrenotazioniFuture = 0;
    foreach ($occupazioni as $prenotazione) {
        $id_prenotazione = $prenotazione->getIdOccupazione();
        $annuncio = $frent->getAnnuncio($prenotazione->getIdAnnuncio());
        $host = $frent->getUser($annuncio->getIdHost());
        $mail = $host->getMail();
        $nomeAnnuncio = $annuncio->getTitolo();
        $descrizionefoto = "";//todo decide cose mettere nell'attributo ALT dell'anteprima
        $luogoAlloggio = $annuncio->getIndirizzo()." citt&agrave;: ".$annuncio->getCitta();
        $dataInizio = $prenotazione->getDataInizio();
        $dataFine = $prenotazione->getDataFine();
        $totalePrenotazione = 0.0;
        $durata=abs(strtotime($prenotazione->getDataFine())-strtotime($prenotazione->getDataInizio()))/(3600*24);
        $prezzo = $annuncio->getPrezzoNotte()* intval($durata) * $prenotazione->getNumOspiti();
        $path = $annuncio->getImgAnteprima();
        $numeroOspiti = $prenotazione->getNumOspiti();
        if ($prenotazione->getDataFine() < $data_corrente) { // prenotazioni passate

            $numPrenotazioniPassate++;
            $prenotazioni_passate .= "
                    <li>
                        <div class=\"intestazione_lista\">
                            <a href=\"./riepilogo_prenotazione.php?id=$id_prenotazione\" tabindex=\"<TABINDEX$i>\"
                                title=\"Vai al riepilogo della prenotazione presso Casa Loreto\">[#$id_prenotazione] $nomeAnnuncio</a>
                        </div>";
            $i++;
            $prenotazioni_passate.="
                        <div class=\"corpo_lista lista_flex\">
                            <div class=\"dettagli_prenotazione\">
                                <img src=\"$path\" alt=\"Immagine di anteprima di Casa Loreto\"/>
                                <p>Luogo: $luogoAlloggio</p>
                                <p>Periodo: $dataInizio - $dataFine</p>
                                <p>Numero ospiti: $numeroOspiti</p>
                            </div>
                            <div class=\"opzioni_prenotazione\">
                                <p>Prezzo: $prezzo&euro;</p><a href=\"Commenta\" tabindex=\"<TABINDEX$i>\" title=\"Contatta il proprietario per posta elettronica\">Commenta</a>
                            </div>
                        </div>
                    </li>";// todo da decidere come far commentare
        } elseif ($data_corrente <= $prenotazione->getDataFine() and $data_corrente >= $prenotazione->getDataInizio()) { //prenotazioni correnti
            
            $numPrenotazioniCorrenti++;
            $prenotazioni_correnti .= "
                <li>
                    <div class=\"intestazione_lista\">
                        <a href=\"./riepilogo_prenotazione.php?id=$id_prenotazione\" tabindex=\"<TABINDEX$i>\"
                            title=\"Vai al riepilogo della prenotazione presso $nomeAnnuncio\">[#$id_prenotazione] Soggiorno presso $nomeAnnuncio</a> </div>";
            $i++;
            $prenotazioni_correnti.="
                    <div class=\"corpo_lista lista_flex\">
                        <div class=\"dettagli_prenotazione\">
                            <img src=\"$path\" alt=\"$descrizionefoto\"/>
                            <p>Luogo: $luogoAlloggio</p><p>Periodo:$dataInizio - $dataFine</p>
                                <p>Numero ospiti: $numeroOspiti</p>
                        </div>
                        <div class=\"opzioni_prenotazione\">
                            <p>Prezzo: $prezzo&euro;</p>
                            <a href=\"mailto:$mail\" tabindex=\"<TABINDEX$i>\"
                                    title=\"Contatta il proprietario per posta elettronica\">Contatta il proprietario</a>
                        </div>
                    </div>
                </li>";
        
        } else if ($data_corrente < $prenotazione->getDataInizio()) { //prenotazioni future
            
            $numPrenotazioniFuture++;
            $prenotazioni_future .= "
                <li>
                    <div class=\"intestazione_lista\">
                        <a href=\"./riepilogo_prenotazione.php?id=$id_prenotazione\" tabindex=\"<TABINDEX$i>\"
                        title=\"Vai al riepilogo della prenotazione presso Casa Loreto\">[#$id_prenotazione] $nomeAnnuncio</a>
                    </div>";
            $i++;
            $prenotazioni_future.="
                    <div class=\"corpo_lista lista_flex\">
                        <div class=\"dettagli_prenotazione\">
                            <img src=\"$path\" alt=\"$descrizionefoto\"/>
                            <p>Luogo: $luogoAlloggio</p>
                            <p>Periodo: $dataInizio - $dataFine</p>
                                <p>Numero ospiti: $numeroOspiti</p>
                        </div>
                        <div class=\"opzioni_prenotazione\">
                            <p>Prezzo: $prezzo&euro;</p>
                            <form action=\"./script_elimina_prenotazione.php?id=$id_prenotazione\" method=\"post\">
                                <fieldset>
                                    <legend class=\"aiuti_alla_navigazione\">Elimina prenotazione</legend>
                                    <input type=\"hidden\" id=\"id\" value=\"$id_prenotazione\"/>
                                    <input type=\"submit\" id=\"elimina\" value=\"elimina\" tabindex=\"<TABINDEX$i>\" title=\"Elimina la prenotazione per $nomeAnnuncio\"/>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                </li>";
        }
        $i = $i + 1;
    }


   if($numPrenotazioniCorrenti!=0 or $prenotazioni_future!=0 or $prenotazioni_passate!=0){
       $pagina = str_replace("<FLAG/>","<PRENOTAZIONIPASSATE/><PRENOTAZIONICORRENTI/><PRENOTAZIONIFUTURE/>",$pagina);
       if ($numPrenotazioniPassate > 0) {
           $prenotazioni_passate .= "</ul>";
           $pagina = str_replace("<PRENOTAZIONIPASSATE/>", $prenotazioni_passate, $pagina);
        
       }else{
           $pagina = str_replace("<PRENOTAZIONIPASSATE/>", "", $pagina);
        
       }
       if ($numPrenotazioniCorrenti > 0) {
           $prenotazioni_correnti .= "</ul>";
           $pagina = str_replace("<PRENOTAZIONICORRENTI/>", $prenotazioni_correnti, $pagina);
        
       }else{
           $pagina = str_replace("<PRENOTAZIONICORRENTI/>", "", $pagina);
       }
       if ($numPrenotazioniFuture > 0) {
           $prenotazioni_future .= "</ul>";
           $pagina = str_replace("<PRENOTAZIONIFUTURE/>", $prenotazioni_future, $pagina);
       }else{
           $pagina = str_replace("<PRENOTAZIONIFUTURE/>", "", $pagina);
        
       }
       for ($j=5; $j<$i; $j=$j+1){
           $pagina= str_replace("<TABINDEX$j>",$j,$pagina);
       }
   }else{
       $pagina= str_replace("<FLAG/>","<h1>Non ci sono prenotazioni!</h1>",$pagina);
   }
    echo $pagina;
} else {
    header("Location: login.php");
}