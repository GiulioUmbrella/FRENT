<?php
require_once "./class_Database.php";
require_once "./class_Frent.php";
require_once "./class_Occupazione.php";
require_once "./class_CredenzialiDB.php";
//todo da rifare il modo di distiguere i 3 tipi di prenotazioni: utilizzare una funzione che trova la data corrente,
// e quindi suddividerli, altrimenti è codice ripetuto.
$pagina = file_get_contents("./components/mie_prenotazioni.html");
session_start();
if (isset($_SESSION["user"])) {
    $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
    $pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);
    
    $content = "";
    // pescare le prenotazioni correnti
    $frent = new Frent(new Database(CredenzialiDB::DB_ADDRESS, CredenzialiDB::DB_USER,
            CredenzialiDB::DB_PASSWORD, CredenzialiDB::DB_NAME)
        , $_SESSION["user"]);
    $occupazioni = $frent->getPrenotazioniGuest();
    $i = 5;
    $prenotazioni_future="";
    $prenotazioni_passate="";
    $prenotazioni_correnti="";
    
    foreach ($occupazioni as $prenotazione) {
        
        
        $id_prenotazione = $prenotazione->getIdOccupazione();
        $annuncio = $frent->getAnnuncio($prenotazione->getIdAnnuncio());
        $mail = "";
        $nomeAnnuncio = "";
        $descrizionefoto = "";
        $luogoAlloggio = "";
        $dataInizio = "";
        $dataFine = "";
        $totalePrenotazione = 0.0;
        $path="";
        
        if(){ // prenotazioni passate
            $content.="<li>
            <div id=\"ID_PRENOTAZIONE_1\" class=\"intestazione_lista\">
                <a href=\"../../html/riepilogo_prenotazione.html\" tabindex=\"$i\"
                   title=\"Vai al riepilogo della prenotazione presso Casa Loreto\">[#id] $titoloAnnuncio</a></div>
            <div class=\"corpo_lista lista_flex\">
                <div class=\"dettagli_prenotazione\">
                    <img src=\"../../immagini/img3.jpg\" alt=\"Immagine di anteprima di Casa Loreto\"/>
                    <p>LUOGO DI ALLOGGIO</p><p>DATA/INIZIO - DATA/FINE</p></div><div class=\"opzioni_prenotazione\">
                    <p>PREZZO</p><a href=\"Commenta\" title=\"Contatta il proprietario per posta elettronica\">Commenta</a>
                </div></div></li>";
        }elseif (){ //prenotazioni correnti
            $content .= "<li><div id=\"ID_PRENOTAZIONE_2\" class=\"intestazione_lista\"><a href=\"./riepilogo_prenotazione.php?id=$id_prenotazione\" tabindex=\"$i\"
        title=\"Vai al riepilogo della prenotazione presso $nomeAnnuncio\">[#$id_prenotazione] Soggiorno presso $nomeAnnuncio</a>
         </div><div class=\"corpo_lista lista_flex\"><div class=\"dettagli_prenotazione\"><img src=\"$path\" alt=\"$descrizionefoto\"/>";
            $content .= "<p>$luogoAlloggio</p><p>$dataInizio- $dataFine</p></div><div class=\"opzioni_prenotazione\"><p>$totalePrenotazione</p>
        <a href=\"mailto:$mail\" title=\"Contatta il proprietario per posta elettronica\">Contatta il proprietario</a></div></div></li>";
    
        
        }else{ //prenotazioni future
            $content .= "<li><div id=\"ID_PRENOTAZIONE_3\" class=\"intestazione_lista\">
                <a href=\"./riepilogo_prenotazione.php\" tabindex=\"$i\"
                   title=\"Vai al riepilogo della prenotazione presso Casa Loreto\">[#$id_prenotazione] $titoloAnnuncio</a>
            </div><div class=\"corpo_lista lista_flex\">
                <div class=\"dettagli_prenotazione\">
                    <img src=\"$path\" alt=\"$descrizionefoto\"/>
                    <p>$luogoAlloggio</p>
                    <p>$dataInizio - $dataFine</p></div>
                <div class=\"opzioni_prenotazione\"><p>$prezzo</p>
            <form action=\"../script/elimina_prenotazione.php\" method=\"post\">
                <fieldset>
                    <legend class=\"aiuti_alla_navigazione\">Elimina prenotazione</legend>
                    <input type=\"hidden\" value=\"$id_prenotazione\"/>
                    <input type=\"submit\" value=\"Elimina\" title=\"Elimina la prenotazione per $titoloAnnuncio\"/>
                </fieldset></form></div></div></li>";
        }
        $i = $i + 1;
    }
    
    
    $pagina = str_replace("<PRENOTAZIONIFUTURE/>", $content, $pagina);
    
    $pagina = str_replace("<PRENOTAZIONICORRENTI/>", $content, $pagina);
    
    ;
    
    
    
//    $prenotazioni = $frent->
    foreach ($occupazioni as $prenotazioni_passate){
       
        $i=$i+1;
    
    }
    
    
    $pagina = str_replace("<PRENOTAZIONIPASSATE/>", $content, $pagina);
    
    
    echo $pagina;
} else {
    header("Location: login.php");
}