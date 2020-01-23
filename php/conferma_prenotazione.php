<?php
require_once "./load_Frent.php";

$pagina = file_get_contents("./components/conferma_prenotazione.html");
$pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
$pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);

if (!isset($_SESSION["user"])) {
    header("Location: ./login.php");
}

if (isset($_SESSION["annuncio"]) and isset($_SESSION["dataInizio"]) and isset($_SESSION["dataFine"]) and isset($_SESSION["numOspiti"])) {
    $annuncio = $_SESSION["annuncio"];
    $utente = $frent->getUser(intval($annuncio->getIdHost()));
    $pagina = str_replace("<TITOLO/>", $annuncio->getTitolo(), $pagina);
    $pagina = str_replace("<DATAINIZIO/>", $_SESSION["dataInizio"], $pagina);
    $pagina = str_replace("<DATAFINE/>", $_SESSION["dataFine"], $pagina);
    $pagina = str_replace("<PROPRIETARIO/>", $utente->getUserName(), $pagina);
    $pagina = str_replace("<INDIRIZZO/>", $annuncio->getIndirizzo() . ", " . $annuncio->getCitta(), $pagina);
    $pagina = str_replace("<NUMOSPITI/>", $_SESSION["numOspiti"], $pagina);
    $durata = (strtotime($_SESSION["dataFine"])- strtotime($_SESSION["dataInizio"]))/(24*3600);
    $pagina = str_replace("<TOTALE/>", "&euro; " . (intval($_SESSION["numOspiti"]) * $annuncio->getPrezzoNotte() * ($durata)), $pagina);
    $prenotazione = Occupazione::build();
    $prenotazione->setIdAnnuncio($annuncio->getIdAnnuncio());
    $prenotazione->setDataInizio($_SESSION["dataInizio"]);
    $prenotazione->setDataFine($_SESSION["dataFine"]);
    $prenotazione->setNumOspiti(intval($_SESSION["numOspiti"]));
    
    $_SESSION["prenotazione"]= $prenotazione;
    $pagina = str_replace("<LINK/>", "id=" . $annuncio->getIdAnnuncio() . "&dataInizio=" . $_SESSION["dataInizio"]
        . "&dataFine=" . $_SESSION["dataFine"] . "&numOspiti=" . $_SESSION["numOspiti"], $pagina);
}else{
    header("Location: ./404.php");
}

echo $pagina;