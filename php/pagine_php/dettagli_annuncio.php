<?php
require_once "../classi/Frent.php";

try {
    session_start();
    $manager = new Frent(new Database("localhost", "root", "", "frentdb"),
        $_SESSION["admin"]);
    $id = $_GET["id"];
    $annuncio = $manager->getAnnuncio($id);
    $foto = $manager->getFotoAnnuncio($id);
    $commeni = $manager->getCommentiAnnuncio($id);
    $pagina = file_get_contents("../../html/dettagli_annunci.html");
    $pagina = str_replace("<TITOLO_ANNUNCIO/>",$annuncio->getTitolo(),$pagina);
    //todo calcolare la media dei commenti
    //
    $mediaCommenti=0;
    $pagina = str_replace("<Valutazione/>",$mediaCommenti,$pagina);
    
    
    
    $_SESSION["manager"] = $manager;
    $pagina = str_replace("<Flag1/>", $content, $pagina);
    echo $pagina;
    
} catch (Eccezione $ex) {
    echo "eccezione " . $ex->getMessage();
}
