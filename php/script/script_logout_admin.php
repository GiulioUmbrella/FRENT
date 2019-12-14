<?php
session_start();

if (isset($_SESSION["admin"])){
    unset($_SESSION["admin"]);
    $pagina = file_get_contents("../components/template.html");
    $pagina = str_replace("<MESSAGGIO/>","Sto effettuando la disconnessione!
    Stai per essere re-indirizzato verso la pagina di accesso dell'amministratore",$pagina);
    echo $pagina;
//    header("Location: ../pagine_php/login_admin.php");
    header("Refresh:3; url=../pagine_php/login_admin.php", true, 303);
}