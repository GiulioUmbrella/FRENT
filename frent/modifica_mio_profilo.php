<?php

require_once "./load_Frent.php";
require_once "./class_ImageManager.php";
require_once "./components/form_functions.php";

// --- REINDIRIZZO SE NON LOGGATO
if(!isset($_SESSION["user"])) {
    header("Location: login.php");
}

// --- CARICAMENTO COMPONENTI PAGINA
$pagina = file_get_contents("./components/modifica_mio_profilo.html");
$pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
$pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);

// reperisco utente
$user = $_SESSION["user"];

// --- SE SONO STATI INVIATI FORM
$messageToUser = "";
$divId = "info_box";
$divClasses = "aligned_with_form messaggio_errore";
$inParagraph = TRUE; // dice alla funzione addUserNotificationToPage che il contenuto non andrà dentro un paragraph

// --- FORM MODIFICA IMG PROFILO
if(isset($_POST["modifica_img_profilo"])) {
    try {
        $imageManager = new ImageManager(uploadsFolder() . "user" . $user->getIdUtente() . "/");
        $imageManager->setFile("nuova_img_profilo", "imgProfilo" . $user->getIdUtente());
        
        // possibilmente non verrà aggiornato nulla
        // ma alla prima volta che cambio la foto da quella di default a una scelta dall'utente e le volte in cui cambia estensione allora ne ho necessità
        $codice_aggiornamento = $frent->editUser(
            $user->getNome(),
            $user->getCognome(),
            $user->getUsername(),
            $user->getMail(),
            $user->getPassword(),
            $user->getDataNascita(),
            "user" . $user->getIdUtente() . "/" . $imageManager->fileName(),
            $user->getTelefono()
        );
        
        if($codice_aggiornamento === -1) {
            $messageToUser = htmlentities("C'è stato un errore nel processo di modifica della foto profilo.");
        } else {
            // tento il salvataggio del nuovo file
            if($imageManager->saveFile()) {
                // aggiorno la variabile in sessione altrimenti ho discrepanze fra server e dati locali
                $user->setImgProfilo("user" . $user->getIdUtente() . "/" . $imageManager->fileName());
                
                $divClasses = "aligned_with_form messaggio_ok";
                $messageToUser = htmlentities("La nuova immagine di profilo è stata correttamente salvata.") . "<a href=\"mio_profilo.php\" title=\"Vai alla pagina del profilo\">Torna alla pagina del profilo</a>.";
            } else {
                $messageToUser = htmlentities("C'è stato un errore nel salvataggio della nuova immagine di profilo.");
            }
        }
    } catch(Eccezione $exc) {
        $messageToUser = htmlentities("C'è stato un errore nell'invio della nuova immagine di profilo. Errore riscontrato: ") . $exc->getMessage();
    }
}

/// --- FORM MODIFICA PASSWORD
if(isset($_POST["modifica_password"])) {
    $risultato_validazione = checkValuesForKeysInAssociativeArray($_POST, ["nuova_password", "conferma_nuova_password"]);
    $form_non_valido = in_array(FALSE, $risultato_validazione);
    
    if($form_non_valido) { // IF1 - RAMO VERO IF1
        $messageToUser = formValidationErrorList("<p>C'&egrave; stato un errore nella compilazione del modulo di modifica della password.</p>", $risultato_validazione);
        $inParagraph = FALSE;
    } else { // FINE RAMO VERO IF1 - RAMO FALSO IF2
        if($_POST["nuova_password"] !== $_POST["conferma_nuova_password"]) { // IF2 - RAMO VERO IF2
            $messageToUser = "Le due password inserite per la modifica non corrispondono.";
        } // FINE RAMO VERO IF2
        
        try {
            $codice_aggiornamento = $frent->editUser(
                $user->getNome(),
                $user->getCognome(),
                $user->getUsername(),
                $user->getMail(),
                $_POST["nuova_password"],
                $user->getDataNascita(),
                $user->getImgProfilo(),
                $user->getTelefono()
            );
            
            if($codice_aggiornamento === -1) { // IF3 - RAMO VERO IF3
                $messageToUser = htmlentities("C'è stato un errore nel processo di modifica della password.");
            } else { // FINE RAMO VERO IF3 - RAMO FALSO IF3
                // aggiorno la variabile in sessione altrimenti ho discrepanze fra server e dati locali
                $user->setPassword($_POST["nuova_password"]);
                
                $divClasses = "aligned_with_form messaggio_ok";
                $messageToUser = "Modifica della password avvenuta con successo! <a href=\"mio_profilo.php\" title=\"Vai alla pagina del profilo\">Torna alla pagina del profilo</a>.";
            } // FINE RAMO FALSO IF3
        } catch(Eccezione $exc) {
            $messageToUser = htmlentities("C'è stato un errore nell'invio dei dati del modulo di modifica della password. Errore riscontrato: ") . $exc->getMessage();
        }     
    } // FINE RAMO FALSO IF1
}

// --- FORM MODIFICA DATI PERSONALI
if(isset($_POST["modifica_dati_personali"])) {
    $risultato_validazione = checkValuesForKeysInAssociativeArray($_POST, ["nome", "cognome", "mail", "username", "telefono", "giorno_nascita", "mese_nascita", "anno_nascita"]);
    $form_non_valido = in_array(FALSE, $risultato_validazione);
    
    if($form_non_valido) { // IF1 - RAMO VERO IF1
        $messageToUser = formValidationErrorList("<p>C'&egrave stato un errore nella compilazione del modulo di modifica dei dati personali.</p>", $risultato_validazione);
        $inParagraph = FALSE;
    } else { // FINE RAMO VERO IF1 - RAMO FALSO IF2
        try {
            $codice_aggiornamento = $frent->editUser(
                $_POST["nome"],
                $_POST["cognome"],
                $_POST["username"],
                $_POST["mail"],
                $user->getPassword(),
                buildDate($_POST["anno_nascita"], $_POST["mese_nascita"], $_POST["giorno_nascita"]),
                $user->getImgProfilo(),
                $_POST["telefono"]
            );
            
            if($codice_aggiornamento === -1) {
                $messageToUser = htmlentities("C'è stato un errore nell'aggiornamento dei dati personali.");
            } else {
                // aggiorno la variabile in sessione altrimenti ho discrepanze fra server e dati locali
                $user->setNome($_POST["nome"]);
                $user->setCognome($_POST["cognome"]);
                $user->setUsername($_POST["username"]);
                $user->setDataNascita(buildDate($_POST["anno_nascita"], $_POST["mese_nascita"], $_POST["giorno_nascita"]));
                $user->setTelefono($_POST["telefono"]);
                
                $divClasses = "aligned_with_form messaggio_ok";
                $messageToUser = "Modifica dei dati personali avvenuta con successo! <a href=\"mio_profilo.php\" title=\"Vai alla pagina del profilo\">Torna alla pagina del profilo</a>.";
            }
        } catch(Eccezione $exc) {
            $messageToUser = htmlentities("C'è stato un errore nel processo di modifica dei dati personali. Errore riscontrato: ") . $exc->getMessage();
        }       
    } // FINE RAMO FALSO IF1
    
}

// aggiunta dati utente
$pagina = str_replace("<PATH/>", uploadsFolder() . $user->getImgProfilo(), $pagina);

// estrazione informazioni su data nascita dell'utente
$dataNascita = DateTime::createFromFormat("Y-m-d", $user->getDataNascita());
$giornoNascita = intval($dataNascita->format("d"));
$meseNascita = intval($dataNascita->format("m"));
$annoNascita = intval($dataNascita->format("Y"));

// popolazione select#giorno_nascita
$GG = "";
for ($i = 1; $i <= 31; $i++) {
    $GG .= "<option value=\"$i\"" . (($i === $giornoNascita) ? " selected=\"selected\"" : "") . ">$i</option>";
}
$pagina = str_replace("<GIORNO/>", $GG, $pagina);

// popolazione select#mese_nascita
$MM = "";
$mesi = array("gennaio", "febbraio", "marzo", "aprile", "maggio", "giugno", "luglio", "agosto", "settembre", "ottobre", "novembre", "dicembre");
for ($i = 1; $i <= 12; $i++) {
    $MM .= "<option value=\"$i\"" . (($i === $meseNascita) ? " selected=\"selected\"" : "") . ">" . $mesi[$i - 1] . "</option>";
}
$pagina = str_replace("<MESE/>", $MM, $pagina);

// popolazione select#anno_nascita
$AAAA = "";
for ($i = intval(date("Y")), $bottom = intval(date("Y")) - 100; $i >= $bottom; $i--) {
    $AAAA .= "<option value=\"$i\"" . (($i === $annoNascita) ? " selected=\"selected\"" : "") . ">$i</option>";
}
$pagina = str_replace("<ANNO/>", $AAAA, $pagina);

// aggiorno il contenuto della pagina modificato (possibilmente)
$pagina = str_replace("<NOME/>", $user->getNome(), $pagina);
$pagina = str_replace("<COGNOME/>", $user->getCognome(), $pagina);
$pagina = str_replace("<MAIL/>", $user->getMail(), $pagina);
$pagina = str_replace("<USERNAME/>", $user->getUsername(), $pagina);
$pagina = str_replace("<TELEFONO/>", $user->getTelefono(), $pagina);

if(isset($_POST["modifica_img_profilo"]) || isset($_POST["modifica_password"]) || isset($_POST["modifica_dati_personali"])) {
    $pagina = addUserNotificationToPage($pagina, $messageToUser, $divId, $divClasses, $inParagraph); // sostituisce <INFO_BOX/>
}

// aggiorno l'oggetto in sessione perché l'ho potenzialmente aggiornato
$_SESSION["user"] = $user;

$pagina = str_replace("<INFO_BOX/>", "", $pagina);

echo $pagina;
