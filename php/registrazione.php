<?php
require_once "./load_Frent.php";
require_once "./components/form_functions.php";

// verifico che l'utente abbia effettuato l'accesso poiché in quel caso lo reindirizzo alla homepage
if (isset($_SESSION["user"])) {
    header("Location: index.php");
}

// carico componenti per la pagina
$pagina = file_get_contents("./components/registrazione.html");
$pagina = str_replace("<HEADER/>", file_get_contents("./components/header_no_logged.html"), $pagina);
$pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);

// se l'utente non ha premuto il tasto di invio dati visualizzo la pagina con il form
if (!isset($_POST["registrati"])) {
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
    echo $pagina;
} else {
    $risultato_validazione = checkValuesForKeysInAssociativeArray($_POST, ["nome", "cognome", "mail", "username", "password", "ripeti_password", "telefono", "giorno_nascita", "mese_nascita", "anno_nascita"], TRUE);
    $form_valido = in_array(FALSE, $risultato_validazione);

    if($form_valido) { // IF1 - RAMO VERO IF1
        $messageToUser = formValidationErrorList("C'è stato un errore nella compilazione del modulo.", $risultato_validazione);
        echo addUserNotificationToPage($pagina, $messageToUser, "credenziali_errate", "aligned_with_form");
    } else { // FINE RAMO VERO IF1 - RAMO FALSO IF1
        if($_POST["password"] !== $_POST["ripeti_password"]) { // IF2 - RAMO VERO IF2
            echo addUserNotificationToPage($pagina, "Le due password inserite non corrispondono.", "credenziali_errate", "aligned_with_form");
        } else { // FINE RAMO VERO IF2 - RAMO FALSO IF2
            try {
                $codice_registrazione = $frent->registrazione(
                    $_POST["nome"],
                    $_POST["cognome"],
                    $_POST["username"],
                    $_POST["mail"],
                    $_POST["password"],
                    buildDate($_POST["anno_nascita"], $_POST["mese_nascita"], $_POST["giorno_nascita"]),
                    "defaultImages/imgProfiloDefault.png",
                    $_POST["telefono"]
                );
                
                if($codice_registrazione === -1) { // IF3 - RAMO VERO IF3
                    echo addUserNotificationToPage($pagina, htmlentities("C'è stato un errore nel processo di registrazione."), "credenziali_errate", "aligned_with_form");
                } else if($codice_registrazione === -2) { // FINE RAMO VERO IF3 - RAMO FALSO IF3 - IF4 - RAMO VERO IF4
                    echo addUserNotificationToPage($pagina, "Un profilo ". htmlentities("è già") . " presente con l'indirizzo <span xml:lang=\"en\">mail</span> fornito. Puoi accedere cliccando su <a href=\"login.php\" title=\"Vai alla pagina di accesso\">questo link</a>.", "credenziali_errate", "aligned_with_form");
                } else { // FINE RAMO VERO IF4 - RAMO FALSO IF4
                    // tento di creare una cartella per salvare da qui in avanti i file dell'utente
                    if(!mkdir("../uploads/user$codice_registrazione")) { // IF5 - RAMO VERO IF5
                        throw new Eccezione("Non è stato possibile creare una cartella per le immagini dell'utente.");
                    } // FINE RAMO VERO IF5 - RAMO FALSO IF5

                    echo addUserNotificationToPage($pagina, "Registrazione avvenuta con successo! Puoi accedere cliccando su <a href=\"login.php\" title=\"Vai alla pagina di accesso\">questo link</a>.", "credenziali_errate", "aligned_with_form");
                } // FINE RAMO FALSO IF3 - FINE RAMO FALSO IF4

            } catch(Eccezione $exc) {
                echo addUserNotificationToPage($pagina, htmlentities("C'è stato un errore nel processo di registrazione. Errore riscontrato: ") . $exc->getMessage(), "credenziali_errate", "aligned_with_form");
            }
        } // FINE RAMO FALSO IF2
    } // FINE RAMO FALSO IF1
}

