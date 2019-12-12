<?php
require_once ($_SERVER["DOCUMENT_ROOT"])."/TECHWEB/php/CheckMethods.php";
require_once "DataConstraints.php";

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
        if (is_int($id) and $id > 0) {
            $this->id_amministratore = $id;
        } else {
            throw new Eccezione(htmlentities("L'ID inserito non è valido."));
        }
    }
    
    public function setUserName($username): void {
        if (is_string($username) &&
            checkStringContainsNoSpace(trim($username)) &&
            checkStringMaxLen(trim($username), DataConstraints::amministratori["user_name"])
        ) {
            $this->user_name = trim($username);
        } else {
            throw new Eccezione(htmlentities("L'username inserito non è valido."));
        }
    }
    
    public function setMail($mail): void {
        if (is_string($mail) && checkIsValidMail(trim($mail), DataConstraints::amministratori["mail"])) {
            $this->mail = trim($mail);
        } else {
            throw new Eccezione(htmlentities("L'email inserita non è valida."));
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
