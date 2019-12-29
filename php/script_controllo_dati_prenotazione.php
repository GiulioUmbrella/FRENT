<?php
require_once "./CheckMethods.php";
require_once "class_Annuncio.php";
require_once "class_Occupazione.php";
require_once "class_Utente.php";
require_once "class_Eccezione.php";
require_once "./load_Frent.php";

if (!isset($_SESSION["user"])) {
    header("Location: ./login.php");
}
if (!isset($_SESSION["annuncio"])) {
    header("Location: ./index.php");
}
$pagina = file_get_contents("./components/conferma_prenotazione.html");
$occupazione = Occupazione::build();
if (isset($_POST["conferma_prenotazione"])) {
    try {
        
        if (isset($_POST["dataInizio"]) and checkIsValidDate($_POST["dataInizio"]) and
            isset($_POST["dataFine"]) and checkIsValidDate($_POST["dataFine"]) and
            isset($_POST["numOspiti"]) and is_int(intval($_POST["numOspiti"])) and intval($_POST["numOspiti"]) > 0 and
            intval($_POST["numOspiti"]) <= $_SESSION["annuncio"]->getMaxOspiti() and
            checkDateBeginAndEnd($_POST["dataInizio"], $_POST["dataFine"])
        ) {
            echo "dati ok!";
            
            $_SESSION["dataFine"] = $_POST["dataFine"];
            $_SESSION["dataInizio"] = $_POST["dataInizio"];
            $_SESSION["numOspiti"] = $_POST["numOspiti"];
            $_SESSION["occupazione"] = $occupazione;
            
            
            $occupazioni = $frent->getOccupazioniAnnuncio($_SESSION["annuncio"]->getIdAnnuncio());
            echo "Comincio controllo";
            $occupazioneIsFit = true;
            if (count($occupazioni) !=0 ){
                echo "Trovati altri annunci";
                if ($_SESSION["dataFine"] < $occupazioni[0]->getDataInizio()) {
                    $occupazioneIsFit = false;
                }
                for ($i = 0; $i < count($occupazioni) - 1 and $occupazioneIsFit; $i++) {
                    echo "$i <br/>";
                    if (!(strtotime($occupazioni[$i]->getDataFine()) < strtotime($_SESSION["dataInizio"]) and
                        strtotime($_SESSION["dataFine"]) < strtotime($occupazioni[$i + 1]->getDataInizio()))) {
                        $occupazioneIsFit = false;
                        echo "confronto in corso";
                    }
                }
                if ($occupazioneIsFit and $occupazioni[count($occupazioni) - 1]->getDataFine() < $_SESSION["dataInizio"])
                    $occupazioneIsFit = false;
            }
    
            if ($occupazioneIsFit) {
                $_SESSION["dati_errati"] = "false";
                $occupazione->setIdAnnuncio($_SESSION["annuncio"]->getIdAnnuncio());
                $occupazione->setNumOspiti(intval($_POST["numOspiti"]));
//                $occupazione->setDataInizio($_POST["dataInizio"]);
                $occupazione->setDataInizio($_POST["dataInizio"]);
                $occupazione->setDataFine($_POST["dataFine"]);
//                $occupazione->setDataFine($_POST["dataFine"]);
//                $occupazione->setPrenotazioneGuest(true);
                $occupazione->setIdUtente($_SESSION["user"]->getIdUtente());
                $_SESSION["prenotazione"] = $occupazione;
                header("Location: ./conferma_prenotazione.php");
            } else {
            
//                echo "Date scelte non disponibili!";
                throw new Eccezione("Date scelte non sono disponibili!");
            }
            
        } else {
            
                $_SESSION["dati_errati"] = "true";
//                $_SESSION["msg"] = $ex->getMessage();
//
            if(isset($_POST["dataInizio"]) and !checkIsValidDate($_POST["dataInizio"])){
                $_SESSION["msg"]="Data inizio non valida";
    
            }else if (isset($_POST["dataFine"]) and !checkIsValidDate($_POST["dataFine"])){
                $_SESSION["msg"]="Data Fine non valida";
    
            }else if(!(isset($_POST["numOspiti"]) and is_int(intval($_POST["numOspiti"])) and intval($_POST["numOspiti"]) > 0 and
            intval($_POST["numOspiti"]) <= $_SESSION["annuncio"]->getMaxOspiti())){
                $_SESSION["msg"]="Numero dei ospiti non valido!";
            }
            else if ( !checkDateBeginAndEnd($_POST["dataInizio"], $_POST["dataFine"])){
                $_SESSION["msg"]="Le date non sono valide!";
            }
    
    
            header("Location: ./dettagli_annuncio.php?id=" . $_SESSION["id"] . "&dataInizio=" . $_POST["dataInizio"]
                . "&dataFine=" . $_POST["dataFine"] . "&numOspiti=" . $_POST["numOspiti"]);
    //                echo $ex->getMessage();
//                throw $ex;
        }
    } catch (Eccezione $ex) {
        
        $_SESSION["dati_errati"] = "true";
        $_SESSION["msg"] = $ex->getMessage();
        
        header("Location: ./dettagli_annuncio.php?id=" . $_SESSION["id"] . "&dataInizio=" . $_POST["dataInizio"]
            . "&dataFine=" . $_POST["dataFine"] . "&numOspiti=" . $_POST["numOspiti"]);
    }
    
}
