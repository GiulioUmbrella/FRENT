<?php

require_once "./CheckMethods.php";

class Occupazione {
    private $id_occupazione;
    private $id_utente; //indica il guest che ha la prenotazione, quindi contiene il suo ID
    private $id_annuncio; // è il primary key dell'annuncio.
    private $prenotazione_guest; // 1 indica che è una prenotazione, 0 è settato dall'host
    private $num_ospiti;
    private $data_inizio;
    private $data_fine;

    private function __construct() {}

    public static function build(): Occupazione {
        return new Occupazione();
    }
    
    /**
     * @param int $id_occupazione
     * @throws Eccezione se $id_occupazione non è un intero positivo
     */
    public function setIdOccupazione($id_occupazione) {
        if (is_int($id_occupazione) and $id_occupazione > 0) {
            $this->id_occupazione = $id_occupazione;
        } else {
            throw new Eccezione("L'ID dell'occupazione non è nel formato valido.");
        }
    }
    
    /**
     * @param int $id_utente
     * @throws Eccezione se $utente non è un intero positivo
     */
    public function setIdUtente($id_utente) {
        if (is_int($id_utente) and $id_utente > 0) {
            $this->id_utente = $id_utente;
        } else {
            throw new Eccezione("L'ID dell'utente non è nel formato valido.");
        }
    }
    
    /**
     * @param int $id_annuncio
     * @throws Eccezione se $annuncio non è un intero positivo
     */
    public function setIdAnnuncio($id_annuncio) {
        if (is_int($id_annuncio) and $id_annuncio > 0) {
            $this->id_annuncio = $id_annuncio;
        } else {
            throw new Eccezione("L'ID dell'annuncio non è nel formato valido.");
        }
    }
    
    /**
     * @param boolean $prenotazione_guest
     * @throws Eccezione se $prenotazione_guest non è TRUE o FALSE
     */
    public function setPrenotazioneGuest($prenotazione_guest) {
        if (is_bool($prenotazione_guest)) {
            $this->prenotazione_guest = $prenotazione_guest;
        } else {
            throw new Eccezione("Il flag di controllo se l'occupazione è una prenotazione non è nel formato valido.");
        }
    }
    
    /**
     * @param int $num_ospiti
     * @throws Eccezione se $num_ospiti non è un intero oppure supera il massimo consentito
     */
    public function setNumOspiti($num_ospiti) {
        if (is_int($num_ospiti) and $num_ospiti <= DataConstraints::occupazioni["num_ospiti"]) {
            $this->num_ospiti = $num_ospiti;
        } else {
            throw new Eccezione("Il numero degli ospiti non è valido.");
        }
    }
    
    /**
     * @param string $data_inizio
     * @throws Eccezione se $data_inizio non è una stringa rappresentante una data valida
     */
    public function setDataInizio($data_inizio) {
        if(checkIsValidDate($data_inizio)) {
            $this->data_inizio = $data_inizio;
        } else {
            throw new Eccezione("La data di inizio non è valida.");
        }
    }
    
    /**
     * @param string $data_fine
     * @throws Eccezione se $data_fine non è una stringa rappresentante una data valida
     */
    public function setDataFine($data_fine) {
        if(checkIsValidDate($data_fine)) {
            $this->data_fine = $data_fine;
        } else {
            throw new Eccezione("La data di fine non è valida.");
        }
    }
    
    public function getIdOccupazione(): int {
        return $this->id_occupazione;
    }
    
    public function getIdUtente(): int {
        return $this->id_utente;
    }
    
    public function getIdAnnuncio(): int {
        return $this->id_annuncio;
    }
    
    public function getPrenotazioneGuest(): bool {
        return $this->prenotazione_guest;
    }
    
    public function getNumOspiti(): int {
        return $this->num_ospiti;
    }
    
    public function getDataInizio(): string {
        return $this->data_inizio;
    }
    
    public function getDataFine(): string {
        return $this->data_fine;
    }
}