<?php


class Commento
{
    private $data_pubblicazione;
    private $titolo;
    private $commento;
    private $votazione;

    /**
     * @return mixed
     */
    public function getDataPubblicazione():DateTime
    {
        return $this->data_pubblicazione;
    }

    /**
     * @param mixed $data_pubblicazione
     */
    public function setDataPubblicazione($data_pubblicazione): void
    {
        $this->data_pubblicazione = $data_pubblicazione;
    }

    /**
     * @return mixed
     */
    public function getTitolo()
    {
        return $this->titolo;
    }

    public function setTitolo($titolo): void
    {
        if (is_string($titolo)){
            $this->commento = $titolo;
        }else{
            // lanciare l'eccezione
        }
    }

    public function getCommento():string
    {
        return $this->commento;
    }

    public function setCommento($commento): void
    {
        if (is_string($commento)){
            $this->commento = $commento;
        }else{
            // lanciare l'eccezione
        }

    }

    public function getVotazione():int
    {
        return $this->votazione;
    }

    public function setVotazione($votazione): void
    {
        if ($votazione>=0 && $votazione <=5){
            $this->votazione = $votazione;
        }
        else{
//            lanciare l'eccezione
        }
    }
}