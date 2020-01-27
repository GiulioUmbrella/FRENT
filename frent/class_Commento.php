<?php
require_once "./CheckMethods.php";
require_once "./class_Utente.php";

class Commento {
    private $data_pubblicazione;
    private $titolo;
    private $commento;
    private $valutazione;
    private $id_prenotazione;
    private $utente; // istanza di Utente che ha pubblicato l'account

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
    
    public function getValutazione(): int {
        return $this->valutazione;
    }
    
    /**
     * @param string $data_pubblicazione formato della date deve essere: aaaa/mm/gg oppure aa/mm/gg
     * @throws Eccezione se $data_pubblicazione non è nel formato
     */
    public function setDataPubblicazione($data_pubblicazione) {
        if (checkIsValidDate($data_pubblicazione)) {
            $this->data_pubblicazione = $data_pubblicazione;
        } else {
            throw new Eccezione("La data di pubblicazione non è nel formato valido.");
        }
    }
    
    /**
     * @param string $titolo
     * @throws Eccezione se $titolo supera la lunghezza massima consentita
     */
    public function setTitolo($titolo) {
        $trim_title = trim(htmlentities($titolo));
        if (checkStringMaxLen($trim_title, DataConstraints::commenti["titolo"])) {
            $this->titolo = $trim_title;
        } else {
            throw new Eccezione("La lunghezza del titolo supera il limite consentito.");
        }
    }
    
    /**
     * @param string $commento
     * @throws Eccezione se $commento supera la lunghezza massima consentita
     */
    public function setCommento($commento) {
        $trim_com = trim(htmlentities($commento));
        if (checkStringMaxLen($trim_com, DataConstraints::commenti["commento"])) {
            $this->commento = $trim_com;
        } else {
            throw new Eccezione("La lunghezza del commento supera il limite consentito.");
        }
    }
    
    /**
     * @param string $valutazione
     * @throws Eccezione se $valutazione non è un intero e non è compreso fra 0 e 5, estremi inclusi
     */
    public function setValutazione($valutazione) {
        if (is_int($valutazione) and $valutazione >= 0 and $valutazione <= 5) {
            $this->valutazione = $valutazione;
        } else {
            throw new Eccezione("Il voto inserito non è nel formato valido.");
        }
    }

    public function getIdPrenotazione(): int {
        return $this->id_prenotazione;
    }

    /**
     * @param int $id
     * @throws Eccezione se $id non è un intero positivo
     */
    public function setIdPrenotazione($id) {
        if (is_int($id) and $id > 0) {
            $this->id_prenotazione = $id;
        } else {
            throw new Eccezione("L'ID della prenotazione non è nel formato valido.");
        }
    }

    /**
     * @param Utente $utente
     * @throws Eccezione se $utente non è un'istanza di classe Utente e se i suoi campi username e img_profilo sono vuoti
     */
    public function setUtente($utente) {
        if(get_class($utente) === "Utente" && strlen($utente->getImgProfilo()) > 0 && strlen($utente->getUserName()) > 0) {
            $this->utente = $utente;
        } else {
            throw new Eccezione("L'istanza di utente non è nel formato valido.");
        }
    }

    public function getUtente(): Utente {
        return $this->utente;
    }
}