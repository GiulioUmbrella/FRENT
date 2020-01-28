<?php
require_once "load_Frent.php";

if (!isset($_SESSION["user"])) {
    header("Location: ./login.php");
}

try {
    
    
    if (isset($_GET["id"])) {
        $idAnnuncio = intval($_GET["id"]);
        if ($_SESSION["user"]->getIdUtente() !== $frent->getAnnuncio($idAnnuncio)->getIdHost()) {
            $_SESSION["msg"] = htmlentities("Non hai il permesso per effettuare quest'operazione!");
            header("Location: ./error_page.php");
        }
        
        $res_code = $frent->deleteAnnuncio($idAnnuncio);
        if ($res_code === 0) {
            header("Location: ./miei_annunci.php");
        } elseif ($res_code === -1) {
            $_SESSION["msg"] = htmlentities("l'annuncio non è eliminabile perchè ci sono prenotazioni in corso o future");
            header("Location: ./error_page.php");
        } elseif ($res_code === -2) {
            $_SESSION["msg"] = htmlentities("Eliminazione fallita!");
            header("Location: ./error_page.php");
        }
    } else {
        $_SESSION["msg"] = htmlentities("Non è stato fornito un ID di un annuncio.");
        header("Location: ./error_page.php");  
    }
} catch (Eccezione $e) {
    $_SESSION["msg"] = $e->getMessage();
    header("Location: ./error_page.php");
}
