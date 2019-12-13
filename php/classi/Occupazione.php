<?php

require_once ($_SERVER["DOCUMENT_ROOT"])."/php/CheckMethods.php";

class Occupazione {
    private $id_occupazione;
    private $utente; //indica il guest che ha la prenotazione, quindi contiene il suo ID
    private $annuncio; // è il primary key dell'annuncio.
    private $prenotazione_guest; // 1 indica che è una prenotazione, 0 è settato dall'host
    private $num_ospiti;
    private $data_inizio;
    private $data_fine;
    
    public function __construct($id, $utente, $annuncio, $prenotazione_guest, $num_ospiti, $dataI, $dataF) {
        if (!checkDateBeginAndEnd($dataI, $dataF)) {        
            throw new Eccezione(htmlentities("Le date di inizio e di fine non sono valide."));
        }
        $this->setIdOccupazione($id);
        $this->setUtente($utente);
        $this->setAnnuncio($annuncio);
        $this->setPrenotazioneGuest($prenotazione_guest);
        $this->setNumOspiti($num_ospiti);
        $this->setDataInizio($dataI);
        $this->setDataFine($dataF);
    }
    
    public function setIdOccupazione($id_occupazione): void {
        if (is_int($id_occupazione) and $id_occupazione > 0) {
            $this->id_occupazione = $id_occupazione;
        } else {
            throw new Eccezione(htmlentities("L'ID dell'occupazione non è valido."));
        }
    }
    
    public function setUtente($utente): void {
        if (is_int($utente) and $utente > 0) {
            $this->utente = $utente;
        } else {
            throw new Eccezione(htmlentities("L'ID dell'utente non è valido."));
        }
    }
    
    public function setAnnuncio($annuncio): void {
        if (is_int($annuncio) and $annuncio > 0) {
            $this->annuncio = $annuncio;
        } else {
            throw new Eccezione(htmlentities("L'ID dell'annuncio non è valido!"));
        }
    }
    
    public function setPrenotazioneGuest($prenotazione_guest): void {
        if (is_bool($prenotazione_guest)) {
            $this->prenotazione_guest = $prenotazione_guest;
        } else {
            throw new Eccezione(htmlentities("Il flag di controllo se è prenotazione non è valido."));
        }
    }
    
    public function setNumOspiti($num_ospiti): void {
        if (is_int($num_ospiti) and $num_ospiti <= DataConstraints::occupazioni["num_ospiti"]) {
            $this->num_ospiti = $num_ospiti;
        } else {
            throw new Eccezione(htmlentities("Il numero degli ospiti non è valido."));
        }
    }
    
    public function setDataInizio($data_inizio): void {
        if(is_string($data_inizio) && checkIsValidDate($data_inizio)) {
            $this->data_inizio = $data_inizio;
        } else {
            throw new Eccezione(htmlentities("La data di inizio non è valida."));
        }
    }
    
    public function setDataFine($data_fine): void {
        if(is_string($data_fine) && checkIsValidDate($data_fine)) {
            $this->data_fine = $data_fine;
        } else {
            throw new Eccezione(htmlentities("La data di fine non è valida."));
        }
    }
    
    public function getIdOccupazione(): int {
        return $this->id_occupazione;
    }
    
    public function getUtente(): int {
        return $this->utente;
    }
    
    public function getAnnuncio(): int {
        return $this->annuncio;
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