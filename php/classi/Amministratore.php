<?php
require_once ($_SERVER["DOCUMENT_ROOT"])."/php/CheckMethods.php";
require_once "DataConstraints.php";

class Amministratore {
    private $id_amministratore;
    private $user_name;
    private $mail;

    private function __construct() {}

    public static function build(): Amministratore {
        return new Amministratore();
    }

    public function setIdAmministratore($id) {
        if (is_int($id) and $id > 0) {
            $this->id_amministratore = $id;
        } else {
            throw new Eccezione(htmlentities("L'ID inserito non è valido."));
        }
    }
    
    public function setUserName($username) {
        $trim_un = trim($username);
        if (checkStringContainsNoSpace($trim_un) &&
            checkStringMaxLen($trim_un, DataConstraints::amministratori["user_name"])
        ) {
            $this->user_name = $trim_un;
        } else {
            throw new Eccezione(htmlentities("L'username inserito non è valido."));
        }
    }
    
    public function setMail($mail) {
        $trim_mail = trim($mail);
        if (checkIsValidMail($trim_mail, DataConstraints::amministratori["mail"])) {
            $this->mail = $trim_mail;
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
