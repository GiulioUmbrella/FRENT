<?php
// funziona e valida.
$pagina = file_get_contents("./components/riepilogo_prenotazione.html");
require_once "load_Frent.php";
if (isset($_SESSION["user"])) {
    try {
        $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
        $pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);
//    todo sistemare il modo per arrivare al riepilogo prenotazione
        
        
        if (isset($_GET["id"])) {
            $id_prenotazione = intval($_GET["id"]);
            if ($frent->getOccupazione($id_prenotazione)->getIdUtente() != $_SESSION["user"]->getIdUtente()) {
                header("Location: ./404.php");
            }
            $prenotazioni = $frent->getOccupazione(intval($id_prenotazione));
            $annuncio = $frent->getAnnuncio($prenotazioni->getIdAnnuncio());
            $host = $frent->getUser($annuncio->getIdHost());
            $durata = abs(strtotime($prenotazioni->getDataFine()) - strtotime($prenotazioni->getDataInizio())) / (3600 * 24);
            $id = $annuncio->getIdAnnuncio();
            $titolo = $annuncio->getTitolo();
            $totale = $durata * $annuncio->getPrezzoNotte() * $prenotazioni->getNumOspiti();
            $pagina = str_replace("<IDPRENOTAZIONE/>", $prenotazioni->getIdOccupazione(), $pagina);
            $pagina = str_replace("<DATAINIZIO/>", $prenotazioni->getDataInizio(), $pagina);
            $pagina = str_replace("<DATAFINE/>", $prenotazioni->getDataFine(), $pagina);
            $pagina = str_replace("<MAILPROPRIETARIO/>", $host->getMail(), $pagina);
            $pagina = str_replace("<NUMOSPITI/>", $prenotazioni->getNumOspiti(), $pagina);
            $pagina = str_replace("<NOMEANNUNCIO/>", "<a href=\"./dettagli_annuncio.php?id=$id\" title=\"Visualizza altre informazioni dell\'annuncio $titolo\">$titolo</a>", $pagina);
            $pagina = str_replace("<INDIRIZZO/>", $annuncio->getIndirizzo(), $pagina);
            $pagina = str_replace("<CITTA/>", $annuncio->getCitta(), $pagina);
            $pagina = str_replace("<PROPRIETARIO/>", $host->getUserName(), $pagina);
            $pagina = str_replace("<PREZZO/>", $totale, $pagina);

            $commentiAnnuncio = $frent->getCommentiAnnuncio($prenotazioni->getIdAnnuncio());
            // ricerca del commento fatto dall'utente
            $commentoUtente = Commento::build();
            $commentoTrovato = false;
            $i = 0; $l = count($commentiAnnuncio);
            while($i < $l && !$commentoTrovato) {
                if($commentiAnnuncio[$i]->getIdPrenotazione() !== $id_prenotazione) {
                    $i++;
                } else {
                    $commentoTrovato = true;
                    $commentoUtente = $commentiAnnuncio[$i];
                }
            }

            $content = "";
            if($commentoTrovato) {
                $content = "<ul class=\"riepilogo_annucio\">";
                $content .= "<li>Titolo: " . $commentoUtente->getTitolo() . "</li>";
                $content .= "<li>Valutazione: " . $commentoUtente->getValutazione() . "</li>";
                $content .= "<li>Commento: " . $commentoUtente->getCommento() . "</li>";
                $content .= "</ul>";
            } else {
		$content = "";
		if ($prenotazioni->getDataFine() < date("Y-m-d")) {
		    $content = file_get_contents("./components/aggiungi_commento_form.html");
		}
		else {
		    $content = "<p>Non &egrave; ancora possibile commentare questa prenotazione.</p>";
		}
            }

            $pagina = str_replace("<COMMENTO/>", $content, $pagina);
            
            echo $pagina;
        } else {
            header("Location: ./404.php");
        }
    } catch (Eccezione $ex) {
        $_SESSION["msg"] = "Non hai il permesso di accedere a questa pagina!";
        header("Location: ./error_page.php");
    }
    
} else {
    header("Location: ./login.php");
}
