<?php

require_once "../CheckMethods.php";

class Commento {
    private $data_pubblicazione;
    private $titolo;
    private $commento;
    private $votazione;
    private $id_prenotazione;
    
    public function __construct($tit, $commento, $data, $voto, $prenot) {
        $this->setTitolo($tit);
        $this->setCommento($commento);
        $this->setDataPubblicazione($data);
        $this->setVotazione($voto);
        $this->setIdPrenotazione($prenot);
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
     * @throws Eccezione
     */
    public function setDataPubblicazione($data_pubblicazione): void {
        if (checkIsValidDate($data_pubblicazione)) {
            $this->data_pubblicazione = $data_pubblicazione;
        } else {
            throw new Eccezione(htmlentities("La data di pubblicazione non è valida."));
        }
    }
    
    public function setTitolo($titolo): void {
        if (is_string($titolo) and checkStringMaxLen(trim($titolo), DataConstraints::commenti["titolo"])) {
            $this->titolo = trim($titolo);
        } else {
            throw new Eccezione(htmlentities("La lunghezza del titolo supera il limite consentito."));
        }
        $this->titolo = htmlentities($titolo);
    }
    
    public function setCommento($commento): void {
        if (is_string($commento) and checkStringMaxLen(trim($commento), DataConstraints::commenti["commento"])) {
            $this->commento = trim($commento);
        } else {
            throw new Eccezione(htmlentities("La lunghezza del commento supera il limite consentito."));
        }
    }
    
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

    public function setIdPrenotazione($id): void {
        if (is_int($id) and $id > 0) {
            $this->id_prenotazione = $id;
        } else {
            throw new Eccezione(htmlentities("L'ID della prenotazione non è valido."));
        }
    }
}