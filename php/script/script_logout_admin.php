<?php
session_start();

if (isset($_SESSION["admin"])){
    unset($_SESSION["admin"]);
    header("Location: ../pagine_php/login_admin.php");
}