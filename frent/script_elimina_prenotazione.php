<?php
require_once "load_Frent.php";
try {
    
    if (isset($_GET["id"])) {
        $res_code = $frent->deletePrenotazione(intval($_GET["id"]));
        if ($res_code == 0)
            header("Location: ./mie_prenotazioni.php");
        else {
            if ($res_code == -1) {
                $_SESSION["msg"] = htmlentities("la prenotazione non è eliminabile in quanto prenotazione presente o passata");
            } elseif ($res_code == -2) {
                $_SESSION["msg"] = htmlentities("Eliminazione fallita!");
            }
            header("Location: ./error_page.php");
        }
    }
} catch (Eccezione $e) {
    $_SESSION["msg"] = $e->getMessage();
    header("Location: ./error_page.php");
}