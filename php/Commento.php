<?php

$var = new Commento("titolo","Ciao mi paice ","21-10-11",2);
echo $var->getDataPubblicazione();

class Commento
{
    private $data_pubblicazione;
    private $titolo;
    private $commento;
    private $votazione;

    public function __construct($tit, $commento,$data, $voto){
        $this->setTitolo($tit);
        $this->setCommento($commento);
        $this->setDataPubblicazione($data);
        $this->setVotazione($voto);
    }

    /**
     * @return mixed
     */
    public function getDataPubblicazione():string
    {
        return $this->data_pubblicazione;
    }


    /**
     * @param $data_pubblicazione string formato della date deve essere: aaaa/mm/gg oppure aa/mm/gg
     * @throws Eccezione
     */
    public function setDataPubblicazione($data_pubblicazione): void
    {
        //controllare che data_nascita sia una data valida

        if (date("Y-m-d",strtotime($data_pubblicazione))){
            $this->data_pubblicazione = $data_pubblicazione;
        }else{
            throw new Eccezione(htmlentities("La data di pubblicazione non è valida"));
        }
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
            $this->titolo = htmlentities($titolo);
    }

    public function getCommento():string
    {
        return $this->commento;
    }

    public function setCommento($commento): void
    {
        if (is_string($commento)){
            $this->commento = htmlentities($commento);
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
        }else{
//            lanciare l'eccezione
            throw new Eccezione("voto inserito non è valido");
        }
    }
}