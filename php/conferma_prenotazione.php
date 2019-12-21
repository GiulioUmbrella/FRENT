<?php
require_once "./class_Annuncio.php";
require_once "./class_Utente.php";
session_start();
require_once "components/connessione_utente.php";
$pagina = file_get_contents("./components/conferma_prenotazione.html");
$pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);

if (!$_SESSION["user"]) {
    header("Location: ./login.php");
}
if (isset($_SESSION["annuncio"]) AND isset($_SESSION["dataInizio"])
    and isset($_SESSION["dataFine"]) and isset($_SESSION["numOspiti"])) {
//    $pagina = str_replace("<LINK/>", "id=" . $_SESSION["id"] . "&dataInizio=" . $_POST["dataInizio"]
//        . "&dataFine=" . $_POST["dataFine"] . "&numOspiti=" . $_POST["numOspiti"], $pagina);
//    $pagina = str_replace("<IDANNUNCIO/>", $_SESSION["annuncio"]->getIdAnnuncio(), $pagina);
//
    $pagina = str_replace("<TITOLO/>", $_SESSION["annuncio"]->getTitolo(), $pagina);
//
    $annuncio = $_SESSION["annuncio"];
    $utente = $manager->getUser(intval($annuncio->getIdHost()));
    $pagina = str_replace("<TITOLO/>", $annuncio->getTitolo(), $pagina);
    $pagina = str_replace("<DATAINIZIO/>", $_SESSION["dataInizio"], $pagina);
    $pagina = str_replace("<DATAFINE/>", $_SESSION["dataFine"], $pagina);
    $pagina = str_replace("<PROPRIETARIO/>", $utente->getUserName(), $pagina);
    $pagina = str_replace("<INDIRIZZO/>", $annuncio->getIndirizzo() . " a " . $annuncio->getCitta(), $pagina);
    $pagina = str_replace("<NUMOSPITI/>", $_SESSION["numOspiti"], $pagina);
    $pagina = str_replace("<TOTALE/>", intval($_SESSION["numOspiti"]) * $annuncio->getPrezzoNotte() . "&euro;", $pagina);
}else{

}

$pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);
echo $pagina;