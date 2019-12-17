<?php
require_once ($_SERVER["DOCUMENT_ROOT"])."/php/CheckMethods.php";

class Commento {
    private $data_pubblicazione;
    private $titolo;
    private $commento;
    private $votazione;
    private $id_prenotazione;

    private function __construct() {}

    public static function build() {
        return new Commento();
    }

    public function getDataPubblicazione(): string {
        return $this->data_pubblicazione;
    }
    
    public function getTitolo() {
        return $this->titolo;
    }
    
    public function getCommento(): string {
        return $this->commento;
    }
    
    public function getVotazione(): int {
        return $this->votazione;
    }
    
    /**
     * @param string $data_pubblicazione formato della date deve essere: aaaa/mm/gg oppure aa/mm/gg
     * @throws Eccezione se $data_pubblicazione non è nel formato
     */
    public function setDataPubblicazione($data_pubblicazione): void {
        if (checkIsValidDate($data_pubblicazione)) {
            $this->data_pubblicazione = $data_pubblicazione;
        } else {
            throw new Eccezione(htmlentities("La data di pubblicazione non è valida."));
        }
    }
    
    /**
     * @param string $titolo
     * @throws Eccezione se $titolo supera la lunghezza massima consentita
     */
    public function setTitolo($titolo): void {
        $trim_title = trim(htmlentities($titolo));
        if (checkStringMaxLen($trim_title, DataConstraints::commenti["titolo"])) {
            $this->titolo = $trim_title;
        } else {
            throw new Eccezione(htmlentities("La lunghezza del titolo supera il limite consentito."));
        }
    }
    
    /**
     * @param string $commento
     * @throws Eccezione se $commento supera la lunghezz massima consentita
     */
    public function setCommento($commento): void {
        $trim_com = trim(htmlentities($commento));
        if (checkStringMaxLen($trim_com, DataConstraints::commenti["commento"])) {
            $this->commento = $trim_com;
        } else {
            throw new Eccezione(htmlentities("La lunghezza del commento supera il limite consentito."));
        }
    }
    
    /**
     * @param string $votazione
     * @throws Eccezione se $votazione non è un intero e non è compreso fra 0 e 5, estremi inclusi
     */
    public function setVotazione($votazione): void {
        if (is_int($votazione) and $votazione >= 0 and $votazione <= 5) {
            $this->votazione = $votazione;
        } else {
            throw new Eccezione(htmlentities("Il voto inserito non è valido."));
        }
    }

    public function getIdPrenotazione(): int {
        return $this->id_prenotazione;
    }

    /**
     * @param int $id
     * @throws Eccezione se $id non è un intero positivo
     */
    public function setIdPrenotazione($id): void {
        if (is_int($id) and $id > 0) {
            $this->id_prenotazione = $id;
        } else {
            throw new Eccezione(htmlentities("L'ID della prenotazione non è valido."));
        }
    }
}