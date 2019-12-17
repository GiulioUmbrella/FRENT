<?php
require_once "../classi/Database.php";
require_once "../classi/Frent.php";
require_once "../classi/Utente.php";


require_once "../CredenzialiDB.php";
$pagina = file_get_contents("../components/login.html");
$pagina = str_replace("<FORM/>", file_get_contents("../components/login_form.html"), $pagina);
$pagina = str_replace("<PAGE/>", "./", $pagina);
if (isset($_POST["accedi"])) {
    
    $nome = $_POST["user"];
    $password = $_POST["password"];
    try {
        $db = new Database(CredenzialiDB::DB_ADDRESS, CredenzialiDB::DB_USER,
            CredenzialiDB::DB_PASSWORD, CredenzialiDB::DB_NAME);
    
        $frent = new Frent($db);
        $user = $frent->login($nome, $password);
    
        echo $user->getCognome();
    
        session_start();
        $_SESSION["user"] = $user;
        header("Location: ../pagine_php/index.php");
        
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
