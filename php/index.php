<?php
//echo phpinfo();
require_once "./CheckMethods.php";
require_once "./class_Frent.php";
require_once "./class_CredenzialiDB.php";
require_once "./class_Annuncio.php";
try {
    session_start();
    $pagina = file_get_contents("./components/index.html");
    if (isset($_SESSION["user"])) {
        $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
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
    foreach ($citta_ricercabili as $citta_ricercabile){
        $lista_citta_ricercabili = $lista_citta_ricercabili . "<option value=\"$citta_ricercabile\"> \n";
    }
    $pagina = str_replace("<CITIES_RICERCA/>", $lista_citta_ricercabili, $pagina);

    $annunci= $frent->getUltimiAnnunciApprovati();
    foreach ($annunci as $annuncio){
        $titolo=  $annuncio->getTitolo();
        $id= $annuncio->getIdAnnuncio();
        $path= $annuncio->getImgAnteprima();
        $content .= "<li class=\"elemento_sei_pannelli\"><a href='./dettagli_annuncio.php?id=$id'>$titolo<img src=\"$path\"
                alt=\"descrizione immagine di antemprima annuncio\"/></a></li>";
    }
   
    
    $pagina = str_replace("<RECENTI/>", $content, $pagina);
    echo $pagina;
    
} catch (Eccezione $ex) {
    echo "eccezione " . $ex->getMessage();
}
