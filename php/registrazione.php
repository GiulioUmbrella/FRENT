<?php
require_once "./load_Frent.php";
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
        ["<VALUENOME>", "<VALUECOGNOME>", "<VALUEMAIL>", "<VALUEUSERNAME>", "<VALUEPASSWORD>", "<VALUERIPETIPASSWORD>", "<VALUETELEFONO>"],
        [$post["nome"], $post["cognome"], $post["mail"], $post["username"], $post["password"], $post["ripeti_password"], $post["telefono"]]
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
        ["<VALUENOME>", "<VALUECOGNOME>", "<VALUEMAIL>", "<VALUEUSERNAME>", "<VALUEPASSWORD>", "<VALUERIPETIPASSWORD>", "<VALUETELEFONO>"],
        ["", "", "", "", "", "", ""]
    );
}


// REGISTRAZIONE

// verifico che l'utente abbia effettuato l'accesso poiché in quel caso lo reindirizzo alla homepage
if (isset($_SESSION["user"])) {
    header("Location: index.php");
}

// carico componenti per la pagina
$pagina = file_get_contents("./components/registrazione.html");
$pagina = str_replace("<HEADER/>", file_get_contents("./components/header_no_logged.html"), $pagina);
$pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);

// popolazione select#giorno_nascita
$GG = "";
for ($i = 1; $i <= 31; $i++) {
    $GG .= "<option value=\"$i\">$i</option>";
}
$pagina = str_replace("<GIORNO/>", $GG, $pagina);

// popolazione select#mese_nascita
$MM = "";
$mesi = array("gennaio", "febbraio", "marzo", "aprile", "maggio", "giugno", "luglio", "agosto", "settembre", "ottobre", "novembre", "dicembre");
for ($i = 1; $i <= 12; $i++) {
    $MM .= "<option value=\"$i\">" . $mesi[$i - 1] . "</option>";
}
$pagina = str_replace("<MESE/>", $MM, $pagina);

// popolazione select#anno_nascita
$AAAA = "";
for ($i = intval(date("Y")), $bottom = intval(date("Y")) - 100; $i >= $bottom; $i--) {
    $AAAA .= "<option value=\"$i\">$i</option>";
}
$pagina = str_replace("<ANNO/>", $AAAA, $pagina);

// se l'utente non ha premuto il tasto di invio dati visualizzo la pagina con il form
if (isset($_POST["registrati"])) {
    $risultato_validazione = checkValuesForKeysInAssociativeArray($_POST, ["nome", "cognome", "mail", "username", "password", "ripeti_password", "telefono", "giorno_nascita", "mese_nascita", "anno_nascita"]);
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
    
    if ($form_non_valido) {
        $messageToUser = formValidationErrorList("<p>C'&egrave; stato un errore nella compilazione del modulo.</p>", $risultato_validazione);
        $inParagraph = FALSE;
    } else {
        if ($_POST["password"] !== $_POST["ripeti_password"]) {
            $messageToUser = "Le due password inserite non corrispondono.";
        } else {
            try {
                $codice_registrazione = $frent->registrazione(
                    $_POST["nome"],
                    $_POST["cognome"],
                    $_POST["username"],
                    $_POST["mail"],
                    $_POST["password"],
                    buildDate($_POST["anno_nascita"], $_POST["mese_nascita"], $_POST["giorno_nascita"]),
                    FOTO_PROFILO_DEFAULT,
                    $_POST["telefono"]
                );
                
                if ($codice_registrazione === -1) {
                    $messageToUser = htmlentities("C'è stato un errore nel processo di registrazione.");
                } else if ($codice_registrazione === -2) {
                    $divClasses = "aligned_with_form messaggio_attenzione";
                    $messageToUser = "Un profilo " . htmlentities("è già") . " presente con l'indirizzo <span xml:lang=\"en\">mail</span> fornito. Puoi accedere cliccando su <a href=\"login.php\" title=\"Vai alla pagina di accesso\">questo link</a>.";
                } else {
                    // tento di creare una cartella per salvare da qui in avanti i file dell'utente
                    if (!mkdir(uploadsFolder() . "user$codice_registrazione")) {
                        throw new Eccezione("Non è stato possibile creare una cartella per le immagini dell'utente.");
                    }
                    
                    // placeholder sostituiti con stringa vuota in quanto avvenuta registrazione
                    placeholder_replacement_with_empty($pagina);
                    $divClasses = "aligned_with_form messaggio_ok";
                    $messageToUser = "Registrazione avvenuta con successo! Puoi accedere cliccando su <a href=\"login.php\" title=\"Vai alla pagina di accesso\">questo link</a>.";
                }
                
            } catch (Eccezione $exc) {
                $messageToUser = htmlentities("C'è stato un errore nel processo di registrazione. Errore riscontrato: ") . $exc->getMessage();
            }
        }
    }
    /// se è stata invocata placeholder_replacement_with_empty, la funzione non avrà side-effect
    placeholder_replacement_with_content($_POST, $pagina); // aggiornata per riferimento
    $pagina = addUserNotificationToPage($pagina, $messageToUser, $divId, $divClasses, $inParagraph);
} else {
    placeholder_replacement_with_empty($pagina); // aggiornata per riferimento
}

echo $pagina;