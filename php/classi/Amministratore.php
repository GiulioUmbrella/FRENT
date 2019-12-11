<?php
require_once "../CheckMethods.php";


class Amministratore {
    private $id_amministratore;
    private $user_name;
    private $mail;
    
    
    public function __construct($id, $un, $mail) {
        $this->setUserName($un);
        $this->setIdAmministratore($id);
        $this->setMail($mail);
    }
    
    public function setIdAmministratore($id): void {
        if ($id > 0) $this->id_amministratore = $id;
        else
            throw new Eccezione(htmlentities("ID inserito non è valido!!"));
    }
    
    public function setUserName($username): void {
        if (checkStringContainsNoSpace(trim($username))) {
            $this->user_name = $username;
        } else {
            throw new Eccezione("Username Non valida");
            
        }
    }
    
    public function setMail($mail): void {
        if (checkIsValidMail($mail, 191)) {
            $this->$mail = $mail;
        } else {
            throw new Eccezione("Email non corretta o troppo lunga!");
        }
        
    }
    
    public function getIdAmministratore(): int {
        return $this->id_amministratore;
    }
    
    public function getUserName(): string {
        return $this->user_name;
    }
    
    public function getMail(): string {
        return $this->mail;
    }
}
