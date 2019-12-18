<?php
require_once "./Frent.class.php";
session_start();
if (isset($_SESSION["user"])) {
    header("Location: index.php");
}

if (!isset($_POST["registrati"])) {
    $pagina = file_get_contents("./components/registrazione.html");
    $GG = "";
    for ($i = 1; $i <= 31; $i++) {
        $GG .= "<option value=\"$i\">$i</option>";
    }
    $pagina = str_replace("<GIORNO/>", $GG, $pagina);
    $GG = "";
    for ($i = 1; $i <= 12; $i++) {
        $GG .= "<option value=\"$i\">$i</option>";
    }
    $pagina = str_replace("<MESE/>", $GG, $pagina);
    $GG = "";
    for ($i = 2019; $i > 1919; $i = $i - 1) {
        $GG .= "<option value=\"($i-i)\">$i</option>";
    }
    $pagina= str_replace("<ANNO/>",$GG,$pagina);
    $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_no_logged.html"), $pagina);
    $pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);
    echo $pagina;
} else {
    $msg="Devi riempire tutti i campi di informazione:\nasdasd";
    if (isset($_POST["nome"]));
    
    echo $msg;
}

