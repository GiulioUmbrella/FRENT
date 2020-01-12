<?php
//echo phpinfo();
require_once "./CheckMethods.php";
require_once "./components/form_functions.php";
require_once "load_Frent.php";
try {
    $pagina = file_get_contents("./components/index.html");
    if (isset($_SESSION["user"])) {
        require_once "./load_header.php";
//        $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
        $pagina = str_replace("<ADMINLINK/>", "", $pagina);
    } else if (isset($_SESSION["admin"])) {
        $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_admin_logged.html"), $pagina);
        $pagina = str_replace("<ADMINLINK/>", "", $pagina);
        
    } else {
        $pagina = str_replace("<ADMINLINK/>", "<li><a href=\"./login_admin.php\" title=\"Vai alla pagina per l'amministratore\">Accesso amministratore</a>
        </li>", $pagina);
        $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_no_logged.html"), $pagina);
    }
    $frent = new Frent(new Database(CredenzialiDB::DB_ADDRESS, CredenzialiDB::DB_USER,
        CredenzialiDB::DB_PASSWORD, CredenzialiDB::DB_NAME));
    
    $content = "";
    
    $citta_ricercabili = $frent->getCittaAnnunci();
    $lista_citta_ricercabili = "";
    foreach ($citta_ricercabili as $citta_ricercabile) {
        
        if (isset($_SESSION["citta"]) and $citta_ricercabile == $_SESSION["citta"]) {
            echo "trovato!";
            $lista_citta_ricercabili .= "<option value=\"$citta_ricercabile\" selected>$citta_ricercabile</option>";
        } else {
            $lista_citta_ricercabili .= "<option value=\"$citta_ricercabile\">$citta_ricercabile</option>";
        }
        
    }
    
    require_once "./components/setMinMaxDates.php";
    $content = "";
    $id = -1;
    if (isset($_SESSION["user"]) and get_class($_SESSION["user"]) == "Utente")
        $id=$_SESSION["user"]->getIdUtente();
    $annunci = $frent->getUltimiAnnunciApprovati($id);
    $index=11;
    foreach ($annunci as $annuncio) {
        $titolo = $annuncio->getTitolo();
        $id = $annuncio->getIdAnnuncio();
        $path = uploadsFolder() . $annuncio->getImgAnteprima();
        $content .= "<li class=\"elemento_sei_pannelli\">
                    <a href='./dettagli_annuncio.php?id=$id' >$titolo<img src=\"$path\"
                alt=\"".$annuncio->getDescAnteprima()."\"/></a></li>";
    }
    $numOspiti = 1;
    $dataInizio = "";
    $dataFine = "";
    
    if (isset($_SESSION["datiRicercaMancanti"])) {
        $pagina = str_replace("<DATIMANCANTI/>", "<p>" . $_SESSION["datiRicercaMancanti"] . "</p>", $pagina);
        if (isset($_SESSION["numOspiti"])){
            $numOspiti = $_SESSION["numOspiti"];
            unset($_SESSION["numOspiti"]);
        }
        if (isset($_SESSION["dataInizio"])){
            echo $_SESSION["dataInizio"];
            $dataInizio = $_SESSION["dataInizio"];
    
            unset($_SESSION["dataInizio"]);
        }
        if (isset($_SESSION["dataFine"]))
        {
            $dataFine = $_SESSION["dataFine"];
            unset($_SESSION["dataFine"]);
        }
//        if (isset($_SESSION["citta"]))
//            $lista_citta_ricercabili .= "<option value=\"$citta_ricercabile\" selected='true'>";
//
        unset($_SESSION["datiRicercaMancanti"]);
    } else {
        $pagina = str_replace("<DATIMANCANTI/>", "", $pagina);
    }
    $pagina = str_replace("<DATAINIZIO/>", $dataInizio, $pagina);
    $pagina = str_replace("<DATAFINE/>", $dataInizio, $pagina);
    $pagina = str_replace("<NUMOSPITI/>", $numOspiti, $pagina);
    
    $pagina = str_replace("<CITIES_RICERCA/>", $lista_citta_ricercabili, $pagina);
    
    $pagina = str_replace("<RECENTI/>", $content, $pagina);
    
    if(isset($_SESSION["delete_user_message"])) {
        $pagina = str_replace("<DELETE_USER_MESSAGE/>", "<p>" . $_SESSION["delete_user_message"] . "</p>", $pagina);
        unset($_SESSION["delete_user_message"]);
    } else {
        $pagina = str_replace("<DELETE_USER_MESSAGE/>", "", $pagina);
    }
    echo $pagina;
} catch (Eccezione $ex) {
    echo "eccezione " . $ex->getMessage();
}
