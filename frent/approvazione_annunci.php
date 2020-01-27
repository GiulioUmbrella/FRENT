<?php
require_once "./load_Frent.php";

if (!isset($_SESSION["admin"])) {
    header("Location: ./login_admin.php");
}

$admin = $_SESSION["admin"];
$pagina = file_get_contents("./components/approvazione_annunci.html");
$msg = "Bentornato, " . $admin->getUserName() . "!";
$pagina = str_replace("<SALUTO/>", $msg, $pagina);
$pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);

$content = "";
try {
    $annunci = $frent->adminGetAnnunci();
    $content = "<h2>Ci sono " . count($annunci) . " annunci da controllare</h2>";
    
    // stampa lista annunci da approvare/rifiutare
    $content .= "<ul>";
    foreach ($annunci as $annuncio) {
        $id = $annuncio->getIdAnnuncio();
        $content .= "<li>";
        $content .= "<a href=\"dettagli_annuncio.php?id=$id\">" . $annuncio->getTitolo() . "</a>";
        $content .= "<a href=\"./script_approvazione_annunci.php?idAnnuncio=$id&amp;approvato=1\" class=\"link_gestione link_approva\">Approva</a>";
        $content .= "<a href=\"./script_approvazione_annunci.php?idAnnuncio=$id&amp;approvato=2\" class=\"link_gestione link_rigetta\">Rigetta</a>";
        $content .= "</li>";
    }
    $content .= "</ul>";
} catch (Eccezione $ex) {
    
    $_SESSION["msg"]="<h2>C'&egrave; stato un errore nel reperimento degli annunci da approvare</h2><p>Errore riscontrato: " . $ex->getMessage() . "</p>";
    header("Location: ./error_page.php");
}

$pagina = str_replace("<ANNUNCI_PENDENTI/>", $content, $pagina);
echo $pagina;