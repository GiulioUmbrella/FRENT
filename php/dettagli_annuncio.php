<?php
require_once "./class_CredenzialiDB.php";
require_once "./CheckMethods.php";
// valida, e dovrebbe funzionare sempre, forse ho mancato qualche test case
/*
todo quando l'annuncio non approvato è visualizzato dal proprietario, il pulsante prenota deve sparire, e deve
    essere visualizza il pulsante modifica, reindirizzandolo verso la pagina della modifica dell'annunciom
    e quindi sparisce anche i due form di data.

*/

require_once "./class_Frent.php";
require_once "./class_CredenzialiDB.php";
require_once "load_Frent.php";
require_once "./components/form_functions.php";

try {
    /*
     * quando annuncio non è stato approvato, fare dei controlli che non può essere visualizzato,
     * il controllo avviene attraverso il controllo dell'auth presente in session, se è admin allora
     * può visualizzare, se è utente o null allora possono visualizzare solo gli annunci nello stato di approvazione= 1
     */
    
    require_once "./load_Frent.php";
    
    if (!isset($_GET["id"])) {
        header("Location: ./404.php");
    }
    
    $id = intval($_GET["id"]);
    $_SESSION["id"] = $id;
    $annuncio = $frent->getAnnuncio($id);
    // se non sono ne admin ne user e annuncio non è stato approvato, non posso vederlo.
    // se sono user ma non sono host e annuncio non è stato approvato, non posso veder.
    if ((!isset($_SESSION["admin"]) and !isset($_SESSION["user"]) and $annuncio->getStatoApprovazione() != 1) or
        (isset($_SESSION["user"]) and $_SESSION["user"]->getIdUtente() != $annuncio->getIdHost()
            and $annuncio->getStatoApprovazione() != 1)) {
        header("Location: ./404.php");
    }
    
    $_SESSION["annuncio"] = $annuncio;
    $prezzoAnnuncio = $annuncio->getPrezzoNotte();
    $ospitiMassimo = $annuncio->getMaxOspiti();
//    $foto = $frent->getFotoAnnuncio($id);
    
    
    $pagina = file_get_contents("./components/dettagli_annuncio.html");
    if (isset($_SESSION["admin"])){
        $pagina = str_replace("<BREAD/>",$annuncio->getTitolo(),$pagina);
        $pagina = str_replace("<FOOTER/>", "", $pagina);
    
    }else{
        $pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);
    
        $pagina= str_replace("<BREAD/>","<a href=\"./index.php\" title=\"Vai alla pagina delle ricerca annunci\">
                        <span xml:lang=\"en\" lang=\"en\">Home</span></a> &gt;&gt; <a href=\"./risultati.php?<PARAMS>\">Ricerca</a>
                        &gt;&gt; <TITOLO_ANNUNCIO/></p>
                        ",$pagina);
    }
    
    // impostazione della pagina in base al tipo di utenza
    if (isset($_SESSION["user"]) or isset($_SESSION["admin"])) {
        if (isset($_SESSION["user"])) {// utente autenticato
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
        
        if (isset($_SESSION["admin"])) { // visualizzazione da parte di un amministratore
            $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_admin_logged.html"), $pagina);
            $pagina = str_replace("<FLAG/>", file_get_contents("./components/dettaglio_annuncio_admin.html"), $pagina);
            $params_si = "?idAnnuncio=" . $annuncio->getIdAnnuncio() . "&approvato=1";
            $params_no = "?idAnnuncio=" . $annuncio->getIdAnnuncio() . "&approvato=0";
            $pagina = str_replace("<PARAMS_SI/>", $params_si, $pagina);
            $pagina = str_replace("<PARAMS_NO/>", $params_no, $pagina);
        }
    } else { // visitatore anonimo, non autenticato
        $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_no_logged.html"), $pagina);
        $pagina = str_replace("<FLAG/>", file_get_contents("./components/dettaglio_annuncio_non_autenticato.html"), $pagina);
        $pagina = str_replace("<LINK/>", "./login.php", $pagina);
    }
    
    require_once "./components/setMinMaxDates.php";
    $link_ricerca="citta=".$annuncio->getCitta();
    $pagina = str_replace("<OSPITIMASSIMO/>", $ospitiMassimo, $pagina);
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
        $numOspiti = $_GET["numOspiti"];
        $link_ricerca.="&numOspiti=".$numOspiti;
        $pagina = str_replace("<VALUENUMERO/>", intval($_GET["numOspiti"]), $pagina);
    } else {
        $pagina = str_replace("<VALUENUMERO/>", 1, $pagina);
    }
    
    if (isset($_SESSION["dati_errati"]) and $_SESSION["dati_errati"]) {
        $pagina = str_replace("<MSG/>", $_SESSION["msg"], $pagina);
        unset($_SESSION["dati_errati"]);
    } else {
//        echo "no error";
        $pagina = str_replace("<MSG/>", "", $pagina);
    }
    
    $pagina = str_replace("<PARAMS>", $link_ricerca,$pagina);
    $str_commenti = "";
    $mediaCommenti = 0;
    try {
        $commenti = $frent->getCommentiAnnuncio($id);
        if (count($commenti) != 0) {
            $totale = 0;
            $str_commenti = "<ul>";
            foreach ($commenti as $commento) {
                $totale += intval($commento->getValutazione());
                $user=$commento->getUtente();
                $user_name =$user->getUserName();
                $immagine_profilo = $user->getImgProfilo();
                $testo_commento = $commento->getCommento();
                $votazione = $commento->getValutazione();
                $data_commento = $commento->getDataPubblicazione();
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
//            $pagina = str_replace("<Commenti/>", "<h2>Ancora Non ci sono commenti!</h2>", $pagina);
    
        }
        $pagina = str_replace("<COMMENTI/>", $str_commenti, $pagina);
    
    
    } catch (Eccezione $e) {
        $pagina = str_replace("<COMMENTI/>", $e->getMessage(), $pagina);
//        $pagina = str_replace("<COMMENTI/>", "<p>Ancora non ci sono commenti!</p>", $pagina);
    }
    
    $pagina = str_replace("<VALUTAZIONE/>", $mediaCommenti, $pagina);
    $img = uploadsFolder() . $annuncio->getImgAnteprima();
    
    
    $pagina = str_replace("<TITOLO_ANNUNCIO/>", $annuncio->getTitolo(), $pagina);
    $pagina = str_replace("<NUMEROCOMMENTI/>", count($commenti), $pagina);
    $pagina = str_replace("<PREZZO/>", $prezzoAnnuncio, $pagina);
    $pagina = str_replace("<DESCRIZIONE/>", $annuncio->getDescrizione(), $pagina);
    
    $pagina = str_replace("<IMMAGINE/>", "<div class=\"shower_immagine_anteprima\">
                        <img id=\"immagine_anteprima\" class=\"immagine_anteprima\" src=\"$img\" alt=\"".$annuncio->getDescAnteprima()."\"/>
                        </div>", $pagina);
    echo $pagina;
    
} catch (Eccezione $ex) {
    echo "eccezione " . $ex->getMessage();
}
