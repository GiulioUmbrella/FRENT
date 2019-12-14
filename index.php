<?php
require ($_SERVER["DOCUMENT_ROOT"])."/php/CheckMethods.php";
require_once "./php/classi/Frent.php";
require_once "php/CredenzialiDB.php";

//require_once "./php/classi/Annuncio.php";
try {
    session_start();
    $pagina = file_get_contents("php/components/index.html");
    if (isset($_SESSION["user"])){
        $pagina =  str_replace("<HEADER/>",file_get_contents("./php/components/header_logged_index.html"),$pagina);
    }else{
        $pagina =  str_replace("<HEADER/>",file_get_contents("./php/components/header_no_logged.html"),$pagina);
    }
    $frent = new Frent(new Database(CredenzialiDB::DB_ADDRESS, CredenzialiDB::DB_USER,
        CredenzialiDB::DB_PASSWORD,CredenzialiDB::DB_NAME));
    
    $annunci_recenti = $frent->getAnnuncio(1);
    $titolo = $annunci_recenti->getTitolo();
    $path = "./immagini/borgoricco.jpg";
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
