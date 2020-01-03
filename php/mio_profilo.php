<?php

require_once "./load_Frent.php";

if(!isset($_SESSION["user"])) {
    header("Location: login.php");
}

$pagina = file_get_contents("./components/mio_profilo_visualizza.html");

require_once "./load_header.php";
//$pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
$pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);

$user = $_SESSION["user"];

// inserisco dati dell'utente nella pagina
$pagina = str_replace("<PATH/>", "../uploads/" . $user->getImgProfilo(), $pagina);
$pagina = str_replace("<NOME/>", $user->getNome(), $pagina);
$pagina = str_replace("<USERNAME/>", $user->getUsername(), $pagina);
$pagina = str_replace("<MAIL/>", $user->getMail(), $pagina);

if(isset($_SESSION["delete_user_message"])) {
    $pagina = str_replace("<DELETE_USER_MESSAGE/>", "<p>" . $_SESSION["delete_user_message"] . "</p>", $pagina);
    unset($_SESSION["delete_user_message"]);
} else {
    $pagina = str_replace("<DELETE_USER_MESSAGE/>", "", $pagina);
}

echo $pagina;