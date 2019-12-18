<?php
require_once "./class_CredenzialiDB.php";
/* todo quando l'annuncio non approvato è visualizzato dal proprietario, il pulsante prenota deve sparire, e deve
    essere visualizza il pulsante modifica, reindirizzandolo verso la pagina della modifica dell'annunciom
    e quindi sparisce anche i due form di data.

*/

require_once "./Frent.php";
require_once "./CredenzialiDB.php";
try {
    /*
     * quando annuncio non è stato approvato, fare dei controlli che non può essere visualizzato,
     * il controllo avviene attraverso il controllo dell'auth presente in session, se è admin allora
     * può visualizzare, se è utente o null allora possono visualizzare solo gli annunci nello stato di approvazione= 1
     */
    session_start();
    $manager = new Frent(new Database(CredenzialiDB::DB_ADDRESS, CredenzialiDB::DB_USER,
        CredenzialiDB::DB_PASSWORD, CredenzialiDB::DB_NAME));
    if (!isset($_SESSION["admin"])) {
    
        if (!isset($_GET["id"])) {
            header("Location: ./404.php");
        }
        $annuncio = $manager->getAnnuncio(intval($_GET["id"]));
        if ($annuncio->getStatoApprovazione() != 1) {
            header("Location: ./404.php");
        }
    }
    
    $id = intval($_GET["id"]);
    $annuncio = $manager->getAnnuncio($id);
    $prezzoAnnuncio = $annuncio->getPrezzoNotte();
    $ospitiMassimo = $annuncio->getMaxOspiti();
    $foto = $manager->getFotoAnnuncio($id);
    
    $pagina = file_get_contents("./components/dettagli_annuncio.html");
    $pagina = str_replace("<DESCRIZIONE/>", $annuncio->getDescrizione(), $pagina);
    //todo stampar le foto
    
    if (isset($_SESSION["user"])) {
        $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
    } else if (isset($_SESSION["admin"])) {
        $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_admin_logged.html"), $pagina);
    } else {
        $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_no_logged.html"), $pagina);
        
    }
    $pagina = str_replace("<TITOLO_ANNUNCIO/>", $annuncio->getTitolo(), $pagina);
    
    if (isset($_GET["dataInizio"])) {
        $dataInizio = $_GET["dataInizio"];
        $pagina = str_replace("<VALUEINIZIO/>", $dataInizio, $pagina);
    } else {
        $pagina = str_replace("<VALUEINIZIO/>", "", $pagina);
    }
    if (isset($_GET["dataFine"])) {
        $dataFine = $_GET["dataFine"];
        $pagina = str_replace("<VALUEFINE/>", $dataFine, $pagina);
    } else {
        $pagina = str_replace("<VALUEFINE/>", "", $pagina);
    }
    
    $str_commenti = "";
    try {
        $commenti = $manager->getCommentiAnnuncio($id);
        $mediaCommenti = 0;
        $str_commenti .= "<ul>";
        $totale = 0;
        foreach ($commenti as $commento) {
            $totale += intval($commento->getValutazione());
            $immagine_profilo = "";
            $testo_commento = $commento->getCommento();
            $votazione = $commento->getValutazione();
            $user_name = "";
            $data_commento = $commento->getDataPubblicazione();
            $titolo_commento = $commento->getTitolo();
            $str_commenti .= "<li><div class=\"intestazione_commento\"><img src=\"$immagine_profilo\" alt=\"\"/>
                <div><p class=\"username_commento\">$user_name</p><p class=\"data_commento\">$data_commento</p>
                </div><p class=\"totale_commenti_utente\">Numero Commenti Totali</p>
                </div><div class=\"corpo_commento\"><h1>$titolo_commento</h1><p>Votazione: $votazione</p>
                <p>$testo_commento</p></div></li>";
        }
//        $totale = $totale/count($commenti);
        $str_commenti .= "</ul>";
        $pagina = str_replace("<Commenti/>", $str_commenti, $pagina);
        $pagina = str_replace("<Valutazione/>", $mediaCommenti, $pagina);
        $pagina = str_replace("<NUMEROCOMMENTI/>", count($commenti), $pagina);
        $pagina = str_replace("<PREZZO/>", $prezzoAnnuncio, $pagina);
        $pagina = str_replace("<OSPITIMASSIMO/>", $mediaCommenti, $pagina);
        $pagina = str_replace("<Valutazione/>", $mediaCommenti, $pagina);
        
    } catch (Eccezione $e) {
        $pagina = str_replace("<Commenti/>", "<p>Ancora non ci sono commenti!</p>", $pagina);
    }
    
    $img = $annuncio->getImgAnteprima();
//    $img="../../immagini/borgoricco.jpg";
    $photos = $manager->getFotoAnnuncio($annuncio->getIdAnnuncio());
    
    $content = "<div class=\"shower_immagine_anteprima\">";
    if (count($photos) != 0) {
        $content .= "<button id=\"immagine_precedente\" class=\"pulsanti_navigazione_immagini\" onclick=\"\">&lt;</button>
        <img id=\"immagine_anteprima\" class=\"immagine_anteprima\" src=\"$img\" alt=\"Descrizione immagine\"/>
        <button id=\"immagine_successiva\" class=\"pulsanti_navigazione_immagini\" onclick=\"\">&gt;</button></div><div class=\"image_picker\">";;
        foreach ($photos as $foto) {
            $path = $foto->getFilePath();
            $content .= "<img class=\"immagine\" alt=\"Descrizione immagine\" src=\"$path\"/>";
            
        }
    } else {
        $content .= "<img id=\"immagine_anteprima\" class=\"immagine_anteprima\" src=\"$img\" alt=\"Descrizione immagine\"/>";
    }
    
    
    $content .= "</div>";
    
    
    $pagina = str_replace("<IMMAGINE/>", $content, $pagina);
    echo $pagina;
    
} catch (Eccezione $ex) {
    echo "eccezione " . $ex->getMessage();
}
