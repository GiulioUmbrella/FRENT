<?php
require_once "load_Frent.php";
require_once "./class_ImageManager.php";
require_once "./components/form_functions.php";

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
}

$pagina = file_get_contents("./components/nuovo_annuncio.html");
$pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
$pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);

// se l'utente ha premuto il tasto di invio dati visualizzo la pagina con il form
if(isset($_POST["nuovo_annuncio"])) {
    $risultato_validazione = checkValuesForKeysInAssociativeArray($_POST, ["titolo", "descrizione", "max_ospiti", "desc_anteprima", "prezzo_notte", "via", "numero_civico", "citta"], TRUE);
    /**
     * se $form_non_valido === TRUE ci sono valori di $_POST non settati
     */
    $form_non_valido = in_array(FALSE, $risultato_validazione);

    $messageToUser = "";
    $divId = "info_box";
    $divClasses = "aligned_with_form";

    if($form_non_valido) { // IF1 - RAMO VERO IF1
        $messageToUser = formValidationErrorList("C'è stato un errore nella compilazione del modulo.", $risultato_validazione);
    } else { // FINE RAMO VERO IF1 - RAMO FALSO IF1
        try {
            $codice_inserimento = $frent->insertAnnuncio(
                $_POST["titolo"],
                $_POST["descrizione"],
                "defaultImages/imgAnteprimaAnnuncioDefault.png",
                $_POST["desc_anteprima"],
                $_POST["via"] . ", " . $_POST["numero_civico"],
                $_POST["citta"],
                intval($_POST["max_ospiti"]),
                floatval($_POST["prezzo_notte"])
            );
            
            if($codice_inserimento === -1 || $codice_inserimento === -2) {
                $messageToUser = htmlentities("C'è stato un errore nel processo di inserimento del nuovo annuncio.");
            } else {
                $user = $_SESSION["user"];
                $imageManager = new ImageManager("../uploads/user" . $user->getIdUtente() . "/");
                $imageManager->setFile("img_anteprima", "annuncio" . $codice_inserimento);

                // tento il salvataggio del file
                if($imageManager->saveFile()) {
                    $codice_aggiornamento = $frent->editAnnuncio(
                        intval($codice_inserimento),
                        $_POST["titolo"],
                        $_POST["descrizione"],
                        "user" . $user->getIdUtente() . "/" . $imageManager->fileName(),
                        $_POST["desc_anteprima"],
                        $_POST["via"] . ", " . $_POST["numero_civico"],
                        $_POST["citta"],
                        intval($_POST["max_ospiti"]),
                        floatval($_POST["prezzo_notte"])
                    );

                    if($codice_aggiornamento === -1) {
                        $messageToUser = htmlentities("L'annuncio è stato salvato, ma non è stato possibile inserire l'immagine di anteprima richiesta (errore di aggiornamento).");
                    } else {
                        $messageToUser = htmlentities("L'annuncio è stato salvato.");
                    }
                } else {
                    $messageToUser = htmlentities("L'annuncio è stato salvato, ma non è stato possibile inserire l'immagine di anteprima richiesta (errore di salvataggio).");
                }

                $messageToUser .= "<a href=\"mio_profilo.php\" title=\"Vai alla pagina del profilo\">Torna alla lista degli annunci</a>.";
            }
        } catch(Eccezione $exc) {
            $messageToUser = htmlentities("C'è stato un errore nel processo di inserimento del nuovo annuncio. Errore riscontrato: ") . $exc->getMessage();
        }
    }
    $pagina = addUserNotificationToPage($pagina, $messageToUser, $divId, $divClasses);
}

echo $pagina;