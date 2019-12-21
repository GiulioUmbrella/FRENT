<?php
require_once "./class_Annuncio.php";
require_once "./class_Frent.php";
require_once "./class_Database.php";
require_once "./class_CredenzialiDB.php";

try {
    require_once "./load_Frent.php";
    if (!isset($_SESSION["admin"])) {
        header("Location: ./login_admin.php");
    }
    
    $annunci = $frent->adminGetAnnunci();
    $pagina = file_get_contents("./components/approvazione_annunci.html");
    $msg = "Bentornato " . $_SESSION["admin"]->getUserName() . "!";
    $pagina = str_replace("<SALUTO/>", $msg, $pagina);
    $content = "<h2>Ci sono " . count($annunci) . " annunci da controllare: </h2> <ul>";
    
    foreach ($annunci as $annuncio) {
        $id = $annuncio->getIdAnnuncio();
        $titolo = $annuncio->getTitolo();
        $content .= "<li>";
        $content .= "<a href=\"dettagli_annuncio.php?id=$id\">" . $titolo . "</a>";
        $content .= "<a href=\"./script_approvazione_annunci.php?idAnnuncio=$id&approvato=1\"  class=\"link_gestione link_approva\">Approva</a>";
        $content .= "<a href=\"./script_approvazione_annunci.php?idAnnuncio=$id&approvato=2\" class=\"link_gestione link_rigetta\">Non approva</a></li>";
    }
    $content .= "</ul>";
    $pagina = str_replace("<Flag1/>", $content, $pagina);
    $pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);
    
    echo $pagina;
    
} catch (Eccezione $ex) {
    echo "eccezione " . $ex->getMessage();
}
