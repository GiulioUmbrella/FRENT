<?php
session_start();
$pagina = file_get_contents("./components/riepilogo_prenotazione.html");

if (isset($_SESSION["user"])){
    
    echo $pagina;
}else{
    header("Location: ./login.php");
}
