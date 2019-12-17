<?php
require_once "../classi/Database.php";
require_once "../classi/Frent.php";

require_once "../CredenzialiDB.php";
$pagina = file_get_contents("../components/login_admin.html");
session_start();
//if(isset($_GET["error_code"])){
//
//
//}
//
//$pagina = str_replace("<FOOTER/>", file_get_contents("../components/footer.html"), $pagina);

if (isset($_POST["accedi"])){
    
    try {
        $db = new Database(CredenzialiDB::DB_ADDRESS, CredenzialiDB::DB_USER,
            CredenzialiDB::DB_PASSWORD, CredenzialiDB::DB_NAME);
    
        $nome = $_POST["user"];
        $password= $_POST["password"];
        $frent = new Frent($db);
        $admin = $frent->adminLogin($nome, $password);
    
        session_start();
        $_SESSION["admin"] = $admin;
        header("Location: ../pagine_php/approvazione_annunci.php");
    
    } catch (Eccezione $e) {
        $pagina = str_replace("<p id=\"credenziali_errate\"></p>",
        "<p id=\"credenziali_errate\">Credenziali errate!</p>", $pagina);
    }
    
}

echo $pagina;
