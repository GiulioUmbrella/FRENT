<?php
require_once "./CheckMethods.php";
require_once "class_Annuncio.php";
require_once "class_Prenotazione.php";
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
$prenotazione = Prenotazione::build();
if (isset($_POST["conferma_prenotazione"])) {
    $id_prenotazione=-1;
    try {
        
        if (isset($_POST["dataInizio"]) and checkIsValidDate($_POST["dataInizio"]) and
            isset($_POST["dataFine"]) and checkIsValidDate($_POST["dataFine"]) and
            isset($_POST["numOspiti"]) and is_int(intval($_POST["numOspiti"])) and intval($_POST["numOspiti"]) > 0 and
            intval($_POST["numOspiti"]) <= $_SESSION["annuncio"]->getMaxOspiti() and
            checkDateBeginAndEnd($_POST["dataInizio"], $_POST["dataFine"])
        ) {
            
            $_SESSION["dataFine"] = $_POST["dataFine"];
            $_SESSION["dataInizio"] = $_POST["dataInizio"];
            $_SESSION["numOspiti"] = $_POST["numOspiti"];
            $_SESSION["prenotazione"] = $prenotazione;
            $prenotazione->setIdAnnuncio($_SESSION["annuncio"]->getIdAnnuncio());
            $prenotazione->setNumOspiti(intval($_POST["numOspiti"]));
            $prenotazione->setDataInizio($_POST["dataInizio"]);
            $prenotazione->setDataFine($_POST["dataFine"]);
            $prenotazione->setIdUtente($_SESSION["user"]->getIdUtente());
            $id_prenotazione = $frent->insertPrenotazione($prenotazione);
        } else {
            
            $_SESSION["dati_errati"] = "true";
            
            if (isset($_POST["dataInizio"]) and !checkIsValidDate($_POST["dataInizio"])) {
                $_SESSION["msg"] = "Data inizio non valida";
                
            } else if (isset($_POST["dataFine"]) and !checkIsValidDate($_POST["dataFine"])) {
                $_SESSION["msg"] = "Data Fine non valida";
                
            } else if (!(isset($_POST["numOspiti"]) and is_int(intval($_POST["numOspiti"])) and intval($_POST["numOspiti"]) > 0 and
                intval($_POST["numOspiti"]) <= $_SESSION["annuncio"]->getMaxOspiti())) {
                $_SESSION["msg"] = "Numero dei ospiti non valido!";
            } else if (!checkDateBeginAndEnd($_POST["dataInizio"], $_POST["dataFine"])) {
                $_SESSION["msg"] = "Le date non sono valide!";
            }
            
        }
        
    
    } catch (Eccezione $ex) {
        
        $_SESSION["dati_errati"] = true;
        $_SESSION["msg"] = $ex->getMessage();
        
    }
    if ($id_prenotazione != -1 and $id_prenotazione!=-2 and $id_prenotazione!=-3){
        header("Location: ./riepilogo_prenotazione.php?id=$id_prenotazione");
    }else{
        switch ($id_prenotazione) {
            case -1:
                $_SESSION["msg"] = htmlentities("la data di fine è antecedente alla data d'inizio!");
                break;
            case -2:
                $_SESSION["msg"] = htmlentities("Le date scelte non sono disponibili!");
                break;
            case -3:
                $_SESSION["msg"] = htmlentities("Errore sconosciuto, inserimento fallito!");
                break;
            default:
        }
        $_SESSION["dati_errati"]= true;
        $destinazione="Location: ./dettagli_annuncio.php?";
        if (isset($_SESSION["id"]))
            $destinazione.="id=" . $_SESSION["id"];
        if (isset($_POST["dataInizio"]))
            $destinazione.="&dataInizio=" . $_POST["dataInizio"];
        if (isset($_POST["dataFine"]))
            $destinazione.="&dataFine=" . $_POST["dataFine"];
        if (isset($_POST["numOspiti"]))
            $destinazione.="&numOspiti=" . $_POST["numOspiti"];
        header($destinazione);
    
    }
    
    
}
