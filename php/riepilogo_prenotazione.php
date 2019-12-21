<?php
$pagina = file_get_contents("./components/riepilogo_prenotazione.html");
require_once "load_Frent.php";
if (isset($_SESSION["user"])){
    $pagina= str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"),$pagina);
    echo $pagina;
}else{
    header("Location: ./login.php");
}
