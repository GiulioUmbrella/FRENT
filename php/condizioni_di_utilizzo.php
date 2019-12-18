<?php
//require_once "./php/classi/Annuncio.php";
try {
    session_start();
    $pagina = file_get_contents(($_SERVER["DOCUMENT_ROOT"]) . "/php/components/condizioni_di_utilizzo.html");
    $pagina = str_replace("<FOOTER/>",file_get_contents("../components/footer.html"),$pagina);
    echo $pagina;
} catch (Eccezione $ex) {
    echo "eccezione " . $ex->getMessage();
}
