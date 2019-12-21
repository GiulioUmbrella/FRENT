<?php

require_once "./class_CredenzialiDB.php";
require_once "./class_Amministratore.php";
require_once "./class_Eccezione.php";
require_once "./class_Utente.php";
require_once "./class_Occupazione.php";
require_once "./class_Annuncio.php";
require_once "./class_Database.php";
require_once "./class_Commento.php";
require_once "./class_Foto.php";
require_once "./class_Frent.php";
$manager = new Frent(new Database(CredenzialiDB::DB_ADDRESS, CredenzialiDB::DB_USER,
    CredenzialiDB::DB_PASSWORD, CredenzialiDB::DB_NAME),$_SESSION["user"]);