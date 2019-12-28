<?php
require_once "load_Frent.php";
require_once "CheckMethods.php";

if (isset($_SESSION["user"]) and $_SESSION["user"]->getIdUtente()==$_SESSION["annuncio"]->getIdHost()){
    $res=0;
    if (isset($_POST["id_occupazione"])){
       if (isset($_POST["rimuovi_button_".$_POST["id_occupazione"]]) and
           $_POST["rimuovi_button_".$_POST["id_occupazione"]]=="Elimina"){
            if (is_int(intval($_POST["id_occupazione"]))){
                $res =$frent->deleteOccupazione(intval($_POST["id_occupazione"]));
                
            }
        }
    }
    if (isset($_POST["aggiungi"]) and $_POST["aggiungi"]=="Aggiungi"){
        if (isset($_POST["dataInizio"]) and isset($_POST["dataFine"]) and isset($_POST["id_annuncio"])
            and checkDateBeginAndEnd($_POST["dataInizio"],$_POST["dataFine"])){
            $res = $frent->insertOccupazione(intval($_POST["id_annuncio"]),1,$_POST["dataInizio"],$_POST["dataFine"]);
            echo $res;
//            if ($res=="1")
        }
        
    }
  
//    header("Location: ./gestione_indisponibilita.php");
}else{
    header("Location: ./404/php");
}