<?php


class Utente
{
    private $id_utente;
    private $nome;
    private $cognome;
    private $user_name;
    private $mail;
    private $data_nascita;
    private $img_profilo;
    private $telefono;

    public function __construct($id, $n,$cog, $username, $mail, $data, $img, $telefono) {
        $this->setUserName($username);
        $this->setIdUtente($id);
        $this->setNome($n);
        $this->setCognome($cog);
        $this->setMail($mail);
        $this->setDataNascita($data);
        $this->setImgProfilo($img);
        $this->setTelefono($telefono);
    }

    public function getIdUtente()
    {
        return $this->id_utente;
    }

    public function setIdUtente($id_utente): void
    {
        //controllare che id_utente sia numerico
        $this->id_utente = $id_utente;
    }
    public function getNome()
    {
        return $this->nome;
    }
    public function setNome($nome): void
    {//controllare che il nome sia valida
        $this->nome = $nome;
    }
    public function getCognome()
    {
        return $this->cognome;
    }
    public function setCognome($cognome): void
    {
        //controllare che cognome non abbia numeri dentro
        $this->cognome = $cognome;
    }

    public function getUserName()
    {
        return $this->user_name;
    }
    public function setUserName($user_name): void
    {
        $this->user_name = htmlentities($user_name);
    }
    public function getMail()
    {
        return $this->mail;
    }
    public function setMail($mail): void
    {// controlalre che la mail sia valida
        if (is_string($mail) and  filter_var($mail, FILTER_VALIDATE_EMAIL)){
            $this->mail = htmlentities($mail);
        }else{
            //gestire l'eccezione
        }
    }

    public function getDataNascita()
    {
        return $this->data_nascita;
    }

    public function setDataNascita($data_nascita): void
    {
        //controlalre che data_nascita sia una data valida
        $this->data_nascita = $data_nascita;
    }

    public function getImgProfilo()
    {
        return $this->img_profilo;
    }

    public function setImgProfilo($img_profilo): void
    {
        $this->img_profilo = $img_profilo;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }

    public function setTelefono($telefono): void{
        // fare i controlli che il parametro contenga un numero di telefono
        if (preg_match("/^[0-9]{3}-[0-9]{4}-[0-9]{4}$/", $telefono)){
            $this->telefono = $telefono;
        }else{
            // gestore l'eccezione quando il numero di telefono non è valido.
        }
    }

}