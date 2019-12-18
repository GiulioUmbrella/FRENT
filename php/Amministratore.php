<?php
require_once("./CheckMethods.php");
require_once "./DataConstraints.php";

class Amministratore {
    private $id_amministratore;
    private $user_name;
    private $mail;
    private $password;

    private function __construct() {}

    public static function build(): Amministratore {
        return new Amministratore();
    }

    public function setIdAmministratore($id) {
        if (is_int($id) and $id > 0) {
            $this->id_amministratore = $id;
        } else {
            throw new Eccezione("L'ID inserito non è nel formato valido.");
        }
    }
    
    public function setUsername($username) {
        $trim_un = trim($username);
        if (checkStringContainsNoSpace($trim_un) &&
            checkStringMaxLen($trim_un, DataConstraints::amministratori["user_name"])
        ) {
            $this->user_name = $trim_un;
        } else {
            throw new Eccezione("L'username inserito non è nel formato valido.");
        }
    }
    
    public function setMail($mail) {
        $trim_mail = trim($mail);
        if (checkIsValidMail($trim_mail, DataConstraints::amministratori["mail"])) {
            $this->mail = $trim_mail;
        } else {
            throw new Eccezione("L'email inserita non è nel formato valido.");
        }
    }
    
    public function getIdAmministratore(): int {
        return $this->id_amministratore;
    }

    public function getUsername(): string {
        return $this->user_name;
    }

    public function getMail(): string {
        return $this->mail;
    }
    
    public function setPassword($password) {
        if(checkStringMaxLen($password, DataConstraints::amministratori["password"])) {
            $this->password = $password;
        } else {
            throw new Eccezione("La password inserita supera la lunghezza consentita.");
        }
    }
}
