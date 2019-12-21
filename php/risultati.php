<?php
session_start();
require_once "class_Frent.php";
require_once "./class_CredenzialiDB.php";
$pagina = file_get_contents("./components/risultati.html");
if (isset($_SESSION["admin"])) {
    $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_admin_logged.html"), $pagina);
} elseif (isset($_SESSION["user"])) {
    $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
} else {
    $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_no_logged.html"), $pagina);
}
require_once "load_Frent.php";
$citta="";
require_once "./components/setMinMaxDates.php";
$numOspiti =intval(1);
$dataInizio ="";
$dataFine ="";
if (isset($_GET["citta"])){
    $citta =$_GET["citta"];
    $pagina=str_replace("<VALUECITTA/>",$citta,$pagina);
}
if (isset($_GET["numOspiti"])){
    $pagina=str_replace("<VALUENUMERO/>",$numOspiti,$pagina);
    $numOspiti =intval($_GET["numOspiti"]);
}
if (isset($_GET["dataInizio"])){
    $dataInizio =$_GET["dataInizio"];
    $pagina=str_replace("<VALUEINIZIO/>",$dataInizio,$pagina);
    
}
if (isset($_GET["dataFine"])){
    $dataFine =$_GET["dataFine"];
    $pagina=str_replace("<VALUEFINE/>",$dataFine,$pagina);
}
$content = "";
try {
    $risultati = $frent->ricercaAnnunci($citta, $numOspiti, $dataInizio, $dataFine);
    foreach ($risultati as $annuncio) {
        $id = $annuncio->getIdAnnuncio();
        $Titolo = $annuncio->getTitolo();
        $descrizione=$annuncio->getDescrizione();
        $prezzoTotale=$annuncio->getPrezzoNotte();
        $path= $annuncio->getImgAnteprima();
        $recensioni=$frent->getCommentiAnnuncio($annuncio->getIdAnnuncio());
        $numeroRecensione= count($recensioni);
        $punteggio=0;
        if ($numeroRecensione!=0){
            foreach ($recensioni as $recensione)
                $punteggio=$recensione->getValutazione()+$punteggio;
            $punteggio=$punteggio/$numeroRecensione;
        }
        $content .= "
                <li>
                    <div class=\"intestazione_lista\">
                        <a href=\"dettagli_annuncio.php?id=$id&dataInizio=$dataInizio&dataFine=$dataFine&numOspiti=$numOspiti\"
                                tabindex=\"12\">$Titolo</a>
                        <p>Punteggio:$punteggio - Num Recensioni:$numeroRecensione </p>
                    </div>
                    <div class=\"corpo_lista\">
                        <img src =\"$path\" alt=\"Foto copertina della casa\" />
                        <div class=\"descrizione_annuncio\">
                        <p>$descrizione</p>
                        <p class=\"prezzototale\">Prezzo: $prezzoTotale&euro; persona/notte</p>
                        </div>
                    </div>
                </li>";
    }
} catch (Eccezione $e) {
    $content="<h1>Non ci sono annunci che soddisfano i parametri di ricerca!</h1>";
}
//$risultati=array();



$pagina = str_replace("<RISULTATI/>", $content, $pagina);
$pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);
echo $pagina;