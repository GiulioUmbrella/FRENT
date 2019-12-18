<?php
class DataConstraints{
    const amministratori = array(
        "user_name" => 32, //string
        "password" => 48, //string
        "mail" => 191 //string
    );

    const utenti = array(
        "nome" => 32, // string
        "cognome" => 32,  // string
        "user_name" => 32,  // string
        "mail" => 191,  // string
        "password" => 48, // string
        "img_profilo" => 48, // string
        "telefono" => 18  // string
    );
    
    const annunci = array(
        "titolo" => 32,  // string
        "descrizione" => 512, // string
        "img_anteprima" => 48, // string
        "indirizzo" => 128, // string
        "citta" => 128, // string
        "stato_approvazione" => 2,  // int
        "bloccato" => 1, // bool (= 0, = 1)
        "max_ospiti" => 99,  // int
    );
    
    const foto_annunci = array(
        "file_path" => 128, // string
        "descrizione" => 128 // string
    );
    
    const occupazioni = array(
        "prenotazione_guest" => 1,  // bool (= 0, = 1)
        "num_ospiti" => 99  // int
    );
    
    const commenti = array(
        "titolo" => 64, // string
        "commento" => 512, // string
        "votazione" => 5  // int
    );
}