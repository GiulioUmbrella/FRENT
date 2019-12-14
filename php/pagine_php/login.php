<?php

$pagina = file_get_contents("../components/login.html");
if(isset($_GET["error_code"])){
    $pagina = str_replace("<p id=\"credenziali_errate\"></p>",
        "<p id=\"credenziali_errate\">Credenziali Errate! Riprova</p>",$pagina);
}
echo $pagina;