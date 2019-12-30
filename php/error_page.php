<?php
session_start();
$pagina = file_get_contents("./components/error_page.html");
if (isset($_SESSION["msg"])){
    $pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"),$pagina);
    $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"),$pagina);
    $pagina = str_replace("<MSG/>",$_SESSION["msg"], $pagina);
    echo $pagina;
}
