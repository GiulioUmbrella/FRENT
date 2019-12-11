<?php

require_once "../CheckMethods.php";

class Commento {
    private $data_pubblicazione;
    private $titolo;
    private $commento;
    private $votazione;
    
    public function __construct($tit, $commento, $data, $voto) {
        $this->setTitolo($tit);
        $this->setCommento($commento);
        $this->setDataPubblicazione($data);
        $this->setVotazione($voto);
    }
    
    /**
     * @return mixed
     */
    public function getDataPubblicazione(): string {
        return $this->data_pubblicazione;
    }
    
    
    /**
     * @return mixed
     */
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
     * @param $data_pubblicazione string formato della date deve essere: aaaa/mm/gg oppure aa/mm/gg
     * @throws Eccezione
     */
    public function setDataPubblicazione($data_pubblicazione): void {
        if (checkIsValidDate($data_pubblicazione)) {
            $this->data_pubblicazione = $data_pubblicazione;
        } else {
            throw new Eccezione(htmlentities("La data di pubblicazione non è valida"));
        }
    }
    
    public function setTitolo($titolo): void {
        if (is_string($titolo) and checkStringLen($titolo, 64)) {
            $this->$titolo = $titolo;
        } else {
            throw new Eccezione(htmlentities("Il titolo è troppo lungo!!"));
        }
        $this->titolo = htmlentities($titolo);
    }
    
    public function setCommento($commento): void {
        if (is_string($commento) and checkStringLen($commento, 512)) {
            $this->commento = $commento;
        } else {
            throw new Eccezione(htmlentities("Il commento è troppo lungo!!"));
        }
    }
    
    public function setVotazione($votazione): void {
        if ($votazione >= 0 && $votazione <= 5) {
            $this->votazione = $votazione;
        } else {
//            lanciare l'eccezione
            throw new Eccezione("voto inserito non è valido");
        }
    }
}