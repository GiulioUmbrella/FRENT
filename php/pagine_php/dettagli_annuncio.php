<?php
require_once "../classi/Frent.php";
require_once "../CredenzialiDB.php";
try {
    session_start();
    $manager = new Frent(new Database(CredenzialiDB::DB_ADDRESS, CredenzialiDB::DB_USER,
        CredenzialiDB::DB_PASSWORD,CredenzialiDB::DB_NAME),
//        $_SESSION["admin"]
    );
    $id = intval($_GET["id"]);
    $annuncio = $manager->getAnnuncio($id);
//    $foto = $manager->getFotoAnnuncio($id);
    $pagina = file_get_contents("../components/dettagli_annuncio.html");
    
    $pagina = str_replace("<HEADER/>",file_get_contents("../components/header_admin_logged.html"),$pagina);
//    $pagina = str_replace("<TITOLO_ANNUNCIO/>", $annuncio->getTitolo(), $pagina);
    $pagina = str_replace("<TITOLO_ANNUNCIO/>", $annuncio->getTitolo(), $pagina);
    //todo calcolare la media dei commenti
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
//    try {
//        $mediaCommenti = 0;
//        $commenti = $manager->getCommentiAnnuncio($id);
//        $commenti = array();
//        $str_commenti .= "<ul>";
//        foreach ($commenti as $commento) {
//            $totale += intval($commenti->getVotazione());
//            $immagine_profilo = "";
//            $testo_commento = $commento->getCommento();
//            $votazione = $commento->getVotazione();
//            $user_name = "";
//            $data_commento = $commento->getDataPubblicazione();
//            $titolo_commento = $commento->getTitolo();
//            $str_commenti .= "<li><div class=\"intestazione_commento\"><img src=\"$immagine_profilo\" alt=\"\"/>
//                <div><p class=\"username_commento\">$user_name</p><p class=\"data_commento\">$data_commento</p>
//                </div><p class=\"totale_commenti_utente\">Numero Commenti Totali</p>
//                </div><div class=\"corpo_commento\"><h1>$titolo_commento</h1><p>Votazione: $votazione</p>
//                <p>$testo_commento</p></div></li>";
//        }
//
//        $str_commenti .= "</ul>";
//        $pagina = str_replace("<Commenti/>", $str_commenti, $pagina);
//        $pagina = str_replace("<Valutazione/>", $mediaCommenti, $pagina);
//    }catch(Eccezione $e){
//        $pagina = str_replace("<Commenti/>","<p>Ancora non ci sono commenti!</p>", $pagina);
//
//    }
    echo $pagina;
    
} catch (Eccezione $ex) {
    echo "eccezione " . $ex->getMessage();
}
