<?php
session_start();
if (isset($_GET["id"])){
    $_SESSION["id"] = $_GET["id"];
    header("Location: ./modifica_annuncio.php");
}else{
    header("Location: ./404.php");
}
