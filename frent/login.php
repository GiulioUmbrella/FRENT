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
        ["<VALUEUSERNAME>","<VALUEPASSWORD>"],
        [$post["user"], $post["password"]]
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
        ["<VALUEUSERNAME>","<VALUEPASSWORD>"],
        ["", ""]
    );
}


// carico pagina e componenti
$pagina = file_get_contents("./components/login.html");
require_once ("./load_header.php");
$pagina = str_replace("<FORM/>", file_get_contents("./components/login_form.html"), $pagina);
$pagina = str_replace("<PAGE/>", "./login.php", $pagina);
$pagina= str_replace("<FOOTER/>",file_get_contents("./components/footer.html"),$pagina);
if(isset($_POST["accedi"])) {
    $risultato_validazione = checkValuesForKeysInAssociativeArray($_POST, ["user", "password"]);
    /**
     * se $form_non_valido === TRUE ci sono valori di $_POST non settati
     */
    $form_non_valido = in_array(FALSE, $risultato_validazione);

    $messageToUser = "";
    $divId = "info_box";
    $divClasses = "aligned_with_form messaggio_errore";
    $inParagraph = TRUE;
    
    if($form_non_valido) {
        $messageToUser = formValidationErrorList("<p>C'&egrave; stato un errore nella compilazione del modulo.</p>", $risultato_validazione);
        $inParagraph = FALSE;
    } else {
        try {
            $utente = $frent->login($_POST["user"], $_POST["password"]);
            
            // aggiunto l'oggetto utente alla sessione
            $_SESSION["user"] = $utente;
            
            header("Location: ./index.php");
            
        } catch (Eccezione $exc) {
            $messageToUser = htmlentities("C'è stato un errore durante l'accesso. Errore riscontrato: ") . $exc->getMessage();
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
