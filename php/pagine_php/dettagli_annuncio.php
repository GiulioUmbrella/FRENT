<?php
require_once "../classi/Frent.php";
require_once "../CredenzialiDB.php";
try {
    /*
     * quando annuncio non è stato approvato, fare dei controlli che non può essere visualizzato,
     * il controllo avviene attraverso il controllo dell'auth presente in session, se è admin allora
     * può visualizzare, se è utente o null allora possono visualizzare solo gli annunci nello stato di approvazione= 1
     */
    session_start();
    if (!isset($_SESSION["admin"])){
        $manager =  new Frent(new Database(CredenzialiDB::DB_ADDRESS, CredenzialiDB::DB_USER,
            CredenzialiDB::DB_PASSWORD,CredenzialiDB::DB_NAME));
        $annuncio= $manager->getAnnuncio(intval($_GET["id"]));
        if ($annuncio->getStatoApprovazione() != 1){
            header("Location: 404.php");
        }
    }
    $manager = new Frent(new Database(CredenzialiDB::DB_ADDRESS, CredenzialiDB::DB_USER,
        CredenzialiDB::DB_PASSWORD,CredenzialiDB::DB_NAME)
//        , $_SESSION["admin"]
    );
    $id = intval($_GET["id"]);
    $annuncio = $manager->getAnnuncio($id);
    $prezzoAnnuncio = $annuncio->getPrezzoNotte();
    $ospitiMassimo= $annuncio->getMaxOspiti();
//    $foto = $manager->getFotoAnnuncio($id);
    $pagina = file_get_contents("../components/dettagli_annuncio.html");
    
    if (isset($_SESSION["user"])){
        $pagina = str_replace("<HEADER/>",file_get_contents("../components/header_logged.html"),$pagina);
    }else if (isset($_SESSION["admin"])){
        $pagina = str_replace("<HEADER/>",file_get_contents("../components/header_admin_logged.html"),$pagina);
    }else{
        $pagina = str_replace("<HEADER/>",file_get_contents("../components/header_no_logged.html"),$pagina);
    
    }
    $pagina = str_replace("<TITOLO_ANNUNCIO/>", $annuncio->getTitolo(), $pagina);
//    $pagina = str_replace("<TITOLO_ANNUNCIO/>", $annuncio->getTitolo(), $pagina);

//    $totale = 0;
//    $str_foto = "";
//    foreach ($foto as $f) {
//
//
//        $str_commenti .= "";
//
//    }
//    //
    
    $str_commenti = "";
    try {
        $mediaCommenti = 0;
        $commenti = $manager->getCommentiAnnuncio($id);
        //$commenti = array();
        $str_commenti .= "<ul>";
        $totale= 0;
        foreach ($commenti as $commento) {
            $totale += intval($commento->getVotazione());
            $immagine_profilo = "";
            $testo_commento = $commento->getCommento();
            $votazione = $commento->getVotazione();
            $user_name = "";
            $data_commento = $commento->getDataPubblicazione();
            $titolo_commento = $commento->getTitolo();
            $str_commenti .= "<li><div class=\"intestazione_commento\"><img src=\"$immagine_profilo\" alt=\"\"/>
                <div><p class=\"username_commento\">$user_name</p><p class=\"data_commento\">$data_commento</p>
                </div><p class=\"totale_commenti_utente\">Numero Commenti Totali</p>
                </div><div class=\"corpo_commento\"><h1>$titolo_commento</h1><p>Votazione: $votazione</p>
                <p>$testo_commento</p></div></li>";
        }
        $totale = $totale/count($commenti);
        $str_commenti .= "</ul>";
        $pagina = str_replace("<Commenti/>", $str_commenti, $pagina);
        $pagina = str_replace("<Valutazione/>", $mediaCommenti, $pagina);
        $pagina = str_replace("<NUMEROCOMMENTI/>", count($commenti), $pagina);
        $pagina = str_replace("<PREZZO/>", $prezzoAnnuncio , $pagina);
        $pagina = str_replace("<OSPITIMASSIMO/>", $mediaCommenti, $pagina);
        $pagina = str_replace("<Valutazione/>", $mediaCommenti, $pagina);
    }catch(Eccezione $e){
        $pagina = str_replace("<Commenti/>","<p>Ancora non ci sono commenti!</p>", $pagina);

    }
    echo $pagina;
    
} catch (Eccezione $ex) {
    echo "eccezione " . $ex->getMessage();
}
