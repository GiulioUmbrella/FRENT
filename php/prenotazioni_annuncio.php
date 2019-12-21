<?php
require_once "class_Frent.php";
require_once "class_Utente.php";
require_once "class_CredenzialiDB.php";

session_start();
if (isset($_SESSION["user"])) {
    $pagina = file_get_contents("./components/prenotazioni_annuncio.html");
    $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
    $pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);
    
    $user = $_SESSION["user"];
    $manager = new Frent(new Database(CredenzialiDB::DB_ADDRESS, CredenzialiDB::DB_USER,
        CredenzialiDB::DB_PASSWORD, CredenzialiDB::DB_NAME), $user);
    
    
    echo $pagina;
} else {
    header("Location: login.php");
}
