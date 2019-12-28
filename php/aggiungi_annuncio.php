<?php
require_once "./class_Frent.php";
require_once "./class_Database.php";
require_once "./class_CredenzialiDB.php";
require_once "load_Frent.php";
if (isset($_SESSION["user"])) {
    if (!isset($_POST["avanti"])) {
        $pagina = file_get_contents("./components/aggiungi_annunci_passaggio_1.html");
//        echo $_SERVER['PHP_SELF'];
        $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
        $pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);
        $pagina = str_replace("<SELF/>", "./aggiungi_annuncio.php", $pagina);
        echo $pagina;
    } else if (isset($_POST["avanti"])) {
        $pagina = file_get_contents("./components/aggiungi_annunci_passaggio_2.html");
    
        $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
        $pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);
    
        $pagina = str_replace("<SELF/>", "./script_aggiungi_annuncio.php", $pagina);
        echo $pagina;
        
    } else {
    
    }
} else {
    header("Location: login.php");
}
