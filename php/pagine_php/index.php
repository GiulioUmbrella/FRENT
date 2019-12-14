<?php
require ($_SERVER["DOCUMENT_ROOT"]) . "/php/CheckMethods.php";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/php/classi/Frent.php";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/php/CredenzialiDB.php";

//require_once "./php/classi/Annuncio.php";
try {
    session_start();
    $pagina = file_get_contents(($_SERVER["DOCUMENT_ROOT"]) . "/php/components/index.html");
    if (isset($_SESSION["user"])){
        $pagina =  str_replace("<HEADER/>",file_get_contents(($_SERVER["DOCUMENT_ROOT"]) . "/php/components/header_logged_index.html"),$pagina);
        $pagina = str_replace("<ADMINLINK/>","",$pagina);
    }else{
        $pagina = str_replace("<ADMINLINK/>","<li><a href=\"../pagine_php/login_admin.php\" title=\"Vai alla pagina per l'amministratore\">Accesso amministratore</a>
        </li>",$pagina);
        $pagina =  str_replace("<HEADER/>",file_get_contents(($_SERVER["DOCUMENT_ROOT"]) . "/php/components/header_no_logged.html"),$pagina);
    }
    $frent = new Frent(new Database(CredenzialiDB::DB_ADDRESS, CredenzialiDB::DB_USER,
        CredenzialiDB::DB_PASSWORD,CredenzialiDB::DB_NAME));
    
    $annunci_recenti = $frent->getAnnuncio(1);
    $titolo = $annunci_recenti->getTitolo();
    $path = "../../immagini/borgoricco.jpg";
//    $path = $annunci_recenti->getImgAnteprima();
    $content="";
    $content .= "<li class=\"elemento_sei_pannelli\"><a>$titolo<img src=\"$path\"
                alt=\"descrizione immagine di antemprima annuncio\"/></a></li>";
    $content .= "<li class=\"elemento_sei_pannelli\"><a>$titolo<img src=\"$path\"
                alt=\"descrizione immagine di antemprima annuncio\"/></a></li>";
    $content .= "<li class=\"elemento_sei_pannelli\"><a>$titolo<img src=\"$path\"
                alt=\"descrizione immagine di antemprima annuncio\"/></a></li>";
    $content .= "<li class=\"elemento_sei_pannelli\"><a>$titolo<img src=\"$path\"
                alt=\"descrizione immagine di antemprima annuncio\"/></a></li>";
    $content .= "<li class=\"elemento_sei_pannelli\"><a>$titolo<img src=\"$path\"
                alt=\"descrizione immagine di antemprima annuncio\"/></a></li>";
    $content .= "<li class=\"elemento_sei_pannelli\"><a>$titolo<img src=\"$path\"
                alt=\"descrizione immagine di antemprima annuncio\"/></a></li>";
    
    
    $pagina = str_replace("<RECENTI/>",$content,$pagina);
    echo $pagina;
    
} catch (Eccezione $ex) {
    echo "eccezione " . $ex->getMessage();
}
