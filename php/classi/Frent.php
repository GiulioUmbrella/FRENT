<?php

require_once "Amministratore.php";
require_once "Annuncio.php";
require_once "Commento.php";
require_once "Database.php";
require_once "Eccezione.php";
require_once "Foto.php";
require_once "Occupazione.php";
require_once "Utente.php";

class Frent {
    private $db_instance;
    private $auth_user;

    public function __construct($db, $auth_user = NULL) {
        $db_instance = $db;
        if($auth_user != NULL && (get_class($auth_user) == "Amministratore" || get_class($auth_user) == "Utente")) {
            $this->auth_user = $auth_user;
        }
    }

    // funzionalità per utenti non autenticati

    public function ricercaAnnunci($citta, $numOspiti, $dataInizio, $dataFine): array {
        $db_instance->connect();
        $lista_annunci = $db_instance->queryProcedure("ricerca_annunci(\'$citta\', $numOspiti, \'$dataInizio\', \'$dataFine\')");
        
        foreach($lista_annunci as $i => $assoc_annuncio) {
            $lista_annunci[$i] = new Annuncio(
                $assoc_annuncio['id_annuncio'],
                $assoc_annuncio['titolo'],
                $assoc_annuncio['descrizione'],
                $assoc_annuncio['img_anteprima'],
                $assoc_annuncio['indirizzo'],
                $citta,
                $assoc_annuncio['prezzo_notte']
            );
        }

        return $lista_annunci;
    }

    public function registrazione($nome, $cognome, $username, $mail, $password, $dataNascita, $imgProfilo, $numTelefono) {
        $db_instance->connect();
        $risultato = $db_instance->queryFunction("registrazione(\'$nome\', \'$cognome\', \'$username\', \'$mail\', \'$password\', \'$dataNascita\', \'$imgProfilo\', \'$numTelefono\')");

        return $risultato != -1;
    }

    public function login($username_or_mail, $password): Utente {
        $db_instance->connect();
        $utente = $db_instance->queryProcedure("login(\'$username_or_mail\', \'$password\')");

        if(count($utente) == 1) {
            return new Utente(
                $utente[0]['id_utente'],
                $utente[0]['nome'],
                $utente[0]['cognome'],
                $utente[0]['user_name'],
                $utente[0]['mail'],
                $utente[0]['data_nascita'],
                $utente[0]['img_profilo'],
                $utente[0]['telefono']
            );
        } else {
            return null;
        }
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