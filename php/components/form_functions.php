<?php

define("FOTO_PROFILO_DEFAULT", "defaultImages/imgProfiloDefault.png");
define("ANTEPRIMA_ANNUNCIO_DEFAULT", "defaultImages/imgAnteprimaAnnuncioDefault.png");

/**
 * Aggiorna la pagina aggiungendo un messaggio per avvisare l'utente, che sia un semplice avviso oppure un messaggio di errore.
 * @param string $pageContent corrisponde al file HTML della pagina da mostrare, sottoforma di stringa
 * @param string $contentToShow corrisponde all'avviso o errore da mostrare all'utente all'interno di un paragrafo (non usa htmlentities, farlo nella chiamata se necessario)
 * @param string $divId corrisponde all'id del tag div che conterrà il messaggio
 * @param string $divClasses corrisponde alla/e classe/i da aggiungere (se necessario, di default è vuoto come parametro) per stilizzare il contenuto mostrato
 * @param bool $inParagraph se $contentToShow va dentro un paragrafo <p></p> oppure no
 * @return string $pageContent con il proprio contenuto modificato secondo le specifiche
 */
function addUserNotificationToPage($pageContent, $contentToShow, $divId, $divClasses = "", $inParagraph = TRUE): string {
    return str_replace(
        "<INFO_BOX/>", // stringa decisa per essere sostituita con box di messaggi errore
        "<div id=\"$divId\" " . ($divClasses === "" ? "" : "class=\"$divClasses\"") . ">" .
            (($inParagraph === TRUE) ? "<p>$contentToShow</p>" : $contentToShow) .
        "</div>",
        $pageContent
    );
}

/**
 * Restituisce una porzione di codice HTML contenente un messaggio per l'utente e una lista di errori.
 * @param string $message messaggio che avvisa l'utente di un form con errori
 * @param array associativo con chiavi di tipo string e valori booleani. Si ottiene invocando la funzione checkValuesForKeysInAssociativeArray.
 * @return string porzione di codice HTML contenente un messaggio per l'utente e una lista di errori.
 */
function formValidationErrorList($message, $resultOfValidation) {
    $errorList = "<ul>";
    foreach($resultOfValidation as $key => $formItem) {
        if(!$formItem) {
            $errorList .= "<li>Il campo \"$key\" non " . htmlentities("è") . " stato compilato.</li>";
        }
    }
    $errorList .= "</ul>";

    return $message . $errorList;
}


/**
 * Dato un array associativo e un array di stringhe, verifica se nell'array associativo ci sono valori impostati (controllo mediante funzione isset e verifica diverso da stringa vuota) con chiavi corrispondenti alle stringhe del secondo array.
 * @param array $assoc_array array in cui verificare se sono presenti valori associati ai contenuti dell'array $keys
 * @param array $keys array di stringhe contenente le chiavi per l'array $assoc_array
 * @return array associativo (con le chiavi passate in $keys) di flag booleani (TRUE se esiste un valore per una chiave in $keys, FALSE altrimenti)
 */
function checkValuesForKeysInAssociativeArray($assoc_array, $keys): array {
    $presence = array();
    foreach($keys as $key) {
        $presence[$key] = isset($assoc_array[$key]) && $assoc_array[$key] !== "";
    }
    return $presence;
}

/**
 * Costruisce una stringa per una data nel formato Y-m-d.
 * @param string $year stringa corrispondente a un numero (deve effettuare il casting a intero) da 0000 a 9999
 * @param string $month stringa corrispondente a un numero (deve effettuare il casting a intero) da 1 a 12
 * @param string $day stringa corrispondente a un numero (deve effettuare il casting a intero) da 1 a 31
 * @return string data a partire dai parametri passati
 * @return bool FALSE se parametri errati
 */
function buildDate($year, $month, $day) {
    $intyear = intval($year);
    $intmonth = intval($month);
    $intday = intval($day);

    // ($intVAR === 0) === TRUE significa bad cast
    if(($intday === 0 || $intday > 31) || ($intmonth === 0 || $intmonth > 12) || ($intyear === 0 || $intyear > 9999)) {
        return FALSE;
    }
    
    return "$year-$month-$day";
}

/**
 * Data una lista di placeholder da cercare su una stringa, li sostituisce con i valori richiesti.
 * Non effettua un controllo di validità sulla lunghezza dei due array (è compito del chiamante)
 * @param string $pageContent contiene la stringa con i placeholder
 * @param array $placeholders array di stringhe dei placeholder
 * @param array $actualValues array di stringhe dei valori da inserire
 * @return string pagina con il nuovo contenuto
 */
function replacePlaceholders($pageContent, $placeholders, $actualValues) {
    $newpage = $pageContent;
    foreach($placeholders as $key => $ph) {
        $newpage = str_replace($ph, $actualValues[$key], $newpage);
    }

    return $newpage;
}

/**
 * Restituisce il path della cartella in cui effettuare gli upload.
 */
function uploadsFolder() {
    return "../uploads/";
}