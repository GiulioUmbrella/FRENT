<?php

require_once "./load_Frent.php";
require_once "./class_ImageManager.php";
require_once "./components/form_functions.php";

// --- REINDIRIZZO SE NON LOGGATO
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
}

// reperisco utente
$user = $_SESSION["user"];

// verifico di essere arrivato in questa pagina da dettagli_annuncio, dove ho settato in sessione l'ID dell'annuncio
if (isset($_SESSION["id"])) {
    $ID_ANNUNCIO = intval($_SESSION["id"]);
    $annuncio = null;
    try {
        $annuncio = $frent->getAnnuncio($ID_ANNUNCIO);
    } catch (Eccezione $e) {
        $_SESSION["msg"] = $e->getMessage();
        header("Location: ./error_page.php");
    }
    if ($annuncio->getIdHost() !== $user->getIdUtente()) {
        header("Location: ./404.php");
    }
    
    // l'annuncio esiste, l'utente pure, l'annuncio è dell'utente
    // --- CARICAMENTO COMPONENTI PAGINA CHE NON CAMBIANO DOPO LA MODIFICA
    $pagina = file_get_contents("./components/dettaglio_annuncio_host_modifica.html");
    $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
    $pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);
    $pagina = str_replace("<IDANNUNCIO/>", $annuncio->getIdAnnuncio(), $pagina);
    $pagina = str_replace("<TITOLO/>", $annuncio->getTitolo(), $pagina);
    
    $messageToUser = "";
    $divId = "info_box";
    $divClasses = "aligned_with_form messaggio_errore";
    $inParagraph = TRUE; // dice alla funzione addUserNotificationToPage che il contenuto non andrà dentro un paragraph
    
    if (isset($_POST["aggiorna"])) {
        $risultato_validazione = checkValuesForKeysInAssociativeArray($_POST, ["descrizione", "max_ospiti", "desc_anteprima", "prezzo_notte", "indirizzo", "citta"]);
        $form_non_valido = in_array(FALSE, $risultato_validazione);
        
        if ($form_non_valido) {
            $messageToUser = formValidationErrorList("<p>C'&egrave; stato un errore nella compilazione del modulo di modifica dell'annuncio.</p>", $risultato_validazione);
            $inParagraph = FALSE;
        } else {
            // gestione modifica dati
            try {
                $imageManager = new ImageManager(uploadsFolder() . "user" . $user->getIdUtente() . "/");
                /// $no_file === TRUE => non è stato caricato un file
                $no_file = isset($_FILES["anteprima"]) && $_FILES["anteprima"]["error"] === 4;
                
                if (!$no_file) { // preparo invio di file
                    $imageManager->setFile("anteprima", "annuncio" . $annuncio->getIdAnnuncio());
                }
                
                
                $codice_aggiornamento = $frent->editAnnuncio(
                    $annuncio->getIdAnnuncio(),
                    $annuncio->getTitolo(),
                    $_POST["descrizione"],
                    (!$no_file ? ("user" . $user->getIdUtente() . "/" . $imageManager->fileName()) : $annuncio->getImgAnteprima()),
                    $_POST["desc_anteprima"],
                    $_POST["indirizzo"],
                    $_POST["citta"],
                    intval($_POST["max_ospiti"]),
                    floatval($_POST["prezzo_notte"])
                );
                
                if ($codice_aggiornamento === -1) {
                    $messageToUser = htmlentities("C'è stato un errore nell'aggiornamento dei dati dell'annuncio.");
                } else {
                    if ($no_file || $imageManager->saveFile()) {
                        $divClasses = "aligned_with_form messaggio_ok";
                        $messageToUser = "Modifica dei dati dell'annuncio avvenuta con successo! <a href=\"dettagli_annuncio.php?id=" . $annuncio->getIdAnnuncio() . "\" title=\"Torna ai dettagli dell'annuncio\">Torna ai dettagli dell'annuncio</a>.";
                    } else {
                        $divClasses = "aligned_with_form messaggio_attenzione";
                        $messageToUser = htmlentities("L'annuncio è stato modificato, ma c'è stato un errore nel salvataggio della nuova immagine dell'annuncio.");
                    }
                }
            } catch (Eccezione $exc) {
                $messageToUser = htmlentities("C'è stato un errore nel processo di modifica dell'annuncio. Errore riscontrato: ") . $exc->getMessage();
            }
        }
        $pagina = addUserNotificationToPage($pagina, $messageToUser, $divId, $divClasses, $inParagraph); // sostituisce <INFO_BOX/>
    }
    
    // aggiorno il contenuto della pagina
    $annuncio = NULL;
    
    try {
        $annuncio = $frent->getAnnuncio($ID_ANNUNCIO);
    } catch (Eccezione $e) {
        $_SESSION["msg"] = $e->getMessage();
        header("Location: ./error_page.php");
    } // ripesco dal database i dati dell'annuncio
    // Inserisco le componenti che possono essere cambiate
    $pagina = str_replace("<DESCRIZIONE/>", $annuncio->getDescrizione(), $pagina);
    $pagina = str_replace("<DESCANTEPRIMA/>", $annuncio->getDescAnteprima(), $pagina);
    $pagina = str_replace("<NUMOSPITIMAX/>", $annuncio->getMaxOspiti(), $pagina);
    $pagina = str_replace("<PREZZONOTTE/>", $annuncio->getPrezzoNotte(), $pagina);
    $pagina = str_replace("<INDIRIZZO/>", $annuncio->getIndirizzo(), $pagina);
    $pagina = str_replace("<CITTA/>", $annuncio->getCitta(), $pagina);
    
    $pagina = str_replace("<INFO_BOX/>", "", $pagina);
    echo $pagina;
} else {
    header("Location: index.php");
}
