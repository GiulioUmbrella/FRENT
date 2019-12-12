<?php

require_once "Amministratore.php";
require_once "Annuncio.php";
require_once "Commento.php";
require_once "Database.php";
require_once "Eccezione.php";
require_once "Foto.php";
require_once "Occupazione.php";
require_once "Utente.php";
require_once "../CheckMethods.php";

class Frent {
    private $db_instance;
    private $auth_user;

    /**
     * Costruttore della classe Frent.
     * @param Database $db
     * @param null $auth_user
     */
    public function __construct($db, $auth_user = NULL) {
        $this->db_instance = $db;
        $this->db_instance->setCharset("utf8");
        if($auth_user !== NULL && (get_class($auth_user) == "Amministratore" || get_class($auth_user) == "Utente")) {
            $this->auth_user = $auth_user;
        }
    }

    /**
     * Ricerca degli annunci data i parametri in ingresso.
     * @param string $citta città in cui cercare gli annunci
     * @param int $numOspiti numero di ospiti per cui cercare fra gli annunci
     * @param string $dataInizio data di inizio del soggiorno cercato
     * @param string $dataFine data di fine del soggiorno cercato
     * @return array di oggetti di tipo Annuncio che corrispondono alle richieste passate come parametro
     * @throws Eccezione in caso di parametri invalidi, errori nella connessione al database, errori nella creazione di oggetti Annuncio
     */
    public function ricercaAnnunci($citta, $numOspiti, $dataInizio, $dataFine): array {
        try {
            if(!is_int($numOspiti) || !checkIsValidDate($dataInizio) || !checkIsValidDate($dataFine) || !checkDateBeginAndEnd($dataInizio, $dataFine)) {
                throw new Eccezione("Parametri di invocazione di ricercaAnnunci errati.");
            }
            $this->db_instance->connect();
            $procedure_name_and_params = "ricerca_annunci(\"$citta\", $numOspiti, \"$dataInizio\", \"$dataFine\")";
            $lista_annunci = $this->db_instance->queryProcedure($procedure_name_and_params);

            foreach($lista_annunci as $i => $assoc_annuncio) {
                $lista_annunci[$i] = new Annuncio(
                    intval($assoc_annuncio['id_annuncio']),
                    $assoc_annuncio['titolo'],
                    $assoc_annuncio['descrizione'],
                    $assoc_annuncio['img_anteprima'],
                    $assoc_annuncio['indirizzo'],
                    $citta,
                    floatval($assoc_annuncio['prezzo_notte'])
                );
            }

            return $lista_annunci;
        } catch(Eccezione $exc) {
            throw $exc;
        }
    }

    /**
     * Registra un nuovo utente nel sito.
     * @param string $nome nome dell'utente
     * @param string $cognome cognome dell'utente
     * @param string $username nome utente scelto dall'utente
     * @param string $mail indirizzo mail dell'utente
     * @param string $password password scelta dall'utente
     * @param string $dataNascita data di nascita dell'utente
     * @param string $imgProfilo nome del file dell'immagine di profilo caricata dall'utente
     * @param string $numTelefono numero di telefono dell'utente
     * @return int ID del nuovo utente registrato se il processo è andato a buon fine
     * @return int -1 se si è verificato un errore
     * @throws Eccezione in caso di parametri invalidi, errori nella connessione al database
     */
    public function registrazione($nome, $cognome, $username, $mail, $password, $dataNascita, $imgProfilo, $numTelefono): int {
        try {
            if(!checkIsValidDate($dataNascita) || !checkPhoneNumber($numTelefono)) {
                throw new Eccezione("Parametri di invocazione di registrazione errati.");
            }
            $this->db_instance->connect();
            $function_name_and_params = "registrazione(\"$nome\", \"$cognome\", \"$username\", \"$mail\", \"$password\", \"$dataNascita\", \"$imgProfilo\", \"$numTelefono\")";
            return intval($this->db_instance->queryFunction($function_name_and_params));
        } catch(Eccezione $exc) {
            throw $exc;
        }      
    }

    /**
     * Verifica se l'utente è registrato nel sito.
     * @param string $username_or_mail nome utente oppure indirizzo e-mail dell'utente
     * @param string $password password dell'utente collegata al nome utente o indirizzo e-mail
     * @return Utente oggetto di classe Utente se è stato effettuato il login (ovvero le credenziali sono corrette e legate ad un profilo utente)
     * @throws Eccezione in caso di parametri invalidi, errori nella connessione al database, errori nella creazione dell'oggetto Utente, restituzioni != 1 record dal DB
     */
    public function login($username_or_mail, $password): Utente {
        try {
            $this->db_instance->connect();
            $procedure_name_and_params = "login(\"$username_or_mail\", \"$password\")";
            $utente = $this->db_instance->queryProcedure($procedure_name_and_params);
            
            if(count($utente) !== 1) {
                throw new Eccezione(htmlentities("Non è stato trovato nessun utente con queste credenziali."));
            }

            return new Utente(
                intval($utente[0]['id_utente']),
                $utente[0]['nome'],
                $utente[0]['cognome'],
                $utente[0]['user_name'],
                $utente[0]['mail'],
                $utente[0]['data_nascita'],
                $utente[0]['img_profilo'],
                $utente[0]['telefono']
            );
        } catch(Eccezione $exc) {
            throw $exc;
        }
    }


    /**
     * Restituisce le occupazioni di un annuncio, dato il suo ID.
     * @param $id_annuncio id dell'annuncio
     * @return array di oggetti di tipo Occupazione
     * @throws Eccezione in caso di parametri invalidi, errori nella connessione al database, errori nella creazione degli oggetti Occupazione
     */
    public function getOccupazioniAnnuncio($id_annuncio): array {
        try {
            if(!is_int($id_annuncio)) {
                throw new Eccezione("Parametri di invocazione di getOccupazioniAnnuncio errati.");
            }
            $this->db_instance->connect();
            $procedure_name_and_params = "get_occupazioni_annuncio($id_annuncio)";
            $lista_occupazioni = $this->db_instance->queryProcedure($procedure_name_and_params);
    
            foreach($lista_occupazioni as $i => $assoc_occupazione) {
                $lista_occupazioni[$i] = new Occupazione(
                    intval($assoc_occupazione['id_occupazione']),
                    intval($assoc_occupazione['utente']),
                    $id_annuncio, // già int, non serve fare il parsing
                    intval($assoc_occupazione['prenotazione_guest']),
                    intval($assoc_occupazione['num_ospiti']),
                    $assoc_occupazione['data_inizio'],
                    $assoc_occupazione['data_fine']
                );
            }
    
            return $lista_occupazioni;
        } catch(Eccezione $exc) {
            throw $exc;
        }
    }
    
    /**
     * Restituisce le foto della galleria di annuncio, dato il suo ID.
     * @param int $id_annuncio id dell'annuncio
     * @return array di oggetti di tipo Foto
     * @throws Eccezione
     */
    public function getFotoAnnuncio($id_annuncio): array {
        try {
            if(!is_int($id_annuncio)) {
                throw new Eccezione("Parametri di invocazione di getFotoAnnuncio errati.");
            }

            $this->db_instance->connect();
            $procedure_name_and_params = "get_foto_annuncio($id_annuncio)";
            $lista_foto = $this->db_instance->queryProcedure($procedure_name_and_params);

            foreach($lista_foto as $i => $assoc_foto) {
                $lista_foto[$i] = new Foto(
                    intval($assoc_foto['id_foto']),
                    $assoc_foto['descrizione'],
                    $assoc_foto['file_path'],
                    $id_annuncio
                );
            }
    
            return $lista_foto;
        } catch(Eccezione $exc) {
            throw $exc;
        }
    }
    
    /**
     * Restituisce i commenti di un annuncio, dato il suo ID.
     * @param int $id_annuncio id dell'annuncio
     * @return array di oggetti di tipo Commento
     * @throws Eccezione
     */
    public function getCommentiAnnuncio($id_annuncio): array {
        try {
            if(!is_int($id_annuncio)) {
                throw new Eccezione("Parametri di invocazione di getFotoAnnuncio errati.");
            }
            $this->db_instance->connect();
            $procedure_name_and_params = "get_commenti_annuncio($id_annuncio)";
            $lista_commenti = $this->db_instance->queryProcedure($procedure_name_and_params);

            foreach($lista_commenti as $i => $assoc_commento) {
                $lista_commenti[$i] = new Commento(
                    $assoc_commento['titolo'],
                    $assoc_commento['commento'],
                    $assoc_commento['data_pubblicazione'],
                    intval($assoc_commento['votazione']),
                    intval($assoc_commento['prenotazione'])
                );
            }

            return $lista_commenti; 
        } catch(Eccezione $exc) {
            throw $exc;
        }
          
    }

    public function getAnnuncio($id_annuncio): Annuncio {
        try {
            if(!is_int($id_annuncio)) {
                throw new Eccezione("Parametri di invocazione di getAnnuncio errati.");
            }
            $this->db_instance->connect();
            $procedure_name_and_params = "get_annuncio($id_annuncio)";
            $annuncio = $this->db_instance->queryProcedure($procedure_name_and_params);

            if(count($annuncio) !== 1) {
                throw new Eccezione(htmlentities("Non è stato trovato nessun annuncio con queste ID."));   
            }

            return new Annuncio(
                intval($annuncio[0]['id_annuncio']),
                $annuncio[0]['titolo'],
                intval($annuncio[0]['stato_approvazione']),
                $annuncio[0]['descrizione'],
                $annuncio[0]['img_anteprima'],
                $annuncio[0]['indirizzo'],
                $annuncio[0]['citta'],
                intval($annuncio[0]["host"]),
                floatval($annuncio[0]['prezzo_notte'])
            );
        } catch(Eccezione $exc) {
            throw $exc;
        }
        
    }

    /**
     * ID dell'occupazione appena inserita se tutto è andato a buon fine
     * -1 se la data di inizio e la data di fine passate in input non sono ordinate temporalmente
     * -2 se ci sono altre occupazioni nel range di date passate in input
     * -3 se l'inserimento è fallito (per esempio a causa di chiavi esterne errate)
    */
    public function insertOccupazione($annuncio, $numospiti, $data_inizio, $data_fine): int {
        try {
            if(get_class($this->auth_user) === "Utente") {
                throw new Eccezione(htmlentities("L'inserimento di un'occupazione può essere svolto solo da un utente registrato."));
            }
            if(!is_int($annuncio) || !is_int($numospiti) || !checkIsValidDate($data_inizio) || !checkIsValidDate($data_fine)) {
                throw new Eccezione(htmlentities("Parametri di invocazione di insertOccupazione errati."));
            }
            $this->db_instance->connect();
            $function_name_and_params = "insert_occupazione(" . $this->auth_user->getIdUtente() . ", $annuncio, $numospiti, \"$data_inizio\", \"$data_fine\")";
        
            return intval($this->db_instance->queryFunction($function_name_and_params));
        } catch(Eccezione $exc) {
            throw $exc;
        }
    }

    public function insertFoto($id_annuncio, $file_path, $descrizione): int {
        if(get_class($this->auth_user) !== "Utente") {
            throw new Eccezione("L'inserimento di una foto di un annuncio può essere svolto solo da un utente registrato.");
        } 
        $this->db_instance->connect();
        $function_name_and_params = "insert_foto($id_annuncio, \"$file_path\", \"$descrizione\")";

        return intval($this->db_instance->queryFunction($function_name_and_params));
    }

    public function insertCommento($prenotazione, $titolo, $commento, $votazione): int {
        if(get_class($this->auth_user) !== "Utente") {
            throw new Eccezione("L'inserimento di un commento può essere svolto solo da un utente registrato.");
        }
        $this->db_instance->connect();
        $function_name_and_params = "insert_commento($prenotazione, $titolo, $commento, $votazione)";

        return intval($this->db_instance->queryFunction($function_name_and_params));
    }

    public function deleteAnnuncio($id_annuncio) {
        if(get_class($this->auth_user) !== "Utente") {
            throw new Eccezione("La cancellazione di un annuncio può essere svolta solo da un utente registrato.");
        }
        $this->db_instance->connect();
        $function_name_and_params = "delete_annuncio($id_annuncio)";

        return intval($this->db_instance->queryFunction($function_name_and_params));
    }

    public function deleteCommento($id_prenotazione): int {
        if(get_class($this->auth_user) !== "Utente") {
            throw new Eccezione("La cancellazione di un commento può essere svolta solo da un utente registrato.");
        }
        $this->db_instance->connect();
        $function_name_and_params = "delete_commento($id_prenotazione)";

        return intval($this->db_instance->queryFunction($function_name_and_params));
    }

    public function deleteFoto($id_foto): int {
        if(get_class($this->auth_user) !== "Utente") {
            throw new Eccezione("La cancellazione di una foto di un annuncio può essere svolta solo da un utente registrato.");
        }
        $this->db_instance->connect();
        $function_name_and_params = "delete_foto($id_foto)";

        return intval($this->db_instance->queryFunction($function_name_and_params));
    }

    public function deleteOccupazione($id_occupazione): int {
        if(get_class($this->auth_user) !== "Utente") {
            throw new Eccezione("La cancellazione di un'occupazione di un annuncio può essere svolta solo da un utente registrato.");
        }
        $this->db_instance->connect();
        $function_name_and_params = "delete_occupazione($id_occupazione)";

        return intval($this->db_instance->queryFunction($function_name_and_params));
    }

    public function deleteUser(): int {
        if(get_class($this->auth_user) !== "Utente") {
            throw new Eccezione("La cancellazione della propria utenza può essere svolta solo da un utente registrato.");
        }
        $this->db_instance->connect();
        $function_name_and_params = "delete_user(" . $this->auth_user->getIdUtente() . ")";

        return intval($this->db_instance->queryFunction($function_name_and_params));
    }

    public function editAnnuncio($id, $titolo, $descrizione, $img_anteprima, $indirizzo, $citta, $max_ospiti, $prezzo_notte): int {
        if(get_class($this->auth_user) !== "Utente") {
            throw new Eccezione("La cancellazione di una foto di un annuncio può essere svolta solo da un utente registrato.");
        }
        $this->db_instance->connect();
        $function_name_and_params = "edit_annuncio($id, \"$titolo\", \"$descrizione\", \"$img_anteprima\", \"$indirizzo\", \"$citta\", $max_ospiti, $prezzo_notte)";

        return intval($this->db_instance->queryFunction($function_name_and_params));
    }

    public function editCommento($id, $titolo, $commento, $valutazione): int {
        if(get_class($this->auth_user) !== "Utente") {
            throw new Eccezione("La modifica di un commento può essere svolta solo da un utente registrato.");
        }
        $this->db_instance->connect();
        $function_name_and_params = "edit_commento($id, \"$titolo\", \"$commento\", $valutazione)";

        return intval($this->db_instance->queryFunction($function_name_and_params));
    }

    public function editUser($nome, $cognome, $username, $mail, $password, $datanascita, $imgprofilo, $telefono): int {
        if(get_class($this->auth_user) !== "Utente") {
            throw new Eccezione("La modifica dei dati della propria utenza può essere svolta solo da un utente registrato.");
        }
        $this->db_instance->connect();
        $function_name_and_params = "edit_user(" . $this->auth_user->getIdUtente() . ", \"$nome\", \"$cognome\", \"$username\", \"$mail\", \"$password\", \"$datanascita\", \"$imgprofilo\", \"$telefono\"";

        return intval($this->db_instance->queryFunction($function_name_and_params));
    }

    public function getAnnunciHost(): array {
        if(get_class($this->auth_user) !== "Utente") {
            throw new Eccezione("Il reperimento della lista degli annunci di un host può essere svolto solo da un utente registrato.");
        }
        $this->db_instance->connect();
        $procedure_name_and_params = "get_annunci_host(" . $this->auth_user->getIdUtente() . ")";
        $lista_annunci = $this->db_instance->queryProcedure($procedure_name_and_params);
        
        foreach($lista_annunci as $i => $assoc_annuncio) {
            $lista_annunci[$i] = new Annuncio(
                intval($assoc_annuncio['id_annuncio']),
                $assoc_annuncio['titolo'],
                $assoc_annuncio['descrizione'],
                $assoc_annuncio['img_anteprima'],
                $assoc_annuncio['indirizzo'],
                $assoc_annuncio['citta'],
                floatval($assoc_annuncio['prezzo_notte'])
            );
        }
        
        return $lista_annunci;
    }

    public function getPrenotazioniGuest(): array {
        if(get_class($this->auth_user) !== "Utente") {
            throw new Eccezione("Il reperimento della lista delle prenotazioni di un guest può essere svolto solo da un utente registrato.");
        }
        $this->db_instance->connect();
        $procedure_name_and_params = "get_prenotazioni_guest(" . $this->auth_user->getIdUtente() . ")";
        $lista_prenotazioni = $this->db_instance->queryProcedure($procedure_name_and_params);

        foreach($lista_prenotazioni as $i => $assoc_prenotazione) {
            $lista_prenotazioni[$i] = new Occupazione(
                intval($assoc_prenotazione['id_occupazione']),
                intval($assoc_prenotazione['utente']),
                intval($assoc_prenotazione['annuncio']),
                intval($assoc_prenotazione['prenotazione_guest']),
                intval($assoc_prenotazione['num_ospiti']),
                $assoc_prenotazione['data_inizio'],
                $assoc_prenotazione['data_fine']
            );
        }

        return $lista_prenotazioni;
    }
    
    /**
     * @param $id_annuncio int id dell'annuncio il quale stato di approvazione deve essere modificato;
     * @param $stato_approvazione int il nuovo stato dell'annuncio
     * @return int restituisce
     * @throws Eccezione
     */
    public function adminEditStatoApprovazioneAnnuncio($id_annuncio, $stato_approvazione): int {
        if(get_class($this->auth_user) !== "Amministratore") {
            throw new Eccezione("La modifica dello stato di approvazione di un annuncio può essere svolto solo da un amministratore.");
        }
        $this->db_instance->connect();
        $function_name_and_params = "admin_edit_stato_approvazione_annuncio($id_annuncio, $stato_approvazione)";

        return intval($this->db_instance->queryFunction($function_name_and_params));
    }
    
    /**
     * @return array restituisce un array di istanze della classe Annuncio che devono essere approvati
     * @throws Eccezione
     */
    public function adminGetAnnunci(): array {
        if(get_class($this->auth_user) !== "Amministratore") {
            throw new Eccezione("Il reperimento degli annunci da approvare può essere svolto solo da un amministratore.");
        }
        $this->db_instance->connect();
        $procedure_name_and_params = "admin_get_annunci()";
        $lista_annunci = $this->db_instance->queryProcedure($procedure_name_and_params);

        foreach($lista_annunci as $i => $assoc_annuncio) {
            $lista_annunci[$i] = new Annuncio(
                intval($assoc_annuncio['id_annuncio']),
                $assoc_annuncio['titolo'],
                    intval($assoc_annuncio['stato_approvazione'])
            );
        }
        return $lista_annunci;
    }
    
    /**
     * @param $username_or_mail string username o la mail con il quale amministratore si è autenticato
     * @param $password string password dell'amministratore
     * @return Amministratore l'istanza della classe Amministratore con i dati dell'amministratore
     * @throws Eccezione
     */
    public function adminLogin($username_or_mail, $password): Amministratore {
        $this->db_instance->connect();
        $procedure_name_and_params = "admin_login(\"$username_or_mail\", \"$password\")";
        $admin = $this->db_instance->queryProcedure($procedure_name_and_params);

        if(count($admin) == 1) {
            return new Amministratore(
                intval($admin[0]['id_amministratore']),
                $admin[0]['user_name'],
                $admin[0]['mail']
            );
        } else {
            return NULL;
        }
    }
}