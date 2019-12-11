<?php
require_once "../classi/Annuncio.php";
require_once "../classi/Frent.php";
require_once "../classi/Database.php";

$connessione = new Database("localhost", "root", "", "frentdb");
try {
    session_start();
    $manager = new Frent($connessione,$_SESSION["admin_obj"]);
    echo "p1";
    $annunci = $manager->adminGetAnnunci();
    echo "p2";
    
    $pagina = file_get_contents("../../html/approvazione_annunci.html");
    $content = "<div id=\"content\" class=\"list_annunci_pendenti\"><h1 >Approvazione annunci</h1>
    <h2>Ci sono " . count($annunci) . " annunci da controllare: </h2> <ul>";
    echo "p3";
    
    foreach ($annunci as $annuncio) {
        $id = $annuncio->getIdAnnuncio();
        $content .= "<li><a href=";
        $content .= "\"dettagli_annuncio.html?id=$id\">ID Titolo annuncio</a> <a href=\"dettagli_annunci.php?idAnnuncio=$id&approvato=true\"";
        $content .= "class=\"link_gestione link_approva\">Approva</a> <a href='?idAnnuncio=$id&approvato=true' ";
        
        $content .= 'class=\"link_gestione link_rigetta\">Non approva</a></li>';
    }
    
    $content .= "  </ul></div>";
    
    $pagina = str_replace("<Flag1/>", $content, $pagina);
    echo $pagina;
    
} catch (Eccezione $ex) {
  
    echo "eccezione";
}
