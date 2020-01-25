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
            $punteggio= $punteggio/$numeroRecensione;
        }
        $stato="Visualizzato, Approvato"; // VA - VISUALIZZATO APPROVATO
        if ($annuncio->getStatoApprovazione()==0){
            $stato="Non Visualizzato, Non Approvato"; // NON VISUALIZZATO - NON APPROVATO
        }else if ($annuncio->getStatoApprovazione()==2){
            $stato="Visualizzato, Non Approvato";
        }
        
        $item = file_get_contents("./components/item_mio_annuncio.html");
        $item = str_replace("<TITOLO/>",$annuncio->getTitolo(),$item);
        $item = str_replace("<ID/>",$annuncio->getIdAnnuncio(),$item);
        $item = str_replace("<PUNTEGGIO/>",$punteggio,$item);
        $item = str_replace("<PATH/>",uploadsFolder().$annuncio->getImgAnteprima(),$item);
        $item = str_replace("<DESC/>",$annuncio->getDescrizione(),$item);
        $item = str_replace("<ANTEPRIMADESC/>",$annuncio->getDescAnteprima(),$item);
        $item = str_replace("<STATO/>", $stato,$item);
        $content.= $item;
    }
    $pagina= str_replace("<LISTAANNUNCI/>",$content,$pagina);
    echo $pagina;
} else {
    header("Location: login.php");
}
require_once "class_CredenzialiDB.php";
