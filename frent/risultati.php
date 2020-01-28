<?php
require_once "./components/form_functions.php";
require_once "load_Frent.php";
$pagina = file_get_contents("./components/risultati.html");
require_once "./components/setMinMaxDates.php";
if (isset($_SESSION["admin"])) {
    $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_admin_logged.html"), $pagina);
} elseif (isset($_SESSION["user"])) {
    $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
} else {
    $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_no_logged.html"), $pagina);
}
$citta="";
$numOspiti=1;
$dataInizio ="";
$dataFine ="";
if (isset($_GET["citta"])){
    $citta = $_GET["citta"];
    $pagina = str_replace("<CITTA/>", $citta, $pagina);
} else {
    $pagina = str_replace("<CITTA/>", "", $pagina);
}
if (isset($_GET["numOspiti"])){
    $numOspiti =intval($_GET["numOspiti"]);
    $_SESSION["numOspiti"]= $numOspiti;
    $pagina=str_replace("<VALUENUMERO/>",$numOspiti,$pagina);
}else{
    $pagina=str_replace("<VALUENUMERO/>",1,$pagina);
    
}
if (isset($_GET["dataInizio"])){
    $dataInizio =$_GET["dataInizio"];
    $_SESSION["dataInizio"] = $dataInizio;
    $pagina=str_replace("<VALUEINIZIO/>",$dataInizio,$pagina);
}else{
    $pagina=str_replace("<VALUEINIZIO/>","",$pagina);
    
}
if (isset($_GET["dataFine"])){
    $dataFine =$_GET["dataFine"];
    $_SESSION["dataFine"] = $dataFine;
    $pagina=str_replace("<VALUEFINE/>",$dataFine,$pagina);
}else{
    $pagina=str_replace("<VALUEFINE/>","",$pagina);
}

try {
    $citta_ricercabili = $frent->getCittaAnnunci();
    $lista_citta_ricercabili = "";
    foreach ($citta_ricercabili as $citta_ricercabile) {
        $lista_citta_ricercabili .= "<option value=\"$citta_ricercabile\">$citta_ricercabile</option>";
    }
    $pagina = str_replace("<CITIES_RICERCA/>", $lista_citta_ricercabili, $pagina);
} catch (Eccezione $e) {
    $content="<h1>" . $e->getMessage() . "</h1>";
}

$content = "";
try {
    $risultati = $frent->ricercaAnnunci($citta, $numOspiti, $dataInizio, $dataFine);
    if (count($risultati) ==0) throw new Eccezione("Non ci sono annunci con questi parametri di ricerca.");
    foreach ($risultati as $annuncio) {
        $path= uploadsFolder() . $annuncio->getImgAnteprima();
        $recensioni=$frent->getCommentiAnnuncio($annuncio->getIdAnnuncio());
        $numeroRecensione= count($recensioni);
        $punteggio=0;
        if ($numeroRecensione!=0){
            foreach ($recensioni as $recensione)
                $punteggio=$recensione->getValutazione()+$punteggio;
            $punteggio=$punteggio/$numeroRecensione;
        }
        $item = file_get_contents("./components/item_annuncio_risultato.html");
        $item = str_replace("<TITOLO/>",$annuncio->getTitolo(),$item);
        $item = str_replace("<ID/>",$annuncio->getIdAnnuncio(),$item);
        $item = str_replace("<PUNTEGGIO/>", $punteggio,$item);
        $item = str_replace("<PATH/>", $path,$item);
        $item = str_replace("<DES/>", $annuncio->getDescrizione(),$item);
        $item = str_replace("<PREZZO/>", $annuncio->getPrezzoNotte(),$item);
        $item = str_replace("<NUMRECENSIONE/>",$numeroRecensione,$item);
        $item = str_replace("<DI/>",$dataInizio,$item);
        $item = str_replace("<DF/>",$dataFine,$item);
        $item = str_replace("<NO/>",$numOspiti,$item);
        $item = str_replace("<DESCANTE/>",$annuncio->getDescAnteprima(),$item);
        
        $content .= $item ;
    }
} catch (Eccezione $e) {
    $content="<h1>" . $e->getMessage() . "</h1>";
    
}



$pagina = str_replace("<RISULTATI/>", $content, $pagina);
$pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);
echo $pagina;
