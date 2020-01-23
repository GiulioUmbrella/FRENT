<?php

$pagina = file_get_contents("./components/pag404.html");
session_start();
$header="";
if (isset($_SESSION["admin"])){
    $header = file_get_contents("./components/header_admin_logged.html");
}elseif (isset($_SESSION["user"])){
    $header = file_get_contents("./components/header_logged.html");
}else{
    $header = file_get_contents("./components/header_no_logged.html");
}
$pagina= str_replace("<HEADER/>",$header,$pagina);
$pagina = str_replace("<FOOTER/>",file_get_contents("./components/footer.html"),$pagina);
echo $pagina;