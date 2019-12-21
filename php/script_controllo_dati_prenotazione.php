<?php
require_once "./CheckMethods.php";
require_once "class_Annuncio.php";
require_once "class_Occupazione.php";
require_once "class_Utente.php";

session_start();
if (!isset($_SESSION["user"])) {
    header("Location: ./login.php");
}
if (!isset($_SESSION["annuncio"])) {
    header("Location: ./index.php");
}
$pagina = file_get_contents("./components/conferma_prenotazione.html");
if (isset($_POST["conferma_prenotazione"])) {
    
    if (!(isset($_SESSION["dataInizio"]) and checkIsValidDate(strtotime($_SESSION["dataInizio"])))) {
        $_SESSION["dati_errati"] = "true";
        echo "Data inizio non valido!";
    } elseif (!(isset($_SESSION["dataFine"]) and checkIsValidDate(strtotime($_SESSION["dataFine"])))) {
        $_SESSION["dati_errati"] = "true";
        echo "Data fine non valido!";
    } elseif (!(isset($_SESSION["numOspiti"]) and is_int($_SESSION["numOspiti"]) and intval($_SESSION["numOspiti"]) > 0
        and intval($_SESSION["numOspiti"]) < $annuncio->getMaxOspiti())) {
        echo "numero ospiti non valido!";
        $_SESSION["dati_errati"] = "true";
    } else {
        $_SESSION["dati_errati"] = "false";
    }
    
    $pagina = str_replace("<LINK/>", "id=" . $_SESSION["id"] . "&dataInizio=" . $_SESSION["dataInizio"]
        . "&dataFine=" . $_SESSION["dataFine"] . "&numOspiti=" . $_SESSION["numOspiti"], $pagina);
    $pagina = str_replace("<IDANNUNCIO/>", $_SESSION["annuncio"]->getIdAnnuncio(), $pagina);
    
    $pagina = str_replace("<TITOLO/>", $_SESSION["annuncio"]->getTitolo(), $pagina);
    $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
    
    echo $pagina;
}
header("Location: ./dettagli_annuncio.php?id=".$_SESSION["id"]);
