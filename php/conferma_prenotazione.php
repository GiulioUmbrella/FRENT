<?php
require_once "./class_Annuncio.php";
require_once "./class_Utente.php";
require_once "./load_Frent.php";

$pagina = file_get_contents("./components/conferma_prenotazione.html");
$pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);

if (!$_SESSION["user"]) {
    header("Location: ./login.php");
}
if (isset($_SESSION["annuncio"]) AND isset($_SESSION["dataInizio"])
    and isset($_SESSION["dataFine"]) and isset($_SESSION["numOspiti"])) {
//    $pagina = str_replace("<IDANNUNCIO/>", $_SESSION["annuncio"]->getIdAnnuncio(), $pagina);
//
    $pagina = str_replace("<TITOLO/>", $_SESSION["annuncio"]->getTitolo(), $pagina);
//
    $annuncio = $_SESSION["annuncio"];
    $utente = $frent->getUser(intval($annuncio->getIdHost()));
    $pagina = str_replace("<TITOLO/>", $annuncio->getTitolo(), $pagina);
    $pagina = str_replace("<DATAINIZIO/>", $_SESSION["dataInizio"], $pagina);
    $pagina = str_replace("<DATAFINE/>", $_SESSION["dataFine"], $pagina);
    $pagina = str_replace("<PROPRIETARIO/>", $utente->getUserName(), $pagina);
    $pagina = str_replace("<INDIRIZZO/>", $annuncio->getIndirizzo() . " a " . $annuncio->getCitta(), $pagina);
    $pagina = str_replace("<NUMOSPITI/>", $_SESSION["numOspiti"], $pagina);
    $durata = (strtotime($_SESSION["dataFine"])- strtotime($_SESSION["dataInizio"]))/(24*3600);
    $pagina = str_replace("<TOTALE/>", intval($_SESSION["numOspiti"]) * $annuncio->getPrezzoNotte() * ($durata) . "&euro;", $pagina);
    
    $pagina = str_replace("<LINK/>", "id=" . $annuncio->getIdAnnuncio() . "&dataInizio=" . $_SESSION["dataInizio"]
        . "&dataFine=" . $_SESSION["dataFine"] . "&numOspiti=" . $_SESSION["numOspiti"], $pagina);
}else{
    header("Location: ./404.php");
}

$pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);
echo $pagina;