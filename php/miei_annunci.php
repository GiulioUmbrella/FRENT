<?php
//todo nell'elenco visualizzare lo stato di approvazione degli annunci!
require_once "./load_Frent.php";
require_once "./components/form_functions.php";

$pagina = file_get_contents("./components/miei_annunci.html");

if (isset($_SESSION["user"])) {
    require_once "./load_header.php";
//    $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
    $pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);
    $user = $_SESSION["user"];
    
    $annunci =$frent->getAnnunciHost();
    $content="";
    foreach ($annunci as $annuncio) {
        $id = $annuncio->getIdAnnuncio();
        $Titolo = $annuncio->getTitolo();
        $descrizione=$annuncio->getDescrizione();
        $prezzoTotale=$annuncio->getPrezzoNotte();
        $path= uploadsFolder() . $annuncio->getImgAnteprima();
        $recensioni=$frent->getCommentiAnnuncio(intval($annuncio->getIdAnnuncio()));
        $numeroRecensione= count($recensioni);
        $punteggio=0;
        if ($numeroRecensione!=0){
            foreach ($recensioni as $recensione)
                $punteggio=intval($recensione->getValutazione())+$punteggio;
            $punteggio=$punteggio/$numeroRecensione;
            $punteggio= $punteggio/$numeroRecensione;
        }
        $stato="Visualizzato, Approvato"; // VA - VISUALIZZATO APPROVATO
        if ($annuncio->getStatoApprovazione()==0){
            $stato="Non Visualizzato, Non Approvato"; // NON VISUALIZZATO - NON APPROVATO
        }else if ($annuncio->getStatoApprovazione()==2){
            $stato="Visualizzato, Non Approvato";
        }
        
        $content.= "
             <li><div class=\"intestazione_lista\">
                <a href=\"dettagli_annuncio.php?id=$id\" >$Titolo</a>
                <p>Stato: $stato - Valutazione media: $punteggio</p>
                </div>
                    <div class=\"corpo_lista\"><img src=\"$path\" alt=\"".$annuncio->getDescAnteprima()."\"/>
                    <div>
                        <p>$descrizione</p>
                        <a class=\"link_gestisci_annuncio\" href=\"./script_salva_dati_modifica_annuncio_in_session.php?id=$id\"
                        title=\"Vai alla gestione dell'annuncio\">Gestione annuncio</a>
                    </div>
                </div>
                </li>";
    }
    $pagina= str_replace("<LISTAANNUNCI/>",$content,$pagina);
    echo $pagina;
} else {
    header("Location: login.php");
}
require_once "class_CredenzialiDB.php";
