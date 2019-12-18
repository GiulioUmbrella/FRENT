<?php

require_once ($_SERVER["DOCUMENT_ROOT"])."/php/CheckMethods.php";

class Foto {
    private $id_foto;
    private $file_path;
    private $descrizione;
    private $id_annuncio;

    private function __construct() {}

    public static function build(): Foto {
        return new Foto();
    }
    
    public function getIdFoto(): int {
        return $this->id_foto;
    }
    
    /**
     * @param int $id_foto
     * @throws Eccezione se $id_foto non è un intero positivo
     */
    public function setIdFoto($id_foto) {
        if (is_int($id_foto) and $id_foto > 0) {
            $this->id_foto = $id_foto;
        } else {
            throw new Eccezione(htmlentities("L'ID della foto non è valido."));
        }
    }
    
    public function getFilePath(): string {
        return $this->file_path;
    }
    
    /**
     * @param string $file_path
     * @throws Eccezione se $file_path supera la lungehezza massima
     */
    public function setFilePath($file_path) {
        $trim_file_path = trim($file_path);
        if (checkStringMaxLen($trim_file_path, DataConstraints::foto_annunci["file_path"])) {
            $this->file_path = str_replace(" ", "_", $trim_file_path);
        } else {
            throw new Eccezione(htmlentities("Il path della foto non è valido."));
        }
    }
    
    public function getDescrizione(): string {
        return $this->descrizione;
    }
    
    /**
     * @param string $descrizione
     * @throws Eccezione se $descrizione supera la lunghezza massima consentita
     */
    public function setDescrizione($descrizione) {
        if (checkStringMaxLen(trim($descrizione), DataConstraints::foto_annunci["descrizione"])) {
            $this->descrizione = trim($descrizione);
        } else {
            throw new Eccezione(htmlentities("La descrizione non è valida!"));
        }
    }

    public function getIdAnnuncio(): int {
        return $this->id_annuncio;
    }

    /**
     * @param int $id
     * @throws Eccezione se $id non è un intero positivo
     */
    public function setIdAnnuncio($id) {
        if (is_int($id) and $id > 0) {
            $this->id_annuncio = $id;
        } else {
            throw new Eccezione(htmlentities("L'ID dell'annuncio non è valido."));
        }
    }
    
}