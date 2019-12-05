<?php


class Annuncio
{
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

    public function getIdAnnuncio():int{
        return $this->id_annuncio;
    }
    public function setIdAnnuncio($id_annuncio): void
    {
        $this->id_annuncio = $id_annuncio;
    }
    public function getTitolo():string{
        return $this->titolo;
    }
    public function setTitolo($titolo): void
    {
        $this->titolo = $titolo;
    }
    public function getDescrizione():string{
        return $this->descrizione;
    }
    public function setDescrizione($descrizione): void
    {
        $this->descrizione = $descrizione;
    }

    public function getImgAnteprima():string{
        return $this->img_anteprima;
    }
    public function setImgAnteprima($img_anteprima): void
    {
        $this->img_anteprima = $img_anteprima;
    }

    public function getIndirizzo():string{
        return $this->indirizzo;
    }

    public function setIndirizzo($indirizzo): void
    {
        $this->indirizzo = $indirizzo;
    }

    public function getCitta():string{
        return $this->citta;
    }

    public function setCitta($citta): void
    {
        $this->citta = $citta;
    }

    public function getHost():int{
        return $this->host;
    }
    public function setHost($host): void
    {
        $this->host = $host;
    }
    public function getStatoApprovazione():int{
        return $this->stato_approvazione;
    }

    public function setStatoApprovazione($stato_approvazione): void{
        $this->stato_approvazione = $stato_approvazione;
    }

    public function getBloccato():bool{
        return $this->bloccato;
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function setBloccato($bloccato): void{
        if(is_bool($bloccato)){
            $this->bloccato = $bloccato;
        }
        else {
            throw new Exception();
        }
    }

    public function getMaxOspiti():int{
        return $this->max_ospiti;
    }

    public function setMaxOspiti($max_ospiti): void{
        $this->max_ospiti = $max_ospiti;
    }

    public function getPrezzoNotte():float{
        return $this->prezzo_notte;
    }

    public function setPrezzoNotte($prezzo_notte): void{
        $this->prezzo_notte = $prezzo_notte;
    }


}