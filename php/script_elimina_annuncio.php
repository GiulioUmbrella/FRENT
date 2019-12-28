<?php
require_once "load_Frent.php";

if (isset($_SESSION["user"])){
    
    echo "hai eliminato il tuo annuncio!!";
    
    header("Location: ./miei_annunci.php");
}
else{
    header("Location: ./login.php");
}
