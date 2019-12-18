<?php
//require_once "./php/classi/class_Annuncio.php";
session_start();
$pagina = file_get_contents("./components/condizioni_di_utilizzo.html");
if (isset($_SESSION["user"])) {
    $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
} else if (isset($_SESSION["admin"])) {
    $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_admin_logged.html"), $pagina);
    
} else {
    $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_no_logged.html"), $pagina);
}
$pagina = str_replace("<FOOTER/>",file_get_contents("./components/footer.html"),$pagina);

echo $pagina;