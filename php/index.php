<?php
require_once "./components/form_functions.php";
require_once "load_Frent.php";
try {
    $pagina = file_get_contents("./components/index.html");
    require_once "./load_header.php";
    
    if (isset($_SESSION["user"])) {
        $dataCorrente = date("m-d");
        $dataNascita = date("m-d", strtotime($_SESSION["user"]->getDataNascita()));
        if ($dataNascita === $dataCorrente){
            $pagina= str_replace("<h1>Frent</h1>","<h1>Buon compleanno!</h1>",$pagina);
        }
        $pagina = str_replace("<ADMINLINK/>", "", $pagina);
    } else if (isset($_SESSION["admin"])) {
        $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_admin_logged.html"), $pagina);
        $pagina = str_replace("<ADMINLINK/>", "", $pagina);
        
    } else {
        $pagina = str_replace("<ADMINLINK/>", "<li><a href=\"./login_admin.php\" title=\"Vai alla pagina per l'amministratore\">Accesso amministratore</a>
        </li>", $pagina);
        $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_no_logged.html"), $pagina);
    }

    $content = "";
    
    $citta_ricercabili = $frent->getCittaAnnunci();
    $lista_citta_ricercabili = "";
    foreach ($citta_ricercabili as $citta_ricercabile) {
        
        if (isset($_SESSION["citta"]) and $citta_ricercabile == $_SESSION["citta"]) {
            $lista_citta_ricercabili .= "<option value=\"$citta_ricercabile\" selected>$citta_ricercabile</option>";
        } else {
            $lista_citta_ricercabili .= "<option value=\"$citta_ricercabile\">$citta_ricercabile</option>";
        }
        
    }
    
    require_once "./components/setMinMaxDates.php";
    $content = "";

    $annunci = array();
    if (isset($_SESSION["user"]) and get_class($_SESSION["user"]) == "Utente") {
        $annunci = $frent->getUltimiAnnunciApprovati($_SESSION["user"]->getIdUtente());
    } else {
        $annunci = $frent->getUltimiAnnunciApprovati();
    }

    foreach ($annunci as $annuncio) {
        $item = file_get_contents("./components/item_index_annuncio.html");
        $item = str_replace("</ID>", $annuncio->getIdAnnuncio(),$item);
        $item = str_replace("</TITOLO>", $annuncio->getTitolo(),$item);
        $item = str_replace("</PATH>", uploadsFolder() . $annuncio->getImgAnteprima(),$item);
        $item = str_replace("</DESC>", $annuncio->getDescAnteprima(),$item);
        
        $content .=  $item;
    }
    $numOspiti = 1;
    $dataInizio = "";
    $dataFine = "";
    
    if (isset($_SESSION["datiRicercaMancanti"])) {
        $pagina = str_replace("<INFO_BOX/>", "<p class=\"messaggio_errore\">" . $_SESSION["datiRicercaMancanti"] . "</p>", $pagina);
        if (isset($_SESSION["numOspiti"])){
            $numOspiti = $_SESSION["numOspiti"];
            unset($_SESSION["numOspiti"]);
        }
        if (isset($_SESSION["dataInizio"])){
            $dataInizio = $_SESSION["dataInizio"];
    
            unset($_SESSION["dataInizio"]);
        }
        if (isset($_SESSION["dataFine"]))
        {
            $dataFine = $_SESSION["dataFine"];
            unset($_SESSION["dataFine"]);
        }
        unset($_SESSION["datiRicercaMancanti"]);
    } else {
        $pagina = str_replace("<INFO_BOX/>", "", $pagina);
    }
    $pagina = str_replace("<DATAINIZIO/>", $dataInizio, $pagina);
    $pagina = str_replace("<DATAFINE/>", $dataInizio, $pagina);
    $pagina = str_replace("<NUMOSPITI/>", $numOspiti, $pagina);
    
    $pagina = str_replace("<CITIES_RICERCA/>", $lista_citta_ricercabili, $pagina);
    
    $pagina = str_replace("<RECENTI/>", $content, $pagina);
    
    if(isset($_SESSION["delete_user_message"])) {
        $pagina = str_replace("<INFO_BOX/>", "<p class=\"messaggio_ok\">" . $_SESSION["delete_user_message"] . "</p>", $pagina);
        unset($_SESSION["delete_user_message"]);
    } else {
        $pagina = str_replace("<INFO_BOX/>", "", $pagina);
    }
    echo $pagina;
} catch (Eccezione $ex) {
    echo "eccezione " . $ex->getMessage();
}
