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
        if($auth_user !== NULL && (get_class($auth_user) == "Amministratore" || get_class($auth_user) == "Utente")) {
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

    public function registrazione($nome, $cognome, $username, $mail, $password, $dataNascita, $imgProfilo, $numTelefono): boolean {
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
        $db_instance->connect();
    }

    public function getFotoAnnuncio($id_annuncio) {
        $db_instance->connect();
    }

    public function getCommentiAnnuncio($id_annuncio) {
        $db_instance->connect();
    }

    public function getAnnuncio($id_annuncio) {
        $db_instance->connect();
    }

    public function insertOccupazione(/*$utente,*/ $annuncio, $numospiti, $data_inizio, $data_fine) {
        if(get_class($this->auth_user) === "Utente") {
            throw Eccezione("L'inserimento di un'occupazione può essere svolto solo da un utente registrato.");
        }
        $db_instance->connect();
    }

    public function insertFoto($id_annuncio, $file_path, $descrizione) {
        if(get_class($this->auth_user) === "Utente") {
            throw Eccezione("L'inserimento di una foto di un annuncio può essere svolto solo da un utente registrato.");
        } 
        $db_instance->connect();
    }

    public function insertCommento($prenotazione, $titolo, $commento, $votazione) {
        if(get_class($this->auth_user) === "Utente") {
            throw Eccezione("L'inserimento di un commento può essere svolto solo da un utente registrato.");
        }
        $db_instance->connect();
    }

    public function deleteAnnuncio($id_annuncio) {
        if(get_class($this->auth_user) === "Utente") {
            throw Eccezione("La cancellazione di un annuncio può essere svolta solo da un utente registrato.");
        }
        $db_instance->connect();
    }

    public function deleteCommento($id_prenotazione) {
        if(get_class($this->auth_user) === "Utente") {
            throw Eccezione("La cancellazione di un commento può essere svolta solo da un utente registrato.");
        }
        $db_instance->connect();
    }

    public function deleteFoto($id_foto) {
        if(get_class($this->auth_user) === "Utente") {
            throw Eccezione("La cancellazione di una foto di un annuncio può essere svolta solo da un utente registrato.");
        }
        $db_instance->connect();
    }

    public function deleteOccupazione($id_occupazione) {
        if(get_class($this->auth_user) === "Utente") {
            throw Eccezione("La cancellazione di un'occupazione di un annuncio può essere svolta solo da un utente registrato.");
        }
        $db_instance->connect();
    }

    public function deleteUser(/*$id_utente*/) {
        if(get_class($this->auth_user) === "Utente") {
            throw Eccezione("La cancellazione della propria utenza può essere svolta solo da un utente registrato.");
        }
        $db_instance->connect();
    }

    public function editAnnuncio($id, $titolo, $descrizione, $img_anteprima, $indirizzo, $citta, $max_ospiti, $prezzo_notte) {
        if(get_class($this->auth_user) === "Utente") {
            throw Eccezione("La cancellazione di una foto di un annuncio può essere svolta solo da un utente registrato.");
        }
        $db_instance->connect();
    }

    public function editCommento($id, $titolo, $commento, $valutazione) {
        if(get_class($this->auth_user) === "Utente") {
            throw Eccezione("La modifica di un commento può essere svolta solo da un utente registrato.");
        }
        $db_instance->connect();
    }

    public function editUser(/*$id_utente, */$nome, $cognome, $username, $mail, $password, $datanascita, $imgprofilo, $telefono) {
        if(get_class($this->auth_user) === "Utente") {
            throw Eccezione("La modifica dei dati della propria utenza può essere svolta solo da un utente registrato.");
        }
        $db_instance->connect();
    }

    public function getAnnunciHost(/*$id_host*/) {
        if(get_class($this->auth_user) === "Utente") {
            throw Eccezione("Il reperimento della lista degli annunci di un host può essere svolto solo da un utente registrato.");
        }
        $db_instance->connect();
    }

    public function getPrenotazioniGuest(/*$id_utente*/) {
        if(get_class($this->auth_user) === "Utente") {
            throw Eccezione("Il reperimento della lista delle prenotazioni di un guest può essere svolto solo da un utente registrato.");
        }
        $db_instance->connect();
    }

    public function adminEditStatoApprovazioneAnnuncio($id_annuncio, $stato_approvazione) {
        if(get_class($this->auth_user) === "Amministratore") {
            throw Eccezione("La modifica dello stato di approvazione di un annuncio può essere svolto solo da un amministratore.");
        }
        $db_instance->connect();
    }

    public function adminGetAnnunci() {
        if(get_class($this->auth_user) === "Amministratore") {
            throw Eccezione("Il reperimento degli annunci da approvare può essere svolto solo da un amministratore.");
        }
        $db_instance->connect();
    }

    public function adminLogin($username_or_mail, $password) {
        $db_instance->connect();
    }
}