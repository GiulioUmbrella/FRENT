<?php
$pagina = file_get_contents("./components/riepilogo_prenotazione.html");
require_once "load_Frent.php";
if (isset($_SESSION["user"])){
    
    echo $pagina;
}else{
    header("Location: ./login.php");
}
