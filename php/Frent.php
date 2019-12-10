<?php

class Frent {
    private $db_instance;
    private $auth_user;

    public function __construct($db, $auth_user = null) {
        $db_instance = $db;
        if(gettype($auth_user) == "admin" || gettype($auth_user) == "user")
            $this->auth_user = $auth_user;
    }

    // funzionalità per utenti non autenticati

    public function ricercaAnnunci($citta, $numOspiti, $dataInizio, $dataFine) {
        
    }

    public function registrazione($nome, $cognome, $username, $mail, $password, $dataNascita, $imgProfilo, $numTelefono) {

    }

    public function login($username_or_mail, $password) {

    }

    public function getOccupazioniAnnuncio($id_annuncio) {

    }

    public function getFotoAnnuncio($id_annuncio) {

    }

    public function getCommentiAnnuncio($id_annuncio) {

    }

    public function getAnnuncio($id_annuncio) {

    }

    public function insertOccupazione(/*$utente,*/ $annuncio, $numospiti, $data_inizio, $data_fine) {

    }

    public function insertFoto($id_annuncio, $file_path, $descrizione) {

    }

    public function insertCommento($prenotazione, $titolo, $commento, $votazione) {

    }

    public function deleteAnnuncio($id_annuncio) {

    }

    public function deleteCommento($id_prenotazione) {

    }

    public function deleteFoto($id_foto) {

    }

    public function deleteOccupazione($id_occupazione) {

    }

    public function deleteUser(/*$id_utente*/) {

    }

    public function editAnnuncio($id, $titolo, $descrizione, $img_anteprima, $indirizzo, $citta, $max_ospiti, $prezzo_notte) {

    }

    public function editCommento($id, $titolo, $commento, $valutazione) {

    }

    public function editUser(/*$id_utente, */$nome, $cognome, $username, $mail, $password, $datanascita, $imgprofilo, $telefono) {
        
    }

    public function getAnnunciHost(/*$id_host*/) {

    }

    public function getPrenotazioniGuest(/*$id_utente*/) {

    }

    public function adminEditStatoApprovazioneAnnuncio($id_annuncio, $stato_approvazione) {

    }

    public function adminGetAnnunci() {

    }

    public function adminLogin($username_or_mail, $password) {
        
    }
}