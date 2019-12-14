<?php
$pagina = file_get_contents("../../html/login_admin.html");
if(isset($_GET["error_code"])){
    $pagina = str_replace("<p id=\"credenziali_errate\"></p>",
        "<p id=\"credenziali_errate\">$msg</p>", $pagina);
}

echo $pagina;
