<?php
require_once "./class_Database.php";
require_once "./class_Frent.php";
require_once "./class_CredenzialiDB.php";
require_once "load_Frent.php";
$pagina = file_get_contents("./components/login_admin.html");
$pagina = str_replace("<FORM/>", file_get_contents("./components/login_form.html"), $pagina);
$pagina = str_replace("<PAGE/>", "./login_admin.php", $pagina);
$pagina= str_replace("<FOOTER/>",file_get_contents("./components/footer.html"),$pagina);
if (isset($_POST["accedi"])) {
    
    $nome = $_POST["user"];
    $password = $_POST["password"];
    try {
    
        require_once "./load_Frent.php";
        $admin = $frent->adminLogin($nome, $password);
    
        $_SESSION["admin"] = $admin;
        header("Location: ./approvazione_annunci.php");
    
    } catch (Eccezione $e) {
    
        $pagina = str_replace("<div id=\"credenziali_errate\"></div>",
            "<div id=\"credenziali_errate\" class=\"aligned_with_form\"><p>Credenziali errate!</p></div>", $pagina);
        $pagina = str_replace("<VALUEUSERNAME>", $nome, $pagina);
        $pagina = str_replace("<VALUEPASSWORD>", $password, $pagina);
    }
    
} else {
    $pagina = str_replace("<VALUEUSERNAME>", "", $pagina);
    $pagina = str_replace("<VALUEPASSWORD>", "", $pagina);
    
    echo $pagina;
}
