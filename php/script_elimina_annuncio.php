<?php
require_once "load_Frent.php";

if (isset($_SESSION["user"])){
    
    if ($_GET["id"]){
        $idAnnuncio = intval($_GET["id"]);
        if ($_SESSION["user"]->getIdUtente()!=$frent->getAnnuncio($idAnnuncio)->getIdHost())
            header("Location: ./error_page.php");
        
        $res_code = $frent->deleteAnnuncio($idAnnuncio);
        
        header("Location: ./miei_annunci.php");
    }
}
else{
    header("Location: ./login.php");
}
