<?php
require_once "./Database.class.php";
require_once "./Frent.class.php";

require_once "./CredenzialiDB.class.php";
$pagina = file_get_contents("./components/login_admin.html");
$pagina = str_replace("<FORM/>", file_get_contents("./components/login_form.html"), $pagina);
$pagina = str_replace("<PAGE/>", "./login_admin.php", $pagina);
session_start();
if (isset($_POST["accedi"])) {
    
    $nome = $_POST["user"];
    $password = $_POST["password"];
    try {
        $db = new Database(CredenzialiDB::DB_ADDRESS, CredenzialiDB::DB_USER,
            CredenzialiDB::DB_PASSWORD, CredenzialiDB::DB_NAME);
    
        $frent = new Frent($db);
        $admin = $frent->adminLogin($nome, $password);
    
        $_SESSION["admin"] = $admin;
        header("Location: ./approvazione_annunci.php");
        
    } catch (Eccezione $e) {
        
        $pagina = str_replace("<div id=\"credenziali_errate\"></div>",
            "<div id=\"credenziali_errate\"><p>Credenziali errate!</p></div>", $pagina);
        $pagina = str_replace("<VALUEUSERNAME>", "value=\"$nome\"", $pagina);
        $pagina = str_replace("<VALUEPASSWORD>", "value=\"$password\"", $pagina);
    }
    
} else {
    $pagina = str_replace("<VALUEUSERNAME>", " ", $pagina);
    $pagina = str_replace("<VALUEPASSWORD>", " ", $pagina);
}
$pagina= str_replace("<FOOTER/>",file_get_contents("../components/footer.html"),$pagina);

echo $pagina;
