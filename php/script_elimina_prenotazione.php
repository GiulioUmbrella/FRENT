<?php
require_once "load_Frent.php";

if (isset($_POST["id"])){
    echo "trovato id =".$_POST["id"];
    $frent->deleteOccupazione(intval($_POST["id"]));
}
//header("Location: ./mie_prenotazioni.php");