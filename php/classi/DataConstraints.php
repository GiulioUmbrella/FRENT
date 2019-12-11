<?php
class DataConstraints{
    const amministratori = array(
        "user_name" => 32,//string
        "password" => 48,//string
        "mail" => 191//string
    );
    
    
    const utenti = array(
        "nome" => 32,
        "cognome" => 32,
        "user_name" => 32,
        "mail" => 191,
        "password" => 48,
        "img_profilo" => 48,// modificare quando si conosce meglio il path
        "telefono" => 18
    );
    
    const annunci = array(
        "titolo" => 32,
        "descrizione" => 512,
        "img_anteprima" => 48,//modificare quando si conosce meglio il path
        "indirizzo" => 128,
        "citta" => 128,
        "stato_approvazione" => 2,// 0 = NVNA / VA = 1 / VNA = 2 (per le sigle guardare analisirequisiti.md)
        "bloccato" => 1,//0 = non bloccato, 1 = bloccato
        "max_ospiti" => 99, //limite da 0 a 99 (almeno da db)
    );
    
    const foto_annunci = array(
        "file_path" => 128,
        "descrizione" => 128
    );
    
    const occupazioni = array(
//    id_occupazione int primary key auto_increment,
//	utente int not null,
//	annuncio int,
        "prenotazione_guest" => 1,// 1 indica che è una prenotazione, 0 è settato dall'host
        "num_ospiti" => 2 //limite da 0 a 99 (almeno da db)
//	"data_inizio" date not null,
//	"data_fine" date not null,
    );
    
    const commenti = array(
//    prenotazione int primary key, --non è solo un occupazione, ha anche il flag prenotazione_guest = true
//	data_pubblicazione datetime DEFAULT CURRENT_TIMESTAMP,
        "titolo" => 64,
        "commento" => 512,
        "votazione" => 5 //verificare via trigger che sia 0 < voto < 6
    );
    
}