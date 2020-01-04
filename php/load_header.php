<?php
    $nomepagina = basename($_SERVER['PHP_SELF']);
    $header="";
if ( $nomepagina=="index.php"){
$header= "<div id=\"logo\">
    <a href=\"#content\" class=\"aiuti_alla_navigazione\" title=\"Salta il men&ugrave;\">Salta il men&ugrave;</a>
    <h1>Frent</h1>
    <a href=\"#nav\" id=\"link_to_menu\"><span></span>Vai al men&ugrave;</a>
    </div>
    <div id=\"nav\">
        <ul>
            <li xml:lang=\"en\" lang=\"en\">Home
            </li>
            <li><a tabindex=\"2\" href=\"./mie_prenotazioni.php\" title=\"Vai alla pagina delle prenotazioni effettuate\">Le
                mie prenotazioni</a></li>
            <li><a tabindex=\"3\" href=\"./miei_annunci.php\" title=\"Vai alla pagina degli annunci posseduti\">I miei
                annunci</a></li>
            <li><a tabindex=\"4\" href=\"./mio_profilo.php\" title=\"Vai alla pagina del profilo\">Il mio
                profilo</a></li>
            <li><a tabindex=\"5\" href=\"./script_logout_user.php\" title=\"Effettua la disconnessione\">Esci</a></li>
        </ul>
    </div>";
}elseif ($nomepagina=="mie_prenotazioni.php"){
$header="<div id=\"logo\">
    <a href=\"#content\" class=\"aiuti_alla_navigazione\" title=\"Salta il men&ugrave;\">Salta il men&ugrave;</a>
    <h1>Frent</h1>
    <a href=\"#nav\" id=\"link_to_menu\"><span></span>Vai al men&ugrave;</a>
    </div>
    <div id=\"nav\">
        <ul>
            <li><a tabindex=\"1\" href=\"./index.php\" title=\"Vai alla pagina di ricerca degli annunci\" xml:lang=\"en\" lang=\"en\">Home</a>
            </li>
            <li>Le mie prenotazioni</li>
            <li><a tabindex=\"3\" href=\"./miei_annunci.php\" title=\"Vai alla pagina degli annunci posseduti\">I miei annunci</a></li>
            <li><a tabindex=\"4\" href=\"./mio_profilo.php\" title=\"Vai alla pagina del profilo\">Il mio
                profilo</a></li>
            <li><a tabindex=\"5\" href=\"./script_logout_user.php\" title=\"Effettua la disconnessione\">Esci</a></li>
        </ul>
    </div>";
}elseif ($nomepagina=="miei_annunci.php"){
$header="<div id=\"logo\">
    <a href=\"#content\" class=\"aiuti_alla_navigazione\" title=\"Salta il men&ugrave;\">Salta il men&ugrave;</a>
    <h1>Frent</h1>
    <a href=\"#nav\" id=\"link_to_menu\"><span></span>Vai al men&ugrave;</a>
    </div>
    <div id=\"nav\">
        <ul>
            <li><a tabindex=\"1\" href=\"./index.php\" title=\"Vai alla pagina di ricerca degli annunci\" xml:lang=\"en\" lang=\"en\">Home</a>
            </li>
            <li><a tabindex=\"2\" href=\"./mie_prenotazioni.php\" title=\"Vai alla pagina delle prenotazioni effettuate\">Le
                mie prenotazioni</a></li>
            <li>I miei annunci</li>
            <li><a tabindex=\"4\" href=\"./mio_profilo.php\" title=\"Vai alla pagina del profilo\">Il mio
                profilo</a></li>
            <li><a tabindex=\"5\" href=\"./script_logout_user.php\" title=\"Effettua la disconnessione\">Esci</a></li>
        </ul>
    </div>";
}elseif ($nomepagina=="mio_profilo.php"){
    $header="<div id=\"logo\">
    <a href=\"#content\" class=\"aiuti_alla_navigazione\" title=\"Salta il men&ugrave;\">Salta il men&ugrave;</a>
    <h1>Frent</h1>
    <a href=\"#nav\" id=\"link_to_menu\"><span></span>Vai al men&ugrave;</a>
    </div>
    <div id=\"nav\">
        <ul>
            <li><a tabindex=\"1\" href=\"./index.php\" title=\"Vai alla pagina di ricerca degli annunci\" xml:lang=\"en\" lang=\"en\">Home</a>
            </li>
            <li><a tabindex=\"2\" href=\"./mie_prenotazioni.php\" title=\"Vai alla pagina delle prenotazioni effettuate\">Le
                mie prenotazioni</a></li>
            <li><a tabindex=\"3\" href=\"./miei_annunci.php\" title=\"Vai alla pagina degli annunci posseduti\">I miei
                annunci</a></li>
            <li>Il mio profilo</li>
            <li><a tabindex=\"5\" href=\"./script_logout_user.php\" title=\"Effettua la disconnessione\">Esci</a></li>
        </ul>
    </div>";
}


$pagina = str_replace("<HEADER/>",$header, $pagina);
