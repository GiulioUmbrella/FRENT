<?php
require_once "load_Frent.php";
require_once "./components/form_functions.php";
// valida, e dovrebbe funzionare sempre, forse ho mancato qualche test case
/**
 * quando l'annuncio non approvato è visualizzato dal proprietario, il pulsante "Prenota" deve sparire, e deve
 * essere visualizza il pulsante "Modifica", reindirizzandolo verso la pagina della modifica dell'annuncio
 * e quindi sparisce anche i due form di data.
*/

try {
    /**
     * quando annuncio non è stato approvato, fare dei controlli che non può essere visualizzato,
     * il controllo avviene attraverso il controllo dell'auth presente in session, se è admin allora
     * può visualizzare, se è utente o null allora possono visualizzare solo gli annunci nello stato di approvazione = 1
     */
    
    if (!isset($_GET["id"])) {
        header("Location: ./404.php");
    }
    
    $id = intval($_GET["id"]);
    $_SESSION["id"] = $id;
    $annuncio = $frent->getAnnuncio($id); // SE NON TROVATO, LANCIO ECCEZIONE
    // se non sono ne admin ne user e annuncio non è stato approvato, non posso vederlo.
    // se sono user ma non sono host e annuncio non è stato approvato, non posso vederlo.
    if (
        (!isset($_SESSION["admin"]) and !isset($_SESSION["user"]) and $annuncio->getStatoApprovazione() != 1) or
        (isset($_SESSION["user"]) and $_SESSION["user"]->getIdUtente() != $annuncio->getIdHost() and $annuncio->getStatoApprovazione() != 1)
    ) {
        header("Location: ./404.php");
    }
    
    $_SESSION["annuncio"] = $annuncio; 
    
    $pagina = file_get_contents("./components/dettagli_annuncio.html");

    /**
     * Modalità admin:
     * - il breadcrumb è solo il nome dell'annuncio
     * - niente footer
     * - visualizzazione foto antemprima, info sull'annuncio e pulsanti approvazione/rigetto
     */
    if (isset($_SESSION["admin"])) { // visualizzazione da parte di un amministratore
        $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_admin_logged.html"), $pagina);
        $pagina = str_replace("<FOOTER/>", "", $pagina); // footer rimosso volontariamente
        $pagina = str_replace("<BREADCRUMB/>", $annuncio->getTitolo(), $pagina);
        $pagina = str_replace("<FLAG/>", file_get_contents("./components/dettaglio_annuncio_admin.html"), $pagina);
        $pagina = str_replace("<PARAMS_SI/>", "?idAnnuncio=" . $annuncio->getIdAnnuncio() . "&approvato=1", $pagina);
        $pagina = str_replace("<PARAMS_NO/>", "?idAnnuncio=" . $annuncio->getIdAnnuncio() . "&approvato=0", $pagina);
    }
    /**
     * Modalità utente:
     * - il breadcrumb è il percorso completo per arrivare all'annuncio
     * - footer di tutte le pagine
     * - pulsante di modifica annuncio se l'utente è host, altrimenti inserisco il form per la prenotazione
     */    
    elseif (isset($_SESSION["user"])) {// utente autenticato
        $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
        
        if ($annuncio->getIdHost() == $_SESSION["user"]->getIdUtente()) { // host dell'annuncio
            $pagina = str_replace("<FLAG/>", file_get_contents("./components/dettaglio_annuncio_host.html"), $pagina);
            $pagina = str_replace("<ID/>", $annuncio->getIdAnnuncio(), $pagina);
            $_SESSION["id_annuncio"] = $annuncio->getIdAnnuncio();
        } else {// utente guest
            $pagina = str_replace("<FLAG/>", file_get_contents("./components/dettaglio_annuncio_visitatore_no_dati.html"), $pagina);
            $pagina = str_replace("<LINK/>", "./script_controllo_dati_prenotazione.php", $pagina);
        }
    }
    /**
     * Modalità utente non autenticato:
     * - header non autenticato
     * - mostro la funzionalità di accesso (link a pagina login)
     */
    else { // visitatore anonimo, non autenticato
        $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_no_logged.html"), $pagina);
        $pagina = str_replace("<FLAG/>", file_get_contents("./components/dettaglio_annuncio_non_autenticato.html"), $pagina);
        $pagina = str_replace("<LINK/>", "./login.php", $pagina);
    }
    
    /**
     * Impostazioni della ricerca da visualizzare.
     * L'admin certamente non le deve visualizzare, quindi lo escludo e gli faccio saltare tutto questo codice.
     * L'utente non loggato deve prima accedere per poter effettuare la prenotazione.
     */
    if(!isset($_SESSION["admin"])) {
        $pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);
        $pagina = str_replace(
            "<BREADCRUMB/>",
            "<a href=\"./index.php\" title=\"Vai alla pagina delle ricerca annunci\">" .
            "<span xml:lang=\"en\" lang=\"en\">Home</span></a> &gt;&gt; <a href=\"./risultati.php?<PARAMS>\">Ricerca</a>" .
            "&gt;&gt; <TITOLO_ANNUNCIO/>",
            $pagina
        );
        require_once "./components/setMinMaxDates.php";
        
        $link_ricerca = "citta=".$annuncio->getCitta();
        
        if (isset($_GET["dataInizio"])) {
            $dataInizio = $_GET["dataInizio"];
            $pagina = str_replace("<VALUEINIZIO/>", $dataInizio, $pagina);
            $link_ricerca.="&dataInizio=".$dataInizio;
        } else {
            $pagina = str_replace("<VALUEINIZIO/>", "", $pagina);
        }
        
        if (isset($_GET["dataFine"])) {
            $dataFine = $_GET["dataFine"];
            $pagina = str_replace("<VALUEFINE/>", $dataFine, $pagina);
            $link_ricerca.="&dataFine=".$dataFine;
        } else {
            $pagina = str_replace("<VALUEFINE/>", "", $pagina);
        }
        
        if (isset($_GET["numOspiti"])) {
            $numOspiti = intval($_GET["numOspiti"]);
            $link_ricerca.="&numOspiti=".$numOspiti;
            $pagina = str_replace("<VALUENUMERO/>", $numOspiti, $pagina);
        } else {
            $pagina = str_replace("<VALUENUMERO/>", 1, $pagina);
        }

        $pagina = str_replace("<PARAMS>", $link_ricerca, $pagina);
    }

    if (isset($_SESSION["dati_errati"]) and $_SESSION["dati_errati"]) {
        $pagina = str_replace("<INFO_BOX/>", $_SESSION["msg"], $pagina);
        unset($_SESSION["dati_errati"]);
    } else {
        $pagina = str_replace("<INFO_BOX/>", "", $pagina);
    }
    
    /**
     * Gestisco le eccezioni dei commenti separatamente.
     * Voglio comunque mostrare la pagina se riesco.
     * Se c'è qualche problema, salto la parte dei commenti e basta.
     */
    $str_commenti = "<p>Ancora Non ci sono commenti!</p>"; 
    $mediaCommenti = 0;
    try {
        $commenti = $frent->getCommentiAnnuncio($id);
        if (count($commenti) > 0) {
            $totale = 0;
            $str_commenti = "<ul>";
            foreach ($commenti as $commento) {
                $totale += intval($commento->getValutazione());
                $user=$commento->getUtente();
                $user_name =$user->getUserName();
                $immagine_profilo = uploadsFolder() . $user->getImgProfilo();
                $testo_commento = $commento->getCommento();
                $votazione = $commento->getValutazione();
                $data_commento = date("Y-m-d", strtotime($commento->getDataPubblicazione()));
                $titolo_commento = $commento->getTitolo();
                $str_commenti .= "
                        <li>
                            <div class=\"intestazione_commento\">
                                <img src=\"$immagine_profilo\" alt=\"\"/>
                                <div>
                                    <p class=\"username_commento\">$user_name</p>
                                    <p class=\"data_commento\">$data_commento</p>
                                </div>
                            </div>
                            <div class=\"corpo_commento\">
                                <h1>$titolo_commento</h1>
                                <p>Votazione: $votazione</p>
                                <p>$testo_commento</p>
                            </div>
                        </li>";
            }
            $mediaCommenti = $totale / (count($commenti));
            $str_commenti .= "</ul>";
        } else {
            $str_commenti="<p>Ancora Non ci sono commenti!</p>";    
        }
    } catch (Eccezione $e) {
        $str_commenti = $e->getMessage();
    }

    // finisco la sostituzione dei placeholder
    $pagina = str_replace("<COMMENTI/>", $str_commenti, $pagina);
    $pagina = str_replace("<VALUTAZIONE/>", $mediaCommenti, $pagina);
    $pagina = str_replace("<TITOLO_ANNUNCIO/>", $annuncio->getTitolo(), $pagina);
    $pagina = str_replace("<OSPITIMASSIMO/>", $annuncio->getMaxOspiti(), $pagina);
    $pagina = str_replace("<NUMEROCOMMENTI/>", count($commenti), $pagina);
    $pagina = str_replace("<PREZZO/>", $annuncio->getPrezzoNotte(), $pagina);
    $pagina = str_replace("<DESCRIZIONE/>", $annuncio->getDescrizione(), $pagina);
    
    $img = uploadsFolder() . $annuncio->getImgAnteprima();
    $pagina = str_replace("<IMMAGINE/>", "<div class=\"shower_immagine_anteprima\">
                        <img id=\"immagine_anteprima\" class=\"immagine_anteprima\" src=\"$img\" alt=\"".$annuncio->getDescAnteprima()."\"/>
                        </div>", $pagina);

    echo $pagina;
} catch (Eccezione $ex) {
    header("Location: ./404.php");
}
