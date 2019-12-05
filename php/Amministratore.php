<?php
class Amministratore{
    private $id_amministratore;
    private $user_name;
    private $mail;


    public function __construct($id, $un, $mail)
    {
        $this->setUserName($un);
        $this->setIdAmministratore($id);
        $this->setMail($mail);
    }

    public function setIdAmministratore($id): void{
        $this->id_amministratore = $id;
    }

    public function setUserName($username): void{
        $this->user_name = $username;
    }

    public function setMail($mail): void{
        $this->mail = $mail;
    }

    public function getIdAmministratore(){
        return $this->id_amministratore;
    }

    public function getUserName(){
        return $this->user_name;
    }
   public function getMail(): string{
        return $this->mail;
    }
}
