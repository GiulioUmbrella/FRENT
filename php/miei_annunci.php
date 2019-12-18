<?php
//todo nell'elenco visualizzare lo stato di approvazione degli annunci!
require_once "Database.php";
require_once "Frent.php";
require_once "CredenzialiDB.php";
$pagina = file_get_contents("./components/miei_annunci.html");
session_start();
if (isset($_SESSION["user"])) {
    $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
    $pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);
    $user = $_SESSION["user"];
    $frent = new Frent(new Database(CredenzialiDB::DB_ADDRESS, CredenzialiDB::DB_USER,
        CredenzialiDB::DB_PASSWORD, CredenzialiDB::DB_NAME), $user);
    
    $annunci =$frent->getAnnunciHost();
    $content="";
    foreach ($annunci as $annuncio) {
        $id = $annuncio->getIdAnnuncio();
        $Titolo = $annuncio->getTitolo();
        $descrizione=$annuncio->getDescrizione();
        $prezzoTotale=$annuncio->getPrezzoNotte();
        $path= $annuncio->getImgAnteprima();
        $recensioni=$frent->getCommentiAnnuncio($annuncio->getIdAnnuncio());
        $numeroRecensione= count($recensioni);
        $punteggio=0;
        if ($numeroRecensione!=0){
            foreach ($recensioni as $recensione)
                $punteggio=$recensione->getValutazione()+$punteggio;
            $punteggio=$punteggio/$numeroRecensione;
        }
//        $content .= "<li><div class=\"intestazione_lista\">
//      <a href=\"dettagli_annuncio.php?id=$id\"  tabindex=\"12\">$Titolo</a>
//      <p>Punteggio:$punteggio - Num Recensioni:$numeroRecensione </p></div><div class=\"corpo_lista\">
//      <img src =\"$path\" alt=\"Foto copertina della casa\" /><div class=\"descrizione_annuncio\">
//      <p>$descrizione</p><p class=\"prezzototale\">Prezzo: $prezzoTotale&euro; persona/notte</p></div></div></li>";
//
//
        $content.= " <li><div class=\"intestazione_lista\">
                <a href=\"\" tabindex=\"12\">$Titolo</a>
                <p>Punteggio: $numeroRecensione: </p></div><div class=\"corpo_lista\"><img src=\"$path\" alt=\"Foto copertina della casa\"/>
                <div><p>$descrizione</p><a class=\"link_gestisci_annuncio\" href=\"./gestione_indisponibilita.php\" tabindex=\"13\" title=\"Vai alla gestione dell'annuncio\">Gestisci</a>
                </div></div></li>";
    }
    $pagina= str_replace("<LISTAANNUNCI/>",$content,$pagina);
    echo $pagina;
} else {
    header("Location: login.php");
}