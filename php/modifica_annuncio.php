<?php
$pagina = file_get_contents("./components/dettaglio_annuncio_host_modifica.html");
require_once "load_Frent.php";
if (isset($_SESSION["user"]) ){
    if (isset($_SESSION["id"])){
        $annuncio = $frent->getAnnuncio(intval($_SESSION["id"]));
        if ($annuncio->getIdHost()!= $_SESSION["user"]->getIdUtente())
            header("Location: ./404.php");
        try {
            $pagina= str_replace("<HEADER/>",file_get_contents("./components/header_logged.html"),$pagina);
            $pagina= str_replace("<FOOTER/>",file_get_contents("./components/footer.html"),$pagina);
            
            $pagina = str_replace("<NUMOSPITIMAX/>",$annuncio->getMaxOspiti(),$pagina);
            $pagina = str_replace("<PrAPERSONA/>",$annuncio->getPrezzoNotte(),$pagina);
            $pagina = str_replace("<DESCRIZIONE/>",$annuncio->getDescrizione(),$pagina);
            $pagina = str_replace("<VIA/>",$annuncio->getIndirizzo(),$pagina);
            $pagina = str_replace("<CITTA/>",$annuncio->getCitta(),$pagina);
    
            $foto = $frent->getFotoAnnuncio(intval($annuncio->getIdAnnuncio()));
            $path=$annuncio->getImgAnteprima();
            $descrizione= $annuncio->getDescrizione();
            $pagina = str_replace("<TITOLO/>",$annuncio->getTitolo(),$pagina);
            $content="
            <li>
                    <fieldset>
                        <div class=\"commenti_foto\">
                            <img src=\"$path\" alt=\"Immagine che hai deciso di caricare.\">
                            <label for=\"commento1\">
                            <textarea id=\"commento1\" maxlength=\"512\" rows=\"8\" cols=\"30\"
                                      placeholder=\"Inserisci la descrizione della foto\">$descrizione</textarea>
                            </label>
                        </div>
                    </fieldset>

                </li>";
    
            foreach ($foto as $f){
                $descrizione=$f->getDescrizione();
                $path = $f->getFilePath();
                $id= $f->getIdFoto();
                $content.="
            <li>
                    <fieldset>
                        <div class=\"commenti_foto\">
                            <img src=\"$path\" alt=\"Immagine che hai deciso di caricare.\">
                            <label for=\"commento1\">
                            <textarea id=\"commento1\" maxlength=\"512\" rows=\"8\" cols=\"30\"
                                      placeholder=\"Inserisci la descrizione della foto\">$descrizione</textarea>
                                <a href=\"script_elimina_foto.php?id=$id\" tabindex=\"17\"
                                   title=\"elimina la foto numero 1\">Elimina</a>
                            </label>
                        </div>
                    </fieldset>

                </li>";
            }
    
            $pagina = str_replace("<IMMAGINI/>",$content,$pagina);
            echo $pagina;
        }catch (Eccezione $ex){
            header("Location: ./404.php");
        }
    }
}else{
    header("Location: login.php");
}
