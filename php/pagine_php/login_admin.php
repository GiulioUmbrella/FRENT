<?php
$pagina = file_get_contents("../components/login_admin.html");
if(isset($_GET["error_code"])){
    
    $pagina = str_replace("<p id=\"credenziali_errate\"></p>",
        "<p id=\"credenziali_errate\">Credenziali errate!</p>", $pagina);
}

echo $pagina;
