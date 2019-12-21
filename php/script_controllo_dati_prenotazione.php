<?php
require_once "./CheckMethods.php";
require_once "class_Annuncio.php";
require_once "class_Occupazione.php";
require_once "class_Utente.php";
require_once "class_Eccezione.php";

session_start();
if (!isset($_SESSION["user"])) {
    header("Location: ./login.php");
}
if (!isset($_SESSION["annuncio"])) {
    header("Location: ./index.php");
}
$pagina = file_get_contents("./components/conferma_prenotazione.html");
$occupazione = Occupazione::build();
if (isset($_POST["conferma_prenotazione"])) {

    if (isset($_POST["dataInizio"]) and checkIsValidDate($_POST["dataInizio"]) and
        isset($_POST["dataFine"]) and checkIsValidDate($_POST["dataFine"]) and
        isset($_POST["numOspiti"]) and is_int(intval($_POST["numOspiti"])) and intval($_POST["numOspiti"]) > 0 and
        intval($_POST["numOspiti"]) <= $_SESSION["annuncio"]->getMaxOspiti() and
        checkDateBeginAndEnd($_POST["dataInizio"],$_POST["dataFine"])
    ) {
        $occupazione->setIdAnnuncio($_SESSION["annuncio"]->getIdAnnuncio());
        $occupazione->setNumOspiti(intval($_POST["numOspiti"]));
        $occupazione->setDataInizio($_POST["dataInizio"]);
        $occupazione->setDataFine($_POST["dataFine"]);
        $occupazione->setPrenotazioneGuest(true);
        $occupazione->setIdUtente($_SESSION["user"]->getIdUtente());
        $_SESSION["dataFine"]= $_POST["dataFine"];
        $_SESSION["dataInizio"]= $_POST["dataInizio"];
        $_SESSION["numOspiti"]= $_POST["numOspiti"];
        
        require_once "components/connessione_utente.php";
        $_SESSION["occupazione"]= $occupazione;
        header("Location: ./conferma_prenotazione.php");


    } else {
        $_SESSION["dati_errati"] = "true";
        try {
                $occupazione = Occupazione::build();
            $occupazione->setNumOspiti(intval($_POST["numOspiti"]));
            $occupazione->setDataInizio($_POST["dataInizio"]);
            $occupazione->setDataFine($_POST["dataInizio"]);
        }catch (Eccezione $ex){
            $_SESSION["msg"] = $ex->getMessage();
        }
        header("Location: ./dettagli_annuncio.php?id=" . $_SESSION["id"] . "&dataInizio=" . $_POST["dataInizio"]
            . "&dataFine=" . $_POST["dataFine"] . "&numOspiti=" . $_POST["numOspiti"]);
    }
    
    
}
