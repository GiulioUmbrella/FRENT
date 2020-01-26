<?php
require_once "CheckMethods.php";
session_start();
$datiOk=true;
if (isset($_GET["dataFine"]) and checkIsValidDate($_GET["dataFine"]) ){
    $dataFine =$_GET["dataFine"];
    echo $dataFine;
    $_SESSION["dataFine"] = $dataFine;
}else{
    
    $_SESSION["datiRicercaMancanti"]="Devi inserire la data di partenza!";
    $datiOk=false;
}
if (isset($_GET["dataInizio"]) and checkIsValidDate($_GET["dataInizio"])){
    $dataInizio =$_GET["dataInizio"];
    $_SESSION["dataInizio"] = $dataInizio;
}else{
    $_SESSION["datiRicercaMancanti"]="Devi inserire la data di arrivo!";
    $datiOk=false;
}
if (isset($_GET["numOspiti"]) and is_int(intval($_GET["numOspiti"]))){
    $numOspiti =intval($_GET["numOspiti"]);
    $_SESSION["numOspiti"]= $numOspiti;
}else{
    $_SESSION["datiRicercaMancanti"]="Devi inserire il numero degli ospiti!";
    $datiOk=false;
}
if (isset($_GET["citta"]) and  $_GET["citta"] != ""){
    $citta =$_GET["citta"];
    echo $citta;
    $_SESSION["citta"]= $citta;
}else{
    $_SESSION["datiRicercaMancanti"]="Devi scegliere la citt&agrave;";
    $datiOk=false;
}

if ($datiOk){
    header("Location: ./risultati.php?citta=".$_SESSION["citta"]."&dataInizio="
        .$_SESSION["dataInizio"]."&dataFine=".$_SESSION["dataFine"]."&numOspiti=".$_SESSION["numOspiti"]);
}else{
    
    header("Location: ./index.php");
}
