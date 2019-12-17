<?php
//todo
$pagina = file_get_contents("../components/mie_prenotazioni.html");
session_start();
if (isset($_SESSION["user"])){
    $pagina= str_replace("<HEADER/>",file_get_contents("../components/header_logged.html"),$pagina);
    $pagina= str_replace("<FOOTER/>",file_get_contents("../components/footer.html"),$pagina);
    
    $prenotazioni="";
    // pescare le prenotazioni correnti
    $frent = new Frent(new Database(CredenzialiDB::DB_ADDRESS, CredenzialiDB::DB_USER,
                CredenzialiDB::DB_PASSWORD, CredenzialiDB::DB_NAME)
        ,$_SESSION["user"]);
//    <li>
//            <div id="ID_PRENOTAZIONE_3" class="intestazione_lista">
//                <a href="../../html/riepilogo_prenotazione.html" tabindex="7"
//                   title="Vai al riepilogo della prenotazione presso Casa Loreto">[#123] Soggiorno presso Casa
//                           Loreto</a>
//            </div>
//            <div class="corpo_lista lista_flex">
//                <div class="dettagli_prenotazione">
//                    <img src="../../immagini/img3.jpg" alt="Immagine di anteprima di Casa Loreto"/>
//                    <p>LUOGO DI ALLOGGIO</p>
//                    <p>DATA/INIZIO - DATA/FINE</p>
//                </div>
//                <div class="opzioni_prenotazione">
//                    <p>PREZZO</p>
//                    <form action="#" method="post">
//                        <fieldset>
//                            <legend class="aiuti_alla_navigazione">Elimina prenotazione</legend>
//                            <input type="hidden" value="ID_PRENOTAZIONE"/>
//                            <input type="submit" value="Elimina" title="Elimina la prenotazione per TITOLO ANNUNCIO"/>
//                            <!--TODO:modificare con PHP-->
//                        </fieldset>
//                    </form>
//                </div>
//            </div>
//        </li>
//
    
    
    
    
    $pagina= str_replace("<PRENOTAZIONICORRENTI/>",$prenotazioni,$pagina);
    
    
    $prenotazioni="";
    
    $pagina= str_replace("<PRENOTAZIONIFUTURE/>",$prenotazioni,$pagina);
    
    $prenotazioni="";
    
    $pagina= str_replace("<PRENOTAZIONIPASSATE/>",$prenotazioni,$pagina);
    



    echo $pagina;
}else{
    header("Location: login.php");
}