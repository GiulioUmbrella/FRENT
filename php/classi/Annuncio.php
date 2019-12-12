<?php
require_once "../CheckMethods.php";
require_once "DataConstraints.php";

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
    private $stato_approvazione;// 0 = NVNA / VA = 1 / VNA = 2 (per le sigle guardare analisirequisiti.md)
    private $bloccato;
    private $max_ospiti;
    private $prezzo_notte;
    
    /**
     * Costruttore di annuncio
     * @param int $id_annuncio
     * @param string $titolo
     * @param int $stato_approvazione
     * @param string $descrizione
     * @param string $img_anteprima
     * @param string $indirizzo
     * @param string $citta
     * @param int $max_ospiti
     * @param float $prezzo_notte
     * @throws Eccezione
     */
    public function __construct(
        $id_annuncio,
        $titolo,
        $stato_approvazione,
        $descrizione = "",
        $img_anteprima = "house_image.png",
        $indirizzo = "",
        $citta = "",
        $host = 0,
        $bloccato = 0,
        $max_ospiti = 1,
        $prezzo_notte = 0.0
    ) {
        $this->setIdAnnuncio($id_annuncio);
        $this->setTitolo($titolo);
        $this->setDescrizione($descrizione);
        $this->setImgAnteprima($img_anteprima);
        $this->setStatoApprovazione($stato_approvazione);
        $this->setIndirizzo($indirizzo);
        $this->setCitta($citta);
        $this->setHost($host);
        $this->setMaxOspiti($max_ospiti);
        $this->setPrezzoNotte($prezzo_notte);
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
    
    public function getHost(): int {
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
    
    public function setBloccato($bloccato): void {
        if (is_bool($bloccato)) {
            $this->bloccato = $bloccato;
        } else {
            throw new Exception(htmlentities("Il valore di bloccato non è valido."));
        }
    }
    
    /**
     * @param $id_annuncio int
     * @throws Eccezione
     */
    public function setIdAnnuncio($id_annuncio): void {
        if (is_int($id_annuncio) and $id_annuncio > 0) {
            $this->id_annuncio = $id_annuncio;
        } else {
            throw new Eccezione(htmlentities("L'ID annuncio non è valido"));
        }
    }
    
    public function setTitolo($titolo): void {
        if (is_string($titolo) && checkStringMaxLen(trim($titolo), DataConstraints::annunci["titolo"])) {
            $this->titolo = trim($titolo);
        } else {
            throw new Eccezione(htmlentities("Il titolo non è valido."));
        }
    }
    
    public function setDescrizione($descrizione): void {
        if (is_string($descrizione) && checkStringMaxLen(trim($descrizione), DataConstraints::annunci["descrizione"]) ) {
            $this->descrizione = trim($descrizione);
        } else {
            throw new Eccezione(htmlentities("La descrizione non è valida."));
        }
    }
    
    public function setImgAnteprima($img_anteprima): void {
        if (is_string($img_anteprima) && checkStringMaxLen(trim($img_anteprima), DataConstraints::annunci["img_anteprima"]))
            $this->img_anteprima = trim($img_anteprima);
        else {
            throw new Eccezione(htmlentities("Il nome dell'immagine di anteprima non è valido."));
        }
    }
    
    public function setIndirizzo($indirizzo): void {
        if (is_string($indirizzo) && checkStringMaxLen(trim($img_anteprima), DataConstraints::annunci["indirizzo"]))
            $this->indirizzo = trim($indirizzo);
        else {
            throw new Eccezione(htmlentities("Il n non è valido."));
        }
    }
    
    public function setCitta($citta): void {
        if (is_string($citta) ||
        (checkStringNoNumber($citta) and checkStringMaxLen(trim($citta), DataConstraints::annunci["citta"]))
        ) {
            $this->citta = trim($citta);
        } else {
            throw new Eccezione(htmlentities("Il nome della città non è valido."));
        }
    }
    
    public function setHost($host): void {
        if (is_int($host) and $host >= 0) {
            $this->host = $host;
        } else {
            throw new Eccezione(htmlentities("L'ID dell'host non è valido."));
        }
    }
    
    public function setStatoApprovazione($stato_approvazione): void {
        if (is_int($stato_approvazione) and $stato_approvazione >= 0 && $stato_approvazione <= 2) {
            $this->stato_approvazione = $stato_approvazione;
        } else {
            throw new Eccezione(htmlentities("Lo stato di approvazione non è valido."));
        }
    }
    
    
    public function setMaxOspiti($max_ospiti): void {
        if (is_int($max_ospiti) and $max_ospiti > 0 and $max_ospiti < 100) {
            $this->max_ospiti = $max_ospiti;
        } else {
            throw new Eccezione(htmlentities("Il numero massimo degli ospiti non è valido!"));
        }
    }
    
    public function setPrezzoNotte($prezzo_notte): void {
        if (is_float($prezzo_notte) and $prezzo_notte >= 0.0) {
            $this->prezzo_notte = $prezzo_notte;
        } else {
            throw new Eccezione(htmlentities("Il prezzo non è valido!"));
        }
    }
}