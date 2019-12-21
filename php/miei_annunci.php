<?php
//todo nell'elenco visualizzare lo stato di approvazione degli annunci!
require_once "class_Database.php";
require_once "class_Frent.php";
require_once "class_CredenzialiDB.php";
$pagina = file_get_contents("./components/miei_annunci.html");
session_start();
if (isset($_SESSION["user"])) {
    $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
    $pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);
    $user = $_SESSION["user"];
    require_once "components/connessione_utente.php";
    $annunci =$manager->getAnnunciHost();
    $content="";
    foreach ($annunci as $annuncio) {
        $id = $annuncio->getIdAnnuncio();
        $Titolo = $annuncio->getTitolo();
        $descrizione=$annuncio->getDescrizione();
        $prezzoTotale=$annuncio->getPrezzoNotte();
        $path= $annuncio->getImgAnteprima();
        $recensioni=$manager->getCommentiAnnuncio(intval($annuncio->getIdAnnuncio()));
        $numeroRecensione= count($recensioni);
        $punteggio=0;
        if ($numeroRecensione!=0){
            foreach ($recensioni as $recensione)
                $punteggio=$recensione->getValutazione()+$punteggio;
            $punteggio=$punteggio/$numeroRecensione;
        }
        $stato="Approvato";
        if ($annuncio->getStatoApprovazione()==0){
            $stato="NVNA";
        }else if ($annuncio->getStatoApprovazione()==2){
            $stato="VNA";
        }

        $content.= " <li><div class=\"intestazione_lista\">
                <a href=\"dettagli_annuncio.php?id=$id\" tabindex=\"12\">$Titolo</a>
                <p>Stato Approvazione: $stato - Punteggio: $numeroRecensione: </p></div><div class=\"corpo_lista\"><img src=\"$path\" alt=\"Foto copertina della casa\"/>
                <div><p>$descrizione</p><a class=\"link_gestisci_annuncio\" href=\"./gestione_indisponibilita.php\" tabindex=\"13\" title=\"Vai alla gestione dell'annuncio\">Gestisci</a>
                </div></div></li>";
    }
    $pagina= str_replace("<LISTAANNUNCI/>",$content,$pagina);
    echo $pagina;
} else {
    header("Location: login.php");
}