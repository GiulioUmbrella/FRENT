<?php
$date = new DateTime();
$date->format("Y-m-d");
$pagina = str_replace("<MINDAYB/>",$date->format("Y-m-d"),$pagina);
$pagina = str_replace("<MINDAYE/>",$date->format("Y-m-d"),$pagina);
$date->modify("+180 day");

$pagina = str_replace("<MAXDAYB/>",$date->format("Y-m-d"),$pagina);
$pagina = str_replace("<MAXDAYE/>",$date->format("Y-m-d"),$pagina);