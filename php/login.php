<?php
require_once "./load_Frent.php";
require_once "./components/form_functions.php";

// carico pagina e componenti
$pagina = file_get_contents("./components/login.html");
$pagina = str_replace("<FORM/>", file_get_contents("./components/login_form.html"), $pagina);
$pagina = str_replace("<PAGE/>", "./login.php", $pagina);
$pagina= str_replace("<FOOTER/>",file_get_contents("./components/footer.html"),$pagina);

if(!isset($_POST["accedi"])) {
    $pagina = str_replace("<VALUEUSERNAME>", "", $pagina);
    $pagina = str_replace("<VALUEPASSWORD>", "", $pagina);
} else {
    $risultato_validazione = checkValuesForKeysInAssociativeArray($_POST, ["user", "password"]);
    /**
     * se $form_non_valido === TRUE ci sono valori di $_POST non settati
     */
    $form_non_valido = in_array(FALSE, $risultato_validazione);

    $messageToUser = "";
    $divId = "credenziali_errate";
    $divClasses = "aligned_with_form";

    if($form_non_valido) { // IF1 - RAMO VERO IF1
        $pagina = str_replace("<VALUEUSERNAME>","", $pagina);
        $pagina = str_replace("<VALUEPASSWORD>","", $pagina);
        $messageToUser = formValidationErrorList("C'è stato un errore nella compilazione del modulo.", $risultato_validazione);
    } else { // FINE RAMO VERO IF1 - RAMO FALSO IF1
        // reperisco i valori da post
        $nome = $_POST["user"];
        $password = $_POST["password"];

        try {
            $utente = $frent->login($nome, $password);
            
            // aggiunto l'oggetto utente alla sessione  amp
            $_SESSION["user"] = $utente;
            header("Location: ./index.php");
        } catch (Eccezione $exc) {
            $messageToUser = htmlentities("C'è stato un errore nel processo di accesso. Errore riscontrato: ") . $exc->getMessage();

            // i valori che ho trovato in $_POST li reinserisco nel form così l'utente non deve reinserirli
            $pagina = str_replace("<VALUEUSERNAME>", $nome, $pagina);
            $pagina = str_replace("<VALUEPASSWORD>", $password, $pagina);
            $pagina = str_replace("</msg>", $messageToUser, $pagina);
            
        }
    }
}

echo $pagina;
