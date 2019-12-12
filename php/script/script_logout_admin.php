<?php
session_start();

if (isset($_SESSION["admin"])){
    unset($_SESSION["admin"]);
    header("Location: ../../html/login_admin.html");
}