<?php

    $nomepagina = basename($_SERVER['PHP_SELF']);
    $header="<div id=\"logo\">
        <a href=\"#content\" class=\"aiuti_alla_navigazione\" title=\"Salta il men&ugrave;\">Salta il men&ugrave;</a>
        <h1>Frent</h1>
	<a href = \"#nav\" id = \"link_to_menu\" > Men&ugrave;</a >
    </div >";
if (isset($_SESSION["user"])){
    if ( $nomepagina=="index.php"){
        $header.= "
    <div id=\"nav\">
        <ul>
            <li class=\"menu_selected\" xml:lang=\"en\" lang=\"en\"><span>Home</span>
            </li>
            <li><a href=\"./mie_prenotazioni.php\" title=\"Vai alla pagina delle prenotazioni effettuate\">Le
                mie prenotazioni</a></li>
            <li><a href=\"./miei_annunci.php\" title=\"Vai alla pagina degli annunci posseduti\">I miei
                annunci</a></li>
            <li><a  href=\"./mio_profilo.php\" title=\"Vai alla pagina del profilo\">Il mio
                profilo</a></li>
            <li><a  href=\"./script_logout_user.php\" title=\"Effettua la disconnessione\">Esci</a></li>
        </ul>
    </div>";
    }elseif ($nomepagina=="mie_prenotazioni.php"){
        $header.="
    <div id=\"nav\">
        <ul>
            <li><a  href=\"./index.php\" title=\"Vai alla pagina di ricerca degli annunci\" xml:lang=\"en\" lang=\"en\">Home</a>
            </li>
            <li class=\"menu_selected\"><span>Le mie prenotazioni</span></li>
            <li><a  href=\"./miei_annunci.php\" title=\"Vai alla pagina degli annunci posseduti\">I miei annunci</a></li>
            <li><a  href=\"./mio_profilo.php\" title=\"Vai alla pagina del profilo\">Il mio
                profilo</a></li>
            <li><a  href=\"./script_logout_user.php\" title=\"Effettua la disconnessione\">Esci</a></li>
        </ul>
    </div>";
    }elseif ($nomepagina=="miei_annunci.php"){
        $header.="
    <div id=\"nav\">
        <ul>
            <li><a href=\"./index.php\" title=\"Vai alla pagina di ricerca degli annunci\" xml:lang=\"en\" lang=\"en\">Home</a>
            </li>
            <li><a  href=\"./mie_prenotazioni.php\" title=\"Vai alla pagina delle prenotazioni effettuate\">Le
                mie prenotazioni</a></li>
            <li class=\"menu_selected\"><span>I miei annunci</span></li>
            <li><a  href=\"./mio_profilo.php\" title=\"Vai alla pagina del profilo\">Il mio
                profilo</a></li>
            <li><a  href=\"./script_logout_user.php\" title=\"Effettua la disconnessione\">Esci</a></li>
        </ul>
    </div>";
    }elseif ($nomepagina=="mio_profilo.php"){
        $header.="
    <div id=\"nav\">
        <ul>
            <li><a  href=\"./index.php\" title=\"Vai alla pagina di ricerca degli annunci\" xml:lang=\"en\" lang=\"en\">Home</a>
            </li>
            <li><a  href=\"./mie_prenotazioni.php\" title=\"Vai alla pagina delle prenotazioni effettuate\">Le
                mie prenotazioni</a></li>
            <li><a  href=\"./miei_annunci.php\" title=\"Vai alla pagina degli annunci posseduti\">I miei
                annunci</a></li>
            <li class=\"menu_selected\"><span>Il mio profilo</span></li>
            <li><a href=\"./script_logout_user.php\" title=\"Effettua la disconnessione\">Esci</a></li>
        </ul>
    </div>";
    }
    
}else{
    if ($nomepagina=="index.php"){
        $header .= "
    <div id=\"nav\">
        <ul>
            <li class=\"menu_selected\"><span>Home</span></li>
            <li><a href=\"./login.php\" title=\"Vai alla pagina di accesso\">Accedi</a></li>
            <li><a href=\"./registrazione.php\" title=\"Vai alla pagina di registrazione\">Registrati</a></li>
        </ul>
    </div>";
    }elseif ($nomepagina=="login.php"){
        $header .="
    <div id=\"nav\">
        <ul>
            <li><a href=\"./index.php\" title=\"Vai alla pagina di ricerca degli annunci\" xml:lang=\"en\" lang=\"en\">Home</a>
            </li>
            <li class=\"menu_selected\"><span>Accedi</span></li>
            <li><a href=\"./registrazione.php\" title=\"Vai alla pagina di registrazione\">Registrati</a></li>
        </ul>
    </div>";
    }elseif ($nomepagina=="registrazione.php"){
        $header.="
    <div id=\"nav\">
        <ul>
            <li><a href=\"./index.php\" title=\"Vai alla pagina di ricerca degli annunci\" xml:lang=\"en\" lang=\"en\">Home</a>
            </li>
            <li><a href=\"./login.php\" title=\"Vai alla pagina di accesso\">Accedi</a></li>
            <li class=\"menu_selected\"><span>Registrati</span></li>
        </ul>
    </div>";
    }
}

$pagina = str_replace("<HEADER/>",$header, $pagina);
