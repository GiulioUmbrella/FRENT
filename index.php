<?php
require "./php/CheckMethods.php";
require_once "./php/classi/Frent.php";
require_once "./php/CheckMethods.php";
//require_once "./php/classi/Annuncio.php";
try {
    session_start();
    $pagina = file_get_contents("./html/index.html");
    if (isset($_SESSION["user"])){
        $pagina =  str_replace("<HEADER/>",file_get_contents("./php/components/header_logged.html"),$pagina);
    }else{
        $pagina =  str_replace("<HEADER/>",file_get_contents("./php/components/header_no_logged.html"),$pagina);
    }
    
    $frent = new Frent(new Database("localhost", "root", "", "frentdb"));
    
    $annunci_recenti = $frent->getAnnuncio(1);
    $titolo = $annunci_recenti->getTitolo();
    $path = $annunci_recenti->getImgAnteprima();
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
    
    $pagina= str_replace("<RECENTI/>",$content,$pagina);
    echo $pagina;
    
} catch (Eccezione $ex) {
    echo "eccezione " . $ex->getMessage();
}
