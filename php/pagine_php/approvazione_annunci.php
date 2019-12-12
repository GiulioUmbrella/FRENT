<?php
require_once "../classi/Annuncio.php";
require_once "../classi/Frent.php";
require_once "../classi/Database.php";

try {
    session_start();
    $manager = new Frent(new Database("localhost", "root", "", "frentdb"),
        $_SESSION["admin"]);
    $annunci = $manager->adminGetAnnunci();
    $pagina = file_get_contents("../../html/approvazione_annunci.html");
    $content = "<h2>Ci sono " . count($annunci) . " annunci da controllare: </h2> <ul>";
    
    foreach ($annunci as $annuncio) {
        $id = $annuncio->getIdAnnuncio();
//        echo $id." ";
        $titolo = $annuncio->getTitolo();
        if ($annuncio->getStatoApprovazione() != 2) {
            $content .= "<li>";
            $content .= "<a href=\"dettagli_annuncio.php?id=$id\">" . $titolo . "</a>";
            $content .= "<a href=\"../script/script_approvazione_annunci.php?idAnnuncio=$id&approvato=1\"  class=\"link_gestione link_approva\">Approva</a>";
            $content .= "<a href=\"../script/script_approvazione_annunci.php?idAnnuncio=$id&approvato=2\" class=\"link_gestione link_rigetta\">Non approva</a></li>";
            
        }
    }
    $content .= "</ul>";
    $_SESSION["manager"] = $manager;
    $pagina = str_replace("<Flag1/>", $content, $pagina);
    echo $pagina;
    
} catch (Eccezione $ex) {
    echo "eccezione " . $ex->getMessage();
}
