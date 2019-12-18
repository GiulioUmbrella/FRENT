<?php
require_once "../classi/Frent.php";
require_once "../classi/Database.php";
require_once "../CredenzialiDB.php";
session_start();

if ($_SESSION["user"]) {
    if (!isset($POST["avanti"])) {
        $pagina = file_get_contents("../components/aggiungi_annunci_passaggio_1.html");
        echo $_SERVER['PHP_SELF'];
        $pagina = str_replace("<HEADER/>", file_get_contents("../components/header_logged.html"), $pagina);
        $pagina = str_replace("<FOOTER/>", file_get_contents("../components/footer.html"), $pagina);
        $pagina = str_replace("<SELF/>", "../pagine_php/aggiungi_annuncio.php", $pagina);
        echo $pagina;
    } else if (isset($POST["avanti"])) {
        $pagina = file_get_contents("../components/aggiungi_annunci_passaggio_2.html");
        
        $pagina = str_replace("<HEADER/>", file_get_contents("../components/header_logged.html"), $pagina);
        $pagina = str_replace("<FOOTER/>", file_get_contents("../components/footer.html"), $pagina);
        echo $pagina;
        
    } else {
    
    }
} else {
    header("Location: login.php");
}
