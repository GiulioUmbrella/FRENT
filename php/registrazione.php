<?php
require_once "./load_Frent.php";

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
    // popolazione select#birth_day
    $GG = "";
    for ($i = 1; $i <= 31; $i++) {
        $GG .= "<option value=\"$i\">$i</option>";
    }
    $pagina = str_replace("<GIORNO/>", $GG, $pagina);

    // popolazione select#birth_month
    $MM = "";
    $mesi = array("gennaio", "febbraio", "marzo", "aprile", "maggio", "giugno", "luglio", "agosto", "settembre", "ottobre", "novembre", "dicembre");
    for ($i = 1; $i <= 12; $i++) {
        $MM .= "<option value=\"$i\">" . $mesi[$i - 1] . "</option>";
    }
    $pagina = str_replace("<MESE/>", $MM, $pagina);

    // popolazione select#birth_year
    $AAAA = "";
    for ($i = intval(date("Y")), $bottom = intval(date("Y")) - 100; $i >= $bottom; $i--) {
        $AAAA .= "<option value=\"$i\">$i</option>";
    }
    $pagina = str_replace("<ANNO/>", $AAAA, $pagina);
    echo $pagina;
} else {
    if( // IF1
        !isset($_POST["nome"]) ||
        !isset($_POST["cognome"]) ||
        !isset($_POST["mail"]) ||
        !isset($_POST["username"]) ||
        !isset($_POST["password"]) ||
        !isset($_POST["ripeti_password"]) ||
        !isset($_POST["telefono"]) ||
        !isset($_FILES["img_profilo"]) ||
        !isset($_POST["birth_day"]) ||
        !isset($_POST["birth_month"]) ||
        !isset($_POST["birth_year"])
    ) { // RAMO VERO IF1
        echo str_replace(
            "<div id=\"credenziali_errate\"></div>",
            "<div id=\"credenziali_errate\" class=\"aligned_with_form\">" .
                "<p>C'è stato un errore nella compilazione del modulo.</p>" .
            "</div>",
            $pagina
        );
    } else { // FINE RAMO VERO IF1 - RAMO FALSO IF1
        if($_POST["password"] !== $_POST["ripetipassword"]) { // IF2 - RAMO VERO IF2
            echo str_replace(
                "<div id=\"credenziali_errate\"></div>",
                "<div id=\"credenziali_errate\" class=\"aligned_with_form\">" .
                    "<p>Le due password inserite non corrispondono.</p>" .
                "</div>",
                $pagina
            );
        } else { // FINE RAMO VERO IF2 - RAMO FALSO IF2
            $pathImgProfilo = "temp";
        
            try {
                $codice_registrazione = $frent->registrazione(
                    $_POST["nome"],
                    $_POST["cognome"],
                    $_POST["username"],
                    $_POST["mail"],
                    $_POST["password"],
                    $_POST["birth_year"] . "-" . $_POST["birth_month"] . "-" . $_POST["birth_day"],
                    $pathImgProfilo,
                    $_POST["telefono"]
                );

                if($codice_registrazione !== -1) { // IF3 - RAMO VERO IF3
                    echo str_replace(
                        "<div id=\"credenziali_errate\"></div>",
                        "<div id=\"credenziali_errate\" class=\"aligned_with_form\">" .
                            "<p>Registrazione avvenuta con successo! Puoi accedere cliccando su <a href=\"login.php\" title=\"Vai alla pagina di accesso\">questo link</a>.</p>" .
                        "</div>",
                        $pagina
                    );
                } else { // FINE RAMO VERO IF3 - RAMO FALSO IF3
                    echo str_replace(
                        "<div id=\"credenziali_errate\"></div>",
                        "<div id=\"credenziali_errate\" class=\"aligned_with_form\">" .
                            "<p>C'è stato un errore nel processo di registrazione.</p>" .
                        "</div>",
                        $pagina
                    ); 
                } // FINE RAMO FALSO IF3

            } catch(Eccezione $exc) {
                echo str_replace(
                    "<div id=\"credenziali_errate\"></div>",
                    "<div id=\"credenziali_errate\" class=\"aligned_with_form\">" .
                        "<p>C'è stato un errore nel processo di registrazione. Errore riscontrato: " . $exc->getMessage() . "</p>" .
                    "</div>",
                    $pagina
                ); 
            }
        } // FINE RAMO FALSO IF2
    } // FINE RAMO FALSO IF1
}

