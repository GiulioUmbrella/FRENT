<?php
$pagina = file_get_contents("./components/aggiungi_annunci_passaggio_2.html");

$pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
$pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);

$pagina = str_replace("<SELF/>", "./script_aggiungi_annuncio.php", $pagina);
echo $pagina;