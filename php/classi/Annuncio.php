<?php
require "CheckMethods.php";

class Annuncio
{

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

    public function getIdAnnuncio():int{
        return $this->id_annuncio;
    }
    public function getTitolo():string{
        return $this->titolo;
    }
    public function getDescrizione():string{
        return $this->descrizione;
    }
    public function getImgAnteprima():string{
        return $this->img_anteprima;
    }

    public function getIndirizzo():string{
        return $this->indirizzo;
    }

    public function getCitta():string{
        return $this->citta;
    }

    public function getHost():int{
        return $this->host;
    }

    public function getStatoApprovazione():int{
        return $this->stato_approvazione;
    }
    public function getBloccato():bool{
        return $this->bloccato;
    }

    public function setBloccato($bloccato): void{
        if(is_bool($bloccato)){
            $this->bloccato = $bloccato;
        }
        else {
            throw new Exception();
        }
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function getMaxOspiti():int{
        return $this->max_ospiti;
    }
    public function getPrezzoNotte():float{
        return $this->prezzo_notte;
    }

    public function setIdAnnuncio($id_annuncio): void
    {
        if (is_int($id_annuncio) and $id_annuncio>0){
            $this->id_annuncio = $id_annuncio;
        }else{
            throw new Eccezione(htmlentities("Id annuncio non valido"));
        }
    }
    public function setTitolo($titolo): void
    {
        if (checkStringLen($titolo,32)){
            $this->titolo = htmlentities($titolo);
        }else{
            throw new Eccezione(htmlentities("Il titolo inserito è troppo lungo."));
        }
    }
    public function setDescrizione($descrizione): void
    {
        if (checkStringLen($descrizione,512)){
            $this->descrizione = htmlentities($descrizione);
        }else{
            throw new Eccezione(htmlentities("La descrizione è troppo lunga!"));
        }
    }
    public function setImgAnteprima($img_anteprima): void
    {
        $this->img_anteprima = htmlentities($img_anteprima);
    }

    public function setIndirizzo($indirizzo): void
    {
        $this->indirizzo = htmlentities($indirizzo);
    }

    public function setCitta($citta): void
    {
        if (checkStringNoNumber($citta)){
            $this->citta = htmlentities($citta);
        }else{
            throw new Eccezione(htmlentities("Il nome della città non è valido!"));
        }
    }
    public function setHost($host): void
    {
        if($host>0){
            $this->host = $host;
        }else{
            throw new Eccezione(htmlentities("Id host non valido!"));
        }
    }

    public function setStatoApprovazione($stato_approvazione): void{
        if (is_int($stato_approvazione) and(
            $stato_approvazione==0 or $stato_approvazione==1 or $stato_approvazione==2
            )){
            $this->stato_approvazione = $stato_approvazione;
        }else{
            throw new Eccezione(htmlentities("Lo stato che stai tentando di inserire non va bene!!"));
        }
    }


    public function setMaxOspiti($max_ospiti): void{
       if (is_int($max_ospiti) and $max_ospiti>0 and $max_ospiti<100){
           $this->max_ospiti = $max_ospiti;
       }else{
           throw new Eccezione(htmlentities("Il numero massimo degli ospiti non è valido!"));
       }
    }

    public function setPrezzoNotte($prezzo_notte): void{
        if (is_float($prezzo_notte) and $prezzo_notte>0) {
            $this->prezzo_notte = $prezzo_notte;
        }else{
            throw new Eccezione(htmlentities("Il prezzo non è valido!"));
        }
    }


}