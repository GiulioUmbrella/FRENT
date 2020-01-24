<?php

require_once "./load_Frent.php";

if(!isset($_SESSION["user"])) {
    header("Location: ./login.php");
}

$nextPage = "";
try {
    switch($frent->deleteUser()) {
        case 0:
            unset($_SESSION["user"]);
            $_SESSION["delete_user_message"] = htmlentities("Cancellazione dell'utente avvenuta con successo.");
            $nextPage = "index.php";
        break;
        case -1:
            $_SESSION["delete_user_message"] = "Hai occupazioni correnti, non puoi attualmente eliminare il tuo profilo.";
            $nextPage = "mio_profilo.php";
        break;
        case -2:
            $_SESSION["delete_user_message"] = "Hai occupazioni future previste, non puoi attualmente eliminare il tuo profilo.";
            $nextPage = "mio_profilo.php";
        break;
        case -3:
            $_SESSION["delete_user_message"] = htmlentities("Non è stato possibile eliminare il tuo profilo. Prova ad uscire dal sistema e riaccedere.");
            $nextPage = "mio_profilo.php";
        break;
    }

} catch(Eccezione $exc) {
    $_SESSION["delete_user_message"] = htmlentities("Non è stato possibile eliminare il tuo profilo poiché è avvenuto un'errore. Errore riscontrato: ") . $exc->getMessage();
    $nextPage = "mio_profilo.php";
}

header("Location: $nextPage");    