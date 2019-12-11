<?php
require_once "../CheckMethods.php";

class Utente {
    private $id_utente;
    private $nome;
    private $cognome;
    private $user_name;
    private $mail;
    private $data_nascita;
    private $img_profilo;
    private $telefono;
    
    public function __construct($id, $n, $cog, $username, $mail, $data, $img, $telefono) {
        $this->setUserName($username);
        $this->setIdUtente($id);
        $this->setNome($n);
        $this->setCognome($cog);
        $this->setMail($mail);
        $this->setDataNascita($data);
        $this->setImgProfilo($img);
        $this->setTelefono($telefono);
    }
    
    
    public function getIdUtente() {
        return $this->id_utente;
    }
    
    public function setIdUtente($id_utente): void {
        //controllare che id_utente sia numerico
        $this->id_utente = $id_utente;
    }
    
    public function getNome() {
        return $this->nome;
    }
    
    public function setNome($nome): void {//controllare che il nome sia valida
        if (checkStringNoNumber($nome) and checkStringMaxLen(trim($nome),DataConstraints::utenti["nome"]) and strlen(trim($nome)) > 0)
            $this->$nome = $nome;
        else{
            throw new Eccezione("Il nome inserito non è valido!");
        }
    }
    
    public function getCognome() {
        return $this->cognome;
    }
    
    public function setCognome($cognome): void {
        //controllare che cognome non abbia numeri dentro
        if (checkStringNoNumber($cognome) and checkStringMaxLen(trim($cognome),DataConstraints::utenti["cognome"]) and strlen(trim($cognome)) > 0)
            $this->cognome = $cognome;
        else{
            throw new Eccezione("Il cognome inserito non è valido!");
        }
    }
    
    public function getUserName() {
        return $this->user_name;
    }
    
    public function setUserName($user_name): void {
      
        if (checkStringContainsNoSpace($user_name) and checkStringMaxLen(trim($user_name),DataConstraints::utenti["user_name"]) and strlen(trim($user_name)) > 0)
            $this->$user_name = $user_name;
        else{
            throw new Eccezione("Il username inserito non è valido!");
        }
    }
    
    public function getMail() {
        return $this->mail;
    }
    
    public function setMail($mail): void {
        if (checkIsValidMail($mail) and checkStringMaxLen(trim($mail),DataConstraints::utenti["mail"])
            and strlen(trim($mail))>6) {//6 perché a@a.aa, e aa perché non ci sono TLD con una lettera.
            $this->mail = htmlentities($mail);
        } else {
            throw new Eccezione(htmlentities("La mail inserito non è valida"));
        }
    }
    
    public function getDataNascita() {
        return $this->data_nascita;
    }
    
    /**
     *
     * @param $data_nascita string il formato di quest stringa deve essere aaaa-mm-gg
     * @throws Eccezione
     */
    public function setDataNascita($data_nascita): void {
        //controllare che data_nascita sia una data valida
        if (checkIsValidDate($data_nascita)) {
            $this->data_nascita = $data_nascita;
        } else {
            throw new Eccezione("La data di nascita inserita non è valida!");
        }
    }
    
    
    public function getImgProfilo() {
        return $this->img_profilo;
    }
    
    public function setImgProfilo($img_profilo): void {
        if (is_string($img_profilo) and checkStringMaxLen($img_profilo, DataConstraints::utenti["img_profilo"])
        and checkStringMinLen(trim($img_profilo),DataConstraints::utenti["img_profilo"])) {
            $img_profilo = str_replace(" ", "_", $img_profilo);
            $this->img_profilo = $img_profilo;
        } else {
            throw new Eccezione("Il path dell'immagine di profilo non è valido. ");
        }
    }
    
    public function getTelefono() {
        return $this->telefono;
    }
    
    public function setTelefono($telefono): void {
        if (checkPhoneNumber($telefono)) {
            $this->telefono = $telefono;
        } else {
            throw new Eccezione("Il numero di telefono non è valido!");
        }
    }
    
}