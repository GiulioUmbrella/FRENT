<?php
// funziona e valida.
require_once "load_Frent.php";
require_once "./components/form_functions.php";

/**
 * Funzioni con l'unico scopo di evitare duplicazione di codice.
 */

/**
 * Sostituisce i placeholder.
 * @param array $post corrisponde all'array $_POST, da passare come parametro
 * @param string $pagina aggiornata per riferimento con i valori dei placeholder presi da $_POST
 */
function placeholder_replacement_with_content($post, &$pagina) {
    // ripristino i dati
    $pagina = replacePlaceholders(
        $pagina,
        ["<VALUETITOLOCOMMENTO/>","<VALUETESTOCOMMENTO/>", "<VALUEVOTOCOMMENTO/>",],
        [$post["titolo_commento"], $post["testo_commento"], $post["valutazione_commento"]]
    );
}

/**
 * Sostituisce i placeholder con stringhe vuote.
 * @param string $pagina aggiornata per riferimento con i valori dei placeholder sostituiti da stringhe vuote
 */
function placeholder_replacement_with_empty(&$pagina) {
    // ripristino i dati
    $pagina = replacePlaceholders(
        $pagina,
        ["<VALUETITOLOCOMMENTO/>","<VALUETESTOCOMMENTO/>", "<VALUEVOTOCOMMENTO/>",],
        ["", "", ""]
    );
}


$pagina = file_get_contents("./components/riepilogo_prenotazione.html");
if (!isset($_SESSION["user"])) {
    header("Location: ./login.php");
}

$pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
$pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);

/**
 * $_GET["id"] lo setto se provengo dalla pagina "Le mie prenotazioni"
 * $_POST["id_prenotazione"] lo setto se ho premuto pubblica commento nel form di pubblicazione del commento
 */
if (!isset($_GET["id"]) && !isset($_POST["id_prenotazione"])) {
    header("Location: 404.php");
}

/**
 * Quindi ho $_GET["id"] oppure $_POST["id_prenotazione"].
 * Scopriamo quale dei due.
 */
$id_prenotazione = isset($_GET["id"]) ? intval($_GET["id"]) : intval($_POST["id_prenotazione"]);

try {
    $prenotazioni = $frent->getPrenotazione($id_prenotazione);
    $annuncio = $frent->getAnnuncio($prenotazioni->getIdAnnuncio());
    $host = $frent->getUser($annuncio->getIdHost());
    
    if ($prenotazioni->getIdUtente() != $_SESSION["user"]->getIdUtente()) {
        header("Location: ./404.php");
    }

    // reperimento dati per visualizzazione
    $durata = abs(strtotime($prenotazioni->getDataFine()) - strtotime($prenotazioni->getDataInizio())) / (3600 * 24);
    $totale = $durata * $annuncio->getPrezzoNotte() * $prenotazioni->getNumOspiti();
    $id = $annuncio->getIdAnnuncio();
    $titolo = $annuncio->getTitolo();
    
    $commentiAnnuncio = $frent->getCommentiAnnuncio($prenotazioni->getIdAnnuncio());
    // ricerca del commento fatto dall'utente
    $commentoUtente = Commento::build();
    $commentoTrovato = false;
    $i = 0;
    $l = count($commentiAnnuncio);
    while ($i < $l && !$commentoTrovato) {
        if ($commentiAnnuncio[$i]->getIdPrenotazione() !== $id_prenotazione) {
            $i++;
        } else {
            $commentoTrovato = true;
            $commentoUtente = $commentiAnnuncio[$i];
        }
    }
    
    $commentoHTML = "";
    if ($commentoTrovato) {
        $commentoHTML = "<ul class=\"riepilogo_annucio\">";
        $commentoHTML .= "<li>Titolo: " . $commentoUtente->getTitolo() . "</li>";
        $commentoHTML .= "<li>Commento: " . $commentoUtente->getCommento() . "</li>";
        $commentoHTML .= "<li>Valutazione: " . $commentoUtente->getValutazione() . "</li>";
        $commentoHTML .= "<li class=\"link_elimina_commento\"><a href=\"script_elimina_commento.php?id=<IDPRENOTAZIONE/>\" title=\"Elimina commento\">Elimina commento</a></li>";
        $commentoHTML .= "</ul>";
    } else {
        $commentoHTML = "";
        if ($prenotazioni->getDataFine() < date("Y-m-d")) {
            $commentoHTML = file_get_contents("./components/aggiungi_commento_form.html");
        } else {
            $commentoHTML = "<p>Mi dispiace, ma non &egrave; ancora possibile commentare questa prenotazione!</p>";
        }
    }

    $pagina = str_replace("<COMMENTO/>", $commentoHTML, $pagina);
    $pagina = str_replace("<IDPRENOTAZIONE/>", $prenotazioni->getIdPrenotazione(), $pagina);
    $pagina = str_replace("<DATAINIZIO/>", $prenotazioni->getDataInizio(), $pagina);
    $pagina = str_replace("<DATAFINE/>", $prenotazioni->getDataFine(), $pagina);
    $pagina = str_replace("<MAILPROPRIETARIO/>", $host->getMail(), $pagina);
    $pagina = str_replace("<NUMOSPITI/>", $prenotazioni->getNumOspiti(), $pagina);
    // tolgo il link quando l'annuncio relativo a questo prenotazione, non è più in stato approvato.
    // se viene prenotata un annuncio, successivamente l'host lo modifica, la prenotazione rimane valida!
    if ($annuncio->getStatoApprovazione()!=1){
        $pagina = str_replace("<NOMEANNUNCIO/>", $titolo, $pagina);
    }else{
    
        $pagina = str_replace("<NOMEANNUNCIO/>", "<a href=\"./dettagli_annuncio.php?id=$id\" title=\"Visualizza altre informazioni dell\'annuncio $titolo\">$titolo</a>", $pagina);
    }
    $pagina = str_replace("<INDIRIZZO/>", $annuncio->getIndirizzo(), $pagina);
    $pagina = str_replace("<CITTA/>", $annuncio->getCitta(), $pagina);
    $pagina = str_replace("<PROPRIETARIO/>", $host->getUserName(), $pagina);
    $pagina = str_replace("<PREZZO/>", $totale, $pagina);
    
    /**
     * Gestione pubblicazione commento
     */
    if(isset($_POST["pubblica_commento"])) {
        $risultato_validazione = checkValuesForKeysInAssociativeArray($_POST, ["titolo_commento", "testo_commento", "valutazione_commento"]);
        /**
         * se $form_non_valido === TRUE ci sono valori di $_POST non settati
         */
        $form_non_valido = in_array(FALSE, $risultato_validazione);
    
        /**
         * Valori per la funzione addUserNotificationToPage
         */
        $messageToUser = "";
        $divId = "info_box";
        $divClasses = "aligned_with_form messaggio_errore";
        $inParagraph = TRUE; // dice alla funzione addUserNotificationToPage che il contenuto non andrà dentro un paragraph
    
        if($form_non_valido) {
            $messageToUser = formValidationErrorList("<p>C'&egrave; stato un errore nella compilazione del modulo.</p>", $risultato_validazione);
            $inParagraph = FALSE;
        } else {
            try {
                $cmt = Commento::build();
                $cmt->setIdPrenotazione($id_prenotazione);
                $cmt->setTitolo($_POST["titolo_commento"]);
                $cmt->setCommento($_POST["testo_commento"]);
                $cmt->setValutazione(intval($_POST["valutazione_commento"]));
                
                $codice_inserimento = $frent->insertCommento($cmt);
                
                if($codice_inserimento === -1 || $codice_inserimento === -4) { // in caso di prenotazione inesistente (-1) e se il commento non è stato inserito (-4)
                    $messageToUser = htmlentities("C'è stato un errore nel processo di inserimento del commento.");
                } elseif($codice_inserimento === -2) { // in caso di prenotazione già commentata
                    $messageToUser = htmlentities("La prenotazione risulta già commentata.");
                } elseif($codice_inserimento === -3) { // in caso l'host stia cercando di commentare una prenotazione ad un suo annucio
                    $messageToUser = htmlentities("Non puoi commentare un tuo annuncio.");
                } else { // ID del commento appena inserito se l'inserimento è andato a buon fine
                    // placeholder sostituiti con stringa vuota in quanto avvenuta registrazione
                    placeholder_replacement_with_empty($pagina);
                    $messageToUser = htmlentities("Il commento è stato pubblicato.");
                    $messageToUser .= "<a href=\"dettagli_annuncio.php?id=" . $prenotazioni->getIdAnnuncio() . "\" title=\"Vai a vedere il tuo commento all'interno della sezione commenti dell'annuncio\">Vai a vedere il tuo commento</a>.";
                    $divClasses = "aligned_with_form messaggio_ok";
                }
            } catch(Eccezione $exc) {
                $messageToUser = htmlentities("C'è stato un errore nel processo di inserimento del commento. Errore riscontrato: ") . $exc->getMessage();
            }
        }
        /// se è stata invocata placeholder_replacement_with_empty, la funzione non avrà side-effect
        placeholder_replacement_with_content($_POST, $pagina); // aggiornata per riferimento
        $pagina = addUserNotificationToPage($pagina, $messageToUser, $divId, $divClasses, $inParagraph); // sostituisce <INFO_BOX/>
    } else {
        $pagina = str_replace("<INFO_BOX/>", "", $pagina);
        placeholder_replacement_with_empty($pagina); // aggiornata per riferimento
    }
    

    echo $pagina;
} catch (Eccezione $ex) {
    
    $_SESSION["msg"]="<h2>".$ex->getMessage()."</h2>";
    header("Location: ./error_page.php");
}
