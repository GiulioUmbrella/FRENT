<?php
$pagina = file_get_contents("./components/conferma_prenotazione.html");
require_once "./class_Annuncio.php";
session_start();

if (!$_SESSION["user"]) {
    header("Location: ./login.php");
}
if (isset($_SESSION["annuncio"]) AND isset($_POST["data_inizio"])
    and isset($_POST["data_fine"]) and isset($_POST["numOspiti"])) {
    $annuncio = $_SESSION["annuncio"];
    $pagina = str_replace("<TITOLO/>", $annuncio->getTitolo(), $pagina);
    $pagina = str_replace("<DATAINIZIO/>", $_POST["data_inizio"], $pagina);
    $pagina = str_replace("<DATAFINE/>", $_POST["data_fine"], $pagina);
    $pagina = str_replace("<PROPRIETARIO/>", $annuncio->getIdHost(), $pagina);
    $pagina = str_replace("<INDIRIZZO/>", $annuncio->getIndirizzo() . " a " . $annuncio->getCitta(), $pagina);
    $pagina = str_replace("<NUMOSPITI/>", $_POST["numOspiti"], $pagina);
    $pagina = str_replace("<TOTALE/>", intval($_POST["numOspiti"]) * $annuncio->getPrezzoNotte() . "&euro;", $pagina);
}else{

}

$pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
$pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);
echo $pagina;