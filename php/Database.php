<?php

class Database {
    // dati per la connessione al database
    private $host_name; // string
    private $user_name; // string
    private $password; // string
    private $db_name; // string

    // riferimento a oggetto di mysqli
    private $db; // mysqli

    /// Costruisce un'istanza del gestore del database, memorizzando le credenziali, senza connettersi
    public function __construct($host_name, $user_name, $password, $db_name) {
        $this->host_name = $host_name;
        $this->user_name = $user_name;
        $this->password = $password;
        $this->db_name = $db_name;
    }

    /// Effettua la connessione al database, lancia un'eccezione se si sono verificati errori nell'apertura della connessione
    public function connect() {
        if($this->db == null) {
            $this->db = new mysqli($this->host_name, $this->user_name, $this->password, $this->db_name);
            if($this->db->connect_errno > 0) {
                throw Exception("C'è stato un errore nella connessione con il database. L'errore che si è verificato è il seguente: " . $this->db->connect_error);
            }
        }
    }

    /// Effettua la disconnessione dal database a cui si è connessi
    /// Ritorna true se la connessione è stata chiusa, false altrimenti
    public function disconnect() {
        return $this->db->close();
    }

    /// Esegue una query nel database a cui si è connessi richiamando una procedura
    /// Viene ritornato un array di hash/array associativi
    public function queryProcedure($procedure_name_and_parametres) {
        $this->db->query("CALL " . $procedure_name);
    }

    /// Esegue una query nel database a cui si è connessi richiamando una funzione
    /// Viene ritornato un un'unico valore e non un array
    public function queryFunction($function_name_and_parametres) {
        $this->db->query("SELECT " . $function_name_and_parametres);
    }
}