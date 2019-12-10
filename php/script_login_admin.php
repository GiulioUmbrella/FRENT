<?php
require "../php/classi/Database.php";

$db = new Database("localhost","root","","frentdb");

$nome = $_POST["user"];
$password = $_POST["password"];
$db->connect();
session_start();
$_SESSION["db"] = $db;
$res = $db->queryProcedure("admin_login($nome, $password.);");

if(count($res)!=0){
    $_SESSION["db"] = $db;
}else{
    echo "password errato!.";
}
