<?php
require_once "../classi/Frent.php";
$pagina = file_get_contents("../components/registrazione.html");

$pagina = str_replace("<HEADER/>",file_get_contents("../components/header_no_logged.html"),$pagina);

$pagina = str_replace("<FOOTER/>", file_get_contents("../components/footer.html"), $pagina);
echo $pagina;