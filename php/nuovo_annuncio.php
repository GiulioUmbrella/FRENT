<?php
require_once "load_Frent.php";
require_once "./class_ImageManager.php";
require_once "./components/form_functions.php";

/**
 * Funzioni con l'unico scopo di evitare duplicazione di codice.
 */

/**
 * Sostituisce i placeholder.
 * @param array $post corrisponde all'array $_POST, da passare come parametor
 * @param string $pagina aggiornata per riferimento con i valori dei placeholder presi da $_POST
 */
function placeholder_replacement_with_content($post, &$pagina) {
    // ripristino i dati
    $pagina = replacePlaceholders(
        $pagina,
        ["<VALUETITOLO>","<VALUEDESCRIZIONE>", "<VALUEMAXOSPITI>", "<VALUEDESCRIZIONEANTEPRIMA>", "<VALUEPREZZONOTTE>", "<VALUEINDIRIZZO>", "<VALUECITTA>"],
        [$post["titolo"], $post["descrizione"], $post["max_ospiti"], $post["desc_anteprima"], $post["prezzo_notte"], $post["indirizzo"], $post["citta"]]
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
        ["<VALUETITOLO>","<VALUEDESCRIZIONE>", "<VALUEMAXOSPITI>", "<VALUEDESCRIZIONEANTEPRIMA>", "<VALUEPREZZONOTTE>", "<VALUEINDIRIZZO>", "<VALUECITTA>"],
        ["", "", "", "", "", "", ""]
    );
}


if (!isset($_SESSION["user"])) {
    header("Location: login.php");
}

$pagina = file_get_contents("./components/nuovo_annuncio.html");
$pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
$pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);

// se l'utente ha premuto il tasto di invio dati visualizzo la pagina con il form
if(isset($_POST["nuovo_annuncio"])) {
    $risultato_validazione = checkValuesForKeysInAssociativeArray($_POST, ["titolo", "descrizione", "max_ospiti", "desc_anteprima", "prezzo_notte", "indirizzo", "citta"]);
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
            $codice_inserimento = $frent->insertAnnuncio(
                $_POST["titolo"],
                $_POST["descrizione"],
                ANTEPRIMA_ANNUNCIO_DEFAULT,
                $_POST["desc_anteprima"],
                $_POST["indirizzo"],
                $_POST["citta"],
                intval($_POST["max_ospiti"]),
                floatval($_POST["prezzo_notte"])
            );
            
            if($codice_inserimento === -1 || $codice_inserimento === -2) {
                $messageToUser = htmlentities("C'è stato un errore nel processo di inserimento del nuovo annuncio.");
            } else {
                $user = $_SESSION["user"];
                $imageManager = new ImageManager(uploadsFolder()."user" . $user->getIdUtente() . "/");
                $imageManager->setFile("img_anteprima", "annuncio" . $codice_inserimento);
                
                // placeholder sostituiti con stringa vuota in quanto avvenuta registrazione
                placeholder_replacement_with_empty($pagina);
                $divClasses = "aligned_with_form messaggio_attenzione";
                // tento il salvataggio del file
                if($imageManager->saveFile()) {
                    $codice_aggiornamento = $frent->editAnnuncio(
                        intval($codice_inserimento),
                        $_POST["titolo"],
                        $_POST["descrizione"],
                        "user" . $user->getIdUtente() . "/" . $imageManager->fileName(),
                        $_POST["desc_anteprima"],
                        $_POST["indirizzo"],
                        $_POST["citta"],
                        intval($_POST["max_ospiti"]),
                        floatval($_POST["prezzo_notte"])
                    );

                    
                    if($codice_aggiornamento === -1) {
                        $messageToUser = htmlentities("L'annuncio è stato salvato, ma non è stato possibile inserire l'immagine di anteprima richiesta (errore di aggiornamento).");
                    } else {
                        $messageToUser = htmlentities("L'annuncio è stato salvato.");
                        $divClasses = "aligned_with_form messaggio_ok";
                    }
                } else {
                    $messageToUser = htmlentities("L'annuncio è stato salvato, ma non è stato possibile inserire l'immagine di anteprima richiesta (errore di salvataggio).");
                }

                $messageToUser .= "<a href=\"miei_annunci.php\" title=\"Vai alla lista degli annunci\">Torna alla lista degli annunci</a>.";
            }
        } catch(Eccezione $exc) {
            $messageToUser = htmlentities("C'è stato un errore nel processo di inserimento del nuovo annuncio. Errore riscontrato: ") . $exc->getMessage();
        }
    }
    /// se è stata invocata placeholder_replacement_with_empty, la funzione non avrà side-effect
    placeholder_replacement_with_content($_POST, $pagina); // aggiornata per riferimento
    $pagina = addUserNotificationToPage($pagina, $messageToUser, $divId, $divClasses, $inParagraph);
} else {
    placeholder_replacement_with_empty($pagina); // aggiornata per riferimento
}

echo $pagina;