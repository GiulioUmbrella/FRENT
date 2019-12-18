<?php

$pagina = file_get_contents("../components/mio_profilo_visualizza.html");
session_start();
if (isset($_SESSION["user"])){
    $pagina= str_replace("<HEADER/>",file_get_contents("../components/header_logged.html"),$pagina);
    $pagina= str_replace("<FOOTER/>",file_get_contents("../components/footer.html"),$pagina);
    echo $pagina;
}else{
    header("Location: login.php");
}