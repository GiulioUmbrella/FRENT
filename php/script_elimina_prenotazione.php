<?php
require_once "load_Frent.php";

if (isset($_GET["id"])){
    echo "trovato id =".$_GET["id"];
    $frent->deleteOccupazione(intval($_GET["id"]));
    header("Location: ./mie_prenotazioni.php");
}
//header("Location: ./mie_prenotazioni.php");