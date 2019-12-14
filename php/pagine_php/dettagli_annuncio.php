<?php
require_once "../classi/Frent.php";
require_once "../CredenzialiDB.php";
try {
    session_start();
    $manager = new Frent(new Database(CredenzialiDB::DB_ADDRESS, CredenzialiDB::DB_USER,
        CredenzialiDB::DB_PASSWORD,CredenzialiDB::DB_NAME),$_SESSION["admin"]);
    $id = intval($_GET["id"]);
    $annuncio = $manager->getAnnuncio($id);
//    $foto = $manager->getFotoAnnuncio($id);
    $pagina = file_get_contents("../components/dettagli_annuncio.html");
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
    
    $str_commenti = "<div id=\"commenti\"><h2>Commenti</h2>";
    try {
        $mediaCommenti = 0;
        $commenti = $manager->getCommentiAnnuncio($id);
    
    
        $str_commenti .= "<ul>";
        foreach ($commenti as $commento) {
            $totale += intval($commenti->getVotazione());
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
    
        $pagina .= "</ul>";
        $pagina = str_replace("<Commenti/>", $str_commenti, $pagina);
        $pagina = str_replace("<Valutazione/>", $mediaCommenti, $pagina);
    }catch(Eccezione $e){
        $pagina = str_replace("<Commenti/>","<p>Non ci sono commenti!</p>", $pagina);
    
    }
    echo $pagina;
    
} catch (Eccezione $ex) {
    echo "eccezione " . $ex->getMessage();
}
