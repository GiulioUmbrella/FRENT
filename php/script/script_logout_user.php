<?php

session_start();

if (isset($_SESSION["user"])) {
    unset($_SESSION["user"]);
    header("Location: ../pagine_php/index.php");
}