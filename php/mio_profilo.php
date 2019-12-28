<?php

require_once "./load_Frent.php";

if(!isset($_SESSION["user"])) {
    header("Location: login.php");
}

$pagina = file_get_contents("./components/mio_profilo_visualizza.html");
$pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
$pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);

$user = $_SESSION["user"];

// inserisco dati dell'utente nella pagina
$pagina = str_replace("<PATH/>", "../uploads/" . $user->getImgProfilo(), $pagina);
$pagina = str_replace("<NOME/>", $user->getNome() . " " . $user->getCognome(), $pagina);
$pagina = str_replace("<USERNAME/>", $user->getUsername(), $pagina);
$pagina = str_replace("<MAIL/>", $user->getMail(), $pagina);

echo $pagina;