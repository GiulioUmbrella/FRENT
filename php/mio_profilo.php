<?php

require_once "class_Frent.php";
require_once "class_Utente.php";
require_once "class_CredenzialiDB.php";

session_start();
if (isset($_SESSION["user"])){
    require_once "components/connessione_utente.php";
    $pagina = file_get_contents("./components/mio_profilo_visualizza.html");
    $pagina= str_replace("<HEADER/>",file_get_contents("./components/header_logged.html"),$pagina);
    $pagina= str_replace("<FOOTER/>",file_get_contents("./components/footer.html"),$pagina);
    
    $user= $_SESSION["user"];
    
    $path=$user->getImgProfilo();
    $pagina= str_replace("<PATH/>",$path,$pagina);
    
    $username= $user->getUsername();
    $pagina = str_replace("<USERNAME/>",$username,$pagina);
    
    $mail= $user->getMail();
    $pagina = str_replace("<MAIL/>",$mail,$pagina);
    echo $pagina;
}else{
    header("Location: login.php");
}


