<?php
session_start();
$pagina = file_get_contents("./components/faq.html");
if (isset($_SESSION["admin"])){
    $pagina= str_replace("<HEADER/>", file_get_contents("./components/header_admin_logged.html"),$pagina);
}elseif (isset($_SESSION["user"])){
    $pagina= str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"),$pagina);
}else{
    $pagina= str_replace("<HEADER/>", file_get_contents("./components/header_no_logged.html"),$pagina);
}
$pagina = str_replace("<FOOTER/>",file_get_contents("./components/footer.html"),$pagina);
echo $pagina;