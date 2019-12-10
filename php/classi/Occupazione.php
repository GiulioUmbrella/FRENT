<?php

require "CheckMethods.php";

class Occupazione
{
    private $id_occupazione;
    private $utente; //indica il guest che ha la prenotazione, quindi contiene il suo ID
    private $annuncio; // è il primary key dell'annuncio.
    private $prenotazione_guest; // 1 indica che è una prenotazione, 0 è settato dall'host
    private $num_ospiti;
    private $data_inizio;
    private $data_fine;

    public function __construct($id, $utente,$annuncio,$prenotazione_guest, $num_ospiti, $dataI, $dataF)
    {
        if ($dataI>$dataF){
            throw new Eccezione(htmlentities("La data di inizio è maggiore della data di fine!"));
        }
        $this->setIdOccupazione($id);
        $this->setUtente($utente);
        $this->setAnnuncio($annuncio);
        $this->setPrenotazioneGuest($prenotazione_guest);
        $this->setNumOspiti($num_ospiti);

        if (checkValidDate($dataI) and checkValidDate($dataF) and checkDateBeginAndEnd($dataI, $dataF)){
            $this->setDataInizio($dataI);
            $this->setDataFine($dataF);
        }else{
            throw new Eccezione(htmlentities("Le date di inizio e di fine non sono valide!!"));
        }
    }

    public function setIdOccupazione($id_occupazione): void
    {
        if (is_int($id_occupazione) and $id_occupazione>0){
            $this->id_occupazione = $id_occupazione;
        }else{
            throw new Eccezione(htmlentities("ID dell'occupazione non è valido!"));
        }
    }
    public function setUtente($utente): void
    {
        if (is_int($utente) and $utente>0){
            $this->utente = $utente;
        }else{
            throw new Eccezione(htmlentities("ID dell'utente non è valido!"));
        }
    }
    public function setAnnuncio($annuncio): void
    {
        if (is_int($annuncio) and $annuncio>0){
            $this->annuncio = $annuncio;
        }else{
            throw new Eccezione(htmlentities("ID dell'annuncio non è valido!"));
        }
    }

    public function setPrenotazioneGuest($prenotazione_guest): void
    {
        if (is_int($prenotazione_guest) and ($prenotazione_guest==0 or $prenotazione_guest==1) ){
            $this->$prenotazione_guest = $prenotazione_guest;
        }else{
            throw new Eccezione(htmlentities("Lo stato della prenotazione non è valido!"));
        }
    }

    public function setNumOspiti($num_ospiti): void
    {
        if (is_int($num_ospiti) and $num_ospiti>0 and $num_ospiti<99 ){
            $this->num_ospiti = $num_ospiti;
        }else{
            throw new Eccezione(htmlentities("Il numero degli ospiti non è valido!"));
        }
    }

    // non hanno bisogno di controllo perchè è stato fatto nel main
    public function setDataInizio($data_inizio): void
    {
        $this->data_inizio= $data_inizio;
    }

    public function setDataFine($data_fine): void
    {
        $this->data_fine = $data_fine;
    }
    public function getIdOccupazione():int
    {
        return $this->id_occupazione;
    }

    public function getUtente()
    {
        return $this->utente;
    }
    public function getAnnuncio():int
    {
        return $this->annuncio;
    }
    public function getPrenotazioneGuest():bool {
        return $this->prenotazione_guest;
    }
    public function getNumOspiti():int
    {
        return $this->num_ospiti;
    }
    public function getDataInizio():DateTime
    {
        return $this->data_inizio;
    }

    public function getDataFine():DateTime
    {
        return $this->data_fine;
    }

}