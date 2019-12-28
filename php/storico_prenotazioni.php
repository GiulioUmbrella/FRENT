<?php
require_once "class_Frent.php";
require_once "class_Utente.php";
require_once "class_CredenzialiDB.php";
require_once "load_Frent.php";
if (isset($_SESSION["user"])) {
    if (isset($_SESSION["id_annuncio"])) {
        $annuncio = $frent->getAnnuncio(intval($_SESSION["id_annuncio"]));
        $pagina = file_get_contents("./components/storico_prenotazioni.html");
        $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
        $pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);
        $pagina = str_replace("<TITOLO/>",$annuncio->getTitolo(),$pagina);
    
        $user = $_SESSION["user"];
    
    echo $pagina;
    } else {
        header("Location: ./404.php");
    }
} else {
    header("Location: ./login.php");
}
