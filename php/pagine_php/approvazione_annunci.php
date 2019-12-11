<?php
require_once "../classi/Annuncio.php";
require_once "../classi/Frent.php";
require_once "../classi/Database.php";

$connessione = new Database("localhost", "root", "", "frentdb");
try {
    session_start();
    $manager = new Frent($connessione, $_SESSION["admin_obj"]);
    $annunci = $manager->adminGetAnnunci();
    
    $pagina = file_get_contents("../../html/approvazione_annunci.html");
    $content = "<h2>Ci sono " . count($annunci) . " annunci da controllare: </h2> <ul>";
    
    foreach ($annunci as $annuncio) {
        $id = $annuncio->getIdAnnuncio();
        $titolo= $annuncio->getTitolo();
        $content .= "<li>";
        $content .= "<a href=\"dettagli_annuncio.php?id=$id\">$titolo</a>";
        // todo
        $content .= "<a href=\"approvazione_annuncio.php?idAnnuncio=$id&approvato=true\" class=\"link_gestione link_approva\">Approva</a>";
        $content .= "<a href=\"?idAnnuncio=$id&approvato=true\" class=\"link_gestione link_rigetta\">Non approva</a></li>";
    }
    $content .= "  </ul>";
    
    $pagina = str_replace("<Flag1/>", $content, $pagina);
    echo $pagina;
    
} catch (Eccezione $ex) {
    echo "eccezione" . $ex->getMessage();
}
