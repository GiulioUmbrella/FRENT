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
     * Annuncio constructor.
     * @param $id_annuncio int
     * @param null $titolo
     * @param int $stato_approvazione
     * @param null $descrizione
     * @param null $img_anteprima
     * @param null $indirizzo
     * @param null $citta
     * @param int $prezzo_notte
     * @throws Eccezione
     */
    public function __construct($id_annuncio, $titolo = NULL, $stato_approvazione = 0, $descrizione = NULL, $img_anteprima = NULL, $indirizzo = NULL, $citta = NULL, $prezzo_notte = 0.0) {
        echo "inizio costruttore annunci";
        $this->setIdAnnuncio($id_annuncio);
        echo "costruttore 1";
        $this->setTitolo($titolo);
        echo "costruttore 2";
        $this->setDescrizione($descrizione);
        echo "costruttore 3";
        $this->setImgAnteprima($img_anteprima);
        echo "costruttore 4";
        $this->setStatoApprovazione($stato_approvazione);
        echo "costruttore 5";
        $this->setIndirizzo($indirizzo);
        echo "costruttore 6";
        $this->setCitta($citta);
        echo "costruttore 7";
        $this->setPrezzoNotte($prezzo_notte);
        echo "fine costruttore annunci";
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
    
    public function setBloccato($bloccato): void {
        if (is_bool($bloccato)) {
            $this->bloccato = $bloccato;
        } else {
            throw new Exception();
        }
    }
    
    /** @noinspection PhpUnhandledExceptionInspection */
    public function getMaxOspiti(): int {
        return $this->max_ospiti;
    }
    
    public function getPrezzoNotte(): float {
        return $this->prezzo_notte;
    }
    
    /**
     * @param $id_annuncio int
     * @throws Eccezione
     */
    public function setIdAnnuncio($id_annuncio): void {
        $id_annuncio=1;
//        echo "ID =".trim($id_annuncio);
//        if (!is_int($id_annuncio))
//            echo "its NOT int";
        if (is_int($id_annuncio) and $id_annuncio > 0) {
            $this->id_annuncio = $id_annuncio;
        } else {
            throw new Eccezione(htmlentities("Id annuncio non valido"));
        }
    }
    
    public function setTitolo($titolo): void {
        if (checkStringMaxLen($titolo, DataConstraints::annunci["titolo"])) {
            $this->titolo = htmlentities($titolo);
        } else {
            throw new Eccezione(htmlentities("Il titolo inserito è troppo lungo."));
        }
    }
    
    public function setDescrizione($descrizione): void {
        if ((checkStringMaxLen($descrizione, DataConstraints::annunci["descrizione"]) or $descrizione==NULL) ) {
            $this->descrizione = htmlentities($descrizione);
        } else {
            throw new Eccezione(htmlentities("La descrizione è troppo lunga!"));
        }
    }
    
    public function setImgAnteprima($img_anteprima): void {
        if (checkStringMaxLen(trim($img_anteprima), DataConstraints::annunci["img_anteprima"]))
            $this->img_anteprima = htmlentities($img_anteprima);
        else {
            throw new Eccezione(htmlentities("Il nome del file non è valido!"));
        }
    }
    
    public function setIndirizzo($indirizzo): void {
        $this->indirizzo = htmlentities($indirizzo);
    }
    
    public function setCitta($citta): void {
        
        if ($citta==NULL or (checkStringNoNumber($citta) and checkStringMaxLen(trim($citta), DataConstraints::annunci["citta"]))
            and strlen(trim($citta)) > 0) {
            $this->citta = htmlentities($citta);
        } else {
            throw new Eccezione(htmlentities("Il nome della città non è valido!"));
        }
    }
    
    public function setHost($host): void {
        if ($host > 0) {
            $this->host = $host;
        } else {
            throw new Eccezione(htmlentities("Id host non valido!"));
        }
    }
    
    public function setStatoApprovazione($stato_approvazione): void {
        if (is_int($stato_approvazione) and (
                $stato_approvazione == 0 or $stato_approvazione == 1 or $stato_approvazione == 2
            )) {
            $this->stato_approvazione = $stato_approvazione;
        } else {
            throw new Eccezione(htmlentities("Lo stato che stai tentando di inserire non va bene!!"));
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
        if (is_float($prezzo_notte) and $prezzo_notte >= 0) {
            $this->prezzo_notte = $prezzo_notte;
        } else {
            throw new Eccezione(htmlentities("Il prezzo non è valido!"));
        }
    }
    
    
}