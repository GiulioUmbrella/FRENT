<?php
$pagina = file_get_contents("./components/conferma_prenotazione.html");
session_start();

if (!$_SESSION["user"]){
    header("Location: ./login.php");
}
$pagina = str_replace("<HEADER/>",file_get_contents("./components/header_logged.html"),$pagina);
$pagina= str_replace("<FOOTER/>",file_get_contents("./components/footer.html"),$pagina);
echo $pagina;