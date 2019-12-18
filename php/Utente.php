<?php

require_once "./CheckMethods.php";

class Utente {
    private $id_utente;
    private $nome;
    private $cognome;
    private $user_name;
    private $mail;
    private $data_nascita;
    private $img_profilo;
    private $telefono;
    private $password;

    private function __construct() {}

    public static function build(): Utente {
        return new Utente();
    }
    
    public function getIdUtente() {
        return $this->id_utente;
    }
    
    /**
     * @param int $id_utente
     * @throws Eccezione se $id_utente non è un intero positivo
     */
    public function setIdUtente($id_utente) {
        echo "Checking id";
        if (is_int($id_utente) and $id_utente > 0) {
            $this->id_utente = $id_utente;
            echo "ID CHECKED";
        } else {
            echo "ECCezione";
            throw new Eccezione("L'ID dell'utente non è nel formato valido.");
        }
    }
    
    public function getNome() {
        return $this->nome;
    }
    
    /**
     * @param string $nome
     * @throws Eccezione se $nome contiene numeri o supera la lunghezza massima
     */
    public function setNome($nome) {
        $trim_nome = trim($nome);
        if (checkStringNoNumber($trim_nome) and checkStringMaxLen($trim_nome, DataConstraints::utenti["nome"]))
            $this->nome = $trim_nome;
        else{
            throw new Eccezione("Il nome inserito non è nel formato valido.");
        }
    }
    
    public function getCognome() {
        return $this->cognome;
    }
    
    /**
     * @param string $cognome
     * @throws Eccezione se $cognome contiene numeri o supera la lunghezza massima
     */
    public function setCognome($cognome) {
        $trim_cognome = trim($cognome);
        if (checkStringNoNumber($trim_cognome) and checkStringMaxLen($trim_cognome, DataConstraints::utenti["cognome"]))
            $this->cognome = $trim_cognome;
        else{
            throw new Eccezione("Il cognome inserito non è nel formato valido.");
        }
    }
    
    public function getUserName() {
        return $this->user_name;
    }
    
    /**
     * @param string $user_name
     * @throws Eccezione se $user_name contiene spazi e la lunghezza supera il massimo consentito
     */
    public function setUserName($user_name) {
        $trim_un = trim($user_name);
        if (checkStringContainsNoSpace($trim_un) and checkStringMaxLen($trim_un, DataConstraints::utenti["user_name"]))
            $this->user_name = $trim_un;
        else{
            throw new Eccezione("Il nome utente inserito non è nel formato valido.");
        }
    }
    
    public function getMail() {
        return $this->mail;
    }
    
    /**
     * @param string $mail
     * @throws Eccezione se $mail non rappresenta un indirizzo mail valido oppure supera la lunghezza massima consentita
     */
    public function setMail($mail) {
        $trim_mail = trim($mail);
        if (checkIsValidMail($trim_mail, DataConstraints::utenti["mail"]) and strlen($trim_mail)  > 6) {
            // 6 perché a@a.aa, e aa perché non ci sono TLD con una lettera
            $this->mail = $trim_mail;
        } else {
            throw new Eccezione("La mail inserita non è nel formato valido.");
        }
    }
    
    public function getDataNascita() {
        return $this->data_nascita;
    }
    
    /**
     * @param string $data_nascita il formato di quest stringa deve essere aaaa-mm-gg
     * @throws Eccezione se $data_nascita non è una stringa rappresentante una data valida
     */
    public function setDataNascita($data_nascita) {
        if (checkIsValidDate($data_nascita)) {
            $this->data_nascita = $data_nascita;
        } else {
            throw new Eccezione("La data di nascita inserita non è nel formato valido.");
        }
    }
    
    
    public function getImgProfilo() {
        return $this->img_profilo;
    }
    
    /**
     * @param string $img_profilo
     * @throws Eccezione se $img_profilo supera la lunghezza massima consentita
     */
    public function setImgProfilo($img_profilo) {
        $trim_img = trim($img_profilo);
        if (checkStringMaxLen($trim_img, DataConstraints::utenti["img_profilo"])) {
            $this->img_profilo = str_replace(" ", "_", $trim_img);
        } else {
            throw new Eccezione("Il path dell'immagine di profilo non è nel formato valido.");
        }
    }
    
    public function getTelefono() {
        return $this->telefono;
    }
    
    /**
     * @param string $telefono
     * @throws Eccezione se $telefono non è un numero di telefono nuovo
     */
    public function setTelefono($telefono) {
        $trim_tel = trim($telefono);
        if (checkPhoneNumber($trim_tel)) {
            $this->telefono = $trim_tel;
        } else {
            throw new Eccezione("Il numero di telefono non è nel formato valido.");
        }
    }

    public function getPassword(): string {
        return $this->password;
    }

    /**
     * @param string $password
     * @throws Eccezione se $password è una stringa più lunga del consentito
     */
    public function setPassword($password) {
        if(checkStringMaxLen($password, DataConstraints::utenti["password"])) {
            $this->password = $password;
        } else {
            throw new Eccezione("La password inserita supera la lunghezza consentita.");
        }
    }

}