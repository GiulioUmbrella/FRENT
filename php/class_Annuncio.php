<?php

require_once "./CheckMethods.php";
require_once "./class_DataConstraints.php";

class Annuncio {
    
    /**
     * Decidere come va fatto l'immagine....
     */
    private $id_annuncio;
    private $titolo;
    private $descrizione;
    private $img_anteprima;
    private $indirizzo;
    private $citta;
    private $host;
    private $stato_approvazione; // 0 = NVNA / VA = 1 / VNA = 2 (per le sigle guardare analisirequisiti.md)
    private $bloccato;
    private $max_ospiti;
    private $prezzo_notte;
    
    private function __construct() {}

    public static function build(): Annuncio {
        return new Annuncio();
    }

    public function getIdAnnuncio(): int {
        return $this->id_annuncio;
    }
    
    public function getTitolo(): string {
        return $this->titolo;
    }
    
    public function getDescrizione(): string {
        return $this->descrizione;
    }
    
    public function getImgAnteprima(): string {
        return $this->img_anteprima;
    }
    
    public function getIndirizzo(): string {
        return $this->indirizzo;
    }
    
    public function getCitta(): string {
        return $this->citta;
    }
    
    public function getIdHost(): int {
        return $this->host;
    }
    
    public function getStatoApprovazione(): int {
        return $this->stato_approvazione;
    }
    
    public function getBloccato(): bool {
        return $this->bloccato;
    }
    
    public function getMaxOspiti(): int {
        return $this->max_ospiti;
    }
    
    public function getPrezzoNotte(): float {
        return $this->prezzo_notte;
    }
    
    /**
     * @param boolean $bloccato
     * @throws Eccezione se $bloccato non è TRUE o FALSE
     */
    public function setBloccato($bloccato) {
        if (is_bool($bloccato)) {
            $this->bloccato = $bloccato;
        } else {
            throw new Eccezione("Il valore di bloccato non è nel formato valido.");
        }
    }
    
    /**
     * @param int $id_annuncio
     * @throws Eccezione se $id_annuncio non è un intero positivo
     */
    public function setIdAnnuncio($id_annuncio) {
        if (is_int($id_annuncio) and $id_annuncio > 0) {
            $this->id_annuncio = $id_annuncio;
        } else {
            throw new Eccezione("L'ID annuncio non è nel formato valido.");
        }
    }
    
    /**
     * @param string $titolo
     * @throws Eccezione se $titolo supera la lunghezza consentita
     */
    public function setTitolo($titolo) {
        if (checkStringMaxLen(trim($titolo), DataConstraints::annunci["titolo"])) {
            $this->titolo = trim($titolo);
        } else {
            throw new Eccezione("Il titolo supera la lunghezza consentita.");
        }
    }
    
    /**
     * @param string $descrizione
     * @throws Eccezione se $descrizione supera la lunghezza consentita
     */
    public function setDescrizione($descrizione) {
        if (checkStringMaxLen(trim($descrizione), DataConstraints::annunci["descrizione"])) {
            $this->descrizione = trim($descrizione);
        } else {
            throw new Eccezione("La descrizione supera la lunghezza consentita.");
        }
    }
    
    /**
     * @param string $img_anteprima
     * @throws Eccezione se $img_anteprima supera la lunghezza consentita
     */
    public function setImgAnteprima($img_anteprima) {
        if (checkStringMaxLen(trim($img_anteprima), DataConstraints::annunci["img_anteprima"]))
            $this->img_anteprima = trim($img_anteprima);
        else {
            throw new Eccezione("Il nome dell'immagine di anteprima supera la lunghezza consentita.");
        }
    }
    
    /**
     * @param string $indirizzo
     * @throws Eccezione se $indirizzo supera la lunghezza consentita
     */
    public function setIndirizzo($indirizzo) {
        if (checkStringMaxLen(trim($indirizzo), DataConstraints::annunci["indirizzo"]))
            $this->indirizzo = trim($indirizzo);
        else {
            throw new Eccezione("L'indirizzo supera la lunghezza consentita.");
        }
    }
    
    /**
     * @param string $citta
     * @throws Eccezione se $citta contiene numeri o $citta supera la lunghezza consentita
     */
    public function setCitta($citta) {
        if (checkStringNoNumber($citta) && checkStringMaxLen(trim($citta), DataConstraints::annunci["citta"])) {
            $this->citta = trim($citta);
        } else {
            throw new Eccezione("Il nome della città non è nel formato valido.");
        }
    }
    
    /**
     * @param int $host
     * @throws Eccezione se $host non è un intero positivo
     */
    public function setIdHost($host) {
        if (is_int($host) and $host >= 0) {
            $this->host = $host;
        } else {
            throw new Eccezione("L'ID dell'host non è nel formato valido.");
        }
    }
    
    /**
     * @param int $stato_approvazione
     * @throws Eccezione se $stato_approvazione non è un intero e non è 0, 1, o 2
     */
    public function setStatoApprovazione($stato_approvazione) {
        if (is_int($stato_approvazione) and $stato_approvazione >= 0 && $stato_approvazione <= DataConstraints::annunci["stato_approvazione"]) {
            $this->stato_approvazione = $stato_approvazione;
        } else {
            throw new Eccezione("Lo stato di approvazione non è nel formato valido.");
        }
    }
    
    /**
     * @param int $max_ospiti
     * @throws Eccezione se $max_ospiti non è un intero da 0 o 100
     */
    public function setMaxOspiti($max_ospiti) {
        if (is_int($max_ospiti) and $max_ospiti > 0 and $max_ospiti <= DataConstraints::annunci["max_ospiti"]) {
            $this->max_ospiti = $max_ospiti;
        } else {
            throw new Eccezione("Il numero massimo degli ospiti non è nel formato valido.");
        }
    }
    
    /**
     * @param int $prezzo_notte
     * @throws Eccezione se $prezzo_notte non è numero floating point positivo
     */
    public function setPrezzoNotte($prezzo_notte) {
        if (is_float($prezzo_notte) and $prezzo_notte >= 0.0) {
            $this->prezzo_notte = $prezzo_notte;
        } else {
            throw new Eccezione("Il prezzo non è nel formato valido.");
        }
    }
}