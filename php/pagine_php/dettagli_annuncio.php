<?php
require_once "../classi/Frent.php";

try {
    session_start();
    $manager = new Frent(new Database("localhost", "root", "", "frentdb"),
        $_SESSION["admin"]);
    $id = intval($_GET["id"]);
    $annuncio = $manager->getAnnuncio($id);
    $commenti = $manager->getCommentiAnnuncio($id);
    $foto = $manager->getFotoAnnuncio($id);
    $pagina = file_get_contents("../../html/dettagli_annunci.html");
    $pagina = str_replace("<TITOLO_ANNUNCIO/>",$annuncio->getTitolo(),$pagina);
    //todo calcolare la media dei commenti
    $totale=0;
    $str_commenti="";
    foreach ($commenti as $commento){
        $totale +=intval( $commenti->getVotazione());
        
        $str_commenti.="";
        
    }
    //
    $mediaCommenti=0;
    $pagina = str_replace("<Valutazione/>",$mediaCommenti,$pagina);
    
    
    
    $_SESSION["manager"] = $manager;
    $pagina = str_replace("<Flag1/>", $content, $pagina);
    echo $pagina;
    
} catch (Eccezione $ex) {
    echo "eccezione " . $ex->getMessage();
}
