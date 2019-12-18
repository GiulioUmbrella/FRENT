<?php
session_start();
require_once "Frent.php";
require_once "./CredenzialiDB.php";
$pagina = file_get_contents("./components/risultati.html");
if (isset($_SESSION["admin"])) {
    $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_admin_logged.html"), $pagina);
} elseif (isset($_SESSION["user"])) {
    $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
} else {
    $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_no_logged.html"), $pagina);
}
$frent = new Frent(new Database(CredenzialiDB::DB_ADDRESS, CredenzialiDB::DB_USER,
    CredenzialiDB::DB_PASSWORD, CredenzialiDB::DB_NAME));

$citta = "Padova";
$numOspiti = 1;
$dataInizio = "2019/12/11";
$dataFine = "2019/12/12";
$risultati = $frent->ricercaAnnunci($citta, $numOspiti, $dataInizio, $dataFine);
$content = "";
foreach ($risultati as $annuncio) {
    $id = $annuncio->getIdAnnuncio();
    $Titolo = $annuncio->getTitolo();
    $punteggio= 0;
    $descrizione=$annuncio->getDescrizione();
    $prezzoTotale=$annuncio->getPrezzoNotte();
    $path= $annuncio->getImgAnteprima();
    $content .= "<li><div class=\"intestazione_lista\">
      <a href=\"dettagli_annuncio.php?id=$id\"  tabindex=\"12\">$Titolo</a>
      <p>Punteggio: Num Recensioni:$punteggio </p></div><div class=\"corpo_lista\">
      <img src =\"$path\" alt=\"Foto copertina della casa\" /><div class=\"descrizione_annuncio\">
      <p>$descrizione</p><p class=\"prezzototale\">Prezzo: $prezzoTotale persona/notte</p></div></div></li>";
}


$pagina = str_replace("<RISULTATI/>", $risultati, $pagina);
$pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);
echo $pagina;