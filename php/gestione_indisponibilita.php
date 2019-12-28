<?php
require_once "load_Frent.php";
require_once "class_Frent.php";

require_once "./class_CredenzialiDB.php";
$pagina = file_get_contents("./components/gestione_indisponibilita.html");
if (isset($_SESSION["user"])) {
    if (isset($_SESSION["id_annuncio"])) {
        $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
        $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_admin_logged.html"), $pagina);
        $occupazioni = $frent->getOccupazioniAnnuncio($_SESSION["id_annuncio"]);
        $annuncio = $frent->getAnnuncio($_SESSION["id_annuncio"]);
        $pagina = str_replace("<IDANNUNCIO/>",$_SESSION["id_annuncio"], $pagina);
        $content = "";
        $pagina = str_replace("<TITOLO/>",$annuncio->getTitolo(),$pagina);
        foreach ($occupazioni as $occupazione) {
//            if (!$occupazione->getPrenotazioneGuest()){
            $ID=$occupazione->getIdOccupazione();
            $dataInizio=$occupazione->getDataInizio();
            $dataFine= $occupazione->getDataFine();
            
                $content .= "<form class=\"form form_orizzontale\" method=\"post\" action=\"./script_gestione_occupazione.php\">
                <fieldset>
                    <input type='hidden' value='$ID' name='id_occupazione' id='id_occupazione'>
                    <legend class=\"aiuti_alla_navigazione\">Modifica indisponibilit&agrave;</legend>
                    <div class=\"input_label_orizzontale\">
                        <label for=\"data_inizio_$ID\">Data inizio</label>
                        <input id=\"data_inizio_$ID\" name=\"data_inizio\" type=\"date\" value=\"$dataInizio\">
                    </div>
                    <div class=\"input_label_orizzontale\">
                        <label for=\"data_fine_$ID\">Data fine</label>
                        <input id=\"data_fine_$ID\" name=\"data_fine\" type=\"date\" value=\"$dataFine\"/>
                    </div>
                    <div class=\"input_label_orizzontale\">
                        <label class=\"aiuti_alla_navigazione\" for=\"rimuovi_button_$ID\">Rimuovi
                            indisponibilit&agrave;</label>
                        <input id=\"rimuovi_button_$ID\" name=\"rimuovi_button_$ID\" type=\"submit\"
                               value=\"Elimina\"/>
                    </div>
                </fieldset>
            </form>";
//            }
        }
        $pagina= str_replace("<OCCUPAZIONI/>", $content,$pagina);
        
        echo $pagina;
    }else{
        header("Location: ./404.php");
    }
} else {
    header("Location: login.php");
}


