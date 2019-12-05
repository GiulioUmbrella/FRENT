<?php


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
        $this->setIdOccupazione($id);
        $this->setUtente($utente);
        $this->setAnnuncio($annuncio);
        $this->setPrenotazioneGuest($prenotazione_guest);
        $this->setNumOspiti($num_ospiti);
        $this->setDataInizio($dataI);
        $this->setDataFine($dataF);
    }

    public function getIdOccupazione():int
    {
        return $this->id_occupazione;
    }

    public function setIdOccupazione($id_occupazione): void
    {
        $this->id_occupazione = $id_occupazione;
    }
    public function getUtente()
    {
        return $this->utente;
    }
    public function setUtente($utente): void
    {
        $this->utente = $utente;
    }
    public function getAnnuncio():int
    {
        return $this->annuncio;
    }
    public function setAnnuncio($annuncio): void
    {
        $this->annuncio = $annuncio;
    }
    public function getPrenotazioneGuest():bool {
        return $this->prenotazione_guest;
    }

    public function setPrenotazioneGuest($prenotazione_guest): void
    {
        $this->prenotazione_guest = $prenotazione_guest;
    }
    public function getNumOspiti():int
    {
        return $this->num_ospiti;
    }

    public function setNumOspiti($num_ospiti): void
    {
        $this->num_ospiti = $num_ospiti;
    }

    public function getDataInizio():DateTime
    {
        return $this->data_inizio;
    }

    public function setDataInizio($data_inizio): void
    {
        $this->data_inizio = $data_inizio;
    }

    public function getDataFine():DateTime
    {
        return $this->data_fine;
    }

    public function setDataFine($data_fine): void
    {
        $this->data_fine = $data_fine;
    }

}