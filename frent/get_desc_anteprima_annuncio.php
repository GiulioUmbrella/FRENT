<?php
require_once("./load_Frent.php");

if(!isset($_GET["id_annuncio"])) {
    echo "";
}

$id_annuncio = intval($_GET["id_annuncio"]);

try {
    $annuncio = $frent->getAnnuncio($id_annuncio);

    echo $annuncio->getDescAnteprima();
} catch(Eccezione $exc) {
    echo "";
}
