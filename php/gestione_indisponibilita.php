<?php
session_start();
require_once "Frent.php";
require_once "./CredenzialiDB.php";
$pagina = file_get_contents("./components/gestione_indisponibilita.html");
if (isset($_SESSION["user"])) {
    $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
    $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_admin_logged.html"), $pagina);
}else{
    header("Location: login.php");
}




echo $pagina;