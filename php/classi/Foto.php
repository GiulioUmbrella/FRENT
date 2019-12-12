<?php

require_once "../CheckMethods.php";

class Foto {
    private $id_foto;
    private $file_path;
    private $descrizione;
    private $id_annuncio;
    
    public function __construct($id, $desc = "Questa foto non &egrave; stata commentata.", $path = "nofile", $id_annuncio) {
        $this->setIdFoto($id);
        $this->setDescrizione($desc);
        $this->setFilePath($path);
        $this->setIdAnnuncio($id_annuncio);
    }
    
    public function getIdFoto(): int {
        return $this->id_foto;
    }
    
    public function setIdFoto($id_foto): void {
        if (is_int($id_foto) and $id_foto > 0) {
            $this->id_foto = $id_foto;
        } else {
            throw new Eccezione(htmlentities("L'ID della foto non è valido."));
        }
    }
    
    public function getFilePath(): string {
        return $this->file_path;
    }
    
    public function setFilePath($file_path): void {
        if (is_string($file_path) and checkStringMaxLen(trim($file_path), DataConstraints::foto_annunci["file_path"])) {
            $file_path = trim($file_path);
            $file_path = str_replace(" ", "_", $file_path);
            $this->file_path = $file_path;
        } else {
            throw new Eccezione(htmlentities("Il path della foto non è valido!"));
        }
    }
    
    public function getDescrizione(): string {
        return $this->descrizione;
    }
    
    public function setDescrizione($descrizione): void {
        if (is_string($descrizione) and checkStringMaxLen(trim($descrizione), DataConstraints::foto_annunci["descrizione"])) {
            $this->descrizione = trim($descrizione);
        } else {
            throw new Eccezione(htmlentities("La descrizione non è valida!"));
        }
    }

    public function getIdAnnuncio(): int {
        return $this->id_annuncio;
    }

    public function setIdAnnuncio($id): void {
        if (is_int($id) and $id > 0) {
            $this->id_annuncio = $id;
        } else {
            throw new Eccezione(htmlentities("L'ID dell'annuncio non è valido."));
        }
    }
    
}